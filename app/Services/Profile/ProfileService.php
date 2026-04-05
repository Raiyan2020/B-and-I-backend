<?php

namespace App\Services\Profile;

use App\Enums\AuthUpdatesAttributesEnum;
use App\Services\Core\BaseService;

class ProfileService extends BaseService
{
    public function editProfile($request): array
    {
        $request['user']->update($request);
        return ['key' => 'success', 'msg' => __('apis.success'), 'user' => $request['user']->refresh()];
    }

    public function storeAtUpdates($request): array
    {
        $update = $request['user']->authUpdates()->updateOrCreate(['type' => $request['type']], $request + ['code' => '']);
        $this->sendCode($request['user'], $update);

        $data = [
            //            filter_var($update->attribute, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone' => $update->attribute,
            'attribute' => $update->attribute,
        ];
        !isset($update->country_code) ?: $data['country_code'] = $update->country_code;
        return [
            'key'  => 'success',
            'msg'  => __('apis.code_sent'),
            'data' => $data
        ];
    }

    protected function sendCode($user, $update): void
    {
        match ($update->attribute) {
            filter_var($update->attribute, FILTER_VALIDATE_EMAIL) => $user->sendCodeAtEmail($update->code, $update->attribute),
            default => $user->sendCodeAtSms($update->code, $update->country_code . $update->attribute),
        };
    }

    public function verifyCode($user, $request): array
    {
        try {
            $row = $user->authUpdates()->where([
                'country_code' => $request['country_code'],
                'code'         => $request['code'],
                'type'         => $request['type']
            ])
            ->where(function($query) use ($request) {
                $query->where('attribute', $request['attribute'])
                    ->orWhere('attribute', fixPhone($request['phone']));
            })
            ->first();


            if (!$row) {
                throw new \Exception(__('apis.invalid_code'));
            }
            $result = match ($row->type) {
                AuthUpdatesAttributesEnum::Email->value, AuthUpdatesAttributesEnum::Phone->value, AuthUpdatesAttributesEnum::Password->value => [
                    $row->update(['code' => null]),
                    'msg' => __('apis.code_verified'),
                ],
                AuthUpdatesAttributesEnum::NewPhone->value => [
                    $user->update([
                        'phone'        => $row->attribute,
                        'country_code' => $row->country_code,
                    ]),
                    $user->authUpdates()->whereIn('type', [AuthUpdatesAttributesEnum::Phone->value, AuthUpdatesAttributesEnum::NewPhone->value])->delete(),
                    'msg' => __('apis.phone_changed'),
                ],
                AuthUpdatesAttributesEnum::NewEmail->value => [
                    $user->update(['email' => $row->attribute]),
                    $user->authUpdates()->whereIn('type', [AuthUpdatesAttributesEnum::Email->value, AuthUpdatesAttributesEnum::NewEmail->value])->delete(),
                    'msg' => __('apis.email_changed'),
                ],
                default => [
                    'msg' => __('apis.invalid_code')
                ],
            };

            $data['attribute'] = $row->attribute;
            //                filter_var($row->attribute, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone' => $row->attribute,
            !isset($row->country_code) ?: $data['country_code'] = $row->country_code;
            return [
                'key'  => 'success',
                'msg'  => $result['msg'],
                'data' => $data
            ];
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function updatePassword($request): array
    {
        $user = $request['user'];
        $user->update(['password' => $request['password']]);
        return ['key' => 'success', 'msg' => __('apis.password_updated')];
    }


}
