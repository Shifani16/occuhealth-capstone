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
                    <h1 class="text-[48px] font-bold mb-6 ml-7 container-nunito">
                        Data Medical Check Up
                    </h1>

                    <div class="ml-7 mb-10 flex justify-between items-center max-w-7xl">
                        <div class="container-open-sans bg-white text-white text-sm rounded-full px-4 py-1 text-[16px] font-bold whitespace-nowrap">
                        </div>
                        <!-- Add v-if to hide for admin -->
                        <button
                           v-if="loggedInUser.role !== 'admin'"
                           @click="showUploadModal = true"
                           @mouseover="hoveringTambah = true"
                           @mouseleave="hoveringTambah = false"
                           class="container-open-sans border border-[#3393AD] text-[#3393AD] hover:bg-[#3393AD] hover:text-white font-semibold px-4 py-2 rounded flex items-center gap-2 transition"
                        >
                            <img :src="hoveringTambah ? TambahMCUHover : TambahMCU" class="h-5" />
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
                            <img src="@/assets/arrow-down.svg" class="absolute right-16 top-1/2 -translate-y-1/2 h-3 pointer-events-none" />
                        </div>

                        <div class="relative">
                            <span
                                class="absolute inset-y-0 left-3 flex items-center"
                            >
                                <img src="@/assets/search.svg" class="w-4 h-4" />
                            </span>
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
                    </div>

                    <div class="mt-6 flex items-center justify-center gap-3">
                        <span
                            class="text-[#299BB8] ml-1 cursor-pointer"
                            :class="{
                                'opacity-50 cursor-not-allowed': currentPage === 1,
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
                                <div class="overflow-y-auto max-h-[calc(100vh-350px)] border rounded-md">
                                    <table class="table-auto text-left border-collapse w-full">
                                        <thead class="bg-white">
                                            <tr class="container-open-sans font-bold">
                                                <th class="px-4 py-2 sticky top-0 z-10">
                                                    No
                                                </th>
                                                <th class="px-4 py-2 sticky top-0 z-10">
                                                    <div class="flex items-center gap-2">
                                                        Nama Pasien
                                                        <img
                                                            src="@/assets/sorting.svg"
                                                            class="h-4 cursor-pointer"
                                                        />
                                                    </div>
                                                </th>
                                                <th class="px-4 py-2 sticky top-0 z-10">
                                                    Nomor Pasien
                                                </th>
                                                <th class="px-4 py-2 sticky top-0 z-10">
                                                    <div class="flex items-center gap-2">
                                                        Tanggal Pemeriksaan
                                                        <img
                                                            src="@/assets/sorting.svg"
                                                            class="h-4 cursor-pointer"
                                                        />
                                                    </div>
                                                </th>
                                                <th class="text-center px-4 py-2 sticky top-0 z-10">
                                                    <div class="flex items-center gap-2">
                                                        Status Hasil
                                                        <img
                                                            src="@/assets/sorting.svg"
                                                            class="h-4 cursor-pointer"
                                                        />
                                                    </div>
                                                </th>
                                                <th class="px-4 py-2" v-if="loggedInUser.role !== 'admin'">Aksi</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <tr
                                                v-for="(item, index) in filteredAndPaginatedHasil"
                                                :key="item.no_pasien"
                                                class="container-open-sans odd:bg-[#E6F6F9]"
                                            >
                                                <td class="px-6 py-4">
                                                    {{ index + 1 + (currentPage - 1) * entriesToShow }}
                                                </td>
                                                <td class="px-6 py-4">
                                                    {{ item.nama }}
                                                </td>
                                                <td class="px-6 py-4">
                                                    {{ item.no_pasien }}
                                                </td>
                                                <td class="px-6 py-4">
                                                    {{ item.tanggal_pemeriksaan }}
                                                </td>

                                                <td class="px-6 py-4">
                                                    <div class="relative inline-block" :ref="(el) => { if (el) dropdownRefs[item.id] = el; else delete dropdownRefs[item.id] }">
                                                        <button
                                                            @click.stop="toggleDropdown(item.id)"
                                                            class="flex items-center justify-between gap-2 px-3 py-1 rounded-full font-semibold text-sm w-36 whitespace-nowrap overflow-hidden text-ellipsis"
                                                            :class="{
                                                                'bg-[#EBF9F1] text-[#1F9254]': item.status === 'Completed',
                                                                'bg-[#FEF2E5] text-[#CD6200]': item.status === 'Process',
                                                                'bg-[#FBE7E8] text-[#A30D11]': item.status === 'Canceled'
                                                            }"
                                                            :disabled="item.isUpdatingStatus"
                                                        >
                                                            <div class="flex items-center gap-1">
                                                                <!-- Icon Status (w-4 h-4) -->
                                                                <!-- <img
                                                                    :src="getStatusIcon(item.status)"
                                                                    class="w-2 h-2"
                                                                    alt="Status Icons"
                                                                /> -->
                                                                <span>{{ item.status }}</span>
                                                            </div>
                                                            <img
                                                                v-if="!item.isUpdatingStatus && loggedInUser.role !== 'admin'"
                                                                :src="getStatusIcon(item.status)"
                                                                class="w-5 h-5 -ml-3 transform transition-transform"
                                                                :class="{'rotate-180': openDropdownId === item.id}"
                                                                alt="Dropdown arrow"
                                                            />
                                                            <div v-else-if="item.isUpdatingStatus" class="spinner"></div>
                                                        </button>

                                                        <div
                                                            v-if="openDropdownId === item.id && loggedInUser.role !== 'admin'"
                                                            class="absolute z-30 mt-1 w-36 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                                                            role="menu" aria-orientation="vertical" aria-labelledby="menu-button"
                                                        >
                                                            <div class="py-1" role="none">
                                                                <button
                                                                    v-for="option in statusOptions"
                                                                    :key="option"
                                                                    @click.stop="selectStatus(item, option)"
                                                                    class="block w-full text-left px-4 py-2 text-sm"
                                                                    :class="{'bg-gray-100': item.status === option, 'text-gray-700 hover:bg-gray-100 hover:text-gray-900': item.status !== option}"
                                                                    role="menuitem"
                                                                >
                                                                    {{ option }}
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td class="px-6 py-4 flex gap-2">
                                                    <button
                                                        v-if="loggedInUser.role !== 'admin'"
                                                        @click="goToDetail(item)"
                                                        class="cursor-pointer w-6 h-6"
                                                    >
                                                        <img src="@/assets/action-info.svg" title="Lihat Detail Hasil MCU" class="object-contain"/>
                                                    </button>

                                                    <button
                                                        v-if="loggedInUser.role !== 'admin'"
                                                        @click="goToEdit(item)"
                                                        class="cursor-pointer w-6 h-6"
                                                    >
                                                        <img src="@/assets/action-edit.svg" title="Edit Hasil MCU" class="object-contain"/>
                                                    </button>

                                                    <button
                                                        v-if="loggedInUser.role !== 'admin'"
                                                        @click="selectHasilToDelete(item)"
                                                        class="cursor-pointer w-6 h-6 object-contain"
                                                    >
                                                        <img src="@/assets/action-delete.svg" title="Hapus Hasil MCU"/>
                                                    </button>
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
    </div>

     <UploadMCU
        v-if="showUploadModal && loggedInUser.role !== 'admin'"
        @close="showUploadModal = false"
        @uploadSuccess="handleUploadSuccess"
        @uploadError="() => { showUploadModal = false; showErrorPopup = true; }"
     />

    <div
        v-if="showDeleteConfirm"
        class="fixed inset-0 bg-[rgba(0,0,0,0.3)] flex items-center justify-center z-50"
    >
        <div
            class="bg-[#27394B] p-6 rounded-2xl shadow-md w-96 text-center"
        >
            <img
                src="@/assets/circle-cancel.svg"
                class="w-20 mx-auto mb-4"
            />
            <p class="text-white font-medium mb-4">
                Apakah Anda yakin ingin menghapus data hasil MCU ini?
            </p>
            <div class="flex justify-center gap-4">
                <button
                    @click="showDeleteConfirm = false"
                    class="border border-white text-white px-4 py-2 rounded hover:bg-white hover:text-[#27394B] font-semibold"
                >
                    Batal
                </button>
                <button
                    @click="confirmDeleteHasil"
                    class="border border-white text-white px-4 py-2 rounded hover:bg-white hover:text-[#27394B] font-semibold"
                >
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>

    <!-- Pop Up Sukses (Upload/Delete) - Keep visible -->
    <div v-if="showSuccessPopup" class="fixed inset-0 bg-[rgba(0,0,0,0.3)] flex items-center justify-center z-50">
        <div class="bg-[#27394B] p-6 rounded-2xl shadow-md w-96 text-center">
            <img src="@/assets/circle-check.svg" class="w-20 mx-auto mb-4"/>
            <p class="text-white font-medium mb-4">{{ successMessage }}</p>
            <div class="flex justify-center">
                <button @click="showSuccessPopup = false" class="border border-white text-white px-4 py-2 rounded hover:bg-white hover:text-[#27394B] font-semibold">OK</button>
            </div>
        </div>
     </div>

    <!-- Pop Up Error (Upload/Delete) - Keep visible -->
     <div v-if="showErrorPopup" class="fixed inset-0 bg-[rgba(0,0,0,0.3)] flex items-center justify-center z-50">
        <div class="bg-[#27394B] p-6 rounded-2xl shadow-md w-96 text-center">
            <img src="@/assets/cloud-x.svg" class="w-20 mx-auto mb-4"/>
            <p class="text-white font-medium mb-4">{{ errorMessage }}</p>
            <div class="flex justify-center">
                <button @click="showErrorPopup = false" class="border border-white text-white px-4 py-2 rounded hover:bg-white hover:text-[#27394B] font-semibold">Tutup</button>
            </div>
        </div>
     </div>
