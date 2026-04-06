<?php

namespace App\Services\Dashboard;

use App\Enums\UserRole;
use App\Models\PreferredSector;
use App\Models\User;
use App\Services\Core\BaseService;

class PreferredSectorService extends BaseService
{
    public function __construct()
    {
        parent::__construct(PreferredSector::class);
    }

    /**
     * Block delete if any investor references this sector via {@see User::$preferred_sector_id}.
     */
    public function hasLinkedInvestors(int $sectorId): bool
    {
        return User::query()
            ->where('role', UserRole::Investor)
            ->where('preferred_sector_id', $sectorId)
            ->exists();
    }

    public function delete(int $id, array $relationsToCheck = [], array $conditions = [], array $relationConditions = []): array
    {
        try {
            if ($this->hasLinkedInvestors($id)) {
                return [
                    'key' => 'error',
                    'msg' => __('dashboard.cannot_delete_preferred_sector_has_investors'),
                ];
            }

            $record = $this->find(id: $id, conditions: $conditions);
            $record->delete();

            return ['key' => 'success', 'msg' => __('dashboard.deleted_successfully')];
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function deleteMultiple($request, array $relationsToCheck = [], array $conditions = [], array $relationConditions = []): array
    {
        try {
            $requestIds = json_decode($request['data'], true);
            $blocked = false;

            foreach (array_column($requestIds, 'id') as $id) {
                if ($this->hasLinkedInvestors((int) $id)) {
                    $blocked = true;
                    continue;
                }
                $this->model::where($conditions)->findOrFail($id)->delete();
            }

            return [
                'key' => $blocked ? 'warning' : 'success',
                'msg' => $blocked
                    ? __('dashboard.some_preferred_sectors_not_deleted_have_investors')
                    : __('dashboard.all_selected_records_have_been_deleted'),
            ];
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
