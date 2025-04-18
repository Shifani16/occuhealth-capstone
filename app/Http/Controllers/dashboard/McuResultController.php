<?php

namespace App\Http\Controllers\dashboard;

use App\Models\McuResult;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class McuResultController extends Controller
{
    public function index()
    {
        return McuResult::with('mcuPatient')->get();
    }

    // Get MCU results for a specific MCU patient
    public function indexByPatient($id)
    {
        return McuResult::where('mcu_patient_id', $id)->get();
    }

    // Get one MCU result
    public function show($id)
    {
        $result = McuResult::find($id);
        if (!$result) {
            return response()->json(['message' => 'MCU Result not found'], 404);
        }
        return response()->json($result);
    }

    // Create new MCU result
    public function store(Request $request)
    {
        $request->validate([
            'mcu_patient_id' => 'required|exists:mcu_patients,id',
            'parameter' => 'required|string',
            'value' => 'required|string',
            'reference' => 'nullable|string',
            'unit' => 'nullable|string',
        ]);

        $result = McuResult::create($request->all());

        return response()->json([
            'message' => 'MCU result created successfully',
            'data' => $result,
        ], 201);
    }

    // Update MCU result
    public function update(Request $request, $id)
    {
        $result = McuResult::find($id);

        if (!$result) {
            return response()->json(['message' => 'MCU Result not found'], 404);
        }

        $result->update($request->all());

        return response()->json([
            'message' => 'MCU result updated successfully',
            'data' => $result,
        ]);
    }

    // Delete MCU result
    public function destroy($id)
    {
        $result = McuResult::find($id);

        if (!$result) {
            return response()->json(['message' => 'MCU Result not found'], 404);
        }

        $result->delete();

        return response()->json(['message' => 'MCU result deleted successfully']);
    }
}
