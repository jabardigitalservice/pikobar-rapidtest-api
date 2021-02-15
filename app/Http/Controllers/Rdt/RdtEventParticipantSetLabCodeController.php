<?php

namespace App\Http\Controllers\Rdt;

use App\Entities\RdtInvitation;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;

class RdtEventParticipantSetLabCodeController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        /**
         * @var RdtInvitation $rdtInvitation
         */
        $rdtInvitation = RdtInvitation::findOrFail($request->input('rdt_invitation_id'));

        // validasi if kode sample sudah digunakan
        if (RdtInvitation::query()->where('lab_code_sample', $request->input('lab_code_sample'))->count() > 0) {
            return response()->json([
                'message' => 'Kode sample sudah ada atau sudah digunakan oleh peserta lain.',
            ], HttpResponse::HTTP_BAD_REQUEST);
        }
        $rdtInvitation->lab_code_sample = $request->input('lab_code_sample');

        if ($rdtInvitation->attended_at === null) {
            $rdtInvitation->attended_at = Carbon::now();
        }
        if ($rdtInvitation->attend_location === null) {
            $rdtInvitation->attend_location = $rdtInvitation->event->event_location;
        }
        $rdtInvitation->save();

        return response()->json(['status' => 'OK']);
    }
}
