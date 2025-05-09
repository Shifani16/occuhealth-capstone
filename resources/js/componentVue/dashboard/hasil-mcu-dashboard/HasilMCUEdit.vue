<template>
    <div>
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
            <LeftBar active="HasilMCU" />

            <main>
                <h1 class="text-[48px] font-bold mb-6 ml-7 container-nunito">
                    Edit Hasil Medical Check Up Pasien
                </h1>

                <div class="ml-7 flex justify-between items-center max-w-7xl">
                    <h2 class="container-nunito text-[32px] font-bold mb-1">{{ pasien.nama }}</h2>
                    <div
                    class="container-open-sans bg-[#185C6D] text-white text-sm rounded-full px-4 py-1 text-[16px] font-bold whitespace-nowrap"
                    >
                        {{ pasien.no_rekam_medis }}
                    </div>
                </div>

                <hr class="ml-7 mt-3 mb-3 border-t-3 border-[#185C6D]" />

                <div class="ml-7 mr-7">
                    <table class="w-full text-left border-collapse container-open-sans text-[16px]">
                        <thead>
                            <tr class="bg-[#E6F6F9] text-[#185C6D] font-bold">
                                <th class="py-3 px-4 border border-[#C2D6DB] w-[50%]">Pemeriksaan</th>
                                <th class="py-3 px-4 border border-[#C2D6DB] w-[25%]"></th>
                                <th class="py-3 px-4 border border-[#C2D6DB] w-[25%]"></th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr
                                v-for="(hasil, index) in hasilMCU"
                                :key="index"
                                class="even:bg-[#E6F6F9] odd:bg-white"
                            >
                                <td class="py-2 px-4">{{ hasil.pemeriksaan }}</td>
                                <td class="py-2 px-4">{{ hasil.hasil }}</td>
                                <td class="py-2 px-4">
                                    <input v-model="hasilMCUEdit[index].hasil" class="w-full border px-2 py-1 rounded">
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="mt-10">
                        <label class="block text-black text-lg font-semibold mb-2 container-open-sans">Saran</label>
                        <textarea
                            v-model="saran"
                            placeholder="Ketik saran untuk pasien di sini"
                            class="w-full h-32 bg-[#E6F6F9] text-black rounded p-3 resize-none"
                        ></textarea>
                    </div>

                    <div class="flex justify-end gap-4 mt-14 text-[18px] container-open-sans">
                        <button
                            @click="cancel"
                            @mouseover="hoveringBatal = true"
                            @mouseleave="hoveringBatal = false"
                            :class="[
                                'border font-semibold px-6 py-2 rounded flex items-center gap-2 transition',
                                hoveringBatal ? 'bg-[#3393AD] text-white border-[#3393AD]' : 'text-[#3393AD] border-[#3393AD]'
                            ]"
                          >
                            <img :src="hoveringBatal ? batalHoverIcon : batalIcon" class="h-5" />
                            Batal
                        </button>

                        <button
                            @click="save"
                            @mouseover="hoveringSimpan = true"
                            @mouseleave="hoveringSimpan = false"
                            :class="[
                                'border border-[#3393AD] text-[#3393AD] font-semibold px-6 py-2 rounded flex items-center gap-2 transition',
                                hoveringSimpan ? 'bg-[#E4EBF1] border-[#E4EBF1]' : ''
                            ]"
                        >
                            <img src="@/assets/simpan.svg" class="h-5" />
                            Simpan
                        </button>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import LeftBar from '../../../composables/LeftBar.vue';

const route = useRoute();
const router = useRouter();

const hoveringBatal = ref(false);
const hoveringSimpan = ref(false);
const showSuccessPopup = ref(false);

const hasilMCU = ref([]);
const hasilMCUEdit = ref([]);
const saran = ref('');

const batalIcon = "@/assets/batal.svg";
const batalHoverIcon = "@/assets/batal-hover.svg";

onMounted(() => {
  hasilMCU.value = hasilData;
  hasilMCUEdit.value = JSON.parse(JSON.stringify(hasilData));
});

function cancel() {
  router.back();
}

function save() {
  console.log("Data baru:", hasilMCUEdit.value, "Saran:", saran.value);
  showSuccessPopup.value = true;
}

function goToPasien() {
  router.push("/hasilmcu");
}

