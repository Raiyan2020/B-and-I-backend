<?php

namespace App\Enums;

use App\Traits\EnumRetriever;

enum WalletTransactionTypeEnum: int
{
    use EnumRetriever;
    case CHARGE = 1;    // شحن
    case PAYMENT = 2;  // دفع
    case TRANSFER = 3; // تحويل

    /**
     * Get the label for the transaction type.
     *
     * @return string
     */
    public function label(): string
    {
        return self::getTranslatedName($this->value);
    }
}
