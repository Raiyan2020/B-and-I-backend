<?php

namespace App\Http\Controllers\Api\V1\General;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\General\ListPublicInvestorsRequest;
use App\Http\Resources\PublicInvestorResource;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;

class InvestorDirectoryController extends Controller
{
    use ResponseTrait;

    public function index(ListPublicInvestorsRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $perPage = (int) ($validated['per_page'] ?? 15);

        $query = User::query()
            ->where('role', UserRole::Investor)
            ->where('is_active', true)
            ->where('is_blocked', false)
            ->with(['preferredSector'])
            ->latest('id');

        if (! empty($validated['investor_type'])) {
            $query->where('investor_type', $validated['investor_type']);
        }

        if (array_key_exists('min_capital', $validated) && $validated['min_capital'] !== null) {
            $query->where('available_capital', '>=', $validated['min_capital']);
        }

        if (array_key_exists('max_capital', $validated) && $validated['max_capital'] !== null) {
            $query->where('available_capital', '<=', $validated['max_capital']);
        }

        if (! empty($validated['investor_experience'])) {
            $query->where('investor_experience', $validated['investor_experience']);
        }

        if (! empty($validated['preferred_sector_id'])) {
            $query->where('preferred_sector_id', $validated['preferred_sector_id']);
        }

        $paginator = $query->paginate($perPage)->withQueryString();

        $payload = [
            'investors' => PublicInvestorResource::collection($paginator->items())->resolve(),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ];

        return $this->jsonResponse(data: $payload);
    }
}
