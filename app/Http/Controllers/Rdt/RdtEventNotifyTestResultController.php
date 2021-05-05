<?php

namespace App\Http\Controllers\Rdt;

use App\Entities\RdtEvent;
use App\Entities\RdtInvitation;
use App\Http\Controllers\Controller;
use App\Notifications\TestResult;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Auth;

class RdtEventNotifyTestResultController extends Controller
{
    public function __invoke(Request $request, RdtEvent $rdtEvent)
    {
        Gate::authorize('notify-participants');

        $target        = $request->input('target');
        $invitationIds = $request->input('invitations_ids');
        $invitations   = $rdtEvent->invitations;

        if ($target === 'SELECTED') {
            $invitations = $rdtEvent->invitations()
                    ->whereIn('id', $invitationIds)
                    ->whereNotNull('lab_result_type')
                    ->get();
        }

        foreach ($invitations as $invitation) {
            $this->notifyEachInvitation($invitation);
        }

        return response()->json(['message' => 'OK']);
    }

    protected function notifyEachInvitation(RdtInvitation $invitation)
    {
        $invitation->applicant->notify(new TestResult());
        $invitation->notified_result_at = Carbon::now();
        $invitation->notified_result_by = Auth::user()->id;
        $invitation->save();

        Log::info('NOTIFY_TEST_RESULT', [
            'applicant'  => $invitation->applicant,
            'invitation' => $invitation,
            'result'     => $invitation->lab_result_type
        ]);
    }
}
