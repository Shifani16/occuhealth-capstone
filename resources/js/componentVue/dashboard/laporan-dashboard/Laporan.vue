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
                    <button class="container-open-sans border border-[#3393AD] text-[#3393AD] hover:bg-[#E4EBF1] hover:border-[#E4EBF1] font-semibold px-4 py-2 rounded flex items-center gap-2 transition">
                        <img src="@/assets/export.svg" class="h-5">
                        Export
                    </button>
                </div>

                <div class="container-open-sans bg-[#F2FAFC] p-4 rounded-lg mx-7 flex items-center gap-4">
                    <span class="min-w-41 text-[16px] font-semibold">Periode tanggal input:</span>

                    <!-- Calendar Selection (From Date) -->
                    <div class="relative">
                        <input ref="startInput" type="date" v-model="startDate" class="rounded-[16px] lg:w-60 bg-[#8AD3E5] font-semibold px-4 py-2 rounded" />
                        <img 
                            src="@/assets/calendar-grouped.svg" 
                            class="absolute -right-2 top-1/2 -translate-y-1/2 h-10"
                            @click="openCalendar('start')" />
                    </div>

                    <span class=" text-[16px] font-semibold">Sampai</span>

                    <!-- Calendar Selection (To Date) -->
                    <div class="relative">
                        <input ref="endInput" type="date" v-model="endDate" class="rounded-[16px] lg:w-60 bg-[#8AD3E5] font-semibold px-4 py-2 rounded" />
                        <img 
                            src="@/assets/calendar-grouped.svg" 
                            class="absolute -right-2 top-1/2 -translate-y-1/2 h-10"
                            @click="openCalendar('end')" />
                    </div>

                    <div class="ml-auto min-w-43">
                        <button
                            @mouseover="hoverLihat = true"
                            @mouseleave="hoverLihat = false"
                            class="border border-[#2293AD] text-[#3393AD] hover:bg-[#3393AD] hover:text-white font-semibold px-4 py-2 rounded flex items-center gap-2 transition" 
                            @click="applyFilter"
                        >
                            <img :src="hoverLihat? lihatHover : lihatIcon" class="h-5" />
                            Lihat Laporan
                        </button>
                    </div>
                </div>

                <div class="mt-6 mx-7">
                    <div class="container-open-sans overflow-x-auto rounded-md">
                        <table class="table-auto text-left w-full">
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
                                    <td class="text-center px-4 py-2">{{ report.tanggal }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 flex justify-center items-center gap-3">
                        <span
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
                        </span>
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
import { ref, computed } from 'vue';

import LeftBar from '../../../composables/LeftBar.vue';
import lihatIcon from '@/assets/lihat-laporan.svg';
import lihatHover from '@/assets/lihat-laporan-hover.svg';

const hoverLihat = ref(false);

const startDate = ref('');
const endDate = ref('');
const startInput = ref(null)
const endInput = ref(null)

const isFilterActive = ref(false)

const currentPage = ref(1);
const entriesPerPage = 10;

// Dummy
const reports = ref([
  { id: 1, namaFile: 'laporan_jan.xlsx', tanggal: '2025-01-15' },
  { id: 2, namaFile: 'laporan_feb.xlsx', tanggal: '2025-02-15' },
  { id: 3, namaFile: 'laporan_mar.xlsx', tanggal: '2025-03-12' },
]);

const filteredReports = computed(() => {
  if (!isFilterActive.value || !startDate.value || !endDate.value) return reports.value;
  return reports.value.filter((r) => {
    const date = new Date(r.tanggal);
    return date >= new Date(startDate.value) && date <= new Date(endDate.value);
  });
});
const totalPages = computed(() => Math.ceil(filteredReports.value.length / entriesPerPage));

const paginatedReports = computed(() => {
  const start = (currentPage.value - 1) * entriesPerPage;
  return filteredReports.value.slice(start, start + entriesPerPage);
});

function prevPage() {
  if (currentPage.value > 1) currentPage.value--;
}

function nextPage() {
  if (currentPage.value < totalPages.value) currentPage.value++;
}

function goToPage(page) {
  currentPage.value = page;
}

function openCalendar(type) {
    if (type === 'start') startInput.value?.click();
    else if (type === 'end') endInput.value?.click();
}

function applyFilter() {
    isFilterActive.value = true;
    currentPage.value = 1;
}

</script>

<style scoped>
input[type="date"]::-webkit-calendar-picker-indicator {
  opacity: 0;
  pointer-events: all;
}

.container-nunito {
  font-family: "Nunito", sans-serif;
}
.container-open-sans {
  font-family: "Open Sans", sans-serif;
}
</style>