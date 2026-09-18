<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class OptionEnum extends Enum
{
    const A = 'A';
    const B = 'B';
    const C = 'C';
    const D = 'D';

    public function title(): string
    {
        return match ($this->value) {
            self::A => __('lang.option_a'),
            self::B => __('lang.option_b'),
            self::C => __('lang.option_c'),
            self::D => __('lang.option_d'),
            default => 'Unknown',
        };
    }
}
