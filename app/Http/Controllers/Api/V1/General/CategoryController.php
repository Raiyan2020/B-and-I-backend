<?php

namespace App\Http\Controllers\Api\V1\General;

use App\Enums\OpportunityStatus;
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
        $options = (new QueryOptions())->latest()->conditions(['status' => true]);
        $options->withCount(['opportunities' => function ($query) {
            $query->whereIn('status', [OpportunityStatus::Published->value, OpportunityStatus::Reserved->value]);
        }]);
        $options->custom(function ($query) {
            if (auth('sanctum')->check())
                $query->whereRelation('opportunities', 'user_id', '!=', auth('sanctum')->user()->id);
        });
        $categories = BaseService::setModel(Category::class)->limit($options);

        return $this->jsonResponse(data: [
            'categories' => CategoryResource::collection($categories),
            'pagination' => [
                'current_page' => $categories->currentPage(),
                'last_page'    => $categories->lastPage(),
                'per_page'     => $categories->perPage(),
                'total'        => $categories->total(),
            ]
        ]);
    }
}
