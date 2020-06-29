<?php

namespace App\Entities;

use UrlSigner;
use App\Enums\RdtApplicantStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\URL;
use Spatie\Enum\Laravel\HasEnums;

/**
 * @property string $registration_code
 * @property string $province_code
 * @property int $status
 */
class RdtApplicant extends Model
{
    use HasEnums, SoftDeletes;

    protected $fillable = [
        'nik', 'name', 'address', 'province_code', 'city_code', 'district_code', 'village_code',
        'email', 'phone_number', 'gender', 'birth_date', 'occupation_type', 'occupation_name', 'workplace_name',
        'symptoms', 'symptoms_notes', 'symptoms_interaction', 'symptoms_activity','people_status',
        'latitude', 'longitude', 'pikobar_session_id', 'pikobar_user_id',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'approved_at',
        'invited_at',
        'attended_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'symptoms'          => 'array',
        'symptoms_activity' => 'array',
    ];

    protected $enums = [
        'status' => RdtApplicantStatus::class,
    ];

    /**
     * @deprecated karena 1 orang bisa ikut test lebih dari 1x
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function event()
    {
        return $this->belongsTo(RdtEvent::class, 'rdt_event_id');
    }

    public function invitations()
    {
        return $this->hasMany(RdtInvitation::class, 'rdt_applicant_id');
    }

    public function labResult()
    {
        return $this->hasOne(RdtLabResult::class, 'rdt_applicant_id');
    }

    public function getQrCodeUrlAttribute()
    {
        $url = URL::route(
            'registration.qrcode',
            ['registration_code' => $this->attributes['registration_code']]
        );

        return UrlSigner::sign($url);
    }
}
