<?php

namespace App\Services\Dashboard;

use App\Models\SubscriptionPackage;
use App\Services\Core\BaseService;

class SubscriptionPackageService extends BaseService
{
    public function __construct()
    {
        parent::__construct(SubscriptionPackage::class);
    }
}
