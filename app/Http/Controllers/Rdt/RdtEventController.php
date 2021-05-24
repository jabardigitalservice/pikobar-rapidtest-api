<?php

namespace App\Http\Controllers\Rdt;

use App\Entities\RdtEvent;
use App\Entities\RdtEventSchedule;
use App\Enums\RdtEventStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Rdt\RdtEventStoreRequest;
use App\Http\Requests\Rdt\RdtEventUpdateRequest;
use App\Http\Resources\RdtEventResource;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RdtEventController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $status = $request->input('status', 'all');
        $search = $request->input('search');
        $eventDateStart = Carbon::parse($request->input('start_date'));
        $eventDateEnd = Carbon::parse($request->input('end_date'));

        $perPage = $this->getPaginationSize($perPage);

        if (
            in_array($sortBy, [
                'id', 'event_name', 'registration_type', 'start_at', 'end_at', 'status', 'created_at',
            ]) === false
        ) {
            $sortBy = 'event_name';
        }

        $records = RdtEvent::query();

        if ($request->has(['start_date', 'end_date']) && $eventDateStart <= $eventDateEnd) {
            $records->whereBetween('start_at', [$eventDateStart, $eventDateEnd])
                ->orWhereBetween('end_at', [$eventDateStart, $eventDateEnd]);
        }

        if ($request->has('city_code')) {
            $records->where('city_code', $request->input('city_code'));
        }

        if ($request->user()->city_code) {
            $records->where('city_code', $request->user()->city_code);
        }

        if ($search) {
            $records->where(function ($query) use ($search) {
                $query->where('event_name', 'like', '%' . $search . '%');
            });
        }

        if ($status === 'draft') {
            $records->whereEnum('status', RdtEventStatus::DRAFT());
        }
        if ($status === 'published') {
            $records->whereEnum('status', RdtEventStatus::PUBLISHED());
        }

        $records->orderBy($sortBy, $sortOrder);
        $records->with(['city']);
        $records->withCount(['invitations', 'schedules']);

        return RdtEventResource::collection($records->paginate($perPage));
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

    protected function getPaginationSize($perPage)
    {
        $perPageAllowed = [50, 100, 500];

        if (in_array($perPage, $perPageAllowed)) {
            return $perPage;
        }
        return 15;
    }
}
