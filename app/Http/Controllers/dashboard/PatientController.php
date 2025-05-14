<?php

namespace App\Http\Controllers\dashboard;

use App\Models\Patient;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 

class PatientController extends Controller
{
    public function getPatients(Request $request) 
    {
        $patients = Patient::leftJoinSub(
            DB::table('mcu_patients')
                ->select('patient_id', DB::raw('MAX(examination_date) as latest_examination_date'))
                ->groupBy('patient_id'),
            'latest_mcu',
            'patients.id', 
            'latest_mcu.patient_id'
        )
        ->select('patients.*', 'latest_mcu.latest_examination_date as examination_date') 
        ->get(); 

        return response()->json(['patients' => $patients]);
    }

    public function show($id)
    {
        $patient = Patient::where('patients.id', $id) 
            ->leftJoinSub(
                DB::table('mcu_patients')
                    ->select('patient_id', DB::raw('MAX(examination_date) as latest_examination_date'))
                    ->groupBy('patient_id'),
                'latest_mcu', 
                'patients.id', 
                '=',
                'latest_mcu.patient_id' 
            )
            ->select('patients.*', 'latest_mcu.latest_examination_date as examination_date')
            ->first(); 

        if (!$patient) {
            return response()->json(['message' => 'Pasien tidak ditemukan.'], 404);
        }

        return response()->json($patient);
    }

    public function store(Request $request)
    {
        $patient = Patient::create($request->all());
        return response()->json($patient, 201);
    }

    public function update(Request $request, $id)
    {
        $patient = Patient::findOrFail($id);
        $patient->update($request->all());
        return response()->json($patient);
    }

    public function destroy($id)
    {
        Patient::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}
