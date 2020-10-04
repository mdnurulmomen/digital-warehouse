/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

import VueRouter from 'vue-router'
Vue.use(VueRouter)

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

// Vue.component('example-component', require('./components/ExampleComponent.vue').default);

import AdminSideMenuBar from './views/AdminSideMenuBar'

import Dashboard from './views/Dashboard'
import Dashboard2 from './views/Dashboard2'
import Dashboard3 from './views/Dashboard3'
import SettingComponent from './views/SettingComponent'

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const router = new VueRouter({
    mode: 'history',
    routes: [
        {
            path: '/home',
            name: 'home',
            component: Dashboard
        },
        {
            path: '/dashboard-2',
            name: 'dashboar-2',
            component: Dashboard2,
        },
        {
            path: '/dashboard-3',
            name: 'dashboar-3',
            component: Dashboard3,
        },
        {
            path: '/settings',
            name: 'settings',
            component: SettingComponent,
        },
    ],
});

const app = new Vue({
    el: '#app',
    components : {AdminSideMenuBar},
    router
});

// custom scripts
window.showSetting = () => {
    router.push({ name: 'settings' });
}
