<?php

namespace App\Http\Controllers\Register;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Illuminate\Validation\ValidationException;

class CheckNikIsAlreadyUsedController extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = \Validator::make($request->all(), ['nik' => 'required|unique:rdt_applicants']);
        if ($validator->fails()) {
            throw  ValidationException::withMessages([
                'nik' => 'Nik sudah pernah digunakan'
          ]);
        }
    }
}
