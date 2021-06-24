<?php

namespace App\Rules;

use App\Enums\LabResultType;
use Illuminate\Contracts\Validation\Rule;

class LabResultRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return in_array($value, LabResultType::getValues());
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Lengkapi hasil tes dalam Bahasa Inggris.';
    }
}
