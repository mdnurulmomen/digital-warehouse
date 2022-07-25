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
import { routeNeedsPermission, userHasPermissionTo } from './public.js'

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
Vue.component('import-excel', require('./components/ImportExcel.vue').default);
Vue.component('tree-item', require('./components/TreeItemComponent.vue').default);
Vue.component('pagination', require('./components/PaginationComponent.vue').default);
Vue.component('breadcrumb', require('./components/BreadcrumbComponent.vue').default);
Vue.component('asset-view-modal', require('./components/AssetViewModal.vue').default);
Vue.component('v-date-picker', require('v-calendar/lib/components/date-picker.umd'));
Vue.component('user-profile-view-modal', require('./components/UserProfileViewModal.vue').default);
Vue.component('table-with-delete-option', require('./components/TableWithDeleteOption.vue').default);
// Vue.component('mail-create-modal', require('./components/MailCreateModal.vue').default);
// Vue.component('container-type-view-modal', require('./components/ContainerTypeViewModal.vue').default);
Vue.component('asset-create-or-edit-modal', require('./components/AssetCreateOrEditModal.vue').default);
Vue.component('delete-confirmation-modal', require('./components/DeleteConfirmationModal.vue').default);
Vue.component('search-and-addition-option', require('./components/SearchAndAdditionOption.vue').default);
Vue.component('restore-confirmation-modal', require('./components/RestoreConfirmationModal.vue').default);
Vue.component('addition-search-export-option', require('./components/AdditionSearchExportOption.vue').default);
Vue.component('table-with-soft-delete-option', require('./components/TableWithSoftDeleteOption.vue').default);
Vue.component('user-profile-create-or-edit-modal', require('./components/UserProfileCreateOrEditModal.vue').default);
Vue.component('permissive-user-profile-view-modal', require('./components/PermissiveUserProfileViewModal.vue').default);
Vue.component('permissive-user-profile-create-or-edit-modal', require('./components/PermissiveUserProfileCreateOrEditModal.vue').default);
// Vue.component('container-type-create-or-edit-modal', require('./components/ContainerTypeCreateOrEditModal.vue').default);

import AdminSideMenuBar from './AdminSideMenuBar'

import Overview from './views/Overview'
import Analytics from './views/Analytics'
import Profile from './views/ProfileComponent'
import UnAuthorized from './views/403'
import NotFound from './views/404'

