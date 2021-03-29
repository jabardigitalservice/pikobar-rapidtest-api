<?php

namespace App\Http\Controllers\Rdt;

use App\Entities\RdtInvitation;
use App\Http\Controllers\Controller;
use App\Http\Resources\RdtInvitationResource;

class RdtEventParticipantDetailController extends Controller
{
    public function __invoke($id)
    {
        $invitation = RdtInvitation::findOrFail($id);

        // add lazy eager loading applicant to provide city, district and village
        return new RdtInvitationResource($invitation->load([
            'applicant', 'applicant.city', 'applicant.district', 'applicant.village',
        ]));
    }
}
