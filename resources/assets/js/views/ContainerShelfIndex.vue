
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
															{{ $route.params.name }} Shelves List
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

												<ul class="nav nav-tabs md-tabs" role="tablist" v-show="query === ''">
													<li class="nav-item">
													    <a 	class="active nav-link" 
															data-toggle="tab" 
															aria-selected="true" 
															@click="showEngagedContents"
														>
															Engaged (Full)
														</a>
														<div class="slide"></div>
													</li>
													<li class="nav-item">
													    <a 	class="nav-link" 
															data-toggle="tab" 
															aria-selected="true" 
															@click="showPartialContents"
														>
															Engaged (Partial)
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

												<div class="tab-content card-block">
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

																	<tr v-for="content in contentsToShow" :key="content.id"
																	>
																		<td>{{ content.name }}</td>
																		
																		<td>
																			<span :class="[content.engaged==1 ? 'badge-danger' : content.engaged==0.5 ? 'badge-info' : 'badge-success', 'badge']">
																				
																				{{ content.engaged==1 ? 'Engaged' : content.engaged==0.5 ? 'Partially Engaged' : 'Empty' }}

																			</span>
																		</td>
																		
																		<td>
																			<button type="button" 
																					class="btn btn-grd-info btn-icon"  
																					v-show="content.container_shelf_unit_statuses.length" 
																					@click="showShelfUnitDetails(content)"
																			>
																				<i class="fas fa-eye"></i>
																			</button>
																			<span class="text-danger" 
																				v-show="!content.container_shelf_unit_statuses.length" 
																			>
																				No Unit
																			</span>
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
																class="btn btn-primary btn-sm" 
																@click="query === '' ? fetchContainerAllShelves() : searchData()"
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
																@paginate="query === '' ? fetchContainerAllShelves() : searchData()"
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

	        	currentTab : 'engaged',

	        	allFetchedContents : [],
	        	contentsToShow : [],

	        	pagination: {
		        	current_page: 1
		      	},

	            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

	        }

		},
		
		created(){

			this.fetchContainerAllShelves();

		},

		watch : {

			query : function(val){
				
				if (val==='') {
					this.fetchContainerAllShelves();
				}
				else {
					this.searchData();
				}
				
			},

		},
		
		methods : {

			fetchContainerAllShelves() {
				
				this.query = '';
				this.error = '';
				this.loading = true;
				this.allFetchedContents = [];
				
				axios
					.get('/api/container-shelves/' + this.$route.params.id + '/' + this.perPage + "?page=" + this.pagination.current_page)
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
				this.pagination.current_page = 1;
				
				axios
				.get(
					"/api/search-container-shelves/" + this.$route.params.id + '/' + this.query + "/" + this.perPage + "?page=" + this.pagination.current_page
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
			showShelfUnitDetails(object) {	
				const shelfId = object.id;
				const shelfName = object.name;
				// this.$router.push({ name: 'container-shelves', params: { shelfId,  shelfName} })
				this.$router.push({ path: `/shelf-units/` + shelfId + '/' + shelfName });
			},
            changeNumberContents(expectedContentsPerPage) {
				this.pagination.current_page = 1;
				this.perPage = expectedContentsPerPage;

				if (this.query === '') {
					this.fetchContainerAllShelves();
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
				this.currentTab = 'engaged';
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
					this.contentsToShow = this.allFetchedContents.engaged.data;
					this.pagination = this.allFetchedContents.engaged;
				}

			},
            
		}
  	}

</script>