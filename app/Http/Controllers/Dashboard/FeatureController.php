<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\Dashboard\Features\StoreRequest;
use App\Http\Requests\Dashboard\Features\UpdateRequest;
use App\Models\Feature;
use App\Services\Dashboard\FeatureService;
use App\Support\QueryOptions;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

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

    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            $rows = $this->serviceName->all(
                (new QueryOptions())
                    ->paginateNum(30)
                    ->scopes($this->indexScopes ?? 'search')
                    ->conditions($this->indexConditions ?? [])
                    ->with($this->with ?? [])
                    ->latest(true)
            );

            return DataTables::of($rows)
                ->order(function () {})
                ->editColumn('image', fn (Feature $feature): ?string => $this->featureImageUrl($feature))
                ->make(true);
        }

        return view('dashboard.features.index');
    }

    private function featureImageUrl(Feature $feature): ?string
    {
        $image = $feature->getRawImageAttribute();

        if (empty($image)) {
            return null;
        }

        if (filter_var($image, FILTER_VALIDATE_URL)) {
            return $image;
        }

        $relativePath = 'storage/images/' . Feature::FOLDER . '/' . $image;

        if (! file_exists(public_path($relativePath))) {
            return null;
        }

        return asset($relativePath);
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
