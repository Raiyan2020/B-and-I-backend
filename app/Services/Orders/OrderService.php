<?php

namespace App\Services\Orders;

use App\Enums\PayTypeEnum;
use App\Models\Order\Order;
use App\Enums\PayStatusEnum;
use Illuminate\Http\Request;
use App\Support\QueryOptions;
use App\Traits\SettingsTrait;
use App\Jobs\NotifyPharmacyGroup;
use App\Models\AllUsers\Pharmacy;
use App\Services\Core\BaseService;
use App\Enums\NotificationTypeEnum;
use App\Enums\OrderStatusEnum;
use App\Jobs\CheckPharmacyResponseTimeout;
use App\Models\AllUsers\DeliveryMan;
use App\Models\IgnoreOrder;
use App\Services\Core\WalletService;
use App\Services\Payment\PayService;
use Illuminate\Support\Facades\Schema;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Notification;

class OrderService extends BaseService
{
    use SettingsTrait;

    public function __construct($model = null)
    {
        $this->model = Order::class;
    }

    public function getOrders(string|array $statuses = [], $paginateNum = 10, $type = null, $conditions = [], $orWhereConditions = [], $scopes = 'search', $user = null): mixed
    {
        try {
            $query = $this->model::query()
                ->where(function ($q) use ($conditions, $orWhereConditions) {
                    $q->where($conditions)
                        ->when(count($orWhereConditions), function ($q) use ($orWhereConditions) {
                            $q->orWhere(function ($q) use ($orWhereConditions) {
                                $q->where($orWhereConditions);
                            });
                        });
                })
                ->when($user, function ($q) use ($user) {
                    $q->where(function ($q) use ($user) {
                        $q->where($user, auth()->id());
                    });
                })
                ->when($type, function ($q) use ($type) {
                    $q->where('type', $type);
                })
                ->when(is_array($statuses) && !empty($statuses), function ($q) use ($statuses) {
                    $q->whereIn('status', $statuses);
                })
                ->when(is_string($statuses), function ($q) use ($statuses) {
                    $q->where('status', $statuses);
                });
            $orders = $this->applyScopes($query, $scopes);
            return $orders->latest()->paginate($paginateNum);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }


    public function updateOrder($request, $additionalData = []): array
    {
        $data = $request instanceof Request ? $request->validated() : (array)$request;

        if (!empty($additionalData)) {
            $data = array_merge($data, $additionalData);
        }

        $request->order->update($data);

        return ['key' => 'success', 'msg' => __('apis.status_changed'), 'row' => $request->order->refresh()];
    }

    /**
     * @throws \Exception
     */
    public function pay($request): array
    {
        $order = $request->validated()['order'];
        $isPending = $order->pay_status == PayStatusEnum::PENDING->value;
        $paid_amount = $isPending ? $order->first_payment_amount + ($order->vat_amount / 2) : ($order->offer_price_amount + $order->vat_amount) - $order->paid_amount;
        $result = match ((int)$request->validated()['pay_type']) {
            PayTypeEnum::WALLET->value => (new WalletService())->debt(auth('user')->user()->wallet, $paid_amount),
            PayTypeEnum::ONLINE->value => (new PayService())->pay(request: $request->validated()),
        };
        if (($result['key'] == 'success' && (int)$request->validated()['pay_type'] == PayTypeEnum::WALLET->value) ||
            $result['key'] == 'success' && $result['data'] == 'Paid' && (int)$request->validated()['pay_type'] == PayTypeEnum::ONLINE->value
        ) {
            return $this->applyPay($request, $order);
        }
        return $result;
    }

    public function applyPay($request, $order): array
    {
        try {
            $additionalData = $this->prepareDataAfterPayment($request);
            $this->updateOrder($request, $additionalData);
            $msg = $order->pay_status == PayStatusEnum::PARTIAL_PAID->value ?
                __('apis.u_r_paid_first_payment') :
                __('apis.u_r_paid_second_payment');
            return ['key' => 'success', 'msg' => $msg, 'data' => [], 'code' => 200];
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    protected function prepareDataAfterPayment($data): array
    {
        $order = $data->order;
        $isPending = $order->pay_status == PayStatusEnum::PENDING->value;
        if (Schema::hasColumn($order->getTable(), 'final_total')) {
            $total_amount = $order->final_total;
            $paid_amount = $isPending ? $order->first_payment_amount :
                $total_amount;
        } else {
            $total_amount = $order->offer_price_amount + $order->vat_amount;
            $paid_amount = $isPending ?
                $order->first_payment_amount + ($order->vat_amount / 2) :
                $total_amount;
        }
        $result = [
            'pay_type'           => $data->pay_type,
            'pay_status'         => $isPending ?
                PayStatusEnum::PARTIAL_PAID->value :
                PayStatusEnum::FULLY_PAID->value,
            'status'             => $data->status,
            'paid_amount'        => $paid_amount,
            'remaining_amount'   => $isPending ? $total_amount - $paid_amount : 0,
            'expiration_minutes' => null,
            'expire_at'          => null
        ];

        return $result;
    }

    /**
     * إرسال الإشعارات للصيدليات عند إنشاء طلب جديد
     * يتم تجميع الصيدليات حسب نسبة الخصم وإرسال الإشعارات بفترات تأخير
     *
     * @param Order $order
     * @return void
     */
    public function notifyPharmaciesForNewOrder(Order $order): void
    {
        $pharmacies = $order->searchAvailablePharmacies();
        if ($pharmacies->count() <= 0) {
            Notification::send($order->user, new GeneralNotification($order, NotificationTypeEnum::EXPAND_RANGE->value));
            return;
        }

        // تجميع الصيدليات حسب نسبة الخصم
        $groupedPharmacies = $pharmacies->groupBy(function ($pharmacy) {
            return (float) ($pharmacy->discount_ratio ?? 0);
        });

        // ترتيب المجموعات حسب نسبة الخصم (الأعلى أولاً)
        $sortedGroups = $groupedPharmacies->sortKeysDesc();

        // إعداد بيانات الإشعار
        $notificationData = [
            'order' => $order,
            'type'  => NotificationTypeEnum::NEW_ORDER->value,
        ];

        // إرسال الإشعارات للمجموعات مع تأخير 5 ثواني بين كل مجموعة
        // كل مجموعة جديدة تحصل على إشعار مع جميع المجموعات السابقة
        $delay = 0;
        $accumulatedPharmacies = collect(); // لتجميع الصيدليات من المجموعات السابقة

        foreach ($sortedGroups as $discountRatio => $pharmacyGroup) {
            // إضافة الصيدليات من المجموعة الحالية إلى المجموع
            $accumulatedPharmacies = $accumulatedPharmacies->merge($pharmacyGroup);

            // إرسال الإشعار لجميع الصيدليات المجمعة حتى الآن
            NotifyPharmacyGroup::dispatch($accumulatedPharmacies, $notificationData)
                ->delay(now()->addSeconds($delay));

            $delay += 5; // زيادة التأخير بمقدار 5 ثواني للمجموعة التالية
        }

        $this->update($order->id, [
            "pharmacy_response_timeout_at" => now()->addSeconds((float)($this->getKeyFromSetting('pharmacy_search_wait_time') ?? 5)),
        ]);
        dispatch(new CheckPharmacyResponseTimeout($order))
            ->delay(now()->addSeconds((float)($this->getKeyFromSetting('pharmacy_search_wait_time') ?? 5)));
    }


    public function getOrdersByStatus($status)
    {
        $pharmacy = auth('pharmacy')->user()->parent_id ?? auth('pharmacy')->id();

        $options = (new QueryOptions())
            ->conditions(['pharmacy_id' => $pharmacy])
            ->latest()
            ->paginateNum(15)
            ->custom(function ($q) use ($status) {
                match ($status) {
                    'current' => $q->whereIn('status', [OrderStatusEnum::PENDING->value, OrderStatusEnum::ACCEPTED->value]),
                    'schedule' => $q->where('status', OrderStatusEnum::SCHEDULED->value),
                    'finished' => $q->whereIn('status', [OrderStatusEnum::COMPLETED->value, OrderStatusEnum::CANCELLED->value]),
                    default => null,
                };
            });

        return $this->limit($options);
    }
}
