<?php

namespace App\Models;

use App\Enums\WalletTransactionTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'description',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'metadata' => 'array',
    ];

    /**
     * Get the type attribute as enum.
     *
     * @param mixed $value
     * @return WalletTransactionTypeEnum|null
     */
    public function getTypeAttribute($value)
    {
        if ($value === null) {
            return null;
        }
        
        // Convert to int if it's a string
        $intValue = is_string($value) ? (int) $value : $value;
        
        // Check if value is valid enum value
        if (!in_array($intValue, WalletTransactionTypeEnum::values())) {
            // Return default (CHARGE) if invalid value
            return WalletTransactionTypeEnum::CHARGE;
        }
        
        return WalletTransactionTypeEnum::from($intValue);
    }

    /**
     * Set the type attribute.
     *
     * @param mixed $value
     * @return void
     */
    public function setTypeAttribute($value)
    {
        if ($value instanceof WalletTransactionTypeEnum) {
            $this->attributes['type'] = $value->value;
        } elseif (is_numeric($value)) {
            $intValue = (int) $value;
            // Validate that the value is a valid enum value
            if (in_array($intValue, WalletTransactionTypeEnum::values())) {
                $this->attributes['type'] = $intValue;
            } else {
                // Default to CHARGE if invalid
                $this->attributes['type'] = WalletTransactionTypeEnum::CHARGE->value;
            }
        } else {
            $this->attributes['type'] = $value;
        }
    }

    /**
     * Get the wallet that owns this transaction.
     */
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}
