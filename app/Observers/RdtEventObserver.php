<?php

namespace App\Observers;

use App\Entities\RdtEvent;
use Illuminate\Support\Facades\DB;
use PragmaRX\Random\Random;
use Auth;

class RdtEventObserver
{
    /**
     * Handle the rdt event "creating" event.
     *
     * @param  \App\Entities\RdtEvent  $rdtEvent
     * @return void
     */
    public function creating(RdtEvent $rdtEvent)
    {
        $rdtEvent->province_code = '32';
        $rdtEvent->created_by    = Auth::user()->id;
        $rdtEvent->event_code    = $this->generateUniqueEventCode();
    }

    public function updating(RdtEvent $rdtEvent)
    {
        $rdtEvent->updated_by    = Auth::user()->id;
    }

    protected function generateUniqueEventCode()
    {
        while (true) {
            $code = $this->generateCode();

            $doesCodeExist = DB::table('rdt_events')
                ->where('event_code', $code)
                ->exists();

            if (! $doesCodeExist) {
                return $code;
            }
        }
    }

    protected function generateCode()
    {
        $random = new Random();

        return $random->uppercase()->size(6)->get();
    }
}
