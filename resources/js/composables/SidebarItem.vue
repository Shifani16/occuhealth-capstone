<template>
  <div
  @click="handleClick"
  class="flex items-center gap-2 rounded cursor-pointer hover:bg-[#C9EBF3] hover:text-[#27394B]"
  :class="{ 'bg-[#C1D6E6] container-open-sans font-bold text-[#305879]': active }"
  >
  <img :src="active ? icons[`${icon.replace('.svg', '')}-clicked.svg`] : icons[icon]" 
      class="max-w-7 h-12" 
  />
  <span v-if="!collapsed">{{ text }}</span>
  </div>
</template>
  
<script setup>
import { useRouter } from 'vue-router'

import dashboardIcon from '@/assets/dashboard.svg'
import dashboardIconClicked from '@/assets/dashboard-clicked.svg'
import dashboardHasilMCU from '@/assets/dashboard-hasil-mcu.svg'
import dashboardHasilMCUClicked from '@/assets/dashboard-hasil-mcu-clicked.svg'
import dashboardKontak from '@/assets/dashboard-kontak.svg'
import dashboardLayanan from '@/assets/dashboard-layanan.svg'
import dashboardPasien from '@/assets/dashboard-pasien.svg'
import dashboardPasienClicked from '@/assets/dashboard-pasien-clicked.svg'
import dashboardRekapitulasi from '@/assets/dashboard-rekapitulasi.svg'
import dashboardRekapitulasiClicked from '@/assets/dashboard-rekapitulasi-clicked.svg'
import dashboardReport from '@/assets/dashboard-report.svg'
import dashboardReportClicked from '@/assets/dashboard-report-clicked.svg'
import dashboardTentangKami from '@/assets/dashboard-tentang-kami.svg'

const icons = {
  'dashboard.svg': dashboardIcon,
  'dashboard-clicked.svg': dashboardIconClicked,
  'dashboard-hasil-mcu.svg' : dashboardHasilMCU,
  'dashboard-hasil-mcu-clicked.svg' : dashboardHasilMCUClicked,
  'dashboard-kontak.svg' : dashboardKontak,
  'dashboard-layanan.svg' : dashboardLayanan,
  'dashboard-pasien.svg' : dashboardPasien,
  'dashboard-pasien-clicked.svg' : dashboardPasienClicked,
  'dashboard-rekapitulasi.svg' : dashboardRekapitulasi,
  'dashboard-rekapitulasi-clicked.svg' : dashboardRekapitulasiClicked,
  'dashboard-report.svg' : dashboardReport,
  'dashboard-report-clicked.svg' : dashboardReportClicked,
  'dashboard-tentang-kami.svg' : dashboardTentangKami
  
}

const props = defineProps({
  icon: String,
  text: String,
  active: Boolean,
  collapsed: Boolean
})

const router = useRouter()
const emit = defineEmits(['navigate'])

function handleClick() {
  emit('navigate', props.text)
}


</script>

<style scoped>
.container-nunito {
    font-family: "Nunito", sans-serif;
}

.container-open-sans {
    font-family: "Open Sans", sans-serif;
}
</style>