<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\Dashboard\SubscriptionPackages\StoreRequest;
use App\Http\Requests\Dashboard\SubscriptionPackages\UpdateRequest;
use App\Models\GeneralSetting;
use App\Models\SubscriptionPackage;
use App\Services\Dashboard\SubscriptionPackageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionPackageController extends AdminBasicController
{
    public function __construct()
    {
        // $this->middleware('permission:subscription-packages', ['only' => ['index']]);
        // $this->middleware('permission:add-subscription-package', ['only' => ['create', 'store']]);
        // $this->middleware('permission:edit-subscription-package', ['only' => ['edit', 'update', 'updateSettings']]);
        // $this->middleware('permission:delete-subscription-package', ['only' => ['destroy', 'destroyMultiple']]);

        $this->model = SubscriptionPackage::class;
        $this->serviceName = new SubscriptionPackageService();
        $this->storeRequest = StoreRequest::class;
        $this->updateRequest = UpdateRequest::class;
        $this->directoryName = 'subscription_packages';
        $this->indexScopes = 'search';
        $this->destroyRelationsToCheck = [];
    }

    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            return parent::index();
        }

        return view('dashboard.subscription_packages.index', [
            'packages_page_title_ar' => GeneralSetting::getValueForKey('packages_page_title_ar') ?? '',
            'packages_page_title_en' => GeneralSetting::getValueForKey('packages_page_title_en') ?? '',
            'packages_page_description_ar' => GeneralSetting::getValueForKey('packages_page_description_ar') ?? '',
            'packages_page_description_en' => GeneralSetting::getValueForKey('packages_page_description_en') ?? '',
        ]);
    }

    public function updateSettings(Request $request): JsonResponse
    {
        $request->validate([
            'packages_page_title_ar' => ['required', 'string', 'max:255'],
            'packages_page_title_en' => ['required', 'string', 'max:255'],
            'packages_page_description_ar' => ['required', 'string', 'max:65000'],
            'packages_page_description_en' => ['required', 'string', 'max:65000'],
        ]);

        foreach ([
            'packages_page_title_ar',
            'packages_page_title_en',
            'packages_page_description_ar',
            'packages_page_description_en',
        ] as $key) {
            GeneralSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $request->input($key)]
            );
        }

        return response()->json([
            'key' => 'success',
            'msg' => __('dashboard.packages_settings_saved'),
        ]);
    }

    public function toggleStatus($id): JsonResponse
    {
        try {
            $result = $this->serviceName->toggleStatus($id);

            return response()->json([
                'key' => 'success',
                'msg' => $result['msg'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'key' => 'error',
                'msg' => $e->getMessage(),
            ], 500);
        }
    }
}
