<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;
use Spatie\UrlSigner\Laravel\UrlSignerFacade as UrlSigner;

class RdtApplicantResource extends JsonResource
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
            'registration_code' => $this->registration_code,
            'name'              => $this->name,
            'qrcode'            => $this->qrCodeUrl,
            'registration_pdf'  => $this->getRegistrationAttachmentPdf(),
            'approved_at'       => $this->approved_at,
            'invitations'       => RdtInvitationResource::collection($this->whenLoaded('invitations')),
            'status'            => $this->status,
            'is_profile_complete' => $this->checkIsProfileComplete(),
            $this->mergeWhen($request->user(), $this->getProtectedAttributes()),
        ];
    }

    protected function checkIsProfileComplete()
    {
        $mandatoryFields = ['nik', 'phone_number', 'address', 'birth_date', 'birth_place'];
        foreach ($mandatoryFields as $field) {
            if ($this->$field === null) {
                return false;
            }
        }

        return true;
    }

    protected function getProtectedAttributes()
    {
        return [
            'id'                   => $this->id,
            'nik'                  => $this->nik,
            'gender'               => $this->gender,
            'birth_place'          => $this->birth_place,
            'birth_date'           => optional($this->birth_date)->toDateString(),
            'age'                  => optional($this->birth_date)->age,
            'city'                 => new AreaResource($this->whenLoaded('city')),
            'district'             => new AreaResource($this->whenLoaded('district')),
            'village'              => new AreaResource($this->whenLoaded('village')),
            'city_code'            => $this->city_code,
            'district_code'        => $this->district_code,
            'village_code'         => $this->village_code,
            'address'              => $this->address,
            'phone_number'         => $this->phone_number,
            'workplace_name'       => $this->workplace_name,
            'occupation_type'      => $this->occupation_type,
            'occupation_type_name' => optional($this->occupation)->name,
            'occupation_name'      => $this->occupation_name,
            'is_pns'               => $this->is_pns,
            'symptoms'             => $this->symptoms,
            'symptoms_interaction' => $this->symptoms_interaction,
            'symptoms_notes'       => $this->symptoms_notes,
            'symptoms_activity'    => $this->symptoms_activity,
            'person_status'        => $this->person_status,
            'pikobar_session_id'   => $this->pikobar_session_id,
            'created_at'           => $this->created_at,
            'updated_at'           => $this->updated_at,
            'city_visited'         => $this->city_visited,
            'congenital_disease'   => $this->congenital_disease

        ];
    }

    protected function getRegistrationAttachmentPdf()
    {
        $url = URL::route(
            'registration.download',
            ['registration_code' => $this->registration_code]
        );

        return UrlSigner::sign($url);
    }
}
