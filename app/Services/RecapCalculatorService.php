<?php

namespace App\Services;

/**
 * Service class to perform rekapitulasi calculations based on patient data.
 * Translates logic from recapCalculator.js.
 */
class RecapCalculatorService
{
    /**
     * Helper to safely get and convert a value to a float.
     * Handles cases where value might be null, empty string, non-numeric string,
     * or a formula string like "=M3/...". Returns null for invalid/missing data
     * suitable for numeric calculations.
     *
     * @param array $item The data item (associative array)
     * @param string $key The key to access within the item
     * @return float|null The numeric value as float, or null if invalid/missing.
     */
    private function getNumericValue(array $item, string $key): ?float
    {
        // Check if the key exists and value is not null
        if (!isset($item[$key]) || $item[$key] === null) {
            return null; // Use null for truly missing/null data
        }

        $value = $item[$key];

        // Handle string values
        if (is_string($value)) {
            $value = trim($value); // Trim whitespace

            // Handle empty strings
            if ($value === '') {
                return null;
            }

            // Handle formulas (common in spreadsheet imports). Assume any string starting with '=' is a formula.
            if (str_starts_with($value, '=')) {
                return null; // Treat formulas as invalid data for calculation
            }

            // Try parsing as float. is_numeric handles integers, floats, and numeric strings.
            if (is_numeric($value)) {
                // Ensure scientific notation strings (e.g., "1.23E+4") are handled correctly by casting
                return (float) $value;
            } else {
                // If it's a non-numeric string (after trimming and checking formulas), it's invalid for calculation
                return null;
            }
        }

        // Handle numeric values directly (int or float)
        if (is_numeric($value)) {
            return (float) $value;
        }

        // Any other type is not valid for numeric calculation
        return null;
    }

     /**
      * Helper to parse TD string like "120/80".
      * Replicates logic from JS parseTD if it was complex, handles simple slash separation.
      * Returns an array with sistolik and diastolik as float or null.
      *
      * @param string|null $tdString The raw TD string value
      * @return array Associative array ['sistolik' => float|null, 'diastolik' => float|null]
      */
    private function parseTD(?string $tdString): array
    {
         // Handle null or non-string input gracefully
         if (!is_string($tdString) || trim($tdString) === '') {
             return ['sistolik' => null, 'diastolik' => null];
         }

         $tdString = trim($tdString);

         // Look for a "/" separator
         if (str_contains($tdString, '/')) {
             $parts = explode('/', $tdString);
             if (count($parts) === 2) {
                 $sistolikStr = trim($parts[0]);
                 $diastolikStr = trim($parts[1]);

                 // Check if both parts are numeric
                 if (is_numeric($sistolikStr) && is_numeric($diastolikStr)) {
                      return ['sistolik' => (float)$sistolikStr, 'diastolik' => (float)$diastolikStr];
                 }
             }
         }

         // If not in "sistolik/diastolik" format, or parts are not numeric, return nulls
         return ['sistolik' => null, 'diastolik' => null];
    }

     /**
      * Helper to parse Kolesterol Total value, handling strings like "233 Borderline" or just "205".
      * Replicates logic from JS parseKolesterolTotal. Returns the numeric part as float or null.
      *
      * @param mixed $kolesterolValue The raw Kolesterol value (can be string, number, null)
      * @return float|null The numeric value as float, or null if invalid/missing.
      */
     private function parseKolesterolTotal(mixed $kolesterolValue): ?float
     {
          // Handle null, empty string, or directly numeric input
         if ($kolesterolValue === null || $kolesterolValue === '') {
             return null;
         }

         if (is_numeric($kolesterolValue)) {
             return (float) $kolesterolValue;
         }

         if (is_string($kolesterolValue)) {
             $kolesterolValue = trim($kolesterolValue);
             // Explode by space and check if the first part is numeric
             $parts = explode(' ', $kolesterolValue);
             if (count($parts) > 0 && is_numeric($parts[0])) {
                 return (float) $parts[0];
             }
         }

         // If none of the above cases match, it's not a valid value for parsing
         return null;
     }


