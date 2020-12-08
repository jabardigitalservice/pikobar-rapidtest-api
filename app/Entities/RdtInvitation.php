<?php

namespace App\Entities;

use App\Enums\LabResultType;
use App\Enums\TestType;
use Illuminate\Database\Eloquent\Model;
use Spatie\Enum\Laravel\HasEnums;

/**
 * @property string $rdt_applicant_id
 * @property string $rdt_event_id
 * @property string $rdt_event_schedule_id
 * @property string $registration_code
 * @property string $attend_location
 * @property string $test_type
 * @property \App\Enums\LabResultType $lab_result_type
 * @property string $lab_code_sample
 * @property \App\Entities\RdtApplicant $applicant
 * @property \App\Entities\RdtEvent $event
 * @property \App\Entities\RdtEventSchedule $schedule
 * @property \Illuminate\Support\Carbon $confirmed_at
 * @property \Illuminate\Support\Carbon $attended_at
 * @property \Illuminate\Support\Carbon $result_at
 * @property \Illuminate\Support\Carbon $notified_at
 * @property \Illuminate\Support\Carbon $notified_result_at
 */
class RdtInvitation extends Model
{
    use HasEnums;

    protected $fillable = [
        'rdt_event_id',
        'rdt_applicant_id',
        'attend_location',
        'test_type',
        'attended_at',
        'lab_result_type',
        'lab_code_sample'
    ];

    protected $enums = [
        'lab_result_type' => LabResultType::class . ':nullable',
        'test_type' => TestType::class . ':nullable',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'attended_at',
        'notified_at',
        'notified_result_at',
        'confirmed_at',
        'result_at',
    ];

    public function applicant()
    {
        return $this->belongsTo(RdtApplicant::class, 'rdt_applicant_id');
    }

    public function event()
    {
        return $this->belongsTo(RdtEvent::class, 'rdt_event_id');
    }

    public function schedule()
    {
        return $this->belongsTo(RdtEventSchedule::class, 'rdt_event_schedule_id');
    }
}
