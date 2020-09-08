<?php

namespace App\Http\Controllers\Register;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Entities\RdtApplicant;

class CheckNikIsAlreadyUsedController extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), ['nik' => 'required']);
        if ($validator->fails()) {
            $statusCode = 422;
            $response['message'] = $validator->messages();
        } else {
            $rdtApplicant = RdtApplicant::where('nik', $request->nik)->first();
            if ($rdtApplicant != null) {
                $response['message'] = "nik sudah pernah digunakan";
                $statusCode = 422;
            } else {
                $response['message'] = "nik belum pernah digunakan";
                $statusCode = 200;
            }
        }
        return response()->json($response, $statusCode);
    }
}