    /**
     * Rekapitulasi Gangguan Metabolisme Glukosa
     * Analyzes 'Glukosa Puasa' values.
     *
     * @param array $data Array of patient data items (associative arrays). Each item represents one MCU encounter's data.
     * @returns array Rekapitulasi data [{ 'Kategori': string, 'Jumlah': number }]
     */
    public function calculateGlukosaRekap(array $data): array
    {
        $normal = 0;
        $peningkatan = 0;
        $tidakAdaData = 0;

        foreach ($data as $item) {
            // Use the helper to get a safe numeric value for 'Glukosa Puasa'
            $glukosaPuasa = $this->getNumericValue($item, 'Glukosa Puasa');

            if ($glukosaPuasa === null) {
                $tidakAdaData++;
            } elseif ($glukosaPuasa >= 70 && $glukosaPuasa <= 105) {
                $normal++;
            } elseif ($glukosaPuasa > 105) {
                $peningkatan++;
            }
             // Values < 70 are not explicitly categorized as per the JS logic.
        }

        // Build the results array, including only categories with counts > 0
        $results = [];
        if ($normal > 0) $results[] = ['Kategori' => 'Normal', 'Jumlah' => $normal];
        if ($peningkatan > 0) $results[] = ['Kategori' => 'Peningkatan Glukosa Puasa', 'Jumlah' => $peningkatan];
        if ($tidakAdaData > 0) $results[] = ['Kategori' => 'Tidak Ada Data', 'Jumlah' => $tidakAdaData];

        return $results;
    }

    /**
     * Rekapitulasi Gangguan Status Gizi (BMI)
     * Analyzes 'IMT' values.
     *
     * @param array $data Array of patient data items.
     * @returns array Rekapitulasi data [{ 'Gangguan Status Gizi': string, 'Jumlah': number }]
     */
    public function calculateStatusGiziRekap(array $data): array
    {
        $underweight = 0;
        $normal = 0;
        $overweight = 0;
        $obeseI = 0;
        $obeseII = 0;
        $obeseIII = 0;
        $tidakAdaData = 0;

        foreach ($data as $item) {
            $imt = $this->getNumericValue($item, 'IMT'); // Use the helper for 'IMT'

            if ($imt === null) {
                $tidakAdaData++;
            } elseif ($imt < 18.4) {
                $underweight++;
            } elseif ($imt >= 18.5 && $imt <= 24.9) {
                $normal++;
            } elseif ($imt >= 25 && $imt <= 29.9) {
                $overweight++;
            } elseif ($imt >= 30 && $imt <= 34.9) {
                $obeseI++;
            } elseif ($imt >= 35 && $imt <= 39.9) {
                $obeseII++;
            } elseif ($imt >= 40) {
                $obeseIII++;
            }
        }

        $results = [];
        if ($underweight > 0) $results[] = ['Gangguan Status Gizi' => 'Underweight', 'Jumlah' => $underweight];
        if ($normal > 0) $results[] = ['Gangguan Status Gizi' => 'Normal weight', 'Jumlah' => $normal];
        if ($overweight > 0) $results[] = ['Gangguan Status Gizi' => 'Overweight', 'Jumlah' => $overweight];
        if ($obeseI > 0) $results[] = ['Gangguan Status Gizi' => 'Obese (Class I)', 'Jumlah' => $obeseI];
        if ($obeseII > 0) $results[] = ['Gangguan Status Gizi' => 'Obese (Class II)', 'Jumlah' => $obeseII];
        if ($obeseIII > 0) $results[] = ['Gangguan Status Gizi' => 'Obese (Class III)', 'Jumlah' => $obeseIII];
        if ($tidakAdaData > 0) $results[] = ['Gangguan Status Gizi' => 'Tidak Ada Data', 'Jumlah' => $tidakAdaData];


        return $results;
    }

