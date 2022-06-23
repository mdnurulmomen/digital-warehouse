
<template>

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
											<my-product-search-export-option
										  		:search-attributes="searchAttributes" 
										  		:caller-page="'my-products'" 
										  		:data-to-export="dataToExport" 
										  		:contents-to-download="productsToShow" 
										  		:pagination="pagination" 
										  		:current-tab="currentTab"
										  		
										  		@searchData="pagination.current_page = 1; searchData($event)" 
										  		@fetchAllContents="pagination.current_page = 1; fetchAllProducts()"
											/>
											
											<div class="col-sm-12 col-lg-12">
												<loading v-show="loading"></loading>

										  		<tab 
										  			v-show="searchAttributes.search === '' && ! searchAttributes.dateFrom && ! searchAttributes.dateTo && ! loading" 
										  			:tab-names="['retail', 'bulk']" 
										  			:current-tab="'retail'" 

										  			@showRetailContents="showRetailContents" 
										  			@showBulkContents="showBulkContents" 
										  		></tab>

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
																		<th>SKU</th>
																		<!-- <td>{{ last requested at }}</td> -->
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
																		<td>{{ content.sku }}</td>
																		<!-- <td>{{ last requested at }}</td> -->
																		
																		<td>
																			<button 
																				type="button" 
																				class="btn btn-grd-info btn-icon" 
																				v-tooltip.bottom-end="'View Details'"  
																				@click="showContentDetails(content)"
																			>
																				<i class="fa fa-eye"></i>
																			</button>
																		</td>
																    
																	</tr>
																	<tr 
																  		v-show="!productsToShow.length"
																  	>
															    		<td colspan="3">
																      		<div class="alert alert-danger" role="alert">
																      			Sorry, No data found.
																      		</div>
																    	</td>
																  	</tr>

																</tbody>
																<tfoot>
																	<tr>	
																		<th>Name</th>
																		<th>SKU</th>
																		<th>Actions</th>
																	</tr>
																</tfoot>
															</table>
														</div>
													</div>
													<div class="row d-flex align-items-center align-content-center">
														<div class="col-sm-2">
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
														<div class="col-sm-2">
															<button 
																type="button" 
																class="btn btn-primary btn-sm" 
																v-tooltip.bottom-end="'Reload'" 
																@click="pagination.current_page = 1; searchAttributes.search === '' ? fetchAllProducts() : searchData()"
															>
																Reload
																<i class="fa fa-sync"></i>
															</button>
														</div>
														<div class="col-sm-8">
															<pagination
																v-if="pagination.last_page > 1"
																:pagination="pagination"
																:offset="5"
																@paginate="searchAttributes.search === '' ? fetchAllProducts() : searchData()"
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

 		<!-- View Modal -->
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
						<div class="card">
							<div class="card-body">
										
								<div class="form-row">
									<label class="col-sm-6 col-form-label font-weight-bold text-right">
										Type :
									</label>
									<label class="col-sm-6 col-form-label text-left">
										{{ singleProductData.category ? singleProductData.category.name : 'Bulk Products'  | capitalize}}
									</label>
								</div>

								<div class="form-row">
									<label class="col-sm-6 col-form-label font-weight-bold text-right">
										Merchant :
									</label>
									<label class="col-sm-6 col-form-label text-left">
										{{ singleProductData.merchant ? singleProductData.merchant.user_name : 'None' | capitalize }}
									</label>
								</div>

								<div class="form-row">
									<label class="col-sm-6 col-form-label font-weight-bold text-right">
										Name :
									</label>
									<label class="col-sm-6 col-form-label text-left">
										{{ singleProductData.name | capitalize }}
									</label>
								</div>

								<div class="form-row">
									<label class="col-sm-6 col-form-label font-weight-bold text-right">
										Description :
									</label>
									<label class="col-sm-6 col-form-label text-left">
										<span v-html="singleProductData.description"></span>
									</label>
								</div>

								<div class="form-row">
									<label class="col-sm-6 col-form-label font-weight-bold text-right">
										SKU Code :
									</label>
									<label class="col-sm-6 col-form-label text-left">
										{{ singleProductData.sku }}
									</label>
								</div>

								<div class="form-row">
									<label class="col-sm-6 col-form-label font-weight-bold text-right">
										Price :
									</label>
									<label class="col-sm-6 col-form-label text-left">
										{{ singleProductData.selling_price || 'NA' }}
										{{ general_settings.official_currency_name || 'BDT' | capitalize }}
									</label>
								</div>

								<!-- 
								<div class="form-row">
									<label class="col-sm-6 col-form-label font-weight-bold text-right">
										Primary Qty
									</label>
									<label class="col-sm-6 col-form-label text-left">
										{{ singleProductData.initial_quantity + ' ' + singleProductData.quantity_type }} 
									</label>
								</div>
 								-->

								<div class="form-row">
									<label class="col-sm-6 col-form-label font-weight-bold text-right">
										Available Qty:
									</label>
									<label class="col-sm-6 col-form-label text-left">
										{{ singleProductData.available_quantity + ' ' + singleProductData.quantity_type }}
									</label>
								</div>

								<div class="form-row">
									<label class="col-sm-6 col-form-label font-weight-bold text-right">
										Dispatched Qty:
									</label>
									<label class="col-sm-6 col-form-label text-left">
										{{ singleProductData.dispatched_quantity + ' ' + singleProductData.quantity_type }}
									</label>
								</div>

								<div class="form-row">
									<label class="col-sm-6 col-form-label font-weight-bold text-right">
										Requested Qty:
									</label>
									<label class="col-sm-6 col-form-label text-left">
										{{ singleProductData.requested_quantity + ' ' + singleProductData.quantity_type }}
									</label>
								</div>

								<div class="form-row" v-show="singleProductData.has_serials && singleProductData.hasOwnProperty('serials') && singleProductData.serials.length">
									<label class="col-sm-6 col-form-label font-weight-bold text-right">
										Available Serials:
									</label>
									<label class="col-sm-6 col-form-label text-left">
										<span 
											v-if="singleProductData.has_serials && singleProductData.hasOwnProperty('serials') && singleProductData.serials.length"
										>
											<span v-for="(productSerial, productIndex) in singleProductData.serials">
												{{ productSerial }}
												<span v-show="(productIndex + 1) < singleProductData.serials.length">, </span> 
											</span>	
										</span>
									</label>
								</div>

								<div class="form-row" v-show="singleProductData.has_serials && singleProductData.hasOwnProperty('dispatched_serials') && singleProductData.dispatched_serials.length">
									<label class="col-sm-6 col-form-label font-weight-bold text-right">
										Dispatched Serials:
									</label>
									<label class="col-sm-6 col-form-label text-left">
										<span 
											v-if="singleProductData.has_serials && singleProductData.hasOwnProperty('dispatched_serials') && singleProductData.dispatched_serials.length"
										>
											<span v-for="(dispatchedProductSerial, dispatchedProductIndex) in singleProductData.dispatched_serials">
												{{ dispatchedProductSerial }}
												<span v-show="(dispatchedProductIndex + 1) < singleProductData.dispatched_serials.length">, </span> 
											</span>	
										</span>
									</label>
								</div>

								<div class="form-row">
									<label class="col-sm-6 col-form-label font-weight-bold text-right">Has Variation :</label>
									<label class="col-sm-6 form-control-plaintext">
										<span :class="[(singleProductData.has_variations && singleProductData.variations.length) ? 'badge-success' : 'badge-danger', 'badge']">{{ (singleProductData.has_variations && singleProductData.variations.length) ? 'Available' : 'NA' }}</span>
									</label>
								</div>

								<div 
									class="form-row" 
									v-if="singleProductData.has_variations && singleProductData.variations.length"
								>
									<label class="col-sm-6 col-form-label font-weight-bold text-right">
										Variations :
									</label>
									<div class="col-sm-12">
										<div class="form-row">
											
											<div 
												class="col-md-6 ml-auto" 
												v-for="(productVariation, variationIndex) in singleProductData.variations" 
												:key="'product-variation-' + variationIndex"
											>
												<div class="card">
													<div class="card-body">
														
														<div class="form-row">
															<label class="col-sm-6 col-form-label font-weight-bold text-right">
																Name :
															</label>
															<label class="col-sm-6 col-form-label text-left">
																{{ productVariation.variation ? productVariation.variation.name : 'NA' | capitalize }}
															</label>
														</div>

														<div class="form-row">
															<label class="col-sm-6 col-form-label font-weight-bold text-right">
																SKU :
															</label>
															<label class="col-sm-6 col-form-label text-left">
																{{ productVariation.sku }}
															</label>
														</div>

														<div class="form-row">
															<label class="col-sm-6 col-form-label font-weight-bold text-right">Price :</label>
															<label class="col-sm-6 col-form-label text-left">
																{{ productVariation.selling_price }}
																{{ general_settings.official_currency_name || 'BDT' | capitalize }}
															</label>
														</div>

														<!-- 
														<div class="form-row">
															<label class="col-sm-6 col-form-label font-weight-bold text-right">Primary Qty :</label>
															<label class="col-sm-6 col-form-label text-left">
																{{ productVariation.initial_quantity + ' ' + singleProductData.quantity_type }}
															</label>
														</div>
 														-->

														<div class="form-row">
															<label class="col-sm-6 col-form-label font-weight-bold text-right">Available Qty :</label>
															<label class="col-sm-6 col-form-label text-left">
																{{ productVariation.available_quantity + ' ' + singleProductData.quantity_type }}
															</label>
														</div>

														<div class="form-row">
															<label class="col-sm-6 col-form-label font-weight-bold text-right">Dispatched Qty :</label>
															<label class="col-sm-6 col-form-label text-left">
																{{ productVariation.dispatched_quantity + ' ' + singleProductData.quantity_type }}
															</label>
														</div>

														<div class="form-row">
															<label class="col-sm-6 col-form-label font-weight-bold text-right">Requested Qty :</label>
															<label class="col-sm-6 col-form-label text-left">
																{{ productVariation.requested_quantity + ' ' + singleProductData.quantity_type }}
															</label>
														</div>

														<div class="form-row" v-show="singleProductData.has_serials && productVariation.hasOwnProperty('serials') && productVariation.serials.length">
															<label class="col-sm-6 col-form-label font-weight-bold text-right">
																Available Serials :
															</label>
															<label class="col-sm-6 col-form-label text-left">
																<span 
																	v-if="singleProductData.has_serials && productVariation.hasOwnProperty('serials') && productVariation.serials.length"
																>
																	<span v-for="(productVariationSerial, productVariationIndex) in productVariation.serials">
																		{{ productVariationSerial }}
																		<span v-show="(productVariationIndex + 1) < productVariation.serials.length">, </span> 
																	</span>	
																</span>
															</label>
														</div>

														<div class="form-row" v-show="singleProductData.has_serials && productVariation.hasOwnProperty('dispatched_serials') && productVariation.dispatched_serials.length">
															<label class="col-sm-6 col-form-label font-weight-bold text-right">
																Dispatched Serials :
															</label>
															<label class="col-sm-6 col-form-label text-left">
																<span 
																	v-if="singleProductData.has_serials && productVariation.hasOwnProperty('dispatched_serials') && productVariation.dispatched_serials.length"
																>
																	<span v-for="(dispatchedProductVariationSerial, dispatchedProductVariationIndex) in productVariation.dispatched_serials">
																		{{ dispatchedProductVariationSerial }}
																		<span v-show="(dispatchedProductVariationIndex + 1) < productVariation.dispatched_serials.length">, </span> 
																	</span>	
																</span>
															</label>
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

					<div class="modal-footer">
						<button type="button" class="btn btn-secondary btn-sm btn-block" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>

		<!-- Create Request Modal -->
		<!-- 
		<div class="modal fade" id="request-createOrEdit-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">
							Make New Request
						</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
						
					<form 	
						class="form-horizontal" 
						v-on:submit.prevent="submitRequest" 
						autocomplete="off" 
					>

						<input type="hidden" name="_token" :value="csrf">

						<div class="modal-body">

							<div class="form-row">
								<div class="form-group col-md-12">
									<label for="inputUsername">Request for</label>
									<multiselect 
	                          			v-model="singleRequestData.requested_product"
	                          			placeholder="Product Name" 
	                              		label="name" 
	                              		track-by="id" 
	                              		:custom-label="objectNameWithCapitalized" 
	                              		:options="totalProducts" 
	                              		:required="true" 
	                              		:allow-empty="false"
	                              		selectLabel = "Press/Click"
	                              		deselect-label="Can't remove single value" 
	                              		:class="!errors.request.requested_product  ? 'is-valid' : 'is-invalid'"
	                              		@close="validateFormInput('requested_product')" 
	                              		@input="setRequestedProduct"
	                          		>
	                            	</multiselect>

	                            	<div 
	                                	class="invalid-feedback" 
	                                	style="display: block;" 
	                                	v-show="errors.request.requested_product"
	                            	>
								    	{{ errors.request.requested_product }}
								    </div>
								</div>
							</div>

							<div class="form-row">
								<div class="form-group col-md-12">
									<label for="inputFirstName">Subject</label>
									<input type="text" 
										class="form-control" 
										v-model="singleRequestData.subject" 
										placeholder="A suitable subject" 
										:class="!errors.request.request_subject  ? 'is-valid' : 'is-invalid'" 
										@change="validateFormInput('request_subject')" 
										required="true" 
									>

									<div class="invalid-feedback">
							        	{{ errors.request.request_subject }}
							  		</div>
								</div>
							</div>

							<div class="form-row">
								<div class="form-group col-md-12">
									<label for="inputFirstName">Description</label>
									<ckeditor 
		                              	class="form-control" 
		                              	:editor="editor" 
		                              	v-model="singleRequestData.description"
		                            >
	                              	</ckeditor>
								</div>
							</div>

						</div>
						<div class="modal-footer flex-column">
							<div class="col-sm-12 text-right" v-show="!submitForm">
								<span class="text-danger small">
							  		Please input required fields
							  	</span>
							</div>
							<div class="col-sm-12">
								<button type="button" class="btn btn-secondary float-left" data-dismiss="modal">
									Close
								</button>
								<button type="submit" class="btn btn-primary float-right" :disabled="!submitForm">
									Save
								</button>
							</div>
						</div>

					</form>
				</div>
			</div>
		</div> 
		-->

	</div>

