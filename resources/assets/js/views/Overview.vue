<template>
    <div class="pcoded-content">

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
             <div class="col-xl-3 col-md-6" v-show="userHasPermissionTo('view-product-stock-index')">
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

                            <div class="col-xl-3 col-md-6" v-show="userHasPermissionTo('view-requisition-index')">
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

                        <div class="col-xl-3 col-md-6" v-show="userHasPermissionTo('view-dispatch-index')">
                            <div class="card prod-p-card card-green">
                                <div class="card-body">
                                    <div class="row align-items-center m-b-30">
                                        <div class="col">
                                            <h6 class="m-b-5 text-white">
                                               Non-received Dispatches
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

                            <div class="col-xl-3 col-md-6" v-show="userHasPermissionTo('view-requisition-index')">
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

                    <!-- Pending Users (Chart) -->
                    <div class="row">
                        <div class="col-xl-3 col-md-6" v-if="dashboard.ownerStates && userHasPermissionTo('view-warehouse-owner-index')">
                            <div class="card comp-card">
                                <div class="card-body">
                                    <doughnut-chart :data="dashboard.ownerStates" :styles="{height: '300px', width: '100%', position: 'relative' }" />
                                </div>
                                <div class="card-footer text-center">
                                    <p>Warehouse-Owners Status</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6" v-if="dashboard.warehouseStates && userHasPermissionTo('view-warehouse-index')">
                            <div class="card comp-card">
                                <div class="card-body">
                                    <doughnut-chart :data="dashboard.warehouseStates" :styles="{height: '300px', width: '100%', position: 'relative' }" />
                                </div>
                                <div class="card-footer text-center">
                                    <p>Warehouses Status</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6" v-if="dashboard.merchantStates && userHasPermissionTo('view-merchant-index')">
                            <div class="card comp-card">
                                <div class="card-body">
                                    <doughnut-chart :data="dashboard.merchantStates" :styles="{height: '300px', width: '100%', position: 'relative' }" />
                                </div>
                                <div class="card-footer text-center">
                                    <p>Merchants Status</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6" v-if="dashboard.managerStates && userHasPermissionTo('view-manager-index')">
                            <div class="card comp-card">
                                <div class="card-body">
                                    <doughnut-chart :data="dashboard.managerStates" :styles="{height: '300px', width: '100%', position: 'relative' }" />
                                </div>
                                <div class="card-footer text-center">
                                    <p>Managers Status</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Warehouse Unused Container Percentages -->
                    
                    <div class="row" v-if="dashboard.hasOwnProperty('warehouseUnrentedContainers') && dashboard.warehouseUnrentedContainers.length && userHasPermissionTo('view-warehouse-index')">
                        <div class="col-xl-12">
                            <div class="card proj-progress-card">
                                <div class="card-block">
                                    <div class="row">
                                        <div class="col-xl-3 col-md-6" v-for="(warehouse, warehouseIndex) in dashboard.warehouseUnrentedContainers" :key="'warehouse-unused-container-percentage-' + warehouseIndex + '-warehouse-' + warehouse.id">
                                            <h6>{{ warehouse.name }} Unused Spaces (%)</h6>
                                            <pie-chart :data="warehouse" :styles="{ width: '100%', position: 'relative' }" v-if="warehouse.hasOwnProperty('datasets') && warehouse.datasets[0].data && warehouse.datasets[0].data.every(item => item != 0)" />
                                            <p class="text-center text-success" v-else>NA</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
               

                <!-- <div class="row" v-if="dashboard.hasOwnProperty('warehouseUnrentedContainers') && dashboard.warehouseUnrentedContainers.length">
                    <div class="col-xl-12">
                        <div class="card proj-progress-card">
                            <div class="card-block">
                                <div class="row">
                                    <div class="col-xl-3 col-md-6" v-for="(warehouse, warehouseIndex) in dashboard.warehouseUnrentedContainers" :key="'warehouse-unused-container-percentage-' + warehouseIndex + '-warehouse-' + warehouse.id">
                                        <h6 class="text-center">{{ warehouse.name }} Not Rented Spaces (%)</h6>
                                        <pie-chart :data="warehouse" :styles="{ height: '300px', width: '90%', position: 'relative' }" v-if="warehouse.hasOwnProperty('datasets') && warehouse.datasets[0].data && warehouse.datasets[0].data.every(item => item != 0)" />
                                            <p class="text-center text-success" v-else>NA</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> -->
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

           // numberPendingOwner : 0,
           // numberPendingManagers : 0,
           // numberPendingMerchants : 0,
           // numberPendingWarehouses : 0,
           // numberPendingDispatches : 0,
           
           numberPendingRequistiions : 0,
           numberPendingProductStocks : 0,
           numberCancelledRequisitions : 0,
           numberUnreceivedDispatches : 0,

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