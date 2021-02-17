<?php

namespace App\Enums;

use Spatie\Enum\Enum;

class SymptomsInteraction extends Enum
{
    public static function YES(): SymptomsInteraction
    {
        return new class () extends SymptomsInteraction {
            public function getValue(): string
            {
                return '0';
            }
        };
    }

    public static function NO(): SymptomsInteraction
    {
        return new class () extends SymptomsInteraction {
            public function getValue(): string
            {
                return '1';
            }
        };
    }

    public static function UNKNOWN(): SymptomsInteraction
    {
        return new class () extends SymptomsInteraction {
            public function getValue(): string
            {
                return 2;
            }
        };
    }
}
