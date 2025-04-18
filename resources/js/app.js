import './bootstrap';

import { createApp } from 'vue';
import Login from './componentVue/auth/Login.vue';
import ForgotPass from './componentVue/auth/ForgotPass.vue';
import VerifyPass from './componentVue/auth/VerifyPass.vue';

if (document.getElementById('login-app')) {
    createApp(Login).mount('#login-app');
} else if (document.getElementById('forgotpass-app')) {
    createApp(ForgotPass).mount('#forgotpass-app');
} else if (document.getElementById('verifypass-app')) {
    createApp(VerifyPass).mount('#verifypass-app');
}