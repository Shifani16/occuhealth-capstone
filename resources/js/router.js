// resources/js/router.js
import { createRouter, createWebHistory } from 'vue-router'

import Login from './componentVue/auth/Login.vue'
import ForgotPass from './componentVue/auth/ForgotPass.vue'
import VerifyPass from './componentVue/auth/VerifyPass.vue'
import AboutUs from './componentVue/main/AboutUs.vue'

const routes = [
  { path: '/', name: 'Login', component: Login },
  { path: '/forgotpass', component: ForgotPass },
  { path: '/verifypass', name: 'VerifyPass', component: VerifyPass },
  { path: '/aboutus', name: 'AboutUs', component: AboutUs }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router