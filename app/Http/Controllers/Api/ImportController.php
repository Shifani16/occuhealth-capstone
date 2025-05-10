<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use App\Models\Patient;
use App\Models\McuPatient;
use App\Models\McuResult;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ImportController extends Controller
{
    protected function parseDate($value)
    {
        if (empty($value)) {
            return null;
        }

        if (is_numeric($value)) {
            try {
                return ExcelDate::excelToDateTimeObject(floor($value))->format('Y-m-d');
            } catch (\Exception $e) {
                Log::warning("Failed to parse numeric date value: {$value}");
            }
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
                 } catch (\Exception $e) {
                    Log::warning("Failed to parse date part after comma: {$datePart}");
                 }
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
                        Log::warning("Failed to parse date value generically: {$value}");
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

        DB::beginTransaction();

        try {
            $spreadsheet = IOFactory::load($file->getRealPath());

            $patientSheet = $spreadsheet->getSheet(0);
            $mcuResultSheet = $spreadsheet->getSheet(1);

            $patientSheetData = $patientSheet->toArray();

            $patientLabelToDbColumn = [
                'nama'                 => 'name',
                'tanggal_pemeriksaan'  => 'examination_date', 
                'no_rekam_medis'       => 'med_record_id',
                'no_pasien'            => 'patient_id',
                'jenis_kelamin'        => 'gender',
                'umur'                 => 'age',
                'tempat_tanggal_lahir' => 'birth_info',
                'alamat'               => 'address',
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

            // --- Validate Required Patient Data from Sheet 1 ---
            $medRecordId = $patientExcelData['med_record_id'] ?? null;
            $patientName = $patientExcelData['name'] ?? null; 
            $rawMcuDate = $patientExcelData['examination_date'] ?? null; 
            $rawBirthInfo = $patientExcelData['birth_info'] ?? null; 

            if (empty($medRecordId) || empty($patientName) || empty($rawMcuDate) || empty($rawBirthInfo)) {
                DB::rollBack();
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
                 DB::rollBack();
                 return response()->json(['error' => 'Invalid MCU examination date format in Sheet 1.'], 422);
            }


            // --- Find or Create Patient Record ---
            $patient = Patient::firstOrNew(['med_record_id' => $medRecordId]);

            $patient->patient_id = $patientExcelData['patient_id'] ?? 'N/A';
            $patient->name = $patientName;
            $patient->examination_date = $mcuSessionDate;

            $patient->examination_type = $patientExcelData['examination_type'] ?? 'MCU Umum'; 
            $patient->unit = $patientExcelData['unit'] ?? null;

            $status = $patientExcelData['status'] ?? 'Process';
            $allowedPatientStatuses = ['Delivered', 'Process', 'Cancelled'];
            $patient->status = in_array($status, $allowedPatientStatuses) ? $status : 'Process';

            $gender = $patientExcelData['gender'] ?? null;
            $allowedGenders = ['Laki-laki', 'Perempuan'];
            $patient->gender = in_array($gender, $allowedGenders) ? $gender : null;

            $patient->age = (int)($patientExcelData['age'] ?? 0); 
            $patient->birth_place = $birthPlace;
            $patient->birth_date = $birthDate;
            $patient->address = $patientExcelData['address'] ?? null;

            $patient->save(); 

            // --- Find or Create MCU Patient (Session) Record ---
            $mcuPatient = McuPatient::firstOrNew([
                'patient_id' => $patient->id,
                'examination_date' => $mcuSessionDate,
                'examination_type' => $patient->examination_type ?? 'MCU', 
            ]);

             $mcuPatient->name = $patient->name; 

             // Update status for the MCU session 
             $allowedMcuStatuses = ['Delivered', 'Process', 'Canceled'];
             // Use the patient's status
             $mcuPatient->status = in_array($patient->status, $allowedMcuStatuses) ? $patient->status : 'Process';


            $mcuPatient->save(); 

            // --- Process Sheet 2 (MCU Results - Horizontal Layout) ---
            $mcuResultSheetData = $mcuResultSheet->toArray();

            
            if (count($mcuResultSheetData) <= 1) {
                 DB::commit();
                 return response()->json(['message' => 'Pasien dan sesi MCU berhasil diimpor. Tidak ada hasil ditemukan di Sheet 2.']);
            }

            $mcuResultHeaders = array_map('trim', $mcuResultSheetData[0]);
            unset($mcuResultSheetData[0]);
            
            $categoryColIndex = array_search('Pemeriksaan', $mcuResultHeaders); 
            $resultColIndex = array_search('Hasil', $mcuResultHeaders);
            $resultDateColIndex = array_search('Tanggal Periksa', $mcuResultHeaders); 

            if ($categoryColIndex === false || $resultColIndex === false || $resultDateColIndex === false) {
                DB::rollBack();
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


                try {
                    $category = trim($row[$categoryColIndex]);
                    $resultValue = $row[$resultColIndex];
                    $rawResultDate = $row[$resultDateColIndex];

                    if (empty($category)) {
                         Log::warning("Skipping result row " . ($rowIndex + 2) . " in Sheet 2 due to empty category.");
                         continue;
                    }

                    $resultDate = $this->parseDate($rawResultDate);

                     if (!$resultDate) {
                        Log::warning("Skipping MCU result row due to invalid date format at row index " . ($rowIndex + 2) . " in Sheet 2. Value: {$rawResultDate}");
                        continue; 
                     }


                    McuResult::updateOrCreate(
                        [
                            'patient_id' => $mcuPatient->id,
                            'category' => $category,
                            'result_date' => $resultDate, 
                        ],
                        [
                            'result' => (string) $resultValue, 
                        ]
                    );

                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Error processing MCU result row.', ['exception' => $e, 'row_index' => $rowIndex + 2, 'sheet' => 2]);
                    return response()->json(['error' => 'Terjadi kesalahan saat memproses baris hasil MCU.', 'message' => $e->getMessage(), 'row' => $rowIndex + 2, 'sheet' => 2], 500); // Return 500 for server error
                }
            }

            DB::commit();

            return response()->json(['message' => 'Data MCU berhasil diimpor!']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('MCU Import Error: ' . $e->getMessage(), ['exception' => $e, 'file' => $file->getClientOriginalName()]);
            return response()->json(['error' => 'Terjadi kesalahan saat mengimpor file.', 'message' => $e->getMessage()], 500); // Return 500 for server error
        }
    }
}