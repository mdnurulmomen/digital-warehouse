/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

// importing plugin
import VueRouter from 'vue-router'
Vue.use(VueRouter)

import HasPermission from './mixins/HasPermission';
Vue.mixin(HasPermission);

// importing specific component only
import { ToggleButton } from 'vue-js-toggle-button'
Vue.component('ToggleButton', ToggleButton)

// importing custom components
import { routeNeedsPermission, userHasPermissionTo } from './public.js'

window.toastr = require('vue-toastr');

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
Vue.component('alert', require('./components/AlertComponent.vue').default);
Vue.component('loading', require('./components/LoadingComponent.vue').default);
Vue.component('pagination', require('./components/PaginationComponent.vue').default);
Vue.component('breadcrumb', require('./components/BreadcrumbComponent.vue').default);
Vue.component('asset-view-modal', require('./components/AssetViewModal.vue').default);
Vue.component('user-profile-view-modal', require('./components/UserProfileViewModal.vue').default);
Vue.component('asset-create-or-edit-modal', require('./components/AssetCreateOrEditModal.vue').default);
Vue.component('delete-confirmation-modal', require('./components/DeleteConfirmationModal.vue').default);
Vue.component('search-and-addition-option', require('./components/SearchAndAdditionOption.vue').default);
Vue.component('restore-confirmation-modal', require('./components/RestoreConfirmationModal.vue').default);
Vue.component('table-with-soft-delete-option', require('./components/TableWithSoftDeleteOption.vue').default);
Vue.component('user-profile-create-or-edit-modal', require('./components/UserProfileCreateOrEditModal.vue').default);

import OwnerSideMenuBar from './views/OwnerSideMenuBar'

import Dashboard from './views/Dashboard'
import Dashboard2 from './views/Dashboard2'
import Dashboard3 from './views/Dashboard3'
import Profile from './views/ProfileComponent'
import MyWarehouseIndex from './views/MyWarehouseIndex'
// special components
import ApplicationSetting from './views/SettingComponent'
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
import RoleIndex from './views/RoleIndex'

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
            path: '/profile',
            name: 'profile',
            component: Profile,
        },
        {
            path: '/my-warehouses',
            name: 'my-warehouses',
            component: MyWarehouseIndex,
        },
        // special routes
        {
            path: '/settings',
            name: 'settings',
            component: ApplicationSetting,
            meta: { 
                requiredPermission: 'view-application-setting-index' 
            },
        },
        {
            path: '/storage-types',
            name: 'storage-types',
            component: StorageTypeIndex,
            meta: { 
                requiredPermission: 'view-asset-index' 
            }
        },
        {
            path: '/containers',
            name: 'containers',
            component: ContainerIndex,
            meta: { 
                requiredPermission: 'view-asset-index' 
            }
        },
        {
            path: '/rent-periods',
            name: 'rent-periods',
            component: RentPeriodIndex,
            meta: { 
                requiredPermission: 'view-asset-index' 
            }
        },
        {
            path: '/variation-types',
            name: 'variation-types',
            component: VariationTypeIndex,
            meta: { 
                requiredPermission: 'view-asset-index' 
            }
        },
        {
            path: '/variations',
            name: 'variations',
            component: VariationIndex,
            meta: { 
                requiredPermission: 'view-asset-index' 
            }
        },
        {
            path: '/warehouse-owners',
            name: 'owners',
            component: WarehouseOwnerIndex,
            meta: { 
                requiredPermission: 'view-warehouse-owner-index' 
            }
        },
        {
            path: '/managers',
            name: 'managers',
            component: ManagerIndex,
            meta: { 
                requiredPermission: 'view-manager-index' 
            }
        },
        {
            path: '/merchants',
            name: 'merchants',
            component: MerchantIndex,
            meta: { 
                requiredPermission: 'view-merchant-index' 
            }
        },
        {
            path: '/warehouses',
            name: 'warehouses',
            component: WarehouseIndex,
            meta: { 
                requiredPermission: 'view-warehouse-index' 
            }
        },
        {
            path: '/product-categories',
            name: 'product-categories',
            component: ProductCategoryIndex,
            meta: { 
                requiredPermission: 'view-product-category-index' 
            }
        },
        {
            path: '/products',
            name: 'products',
            component: ProductIndex,
            meta: { 
                requiredPermission: 'view-product-index' 
            }
        },
        {
            path: '/requisitions',
            name: 'requisitions',
            component: RequisitionIndex,
            meta: { 
                requiredPermission: 'view-requisition-index' 
            }
        },
        {
            path: '/dispatches',
            name: 'dispatches',
            component: DispatchIndex,
            meta: { 
                requiredPermission: 'view-dispatch-index' 
            }
        },
        {
            path: '/product-stocks/:productName',
            name: 'product-stocks',
            component: ProductStockIndex,
            props: true,
            meta: { 
                requiredPermission: 'view-product-stock-index' 
            }
        },
        {
            path: '/roles',
            name: 'roles',
            component: RoleIndex,
            meta: { 
                requiredPermission: 'view-role-index' 
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
        next(false);
    }

});

const app = new Vue({
    el: '#app',
    components : {OwnerSideMenuBar},
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
    window.localStorage.removeItem('roles');
    window.localStorage.removeItem('permissions');
}
