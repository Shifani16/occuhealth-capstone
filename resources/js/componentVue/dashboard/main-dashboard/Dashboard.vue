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
      <LeftBar active="Dashboard" />

      <main class="flex-1 p-8 bg-[#F4FAFB]">
        <div class="relative overflow-hidden max-w-full mb-12">
          <div
            ref="scrollContainer"
            class="flex gap-4 overflow-x-auto hide-scrollbar scroll-wrapper pr-12"
            @scroll="checkOverflow"
          >
            <div
              v-for="item in boxes"
              :key="item.title"
              class="relative flex flex-col w-72 min-w-[18rem] p-5 border-2 border-[#007BBD] rounded-xl cursor-pointer transition-colors duration-200"
              :class="hoveredBox === item.title ? 'bg-[#E2EBF3] text-[#305879]' : 'bg-white text-[#27394B]'"
              @click="navigate(item.route)"
              @mouseenter="hoveredBox = item.title"
              @mouseleave="hoveredBox = null"
            >
              <div class="container-nunito flex items-center gap-4">
                <img
                  :src="hoveredBox === item.title ? item.iconHover : item.icon"
                  class="w-10 h-10 transition-transform duration-200"
                />
                <span class="text-lg font-bold">{{ item.title }}</span>
              </div>

              <transition name="expand">
                <div
                  v-if="hoveredBox === item.title"
                  class="mt-2 text-sm leading-snug container-open-sans"
                >
                  {{ item.description }}
                </div>
              </transition>
            </div>
          </div>

          <!-- Scroll arrow (optional, adjust positioning if needed) -->
          <!-- <img
            v-if="isOverflowing"
            src="@/assets/arrow-left.svg" // Use arrow-right if scrolling right
            @click="scrollRight"
            class="absolute right-2 top-1/2 -translate-y-1/2 z-10 bg-white rounded-full p-2 shadow cursor-pointer hover:scale-110 transition-transform rotate-180"
          /> -->
           <button
               v-if="isOverflowingRight"
               @click="scrollRight"
               class="absolute right-2 top-1/2 transform -translate-y-1/2 z-10 bg-white rounded-full p-2 shadow cursor-pointer hover:scale-110 transition-transform"
           >
                <img src="@/assets/arrow-right.svg" class="w-5 h-5"/>
           </button>
            <button
                v-if="isOverflowingLeft"
                @click="scrollLeft"
                class="absolute left-2 top-1/2 transform -translate-y-1/2 z-10 bg-white rounded-full p-2 shadow cursor-pointer hover:scale-110 transition-transform"
            >
                 <img src="@/assets/arrow-left.svg" class="w-5 h-5"/>
            </button>

        </div>

        <div class="mt-12">
          <h2 class="text-3xl font-bold mb-6 container-nunito">Ringkasan Hasil MCU</h2>

          <div v-if="loadingData" class="text-center py-8 text-gray-600 text-xl">
              Memuat data rekapitulasi...
          </div>
          <div v-else-if="fetchError" class="text-center py-8 text-red-600 text-xl">
              Gagal memuat data rekapitulasi: {{ fetchError.message }}
          </div>
           <div v-else-if="allRawData.length === 0" class="text-center py-8 text-gray-500 text-xl">
              Tidak ada data hasil MCU tersedia untuk rekapitulasi.
           </div>

          <div v-else class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">

            <div class="bg-white p-4 rounded shadow chart-container flex flex-col">
              <h3 class="text-lg font-semibold mb-4 container-open-sans">Rekapitulasi Gangguan Status Gizi</h3>
              <div class="chart-wrapper flex-grow relative"> 
                 <RekapChart v-if="giziChartData.labels && giziChartData.labels.length > 0" :dataChart="giziChartData" />
                 <p v-else class="text-center text-gray-500 absolute inset-0 flex items-center justify-center">Tidak ada data untuk grafik ini.</p>
              </div>
            </div>

            <div class="bg-white p-4 rounded shadow chart-container flex flex-col">
              <h3 class="text-lg font-semibold mb-4 container-open-sans">Rekapitulasi Gangguan Tekanan Darah</h3>
              <div class="chart-wrapper flex-grow relative">
                <RekapChart v-if="tdChartData.labels && tdChartData.labels.length > 0" :dataChart="tdChartData" />
                <p v-else class="text-center text-gray-500 absolute inset-0 flex items-center justify-center">Tidak ada data untuk grafik ini.</p>
              </div>
            </div>

            <div class="bg-white p-4 rounded shadow chart-container flex flex-col">
              <h3 class="text-lg font-semibold mb-4 container-open-sans">Rekapitulasi Gangguan Metabolisme Glukosa</h3>
              <div class="chart-wrapper flex-grow relative">
                <RekapChart v-if="glukosaChartData.labels && glukosaChartData.labels.length > 0" :dataChart="glukosaChartData" />
                <p v-else class="text-center text-gray-500 absolute inset-0 flex items-center justify-center">Tidak ada data untuk grafik ini.</p>
              </div>
            </div>

          </div>

           <div v-if="!loadingData && !fetchError && allRawData.length > 0" class="mt-8 text-center">
                <button
                    @click="navigate('/rekapitulasi')"
                    class="px-6 py-3 border border-[#3393AD] text-[#3393AD] font-semibold rounded-lg transition-colors duration-200
                           hover:bg-[#3393AD] hover:text-white container-open-sans"
                >
                    Lihat Grafik Lainnya
                </button>
           </div>


        </div>

      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, watch } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios'; 

