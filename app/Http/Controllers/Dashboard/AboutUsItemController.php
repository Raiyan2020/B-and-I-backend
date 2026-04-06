<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\Dashboard\AboutUsItems\StoreRequest;
use App\Http\Requests\Dashboard\AboutUsItems\UpdateRequest;
use App\Models\AboutUsItem;
use App\Models\GeneralSetting;
use App\Services\Dashboard\AboutUsItemService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AboutUsItemController extends AdminBasicController
{
    public function __construct()
    {
        $this->middleware('permission:about-us-items', ['only' => ['index']]);
        $this->middleware('permission:add-about-us-item', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-about-us-item', ['only' => ['edit', 'update', 'updateSettings']]);
        $this->middleware('permission:delete-about-us-item', ['only' => ['destroy', 'destroyMultiple']]);

        $this->model = AboutUsItem::class;
        $this->serviceName = new AboutUsItemService();
        $this->storeRequest = StoreRequest::class;
        $this->updateRequest = UpdateRequest::class;
        $this->directoryName = 'about_us_items';
        $this->indexScopes = 'search';
        $this->destroyRelationsToCheck = [];
    }

    /**
     * Override index to pass settings data.
     */
    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            return parent::index();
        }

        $legacyTitle = GeneralSetting::getValueForKey('about_us_title');
        $legacyDescription = GeneralSetting::getValueForKey('about_us_description');

        return view('dashboard.about_us_items.index', [
            'about_us_title_ar' => GeneralSetting::getValueForKey('about_us_title_ar') ?: $legacyTitle,
            'about_us_title_en' => GeneralSetting::getValueForKey('about_us_title_en') ?: '',
            'about_us_description_ar' => GeneralSetting::getValueForKey('about_us_description_ar') ?: $legacyDescription,
            'about_us_description_en' => GeneralSetting::getValueForKey('about_us_description_en') ?: '',
        ]);
    }

    /**
     * Update about us settings (title and description) via AJAX.
     */
    public function updateSettings(Request $request): JsonResponse
    {
        $request->validate([
            'about_us_title_ar' => ['required', 'string', 'max:255'],
            'about_us_title_en' => ['required', 'string', 'max:255'],
            'about_us_description_ar' => ['required', 'string', 'max:5000'],
            'about_us_description_en' => ['required', 'string', 'max:5000'],
        ]);

        foreach ([
            'about_us_title_ar',
            'about_us_title_en',
            'about_us_description_ar',
            'about_us_description_en',
        ] as $key) {
            GeneralSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $request->input($key)]
            );
        }

        return response()->json([
            'key' => 'success',
            'msg' => __('dashboard.about_us_settings_saved')
        ]);
    }

    /**
     * Toggle status for an about us item.
     */
    public function toggleStatus($id): JsonResponse
    {
        try {
            $result = $this->serviceName->toggleStatus($id);
            return response()->json([
                'key' => 'success',
                'msg' => $result['msg']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'key' => 'error',
                'msg' => $e->getMessage()
            ], 500);
        }
    }
}
