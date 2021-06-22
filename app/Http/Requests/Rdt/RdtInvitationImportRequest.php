<?php

namespace App\Http\Requests\Rdt;

use App\Rules\ExcelExtensionRule;
use Illuminate\Foundation\Http\FormRequest;

class RdtInvitationImportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'file' => new ExcelExtensionRule(optional($this->file)->getClientOriginalExtension())
        ];
    }
}
