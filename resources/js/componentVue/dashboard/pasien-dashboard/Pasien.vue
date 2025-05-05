<template>
    <div class="overflow-x-hidden">
    <header class="bg-[#CAEAF2] px-5 py-3 flex justify-between items-center">
        <div class="flex items-center gap-6">
                <img src="@/assets/occuhelp-full-logo.svg" alt="OccuHelp Full Logo" class="h-12"/>
        </div>
    </header>

    <div class="flex min-h-screen">
        <!-- Left Bar -->
        <aside :class="['transition-all duration-300 bg-white shadow-md', collapsed ? 'w-20' : 'w-64']">
            <div class="flex items-center justify-between p-4">
                <img src="@/assets/occuhelp-full-logo.svg" alt="OccuHelp" class="h-13" v-if="!collapsed"/>
                <img
                    :src="collapsed ? arrowRight : arrowLeft"
                    class="ml-2 h-7 cursor-pointer"
                    @click="toggleSidebar"
                />
            </div>

            <nav class="container-open-sans font-bold text-[18px]">
                <SidebarItem icon ='dashboard.svg' text="Dashboard" :collapsed="collapsed"></SidebarItem>
                <SidebarItem icon ='dashboard-tentang-kami.svg' text="Tentang Kami" :collapsed="collapsed"></SidebarItem>
                <SidebarItem icon ='dashboard-pasien.svg' text="Pasien" active :collapsed="collapsed"></SidebarItem>
                <SidebarItem icon ='dashboard-hasil-mcu.svg' text="Hasil MCU" :collapsed="collapsed"></SidebarItem>
                <SidebarItem icon ='dashboard-rekapitulasi.svg' text="Rekapitulasi" :collapsed="collapsed"></SidebarItem>
                <SidebarItem icon ='dashboard-report.svg' text="Report":collapsed="collapsed"></SidebarItem>
                <SidebarItem icon ='dashboard-layanan.svg' text="Layanan Kami" :collapsed="collapsed"></SidebarItem>
                <SidebarItem icon ='dashboard-kontak.svg' text="Kontak":collapsed="collapsed"></SidebarItem>
            </nav>

            <div class="mt-auto p-4 py-5 container-open-sans font-bold">
                <div class="flex items-center gap-4 mb-6">
                    <img src="@/assets/profileBlack.svg" class="h-10"/>
                    <span v-if="!collapsed" class="text-[20px]">Nadira</span>
                </div>

                <button
                   @mouseover="hovering = true"
                   @mouseleave="hovering = false"
                   class="w-full border rounded-2xl py-2 flex items-center justify-center gap-3 text-[#3393AD] text-[18px] max-w-[190px]"
                   :class="{
                      'bg-[#3393AD] text-white': hovering,
                      'border-[#3393AD]': true
                   }"
                >
                   <img :src="hovering ? logoutHover : logout" class="h-5"/>
                   <span v-if="!collapsed" :class="{ 'text-white': hovering }">Keluar</span>
                </button>
            </div>
        </aside>

        <main class="flex-1 bg-[#FFFFFF] p-6">
            <h1 class="text-[48px] font-bold mb-6 ml-7 container-nunito">Pasien</h1>
            <div class="container-open-sans ml-7 flex flex-wrap items-center gap-4">
                <div class="relative inline-flex items-center">
                    <label class="text-[14px] font-semibold mr-2">Show</label>
                    <select v-model="entriesToShow"
                            class="appearance-none bg-[#C9EBF3] border-[#C9EBF3] text-black px-2 py-2 rounded hover:bg-white border hover:border-[#299BB8] pr-6">
                        <option :value="5">5</option>
                        <option :value="10">10</option>
                        <option :value="15">15</option>
                        <option :value="20">20</option>
                        <option :value="30">30</option>
                        <option :value="40">40</option>
                        <option :value="50">50</option>
                        <option :value="100">100</option>
                    </select>
                    <span class="ml-2 text-[14px] font-semibold">entries</span>
                    <!-- <img src="@/assets/arrow-down.svg" class="absolute right-2 top-1/2 -translate-y-1/2 h-3 pointer-events-none" /> -->
                </div>

                <div>
                    <input
                        type="text"
                        placeholder="Cari..."
                        class="border border-[#299BB8] px-4 py-2 rounded bg-white w-270 placeholder-[#299BB8] text-[#299BB8]"
                        style="background-image: url('@/assets/search.svg'); background-repeat: no-repeat; background-position: right 0.5rem center;" />
                </div>
            </div>

            <div class="mt-6 flex items-center justify-center gap-3">
                <span class="text-[#8AD3E5] ml-1">Sebelumnya</span>
                <span class="cursor-pointer text-white bg-[#8AD3E5] px-3 py-1 rounded">1</span>
                <span class="cursor-pointer text-black bg-[#F2FAFC] px-3 py-1 rounded">2</span>
                <span class="cursor-pointer text-black bg-[#F2FAFC] px-3 py-1 rounded">3</span>
                <span class="text-[#8AD3E5] ml-1">Selanjutnya</span>
            </div>

            <div class="w-full bg-white p-6 rounded-md shadow">
                <div class="overflow-x-auto w-full max-w-full">
                <table class="text-left border-collapse w-full">
                    <thead class="bg-white">
                        <tr class="container-open-sans font-bold">
                            <th class="px-4 py-2 sticky top-0 z-10"> No.</th>
                            <th class="px-4 py-2 sticky top-0 z-10"> 
                                <div class="flex items-center gap-2">
                                    Nama Pasien
                                    <img src="@/assets/sorting.svg" class="h-4 cursor-pointer"/>
                                </div>
                            </th>
                            <th class="px-4 py-2 sticky top-0 z-10 flex items-center gap-2"> 
                                Tanggal Pemeriksaan
                                <img src="@/assets/sorting.svg" class="h-4 cursor-pointer"/>
                            </th>
                            <th class="px-4 py-2 sticky top-0 z-10"> No. Rekam Medis</th>
                            <th class="px-4 py-2 sticky top-0 z-10"> No. Pasien</th>
                            <th class="px-4 py-2 sticky top-0 z-10"> Jenis Kelamin</th>
                            <th class="px-4 py-2 sticky top-0 z-10"> 
                                <div class="flex items-center gap-2">
                                    Umur
                                    <img src="@/assets/sorting.svg" class="h-4 cursor-pointer"/>
                                </div>
                            </th>
                            <th class="px-4 py-2"> TTL</th>
                            <th class="px-4 py-2"> Alamat</th>
                            <th class="px-4 py-2"> Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(item, index) in displayedPasienList"
                            :key="item.no_pasien"
                            class="container-open-sans odd:bg-[#E6F6F9]"
                        >
                            <td class="px-4 py-2">{{  index + 1 }}</td>
                            <td class="px-4 py-2">{{  item.nama }}</td>
                            <td class="px-4 py-2">{{  item.tanggal_pemeriksaan }}</td>
                            <td class="px-4 py-2">{{  item.no_rekam_medis }}</td>
                            <td class="px-4 py-2">{{  item.no_pasien }}</td>
                            <td class="px-4 py-2">{{  item.jenis_kelamin }}</td>
                            <td class="px-4 py-2">{{  item.umur }}</td>
                            <td class="px-4 py-2">{{  item.tempat_tanggal_lahir }}</td>
                            <td class="px-4 py-2">{{  item.alamat }}</td>
                            <td class="px-4 py-2 space-x-2 flex">
                                <button class="cursor-pointer h-10">
                                    <img src="@/assets/action-info.svg" title="Lihat Info Pasien" class="object-contain"/>
                                </button>
                                <button class="cursor-pointer h-10">
                                    <img src="@/assets/action-edit.svg" title="Edit Data Pasien" class="object-contain"/>
                                </button>
                                <button @click="showDeleteConfirm = true" class="cursor-pointer w-10 h-10 object-contain">
                                    <img src="@/assets/action-delete.svg" title="Hapus Data Pasien"/>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            </div>

            <hr class="my-4"/>
            <p class="text-left text-sm text-black font-bold text-[20px] mb-2">© 2025 OccuHelp. All Rights Reserved.</p>
        </main>
    </div>
    
    <!-- Pop Up -->
    <div v-if="showDeleteConfirm" class="fixed inset-0 bg-[rgba(0,0,0,0.3)] flex items-center justify-center z-50">
        <div class="bg-[#27394B] p-6 rounded-2xl shadow-md w-96 text-center">
            <img src="@/assets/circle-cancel.svg" class="w-20 mx-auto mb-4"/>
            <p class="text-white font-medium mb-4">Apakah Anda yakin ingin menghapus data pasien?</p>
            <div class="flex justify-center gap-4">
                <button @click="showDeleteConfirm = false" class="border border-white text-white px-4 py-2 rounded hover:bg-white hover:text-[#27394B] font-semibold">Batal</button>
                <button @click="confirmDelete" class="border border-white text-white px-4 py-2 rounded hover:bg-white hover:text-[#27394B] font-semibold">Ya</button>
            </div>
        </div>
    </div>

    <div v-if="showSuccessPopup" class="fixed inset-0 bg-[rgba(0,0,0,0.3)] flex items-center justify-center z-50">
        <div class="bg-[#27394B] p-6 rounded-2xl shadow-md w-96 text-center">
            <img src="@/assets/circle-check.svg" class="w-20 mx-auto mb-4" />
            <p class="text-white font-medium mb-4">Data pasien berhasil dihapus</p>
            <div class="flex justify-center">
                <button @click="showPopUp" class="border border-white text-white px-4 py-2 rounded hover:bg-white hover:text-[#27394B] font-semibold">Tutup</button>
            </div>
        </div>
    </div>
    </div>