    /**
     * Rekapitulasi Gangguan Tekanan Darah
     * Analyzes 'TD' or 'Tekanan Darah' values using sequential classification
     * to assign each patient to the highest relevant category based on guidelines.
     *
     * @param array $data Array of patient data items.
     * @returns array Rekapitulasi data [{ Kategori: string, Jumlah: number }]
     */
    public function calculateTekananDarahRekap(array $data): array
    {
        $tdCounts = [
            'Optimal' => 0,
            'Normal' => 0,
            'Prehipertensi' => 0,
            'Hipertensi Grade I' => 0,
            'Hipertensi Grade II' => 0,
            'Hipertensi Grade III' => 0,
            'Hipertensi Sistolik Terisolasi' => 0,
            'Tidak Ada Data' => 0
        ];

        foreach ($data as $item) {
            // Try both 'TD' and 'Tekanan Darah' keys, parse the string value
            $tdString = $item['TD'] ?? $item['Tekanan Darah'] ?? null;
            $parsed = $this->parseTD($tdString); // Use the helper for TD parsing
            $sistolik = $parsed['sistolik'];
            $diastolik = $parsed['diastolik'];

            // Check for missing/invalid data first
            if ($sistolik === null || $diastolik === null) {
                $tdCounts['Tidak Ada Data']++;
                continue; // Move to the next item
            }

            // Classify based on highest category met (sequential check downwards)
            if ($sistolik >= 180 || $diastolik >= 110) {
                $tdCounts['Hipertensi Grade III']++;
            } elseif (($sistolik >= 160 && $sistolik <= 179) || ($diastolik >= 100 && $diastolik <= 109)) {
                $tdCounts['Hipertensi Grade II']++;
            } elseif (($sistolik >= 140 && $sistolik <= 159) || ($diastolik >= 90 && $diastolik <= 99)) {
                $tdCounts['Hipertensi Grade I']++;
            } elseif ($sistolik >= 140 && $diastolik < 90) { // Isolated Systolic Hypertension (ISH) S>=140 AND D<90
                // Must not have been caught by Grade II or III
                $tdCounts['Hipertensi Sistolik Terisolasi']++;
            } elseif (($sistolik >= 130 && $sistolik <= 139) || ($diastolik >= 85 && $diastolik <= 89)) { // Prehipertensi
                 $tdCounts['Prehipertensi']++;
            } elseif (($sistolik >= 120 && $sistolik <= 129) || ($diastolik >= 80 && $diastolik <= 84)) { // Normal
                 $tdCounts['Normal']++;
            } elseif ($sistolik < 120 && $diastolik < 80) { // Optimal
                 $tdCounts['Optimal']++;
            }
             // If a value is numeric but doesn't fit any range (e.g., S=110, D=70), it's not counted in a specific category
             // as per the JS logic structure.

        }

        // Build the results array, only including categories with counts > 0
        $results = [];
        if ($tdCounts['Optimal'] > 0) $results[] = ['Kategori' => 'Optimal', 'Jumlah' => $tdCounts['Optimal']];
        if ($tdCounts['Normal'] > 0) $results[] = ['Kategori' => 'Normal', 'Jumlah' => $tdCounts['Normal']];
        if ($tdCounts['Prehipertensi'] > 0) $results[] = ['Kategori' => 'Prehipertensi', 'Jumlah' => $tdCounts['Prehipertensi']];
        if ($tdCounts['Hipertensi Grade I'] > 0) $results[] = ['Kategori' => 'Hipertensi Grade I', 'Jumlah' => $tdCounts['Hipertensi Grade I']];
        if ($tdCounts['Hipertensi Grade II'] > 0) $results[] = ['Kategori' => 'Hipertensi Grade II', 'Jumlah' => $tdCounts['Hipertensi Grade II']];
        if ($tdCounts['Hipertensi Grade III'] > 0) $results[] = ['Kategori' => 'Hipertensi Grade III', 'Jumlah' => $tdCounts['Hipertensi Grade III']];
        if ($tdCounts['Hipertensi Sistolik Terisolasi'] > 0) $results[] = ['Kategori' => 'Hipertensi Sistolik Terisolasi', 'Jumlah' => $tdCounts['Hipertensi Sistolik Terisolasi']];
        if ($tdCounts['Tidak Ada Data'] > 0) $results[] = ['Kategori' => 'Tidak Ada Data', 'Jumlah' => $tdCounts['Tidak Ada Data']];

        return $results;
    }

