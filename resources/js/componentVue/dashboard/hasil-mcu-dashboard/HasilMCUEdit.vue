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

            <main>
                <h1 class="text-[48px] font-bold mb-6 ml-7 container-nunito">
                    Edit Hasil Medical Check Up Pasien
                </h1>

                <div
                    v-if="loading"
                    class="ml-7 mt-8 text-center text-[#3393AD]"
                >
                    Memuat data...
                </div>
                <div
                    v-else-if="fetchError"
                    class="ml-7 mt-8 text-center text-red-600"
                >
                    Error: {{ fetchError }}
                    <br />
                    <!-- Add a retry button -->
                    <button
                        @click="fetchMcuDetails(route.params.id)"
                        class="mt-2 text-[#3393AD] underline"
                    >
                        Coba Lagi
                    </button>
                </div>

                <template v-else-if="pasien">
                    <div
                        class="ml-7 flex justify-between items-center max-w-7xl"
                    >
                        <!-- Use backend field names for patient data -->
                        <h2 class="container-nunito text-[32px] font-bold mb-1">
                            {{ pasien.name || "N/A" }}
                        </h2>
                        <div
                            class="container-open-sans bg-[#185C6D] text-white text-sm rounded-full px-4 py-1 text-[16px] font-bold whitespace-nowrap"
                        >
                            <!-- Use backend field names for patient IDs -->
                            {{
                                pasien.med_record_id ||
                                pasien.patient_id ||
                                "N/A"
                            }}
                        </div>
                    </div>
                    <hr class="ml-7 mt-3 mb-3 border-t-3 border-[#185C6D]" />

                    <div class="ml-7 mr-7">
                        <!-- Table of individual MCU results -->
                        <table
                            v-if="hasilMCUEdit && hasilMCUEdit.length > 0"
                            class="w-full text-left border-collapse container-open-sans text-[16px]"
                        >
                            <thead>
                                <tr
                                    class="bg-[#E6F6F9] text-[#185C6D] font-bold"
                                >
                                    <th
                                        class="py-3 px-4 border border-[#C2D6DB] w-[40%]"
                                    >
                                        Pemeriksaan
                                    </th>
                                    <th
                                        class="py-3 px-4 border border-[#C2D6DB] w-[30%]"
                                    >
                                        Hasil Asli
                                    </th>
                                    <!-- Display original value -->
                                    <th
                                        class="py-3 px-4 border border-[#C2D6DB] w-[30%]"
                                    >
                                        Hasil Koreksi
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr
                                    v-for="(hasil, index) in hasilMCUEdit"
                                    :key="hasil.id || index"
                                    class="even:bg-[#E6F6F9] odd:bg-white"
                                >
                                    <!-- Use backend field names from McuResult -->
                                    <td
                                        class="py-2 px-4 border border-[#C2D6DB]"
                                    >
                                        {{ hasil.category || "N/A" }}
                                    </td>
                                    <!-- Show original value from the non-edited array -->
                                    <td
                                        class="py-2 px-4 border border-[#C2D6DB]"
                                    >
                                        {{ hasilMCU[index].result || "N/A" }}
                                    </td>
                                    <td
                                        class="py-2 px-4 border border-[#C2D6DB]"
                                    >
                                        <input
                                            v-model="hasilMCUEdit[index].result"
                                            class="w-full border px-2 py-1 rounded"
                                        />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div v-else class="ml-7 mt-8 text-center text-gray-600">
                            Tidak ada hasil pemeriksaan yang ditemukan untuk
                            diedit.
                        </div>

                        <div class="mt-10">
                            <label
                                class="block text-black text-lg font-semibold mb-2 container-open-sans"
                                >Saran</label
                            >
                            <textarea
                                v-model="saran"
                                placeholder="Ketik saran untuk pasien di sini"
                                class="w-full h-32 bg-[#E6F6F9] text-black rounded p-3 resize-none"
                            ></textarea>
                        </div>

                        <div
                            class="flex justify-end gap-4 mt-14 text-[18px] container-open-sans"
                        >
                            <!-- Buttons -->
                            <button
                                @click="cancel"
                                class="border font-semibold px-6 py-2 rounded flex items-center gap-2 transition border-[#3393AD] text-[#3393AD] hover:bg-[#3393AD] hover:text-white"
                            >
                                <img src="@/assets/batal.svg" class="h-5" />
                                Batal
                            </button>

                            <button
                                @click="save"
                                class="border border-[#3393AD] text-[#3393AD] font-semibold px-6 py-2 rounded flex items-center gap-2 transition hover:bg-[#E4EBF1]"
                            >
                                <img src="@/assets/simpan.svg" class="h-5" />
                                Simpan
                            </button>
                        </div>
                    </div>
                </template>
                <div v-else class="ml-7 mt-8 text-center text-gray-600">
                    Data hasil MCU tidak tersedia.
                </div>
            </main>
        </div>

        <div
            v-if="showSuccessPopup"
            class="fixed inset-0 bg-[rgba(0,0,0,0.3)] flex items-center justify-center z-50"
        >
            <div
                class="bg-[#27394B] p-6 rounded-2xl shadow-md w-96 text-center"
            >
                <img
                    src="@/assets/circle-check.svg"
                    class="w-20 mx-auto mb-4"
                />
                <p class="text-white font-medium mb-4">
                    Perubahan berhasil disimpan!
                </p>
                <div class="flex justify-center">
                    <button
                        @click="
                            showSuccessPopup = false;
                            goToPasien();
                        "
                        class="border border-white text-white px-4 py-2 rounded hover:bg-white hover:text-[#27394B] font-semibold"
                    >
                        OK
                    </button>
                </div>
            </div>
        </div>
        <div
            v-if="showErrorPopup"
            class="fixed inset-0 bg-[rgba(0,0,0,0.3)] flex items-center justify-center z-50"
        >
            <div
                class="bg-[#27394B] p-6 rounded-2xl shadow-md w-96 text-center"
            >
                <img src="@/assets/cloud-x.svg" class="w-20 mx-auto mb-4" />
                <p class="text-white font-medium mb-4">
                    {{ saveErrorMessage }}
                </p>
                <!-- Use a specific error message ref -->
                <div class="flex justify-center">
                    <button
                        @click="showErrorPopup = false"
                        class="border border-white text-white px-4 py-2 rounded hover:bg-white hover:text-[#27394B] font-semibold"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";
