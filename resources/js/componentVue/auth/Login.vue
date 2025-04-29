<template>
    <div class="flex h-screen">
        <!-- Kiri -->
        <div class="w-1/2 bg-white flex items-center justify-center">
            <img
                src="@/assets/authImage.svg"
                alt="Auth Illustration"
                class="w-4/5 h-auto"
            />
        </div>

        <!-- Kanan -->
        <div class="w-1/2 flex items-center justify-center bg-[#FFFF]">
            <div class="bg-[#D4ECF2] rounded-3xl shadow-md px-10 py-16 w-full max-w-md">
              <!-- loading -->
              <div
                    v-if="loading"
                    class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-40 rounded-3xl"
                >
                    <div
                        class="w-16 h-16 border-4 border-t-4 border-[#3393AD] border-solid rounded-full animate-spin"
                    ></div>
                </div>

                <div class="text-center">
                    <img
                        src="@/assets/logo.svg"
                        alt="Logo"
                        class="mx-auto mb-4 w-26 h-26 -mt-4"
                    />
                    <h2 class="text-3xl font-bold text-[#3393AD] leading-snug">
                        Halo, <br />
                        <span class="font-bold">Selamat Datang Kembali!</span>
                    </h2>
                    <p class="text-sm text-[#3393AD] mt-2 mb-6">
                        Masukkan NIP dan kata sandi untuk melanjutkan akses.
                    </p>
                </div>

                <form @submit.prevent="login">
                    <input
                        v-model="nip"
                        type="text"
                        placeholder="NIP"
                        class="w-full p-3 mb-4 border border-[#3393AD] rounded focus:outline-none focus:ring-2 focus:ring-blue-400 text-[#3393AD] font-semibold"
                    />
                    <div class="relative">
                        <input
                            v-model="password"
                            :type="showPassword ? 'text' : 'password'"
                            placeholder="Kata Sandi"
                            class="w-full p-3 mb-4 border border-[#3393AD] rounded focus:outline-none focus:ring-2 focus:ring-blue-400 text-[#3393AD] font-semibold"
                        />
                        <!-- Cek lihat password -->
                        <button
                            type="button"
                            class="absolute right-3 top-3 text-gray-500"
                            @click="togglePassword"
                        >
                            <img
                                :src="
                                    showPassword
                                        ? passwordOpenIcon
                                        : passwordCloseIcon
                                "
                                alt="Toggle password"
                                class="w-6 h-6"
                            />
                        </button>
                    </div>

                    <!-- Ubah Kata Sandi Clickable Text -->
                    <div class="flex justify-end items-center mb-6">
                        <a
                            href="#"
                            class="text-sm text-[#3393AD] hover:underline"
                            @click.prevent="showConfirmPopUp"
                        >
                            Ubah Kata Sandi
                        </a>
                    </div>

                    <!-- Button Masuk -->
                    <div class="flex justify-center">
                        <button
                            type="submit"
                            @mouseenter="hovering = true"
                            @mouseleave="hovering = false"
                            class="flex items-center justify-center px-6 py-2 border border-[#3393AD] rounded transition bg-transparent hover:bg-[#3393AD]"
                        >
                            <img
                                :src="hovering ? masukWhite : masukGreen"
                                alt="Masuk Icon"
                                class="w-5 h-5 mr-2 transition duration-200"
                            />

                            <span
                                :class="[
                                    'mr-2 font-semibold transition',
                                    hovering ? 'text-white' : 'text-[#3393AD]',
                                ]"
                            >
                                Masuk
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Pop Up -->
        <div
            v-if="showPopUp"
            class="fixed inset-0 bg-[rgba(0,0,0,0.3)] flex items-center justify-center z-50"
        >
            <div
                class="bg-[#27394B] p-6 rounded-2xl shadow-md w-96 text-center"
            >
                <img
                    :src="popUpIcon"
                    alt="Icon"
                    class="w-20 mx-auto mt-4 mb-4"
                />
                <p class="text-white px-4 py-2 font-medium mb-4">
                    {{ popUpMessage }}
                </p>
                <div class="flex justify-center gap-4">
                    <!-- Kalau klik ubah kata sandi -->
                    <template v-if="popUpType == 'confirm'">
                        <button
                            class="bg-transparent text-white border border-white px-4 py-2 rounded-md hover:bg-white hover:text-[#27394B] font-semibold"
                            @click="showPopUp = false"
                        >
                            Batal
                        </button>
                        <button
                            class="bg-transparent text-white border border-white px-4 py-2 rounded-md hover:bg-white hover:text-[#27394B] font-semibold"
                            @click="goToVerifyPage"
                        >
                            Ya
                        </button>
                    </template>

                    <!-- Kalau klik Button Masuk dan syarat: (1) NIP/pass kosong || (2) NIP/pass salah -->
                    <template v-else>
                        <button
                            class="bg-transparent text-white border border-white px-4 py-2 rounded-md hover:bg-white hover:text-[#27394B] font-semibold"
                            @click="showPopUp = false"
                        >
                            Coba Lagi
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import axios from "axios";

// Value for v-model
import { ref } from "vue";

// I can't use this dawg buat ngarahin ke page VerifyPass.vue
// kelar bwang
import { useRouter } from "vue-router";
const router = useRouter();

const nip = ref("");
const password = ref("");
const showPassword = ref(false);

const showPopUp = ref(false);
const popUpMessage = ref("");
const popUpIcon = ref("");
const popUpType = ref(""); // "error", "empty", "confirm"

const loading = ref(false);

// Asset path
import passwordOpenIcon from "@/assets/passwordOpen.svg";
import passwordCloseIcon from "@/assets/passwordClose.svg";
import masukGreen from "@/assets/masukGreen.svg";
import masukWhite from "@/assets/masukWhite.svg";
import iconError from "@/assets/circle-x.svg";
import iconWarning from "@/assets/warning-triangle.svg";
import iconConfirm from "@/assets/userCheck.svg";

function togglePassword(state) {
    showPassword.value = !showPassword.value;
}

async function login() {
    if (!nip.value || !password.value) {
        popUpIcon.value = iconWarning;
        popUpMessage.value = "Tidak ada yang valid. Isi data terlebih dahulu!";
        popUpType.value = "empty";
        showPopUp.value = true;
    } else {
        loading.value = true;
        try {
            const response = await axios.post(
                "https://occuhelp-capstone-production.up.railway.app/api/login",
                {
                    nip: nip.value,
                    password: password.value,
                },
                {
                    headers: {
                        "Content-Type": "application/json",
                    },
                }
            );

            if (response.data.token) {
                localStorage.setItem("auth_token", response.data.token);
                router.push("/aboutus");
            }
        } catch (error) {
            popUpIcon.value = iconError;
            if (error.response && error.response.status === 401) {
                popUpMessage.value = "NIP/Kata sandi yang Anda masukkan salah.";
            } else {
                popUpMessage.value =
                    "Terjadi kesalahan saat login. Coba lagi nanti.";
            }
            popUpType.value = "error";
            showPopUp.value = true;
        } finally {
            loading.value = false;
        }
    }
}

function showConfirmPopUp() {
    popUpIcon.value = iconConfirm;
    popUpMessage.value = "Apakah Anda yakin ingin mengubah kata sandi?";
    popUpType.value = "confirm";
    showPopUp.value = true;
}

function goToVerifyPage() {
    showPopUp.value = false;
    router.push("/verifypass");
}

// For hover button
const hovering = ref(false);
</script>