    /**
     * Rekapitulasi Kelompok Umur Peserta MCU (<35 vs >=35 by Gender)
     * Analyzes 'Usia' and 'Jenis Kelamin'.
     *
     * @param array $data Array of patient data items.
     * @returns array Rekapitulasi data [{ 'Jenis Kelamin': string, Kategori: string, Jumlah: number }]
     */
    public function calculateUmurRekap(array $data): array
    {
        $counts = [
            'Laki-laki_<35' => 0,
            'Laki-laki_>=35' => 0,
            'Perempuan_<35' => 0,
            'Perempuan_>=35' => 0,
            'Tidak Ada Data' => 0
        ];

        foreach ($data as $item) {
            $usia = $this->getNumericValue($item, 'Usia'); // Use helper for age
            $jenisKelamin = $item['Jenis Kelamin'] ?? null; // Get gender string

            // Check for missing/invalid data for age OR gender
            if ($usia === null || !is_string($jenisKelamin) || trim($jenisKelamin) === '') {
                $counts['Tidak Ada Data']++;
                continue; // Move to the next item
            }

            // Standardize gender string for key comparison
            $genderKey = strtolower(trim($jenisKelamin));

            $ageCategory = ($usia !== null && $usia < 35) ? '<35' : '>=35'; // Use >=35 if age is >= 35 or if age is numeric but not < 35

            if ($genderKey === 'laki-laki') {
                 $counts["Laki-laki_{$ageCategory}"]++;
             } elseif ($genderKey === 'perempuan') {
                 $counts["Perempuan_{$ageCategory}"]++;
             } else {
                 // Handle unrecognized gender string even if age is valid
                 $counts['Tidak Ada Data']++;
             }
        }

        // Build results array, including only categories with counts > 0
        $results = [];
        if ($counts['Laki-laki_<35'] > 0) $results[] = ['Jenis Kelamin' => 'Laki-laki', 'Kategori' => '<35 Tahun', 'Jumlah' => $counts['Laki-laki_<35']];
        if ($counts['Laki-laki_>=35'] > 0) $results[] = ['Jenis Kelamin' => 'Laki-laki', 'Kategori' => '>=35 Tahun', 'Jumlah' => $counts['Laki-laki_>=35']];
        if ($counts['Perempuan_<35'] > 0) $results[] = ['Jenis Kelamin' => 'Perempuan', 'Kategori' => '<35 Tahun', 'Jumlah' => $counts['Perempuan_<35']];
        if ($counts['Perempuan_>=35'] > 0) $results[] = ['Jenis Kelamin' => 'Perempuan', 'Kategori' => '>=35 Tahun', 'Jumlah' => $counts['Perempuan_>=35']];
        if ($counts['Tidak Ada Data'] > 0) $results[] = ['Jenis Kelamin' => 'Tidak Diketahui', 'Kategori' => 'Tidak Ada Data', 'Jumlah' => $counts['Tidak Ada Data']];

        return $results;
    }

    /**
     * Rekapitulasi Kadar Hemoglobin
     * Analyzes 'Hb' and 'Jenis Kelamin'.
     *
     * @param array $data Array of patient data items.
     * @returns array Rekapitulasi data [{ 'Jenis Kelamin': string, Kategori: string, Jumlah: number }]
     */
    public function calculateHbRekap(array $data): array
    {
        $counts = [
            'Laki-laki_Anemia' => 0,
            'Laki-laki_Tidak Anemia' => 0,
            'Perempuan_Anemia' => 0,
            'Perempuan_Tidak Anemia' => 0,
            'Tidak Ada Data' => 0
        ];

        foreach ($data as $item) {
            $hb = $this->getNumericValue($item, 'Hb'); // Use helper for Hb
            $jenisKelamin = $item['Jenis Kelamin'] ?? null; // Get gender string

            // Check for missing/invalid data for Hb OR gender
            if ($hb === null || !is_string($jenisKelamin) || trim($jenisKelamin) === '') {
                $counts['Tidak Ada Data']++;
                continue; // Move to the next item
            }

            // Standardize gender string
            $genderKey = strtolower(trim($jenisKelamin));

            if ($genderKey === 'laki-laki') {
                if ($hb < 13) {
                    $counts['Laki-laki_Anemia']++;
                } else { // Hb is >= 13 and is numeric
                    $counts['Laki-laki_Tidak Anemia']++;
                }
            } elseif ($genderKey === 'perempuan') {
                if ($hb < 12) {
                    $counts['Perempuan_Anemia']++;
                } else { // Hb is >= 12 and is numeric
                    $counts['Perempuan_Tidak Anemia']++;
                }
            } else {
                 // Handle unrecognized gender string
                 $counts['Tidak Ada Data']++;
            }
        }

        // Build results array, including only categories with counts > 0
        $results = [];
        if ($counts['Laki-laki_Anemia'] > 0) $results[] = ['Jenis Kelamin' => 'Laki-laki', 'Kategori' => 'Anemia', 'Jumlah' => $counts['Laki-laki_Anemia']];
        if ($counts['Laki-laki_Tidak Anemia'] > 0) $results[] = ['Jenis Kelamin' => 'Laki-laki', 'Kategori' => 'Tidak Anemia', 'Jumlah' => $counts['Laki-laki_Tidak Anemia']];
        if ($counts['Perempuan_Anemia'] > 0) $results[] = ['Jenis Kelamin' => 'Perempuan', 'Kategori' => 'Anemia', 'Jumlah' => $counts['Perempuan_Anemia']];
        if ($counts['Perempuan_Tidak Anemia'] > 0) $results[] = ['Jenis Kelamin' => 'Perempuan', 'Kategori' => 'Tidak Anemia', 'Jumlah' => $counts['Perempuan_Tidak Anemia']];
        if ($counts['Tidak Ada Data'] > 0) $results[] = ['Jenis Kelamin' => 'Tidak Diketahui', 'Kategori' => 'Tidak Ada Data', 'Jumlah' => $counts['Tidak Ada Data']];

        return $results;
    }

