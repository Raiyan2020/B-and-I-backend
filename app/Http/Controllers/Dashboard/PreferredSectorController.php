<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\Dashboard\PreferredSectors\StoreRequest;
use App\Http\Requests\Dashboard\PreferredSectors\UpdateRequest;
use App\Models\PreferredSector;
use App\Services\Dashboard\PreferredSectorService;
use Illuminate\Http\JsonResponse;

class PreferredSectorController extends AdminBasicController
{
    public function __construct()
    {
        $this->middleware('permission:preferred-sectors', ['only' => ['index', 'show']]);
        $this->middleware('permission:add-preferred-sector', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-preferred-sector', ['only' => ['edit', 'update', 'toggleStatus']]);
        $this->middleware('permission:delete-preferred-sector', ['only' => ['destroy', 'destroyMultiple']]);

        $this->model = PreferredSector::class;
        $this->serviceName = new PreferredSectorService();
        $this->storeRequest = StoreRequest::class;
        $this->updateRequest = UpdateRequest::class;
        $this->directoryName = 'preferred_sectors';
        $this->indexScopes = 'search';
        $this->destroyRelationsToCheck = [];
    }

    public function toggleStatus($id): JsonResponse
    {
        try {
            $result = $this->serviceName->toggleStatus((int) $id);

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
