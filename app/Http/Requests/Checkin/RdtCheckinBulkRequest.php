<?php

namespace App\Http\Requests\Checkin;

use Illuminate\Foundation\Http\FormRequest;

class RdtCheckinBulkRequest extends FormRequest
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
            'data'                      => 'required',
            'data.*.event_code'         => 'required',
            'data.*.registration_code'  => 'required',
            'data.*.lab_code_sample'    => 'required',
            'data.*.location'           => 'required',
            'data.*.attended_at'        => 'required',
             
        ];
    }
}
