<?php

namespace App\Http\Requests\Dashboard\Roles;

use App\Models\Role;
use App\Rules\UniqueRoleTitleLocale;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Role $role */
        $role = $this->route('role');

        return [
            'title.ar' => ['required', 'string', 'max:255', new UniqueRoleTitleLocale('ar', $role->id)],
            'title.en' => ['required', 'string', 'max:255', new UniqueRoleTitleLocale('en', $role->id)],
            'permission' => ['required', 'array'],
            'permission.*' => ['required', Rule::exists('permissions', 'id')],
        ];
    }
}
