<?php

namespace App\Http\Controllers\Api\V1\General;

use App\Facades\BaseService;
use App\Enums\InvestorExperience;
use App\Enums\InvestorType;
use App\Http\Controllers\Controller;
use App\Http\Resources\PreferredSectorOptionResource;
use App\Models\PreferredSector;
use App\Support\QueryOptions;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;

/**
 * مرجع ثابت للواجهات (أنواع المستثمر، مستويات الخبرة، …).
 */
class ReferenceDataController extends Controller
{
    use ResponseTrait;

    public function investorTypes(): JsonResponse
    {
        return $this->jsonResponse(data: $this->mapEnum(InvestorType::cases(), 'investor_type'));
    }

    public function investorExperience(): JsonResponse
    {
        return $this->jsonResponse(
            data: $this->mapEnum(InvestorExperience::cases(), 'investor_experience'),
        );
    }

    public function preferredSectors(): JsonResponse
    {
        $options = (new QueryOptions())->latest()->conditions(['status' => true]);
        $sectors = BaseService::setModel(PreferredSector::class)->all($options);

        return $this->jsonResponse(
            data: PreferredSectorOptionResource::collection($sectors),
        );
    }

    /**
     * @param  list<\BackedEnum>  $cases
     * @return list<array{value: string|int, name: string, label: string}>
     */
    private function mapEnum(array $cases, string $translationKey): array
    {
        return array_map(function (\BackedEnum $case) use ($translationKey) {
            return [
                'value' => $case->value,
                'name' => $case->name,
                'label' => __('enums.'.$translationKey.'.'.$case->value),
            ];
        }, $cases);
    }
}
