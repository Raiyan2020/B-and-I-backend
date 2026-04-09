<?php

namespace App\Models;

use App\Enums\OpportunityGoal;
use App\Enums\OpportunityStatus;
use App\Traits\FilterTrait;
use App\Traits\UploadTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Opportunity extends BaseModel
{
    use HasFactory, FilterTrait, SoftDeletes, UploadTrait;

    protected $fillable = [
        'opportunity_number',
        'image',
        'user_id',
        'category_id',
        'reviewed_by_admin_id',
        'goal',
        'status',
        'contact_name',
        'contact_phone',
        'contact_email',
        'owner_name',
        'admin_company_name',
        'license_number',
        'company_name',
        'business_age_years',
        'investment_required',
        'business_stage',
        'sale_percentage',
        'legal_entity',
        'financial_status',
        'investment_reason',
        'full_description',
        'review_note',
        'reviewed_at',
    ];

    protected $casts = [
        'goal'                => OpportunityGoal::class,
        'status'              => OpportunityStatus::class,
        'business_age_years'  => 'integer',
        'investment_required' => 'decimal:3',
        'sale_percentage'     => 'decimal:2',
        'reviewed_at'         => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'reviewed_by_admin_id');
    }
}
