<?php

namespace App\Http\Requests\Dashboard\Admins;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $code = $this->input('country_code', '+966');
        $local = preg_replace('/\D/', '', (string) $this->input('phone_local', ''));
        if ($local !== '') {
            $this->merge(['phone' => $code.$local]);
        }

        $this->normalizePhoneToE164();
    }

    /**
     * Legacy rows may store digits without "+"; normalize for E.164 validation.
     */
    protected function normalizePhoneToE164(): void
    {
        $phone = $this->input('phone');
        if (! is_string($phone) || $phone === '') {
            return;
        }
        $phone = trim($phone);
        if (! str_starts_with($phone, '+')) {
            $digits = preg_replace('/\D/', '', $phone);
            if ($digits !== '') {
                $this->merge(['phone' => '+'.$digits]);
            }
        }
    }

    public function rules()
    {
        return [
            'role' => ['required', Rule::exists('roles', 'name')->where('name', '!=', 'super_admin')],
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
            'name' => ['required', 'min:3', 'max:100'],
            'email' => ['required', 'email:dns,rfc,spoof', Rule::unique('admins', 'email')->whereNull('deleted_at')],
            'country_code' => ['nullable', 'string', 'max:8'],
            'phone_local' => ['nullable', 'string', 'max:20'],
            'phone' => ['required', 'regex:/^\+[1-9]\d{7,14}$/', Rule::unique('admins', 'phone')->whereNull('deleted_at')],
            'password' => ['required', 'confirmed', 'min:6', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => __('validation.phone_international'),
        ];
    }
}