</template>

<script setup>
import { ref, computed, onMounted, watch, reactive, onBeforeUnmount } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

import LeftBar from "../../../composables/LeftBar.vue";
import UploadMCU from "../../../composables/UploadMCU.vue";
import TambahMCU from "@/assets/tambah-hasil-mcu.svg";
import TambahMCUHover from "@/assets/tambah-hasil-mcu-hover.svg";

const loggedInUser = ref({ role: null });

const collapsed = ref(false);
const hovering = ref(false);

const showUploadModal = ref(false);
const showSuccessPopup = ref(false);
const showErrorPopup = ref(false);
const successMessage = ref("");
const errorMessage = ref("");

const searchQuery = ref("");
const entriesToShow = ref(10);
const currentPage = ref(1);

const hasilList = ref([]);
const loading = ref(false);

const router = useRouter();

const hoveringTambah = ref(false);
const statusOptions = ['Completed', 'Process', 'Canceled'];
const openDropdownId = ref(null);
const dropdownRefs = reactive({});

const showDeleteConfirm = ref(false);
const hasilToDelete = ref(null);

function toggleDropdown(itemId) {
    if (loggedInUser.value.role !== 'admin') {
        if (openDropdownId.value === itemId) {
            openDropdownId.value = null;
        } else {
            openDropdownId.value = itemId;
        }
    }
}

