<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\Dashboard\Categories\StoreRequest;
use App\Http\Requests\Dashboard\Categories\UpdateRequest;
use App\Models\Category;
use App\Services\Dashboard\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class CategoryController extends AdminBasicController
{
    public function __construct()
    {
        $this->middleware('permission:categories', ['only' => ['index', 'show']]);
        $this->middleware('permission:add-category', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-category', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-category', ['only' => ['destroy', 'destroyMultiple']]);

        $this->model = Category::class;
        $this->serviceName = new CategoryService();
        $this->storeRequest = StoreRequest::class;
        $this->updateRequest = UpdateRequest::class;
        $this->directoryName = 'categories';
        $this->indexScopes = 'search';
        $this->destroyRelationsToCheck = [];
    }

    /**
     * Override edit to include max_order.
     *
     * @param int $id
     * @return View
     */
    public function edit($id): View
    {
        $row = $this->serviceName->find($id);
        $maxOrder = $this->serviceName->getMaxOrder();

        return view('dashboard.categories.edit', [
            'row' => $row,
            'max_order' => $maxOrder
        ]);
    }

    /**
     * Toggle status for a category.
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
