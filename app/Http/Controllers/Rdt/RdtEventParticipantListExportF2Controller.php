<?php

namespace App\Http\Controllers\Rdt;

use App\Entities\RdtEvent;
use App\Exports\ParticipantListExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class RdtEventParticipantListExportF2Controller extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(RdtEvent $rdtEvent)
    {
        $filename = Str::slug('F2-' . $rdtEvent->event_name, '-') . '.xlsx';

        return Excel::download(new ParticipantListExport($rdtEvent), $filename);
    }
}
