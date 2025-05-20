<template>
    <!-- <div class="overflow-x-hidden"> -->
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
            <!-- Left Bar -->
            <LeftBar active="Pasien" />

            <main class="flex-1 p-6">
                <h1 class="text-[48px] font-bold mb-6 ml-7 container-nunito">
                    Pasien
                </h1>
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
                            class="absolute right-16 top-1/2 -translate-y-1/2 h-3 pointer-events-none"
                        />
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
                            <div
                                class="overflow-y-auto max-h-[calc(100vh-350px)] border rounded-md"
                            >
                                <table
                                    class="table-auto text-left border-collapse w-full"
                                >
                                    <thead>
                                        <tr
                                            class="container-open-sans font-bold sticky top-0 z-10"
                                        >
                                            <th class="px-4 py-2">
                                                No.
                                            </th>
                                            <th class="px-4 py-2">
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
                                                class="px-4 py-2 flex items-center gap-2"
                                            >
                                                Tanggal Pemeriksaan
                                                <img
                                                    src="@/assets/sorting.svg"
                                                    class="h-4 cursor-pointer"
                                                />
                                            </th>
                                            <th class="px-4 py-2">
                                                No. Rekam Medis
                                            </th>
                                            <th class="px-4 py-2">
                                                No. Pasien
                                            </th>
                                            <th class="px-4 py-2">
                                                Jenis Kelamin
                                            </th>
                                            <th class="px-4 py-2">
                                                <div
                                                    class="flex items-center gap-2"
                                                >
                                                    Umur
                                                    <img
                                                        src="@/assets/sorting.svg"
                                                        class="h-4 cursor-pointer"
                                                    />
                                                </div>
                                            </th>
                                            <th class="px-4 py-2">TTL</th>
                                            <th class="px-4 py-2" v-if="loggedInUser.role !== 'admin'">Aksi</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <tr
                                            v-for="(
                                                item, index
                                            ) in filteredAndPaginatedPasien"
                                            :key="item.id || item.no_pasien"
                                            class="container-open-sans odd:bg-[#E6F6F9]"
                                        >
                                            <td class="px-4 py-2">
                                                {{ index + 1 }}
                                            </td>
                                            <td class="px-4 py-2">
                                                {{ item.nama }}
                                            </td>
                                            <td class="px-4 py-2">
                                                {{ item.tanggal_pemeriksaan }}
                                            </td>
                                            <td class="px-4 py-2">
                                                {{ item.no_rekam_medis }}
                                            </td>
                                            <td class="px-4 py-2">
                                                {{ item.no_pasien }}
                                            </td>
                                            <td class="px-4 py-2">
                                                {{ item.jenis_kelamin }}
                                            </td>
                                            <td class="px-4 py-2">
                                                {{ item.umur }}
                                            </td>
                                            <td class="px-4 py-2">
                                                {{ item.tempat_tanggal_lahir }}
                                            </td>
                                            <td class="px-4 py-2 flex">
                                                <button
                                                    v-if="loggedInUser.role !== 'admin'"
                                                    @click="goToDetail(item)"
                                                    class="cursor-pointer w-10 h-10"
                                                >
                                                    <img
                                                        src="@/assets/action-info.svg"
                                                        title="Lihat Info Pasien"
                                                        class="object-contain"
                                                    />
                                                </button>
                                                <!-- Edit Button (Hide for Admin) -->
                                                <button
                                                    v-if="loggedInUser.role !== 'admin'"
                                                    @click="goToEdit(item)"
                                                    class="cursor-pointer w-10 h-10"
                                                >
                                                    <img
                                                        src="@/assets/action-edit.svg"
                                                        title="Edit Data Pasien"
                                                        class="object-contain"
                                                    />
                                                </button>
                                                <button
                                                    v-if="loggedInUser.role !== 'admin'"
                                                    @click="
                                                        selectPasienToDelete(
                                                            item
                                                        )
                                                    "
                                                    class="cursor-pointer w-10 h-10 object-contain"
                                                >
                                                    <img
                                                        src="@/assets/action-delete.svg"
                                                        title="Hapus Data Pasien"
                                                    />
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-4" />
                <p
                    class="container-open-sans text-left text-sm text-black font-bold text-[20px] mb-2"
                >
                    © 2025 OccuHelp. All Rights Reserved.
                </p>
            </main>
        </div>

        <!-- Pop Up Delete Confirmation -->
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
                    Apakah Anda yakin ingin menghapus data pasien?
                </p>
                <div class="flex justify-center gap-4">
                    <button
                        @click="showDeleteConfirm = false"
                        class="border border-white text-white px-4 py-2 rounded hover:bg-white hover:text-[#27394B] font-semibold"
                    >
                        Batal
                    </button>
                    <button
                        @click="confirmDelete"
                        class="border border-white text-white px-4 py-2 rounded hover:bg-white hover:text-[#27394B] font-semibold"
                    >
                        Ya
                    </button>
                </div>
            </div>
        </div>

        <!-- Pop Up Success -->
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
                    Data pasien berhasil dihapus
                </p>
                <div class="flex justify-center">
                    <button
                        @click="showSuccessPopup = false"
                        class="border border-white text-white px-4 py-2 rounded hover:bg-white hover:text-[#27394B] font-semibold"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        <!-- Pop Up Error -->
        <div v-if="showErrorPopup" class="fixed inset-0 bg-[rgba(0,0,0,0.3)] flex items-center justify-center z-50">
            <div class="bg-[#27394B] p-6 rounded-2xl shadow-md w-96 text-center">
                <img src="@/assets/cloud-x.svg" class="w-20 mx-auto mb-4"/>
                <p class="text-white font-medium mb-4">{{ errorMessage }}</p>
                <div class="flex justify-center">
                    <button @click="showErrorPopup = false" class="border border-white text-white px-4 py-2 rounded hover:bg-white hover:text-[#27394B] font-semibold">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

