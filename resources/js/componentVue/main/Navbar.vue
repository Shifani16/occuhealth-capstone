<template>
    <header
        class="bg-[#CAEAF2] px-6 py-4 flex justify-between items-center relative"
    >
        <!-- Logo -->
        <div class="flex items-center gap-6">
            <img
                src="@/assets/occuhelp-full-logo.svg"
                alt="OccuHelp Full Logo"
                class="h-12"
            />
        </div>

        <!-- Hamburger Button -->
        <button
            @click="menuOpen = !menuOpen"
            class="sm:hidden focus:outline-none"
        >
            <svg
                class="w-6 h-6 text-black"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16"
                ></path>
            </svg>
        </button>

        <!-- Desktop Nav -->
        <nav
            class="container-open-sans ml-auto max-sm:!hidden sm:!flex sm:!flex-row space-x-6 font-bold text-black"
        >
            <!-- Modified Dashboard Link (Desktop) -->
            <router-link
                to="#"
                @click.prevent="handleDashboardClick"
                :class="linkClass('/pasien')"
                >Dashboard</router-link
            >

            <router-link to="/aboutus" :class="linkClass('/aboutus')"
                >Tentang Kami</router-link
            >
            <router-link to="/ourservice" :class="linkClass('/ourservice')"
                >Layanan Kami</router-link
            >
            <router-link to="/contactus" class="hover:text-[#305879]">
                <span
                    :class="
                        route.path === '/contactus'
                            ? 'border-b-2 border-sky-800 inline-block'
                            : 'inline-block'
                    "
                >
                    Kontak
                </span>
            </router-link>
        </nav>

        <!-- Profile Icon -->
        <span class="h-10 ml-4 max-sm:!hidden sm:!block">
            <img
                src="@/assets/profileBlack.svg"
                class="h-8 cursor-pointer"
                @click="goToLogin"
            />
        </span>

        <!-- Mobile Dropdown Menu -->
        <div
            v-if="menuOpen"
            class="absolute top-full right-0 w-full bg-[#CAEAF2] sm:hidden flex flex-col items-start px-6 py-4 space-y-3 font-bold text-black z-50"
        >
            <!-- Modified Dashboard Link (Mobile) -->
            <router-link
                to="#"
                @click.prevent="handleDashboardClick(); menuOpen = false;"
                :class="linkClass('/dashboard')"
                >Dashboard</router-link
            >

            <router-link
                @click="menuOpen = false"
                to="/aboutus"
                :class="linkClass('/aboutus')"
                >Tentang Kami</router-link
            >
            <router-link
                @click="menuOpen = false"
                to="/ourservice"
                :class="linkClass('/ourservice')"
                >Layanan Kami</router-link
            >
            <router-link @click="menuOpen = false" to="/contactus">
                <span
                    :class="
                        route.path === '/contactus'
                            ? 'border-b-2 border-sky-800 inline-block'
                            : 'inline-block'
                    "
                >
                    Kontak
                </span>
            </router-link>
            <img src="@/assets/profileBlack.svg" class="h-8 mt-2 cursor-pointer" @click="goToLogin(); menuOpen = false;" /> // Add click handler and close menu for mobile profile icon
        </div>
    </header>
</template>

<script setup>
import { ref } from "vue";
import { useRoute, useRouter } from "vue-router";

const route = useRoute();
const menuOpen = ref(false);
const router = useRouter(); // Get the router instance

const linkClass = (path) => {
    // This class logic remains the same, highlighting based on the target path (/pasien)
    return route.path === path
        ? "text-[#305879] border-b-2 border-[#305879] pb-1"
        : "hover:text-[#305879]";
};

// Function to navigate to the login page
const goToLogin = () => {
  router.push('/login');
};

// --- New function to handle Dashboard click ---
const handleDashboardClick = () => {
  // Check if user data exists in localStorage (indicates logged-in state)
  const user = localStorage.getItem('user');

  if (user) {
    router.push('/dashboard');
  } else {
    router.push('/login');
  }
};
// --- End of New function ---

</script>

<style scoped>
.container-open-sans {
    font-family: "Open Sans", sans-serif;
}
</style>