</template>

<script type="text/javascript">

	import axios from 'axios';
	// import Multiselect from 'vue-multiselect';
	import CKEditor from '@ckeditor/ckeditor5-vue';
	import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

    let singleProductData = {
    	// name : null,
    	// description : null,
    	// sku : null,
    	// selling_price : null,
    	// initial_quantity : null,
    	// available_quantity : null,
    	// quantity_type : null,
    	// has_variations : null,
  		// variations : [
		// 	{},
		// ],
    	// product_category_id : null,
    	// merchant_id : null,
    	// category : {},
    	// merchant : {},
    };

    let singleRequestData = {

    };

	export default {

	    components: { 
			// multiselect : Multiselect,
			ckeditor: CKEditor.component,
		},

	    data() {

	        return {

	        	// query : '',
	        	error : '',
    			perPage : 10,
	        	loading : false,
	        	currentTab : 'retail',

	        	submitForm : true,

	        	// currentUser : {},

	        	editor: ClassicEditor,

	        	totalProducts : [],
	        	productsToShow : [],
	        	allFetchedProducts : [],

	        	pagination: {
		        	current_page: 1
		      	},

	        	singleProductData : singleProductData,
	        	singleRequestData : singleRequestData,

	        	errors : {
					request : {

					}
				},

				searchAttributes : {

	        		dates : {},
	        		search : '',
		        	dateTo : null,
		        	dateFrom : null,
		        	
		        	/*
			        	showPendingRequisitions : false,
			        	showCancelledRequisitions : false,
			        	showDispatchedRequisitions : false,
			        	showProduct : null,
		        	*/

	        	},

				dataToExport: {

					"Product": {
						field: "name",
						callback: (name) => {
							return this.$options.filters.capitalize(name)
						},
					},
					
					"Manufacturer": {
						field: "manufacturer",
						callback: (manufacturer) => {
							if (manufacturer) {
								return this.$options.filters.capitalize(manufacturer.name);
							}
							else{
								return 'Own Product'
							}
						},
					},

					SKU: 'sku',

					Price: 'selling_price',

					"Discount": {
						field: "discount",
						callback: (discount) => {
							if (discount) {
								return discount + ' %';
							}
							else{
								return 0  + ' %';
							}
						},
					},

					"Available Qty": 'available_quantity',

					"Qty Type": {
						callback: (object) => {
							if (object) {
								return object.quantity_type ?? 'Unit'
							}
							else{
								return;
							}
						},
					},

					"Variations": {

						callback: (object) => {

							let infosToReturn = '';

							if (object.has_variations && object.variations.length) {

								object.variations.forEach(
					
									(objectVariation, variationIndex) => {

										infosToReturn += ((variationIndex + 1) + '. ' + this.$options.filters.capitalize(objectVariation.variation.name) + ", \n" + 'Available Qty: ' + (objectVariation.available_quantity + ' ' + (object.product ? object.product.quantity_type : 'unit')) + "\n\n");

									}
									
								);


							}
							else {

								infosToReturn += 'NA';

							}
							
							return infosToReturn;

						},
					},

					"Serials": {

						callback: (object) => {

							let infosToReturn = '';

							// infosToReturn += (object.available_quantity ?? 0) + ' ';

							if (object.has_serials && ! object.has_variations && object.serials.length) {

								infosToReturn += `Serials:
								`;

								object.serials.forEach(
			
									(serial, serialIndex) => {

										infosToReturn +=  (serialIndex + 1) + ". " + serial  + "\n";					

									}
									
								);

							}

							if (object.has_variations && object.variations.length) {

								object.variations.forEach(
					
									(objectVariation, variationIndex) => {

										if (object.has_serials && objectVariation.serials.length) {

											infosToReturn += this.$options.filters.capitalize(objectVariation.variation.name) + " Serials:\n";

											objectVariation.serials.forEach(
						
												(variationSerial, variationSerialIndex) => {

													infosToReturn +=  (variationSerialIndex + 1) + ". " + variationSerial + "\n";					

												}
												
											);

										}

										infosToReturn += "\n";

									}
									
								);

							}
							
							return infosToReturn;

						},
					},

					/*
					"Warning Qty": {
						callback: (object) => {
							return (object.warning_quantity + ' ' + object.product ? object.product.quantity_type : ' Unit');
						},
					},
					*/

					'Total Dispatched': 'dispatched_quantity',

					'Pending Requests': 'requested_quantity',

					"Created": 'created_at',
					
				},

				general_settings : JSON.parse(window.localStorage.getItem('general_settings')),

	            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

	        }

		},
		
		created(){

			this.fetchAllProducts();
			// this.getCurrentUser();
		
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

		watch : {

			'searchAttributes.search' : function(val){
				
				this.pagination.current_page = 1;

				if (this.searchAttributes.search==='' && ! this.searchAttributes.dateTo && ! this.searchAttributes.dateFrom) {

					this.fetchAllProducts();

				}
				else {

					let format = /[`!@#$%^&*+\=\[\]{};':"\\|,.<>\/?~]/;

					if (! format.test(val)) {

						this.searchData();
					
					}

				}

			},
			
			'searchAttributes.dateFrom' : function(val){
				
				this.pagination.current_page = 1;

				if (this.searchAttributes.search==='' && ! this.searchAttributes.dateTo && ! this.searchAttributes.dateFrom) {

					this.fetchAllProducts();

				}
				else {

					this.searchData();
						
				}

			},

			'searchAttributes.dateTo' : function(val){
				
				this.pagination.current_page = 1;

				if (this.searchAttributes.search==='' && ! this.searchAttributes.dateTo && ! this.searchAttributes.dateFrom) {

					this.fetchAllProducts();

				}
				else {

					this.searchData();
						
				}

			},

		},
		
		methods : {
			
		/*
			getCurrentUser() {

				axios
					.get('/api/current-user/')
					.then(response => {
						if (response.status == 200) {
							this.currentUser = response.data.user;
							this.fetchAllProducts();
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
		*/
			fetchAllProducts() {
				
				// this.query = '';
				this.error = '';
				this.loading = true;
				this.allFetchedProducts = [];
				this.searchAttributes.search = '';
				
				axios
					.get('/api/my-products/' + this.perPage + "?page=" + this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							this.allFetchedProducts = response.data;
							this.showSelectedTabProducts();
							this.setTotalProducts();
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
    		showContentDetails(object) {		
				this.singleProductData = { ...object };
				// this.singleProductData = Object.assign({}, this.singleProductData, object);
				$('#product-view-modal').modal('show');
			},
			/*
				showRequestCreateForm() {
					$('#request-createOrEdit-modal').modal('show');
				},
				submitRequest() {

					if (!this.verifyUserInput()) {
						this.submitForm = false;
						return;
					}

					axios
						.post('/requests/' + this.perPage, this.singleRequestData)
						.then(response => {
							if (response.status == 200) {
								this.$toastr.s("New request has been stored", "Success");
								// this.allFetchedProducts = response.data;
								// this.query !== '' ? this.searchData() : this.showSelectedTabProducts();
								$('#request-createOrEdit-modal').modal('hide');
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
							this.fetchAllContainers();
						});

				},
			*/
			searchData() {

				this.error = '';
				this.allFetchedProducts = [];
				// this.pagination.current_page = 1;
				
				axios
				.post('/search-my-products/' + this.perPage + "?page=" + this.pagination.current_page, this.searchAttributes)
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

				if (this.searchAttributes.search === '') {
					this.fetchAllProducts();
				}
				else {
					this.searchData();
				}
				
    		},
			showSelectedTabProducts() {
				
				if (this.currentTab=='retail') {
					this.productsToShow = this.allFetchedProducts.retail.data;
					this.pagination = this.allFetchedProducts.retail;
				}
				else {
					this.productsToShow = this.allFetchedProducts.bulk.data;
					this.pagination = this.allFetchedProducts.bulk;
				}

			},
			setTotalProducts() {

				this.totalProducts = [];
				this.totalProducts = this.allFetchedProducts.retail.data.concat(this.allFetchedProducts.bulk.data)

			},
			showRetailContents() {
				this.currentTab = 'retail';
				this.showSelectedTabProducts();
			},
			showBulkContents() {
				this.currentTab = 'bulk';
				this.showSelectedTabProducts();
			},
		    setRequestedProduct() {
		    	if (this.singleProductData.requested_product && Object.keys(this.singleProductData.requested_product).length > 0) {
		    		this.singleProductData.requested_product_id = this.singleProductData.requested_product.id;
		    	}
		    },
		    verifyUserInput() {
				this.validateFormInput('requested_product');
				this.validateFormInput('request_subject');

				if (this.errors.request.constructor === Object && Object.keys(this.errors.request).length < 1) {

					return true;
				
				}

				return false;
			},
			objectNameWithCapitalized ({ name }) {
		      	if (name) {
				    name = name.toString()
				    return name.charAt(0).toUpperCase() + name.slice(1)
		      	}
		      	else 
		      		return ''
		    },
		    validateFormInput (formInputName) {

				this.submitForm = false;

				switch(formInputName) {

					case 'requested_product' :

						if (!this.singleProductData.requested_product || Object.keys(this.singleProductData.requested_product).length == 0) {
							this.errors.request.requested_product = 'Product is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.request, 'requested_product');
						}

						break;

					case 'request_subject' :

						if (!this.singleProductData.request_subject || !this.singleProductData.request_subject.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.request.request_subject = 'No special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.request, 'request_subject');
						}

						break;
				}
			}

		}
  	}

</script>

<style scoped>
	@import '~vue-multiselect/dist/vue-multiselect.min.css';
</style>