<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventNotifyTestResultRequest extends FormRequest
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
            'invitations_ids' => 'required|array|min:1',
            'invitations_ids.*' => 'exists:rdt_invitations,id'
        ];
    }
}
