import './bootstrap';

import { createApp } from 'vue';
import router from './router';

// Import the new root App component
import App from './componentVue/App.vue';

// Find the single mount point
const appElement = document.getElementById('app');

if (appElement) {
    // Create the Vue application, use the router, and mount the root App component
    createApp(App)
        .use(router) // Use the router plugin
        .mount(appElement); // Mount the root App component onto the #app element
}

// import Login from './componentVue/auth/Login.vue';
// import ForgotPass from './componentVue/auth/ForgotPass.vue';
// import VerifyPass from './componentVue/auth/VerifyPass.vue';

// import AboutUs from './componentVue/main/AboutUs.vue';

// const appElement = 
//     document.getElementById('login-app') || 
//     document.getElementById('forgotpass-app') || 
//     document.getElementById('verifypass-app') ||
//     document.getElementById('aboutus-app');

// let appComponent = null;

// if (document.getElementById('login-app')) {
//     appComponent = Login;
// } else if (document.getElementById('forgotpass-app')) {
//     appComponent = ForgotPass;
// } else if (document.getElementById('verifypass-app')) {
//     appComponent = VerifyPass;
// } else if (document.getElementById('aboutus-app')) {
//     appComponent = AboutUs;
// }

// if (appElement && appComponent) {
//     createApp(appComponent).use(router).mount(appElement);
// }