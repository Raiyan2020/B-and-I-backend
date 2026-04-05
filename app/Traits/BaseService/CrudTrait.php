<?php

namespace App\Traits\BaseService;

use Illuminate\Database\Eloquent\Model;

trait CrudTrait
{
    /**
     * Create a new record.
     */
    public function create(array $data): Model
    {
        try {
            return $this->model::create($data);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Update a record by ID.
     */
    public function update(int|null $id, array $data, array $conditions = []): object|int
    {
        try {
            return $id ?
                $this->find($id, conditions: $conditions)->update($data) :
                $this->model::where($conditions)->update($data);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Delete a record by ID after checking for related data.
     *
     * @param int $id The ID of the record to delete.
     * @param array $relationsToCheck An array of relations to check for existence.
     * @return array Returns an array with a 'key' and 'msg' indicating the result.
     */
    public function delete(int $id, array $relationsToCheck = [], array $conditions = [], array $relationConditions = []): array
    {
        try {
            // Find the record or fail
            $record = $this->find(id: $id, conditions: $conditions);

            // Check if any of the specified relations exist
            foreach ($relationsToCheck as $relation) {
                $conditionsOfRelation = $relationConditions[$relation] ?? [];
                if ($record->$relation()->where($conditionsOfRelation)->exists()) {
                    return ['key' => 'error', 'msg' => __('dashboard.record_has_related_data_and_cannot_be_deleted')];
                }
            }

            // If no related data exists, delete the record
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

            // Initialize a flag to track if any record has related data
            $hasRelatedData = false;

            // Loop through each ID
            foreach (array_column($requestIds, 'id') as $id) {
                // Find the record or fail
                $record = $this->model::where($conditions)->findOrFail($id);

                // Check if any of the specified relations exist
                foreach ($relationsToCheck as $relation) {
                    $conditionsOfRelation = $relationConditions[$relation] ?? [];
                    if ($record->$relation()->where($conditionsOfRelation)->exists()) {
                        $hasRelatedData = true;
                        break 2; // Exit both loops if related data is found
                    }
                }

                // If no related data exists, delete the record
                $record->delete();
            }

            return [
                'key' => $hasRelatedData ? 'warning' : 'success',
                'msg' => $hasRelatedData ? __('dashboard.some_records_have_related_data_and_cannot_be_deleted') :
                    __('dashboard.all_selected_records_have_been_deleted')
            ];
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function deleteWhere(array $conditions): int
    {
        return $this->model::where($conditions)->delete();
    }
}

