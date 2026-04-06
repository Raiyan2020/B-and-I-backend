<?php

namespace App\Models;

use Illuminate\Support\Str;
use Spatie\Permission\Models\Role as SpatieRole;
use Spatie\Translatable\HasTranslations;

class Role extends SpatieRole
{
    use HasTranslations;

    public array $translatable = ['title'];

    protected $appends = [
        'display_name',
    ];

    public function getDisplayNameAttribute(): string
    {
        return $this->getTranslation('title', app()->getLocale())
            ?? $this->getTranslation('title', 'ar')
            ?? $this->getTranslation('title', 'en')
            ?? $this->name;
    }

    /**
     * Internal slug for Spatie (assignRole / syncRoles). Not shown in the dashboard.
     */
    public static function generateUniqueSlug(string $englishTitle): string
    {
        $base = Str::slug($englishTitle);
        if ($base === '') {
            $base = 'role';
        }

        $slug = $base;
        $attempts = 0;
        while (static::query()->where('guard_name', 'admin')->where('name', $slug)->exists()) {
            $slug = $base.'-'.Str::lower(Str::random(6));
            $attempts++;
            if ($attempts > 50) {
                $slug = 'role-'.Str::lower(Str::random(12));
                break;
            }
        }

        return $slug;
    }
}
