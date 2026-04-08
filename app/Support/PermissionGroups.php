<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;

class PermissionGroups
{
    /**
     * Group dashboard permissions for role create/edit forms.
     *
     * @param  Collection<int, Permission>|array<int, Permission>  $permissions
     * @return array<string, array<int, Permission>>
     */
    public static function group(Collection|array $permissions): array
    {
        $grouped = [];
        $modelNames = ['users', 'user', 'roles', 'role', 'admins', 'admin', 'categories', 'category'];

        foreach ($permissions as $permission) {
            $permissionName = strtolower($permission->name);
            $category = self::resolveCategory($permissionName, $modelNames);

            if (! isset($grouped[$category])) {
                $grouped[$category] = [];
            }
            $grouped[$category][] = $permission;
        }

        $order = [
            'users' => 1,
            'admins' => 2,
            'roles' => 3,
            'categories' => 4,
            'settings' => 5,
            'preferred_sectors' => 6,
            'about_us' => 7,
            'subscription_packages' => 8,
            'other' => 99,
        ];
        uksort($grouped, function ($a, $b) use ($order) {
            return ($order[$a] ?? 98) <=> ($order[$b] ?? 98);
        });

        return $grouped;
    }

    private static function resolveCategory(string $permissionName, array $modelNames): string
    {
        if (str_contains($permissionName, 'settings')) {
            return 'settings';
        }
        if (str_contains($permissionName, 'preferred-sector')) {
            return 'preferred_sectors';
        }
        if (str_contains($permissionName, 'about-us')) {
            return 'about_us';
        }
        if (str_contains($permissionName, 'subscription-package')) {
            return 'subscription_packages';
        }

        $category = 'other';
        foreach ($modelNames as $model) {
            if (strpos($permissionName, $model) !== false) {
                if (in_array($model, ['user', 'users'], true)) {
                    return 'users';
                }
                if (in_array($model, ['role', 'roles'], true)) {
                    return 'roles';
                }
                if (in_array($model, ['admin', 'admins'], true)) {
                    return 'admins';
                }
                if (in_array($model, ['category', 'categories'], true)) {
                    return 'categories';
                }

                return $model;
            }
        }

        if ($category === 'other' && in_array($permissionName, $modelNames, true)) {
            if (in_array($permissionName, ['user', 'users'], true)) {
                return 'users';
            }
            if (in_array($permissionName, ['role', 'roles'], true)) {
                return 'roles';
            }
            if (in_array($permissionName, ['admin', 'admins'], true)) {
                return 'admins';
            }
            if (in_array($permissionName, ['category', 'categories'], true)) {
                return 'categories';
            }

            return $permissionName;
        }

        return 'other';
    }
}
