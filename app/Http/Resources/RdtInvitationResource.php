<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RdtInvitationResource extends JsonResource
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
            'id'                    => $this->id,
            'applicant'             => new RdtApplicantResource($this->whenLoaded('applicant')),
            'event'                 => new RdtEventResource($this->whenLoaded('event')),
            'schedule'              => new RdtEventScheduleResource($this->whenLoaded('schedule')),
            'rdt_applicant_id'      => $this->rdt_applicant_id,
            'event_id'              => $this->rdt_event_id,
            'rdt_event_schedule_id' => $this->rdt_event_schedule_id,
            'attend_location'       => $this->attend_location,
            'test_type'             => $this->test_type,
            'lab_code_sample'       => $this->lab_code_sample,
            'lab_result_type'       => $this->lab_result_type,
            'notified_at'           => $this->notified_at,
            'attended_at'           => $this->attended_at,
            'result_at'             => $this->result_at,
            'created_at'            => $this->created_at,
        ];
    }
}
