<?php

namespace App\Services\Core;

use App\Traits\UploadTrait;
use App\Traits\BaseService\QueryTrait;
use App\Traits\BaseService\CrudTrait;
use App\Traits\BaseService\RelationTrait;
use App\Traits\BaseService\StatusTrait;
use App\Traits\BaseService\FileNotificationTrait;

class BaseService
{
    use UploadTrait;
    use QueryTrait;
    use CrudTrait;
    use RelationTrait;
    use StatusTrait;
    use FileNotificationTrait;

    protected $model;

    public function __construct($model = null)
    {
        $this->model = $model;
    }

    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }


}
