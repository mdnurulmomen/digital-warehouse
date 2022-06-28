
<template>
	<div class="pcoded-content">
		<breadcrumb 
			:title="'containers'" 
			:message="'All our containers'"
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
														Warehouse Containers List
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
											  				New Container
											  			</button>
											  		</div>
 												-->
											  	</div>
											</div>
											
											<div class="col-sm-12 col-lg-12">
												<ul class="nav nav-tabs md-tabs" role="tablist" v-show="query==''">
													<li class="nav-item">
													    <a 	class="active nav-link" 
															data-toggle="tab" 
															aria-selected="true" 
															@click="showEngagedContents"
														>
															Occupied (Full)
														</a>
														<div class="slide"></div>
													</li>
													<li class="nav-item">
													    <a 	class="nav-link" 
															data-toggle="tab" 
															aria-selected="true" 
															@click="showPartialContents"
														>
															Occupied (Partial)
														</a>
														<div class="slide"></div>
													</li>
													<li class="nav-item">
													    <a 	class="nav-link" 
															data-toggle="tab" 
															aria-selected="true" 
															@click="showEmptyContents"
														>
															Empty
														</a>
														<div class="slide"></div>
													</li>
												</ul>

												<div class="tab-content card-block pl-0 pr-0">
													<div class="card">
														<div class="table-responsive">
															<table class="table table-striped table-bordered nowrap text-center">
																<thead>
																	<tr>
																		<th>Name</th>
																		<th>Status</th>
																		<th>Actions</th>
																	</tr>
																</thead>
																<tbody>
																	<tr 
																		v-for="content in contentsToShow" 
																		:key="content.id" 
																		:class="content.id==singleContainerData.id ? 'highlighted' : ''"
																	>
																		<td>{{ content.name }}</td>
																		
																		<td>
																			<span :class="[content.occupied==1 ? 'badge-danger' : content.occupied==0.5 ? 'badge-warning' : 'badge-success', 'badge']">
																				
																				{{ content.occupied==1 ? 'Packed' : content.occupied==0.5 ? 'Partially Occupied' : 'Empty' }}

																			</span>
																		</td>
																		
																		<td>
																			<button type="button" 
																					class="btn waves-effect waves-dark btn-info btn-outline-info btn-icon" 
																					v-tooltip.bottom-end="'View Details'" 
																					@click="showContainerDetails(content)"
																			>
																				<i class="fa fa-eye"></i>
																			</button>

																			<button type="button" 
																					class="btn waves-effect waves-dark btn-primary btn-outline-primary btn-icon"  
																					v-tooltip.bottom-end="'Move Inside'"
																					v-show="content.container_shelf_statuses && content.container_shelf_statuses.length" 
																					@click="showContainerShelfDetails(content)"
																			>
																				<i class="fas fa-arrow-alt-circle-up"></i>
																			</button>

																			<!-- 
																			<span class="text-danger" 
																				v-show="!content.container_shelf_statuses.length" 
																			>
																				<span class="badge badge-danger">No Shelf</span>
																			</span> 
																			-->
																		</td>
																    
																	</tr>
																	<tr 
																  		v-show="!contentsToShow.length"
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
																class="btn waves-effect waves-dark btn-primary btn-outline-primary btn-sm" 
																v-tooltip.bottom-end="'Reload'"
																@click="pagination.current_page = 1; query === '' ? fetchAllContainers() : searchData()"
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
																@paginate="query === '' ? fetchAllContainers() : searchData()"
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
		<div class="modal fade" id="container-view-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">
							Container Details
						</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="modal-body text-center">	
								
						<div class="card">
							<div class="card-body">
								<!-- <h4 class="card-title">Container</h4> -->

								<div class="form-row">
									<label class="col-sm-6 col-form-label font-weight-bold text-right">Container Name :</label>
									<label class="col-sm-6 form-control-plaintext">{{ singleContainerData.name | capitalize }}</label>
								</div>

								<div class="form-row" v-if="singleContainerData.product" >
									<label class="col-sm-6 col-form-label font-weight-bold text-right">Product Name :</label>
									<label class="col-sm-6 form-control-plaintext">{{ singleContainerData.product && singleContainerData.product.merchant_product && singleContainerData.product.merchant_product.product ? singleContainerData.product && singleContainerData.product.merchant_product && singleContainerData.product.merchant_product.product.name : 'No Product' | capitalize }}</label>
								</div>

								<div class="form-row" v-if="singleContainerData.product">
									<label class="col-sm-6 col-form-label font-weight-bold text-right">Product SKU :</label>
									<label class="col-sm-6 form-control-plaintext">{{  singleContainerData.product.merchant_product.sku }}</label>
								</div>

								<!-- 
								<div class="form-row">
									<label class="col-sm-6 col-form-label font-weight-bold text-right">Length :</label>
									<label class="col-sm-6 form-control-plaintext">{{ singleContainerData.length }}</label>
								</div>

								<div class="form-row">
									<label class="col-sm-6 col-form-label font-weight-bold text-right">Width :</label>
									<label class="col-sm-6 form-control-plaintext">{{ singleContainerData.width }}</label>
								</div>
								
								<div class="form-row">
									<label class="col-sm-6 col-form-label font-weight-bold text-right">Length :</label>
									<label class="col-sm-6 form-control-plaintext">{{ singleContainerData.height }}</label>
								</div>
								-->

								<div class="form-row" v-if="singleContainerData.hasOwnProperty('container_shelf_statuses')">
									<label class="col-sm-6 col-form-label font-weight-bold text-right">Has Shelf :</label>
									<label class="col-sm-6 form-control-plaintext">
										<span :class="[singleContainerData.container_shelf_statuses && singleContainerData.container_shelf_statuses.length ? 'badge-success' : 'badge-secondary', 'badge']">{{ singleContainerData.container_shelf_statuses.length ? 'Available' : 'No Shelf' }}</span>
									</label>
								</div>

								<div class="form-row" v-if="singleContainerData.hasOwnProperty('container_shelf_statuses') && singleContainerData.container_shelf_statuses.length">
									<label class="col-sm-6 col-form-label font-weight-bold text-right">Number Shelves :</label>
									<label class="col-sm-6 form-control-plaintext">
										{{ singleContainerData.container_shelf_statuses.length }}
									</label>
								</div>
							</div>
						</div>

						<!-- shelf -->
						<!-- 
						<div class="card" v-if="singleContainerData.has_shelve">
							<div class="card-body">
								<h4 class="card-title">Container Shelf</h4>
								
								<div class="form-row">
									<label class="col-sm-6 col-form-label font-weight-bold text-right"># Shelves :</label>
									<label class="col-sm-6 form-control-plaintext">{{ singleContainerData.shelf.quantity }}</label>
								</div>

								<div class="form-row">
									<label class="col-sm-6 col-form-label font-weight-bold text-right">Has Units :</label>
									<label class="col-sm-6 form-control-plaintext">
										<span :class="[singleContainerData.shelf.has_units ? 'badge-success' : 'badge-danger', 'badge']">{{ singleContainerData.shelf.has_units ? 'Available' : 'NA' }}</span>
									</label>
								</div>
							</div>
						</div>
 						-->
						<!-- unit -->
						<!-- 
						<div class="card" v-if="singleContainerData.has_shelve && singleContainerData.shelf.has_units">
							<div class="card-body">
								<h4 class="card-title">Shelf Unit</h4>
								<div class="form-row">
									<label class="col-sm-6 col-form-label font-weight-bold text-right"># Units :</label>
									<label class="col-sm-6 form-control-plaintext">{{ singleContainerData.shelf.unit.quantity }}</label>
								</div>
							</div>
						</div>
 						-->
					</div>

					<div class="modal-footer">
						<button type="button" class="btn waves-effect waves-dark btn-secondary btn-outline-secondary btn-block btn-sm" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>
	</div>

