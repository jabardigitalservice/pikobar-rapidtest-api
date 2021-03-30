<?php

namespace App\Http\Controllers\Rdt;

use App\Http\Controllers\Controller;
use App\Entities\RdtInvitation;

class RdtInvitationResetController extends Controller
{
    public function __invoke(RdtInvitation $rdtInvitation)
    {
        $rdtInvitation->update(['lab_code_sample' => null ,'attended_at' => null,'attend_location' => null]);
        return response()->json(['message' => 'OK']);
    }
}