import LeftBar from "../../../composables/LeftBar.vue";

const loggedInUser = ref({ role: null }); 

// --- Refs and State ---
const collapsed = ref(false); 
const hovering = ref(false); 

const pasienList = ref([]);
const loading = ref(false);
const errorMessage = ref("");
const successMessage = ref("");

const showDeleteConfirm = ref(false);
const showSuccessPopup = ref(false);
const showErrorPopup = ref(false);
const pasienToDelete = ref(null);
const showPopUp = ref(false);

const searchQuery = ref("");
const entriesToShow = ref(10);
const currentPage = ref(1);

const router = useRouter();

// --- Data Fetching ---
async function fetchPasienData() {
    loading.value = true;
    errorMessage.value = "";
    try {
        const response = await axios.get("/api/patients");

        console.log("Full Pasien API response:", response);
        console.log("Pasien API response data:", response.data);

        const responseData = response.data.patients;

        if (response.data && Array.isArray(responseData)) {
            pasienList.value = responseData.map((item) => {
                const examinationDate = item.examination_date
                    ? new Date(item.examination_date).toLocaleDateString(
                          "id-ID",
                          {
                              year: "numeric",
                              month: "2-digit",
                              day: "2-digit",
                          }
                      )
                    : "N/A";

                const birthDate = item.birth_date
                    ? new Date(item.birth_date).toLocaleDateString("id-ID", {
                          year: "numeric",
                          month: "long",
                          day: "numeric",
                      })
                    : ""; 
                const tempatTanggalLahir =
                    item.birth_place && birthDate 
                        ? `${item.birth_place}, ${birthDate}`
                        : item.birth_place || birthDate || "N/A"; 


                return {
                    id: item.id, 
                    nama: item.name || "N/A",
                    tanggal_pemeriksaan: examinationDate,
                    no_rekam_medis: item.med_record_id || "N/A",
                    no_pasien: item.patient_id || "N/A",
                    jenis_kelamin: item.gender || "N/A",
                    umur: item.age?.toString() || "N/A",
                    tempat_tanggal_lahir: tempatTanggalLahir,
                };
            });
             console.log("Mapped pasienList:", pasienList.value);
        } else {
            console.error(
                "API response format for /api/patients is not as expected:",
                response.data
            );
            pasienList.value = [];
            errorMessage.value = "Format data pasien dari server tidak sesuai.";

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
        console.error("Error fetching Pasien data:", error);
        errorMessage.value = "Gagal mengambil data pasien.";
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
             errorMessage.value = `Gagal mengambil data: ${error.message}`;
        }
        showErrorPopup.value = true; 
    } finally {
        loading.value = false;
    }
}

// --- Load User Role on Mount ---
onMounted(() => {
    const storedUser = localStorage.getItem('user');
    if (storedUser) {
        try {
            const user = JSON.parse(storedUser);
            loggedInUser.value = { ...user, role: user.role || null };
        } catch (e) {
            console.error("Failed to parse user from localStorage in Pasien:", e);
            loggedInUser.value = { role: null }; 
        }
    } else {
        loggedInUser.value = { role: null };
    }

    fetchPasienData();
});


// --- Pagination and Filtering ---
const searchPasienListRaw = computed(() => {
    const query = searchQuery.value ? searchQuery.value.toLowerCase() : "";
    if (!query) {
        return pasienList.value;
    }
    return pasienList.value.filter(
        (pasien) =>
            pasien.nama?.toLowerCase().includes(query) ||
            pasien.no_rekam_medis?.toLowerCase().includes(query) ||
            pasien.no_pasien?.toLowerCase().includes(query) ||
            pasien.tanggal_pemeriksaan?.toLowerCase().includes(query) ||
            pasien.jenis_kelamin?.toLowerCase().includes(query) ||
            pasien.umur?.toLowerCase().includes(query) ||
            pasien.tempat_tanggal_lahir?.toLowerCase().includes(query)
    );
});

