<template>
	<div class="pcoded-content">
		<div class="page-header card">
			<div class="row align-items-center">
				<div class="col-6">
					<div class="row">
						<div class="col-12 d-flex align-items-center">
							<div class="d-inline mr-2">
								<img src="icons/cms/dashboard-2.png">
							</div>

							<div class="d-inline">
								<h5>Analytics</h5>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-12 mt-1">
							<h6 class="text-nowrap">You may view individual analytics here</h6>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="pcoded-inner-content">
			<div class="main-body">
				<div class="page-wrapper">
					<div class="page-body">
						<div 
						class="card" 
						v-if="userHasPermissionTo('view-product-stock-index') && userHasPermissionTo('view-dispatch-index')"
						>
						<div class="card-header text-center">
							<div class="row">
								<div class="col-sm-12 form-group">
									<p class="font-weight-bold">Change Merchant & Date to view more</p>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<select 
									class="form-control" 
									v-model="stockMerchant" 
									@change="fetchStocksData()"
									>
									<option disabled :value="{}">Choose Merchant</option>
									<option 
									v-for="(merchant, merchantIndex) in allMerchants" 
									:value="merchant" 
									:key="'bar-selected-merchant-index-' + merchantIndex + '-merchant-' + merchant.id"
									>
									{{ merchant.user_name | capitalize }}
								</option>
							</select>
						</div>

						<div class="col-md-6">
							<v-date-picker 
								v-model="dateSelected" 
								mode="date" 
								color="red" 
								is-dark
								is-inline
								:max-date="new Date()" 
								:model-config="{ type: 'string', mask: 'YYYY-MM-DD' }" 
								:attributes="[ { key: 'today', dot: true } ]" 
								@input="fetchStocksData()"
							>
							<template v-slot="{ inputValue, inputEvents }">
								<input
									class="px-2 py-1 border rounded"
									:value="inputValue"
									v-on="inputEvents"
								/>
							</template>
						</v-date-picker>
					</div>
				</div>
			</div>

			<div class="card-body">
				<loading v-show="loading"></loading>

				<div class="row" v-show="!loading">
					<div class="col-md-6 col-xl-6">
						<div class="card" v-if="dashboard.hasOwnProperty('stockIns')">
							<div class="card-body">
								<div class="row align-items-center">
									<bar-chart :data="dashboard.stockIns" :styles="{ height: '300px', width: '100%', position: 'relative' }" />
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
									<bar-chart :data="dashboard.stockOuts" :styles="{ height: '300px', width: '100%', position: 'relative' }" />
								</div>
							</div>
							<div class="card-footer">
								<p class="text-center">Last week stock out's</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Limited Products -->
		<div class="row" v-if="userHasPermissionTo('view-merchant-product-index')">
			<div class="col-md-12">
				<div class="card table-card">
					<div class="card-header">
						<h5>{{ productMerchant.user_name || 'Merchant' | capitalize }} Limited Products</h5>
						<div class="card-header-right">
							<select
							class="form-control" 
							v-model="productMerchant" 
							@change="fetchSelectedMerchantProducts()"
							>
							<option disabled :value="{}">Choose Merchant</option>
							<option 
							v-for="(merchant, merchantIndex) in allMerchants" 
							:value="merchant" 
							:key="'selected-merchant-index-' + merchantIndex + '-merchant-' + merchant.id"
							>
							{{ merchant.user_name | capitalize }}
						</option>
					</select>
				</div>
			</div>
			<div class="card-block p-b-0">
				<loading v-show="loading"></loading>

				<div class="table-responsive" v-show="!loading">
					<table class="table table-hover m-b-0 text-center">
						<thead>
							<tr>
								<th>Product</th>
								<th>Code/SKU</th>
								<th>Manufacturer</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="merchantProduct in limitedStockProducts" :key="'merchant-product-' + merchantProduct.id"
							>
							<td>
								<img 
								class="img-thumbnail" 
								style="width: 40px; height: 40px; margin-right: 5px" 
								:src="merchantProduct.preview || (merchantProduct.hasOwnProperty('product') && merchantProduct.product.preview) ? merchantProduct.product.preview : '/storage/products/default-product.jpg'"
								>

								{{ merchantProduct.hasOwnProperty('product') ? merchantProduct.product.name : 'NA' | capitalize }}
							</td>

							<td>{{ merchantProduct.sku }}</td>

							<td>
								{{ merchantProduct.hasOwnProperty('manufacturer') && merchantProduct.manufacturer ? merchantProduct.manufacturer.name : 'Own Product' | capitalize }}
							</td>

							<td>
								<span :class="[! merchantProduct || merchantProduct.available_quantity == 0 ? 'badge-danger' : 'badge-warning', 'badge']" style="font-size: 100%;">
									{{ ! merchantProduct || merchantProduct.available_quantity == 0 ? 'Stock Out' : merchantProduct.available_quantity }}
								</span>
							</td>
						</tr>

						<tr 
						v-show="Object.keys(productMerchant).length && ! limitedStockProducts.length"
						>
						<td colspan="4">
							<div class="alert alert-danger" role="alert">
								Sorry, No data found.
							</div>
						</td>
					</tr>

					<tr 
					v-show="! Object.keys(productMerchant).length && ! limitedStockProducts.length"
					>
					<td colspan="4">
						<div class="alert alert-danger" role="alert">
							No Merchant Selected.
						</div>
					</td>
				</tr>
			</tbody>

			<tfoot>
				<tr>
					<th>Product</th>
					<th>Code/SKU</th>
					<th>Manufacturer</th>
					<th>Status</th>
				</tr>
			</tfoot>
		</table>

		<div class="card-footer">
			<div class="row d-flex align-items-center align-content-center">
				<div class="col-sm-2 col-4">
					<select 
					class="form-control" 
					v-model.number="perPage" 
					@change="changeNumberContents"
					>
					<option>10</option>

					<option>20</option>

					<option>30</option>

					<option>40</option>

					<option>50</option>
				</select>
			</div>
			<div class="col-sm-2 col-8">
				<button 
				type="button" 
				class="btn waves-effect waves-dark btn-primary btn-outline-primary btn-sm" 
				v-tooltip.bottom-end="'Reload'" 
				@click="pagination.current_page = 1; fetchSelectedMerchantProducts()"
				>
				Reload
				<i class="fa fa-sync"></i>
			</button>
		</div>
		<div class="col-sm-8 col-12 text-right form-group">
			<pagination
			v-if="pagination.last_page > 1"
			:pagination="pagination"
			:offset="5"
			@paginate="fetchSelectedMerchantProducts()"
			>
		</pagination>
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
</div>
</div>
</template>

