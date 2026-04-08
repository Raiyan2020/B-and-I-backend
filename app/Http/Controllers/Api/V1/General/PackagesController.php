<?php

namespace App\Http\Controllers\Api\V1\General;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubscriptionPackageResource;
use App\Models\GeneralSetting;
use App\Models\SubscriptionPackage;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
class PackagesController extends Controller
{
    use ResponseTrait;

    /**
     * Active subscription packages + page copy from settings.
     * Optional Sanctum user: {@see SubscriptionPackageResource} for can_register / is_subscribed.
     */
    public function index(): JsonResponse
    {
        $lang = app()->getLocale();
        $packages = SubscriptionPackage::query()
            ->where('status', true)
            ->orderByDesc('id')
            ->get();

        $data = [
            'pageSettings' => [
                'title' => GeneralSetting::getValueForKey('packages_page_title_'.$lang) ?? '',
                'description' => GeneralSetting::getValueForKey('packages_page_description_'.$lang) ?? '',
            ],
            'packages' => SubscriptionPackageResource::collection($packages)->resolve(),
        ];

        return $this->jsonResponse(data: $data);
    }
}
