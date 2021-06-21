<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ExcelExtensionRule implements Rule
{

    private $fileExtension;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($fileExtension)
    {
        $this->fileExtension = $fileExtension;
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
        $extension = $this->fileExtension ? strtolower($this->fileExtension) : $this->fileExtension;
        return in_array($extension, ['csv', 'xls', 'xlsx']);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.excel_ext');
    }
}
