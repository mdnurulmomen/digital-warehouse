<template v-if="userHasPermissionTo('view-gereral-dashboard-one')">
	<div class="pcoded-content">

        <!-- 
        <breadcrumb 
            :icon="'home'"
            :title="'dashboard'" 
            :message="'You may view basic analytics here'"
        ></breadcrumb>  
        -->

        <div class="page-header card">
            <div class="row align-items-center">
                <div class="col-6">
                    <div class="row">
                        <div class="col-12 d-flex align-items-center">
                            <div class="d-inline mr-2">
                                <img src="icons/cms/dashboard-1.png">
                            </div>

                            <div class="d-inline">
                                <h5>Overview</h5>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mt-1">
                            <h6 class="text-nowrap">You may view basic analytics here</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="pcoded-inner-content">
         <div class="main-body">
            <div class="page-wrapper">
               <div class="page-body">
                  <!-- Pending Stocks and Requisitions -->
                  <div class="row">
                     <div class="col-xl-3 col-md-6">
                        <div class="card prod-p-card card-yellow">
                            <div class="card-body">
                                <div class="row align-items-center m-b-30">
                                    <div class="col">
                                        <h6 class="m-b-5 text-white">
                                           Pending Product-Stocks
                                       </h6>
                                       <h3 class="m-b-0 f-w-700 text-white">
                                           {{ dashboard.numberPendingProductStocks || 0 }}
                                       </h3>
                                   </div>
                                   <div class="col-auto">
                                       <router-link :to="{ name: 'products' }">
                                           <i class="fa fa-eye text-c-yellow f-18"></i>
                                       </router-link>
                                   </div>
                               </div>
                                        <!-- 
	                                        <p class="m-b-0 text-white">
	                                        	<span class="label label-danger m-r-10">
	                                        		+11%
	                                        	</span>
	                                        	From Previous Month
	                                        </p> 
	                                    -->
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="card prod-p-card card-blue">
                                    <div class="card-body">
                                        <div class="row align-items-center m-b-30">
                                            <div class="col">
                                                <h6 class="m-b-5 text-white">
                                                	Pending Requisitions
                                                </h6>
                                                <h3 class="m-b-0 f-w-700 text-white">
                                                	{{ dashboard.numberPendingRequistiions || 0 }}
                                                </h3>
                                            </div>
                                            <div class="col-auto">
                                            	<router-link :to="{ name: 'requisitions' }">
                                                   <i class="fa fa-bullseye text-c-blue f-18"></i>
                                               </router-link>
                                           </div>
                                       </div>
                                        <!-- 
	                                        <p class="m-b-0 text-white">
	                                        	<span class="label label-danger m-r-10">
	                                        		+11%
	                                        	</span>
	                                        	From Previous Month
	                                        </p> 
	                                    -->
                                    </div>
                                </div>
                            </div>

                            <!-- 
                            <div class="col-xl-3 col-md-6">
                                <div class="card prod-p-card card-red">
                                    <div class="card-body">
                                        <div class="row align-items-center m-b-30">
                                            <div class="col">
                                                <h6 class="m-b-5 text-white">
                                                	Pending Dispatches
                                                </h6>
                                                <h3 class="m-b-0 f-w-700 text-white">
                                                	{{ dashboard.numberPendingDispatches || 0 }}
                                                </h3>
                                            </div>
                                            <div class="col-auto">
                                            	<router-link :to="{ name: 'requisitions' }">
                                                	<i class="fa fa-eye text-c-blue f-18"></i>
                                            	</router-link>
                                            </div>
                                        </div>
                                        
                                        <p class="m-b-0 text-white">
                                        	<span class="label label-danger m-r-10">
                                        		+11%
                                        	</span>
                                        	From Previous Month
                                        </p> 
                                    </div>
                                </div>
                            </div> 
                        -->

                        <div class="col-xl-3 col-md-6">
                            <div class="card prod-p-card card-green">
                                <div class="card-body">
                                    <div class="row align-items-center m-b-30">
                                        <div class="col">
                                            <h6 class="m-b-5 text-white">
                                               Unreceived Dispatches
                                           </h6>
                                           <h3 class="m-b-0 f-w-700 text-white">
                                               {{ dashboard.numberUnreceivedDispatches || 0 }}
                                           </h3>
                                       </div>
                                       <div class="col-auto">
                                           <router-link :to="{ name: 'requisitions' }">
                                               <i class="fa fa-thermometer-half text-c-red f-18"></i>
                                           </router-link>
                                       </div>
                                   </div>
                                        <!-- 
	                                        <p class="m-b-0 text-white">
	                                        	<span class="label label-danger m-r-10">
	                                        		+11%
	                                        	</span>
	                                        	From Previous Month
	                                        </p> 
	                                    -->
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="card prod-p-card card-danger">
                                    <div class="card-body">
                                        <div class="row align-items-center m-b-30">
                                            <div class="col">
                                                <h6 class="m-b-5 text-white">
                                                	Cancelled Requisitions
                                                </h6>
                                                <h3 class="m-b-0 f-w-700 text-white">
                                                	{{ dashboard.numberCancelledRequisitions || 0 }}
                                                </h3>
                                            </div>
                                            <div class="col-auto">
                                            	<router-link :to="{ name: 'requisitions' }">
                                                   <i class="fa fa-trash text-c-orenge f-18"></i>
                                               </router-link>
                                           </div>
                                       </div>
                                        <!-- 
	                                        <p class="m-b-0 text-white">
	                                        	<span class="label label-danger m-r-10">
	                                        		+11%
	                                        	</span>
	                                        	From Previous Month
	                                        </p> 
	                                    -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Limited Products -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card table-card">
                                    <div class="card-header">
                                        <h5>Limited Products</h5>
                                        <div class="card-header-right">
                                            <ul class="list-unstyled card-option">
                                                <li class="first-opt"><i class="feather icon-chevron-left open-card-option"></i></li>
                                                <li><i class="feather icon-maximize full-card"></i></li>
                                                <li><i class="feather icon-minus minimize-card"></i></li>
                                                <li><i class="feather icon-refresh-cw reload-card"></i></li>
                                                <li><i class="feather icon-trash close-card"></i></li>
                                                <li><i class="feather icon-chevron-left open-card-option"></i></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="card-block p-b-0">
                                        <div class="table-responsive">
                                            <table class="table table-hover m-b-0 text-center">
                                                <thead>
                                                    <tr>
                                                        <th>Product</th>
                                                        <th>Code/SKU</th>
                                                        <th>Manufacturer</th>
                                                        <th>Merchant</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="merchantProduct in dashboard.limitedStockProducts" :key="'merchant-product-' + merchantProduct.id"
                                                    >
                                                    <td>
                                                        <img 
                                                        class="img-thumbnail" 
                                                        style="width: 40px; margin-right: 5px" 
                                                        :src="merchantProduct.preview || (merchantProduct.hasOwnProperty('product') && merchantProduct.product.preview) ? merchantProduct.product.preview : '/storage/products/default-product.jpg'"
                                                        >

                                                        {{ merchantProduct.hasOwnProperty('product') ? merchantProduct.product.name : 'NA' | capitalize }}
                                                    </td>

                                                    <td>{{ merchantProduct.sku }}</td>

                                                    <td>
                                                        {{ merchantProduct.hasOwnProperty('manufacturer') && merchantProduct.manufacturer ? merchantProduct.manufacturer.name : 'Own Product' | capitalize }}
                                                    </td>

                                                    <td>
                                                        {{ merchantProduct.hasOwnProperty('merchant') ? merchantProduct.merchant.user_name : 'NA' | capitalize }}
                                                    </td>

                                                    <td>
                                                        <span :class="[! merchantProduct || merchantProduct.available_quantity == 0 ? 'badge-danger' : 'badge-warning', 'badge']">
                                                            {{ ! merchantProduct || merchantProduct.available_quantity == 0 ? 'Stock Out' : merchantProduct.available_quantity }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr 
                                                v-show="! dashboard.hasOwnProperty('limitedStockProducts') || ! dashboard.limitedStockProducts.length"
                                                >
                                                <td colspan="5">
                                                    <div class="alert alert-danger" role="alert">
                                                        Sorry, No data found.
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent seven days stock-in and stock-out -->
                        <!-- 
                        <div class="row">
                            <div class="col-md-6 col-xl-6">
                                <div class="card" v-if="dashboard.hasOwnProperty('stockIns')">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <doughnut-chart :data="dashboard.stockIns" :styles="{height: '300px', width: '100%', position: 'relative' }" />
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <p class="text-center">Last week stock in's</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-xl-6">
                                <div class="card" v-if="dashboard.hasOwnProperty('stockOuts')">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <doughnut-chart :data="dashboard.stockOuts" :styles="{ height: '300px', width: '100%', position: 'relative' }" />
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <p class="text-center">Last week stock out's</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    -->

                    <!-- Pending Users -->
                        <!-- 
                        <div class="row">
                            <div class="col-xl-3 col-md-6">
                                <div class="card comp-card">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <h6 class="m-b-25">Pending Managers</h6>
                                                <h3 class="f-w-700 text-c-green">
                                                    {{ dashboard.numberPendingManagers || 0 }}
                                                </h3>
                                            </div>
                                            <div class="col-auto">
                                                <router-link :to="{ name: 'managers' }">
                                                    <i class="fa fa-users bg-c-green"></i>
                                                </router-link>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="card comp-card">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <h6 class="m-b-25">Pending Merchants</h6>
                                                <h3 class="f-w-700 text-c-blue">
                                                    {{ dashboard.numberPendingMerchants || 0 }}
                                                </h3>
                                            </div>
                                            <div class="col-auto">
                                                <router-link :to="{ name: 'merchants' }">
                                                    <i class="fa fa-industry bg-c-blue"></i>
                                                </router-link>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="card comp-card">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <h6 class="m-b-25">Pending Owners</h6>
                                                <h3 class="f-w-700 text-c-yellow">
                                                    {{ dashboard.numberPendingOwner || 0 }}
                                                </h3>
                                            </div>
                                            <div class="col-auto">
                                                <router-link :to="{ name: 'owners' }">
                                                    <i class="fa fa-user bg-c-yellow"></i>
                                                </router-link>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="card comp-card">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <h6 class="m-b-25">Pending Warehouses</h6>
                                                <h3 class="f-w-700 text-c-orenge">
                                                    {{ dashboard.numberPendingWarehouses || 0 }}
                                                </h3>
                                            </div>
                                            <div class="col-auto">
                                                <router-link :to="{ name: 'warehouses' }">
                                                    <i class="fa fa-bullseye bg-c-orenge"></i>
                                                </router-link>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    -->
                    <!-- Pending Users (Chart) -->
                    <div class="row">
                        <div class="col-xl-3 col-md-6" v-if="dashboard.owners">
                            <div class="card comp-card">
                                <div class="card-body">
                                    <doughnut-chart :data="dashboard.owners" :styles="{height: '300px', width: '100%', position: 'relative' }" />
                                </div>
                                <div class="card-footer text-center">
                                    <p>Warehouse-Owners Status</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6" v-if="dashboard.warehouses">
                            <div class="card comp-card">
                                <div class="card-body">
                                    <doughnut-chart :data="dashboard.warehouses" :styles="{height: '300px', width: '100%', position: 'relative' }" />
                                </div>
                                <div class="card-footer text-center">
                                    <p>Warehouses Status</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6" v-if="dashboard.merchants">
                            <div class="card comp-card">
                                <div class="card-body">
                                    <doughnut-chart :data="dashboard.merchants" :styles="{height: '300px', width: '100%', position: 'relative' }" />
                                </div>
                                <div class="card-footer text-center">
                                    <p>Merchants Status</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6" v-if="dashboard.managers">
                            <div class="card comp-card">
                                <div class="card-body">
                                    <doughnut-chart :data="dashboard.managers" :styles="{height: '300px', width: '100%', position: 'relative' }" />
                                </div>
                                <div class="card-footer text-center">
                                    <p>Managers Status</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Warehouse Unused Container Percentages -->
                        <!-- 
                        <div class="row" v-if="dashboard.hasOwnProperty('warehouses') && dashboard.warehouses.length">
                            <div class="col-xl-12">
                                <div class="card proj-progress-card">
                                    <div class="card-block">
                                        <div class="row">
                                            <div class="col-xl-3 col-md-6" v-for="(warehouse, warehouseIndex) in dashboard.warehouses" :key="'warehouse-unused-container-percentage-' + warehouseIndex + '-warehouse-' + warehouse.id">
                                                <h6>{{ warehouse.name }} Unused Spaces (%)</h6>
                                                <pie-chart :data="warehouse" :styles="{ width: '100%', position: 'relative' }" v-if="warehouse.hasOwnProperty('datasets') && warehouse.datasets[0].data && warehouse.datasets[0].data.every(item => item != 0)" />
                                                <p class="text-center text-success" v-else>NA</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    -->
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script>
	
    import PieChart from '../components/PieChart.vue'
    import DoughnutChart from '../components/DoughnutChart.vue'

    export default {

      components : {

        PieChart,
        DoughnutChart,
        
    },

    data() {

     return {

        query : '',
        error : '',
        loading : false,

        dashboard : {

         numberPendingOwner : 0,
         numberPendingManagers : 0,
         numberPendingMerchants : 0,
         numberPendingWarehouses : 0,
         numberPendingDispatches : 0,
         numberPendingRequistiions : 0,
         numberPendingProductStocks : 0,
         limitedStockProducts : [],
         numberUnreceivedDispatches : 0,
         numberCancelledRequisitions : 0,

     }

 }

},

created(){

			// console.log('created');
            this.getDashboardOneData();			

        },

        filters: {

         capitalize: function (value) {

            if (!value) return ''

                const words = value.split(" ");

            for (let i = 0; i < words.length; i++) {

               if (words[i]) {

                   words[i] = words[i][0].toUpperCase() + words[i].substr(1);

               }

           }

           return words.join(" ");

       }

   },

   methods : {

     getDashboardOneData() {

        this.query = '';
        this.error = '';
        this.loading = true;
        this.dashboard = {};

        axios
        .get('/api/general-dashboard-one')
        .then(response => {
          if (response.status == 200) {
             this.dashboard = response.data;
         }
     })
        .catch(error => {
          this.error = error.toString();
						// Request made and server responded
						if (error.response) {
							console.log(error.response.data);
							console.log(error.response.status);
							console.log(error.response.headers);
							console.log(error.response.data.errors[x]);
						} 
						// The request was made but no response was received
						else if (error.request) {
							console.log(error.request);
						} 
						// Something happened in setting up the request that triggered an Error
						else {
							console.log('Error', error.message);
						}

					})
        .finally(response => {
          this.loading = false;
      });

    }

}

}

</script>