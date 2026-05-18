<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Enums\InvestorExperience;
use App\Enums\InvestorType;
use App\Enums\UserRole;
use App\Traits\FilterTrait;
use App\Traits\UploadTrait;
use Illuminate\Http\UploadedFile;

class User extends Authenticatable implements HasLocalePreference, MustVerifyEmailContract
{
    use HasApiTokens, HasFactory, MustVerifyEmail, Notifiable, SoftDeletes, UploadTrait;
    use FilterTrait {
        applyColumnFilter as protected applyColumnFilterTrait;
    }

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
        'email_verified_at',
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
        'otp_expires_at',
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
        'full_phone',
        'email_verified',
        'has_pending_profile_update_request',
        'has_pending_account_deletion_request',
    ];

    /**
     * الاسم الكامل للعرض في لوحة التحكم / DataTables (first_name + last_name).
     */
    public function getNameAttribute(): string
    {
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
    }

    public function getFullPhoneAttribute(): string
    {
        return trim(collect([$this->country_code, $this->phone])->filter()->implode(''));
    }

    public function getEmailVerifiedAttribute(): bool
    {
        return ! is_null($this->email_verified_at);
    }

    public function getHasPendingProfileUpdateRequestAttribute(): bool
    {
        if ($this->relationLoaded('latestPendingProfileUpdateRequest')) {
            return $this->latestPendingProfileUpdateRequest !== null;
        }

        return $this->pendingProfileUpdateRequests()->exists();
    }

    public function getHasPendingAccountDeletionRequestAttribute(): bool
    {
        if ($this->relationLoaded('latestPendingAccountDeletionRequest')) {
            return $this->latestPendingAccountDeletionRequest !== null;
        }

        return $this->pendingAccountDeletionRequests()->exists();
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
    public function notifications(): MorphMany
    {
        return $this->morphMany(Notification::class, 'notifiable')->latest();
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    public function authUpdates(): MorphMany
    {
        return $this->morphMany(AuthUpdate::class, 'auth_updateable');
    }

    public function pendingProfileUpdateRequests(): HasMany
    {
        return $this->hasMany(ProfileUpdateRequest::class)
            ->where('status', \App\Enums\ProfileUpdateRequestStatus::Pending);
    }

    public function latestPendingProfileUpdateRequest(): HasOne
    {
        return $this->hasOne(ProfileUpdateRequest::class)
            ->where('status', \App\Enums\ProfileUpdateRequestStatus::Pending)
            ->latestOfMany();
    }

    public function profileUpdateRequests(): HasMany
    {
        return $this->hasMany(ProfileUpdateRequest::class)->latest();
    }

    public function pendingAccountDeletionRequests(): HasMany
    {
        return $this->hasMany(AccountDeletionRequest::class)
            ->where('status', \App\Enums\AccountDeletionRequestStatus::Pending);
    }

    public function latestPendingAccountDeletionRequest(): HasOne
    {
        return $this->hasOne(AccountDeletionRequest::class)
            ->where('status', \App\Enums\AccountDeletionRequestStatus::Pending)
            ->latestOfMany();
    }

    public function accountDeletionRequests(): HasMany
    {
        return $this->hasMany(AccountDeletionRequest::class)->latest();
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

    public function awardedOpportunities(): HasMany
    {
        return $this->hasMany(Opportunity::class, 'investor_id');
    }

    public function investmentSeats(): HasMany
    {
        return $this->hasMany(InvestmentSeat::class);
    }

    public function interestRequests(): HasMany
    {
        return $this->hasMany(InterestRequest::class);
    }

    public function companyInvestorInterestRequestsSent(): HasMany
    {
        return $this->hasMany(CompanyInvestorInterestRequest::class, 'company_id');
    }

    public function companyInvestorInterestRequestsReceived(): HasMany
    {
        return $this->hasMany(CompanyInvestorInterestRequest::class, 'investor_id');
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

    public function isInvestor(): bool
    {
        return $this->role === UserRole::Investor;
    }

    public function isCompany(): bool
    {
        return $this->role === UserRole::Advertiser;
    }

    protected function applyColumnFilter(Builder $query, string $column, $value, ?string $op = null): void
    {
        if ($column === 'name') {
            $like = '%' . $value . '%';
            $query->where(function (Builder $q) use ($like): void {
                $q->where('first_name', 'like', $like)
                    ->orWhere('last_name', 'like', $like)
                    ->orWhere('display_name', 'like', $like)
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", [$like]);
            });

            return;
        }

        if ($column === 'phone') {
            $normalizedValue = preg_replace('/\s+/', '', (string) $value);
            $query->where(function (Builder $q) use ($normalizedValue): void {
                $q->where('phone', 'like', '%' . $normalizedValue . '%')
                    ->orWhereRaw(
                        "REPLACE(CONCAT(COALESCE(country_code, ''), COALESCE(phone, '')), ' ', '') LIKE ?",
                        ['%' . $normalizedValue . '%']
                    );
            });

            return;
        }

        if ($column === 'email_verified') {
            if ((string) $value === '1') {
                $query->whereNotNull('email_verified_at');
            } elseif ((string) $value === '0') {
                $query->whereNull('email_verified_at');
            }

            return;
        }

        $this->applyColumnFilterTrait($query, $column, $value, $op);
    }
}
