<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Resources\FasyankesResource;
use App\Http\Controllers\Controller;
use App\Entities\Fasyankes;

class FasyankesListController extends Controller
{
    public function __invoke(Request $request)
    {
        $fasyankes = Fasyankes::query();
        if ($request->has('name')) {
            $fasyankes->where('name', 'like', '%' . $request->input('name') . '%');
        }
        if ($request->has('type')) {
            $fasyankes->where('type', $request->type);
        }
        return FasyankesResource::collection($fasyankes->orderBy('name')->get());
    }
}
