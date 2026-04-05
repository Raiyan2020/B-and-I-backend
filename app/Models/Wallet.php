<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wallet extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'walletable_type',
        'walletable_id',
        'available_balance',
        'reserved_balance',
    ];

    protected $casts = [
        'available_balance' => 'decimal:2',
        'reserved_balance' => 'decimal:2',
    ];

    /**
     * Get the parent walletable model (User, Admin, etc.).
     */
    public function walletable()
    {
        return $this->morphTo();
    }

    /**
     * Get all wallet transactions.
     */
    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    /**
     * Get total balance (available + reserved).
     */
    public function getTotalBalanceAttribute()
    {
        return $this->available_balance + $this->reserved_balance;
    }
}
