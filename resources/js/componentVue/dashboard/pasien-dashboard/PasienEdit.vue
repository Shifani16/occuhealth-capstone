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
                <h1 class="text-[40px] font-bold mb-4 ml-7 container-nunito">Edit Pasien</h1>

                <div class="ml-7 flex justify-between items-center max-w-7xl">
                    <h2 class="container-nunito text-[32px] font-bold mb-1">{{ pasien.nama }}</h2>
                    <div class="container-open-sans bg-[#185C6D] text-white text-sm rounded-full px-4 py-1 text-[16px] font-bold whitespace-nowrap">
                        #Biodata Pasien
                    </div>
                </div>
                <hr class="ml-7 mt-3 mb-3 border-t-3 border-[#185C6D]"/>

                <div class="mx-auto grid gap-y-3 w-full max-w-6xl text-[20px] container-open-sans">
                    <div v-for="(value, label) in formData" :key="label" class="grid grid-cols-2 gap-x-8 items-center">
                        <div class="text-right font-bold text-[#27394B]">
                            {{ label }}
                        </div>
                        <input
                            v-model="formData[label]" 
                            class="bg-[#E6F6F9] px-3 py-1 rounded w-full" 
                        />
                    </div>
                </div>

                <div class="flex justify-end gap-4 mt-14 text-[18px] container-open-sans">
                    <button
                        @click="cancel"
                        @mouseover="hoveringBatal = true"
                        @mouseleave="hoveringBatal = false"
                        :class="['border font-semibold px-6 py-2 rounded flex items-center gap-2 transition', 
                            hoveringBatal ? 'bg-[#3393AD] text-white border-[#3393AD]' : 'text-[#3393AD] border-[#3393AD]']"
                        >
                        <img :src="hoveringBatal ? batalHoverIcon : batalIcon" class="h-5" />
                        Batal
                    </button>

                    <button
                        @click="save"
                        @mouseover="hoveringSimpan = true"
                        @mouseleave="hoveringSimpan = false"
                        :class="['border border-[#3393AD] text-[#3393AD] font-semibold px-6 py-2 rounded flex items-center gap-2 transition', 
                            hoveringSimpan ? 'bg-[#E4EBF1] border-[#E4EBF1]' : '']"
                    >
                        <img src="@/assets/simpan.svg" class="h-5" />
                        Simpan
                    </button>
                </div>
                <hr class="my-4"/>
                <p class="container-open-sans text-left text-sm text-black font-bold text-[20px] mb-2">© 2025 OccuHelp. All Rights Reserved.</p>
            </main>
        </div>
    </div>

    <!-- Pop Up -->
    <div v-if="showSuccessPopup" class="fixed inset-0 bg-[rgba(0,0,0,0.3)] flex items-center justify-center z-50">
      <div class="bg-[#27394B] p-6 rounded-2xl shadow-md w-96 text-center">
        <img src="@/assets/circle-check.svg" class="w-20 mx-auto mb-4" />
        <p class="text-white font-medium mb-4">Data pasien berhasil diubah</p>
        <div class="flex justify-center">
          <button @click="goToPasien" class="border border-white text-white px-4 py-2 rounded hover:bg-white hover:text-[#27394B] font-semibold">Tutup</button>
        </div>
      </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import LeftBar from '../../../composables/LeftBar.vue'

import batalIcon from '@/assets/batal.svg'
import batalHoverIcon from '@/assets/batal-hover.svg'

const hoveringBatal = ref(false)
const hoveringSimpan = ref(false)
const showSuccessPopup = ref(false)

const route = useRoute()
const router = useRouter()

const pasien = computed(() => route.query)

const formData = reactive({
  'Tanggal Pemeriksaan': pasien.value.tanggal_pemeriksaan,
  'Nomor Rekam Medis': pasien.value.no_rekam_medis,
  'Nomor Pasien': pasien.value.no_pasien,
  'Jenis Kelamin': pasien.value.jenis_kelamin,
  'Umur': pasien.value.umur,
  'Tempat dan Tanggal Lahir': pasien.value.tempat_tanggal_lahir,
  'Alamat Lengkap': pasien.value.alamat
})

function cancel() {
  router.push('/pasien')
}

function save() {
  showSuccessPopup.value = true
}

function goToPasien() {
  showSuccessPopup.value = false
  router.push('/pasien')
}
</script>

<style scoped>
.container-nunito {
  font-family: "Nunito", sans-serif;
}
.container-open-sans {
  font-family: "Open Sans", sans-serif;
}
</style>