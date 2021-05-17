<?php

namespace App\Http\Controllers\Rdt;

use App\Entities\RdtEvent;
use App\Entities\RdtInvitation;
use App\Http\Controllers\Controller;
use App\Http\Requests\EventNotifyTestResultRequest;
use App\Notifications\TestResult;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Auth;
use Illuminate\Validation\ValidationException;

class RdtEventNotifyTestResultController extends Controller
{
    public function __invoke(EventNotifyTestResultRequest $request, RdtEvent $rdtEvent)
    {
        Gate::authorize('notify-participants');

        $invitationIds = $request->input('invitations_ids');
        $invitations   = $rdtEvent->invitations;

        $invitations = $rdtEvent->invitations()
                ->whereIn('id', $invitationIds)
                ->whereNotNull('lab_result_type')
                ->get();

        $isEmptyBlast = count($invitations) === 0;

        // throw error if invitation is empty
        if ($isEmptyBlast) {
            throw ValidationException::withMessages([
                'blast_failed' => __('response.blast_failed')
            ]);
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
