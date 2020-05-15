<?php

namespace App\Http\Controllers\Rdt;

use App\Entities\RdtApplicant;
use App\Http\Controllers\Controller;
use App\Http\Requests\Rdt\RdtCheckStatusRequest;
use App\Http\Resources\RdtApplicantResource;

class RdtCheckinController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \App\Http\Requests\Rdt\RdtCheckStatusRequest  $request
     * @return \App\Http\Resources\RdtApplicantResource
     */
    public function __invoke(RdtCheckStatusRequest $request)
    {
        $registrationCode = $request->input('registration_code');

        $applicant              = RdtApplicant::where('registration_code', $registrationCode)->firstOrFail();
        $applicant->attended_at = now();
        $applicant->save();

        return new RdtApplicantResource($applicant);
    }
}
