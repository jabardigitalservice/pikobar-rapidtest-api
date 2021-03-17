<?php

namespace App\Entities;

use App\Enums\JenisRegistrasi;
use App\Enums\LabResultType;
use App\Enums\RdtEventStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Enum\Laravel\HasEnums;

/**
 * @property int $id
 * @property \Carbon\Carbon $start_at
 * @property \Carbon\Carbon $end_at
 * @property string $event_name
 * @property string $event_code
 * @property \App\Entities\RdtEventSchedule[] $schedules
 * @property \App\Entities\RdtInvitation[] $invitations
 * @property string $province_code
 * @property string $host_name
 */
class RdtEvent extends Model
{
    use HasEnums;
    use SoftDeletes;

    protected $enums = [
        'status' => RdtEventStatus::class,
        'jenis_registrasi' => JenisRegistrasi::class,
    ];

    protected $fillable = [
        'event_name',
        'event_location',
        'host_type',
        'host_name',
        'city_code',
        'start_at',
        'end_at',
        'status',
        'jenis_registrasi',
        'created_by',
        'updated_by'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'start_at',
        'end_at',
    ];

    public function city()
    {
        return $this->belongsTo(Area::class, 'city_code', 'code_kemendagri');
    }

    public function schedules()
    {
        return $this->hasMany(RdtEventSchedule::class, 'rdt_event_id');
    }

    public function invitations()
    {
        return $this->hasMany(RdtInvitation::class, 'rdt_event_id');
    }

    public function attendees()
    {
        return $this->hasMany(RdtInvitation::class, 'rdt_event_id')->whereNotNull('attended_at');
    }

    public function attendeesResult()
    {
        return $this->hasMany(RdtInvitation::class, 'rdt_event_id')->whereNotNull('lab_result_type');
    }
}
