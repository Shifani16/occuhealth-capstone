<template>
    <aside
        :class="[
            'transition-all duration-300 bg-white shadow-md',
            collapsed ? 'w-20' : 'w-64',
        ]"
    >
        <div class="flex items-center justify-between p-4">
            <img
                v-if="!collapsed"
                src="@/assets/occuhelp-full-logo.svg"
                alt="OccuHelp"
                class="h-13"
            />
            <img
                :src="collapsed ? arrowRight : arrowLeft"
                class="ml-2 min-h-7 cursor-pointer"
                @click="toggleSidebar"
            />
        </div>

        <nav class="container-open-sans font-bold text-[18px]">
            <SidebarItem
                icon="dashboard.svg"
                text="Dashboard"
                :collapsed="collapsed"
            />
            <SidebarItem
                icon="dashboard-tentang-kami.svg"
                text="Tentang Kami"
                :collapsed="collapsed"
                @navigate="handleNavigation"
            />
            <SidebarItem
                icon="dashboard-pasien.svg"
                text="Pasien"
                :collapsed="collapsed"
                :active="active === 'Pasien'"
                @navigate="handleNavigation"
            />
            <SidebarItem
                icon="dashboard-hasil-mcu.svg"
                text="Hasil MCU"
                :collapsed="collapsed"
                :active="active === 'HasilMCU'"
                @navigate="handleNavigation"
            />
            <SidebarItem
                icon="dashboard-rekapitulasi.svg"
                text="Rekapitulasi"
                :collapsed="collapsed"
                :active="active === 'Rekapitulasi'"
                @navigate="handleNavigation"
            />
            <SidebarItem
                icon="dashboard-report.svg"
                text="Laporan"
                :collapsed="collapsed"
                :active="active === 'Laporan'"
                @navigate="handleNavigation"
            />
            <SidebarItem
                icon="dashboard-layanan.svg"
                text="Layanan Kami"
                :collapsed="collapsed"
            />
            <SidebarItem
                icon="dashboard-kontak.svg"
                text="Kontak"
                :collapsed="collapsed"
            />
        </nav>

        <div class="mt-auto p-4 py-5 container-open-sans font-bold">
            <div class="flex items-center gap-4 mb-6">
                <img src="@/assets/profileBlack.svg" class="h-10" />
                <span v-if="!collapsed" class="text-[20px]">{{
                    user.name
                }}</span>
            </div>

            <button
                @click="handleNavigation('Keluar')"
                @mouseover="hovering = true"
                @mouseleave="hovering = false"
                class="w-full border rounded-2xl py-2 flex items-center justify-center gap-3 text-[#3393AD] text-[18px] max-w-[190px]"
                :class="{
                    'bg-[#3393AD] text-white': hovering,
                    'border-[#3393AD]': true,
                }"
            >
                <img :src="hovering ? logoutHover : logoutNormal" class="h-5" />
                <span v-if="!collapsed" :class="{ 'text-white': hovering }"
                    >Keluar</span
                >
            </button>
        </div>
    </aside>

    <div
        v-if="showLogoutPopup"
        class="fixed inset-0 bg-[rgba(0,0,0,0.3)] flex items-center justify-center z-50"
    >
        <div class="bg-[#27394B] p-6 rounded-2xl shadow-md w-96 text-center">
            <img src="@/assets/circle-cancel.svg" class="w-20 mx-auto mb-4" />
            <p class="text-white font-medium mb-4">
                Apakah Anda yakin ingin keluar?
            </p>
            <div class="flex justify-center gap-4">
                <button
                    @click="showLogoutPopup = false"
                    class="border border-white text-white px-4 py-2 rounded hover:bg-white hover:text-[#27394B] font-semibold"
                >
                    Batal
                </button>
                <button
                    @click="logout"
                    class="border border-white text-white px-4 py-2 rounded hover:bg-white hover:text-[#27394B] font-semibold"
                >
                    Ya
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import SidebarItem from "./SidebarItem.vue";

import arrowRight from "@/assets/double-arrow-right.svg";
import arrowLeft from "@/assets/double-arrow-left.svg";
import logoutNormal from "@/assets/logout.svg";
import logoutHover from "@/assets/logout-hover.svg";

import { onMounted } from "vue";

const user = ref({});

const props = defineProps({
    active: String,
});

const collapsed = ref(false);
const hovering = ref(false);
const router = useRouter();
const showLogoutPopup = ref(false);

function toggleSidebar() {
    collapsed.value = !collapsed.value;
}

onMounted(() => {
    const storedUser = localStorage.getItem("user");
    if (storedUser) {
        user.value = JSON.parse(storedUser);
    }
});

function logout() {
    showLogoutPopup.value = false;
    router.push("/login");
}

function handleNavigation(item) {
    switch (item) {
        case "Pasien":
            router.push("/pasien");
            break;
        case "Tentang Kami":
            router.push("/aboutus");
            break;
        case "Keluar":
            showLogoutPopup.value = true;
            break;
        case "Hasil MCU":
            router.push("/hasilmcu");
            break;
        case "Rekapitulasi":
            router.push("/rekapitulasi");
            break;
        case "Laporan":
            router.push("/laporan");
            break;
        case "Kontak":
            router.push("/contactus");
            break;
        case "Layanan Kami":
            router.push("/ourservice");
            break;
    }
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
