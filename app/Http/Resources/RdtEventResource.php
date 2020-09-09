<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RdtEventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'event_name'     => $this->event_name,
            'event_location' => $this->event_location,
            'event_reg_url'  => sprintf('%s/#/?sessionId=%s', config('app.client_url'), $this->event_code),
            'host_name'      => $this->host_name,
            'start_at'       => $this->start_at,
            'end_at'         => $this->end_at,
            'status'         => $this->status,
            'city'           => new AreaResource($this->whenLoaded('city')),
            $this->merge($this->getStatisticCountAttributes()),
            $this->mergeWhen($request->user(), $this->getProtectedAttributes()),
        ];
    }

    protected function getStatisticCountAttributes()
    {
        return [
            $this->mergeWhen($this->invitations_count !== null, [
                'invitations_count' => $this->invitations_count,
            ]),
            $this->mergeWhen($this->attendees_count !== null, [
                'attendees_count' => $this->attendees_count,
            ]),
            $this->mergeWhen($this->schedules_count !== null, [
                'schedules_count' => $this->schedules_count,
            ]),
            $this->mergeWhen($this->attendees_result_count !== null, [
                'attendees_result_count' => $this->attendees_result_count,
            ]),
        ];
    }

    protected function getProtectedAttributes()
    {
        return [
            'id'          => $this->id,
            'event_code'  => $this->event_code,
            'invitations' => RdtInvitationResource::collection($this->whenLoaded('invitations')),
            'schedules'   => RdtEventScheduleResource::collection($this->whenLoaded('schedules')),
            'created_at'  => $this->created_at,
            'updated_at'  => $this->updated_at,
        ];
    }
}
