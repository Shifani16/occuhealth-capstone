
<template>
    <div>
        <header class="w-full bg-[#CAEAF2] px-5 py-3 flex justify-between items-center fixed top-0 left-0 z-50">
            <div class="flex items-center gap-6">
                <img src="@/assets/occuhelp-full-logo.svg" alt="OccuHelp Full Logo" class="h-12"/>
            </div>
        </header>

        <div class="mt-20 flex min-h-screen">
            <LeftBar active="Pasien"/>

            <main class="flex-1 p-6">
                <h1 class="text-[40px] font-bold mb-4 ml-7 container-nunito">Detail Pasien</h1>

                <div class="ml-7 flex justify-between items-center max-w-7xl">
                    <h2 class="container-nunito text-[32px] font-bold mb-1">{{ pasien.nama }}</h2>
                    <div class="container-open-sans bg-[#185C6D] text-white text-sm rounded-full px-4 py-1 text-[16px] font-bold whitespace-nowrap">
                        #Biodata Pasien
                    </div>
                </div>
                <hr class="ml-7 mt-3 mb-3 border-t-3 border-[#185C6D]"/>

                <div class="ml-7 grid gap-y-3 w-full max-w-5xl text-[20px] container-open-sans">
                    <div v-for="(value, label) in formattedPasienData" :key="label" class="grid grid-cols-2 gap-x-8 items-center">
                        <div class="text-right font-bold text-[#27394B]">{{ label }}</div>
                        <div class="text-left text-[#27394B]">{{ value }}</div>
                    </div>
                </div>

                <div class="flex justify-end gap-4 mt-14 text-[18px] container-open-sans">
                    <button
                        @mouseover="hoveringHasil = true"
                        @mouseleave="hoveringHasil = false"
                        class="border border-[#3393AD] text-[#3393AD] hover:bg-[#3393AD] hover:text-white font-semibold px-4 py-2 rounded flex items-center gap-2 transition"
                    >
                        <img :src="hoveringHasil ? hasilMCUHover : hasilMCU" class="h-5" />
                        Lihat Hasil MCU
                    </button>

                    <button
                        @click="$router.push({ path: '/pasienedit', query: pasien.value })"
                        @mouseover="hoveringEdit = true"
                        @mouseleave="hoveringEdit = false"
                        :class="['border border-[#3393AD] font-semibold px-4 py-2 rounded flex items-center gap-2 transition', hoveringEdit ? 'bg-[#E4EBF1] border-[#E4EBF1] text-[#3393AD]' : 'text-[#3393AD]']"
                    >
                        <img src="@/assets/edit.svg" class="h-5" />
                        Edit
                    </button>
                </div>

                <hr class="my-4"/>
                <p class="container-open-sans text-left text-sm text-black font-bold text-[20px] mb-2">© 2025 OccuHelp. All Rights Reserved.</p>
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'

import LeftBar from '../../../composables/LeftBar.vue'
import hasilMCU from '@/assets/lihat-hasil-mcu.svg'
import hasilMCUHover from '@/assets/lihat-hasil-mcu-hover.svg'

const hoveringHasil = ref(false)
const hoveringEdit = ref(false)

const route = useRoute()
const pasien = computed(() => route.query)

const formattedPasienData = computed(() => ({
  'Tanggal Pemeriksaan': pasien.value.tanggal_pemeriksaan,
  'Nomor Rekam Medis': pasien.value.no_rekam_medis,
  'Nomor Pasien': pasien.value.no_pasien,
  'Jenis Kelamin': pasien.value.jenis_kelamin,
  'Umur': pasien.value.umur,
  'Tempat dan Tanggal Lahir': pasien.value.tempat_tanggal_lahir,
  'Alamat': pasien.value.alamat
}))
</script>

<style scoped>
.container-nunito {
  font-family: "Nunito", sans-serif;
}
.container-open-sans {
  font-family: "Open Sans", sans-serif;
}
</style>