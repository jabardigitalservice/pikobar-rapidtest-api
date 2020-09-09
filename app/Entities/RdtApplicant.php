<?php

namespace App\Entities;

use App\Enums\PersonCaseStatusEnum;
use App\Enums\RdtApplicantStatus;
use App\Enums\SymptomsInteraction;
use App\Enums\HaveInteractedEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\URL;
use Spatie\Enum\Laravel\HasEnums;
use UrlSigner;

/**
 * @property int $id
 * @property string $registration_code
 * @property string $pikobar_session_id
 * @property string $province_code
 * @property int $status
 * @property string $name
 * @property string $address
 * @property string $city_code
 * @property bool $is_pns
 * @property \Illuminate\Support\Carbon $birth_date
 * @property \App\Enums\SymptomsInteraction $symptoms_interaction
 * @property \App\Enums\UserStatus $person_status
 * @property string $occupation_name
 * @property string $workplace_name
 */
class RdtApplicant extends Model
{
    use Notifiable;
    use HasEnums;
    use SoftDeletes;

    protected $fillable = [
        'nik', 'name', 'address', 'province_code', 'city_code', 'district_code', 'village_code',
        'email', 'phone_number', 'gender', 'birth_date', 'occupation_type', 'occupation_name', 'workplace_name',
        'is_pns',
        'symptoms', 'symptoms_notes', 'symptoms_interaction', 'symptoms_activity', 'person_status', 'status',
        'latitude', 'longitude', 'pikobar_session_id', 'pikobar_user_id', 'city_visited' , 'congenital_disease' , 'have_interacted'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'birth_date',
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
        'congenital_disease' => 'array',
        'is_pns'            => 'boolean',
    ];

    protected $enums = [
        'symptoms_interaction' => SymptomsInteraction::class . ':nullable',
        'person_status'        => PersonCaseStatusEnum::class . ':nullable',
        'status'               => RdtApplicantStatus::class,
        'have_interacted'      => HaveInteractedEnum::class . ':nullable',
    ];

    public function city()
    {
        return $this->belongsTo(Area::class, 'city_code', 'code_kemendagri');
    }

    public function district()
    {
        return $this->belongsTo(Area::class, 'district_code', 'code_kemendagri');
    }

    public function village()
    {
        return $this->belongsTo(Area::class, 'village_code', 'code_kemendagri');
    }

    public function invitations()
    {
        return $this->hasMany(RdtInvitation::class, 'rdt_applicant_id');
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strtoupper($value);
    }

    public function setProvinceCodeAttribute($value)
    {
        $this->attributes['province_code'] = '32';
    }

    public function setAddressAttribute($value)
    {
        $this->attributes['address'] = strtoupper($value);
    }

    public function setOccupationNameAttribute($value)
    {
        $this->attributes['occupation_name'] = strtoupper($value);
    }

    public function setWorkplaceNameAttribute($value)
    {
        $this->attributes['workplace_name'] = strtoupper($value);
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