    /**
     * Rekapitulasi Pemeriksaan Creatinin Darah
     * Analyzes 'Kreatinin' values.
     *
     * @param array $data Array of patient data items.
     * @returns array Rekapitulasi data [{ Kategori: string, Jumlah: number }]
     */
    public function calculateCreatininRekap(array $data): array
    {
        $normal = 0;
        $tidakNormal = 0;
        $tidakAdaData = 0;

        foreach ($data as $item) {
            $creatinin = $this->getNumericValue($item, 'Kreatinin'); // Use helper for Kreatinin

            if ($creatinin === null) {
                $tidakAdaData++;
            } elseif ($creatinin >= 0.6 && $creatinin <= 1.2) {
                $normal++;
            } else { // creatinin < 0.6 or > 1.2
                $tidakNormal++;
            }
        }

        $results = [];
        if ($normal > 0) $results[] = ['Kategori' => 'Normal', 'Jumlah' => $normal];
        if ($tidakNormal > 0) $results[] = ['Kategori' => 'Tidak Normal', 'Jumlah' => $tidakNormal];
        if ($tidakAdaData > 0) $results[] = ['Kategori' => 'Tidak Ada Data', 'Jumlah' => $tidakAdaData];

        return $results;
    }

    /**
     * Rekapitulasi Suspek Gangguan Fungsi Hati (SGOT/SGPT)
     * Analyzes 'Sgot' and 'Sgpt' values.
     *
     * @param array $data Array of patient data items.
     * @returns array Rekapitulasi data [{ Kategori: string, Jumlah: number }]
     */
    public function calculateFungsiHatiRekap(array $data): array
    {
        $normal = 0;
        $peningkatan = 0;
        $tidakAdaData = 0;

        foreach ($data as $item) {
            $sgot = $this->getNumericValue($item, 'Sgot'); // Use helper for Sgot
            $sgpt = $this->getNumericValue($item, 'Sgpt'); // Use helper for Sgpt

            // Check for missing/invalid data for *either* SGOT or SGPT
             if ($sgot === null || $sgpt === null) {
                 $tidakAdaData++;
                 continue; // Skip patient if either value is missing/invalid
             }

            // Classification logic based on thresholds
            if ($sgot > 34 || $sgpt > 55) {
                $peningkatan++;
            } else { // SGOT <= 34 AND SGPT <= 55
                $normal++;
            }
        }

        $results = [];
        if ($peningkatan > 0) $results[] = ['Kategori' => 'Peningkatan SGOT dan atau SGPT', 'Jumlah' => $peningkatan];
        if ($normal > 0) $results[] = ['Kategori' => 'Normal', 'Jumlah' => $normal];
        if ($tidakAdaData > 0) $results[] = ['Kategori' => 'Tidak Ada Data', 'Jumlah' => $tidakAdaData];

        return $results;
    }