async function selectStatus(item, newStatus) {
     if (loggedInUser.value.role === 'admin') {
         console.warn("Admin users cannot change status.");
         openDropdownId.value = null; 
         return;
     }


    if (item.status === newStatus) {
        openDropdownId.value = null;
        return;
    }

    const originalStatus = item.status;
    item.status = newStatus;
    openDropdownId.value = null;
    if (!item.hasOwnProperty('isUpdatingStatus')) {
        item = reactive(item);
    }
    item.isUpdatingStatus = true;


    try {
        const response = await axios.put(`/api/mcu-patients/${item.id}/status`, { status: newStatus });
        console.log(`Status updated successfully for item ${item.id} to ${newStatus}`, response.data);

        successMessage.value = `Status berhasil diubah menjadi ${newStatus}`;
        showSuccessPopup.value = true;

    } catch (error) {
        console.error(`Error updating status for item ${item.id} to ${newStatus}:`, error);

        item.status = originalStatus;
        errorMessage.value = `Gagal mengubah status menjadi ${newStatus}: ${error.message || (error.response?.data?.message || 'Server Error')}`;
        showErrorPopup.value = true;
    } finally {
        item.isUpdatingStatus = false;
    }
}

function getStatusIcon(status) {
    switch (status) {
        case 'Completed':
            return new URL('@/assets/arrow-down-green.svg', import.meta.url).href;
        case 'Process':
            return new URL('@/assets/arrow-down-orange.svg', import.meta.url).href;
        case 'Canceled':
            return new URL('@/assets/arrow-down-red.svg', import.meta.url).href;
        default:
             return new URL('@/assets/arrow-down-grey.svg', import.meta.url).href;
    }
}

