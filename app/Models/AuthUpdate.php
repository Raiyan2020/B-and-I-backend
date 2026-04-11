<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuthUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'sub_type',
        'attribute',
        'country_code',
        'code',
        'code_expires_at',
        'verified_at',
    ];

    protected $casts = [
        'code_expires_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function authUpdateable(): MorphTo
    {
        return $this->morphTo();
    }
}
