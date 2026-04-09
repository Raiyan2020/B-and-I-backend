<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\User;
use App\Http\Requests\Dashboard\Users\StoreRequest;
use App\Http\Requests\Dashboard\Users\UpdateRequest;
use App\Http\Requests\Dashboard\Users\ChargeWalletRequest;
use App\Http\Requests\Dashboard\Users\SendNotificationRequest;
use App\Enums\NotificationCategory;
use App\Notifications\GeneralNotification;
use App\Services\Core\BaseService;
use App\Services\Notifications\GeneralNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class UserController extends AdminBasicController
{
    public function __construct(private readonly GeneralNotificationService $generalNotificationService)
    {
        parent::__construct(
            model: User::class,
            storeRequest: StoreRequest::class,
            updateRequest: UpdateRequest::class,
            directoryName: 'users',
            serviceName: new BaseService(User::class),
            indexScopes: 'search',
            with: ['wallet', 'walletTransactions', 'orders'],
        );

        $this->middleware('permission:users', ['only' => ['index', 'show']]);
        $this->middleware('permission:add-user', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-user', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-user', ['only' => ['destroy', 'destroyMultiple']]);
        $this->middleware('permission:block-user', ['only' => ['toggleBlock']]);
    }

    /**
     * Toggle block status for a user.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleBlock($id)
    {
        try {
            $result = $this->serviceName->toggleBlock($id);
            return response()->json([
                'key' => 'success',
                'msg' => $result['msg']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'key' => 'error',
                'msg' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle active status (phone verification) for a user.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleActive($id)
    {
        try {
            $result = $this->serviceName->toggleActive($id);
            return response()->json([
                'key' => 'success',
                'msg' => $result['msg']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'key' => 'error',
                'msg' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Override show method to load wallet and orders relationships.
     *
     * @param int $id
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function show($id): View|JsonResponse
    {
        $row = $this->serviceName->find(id: $id, with: ['wallet', 'walletTransactions' => function($query) {
            $query->latest()->take(10);
        }, 'orders' => function($query) {
            $query->latest()->take(10);
        }]);

        // Load counts for statistics (safely handle if relationships don't exist)
        try {
            $row->loadCount([
                'orders',
                'orders as completed_orders_count' => function($query) {
                    $query->where('status', 'completed');
                },
                'orders as pending_orders_count' => function($query) {
                    $query->where('status', 'pending');
                },
                'walletTransactions'
            ]);
        } catch (\Exception $e) {
            // If relationships don't exist, set default values
            $row->setAttribute('orders_count', 0);
            $row->setAttribute('completed_orders_count', 0);
            $row->setAttribute('pending_orders_count', 0);
            $row->setAttribute('wallet_transactions_count', 0);
        }

        return view('dashboard.' . $this->directoryName . '.show', ['row' => $row]);
    }

    /**
     * Send notification to user.
     *
     * @param SendNotificationRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function sendNotification(SendNotificationRequest $request, $id): JsonResponse
    {
        try {
            $user = $this->serviceName->find($id);
            $validated = $request->validated();
            $payload = $validated['payload'] ?? [];

            if (! empty($validated['model_type'])) {
                $payload['model_type'] = $validated['model_type'];
            }

            if (! empty($validated['model_id'])) {
                $payload['model_id'] = $validated['model_id'];
            }

            $this->generalNotificationService->sendToUser(
                $user,
                new GeneralNotification(
                    title: [
                        'ar' => $validated['title_ar'],
                        'en' => $validated['title_en'],
                    ],
                    body: [
                        'ar' => $validated['body_ar'],
                        'en' => $validated['body_en'],
                    ],
                    notificationType: $validated['notification_type'] ?? 'user_notification',
                    category: $validated['category'] ?? NotificationCategory::System,
                    payload: $payload,
                ),
            );

            return response()->json([
                'key' => 'success',
                'msg' => __('dashboard.notification_sent_successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'key' => 'error',
                'msg' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Charge user wallet.
     *
     * @param ChargeWalletRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function chargeWallet(ChargeWalletRequest $request, $id): JsonResponse
    {
        try {
            $user = $this->serviceName->find($id);
            $validated = $request->validated();

            // Get or create wallet
            $wallet = $user->wallet;
            if (!$wallet || !$wallet->id) {
                $wallet = \App\Models\Wallet::firstOrCreate(
                    [
                        'walletable_type' => \App\Models\User::class,
                        'walletable_id' => $user->id,
                    ],
                    [
                        'available_balance' => 0,
                        'reserved_balance' => 0,
                    ]
                );
            }

            // Calculate new balance
            $balanceBefore = $wallet->available_balance ?? 0;
            $balanceAfter = $balanceBefore + $validated['amount'];

            // Update wallet
            $wallet->update([
                'available_balance' => $balanceAfter
            ]);

            // Refresh wallet to get updated balance
            $wallet->refresh();

            // Create transaction
            $transaction = $wallet->transactions()->create([
                'type' => \App\Enums\WalletTransactionTypeEnum::CHARGE,
                'amount' => $validated['amount'],
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'description' => $validated['description'] ?? __('dashboard.wallet_charge'),
            ]);

            // Load the latest transactions for response
            $latestTransactions = $wallet->transactions()
                ->latest()
                ->take(10)
                ->get()
                ->map(function ($trans) {
                    // Get type value - handle both enum object and raw value
                    if ($trans->type instanceof \App\Enums\WalletTransactionTypeEnum) {
                        $typeValue = $trans->type->value;
                    } elseif (is_object($trans->type) && isset($trans->type->value)) {
                        $typeValue = $trans->type->value;
                    } else {
                        $typeValue = (int) $trans->type;
                    }

                    $typeObj = \App\Enums\WalletTransactionTypeEnum::getFullObj($typeValue);

                    return [
                        'id' => $trans->id,
                        'type' => $typeObj['value'],
                        'type_label' => $typeObj['label'],
                        'amount' => number_format($trans->amount, 2),
                        'balance_after' => number_format($trans->balance_after, 2),
                        'description' => $trans->description,
                        'created_at' => $trans->created_at->format('Y-m-d H:i'),
                    ];
                });

            return response()->json([
                'key' => 'success',
                'msg' => __('dashboard.wallet_charged_successfully'),
                'balance' => $balanceAfter,
                'reserved_balance' => $wallet->reserved_balance ?? 0,
                'transactions' => $latestTransactions,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'key' => 'error',
                'msg' => $e->getMessage()
            ], 500);
        }
    }
}
