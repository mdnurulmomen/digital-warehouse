
<template>

	<div class="pcoded-content">

		<breadcrumb 
			:title="'warehouses'" 
			:message="'All our warehouses'"
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

											  	<div class="row d-flex align-items-center">
											  		<div class="col-sm-3 text-left">	
															Warehouses List
											  		</div>
											  		<div class="col-sm-6 was-validated text-center">
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
											  		<div class="col-sm-3 text-right"></div>
											  	</div>

											</div>
											
											<div class="col-sm-12 col-lg-12">

										  		<tab 
										  			v-show="query === ''" 
										  			:tab-names="['approved', 'pending']" 
										  			:current-tab="currentTab" 

										  			@showApprovedContents="showApprovedContents" 
										  			@showPendingContents="showPendingContents" 
										  		></tab>

										  		<div class="tab-content card-block">
													<div class="card">
														<div class="table-responsive">
															<table class="table table-striped table-bordered nowrap text-center">
																<thead>
																	<tr>
																		<th>Name</th>
																		<th>Email</th>
																		<th>Mobile</th>
																		<th>Status</th>
																		<th>Actions</th>
																	</tr>
																</thead>
																<tbody>

																	<tr v-for="content in contentsToShow" :key="'content-' + content.id"
																	>
																		<td>{{ content.name }}</td>
																		<td>{{ content.email }}</td>
																		<td>{{ content.mobile }}</td>
																		<td>
																			<span 
																				:class="[content.active ? 'badge-success' : 'badge-danger', 'badge']"
																			>
																				{{ content.active ? 'Active' : 'Pending' }}
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
																		</td>
																    
																	</tr>
																	<tr 
																  		v-show="!contentsToShow.length"
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
																		<th>Email</th>
																		<th>Mobile</th>
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
																@click="query === '' ? fetchAllProducts() : searchData()"
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

		<!-- View Modal -->
		<div class="modal fade" id="warehouse-view-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">
							Warehouse Details
						</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="modal-body">	
							
						<ul class="nav nav-tabs tabs" role="tablist">
							<li class="nav-item">
								<a class="nav-link active show" data-toggle="tab" href="#profile" role="tab" aria-selected="false">
									Profile
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" data-toggle="tab" href="#owner" role="tab" aria-selected="false">
									Deal
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" data-toggle="tab" href="#features" role="tab" aria-selected="true">
									Features
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" data-toggle="tab" href="#storage-details" role="tab" aria-selected="false">
									Storage
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" data-toggle="tab" href="#container-details" role="tab" aria-selected="false">
									Container
								</a>
							</li>
						
						</ul>

						<div class="tab-content tabs card-block">

							<div class="tab-pane active" id="profile" role="tabpanel">	
					    		<div class="form-group form-row text-center">
									<div class="col-md-12 text-center">
										<img class="img-fluid" 
											:src="singleWarehouseData.site_map_preview || ''"
											alt="site_map_preview" 
										>
									</div>
								</div>
								<div class="form-group form-row">
									<label class="col-sm-6 col-form-label font-weight-bold text-right">Name :</label>
									<label class="col-sm-6 form-control-plaintext">{{ singleWarehouseData.name }}</label>
								</div>
								<div class="form-group form-row">
									<label class="col-sm-6 col-form-label font-weight-bold text-right">Code :</label>
									<label class="col-sm-6 form-control-plaintext">{{ singleWarehouseData.code }}</label>
								</div>
								<div class="form-group form-row">
									<label class="col-sm-6 col-form-label font-weight-bold text-right">Username :</label>
									<label class="col-sm-6 form-control-plaintext">{{ singleWarehouseData.user_name }}</label>
								</div>
								<div class="form-group form-row">
									<label class="col-sm-6 col-form-label font-weight-bold text-right">Email :</label>
									<label class="col-sm-6 form-control-plaintext">{{ singleWarehouseData.email }}</label>
								</div>
								<div class="form-group form-row">
									<label class="col-sm-6 col-form-label font-weight-bold text-right">Mobile :</label>
									<label class="col-sm-6 form-control-plaintext">{{ singleWarehouseData.mobile }}</label>
								</div>
								<div class="form-group form-row">
									<label class="col-sm-6 col-form-label font-weight-bold text-right">Location :</label>
									<label class="col-sm-6 form-control-plaintext"></label>
								</div>
							</div>

							<div class="tab-pane" id="owner" role="tabpanel" v-if="singleWarehouseData.owner">
								<div class="form-group form-row">
									<label class="col-sm-6 col-form-label font-weight-bold text-right">Owner name :</label>
									<label class="col-sm-6 form-control-plaintext">{{ getOwnerFullName(singleWarehouseData.owner) }}</label>
								</div>
								<div class="form-group form-row">
									<label class="col-sm-6 col-form-label font-weight-bold text-right">Username :</label>
									<label class="col-sm-6 form-control-plaintext">{{ singleWarehouseData.owner.user_name }}</label>
								</div>
								<div class="form-group form-row">
									<label class="col-sm-6 col-form-label font-weight-bold text-right">Email :</label>
									<label class="col-sm-6 form-control-plaintext">{{ singleWarehouseData.owner.email }}</label>
								</div>
								<div class="form-group form-row">
									<label class="col-sm-6 col-form-label font-weight-bold text-right">Mobile :</label>
									<label class="col-sm-6 form-control-plaintext">{{ singleWarehouseData.owner.mobile }}</label>
								</div>
								<div class="form-group form-row">
									<label class="col-sm-6 col-form-label font-weight-bold text-right">Deal Details :</label>
									<label class="col-sm-6 form-control-plaintext" v-html="singleWarehouseData.warehouse_deal"></label>
								</div>
							</div>

							<div class="tab-pane" id="features" role="tabpanel">	
								<div class="form-group form-row">
									<label class="col-sm-6 col-form-label font-weight-bold text-right">Warehouse Features:</label>
									<label class="col-sm-6 form-control-plaintext" v-html="singleWarehouseData.feature.features"></label>
								</div>
								<div class="form-group form-row">
									<label class="col-sm-6 col-form-label font-weight-bold text-right">
										Warehouse Previews:
									</label>
						    		<div class="col-sm-6">
										<div class="form-row" v-show="singleWarehouseData.previews.length">
											<div 
												class="form-group col-sm-6" 
												v-for="(warehousePreview, index) in singleWarehouseData.previews" 
												:key="'A' + index + warehousePreview.id"
											>
												<img class="img-fluid" 
													:src="warehousePreview.preview || ''"
													alt="warehouse preview" 
												>
											</div>
										</div>
									</div>
								</div>
							</div>
							
							<div class="tab-pane" id="storage-details" role="tabpanel">
								<div v-if="singleWarehouseData.storages.length">
									<div 
										class="card" 
										v-for="(warehouseStorageType, index) in singleWarehouseData.storages" 
								    	:key="'B' + index + warehouseStorageType.id"
									>
								    	<div class="card-body">

											<div class="form-group form-row">
												<label class="col-sm-6 col-form-label font-weight-bold text-right">
													Storage Name:
												</label>
												<label class="col-sm-6 form-control-plaintext" v-html="warehouseStorageType.storage_type.name"></label>
											</div>

											<div class="form-group form-row">
												<label class="col-sm-6 col-form-label font-weight-bold text-right">Storage Code:</label>
												<label class="col-sm-6 form-control-plaintext" v-html="warehouseStorageType.storage_type.code"></label>
											</div>

											<div class="form-group form-row">
												<label class="col-sm-6 col-form-label font-weight-bold text-right">Storage Features:</label>
												<label class="col-sm-6 form-control-plaintext" v-html="warehouseStorageType.feature.features"></label>
											</div>
											
								    		<div class="form-group form-row">
												<label class="col-sm-6 col-form-label font-weight-bold text-right">
													Storage Previews:
												</label>
									    		<div class="col-sm-6">
													<div class="form-row" v-show="warehouseStorageType.previews.length">
														<div 
															class="form-group col-sm-6" 
															v-for="(storagePreview, index) in warehouseStorageType.previews" 
															:key="'C' + index + storagePreview.id"
														>
															<img class="img-fluid" 
																:src="storagePreview.preview || ''"
																alt="storage preview" 
															>
														</div>
													</div>
												</div>
											</div>

								    	</div>
								    </div>
								</div>
							</div>

							<div class="tab-pane" id="container-details" role="tabpanel">
								<div v-if="singleWarehouseData.containers.length">
									<div 
										class="card" 
										v-for="(warehouseContainer, index) in singleWarehouseData.containers" 
								    	:key="'C' + index + warehouseContainer.id"
									>
								    	<div class="card-body">

											<div class="form-group form-row">
												<label class="col-sm-6 col-form-label font-weight-bold text-right">
													Container Type:
												</label>
												<label class="col-sm-6 form-control-plaintext">
													{{ warehouseContainer.container.name }}
												</label>
											</div>

											<div class="form-group form-row">
												<label class="col-sm-6 col-form-label font-weight-bold text-right">Quantity:</label>
												<label class="col-sm-6 form-control-plaintext">
													{{ warehouseContainer.quantity }}
												</label>
											</div>

											<div class="form-group form-row">
												<label class="col-sm-6 col-form-label font-weight-bold text-right">Engaged Quantity:</label>
												<label class="col-sm-6 form-control-plaintext">
													{{ warehouseContainer.engaged_quantity }}
												</label>
											</div>

											<div class="form-group form-row">
												<label class="col-sm-6 col-form-label font-weight-bold text-right">Partially Engaged:</label>
												<label class="col-sm-6 form-control-plaintext">
													{{ warehouseContainer.partially_engaged }}
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

	</div>

