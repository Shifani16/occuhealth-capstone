<template>
    <div class="flex h-screen">
      <!-- Kiri -->
      <div class="w-1/2 bg-white flex items-center justify-center">
        <img src="@/assets/authImage.svg" alt="Auth Illustration" class="w-4/5 h-auto" />
      </div>
  
      <!-- Kanan -->
      <div class="w-1/2 flex items-center justify-center bg-[#FFFF]">
        <div class="bg-[#D4ECF2] rounded-3xl shadow-md px-10 py-16 w-full max-w-md">
          <div class="text-center">
            <h2 class="text-3xl font-bold text-[#3393AD] leading-snug -mt-6 mb-6">
              <span class="font-semibold">Silahkan Masukkan Email Anda di Sini!</span>
            </h2>
            <p class="text-sm text-[#3393AD] mt-2 mb-6 justify">
                Kami akan mengirimkan tautan verifikasi ke email Anda. Klik tautan
                tersebut untuk melanjutkan proses ganti kata sandi akun Anda.
            </p>
          </div>
  
          <form @submit.prevent="verifyPass">
            <input
              v-model="email"
              type="text"
              placeholder="Email"
              class="w-full p-3 mb-35 border border-[#3393AD] rounded focus:outline-none focus:ring-2 focus:ring-blue-400 text-[#3393AD] font-semibold"
            />

            <div class="flex justify-center mt-10">
                <button
                    type="submit"
                    @mouseenter="hovering = true"
                    @mouseleave="hovering = false"
                    class="flex items-center justify-center px-6 py-2 border border-[#3393AD] rounded transition bg-transparent hover:bg-[#3393AD]">
                    
                    <img 
                        :src="hovering ? verifyWhite : verifyGreen" 
                        alt="Lock Icon" 
                        class="w-5 h-5 mr-2 transition duration-200"
                    />

                    <span :class="['mr-2 font-semibold transition', hovering ? 'text-white' : 'text-[#3393AD]']">
                        Kirim Link Verifikasi
                    </span>
                </button>
            </div>   
          </form>
        </div>
      </div>

      <!-- Pop Up -->
      <div v-if="showPopUp" class="fixed inset-0 bg-[rgba(0,0,0,0.3)] flex items-center justify-center z-50">
        <div class="bg-[#27394B] p-6 rounded-2xl shadow-md w-96 text-center">
            <img src="@/assets/verifyWhite.svg"
            alt="Verifikasi" 
            class="w-23 mx-auto mb-4" 
            />
            <p class="text-white px-4 py-2 font-medium mb-4">
                Tautan verifikasi sudah dikirim melalui Email Anda
            </p>
            <button
            class="bg-transparent text-white border border-white px-4 py-2 rounded-md hover:bg-white hover:text-[#27394B] font-semibold"
            @click="openGmail"
            >
                Cek Email
            </button>
        </div>
       </div>
    </div>
  </template>
  
<script setup>
// Value for v-model
const email = ref('')

// Asset path
import { ref } from 'vue'
import verifyWhite from '@/assets/verifyWhite.svg'
import verifyGreen from '@/assets/verifyGreen.svg'

import axios from 'axios'

async function verifyPass() {
  if (email.value.trim()) {
    try {
      await axios.post('https://occuhealth-capstone-production.up.railway.app/api/send-reset-link', {
        email: email.value
      });
      showPopUp.value = true;
    } catch (error) {
      console.error(error);
      alert(error.response?.data?.message || 'Terjadi kesalahan saat mengirim email.');
    }
  } else {
    alert('Mohon masukkan email terlebih dahulu.');
  }
}

function openGmail() {
  window.open('https://mail.google.com/', '_blank');
  showPopUp.value = false;
}


// For hover button masuk
const hovering = ref(false)
const showPopUp = ref(false)
</script>