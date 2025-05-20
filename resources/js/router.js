// resources/js/router.js
import { createRouter, createWebHistory } from 'vue-router'

import Login from './componentVue/auth/Login.vue'
import ForgotPass from './componentVue/auth/ForgotPass.vue'
import VerifyPass from './componentVue/auth/VerifyPass.vue'

import AboutUs from './componentVue/main/AboutUs.vue'
import ContactUs from './componentVue/main/ContactUs.vue'
import OurService from './componentVue/main/OurService.vue'

import Pasien from './componentVue/dashboard/pasien-dashboard/Pasien.vue'
import PasienDetail from './componentVue/dashboard/pasien-dashboard/PasienDetail.vue'
import PasienEdit from './componentVue/dashboard/pasien-dashboard/PasienEdit.vue'

import HasilMCU from './componentVue/dashboard/hasil-mcu-dashboard/HasilMCU.vue'
import HasilMCUDetail from './componentVue/dashboard/hasil-mcu-dashboard/HasilMCUDetail.vue'
import HasilMCUEdit from './componentVue/dashboard/hasil-mcu-dashboard/HasilMCUEdit.vue'

import Laporan from './componentVue/dashboard/laporan-dashboard/Laporan.vue'

import Rekapitulasi from './componentVue/dashboard/rekapitulasi-dashboard/Rekapitulasi.vue'

import Dashboard from './componentVue/dashboard/main-dashboard/Dashboard.vue'
import Admin from './componentVue/dashboard/admin-dashboard/Admin.vue'

const routes = [
  { path: '/', redirect: '/aboutus' },
  { path: '/login', name: 'Login', component: Login },
  { path: '/forgotpass', name: 'ForgotPass', component: ForgotPass },
  { path: '/verifypass', name: 'VerifyPass', component: VerifyPass },
  { path: '/aboutus', name: 'AboutUs', component: AboutUs },
  { path: '/contactus', name:'ContactUs', component: ContactUs},
  { path: '/ourservice', name:'OurService', component: OurService},
  { path: '/pasien', name:'Pasien', component: Pasien},
  { path: '/pasien/:id', name:'PasienDetail', component: PasienDetail},
  { path: '/pasien/edit/:id', name:'PasienEdit', component: PasienEdit},
  { path: '/hasilmcu', name:'HasilMCU', component: HasilMCU},
  { path: '/hasilmcu/:id', name:'HasilMCUDetail', component: HasilMCUDetail},
  { path: '/hasilmcu/edit/:id', name:'HasilMCUEdit', component: HasilMCUEdit},
  { path: '/laporan', name: 'Laporan', component: Laporan },
  { path: '/rekapitulasi', name: 'Rekapitulasi', component: Rekapitulasi },
  { path: '/dashboard', name:'Dashboard', component: Dashboard },
  {
    path: '/admin',
    name:'Admin',
    component: Admin,
    meta: { requiresAdmin: true } // Add this meta field
  },
  { path: '/verify-reset/:user(.*)', redirect: '/' }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to, from, next) => {
    if (to.name === null) {
        next('/login');
        return;
    }
    if (to.name === 'ForgotPass' && !to.query.user) {
        next('/login');
        return; 
    }

    if (to.meta.requiresAdmin) {
        const storedUser = localStorage.getItem('user');
        let userRole = null;

        if (storedUser) {
            try {
                const user = JSON.parse(storedUser);
                userRole = user.role;
            } catch (e) {
                console.error("Failed to parse user from localStorage in router guard:", e);
              
            }
        }
        if (userRole !== 'admin') {
             if (from.fullPath === to.fullPath || from.fullPath === '/') {
                 next('/dashboard'); 
             } else {
                next(from.fullPath);
             }

            return;
        }
    }
    next();
});

export default router