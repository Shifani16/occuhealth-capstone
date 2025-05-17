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
            <LeftBar active="Dashboard" />
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

import LeftBar from '../../../composables/LeftBar.vue';
import RekapChart from '../../../composables/RekapChart.vue';
</script>