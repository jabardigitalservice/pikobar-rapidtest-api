<?php

namespace App\Http\Controllers\Rdt;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exports\RdtInvitationExcel;
use Maatwebsite\Excel\Facades\Excel;
use App\Entities\RdtEvent;

class RdtInvitationExportExcelF1Controller extends Controller
{
    public function __invoke($id)
    {
        $rdtEvent = RdtEvent::findOrFail($id);
        $fileName = str_replace(" ", "-", $rdtEvent->event_name);
        return Excel::download(new RdtInvitationExcel($id), $fileName . '.xlsx');
    }
}
