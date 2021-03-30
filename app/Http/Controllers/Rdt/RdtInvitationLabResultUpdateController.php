<?php

namespace App\Http\Controllers\Rdt;

use App\Entities\RdtInvitation;
use App\Http\Controllers\Controller;
use App\Http\Requests\Rdt\RdtInvitationLabResultUpdateRequest as Request;

class RdtInvitationLabResultUpdateController extends Controller
{
    public function __invoke(RdtInvitation $rdtInvitation, Request $request)
    {
        $rdtInvitation->update($request->only('lab_result_type'));
        return response()->json(['message' => 'OK']);
    }
}
