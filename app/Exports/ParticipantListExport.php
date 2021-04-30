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
        $this->date = Carbon::parse($event->end_at)
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
                'rdt_invitations.lab_code_sample'
            )
            ->leftJoin('rdt_applicants', 'rdt_applicants.id', 'rdt_invitations.rdt_applicant_id')
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
            ["Tanggal :  {$this->date}"],
            ["DINAS KESEHATAN : {$this->event->host_name}"],
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
}
