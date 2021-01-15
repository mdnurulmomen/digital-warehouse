
<template>

	<div class="pcoded-content">

		<breadcrumb 
			:title="'requisitions'" 
			:message="'All our requisitions'"
		></breadcrumb>			

		<div class="pcoded-inner-content">
			<div class="main-body">
				<div class="page-wrapper">	
					<div class="page-body">

						<loading v-show="loading"></loading>

						<alert v-show="error" :error="error"></alert>
				
					  	<div class="row" v-show="!loading">
							<div class="col-sm-12">
							  	<div class="card">
									<div class="card-block">
										<div class="row">											

											<div class="col-sm-12 sub-title">
										  	<!-- 
											  	<search-and-addition-option 
											  		:query="query" 
											  		:caller-page="'requisition'" 
											  		
											  		@showContentCreateForm="showContentCreateForm" 
											  		@searchData="searchData($event)" 
											  		@fetchAllContents="fetchAllRequisitions"
											  	></search-and-addition-option>
 											-->
 												<div class="row d-flex align-items-center">										  	
											  		<div class="col-sm-3 text-left">	
															Requisitions List
											  		</div>
											  		<div class="col-sm-9 was-validated text-center">
											  			<input 	type="text" 
														  		v-model="query" 
														  		pattern="[^'!#$%^()\x22]+" 
														  		class="form-control" 
														  		placeholder="Search"
													  	>
													  	<div class="invalid-feedback">
													  		Please search with releavant input
													  	</div>
											  		</div>
										  			<!-- 
											  		<div class="col-sm-3 text-right">
											  			<button 
												  			class="btn btn-success btn-outline-success btn-sm" 
												  			@click="$emit('showContentCreateForm')"
											  			>
											  				<i class="fa fa-plus"></i>
											  				New {{ callerPage | capitalize }}
											  			</button>
											  		</div>
 													-->
											  	</div>
											</div>
											
											<div class="col-sm-12 col-lg-12">
										  		
										  		<tab 
										  			v-show="query === ''" 
										  			:tab-names="['pending', 'dispatched']" 
										  			:current-tab="'pending'" 

										  			@showPendingContents="showPendingContents" 
										  			@showDispatchedContents="showDispatchedContents" 
										  		></tab>

 												<div class="tab-content card-block">
													<div class="card">
														<div class="table-responsive">
															<table class="table table-striped table-bordered nowrap text-center">
																<thead>
																	<tr>
																		<th>Subject</th>
																		<th>Status</th>
																		<th>Actions</th>
																	</tr>
																</thead>
																<tbody>

																	<tr v-for="content in requisitionsToShow" :key="'content-' + content.id"
																	>
																		<td>{{ content.subject }}</td>
																		<td>
																			<span :class="[singleRequisitionData.status ? 'badge-success' : 'badge-danger', 'badge']">
																				{{ singleRequisitionData.status ? 'Despatched' : 'Pending' }}
																			</span>
																		</td>
																		<td>
																			
																			<button 
																				type="button" 
																				class="btn btn-grd-info btn-icon"  
																				@click="showContentDetails(content)"
																			>
																				<i class="fas fa-eye"></i>
																			</button>

																			<button 
																				type="button" 
																				class="btn btn-grd-warning btn-icon"  
																				@click="showProductDispatchForm(content)"
																			>
																				<i class="fas fa-truck"></i>
																			</button>

																		</td>
																    
																	</tr>
																	<tr 
																  		v-show="!requisitionsToShow.length"
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
																		<th>Status</th>
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
																@click="query === '' ? fetchAllRequisitions() : searchData()"
															>
																Reload
																<i class="fas fa-sync"></i>
															</button>
														</div>
														<div class="col-sm-8">
															<pagination
																v-if="pagination.last_page > 1"
																:pagination="pagination"
																:offset="5"
																@paginate="query === '' ? fetchAllRequisitions() : searchData()"
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

 		<!--Create Or Edit Modal -->
 	<!--
		<div class="modal fade" id="requisition-createOrEdit-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">
							Make Requisition
						</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
						
					<form 	
						class="form-horizontal" 
						v-on:submit.prevent="storeRequisition()" 
						autocomplete="off" 
						novalidate="true" 
					>
						<input type="hidden" name="_token" :value="csrf">

						<div class="modal-body">

							<transition-group name="fade">

								<div 
									class="row" 
									v-bind:key="'requisition-modal-step-' + 1" 
									v-show="!loading && step==1"
								>								  	

							        <div class="col-md-12">
							        	
										<div class="form-row">
											<div class="form-group col-md-12">
												<label for="inputFirstName">Subject</label>
												<input type="text" 
													class="form-control" 
													v-model="singleRequisitionData.subject" 
													placeholder="Relevant Subject" 
													:class="!errors.subject  ? 'is-valid' : 'is-invalid'" 
													@input="validateFormInput('subject')" 
													required="true" 
												>

												<div class="invalid-feedback">
										        	{{ errors.subject }}
										  		</div>
											</div>
										</div>

										<div class="form-row">
											<div class="form-group col-md-12">
												<label for="inputFirstName">Description</label>
												<ckeditor 
					                              	class="form-control" 
					                              	:editor="editor" 
					                              	v-model="singleRequisitionData.description"
					                            >
				                              	</ckeditor>
											</div>
										</div>
								    
								    	<div class="form-row">
									    	<div class="form-group col-sm-12 mb-2 text-right">
								          		<div class="text-danger small mb-1" v-show="!submitForm">
											  		Please input required fields
									          	</div>
									          	<button type="button" class="btn btn-outline-secondary btn-sm btn-round" v-on:click="nextPage">
							                    	<i class="fa fa-2x fa-angle-double-right" aria-hidden="true"></i>
							                  	</button>
								          	</div>
								    	</div>

							        </div>

							    </div>

							    <div 
									class="row" 
									v-bind:key="2" 
									v-show="!loading && step==2"
								>
									<h2 class="mx-auto mb-4 lead">Required Products</h2>

									<div 
										class="col-md-12"
										v-for="(requiredProduct, index) in singleRequisitionData.products" 
														:key="'required-product-' + index"
									>
										<div 
											class="card"
											v-if="singleRequisitionData.products.length==errors.product_id.length && singleRequisitionData.products.length==errors.product_quantity.length"
										>
											<div class="card-body">

												<div class="form-row">
													<div class="form-group col-md-6">
														<label for="inputFirstName">Select Product</label>
														<multiselect 
					                              			v-model="singleRequisitionData.products[index].product"
					                              			placeholder="Product Name" 
					                              			label="name" 
					                                  		track-by="id" 
					                                  		:custom-label="objectNameWithCapitalized" 
					                                  		:options="availableProducts" 
					                                  		:required="true" 
					                                  		:allow-empty="false"
					                                  		:class="!errors.product_id[index] ? 'is-valid' : 'is-invalid'" 
					                                  		:disabled="singleRequisitionData.products.length > (index+1)"
					                                  		@input="setRequiredProduct(index)"
					                                  		@close="validateFormInput('product_id')" 
					                              		>
					                                	</multiselect>
					                                	<div 
						                                	class="invalid-feedback" 
						                                	style="display: block;" 
						                                	v-show="errors.product_id[index]"
					                                	>
													    	{{ errors.product_id[index] }}
													    </div>
													</div>

													<div class="form-group col-md-6">
														<label for="inputFirstName">Quantity</label>
														<input type="number" 
															class="form-control" 
															v-model.number="singleRequisitionData.products[index].quantity" 
															placeholder="Product Quantity" 
															:class="!errors.product_quantity[index]  ? 'is-valid' : 'is-invalid'" 
															@change="validateFormInput('product_quantity')" 
															required="true" 
															min="1"
														>

														<div class="invalid-feedback">
												        	{{ errors.product_quantity[index] }}
												  		</div>
													</div>
												</div>

											</div>
										</div>
									</div>

									<div class="col-md-12">
										<div class="form-row">
											<div class="form-group col-md-6">
												<button 
													type="button" 
													class="btn waves-effect waves-light hor-grd btn-grd-primary btn-sm btn-block" 
													@click="addMoreProduct()"
												>
													More Product
												</button>
											</div>
											<div class="form-group col-md-6">
												<button 
													type="button" 
													class="btn waves-effect waves-light hor-grd btn-grd-info btn-sm btn-block" 
													@click="removeProduct()"
												>
													Remove Product
												</button>
											</div>
										</div>
									</div>

									<div class="col-sm-12">
										<div class="form-row">
											<div class="col-sm-12 text-right" v-show="!submitForm">
												<span class="text-danger small mb-1">
											  		Please input required fields
											  	</span>
											</div>
											<div class="col-sm-12">
												<button type="button" class="btn btn-outline-secondary btn-sm btn-round float-left" v-on:click="step-=1">
							                    	<i class="fa fa-2x fa-angle-double-left" aria-hidden="true"></i>
							                  	</button>
												<button type="submit" class="btn btn-primary float-right" :disabled="!submitForm">
													Save
												</button>
											</div>
										</div>
									</div>

									
								</div>

							</transition-group>

						</div>

					</form>
				</div>
			</div>
		</div>
 	-->

 		<!-- Modal -->
		<div class="modal fade" id="requisition-view-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Requisition Details</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="modal-body">
						<div class="card">
							<div class="card-body">
								
								<ul class="nav nav-tabs tabs justify-content-center" role="tablist">
									<li class="nav-item">
										<a class="nav-link active" data-toggle="tab" href="#requisition-profile" role="tab">
											Requisition
										</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" data-toggle="tab" href="#requisition-product" role="tab">
											Product
										</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" data-toggle="tab" href="#requisition-address" role="tab">
											Address
										</a>
									</li>
								</ul>

								<div class="tab-content tabs card-block">
									
									<div class="tab-pane active" id="requisition-profile" role="tabpanel">	
										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Subject :
											</label>
											<label class="col-sm-6 col-form-label">
												{{ singleRequisitionData.subject }}
											</label>
										</div>

										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Description :
											</label>
											<label class="col-sm-6 col-form-label">
												<span v-html="singleRequisitionData.description"></span>
											</label>
										</div>

										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">Status :</label>
											<label class="col-sm-6 col-form-label">
												<span :class="[singleRequisitionData.status ? 'badge-success' : 'badge-danger', 'badge']">{{ singleRequisitionData.status ? 'Available' : 'NA' }}</span>
											</label>
										</div>

										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Requested on :
											</label>
											<label class="col-sm-6 col-form-label">
												{{ singleRequisitionData.created_at }}
											</label>
										</div>
									</div>

									<div class="tab-pane" id="requisition-product" role="tabpanel">
										<div 
											class="form-row" 
											v-if="singleRequisitionData.products && singleRequisitionData.products.length"
										>
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Product Detail :
											</label>
											<div class="col-sm-6">
												<div class="form-row">
													
													<div 
														class="col-md-12 ml-auto" 
														v-for="(requiredProduct, productIndex) in singleRequisitionData.products" 
														:key="'requisition-detail-' + requiredProduct.id + productIndex"
													>
														<div class="card">
															<div class="card-body">
																<div class="form-row">
																	<label class="col-sm-6 col-form-label font-weight-bold text-right">
																		Product Name :
																	</label>
																	<label class="col-sm-6 col-form-label">
																		{{ requiredProduct.product ? requiredProduct.product.name : 'NA' }}
																	</label>
																</div>

																<div class="form-row">
																	<label class="col-sm-6 col-form-label font-weight-bold text-right">
																		Total Quantity :
																	</label>
																	<label class="col-sm-6 col-form-label">
																		{{ requiredProduct.quantity }}
																	</label>
																</div>

																<div class="form-row">
																	<label class="col-sm-6 col-form-label font-weight-bold text-right">
																		Available Quantity :
																	</label>
																	<label class="col-sm-6 col-form-label">
																		{{ requiredProduct.product ? requiredProduct.product.available_quantity : 'NA' }}
																	</label>
																</div>

															</div>
														</div>
													</div>

												</div>
											</div>
										</div>
									</div>

									<div class="tab-pane" id="requisition-address" role="tabpanel">	
										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Delivery Service :
											</label>
											<label class="col-sm-6 col-form-label">
												<span :class="[singleRequisitionData.address ? 'badge-success' : 'badge-danger', 'badge']">{{ singleRequisitionData.address ? 'Available' : 'NA' }}</span>
											</label>
										</div>

										<div class="form-row" v-if="singleRequisitionData.address">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Delivery Address :
											</label>
											<label class="col-sm-6 col-form-label">
												<span v-html="singleRequisitionData.address.delivery_address"></span>
											</label>
										</div>

										<div class="form-row" v-show="singleRequisitionData.agent">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Agent Name :
											</label>
											<label class="col-sm-6 col-form-label">
												{{ singleRequisitionData.agent ? singleRequisitionData.agent.name : 'NA' }}
											</label>
										</div>

										<div class="form-row" v-show="singleRequisitionData.agent">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Agent Mobile :
											</label>
											<label class="col-sm-6 col-form-label">
												{{ singleRequisitionData.agent ? singleRequisitionData.agent.mobile : 'NA' }}
											</label>
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

	</div>

