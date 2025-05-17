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
                <div class="container-open-sans relative">
                    <div
                    ref="scrollContainer"
                    class="flex gap-6 overflow-x-auto overflow-y-hidden pb-4 hide-scrollbar scroll-wrapper"
                    @scroll="checkOverflow"
                    >
                        <div
                            v-for="item in boxes"
                            :key="item.title"
                            class="bg-white rounded-xl shadow-md p-6 w-60 flex-shrink-0 cursor-pointer hover:shadow-lg transition-all duration-200"
                            @mouseenter="hoveredBox = item.title"
                            @mouseleave="hoveredBox = null"
                            @click="navigate(item.route)"
                        >
                            <div class="flex items-center gap-3">
                                <img :src="item.icon" class="h-8 w-8" />
                                <span class="text-lg font-semibold text-[#27394B]">
                                    {{ item.title }}
                                </span>
                            </div>

                            <div
                                v-if="hoveredBox === item.title"
                                class="text-left mt-4 bg-[#F9F9F9] text-md text-[#4A4A4A] rounded-md p-3"
                            >
                                {{ item.description }}
                            </div>
                        </div>
                    </div>

                    <img
                        v-if="isOverflowing"
                        src="@/assets/arrow-left.svg"
                        @click="scrollRight"
                        class="absolute right-0 top-1/2 -translate-y-1/2 z-50 bg-white rounded-full p-2 shadow cursor-pointer hover:scale-110 transition-transform"
                    />
                    </div>
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { useRouter } from 'vue-router';

import LeftBar from '../../../composables/LeftBar.vue';
import RekapChart from '../../../composables/RekapChart.vue';

import pasienIcon from '@/assets/dashboard-pasien.svg';
import hasilIcon from '@/assets/dashboard-hasil-mcu.svg';
import rekapIcon from '@/assets/dashboard-rekapitulasi.svg';
import laporanIcon from '@/assets/dashboard-report.svg';

const router = useRouter();
const expandedBox = ref(null);
const scrollContainer = ref(null);
const isOverflowing = ref(false);
const hoveredBox = ref(null);

function checkOverflow() {
    const el = scrollContainer.value;
    isOverflowing.value = el.scrollWidth > el.clientWidth;
}

function toggleExpand(title) {
    expandedBox.value = expandedBox.value === title ? null : title;
}

onMounted(() => {
    checkOverflow();
    window.addEventListener('resize', checkOverflow);
});
onBeforeUnmount(() => {
    window.removeEventListener('resize', checkOverflow);
});

const boxes = [
    {
        title: 'Pasien',
        icon: pasienIcon,
        description: 'Berfungsi untuk mengelola data pasien seperti biodata dan riwayat pemeriksaan.',
        route: '/pasien',
    },
    {
        title: 'Hasil MCU',
        icon: hasilIcon,
        description: 'Menampilkan dan mengelola hasil pemeriksaan MCU secara lengkap.',
        route: '/hasilmcu',
    },
    {
        title: 'Rekapitulasi',
        icon: rekapIcon,
        description: 'Menyajikan ringkasan kondisi kesehatan berdasarkan kategori tertentu dalam bentuk grafik.',
        route: '/rekapitulasi',
    },
    {
        title: 'Laporan',
        icon: laporanIcon,
        description: 'Menampilkan dan mengekspor laporan berdasarkan rentang waktu tertentu.',
        route: '/laporan',
    },
];

function navigate(route) {
    router.push(route);
}

function scrollLeft() {
    scrollContainer.value.scrollBy({ left: -200, behavior: 'smooth' });
}

function scrollRight() {
    scrollContainer.value.scrollBy({ left: 200, behavior: 'smooth' });
}
</script>

<style scoped>
.container-nunito {
  font-family: "Nunito", sans-serif;
}

.container-open-sans {
  font-family: "Open Sans", sans-serif;
}

.group:hover .group-hover\:block {
    display: block;
}

.scroll-wrapper {
    position: relative;
    overflow-y: hidden !important;
}

img:hover {
    transform: scale(1.1);
    transition: transform 0.2s ease;
}
</style>