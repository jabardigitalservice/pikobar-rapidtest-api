<?php

namespace App\Enums;

use Spatie\Enum\Enum;

class RegistrationType extends Enum
{
    public static function rujukan(): RegistrationType
    {
        return new class () extends RegistrationType
        {
            public function getValue(): string
            {
                return 'rujukan';
            }
        };
    }

    public static function mandiri(): RegistrationType
    {
        return new class () extends RegistrationType
        {
            public function getValue(): string
            {
                return 'mandiri';
            }
        };
    }
}
