<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ParticipantListExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithEvents,
    WithColumnWidths
{

    public $index;

    public function __construct($event)
    {
        $this->event = $event;
        $this->number = 1;
        $this->index;
        $this->end_date = Carbon::parse($event->end_at)
            ->locale('id')
            ->translatedFormat('d F Y');
        $this->start_date = Carbon::parse($event->start_at)
            ->locale('id')
            ->translatedFormat('d F Y');
    }

    public function collection()
    {
        $data = DB::table('rdt_invitations')
            ->select(
                'rdt_applicants.name',
                'rdt_applicants.birth_date',
                'rdt_applicants.workplace_name',
                'rdt_invitations.lab_code_sample',
                'rdt_events.city_code'
            )
            ->leftJoin('rdt_applicants', 'rdt_applicants.id', 'rdt_invitations.rdt_applicant_id')
            ->leftJoin('rdt_events', 'rdt_events.id', 'rdt_invitations.rdt_event_id')
            ->where('rdt_invitations.rdt_event_id', $this->event->id)
            ->whereNotNull('rdt_invitations.lab_code_sample')
            ->whereNotNull('rdt_invitations.attended_at')
            ->get();

        $this->index = count($data);

        return $data;
    }

    public function headings(): array
    {
        return [
            ["FORMULIR F2 : REGISTER SPESIMEN"],
            ["Nama Kegiatan : {$this->event->event_name}"],
            ["Tanggal :  {$this->getDifferenceDays()}"],
            ["DINAS KESEHATAN : {$this->event->city->name}"],
            [
                'No',
                'Nama Pasien',
                'Tanggal Lahir',
                'Institusi Pengirim Spesimen',
                'Nomor Spesimen (Label Barcode)',
            ],
        ];
    }

    public function map($event): array
    {
        return [
            $this->number++ ,
            $event->name,
            $event->birth_date,
            $event->workplace_name,
            $event->lab_code_sample,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                for ($cells = 1; $cells <= 4; $cells++) {
                    $event->sheet->mergeCells("A{$cells}:E{$cells}");
                    $event->sheet->getDelegate()
                        ->getStyle("A{$cells}:E{$cells}")
                        ->getAlignment()
                        ->setVertical(Alignment::VERTICAL_CENTER);
                }

                for ($key = 1; $key <= $this->index + 5; $key++) {
                    $event->sheet->getRowDimension($key)->setRowHeight(35);
                    $event->sheet->getDelegate()
                        ->getStyle("A{$key}:W{$key}")
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }
            },
        ];
    }

    public function columnWidths(): array
    {
        return [
            'E' => 45,
        ];
    }

    /**
     * Fungsi untuk menentukan kegiatan event harian atau rentang waktu
     *
     * @return void
     */
    public function getDifferenceDays()
    {
        if ($this->event->start_at->diff($this->event->end_at)->days > 1) {
            return $this->start_date . ' - ' . $this->end_date;
        }

        return $this->start_date;
    }
}
