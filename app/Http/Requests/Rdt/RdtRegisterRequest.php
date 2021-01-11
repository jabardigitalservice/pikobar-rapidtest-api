<?php

namespace App\Http\Requests\Rdt;

use App\Enums\Gender;
use App\Enums\PersonCaseStatusEnum;
use App\Rules\NikRule;
use App\Rules\RecaptchaRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Enum\Laravel\Rules\EnumValueRule;

class RdtRegisterRequest extends FormRequest
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
            'g-recaptcha-response' => ['required', new RecaptchaRule()],
            'name'                 => ['required', 'min:3'],
            'nik'                  => ['required' , new NikRule()],
            'address'              => 'required',
            'city_code'            => ['required', 'exists:areas,code_kemendagri'],
            'district_code'        => ['required', 'exists:areas,code_kemendagri'],
            'village_code'         => ['required', 'exists:areas,code_kemendagri'],
            'email'                => ['nullable', 'email'],
            'phone_number'         => ['required', 'min:10','max:13'],
            'gender'               => ['required', new EnumValueRule(Gender::class)],
            'person_status'        => ['required', new EnumValueRule(PersonCaseStatusEnum::class)],
            'birth_date'           => ['required', 'date'],
            'occupation_type'      => ['required', 'integer'],
            'workplace_name'       => 'required',
            'symptoms'             => 'required',
            'symptoms_notes'       => 'required',
            'have_interacted'      => 'sometimes|required',
            'congenital_disease'   => 'sometimes|required',
            'birth_place'          => 'sometimes|required|max:60'
        ];
    }
}