    /**
     * Rekapitulasi Pemeriksaan Kolesterol Total
     * Analyzes 'Kolesterol' values, handling potential string formats.
     *
     * @param array $data Array of patient data items.
     * @returns array Rekapitulasi data [{ Kategori: string, Jumlah: number }]
     */
    public function calculateKolesterolTotalRekap(array $data): array
    {
        $normal = 0;
        $batasTinggi = 0;
        $tinggi = 0;
        $tidakAdaData = 0;

        foreach ($data as $item) {
             // Use the specialized helper to parse Kolesterol Total string values
            $kolesterolTotal = $this->parseKolesterolTotal($item['Kolesterol'] ?? null);

            if ($kolesterolTotal === null) {
                $tidakAdaData++;
            } elseif ($kolesterolTotal < 200) {
                $normal++;
            } elseif ($kolesterolTotal >= 200 && $kolesterolTotal <= 239) {
                $batasTinggi++;
            } elseif ($kolesterolTotal >= 240) {
                $tinggi++;
            }
        }

        $results = [];
        if ($normal > 0) $results[] = ['Kategori' => 'Normal', 'Jumlah' => $normal];
        if ($batasTinggi > 0) $results[] = ['Kategori' => 'Batas Tinggi', 'Jumlah' => $batasTinggi];
        if ($tinggi > 0) $results[] = ['Kategori' => 'Tinggi', 'Jumlah' => $tinggi];
        if ($tidakAdaData > 0) $results[] = ['Kategori' => 'Tidak Ada Data', 'Jumlah' => $tidakAdaData];

        return $results;
    }

    /**
     * Rekapitulasi Riwayat Kesehatan Peserta MCU
     * Counts patients having specific keywords in their 'Riwayat Kesehatan Pribadi' field.
     * A patient is counted only once per disease category, even if multiple keywords for that category are present.
     *
     * @param array $data Array of patient data items.
     * @returns array Rekapitulasi data [{ 'Kategori Penyakit': string, Jumlah: number }]
     */
    public function calculateRiwayatKesehatanRekap(array $data): array
    {
        // Map keywords to canonical disease names (ensure unique canonical names if any keywords map to the same name)
        $keywords = [
            'batu empedu' => 'Batu Empedu',
            'dm' => 'DM (Diabetes Mellitus)',
            'diabetes mellitus' => 'DM (Diabetes Mellitus)',
            'dyspepsia' => 'Dyspepsia',
            'thypoid' => 'Thypoid', // Note: Typo "Thypoid" in original JS, preserving for direct translation. Consider fixing to "Typhoid"
            'penyakit jantung' => 'Penyakit Jantung',
            'tbc' => 'TBC',
            'keluhan muskoloskeletal' => 'Keluhan Muskoloskeletal',
            'asma' => 'Asma',
            'hipertensi' => 'Hipertensi',
            'covid-19' => 'Covid-19'
        ];

        // Initialize counts for each unique canonical name found in the keywords mapping
        $counts = array_fill_keys(array_unique(array_values($keywords)), 0);
        $tidakAdaData = 0;

        foreach ($data as $item) {
            $riwayatString = $item['Riwayat Kesehatan Pribadi'] ?? null; // Get riwayat string

            // Check for missing/invalid data
            if (!is_string($riwayatString) || trim($riwayatString) === '') {
                $tidakAdaData++;
                continue; // Move to the next item
            }

            $lowerRiwayat = strtolower(trim($riwayatString));
            $patientKeywordsFoundCanonical = []; // Array to track unique canonical names found for THIS patient

            // Check for each keyword in the patient's history string
            foreach ($keywords as $keyword => $canonicalName) {
                if (str_contains($lowerRiwayat, $keyword)) {
                    // Add the canonical name to the patient's found list if not already there
                    if (!in_array($canonicalName, $patientKeywordsFoundCanonical)) {
                         $patientKeywordsFoundCanonical[] = $canonicalName;
                    }
                }
            }

            // Increment the main counts for each unique canonical name found for this patient
            foreach ($patientKeywordsFoundCanonical as $canonicalName) {
                // Ensure the canonical name is one we initialized counts for (safety check)
                if (array_key_exists($canonicalName, $counts)) {
                    $counts[$canonicalName]++;
                }
            }
        }

        // Build results array, only including categories with counts > 0
        $results = [];
        foreach ($counts as $canonicalName => $jumlah) {
            if ($jumlah > 0) {
                $results[] = ['Kategori Penyakit' => $canonicalName, 'Jumlah' => $jumlah];
            }
        }
        if ($tidakAdaData > 0) $results[] = ['Kategori Penyakit' => 'Tidak Ada Data', 'Jumlah' => $tidakAdaData];

        // Optional: Sort results if needed (e.g., by count descending)
        // usort($results, function($a, $b) { return $b['Jumlah'] <=> $a['Jumlah']; }); // Using spaceship operator for PHP 7+

        return $results;
    }

