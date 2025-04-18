<?php

namespace App\Http\Controllers\dashboard;

use App\Models\McuPatient;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
        $patient = McuPatient::with('patient')->find($id);
        if (!$patient) {
            return response()->json(['message' => 'MCU Patient not found'], 404);
        }
        return response()->json($patient);
    }

    // Create new MCU patient
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
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

    // Update MCU patient
    public function update(Request $request, $id)
    {
        $mcuPatient = McuPatient::find($id);

        if (!$mcuPatient) {
            return response()->json(['message' => 'MCU Patient not found'], 404);
        }

        $mcuPatient->update($request->all());

        return response()->json([
            'message' => 'MCU patient updated successfully',
            'data' => $mcuPatient,
        ]);
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
    
}
