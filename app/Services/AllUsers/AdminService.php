<?php

namespace App\Services\AllUsers;

use App\Models\Admin;
use App\Services\Core\BaseService;

class AdminService extends BaseService
{
    public function __construct()
    {
        $this->model = Admin::class;
    }

    public function afterCreate($admin, array $data): void
    {
        $admin->assignRole($data['role']);
    }

    public function afterUpdate($admin, array $data): void
    {
        $admin->syncRoles([$data['role']]);
    }
}
