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
            <LeftBar active="HasilMCU" />

            <main class="flex-1 p-6">
                <h1 class="text-[48px] font-bold mb-6 ml-7 container-nunito">
                    Hasil Medical Check Up
                </h1>

                <!-- Conditional rendering for loading, error, and data -->
                <div v-if="loading" class="ml-7 mt-8 text-center text-[#3393AD]">
                     Memuat data hasil MCU...
                </div>

                 <div v-else-if="fetchError" class="ml-7 mt-8 text-center text-red-600">
                     Error: {{ fetchError }}
                     <br>
                     <!-- Add a retry button -->
                     <button @click="fetchMcuDetails(route.params.id)" class="mt-2 text-[#3393AD] underline">Coba Lagi</button>
                 </div>

                <template v-else-if="pasien && hasilMCU.length > 0">
                     <div class="ml-7 flex justify-between items-center max-w-7xl">
                         <h2 class="container-nunito text-[32px] font-bold mb-1">{{ pasien.name || 'N/A' }}</h2>
                         <div
                         class="container-open-sans bg-[#185C6D] text-white text-sm rounded-full px-4 py-1 text-[16px] font-bold whitespace-nowrap"
                         >
                             {{ pasien.med_record_id || pasien.patient_id || 'N/A' }}
                         </div>
                     </div>

                     <hr class="ml-7 mt-3 mb-3 border-t-3 border-[#185C6D]" />

                     <div class="ml-7 mr-7 mt-10">
                         <table class="lg:w-[60%] text-left border-collapse container-open-sans text-[16px]">
                             <thead>
                                 <tr class="bg-[#E6F6F9] text-[#185C6D] font-bold">
                                     <th class="py-3 px-4 border border-[#C2D6DB] w-[40%]">Pemeriksaan</th>
                                     <th class="py-3 px-4 border border-[#C2D6DB] w-[30%]">Hasil</th>
                                     <th class="py-3 px-4 border border-[#C2D6DB] w-[30%]">Tanggal Periksa</th>
                                 </tr>
                             </thead>

                             <tbody>
                                 <tr
                                     v-for="(hasil, index) in hasilMCU"
                                     :key="hasil.id || index" 
                                     class="even:bg-[#E6F6F9] odd:bg-white"
                                 >
                                     
                                     <td class="py-2 px-4 border border-[#C2D6DB]">{{ hasil.category || 'N/A' }}</td>
                                     <td class="py-2 px-4 border border-[#C2D6DB]">{{ hasil.result || 'N/A' }}</td>
                                     <td class="py-2 px-4 border border-[#C2D6DB]">{{ formatBackendDate(hasil.result_date) }}</td> <!-- Format date for display -->
                                 </tr>
                             </tbody>
                         </table>

                         <div v-if="saran" class="mt-10 ml-7">
                             <h3 class="block text-black text-lg font-semibold mb-2 container-open-sans">Saran</h3>
                             <p class="bg-[#E6F6F9] text-black rounded p-3">{{ saran }}</p>
                         </div>

                     </div>

                     <div class="flex justify-end gap-4 mt-14 text-[18px] container-open-sans">
                         <button
                             @click="goToEdit(route.params.id)"
                             @mouseover="hoveringEdit = true"
                             @mouseleave="hoveringEdit = false"
                             :class="[
                                 'border border-[#3393AD] font-semibold px-4 py-2 rounded flex items-center gap-2 transition',
                                 hoveringEdit ? 'bg-[#E4EBF1] border-[#E4EBF1] text-[#3393AD]' : 'text-[#3393AD)'
                             ]"
                         >
                             <img src="@/assets/edit.svg" class="h-5"/>
                             Edit
                         </button>
                     </div>
                </template>

                <div v-else class="ml-7 mt-8 text-center text-gray-600">
                    Data hasil MCU tidak tersedia atau tidak lengkap.
                </div>


            </main>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router'; 
import axios from 'axios'; 

import LeftBar from '../../../composables/LeftBar.vue';

const route = useRoute();
const router = useRouter();

const pasien = ref(null);
const hasilMCU = ref([]);
const saran = ref('');

const loading = ref(true);
const fetchError = ref(null); 

const hoveringEdit = ref(false);

async function fetchMcuDetails(id) {
    loading.value = true;
    fetchError.value = null; 
    pasien.value = null;
    hasilMCU.value = [];
    saran.value = '';


    try {
        const response = await axios.get(`/api/mcu-patients/${id}`);

        console.log("Fetched MCU Details:", response.data);

        // --- Assign fetched data to refs ---
        if (response.data) {
             pasien.value = response.data.patient || null;
             saran.value = response.data.saran || '';

             if (Array.isArray(response.data.individual_results)) {
                hasilMCU.value = response.data.individual_results.map(res => ({
                     ...res, 
                 }));
            } else {
                 console.warn("Individual MCU results array not found or is not an array in response:", response.data);
                 hasilMCU.value = []; 
            }

        } else {
            fetchError.value = 'Data hasil MCU tidak ditemukan di server.';
        }


    } catch (error) {
        console.error('Error fetching MCU details:', error);
        fetchError.value = 'Gagal memuat detail hasil MCU.';

        if (error.response) {
             if (error.response.status === 404) {
                 fetchError.value = 'Hasil MCU dengan ID ini tidak ditemukan.';
             } else if (error.response.data && error.response.data.error) {
                  fetchError.value = error.response.data.error;
             } else if (error.response.data && error.response.data.message) {
                  fetchError.value = error.response.data.message;
             } else if (typeof error.response.data === 'string') {
                  fetchError.value = error.response.data;
             } else {
                fetchError.value = `Gagal memuat data: ${error.response.status} ${error.response.statusText}`;
             }
         } else if (error.request) {
             fetchError.value = 'Tidak ada respons dari server saat memuat data.';
         } else {
             fetchError.value = `Terjadi kesalahan saat mengambil data: ${error.message}`;
         }

        pasien.value = null;
        hasilMCU.value = [];
        saran.value = '';

    } finally {
        loading.value = false; 
    }
}

function formatBackendDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);

    if (!isNaN(date.getTime())) {
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        return `${day}/${month}/${year}`;

    }

    console.warn("Could not parse date string for formatting:", dateString);
    return dateString; 
}

onMounted(() => {
    const mcuSessionId = route.params.id; 
    if (mcuSessionId) {
        fetchMcuDetails(mcuSessionId);
    } else {
        fetchError.value = "ID Hasil MCU tidak ditemukan di URL.";
        loading.value = false;
    }
});

function goToEdit(id) { 
    console.log("Navigating to edit for MCU Session ID:", id);
     if (id) {
        router.push({
            name: "HasilMCUEdit",
            params: { id: id }, 
        });
     } else {
         console.error("Cannot navigate to edit: MCU Session ID is missing.");
         fetchError.value = "Tidak dapat mengedit: ID sesi MCU hilang."; 
     }
}


</script>

<style scoped>
.container-nunito {
  font-family: 'Nunito', sans-serif;
}
.container-open-sans {
  font-family: 'Open Sans', sans-serif;
}

table, th, td {
    border: 1px solid #e0e0e0; 
}
thead th {
    background-color: #f0f0f0;
}
</style>