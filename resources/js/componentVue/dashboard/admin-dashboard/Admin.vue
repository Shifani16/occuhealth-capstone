<template>
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
    <LeftBar active="Admin" />
    <main class="flex-1 p-6">
      <h1 class="text-[48px] font-bold mb-6 ml-7 container-nunito">Admin</h1>

      <!-- Flex container for Form and Table -->
      <div class="flex flex-wrap lg:flex-nowrap gap-8 ml-7">
        <!-- Registration Form Section -->
        <div
          class="w-full lg:w-1/2 flex items-center justify-center bg-[#FFFF] order-2 lg:order-1"
        >
          <div
            class="bg-[#D4ECF2] rounded-3xl shadow-md px-10 py-16 w-full max-w-md"
          >
            <div class="text-center">
              <h2
                class="container-nunito text-left sm:text-2xl md:text 3xl lg:text-3xl font-bold text-[#3393AD] leading-snug"
              >
                <span class="font-bold">Daftar Akun Baru</span>
              </h2>
              <p
                class="container-open-sans text-left text-sm text-[#3393AD] mt-2 mb-6"
              >
                Masukkan data Nama Lengkap, NIP, Email, Kata Sandi, dan Role untuk membuat akun baru.
              </p>
            </div>

            <form @submit.prevent="register">
              <input
                v-model="nama"
                type="text"
                placeholder="Nama Lengkap"
                required
                class="w-full p-3 mb-4 border border-[#3393AD] rounded focus:outline-none focus:ring-2 focus:ring-blue-400 text-[#3393AD] font-semibold placeholder-[#3393AD]::placeholder"
              />
              <input
                v-model="nip"
                type="text"
                placeholder="NIP"
                required
                class="w-full p-3 mb-4 border border-[#3393AD] rounded focus:outline-none focus:ring-2 focus:ring-blue-400 text-[#3393AD] font-semibold placeholder-[#3393AD]::placeholder"
              />
              <input
                v-model="email"
                type="email"
                placeholder="Email"
                required
                class="w-full p-3 mb-4 border border-[#3393AD] rounded focus:outline-none focus:ring-2 focus:ring-blue-400 text-[#3393AD] font-semibold placeholder-[#3393AD]::placeholder"
              />
              <div class="relative">
                <input
                  v-model="password"
                  :type="showPassword ? 'text' : 'password'"
                  placeholder="Kata Sandi"
                  required
                  class="w-full p-3 mb-4 border border-[#3393AD] rounded focus:outline-none focus:ring-2 focus:ring-blue-400 text-[#3393AD] font-semibold pr-10 placeholder-[#3393AD]::placeholder"
                />
                <button
                  type="button"
                  class="absolute right-3 top-3 text-gray-500"
                  @click="togglePassword"
                >
                  <img
                    :src="showPassword ? passwordOpenIcon : passwordCloseIcon"
                    alt="Toggle password"
                    class="w-6 h-6"
                  />
                </button>
              </div>
              <div class="relative">
                <input
                  v-model="password_confirmation"
                  :type="showPassword ? 'text' : 'password'"
                  placeholder="Konfirmasi Kata Sandi"
                  required
                  class="w-full p-3 mb-4 border border-[#3393AD] rounded focus:outline-none focus:ring-2 focus:ring-blue-400 text-[#3393AD] font-semibold pr-10 placeholder-[#3393AD]::placeholder"
                />
                <button
                  type="button"
                  class="absolute right-3 top-3 text-gray-500"
                  @click="togglePassword"
                >
                  <img
                    :src="showPassword ? passwordOpenIcon : passwordCloseIcon"
                    alt="Toggle password"
                    class="w-6 h-6"
                  />
                </button>
              </div>
              <select
                v-model="role"
                required
                class="w-full p-3 mb-4 border border-[#3393AD] rounded focus:outline-none focus:ring-2 focus:ring-blue-400 text-[#3393AD] font-semibold"
              >
                <option disabled value="">Pilih Role</option>
                <option value="admin">admin</option>
                <option value="nakes">nakes</option>
              </select>
              <div class="flex justify-center">
                <button
                  type="submit"
                  @mouseenter="hovering = true"
                  @mouseleave="hovering = false"
                  :disabled="loading"
                  class="flex items-center justify-center px-6 py-2 border border-[#3393AD] rounded transition bg-transparent hover:bg-[#3393AD] disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <img
                    :src="hovering && !loading ? masukWhite : masukGreen"
                    alt="Buat Akun Icon"
                    class="w-5 h-5 mr-2 transition duration-200"
                  />
                  <span
                    :class="[
                      'container-open-sans mr-2 font-semibold transition',
                      hovering && !loading ? 'text-white' : 'text-[#3393AD]',
                    ]"
                  >
                    {{ loading ? 'Membuat Akun...' : 'Buat Akun' }}
                  </span>
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- User List Table Section -->
        <div class="w-full lg:w-1/2 bg-white p-6 rounded-md shadow order-1 lg:order-2">
          <h2 class="container-nunito text-2xl font-bold text-[#3393AD] mb-4">
            Daftar Akun Terdaftar
          </h2>
          <div v-if="loadingUsers" class="text-center text-gray-500 py-4">
            Memuat data pengguna...
          </div>
          <div v-else-if="usersList.length === 0" class="text-center text-gray-500 py-4">
            Tidak ada akun terdaftar.
          </div>
          <div v-else class="overflow-x-auto max-h-[calc(100vh-350px)] border rounded-md">
            <table class="table-auto text-left border-collapse w-full">
              <thead class="bg-white">
                <tr class="container-open-sans font-bold">
                  <th class="px-4 py-2 sticky top-0 z-10">No</th>
                  <th class="px-4 py-2 sticky top-0 z-10">Nama</th>
                  <th class="px-4 py-2 sticky top-0 z-10">Role</th>
                  <th class="px-4 py-2 sticky top-0 z-10 text-center">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(user, index) in usersList"
                  :key="user.id"
                  class="container-open-sans odd:bg-[#E6F6F9] text-sm"
                >
                  <td class="px-4 py-2">{{ index + 1 }}</td>
                  <td class="px-4 py-2">{{ user.name }}</td>
                  <td class="px-4 py-2">{{ user.role }}</td>
                  <td class="px-4 py-2 text-center">
                    <button
                      @click="selectUserToDelete(user)"
                      class="cursor-pointer w-6 h-6 object-contain"
                      title="Hapus Akun"
                    >
                      <img src="@/assets/action-delete.svg" />
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Pop Up -->
      <div
        v-if="showPopUp"
        class="fixed inset-0 bg-[rgba(0,0,0,0.3)] flex items-center justify-center z-50"
      >
        <div class="bg-[#27394B] p-6 rounded-2xl shadow-md w-96 text-center">
          <img
            :src="popUpIcon"
            alt="Icon"
            class="w-20 mx-auto mt-4 mb-4"
          />
          <p class="text-white px-4 py-2 font-medium mb-4">
            {{ popUpMessage }}
          </p>
          <div class="flex justify-center gap-4">
            <template v-if="popUpType === 'confirm'">
              <button
                class="bg-transparent text-white border border-white px-4 py-2 rounded-md hover:bg-white hover:text-[#27394B] font-semibold"
                @click="showPopUp = false"
              >
                Batal
              </button>
              <button
                class="bg-transparent text-white border border-white px-4 py-2 rounded-md hover:bg-white hover:text-[#27394B] font-semibold"
                @click="confirmDeleteUser"
              >
                Ya, Hapus
              </button>
            </template>
            <template v-else>
              <button
                class="bg-transparent text-white border border-white px-4 py-2 rounded-md hover:bg-white hover:text-[#27394B] font-semibold"
                @click="showPopUp = false"
              >
                Tutup
              </button>
            </template>
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
</template>


