/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

import VueRouter from 'vue-router'
Vue.use(VueRouter)

import { ToggleButton } from 'vue-js-toggle-button'
Vue.component('ToggleButton', ToggleButton)

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

// Registering component globally
Vue.component('tab', require('./components/TabComponent.vue').default);
Vue.component('pagination', require('./components/PaginationComponent.vue').default);
Vue.component('loading', require('./components/LoadingComponent.vue').default);
Vue.component('delete-confirmation-modal', require('./components/DeleteConfirmationModal.vue').default);
Vue.component('restore-confirmation-modal', require('./components/RestoreConfirmationModal.vue').default);
Vue.component('breadcrumb', require('./components/BreadcrumbComponent.vue').default);
Vue.component('user-profile-view-modal', require('./components/UserProfileViewModal.vue').default);
Vue.component('search-and-addition-option', require('./components/SearchAndAdditionOption.vue').default);
Vue.component('table-with-soft-delete-option', require('./components/TableWithSoftDeleteOption.vue').default);
Vue.component('user-profile-create-or-edit-modal', require('./components/UserProfileCreateOrEditModal.vue').default);

Vue.component('asset-view-modal', require('./components/AssetViewModal.vue').default);
Vue.component('asset-create-or-edit-modal', require('./components/AssetCreateOrEditModal.vue').default);

Vue.component('alert', require('./components/AlertComponent.vue').default);

import MerchantSideMenuBar from './views/MerchantSideMenuBar'

import Dashboard from './views/Dashboard'
import Dashboard2 from './views/Dashboard2'
import Profile from './views/ProfileComponent'
import ProductIndex from './views/MerchantProductIndex'

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
            path: '/profile',
            name: 'profile',
            component: Profile,
        },
        {
            path: '/my-products',
            name: 'my-products',
            component: ProductIndex,
        },
    ],
});

const app = new Vue({
    el: '#app',
    components : {MerchantSideMenuBar},
    router
});

// custom scripts
window.showProfile = () => {
    router.push({ name: 'profile' });
}

window.toastr = require('vue-toastr');