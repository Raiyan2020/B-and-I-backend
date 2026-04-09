<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\FilterTrait;
use App\Traits\UploadTrait;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasRoles,FilterTrait,UploadTrait;

    const FOLDER = 'admins';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'image',
        'name',
        'email',
        'password',
        'phone',
        'is_blocked',
    ];

    public function setPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['password'] = bcrypt($value);
        }
    }

    public function getImageAttribute()
    {
        $imageName = $this->attributes['image'] ?? null;

        if ($imageName != 'default.png' && $imageName != null) {
            $image = $this->getImage($imageName, self::FOLDER);
        } else {
            $image = $this->defaultImage(self::FOLDER);
        }
        return $image;
    }
    public function setImageAttribute($value)
    {
        if (null != $value && is_file($value)) {
            isset($this->attributes['image']) ? $this->deleteFile($this->attributes['image'], self::FOLDER) : '';
            $this->attributes['image'] = $this->uploadAllTypes($value, self::FOLDER);
        }
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }
}
