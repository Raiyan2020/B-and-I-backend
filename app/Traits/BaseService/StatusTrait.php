<?php

namespace App\Traits\BaseService;

use App\Models\Admin;
use App\Models\User;
use App\Services\Auth\AccountAccessService;
use App\Services\Core\WalletService;

trait StatusTrait
{
    /**
     * Toggle the status of a record.
     */
    public function toggleStatus(int $id): array
    {
        try {
            $model = $this->find($id);
            $model->update(['status' => !$model->status]);
            $message = $model->status ? __('dashboard.active') : __('dashboard.dis_activate');
            return ['key' => 'success', 'msg' => $message, 'data' => $model];
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Toggle the blocked status of a user and notify them.
     */
    public function toggleBlock(int $id): array
    {
        try {
            $user = $this->find($id);
            $user->update(['is_blocked' => !$user->is_blocked]);

            if ($user instanceof User && $user->is_blocked) {
                app(AccountAccessService::class)->blockUser($user, auth('admin')->user());

                return ['msg' => __('dashboard.blocked')];
            }

            if ($user instanceof Admin && $user->is_blocked) {
                app(AccountAccessService::class)->blockAdmin($user, auth('admin')->user());

                return ['msg' => __('dashboard.blocked')];
            }

            return ['msg' => __('dashboard.unblocked')];
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Toggle the account active status of a user.
     */
    public function toggleActive(int $id): array
    {
        try {
            $user = $this->find($id);
            $user->update([
                'is_active' => ! $user->is_active,
            ]);

            if ($user->is_active) {
                return ['msg' => __('dashboard.active')];
            }

            if ($user instanceof User) {
                app(AccountAccessService::class)->deactivateUser($user, auth('admin')->user());
            }

            return ['msg' => __('dashboard.inactive')];
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Update the balance of a user's wallet.
     */
    public function updateBalance(int $id, float $balance, int $type): array
    {
        try {
            $walletService = new WalletService();
            $user = $this->find($id);

            if ($balance <= 0) {
                return ['key' => 'fail', 'msg' => __('dashboard.invalid_balance'), 'balance' => $user->balance];
            }

            if ($type === 0) {
                $walletService->charge($user->wallet, $balance, $user);
            } else {
                if ($user->wallet?->balance < $balance) {
                    return ['key' => 'fail', 'msg' => __('dashboard.balance_not_enough'), 'balance' => $user->wallet?->balance];
                }
                $walletService->debt($user->wallet, $balance);
            }

            // إعادة تحميل العلاقة wallet للحصول على القيمة المحدثة من قاعدة البيانات
            // إزالة العلاقة من الذاكرة وإعادة تحميلها
            $user->unsetRelation('wallet');
            $user->load('wallet');

            // التأكد من الحصول على القيمة المحدثة
            $updatedBalance = $user->wallet?->balance ?? 0;

            return ['key' => 'success', 'msg' => __('dashboard.balance_updated'), 'balance' => $updatedBalance];
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}

