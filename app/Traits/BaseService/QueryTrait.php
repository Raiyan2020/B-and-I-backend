<?php

namespace App\Traits\BaseService;

use App\Support\QueryOptions;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

trait QueryTrait
{
    public function limit(QueryOptions $options)
    {
        try {
            $query = $this->model::query()
                ->when($options->latest, fn($q) => $q->latest())
                ->when(!empty($options->conditions), fn($q) => $q->where($options->conditions))
                ->with($options->with)
                ->withCount($options->withCount);

            $query->when(!empty($options->anyOfRelationsMustExist), function ($q) use ($options) {
                $q->where(function ($sub) use ($options) {
                    foreach ($options->anyOfRelationsMustExist as $index => $relationName) {
                        $index === 0
                            ? $sub->has($relationName)
                            : $sub->orHas($relationName);
                    }
                });
            });

            if ($options->customQuery) {
                ($options->customQuery)($query);
            }

            $query = $this->applyScopes($query, $options->scopes);

            return $query->paginate($options->paginateNum);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Get all records with optional conditions and relationships.
     */
    public function all(QueryOptions $options): Collection
    {
        try {
            $query = $this->model::query()
                ->with($options->with)
                ->withCount($options->withCount)
                ->where($options->conditions);

            $query->when(!empty($options->anyOfRelationsMustExist), function ($q) use ($options) {
                $q->where(function ($sub) use ($options) {
                    foreach ($options->anyOfRelationsMustExist as $index => $relationName) {
                        $index === 0
                            ? $sub->has($relationName)
                            : $sub->orHas($relationName);
                    }
                });
            });
            if ($options->customQuery) {
                ($options->customQuery)($query);
            }

            $query = $this->applyScopes($query, $options->scopes);
            return $query->get();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function count(QueryOptions $options): int
    {
        try {
            $query = $this->model::query()
                ->where($options->conditions);

            $query = $this->applyScopes($query, $options->scopes);
            return $query->count();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Apply scopes to the query.
     */
    protected function applyScopes($query, string $scopes): object
    {
        try {
            if (empty($scopes)) {
                return $query;
            }

            $scopeMethods = explode('->', $scopes);
            foreach ($scopeMethods as $scopeMethod) {
                if (method_exists($this->model, 'scope' . ucfirst($scopeMethod))) {
                    if ($scopeMethod == 'search') {
                        // Support both filters and searchArray for backward compatibility
                        $searchData = request()->filters ?? request()->searchArray ?? [];
                        $query = $query->search($searchData);
                    }
                    $query->$scopeMethod();
                }
            }

            return $query;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function find($id, array $with = [], array $conditions = []): ?object
    {
        try {
            return $this->model::query()
                ->with($with)
                ->where($conditions)
                ->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
    public function findWithTrashed($id, array $with = [], array $conditions = []): ?object
    {
        try {
            return $this->model::query()
                ->with($with)
                ->where($conditions)
                ->withTrashed()
                ->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function first(array $with = [], array $conditions = []): ?object
    {
        try {
            return $this->model::query()
                ->with($with)
                ->where($conditions)
                ->first();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
