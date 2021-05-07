<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;

class ExportNotContainDataException extends Exception
{
    public function validationException()
    {
        return ValidationException::withMessages([
            'export_failed' => __('response.export_failed')
        ]);
    }
}
