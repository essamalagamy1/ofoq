<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class SubjectEnum extends Enum
{
    const Science = 'science';
    const Math = 'math';
    const Arabic = 'arabic';

    public function title(): string
    {
        return match ($this->value) {
            self::Science => __('lang.science'),
            self::Math => __('lang.math'),
            self::Arabic => __('lang.arabic'),
            default => 'Unknown',
        };
    }
}
