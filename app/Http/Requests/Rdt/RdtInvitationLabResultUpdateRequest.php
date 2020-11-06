<?php

namespace App\Http\Requests\Rdt;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\LabResultType;
use Spatie\Enum\Laravel\Rules\EnumValueRule;

class RdtInvitationLabResultUpdateRequest extends FormRequest
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
            'lab_result_type' => ['required', new EnumValueRule(LabResultType::class)]
        ];
    }
}
