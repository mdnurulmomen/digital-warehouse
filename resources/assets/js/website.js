/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
// window.toastr = require('vue-toastr');

// importing plugin
import VueRouter from 'vue-router'
Vue.use(VueRouter)

import VTooltip from 'v-tooltip'
Vue.use(VTooltip)

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

import WebsiteMenuBar from './WebsiteMenuBar'

import Index from './views/website/Index'
import Services from './views/website/Services'
import Warehouses from './views/website/Warehouses'
import WarehouseDetails from './views/website/WarehouseDetails'
import Careers from './views/website/Careers'
import JobDetails from './views/website/JobDetails'
import ContactUs from './views/website/ContactUs'
import Registrations from './views/website/Registrations'
import TermsAndConditions from './views/website/TermsAndConditions'
import AboutUs from './views/website/AboutUs'

// Registering component globally
Vue.component('carousel', require('vue-owl-carousel'));

Vue.component('query-contact', require('./components/website/QueryContact.vue').default);
Vue.component('footer-component', require('./components/website/FooterComponent.vue').default);
Vue.component('copyright-component', require('./components/website/CopyrightComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const router = new VueRouter({
    mode: 'history',
    routes: [
        {
            path: '/',
            name: 'home',
            component: Index
        },
        {
            path: '/services',
            name: 'services',
            component: Services
        },
        {
            path: '/warehouses',
            name: 'warehouses',
            component: Warehouses
        },
        {
            path: '/warehouses/:id?',
            name: 'warehouse-details',
            component: WarehouseDetails
        },
        {
            path: '/contact-us',
            name: 'contact-us',
            component: ContactUs
        },
        {
            path: '/careers',
            name: 'careers',
            component: Careers
        },
        {
            path: '/jobs/:id?',
            name: 'job-details',
            component: JobDetails
        },
        {
            path: '/registrations',
            name: 'registrations',
            component: Registrations
        },
        {
            path: '/terms-and-conditions',
            name: 'terms-and-conditions',
            component: TermsAndConditions
        },
        {
            path: '/about-us',
            name: 'about-us',
            component: AboutUs
        },

    ],
});

const app = new Vue({
    el: '#app',
    components : {WebsiteMenuBar},
    router
});