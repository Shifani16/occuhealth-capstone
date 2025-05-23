<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\McuPatient;
use App\Models\Patient;
use App\Models\McuResult;
use App\Services\RecapCalculatorService;
// No need to import ChartGeneratorService anymore
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class ReportController extends Controller
{
    protected $recapCalculator;

    /**
     * Inject the RecapCalculatorService.
     * @param RecapCalculatorService $recapCalculator
     */
    public function __construct(RecapCalculatorService $recapCalculator)
    {
        $this->recapCalculator = $recapCalculator;
         // No chartGenerator property or injection needed
    }

    /**
     * Generate a PDF report based on date range and client-provided chart images.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function generate(Request $request)
    {
        try {
            // Validate incoming dates and the chartImages array
            $request->validate([
                'startDate' => 'required|date_format:Y-m-d',
                'endDate' => 'required|date_format:Y-m-d|after_or_equal:startDate',
                'chartImages' => 'nullable|array', // Expecting a nullable array of chart images
                 // You might add more specific validation if needed, e.g., keys are strings, values start with 'data:image/'
            ]);

            $startDate = Carbon::createFromFormat('Y-m-d', $request->startDate)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $request->endDate)->endOfDay();

            // Get the chart images data from the request
            $chartImages = $request->input('chartImages', []); // Get the array, default to empty array if not present

            // --- Fetch Data ---
            // Still need to fetch data to pass to the recap service for the *tables* in the PDF.
            // The tables in the PDF should show the numbers that the charts represent.
            $mcuPatients = McuPatient::whereBetween('examination_date', [$startDate, $endDate])
                                        ->with(['patient', 'mcuResults'])
                                        ->get();

            if ($mcuPatients->isEmpty()) {
                 // Even if charts were sent, we might not have underlying data for tables.
                 // Decide if you still want a PDF with charts but no tables in this case.
                 // For now, keep the 404 if no *patient data* is found.
                 return response()->json(['message' => 'Tidak ada data MCU untuk periode yang dipilih.'], 404);
            }

            // --- Prepare Data for Calculations (for tables in PDF) ---
            $formattedDataForCalculation = $mcuPatients->map(function($mcuPatient) {
                $formattedItem = [];
                if ($mcuPatient->patient) {
                     $formattedItem['Jenis Kelamin'] = $mcuPatient->patient->gender ?? null;
                     $formattedItem['Usia'] = $mcuPatient->patient->age ?? null;
                     // !! IMPORTANT: Confirm where 'Riwayat Kesehatan Pribadi' is stored !!
                     // Replace 'riwayat_kesehatan_pribadi_column_name'
                     $formattedItem['Riwayat Kesehatan Pribadi'] = $mcuPatient->patient->riwayat_kesehatan_pribadi_column_name ?? null;
                     // $formattedItem['Riwayat Kesehatan Pribadi'] = $mcuPatient->saran ?? null;
                } else {
                    $formattedItem['Jenis Kelamin'] = null;
                    $formattedItem['Usia'] = null;
                    $formattedItem['Riwayat Kesehatan Pribadi'] = null;
                }

                if ($mcuPatient->mcuResults->isNotEmpty()) {
                    foreach ($mcuPatient->mcuResults as $result) {
                        $formattedItem[$result->category] = $result->result;
                    }
                }
                return $formattedItem;
            })->toArray();

            // --- Perform Calculations (for tables in PDF) ---
            // Still run calculations to get the table data in the correct format.
            $recaps = [
                'Glukosa' => $this->recapCalculator->calculateGlukosaRekap($formattedDataForCalculation),
                'Status Gizi (BMI)' => $this->recapCalculator->calculateStatusGiziRekap($formattedDataForCalculation),
                'Tekanan Darah' => $this->recapCalculator->calculateTekananDarahRekap($formattedDataForCalculation),
                'Kelompok Umur' => $this->recapCalculator->calculateUmurRekap($formattedDataForCalculation),
                'Hemoglobin' => $this->recapCalculator->calculateHbRekap($formattedDataForCalculation),
                'Creatinin Darah' => $this->recapCalculator->calculateCreatininRekap($formattedDataForCalculation),
                'Fungsi Hati (SGOT/SGPT)' => $this->recapCalculator->calculateFungsiHatiRekap($formattedDataForCalculation),
                'Kolesterol Total' => $this->recapCalculator->calculateKolesterolTotalRekap($formattedDataForCalculation),
                'Kolesterol HDL' => $this->recapCalculator->calculateKolesterolHdlRekap($formattedDataForCalculation),
                'Kolesterol LDL' => $this->recapCalculator->calculateKolesterolLdlRekap($formattedDataForCalculation),
                'Trigliserida' => $this->recapCalculator->calculateTrigliseridaRekap($formattedDataForCalculation),
                'Ureum' => $this->recapCalculator->calculateUreumRekap($formattedDataForCalculation),
                'Asam Urat' => $this->recapCalculator->calculateAsamUratRekap($formattedDataForCalculation),
                'Riwayat Kesehatan' => $this->recapCalculator->calculateRiwayatKesehatanRekap($formattedDataForCalculation),
            ];

            // --- Generate PDF ---
            // Load the Blade view and pass the date range, calculated data for tables,
            // AND the chart image data received from the frontend.
            $pdf = Pdf::loadView('reports.recap_report', [
                'startDate' => $startDate->format('d/m/Y'),
                'endDate' => $endDate->format('d/m/Y'),
                'recaps' => $recaps, // Data for tables/descriptions
                'chartImages' => $chartImages, // Image data from frontend
            ]);

            // Optional: Configure PDF options
            // $pdf->setPaper('a4', 'portrait');

            // Return the PDF as a stream response.
            return $pdf->stream("laporan_MCU_{$startDate->format('Ymd')}_sd_{$endDate->format('Ymd')}.pdf");

        } catch (ValidationException $e) {
             return response()->json(['message' => 'Input tidak valid.', 'errors' => $e->errors()], 422); // Changed message slightly

        } catch (\Exception $e) {
            logger()->error('Error generating report:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            // Return a 500 error response
            return response()->json(['message' => 'Terjadi kesalahan saat membuat laporan PDF. Silakan coba lagi atau hubungi administrator.', 'error' => $e->getMessage()], 500); // Changed message slightly
        }
    }
}