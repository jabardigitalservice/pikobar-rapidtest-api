<?php

namespace App\Http\Controllers\Master;

use App\Entities\Area;
use App\Http\Controllers\ApiController;
use App\Http\Resources\AreaResource as AreaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AreaController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $records = Area::query();
        $records->orderBy('name');

        if ($request->has('depth')) {
            $records = $records->where('depth', $request->input('depth'));
        }

        if ($request->has('parent_code_kemendagri')) {
            $records = $records->where('parent_code_kemendagri', $request->input('parent_code_kemendagri'));

            return $this->responseResourceCollection($records);
        }

        $records = $records->where('parent_code_kemendagri', '32');

        return $this->responseResourceCollection($records);
    }

    protected function responseResourceCollection($records)
    {
        return AreaResource::collection($records->get());
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param Area $area
     * @return AreaResource|\Illuminate\Http\Response
     */
    public function show(Area $area)
    {
        return new AreaResource($area);
    }
}
