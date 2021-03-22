<?php

namespace App\Http\Controllers;

use App\Entities\RdtEvent;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class SyncToLabkesController extends Controller
{
    public function __invoke(RdtEvent $rdtEvent)
    {
        $labkesUrl = config('app.labkes_url') . 'api/v1/tes-masif/bulk';
        $labkesApiKey = config('app.labkes_api_key');

        $data = $this->getDataEventInvitation($rdtEvent);

        if (count($data) < 1) {
            return response()->json(['message' => 'Belum Ada Data Terbaru']);
        }

        $personStatusValue = [
            'CONFIRMED' => 'konfirmasi',
            'SUSPECT' => 'suspek',
            'PROBABLE' => 'probable',
            'CLOSE_CONTACT' => 'kontak erat',
            'NOT_ALL' => 'tanpa kriteria',
            'UNKNOWN' => 'tanpa kriteria',
            'ODP' => 'tanpa kriteria',
            'OTG' => 'tanpa kriteria',
            'PDP' => 'tanpa kriteria',
        ];

        $payloads = [];

        foreach ($data as $row) {
            if ($row->gender == 'F') {
                $gender = 'P';
            } elseif ($row->gender == 'M') {
                $gender = "L";
            } else {
                $gender = "";
            }

            $attendedAt = Carbon::createFromFormat('Y-m-d H:i:s', $row->attended_at);
            $attendedAt->setTimezone(config('app.timezone_frontend'));

            $payloads[] = [
                'kewarganegaraan' => 'WNI',
                'kategori' => $rdtEvent->event_name . ' ' . Carbon::parse($row->attended_at)->format('dmY'),
                'kriteria' => $personStatusValue[$row->person_status],
                'nama_pasien' => $row->name,
                'nik' => $row->nik,
                'registration_code' => $row->registration_code,
                'tempat_lahir' => $row->birth_place,
                'tanggal_lahir' => Carbon::parse($row->birth_date),
                'jenis_kelamin' => $gender,
                'provinsi_id' => str_replace('.', '', $row->province_code),
                'kota_id' => str_replace('.', '', $row->city_code),
                'kecamatan_id' => null,
                'kelurahan_id' => null,
                'alamat' => $row->address,
                'rt' => null,
                'rw' => null,
                'no_hp' => $row->phone_number,
                'suhu' => null,
                'nomor_sampel' => $row->lab_code_sample,
                'keterangan' => null,
                'hasil_rdt' => null,
                'usia_tahun' => $this->countAge($row->birth_date, 'y'),
                'usia_bulan' => $this->countAge($row->birth_date, 'm'),
                'kunjungan' => 1,
                'jenis_registrasi' => $rdtEvent->registration_type === null ? 'mandiri' : 'rujukan',
                'fasyankes_id' => $row->fasyankes_id,
                'tanggal_kunjungan' => $attendedAt->toDateTimeString(),
                'rs_kunjungan' => $row->attend_location,
            ];
        }

        try {
            $request = Http::post($labkesUrl, ['data' => $payloads, 'api_key' => $labkesApiKey]);
            if ($request->getStatusCode() === Response::HTTP_OK) {
                $result = json_decode($request->getBody()->getContents());
                $response['message'] = $this->addFlagSendToLabkes($result);
                $response['result'] = ['success' => $result->result->berhasil, 'failed' => $result->result->gagal];
                $statusCode = Response::HTTP_OK;
            } else {
                $result = json_decode($request->getBody()->getContents());
                $response['message'] = $this->addFlagSendToLabkes($result);
                $response['result'] = ['failed' => $result->result->gagal];
                $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;
            }
        } catch (Exception $e) {
            $response['message'] = __('response.sync_failed');
            $response['result'] = null;
            $statusCode = Response::HTTP_BAD_GATEWAY;
        }

        return response()->json($response, $statusCode);
    }

    public function addFlagSendToLabkes($result)
    {
        if (count($result->result->berhasil) > 0) {
            DB::table('rdt_invitations')
                ->whereIn('lab_code_sample', array_values($result->result->berhasil))
                ->update(['synchronization_at' => now(), 'status_on_simlab' => 'SENT']);
        }

        if (count($result->result->gagal) > 0) {
            $resultFailed = collect($result->result->gagal)->pluck('nomor_sampel')->toArray();

            DB::table('rdt_invitations')
                ->whereIn('lab_code_sample', array_values($resultFailed))
                ->update(['status_on_simlab' => 'FAILED']);
        }

        $countSuccess = count($result->result->berhasil);
        $countFailed = count($result->result->gagal);
        $payloadSuccess = $countSuccess . ' ' . __('response.sync_success');
        $payloadFailed = $countFailed . ' ' . __('response.sync_failed');

        return $countSuccess > 0 ? $payloadSuccess . ' & ' . $payloadFailed : $payloadFailed;
    }

    public function countAge($birthDate, $format)
    {
        $age = Carbon::parse($birthDate)->diff(Carbon::now());
        return $age->format('%' . $format);
    }

    public function getDataEventInvitation($rdtEvent)
    {
        $result = DB::table('rdt_invitations')
            ->select(
                'rdt_invitations.lab_code_sample',
                'rdt_invitations.attended_at',
                'rdt_invitations.lab_result_type',
                'rdt_invitations.attend_location',
                'rdt_invitations.registration_code',
                'rdt_applicants.person_status',
                'rdt_applicants.occupation_type',
                'rdt_applicants.name',
                'rdt_applicants.workplace_name',
                'rdt_applicants.nik',
                'rdt_applicants.phone_number',
                'rdt_applicants.gender',
                'rdt_applicants.province_code',
                'rdt_applicants.city_code',
                'rdt_applicants.district_code',
                'rdt_applicants.village_code',
                'rdt_applicants.birth_date',
                'rdt_applicants.birth_place',
                'rdt_applicants.address',
                'rdt_events.host_name',
                'fasyankes.id as fasyankes_id',
                'rdt_events.start_at',
                'rdt_events.end_at',
                'fasyankes.id as fasyankes_id',
                'rdt_events.event_location',
                'city.name as city',
                'district.name as district',
                'village.name as village'
            )
            ->leftJoin('rdt_applicants', 'rdt_applicants.id', 'rdt_invitations.rdt_applicant_id')
            ->leftJoin('rdt_events', 'rdt_events.id', 'rdt_invitations.rdt_event_id')
            ->leftJoin('areas as city', 'city.code_kemendagri', 'rdt_applicants.city_code')
            ->leftJoin('areas as district', 'district.code_kemendagri', 'rdt_applicants.district_code')
            ->leftJoin('areas as village', 'village.code_kemendagri', 'rdt_applicants.village_code')
            ->leftJoin('fasyankes', 'fasyankes.name', '=', 'rdt_events.host_name')
            ->where('rdt_invitations.rdt_event_id', $rdtEvent->id)
            ->whereNotNull('rdt_invitations.lab_code_sample')
            ->whereNotNull('rdt_invitations.attended_at')
            ->whereNull('rdt_invitations.synchronization_at')
            ->get();
        return $result;
    }
}
