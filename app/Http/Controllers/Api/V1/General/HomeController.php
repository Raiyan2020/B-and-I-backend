<?php

namespace App\Http\Controllers\Api\V1\General;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    use ResponseTrait;
    public function whoWeAre(): JsonResponse
    {
        $whoWeAre = GeneralSetting::where('key', 'like', '%about_us_%')->get()->map(function($item){
            return [
                'key' => $item->key,
                'value' => $item->value,
            ];
        });
        return $this->jsonResponse(data: $whoWeAre);
    }

    public function homePage(): JsonResponse
    {
        $heroSection = GeneralSetting::where('key', 'like', '%website_header_%')->get()->map(function($item){
            return [
                'key' => $item->key,
                'value' => $item->value,
            ];
        });
        return $this->jsonResponse(data: $heroSection);
    }
}
