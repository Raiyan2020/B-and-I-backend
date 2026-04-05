<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;


trait FilterTrait
{

    public function scopeSearch(Builder $query, array $filters = []): Builder
    {
        $filters = $filters ?: (array) request()->input('filters', []);

        // Order settings
        $orderBy  = $filters['order_by'] ?? 'created_at';
        $orderDir = strtoupper($filters['order'] ?? 'DESC');
        $orderDir = in_array($orderDir, ['ASC', 'DESC'], true) ? $orderDir : 'DESC';

        // Remove non-filter keys
        unset($filters['order'], $filters['order_by']);

        foreach ($filters as $key => $value) {
            // Normalize value
            if (is_string($value)) {
                $value = trim($value);
            }

            // Skip empty values / guest
            if ($value === null || $value === '' || $value === 'guest') {
                continue;
            }

            // Date range filters
            if ($key === 'created_at_min') {
                $date = $this->parseDate($value);
                if ($date) {
                    $query->whereDate('created_at', '>=', $date);
                }
                continue;
            }

            if ($key === 'created_at_max') {
                $date = $this->parseDate($value);
                if ($date) {
                    $query->whereDate('created_at', '<=', $date);
                }
                continue;
            }

            // Support operators via suffix: field__op
            // Examples: price__gte, status__in, name__like
            $op = null;
            if (Str::contains($key, '__')) {
                [$key, $op] = explode('__', $key, 2);
                $op = strtolower($op);
            }

            // Relation filters: relation.column
            if (Str::contains($key, '.')) {
                [$relation, $column] = explode('.', $key, 2);

                $this->applyRelationFilter($query, $relation, $column, $value, $op);
                continue;
            }

            // Column filters
            $this->applyColumnFilter($query, $key, $value, $op);
        }

        return $query->orderBy($orderBy, $orderDir);
    }

    /**
     * Parse dates safely.
     */
    protected function parseDate($value): ?Carbon
    {
        try {
            // Accept common formats + fallback to Carbon::parse
            if (is_string($value) && preg_match('/^\d{2}-\d{2}-\d{4}$/', $value)) {
                return Carbon::createFromFormat('m-d-Y', $value);
            }

            if (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                return Carbon::createFromFormat('Y-m-d', $value);
            }

            return Carbon::parse($value);
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function applyColumnFilter(Builder $query, string $column, $value, ?string $op = null): void
    {
        $op = $op ?: (Str::endsWith($column, '_id') ? 'eq' : 'like');

        match ($op) {
            'eq'   => $query->where($column, '=', $value),
            'neq'  => $query->where($column, '!=', $value),
            'gt'   => $query->where($column, '>', $value),
            'gte'  => $query->where($column, '>=', $value),
            'lt'   => $query->where($column, '<', $value),
            'lte'  => $query->where($column, '<=', $value),
            'like' => $query->where($column, 'like', '%' . $value . '%'),
            'in'   => $query->whereIn($column, Arr::wrap($value)),
            default => $query->where($column, 'like', '%' . $value . '%'),
        };
    }

    protected function applyRelationFilter(Builder $query, string $relation, string $column, $value, ?string $op = null): void
    {
        $op = $op ?: 'eq';

        $query->whereHas($relation, function (Builder $q) use ($column, $value, $op) {
            match ($op) {
                'eq'   => $q->where($column, '=', $value),
                'neq'  => $q->where($column, '!=', $value),
                'like' => $q->where($column, 'like', '%' . $value . '%'),
                'in'   => $q->whereIn($column, Arr::wrap($value)),
                'gt'   => $q->where($column, '>', $value),
                'gte'  => $q->where($column, '>=', $value),
                'lt'   => $q->where($column, '<', $value),
                'lte'  => $q->where($column, '<=', $value),
                default => $q->where($column, '=', $value),
            };
        });
    }
}
