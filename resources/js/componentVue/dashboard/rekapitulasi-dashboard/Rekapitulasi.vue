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
                        :class="{ 'bg-[#C9EB3]' : showDropdown }"
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

                    <div v-if="tableData.length > 0" class="w-full lg:w-1/2 bg-[#E6F6F9] p-4 rounded shadow chart-container">
                        <h2 class="text-lg font-semibold mb-4">
                            Analisis {{ selectedOption }}
                        </h2>
                        <RekapChart :dataChart="chartData" />
                    </div>

                    <div v-else class="w-full lg:w-1/2 bg-[#E6F6F9] p-4 rounded shadow">
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

// Dropdown
const showDropdown = ref(false);
const selectedOption = ref('');
const options = [
  'Gangguan Metabolisme Glukosa',
  'Gangguan Status Gizi',
  'Gangguan Tekanan Darah',
  'Kelompok Umur Peserta MCU',
  'Kadar Hemoglobin',
  'Pemeriksaan Creatinin Darah',
  'Pemeriksaan Dislipidemia',
  'Pemeriksaan Kolestrol Total',
  'Pemeriksaan Infeksi Salurah Kemih',
  'Pemeriksaan Kolesterol HDL',
  'Pemeriksaan Kolesterol LDL',
  'Pemeriksaan Trigliserida',
  'Pemeriksaan Ureum',
  'Riwayat Kesehatan Peserta MCU',
  'Gangguan Fungsi Hati',
  'Hasil Pemeriksaan EKG'
];

const toggleDropdown = () => {
  showDropdown.value = !showDropdown.value;
};

const selectOption = (option) => {
  selectedOption.value = option;
  showDropdown.value = false;
  loadData(option);
};

const rawData = [];
const patientCount = 100;
const riwayatKesehatanKeywords = [
  'Batu empedu', 'DM', 'dispepsia', 'thypoid', 'penyakit jantung', 'TBC',
  'keluhan muskoloskeletal', 'asma', 'hipertensi', 'Covid-19'
];

const jenisKelaminList = ['Laki-laki', 'Perempuan'];

for (let i = 1; i <= patientCount; i++) {
    const jenisKelamin = jenisKelaminList[Math.floor(Math.random() * jenisKelaminList.length)];
    const usia = Math.floor(Math.random() * 50) + 20;
    const bb = Math.random() * (100 - 40) + 40;
    const tb = Math.random() * (190 - 140) + 140;
    const imt = bb / ((tb / 100) * (tb / 100));
    let kategoriIMT = 'Normal';
    if (imt < 18.5) kategoriIMT = 'Underweight';
    else if (imt >= 18.5 && imt < 25) kategoriIMT = 'Normal';
    else if (imt >= 25 && imt < 30) kategoriIMT = 'Overweight';
    else if (imt >= 30 && imt < 35) kategoriIMT = 'Obesitas Grade I';
    else kategoriIMT = 'Obesitas Grade II';

    const tdSystolic = Math.floor
}

const tableHeaders = ref([]);
const tableData = ref([]);
const chartData = ref({});

const loadData = (option) => {
  const data = rawData[option] || [];
  tableData.value = data;
  tableHeaders.value = data.length ? Object.keys(data[0]) : [];

  const chartLabels = data.map(row => Object.values(row).slice(0, -1).join(' - '));
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
};
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