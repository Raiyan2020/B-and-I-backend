<?php

namespace App\Models;

use App\Traits\FilterTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class SubscriptionPackage extends BaseModel
{
    use HasFactory, HasTranslations, SoftDeletes;

    use FilterTrait {
        applyColumnFilter as protected applyColumnFilterTrait;
    }

    protected $fillable = [
        'name',
        'price_monthly',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'price_monthly' => 'decimal:3',
    ];

    public $translatable = ['name', 'description'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'subscription_package_id');
    }

    /**
     * Search translatable JSON fields by ar/en substring.
     */
    protected function applyColumnFilter(Builder $query, string $column, $value, ?string $op = null): void
    {
        if (in_array($column, ['name', 'description'], true)) {
            $op = $op ?: 'like';
            if ($op !== 'like') {
                $this->applyColumnFilterTrait($query, $column, $value, $op);

                return;
            }
            $like = '%'.$value.'%';
            $query->where(function (Builder $q) use ($column, $like): void {
                $q->where($column.'->ar', 'like', $like)
                    ->orWhere($column.'->en', 'like', $like);
            });

            return;
        }

        $this->applyColumnFilterTrait($query, $column, $value, $op);
    }
}
