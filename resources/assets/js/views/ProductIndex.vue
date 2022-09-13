<template v-if="userHasPermissionTo('view-product-index')">

	<div class="pcoded-content">
		<breadcrumb 
			:icon="'products'"
			:title="'products'" 
			:message="'All our products'"
		></breadcrumb>			

		<div class="pcoded-inner-content">
			<div class="main-body">
				<div class="page-wrapper">	
					<div class="page-body">
						<alert v-show="error" :error="error"></alert>
				
					  	<div class="row">
							<div class="col-sm-12">
							  	<div class="card">
									<div class="card-block">
										<div class="row">
											<div class="col-sm-12 sub-title">
											  	<search-and-addition-option 
											  		v-if="userHasPermissionTo('view-product-index') || userHasPermissionTo('create-product')" 
											  		:query="query" 
											  		:caller-page="'products'" 
											  		:required-permission = "'product'" 
											  		:data-to-export="dataToExport" 
											  		:contents-to-download="productsToShow" 
											  		:pagination="pagination" 
											  		:disable-add-button="allProductCategories.length==0 ? true : false" 
											  		
											  		@showContentCreateForm="showContentCreateForm" 
											  		@searchData="pagination.current_page = 1; searchData($event)" 
											  		@fetchAllContents="pagination.current_page = 1; fetchAllProducts()" 
											  		@importExcelFile="importExcelFile($event)" 
											  	></search-and-addition-option>
											</div>
											
											<div class="col-sm-12 col-lg-12">
										  		<loading v-show="loading"></loading>
										  		
										  		<tab 
										  			v-show="query === '' && ! loading" 
										  			:tab-names="['retail', 'bulk', 'trashed']" 
										  			:current-tab="'retail'" 

										  			@showRetailContents="showRetailContents" 
										  			@showBulkContents="showBulkContents" 
										  			@showTrashedContents="showTrashedContents" 
										  		></tab>

												<!-- 
												<table-with-soft-delete-option 
													:query="query" 
													:per-page="perPage"  
													:column-names="['name']" 
													:column-values-to-show="['name']" 
													:contents-to-show = "productsToShow" 
													:pagination = "pagination"

													@showContentDetails="showContentDetails($event)" 
													@openContentEditForm="openContentEditForm($event)" 
													@openContentDeleteForm="openContentDeleteForm($event)" 
													@openContentRestoreForm="openContentRestoreForm($event)" 
													@changeNumberContents="changeNumberContents($event)" 
													@fetchAllProducts="fetchAllProducts" 
													@searchData="searchData" 
												>	
												</table-with-soft-delete-option>
												-->

 												<div 
 													class="tab-content card-block pl-0 pr-0" 
 													v-show="!loading"
 												>
													<div class="card">
														<div class="table-responsive">
															<table class="table table-striped table-bordered nowrap text-center">
																<thead>
																	<tr>
																		<th>Name</th>
																		<th>Category</th>
																		<th># Merchants</th>
																		<th># Total</th>
																		<th>Actions</th>
																	</tr>
																</thead>

																<tbody>
																	<tr 
																		v-for="content in productsToShow" 
																		:key="'content-' + content.id" 
																		:class="content.id==singleProductData.id ? 'highlighted' : ''"
																	>
																		<td>{{ content.name | capitalize }}</td>

																		<td>{{ content.category ? content.category.name : '' | capitalize }}</td>
																		
																		<td>
																			<span 
																				v-show="content.merchants_count"
																				style="cursor: pointer;" 
																				@click="goProductMerchants(content)" 
																			>
																				{{ content.merchants_count || 0 }}
																			</span>

																			<span v-show="! content.merchants_count">
																				{{ content.merchants_count || 0 }}
																			</span>
																		</td>

																		<td>
																			{{ content.total_available || 0 }} 
																			{{ content.quantity_type | capitalize }}
																		</td>
																		
																		<td>
																			<button 
																				type="button" 
																				class="btn waves-effect waves-dark btn-info btn-outline-info btn-icon" 
																				v-tooltip.bottom-end="'View Details'"  
																				@click="showContentDetails(content)"
																			>
																				<i class="fa fa-eye"></i>
																			</button>

																			<button 
																				type="button" 
																				class="btn waves-effect waves-dark btn-primary btn-outline-primary btn-icon" 
																				v-tooltip.bottom-end="'Edit'"  
																				@click="openContentEditForm(content)" 
																				v-if="userHasPermissionTo('update-product')" 
																				v-show="! content.deleted_at"
																			>
																				<i class="fa fa-edit"></i>
																			</button>

																			<button 
																				type="button" 
																				class="btn waves-effect waves-dark btn-success btn-outline-success btn-icon" 
																				v-tooltip.bottom-end="'Merchants'"  
																				@click="goProductMerchants(content)" 
																				v-show="! content.deleted_at"
																				v-if="userHasPermissionTo('view-merchant-product-index')"
																			>
																				<i class="fa fa-users" aria-hidden="true"></i>
																			</button>

																			<button 
																				type="button" 
																				class="btn waves-effect waves-dark btn-danger btn-outline-danger btn-icon" 
																				v-tooltip.bottom-end="'Delete'" 
																				v-show="! content.deleted_at"
																				:disabled="content.product_immutability"  
																				@click="openContentDeleteForm(content)" 
																				v-if="userHasPermissionTo('delete-product')"
																			>
																				<i class="fa fa-trash"></i>
																			</button>

																			<button 
																				type="button" 
																				class="btn waves-effect waves-dark btn-warning btn-outline-warning btn-icon" 
																				v-tooltip.bottom-end="'Restore'" 
																				v-show="content.deleted_at"  
																				@click="openContentRestoreForm(content)" 
																				v-if="userHasPermissionTo('delete-product')"
																			>
																				<i class="fa fa-undo"></i>
																			</button>
																		</td>
																	</tr>

																	<tr 
																  		v-show="!productsToShow.length"
																  	>
															    		<td colspan="5">
																      		<div class="alert alert-danger" role="alert">
																      			Sorry, No data found.
																      		</div>
																    	</td>
																  	</tr>
																</tbody>

																<tfoot>
																	<tr>	
																		<th>Name</th>
																		<th>Category</th>
																		<th># Merchants</th>
																		<th># Total</th>
																		<th>Actions</th>
																	</tr>
																</tfoot>
															</table>
														</div>
													</div>
													<div class="row d-flex align-items-center">
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
																@click="pagination.current_page = 1; query === '' ? fetchAllProducts() : searchData()"
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
																@paginate="query === '' ? fetchAllProducts() : searchData()"
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

		<!-- 
		<asset-create-or-edit-modal 
			:create-mode="createMode" 
			:caller-page="'variation'" 
			:single-asset-data="singleProductData" 
			:csrf="csrf"

			@storeProduct="storeProduct($event)" 
			@updateAsset="updateAsset($event)" 
		></asset-create-or-edit-modal>
	 	-->

 		<product-create-or-edit-modal 
 			:csrf="csrf" 
 			:errors="errors" 
			:createMode="createMode" 
 			:productMode="productMode" 
 			:formSubmitted="formSubmitted" 
 			:singleProductData="singleProductData" 
 			:allVariationTypes="allVariationTypes" 
 			:allProductCategories="allProductCategories" 
 			@storeProduct="storeProduct()" 
 			@updateAsset="updateAsset()" 
 		/>
 
		<delete-confirmation-modal 
			:csrf="csrf" 
			:submit-method-name="'deleteAsset'" 
			:content-to-delete="singleProductData"
			:restoration-message="'But once you think, you can restore this item !'" 
			
			@deleteAsset="deleteAsset($event)" 
		></delete-confirmation-modal>

		<restore-confirmation-modal 
			:csrf="csrf" 
			:submit-method-name="'restoreAsset'" 
			:content-to-restore="singleProductData"
			:restoration-message="'This product will appear at everywhere !'" 

			@restoreAsset="restoreAsset($event)" 
		></restore-confirmation-modal> 		

 		<!-- Modal -->
		<div class="modal fade" id="product-view-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">{{ singleProductData.name | capitalize }} Details</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="modal-body">
						<div class="form-row d-flex">
							<div class="col-md-4 align-self-center text-center">
								<img :src="singleProductData.preview" class="img-fluid" alt="Product Preview" width="100px">
							</div>

							<div class="col-md-8">
								<div class="form-row">
									<label class="col-sm-4 col-form-label font-weight-bold text-right">
										Type :
									</label>
									<label class="col-sm-8 col-form-label">
										{{ singleProductData.category ? 'Retail Product' : 'Bulk Product' | capitalize }}
									</label>
								</div>

								<div class="form-row">
									<label class="col-sm-4 col-form-label font-weight-bold text-right">
										Category :
									</label>
									<label class="col-sm-8 col-form-label">
										{{ singleProductData.category ? singleProductData.category.name : 'NA' | capitalize }}
									</label>
								</div>

								<div class="form-row">
									<label class="col-sm-4 col-form-label font-weight-bold text-right">
										Name :
									</label>
									<label class="col-sm-8 col-form-label">
										{{ singleProductData.name | capitalize }}
									</label>
								</div>

								<div class="form-row">
									<label class="col-sm-4 col-form-label font-weight-bold text-right">
										Quantity Type :
									</label>
									<label class="col-sm-8 col-form-label">
										{{ singleProductData.quantity_type | capitalize }}
									</label>
								</div>

								<div class="form-row">
									<label class="col-sm-4 col-form-label font-weight-bold text-right">
										# Merchants :
									</label>
									<label class="col-sm-8 col-form-label">
										{{ singleProductData.merchants_count || 0 }}
									</label>
								</div>

								<div class="form-row">
									<label class="col-sm-4 col-form-label font-weight-bold text-right">
										Total Available :
									</label>
									<label class="col-sm-8 col-form-label">
										{{ singleProductData.total_available || 0 }}
										{{ singleProductData.quantity_type | capitalize }}
									</label>
								</div>

								<div class="form-row">
									<label class="col-sm-4 col-form-label font-weight-bold text-right">Serial :</label>
									<label class="col-sm-6 form-control-plaintext">
										<span :class="[singleProductData.has_serials ? 'badge-info' : 'badge-primary', 'badge']">{{ singleProductData.has_serials ? 'Available' : '--' }}</span>
									</label>
								</div>

								<div class="form-row">
									<label class="col-sm-4 col-form-label font-weight-bold text-right">Variation :</label>
									<label class="col-sm-6 form-control-plaintext">
										<span :class="[singleProductData.has_variations ? 'badge-info' : 'badge-primary', 'badge']">{{ singleProductData.has_variations ? 'Available' : '--' }}</span>
									</label>
								</div>

								<div class="form-row" v-if="singleProductData.has_variations && singleProductData.variations.length">
									<label class="col-sm-4 col-form-label font-weight-bold text-right">
										Variations :
									</label>
									<div class="col-sm-6 col-form-label">
										<div 
											class="form-group text-center" 
											v-for="(productVariation, variationIndex) in singleProductData.variations" 
											:key="'product-variation-' + variationIndex"
										>
											<img class="img-fluid" 
												:src="productVariation.preview || ''"
												:alt="productVariation.variation ? productVariation.variation.name : 'NA' + 'Picture'" 
												width="100px"
											>

											<p>
												
												{{ productVariation.variation ? productVariation.variation.name : 'NA' | capitalize }}
												
												<!-- 
													<span v-show="productVariation.variation && productVariation.variation.sub_variation">
														(
															{{ (productVariation.variation && productVariation.variation.sub_variation) ? productVariation.variation.sub_variation.name : 'NA'  }}
														)
													</span>
												-->

											</p>

											<!-- <span v-show="singleProductData.variations.length > variationIndex+1">, </span> -->
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="modal-footer">
						<button type="button" class="btn waves-effect waves-dark btn-secondary btn-outline-secondary btn-sm btn-block" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript">

  	import ProductCreateOrEditModal from '../components/ProductCreateOrEditModal';

    let singleProductData = {
    	// name : null,
    	// description : null,
    	// sku : null,
    	// price : null,
    	// initial_quantity : null,
    	// available_quantity : null,
    	// quantity_type : null,
    	// has_variations : null,
    	
    	preview : null,
    	variations : [],
		
		/*
		addresses : [
			{},
		],
		*/
	
    	// product_category_id : null,
    	// merchant_id : null,
    	// category : {},
    	// merchant : {},
    };

	export default {

	    components: { 
	    	ProductCreateOrEditModal, 
		},

	    data() {

	        return {

	        	// step : 1,
	        	query : '',
	        	error : '',
    			perPage : 10,
	        	loading : false,
	        	
	        	currentTab : 'retail',
	        	productMode : 'retail product',

	        	createMode : true,
	        	submitForm : true,
	        	formSubmitted : false,

	        	allVariationTypes : [],
	        	// availableVariations : [],
	        	allProductCategories : [],

	        	productsToShow : [],
	        	allFetchedProducts : [],

	        	pagination: {
		        	current_page: 1
		      	},

	        	singleProductData : singleProductData,

	        	errors : {

					variations : [],
					
					/*
					addresses : [
						{}
					],
					*/
					
				},

				dataToExport: {
					
					"Name": {
						field: "name",
						callback: (name) => {
							
							return this.$options.filters.capitalize(name);

						},
					},

					"Category": {
						field: "category",
						
						callback: (category) => {
							if (category) {
								return this.$options.filters.capitalize(category.name);
							}

							else {
								return '--';
							}
						},
					},

					'Quantity Type' : 'quantity_type', 

					"Serials": {
						field: "has_serials",
						
						callback: (has_serials) => {
							if (has_serials) {
								return 'Available';
							}

							else {
								return '--';
							}
						},
					},

					"Variations": {

						callback: (object) => {

							let infosToReturn = '';

							if (object.has_variations && object.variations.length) {

								object.variations.forEach(
					
									(objectVariation, variationIndex) => {

										infosToReturn += (variationIndex + 1) + '. ' + this.$options.filters.capitalize(objectVariation.variation.name) + ", \n";

									}
									
								);


							}
							else {

								infosToReturn += 'NA';

							}
							
							return infosToReturn;

						},
					},
					
				},

	            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

	        }

		},
		
		created(){

			this.fetchAllProducts();
			
			if (this.userHasPermissionTo('view-product-asset-index')) {

				this.fetchAllVariationTypes();
				this.fetchProductAllCategories();

			}

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

			fetchAllProducts() {

				this.query = '';
				this.error = '';
				this.loading = true;
				this.allFetchedProducts = [];
				
				axios
					.get('/api/products/' + this.perPage + "?page=" + this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							this.allFetchedProducts = response.data;
							this.showSelectedTabProducts();
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
			fetchProductAllCategories() {

				if (! this.userHasPermissionTo('view-product-asset-index')) {

					this.error = 'You do not have permission to view product-categories';
					return;

				}
				
				this.query = '';
				this.error = '';
				this.loading = true;
				this.allProductCategories = [];
				
				axios
					.get('/api/product-categories/')
					.then(response => {
						if (response.status == 200) {
							this.allProductCategories = response.data.data;
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
			fetchAllVariationTypes() {

				if (! this.userHasPermissionTo('view-product-asset-index')) {

					this.error = 'You do not have permission to view variation types';
					return;

				}
				
				this.query = '';
				this.error = '';
				this.loading = true;
				this.allVariationTypes = [];
				
				axios
					.get('/api/variation-types/')
					.then(response => {
						if (response.status == 200) {
							// this.allVariationTypes = response.data;
							this.allVariationTypes = response.data.data.filter(variationType=>variationType.variations.length > 1) ?? [];
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
			searchData(emitedValue=false) {

				if (emitedValue) {
					this.query=emitedValue;
				}

				this.error = '';
				this.allFetchedProducts = [];
				
				axios
				.get(
					"/api/search-products/" + this.query + "/" + this.perPage + "?page=" + this.pagination.current_page
				)
				.then(response => {
					this.allFetchedProducts = response.data;
					this.productsToShow = this.allFetchedProducts.all.data;
					this.pagination = this.allFetchedProducts.all;
				})
				.catch(e => {
					this.error = e.toString();
				});

			},
			changeNumberContents() {
				
				this.pagination.current_page = 1;

				if (this.query === '') {
					this.fetchAllProducts();
				}
				else {
					this.searchData();
				}
    		},
    		showContentDetails(object) {		
				// this.singleProductData = { ...object };
				// this.singleProductData = Object.assign({}, this.singleProductData, object);
				this.singleProductData = JSON.parse(JSON.stringify(object));
				$('#product-view-modal').modal('show');
			},
			showContentCreateForm() {
				// this.step = 1;
				this.createMode = true;
	        	this.submitForm = true;
	        	
				this.singleProductData = {
				
					// name : null,
			    	// description : null,
			    	// sku : null,
			    	// price : null,
			    	// initial_quantity : null,
			    	// available_quantity : null,
			    	// quantity_type : null,
			    	// has_variations : null,
			    	
			    	preview : null,

			    	variations : [],
					
					/*
					addresses : [
						{},
					],
					*/
				
			    	// product_category_id : null,
			    	// merchant_id : null,
			    	// category : {},
			    	// merchant : {},
				
				};

				this.errors = {
					
					variations : [],
					
					/*
					addresses : [
						{},
					],
					*/

				};

				$('#product-createOrEdit-modal').modal('show');
			},
			openContentEditForm(object) {

				// this.step = 1;
				this.createMode = false;
	        	this.submitForm = true;
				
				this.errors = {
					
					variations : [],
					/* addresses : [], */

				};

				/*
				object.addresses.forEach(
					(productAddress, index) => {
						this.errors.addresses.push({});
					}
				);
				*/

				if (object.hasOwnProperty('category') && object.category) {

					this.productMode = 'retail product';

					if (object.has_variations && object.variations.length) {
						
						object.variations.forEach(
							(productVariation, index) => {
								this.errors.variations.push({});
							}
						);
						
						// this.availableVariations = object.variation_type.variations ?? [];
						
						/*
						this.availableVariations = this.allVariationTypes.find(
							(variationType) => variationType.id == object.variation_type.id && variationType.name == object.variation_type.name
						).variations;
						*/
					
					}

				}
				else {

					this.productMode = 'bulk product';

				}

				this.singleProductData = JSON.parse(JSON.stringify(object));

				/*this.setAvailableShelvesAndUnits();*/

				$('#product-createOrEdit-modal').modal('show');
			},
			openContentDeleteForm(object) {

				this.singleProductData = JSON.parse(JSON.stringify(object));
				$('#delete-confirmation-modal').modal('show');

			},
			openContentRestoreForm(object) {

				this.singleProductData = JSON.parse(JSON.stringify(object));
				$('#restore-confirmation-modal').modal('show');

			},
			storeProduct() {

				this.formSubmitted = true;

				axios
					.post('/products/' + this.perPage, this.singleProductData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("New product has been stored", "Success");

							this.pagination.current_page = 1; 
							this.allFetchedProducts = response.data;
							this.query !== '' ? this.searchData() : this.showSelectedTabProducts();

							$('#product-createOrEdit-modal').modal('hide');
						}
					})
					.catch(error => {
						if (error.response.status == 422) {
							for (var x in error.response.data.errors) {
								this.$toastr.w(error.response.data.errors[x], "Warning");
							}
				      	}
					})
					.finally(response => {
						this.formSubmitted = false;
						// this.fetchAllContainers();
					});

			},
			updateAsset() {

				this.formSubmitted = true;

				axios
					.put('/products/' + this.singleProductData.id + '/' + this.perPage, this.singleProductData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Product has been updated", "Success");

							this.pagination.current_page = 1; 
							this.allFetchedProducts = response.data;
							this.query !== '' ? this.searchData() : this.showSelectedTabProducts();

							$('#product-createOrEdit-modal').modal('hide');
						}
					})
					.catch(error => {
						if (error.response.status == 422) {
							for (var x in error.response.data.errors) {
								this.$toastr.w(error.response.data.errors[x], "Warning");
							}
				      	}
					})
					.finally(response => {
						this.formSubmitted = false;
						// this.fetchAllContainers();
					});

			},
			deleteAsset() {
				
				if (this.singleProductData.product_immutability) {
					this.$toastr.w("Product is in use", "Warning");
					this.submitForm = false;
					return;
				}

				this.formSubmitted = true;

				axios
					.delete('/products/' + this.singleProductData.id + '/' + this.perPage)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Product has been deleted", "Success");

							this.pagination.current_page = 1; 
							this.allFetchedProducts = response.data;
							this.query !== '' ? this.searchData() : this.showSelectedTabProducts();

							$('#delete-confirmation-modal').modal('hide');
						}
					})
					.catch(error => {
						if (error.response.status == 422) {
							for (var x in error.response.data.errors) {
								this.$toastr.w(error.response.data.errors[x], "Warning");
							}
				      	}
					})
					.finally(response => {
						this.formSubmitted = false;
						// this.fetchAllContainers();
					});

			},
			restoreAsset() {

				this.formSubmitted = true;

				axios
					.patch('/products/' + this.singleProductData.id + '/' + this.perPage)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Product has been restored", "Success");

							this.pagination.current_page = 1; 
							this.allFetchedProducts = response.data;
							this.query !== '' ? this.searchData() : this.showSelectedTabProducts();

							$('#restore-confirmation-modal').modal('hide');
						}
					})
					.catch(error => {
						if (error.response.status == 422) {
							for (var x in error.response.data.errors) {
								this.$toastr.w(error.response.data.errors[x], "Warning");
							}
				      	}
					})
					.finally(response => {
						this.formSubmitted = false;
						// this.fetchAllContainers();
					});

			},
			goProductMerchants(object) {

				// console.log(object);
				this.$router.push({ name: 'product-merchants', params: { product: object, productId: object.id }});

			},
			showSelectedTabProducts() {
				
				if (this.currentTab=='retail') {
					this.productsToShow = this.allFetchedProducts.retail ? this.allFetchedProducts.retail.data : [];
					this.pagination = this.allFetchedProducts.retail ?? {};
				}
				else if (this.currentTab=='trashed') {
					this.productsToShow = this.allFetchedProducts.trashed ? this.allFetchedProducts.trashed.data : [];
					this.pagination = this.allFetchedProducts.trashed ?? {};
				}
				else {
					this.productsToShow = this.allFetchedProducts.bulk ? this.allFetchedProducts.bulk.data : [];
					this.pagination = this.allFetchedProducts.bulk ?? {};
				}

			},
			showRetailContents() {
				this.currentTab = 'retail';
				this.showSelectedTabProducts();
			},
			showBulkContents() {
				this.currentTab = 'bulk';
				this.showSelectedTabProducts();
			},
			showTrashedContents() {
				this.currentTab = 'trashed';
				this.showSelectedTabProducts();
			},
			importExcelFile(fileToExport) {

				// console.log(fileToExport);

				this.formSubmitted = true;

				fileToExport.set('perPage', this.perPage);

				axios
					.post('/import-products', fileToExport)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("New products has been stored", "Success");

							this.pagination.current_page = 1; 
							this.allFetchedProducts = response.data;
							this.query !== '' ? this.searchData() : this.showSelectedTabProducts();
							
							$('#products-importing-modal').modal('hide');
						}
					})
					.catch(error => {
						if (error.response.status == 422) {
							for (var x in error.response.data.errors) {
								this.$toastr.w(error.response.data.errors[x], "Warning");
							}
				      	}
					})
					.finally(response => {
						this.formSubmitted = false;
						// this.fetchAllContainers();
					});

			},
            
		}
  	}

</script>