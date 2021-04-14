<?php

namespace App\Http\Controllers\Rdt;

use App\Entities\RdtEvent;
use App\Http\Controllers\Controller;
use Box\Spout\Common\Entity\Style\CellAlignment;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RdtEventParticipantListExportF2Controller extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(RdtEvent $rdtEvent)
    {
        // make writer
        $excelWriter = WriterEntityFactory::createXLSXWriter();
        $excelWriter->openToFile('php://output');

        // define name file
        $fileName = Str::slug($rdtEvent->event_name . ' F2', '-') . '.xlsx';

        // make some column template
        $headers = [
            'No',
            'Nama Pasien',
            'Tanggal Lahir / Usia',
            'Jenis Spesimen',
            'Institusi Pengirim Spesimen',
            'Nomor Spesimen (Label Barcode)',
        ];

        /** Create a style with the StyleBuilder */
        $style = (new StyleBuilder())
            ->setCellAlignment(CellAlignment::CENTER)
            ->build();

        $rowFromValues = WriterEntityFactory::createRowFromArray($headers, $style);
        $excelWriter->addRow($rowFromValues);

        DB::statement(DB::raw('set @number=0'));

        // containing column with value
        $data = DB::table('rdt_invitations')
            ->select(
                DB::raw('@number:=@number+1 as number'),
                'rdt_applicants.name',
                'rdt_applicants.birth_date',
                'rdt_applicants.workplace_name',
            )
            ->leftJoin('rdt_applicants', 'rdt_applicants.id', 'rdt_invitations.rdt_applicant_id')
            ->where('rdt_invitations.rdt_event_id', $rdtEvent->id)
            ->get();

        foreach ($data as $row) {
            $row = [
                $row->number,
                $row->name,
                $row->birth_date,
                '',
                $row->workplace_name,
                '',
            ];

            $rowFromValues = WriterEntityFactory::createRowFromArray($row);
            $excelWriter->addRow($rowFromValues);
        }
        return $this->responseStream($fileName, $excelWriter);
    }

    protected function responseStream($fileName, $excelWriter)
    {
        $headers = [
            'Content-Disposition' => "attachment; filename=\"{$fileName}\";",
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        return response()->stream(function () use ($excelWriter) {
            $excelWriter->close();
        }, Response::HTTP_OK, $headers);
    }
}
