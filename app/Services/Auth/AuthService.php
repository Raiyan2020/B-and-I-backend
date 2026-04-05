<?php

namespace App\Services\Auth;

use App\Traits\UploadTrait;
use App\Traits\GeneralTrait;
use Illuminate\Http\Response;
use App\Services\Core\BaseService;
use Illuminate\Support\Facades\Schema;


class AuthService extends BaseService
{
    use GeneralTrait, UploadTrait;


    public function sendVerificationCodeToPhone($user): array
    {
        $user->sendVerificationCode();

        return [
            'key'  => 'success',
            'msg'  => __('auth.send_verification_code_to_phone'),
            'data' => [
                'phone'        => $user->phone,
                'country_code' => $user->country_code
            ]
        ];
    }

    public function login($user): array
    {
        $token = $user->login();
        return [
            'key'  => 'success',
            'msg'  => __('auth.success_login'),
            'data' => [
                'token' => $token,
                'user'  => $user->refresh(),
            ]
        ];
    }

    public function activate($request): array
    {
        $table = $request['user']->getTable();
        $msg = (Schema::hasColumn($table, 'active')&&!$request['user']->active) ? __('auth.delivery_company_join_request_sent') : __('auth.registered_success');
        $request['user']->markAsActive();
        // Return the response data
        return [
            'key'  => 'success',
            'msg'  => $msg,
            'data' => [
                // 'token' => $request['user']->login(),
                'user'  => $request['user']->refresh(),
            ]
        ];
    }

    public function register($request): array
    {
        $user = $this->model::create($request);

        return [
            'key'  => 'success',
            'msg'  => __('auth.done_registration_verification_code_sent_to_phone'),
            'data' => $user
        ];
    }


    public function resendCode($request): array
    {
        $request['user']->sendVerificationCode();
        return [
            'key'  => 'success',
            'msg'  => __('auth.code_re_send'),
            'user' => $request['user']->refresh()
        ];
    }

    public function logout($request): array
    {
        auth()->user()->logout($request);
        return [
            'msg' => __('auth.logout_success')
        ];
    }

    public function deleteAccount($request)
    {
        auth()->user()->delete();
        return ['msg' => __('auth.account_deleted')];
    }
}
