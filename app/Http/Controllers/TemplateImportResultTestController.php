<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class TemplateImportResultTestController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $filename = 'Template_Import_Tes_Result.xlsx';
        $path = public_path('template/' . $filename);

        if (!File::exists($path)) {
            abort(404, 'File not found.');
        }

        $type = File::mimeType($path);

        return response()->download($path, $filename, [
            'Content-Type' => $type,
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }
}
