<template v-if="userHasPermissionTo('view-gereral-dashboard-two')">
	<div class="pcoded-content">
		<div class="page-header card">
			<div class="row align-items-end">
				<div class="col-lg-8">
					<div class="page-header-title">
						<i class="feather icon-home bg-c-blue"></i>
						<div class="d-inline">
							<h5>Dashboard</h5>
							<span>You may view individual analytics here</span>
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
                            <div class="col-md-6 col-xl-6">
                                <!-- Stock In Chart -->
                                <div class="card" v-if="dashboard.hasOwnProperty('stockIns')">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            
                                            <!-- 
                                            <doughnut-chart :data="dashboard.stockIns" :styles="{ height: '300px', width: '100%', position: 'relative' }" />
                                             -->

                                            <bar-chart :data="dashboard.stockIns" :styles="{ height: '300px', width: '100%', position: 'relative' }" />

                                        </div>
                                    </div>
                                    <div class="card-footer">
                                    	<p class="text-center">Last week stock in's</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-xl-6">
                                <!-- Stock Out Chart -->
                                <div class="card" v-if="dashboard.hasOwnProperty('stockOuts')">
                                    <div class="card-body">
                                        <div class="row align-items-center">
											
											<!-- 
                                            <doughnut-chart :data="dashboard.stockOuts" :styles="{ height: '300px', width: '100%', position: 'relative' }" />
                                             -->

                                            <bar-chart :data="dashboard.stockOuts" :styles="{ height: '300px', width: '100%', position: 'relative' }" />

                                        </div>
                                    </div>
                                    <div class="card-footer">
                                    	<p class="text-center">Last week stock out's</p>
                                    </div>
                                </div>
                            </div>
						</div>

						<div class="row" v-if="dashboard.hasOwnProperty('warehouses') && dashboard.warehouses.length">
							<div class="col-xl-12">
                                <div class="card proj-progress-card">
                                    <div class="card-block">
                                        <div class="row">
                                            <div class="col-xl-3 col-md-6" v-for="(warehouse, warehouseIndex) in dashboard.warehouses" :key="'warehouse-unused-container-percentage-' + warehouseIndex + '-warehouse-' + warehouse.id">
                                                <h6 class="text-center">{{ warehouse.name }} Not Rented Spaces (%)</h6>
                                                <!-- Pie Chart -->
		                                        <pie-chart :data="warehouse" :styles="{ height: '300px', width: '50%', position: 'relative' }" v-if="warehouse.hasOwnProperty('datasets') && warehouse.datasets[0].data && warehouse.datasets[0].data.every(item => item != 0)" />
		                                        <p class="text-center text-success" v-else>NA</p>
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
	
	import PieChart from '../components/PieChart.vue'
	import BarChart from '../components/BarChart.vue'

	export default {

		components : {

			PieChart,
			BarChart,
		
		},

		data() {

			return {

				query : '',
	        	error : '',
	        	loading : false,

	        	dashboard : {

	        		// stockIns : {},
	        		// stockOuts : {}
	        		// warehouses : [],

	        	}

			}

		},

		created(){
		
			this.getDashboardTwoData();			

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

			getDashboardTwoData() {

				this.query = '';
				this.error = '';
				this.loading = true;

				this.dashboard = {};
				
				axios
					.get('/api/general-dashboard-two')
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