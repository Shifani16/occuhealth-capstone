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

const routes = [
  { path: '/', redirect: '/login' },
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
  { path: '/verify-reset/:user(.*)', redirect: '/' }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to, from, next) => {
  if (to.name === null) {
    next('/login');
  } else if (to.name === 'ForgotPass' && !to.query.user) {
    next('/login');
  } else {
    next();
  }
});

export default router