async function fetchMcuData() {
    loading.value = true;
    errorMessage.value = "";
    try {
        const response = await axios.get("/api/mcu-patients");

        console.log("Fetched MCU data:", response.data);

         if (response.data && Array.isArray(response.data)) {
            hasilList.value = response.data.map((item) => {
                const examinationDate = item.examination_date
                    ? new Date(item.examination_date).toLocaleDateString("id-ID", {
                        year: "numeric",
                        month: "2-digit",
                        day: "2-digit",
                    })
                    : "N/A";

                return reactive({
                    id: item.id,
                    nama: item.patient ? item.patient.name : item.name || "N/A",
                    tanggal_pemeriksaan: examinationDate,
                    no_pasien: item.patient ? item.patient.patient_id : "N/A",
                    status: item.status || "Process", 
                    isUpdatingStatus: false,
                });
            });
         } else {
             console.error(
                "API response format for /api/mcu-patients is not as expected:",
                response.data
            );
            hasilList.value = [];
            errorMessage.value = "Format data hasil MCU dari server tidak sesuai.";

             if (
                response.data &&
                (response.data.message || response.data.error)
            ) {
                errorMessage.value =
                    response.data.message || response.data.error;
            }
            showErrorPopup.value = true;
         }

    } catch (error) {
        console.error("Error fetching MCU data:", error);
        errorMessage.value = "Gagal mengambil data hasil MCU.";
         if (error.response && error.response.data) {
            if (error.response.data.message) {
                errorMessage.value = error.response.data.message;
            } else if (error.response.data.error) {
                errorMessage.value = error.response.data.error;
            } else if (typeof error.response.data === "string") {
                errorMessage.value = error.response.data;
            }
        } else if (error.message) {
             errorMessage.value = `Gagal mengambil data: ${error.message}`;
        }
        showErrorPopup.value = true;
    } finally {
        loading.value = false;
    }
}

function handleClickOutside(event) {
  if (loggedInUser.value.role !== 'admin') {
        if (showDeleteConfirm.value || showUploadModal.value || showSuccessPopup.value || showErrorPopup.value) {
            return;
        }

        if (openDropdownId.value && dropdownRefs[openDropdownId.value]) {
            const dropdownElement = dropdownRefs[openDropdownId.value];
            if (!dropdownElement.contains(event.target)) {
                openDropdownId.value = null;
            }
        }
  } else {
        if (showDeleteConfirm.value || showUploadModal.value || showSuccessPopup.value || showErrorPopup.value) {
             return;
        }
         openDropdownId.value = null;
  }
}


