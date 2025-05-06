<template>
    <div class="ourservice">
        <Navbar />

        <section class="relative flex justify-center items-start px-4 md:px-0 mt-30 mb-10">
            <img
                src="@/assets/our-service-image.svg"
                alt="Our Service Image"
                class="absolute -top-20 md:-top-24 z-10 w-72 sm:w-100 md:w-110 lg:w-130"
            />

            <div class="bg-[#E6F6F9] sm:mt-20 md:mt-22 lg:mt-35 rounded-3xl pt-20 pb-8 px-6 sm:px-10 md:px-10 md:max-w-3xl lg:max-w-5xl w-full">
                <h2 class="container-nunito sm:text-[28px] md:text-[30px] lg:text-[48px] font-bold text-black mb-4">
                    Apa itu <span class="whitespace-nowrap">Medical Check-Up (MCU)</span> <span class="text-[#217F96]">berbasis Okupasi?</span>
                </h2>
                <p class="container-open-sans text-justify lg:text-[22px] md:text-base text-[#1B1B1B] leading-relaxed">
                    MCU (Medical Check-Up) berbasis okupasi adalah pemeriksaan medis untuk memastikan seseorang memiliki kondisi 
                    kesehatan yang memadai guna menjalankan tugas pekerjaan secara aman dan efektif. Pemeriksaan ini disesuaikan 
                    dengan jenis pekerjaan, mengingat tiap pekerjaan memiliki risiko kesehatan berbeda.
                </p>
            </div>
        </section>

        <section class="bg-[url('@/assets/mcu-service-bg.svg')] bg-cover bg-center py-20 px-6 md:px-10">
            <div class="container-nunito max-w-5xl mx-auto text-left">
                <h2 class="text-[30px] sm:text-3xl md:text-4xl lg:text-[48px] font-bold mb-8">
                    Apa saja jenis Medical Check-Up?
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                    <!-- Pemeriksaan Fisik -->
                    <div class="flex-1 bg-[#C1D6E6] rounded-2xl p-6 flex flex-col items-center text-center shadow-md">
                        <img src="@/assets/pemeriksaan-fisik.svg" alt="Pemeriksaan Fisik" class="mb-2 h-36 object-contain"/>
                        <h3 class="text-lg font-semibold sm:text-[22px] md:text-[24px] lg:text-[32px]">Pemeriksaan Fisik</h3>
                    </div>

                    <!-- Pemeriksaan Psikologis -->
                    <div class="flex-1 bg-[#C1D6E6] rounded-2xl p-6 flex flex-col items-center text-center shadow-md">
                        <img src="@/assets/pemeriksaan-psikologis.svg" alt="Pemeriksaan Psikologis" class="mb-2 h-36 object-contain"/>
                        <h3 class="text-lg font-semibold sm:text-[22px] md:text-[24px] lg:text-[32px]">Pemeriksaan Psikologis</h3>
                    </div>

                    <!-- Pemeriksaan Kesehatan Spesifik -->
                        <div class="flex-1 bg-[#C1D6E6] rounded-2xl p-6 flex flex-col items-center text-center shadow-md">
                        <img src="@/assets/pemeriksaan-kesehatan-spesifik.svg" alt="Pemeriksaan Kesehatan Spesifik" class="mb-2 h-36 object-contain"/>
                        <h3 class="text-lg font-semibold sm:text-[22px] md:text-[24px] lg:text-[32px]">Pemeriksaan Kesehatan Spesifik</h3>
                    </div>

                    <!-- Pemeriksaan Kesehatan Berkala -->
                    <div class="flex-1 bg-[#C1D6E6] rounded-2xl p-6 flex flex-col items-center text-center shadow-md">
                        <img src="@/assets/pemeriksaan-kesehatan-berkala.svg" alt="Pemeriksaan Kesehatan Berkala" class="mb-2 h-36 object-contain"/>
                        <h3 class="text-lg font-semibold sm:text-[22px] md:text-[24px] lg:text-[32px]">Pemeriksaan Kesehatan Berkala</h3>
                    </div>
                </div>  
            </div>
        </section>

        <section class="py-10 px-3 bg-white flex justify-center">
            <div class="bg-[#E6F6F9] rounded-3xl p-8 w-full max-w-6xl flex flex-col sm:flex-row md:flex-row items-center gap-8">
                <div class="w-full md:w-1/2 flex justify-center">
                    <img src="@/assets/tips-mcu-image.svg" alt="Tips MCU Image" class="max-w-sm w-full"/>
                </div>

                <div class="w-full md:w-1/2">
                    <h2 class="container-nunito text-2xl -ml-15 md:-ml-5 sm:ml-1 sm:text-2xl md:text-3xl lg:text-[48px] font-semibold text-black mb-6">
                        Tips untuk <span class="text-[#299BB8] font-bold">Memaksimalkan</span><br />MCU Berkala:
                    </h2>

                    <div class="container-open-sans -ml-15 md:-ml-5 sm:ml-1 flex flex-col gap-4">
                        <div class="bg-[#299BB8] text-white px-6 py-6 rounded-xl text-base font-bold md:text-[19px] lg:text-[24px]">
                            1. Melakukan pemeriksaan secara rutin
                        </div>
                        <div class="bg-[#299BB8] text-white px-6 py-6 rounded-xl text-base font-bold md:text-[19px] lg:text-[24px]">
                            2. Mengikuti anjuran kesehatan dari dokter
                        </div>
                        <div class="bg-[#299BB8] text-white px-6 py-6 rounded-xl text-base font-bold md:text-[19px] lg:text-[24px]">
                            3. Memberi informasi mengenai riwayat kesehatan kepada dokter Anda
                        </div>
                        <div class="bg-[#299BB8] text-white px-6 py-6 rounded-xl text-base font-bold md:text-[19px] lg:text-[24px]">
                            4. Menjaga pola makan dan kehidupan yang sehat
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <footer class="container-open-sans bg-[#78C8DD] px-10 py-10 mt-20 rounded-tl-4xl rounded-tr-4xl">
            <div class="flex justify-between flex-wrap gap-8">
                <div>
                    <h4 class="font-bold mb-2 text-[24px]">Butuh Bantuan?</h4>
                    <p class="mb-3">
                        Kami siap membantu! Jika ada pertanyaan atau butuh informasi lebih 
                        lanjut, hubungi tim kami untuk solusi terbaik.
                    </p>
                    <div class="flex items-center gap-2 mb-2">
                        <input 
                          v-model="footerEmail"
                          type="email" 
                          placeholder="Email"
                          class="text-[#3C646F] font-bold mt-2 p-2 rounded-md w-full max-w-xs focus:outline-none"
                          @keyup.enter="footerEmailSubmit"
                        />
                        <img 
                          src="@/assets/arrow-right.svg" 
                          alt="Arrow" 
                          class="mt-3 h-4 cursor-pointer"
                          @click="footerEmailSubmit"
                         />
                    </div>
                    
                    <img src="@/assets/footer-line.svg" class="w-89">
                </div>

                <div>
                    <h4 class="font-bold mb-2 text-[20px]">Halaman</h4>
                    <ul class="space-y-1 text-gray-800">
                        <li>Dashboard</li>
                        <li>Tentang Kami</li>
                        <li>FAQ</li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold mb-2 text-[20px]">Dukungan</h4>
                    <ul class="space-y-1 text-gray-800">
                        <li>Hubungi Kami</li>
                        <li>Pusat Dukungan</li>
                        <li>Privasi dan Kebijakan</li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold mb-2 text-[20px]">Mitra</h4>
                    <ul class="space-y-1 text-gray-800">
                        <li>Mitra Kami</li>
                        <li>Komunitas</li>
                        <li>Investor</li>
                    </ul>
                </div>
            </div>

            <div class="relative bg-[#CAEAF2] rounded-3xl mt-8 px-10 py-4">
                <div class="relative flex flex-wrap justify-between items-center max-w-7xl mx-auto">
                    <!-- Bottom Left -->
                    <img src="@/assets/occuhelp-full-logo.svg" alt="OccuHelp Logo" class="h-12"/>

                    <!-- Bottom Center -->
                    <p class="text-xs sm:text-sm md:text-base lg:text-lg font-bold text-center">
                        © 2025 OccuHelp. All rights reserved.
                    </p>

                    <!-- Bottom Right -->
                     <div class="flex gap-4">
                        <img src="@/assets/facebook.svg" alt="Facebook"
                            class="h-5 w-5 transition-transform transform hover:scale-125 cursor-pointer hover:content-[url('@/assets/facebook-hover.svg')]"/>
                        <img src="@/assets/twitter.svg" alt="Twitter"
                            class="h-5 w-5 transition-transform transform hover:scale-125 cursor-pointer hover:content-[url('@/assets/twitter-hover.svg')]"/>
                        <img src="@/assets/insta.svg" alt="Instagram"
                            class="h-5 w-5 transition-transform transform hover:scale-125 cursor-pointer hover:content-[url('@/assets/insta-hover.svg')]"/>
                        <img src="@/assets/whatsapp.svg" alt="Whatsapp"
                            class="h-5 w-5 transition-transform transform hover:scale-125 cursor-pointer hover:content-[url('@/assets/whatsapp-hover.svg')]"/>
                     </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
.container-nunito {
    font-family: "Nunito", sans-serif;
}

.container-open-sans {
    font-family: "Open Sans", sans-serif;
}
</style>

<script setup>
import Navbar from '@/componentVue/main/Navbar.vue'
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const footerEmail = ref('')
const router = useRouter()

function footerEmailSubmit() {
  if (footerEmail.value) {
    router.push({ path: '/contactus', query: { email: footerEmail.value } })
  }
}
</script>