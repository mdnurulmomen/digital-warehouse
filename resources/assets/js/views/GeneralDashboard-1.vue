<template v-if="userHasPermissionTo('view-gereral-dashboard-one')">
	<div class="pcoded-content">
		<div class="page-header card">
			<div class="row align-items-end">
				<div class="col-lg-8">
					<div class="page-header-title">
						<i class="feather icon-home bg-c-blue"></i>
						<div class="d-inline">
							<h5>Dashboard</h5>
							<span>You may view basic analytics here</span>
						</div>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="page-header-breadcrumb d-flex justify-content-center">
						<ul class=" breadcrumb breadcrumb-title">
							<li class="breadcrumb-item">
								<a href="index.html"><i class="feather icon-home"></i></a>
							</li>
							<li class="breadcrumb-item"><a href="#!">Dashboard</a> </li>
						</ul>
					</div>
				</div>
			</div>
		</div>

		<div class="pcoded-inner-content">
			<div class="main-body">
				<div class="page-wrapper">
					<div class="page-body">
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
                                                	<i class="fas fa-eye text-c-yellow f-18"></i>
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
                                                	<i class="fas fa-bullseye text-c-blue f-18"></i>
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
                                                	<i class="fas fa-eye text-c-blue f-18"></i>
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
                                <div class="card prod-p-card card-red">
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
                                                	<i class="fas fa-thermometer-half text-c-red f-18"></i>
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
                                <div class="card prod-p-card card-default">
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
                                                	<i class="fas fa-trash text-c-orenge f-18"></i>
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
											<table class="table table-hover m-b-0">
												<thead>
													<tr>
														<th>Name</th>
														<th>Product Code</th>
														<th>Merchant</th>
														<th>Status</th>
													</tr>
												</thead>
												<tbody>
													<tr v-for="product in dashboard.limitedStockProducts" :key="'product-' + product.id"
													>
														<td>{{ product.name | capitalize }}</td>
														<td>{{ product.sku }}</td>
														<td>{{ product.hasOwnProperty('merchant') ? product.merchant.user_name : 'NA' | capitalize }}</td>
														<td>
															<span :class="[! product.latest_stock || product.latest_stock.available_quantity == 0 ? 'badge-danger' : 'badge-warning', 'badge']">
																{{ ! product.latest_stock || product.latest_stock.available_quantity == 0 ? 'Stock Out' : product.latest_stock.available_quantity }}
															</span>
														</td>
													</tr>
													<tr 
												  		v-show="! dashboard.hasOwnProperty('limitedStockProducts') || ! dashboard.limitedStockProducts.length"
												  	>
											    		<td colspan="4">
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

                            <div class="col-xl-3 col-md-6">
                                <div class="card comp-card">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <h6 class="m-b-25">Pending Managers</h6>
                                                <h3 class="f-w-700 text-c-green">
                                                    {{ dashboard.numberPendingManagers || 0 }}
                                                </h3>
                                                <!-- <p class="m-b-0">May 23 - June 01 (2017)</p> -->
                                            </div>
                                            <div class="col-auto">
                                                <router-link :to="{ name: 'managers' }">
                                                    <i class="fas fa-users bg-c-green"></i>
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
                                                <!-- <p class="m-b-0">May 23 - June 01 (2017)</p> -->
                                            </div>
                                            <div class="col-auto">
                                                <router-link :to="{ name: 'merchants' }">
                                                    <i class="fas fa-industry bg-c-blue"></i>
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
                                                <!-- <p class="m-b-0">May 23 - June 01 (2017)</p> -->
                                            </div>
                                            <div class="col-auto">
                                                <router-link :to="{ name: 'owners' }">
                                                    <i class="fas fa-user bg-c-yellow"></i>
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
                                                <!-- <p class="m-b-0">May 23 - June 01 (2017)</p> -->
                                            </div>
                                            <div class="col-auto">
                                                <router-link :to="{ name: 'warehouses' }">
                                                    <i class="fas fa-bullseye bg-c-orenge"></i>
                                                </router-link>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	
	export default {

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