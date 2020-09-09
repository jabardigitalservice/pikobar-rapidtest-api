<?php

namespace App\Exports;

use App\Entities\RdtInvitation;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class RdtInvitationExcel implements FromView, ShouldAutoSize
{
    protected $rdtEventId;

    public function __construct($rdtEventId)
    {
        $this->rdtEventId = $rdtEventId;
    }
    public function view(): View
    {
        return view('excel.rdt_invitation_excel_f1', [
            'rdtinvitations' => RdtInvitation::where('rdt_event_id', $this->rdtEventId)->get()
        ]);
    }
}