    /**
     * Rekapitulasi Pemeriksaan Kolesterol HDL
     * Analyzes 'Koles Hdl' values.
     *
     * @param array $data Array of patient data items.
     * @returns array Rekapitulasi data [{ Kategori: string, Jumlah: number }]
     */
    public function calculateKolesterolHdlRekap(array $data): array
    {
        $normal = 0;
        $rendah = 0;
        $tidakAdaData = 0;

        foreach ($data as $item) {
            $hdl = $this->getNumericValue($item, 'Koles Hdl'); // Use helper for Koles Hdl

            if ($hdl === null) {
                $tidakAdaData++;
            } elseif ($hdl < 40) {
                $rendah++;
            } else { // hdl >= 40
                $normal++;
            }
        }

        $results = [];
        if ($normal > 0) $results[] = ['Kategori' => 'Normal', 'Jumlah' => $normal];
        if ($rendah > 0) $results[] = ['Kategori' => 'Rendah', 'Jumlah' => $rendah];
        if ($tidakAdaData > 0) $results[] = ['Kategori' => 'Tidak Ada Data', 'Jumlah' => $tidakAdaData];

        return $results;
    }

    /**
     * Rekapitulasi Pemeriksaan Kolesterol LDL
     * Analyzes 'Koles Ldl' values.
     *
     * @param array $data Array of patient data items.
     * @returns array Rekapitulasi data [{ Kategori: string, Jumlah: number }]
     */
    public function calculateKolesterolLdlRekap(array $data): array
    {
        $optimal = 0;
        $hampirOptimal = 0;
        $batasTinggi = 0;
        $tinggi = 0;
        $sangatTinggi = 0;
        $tidakAdaData = 0;

        foreach ($data as $item) {
            $ldl = $this->getNumericValue($item, 'Koles Ldl'); // Use helper for Koles Ldl

            if ($ldl === null) {
                $tidakAdaData++;
            } elseif ($ldl < 100) {
                $optimal++;
            } elseif ($ldl >= 100 && $ldl <= 129) {
                $hampirOptimal++;
            } elseif ($ldl >= 130 && $ldl <= 159) {
                $batasTinggi++;
            } elseif ($ldl >= 160 && $ldl <= 189) {
                $tinggi++;
            } elseif ($ldl >= 190) {
                $sangatTinggi++;
            }
        }

        $results = [];
        if ($optimal > 0) $results[] = ['Kategori' => 'Optimal', 'Jumlah' => $optimal];
        if ($hampirOptimal > 0) $results[] = ['Kategori' => 'Hampir Optimal', 'Jumlah' => $hampirOptimal];
        if ($batasTinggi > 0) $results[] = ['Kategori' => 'Batas Tinggi', 'Jumlah' => $batasTinggi];
        if ($tinggi > 0) $results[] = ['Kategori' => 'Tinggi', 'Jumlah' => $tinggi];
        if ($sangatTinggi > 0) $results[] = ['Kategori' => 'Sangat Tinggi', 'Jumlah' => $sangatTinggi];
        if ($tidakAdaData > 0) $results[] = ['Kategori' => 'Tidak Ada Data', 'Jumlah' => $tidakAdaData];

        return $results;
    }

