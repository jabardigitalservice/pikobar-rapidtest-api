<?php

namespace App\Http\Controllers\Rdt;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Entities\RdtInvitation;
use App\Http\Requests\Checkin\RdtCheckinBulkRequest;

class RdtCheckinBulkController extends Controller
{
    public function __invoke(RdtCheckinBulkRequest $request)
    {
        $data           = $request->data;
        $successSync    = [];
        $failedSync     = [];

        foreach ($data as $row) {
            $rdtInvitation = RdtInvitation::select('rdt_invitations.id', 'rdt_invitations.attended_at')
            ->join('rdt_events', 'rdt_events.id', 'rdt_invitations.rdt_event_id')
            ->where('rdt_invitations.registration_code', $row['registration_code'])
            ->where('rdt_events.event_code', $row['event_code'])
            ->first();

            if ($rdtInvitation != null && $rdtInvitation->attended_at == null) {
                $invitation = RdtInvitation::where('id', $rdtInvitation->id)->first();
                $invitation->attended_at     = $row['attended_at'];
                $invitation->lab_code_sample = $row['lab_code_sample'];
                $invitation->attend_location = $row['location'];
                $invitation->save();

                $successSync[] = $row['registration_code'];
            } else {
                $failedSync[] = [
                    'registration_code' =>  $row['registration_code'],
                    'message'           =>  'Gagal Melakukan Checkin'
                ];
            }
        }

        return response()->json(['succes' => $successSync,'failed' => $failedSync]);
    }
}