import LeftBar from '../../../composables/LeftBar.vue';
import RekapChart from '../../../composables/RekapChart.vue'; 

import {
    calculateStatusGiziRekap,
    calculateTekananDarahRekap,
    calculateGlukosaRekap,
    
} from '../../../composables/recapCalculator';

import pasienIcon from '@/assets/dashboard-pasien.svg';
import pasienIconHover from '@/assets/dashboard-pasien-clicked.svg';
import hasilIcon from '@/assets/dashboard-hasil-mcu.svg';
import hasilIconHover from '@/assets/dashboard-hasil-mcu-clicked.svg';
import rekapIcon from '@/assets/dashboard-rekapitulasi.svg';
import rekapIconHover from '@/assets/dashboard-rekapitulasi-clicked.svg';
import laporanIcon from '@/assets/dashboard-report.svg';
import laporanIconHover from '@/assets/dashboard-report-clicked.svg';
import arrowRightBlue from '@/assets/arrow-right.svg';
import arrowLeftBlue from '@/assets/arrow-left.svg';


const router = useRouter();
const scrollContainer = ref(null);
const isOverflowingRight = ref(false);
const isOverflowingLeft = ref(false);
const hoveredBox = ref(null);

let resizeObserver;

const allRawData = ref([]);
const loadingData = ref(true);
const fetchError = ref(null);

const giziChartData = ref({ labels: [], datasets: [] });
const tdChartData = ref({ labels: [], datasets: [] });
const glukosaChartData = ref({ labels: [], datasets: [] });

const formatRekapDataForChart = (data, label) => {
    if (!data || data.length === 0) {
        return { labels: [], datasets: [] };
    }

    const chartLabels = data.map(row => {
         const labelValues = Object.keys(row)
             .filter(key => key !== 'Jumlah')
             .map(key => row[key]);
         return labelValues.map(val => val ?? '').join(' - ');
    });

    const chartValues = data.map(row => row.Jumlah ?? 0); 

    return {
        labels: chartLabels,
        datasets: [
          {
            label: label, 
            backgroundColor: '#3DB1D5',
            data: chartValues
          }
        ]
    };
};

const calculateDashboardCharts = (data) => {
    if (!data || data.length === 0) {
        giziChartData.value = { labels: [], datasets: [] };
        tdChartData.value = { labels: [], datasets: [] };
        glukosaChartData.value = { labels: [], datasets: [] };
        return;
    }

    const giziResult = calculateStatusGiziRekap(data);
    giziChartData.value = formatRekapDataForChart(giziResult, 'Jumlah Peserta');

    const tdResult = calculateTekananDarahRekap(data);
    tdChartData.value = formatRekapDataForChart(tdResult, 'Jumlah Peserta');

    const glukosaResult = calculateGlukosaRekap(data);
    glukosaChartData.value = formatRekapDataForChart(glukosaResult, 'Jumlah Peserta');

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
            console.log("Fetched raw data for dashboard charts:", allRawData.value.length, "records");

            calculateDashboardCharts(allRawData.value);

        } else {
            throw new Error("API response is not an array.");
        }

    } catch (error) {
        console.error("Error fetching raw MCU data for dashboard:", error);
        fetchError.value = new Error(error.message || "Terjadi kesalahan saat mengambil data pasien.");
        allRawData.value = []; 
        calculateDashboardCharts([]); 

    } finally {
        loadingData.value = false;
    }
});

