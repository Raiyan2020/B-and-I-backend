<?php

namespace App\Models;

use App\Enums\ProfileUpdateRequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileUpdateRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'old_data',
        'new_data',
        'rejection_reason',
        'reviewed_by_admin_id',
        'reviewed_at',
    ];

    protected $casts = [
        'status' => ProfileUpdateRequestStatus::class,
        'old_data' => 'array',
        'new_data' => 'array',
        'reviewed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'reviewed_by_admin_id');
    }
}
