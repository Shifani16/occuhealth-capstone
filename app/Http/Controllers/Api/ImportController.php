<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\McuImport;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class ImportController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
        ], [
            'file.required' => 'Please select an Excel file to upload.',
            'file.file' => 'The uploaded item must be a file.',
            'file.mimes' => 'The file must be a valid Excel file (.xlsx or .xls).',
            'file.max' => 'The file size must not exceed 10MB.',
        ]);

        try {
            Excel::import(new McuImport, $request->file('file'));


            return response()->json([
                'message' => 'File uploaded and data import initiated successfully. Please check logs for potential row errors.',
            ]);

        } catch (ValidationException $e) {
             return response()->json([
                 'error' => 'Validation failed.',
                 'messages' => $e->errors(),
             ], 422);

         } catch (\Exception $e) {
             Log::error('Excel import failed: ' . $e->getMessage(), ['exception' => $e]);

             return response()->json([
                 'error' => 'Failed to import data from file.',
                 'message' => $e->getMessage(), 
             ], 500); 
         }
    }
}