</template>

<script type="text/javascript">

	import axios from 'axios';

	export default {

	    data() {

	        return {

	        	query : '',
	        	error : '',
    			perPage : 10,
	        	loading : false,

	        	currentTab : 'occupied',

	        	allFetchedContents : [],
	        	contentsToShow : [],

	        	pagination: {
		        	current_page: 1
		      	},

		      	singleContainerData : {},

	            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

	        }

		},
		
		created(){

			this.fetchAllContainers();

		},

		watch : {

			query : function(val){
				
				this.pagination.current_page = 1; 

				if (val==='') {

					this.fetchAllContainers();

				}
				else {

					this.searchData();
					
				}
				
			},

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

			fetchAllContainers() {
				
				this.query = '';
				this.error = '';
				this.loading = true;
				this.allFetchedContents = [];
				
				axios
					.get('/api/my-containers/' + this.perPage + "?page=" + this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							this.allFetchedContents = response.data;
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
				this.allFetchedContents = [];
				// this.pagination.current_page = 1;
				
				axios
				.get(
					"/api/search-my-containers/" + this.query + "/" + this.perPage + "?page=" + this.pagination.current_page
				)
				.then(response => {
					this.allFetchedContents = response.data;
					this.contentsToShow = this.allFetchedContents.all.data;
					this.pagination = response.data.all;
				})
				.catch(e => {
					this.error = e.toString();
				});

			},
			showContainerDetails(object) {
				this.singleContainerData = object;
				$('#container-view-modal').modal('show');
			},
			showContainerShelfDetails(object) {
				const containerId = object.id;
				const containerName = object.name;
				// this.$router.push({ name: 'container-shelves', params: { containerId,  containerName} })
				// this.$router.push({ path: `/my-container/` + containerId + `/shelves/` + containerName });
				this.$router.push({ name: 'my-container-shelves', params: { id: containerId, containerName: containerName }});
			},
            changeNumberContents() {
				this.pagination.current_page = 1;

				if (this.query === '') {
					this.fetchAllContainers();
				}
				else {
					this.searchData();
				}
    		},
			showEmptyContents() {
				this.currentTab = 'empty';
				this.showSelectedTabContents();
			},
			showPartialContents() {
				this.currentTab = 'partial';
				this.showSelectedTabContents();
			},
			showEngagedContents() {
				this.currentTab = 'occupied';
				this.showSelectedTabContents();
			},
			showSelectedTabContents() {
				
				if (this.currentTab=='empty') {
					this.contentsToShow = this.allFetchedContents.empty.data;
					this.pagination = this.allFetchedContents.empty;
				}else if (this.currentTab=='partial') {
					this.contentsToShow = this.allFetchedContents.partial.data;
					this.pagination = this.allFetchedContents.partial;
				}
				else {
					this.contentsToShow = this.allFetchedContents.occupied.data;
					this.pagination = this.allFetchedContents.occupied;
				}

			},
            
		}
  	}

</script>

<style scoped>
	.branches-enter-active {
  		transition: all 1s ease;
	}
	.branches-leave-active {
  		transition: all 1s cubic-bezier(1.0, 0.5, 0.8, 1.0);
	}
	.branches-enter, .branches-leave-to {
  		transform: translateX(10px);
  		opacity: 0;
	}
</style>