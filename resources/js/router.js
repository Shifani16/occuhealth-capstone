import { createRouter, createWebHistory } from 'vue-router';

import Login from './componentVue/auth/Login.vue';
import ForgotPass from './componentVue/auth/ForgotPass.vue';
import VerifyPass from './componentVue/auth/VerifyPass.vue';
import AboutUs from './componentVue/main/AboutUs.vue';
import ContactUs from './componentVue/main/ContactUs.vue';
import OurService from './componentVue/main/OurService.vue';
import Pasien from './componentVue/dashboard/pasien-dashboard/Pasien.vue';
import PasienDetail from './componentVue/dashboard/pasien-dashboard/PasienDetail.vue';
import PasienEdit from './componentVue/dashboard/pasien-dashboard/PasienEdit.vue';
import HasilMCU from './componentVue/dashboard/hasil-mcu-dashboard/HasilMCU.vue';
import HasilMCUDetail from './componentVue/dashboard/hasil-mcu-dashboard/HasilMCUDetail.vue';
import HasilMCUEdit from './componentVue/dashboard/hasil-mcu-dashboard/HasilMCUEdit.vue';
import Laporan from './componentVue/dashboard/laporan-dashboard/Laporan.vue';
import Rekapitulasi from './componentVue/dashboard/rekapitulasi-dashboard/Rekapitulasi.vue';
import Dashboard from './componentVue/dashboard/main-dashboard/Dashboard.vue';
import Admin from './componentVue/dashboard/admin-dashboard/Admin.vue';

const routes = [
  { path: '/', redirect: '/aboutus' },
  { path: '/login', name: 'Login', component: Login },
  { path: '/forgotpass', name: 'ForgotPass', component: ForgotPass },
  { path: '/verifypass', name: 'VerifyPass', component: VerifyPass },

  { path: '/aboutus', name: 'AboutUs', component: AboutUs },
  { path: '/contactus', name:'ContactUs', component: ContactUs},
  { path: '/ourservice', name:'OurService', component: OurService},

  { path: '/dashboard', name:'Dashboard', component: Dashboard, meta: { requiresAuth: true } },
  { path: '/pasien', name:'Pasien', component: Pasien, meta: { requiresAuth: true } },
  { path: '/pasien/:id', name:'PasienDetail', component: PasienDetail, meta: { requiresAuth: true } },
  { path: '/pasien/edit/:id', name:'PasienEdit', component: PasienEdit, meta: { requiresAuth: true } },
  { path: '/hasilmcu', name:'HasilMCU', component: HasilMCU, meta: { requiresAuth: true } },
  { path: '/hasilmcu/:id', name:'HasilMCUDetail', component: HasilMCUDetail, meta: { requiresAuth: true } },
  { path: '/hasilmcu/edit/:id', name:'HasilMCUEdit', component: HasilMCUEdit, meta: { requiresAuth: true } },
  { path: '/laporan', name: 'Laporan', component: Laporan, meta: { requiresAuth: true } },
  { path: '/rekapitulasi', name: 'Rekapitulasi', component: Rekapitulasi, meta: { requiresAuth: true } },
  {
    path: '/admin',
    name:'Admin',
    component: Admin,
    meta: { requiresAuth: true, requiresAdmin: true }
  },

  // { path: '/verify-reset/:user(.*)', redirect: '/' } 

  // { path: '/:catchAll(.*)', redirect: '/login' } 
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// --- Navigation Guard ---
router.beforeEach((to, from, next) => {
    console.log(`Navigating to: ${to.fullPath}`); 

    if (to.name === null) {
         console.warn(`Attempted access to route without a name: ${to.fullPath}. Redirecting to login.`);
         next('/login'); 
         return;
    }

    if (to.name === 'ForgotPass' && !to.query.user) {
        console.warn(`Attempted access to ForgotPass without user query param. Redirecting to login.`);
        next('/login');
        return;
    }

    const isAuthenticated = !!localStorage.getItem('user');

    if (to.meta.requiresAuth && !isAuthenticated) {
        console.log(`Route ${to.name} requires authentication, but user is not logged in. Redirecting to login.`);
        next('/login');
        return; 
    }

    if (to.meta.requiresAdmin && isAuthenticated) {
        const storedUser = localStorage.getItem('user');
        let userRole = null;

        if (storedUser) {
            try {
                const user = JSON.parse(storedUser);
                userRole = user.role;
            } catch (e) {
                console.error("Failed to parse user from localStorage in router guard (admin check):", e);
            }
        }

        if (userRole !== 'admin') {
            console.log(`Authenticated user (${userRole}) attempted access to admin route ${to.name}. Redirecting to dashboard.`);
            next('/dashboard'); 
            return; 
        }
    }

    console.log(`Authentication and role checks passed for route ${to.name}. Proceeding.`);
    next();
});

export default router;