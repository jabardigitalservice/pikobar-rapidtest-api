<?php

namespace App\Http\Controllers\Checkin;

use App\Entities\RdtEvent;
use App\Entities\RdtInvitation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\Http\Requests\Rdt\RdtEventCheckRequest;
use App\Http\Resources\RdtApplicantInvitationResource;

class RdtEventParticipantsController extends Controller
{
    public function __invoke(RdtEventCheckRequest $request)
    {
        $eventCode = $request->input('event_code');
        $perPage   = $request->input('per_page', 50);
        $keyword   = $request->input('keyword');

        $event = RdtEvent::where('event_code', $eventCode)->firstOrFail();
        $invitations = RdtInvitation::leftJoin(
            'rdt_applicants',
            'rdt_applicants.id',
            '=',
            'rdt_invitations.rdt_applicant_id'
        )
        ->select(
            'rdt_applicants.name',
            'rdt_applicants.registration_code',
            'rdt_invitations.lab_code_sample',
            'rdt_applicants.created_at',
            'rdt_invitations.attended_at'
        )
        ->where('rdt_invitations.rdt_event_id', $event->id)
        ->orderBy('rdt_applicants.name');

        if ($keyword) {
            $invitations->where('rdt_applicants.name', 'like', "%$keyword%");
        }

        return RdtApplicantInvitationResource::collection($invitations->paginate($perPage));
    }
}