onMounted(() => {
    // Load user role from localStorage first
    const storedUser = localStorage.getItem('user');
    if (storedUser) {
        try {
            const user = JSON.parse(storedUser);
            // Ensure role exists, default to null
            loggedInUser.value = { ...user, role: user.role || null };
        } catch (e) {
            console.error("Failed to parse user from localStorage in HasilMCU:", e);
            loggedInUser.value = { role: null }; // Reset on error
        }
    } else {
        loggedInUser.value = { role: null }; // Set default if no user in storage
    }

    // Then fetch data and set up event listener
    fetchMcuData();
    document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
});

const filteredHasilListRaw = computed(() => {
    const query = searchQuery.value ? searchQuery.value.toLowerCase() : "";
    if (!query) {
        return hasilList.value;
    }
    return hasilList.value.filter(
        (item) =>
            item.nama?.toLowerCase().includes(query) ||
            item.no_pasien?.toLowerCase().includes(query) ||
            item.tanggal_pemeriksaan?.toLowerCase().includes(query) ||
            item.status?.toLowerCase().includes(query)
    );
});

const totalPages = computed(() => {
    if (filteredHasilListRaw.value.length === 0) {
        return 1;
    }
    return Math.ceil(filteredHasilListRaw.value.length / entriesToShow.value);
});


watch([entriesToShow, searchQuery], () => {
    currentPage.value = 1;
});

watch(totalPages, (newTotal) => {
    if (currentPage.value > newTotal) {
        currentPage.value = newTotal > 0 ? newTotal : 1;
    }
});

const filteredAndPaginatedHasil = computed(() => {
    const safeCurrentPage = Math.max(
        1,
        Math.min(currentPage.value, totalPages.value)
    );

    const start = (safeCurrentPage - 1) * entriesToShow.value;
    const end = start + entriesToShow.value;

    return filteredHasilListRaw.value.slice(start, end);
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
    } else if (totalPages.value === 0) {
        currentPage.value = 1;
    }
}

function selectHasilToDelete(item) {
     // Only allow deletion if user is NOT admin
     if (loggedInUser.value.role === 'admin') {
         console.warn("Admin users cannot delete data.");
         // Optionally show a message
         // errorMessage.value = "Anda tidak memiliki izin untuk menghapus data.";
         // showErrorPopup.value = true;
         return;
     }

    console.log("selectHasilToDelete triggered for:", item);
    hasilToDelete.value = item;
    showDeleteConfirm.value = true;

    showSuccessPopup.value = false;
    showErrorPopup.value = false;
    openDropdownId.value = null;
}

async function confirmDeleteHasil() {
    if (!hasilToDelete.value || !hasilToDelete.value.id) {
        console.error(
            "No MCU result selected for deletion or ID missing.",
            hasilToDelete.value
        );
        errorMessage.value =
            "Terjadi kesalahan: Data hasil MCU tidak ditemukan atau ID hilang.";
        showErrorPopup.value = true;
        showDeleteConfirm.value = false;
        hasilToDelete.value = null;
        return;
    }

    showDeleteConfirm.value = false;
    loading.value = true;
    errorMessage.value = "";

    try {
        const response = await axios.delete(
            `/api/mcu-patients/${hasilToDelete.value.id}`
        );
        console.log("Delete successful:", response.data);

        successMessage.value = "Data hasil MCU berhasil dihapus";
        showSuccessPopup.value = true;

        hasilList.value = hasilList.value.filter(
            (item) => item.id !== hasilToDelete.value.id
        );

        hasilToDelete.value = null;

        const currentFilteredCount = hasilList.value.filter(
            (item) =>
                item.nama?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                item.no_pasien?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                item.tanggal_pemeriksaan?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                item.status?.toLowerCase().includes(searchQuery.value.toLowerCase())
        ).length;

        const newTotalPages = Math.ceil(currentFilteredCount / entriesToShow.value);

        if (currentPage.value > newTotalPages && newTotalPages > 0) {
             currentPage.value = newTotalPages;
        } else if (newTotalPages === 0) {
             currentPage.value = 1;
        }

    } catch (error) {
        console.error("Error deleting MCU result:", error);
        errorMessage.value = "Gagal menghapus data hasil MCU.";
        if (error.response && error.response.data) {
            if (error.response.data.message) {
                errorMessage.value = error.response.data.message;
            } else if (error.response.data.error) {
                errorMessage.value = error.response.data.error;
            } else if (typeof error.response.data === "string") {
                errorMessage.value = error.response.data;
            } else {
                 errorMessage.value = JSON.stringify(error.response.data);
            }
        } else if (error.message) {
            errorMessage.value = `Gagal menghapus data: ${error.message}`;
        }
        showErrorPopup.value = true;
    } finally {
        loading.value = false;
        hasilToDelete.value = null;
    }
}


