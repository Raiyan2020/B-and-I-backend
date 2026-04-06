<?php

namespace App\Services\Dashboard;

use App\Models\AboutUsItem;
use App\Services\Core\BaseService;

class AboutUsItemService extends BaseService
{
    public function __construct()
    {
        parent::__construct(AboutUsItem::class);
    }
}
