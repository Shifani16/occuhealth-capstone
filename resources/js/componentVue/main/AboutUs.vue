<template>
    <!-- <link href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet"> -->

    <div class="aboutus">
        <header class="bg-[#CAEAF2] px-10 py-6 flex justify-between items-center">
            <div class="flex items-center gap-6">
                <img src="@/assets/occuhelp-full-logo.svg" alt="OccuHelp Full Logo" class="h-15"/>
            </div>
                
            <nav class="ml-auto hidden md:flex space-x-6 font-bold text-black">
                <a href="#" class="hover:text-[#305879]">Dashboard</a>
                <a href="#" class="text-[#305879] border-b-2 border-sky-800">Tentang Kami</a>
                <a href="#" class="hover:text-[#305879]">Layanan Kami</a>
                <a href="#" class="hover:text-[#305879]">Kontak</a>
            </nav>

            <span class="h-10 ml-15">
                <img src="@/assets/profileBlack.svg" class="h-8"/>
            </span>
        </header>

        <!-- About Us: Introduction -->
        <section class="bg-[#8AD3E5] px-10 py-14 relative">
            <!-- <img src="@/assets/aboutus-bottom-half.svg" class="absolute bottom-0 left-0 w-full z-0" /> -->
            <img src="@/assets/aboutus-top-half.svg" class="absolute top-0 left-0 w-full  z-0" />
            <img src="@/assets/aboutus-bottom-right.svg" class="absolute bottom-0 right-0 w-60 z-10" />

            <div class="relative z-20 flex flex-col md:flex-row items-center justify-between max-w-7xl mx-auto gap-10">
                <div class="md:w-1/2 text-left">
                    <h1 class=" text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold mb-8 -mt-6">
                        KESEHATAN ANDA ADALAH
                        <span class="text-[#217F96]">PRIORITAS</span>
                        KAMI
                    </h1>

                    <p class="text-sm sm:text-base md:text-lg lg:text-2xl text-gray-700">
                        Kami menyediakan layanan pengelolaan data hasil MCU secara aman dan 
                        efisien untuk membantu Anda memantau kesehatan dengan lebih mudah.
                    </p>
                </div>

                <div class="md:w-1/2">
                    <img src="@/assets/aboutUsImage.svg" alt="About Us Illust" class="w-full max-w-md mx-auto h-92" />
                </div>
            </div>
        </section>

        <!-- About Us: Apa itu OccuHelp -->
         <section class="relative mt-20 px-4 sm:px-1 py-10 sm:py-16 md:py-24 bg-white min-h-[50vh] flex items-center">
            <div class="absolute inset-0 flex justify-center items-center z-0">
                <img src="@/assets/aboutus-occuhelp-bg.svg" alt="Background Apa Itu OccuHelp" class="max-h-[90%] object-contain"/>
            </div>

            <div class="relative z-10 max-w-4xl mx-auto text-left px-4 sm:px-44 md:px-60">
                <h2 class="text-4xl font-bold mb-6">Apa itu OccuHelp?</h2>
                <p class="text-gray-700 text-justify">
                    OccuHelp adalah platform digital yang memudahkan tenaga kesehatan 
                    dalam mengelola data Medical Check-Up pekerja, menawarkan solusi 
                    efisien untuk pencatatan dan pemantauan kesehatan pekerja.
                </p>
            </div>
        </section>

        <section>
            <div class="flex justify-center gap-10 mt-20 mb-30 flex-wrap">
                <!-- Visi -->
                <div class="card hoverable-card group bg-[#95D4E4] p-8 rounded-lg flex-1 min-w-[250px] max-w-[450px] transition-all duration-300">
                    <img src="@/assets/visionBlack.svg" alt="Visi" class="icon-img mx-auto h-15 mb-5 mt-1 transition-all duration-300" />
                    <h3 class="text-center text-[24px] font-bold mb-2">VISI</h3>
                    <p class="text-gray-700 text-justify text-[20px]">
                        Visi kami adalah mempermudah tenaga kesehatan mengelola data 
                        Medical Check-Up pekerja secara efisien dan akurat, serta mendukung 
                        kesehatan pekerja melalui sistem yang mudah diakses dan terpercaya.
                    </p>
                </div>  
                
                <!-- Misi -->
                <div class="card hoverable-card group bg-[#95D4E4] p-8 rounded-lg flex-1 min-w-[250px] max-w-[450px] transition-all duration-300">
                    <img src="@/assets/missionBlack.svg" alt="Misi" class="icon-img mx-auto h-15 mb-5 mt-1 transition-all duration-300" />
                    <h3 class="text-center text-[24px] font-bold mb-2">MISI</h3>
                    <p class="text-gray-700 text-justify text-[20px]">
                        Misi kami adalah menyediakan platform teknologi untuk memudahkan 
                        tenaga medis mengelola data kesehatan pekerja, mengurangi beban 
                        administratif, dan meningkatkan akurasi pengelolaan data.
                    </p>
                </div>  
            </div>

            <div class="relative bg-[#E6F6F9] p-10 rounded-xl max-w-5xl mx-auto h-min-h-[200px] sm:min-h-[350px] md:min-h-[150px] flex items-center justify-center gap-10 overflow-visible">
                <img src="@/assets/aboutUsPerson.svg" alt="About Us Person" 
                    class="absolute mr-150 -top-10 sm:-top-20 h-[300px] sm:h-[400px] md:h-[500px] lg:h-[430px] object-contain rounded-xl"
                />
                <div class="ml-90 text-left">
                    <h3 class="text-[48px] font-bold mb-4">Kenapa harus OccuHelp?</h3>
                    <p class="text-gray-700 text-justify">
                        OccuHelp mengatasi tantangan pengelolaan data kesehatan pekerja 
                        dengan solusi teknologi praktis, memungkinkan tenaga medis fokus 
                        pada perawatan pasien tanpa khawatir administrasi data.
                    </p>
                </div>
            </div>
        </section>

        <footer class="bg-[#78C8DD] px-10 py-10 mt-20 rounded-tl-4xl rounded-tr-4xl">
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

<script setup></script>

<style scoped>
.hoverable-card:hover {
  background: linear-gradient(to bottom, #CAEAF2, #95D4E4);
}

.hoverable-card:hover .icon-img[alt="Visi"] {
  content: url("@/assets/visionHover.svg");
}

.hoverable-card:hover .icon-img[alt="Misi"] {
  content: url("@/assets/missionHover.svg");
}
</style>