<?php

namespace App\Exports;

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
    }

    public function collection()
    {
        $data = DB::table('rdt_invitations')
            ->select(
                'rdt_applicants.name',
                'rdt_applicants.birth_date',
                'rdt_applicants.workplace_name',
            )
            ->leftJoin('rdt_applicants', 'rdt_applicants.id', 'rdt_invitations.rdt_applicant_id')
            ->where('rdt_invitations.rdt_event_id', $this->event->id)
            ->get();

        $this->index = count($data);

        return $data;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Pasien',
            'Tanggal Lahir',
            'Institusi Pengirim Spesimen',
            'Nomor Spesimen (Label Barcode)',
        ];
    }

    public function map($event): array
    {
        return [
            $this->number++ ,
            $event->name,
            $event->birth_date,
            $event->workplace_name,
            ' ',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = 'A1:W1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12)->setBold(true);

                for ($key = 1; $key <= $this->index + 1; $key++) {
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