import LeftBar from "../../../composables/LeftBar.vue";

const route = useRoute();
const router = useRouter();

const hoveringBatal = ref(false);
const hoveringSimpan = ref(false);

const pasien = ref(null); 
const hasilMCU = ref([]); 
const hasilMCUEdit = ref([]); 
const saran = ref(""); 

const loading = ref(true); 
const fetchError = ref(null); 
const showSuccessPopup = ref(false);
const showErrorPopup = ref(false); 
const saveErrorMessage = ref(""); 

const batalIcon = "@/assets/batal.svg";
const batalHoverIcon = "@/assets/batal-hover.svg";

async function fetchMcuDetails(id) {
    loading.value = true;
    fetchError.value = null; 
    pasien.value = null; 
    hasilMCU.value = [];
    hasilMCUEdit.value = [];
    saran.value = "";

    try {
        const response = await axios.get(`/api/mcu-patients/${id}`);

        console.log("Fetched MCU Details:", response.data);

        // --- Assign fetched data to refs ---
        if (response.data) {
            pasien.value = response.data.patient || null; 
            saran.value = response.data.saran || ""; 

           
            if (Array.isArray(response.data.individual_results)) {
                
                hasilMCU.value = response.data.individual_results.map(
                    (res) => ({
                   
                        id: res.id, 
                        category: res.category,
                        result: res.result,
                        result_date: res.result_date,
                        patient_id: res.patient_id,
                     
                    })
                );
                hasilMCUEdit.value = JSON.parse(JSON.stringify(hasilMCU.value));
            } else {
                console.warn(
                    "Individual MCU results array not found or is not an array in response:",
                    response.data
                );
                hasilMCU.value = [];
                hasilMCUEdit.value = [];
            }
        } else {
            fetchError.value = "Data hasil MCU tidak ditemukan di server.";
        }
    } catch (error) {
        console.error("Error fetching MCU details:", error);
        fetchError.value = "Gagal memuat detail hasil MCU.";
        if (error.response) {
            if (error.response.status === 404) {
                fetchError.value = "Hasil MCU dengan ID ini tidak ditemukan.";
            } else if (error.response.data && error.response.data.error) {
                fetchError.value = error.response.data.error;
            } else if (error.response.data && error.response.data.message) {
                fetchError.value = error.response.data.message;
            } else if (typeof error.response.data === "string") {
                fetchError.value = error.response.data;
            } else {
                fetchError.value = `Gagal memuat data: ${error.response.status} ${error.response.statusText}`;
            }
        } else if (error.request) {
            fetchError.value =
                "Tidak ada respons dari server saat memuat data.";
        } else {
            fetchError.value = `Terjadi kesalahan saat mengambil data: ${error.message}`;
        }

        pasien.value = null;
        hasilMCU.value = [];
        hasilMCUEdit.value = [];
        saran.value = "";
    } finally {
        loading.value = false;
    }
}

onMounted(() => {
    const mcuResultId = route.params.id; 
    if (mcuResultId) {
        fetchMcuDetails(mcuResultId); 
    } else {
        fetchError.value = "ID Hasil MCU tidak ditemukan di URL.";
        loading.value = false;
    }
});

function cancel() {
    router.back(); 
}

async function save() {
   
    const mcuPatientId = route.params.id;
    if (!mcuPatientId) {
        saveErrorMessage.value =
            "Tidak dapat menyimpan: ID Hasil MCU tidak ditemukan.";
        showErrorPopup.value = true;
        return;
    }

    showSuccessPopup.value = false; 
    showErrorPopup.value = false; 
    saveErrorMessage.value = ""; 

    try {
       
        const dataToSave = {
           
            individual_results: hasilMCUEdit.value.map((res) => ({
                id: res.id,
                result: res.result,
            })),
            saran: saran.value,
        };

        const response = await axios.put(
            `/api/mcu-patients/${mcuPatientId}`,
            dataToSave
        );

        console.log("Save successful:", response.data);
        showSuccessPopup.value = true;
      
    } catch (error) {
        console.error("Save failed:", error);
        saveErrorMessage.value = "Gagal menyimpan perubahan."; 

        if (error.response) {
            if (error.response.data && error.response.data.message) {
                saveErrorMessage.value = error.response.data.message;
            } else if (error.response.data && error.response.data.error) {
                saveErrorMessage.value = error.response.data.error;
            } else if (typeof error.response.data === "string") {
                saveErrorMessage.value = error.response.data;
            } else if (error.response.status === 404) {
                saveErrorMessage.value =
                    "Hasil MCU atau data terkait tidak ditemukan di server.";
            } else {
                saveErrorMessage.value = `Gagal menyimpan: ${error.response.status} ${error.response.statusText}`;
            }
        } else if (error.request) {
            saveErrorMessage.value =
                "Tidak ada respons dari server saat menyimpan.";
        } else {
            saveErrorMessage.value = `Terjadi kesalahan saat menyimpan: ${error.message}`;
        }

        showErrorPopup.value = true; 
    } 
}

function goToPasien() {
    router.push("/hasilmcu");
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
