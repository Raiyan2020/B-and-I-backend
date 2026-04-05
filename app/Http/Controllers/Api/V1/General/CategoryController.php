<?php

namespace App\Http\Controllers\Api\V1\General;

use App\Facades\BaseService;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Support\QueryOptions;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    use ResponseTrait;
    public function index(): JsonResponse
    {
        $categories = BaseService::setModel(Category::class)->all(new QueryOptions());

        return $this->jsonResponse(data: CategoryResource::collection($categories));
    }
}