<script setup>
import axios from "axios";

import { ref, onMounted } from "vue"; 
import { useRouter } from "vue-router";

import LeftBar from "../../../composables/LeftBar.vue";

import passwordOpenIcon from "@/assets/passwordOpen.svg";
import passwordCloseIcon from "@/assets/passwordClose.svg";
import masukGreen from "@/assets/masukGreen.svg";
import masukWhite from "@/assets/masukWhite.svg";
import iconError from "@/assets/circle-x.svg"; 
import iconConfirm from "@/assets/userCheck.svg"; 
import iconDeleteConfirm from "@/assets/circle-cancel.svg"; 

const router = useRouter();

// --- Form State ---
const nama = ref("");
const nip = ref("");
const email = ref("");
const password = ref("");
const password_confirmation = ref(""); 
const role = ref("");
const showPassword = ref(false);
const loading = ref(false);
const hovering = ref(false);

// --- User List Table State ---
const usersList = ref([]);
const loadingUsers = ref(false);

// --- Pop Up State (for Registration, Fetching Error, Delete Success/Error/Confirm) ---
const showPopUp = ref(false);
const popUpMessage = ref("");
const popUpIcon = ref("");
const popUpType = ref(null); 

// --- Delete State ---
const userToDelete = ref(null);


// --- Methods ---
function togglePassword() {
    showPassword.value = !showPassword.value;
}

// --- Fetch Users ---
const fetchUsers = async () => {
    loadingUsers.value = true;
    try {
        const response = await axios.get("/api/users");
        console.log("Fetched users:", response.data);
        if (response.data && Array.isArray(response.data.users)) {
            usersList.value = response.data.users;
        } else {
            console.error("Unexpected response format for /api/users:", response.data);
            popUpMessage.value = "Gagal memuat daftar pengguna: Format data tidak sesuai.";
            popUpIcon.value = iconError;
            popUpType.value = 'error';
            showPopUp.value = true;
            usersList.value = []; 
        }
    } catch (error) {
        console.error("Error fetching users:", error.response?.data || error.message);
         let message = "Gagal memuat daftar pengguna.";
         if (error.response && error.response.data) {
            message = error.response.data.message || error.response.data.error || JSON.stringify(error.response.data);
         } else if (error.message) {
            message = `Gagal memuat data: ${error.message}`;
         }
        popUpMessage.value = message;
        popUpIcon.value = iconError;
        popUpType.value = 'error';
        showPopUp.value = true;
        usersList.value = [];
    } finally {
        loadingUsers.value = false;
    }
};


