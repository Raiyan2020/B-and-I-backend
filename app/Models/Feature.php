<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use App\Traits\FilterTrait;

class Feature extends BaseModel
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

    public const FOLDER = 'features';

    /**
     * Translatable attributes.
     *
     * @var array<int, string>
     */
    public $translatable = ['title', 'description'];

    public function getAttribute($key)
    {
        if ($key === 'image') {
            $image = $this->attributes['image'] ?? null;

            if (empty($image)) {
                return null;
            }

            if (filter_var($image, FILTER_VALIDATE_URL)) {
                return $image;
            }

            $relativePath = 'storage/images/' . self::FOLDER . '/' . $image;

            if (! file_exists(public_path($relativePath))) {
                return null;
            }

            return asset($relativePath);
        }

        return parent::getAttribute($key);
    }
}
