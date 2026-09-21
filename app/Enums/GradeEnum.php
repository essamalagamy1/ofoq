<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class GradeEnum extends Enum
{
    const Third = 3;

    const Fourth = 4;

    const Fifth = 5;

    const Sixth = 6;

    public function title(): string
    {
        return match ($this->value) {
            self::Third => __('lang.grade_third'),
            self::Fourth => __('lang.grade_fourth'),
            self::Fifth => __('lang.grade_fifth'),
            self::Sixth => __('lang.grade_sixth'),
            default => 'Unknown',
        };
    }
}
