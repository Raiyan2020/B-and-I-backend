<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\Dashboard\Features\StoreRequest;
use App\Http\Requests\Dashboard\Features\UpdateRequest;
use App\Models\Feature;
use App\Services\Dashboard\FeatureService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class FeatureController extends AdminBasicController
{
    public function __construct()
    {
        $this->middleware('permission:features', ['only' => ['index', 'show']]);
        $this->middleware('permission:add-feature', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-feature', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-feature', ['only' => ['destroy', 'destroyMultiple']]);

        $this->model = Feature::class;
        $this->serviceName = new FeatureService();
        $this->storeRequest = StoreRequest::class;
        $this->updateRequest = UpdateRequest::class;
        $this->directoryName = 'features';
        $this->indexScopes = 'search';
        $this->destroyRelationsToCheck = [];
    }

    /**
     * Toggle status for a feature.
     *
     * @param int $id
     * @return JsonResponse
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
