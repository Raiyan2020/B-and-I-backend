<?php

namespace App\Http\Requests\Dashboard\Users;

use Illuminate\Foundation\Http\FormRequest;

class SendNotificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['required', 'string', 'max:255'],
            'body_ar' => ['required', 'string'],
            'body_en' => ['required', 'string'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title_ar.required' => __('dashboard.title_ar_required'),
            'title_ar.max' => __('dashboard.title_ar_max_length'),
            'title_en.required' => __('dashboard.title_en_required'),
            'title_en.max' => __('dashboard.title_en_max_length'),
            'body_ar.required' => __('dashboard.body_ar_required'),
            'body_en.required' => __('dashboard.body_en_required'),
        ];
    }
}
