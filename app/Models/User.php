<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Traits\FilterTrait;
use App\Traits\UploadTrait;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, FilterTrait, UploadTrait;

    const FOLDER = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role',
        'first_name',
        'last_name',
        'image',
        'country_code',
        'phone',
        'email',
        'password',
        'code',
        'is_blocked',
        'is_active',
        'subscription_plan',
        'bio',
        'tagline',
        'investor_type',
        'investor_sector',
        'investor_capital',
        'investment_count',
        'investor_experience',
        'company_name',
        'company_license_url',
        'license_number',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'code',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_blocked' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Set password attribute with encryption.
     */
    public function setPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['password'] = bcrypt($value);
        }
    }

    /**
     * Get image attribute with default fallback.
     */
    public function getImageAttribute()
    {
        if ($this->attributes['image'] != 'default.png' && $this->attributes['image'] != null) {
            $image = $this->getImage($this->attributes['image'], self::FOLDER);
        } else {
            $image = $this->defaultImage(self::FOLDER);
        }
        return $image;
    }

    /**
     * Set image attribute with upload handling.
     */
    public function setImageAttribute($value)
    {
        if (null != $value && is_file($value)) {
            isset($this->attributes['image']) ? $this->deleteFile($this->attributes['image'], self::FOLDER) : '';
            $this->attributes['image'] = $this->uploadAllTypes($value, self::FOLDER);
        }
    }

    /**
     * Get notifications relationship.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get wallet relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function wallet()
    {
        return $this->morphOne(Wallet::class, 'walletable')->withDefault([
            'available_balance' => 0,
            'reserved_balance' => 0,
        ]);
    }

    /**
     * Get wallet transactions relationship through wallet.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function walletTransactions()
    {
        return $this->hasManyThrough(WalletTransaction::class, Wallet::class, 'walletable_id', 'wallet_id')
            ->where('wallets.walletable_type', static::class);
    }

    /**
     * Get orders relationship.
     * Note: This relationship will work when Order model is created.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        // Placeholder - will be implemented when Order model exists
        $orderClass = 'App\Models\Order';
        $impossibleCondition = '1 = 0';
        if (class_exists($orderClass)) {
            return $this->hasMany($orderClass);
        }
        // Return a dummy relationship that always returns empty collection
        return $this->hasMany(static::class, 'id', 'id')->whereRaw($impossibleCondition);
    }
}
