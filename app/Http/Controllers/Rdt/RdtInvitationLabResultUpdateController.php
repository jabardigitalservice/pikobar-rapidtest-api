<?php

namespace App\Http\Controllers\Rdt;

use App\Http\Controllers\Controller;
use App\Http\Requests\Rdt\RdtInvitationLabResultUpdateRequest as Request;
use App\Entities\RdtInvitation;

class RdtInvitationLabResultUpdateController extends Controller
{
    public function __invoke(RdtInvitation $rdtInvitation, Request $request)
    {
        $rdtInvitation->update($request->only('lab_result_type'));
        return response()->json(['message' => 'OK']);
    }
}
