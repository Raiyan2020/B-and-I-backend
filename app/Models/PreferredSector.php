<?php

namespace App\Models;

use App\Traits\FilterTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class PreferredSector extends BaseModel
{
    use HasFactory, HasTranslations, SoftDeletes;

    use FilterTrait {
        applyColumnFilter as protected applyColumnFilterTrait;
    }

    protected $fillable = [
        'name',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public $translatable = ['name'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'preferred_sector_id');
    }

    /**
     * Search translatable JSON `name` by ar/en substring (FilterTrait default LIKE on JSON is unreliable).
     */
    protected function applyColumnFilter(Builder $query, string $column, $value, ?string $op = null): void
    {
        if ($column === 'name') {
            $op = $op ?: 'like';
            if ($op !== 'like') {
                $this->applyColumnFilterTrait($query, $column, $value, $op);

                return;
            }
            $like = '%' . $value . '%';
            $query->where(function (Builder $q) use ($like): void {
                $q->whereRaw('JSON_UNQUOTE(JSON_EXTRACT(name, \'$.ar\')) LIKE ?', [$like])
                    ->orWhereRaw('JSON_UNQUOTE(JSON_EXTRACT(name, \'$.en\')) LIKE ?', [$like]);
            });

            return;
        }

        $this->applyColumnFilterTrait($query, $column, $value, $op);
    }
}
