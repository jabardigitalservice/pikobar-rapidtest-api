<?php

namespace App\Http\Requests\Rdt;

use App\Enums\Gender;
use App\Enums\RdtApplicantStatus;
use App\Rules\NikRule;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\Enum\Laravel\Rules\EnumValueRule;

class RdtApplicantStoreRequest extends FormRequest
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
            'name'                 => ['required', 'min:3'],
            'nik'                  => ['required', new NikRule()],
            'address'              => 'required',
            'city_code'            => ['required', 'exists:areas,code_kemendagri'],
            'phone_number'         => 'required',
            'gender'               => ['required', new EnumValueRule(Gender::class)],
            'status'               => ['required', new EnumValueRule(RdtApplicantStatus::class)],
        ];
    }
}
