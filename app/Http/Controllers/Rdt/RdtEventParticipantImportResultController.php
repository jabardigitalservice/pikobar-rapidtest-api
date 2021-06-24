<?php

namespace App\Http\Controllers\Rdt;

use App\Entities\RdtEvent;
use App\Entities\RdtInvitation;
use App\Http\Controllers\Controller;
use App\Http\Requests\Rdt\RdtInvitationImportRequest;
use App\Notifications\TestResult;
use App\Rules\LabResultRule;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RdtEventParticipantImportResultController extends Controller
{
    public $result = [
        'message' => '',
        'errors_count' => 0,
        'errors' => [],
    ];

    public function __invoke(RdtInvitationImportRequest $request, RdtEvent $rdtEvent)
    {
        Log::info('IMPORT_TEST_RESULT_START', [
            'file_name' => $request->file('file')->getClientOriginalName(),
            'user_id' => $request->user()->id,
        ]);

        $reader = ReaderEntityFactory::createXLSXReader();

        $reader->open($request->file('file')->path());

        $rowsCount = 0;
        $now = now();
        $arrHeader = [];

        DB::beginTransaction();
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $index => $row) {
                $rowArray = $row->toArray();

                if ($index === 1) {
                    continue;
                }

                $arrHeader['kode_pendaftaran'] = $rowArray[0];
                $arrHeader['hasil'] = strtoupper($rowArray[1]);
                $arrHeader['notify'] = strtoupper($rowArray[2]);

                // validation import excel
                $this->validated($arrHeader, $rowsCount);

                $registrationCode = $arrHeader['kode_pendaftaran'];
                $result = $arrHeader['hasil'];
                $notify = $arrHeader['notify'];

                /**
                 * @var RdtInvitation $invitation
                 */
                $invitation = RdtInvitation::where('registration_code', $registrationCode)
                    ->where('rdt_event_id', $rdtEvent->id)
                    ->first();

                // Handling error, skip if not found
                if ($invitation === null || count($this->result['errors']) > 0) {
                    Log::info('IMPORT_TEST_RESULT_INVITATION_NOTFOUND', [
                        'rdt_event_id' => $rdtEvent->id,
                        'registration_code' => $registrationCode,
                        'result' => $result,
                        'notify' => $notify,
                        'user_id' => $request->user()->id,
                    ]);

                    $rowsCount++;
                    continue;
                } else {
                    $rowsCount++;
                }

                Log::info('IMPORT_TEST_RESULT_ROW', [
                    'row' => $index,
                    'event' => $rdtEvent,
                    'registration_code' => $registrationCode,
                    'result' => $result,
                    'notify' => $notify,
                    'invitation' => $invitation,
                    'user_id' => $request->user()->id,
                ]);

                $invitation->lab_result_type = $result;
                $invitation->result_at = $now;
                $invitation->save();

                if ($notify === 'YES') {
                    $applicant = $invitation->applicant;
                    $applicant->notify(new TestResult());

                    $invitation->notified_result_at = $now;
                    $invitation->save();

                    Log::info('NOTIFY_TEST_RESULT', [
                        'applicant' => $applicant,
                        'invitation' => $invitation,
                        'result' => $invitation->lab_result_type,
                        'user_id' => $request->user()->id,
                    ]);
                }
            }
        }

        if (!$this->result['errors_count'] > 0) {
            DB::commit();
            $this->result['message'] = __('response.import_success');
        } else {
            $this->result['message'] = __('response.import_failed');
            DB::rollBack();
        }

        Log::info('IMPORT_TEST_RESULT_SUCCESS', [
            'file_name' => $request->file('file')->getClientOriginalName(),
            'rows_total' => $rowsCount,
            'user_id' => $request->user()->id,
        ]);

        return response()->json($this->result, $this->getStatusCodeResponse());
    }

    public function validated(array $rows, $key)
    {
        $validator = Validator::make($rows, $this->rules());
        $msgErr = str_replace('.', '', implode(', ', $validator->errors()->all()));
        if ($validator->fails()) {
            $this->setError($key, $msgErr);
        }
    }

    protected function rules()
    {
        return [
            'kode_pendaftaran' => 'required',
            'hasil' => ['required', new LabResultRule()],
            'notify' => 'required',
        ];
    }

    protected function getStatusCodeResponse()
    {
        return $this->result['errors_count'] > 0 ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK;
    }

    protected function setError($key, $message)
    {
        if ($message) {
            $this->result['errors'][$key + 1] = 'Baris ' . ($key + 1) . ': ' . $message;
        }

        ++$this->result['errors_count'];
    }
}
