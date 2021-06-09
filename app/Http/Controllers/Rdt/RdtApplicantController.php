<?php

namespace App\Http\Controllers\Rdt;

use App\Entities\RdtApplicant;
use App\Enums\RdtApplicantStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Rdt\RdtApplicantStoreRequest;
use App\Http\Requests\Rdt\RdtApplicantUpdateRequest;
use App\Http\Resources\RdtApplicantResource;
use App\Traits\PaginationTrait;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class RdtApplicantController extends Controller
{
    use PaginationTrait;

    public $sort = [
        'id',
        'name',
        'gender',
        'age',
        'person_status',
        'created_at',
        'updated_at',
        'registration_at',
    ];

    public $allowedFilter = [
        'person_status',
        'city_code',
        'status',
        'pikobar_session_id',
    ];

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page');
        $search = $request->input('search');
        $sortBy = $this->getValidOrderBy($request->input('sort_by'), 'name');
        $sortOrder = $this->getValidSortOders($request->input('sort_order'));
        $params = $this->getValidParams($request);
        $params['user_city_code'] = $request->user()->city_code;

        if ($sortBy === 'age') {
            $sortBy = 'birth_date';
        }

        $records = RdtApplicant::with(['invitations', 'invitations.event', 'city', 'district', 'village']);

        $records = $this->searchList($search, $records);
        $records = $this->filterList($records, $params);
        $records = $this->filterStatus($records, $params);

        $records->orderBy($sortBy, $sortOrder);

        return RdtApplicantResource::collection($this->getRecords($records, $perPage));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param RdtApplicantStoreRequest $request
     * @return \App\Http\Resources\RdtApplicantResource
     */
    public function store(RdtApplicantStoreRequest $request)
    {
        $rdtApplicant = RdtApplicant::create($request->all() + [
            'status' => $request->input('status'),
            'registration_at' => Carbon::now(),
            'province_code' => '32',
        ]);

        return new RdtApplicantResource($rdtApplicant);
    }

    /**
     * Display the specified resource.
     *
     * @param RdtApplicant $rdtApplicant
     * @return \App\Http\Resources\RdtApplicantResource
     */
    public function show(RdtApplicant $rdtApplicant)
    {
        $rdtApplicant->load(['invitations' => function ($query) {
            $query->has('event');
        }, 'invitations.event', 'city', 'district', 'village']);
        return new RdtApplicantResource($rdtApplicant);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Rdt\RdtApplicantUpdateRequest $request
     * @param RdtApplicant $rdtApplicant
     * @return \App\Http\Resources\RdtApplicantResource
     */
    public function update(RdtApplicantUpdateRequest $request, RdtApplicant $rdtApplicant)
    {
        $rdtApplicant->update($request->all());

        return new RdtApplicantResource($rdtApplicant);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param RdtApplicant $rdtApplicant
     * @return \Illuminate\Http\Response
     */
    public function destroy(RdtApplicant $rdtApplicant)
    {
        $rdtApplicant->delete();

        return response()->json(['message' => 'DELETED']);
    }

    protected function searchList($search, $records)
    {
        if ($search) {
            $records->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('registration_code', 'like', '%' . $search . '%')
                    ->orWhere('workplace_name', 'like', '%' . $search . '%')
                    ->orWhere('nik', $search)
                    ->orWhere('pikobar_session_id', $search)
                    ->orWhere('phone_number', 'like', '%' . $search . '%');
            });
        }

        return $records;
    }

    protected function filterList($records, $params)
    {
        foreach ($params as $key => $value) {
            $records->when(in_array($key, $this->allowedFilter), function ($query) use ($key, $value) {
                $query->where($key, $value);
            });

            $records->when($key == 'user_city_code' && $value, function ($query) use ($value) {
                $query->where('city_code', $value);
            });

            $records->when($key == 'registration_date_start', function ($query) use ($value) {
                $query->whereDate('registration_at', '>=', Carbon::parse($value));
            });

            $records->when($key == 'registration_date_end', function ($query) use ($value) {
                $query->whereDate('registration_at', '<=', Carbon::parse($value));
            });
        }

        return $records;
    }

    protected function filterStatus($records, $params)
    {
        foreach ($params as $key => $value) {
            if ($key == 'status') {
                if (strtoupper($value) == RdtApplicantStatus::NEW()) {
                    $records->whereEnum('status', RdtApplicantStatus::NEW());
                } else {
                    $records->whereEnum('status', RdtApplicantStatus::APPROVED());
                }
            }
        }

        return $records;
    }
}
