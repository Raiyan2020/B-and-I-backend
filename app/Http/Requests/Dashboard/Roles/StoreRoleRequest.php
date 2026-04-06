<?php

namespace App\Http\Requests\Dashboard\Roles;

use App\Rules\UniqueRoleTitleLocale;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title.ar' => ['required', 'string', 'max:255', new UniqueRoleTitleLocale('ar')],
            'title.en' => ['required', 'string', 'max:255', new UniqueRoleTitleLocale('en')],
            'permission' => ['required', 'array'],
            'permission.*' => ['numeric', 'required', Rule::exists('permissions', 'id')],
        ];
    }
}
