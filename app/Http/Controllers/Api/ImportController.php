<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class ImportController extends Controller
{
    protected function parseDate($value)
    {
        if (empty($value)) return null;

        if (is_numeric($value)) {
            try {
                return ExcelDate::excelToDateTimeObject(floor($value))->format('Y-m-d');
            } catch (\Exception $e) {}
        }

        $value = (string) $value;
        $commaPos = strpos($value, ',');
        if ($commaPos !== false) {
            $datePart = trim(substr($value, $commaPos + 1));
            try {
                return Carbon::createFromFormat('d/m/Y', $datePart)->format('Y-m-d');
            } catch (\Exception $e) {
                try {
                    return Carbon::createFromFormat('m/d/Y', $datePart)->format('Y-m-d');
                } catch (\Exception $e) {}
            }
        }

        try {
            return Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
        } catch (\Exception $e) {
            try {
                return Carbon::createFromFormat('m/d/Y', $value)->format('Y-m-d');
            } catch (\Exception $e) {
                try {
                    return Carbon::createFromFormat('Y-m-d', $value)->format('Y-m-d');
                } catch (\Exception $e) {
                    try {
                        return Carbon::parse($value)->format('Y-m-d');
                    } catch (\Exception $e) {
                        return null;
                    }
                }
            }
        }
    }

    public function importMcu(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'File validation failed.',
                'messages' => $validator->errors()->all()
            ], 422);
        }

        $file = $request->file('file');

        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $patientSheet = $spreadsheet->getSheet(0);
            $mcuResultSheet = $spreadsheet->getSheet(1);
            $patientSheetData = $patientSheet->toArray();

            $patientLabelToDbColumn = [
                'nama' => 'name',
                'tanggal_pemeriksaan' => 'examination_date',
                'no_rekam_medis' => 'med_record_id',
                'no_pasien' => 'patient_id',
                'jenis_kelamin' => 'gender',
                'umur' => 'age',
                'tempat_tanggal_lahir' => 'birth_info',
                'alamat' => 'address',
            ];

            $patientExcelData = [];
            foreach ($patientSheetData as $row) {
                if (count($row) >= 2 && !empty($row[0])) {
                    $label = trim($row[0]);
                    $value = $row[1];
                    if (isset($patientLabelToDbColumn[$label])) {
                        $dbColumn = $patientLabelToDbColumn[$label];
                        $patientExcelData[$dbColumn] = $value;
                    }
                }
            }

            $medRecordId = $patientExcelData['med_record_id'] ?? null;
            $patientName = $patientExcelData['name'] ?? null;
            $rawMcuDate = $patientExcelData['examination_date'] ?? null;
            $rawBirthInfo = $patientExcelData['birth_info'] ?? null;

            if (empty($medRecordId) || empty($patientName) || empty($rawMcuDate) || empty($rawBirthInfo)) {
                return response()->json([
                    'error' => 'Missing required patient data in Sheet 1.',
                    'messages' => [
                        'Nomor Rekam Medis, Nama Pasien, Tanggal Pemeriksaan, atau Tempat/Tanggal Lahir tidak ditemukan di Sheet 1.'
                    ]
                ], 422);
            }

            $birthPlace = null;
            $birthDate = null;
            if ($rawBirthInfo) {
                $parts = explode(',', $rawBirthInfo);
                if (count($parts) > 1) {
                    $birthPlace = trim($parts[0]);
                    $birthDate = $this->parseDate(trim($parts[1]));
                } else {
                    $birthPlace = trim($rawBirthInfo);
                }
            }

            $mcuSessionDate = $this->parseDate($rawMcuDate);
            if (!$mcuSessionDate) {
                return response()->json(['error' => 'Invalid MCU examination date format in Sheet 1.'], 422);
            }

            $compiledResults = [];

            $mcuResultSheetData = $mcuResultSheet->toArray();
            if (count($mcuResultSheetData) > 1) {
                $mcuResultHeaders = array_map('trim', $mcuResultSheetData[0]);
                unset($mcuResultSheetData[0]);

                $categoryColIndex = array_search('Pemeriksaan', $mcuResultHeaders);
                $resultColIndex = array_search('Hasil', $mcuResultHeaders);
                $resultDateColIndex = array_search('Tanggal Periksa', $mcuResultHeaders);

                if ($categoryColIndex === false || $resultColIndex === false || $resultDateColIndex === false) {
                    return response()->json([
                        'error' => 'Missing required columns in Sheet 2.',
                        'messages' => ['Kolom "Pemeriksaan", "Hasil", atau "Tanggal Periksa" tidak ditemukan di Sheet 2.']
                    ], 422);
                }

                foreach ($mcuResultSheetData as $rowIndex => $row) {
                    if (empty(array_filter($row))) continue;

                    if (count($row) < max($categoryColIndex, $resultColIndex, $resultDateColIndex) + 1) {
                        Log::warning("Skipping row " . ($rowIndex + 2) . " in Sheet 2 due to insufficient columns.");
                        continue;
                    }

                    $category = trim($row[$categoryColIndex]);
                    $resultValue = $row[$resultColIndex];
                    $rawResultDate = $row[$resultDateColIndex];

                    if (empty($category)) {
                        Log::warning("Skipping result row " . ($rowIndex + 2) . " in Sheet 2 due to empty category.");
                        continue;
                    }

                    $resultDate = $this->parseDate($rawResultDate);
                    if (!$resultDate) {
                        Log::warning("Skipping MCU result row due to invalid date at row index " . ($rowIndex + 2));
                        continue;
                    }

                    $compiledResults[] = [
                        'category' => $category,
                        'result' => (string) $resultValue,
                        'result_date' => $resultDate,
                    ];
                }
            }

            $payload = [
                'patient' => [
                    'med_record_id' => $medRecordId,
                    'patient_id' => $patientExcelData['patient_id'] ?? 'N/A',
                    'name' => $patientName,
                    'gender' => $patientExcelData['gender'] ?? null,
                    'age' => (int)($patientExcelData['age'] ?? 0),
                    'birth_place' => $birthPlace,
                    'birth_date' => $birthDate,
                    'address' => $patientExcelData['address'] ?? null,
                    'examination_date' => $mcuSessionDate,
                    'examination_type' => $patientExcelData['examination_type'] ?? 'MCU Umum',
                    'unit' => $patientExcelData['unit'] ?? null,
                    'status' => $patientExcelData['status'] ?? 'Process',
                ],
                'mcu_patient' => [
                    'examination_date' => $mcuSessionDate,
                    'examination_type' => $patientExcelData['examination_type'] ?? 'MCU Umum',
                    'status' => $patientExcelData['status'] ?? 'Process',
                    'name' => $patientName,
                ],
                'mcu_results' => $compiledResults
            ];

            $response = Http::post('https://occuhelp-capstone-production.up.railway.app/api/import/mcu', $payload);

            if ($response->failed()) {
                return response()->json([
                    'error' => 'Failed to upload to production API',
                    'details' => $response->json()
                ], 500);
            }

            return response()->json(['message' => 'Data MCU berhasil diunggah ke server online!']);

        } catch (\Exception $e) {
            Log::error('MCU Import Error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'error' => 'Terjadi kesalahan saat memproses file.',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}