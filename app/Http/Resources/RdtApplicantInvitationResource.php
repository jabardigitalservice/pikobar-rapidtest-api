<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RdtApplicantInvitationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'name'                  =>  $this->name,
            'registration_code'     =>  $this->registration_code,
            'lab_code_sample'       =>  $this->lab_code_sample,
            'created_at'            =>  $this->created_at,
            'attended_at'           =>  $this->attended_at
        ];
    }
}
