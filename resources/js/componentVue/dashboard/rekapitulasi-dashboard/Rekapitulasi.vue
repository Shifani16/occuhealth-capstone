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
                        <span>{{ selectedOption || 'Pilih Jenis Rekapitulasi' }}</span>
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
                            :key="option"
                            @click="selectOption(option)"
                            class="px-4 py-2 hover:bg-[#C9EBF3] cursor-pointer"
                        >
                            {{ option }}
                        </li>
                    </ul>
                </div>
                
                <div v-if="selectedOption" class="flex gap-6 items-start flex-wrap lg:flex-nowrap">
                    <div class="w-full lg:w-1/2 bg-[#F9FAFB] p-4 rounded shadow">
                        <h2 class="text-lg font-semibold mb-4">
                            Rekapitulasi {{ selectedOption }}
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
                                <!-- <tr v-for="(row, index) in tableData" :key="index" class="border-b hover:bg-gray-100">
                                    <td class="py-2 px-3">{{ index + 1 }}</td>
                                    <td v-for="key in Object.keys(row)" :key="key" class="py-2 px-3">
                                        {{ row[key] }}
                                    </td>
                                </tr> -->
                                <tr v-if="tableData.length === 0">
                                    <td :colspan="tableHeaders.length + 1" class="py-4 px-3 text-center text-gray-500">Tidak ada data untuk rekapitulasi ini.</td>
                                </tr>

                                <tr v-else v-for="(row, index) in tableData" :key="index" class="border-b last:border-b-0 hover:bg-gray-100">
                                    <td class="py-2 px-3 text-sm">
                                        {{ index + 1 }}
                                    </td>

                                    <td v-for="key in Object.keys(row)" :key="key" class="py-2 px-3 text-sm">
                                        {{ row[key] }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- <div class="w-full lg:1/2 bg-[#E6F6F9] p-4 rounded shadow">
                        <h2 class="text-lg font-semibold mb-4">Analisis {{ selectedOption }}</h2>
                        <RekapChart :dataChart="chartData" />
                    </div> -->

                    <div v-if="tableData.length > 0" class="container-open-sans w-full lg:w-1/2 bg-[#E6F6F9] p-4 rounded shadow chart-container">
                        <h2 class="text-lg font-semibold mb-4">
                            Analisis {{ selectedOption }}
                        </h2>
                        <RekapChart :dataChart="chartData" />
                    </div>

                    <div v-else class="container-open-sans w-full lg:w-1/2 bg-[#E6F6F9] p-4 rounded shadow">
                        <h2 class="text-lg font-semibold mb-4">
                            Analisis {{ selectedOption }}
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
import { ref, computed } from 'vue';

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
    calculateAsamUratRekap
 } from '../../../composables/recapCalculator';

 // Mock Data
 const allRawData = ref(mockRawData);
const loadingData = ref(true);
const fetchError = ref(null);

// Dropdown
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

const toggleDropdown = () => {
  showDropdown.value = !showDropdown.value;
};

const selectOption = (option) => {
  selectedOption.value = option;
  showDropdown.value = false;
  if (allRawData.value.length > 0) {
    loadRekapData(option.value); // Pass the value to loadRekapData
  } else {
      tableData.value = [];
      tableHeaders.value = [];
      chartData.value = { labels: [], datasets: [] };
  }
};


const tableHeaders = ref([]);
const tableData = ref([]);
const chartData = ref({});

const loadRekapData = (optionValue) => {
  const calculate = calculationFunctions[optionValue];

  if (!calculate) {
    tableData.value = [];
    tableHeaders.value = [];
    chartData.value = { labels: [], datasets: [] };
    console.warn(`Calculation function not found for option: ${optionValue}`);
    return;
  }

  // Perform calculation using the fetched raw data
  const data = calculate(allRawData.value);
  tableData.value = data;

  // Dynamically determine headers from the first data row (excluding 'Jumlah')
  if (data.length > 0) {
      // Get keys of the first row, filter out 'Jumlah'
      tableHeaders.value = Object.keys(data[0]).filter(key => key !== 'Jumlah');
      // Add 'Jumlah' header at the end
      tableHeaders.value.push('Jumlah');

       // Prepare chart data
      const chartLabels = data.map(row => {
          // Get values for label columns (all except 'Jumlah')
          const labelValues = Object.keys(row)
              .filter(key => key !== 'Jumlah')
              .map(key => row[key]);
          // Join label values for the chart label
          return labelValues.join(' - ');
      });
      const chartValues = data.map(row => row.Jumlah);

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
      // If calculation returns no data, clear table/chart
      tableHeaders.value = [];
      chartData.value = { labels: [], datasets: [] };
  }
};

// Fetch raw data from backend when the component is mounted
onMounted(async () => {
    loadingData.value = true;
    fetchError.value = null;
    try {
        // Replace with your actual backend endpoint
        const response = await axios.get('/api/mcu-detailed-data');
        // Assuming response.data is the array of patient objects
        allRawData.value = response.data;
        console.log("Fetched raw data for rekapitulasi:", allRawData.value.length, "records");

        // If a default option is selected on mount (optional) or if data was loaded before selecting
        if (selectedOption.value && allRawData.value.length > 0) {
             loadRekapData(selectedOption.value.value);
        }

    } catch (error) {
        console.error("Error fetching detailed MCU data:", error);
        fetchError.value = error;
        allRawData.value = []; // Clear data on error
    } finally {
        loadingData.value = false;
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
</style>