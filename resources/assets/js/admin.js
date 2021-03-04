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

import AdminSideMenuBar from './views/AdminSideMenuBar'

import Dashboard from './views/Dashboard'
import Dashboard2 from './views/Dashboard2'
import Dashboard3 from './views/Dashboard3'
import ApplicationSetting from './views/SettingComponent'
import Profile from './views/ProfileComponent'
import WarehouseOwnerIndex from './views/WarehouseOwnerIndex'
import ManagerIndex from './views/ManagerIndex'
import MerchantIndex from './views/MerchantIndex'
import StorageTypeIndex from './views/StorageTypeIndex'
import ContainerIndex from './views/ContainerTypeIndex'
import WarehouseIndex from './views/WarehouseIndex'
import RentPeriodIndex from './views/RentPeriodIndex'
import VariationTypeIndex from './views/VariationTypeIndex'
import VariationIndex from './views/VariationIndex'
import ProductCategoryIndex from './views/ProductCategoryIndex'
import ProductIndex from './views/ProductIndex'
import RequisitionIndex from './views/RequisitionIndex'
import DispatchIndex from './views/DispatchIndex'
import ProductStockIndex from './views/ProductStockIndex'

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
            component: ApplicationSetting,
        },
        {
            path: '/profile',
            name: 'profile',
            component: Profile,
        },
        {
            path: '/warehouse-owners',
            name: 'owners',
            component: WarehouseOwnerIndex,
        },
        {
            path: '/managers',
            name: 'managers',
            component: ManagerIndex,
        },
        {
            path: '/merchants',
            name: 'merchants',
            component: MerchantIndex,
        },
        {
            path: '/storage-types',
            name: 'storage-types',
            component: StorageTypeIndex,
        },
        {
            path: '/containers',
            name: 'containers',
            component: ContainerIndex,
        },
        {
            path: '/warehouses',
            name: 'warehouses',
            component: WarehouseIndex,
        },
        {
            path: '/rent-periods',
            name: 'rent-periods',
            component: RentPeriodIndex,
        },
        {
            path: '/variation-types',
            name: 'variation-types',
            component: VariationTypeIndex,
        },
        {
            path: '/variations',
            name: 'variations',
            component: VariationIndex,
        },
        {
            path: '/product-categories',
            name: 'product-categories',
            component: ProductCategoryIndex,
        },
        {
            path: '/products',
            name: 'products',
            component: ProductIndex,
        },
        {
            path: '/requisitions',
            name: 'requisitions',
            component: RequisitionIndex,
        },
        {
            path: '/dispatches',
            name: 'dispatches',
            component: DispatchIndex,
        },
        {
            path: '/product-stocks/:productName',
            name: 'product-stocks',
            component: ProductStockIndex,
            props: true,
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

window.showProfile = () => {
    router.push({ name: 'profile' });
}

window.toastr = require('vue-toastr');
