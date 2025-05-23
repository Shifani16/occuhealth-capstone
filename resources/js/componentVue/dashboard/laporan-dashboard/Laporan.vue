<template>
    <div>
        <header class="w-full bg-[#CAEAF2] px-5 py-3 flex justify-between items-center fixed top-0 left-0 z-50">
            <div class="flex items-center gap-6">
                <img src="@/assets/occuhelp-full-logo.svg" alt="OccuHelp Full Logo" class="h-12"/>
            </div>
        </header>

        <div class="mt-20 flex min-h-screen">
            <LeftBar active="Laporan"/>

            <main class="flex-1 p-6">
                <h1 class="text-[48px] font-bold mb-6 ml-7 container-nunito">Laporan</h1>

                <div class="flex justify-end mb-6 mr-6">
                    <!-- Modify this button to trigger the chart capture and PDF generation -->
                    <!-- <button
                        class="container-open-sans border border-[#3393AD] text-[#3393AD] hover:bg-[#E4EBF1] hover:border-[#E4EBF1] font-semibold px-4 py-2 rounded flex items-center gap-2 transition"
                        @click="generateReportWithCharts"
                        :disabled="isLoading"
                    >
                        <img src="@/assets/export.svg" class="h-5">
                        {{ isLoading ? 'Generating...' : 'Export' }} Change text while loading
                    </button> -->
                </div>

                <div class="container-open-sans bg-[#F2FAFC] p-4 rounded-lg mx-7 flex items-center gap-4">
                    <span class="min-w-41 text-[16px] font-semibold">Periode tanggal input:</span>

                    <!-- Calendar Selection (From Date) -->
                    <div class="relative">
                        <input type="date" v-model="startDate" class="rounded-[16px] lg:w-60 bg-[#8AD3E5] font-semibold px-4 py-2 rounded" />
                        <img
                           src="@/assets/calendar-grouped.svg"
                           class="absolute -right-2 top-1/2 -translate-y-1/2 h-10"
                           style="pointer-events: none;"  />
                    </div>

                    <span class=" text-[16px] font-semibold">Sampai</span>

                    <!-- Calendar Selection (To Date) -->
                    <div class="relative">
                        <input type="date" v-model="endDate" class="rounded-[16px] lg:w-60 bg-[#8AD3E5] font-semibold px-4 py-2 rounded" />
                        <img
                            src="@/assets/calendar-grouped.svg"
                            class="absolute -right-2 top-1/2 -translate-y-1/2 h-10"
                            style="pointer-events: none;" />
                    </div>

                    <div class="ml-auto min-w-43">
                         <!-- Modify this button as well -->
                        <button
                            @mouseover="hoverLihat = true"
                            @mouseleave="hoverLihat = false"
                            class="border border-[#2293AD] text-[#3393AD] hover:bg-[#3393AD] hover:text-white font-semibold px-4 py-2 rounded flex items-center gap-2 transition"
                            @click="generateReportWithCharts"
                             :disabled="isLoading"
                        >
                            <img src="@/assets/export.svg" class="h-5">
                            {{ isLoading ? 'Generating...' : 'Export' }}
                        </button>
                    </div>
                </div>


                 <div id="charts-for-pdf" style="position: absolute; left: -9999px; top: -9999px;">
      
                 </div>


                <!-- The table for displaying a list of reports remains below -->
                <div class="mt-6 mx-7">
                    <div class="container-open-sans overflow-x-auto rounded-md">
                        <!-- <table class="table-auto text-left w-full">
                            <thead class="bg-white font-bold">
                                <tr>
                                    <th class="text-center px-4 py-3">No.</th>
                                    <th class="px-4 py-3">Nama File yang Diunduh</th>
                                    <th class="text-center px-4 py-3">Tanggal</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="(report, index) in paginatedReports" :key="report.id" class="bg-[#F7F6FE]">
                                    <td class="text-center px-4 py-2">{{ index + 1 + (currentPage - 1) * entriesPerPage }}</td>
                                    <td class="px-4 py-2">{{ report.namaFile }}</td>
                                    <td class="text-center px-4 py-2">{{ formatDateDisplay(report.tanggal) }}</td>
                                </tr>
                                 <tr v-if="reports.length === 0">
                                    <td colspan="3" class="text-center px-4 py-2">Tidak ada laporan yang tersedia.</td>
                                </tr>
                            </tbody>
                        </table> -->
                    </div>

                    <!-- Pagination controls -->
                    <div class="mt-4 flex justify-center items-center gap-3" v-if="totalPages > 1">
                        <!-- <span
                            class="text-[#299BB8] cursor-pointer"
                            :class="{ 'opacity-50 cursor-not-allowed': currentPage === 1 }"
                            @click="prevPage"
                        >
                            Sebelumnya
                        </span>

                        <span
                            v-for="page in totalPages"
                            :key="page"
                            @click="goToPage(page)"
                            class="cursor-pointer px-3 py-1 rounded"
                            :class="{
                                'bg-[#8AD3E5] text-white' : currentPage === page,
                                'bg-[#F2FAFC] text-black' : currentPage !== page
                            }"
                        >
                            {{ page }}
                        </span>

                        <span
                            class="text-[#299BB8] cursor-pointer"
                            :class="{ 'opacity-50 cursor-not-allowed': currentPage === totalPages }"
                            @click="nextPage"
                        >
                            Selanjutnya
                        </span> -->
                    </div>
                </div>


                <hr class="my-4 ml-7" />
                <p class="ml-7 container-open-sans text-left text-sm text-black font-bold text-[20px] mb-2">
                    © 2025 OccuHelp. All Rights Reserved.
                </p>
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue'; // Import nextTick
import axios from 'axios';