</template>
 
<script setup>
import { ref, computed } from 'vue'
import SidebarItem from '../../../composables/SidebarItem.vue'

import arrowRight from '@/assets/double-arrow-right.svg'
import arrowLeft from '@/assets/double-arrow-left.svg'
import logout from '@/assets/logout.svg'
import logoutHover from '@/assets/logout-hover.svg'
import Actioninfo from '@/assets/action-info.svg'
import Actionedit from '@/assets/action-edit.svg'
import Actiondelete from '@/assets/action-delete.svg'

const collapsed = ref(false)
const hovering = ref(false)
const showPopUp = ref(false)
const showDeleteConfirm = ref(false)
const showSuccessPopup = ref(false)

const entriesToShow = ref(10)

function confirmDelete() {
  showDeleteConfirm.value = false
  showSuccessPopup.value = true
  setTimeout(() => {
    showSuccessPopup.value = false
  }, 2000)
}

function toggleSidebar() {
    collapsed.value = !collapsed.value
}

const displayedPasienList = computed(() => {
  return pasienList.value.slice(0, entriesToShow.value)
})

const pasienList = ref([
  {
    nama: 'Mydei',
    tanggal_pemeriksaan: '13/05/2022',
    no_rekam_medis: 'RM-20250329001',
    no_pasien: 'PSN-240102625',
    jenis_kelamin: 'Laki-Laki',
    umur: '28',
    tempat_tanggal_lahir: '1 April 1990',
    alamat: 'Jalan Meow No. 13'
    
  },
  {
    nama: 'Phainon',
    tanggal_pemeriksaan: '22/05/2022',
    no_rekam_medis: 'RM-20250329002',
    no_pasien: 'PSN-240102626',
    jenis_kelamin: 'Laki-Laki',
    umur: '28',
    tempat_tanggal_lahir: '1 Mei 1990',
    alamat: 'Jalan Yippee No. 13'
    
  },
])

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
  background-color: #8AD3E5;
  border-radius: 4px;
}
</style>