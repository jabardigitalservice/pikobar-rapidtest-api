<?php

namespace App\Http\Controllers\Rdt;

use App\Http\Controllers\Controller;
use App\Entities\RdtEvent;
use File;
use DB;
use Carbon\Carbon;
use App\Enums\PersonCaseStatusEnum;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Common\Entity\Row;

class RdtEventParticipantListExportF1Controller extends Controller
{
    public function __invoke(RdtEvent $rdtEvent)
    {
        $writer = WriterEntityFactory::createXLSXWriter();

        $fileName = Str::slug($rdtEvent->event_name, '-') . '.xlsx';
        $writer->openToFile('php://output');

        $header = [
            'NO',
            'NOMOR SAMPEL',
            'KEWARGANEGARAAN',
            'FASYANKES/ DINKES',
            'DOKTER',
            'TELP FASYANKES',
            'KATEGORI',
            'KRITERIA',
            'NAMA PASIEN',
            'NIK',
            'TEMPAT LAHIR',
            'TANGGAL LAHIR',
            'USIA TAHUN',
            'USIA BULAN',
            'JENIS KELAMIN',
            'ID KOTA',
            'KOTA',
            'NO HP',
            'ID KECAMATAN',
            'KECAMATAN',
            'ID KELURAHAN/ DESA',
            'KELURAHAN/ DESA',
            'ALAMAT',
            'RT',
            'RW',
            'KUNJUNGAN',
            'SUHU',
            'KETERANGAN',
            'INSTANSI PENGIRIM',
            'ID FASYANKES',
            'TANGGAL KUNJUNGAN',
            'RS KUNJUNGAN'
        ];

        $rowFromValues = WriterEntityFactory::createRowFromArray($header);
        $writer->addRow($rowFromValues);

        DB::statement(DB::raw('set @number=0'));

        $data = \DB::table('rdt_invitations')
                ->select(
                    DB::raw('@number:=@number+1 as number'),
                    'rdt_invitations.lab_code_sample',
                    'rdt_invitations.attended_at',
                    'rdt_invitations.lab_result_type',
                    'rdt_invitations.registration_code',
                    'rdt_applicants.person_status',
                    'rdt_applicants.occupation_type',
                    'rdt_applicants.name',
                    'rdt_applicants.nik',
                    'rdt_applicants.phone_number',
                    'rdt_applicants.gender',
                    'rdt_applicants.city_code',
                    'rdt_applicants.district_code',
                    'rdt_applicants.village_code',
                    'rdt_applicants.birth_date',
                    'rdt_applicants.birth_place',
                    'rdt_applicants.address',
                    'rdt_applicants.symptoms',
                    'rdt_applicants.city_visited',
                    'rdt_applicants.have_interacted',
                    'rdt_applicants.congenital_disease',
                    'rdt_events.host_name',
                    'rdt_events.start_at',
                    'rdt_events.end_at',
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
                ->where('rdt_invitations.rdt_event_id', $rdtEvent->id)
                ->get();

        $personStatusValue = [
            'CONFIRMED' => 'Terkonfirmasi',
            'SUSPECT' => 'Kasus Suspek',
            'PROBABLE' => 'Kasus Probable',
            'CLOSE_CONTACT' => 'Kontak Erat',
            'NOT_ALL' => 'Bukan Semuanya',
            'UNKNOWN' => 'Tidak Tahu',
            'ODP' => 'Orang Dalam pengawasan',
            'OTG' => 'Orang Tanpa Gejala',
            'PDP' => 'Pasien Dalam Pengawasan'
        ];

        foreach ($data as $row) {
            if ($row->birth_date) {
                $age = Carbon::parse($row->birth_date)->diff(Carbon::now());
                $ageYear = $age->format('%y');
                $ageMonth = $age->format('%m');
            } else {
                $ageYear = '';
                $ageMonth = '';
            }

            if ($row->gender == 'F') {
                $gender = 'P';
            } elseif ($row->gender == 'M') {
                $gender = "L";
            } else {
                $gender = "";
            }
            
            $row =  [
                        $row->number,
                        $row->lab_code_sample ,
                        'WNI',
                        $row->host_name,
                        '',
                        '',
                        '',
                        $personStatusValue[$row->person_status] ?? null,
                        $row->name,
                        $row->nik,
                        $row->birth_place,
                        Carbon::parse($row->birth_date)->format('d-m-Y'),
                        $ageYear,
                        $ageMonth,
                        $gender,
                        $row->city_code,
                        $row->city,
                        $row->phone_number,
                        $row->district_code,
                        $row->district,
                        $row->village_code,
                        $row->village,
                        $row->address,
                        '',
                        '',
                        1,
                        '',
                        '',
                        '',
                        '',
                        Carbon::parse($row->attended_at)->format('d-m-Y H:i:s'),
                         ''
                    ];
        
            $rowFromValues = WriterEntityFactory::createRowFromArray($row);
            $writer->addRow($rowFromValues);
        }

        return $this->responseStream($fileName, $writer);
    }

    protected function responseStream($fileName, $writer)
    {
        $headers = [
            'Content-Disposition' => "attachment; filename=\"{$fileName}\";",
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        return response()->stream(function () use ($writer) {
            $writer->close();
        }, Response::HTTP_OK, $headers);
    }
}
