<?php

namespace App\Rules;

use App\Models\Role;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueRoleTitleLocale implements ValidationRule
{
    public function __construct(
        protected string $locale,
        protected ?int $ignoreRoleId = null
    ) {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            return;
        }

        $value = trim($value);
        if ($value === '') {
            return;
        }

        if (! in_array($this->locale, ['ar', 'en'], true)) {
            return;
        }

        $query = Role::query()
            ->where('guard_name', 'admin')
            ->where("title->{$this->locale}", $value);

        if ($this->ignoreRoleId !== null) {
            $query->where('id', '!=', $this->ignoreRoleId);
        }

        if ($query->exists()) {
            $fail(__('validation.unique_role_title_locale', [
                'locale' => $this->locale === 'ar'
                    ? __('validation.attributes.title_ar')
                    : __('validation.attributes.title_en'),
            ]));
        }
    }
}
