import './bootstrap';

import { createApp } from 'vue';
import router from './router';

import Login from './componentVue/auth/Login.vue';
import ForgotPass from './componentVue/auth/ForgotPass.vue';
import VerifyPass from './componentVue/auth/VerifyPass.vue';

import AboutUs from './componentVue/main/AboutUs.vue';

const appElement = 
    document.getElementById('login-app') || 
    document.getElementById('forgotpass-app') || 
    document.getElementById('verifypass-app') ||
    document.getElementById('aboutus-app');

let appComponent = null;

if (document.getElementById('login-app')) {
    appComponent = Login;
} else if (document.getElementById('forgotpass-app')) {
    appComponent = ForgotPass;
} else if (document.getElementById('verifypass-app')) {
    appComponent = VerifyPass;
} else if (document.getElementById('aboutus-app')) {
    appComponent = AboutUs;
}

if (appElement && appComponent) {
    createApp(appComponent).use(router).mount(appElement);
}