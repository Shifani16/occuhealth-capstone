<?php

namespace App\Http\Controllers\dashboard;

use App\Models\Patient;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function getPatients()
    {
        $patients = Patient::all();

        return response()->json([
            'message' => 'Patients retrieved successfully',
            'patients' => $patients
        ]);
    }

    public function show($id)
    {
        return response()->json(Patient::findOrFail($id));
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