<script>
	
	// import PieChart from '../components/PieChart.vue'
	import BarChart from '../components/BarChart.vue'

	export default {

		
		components : {

			// PieChart,
			BarChart,

		},
		

		data() {

			return {

				error : '',
				loading : false,

				dashboard : {

					stockIns : {},
					stockOuts : {}

				},

				pagination: {
					current_page: 1
				},

				perPage : 10,
				allMerchants : [],

	        	// dateSelected: '',
	        	stockMerchant : {},
	        	productMerchant : {},
	        	dateSelected: new Date().getFullYear() + '-' +  (new Date().getMonth() + 1) + '-' + new Date().getDate(),
	        	
	        	limitedStockProducts : [],

	        }

	    },

	    created(){

	    	this.fetchStocksData();
	    	this.fetchAllMerchants();		

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

	    	fetchStocksData() {

	    		if (! this.userHasPermissionTo('view-product-stock-index') || ! this.userHasPermissionTo('view-dispatch-index')) {

	    			this.error = 'Sorry, You do not have enough permissions to view merchant-products or dispatches';
	    			return;

	    		}

	    		this.error = '';
	    		this.loading = true;
	    		this.dashboard = {};

	    		axios
	    		.get('/api/general-dashboard-two/' + (this.stockMerchant.id ?? false) + '/' + this.dateSelected)
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

	    	},
	    	fetchAllMerchants() {

	    		if (! this.userHasPermissionTo('view-merchant-index')) {

	    			this.error = 'Sorry, You do not have enough permissions to view merchants';
	    			return;

	    		}

	    		this.error = '';
	    		this.loading = true;
	    		this.allMerchants = [];

	    		axios
	    		.get('/api/merchants/')
	    		.then(response => {
	    			if (response.status == 200) {
	    				this.allMerchants = response.data;
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

	    	},
	    	fetchSelectedMerchantProducts() {

	    		if (! Object.keys(this.productMerchant).length) {

	    			return;

	    		}
	    		
	    		this.error = '';
	    		this.loading = true;
	    		this.limitedStockProducts = [];

	    		axios
	    		.get('/api/merchant-limited-products/' + this.productMerchant.id + '/' + this.perPage + "?page=" + this.pagination.current_page)
	    		.then(response => {
	    			if (response.status == 200) {
	    				this.pagination = response.data.limitedStockProducts;
	    				this.limitedStockProducts = response.data.limitedStockProducts.data;
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

	    	},

	    	changeNumberContents() {

	    		this.pagination.current_page = 1;
	    		this.fetchSelectedMerchantProducts();

	    	},

	    }

	}

</script>