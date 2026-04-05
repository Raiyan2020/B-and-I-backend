<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class BaseService extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'base-service'; // Must match the container key used above
    }
}