// special components
import ApplicationSetting from './views/SettingComponent'
import WarehouseOwnerIndex from './views/WarehouseOwnerIndex'
import ManagerIndex from './views/ManagerIndex'
import MerchantIndex from './views/MerchantIndex'
import StorageTypeIndex from './views/StorageTypeIndex'
import ContainerIndex from './views/ContainerIndex'
import ContainerTypeIndex from './views/ContainerTypeIndex'
import WarehouseIndex from './views/WarehouseIndex'
import WarehouseContainerIndex from './views/WarehouseContainerIndex'
import ContainerShelfIndex from './views/ContainerShelfIndex'
import ShelfUnitIndex from './views/ShelfUnitIndex'
import RentPeriodIndex from './views/RentPeriodIndex'
import VariationTypeIndex from './views/VariationTypeIndex'
import VariationIndex from './views/VariationIndex'
import ProductCategoryIndex from './views/ProductCategoryIndex'
import ProductManufacturerIndex from './views/ProductManufacturerIndex'
import ProductIndex from './views/ProductIndex'
import CategoryProductIndex from './views/CategoryProductIndex'
import RequisitionIndex from './views/RequisitionIndex'
// import DispatchIndex from './views/DispatchIndex'
import ProductStockIndex from './views/ProductStockIndex'
import ProductRequisitionIndex from './views/ProductRequisitionIndex'
import StockIndex from './views/StockIndex'
import ProductMerchantIndex from './views/ProductMerchantIndex'
import MerchantProductIndex from './views/MerchantProductIndex'
import RoleIndex from './views/RoleIndex'
import DeliveryCompanyIndex from './views/DeliveryCompanyIndex'
import PackagingPackageIndex from './views/PackagingPackageIndex'
import MerchantDealIndex from './views/MerchantDealIndex'
import DealPaymentIndex from './views/DealPaymentIndex'
import MailIndex from './views/MailIndex'
// import WarehouseManagerIndex from './views/WarehouseManagerIndex'

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
            component: Overview,
            meta: { 
                // authRequired: true 
                // requiredPermission: 'view-dashboard-one-index' // public for admin & manager (conditionally)
            },
        },
        {
            path: '/analytics',
            name: 'analytics',
            component: Analytics,
            meta: { 
                // authRequired: true 
                // requiredPermission: 'view-dashboard-dashboard-two'  // public for admin & manager (conditionally)
            },
        },
        {
            path: '/profile',
            name: 'profile',
            component: Profile,
            meta: { 
                // authRequired: true 
            },
        },
        
        // special routes
        
        {
            path: '/settings',
            name: 'settings',
            component: ApplicationSetting,
            meta: {
                // authRequired: true,
                requiredPermission: 'view-application-setting-index' 
            },
        },
        {
            path: '/mails',
            name: 'mails',
            component: MailIndex,
            meta: {
                // authRequired: true,
                requiredPermission: 'view-mail-index' 
            }
        },
        {
            path: '/storage-types',
            name: 'storage-types',
            component: StorageTypeIndex,
            meta: {
                // authRequired: true,
                requiredPermission: 'view-warehouse-asset-index' 
            }
        },
        {
            path: '/container-types',
            name: 'container-types',
            component: ContainerTypeIndex,
            meta: {
                // authRequired: true,
                requiredPermission: 'view-warehouse-asset-index' 
            }
        },
        {
            path: '/containers',
            name: 'containers',
            component: ContainerIndex,
            meta: {
                // authRequired: true,
                requiredPermission: 'view-warehouse-asset-index' 
            }
        },
        {
            path: '/rent-periods',
            name: 'rent-periods',
            component: RentPeriodIndex,
            meta: {
                // authRequired: true,
                requiredPermission: 'view-warehouse-asset-index' 
            }
        },
        {
            path: '/variation-types',
            name: 'variation-types',
            component: VariationTypeIndex,
            meta: {
                // authRequired: true,
                requiredPermission: 'view-product-asset-index' 
            }
        },
        {
            path: '/variations',
            name: 'variations',
            component: VariationIndex,
            meta: {
                // authRequired: true,
                requiredPermission: 'view-product-asset-index' 
            }
        },
        {
            path: '/warehouse-owners',
            name: 'owners',
            component: WarehouseOwnerIndex,
            meta: {
                // authRequired: true,
                requiredPermission: 'view-warehouse-owner-index' 
            }
        },
        {
            path: '/managers',
            name: 'managers',
            component: ManagerIndex,
            meta: {
                // authRequired: true,
                requiredPermission: 'view-manager-index' 
            }
        },
        {
            path: '/merchants',
            name: 'merchants',
            component: MerchantIndex,
            meta: {
                // authRequired: true,
                requiredPermission: 'view-merchant-index' 
            }
        },
        {
            path: '/warehouses',
            name: 'warehouses',
            component: WarehouseIndex,
            meta: {
                // authRequired: true,
                requiredPermission: 'view-warehouse-index' 
            }
        },
        {
            path: '/warehouse/:id/containers',
            name: 'warehouse-containers',
            component: WarehouseContainerIndex, 
            props : true,
            meta: {
                // authRequired: true,
                requiredPermission: 'view-warehouse-index' 
            },
            beforeEnter: (to, from, next) => {
                if (to.params.id && to.params.warehouseName) {
                    next(); // <-- everything good, proceed
                }
                else {
                    next('/warehouses');
                }
            }
        },
        {
            path: '/warehouse-container/:id/shelves',
            name: 'warehouse-container-shelves',
            component: ContainerShelfIndex,
            props: true,
            meta: {
                // authRequired: true,
                requiredPermission: 'view-warehouse-index' 
            },
            beforeEnter: (to, from, next) => {
                if (to.params.id && to.params.containerName) {
                    next(); // <-- everything good, proceed
                }
                else {
                    // next(false);
                    next('/warehouses');
                }
            }
        },
        {
            path: '/warehouse-container-shelf/:id/units',
            name: 'warehouse-container-shelf-units',
            component: ShelfUnitIndex,
            props: true,
            meta: {
                // authRequired: true,
                requiredPermission: 'view-warehouse-index' 
            },
            beforeEnter: (to, from, next) => {
                if (to.params.id && to.params.containerName && to.params.shelfName) {
                    next(); // <-- everything good, proceed
                }
                else {
                    // next(false);
                    next('/warehouses');
                }
            }
        },
        /*
            {
                path: '/warehouse-managers',
                name: 'warehouse-managers',
                component: WarehouseManagerIndex,
                meta: {
                    // authRequired: true,
                    requiredPermission: 'view-warehouse-manager-index' 
                }
            },
        */
        {
            path: '/product-categories',
            name: 'product-categories',
            component: ProductCategoryIndex,
            meta: {
                // authRequired: true,
                requiredPermission: 'view-product-asset-index' 
            }
        },
        {
            path: '/product-manufacturers',
            name: 'product-manufacturers',
            component: ProductManufacturerIndex,
            meta: {
                // authRequired: true,
                requiredPermission: 'view-product-asset-index' 
            }
        },
        {
            path: '/product-categories/:categoryName',
            name: 'category-products',
            component: CategoryProductIndex,
            props: true,
            meta: {
                // authRequired: true,
                requiredPermission: 'view-product-index' 
            },
            beforeEnter: (to, from, next) => {
                if (to.params.category && to.params.categoryName) {
                    next(); // <-- everything good, proceed
                }
                else {
                    next('/product-categories');
                }
            }
        },
        {
            path: '/products',
            name: 'products',
            component: ProductIndex,
            meta: {
                // authRequired: true,
                requiredPermission: 'view-product-index' 
            }
        },
        {
            path: '/requisitions',
            name: 'requisitions',
            component: RequisitionIndex,
            meta: {
                // authRequired: true,
                requiredPermission: 'view-requisition-index' 
            }
        },
        /*
            {
                path: '/dispatches',
                name: 'dispatches',
                component: DispatchIndex,
                meta: {
                    // authRequired: true,
                    requiredPermission: 'view-dispatch-index' 
                }
            },
        */
        {
            path: '/products/:productId/merchants/:merchantId/requisitions',
            name: 'product-requisitions',
            component: ProductRequisitionIndex,
            props: true,
            meta: {
                // authRequired: true,
                requiredPermission: 'view-requisition-index' 
            },
            beforeEnter: (to, from, next) => {
                if (to.params.merchantId && to.params.productId && to.params.merchantProduct) {
                    next(); // <-- everything good, proceed
                }
                else {
                    next('/products');
                }
            }
        },
        {
            path: '/product-stocks/:merchantName',
            name: 'product-stocks',
            component: ProductStockIndex,
            props: true,
            meta: {
                // authRequired: true,
                requiredPermission: 'view-product-stock-index' 
            },
            beforeEnter: (to, from, next) => {
                if (to.params.product && to.params.merchantName && to.params.productMerchant) {
                    next(); // <-- everything good, proceed
                }
                else {
                    next('/products');
                }
            }
        },
        {
            path: '/stocks',
            name: 'stocks',
            component: StockIndex,
            meta: {
                // authRequired: true,
                requiredPermission: 'view-product-stock-index' 
            }
        },
        {
            path: '/product-merchants/:productName',
            name: 'product-merchants',
            component: ProductMerchantIndex,
            props: true,
            meta: {
                // authRequired: true,
                requiredPermission: 'view-merchant-product-index' 
            },
            beforeEnter: (to, from, next) => {
                if (to.params.product) {
                    next(); // <-- everything good, proceed
                }
                else {
                    next('/products');
                }
            }
        },
        {
            path: '/merchant-products/:merchantName',
            name: 'merchant-products',
            component: MerchantProductIndex,
            props: true,
            meta: {
                // authRequired: true,
                requiredPermission: 'view-merchant-product-index' 
            },
            beforeEnter: (to, from, next) => {
                if (to.params.merchant) {
                    next(); // <-- everything good, proceed
                }
                else {
                    next('/merchants');
                }
            }
        },
        {
            path: '/roles',
            name: 'roles',
            component: RoleIndex,
            meta: {
                // authRequired: true,
                requiredPermission: 'view-role-index' 
            }
        },
        {
            path: '/merchant-deals/:merchantName',
            name: 'merchant-deals',
            component: MerchantDealIndex,
            props: true,
            meta: {
                // authRequired: true,
                requiredPermission: 'view-merchant-deal-index' 
            },
            beforeEnter: (to, from, next) => {
                if (to.params.merchant && to.params.merchantName) {
                    next(); // <-- everything good, proceed
                }
                else {
                    next('/merchants');
                }
            }
        },
        {
            path: '/deal-payments/:merchantName/:dealName',
            name: 'deal-payments',
            component: DealPaymentIndex,
            props: true,
            meta: {
                // authRequired: true,
                requiredPermission: 'view-merchant-payment-index' 
            },
            beforeEnter: (to, from, next) => {
                if (to.params.merchantName && to.params.dealName && to.params.deal) {
                    next(); // <-- everything good, proceed
                }
                else {
                    next({ name : 'merchant-deals', params: { merchantName: to.params.merchantName }});
                }
            }
        },
        
        // view packages is permissible for all
        {
            path: '/packaging-packages',
            name: 'packaging-packages',
            component: PackagingPackageIndex,
            meta: {
                // authRequired: true,
                // requiredPermission: 'view-logistic-asset-index' 
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

// router guard
router.beforeEach((to, from, next) => {

    // routes that need not permissions
    if (! to.matched.length || to.matched.find(routeNeedsPermission) === undefined) {
        next();
    }
    // routes than need permission
    else if (userHasPermissionTo(to.matched.find(routeNeedsPermission).meta.requiredPermission)) {
        next();
    }
    else {
        // next(false);  // not authorized
        next('/403');
    }

});

const app = new Vue({
    el: '#app',
    components : {AdminSideMenuBar},
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
    localStorage.removeItem('roles');
    localStorage.removeItem('permissions');
    // localStorage.removeItem('access_token');
    localStorage.removeItem('general_settings');
}