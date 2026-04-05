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

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }


}
