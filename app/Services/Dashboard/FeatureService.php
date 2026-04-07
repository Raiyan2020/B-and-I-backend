<?php

namespace App\Services\Dashboard;

use App\Models\Feature;
use App\Services\Core\BaseService;

class FeatureService extends BaseService
{
    public function __construct()
    {
        parent::__construct(Feature::class);
    }
}
