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

                <div v-if="loading" class="text-center text-[#3393AD] mt-8">
                    Memuat detail pasien...
                </div>

                <div v-else-if="fetchError" class="text-center text-red-600 mt-8">
                    Gagal memuat detail pasien: {{ fetchError }}
                     <br>
                    <button @click="fetchPatientData(route.params.id)" class="mt-2 text-[#3393AD] underline">Coba Lagi</button>
                </div>

                <template v-else-if="pasienData">
                    <div class="ml-7 flex justify-between items-center max-w-7xl">
                        <h2 class="container-nunito text-[32px] font-bold mb-1">{{ pasienData.name || 'Nama Tidak Tersedia' }}</h2>
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
                            @click="goToHasil(pasienData)"
                            @mouseover="hoveringHasil = true"
                            @mouseleave="hoveringHasil = false"
                            class="border border-[#3393AD] text-[#3393AD] hover:bg-[#3393AD] hover:text-white font-semibold px-4 py-2 rounded flex items-center gap-2 transition disabled:opacity-50 disabled:cursor-not-allowed"
                            :disabled="loading || fetchError || !pasienData || !pasienData.latest_mcu_id"
                             title="Lihat Hasil MCU Terbaru"
                        >
                            <img :src="hoveringHasil ? hasilMCUHover : hasilMCU" class="h-5" />
                            Lihat Hasil MCU Terbaru
                        </button>

                        <button
                            @click="goToEdit(pasienData)"
                            @mouseover="hoveringEdit = true"
                            @mouseleave="hoveringEdit = false"
                             :disabled="loading || fetchError || !pasienData"
                            :class="['border border-[#3393AD] font-semibold px-4 py-2 rounded flex items-center gap-2 transition disabled:opacity-50 disabled:cursor-not-allowed', hoveringEdit ? 'bg-[#E4EBF1] border-[#E4EBF1] text-[#3393AD]' : 'text-[#3393AD]']"
                             title="Edit Data Pasien"
                        >
                            <img src="@/assets/edit.svg" class="h-5" />
                            Edit Data Pasien
                        </button>
                    </div>
                </template>

                <div v-else class="text-center text-gray-500 mt-8">
                     Data pasien tidak ditemukan setelah memuat.
                </div>


                <hr class="my-4 ml-7"/>
                <p class="ml-7 container-open-sans text-left text-sm text-black font-bold text-[20px] mb-2">© 2025 OccuHelp. All Rights Reserved.</p>
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'

import LeftBar from '../../../composables/LeftBar.vue'
import hasilMCU from '@/assets/lihat-hasil-mcu.svg'
import hasilMCUHover from '@/assets/lihat-hasil-mcu-hover.svg'

// --- State ---
const hoveringHasil = ref(false)
const hoveringEdit = ref(false)

const route = useRoute()
const router = useRouter()

const pasienData = ref(null)
const loading = ref(true)
const fetchError = ref(null)

// --- Computed Properties ---
const formattedPasienData = computed(() => {
    const data = pasienData.value;
    if (!data) {
        return {};
    }

   
    const examinationDate = data.examination_date
        ? new Date(data.examination_date).toLocaleDateString('id-ID', {
              year: 'numeric',
              month: '2-digit',
              day: '2-digit'
          })
        : 'Belum Ada MCU'; 


     const birthDateDisplay = data.birth_date
        ? new Date(data.birth_date).toLocaleDateString('id-ID', {
             year: 'numeric',
             month: 'long',
             day: 'numeric'
         })
        : '';

    const tempatTanggalLahirDisplay = data.birth_place && birthDateDisplay
        ? `${data.birth_place}, ${birthDateDisplay}`
        : data.birth_place || birthDateDisplay || 'N/A';


    return {
      'Nama Pasien': data.name || 'N/A',
      'Tanggal Pemeriksaan Terakhir': examinationDate,
      'Nomor Rekam Medis': data.med_record_id || 'N/A',
      'Nomor Pasien': data.patient_id || 'N/A',
      'Jenis Kelamin': data.gender || 'N/A',
      'Umur': data.age?.toString() || 'N/A',
      'Tempat dan Tanggal Lahir': tempatTanggalLahirDisplay,
        'Unit': data.unit || 'N/A',
       'Jabatan': data.jabatan || 'N/A',
       'Ketenagaan': data.ketenagaan || 'N/A',

    }
})

// --- Data Fetching ---
async function fetchPatientData(id) {
    if (!id) {
        fetchError.value = "ID pasien tidak ditemukan di URL.";
        loading.value = false;
        pasienData.value = null;
        return;
    }

    loading.value = true;
    fetchError.value = null;
    pasienData.value = null;

    try {
        const response = await axios.get(`/api/patients/${id}`);
        console.log('Fetched patient data for detail:', response.data);

        if (response.data) {
             pasienData.value = response.data;
        } else {
            fetchError.value = "Data pasien tidak ditemukan di server (respons kosong).";
            pasienData.value = null;
        }


    } catch (error) {
        console.error('Error fetching patient data for detail:', error);
        pasienData.value = null;

        fetchError.value = 'Gagal memuat data pasien.';
         if (error.response) {
             if (error.response.status === 404) {
                 fetchError.value = "Pasien tidak ditemukan.";
             } else if (error.response.data) {
                 if (error.response.data.message) {
                      fetchError.value = error.response.data.message;
                 } else if (error.response.data.error) {
                      fetchError.value = error.response.data.error;
                 } else if (typeof error.response.data === 'string') {
                      fetchError.value = error.response.data;
                 }
             } else {
                fetchError.value = `Gagal memuat data: ${error.response.status} ${error.response.statusText}`;
             }
         } else if (error.request) {

             fetchError.value = 'Tidak ada respons dari server. Periksa koneksi Anda.';
         } else {
             fetchError.value = `Terjadi kesalahan: ${error.message}`;
         }


    } finally {
        loading.value = false;
    }
}

onMounted(() => {
  const id = route.params.id;
  if (id) {
    fetchPatientData(id);
  } else {
    fetchError.value = "ID pasien tidak ditemukan di URL.";
    loading.value = false;
  }
});


// --- Navigation ---
function goToEdit(pasien) {
     console.log("Attempting to navigate to edit for patient ID:", pasien?.id);
     const patientId = pasien?.id;

     if (patientId) {
          router.push({ name: 'PasienEdit', params: { id: patientId } });
     } else {
          console.error("Cannot navigate to edit: Patient ID is missing", pasien);
          fetchError.value = "Tidak dapat mengedit: ID pasien tidak tersedia.";
     }
}

function goToHasil(pasien) {
     console.log("Attempting to navigate to Hasil MCU for MCU ID:", pasien?.latest_mcu_id);
     const mcuId = pasien?.latest_mcu_id;

     if (mcuId) {
          router.push({ name: 'HasilMCUDetail', params: { id: mcuId } });
     } else {
          console.error("Cannot navigate to Hasil MCU: Latest MCU ID is missing", pasien);
          fetchError.value = "Tidak dapat melihat hasil MCU: Data hasil MCU terbaru tidak ditemukan untuk pasien ini.";
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
</style>