// --- Registration Logic ---
const register = async () => {
    if (!nama.value || !nip.value || !email.value || !password.value || !password_confirmation.value || !role.value) {
        popUpMessage.value = "Semua bidang wajib diisi.";
        popUpIcon.value = iconWarning; 
        popUpType.value = 'warning'; 
        showPopUp.value = true;
        return;
    }

     if (password.value !== password_confirmation.value) {
        popUpMessage.value = "Kata sandi dan konfirmasi kata sandi tidak cocok.";
        popUpIcon.value = iconWarning;
        popUpType.value = 'warning';
        showPopUp.value = true;
        return;
     }


    loading.value = true;
    showPopUp.value = false;
    popUpMessage.value = "";
    popUpIcon.value = "";
    popUpType.value = null;

    try {
        const response = await axios.post('/api/register', {
            name: nama.value,
            nip: nip.value,
            email: email.value,
            password: password.value,
            password_confirmation: password_confirmation.value, 
            role: role.value,
        });

        console.log("Registration successful:", response.data);

        popUpMessage.value = response.data.message || 'Akun berhasil dibuat!';
        popUpIcon.value = iconConfirm;
        popUpType.value = 'success';

        showPopUp.value = true;

        nama.value = "";
        nip.value = "";
        email.value = "";
        password.value = "";
        password_confirmation.value = "";
        role.value = "";

        fetchUsers();


    } catch (error) {
        console.error("Registration failed:", error.response?.data);

        let message = 'Terjadi kesalahan saat membuat akun.';
        if (error.response && error.response.data) {
             if (error.response.data.message) {
                message = error.response.data.message;
            } else if (error.response.data.error) {
                message = error.response.data.error;
            } else if (error.response.data.errors) {
                 const validationErrors = error.response.data.errors;
                 message = Object.values(validationErrors).flat().join(' ');
            } else if (typeof error.response.data === 'string') {
                 message = error.response.data;
            }
        } else if (error.message) {
            message = `Pendaftaran gagal: ${error.message}`;
        }


        popUpMessage.value = message;
        popUpIcon.value = iconError;
        popUpType.value = 'error';

        showPopUp.value = true;

    } finally {
        loading.value = false;
    }
};

// --- Delete Logic ---
function selectUserToDelete(user) {
    userToDelete.value = user;
    popUpMessage.value = `Apakah Anda yakin ingin menghapus akun dengan NIP ${user.nip}?`;
    popUpIcon.value = iconDeleteConfirm;
    popUpType.value = 'confirm'; 
    showPopUp.value = true;
}

async function confirmDeleteUser() {
    if (!userToDelete.value || !userToDelete.value.id) {
        console.error("No user selected for deletion or ID missing.");
        popUpMessage.value = "Terjadi kesalahan: Data pengguna tidak ditemukan atau ID hilang.";
        popUpIcon.value = iconError;
        popUpType.value = 'error';
        showPopUp.value = true;
        userToDelete.value = null;
        return;
    }

    showPopUp.value = false;
    popUpMessage.value = ""; 
    popUpIcon.value = "";
    popUpType.value = null;
    loadingUsers.value = true;


    try {
        const response = await axios.delete(`/api/users/${userToDelete.value.id}`);
        console.log("Delete successful:", response.data);

        popUpMessage.value = response.data.message || "Akun berhasil dihapus";
        popUpIcon.value = iconConfirm;
        popUpType.value = 'success';
        showPopUp.value = true;

        usersList.value = usersList.value.filter(user => user.id !== userToDelete.value.id);

        userToDelete.value = null; 

    } catch (error) {
        console.error("Error deleting user:", error.response?.data || error.message);
         let message = "Gagal menghapus akun.";
         if (error.response && error.response.data) {
            message = error.response.data.message || error.response.data.error || JSON.stringify(error.response.data);
         } else if (error.message) {
            message = `Gagal menghapus data: ${error.message}`;
         }
        popUpMessage.value = message;
        popUpIcon.value = iconError;
        popUpType.value = 'error';
        showPopUp.value = true;

    } finally {
         loadingUsers.value = false;
    }
}


onMounted(() => {
    fetchUsers();
});

</script>

<style scoped>
.container-nunito {
    font-family: "Nunito", sans-serif;
}

.container-open-sans {
    font-family: "Open Sans", sans-serif;
}
</style>
