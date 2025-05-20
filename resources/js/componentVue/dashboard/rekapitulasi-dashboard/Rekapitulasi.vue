<template>
    <div>
        <header
            class="w-full bg-[#CAEAF2] px-5 py-3 flex justify-between items-center fixed top-0 left-0 z-50"
        >
            <div class="flex items-center gap-6">
                <img
                    src="@/assets/occuhelp-full-logo.svg"
                    alt="OccuHelp Full Logo"
                    class="h-12"
                />
            </div>
        </header>

        <div class="mt-20 flex min-h-screen">
            <LeftBar active="Rekapitulasi" />

            <main class="flex-1 px-10 py-6">
                <h1 class="text-4xl font-bold mb-6">Rekapitulasi</h1>

                <div class="relative mb-6 w-64">
                    <button
                        class="w-full flex justify-between items-center px-4 py-2 rounded bg-[#E6F6F9] hover:bg-[#C9EBF3] transition"
                        @click="toggleDropdown"
                        :class="{ 'bg-[#C9EBF3]' : showDropdown }"
                    >
                        <span>{{ selectedOption ? selectedOption.label : 'Pilih Jenis Rekapitulasi' }}</span>
                        <img
                            :src="showDropdown ? upIcon : downIcon"
                            alt="Dropdown Icon"
                            class="w-4 h-4 ml-2"
                        />
                    </button>

                    <ul
                        v-if="showDropdown"
                        class="absolute top-full mt-2 w-full bg-[#E6F6F9] rounded shadow z-10"
                    >
                        <li
                            v-for="option in options"
                            :key="option.value"
                            @click="selectOption(option)"
                            class="px-4 py-2 hover:bg-[#C9EBF3] cursor-pointer"
                        >
                            {{ option.label }}
                        </li>
                    </ul>
                </div>

                <!-- Dropdown Filter Tahun -->
                <div class="relative mb-6 w-40 custom-dropdown"> 
                    <button
                        class="w-full flex justify-between items-center px-4 py-2 rounded bg-[#E6F6F9] hover:bg-[#C9EBF3] transition"
                        @click="toggleYearDropdown"
                         :class="{ 'bg-[#C9EBF3]' : showYearDropdown }"
                         :disabled="availableYears.length <= 1 && selectedYear === 'all'"
                    >
                        <span>{{ selectedYear === 'all' ? 'Semua Tahun' : selectedYear }}</span>
                        <img
                            :src="showYearDropdown ? upIcon : downIcon"
                            alt="Dropdown Icon"
                            class="w-4 h-4 ml-2"
                        />
                    </button>

                    <ul
                        v-if="showYearDropdown"
                        class="absolute top-full mt-2 w-full bg-[#E6F6F9] rounded shadow z-10 custom-dropdown-list"
                    >
                        <li
                            v-for="yearOption in availableYears"
                            :key="yearOption"
                            @click="selectYear(yearOption)"
                            class="px-4 py-2 hover:bg-[#C9EBF3] cursor-pointer"
                        >
                            {{ yearOption === 'all' ? 'Semua Tahun' : yearOption }}
                        </li>
                    </ul>
                </div>

                <div v-if="loadingData" class="text-center py-8 text-gray-600">
                    Memuat data pasien...
                </div>

                <div v-if="fetchError" class="text-center py-8 text-red-600">
                    Gagal memuat data pasien: {{ fetchError.message }}
                </div>

                <div v-if="!loadingData && !fetchError && selectedOption" class="flex gap-6 items-start flex-wrap lg:flex-nowrap">
                    <div class="w-full lg:w-1/2 bg-[#F9FAFB] p-4 rounded shadow">
                        <h2 class="text-lg font-semibold mb-4">
                            Rekapitulasi {{ selectedOption.label }}
                        </h2>

                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b">
                                    <th class="py-2 px-3">No.</th>
                                    <th v-for="header in tableHeaders" :key="header" class="py-2 px-3">
                                        {{ header }}
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-if="tableData.length === 0">
                                    <td :colspan="tableHeaders.length + 1" class="py-4 px-3 text-center text-gray-500">Tidak ada data untuk rekapitulasi ini.</td>
                                </tr>

                                <tr v-else v-for="(row, index) in tableData" :key="index" class="border-b last:border-b-0 hover:bg-gray-100">
                                    <td class="py-2 px-3 text-sm">
                                        {{ index + 1 }}
                                    </td>
                                    <td v-for="headerKey in tableHeaders" :key="headerKey" class="py-2 px-3 text-sm">
                                        {{ row[headerKey] }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Container Grafik -->
                    <div v-if="tableData.length > 0" class="container-open-sans w-full lg:w-1/2 bg-[#E6F6F9] p-4 rounded shadow chart-container">
                        <h2 class="text-lg font-semibold mb-4">
                            Analisis {{ selectedOption.label }}
                        </h2>
                         <RekapChart ref="rekapChartRef" :dataChart="chartData" />

                         <div class="mt-4 text-right">
                             <button
                                 @click="handleExport('image/png')"
                                 class="px-4 py-2 border border-[#3393AD] text-[#3393AD] hover:bg-[#3393AD] hover:text-white font-semibold transition mr-2"
                                 :disabled="!rekapChartRef || !rekapChartRef.barRef || !rekapChartRef.barRef.chart"
                             >
                                 Export PNG
                             </button>
                         </div>
                    </div>

                    <div v-else class="container-open-sans w-full lg:w-1/2 bg-[#E6F6F9] p-4 rounded shadow chart-container">
                         <h2 class="text-lg font-semibold mb-4">
                            Analisis {{ selectedOption.label }}
                        </h2>
                        <p class="text-center text-gray-500">
                            Tidak ada data untuk menampilkan grafik.
                        </p>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>

      
<script setup>
import { ref, onMounted, watch } from 'vue';

import LeftBar from '../../../composables/LeftBar.vue';
import RekapChart from '../../../composables/RekapChart.vue';
import upIcon from '@/assets/pilih-up.svg';
import downIcon from '@/assets/pilih-down.svg';

import {
    calculateGlukosaRekap,
    calculateStatusGiziRekap,
    calculateTekananDarahRekap,
    calculateUmurRekap,
    calculateHbRekap,
    calculateCreatininRekap,
    calculateFungsiHatiRekap,
    calculateKolesterolTotalRekap,
    calculateKolesterolHdlRekap,
    calculateKolesterolLdlRekap,
    calculateTrigliseridaRekap,
    calculateUreumRekap,
    calculateAsamUratRekap,
    calculateRiwayatKesehatanRekap
} from '../../../composables/recapCalculator';

const allRawData = ref([]); 
const loadingData = ref(true);
const fetchError = ref(null);

const showDropdown = ref(false);
const selectedOption = ref(null);

const options = [
  { label: 'Gangguan Metabolisme Glukosa', value: 'glukosa' },
  { label: 'Gangguan Status Gizi', value: 'statusGizi' },
  { label: 'Gangguan Tekanan Darah', value: 'tekananDarah' },
  { label: 'Kelompok Umur Peserta MCU', value: 'umur' },
  { label: 'Kadar Hemoglobin', value: 'hb' },
  { label: 'Pemeriksaan Creatinin Darah', value: 'creatinin' },
  { label: 'Pemeriksaan Kolestrol Total', value: 'kolesterolTotal' },
  { label: 'Pemeriksaan Kolesterol HDL', value: 'kolesterolHdl' },
  { label: 'Pemeriksaan Kolesterol LDL', value: 'kolesterolLdl' },
  { label: 'Pemeriksaan Trigliserida', value: 'trigliserida' },
  { label: 'Pemeriksaan Ureum', value: 'ureum' },
  { label: 'Pemeriksaan Asam Urat', value: 'asamUrat' },
  { label: 'Riwayat Kesehatan Peserta MCU', value: 'riwayatKesehatan' },
  { label: 'Suspek Gangguan Fungsi Hati', value: 'fungsiHati' },
];

const calculationFunctions = {
    glukosa: calculateGlukosaRekap,
    statusGizi: calculateStatusGiziRekap,
    tekananDarah: calculateTekananDarahRekap,
    umur: calculateUmurRekap,
    hb: calculateHbRekap,
    creatinin: calculateCreatininRekap,
    kolesterolTotal: calculateKolesterolTotalRekap,
    kolesterolHdl: calculateKolesterolHdlRekap,
    kolesterolLdl: calculateKolesterolLdlRekap,
    trigliserida: calculateTrigliseridaRekap,
    ureum: calculateUreumRekap,
    asamUrat: calculateAsamUratRekap,
    riwayatKesehatan: calculateRiwayatKesehatanRekap,
    fungsiHati: calculateFungsiHatiRekap,
};

const availableYears = ref([]); 
const selectedYear = ref('all'); 
const showYearDropdown = ref(false); 

const tableHeaders = ref([]);
const tableData = ref([]);
const chartData = ref({ labels: [], datasets: [] }); 

const rekapChartRef = ref(null);


// --- Dropdown Jenis Rekapitulasi Methods ---
const toggleDropdown = () => {
  showDropdown.value = !showDropdown.value;
};

const selectOption = (option) => {
  selectedOption.value = option;
  showDropdown.value = false;

  triggerFilteringAndCalculation();
};


// --- Dropdown Filter Tahun Methods ---
const toggleYearDropdown = () => {
  showYearDropdown.value = !showYearDropdown.value;
};

const selectYear = (year) => {
  selectedYear.value = year;
  showYearDropdown.value = false;

  triggerFilteringAndCalculation();
};


// --- Filtering and Calculation Logic ---
const applyYearFilter = (data, yearFilter) => {
    if (!data || data.length === 0 || yearFilter === 'all') {
        return data; 
    }

    return data.filter(record => {
        if (!record.examination_date) {
            return false; 
        }
        try {
            const recordYear = new Date(record.examination_date).getFullYear();
            if (isNaN(recordYear)) {
                console.warn("Invalid date format found:", record.examination_date);
                return false;
            }
            return recordYear === yearFilter;
        } catch (e) {
            console.warn("Error parsing date:", record.examination_date, e);
            return false; 
        }
    });
};

const triggerFilteringAndCalculation = () => {
    tableData.value = [];
    tableHeaders.value = [];
    chartData.value = { labels: [], datasets: [] };

    if (!loadingData.value && !fetchError.value && selectedOption.value) {
        if (allRawData.value.length > 0) {
            console.log(`Applying year filter: ${selectedYear.value}`);
            const filteredData = applyYearFilter(allRawData.value, selectedYear.value);
            console.log(`Filtered data count: ${filteredData.length}`);

            performCalculation(selectedOption.value.value, filteredData);
        } else {
             console.warn("No raw data available after fetch.");
        }
    } else {
         console.log("Skipping calculation: loading, error, or no option selected.");
    }
};


const performCalculation = (optionValue, dataToCalculate) => {
    const calculate = calculationFunctions[optionValue];

    if (!calculate) {
        console.warn(`Calculation function not found for option: ${optionValue}`);
         tableData.value = [];
         tableHeaders.value = [];
         chartData.value = { labels: [], datasets: [] };
        return;
    }

    console.log(`Performing calculation for "${optionValue}" with ${dataToCalculate.length} records.`);
    const calculatedResult = calculate(dataToCalculate);
    console.log("Calculation result:", calculatedResult);

    tableData.value = calculatedResult;

    if (calculatedResult.length > 0) {
        tableHeaders.value = Object.keys(calculatedResult[0]);

        const chartLabels = calculatedResult.map(row => {
            const labelValues = Object.keys(row)
                .filter(key => key !== 'Jumlah')
                .map(key => row[key]);
             return labelValues.map(val => val ?? '').join(' - ');
        });
        const chartValues = calculatedResult.map(row => row.Jumlah ?? 0); 

        chartData.value = {
            labels: chartLabels,
            datasets: [
              {
                label: 'Jumlah',
                backgroundColor: '#3DB1D5',
                data: chartValues
              }
            ]
        };

    } else {
        tableHeaders.value = [];
        chartData.value = { labels: [], datasets: [] };
    }
};

const handleExport = (type = 'image/png', quality = 1) => {
    if (rekapChartRef.value && rekapChartRef.value.exportAsImage) {
        const optionLabel = selectedOption.value ? selectedOption.value.label : 'data';
        const yearLabel = selectedYear.value === 'all' ? 'SemuaTahun' : selectedYear.value;
        const filename = `${optionLabel.replace(/\s+/g, '_')}_${yearLabel}_${Date.now()}.${type.split('/')[1]}`;

        rekapChartRef.value.exportAsImage(type, filename, quality);
    } else {
        console.warn("RekapChart component or export method not available.");
    }
};


onMounted(async () => {
    loadingData.value = true;
    fetchError.value = null;
    try {
        const response = await fetch('/api/mcu/raw-data');

        if (!response.ok) {
            const errorBody = await response.text();
             throw new Error(`HTTP error! status: ${response.status}, body: ${errorBody}`);
        }

        const data = await response.json();

        if (Array.isArray(data)) {
            allRawData.value = data;
            console.log("Fetched raw data for rekapitulasi:", allRawData.value.length, "records");

            // --- Extract unique years ---
            const years = new Set();
            data.forEach(record => {
                 if (record.examination_date) {
                     try {
                         const year = new Date(record.examination_date).getFullYear();
                         if (!isNaN(year)) {
                             years.add(year);
                         }
                     } catch (e) {
                         console.warn("Could not parse date for year extraction:", record.examination_date);
                     }
                 }
            });
            availableYears.value = ['all', ...Array.from(years).sort((a, b) => b - a)];
            console.log("Available years:", availableYears.value);
            // --- End Extract unique years ---

            if (selectedOption.value) {
                 triggerFilteringAndCalculation();
            }

        } else {
             throw new Error("API response is not an array.");
        }

    } catch (error) {
        console.error("Error fetching raw MCU data:", error);
        fetchError.value = new Error(error.message || "Terjadi kesalahan saat mengambil data pasien.");
        allRawData.value = [];
        availableYears.value = [];
        tableData.value = [];
        tableHeaders.value = [];
        chartData.value = { labels: [], datasets: [] };

    } finally {
        loadingData.value = false;
    }
});

watch(selectedYear, (newYear, oldYear) => {
     console.log(`selectedYear changed from ${oldYear} to ${newYear}`);
    if (selectedOption.value) {
        triggerFilteringAndCalculation();
    } else {
        console.log("Year changed, but no rekapitulasi option selected. Skipping calculation.");
    }
});


</script>
<style scoped>
table th,
table td {
  font-size: 14px;
}

.container-nunito {
    font-family: "Nunito", sans-serif;
}

.container-open-sans {
    font-family: "Open Sans", sans-serif;
}

.chart-container {
  height: 400px; 

}

.custom-dropdown {
}

.custom-dropdown-list {
    max-height: 200px; 
    overflow-y: auto;
}

</style>