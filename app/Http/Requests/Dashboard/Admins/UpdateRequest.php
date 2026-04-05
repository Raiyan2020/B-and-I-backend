<?php

namespace App\Http\Requests\Dashboard\Admins;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
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
        $adminId = $this->route('admin'); // Get admin ID from route parameter (could be model or ID)
        // If route model binding is used, get ID from model
        $adminId = is_object($adminId) ? $adminId->id : $adminId;

        return [
            'role' => ['required', Rule::exists('roles', 'name')->where('name', '!=', 'super_admin')],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'name' => ['required', 'min:3', 'max:100'],
            'email' => ['required', 'email', Rule::unique('admins', 'email')->whereNull('deleted_at')->ignore($adminId)],
            'phone' => ['required', 'digits_between:9,15', Rule::unique('admins', 'phone')->whereNull('deleted_at')->ignore($adminId)],
            'password' => ['nullable', 'confirmed', 'min:6', 'max:100'],
        ];
    }
}