</template>

<script type="text/javascript">

	import axios from 'axios';
	// import Multiselect from 'vue-multiselect';
	// import CKEditor from '@ckeditor/ckeditor5-vue';
	// import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

    let singleWarehouseData = {
    	
    	owner : {},
    	previews : [],
    	
    	feature : {
    		features : ''
    	},
    	
    	storages : [
    		{  	// warehouse_storage_types
				previews : [],
				storage_type : {

				},
				feature : {
					features : ''
				},
			},
    	],

    	containers : [
			{	/*warehouse_containers*/	
				
				// quantity : '',
				// container_id : ''
				// engaged_quantity : '',
				container : {	 // containers	
					// name : 0,
					// has_shelve : 0,
					shelf : { 	// warehouse_container_shelfs
						// has_units : true,
						// quantity : 0,  // total number warehouse_container_shelfs
						unit :  {    // warehouse_container_shelf_units
							// quantity : 0,  // length of warehouse_container_shelf_units	
						},	
					}, 
				},

			},
    	],

    };

	export default {

	    data() {

	        return {

	        	query : '',
	        	error : '',
    			perPage : 10,
	        	loading : false,
	        	currentTab : 'approved',

	        	contentsToShow : [],
	        	allFetchedWarehouses : [],

	        	pagination: {
		        	current_page: 1
		      	},

		      	currentOwner : {},

	        	singleWarehouseData : singleWarehouseData,

	            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

	        }

		},
		
		created(){

			this.getCurrentUser();

		},

		watch : {

			query : function(val){
				if (val==='') {
					this.fetchAllWarehouses();
				}
				else {
					this.searchData();
				}
			},

		},
		
		methods : {

			getCurrentUser() {

				axios
					.get('/api/current-owner/')
					.then(response => {
						if (response.status == 200) {
							this.currentOwner = response.data.user;
							this.fetchAllWarehouses();
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

			fetchAllWarehouses() {
				
				this.query = '';
				this.error = '';
				this.loading = true;
				this.allFetchedWarehouses = [];
				
				axios
					.get('/my-warehouses/' + this.currentOwner.id + '/' + this.perPage + "?page=" + this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							this.allFetchedWarehouses = response.data;
							this.showSelectedTabContents();
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
			searchData() {

				this.error = '';
				this.allFetchedWarehouses = [];
				this.pagination.current_page = 1;
				
				axios
				.get('/search-warehouses/' + this.currentOwner.id + '/' + this.query + "/" +  this.perPage + "?page=" + this.pagination.current_page)
				.then(response => {
					this.allFetchedWarehouses = response.data;
					this.contentsToShow = this.allFetchedWarehouses.all.data;
					this.pagination = this.allFetchedWarehouses.all;
				})
				.catch(e => {
					this.error = e.toString();
				});

			},
			showContentDetails(object) {
				this.singleWarehouseData = object;
				$('#warehouse-view-modal').modal('show');
			},
            changeNumberContents(expectedContentsPerPage) {
				this.pagination.current_page = 1;
				this.perPage = expectedContentsPerPage;

				if (this.query === '') {
					this.fetchAllWarehouses();
				}
				else {
					this.searchData();
				}
    		},
    		showSelectedTabContents() {
				
				if (this.currentTab=='approved') {
					this.contentsToShow = this.allFetchedWarehouses.approved.data;
					this.pagination = this.allFetchedWarehouses.approved;
				}
				else if (this.currentTab=='pending') {
					this.contentsToShow = this.allFetchedWarehouses.pending.data;
					this.pagination = this.allFetchedWarehouses.pending;
				}

			},
			showApprovedContents() {
				this.currentTab = 'approved';
				this.showSelectedTabContents();
			},
			showPendingContents() {
				this.currentTab = 'pending';
				this.showSelectedTabContents();
			},
			getOwnerFullName(owner) {
				if (!owner.first_name && !owner.last_name) {
					return 'NA';
				}

				return owner.first_name || '' + ' ' + owner.last_name || '';
			},
            
		}
  	}

</script>

<style scoped>
	
	/*@import '~vue-multiselect/dist/vue-multiselect.min.css';*/

	.modal { 
		overflow: auto !important; 
	}
	.modal-body {
		word-break: break-all;
	}

</style>