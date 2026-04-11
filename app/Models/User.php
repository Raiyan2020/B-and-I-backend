<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Enums\InvestorExperience;
use App\Enums\InvestorType;
use App\Enums\UserRole;
use App\Traits\FilterTrait;
use App\Traits\UploadTrait;
use Illuminate\Http\UploadedFile;

class User extends Authenticatable implements HasLocalePreference, MustVerifyEmailContract
{
    use HasApiTokens, HasFactory, MustVerifyEmail, Notifiable, SoftDeletes, FilterTrait, UploadTrait;

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
        'display_name',
        'country_code',
        'phone',
        'email',
        'password',
        'company_license',

        'investor_type',
        'capital',
        'available_capital',
        'preferred_sector_id',
        'category_id',
        'subscription_package_id',
        'experience_level',
        'previous_investments_count',
        'investor_experience',

        'image',
        'bio',
        'short_description',
        'lang',
        'is_blocked',
        'is_active',
        'order_notifications_enabled',
        'interest_notifications_enabled',
        'system_notifications_enabled',
        'otp',
        'otp_expires_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp',
        'otp_expires_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'otp_expires_at' => 'datetime',
        'password' => 'hashed',
        'is_blocked' => 'boolean',
        'is_active' => 'boolean',
        'order_notifications_enabled' => 'boolean',
        'interest_notifications_enabled' => 'boolean',
        'system_notifications_enabled' => 'boolean',
        'role' => UserRole::class,
        'investor_type' => InvestorType::class,
        'investor_experience' => InvestorExperience::class,
        'capital' => 'decimal:3',
        'available_capital' => 'decimal:3',
        'experience_level' => 'decimal:3',
        'previous_investments_count' => 'integer',
    ];

    /**
     * @var list<string>
     */
    protected $appends = [
        'name',
    ];

    /**
     * الاسم الكامل للعرض في لوحة التحكم / DataTables (first_name + last_name).
     */
    public function getNameAttribute(): string
    {
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
    }

    /**
     * Get image attribute with default fallback.
     */
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
     * Store uploaded company license (image or PDF) under storage/images/{FOLDER}.
     */
    public function setCompanyLicenseAttribute(mixed $value): void
    {
        if ($value === null || $value === '') {
            $this->attributes['company_license'] = null;

            return;
        }

        if ($value instanceof UploadedFile) {
            $existing = $this->attributes['company_license'] ?? null;
            if (! empty($existing)) {
                $this->deleteFile($existing, self::FOLDER);
            }
            $this->attributes['company_license'] = $this->uploadAllTypes($value, self::FOLDER);

            return;
        }

        $this->attributes['company_license'] = $value;
    }

    /**
     * Public URL for the stored company license file (image or PDF).
     */
    public function getCompanyLicenseUrlAttribute(): ?string
    {
        $value = $this->attributes['company_license'] ?? null;
        if (empty($value)) {
            return null;
        }

        return static::getImage($value, self::FOLDER);
    }

    /**
     * Get notifications relationship.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class)->latest();
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    public function preferredSector()
    {
        return $this->belongsTo(PreferredSector::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subscriptionPackage()
    {
        return $this->belongsTo(SubscriptionPackage::class, 'subscription_package_id');
    }

    public function opportunities()
    {
        return $this->hasMany(Opportunity::class);
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

    public function preferredLocale(): string
    {
        return in_array($this->lang, ['ar', 'en'], true) ? $this->lang : app()->getLocale();
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification());
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
