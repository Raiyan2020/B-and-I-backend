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

    public function rules()
    {
        return [
            'role' => ['required', Rule::exists('roles', 'name')->where('name', '!=', 'super_admin')],
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'name' => ['required', 'min:3', 'max:100'],
            'email' => ['required', 'email:dns,rfc,spoof', Rule::unique('admins', 'email')->whereNull('deleted_at')],
            'phone' => ['required', 'digits_between:9,15', Rule::unique('admins', 'phone')->whereNull('deleted_at')],
            'password' => ['required', 'confirmed', 'min:6', 'max:100'],
        ];
    }
}
