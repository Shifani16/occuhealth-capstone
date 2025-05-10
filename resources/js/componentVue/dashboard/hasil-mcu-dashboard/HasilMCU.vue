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
                <div class="flex-1 p-6">
                    <h1
                        class="text-[48px] font-bold mb-6 ml-7 container-nunito"
                    >
                        Data Medical Check Up
                    </h1>

                    <div
                        class="ml-7 mb-10 flex justify-between items-center max-w-7xl"
                    >
                        <!-- <h2 class="container-nunito text-[32px] font-bold mb-1"></h2> -->
                        <div
                            class="container-open-sans bg-white text-white text-sm rounded-full px-4 py-1 text-[16px] font-bold whitespace-nowrap"
                        >
                            <!-- This div seems to be a placeholder, leaving it as is -->
                        </div>
                        <button
                            @click="showUploadModal = true"
                            @mouseover="hoveringTambah = true"
                            @mouseleave="hoveringTambah = false"
                            class="container-open-sans border border-[#3393AD] text-[#3393AD] hover:bg-[#3393AD] hover:text-white font-semibold px-4 py-2 rounded flex items-center gap-2 transition"
                        >
                            <img
                                :src="
                                    hoveringTambah ? TambahMCUHover : TambahMCU
                                "
                                class="h-5"
                            />
                            Tambah Hasil MCU
                        </button>
                    </div>

                    <div
                        class="container-open-sans ml-7 flex flex-wrap items-center gap-4"
                    >
                        <div class="relative inline-flex items-center">
                            <label class="text-[14px] font-semibold mr-2"
                                >Show</label
                            >
                            <select
                                v-model="entriesToShow"
                                class="appearance-none bg-[#C9EBF3] border-[#C9EBF3] text-black px-2 py-2 rounded hover:bg-white border hover:border-[#299BB8] pr-6"
                            >
                                <option :value="5">5</option>
                                <option :value="10">10</option>
                                <option :value="15">15</option>
                                <option :value="20">20</option>
                                <option :value="30">30</option>
                                <option :value="40">40</option>
                                <option :value="50">50</option>
                                <option :value="100">100</option>
                            </select>
                            <span class="ml-2 text-[14px] font-semibold"
                                >entries</span
                            >
                            <img
                                src="@/assets/arrow-down.svg"
                                class="absolute right-3 top-1/2 -translate-y-1/2 h-3 pointer-events-none"
                            />
                        </div>

                        <div class="relative flex items-center">
                            <img
                                src="@/assets/search.svg"
                                class="absolute left-3 w-4 h-4"
                            />
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Cari..."
                                class="pl-10 border border-[#299BB8] px-4 py-2 rounded bg-white md:w-100 lg:w-220 placeholder-[#299BB8] text-[#299BB8]"
                                style="
                                    background-image: url('@/assets/search.svg');
                                    background-repeat: no-repeat;
                                    background-position: right 0.5rem center;
                                "
                            />
                        </div>
                        <div v-if="loading" class="text-[#3393AD] ml-4">
                            Loading...
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-center gap-3">
                        <span
                            class="text-[#299BB8] ml-1 cursor-pointer"
                            :class="{
                                'opacity-50 cursor-not-allowed':
                                    currentPage === 1,
                            }"
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
                                'bg-[#8AD3E5] text-white': currentPage === page,
                                'bg-[#F2FAFC] text-black': currentPage !== page,
                            }"
                        >
                            {{ page }}
                        </span>

                        <span
                            class="text-[#299BB8] ml-1 cursor-pointer"
                            :class="{
                                'opacity-50 cursor-not-allowed':
                                    currentPage === totalPages,
                            }"
                            @click="nextPage"
                        >
                            Selanjutnya
                        </span>
                    </div>

                    <div class="w-full bg-white p-6 rounded-md shadow">
                        <div class="overflow-x-auto">
                            <div class="min-w-full">
                                <div
                                    class="overflow-y-auto max-h-[calc(100vh-350px)] border rounded-md"
                                >
                                    <table
                                        class="table-auto text-left border-collapse w-full"
                                    >
                                        <thead class="bg-white">
                                            <tr
                                                class="container-open-sans font-bold"
                                            >
                                                <th
                                                    class="px-4 py-2 sticky top-0 z-10"
                                                >
                                                    No
                                                </th>
                                                <th
                                                    class="px-4 py-2 sticky top-0 z-10"
                                                >
                                                    <div
                                                        class="flex items-center gap-2"
                                                    >
                                                        Nama Pasien
                                                        <img
                                                            src="@/assets/sorting.svg"
                                                            class="h-4 cursor-pointer"
                                                        />
                                                    </div>
                                                </th>
                                                <th
                                                    class="px-4 py-2 sticky top-0 z-10"
                                                >
                                                    Nomor Pasien
                                                </th>
                                                <th
                                                    class="px-4 py-2 sticky top-0 z-10"
                                                >
                                                    <div
                                                        class="flex items-center gap-2"
                                                    >
                                                        Tanggal Pemeriksaan
                                                        <img
                                                            src="@/assets/sorting.svg"
                                                            class="h-4 cursor-pointer"
                                                        />
                                                    </div>
                                                </th>
                                                <th
                                                    class="px-4 py-2 sticky top-0 z-10"
                                                >
                                                    <div
                                                        class="flex items-center gap-2"
                                                    >
                                                        Status Hasil
                                                        <img
                                                            src="@/assets/sorting.svg"
                                                            class="h-4 cursor-pointer"
                                                        />
                                                    </div>
                                                </th>
                                                <th
                                                    class="px-4 py-2 sticky top-0 z-10"
                                                >
                                                    Aksi
                                                </th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <!-- Use filteredAndPaginatedHasil for display -->
                                            <tr
                                                v-for="(
                                                    item, index
                                                ) in filteredAndPaginatedHasil"
                                                :key="item.id"
                                                <!--
                                                Use
                                                unique
                                                backend
                                                ID
                                                --
                                            >
                                                class="container-open-sans
                                                odd:bg-[#E6F6F9]" >
                                                <td class="px-6 py-4">
                                                    {{
                                                        (currentPage - 1) *
                                                            entriesToShow +
                                                        index +
                                                        1
                                                    }}
                                                    <!-- Correct index for pagination -->
                                                </td>
                                                <td class="px-6 py-4">
                                                    {{ item.nama }}
                                                </td>
                                                <td class="px-6 py-4">
                                                    {{ item.no_pasien }}
                                                </td>
                                                <td class="px-6 py-4">
                                                    {{
                                                        item.tanggal_pemeriksaan
                                                    }}
                                                </td>
                                                <td class="px-6 py-4">
                                                    {{ item.status }}
                                                </td>
                                                <td
                                                    class="px-6 py-4 flex gap-2"
                                                >
                                                    <!-- Pass the entire item or just the ID for detail/edit -->
                                                    <button
                                                        @click="
                                                            goToDetail(item)
                                                        "
                                                        class="cursor-pointer w-6 h-6"
                                                    >
                                                        <img
                                                            src="@/assets/action-info.svg"
                                                            title="Lihat Detail Hasil MCU"
                                                            class="object-contain"
                                                        />
                                                    </button>

                                                    <button
                                                        @click="goToEdit(item)"
                                                        class="cursor-pointer w-6 h-6"
                                                    >
                                                        <img
                                                            src="@/assets/action-edit.svg"
                                                            title="Edit Hasil MCU"
                                                            class="object-contain"
                                                        />
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr
                                                v-if="
                                                    filteredAndPaginatedHasil.length ===
                                                        0 && !loading
                                                "
                                            >
                                                <td
                                                    colspan="6"
                                                    class="px-6 py-4 text-center text-gray-500"
                                                >
                                                    Tidak ada data hasil MCU
                                                    ditemukan.
                                                </td>
                                            </tr>
                                            <tr v-if="loading">
                                                <td
                                                    colspan="6"
                                                    class="px-6 py-4 text-center text-gray-500"
                                                >
                                                    Memuat data...
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="my-4 ml-7" />
                <p
                    class="ml-7 container-open-sans text-left text-sm text-black font-bold text-[20px] mb-2"
                >
                    © 2025 OccuHelp. All Rights Reserved.
                </p>
            </main>
        </div>

        <!-- Upload Pop Up Modal -->
        <UploadMCU
            v-if="showUploadModal"
            @close="showUploadModal = false"
            @uploadSuccess="handleUploadSuccess"
            @uploadError="handleUploadError"
        />

        <!-- Success Popup -->
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
                <p class="text-white font-medium mb-4">{{ successMessage }}</p>
                <!-- Use dynamic message -->
                <div class="flex justify-center">
                    <button
                        @click="showSuccessPopup = false"
                        class="border border-white text-white px-4 py-2 rounded hover:bg-white hover:text-[#27394B] font-semibold"
                    >
                        OK
                    </button>
                </div>
            </div>
        </div>

        <!-- Error Popup -->
        <div
            v-if="showErrorPopup"
            class="fixed inset-0 bg-[rgba(0,0,0,0.3)] flex items-center justify-center z-50"
        >
            <div
                class="bg-[#27394B] p-6 rounded-2xl shadow-md w-96 text-center"
            >
                <img src="@/assets/cloud-x.svg" class="w-20 mx-auto mb-4" />
                <!-- Use dynamic error message from uploadError event -->
                <p class="text-white font-medium mb-4">{{ errorMessage }}</p>
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
import { ref, computed, onMounted } from "vue"; 
import { useRouter } from "vue-router";
import axios from "axios";

import LeftBar from "../../../composables/LeftBar.vue";
import UploadMCU from "../../../composables/UploadMCU.vue";
// import Actioninfo from "@/assets/action-info.svg"; // Remove if not directly used
// import Actionedit from "@/assets/action-edit.svg"; // Remove if not directly used
// import circleCancel from "@/assets/circle-cancel.svg"; // Remove if not directly used
// import circleCheck from "@/assets/circle-check.svg"; // Remove if not directly used
import TambahMCU from "@/assets/tambah-hasil-mcu.svg";
import TambahMCUHover from "@/assets/tambah-hasil-mcu-hover.svg";

// --- Refs and State ---
const collapsed = ref(false); 
const hovering = ref(false); 

const showUploadModal = ref(false);
const showSuccessPopup = ref(false);
const showErrorPopup = ref(false);
const successMessage = ref("File Anda berhasil ditambahkan"); 
const errorMessage = ref(
    "File tidak valid. Coba lagi dengan format yang sesuai."
); 

const searchQuery = ref("");
const entriesToShow = ref(10);
const currentPage = ref(1);

const hasilList = ref([]);
const loading = ref(false); 

const router = useRouter();

const hoveringTambah = ref(false);

// --- Data Fetching ---
async function fetchMcuData() {
    loading.value = true;
    try {
        const response = await axios.get("https://occuhelp-capstone-production.up.railway.app/api/mcu-patients", {
        });

        console.log("Fetched MCU data:", response.data);

        hasilList.value = response.data.map((item) => {
             const examinationDate = item.examination_date
                 ? new Date(item.examination_date).toLocaleDateString('id-ID')
                 : 'N/A';

            return {
                id: item.id,
                nama: item.patient ? item.patient.name : (item.name || 'N/A'), 
                tanggal_pemeriksaan: examinationDate, 
                no_pasien: item.patient ? item.patient.patient_id : 'N/A',
                status: item.status,
            };
        });
    } catch (error) {
        console.error("Error fetching MCU data:", error);
         errorMessage.value = 'Gagal mengambil data hasil MCU.';
         showErrorPopup.value = true;
    } finally {
        loading.value = false;
    }
}

onMounted(() => {
    fetchMcuData();
});

// --- Pagination and Filtering ---
const filteredHasilListRaw = computed(() => {
    const query = searchQuery.value.toLowerCase();
    if (!query) {
        return hasilList.value;
    }
    return hasilList.value.filter(
        (item) =>
            item.nama.toLowerCase().includes(query) ||
            item.no_pasien.toLowerCase().includes(query) ||
            item.tanggal_pemeriksaan.toLowerCase().includes(query) ||
            item.status.toLowerCase().includes(query)
    );
});

const filteredAndPaginatedHasil = computed(() => {
    const start = (currentPage.value - 1) * entriesToShow.value;
    const end = start + entriesToShow.value;

    if (currentPage.value > totalPages.value && totalPages.value > 0) {
        currentPage.value = totalPages.value;
    } else if (totalPages.value === 0 && currentPage.value !== 1) {
        currentPage.value = 1;
    }

    return filteredHasilListRaw.value.slice(start, end);
});

const totalPages = computed(() => {
    return Math.ceil(filteredHasilListRaw.value.length / entriesToShow.value);
});

function nextPage() {
    if (currentPage.value < totalPages.value) {
        currentPage.value++;
    }
}

function prevPage() {
    if (currentPage.value > 1) {
        currentPage.value--;
    }
}

function goToPage(page) {
    if (page >= 1 && page <= totalPages.value) {
        currentPage.value = page;
    }
}

// --- Modal and Popup Handlers ---
function handleUploadSuccess(response) {
    showUploadModal.value = false;
    successMessage.value = response.message || "File Anda berhasil ditambahkan"; 
    showSuccessPopup.value = true; // Show success popup

    fetchMcuData();

 
}

function handleUploadError(error) {
    console.error("Upload failed:", error);
    showUploadModal.value = false;
    errorMessage.value =
        error.message ||
        error.response?.data?.message ||
        error.response?.data?.error ||
        "Terjadi kesalahan saat mengupload file.";
    showErrorPopup.value = true; // Show error popup
}

// --- Navigation ---
function goToDetail(item) {
    console.log("Navigate to detail for:", item);
    router.push({
        name: "HasilMCUDetail",
        params: { id: item.id },
    });
}

function goToEdit(item) {
    console.log("Navigate to edit for:", item);
    router.push({
        name: "HasilMCUEdit",
        params: { id: item.id }, 
    });
}

</script>

<style>
nav {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    padding: 1rem;
}

.container-nunito {
    font-family: "Nunito", sans-serif;
}

.container-open-sans {
    font-family: "Open Sans", sans-serif;
}

::-webkit-scrollbar {
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f0f0f0;
}

::-webkit-scrollbar-thumb {
    background-color: #8ad3e5;
    border-radius: 4px;
}
</style>
