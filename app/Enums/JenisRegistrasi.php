<?php

namespace App\Enums;

use Spatie\Enum\Enum;

class JenisRegistrasi extends Enum
{
    public static function rujukan(): JenisRegistrasi
    {
        return new class () extends JenisRegistrasi
        {
            public function getValue(): string
            {
                return 'rujukan';
            }
        };
    }

    public static function mandiri(): JenisRegistrasi
    {
        return new class () extends JenisRegistrasi
        {
            public function getValue(): string
            {
                return 'mandiri';
            }
        };
    }
}
