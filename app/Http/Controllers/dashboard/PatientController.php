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
        $latestMcuSubquery = DB::table('mcu_patients')
            ->selectRaw('*, ROW_NUMBER() OVER (PARTITION BY patient_id ORDER BY examination_date DESC, id DESC) as rn');

        $patient = Patient::where('patients.id', $id)
            ->leftJoinSub(
                $latestMcuSubquery,
                'ranked_mcus', 
                function($join) {
                    $join->on('ranked_mcus.patient_id', '=', 'patients.id')
                         ->where('ranked_mcus.rn', '=', 1);
                }
            )
            ->select(
                'patients.*', 
                'ranked_mcus.examination_date as examination_date', 
                'ranked_mcus.id as latest_mcu_id' 
            )
            ->first(); 

        if (!$patient) {
            return response()->json(['message' => 'Pasien tidak ditemukan.'], 404);
        }

        return response()->json($patient);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'med_record_id' => 'required|string|unique:patients,med_record_id',
            'patient_id' => 'required|string|unique:patients,patient_id',
            'name' => 'required|string|max:255',
            'unit' => 'nullable|string|max:255',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'age' => 'required|integer|min:0',
            'birth_date' => 'required|date',
            'jabatan' => 'nullable|string|max:255',
            'ketenagaan' => 'nullable|string|max:255',
            
        ]);

        $patient = Patient::create($validatedData);
        return response()->json($patient, 201);
    }

    public function update(Request $request, $id)
    {
        $patient = Patient::findOrFail($id);

        $validatedData = $request->validate([
             'med_record_id' => 'required|string|unique:patients,med_record_id,' . $id,
             'patient_id' => 'required|string|unique:patients,patient_id,' . $id,  
             'name' => 'required|string|max:255',
             'unit' => 'nullable|string|max:255',
             'gender' => 'required|in:Laki-laki,Perempuan',
             'age' => 'required|integer|min:0',
             'birth_date' => 'required|date',
             'jabatan' => 'nullable|string|max:255',
             'ketenagaan' => 'nullable|string|max:255',
           
         ]);

        $patient->update($validatedData);
        return response()->json($patient);
    }

    public function destroy($id)
    {
        Patient::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}