<template>
    <div class="ourservice">
        <header class="bg-[#CAEAF2] px-10 py-6 flex justify-between items-center">
            <div class="flex items-center gap-6">
                <img src="@/assets/occuhelp-full-logo.svg" alt="OccuHelp Full Logo" class="h-15"/>
            </div>
                
            <nav class="container-open-sans ml-auto hidden md:flex space-x-6 font-bold text-black">
                <router-link to="/" class="hover:text-[#305879]">Dashboard</router-link>
                <router-link to="/aboutus" class="hover:text-[#305879]">Tentang Kami</router-link>
                <a href="#" class="text-[#305879] border-b-2 border-sky-800">Layanan Kami</a>
                <router-link to="/contactus" class="hover:text-[#305879]">Kontak</router-link>
            </nav>

            <span class="h-10 ml-15">
                <img src="@/assets/profileBlack.svg" class="h-8"/>
            </span>
        </header>

        <section class="relative flex justify-center items-start px-4 md:px-0 my-30">
            <img
                src="@/assets/our-service-image.svg"
                alt="Our Service Image"
                class="absolute -top-20 md:-top-24 z-10 w-72 md:w-96 lg:w-100"
            />

            <div class="bg-[#E6F6F9] rounded-3xl pt-40 pb-8 px-6 md:px-10 max-w-5xl w-full">
                <h2 class="container-nunito md:text-[30px] lg:text-[48px] font-bold text-black mb-4">
                    Apa itu <span class="whitespace-nowrap">Medical Check-Up (MCU)</span> <span class="text-[#217F96]">berbasis Okupasi?</span>
                </h2>
                <p class="container-open-sans text-justify text-sm md:text-base text-[#1B1B1B] leading-relaxed">
                    MCU (Medical Check-Up) berbasis okupasi adalah pemeriksaan medis untuk memastikan seseorang memiliki kondisi kesehatan yang memadai guna menjalankan tugas pekerjaan secara aman dan efektif. Pemeriksaan ini disesuaikan dengan jenis pekerjaan, mengingat tiap pekerjaan memiliki risiko kesehatan berbeda.
                </p>
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
                        <input type="email" placeholder="Email"
                        class="text-[#3C646F] font-bold mt-2 p-2 rounded-md w-full max-w-xs focus:outline-none"/>
                        <img src="@/assets/arrow-right.svg" alt="Arrow" class="mt-3 h-4" />
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