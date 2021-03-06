<?php

namespace App\Http\Controllers\Rdt;

use App\Entities\RdtEvent;
use App\Http\Controllers\Controller;
use App\Http\Resources\RdtInvitationResource;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RdtEventParticipantListController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Entities\RdtEvent  $rdtEvent
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function __invoke(Request $request, RdtEvent $rdtEvent)
    {
        $perPage = $request->input('per_page', 'ALL');
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $search = $request->input('search');
        $startDate = Carbon::parse($request->input('start_date'));
        $endDate = Carbon::parse($request->input('end_date'));

        $perPage = $this->getPaginationSize($perPage);

        $records = $rdtEvent->invitations();
        $records->select('rdt_invitations.*')
            ->join('rdt_applicants', 'rdt_invitations.rdt_applicant_id', '=', 'rdt_applicants.id');
        $records->where('rdt_invitations.rdt_event_id', $rdtEvent->id);
        $records->whereNull('rdt_applicants.deleted_at');

        if ($request->has(['start_date', 'end_date'])) {
            $records->whereDate('rdt_invitations.created_at', '>=', $startDate)
                ->whereDate('rdt_invitations.created_at', '<=', $endDate);
        }

        if ($search) {
            $records->where(function ($query) use ($search) {
                $query->where('rdt_applicants.name', 'like', '%' . $search . '%');
                $query->orWhere('rdt_invitations.registration_code', 'like', '%' . $search . '%');
                $query->orWhere('lab_code_sample', 'like', '%' . $search . '%');
                $query->orWhere('rdt_applicants.workplace_name', 'like', '%' . $search . '%');
                $query->orWhere('rdt_applicants.nik', '=', $search);
                $query->orWhere('rdt_applicants.pikobar_session_id', '=', $search);
            });
        }

        $sortBy = str_replace('applicant.', 'rdt_applicants.', $sortBy);

        $records->orderBy($sortBy, $sortOrder);
        $records->with(['applicant', 'schedule']);

        if (strtoupper($perPage) === 'ALL') {
            return RdtInvitationResource::collection($records->get());
        }

        return RdtInvitationResource::collection($records->paginate($perPage));
    }

    protected function getPaginationSize($perPage)
    {
        $perPageAllowed = [50, 100, 500];

        if (in_array($perPage, $perPageAllowed)) {
            return $perPage;
        }
        return $perPage;
    }
}
