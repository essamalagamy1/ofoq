<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class SuggestionStatusEnum extends Enum
{
    const Pending = 'pending';
    const Approved = 'approved';
    const Rejected = 'rejected';

    public function title(): string
    {
        return match ($this->value) {
            self::Pending => __('lang.pending'),
            self::Approved => __('lang.approved'),
            self::Rejected => __('lang.rejected'),
            default => 'Unknown',
        };
    }

    public function color(): string
    {
        return match ($this->value) {
            self::Pending => 'yellow-500',
            self::Approved => 'green-500',
            self::Rejected => 'red-500',
            default => 'gray-500',
        };
    }
}
