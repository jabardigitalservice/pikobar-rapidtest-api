<?php

namespace App\Http\Requests\Check;

use Illuminate\Foundation\Http\FormRequest;

class ApplicantCheckProfileRequest extends FormRequest
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
            'registration_code' => 'required'
        ];
    }
}
