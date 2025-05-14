<?php

namespace App\Imports;

use App\Models\Patient;
use App\Models\McuPatient;
use App\Models\McuResult;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class McuImport implements ToCollection
{
    const EXTRA_TRIM_CHARS = " \t\n\r\0\x0B\xC2\xA0";

    const COLUMNS = [
        'tanggal_entry'         => 2, 
        'no_reg'                => 3,  
        'no_rm'                 => 4, 
        'nama_pasien'           => 5,  
        'jenis_kelamin'         => 6,  
        'usia'                  => 7, 
        'tanggal_lahir'         => 8,  
        'unit_kerja'            => 9,  
        'jabatan'               => 10, 
        'ketenagaan'            => 11, 
        'tanggal_pemeriksaan'   => 12,
        'bb_kg'                 => 13,
        'tb_cm'                 => 14, 
        'imt'                   => 15, 
        'kategori_IMT'          => 16, 
        'td'                    => 17, 
        'kategori_td'           => 18, 
        'riwayat_pribadi'       => 19, 
        'riwayat_keluarga'      => 20, 
        'anamnesa'              => 21, 
        'merokok'               => 22, 
        'alkohol'               => 23, 
        'olahraga'              => 24,
        'ekg'                   => 25, 
        'kreatinin'             => 26,
        'egfr'                  => 27, 
        'ureum'                 => 28, 
        'glukosa_puasa'         => 29, 
        'anti_hbs'              => 30, 
        'hbsag'                 => 31,
        'asam_urat'             => 32, 
        'basofil'               => 33,
        'eosinofil'             => 34,
        'hb'                    => 35, 
        'hematokrit'            => 36, 
        'trombosit'             => 37, 
        'eritrosit'             => 38, 
        'lekosit'               => 39, 
        'mch'                   => 40, 
        'mchc'                  => 41, 
        'mcv'                   => 42, 
        'limfosit'              => 43,
        'monosit'               => 44, 
        'neutrofil'             => 45, 
        'neutrofil_limfosit_ratio' => 46, 
        'rdw_cv'                => 47, 
        'koles_hdl'             => 48, 
        'koles_ldl'             => 49, 
        'trigliserid'           => 50, 
        'sgot'                  => 51, 
        'sgpt'                  => 52, 
        'kolesterol'            => 53, 
        'ph'                    => 54, 
        'warna'                 => 55, 
        'kejernihan'            => 56, 
        'leukosit'              => 57, 
        'eritrosit'             => 58, 
        'epitel'                => 59,
        'bakteri'               => 60, 
        'silinder'              => 61, 
        'kristal'               => 62,
        'berat_jenis'           => 63, 
        'protein'               => 64, 
        'glukosa'               => 65,
        'keton'                 => 66, 
        'darah'                 => 67,  
        'bilirubin'             => 68,
        'urobilinogen'          => 69, 
        'nitrit'                => 70, 
        'lekosit_esteras'       => 71, 
        'rasio_albumin_kreatinin' => 72, 
        'laboratorium'          => 73,
        'pemeriksaan_hepatitis' => 74, 
        'audiometri'            => 75,
        'radiologi'             => 76, 
        'fisik'                 => 77, 
        'visus'                 => 78, 
        'buta_warna'            => 79, 
        'saran'                 => 80 
    ];

    const RESULT_COLUMNS = [
        'bb_kg'                     => 'BB (Kg)',
        'tb_cm'                     => 'TB (Cm)',
        'imt'                       => 'IMT',
        'kategori_IMT'              => 'Kategori IMT',
        'td'                        => 'Tekanan Darah',
        'kategori_td'               => 'Kategori Tekanan Darah',
        'riwayat_pribadi'           => 'Riwayat Kesehatan Pribadi',
        'riwayat_keluarga'          => 'Riwayat Kesehatan Keluarga',
        'anamnesa'                  => 'Anamnesa',
        'merokok'                   => 'Merokok',
        'alkohol'                   => 'Alkohol',
        'olahraga'                  => 'Olahraga',
        'ekg'                       => 'EKG',
        'kreatinin'                 => 'Kreatinin',
        'egfr'                      => 'Egfr',
        'ureum'                     => 'Ureum',
        'glukosa_puasa'             => 'Glukosa Puasa',
        'anti_hbs'                  => 'Anti Hbs',
        'hbsag'                     => 'Hbsag',
        'asam_urat'                 => 'Asam Urat',
        'basofil'                   => 'Basofil',
        'eosinofil'                 => 'Eosinofil',
        'hb'                        => 'Hb',
        'hematokrit'                => 'Hematokrit',
        'trombosit'                 => 'Trombosit',
        'eritrosit'                 => 'Eritrosit',
        'lekosit'                   => 'Lekosit',
        'mch'                       => 'Mch',
        'mchc'                      => 'Mchc',
        'mcv'                       => 'Mcv',
        'limfosit'                  => 'Limfosit',
        'monosit'                   => 'Monosit',
        'neutrofil'                 => 'Neutrofil',
        'neutrofil_limfosit_ratio'  => 'Neutrofil Limfosit Ratio',
        'rdw_cv'                    => 'Rdw-Cv',
        'koles_hdl'                 => 'Koles Hdl',
        'koles_ldl'                 => 'Koles Ldl',
        'trigliserid'               => 'Trigliserid',
        'sgot'                      => 'Sgot',
        'sgpt'                      => 'Sgpt',
        'kolesterol'                => 'Kolesterol',
        'ph'                        => 'pH',
        'warna'                     => 'Warna',
        'kejernihan'                => 'Kejernihan',
        'leukosit'                  => 'Leukosit (Urine)',
        'eritrosit'                 => 'Eritrosit (Urine)',
        'epitel'                    => 'Epitel',
        'bakteri'                   => 'Bakteri',
        'silinder'                  => 'Silinder',
        'kristal'                   =>'Kristal',
        'berat_jenis'               =>'Berat Jenis',
        'protein'                   =>'Protein',
        'glukosa'                   =>'Glukosa (Urine)',
        'keton'                     =>'Keton',
        'darah'                     =>'Darah (Urine)',
        'bilirubin'                 =>'Bilirubin',
        'urobilinogen'              =>'Urobilinogen',
        'nitrit'                    =>'Nitrit',
        'lekosit_esteras'           =>'Lekosit Esterase',
        'rasio_albumin_kreatinin'   =>'Rasio Albumin/Kreatinin',
        'laboratorium'              =>'Laboratorium (Summary)',
        'pemeriksaan_hepatitis'     =>'Pemeriksaan Hepatitis',
        'audiometri'                =>'Audiometri',
        'radiologi'                 =>'Radiologi',
        'fisik'                     =>'Pemeriksaan Fisik',
        'visus'                     =>'Visus',
        'buta_warna'                =>'Buta Warna',
    ];

    const STATUS_MAPPING = [];

    public function collection(Collection $rows)
    {
        Log::info("MCU Import started.");
        Log::info("Total rows received (including headers): " . $rows->count());

        $dataRows = $rows->skip(2);
        Log::info("Data rows after skipping headers: " . $dataRows->count());

        $importedMcuPatientsCount = 0;
        $importedResultsCount = 0;
        $processedRowsCount = 0;
        $skippedRowsCount = 0;
        $failedRows = [];

        foreach ($dataRows as $index => $row) {
            $processedRowsCount++;
            $rowIndex = $index + 3;


            $noRegExcel = trim($row[self::COLUMNS['no_reg'] - 1] ?? '', self::EXTRA_TRIM_CHARS);
            $noRm = trim($row[self::COLUMNS['no_rm'] - 1] ?? '', self::EXTRA_TRIM_CHARS);
            $namaPasien = trim($row[self::COLUMNS['nama_pasien'] - 1] ?? '', self::EXTRA_TRIM_CHARS);
            $jenisKelaminExcel = trim($row[self::COLUMNS['jenis_kelamin'] - 1] ?? '', self::EXTRA_TRIM_CHARS);
            $usiaExcel = $row[self::COLUMNS['usia'] - 1] ?? null;
            $unitKerjaExcel = trim($row[self::COLUMNS['unit_kerja'] - 1] ?? '', self::EXTRA_TRIM_CHARS);
            $jabatanExcel = trim($row[self::COLUMNS['jabatan'] - 1] ?? '', self::EXTRA_TRIM_CHARS);
            $ketenagaanExcel = trim($row[self::COLUMNS['ketenagaan'] - 1] ?? '', self::EXTRA_TRIM_CHARS);
            $tanggalPemeriksaanRaw = $row[self::COLUMNS['tanggal_pemeriksaan'] - 1] ?? null;
            $tanggalLahirRaw = $row[self::COLUMNS['tanggal_lahir'] - 1] ?? null;
            $saranValueExcel = trim($row[self::COLUMNS['saran'] - 1] ?? '', self::EXTRA_TRIM_CHARS);

            $usiaTrimmed = trim((string) ($usiaExcel ?? ''), self::EXTRA_TRIM_CHARS);

            Log::info("Processing row {$rowIndex}. Extracted -> NO REG: '{$noRegExcel}', NO RM: '{$noRm}', Nama Pasien: '{$namaPasien}', Tanggal Periksa Raw: '{$tanggalPemeriksaanRaw}', Tanggal Lahir Raw: '{$tanggalLahirRaw}', Jenis Kelamin: '{$jenisKelaminExcel}', Usia Raw: '{$usiaExcel}', Usia Trimmed: '{$usiaTrimmed}'");

            if (empty($noRm) || empty($namaPasien) || empty($jenisKelaminExcel) || $usiaTrimmed === '' || empty($tanggalLahirRaw) || is_null($tanggalPemeriksaanRaw) || $tanggalPemeriksaanRaw === '' || empty($noRegExcel)) {
                 $skippedRowsCount++;
                 $reason = 'Missing critical data: ';
                 if (empty($noRm)) $reason .= 'NO RM empty. ';
                 if (empty($namaPasien)) $reason .= 'Nama Pasien empty. ';
                 if (empty($jenisKelaminExcel)) $reason .= 'Jenis Kelamin empty. ';
                 if ($usiaTrimmed === '') $reason .= 'Usia empty after trim. ';
                 if (empty($tanggalLahirRaw)) $reason .= 'Tanggal Lahir empty. ';
                 if (is_null($tanggalPemeriksaanRaw) || $tanggalPemeriksaanRaw === '') $reason .= 'Tanggal Periksa empty. ';
                 if (empty($noRegExcel)) $reason .= 'NO REG empty (needed for med_record_id). ';

                 $failedRows[] = ['row' => $rowIndex, 'reason' => trim($reason)];
                 Log::warning("Row {$rowIndex} skipped: " . trim($reason));
                 continue;
             }

             $validGenders = ['Laki-laki', 'Perempuan'];
             if (!in_array($jenisKelaminExcel, $validGenders)) {
                  Log::warning("Row {$rowIndex}: Invalid gender '{$jenisKelaminExcel}'. Skipping row.");
                  $skippedRowsCount++;
                 $failedRows[] = ['row' => $rowIndex, 'reason' => 'Invalid gender value: "' . ($jenisKelaminExcel ?? 'NULL') . '". Must be Laki-laki or Perempuan.'];
                 Log::info("Row {$rowIndex}: Skipping row due to invalid gender.");
                 continue;
             }

            $tanggalPemeriksaan = $this->parseExcelDate($tanggalPemeriksaanRaw, $rowIndex, 'Tanggal Pemeriksaan');
            $tanggalLahir = $this->parseExcelDate($tanggalLahirRaw, $rowIndex, 'Tanggal Lahir');

            if (empty($tanggalPemeriksaan)) {
                 $skippedRowsCount++;
                 $failedRows[] = ['row' => $rowIndex, 'reason' => 'Failed to parse Tanggal Pemeriksaan. Raw: ' . ($tanggalPemeriksaanRaw ?? 'NULL')];
                 Log::warning("Row {$rowIndex} skipped: Failed to parse Tanggal Pemeriksaan.");
                 continue;
            }
             if (empty($tanggalLahir)) {
                 $skippedRowsCount++;
                 $failedRows[] = ['row' => $rowIndex, 'reason' => 'Failed to parse Tanggal Lahir. Raw: ' . ($tanggalLahirRaw ?? 'NULL')];
                 Log::warning("Row {$rowIndex} skipped: Failed to parse Tanggal Lahir.");
                 continue;
            }

            DB::beginTransaction();
            Log::info("Row {$rowIndex}: Transaction started after validation and parsing.");

            try {
                Log::info("Row {$rowIndex}: Finding or creating Patient with patient_id '{$noRm}'");
                $patient = Patient::firstOrNew(['patient_id' => $noRm]);

                $patient->med_record_id = $noRegExcel;
                Log::info("Row {$rowIndex}: Assigned med_record_id = '{$patient->med_record_id}' (from Excel NO REG).");

                $patient->name = $namaPasien;
                $patient->gender = $jenisKelaminExcel; 
                $patient->age = (int) $usiaTrimmed;
                $patient->birth_date = $tanggalLahir;
                $patient->unit = $unitKerjaExcel; 
                $patient->jabatan = $jabatanExcel;
                $patient->ketenagaan = $ketenagaanExcel; 

                Log::info("Row {$rowIndex}: Patient attributes before save -> med_record_id: '{$patient->med_record_id}', patient_id: '{$patient->patient_id}', Name: '{$patient->name}', Gender: '{$patient->gender}', Age: {$patient->age}, Birth Date: {$patient->birth_date}, Unit: '{$patient->unit}', Jabatan: '{$patient->jabatan}', Ketenagaan: '{$patient->ketenagaan}'");


                $patient->save();
                Log::info("Row {$rowIndex}: Patient saved. ID: {$patient->id}");

                 Log::info("Row {$rowIndex}: Finding or creating McuPatient for patient ID {$patient->id} and date {$tanggalPemeriksaan}");
                $mcuPatient = McuPatient::firstOrNew([
                    'patient_id' => $patient->id,
                    'examination_date' => $tanggalPemeriksaan,
                ]);

                $mcuPatient->status = 'Completed';
                Log::info("Row {$rowIndex}: Setting McuPatient status to 'Delivered'.");

                $mcuPatient->name = $namaPasien;
                $mcuPatient->examination_type = 'MCU';

                $mcuPatient->saran = $saranValueExcel;
                Log::info("Row {$rowIndex}: McuPatient Saran: '{$mcuPatient->saran}'");


                $mcuPatient->save();
                Log::info("Row {$rowIndex}: McuPatient saved. ID: {$mcuPatient->id}. Status: {$mcuPatient->status}. Examination Date: {$mcuPatient->examination_date}");
                $importedMcuPatientsCount++;


                // --- Delete existing MCU Results ---
                $deletedResultsCount = McuResult::where('patient_id', $patient->id)
                    ->where('result_date', $tanggalPemeriksaan)
                    ->delete();
                Log::info("Row {$rowIndex}: Deleted {$deletedResultsCount} existing results for patient ID {$patient->id} and date {$tanggalPemeriksaan}.");


                // --- Process and Create MCU Results ---
                Log::info("Row {$rowIndex}: Starting result processing loop.");
                foreach (self::RESULT_COLUMNS as $colKey => $categoryName) {
                    if (!array_key_exists($colKey, self::COLUMNS)) {
                         Log::warning("Row {$rowIndex}: RESULT_COLUMNS key '{$colKey}' does not exist in COLUMNS mapping. Skipping result.");
                         continue;
                    }

                    $excelColumnIndex1Based = self::COLUMNS[$colKey];
                    $excelColumnIndex0Based = $excelColumnIndex1Based - 1;

                    if (!isset($row[$excelColumnIndex0Based])) {
                         Log::warning("Row {$rowIndex}: Result Column '{$colKey}' (Excel index {$excelColumnIndex1Based}) is missing in the row data array from Excel. Skipping result.");
                         continue;
                    }

                    $resultValue = $row[$excelColumnIndex0Based];
                    $trimmedResultValue = trim((string) $resultValue, self::EXTRA_TRIM_CHARS);

                    if ($trimmedResultValue !== '' && strtolower($trimmedResultValue) !== 'null') {
                         Log::info("Row {$rowIndex}: Creating result for category '{$categoryName}' with value '{$trimmedResultValue}' (raw: '{$resultValue}'). Patient ID: {$patient->id}, Result Date: {$tanggalPemeriksaan}");
                        McuResult::create([
                            'patient_id' => $patient->id,
                            'category' => $categoryName,
                            'result' => $trimmedResultValue,
                            'result_date' => $tanggalPemeriksaan,
                        ]);
                        $importedResultsCount++;
                         Log::info("Row {$rowIndex}: Result for '{$categoryName}' saved successfully.");
                    } else {
                         Log::info("Row {$rowIndex}: Skipping empty result for category '{$categoryName}' (raw: '{$resultValue}').");
                    }
                }
                 Log::info("Row {$rowIndex}: Finished result processing loop.");


                DB::commit();
                Log::info("Row {$rowIndex}: Transaction committed successfully.");

            } catch (\Exception $e) {
                DB::rollBack();
                $skippedRowsCount++;
                $failedRows[] = ['row' => $rowIndex, 'reason' => 'Database or processing error: ' . $e->getMessage()];
                Log::error("Row {$rowIndex} processing failed: " . $e->getMessage(), ['exception' => $e, 'row_data' => $row->toArray()]);
                Log::info("Row {$rowIndex}: Transaction rolled back due to error.");
            }
        }

        Log::info("MCU Import finished.");
        Log::info("Summary: Processed rows: {$processedRowsCount}, Skipped rows: {$skippedRowsCount}, Imported McuPatients (rows with valid data): {$importedMcuPatientsCount}, Imported Results: {$importedResultsCount}.");
        if (!empty($failedRows)) {
            Log::warning("Failed rows details:", $failedRows);
        }
    }

     // Helper method to handle Excel dates more robustly
     private function parseExcelDate($value, $rowIndex, $fieldName)
     {
         if (is_null($value) || $value === '') {
             Log::debug("Row {$rowIndex}: Date value for {$fieldName} is empty.");
             return null;
         }

         $value = (string) $value;
         Log::debug("Row {$rowIndex}: Attempting to parse date for {$fieldName}. Raw value: '{$value}'");

         if (is_numeric($value) && $value > 0) {
             try {
                 $dateObj = ExcelDate::excelToDateTimeObject((float) $value);
                 if ($dateObj instanceof \DateTimeInterface) {
                      $formattedDate = Carbon::instance($dateObj)->format('Y-m-d');
                      Log::debug("Row {$rowIndex}: Parsed numeric Excel date for {$fieldName}: {$formattedDate}");
                     return $formattedDate;
                 } else {
                     Log::warning("Row {$rowIndex}: ExcelDate::excelToDateTimeObject did not return a valid date object for {$fieldName}. Value: '{$value}'");
                 }
             } catch (\Exception $e) {
                 Log::warning("Row {$rowIndex}: Failed to parse numeric Excel date for {$fieldName}. Value: '{$value}'. Error: " . $e->getMessage());
             }
         }

         $trimmedValue = trim($value, self::EXTRA_TRIM_CHARS);
          Log::debug("Row {$rowIndex}: Attempting string parse for {$fieldName}. Trimmed value: '{$trimmedValue}'");

         if (!empty($trimmedValue)) {
             try {
                 $date = Carbon::parse($trimmedValue)->format('Y-m-d');
                  Log::debug("Row {$rowIndex}: Parsed string date for {$fieldName}: {$date}");
                 return $date;
             } catch (\Exception $e) {
                 Log::warning("Row {$rowIndex}: Failed to parse string date for {$fieldName}. Value: '" . $trimmedValue . "'. Error: " . $e->getMessage());
             }
         }

         Log::warning("Row {$rowIndex}: Value for {$fieldName} could not be parsed as date after all attempts. Value: '" . ($value ?? 'NULL') . "'");
         return null;
     }
}