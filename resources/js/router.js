// resources/js/router.js
import { createRouter, createWebHistory } from 'vue-router'

import Login from './Pages/Login.vue'
import VerifyPass from './Pages/VerifyPass.vue'

const routes = [
  { path: '/', name: 'Login', component: Login },
  { path: '/verifypass', name: 'VerifyPass', component: VerifyPass },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
