<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use App\Traits\FilterTrait;

class Category extends BaseModel
{
    use HasFactory, SoftDeletes, HasTranslations, FilterTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name', 'image', 'status', 'order','parent_id'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'boolean',
        'order' => 'integer',
    ];

    public const FOLDER = 'categories';

    /**
     * Translatable attributes.
     *
     * @var array<int, string>
     */
    public $translatable = ['name'];

    public function getAttribute($key)
    {
        if ($key === 'image') {
            $image = $this->attributes['image'] ?? null;

            if (empty($image)) {
                return null;
            }

            $relativePath = 'storage/images/' . self::FOLDER . '/' . $image;

            if (! file_exists(public_path($relativePath))) {
                return null;
            }

            return asset($relativePath);
        }

        return parent::getAttribute($key);
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function opportunities()
    {
        return $this->hasMany(Opportunity::class, 'category_id');
    }


}
