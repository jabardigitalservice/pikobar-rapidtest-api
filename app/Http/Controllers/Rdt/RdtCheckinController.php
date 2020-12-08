<?php

namespace App\Http\Controllers\Rdt;

use App\Entities\RdtApplicant;
use App\Entities\RdtEvent;
use App\Entities\RdtInvitation;
use App\Enums\RdtApplicantStatus;
use App\Events\Rdt\ApplicantEventCheckin;
use App\Http\Controllers\Controller;
use App\Http\Requests\Rdt\RdtCheckinRequest;
use App\Http\Resources\RdtApplicantResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class RdtCheckinController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \App\Http\Requests\Rdt\RdtCheckinRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(RdtCheckinRequest $request)
    {
        Log::info('APPLICANT_EVENT_CHECKIN_REQUEST', $request->all());

        $eventCode        = $request->input('event_code');
        $registrationCode = $request->input('registration_code');
        $labCodeSample    = $request->input('lab_code_sample');
        $attendLocation   = $request->input('location');

        /**
         * @var RdtEvent $event
         */
        $event = RdtEvent::where('event_code', $eventCode)->firstOrFail();

        // Pastikan tidak bisa checkin setelah tanggal selesai
        // Beri tambahan extra 12 jam
        if ($event->end_at->addHours(12)->isPast()) {
            return $this->responseFailedEventPast($event);
        }

        /**
         * @var RdtInvitation $invitation
         */
        $invitation = RdtInvitation::where('registration_code', $registrationCode)
            ->where('rdt_event_id', $event->id)
            ->first();

        if ($invitation === null) {
            $applicant = RdtApplicant::where('registration_code', $registrationCode)->firstOrFail();
            return $this->responseApplicantEventCheckinNotInvited($eventCode, $applicant, $invitation);
        }

        if ($invitation->attended_at !== null) {
            return $this->responseFailedAlreadyCheckin($event, $invitation);
        }

        // Make sure lab code sample doesn't duplicate
        $labCodeSampleExisting = RdtInvitation::where('rdt_event_id', $event->id)
            ->where('lab_code_sample', $request->input('lab_code_sample'))
            ->first();

        if ($labCodeSampleExisting !== null) {
            return $this->responseFailedDuplicateLabCode($event, $invitation, $labCodeSample);
        }

        $invitation->lab_code_sample = $labCodeSample;
        $invitation->attend_location = $attendLocation;
        $invitation->attended_at     = now();
        $invitation->save();

        $applicant = $invitation->applicant;

        event(new ApplicantEventCheckin($applicant, $invitation));

        return new RdtApplicantResource($applicant);
    }

    /**
     * @param \App\Entities\RdtEvent $event
     * @return \Illuminate\Http\JsonResponse
     */
    protected function responseFailedEventPast(RdtEvent $event)
    {
        Log::info('APPLICANT_EVENT_CHECKIN_FAILED_PAST', ['event_code' => $event->event_code]);

        $frontendTimezone = config('app.timezone_frontend');

        $endAt = $event->end_at->setTimezone($frontendTimezone);

        return response()->json([
            'error'   => 'EVENT_PAST',
            'message' => "Kode Event: {$event->event_code} - {$event->event_name} sudah berakhir pada {$endAt}.
            Periksa kembali input Kode Event.",
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @param \App\Entities\RdtEvent $event
     * @param \App\Entities\RdtInvitation $invitation
     * @return \Illuminate\Http\JsonResponse
     */
    protected function responseFailedAlreadyCheckin(RdtEvent $event, RdtInvitation $invitation)
    {
        Log::info('APPLICANT_EVENT_CANNOT_CHECKIN_ALREADY', [
            'event_code'        => $event->event_code,
            'registration_code' => $invitation->registration_code,
            'invitation'        => $invitation,
        ]);

        return response()->json([
            'error'   => 'ALREADY_CHECKIN',
            'message' => 'Nomor Pendaftar sudah digunakan untuk checkin pada event ini.',
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @param \App\Entities\RdtEvent $event
     * @param \App\Entities\RdtInvitation $invitation
     * @param $labCodeSample
     * @return \Illuminate\Http\JsonResponse
     */
    protected function responseFailedDuplicateLabCode(RdtEvent $event, RdtInvitation $invitation, $labCodeSample)
    {
        Log::info('APPLICANT_EVENT_CANNOT_CHECKIN_ALREADY_LAB_CODE_SAMPLE', [
            'event_code'        => $event->event_code,
            'registration_code' => $invitation->registration_code,
            'lab_code_sample'   => $labCodeSample,
            'invitation'        => $invitation,
        ]);

        return response()->json([
            'error'   => 'ALREADY_USED_LAB_CODE_SAMPLE',
            'message' => 'Kode Sampel Lab sudah digunakan untuk checkin pada event ini.',
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }


    protected function responseApplicantEventCheckinNotInvited($eventCode, $applicant, $invitation)
    {
        Log::info('APPLICANT_EVENT_CHECKIN_NOT_INVITED', [
            'event_code' => $eventCode,
            'applicant'  => $applicant,
            'invitation' => $invitation,
        ]);

        return response()->json([
            'error'   => 'APPLICANT_NOT_INVITED_AT_THIS_EVENT',
            'message' => 'peserta tidak terdaftar pada event ini.',
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
