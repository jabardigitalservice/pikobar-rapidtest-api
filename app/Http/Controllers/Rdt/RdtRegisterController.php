<?php

namespace App\Http\Controllers\Rdt;

use App\Entities\RdtApplicant;
use App\Enums\RdtApplicantStatus;
use App\Events\Rdt\ApplicantRegistered;
use App\Http\Controllers\Controller;
use App\Http\Requests\Rdt\RdtRegisterRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use UrlSigner;
use App\Entities\RdtEvent;
use Carbon\Carbon;

class RdtRegisterController extends Controller
{

    /**
     * Handle the incoming request.
     *
     * @param  \App\Http\Requests\Rdt\RdtRegisterRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(RdtRegisterRequest $request)
    {
        Log::info('APPLICANT_REGISTER_REQUEST', $request->all());

        $applicant         = new RdtApplicant();
        $applicant->status = RdtApplicantStatus::NEW();
        $applicant->fill($request->all());
        $applicant->save();

        $event = RdtEvent::where('event_code', $request->pikobar_session_id)->first();

        event(new ApplicantRegistered($applicant));

        $url = URL::route(
            'registration.download',
            ['registration_code' => $applicant->registration_code]
        );
        return response()->json([
            'name'              => $applicant->name,
            'status'            => $applicant->status,
            'registration_code' => $applicant->registration_code,
            'event_start_at'    => optional($event)->start_at,
            'event_end_at'      => optional($event)->end_at,
            'event_location'    => optional($event)->event_location,
            'qr_code'           => $applicant->QrCodeUrl,
            'download_url'      => UrlSigner::sign($url),
        ]);
    }
}
