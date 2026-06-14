<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use App\Traits\FilterTrait;

class AboutUsItem extends BaseModel
{
    use HasFactory, SoftDeletes, HasTranslations, FilterTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['image', 'title', 'description', 'status'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'boolean',
    ];

    public const FOLDER = 'about_us';

    /**
     * Translatable attributes.
     *
     * @var array<int, string>
     */
    public $translatable = ['title', 'description'];

    public function scopeSearch(Builder $query, array $filters = []): Builder
    {
        $filters = $filters ?: (array) request()->input('filters', []);
        $search = trim((string) ($filters['title'] ?? $filters['search'] ?? ''));
        $status = $filters['status'] ?? null;

        if ($search !== '') {
            $query->where(function (Builder $query) use ($search) {
                $query->where('title->ar', 'like', '%' . $search . '%')
                    ->orWhere('title->en', 'like', '%' . $search . '%')
                    ->orWhere('description->ar', 'like', '%' . $search . '%')
                    ->orWhere('description->en', 'like', '%' . $search . '%');
            });
        }

        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        return $query->latest();
    }
}