// --- Modal and Popup Handlers (Upload) ---
function handleUploadSuccess(response) {
    // Only process if user is NOT admin (shouldn't be triggered by admin anyway due to v-if)
    if (loggedInUser.value.role !== 'admin') {
        showUploadModal.value = false;
        successMessage.value = response.message || "File Anda berhasil ditambahkan";
        showSuccessPopup.value = true;

        fetchMcuData(); // Refresh data
    }
}

function handleUploadError(error) {
    // Only process if user is NOT admin
     if (loggedInUser.value.role !== 'admin') {
        console.error("Upload failed:", error);
        showUploadModal.value = false;

        let message = "Terjadi kesalahan saat mengunggah file.";
        if (error.response && error.response.data) {
            if (error.response.data.message) {
                message = error.response.data.message;
            } else if (error.response.data.error) {
                message = error.response.data.error;
                if (
                    error.response.data.messages &&
                    Array.isArray(error.response.data.messages)
                ) {
                    message += ": " + error.response.data.messages.join(", ");
                }
            } else if (
                error.response.data.messages &&
                Array.isArray(error.response.data.messages)
            ) {
                message = error.response.data.messages.join(", ");
            } else if (typeof error.response.data === "string") {
                message = error.response.data;
            }
        } else if (error.message) {
            message = `Upload gagal: ${error.message}`;
        }

        errorMessage.value = message;
        showErrorPopup.value = true;
     }
}

// --- Navigation ---
function goToDetail(item) {
    console.log("Navigate to detail for:", item);
    if (item && item.id) {
        router.push({
            name: "HasilMCUDetail",
            params: { id: item.id },
        });
    } else {
        console.error(
            "Cannot navigate to detail: Item or item ID is missing",
            item
        );
        errorMessage.value = "Tidak dapat melihat detail: ID hasil MCU hilang.";
        showErrorPopup.value = true;
    }
}

function goToEdit(item) {
     // Only allow edit if user is NOT admin
     if (loggedInUser.value.role === 'admin') {
         console.warn("Admin users cannot edit data.");
         // Optionally show a message
         // errorMessage.value = "Anda tidak memiliki izin untuk mengedit data.";
         // showErrorPopup.value = true;
         return;
     }

    console.log("Navigate to edit for:", item);
    if (item && item.id) {
        router.push({
            name: "HasilMCUEdit",
            params: { id: item.id },
            state: { patientData: item } // Note: State might not persist on refresh
        });
    } else {
        console.error(
            "Cannot navigate to edit: Item or item ID is missing",
            item
        );
        errorMessage.value = "Tidak dapat mengedit: ID hasil MCU hilang.";
        showErrorPopup.value = true;
    }
}

</script>

<style>
/* Styles Tetap Sama */
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

table,
th,
td {
    border: 1px solid #e0e0e0;
}

th {
    background-color: #FFF;
}

.spinner {
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-top: 2px solid #fff;
    border-radius: 50%;
    width: 1em;
    height: 1em;
    animation: spin 1s linear infinite;
    margin-left: 0.5em;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>