import LeftBar from '../../../composables/LeftBar.vue';
import lihatIcon from '@/assets/lihat-laporan.svg';
import lihatHover from '@/assets/lihat-laporan-hover.svg';

const hoverLihat = ref(false);

const startDate = ref('');
const endDate = ref('');

const isLoading = ref(false); // Loading state for the button

const reports = ref([
  { id: 1, namaFile: 'Laporan_MCU_20250101_sd_20250131.pdf', tanggal: '2025-01-15' },
  { id: 2, namaFile: 'Laporan_MCU_20250201_sd_20250228.pdf', tanggal: '2025-02-15' },
   { id: 3, namaFile: 'Laporan_MCU_20250301_sd_20250331.pdf', tanggal: '2025-03-12' },
    { id: 4, namaFile: 'Laporan_MCU_20250401_sd_20250430.pdf', tanggal: '2025-04-10' },
    { id: 5, namaFile: 'Laporan_MCU_20250501_sd_20250531.pdf', tanggal: '2025-05-20' },
    { id: 6, namaFile: 'Laporan_MCU_20250601_sd_20250630.pdf', tanggal: '2025-06-05' },
    { id: 7, namaFile: 'Laporan_MCU_20250701_sd_20250731.pdf', tanggal: '2025-07-18' },
    { id: 8, namaFile: 'Laporan_MCU_20250801_sd_20250831.pdf', tanggal: '2025-08-25' },
    { id: 9, namaFile: 'Laporan_MCU_20250901_sd_20250930.pdf', tanggal: '2025-09-01' },
    { id: 10, namaFile: 'Laporan_MCU_20251001_sd_20251031.pdf', tanggal: '2025-10-11' },
    { id: 11, namaFile: 'Laporan_MCU_20251101_sd_20251130.pdf', tanggal: '2025-11-22' },
    { id: 12, namaFile: 'Laporan_MCU_20251201_sd_20251231.pdf', tanggal: '2025-12-03' },
]);

const currentPage = ref(1);
const entriesPerPage = 10;
const totalPages = computed(() => Math.ceil(reports.value.length / entriesPerPage));
const paginatedReports = computed(() => {
  const start = (currentPage.value - 1) * entriesPerPage;
  return reports.value.slice(start, start + entriesPerPage);
});
function prevPage() { if (currentPage.value > 1) currentPage.value--; }
function nextPage() { if (currentPage.value < totalPages.value) currentPage.value++; }
function goToPage(page) { currentPage.value = page; }
function formatDateDisplay(dateString) {
    if (!dateString) return '';
     try {
        const date = new Date(dateString.split('/').reverse().join('-'));
        if (isNaN(date.getTime())) {
             const fallbackDate = new Date(dateString);
             if(isNaN(fallbackDate.getTime())) {
                 return dateString;
             }
         }
        const dateParts = dateString.split('-');
        if(dateParts.length === 3) {
            return `${dateParts[2]}/${dateParts[1]}/${dateParts[0]}`;
        }
         const d = new Date(dateString);
         if (!isNaN(d.getTime())) {
             const day = String(d.getDate()).padStart(2, '0');
             const month = String(d.getMonth() + 1).padStart(2, '0');
             const year = d.getFullYear();
             return `${day}/${month}/${year}`;
         }
        return dateString;
    } catch (error) {
        console.error("Error formatting date:", dateString, error);
        return dateString;
    }
}

