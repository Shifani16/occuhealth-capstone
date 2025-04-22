<template>
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <div class="aboutus">
        <header class="bg-[#CAEAF2] px-10 py-6 flex justify-between items-center">
            <div class="flex items-center gap-6">
                <img src="@/assets/occuhelp-full-logo.svg" alt="OccuHelp Full Logo" class="h-14"/>
                <nav class=" ml-60 space-x-6 font-bold text-black">
                    <a href="#" class="hover:text-[#305879]">Dashboard</a>
                    <a href="#" class="text-[#305879] border-b-2 border-sky-800">Tentang Kami</a>
                    <a href="#" class="hover:text-[#305879]">Layanan Kami</a>
                    <a href="#" class="hover:text-[#305879]">Kontak</a>
                </nav>
            </div>
                <span class="h-10">
                    <img src="@/assets/profileBlack.svg"/>
                </span>
        </header>

        <!-- About Us: Introduction -->
        <section class="bg-[#8AD3E5] px-10 py-14 relative">
            <!-- <img src="@/assets/aboutus-bottom-half.svg" class="absolute bottom-0 left-0 w-full z-0" /> -->
            <img src="@/assets/aboutus-top-half.svg" class="absolute top-0 left-0 w-full  z-0" />
            <img src="@/assets/aboutus-bottom-right.svg" class="absolute bottom-0 right-0 w-60 z-10" />

            <div class="relative z-20 flex flex-col md:flex-row items-center justify-between max-w-7xl mx-auto gap-10">
                <div class="md:w-1/2 text-left">
                    <h1 class=" -mt-15 text-5xl font-bold">
                        KESEHATAN ANDA ADALAH
                        <span class="text-[#217F96]">PRIORITAS</span>
                        KAMI
                    </h1>

                    <p class="mt-8 mb-10 text-gray-700 text-2xl">
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
         <section class="relative px-10 py-24 bg-white min-h-[50vh]">
            <div class="absolute inset-0 flex justify-center items-center z-0">
                <img src="@/assets/aboutus-occuhelp-bg.svg" alt="Background Apa Itu OccuHelp" class="max-h-[90%] object-contain"/>
            </div>

            <div class="relative z-10 max-w-4xl mx-auto text-left px-60">
                <h2 class="text-4xl font-bold mb-6">Apa itu OccuHelp?</h2>
                <p class="text-gray-700 text-justify">
                    OccuHelp adalah platform digital yang memudahkan tenaga kesehatan 
                    dalam mengelola data Medical Check-Up pekerja, menawarkan solusi 
                    efisien untuk pencatatan dan pemantauan kesehatan pekerja.
                </p>
            </div>
        </section>

        <section>
            <div class="flex justify-center gap-10 mb-16 flex-wrap">
                <!-- Visi -->
                <div class="bg-[#95D4E4] p-6 rounded-lg flex-1 min-w-[250px] max-w-sm">
                    <img src="@/assets/visionBlack.svg" alt="Visi" class="mx-auto h-15 mb-5 mt-1" />
                    <h3 class="text-center text-2xl font-bold mb-2">VISI</h3>
                    <p class="text-gray-700 text-justify">
                        Visi kami adalah mempermudah tenaga kesehatan mengelola data 
                        Medical Check-Up pekerja secara efisien dan akurat, serta mendukung 
                        kesehatan pekerja melalui sistem yang mudah diakses dan terpercaya.
                    </p>
                </div>  
                
                <!-- Misi -->
                <div class="bg-[#95D4E4] p-6 rounded-lg flex-1 min-w-[250px] max-w-sm">
                    <img src="@/assets/missionBlack.svg" alt="Misi" class="mx-auto h-15 mb-5 mt-1" />
                    <h3 class="text-center text-2xl font-bold mb-2">MISI</h3>
                    <p class="text-gray-700 text-justify">
                        Misi kami adalah menyediakan platform teknologi untuk memudahkan 
                        tenaga medis mengelola data kesehatan pekerja, mengurangi beban 
                        administratif, dan meningkatkan akurasi pengelolaan data.
                    </p>
                </div>  
            </div>

            <div class="flex items-center justify-center gap-10 bg-[#E6F6F9] p-10 rounded-xl max-w-5xl mx-auto">
                <img src="@/assets/aboutUsPerson.svg" alt="About Us Person" class="w-60 rounded-xl"/>
                <div class="text-left">
                    <h3 class="text-3xl font-bold mb-4">Kenapa harus OccuHelp?</h3>
                    <p class="text-gray-700 text-justify">
                        OccuHelp mengatasi tantangan pengelolaan data kesehatan pekerja 
                        dengan solusi teknologi praktis, memungkinkan tenaga medis fokus 
                        pada perawatan pasien tanpa khawatir administrasi data.
                    </p>
                </div>
            </div>
        </section>

         <footer class="bg-[#78C8DD] px-10 py-10 mt-20">
            <div class="flex justify-between flex-wrap gap-8">
                <div>
                    <h4 class="font-bold mb-2">Butuh Bantuan?</h4>
                    <p class="mb-3">
                        Kami siap membantu! Jika ada pertanyaan atau butuh informasi lebih 
                        lanjut, hubungi tim kami untuk solusi terbaik.
                    </p>
                    <p class="text-[#3C646F] font-bold mb-2">
                        Email
                    </p>
                    <img src="@/assets/footer-line.svg">
                    <input>
                </div>

                <div>
                    <h4 class="font-bold mb-2">Halaman</h4>
                    <ul class="space-y-1 text-gray-800">
                        <li>Dashboard</li>
                        <li>Tentang Kami</li>
                        <li>FAQ</li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold mb-2">Dukungan</h4>
                    <ul class="space-y-1 text-gray-800">
                        <li>Hubungi Kami</li>
                        <li>Pusat Dukungan</li>
                        <li>Privasi dan Kebijakan</li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold mb-2">Mitra</h4>
                    <ul class="space-y-1 text-gray-800">
                        <li>Mitra Kami</li>
                        <li>Komunitas</li>
                        <li>Investor</li>
                    </ul>
                </div>

            </div>
         </footer>
    </div>
</template>

<script setup></script>
<style scoped></style>