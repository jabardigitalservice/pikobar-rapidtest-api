<?php

namespace App\Http\Controllers\Checkin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Check\ApplicantCheckProfileRequest;
use App\Entities\RdtApplicant;
use App\Http\Resources\ApplicantProfileResource;

class ApplicantCheckProfileController extends Controller
{
    public function __invoke(ApplicantCheckProfileRequest $request)
    {
        $rdtApplicant = RdtApplicant::where('registration_code', $request->registration_code)->firstOrFail();
        return new ApplicantProfileResource($rdtApplicant);
    }
}
