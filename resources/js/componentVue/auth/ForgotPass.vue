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
            <div
                class="bg-[#D4ECF2] rounded-3xl shadow-md px-10 py-16 w-full max-w-md"
            >
                <div class="text-center">
                    <h2 class="text-3xl font-bold text-[#3393AD] leading-snug">
                        <span class="font-semibold"
                            >Silahkan Ganti Kata Sandi Anda di Sini!</span
                        >
                    </h2>
                    <p class="text-sm text-[#3393AD] mt-2 mb-6">
                        Jangan gunakan kata-kata yang mudah ditebak, seperti
                        nama atau tanggal lahir.
                    </p>
                </div>

                <form @submit.prevent="submitReset">
                    <!-- Password Lama -->
                    <div class="relative">
                        <input
                            v-model="oldPassword"
                            :type="showOld ? 'text' : 'password'"
                            placeholder="Kata Sandi Lama"
                            class="w-full p-3 mb-4 border border-[#3393AD] rounded focus:outline-none focus:ring-2 focus:ring-blue-400 text-[#3393AD] font-semibold"
                        />
                        <button
                            type="button"
                            class="absolute right-3 top-3"
                            @click="showOld = !showOld"
                        >
                            <img
                                :src="togglePassword(showOld)"
                                alt="Toggle password"
                                class="w-6 h-6"
                            />
                        </button>
                    </div>

                    <!-- Password Baru -->
                    <div class="relative">
                        <input
                            v-model="newPassword"
                            :type="showNew ? 'text' : 'password'"
                            placeholder="Kata Sandi Baru"
                            class="w-full p-3 mb-4 border border-[#3393AD] rounded focus:outline-none focus:ring-2 focus:ring-blue-400 text-[#3393AD] font-semibold"
                        />
                        <button
                            type="button"
                            class="absolute right-3 top-3"
                            @click="showNew = !showNew"
                        >
                            <img
                                :src="togglePassword(showNew)"
                                alt="Toggle password"
                                class="w-6 h-6"
                            />
                        </button>
                    </div>

                    <!-- Konfirmasi Password Baru -->
                    <div class="relative">
                        <input
                            v-model="confirmPassword"
                            :type="showConfirm ? 'text' : 'password'"
                            placeholder="Konfirmasi Kata Sandi Baru"
                            :class="[
                                'w-full p-3 mb-4 border rounded focus:outline-none focus:ring-2 text-[#3393AD] font-semibold',
                                shakeConfirm
                                    ? 'border-red-500 animate-shake'
                                    : 'border-[#3393AD] focus:ring-blue-400',
                            ]"
                        />
                        <button
                            type="button"
                            class="absolute right-3 top-3"
                            @click="showConfirm = !showConfirm"
                        >
                            <img
                                :src="togglePassword(showConfirm)"
                                alt="Toggle password"
                                class="w-6 h-6"
                            />
                        </button>
                    </div>

                    <div class="flex justify-center mt-10">
                        <button
                            type="submit"
                            @mouseenter="hovering = true"
                            @mouseleave="hovering = false"
                            class="flex items-center justify-center px-6 py-2 border border-[#3393AD] rounded transition bg-transparent hover:bg-[#3393AD]"
                        >
                            <img
                                :src="hovering ? lockWhite : lock"
                                alt="Lock Icon"
                                class="w-5 h-5 mr-2 transition duration-200"
                            />

                            <span
                                :class="[
                                    'mr-2 font-semibold transition',
                                    hovering ? 'text-white' : 'text-[#3393AD]',
                                ]"
                            >
                                Perbarui Kata Sandi
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Pop-up Data Kosong -->
        <div
            v-if="showWarningPopup"
            class="fixed inset-0 bg-[rgba(0,0,0,0.3)] flex items-center justify-center z-50"
        >
            <div
                class="bg-[#27394B] p-6 rounded-2xl shadow-md w-96 text-center"
            >
                <img
                    src="@/assets/warning-triangle.svg"
                    alt="Warning"
                    class="w-20 mx-auto mt-4 mb-4"
                />
                <p class="text-white px-4 py-2 font-medium mb-4">
                    Tidak ada yang valid. Isi data terlebih dahulu!
                </p>
                <button
                    class="bg-transparent text-white border border-white px-4 py-2 rounded-md hover:bg-white hover:text-[#27394B] font-semibold"
                    @click="showWarningPopup = false"
                >
                    Coba Lagi
                </button>
            </div>
        </div>

        <!-- Pop-up Berhasil Ganti Pass -->
        <div
            v-if="showSuccessPopup"
            class="fixed inset-0 bg-[rgba(0,0,0,0.3)] flex items-center justify-center z-50"
        >
            <div
                class="bg-[#27394B] p-6 rounded-2xl shadow-md w-96 text-center"
            >
                <img
                    src="@/assets/circle-check.svg"
                    alt="Pass Change Success"
                    class="w-20 mx-auto mt-4 mb-4"
                />
                <p class="text-white px-4 py-2 font-medium mb-4">
                    Kata sandi Anda sudah diperbarui!
                </p>
                <button
                    class="bg-transparent text-white border border-white px-4 py-2 rounded-md hover:bg-white hover:text-[#27394B] font-semibold"
                    @click="goToLogin"
                >
                    Kembali ke Halaman Login
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
// Value for v-model
const oldPassword = ref("");
const newPassword = ref("");
const confirmPassword = ref("");

const showOld = ref(false);
const showNew = ref(false);
const showConfirm = ref(false);
const showPassword = ref(false);

const showWarningPopup = ref(false);
const showSuccessPopup = ref(false);
const shakeConfirm = ref(false);

// Asset path
import { ref } from "vue";

import passwordOpen from "@/assets/passwordOpen.svg";
import passwordClose from "@/assets/passwordClose.svg";
import lock from "@/assets/lock.svg";
import lockWhite from "@/assets/lockWhite.svg";

import axios from "axios";
import { useRoute, useRouter } from "vue-router";

const route = useRoute();
const router = useRouter();

const userId = ref(route.query.user);

import { onBeforeMount } from 'vue'

onBeforeMount(() => {
  if (!route.query.user) {
    router.replace('/login');
  }
});


function togglePassword(state) {
    return state ? passwordOpen : passwordClose;
}

function goToLogin() {
    showSuccessPopup.value = false;
    router.push("/login");
}

async function submitReset() {
    if (!newPassword.value || !confirmPassword.value) {
        showWarningPopup.value = true;
        return;
    }

    if (newPassword.value !== confirmPassword.value) {
        shakeConfirm.value = true;
        setTimeout(() => {
            shakeConfirm.value = false;
        }, 600);
        return;
    }

    try {
        await axios.post("/api/reset-password", {
            user_id: Number(userId.value),
            password: newPassword.value,
        });

        showSuccessPopup.value = true;
        newPassword.value = "";
        confirmPassword.value = "";
    } catch (error) {
        console.error(error);
        alert(
            error.response?.data?.message ||
                "Gagal memperbarui kata sandi. Coba lagi nanti."
        );
    }
}

// For hover button masuk wooooooooo
const hovering = ref(false);
</script>

<style scoped>
@keyframes shake {
    0% {
        transform: translateX(0px);
    }
    25% {
        transform: translateX(-5px);
    }
    50% {
        transform: translateX(5px);
    }
    75% {
        transform: translateX(-5px);
    }
    100% {
        transform: translateX(0px);
    }
}

.animate-shake {
    animation: shake 0.4s ease;
}
</style>
