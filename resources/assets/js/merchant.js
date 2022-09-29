/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
window.toastr = require('vue-toastr');

// importing plugin
import VueRouter from 'vue-router'
Vue.use(VueRouter)

import VTooltip from 'v-tooltip'
Vue.use(VTooltip)

// import mixin
import HasPermission from './mixins/HasPermission';
Vue.mixin(HasPermission);

// importing specific component only
import { ToggleButton } from 'vue-js-toggle-button'
Vue.component('ToggleButton', ToggleButton)

// importing custom components
import { routeNeedsPermission, userHasPermissionTo } from './mixins/public.js'

import VueHtmlToPaper from 'vue-html-to-paper';
Vue.use(VueHtmlToPaper);

import JsBarcode from 'jsbarcode'
Vue.component('JsBarcode', JsBarcode)

import VueQRCodeComponent from 'vue-qrcode-component'
Vue.component('qr-code', VueQRCodeComponent)

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
// Vue.component('barcode', require('vue-barcode'));
Vue.component('download-excel', require('vue-json-excel').default);
Vue.component('tab', require('./components/TabComponent.vue').default);
Vue.component('alert', require('./components/AlertComponent.vue').default);
Vue.component('loading', require('./components/LoadingComponent.vue').default);
// Vue.component('import-excel', require('./components/ImportExcel.vue').default);
Vue.component('tree-item', require('./components/TreeItemComponent.vue').default);
Vue.component('pagination', require('./components/PaginationComponent.vue').default);
Vue.component('breadcrumb', require('./components/BreadcrumbComponent.vue').default);
Vue.component('asset-view-modal', require('./components/AssetViewModal.vue').default);
Vue.component('v-date-picker', require('v-calendar/lib/components/date-picker.umd'));
Vue.component('user-profile-view-modal', require('./components/UserProfileViewModal.vue').default);
// Vue.component('table-with-delete-option', require('./components/TableWithDeleteOption.vue').default);
// Vue.component('mail-create-modal', require('./components/MailCreateModal.vue').default);
// Vue.component('container-type-view-modal', require('./components/ContainerTypeViewModal.vue').default);
Vue.component('asset-create-or-edit-modal', require('./components/AssetCreateOrEditModal.vue').default);
Vue.component('delete-confirmation-modal', require('./components/DeleteConfirmationModal.vue').default);
Vue.component('search-and-addition-option', require('./components/SearchAndAdditionOption.vue').default);
Vue.component('restore-confirmation-modal', require('./components/RestoreConfirmationModal.vue').default);
Vue.component('addition-search-export-option', require('./components/AdditionSearchExportOption.vue').default);
Vue.component('table-with-soft-delete-option', require('./components/TableWithSoftDeleteOption.vue').default);
Vue.component('user-profile-create-or-edit-modal', require('./components/UserProfileCreateOrEditModal.vue').default);
// Vue.component('permissive-user-profile-view-modal', require('./components/PermissiveUserProfileViewModal.vue').default);
// Vue.component('permissive-user-profile-create-or-edit-modal', require('./components/PermissiveUserProfileCreateOrEditModal.vue').default);
// Vue.component('container-type-create-or-edit-modal', require('./components/ContainerTypeCreateOrEditModal.vue').default);

// Special Components
Vue.component('my-product-search-export-option', require('./components/MyProductSearchExportOption.vue').default);
Vue.component('my-requisition-addition-search-export-option', require('./components/MyRequisitionAdditionSearchExportOption.vue').default);

import MerchantSideMenuBar from './MerchantSideMenuBar'

import MerchantHome from './views/Home'
import Profile from './views/ProfileComponent'
import MyProductIndex from './views/MyProductIndex'
import MyProductStockIndex from './views/MyProductStockIndex'
import MyRequisitionIndex from './views/MyRequisitionIndex'
import MySpaceDealIndex from './views/MySpaceDealIndex'
import MySpaceDealRentIndex from './views/MySpaceDealRentIndex'
import DeliveryCompanyIndex from './views/DeliveryCompanyIndex'     // public
import PackagingPackageIndex from './views/PackagingPackageIndex'   // public
import UnAuthorized from './views/403'
import NotFound from './views/404'


// import DispatchIndex from './views/DispatchIndex'


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
            component: MerchantHome
        },
        {
            path: '/profile',
            name: 'profile',
            component: Profile,
        },
        {
            path: '/my-products',
            name: 'my-products',
            component: MyProductIndex,
        },
        {
            path: '/my-product-stocks/:merchantProductId',
            name: 'my-product-stocks',
            component: MyProductStockIndex,
            props: true,
            
            beforeEnter: (to, from, next) => {
                if (to.params.product && to.params.productMerchant && to.params.merchantProductId) {
                    next(); // <-- everything good, proceed
                }
                else {
                    next('/my-products');
                }
            }
        },
        {
            path: '/my-requisitions',
            name: 'my-requisitions',
            component: MyRequisitionIndex,
        },
        {
            path: '/my-space-deals',
            name: 'my-space-deals',
            component: MySpaceDealIndex,
        },
        {
            path: '/my-space-deal/:dealId/rents',
            name: 'my-space-deal-rents',
            component: MySpaceDealRentIndex,
            props: true,

            beforeEnter: (to, from, next) => {
                if (to.params.deal && to.params.dealId) {
                    next(); // <-- everything good, proceed
                }
                else {
                    next('/my-space-deals');
                }
            }
        },
        
        // public routes
        
        
        // view packages is permissible for all
        {
            path: '/packaging-packages',
            name: 'packaging-packages',
            component: PackagingPackageIndex,
            meta: {
                // authRequired: true,
                // requiredPermission: 'view-product-asset-index' 
            }
        },
        // view companies is permissible for all
        {
            path: '/delivery-companies',
            name: 'delivery-companies',
            component: DeliveryCompanyIndex,
            meta: {
                // authRequired: true,
                // requiredPermission: 'view-product-asset-index' 
            }
        },

        // UnAuthorized Page
        {
            path: '/403',
            name: 'un-authorized',
            component: UnAuthorized,
            meta: {
                // authRequired: true,
            }
        },

        // Not Found Page
        {
            path: '/*',
            name: 'not-found',
            component: NotFound,
            meta: {
                // authRequired: true,
            }
        },

    ],
});

router.beforeEach((to, from, next) => {

    if (! to.matched.length || to.matched.find(routeNeedsPermission) === undefined) {
        next();
    }
    else if (userHasPermissionTo(to.matched.find(routeNeedsPermission).meta.requiredPermission)) {
        next();
    }
    else {
        // next(false);
        next('/403');
    }

});

const app = new Vue({
    el: '#app',
    components : {MerchantSideMenuBar},
    router
});

// public functions
window.showSetting = () => {
    router.push({ name: 'settings' });
}

window.showProfile = () => {
    router.push({ name: 'profile' });
}

window.logout = () => {
    /*
    localStorage.removeItem('roles');
    localStorage.removeItem('permissions');
    */
    
    // localStorage.removeItem('access_token');
    localStorage.removeItem('general_settings');
}