const totalPages = computed(() => {
    return Math.max(1, Math.ceil(searchPasienListRaw.value.length / entriesToShow.value));
});

watch([entriesToShow, searchQuery], () => {
    currentPage.value = 1;
});

watch(totalPages, (newTotal) => {
    if (currentPage.value > newTotal) {
        currentPage.value = newTotal > 0 ? newTotal : 1;
    }
});


const filteredAndPaginatedPasien = computed(() => {
    const safeCurrentPage = Math.max(
        1,
        Math.min(currentPage.value, totalPages.value)
    );

    const start = (safeCurrentPage - 1) * entriesToShow.value;
    const end = start + entriesToShow.value;

    return searchPasienListRaw.value.slice(start, end);
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

// --- Delete Handlers ---
function selectPasienToDelete(pasien) {
    if (loggedInUser.value.role === 'admin') {
        console.warn("Admin users cannot delete data.");
        return;
    }

    console.log("selectPasienToDelete triggered for:", pasien);
    pasienToDelete.value = pasien;
    showDeleteConfirm.value = true;

    console.log("showDeleteConfirm set to:", showDeleteConfirm.value);
    showSuccessPopup.value = false;
    showErrorPopup.value = false;
}

function cancelDelete() {
    showDeleteConfirm.value = false;
    pasienToDelete.value = null;
}

async function confirmDelete() {
     if (loggedInUser.value.role === 'admin') {
        console.warn("Admin users cannot perform delete.");
        cancelDelete();
        return;
     }


    if (!pasienToDelete.value || !pasienToDelete.value.id) {
        console.error(
            "No patient selected for deletion or patient ID missing."
        );
        cancelDelete();
        errorMessage.value =
            "Terjadi kesalahan: Pasien tidak ditemukan atau ID hilang.";
        showErrorPopup.value = true;
        return;
    }

    showDeleteConfirm.value = false; 
    loading.value = true;
    errorMessage.value = ""; 

    try {
        const response = await axios.delete(
            `/api/patients/${pasienToDelete.value.id}`
        );
        console.log("Delete successful:", response.data);

        successMessage.value = "Data pasien berhasil dihapus";
        showSuccessPopup.value = true;

        pasienList.value = pasienList.value.filter(
            (p) => p.id !== pasienToDelete.value.id
        );

        pasienToDelete.value = null; 

        const currentFilteredCount = pasienList.value.filter(
            (pasien) =>
                pasien.nama
                    ?.toLowerCase()
                    .includes(searchQuery.value.toLowerCase()) ||
                pasien.no_rekam_medis
                    ?.toLowerCase()
                    .includes(searchQuery.value.toLowerCase()) ||
                pasien.no_pasien
                    ?.toLowerCase()
                    .includes(searchQuery.value.toLowerCase()) ||
                pasien.tanggal_pemeriksaan
                    ?.toLowerCase()
                    .includes(searchQuery.value.toLowerCase()) ||
                pasien.jenis_kelamin
                    ?.toLowerCase()
                    .includes(searchQuery.value.toLowerCase()) ||
                pasien.umur
                    ?.toLowerCase()
                    .includes(searchQuery.value.toLowerCase()) ||
                pasien.tempat_tanggal_lahir
                    ?.toLowerCase()
                    .includes(searchQuery.value.toLowerCase())
        ).length;

        const newTotalPages = Math.ceil(currentFilteredCount / entriesToShow.value);

        if (currentPage.value > newTotalPages && newTotalPages > 0) {
            currentPage.value = newTotalPages;
        } else if (newTotalPages === 0) {
            currentPage.value = 1;
        }

    } catch (error) {
        console.error("Error deleting patient:", error);
        errorMessage.value = "Gagal menghapus data pasien.";
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
        pasienToDelete.value = null; 
    }
}

// --- Navigation ---
function goToDetail(pasien) {
    console.log("Navigate to detail for:", pasien);
    const id = pasien.id || pasien.no_pasien; 

    if (id) {
        router.push({
            name: "PasienDetail",
            params: { id: id },
            query: { ...pasien },
        });
    } else {
        console.error(
            pasien
        );
        errorMessage.value =
        showErrorPopup.value = true;
    }
}

function goToEdit(pasien) {
     if (loggedInUser.value.role === 'admin') {
        console.warn("Admin users cannot edit data.");
        return;
     }

    console.log("Navigate to edit for:", pasien);
    const id = pasien.id || pasien.no_pasien; 

    if (id) {
        router.push({
            name: "PasienEdit",
            params: { id: id },
            query: { ...pasien }, 
        });
    } else {
        console.error(
            "Cannot navigate to edit: Patient object or identifier is missing",
            pasien
        );
        errorMessage.value = "Tidak dapat mengedit: ID pasien hilang.";
        showErrorPopup.value = true;
    }
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

table,
th,
td {
    border: 1px solid #e0e0e0;
}

th {
    background-color: #fff;
}
</style>