async function getChartImageData() {
    const chartImages = {};

    const chartsContainer = document.getElementById('charts-for-pdf');
    if (!chartsContainer) {
        console.error("Chart container element not found!");
        return chartImages; // Return empty object
    }

    const chartElements = chartsContainer.querySelectorAll('canvas'); // Example: Find all canvas elements inside the container

    for (const canvas of chartElements) {
        try {
            const chartTitle = canvas.dataset.chartTitle || 'Unknown Chart'; // Assuming data-chart-title attribute

            const base64Image = canvas.toDataURL('image/png', 1.0); // Get data URI (PNG, full quality)

            // Store the base64 string keyed by title
            chartImages[chartTitle] = base64Image;

        } catch (error) {
            console.error("Error capturing chart image:", error);
            // Continue to the next chart
        }
    }

    console.log("Captured chart images:", Object.keys(chartImages).length);
    return chartImages;
}

// New function to handle the export process including chart capture
async function generateReportWithCharts() {
    if (!startDate.value || !endDate.value) {
        alert('Mohon pilih periode tanggal terlebih dahulu.');
        return;
    }

    isLoading.value = true;

    try {
         await nextTick();

        const chartImagesData = await getChartImageData(); 

        const response = await axios({
            url: '/api/reports/generate', 
            method: 'POST',
            responseType: 'blob', // Expect binary PDF data
            data: { 
                startDate: startDate.value,
                endDate: endDate.value,
                chartImages: chartImagesData, // Send the captured images
            },
        });

        // Step 4: Handle the PDF download
        const blob = new Blob([response.data], { type: 'application/pdf' });
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `laporan_MCU_${startDate.value.replace(/-/g, '')}_sd_${endDate.value.replace(/-/g, '')}.pdf`);
        document.body.appendChild(link);
        link.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(link);

        console.log('Report generated and downloaded successfully.');

        // Optional: Fetch updated reports list
        // fetchReportsList();

    } catch (error) {
        console.error('Error generating report:', error);
        let errorMessage = 'Gagal membuat laporan.';
         if (error.response && error.response.data) {
             // Try to read the error message from the response blob if it's a JSON error
             const reader = new FileReader();
             reader.onload = function() {
                 try {
                     const errorJson = JSON.parse(reader.result);
                     errorMessage = errorJson.message || errorMessage + ' Detail: ' + (errorJson.error || 'Unknown error from server.');
                 } catch (parseError) {
                     // If it's not JSON, use a generic message
                     errorMessage = errorMessage + ' Detail: Respons server tidak dapat dibaca.';
                 }
                 alert(errorMessage); // Display the message after reading the blob
             };
             reader.readAsText(error.response.data); // Read the blob as text
         } else {
            // Handle network errors or errors without response data
             alert(errorMessage + ' Terjadi kesalahan jaringan atau server tidak merespons.');
         }


    } finally {
        isLoading.value = false;
    }
}

// The original applyFilter might be removed or repurposed if generateReportWithCharts replaces its functionality
// function applyFilter() {
//    console.log('Filter applied - now triggering PDF generation with charts');
//    generateReportWithCharts();
// }

</script>

<style scoped>
/* Keep existing styles */
input[type="date"]::-webkit-calendar-picker-indicator {
  opacity: 0;
  pointer-events: all;
  position: absolute;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 100%;
  cursor: pointer;
  z-index: 1;
}

input[type="date"]::-moz-calendar-picker-indicator {
    opacity: 0;
    pointer-events: all;
    cursor: pointer;
    z-index: 1;
}

.container-nunito {
  font-family: "Nunito", sans-serif;
}
.container-open-sans {
  font-family: "Open Sans", sans-serif;
}
</style>