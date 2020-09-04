<?php

namespace App\Http\Requests\Checkin;

use Illuminate\Foundation\Http\FormRequest;

class CheckNikIsAlreadyRequest extends FormRequest
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
            'nik' => 'required|unique:rdt_applicants'
        ];
    }
}
