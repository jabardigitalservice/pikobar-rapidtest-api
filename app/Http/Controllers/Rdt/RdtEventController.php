<?php

namespace App\Http\Controllers\Rdt;

use App\Entities\RdtEvent;
use App\Entities\RdtEventSchedule;
use App\Enums\RdtEventStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Rdt\RdtEventStoreRequest;
use App\Http\Requests\Rdt\RdtEventUpdateRequest;
use App\Http\Resources\RdtEventResource;
use App\Traits\PaginationTrait;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RdtEventController extends Controller
{
    use PaginationTrait;

    public $sort = [
        'id',
        'event_name',
        'registration_type',
        'start_at',
        'end_at',
        'status',
        'created_at',
    ];

    /**
     * Display a listing of the resource.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $perpage = $request->input('per_page');
        $search = $request->input('search');
        $sortBy = $this->getValidOrderBy($request->input('sort_by'), 'created_at');
        $sortOrder = $this->getValidSortOders($request->input('sort_order'));
        $params = $this->getValidParams($request);
        $params['user_city_code'] = $request->user()->city_code;
        $eventDateStart = Carbon::parse($request->input('start_date'));
        $eventDateEnd = Carbon::parse($request->input('end_date'));

        $records = RdtEvent::with(['city'])->withCount(['invitations', 'schedules']);

        $records = $this->searchList($records, $search);
        $records = $this->filterList($records, $params);
        $records = $this->filterStatus($records, $params);
        $records = $this->filterDate($request, $eventDateStart, $eventDateEnd, $records);

        $records->orderBy($sortBy, $sortOrder);

        return RdtEventResource::collection($this->getRecords($records, $perpage));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Rdt\RdtEventStoreRequest  $request
     * @return \App\Http\Resources\RdtEventResource
     */
    public function store(RdtEventStoreRequest $request)
    {
        $rdtEvent = new RdtEvent();
        $rdtEvent->fill($request->all());
        $rdtEvent->save();

        $inputSchedules = $request->input('schedules');

        foreach ($inputSchedules as $inputSchedule) {
            $schedule = new RdtEventSchedule();
            $schedule->start_at = $inputSchedule['start_at'];
            $schedule->end_at = $inputSchedule['end_at'];
            $rdtEvent->schedules()->save($schedule);
        }

        $rdtEvent->load('city');

        return new RdtEventResource($rdtEvent);
    }

    /**
     * Display the specified resource.
     *
     * @param  RdtEvent  $rdtEvent
     * @return RdtEventResource
     */
    public function show(RdtEvent $rdtEvent)
    {
        $rdtEvent->loadCount([
            'invitations', 'schedules', 'attendees', 'attendeesResult', 'applicantsNotifiedResult',
        ]);

        $rdtEvent->load(['schedules', 'city']);

        return new RdtEventResource($rdtEvent);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  RdtEventUpdateRequest  $request
     * @param  RdtEvent  $rdtEvent
     * @return \App\Http\Resources\RdtEventResource
     */
    public function update(RdtEventUpdateRequest $request, RdtEvent $rdtEvent)
    {
        $rdtEvent->fill($request->all());
        $rdtEvent->save();

        if ($request->has('schedules')) {
            foreach ($request->input('schedules') as $schedule) {
                RdtEventSchedule::find($schedule['id'])
                    ->update([
                        'start_at' => $schedule['start_at'],
                        'end_at' => $schedule['end_at'],
                    ]);
            }
        }

        $rdtEvent->load('city');

        return new RdtEventResource($rdtEvent);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  RdtEvent  $rdtEvent
     * @return JsonResponse
     * @throws \Exception
     */
    public function destroy(RdtEvent $rdtEvent)
    {
        $rdtEvent->delete();

        return response()->json(['message' => 'DELETED']);
    }

    protected function searchList($records, $search)
    {
        if ($search) {
            $records->where('event_name', 'like', '%' . $search . '%');
        }

        return $records;
    }

    protected function filterList($records, $params)
    {
        foreach ($params as $key => $value) {
            $records->when($key == 'city_code', function ($query) use ($value) {
                $query->where('city_code', $value);
            });

            $records->when($key == 'user_city_code' && $value, function ($query) use ($value) {
                $query->where('city_code', $value);
            });
        }

        return $records;
    }

    protected function filterStatus($records, $params)
    {
        foreach ($params as $key => $value) {
            if ($key == 'status') {
                if (strtoupper($value) == RdtEventStatus::DRAFT()) {
                    $records->whereEnum('status', RdtEventStatus::DRAFT());
                } else {
                    $records->whereEnum('status', RdtEventStatus::PUBLISHED());
                }
            }
        }

        return $records;
    }

    protected function filterDate($request, $eventDateStart, $eventDateEnd, $records)
    {
        $records->when(
            $request->has(['start_date', 'end_date']),
            function ($query) use ($eventDateStart, $eventDateEnd) {
            // condition if event on 1 day
                if ($eventDateStart == $eventDateEnd) {
                    $eventDateEnd = $eventDateEnd->endOfDay();
                }

                $query->where(function ($query) use ($eventDateStart, $eventDateEnd) {
                    $query->whereBetween('start_at', [$eventDateStart, $eventDateEnd])
                    ->orWhereBetween('end_at', [$eventDateStart, $eventDateEnd]);
                });
            }
        );

        return $records;
    }
}
