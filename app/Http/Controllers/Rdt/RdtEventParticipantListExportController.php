<?php

namespace App\Http\Controllers\Rdt;

use App\Entities\RdtEvent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RdtEventParticipantListExportController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @param RdtEvent $rdtEvent
     */
    public function __invoke(Request $request, RdtEvent $rdtEvent)
    {
        header('Access-Control-Allow-Methods: *');
        header('Access-Control-Allow-Headers: *');
        header('Access-Control-Expose-Headers: *');

        Log::info('EVENT_PARTICIPANT_LIST_EXPORT', [
            'event_id' => $rdtEvent->id,
            'user_id'  => $request->user()->id,
        ]);

        try {
            $serviceUrl = config('services.internal.participant_export');

            $response = Http::get("{$serviceUrl}/export", [
                'rdt_event_id' => $rdtEvent->id,
            ]);
        } catch (ConnectionException $exception) {
            return $this->responseConnectFailed($rdtEvent, $request, $exception);
        }

        if ($response->failed()) {
            return $this->responseFetchFailed($rdtEvent, $request);
        }

        return $this->responseSuccess($rdtEvent, $request, $response);
    }

    protected function responseConnectFailed(RdtEvent $rdtEvent, Request $request, ConnectionException $exception)
    {
        Log::info('EVENT_PARTICIPANT_LIST_EXPORT_CONNECT_FAILED', [
            'event_id' => $rdtEvent->id,
            'user_id'  => $request->user()->id,
            'message'  => $exception->getMessage(),
        ]);

        return response()->json([
            'error'   => "CONNECTION_FAILED",
            'message' => "Cannot connect to service. Message: {$exception->getMessage()}",
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    protected function responseFetchFailed(RdtEvent $rdtEvent, Request $request)
    {
        Log::info('EVENT_PARTICIPANT_LIST_EXPORT_FETCH_FAILED', [
            'event_id' => $rdtEvent->id,
            'user_id'  => $request->user()->id,
        ]);

        return response()->json([
            'error'   => "FETCH_FAILED",
            'message' => "Error fetch data from service.",
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    protected function responseSuccess(RdtEvent $rdtEvent, Request $request, $serviceResponse)
    {
        Log::info('EVENT_PARTICIPANT_LIST_EXPORT_SUCCESS', [
            'event_id' => $rdtEvent->id,
            'user_id'  => $request->user()->id,
        ]);

        $fileName           = $this->getFileName($rdtEvent);
        $contentDisposition = "attachment; filename=\"{$fileName}\";";

        return response($serviceResponse->body(), Response::HTTP_OK, [
            'Content-Length'      => $serviceResponse->header('Content-Length'),
            'Content-Type'        => $serviceResponse->header('Content-Type'),
            'Content-Disposition' => $contentDisposition,
        ]);
    }

    protected function getFileName(RdtEvent $rdtEvent)
    {
        $now         = now()->format('YmdHis');
        $eventDate   = $rdtEvent->start_at->format('Ymd');
        $eventNumber = str_pad($rdtEvent->id, 5, '0', STR_PAD_LEFT);
        $eventName   = strtoupper(Str::slug($rdtEvent->event_name, '_'));

        return sprintf('PIKOBAR_TESMASIF_PESERTA_%s_%s_%s_%s.xlsx', $eventNumber, $eventDate, $now, $eventName);
    }
}
