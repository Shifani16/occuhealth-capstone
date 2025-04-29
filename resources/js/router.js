// resources/js/router.js
import { createRouter, createWebHistory } from 'vue-router'

import Login from './componentVue/auth/Login.vue'
import ForgotPass from './componentVue/auth/ForgotPass.vue'
import VerifyPass from './componentVue/auth/VerifyPass.vue'

import AboutUs from './componentVue/main/AboutUs.vue'
import ContactUs from './componentVue/main/ContactUs.vue'


const routes = [
  { path: '/', redirect: '/login' },
  { path: '/login', name: 'Login', component: Login },
  { path: '/forgotpass', name: 'ForgotPass', component: ForgotPass }, 
  { path: '/verifypass', name: 'VerifyPass', component: VerifyPass },
  { path: '/aboutus', name: 'AboutUs', component: AboutUs },
  { path: '/contactus', name:'ContactUs', component: ContactUs},
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
