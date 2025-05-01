<template>
    <div class="contactus">
        <header class="bg-[#CAEAF2] px-10 py-6 flex justify-between items-center">
            <div class="flex items-center gap-6">
                <img src="@/assets/occuhelp-full-logo.svg" alt="OccuHelp Full Logo" class="h-15"/>
            </div>
                
            <nav class="container-open-sans ml-auto hidden md:flex space-x-6 font-bold text-black">
                <router-link to="/" class="hover:text-[#305879]">Dashboard</router-link>
                <router-link to="/aboutus" class="hover:text-[#305879]">Tentang Kami</router-link>
                <router-link to="/ourservice" class="hover:text-[#305879]">Layanan Kami</router-link>
                <a href="#" class="text-[#305879] border-b-2 border-sky-800">Kontak</a>
            </nav>

            <span class="h-10 ml-15">
                <img src="@/assets/profileBlack.svg" class="h-8"/>
            </span>
        </header>

        <section class="relative z-10 max-w-8xl ml-8 mt-10 mb-10 text-left px-4 ">
            <h2 class="container-nunito font-bold sm:text-[24px] md:text-[32px] lg:text-[36px]">Terima kasih atas ketertarikan Anda kepada kami.</h2>
            <p class="container-open-sans font-semibold sm:text-[14px] md:text-[16px] lg:text-[20px]">
                Jika Anda memiliki pertanyaan, ingin memberikan masukan, atau membutuhkan informasi lebih lanjut, jangan ragu untuk menghubungi kami. Kami dengan senang hati siap membantu Anda.
            </p>
        </section>

        <section class="bg-[#E6F6F9] px-10 py-14 relative overflow-hidden rounded-xl mx-8 lg:mx-20">
            <div class="max-w-6xl mx-auto flex flex-row lg:flex-row items-center justify-between gap-10">
                <div class="container-open-sans sm:text-[14px] md:text-[18px] lg:text-[20px] w-full  sm:w-1/2 md:w-1/2 lg:w-1/2 space-y-4">
                    <input
                    v-model="email"
                       type="email"
                       placeholder="Email"
                       class="w-full p-3 rounded-md bg-[#299BB8] placeholder-[#ABE0ED] text-white font-semibold focus:outline-none"
                    />
                    <input
                       type="text"
                       placeholder="Nomor Telepon"
                       class="w-full p-3 rounded-md bg-[#299BB8] placeholder-[#ABE0ED] text-white font-semibold focus:outline-none"
                    />
                    <textarea
                       rows="5"
                       placeholder="Ketik Pesan Anda di sini..."
                       class="w-full p-3 rounded-md bg-[#299BB8] placeholder-[#ABE0ED] text-white font-semibold focus:outline-none"
                    ></textarea>
                    <button class="w-full text-black py-2 border border-black rounded-md font-semibold hover:bg-[#C9EBF3] hover:border-[#C9EBF3] transition duration-200">
                        Kirim
                    </button>
                </div>

                <div class="w-full sm:w-1/2 md:w-1/2 lg:w-1/2 flex justify-center">
                    <img src="@/assets/contact-us-image.svg" alt="Contact Us Image" 
                         class="max-w-sm w-full"
                    />
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
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const email = ref('')

onMounted(() => {
  if (route.query.email) {
    email.value = route.query.email
  }
})

const footerEmail = ref('')
// const router = useRoute()

function footerEmailSubmit() {
  if (footerEmail.value) {
    window.location.href = `/contactus?email=${encodeURIComponent(footerEmail.value)}`
  }
}
</script>