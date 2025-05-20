<?php

namespace App\Http\Controllers\dashboard;

use App\Models\McuPatient;
use App\Models\Patient;   
use App\Models\McuResult; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class McuPatientController extends Controller
{
    // Get all MCU patients
    public function index()
    {
        $patients = McuPatient::with('patient')->get();
        return response()->json($patients);
    }

    // Get one MCU patient
        public function show($id)
    {
        try {
            $mcuPatient = McuPatient::with('patient')->findOrFail($id);

            $individualResults = McuResult::where('patient_id', $mcuPatient->patient_id)
                                          ->where('result_date', $mcuPatient->examination_date) 
                                          ->get(); 

         
            $saran = $mcuPatient->saran ?? null;

            return response()->json([
                'mcu_session' => $mcuPatient,
                'patient' => $mcuPatient->patient,
                'individual_results' => $individualResults,
                'saran' => $saran 
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning("MCU Patient with ID {$id} not found in show method.");
            return response()->json(['error' => 'Hasil MCU tidak ditemukan.'], 404);
        } catch (\Exception $e) {
            Log::error('Error fetching specific MCU details: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Terjadi kesalahan saat memuat data hasil MCU.'], 500);
        }
    }

    // Create new MCU patient
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'status' => 'nullable|string',
            'examination_date' => 'required|date',
            'examination_type' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        $mcuPatient = McuPatient::create($request->all());

        return response()->json([
            'message' => 'MCU patient created successfully',
            'data' => $mcuPatient,
        ], 201);
    }

    // Delete MCU patient
    public function destroy($id)
    {
        $mcuPatient = McuPatient::find($id);

        if (!$mcuPatient) {
            return response()->json(['message' => 'MCU Patient not found'], 404);
        }

        $mcuPatient->delete();

        return response()->json(['message' => 'MCU patient deleted successfully']);
    }

    // Update MCU patient
    public function update(Request $request, $id)
    {
        // Validate incoming request data
        $request->validate([
            'saran' => 'nullable|string', 
            'status' => 'nullable|string',
            'individual_results' => 'required|array',
            'individual_results.*.id' => 'required|exists:mcu_results,id', 
            'individual_results.*.result' => 'nullable|string',
        ]);

        DB::beginTransaction(); 

        try {
            $mcuPatient = McuPatient::findOrFail($id);

            $mcuPatient->saran = $request->input('saran');
            $mcuPatient->save();

            $resultsToUpdate = $request->input('individual_results');

            foreach ($resultsToUpdate as $resultData) {
                $mcuResult = McuResult::findOrFail($resultData['id']);

                $mcuResult->result = $resultData['result'];
                $mcuResult->save();
            }

            DB::commit(); 

            Log::info("MCU Patient ID {$id} details updated successfully.");

            return response()->json(['message' => 'Perubahan berhasil disimpan!', 'data' => $mcuPatient]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
             DB::rollBack();
             Log::warning("Update failed: Model not found.", ['id' => $id, 'error' => $e->getMessage()]);
             return response()->json(['error' => 'Data yang ingin diubah tidak ditemukan.'], 404);

        } catch (\Exception $e) {
            DB::rollBack(); 
            Log::error('Error updating MCU details: ' . $e->getMessage(), ['exception' => $e, 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Terjadi kesalahan saat menyimpan perubahan.', 'details' => $e->getMessage()], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        try {
            $mcuPatient = McuPatient::findOrFail($id);

            $mcuPatient->status = $request->input('status');
            $mcuPatient->save();

            Log::info("MCU Patient ID {$id} status updated successfully to {$request->input('status')}.");

            return response()->json([
                'message' => 'Status berhasil diperbarui',
                'data' => $mcuPatient 
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
             Log::warning("Status update failed: Model not found.", ['id' => $id, 'error' => $e->getMessage()]);
             return response()->json(['error' => 'Hasil MCU tidak ditemukan.'], 404);
        } catch (\Exception $e) {
            Log::error('Error updating MCU status: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Terjadi kesalahan saat memperbarui status.', 'details' => $e->getMessage()], 500);
        }
    }
    
}