    /**
     * Rekapitulasi Pemeriksaan Trigliserida
     * Analyzes 'Trgliserid' values.
     *
     * @param array $data Array of patient data items.
     * @returns array Rekapitulasi data [{ Kategori: string, Jumlah: number }]
     */
    public function calculateTrigliseridaRekap(array $data): array
    {
        $normal = 0;
        $batasTinggi = 0;
        $tinggi = 0;
        $sangatTinggi = 0;
        $tidakAdaData = 0;

        foreach ($data as $item) {
            $trigliserida = $this->getNumericValue($item, 'Trgliserid'); // Use helper for Trgliserid

            if ($trigliserida === null) {
                $tidakAdaData++;
            } elseif ($trigliserida < 150) {
                $normal++;
            } elseif ($trigliserida >= 150 && $trigliserida <= 199) {
                $batasTinggi++;
            } elseif ($trigliserida >= 200 && $trigliserida <= 499) {
                $tinggi++;
            } elseif ($trigliserida >= 500) {
                $sangatTinggi++;
            }
        }

        $results = [];
        if ($normal > 0) $results[] = ['Kategori' => 'Normal', 'Jumlah' => $normal];
        if ($batasTinggi > 0) $results[] = ['Kategori' => 'Batas Tinggi', 'Jumlah' => $batasTinggi];
        if ($tinggi > 0) $results[] = ['Kategori' => 'Tinggi', 'Jumlah' => $tinggi];
        if ($sangatTinggi > 0) $results[] = ['Kategori' => 'Sangat Tinggi', 'Jumlah' => $sangatTinggi];
        if ($tidakAdaData > 0) $results[] = ['Kategori' => 'Tidak Ada Data', 'Jumlah' => $tidakAdaData];

        return $results;
    }

    /**
     * Rekapitulasi Pemeriksaan Ureum
     * Analyzes 'Ureum' values.
     *
     * @param array $data Array of patient data items.
     * @returns array Rekapitulasi data [{ Kategori: string, Jumlah: number }]
     */
    public function calculateUreumRekap(array $data): array
    {
        $normal = 0;
        $tidakNormal = 0;
        $tidakAdaData = 0;

        foreach ($data as $item) {
            $ureum = $this->getNumericValue($item, 'Ureum'); // Use helper for Ureum

            if ($ureum === null) {
                $tidakAdaData++;
            } elseif ($ureum >= 15 && $ureum <= 40) {
                $normal++;
            } else { // ureum < 15 or > 40
                $tidakNormal++;
            }
        }

        $results = [];
        if ($normal > 0) $results[] = ['Kategori' => 'Normal', 'Jumlah' => $normal];
        if ($tidakNormal > 0) $results[] = ['Kategori' => 'Tidak Normal', 'Jumlah' => $tidakNormal];
        if ($tidakAdaData > 0) $results[] = ['Kategori' => 'Tidak Ada Data', 'Jumlah' => $tidakAdaData];

        return $results;
    }

    /**
     * Rekapitulasi Pemeriksaan Asam Urat
     * Analyzes 'Asam Urat' based on 'Jenis Kelamin'.
     *
     * @param array $data Array of patient data items.
     * @returns array Rekapitulasi data [{ Kategori: string, Jumlah: number }]
     */
    public function calculateAsamUratRekap(array $data): array
    {
        $normal = 0;
        $tidakNormal = 0;
        $tidakAdaData = 0;

        foreach ($data as $item) {
            $asamUrat = $this->getNumericValue($item, 'Asam Urat'); // Use helper for Asam Urat
            $jenisKelamin = $item['Jenis Kelamin'] ?? null; // Get gender string

            // Check for missing/invalid data for Asam Urat OR gender
            if ($asamUrat === null || !is_string($jenisKelamin) || trim($jenisKelamin) === '') {
                $tidakAdaData++;
                continue; // Move to the next item
            }

            // Standardize gender string
            $genderKey = strtolower(trim($jenisKelamin));

            $isNormal = false;
            // Use the non-null $asamUrat value here
            if ($genderKey === 'laki-laki') {
                if ($asamUrat >= 3.5 && $asamUrat <= 7.2) {
                    $isNormal = true;
                }
            } elseif ($genderKey === 'perempuan') {
                if ($asamUrat >= 2.6 && $asamUrat <= 6.0) {
                    $isNormal = true;
                }
            } else {
                 // Unrecognized gender, count as no data for this entry
                 $tidakAdaData++;
                 continue; // Move to the next item
            }

            // Classify based on the $isNormal flag
             if ($isNormal) {
                 $normal++;
             } else {
                 $tidakNormal++;
             }
        }

        // Build results array, only including categories with counts > 0
        $results = [];
        if ($normal > 0) $results[] = ['Kategori' => 'Normal', 'Jumlah' => $normal];
        if ($tidakNormal > 0) $results[] = ['Kategori' => 'Tidak Normal', 'Jumlah' => $tidakNormal];
        if ($tidakAdaData > 0) $results[] = ['Kategori' => 'Tidak Ada Data', 'Jumlah' => $tidakAdaData];

        return $results;
    }
}