watch(allRawData, (newData) => {
    console.log("Raw data changed, recalculating dashboard charts.");
    calculateDashboardCharts(newData);
});


function checkOverflow() {
  const el = scrollContainer.value;
  if (!el) {
       isOverflowingRight.value = false;
       isOverflowingLeft.value = false;
       return;
  }
  const scrollRightMax = el.scrollWidth - el.clientWidth;
  isOverflowingRight.value = scrollRightMax > el.scrollLeft + 1; 
  isOverflowingLeft.value = el.scrollLeft > 1; 
}

onMounted(() => {
  requestAnimationFrame(checkOverflow);

  window.addEventListener('resize', checkOverflow);

  resizeObserver = new ResizeObserver(() => {
    requestAnimationFrame(checkOverflow);
  });
  if (scrollContainer.value) {
    resizeObserver.observe(scrollContainer.value);
  }
});

onBeforeUnmount(() => {
  window.removeEventListener('resize', checkOverflow);
  if (resizeObserver && scrollContainer.value) {
    resizeObserver.unobserve(scrollContainer.value);
  }
});


const boxes = [
  {
    title: 'Pasien',
    icon: pasienIcon,
    iconHover: pasienIconHover,
    description: 'Berfungsi untuk mengelola data pasien seperti biodata dan riwayat pemeriksaan.',
    route: '/pasien',
  },
  {
    title: 'Hasil MCU',
    icon: hasilIcon,
    iconHover: hasilIconHover,
    description: 'Menampilkan dan mengelola hasil pemeriksaan MCU secara lengkap.',
    route: '/hasilmcu',
  },
  {
    title: 'Rekapitulasi',
    icon: rekapIcon,
    iconHover: rekapIconHover,
    description: 'Menyajikan ringkasan kondisi kesehatan berdasarkan kategori tertentu dalam bentuk grafik.',
    route: '/rekapitulasi',
  },
  {
    title: 'Laporan',
    icon: laporanIcon,
    iconHover: laporanIconHover,
    description: 'Menampilkan dan mengekspor laporan berdasarkan rentang waktu tertentu.',
    route: '/laporan',
  },
];

function navigate(route) {
  router.push(route);
}

function scrollRight() {
   const el = scrollContainer.value;
   if (el) {
       el.scrollBy({ left: 300, behavior: 'smooth' }); 
   }
}

function scrollLeft() {
   const el = scrollContainer.value;
   if (el) {
       el.scrollBy({ left: -300, behavior: 'smooth' });
   }
}
</script>

<style scoped>
.container-nunito {
  font-family: "Nunito", sans-serif;
}

.container-open-sans {
  font-family: "Open Sans", sans-serif;
}

.hide-scrollbar {
  -ms-overflow-style: none;  
  scrollbar-width: none;  
}

.hide-scrollbar::-webkit-scrollbar {
  display: none;
}

.scroll-wrapper {
  overflow-x: auto; 
  overflow-y: hidden; 
}


img:hover {
  transform: scale(1.1);
  transition: transform 0.2s ease;
}

.expand-enter-active,
.expand-leave-active {
  transition: all 0.3s ease;
  overflow: hidden; 
}
.expand-enter-from,
.expand-leave-to {
  opacity: 0;
  max-height: 0;
}
.expand-enter-to,
.expand-leave-from {
  opacity: 1;
  max-height: 200px; 
}

.chart-container {
    height: 300px; 
    display: flex;
    flex-direction: column;
}

.chart-container h3 {
    flex-shrink: 0; 
}

.chart-container .chart-wrapper {
    flex-grow: 1; 
    min-height: 0; 
    position: relative; 
}
</style>