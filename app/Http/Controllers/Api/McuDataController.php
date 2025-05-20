<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\McuPatient; 
use App\Models\McuResult;
use App\Models\Patient; 
use Illuminate\Support\Facades\Log;

class McuDataController extends Controller
{
    public function getRawData(Request $request)
    {
        Log::info('Fetching raw data for MCU rekapitulasi.');

        try {
            $mcuPatients = McuPatient::with('patient')->get();

            $formattedData = [];

            foreach ($mcuPatients as $mcuPatient) {
                if (!$mcuPatient->patient) {
                    Log::warning("McuPatient ID {$mcuPatient->id} has no associated Patient. Skipping.");
                    continue;
                }

                $results = McuResult::where('patient_id', $mcuPatient->patient_id)
                                    ->whereDate('result_date', $mcuPatient->examination_date)
                                    ->get();

                $patientData = [
                    'id' => $mcuPatient->patient->id, 
                    'mcu_patient_id' => $mcuPatient->id, 
                    'name' => $mcuPatient->patient->name,
                    'Jenis Kelamin' => $mcuPatient->patient->gender,
                    'Usia' => $mcuPatient->patient->age,
                    'examination_date' => $mcuPatient->examination_date,
                    'unit' => $mcuPatient->patient->unit,
                    'jabatan' => $mcuPatient->patient->jabatan,
                    'ketenagaan' => $mcuPatient->patient->ketenagaan,
                    'saran' => $mcuPatient->saran,
                ];

                foreach ($results as $result) {
                    $patientData[$result->category] = $result->result;
                }

                $formattedData[] = $patientData;
            }

            Log::info('Successfully formatted raw data.', ['count' => count($formattedData)]);
            return response()->json($formattedData);

        } catch (\Exception $e) {
            Log::error('Error fetching raw MCU data: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'message' => 'Failed to fetch raw data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