</template>

<script type="text/javascript">

	import axios from 'axios';
	import Multiselect from 'vue-multiselect';
	import CKEditor from '@ckeditor/ckeditor5-vue';
	import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

    let singleRequisitionData = {

		products : [
			{}
		]
				
    };

	export default {

	    components: { 
			multiselect : Multiselect,
			ckeditor: CKEditor.component,
		},

	    data() {

	        return {

	        	step : 1,
	        	query : '',
	        	error : '',
    			perPage : 10,
	        	loading : false,
	        	
	        	currentTab : 'pending',
	        	
	        	editor: ClassicEditor,

	        	submitForm : true,

	        	requisitionsToShow : [],
	        	allFetchedRequisitions : [],
	        	
	        	// availableProducts : [],

	        	pagination: {
		        	current_page: 1
		      	},

	        	singleRequisitionData : singleRequisitionData,

	   //      	errors : {
				// 	product_id : [],
				// 	product_quantity : [],
				// },

	            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

	        }

		},
		
		created(){
			this.fetchAllRequisitions();
			// this.fetchMerchantAllProducts();
		},

		watch : {

			query : function(val){
				if (val==='') {
					this.fetchAllRequisitions();
				}
				else {
					this.searchData();
				}
			},

		},
		
		methods : {
			fetchAllRequisitions() {
				
				this.query = '';
				this.error = '';
				this.loading = true;
				this.allFetchedRequisitions = [];
				
				axios
					.get('/api/requisitions/' + this.perPage + "?page=" + this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							this.allFetchedRequisitions = response.data;
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
		/*
			fetchMerchantAllProducts() {
				
				this.query = '';
				this.error = '';
				this.loading = true;
				this.availableProducts = [];
				
				axios
					.get('/api/products/')
					.then(response => {
						if (response.status == 200) {
							this.availableProducts = response.data.data;
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
			searchData(emitedValue=false) {

				if (emitedValue) {
					this.query=emitedValue;
				}

				this.error = '';
				this.allFetchedRequisitions = [];
				this.pagination.current_page = 1;
				
				axios
				.get(
					"/api/search-requisitions/" + this.query + "/" + this.perPage + "?page=" + this.pagination.current_page
				)
				.then(response => {
					this.allFetchedRequisitions = response.data;
					this.requisitionsToShow = this.allFetchedRequisitions.all.data;
					this.pagination = this.allFetchedRequisitions.all;
				})
				.catch(e => {
					this.error = e.toString();
				});

			},
			changeNumberContents() {
				
				this.pagination.current_page = 1;

				if (this.query === '') {
					this.fetchAllRequisitions();
				}
				else {
					this.searchData();
				}
				
    		},
    		showContentDetails(object) {		
				// this.singleRequisitionData = { ...object };
				this.singleRequisitionData = Object.assign({}, this.singleRequisitionData, object);
				$('#requisition-view-modal').modal('show');
			},
/*
			showContentCreateForm() {
				this.step = 1;
	        	this.submitForm = true;
	        	
				this.singleRequisitionData = {
					products : [
						{}
					]
				};

				this.errors = {
					product_id : [
						null
					],
					product_quantity : [
						null
					],
				};

				$('#requisition-createOrEdit-modal').modal('show');
			},

			storeRequisition() {
				
				if (!this.verifyUserInput()) {
					this.submitForm = false;
					return;
				}

				axios
					.post('/requisitions/' + this.perPage, this.singleRequisitionData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("New requisition has been stored", "Success");
							this.allFetchedRequisitions = response.data;
							this.query !== '' ? this.searchData() : this.showSelectedTabProducts();
							$('#requisition-createOrEdit-modal').modal('hide');
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
			showSelectedTabProducts() {
				
				if (this.currentTab=='pending') {
					this.requisitionsToShow = this.allFetchedRequisitions.pending.data;
					this.pagination = this.allFetchedRequisitions.pending;
				}
				else {
					this.requisitionsToShow = this.allFetchedRequisitions.dispatched.data;
					this.pagination = this.allFetchedRequisitions.dispatched;
				}

			},
			verifyUserInput() {

				this.validateFormInput('product_id');
				this.validateFormInput('product_quantity');

				if (this.errors.constructor === Object && !this.errorInArray(this.errors.product_id) && !this.errorInArray(this.errors.product_quantity)) {

					return true;
				
				}

				return false;
			},
			errorInArray(array = []) {
				
				if (array.length) {

					return array.some(element => element != null);

				}

				return false;
			},
			nextPage() {
					
				this.validateFormInput('subject');
				this.validateFormInput('description');

				if (!this.errors.subject) {
					this.step += 1;
					this.submitForm = true;
				}
				else {
					this.submitForm = false;
				}

			},
			showPendingContents() {
				this.currentTab = 'pending';
				this.showSelectedTabProducts();
			},
			showDispatchedContents() {
				this.currentTab = 'dispatched';
				this.showSelectedTabProducts();
			},
			addMoreProduct() {
				if (this.singleRequisitionData.products.length < 3) {

					this.singleRequisitionData.products.push({});
					this.errors.product_id.push(null);
					this.errors.product_quantity.push(null);

				}
			},
			removeProduct() {
					
				if (this.singleRequisitionData.products.length > 1) {

					this.singleRequisitionData.products.pop();
					this.errors.product_id.pop();
					this.errors.product_quantity.pop();
				
				}
				
			},
			objectNameWithCapitalized ({ name, user_name }) {
		      	if (name) {
				    name = name.toString()
				    return name.charAt(0).toUpperCase() + name.slice(1)
		      	}
		      	else if (user_name) {
		      		user_name = user_name.toString()
				    return user_name.charAt(0).toUpperCase() + user_name.slice(1)
		      	}
		      	else 
		      		return ''
		    },
			setRequiredProduct(index) {
				if (this.singleRequisitionData.products[index].product && Object.keys(this.singleRequisitionData.products[index].product).length > 0) {
					this.singleRequisitionData.products[index].id = this.singleRequisitionData.products[index].product.id;
				}
			},
		
			validateFormInput (formInputName) {

				this.submitForm = false;

				switch(formInputName) {

					case 'subject' :

						if (!this.singleRequisitionData.subject) {
							this.errors.subject = 'Subject is required';
						}
						else if (!this.singleRequisitionData.subject.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.subject = 'No special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'subject');
						}

						break;
				/*
					case 'description' :

						if (this.singleRequisitionData.description && !this.singleRequisitionData.description.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.description = 'No special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'description');
						}

						break;
				*/

					case 'product_id' :

						this.singleRequisitionData.products.forEach(
							
							(requiredProduct, index) => {

								if (!requiredProduct.id || !requiredProduct.product || Object.keys(requiredProduct.product).length==0) {
									this.errors.product_id[index] = 'Product is required';
								}
								else{
									this.errors.product_id[index] = null;
								}

								if (!this.errorInArray(this.errors.product_id)) {
									this.submitForm = true;
								}

							}
						);

						break;

					case 'product_quantity' :

						this.singleRequisitionData.products.forEach(
							
							(requiredProduct, index) => {

								if (!requiredProduct.quantity || requiredProduct.quantity < 1) {
									this.errors.product_quantity[index] = 'Quantity is required';
								}
								else{
									this.errors.product_quantity[index] = null;
								}

								if (!this.errorInArray(this.errors.product_quantity)) {
									this.submitForm = true;
								}

							}
						);

						break;

				}
	 
			},
            
		}
  	}

</script>

<style scoped>
	@import '~vue-multiselect/dist/vue-multiselect.min.css';

	.fade-enter-active {
  		transition: all .3s ease;
	}
	.fade-leave-active {
  		transition: all .8s cubic-bezier(1.0, 0.5, 0.8, 1.0);
	}
	.fade-enter, .fade-leave-to {
  		transform: translateX(10px);
  		opacity: 0;
	}
</style>