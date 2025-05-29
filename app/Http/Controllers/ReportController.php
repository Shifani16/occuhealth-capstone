<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\McuPatient;
use App\Models\ReportLog;
use App\Services\RecapCalculatorService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class ReportController extends Controller
{
    protected $recapCalculator;

    /**
     * @param RecapCalculatorService $recapCalculator
     */
    public function __construct(RecapCalculatorService $recapCalculator)
    {
        $this->recapCalculator = $recapCalculator;
    }

    /**
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRecapData(Request $request)
    {
        try {
            $request->validate([
                'startDate' => 'required|date_format:Y-m-d',
                'endDate' => 'required|date_format:Y-m-d|after_or_equal:startDate',
            ]);
            $startDate = Carbon::parse($request->startDate)->startOfDay();
            $endDate = Carbon::parse($request->endDate)->endOfDay();

            $mcuPatients = McuPatient::whereBetween('examination_date', [$startDate, $endDate])
                ->with(['patient', 'mcuResults'])
                ->get();

            if ($mcuPatients->isEmpty()) {
                return response()->json(['message' => 'Tidak ada data MCU untuk periode yang dipilih.'], 404);
            }

            $formattedDataForCalculation = $mcuPatients->map(function ($mcuPatient) {
                $formattedItem = [];
                if ($mcuPatient->patient) {
                    $formattedItem['Jenis Kelamin'] = $mcuPatient->patient->gender ?? null;
                    $formattedItem['Usia'] = $mcuPatient->patient->age ?? null;
                    $riwayat = null;
                    if ($mcuPatient->patient && property_exists($mcuPatient->patient, 'riwayat_kesehatan_pribadi_column_name')) {
                        $riwayat = $mcuPatient->patient->riwayat_kesehatan_pribadi_column_name;
                    } elseif ($mcuPatient->saran !== null) {
                        $riwayat = $mcuPatient->saran;
                    } else {
                        $riwayatResult = $mcuPatient->mcuResults->firstWhere('category', 'Riwayat Kesehatan Pribadi');
                        $riwayat = $riwayatResult ? $riwayatResult->result : null;
                    }
                    $formattedItem['Riwayat Kesehatan Pribadi'] = $riwayat;
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
                'Riwayat Kesehatan' => $this->recapCalculator->calculateRiwayatKesehatanRekap($formattedDataForCalculation), // Note: This one returns different keys ('Kategori Penyakit')
            ];

            return response()->json($recaps);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Input tidak valid.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            logger()->error('Error fetching recap data:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Terjadi kesalahan saat mengambil data rekap.', 'error' => $e->getMessage()], 500);
        }
    }


    /**
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function generate(Request $request)
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 180);

        try {
            $request->validate([
                'startDate' => 'required|date_format:Y-m-d', 
                'endDate' => 'required|date_format:Y-m-d|after_or_equal:startDate',
                'chartImages' => 'nullable|array',
                'chartImages.*' => 'string|starts_with:data:image/',
            ]);
            $startDate = Carbon::parse($request->startDate)->startOfDay();
            $endDate = Carbon::parse($request->endDate)->endOfDay();   


            $chartImages = $request->input('chartImages', []);


            // --- Fetch Data ---
            $mcuPatients = McuPatient::whereBetween('examination_date', [$startDate, $endDate])
                ->with(['patient', 'mcuResults'])
                ->get();

            $formattedDataForCalculation = $mcuPatients->map(function ($mcuPatient) {
                $formattedItem = [];
                if ($mcuPatient->patient) {
                    $formattedItem['Jenis Kelamin'] = $mcuPatient->patient->gender ?? null;
                    $formattedItem['Usia'] = $mcuPatient->patient->age ?? null;
                    $riwayat = null;
                    if ($mcuPatient->patient && property_exists($mcuPatient->patient, 'riwayat_kesehatan_pribadi_column_name')) {
                        $riwayat = $mcuPatient->patient->riwayat_kesehatan_pribadi_column_name;
                    } elseif ($mcuPatient->saran !== null) {
                        $riwayat = $mcuPatient->saran;
                    } else {
                        $riwayatResult = $mcuPatient->mcuResults->firstWhere('category', 'Riwayat Kesehatan Pribadi');
                        $riwayat = $riwayatResult ? $riwayatResult->result : null;
                    }
                    $formattedItem['Riwayat Kesehatan Pribadi'] = $riwayat;
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

            $hasTableData = !empty($recaps) && array_filter($recaps, fn($r) => !empty($r));
            $hasChartData = !empty($chartImages);

            if (!$hasTableData && !$hasChartData) {
                return response()->json(['message' => 'Tidak ada data MCU atau grafik yang tersedia untuk periode yang dipilih.'], 404);
            }


            // --- Generate PDF ---
            $pdf = Pdf::loadView('reports.recap_report', [
                'startDate' => $startDate->format('d/m/Y'),
                'endDate' => $endDate->format('d/m/Y'),
                'recaps' => $recaps,
                'chartImages' => $chartImages,
                'hasTableData' => $hasTableData,
                'hasChartData' => $hasChartData,
            ]);

            $pdf->setPaper('a4', 'portrait');
            $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

            return $pdf->stream("laporan_MCU_{$startDate->format('Ymd')}_sd_{$endDate->format('Ymd')}.pdf");
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Input tidak valid.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            logger()->error('Error generating report:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Terjadi kesalahan saat membuat laporan PDF. Silakan coba lagi atau hubungi administrator.', 'error' => $e->getMessage()], 500);
        }
    }

    public function getLogs()
    {
        try {
            $logs = ReportLog::orderBy('created_at', 'desc')->get();

            $formattedLogs = $logs->map(function ($log) {
                return [
                    'id' => $log->id,
                    'namaFile' => $log->file_name,
                    'tanggal' => $log->created_at->toDateString(),
                ];
            });


            return response()->json($formattedLogs);
        } catch (\Exception $e) {
            logger()->error('Error fetching report logs:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Terjadi kesalahan saat mengambil log laporan.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeLog(Request $request)
    {
        try {
            $request->validate([
                'namaFile' => 'required|string|max:255',
            ]);

            $log = ReportLog::create([
                'file_name' => $request->namaFile,
            ]);

            return response()->json(['message' => 'Log laporan berhasil disimpan.', 'log' => $log], 201);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Input log tidak valid.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            logger()->error('Error storing report log:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Terjadi kesalahan saat menyimpan log laporan.', 'error' => $e->getMessage()], 500);
        }
    }
}