// Data dummy 
const hasilData = [
  { pemeriksaan: "Kreatinin", hasil: "0,98", tanggal: "29/03/2025" },
  { pemeriksaan: "eGFR", hasil: "100", tanggal: "29/03/2025" },
  { pemeriksaan: "Ureum", hasil: "27,3", tanggal: "29/03/2025" },
  { pemeriksaan: "Glukosa Puasa", hasil: "74,4", tanggal: "29/03/2025" },
  { pemeriksaan: "Anti Hbs", hasil: "550", tanggal: "29/03/2025" },
  { pemeriksaan: "HBsAg", hasil: "Negatif", tanggal: "29/03/2025" },
  { pemeriksaan: "Asam Urat", hasil: "6,8", tanggal: "29/03/2025" },
  { pemeriksaan: "Basofil", hasil: "0,5", tanggal: "29/03/2025" },
  { pemeriksaan: "Eosinofil", hasil: "1,3", tanggal: "29/03/2025" },
  { pemeriksaan: "Hemoglobin", hasil: "12,6", tanggal: "29/03/2025" },
  { pemeriksaan: "Hematokrit", hasil: "40,8", tanggal: "29/03/2025" },
  { pemeriksaan: "Trombosit", hasil: "174", tanggal: "29/03/2025" },
  { pemeriksaan: "Eritrosit", hasil: "4,57", tanggal: "29/03/2025" },
  { pemeriksaan: "Leukosit", hasil: "8,7", tanggal: "29/03/2025" },
  { pemeriksaan: "MCH", hasil: "28,4", tanggal: "29/03/2025" },
  { pemeriksaan: "MCHC", hasil: "34,6", tanggal: "29/03/2025" },
  { pemeriksaan: "MCV", hasil: "96,6", tanggal: "29/03/2025" },
  { pemeriksaan: "Limfosit", hasil: "34,4", tanggal: "29/03/2025" },
  { pemeriksaan: "Monosit", hasil: "3,8", tanggal: "29/03/2025" },
  { pemeriksaan: "Neutrofil", hasil: "64,7", tanggal: "29/03/2025" },
  { pemeriksaan: "Neutrofil Limfosit", hasil: "2", tanggal: "29/03/2025" },
  { pemeriksaan: "Kolesterol HDL", hasil: "53,8", tanggal: "29/03/2025" },
  { pemeriksaan: "Kolesterol LDL", hasil: "104,8", tanggal: "29/03/2025" },
  { pemeriksaan: "Trigliserid", hasil: "115,1", tanggal: "29/03/2025" },
  { pemeriksaan: "SGOT", hasil: "2,7", tanggal: "29/03/2025" },
  { pemeriksaan: "SGTP", hasil: "18,9", tanggal: "29/03/2025" },
  { pemeriksaan: "Kolestrol", hasil: "190,2", tanggal: "29/03/2025" },
  { pemeriksaan: "PH", hasil: "5,7", tanggal: "29/03/2025" },
  { pemeriksaan: "Warna", hasil: "Kuning", tanggal: "29/03/2025" },
  { pemeriksaan: "Kejernihan", hasil: "Jernih", tanggal: "29/03/2025" },
  { pemeriksaan: "Leukosit (Urine)", hasil: "Negatif", tanggal: "29/03/2025" },
  { pemeriksaan: "Eritosit (Urine)", hasil: "Negatif", tanggal: "29/03/2025" },
  { pemeriksaan: "Epitel", hasil: "Sedikit", tanggal: "29/03/2025" },
  { pemeriksaan: "Bakteri", hasil: "Negatif", tanggal: "29/03/2025" },
  { pemeriksaan: "Silinder", hasil: "Negatif", tanggal: "29/03/2025" },
  { pemeriksaan: "Kristal", hasil: "Negatif", tanggal: "29/03/2025" },
  { pemeriksaan: "Berat Jenis", hasil: "1,014", tanggal: "29/03/2025" },
  { pemeriksaan: "Protein (Urine)", hasil: "Negatif", tanggal: "29/03/2025" },
  { pemeriksaan: "Glukosa (Urine)", hasil: "Negatif", tanggal: "29/03/2025" },
  { pemeriksaan: "Keton", hasil: "Negatif", tanggal: "29/03/2025" },
  { pemeriksaan: "Darah (Urine)", hasil: "Negatif", tanggal: "29/03/2025" },
  { pemeriksaan: "Bilirubin", hasil: "Negatif", tanggal: "29/03/2025" },
  { pemeriksaan: "Urobilinogen", hasil: "Normal", tanggal: "29/03/2025" },
  { pemeriksaan: "Nitrit", hasil: "Negatif", tanggal: "29/03/2025" },
  { pemeriksaan: "Leukosit Esteras", hasil: "Negatif", tanggal: "29/03/2025" },
  { pemeriksaan: "Albumin Kreatinin", hasil: "26,7", tanggal: "29/03/2025" },
  { pemeriksaan: "Buta Warna", hasil: "Normal", tanggal: "29/03/2025" },
];
</script>

<style scoped>
.container-nunito {
  font-family: 'Nunito', sans-serif;
}
.container-open-sans {
  font-family: 'Open Sans', sans-serif;
}
</style>