
<template v-if="userHasPermissionTo('view-warehouse-manager-index')">

	<div class="pcoded-content">

		<breadcrumb 
			:title="'managers'" 
			:message="'All our warehouse managers'"
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
												<div class="row d-flex align-items-center text-center">			  	
											  		<div class="col-sm-3 form-group">	
															{{ 'warehouse managers' | capitalize }} List
											  		</div>
											  		<div class="col-sm-9 was-validated form-group" v-if="userHasPermissionTo('view-warehouse-manager-index')">
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
											  	</div>
											</div>
											
											<div class="col-sm-12 col-lg-12">
										  		<div class="tab-content card-block">
											  		<div class="table-responsive">
														<table class="table table-striped table-bordered nowrap text-center">
															<thead>
																<tr>
																	<th>
																		<a 
																			href="javascript:void(0)" 
																			@click="changeNamesOrder"
																		> 
																			Warehouse
																			<span v-show="ascending">
																				<i class="fa fa-sort-up" aria-hidden="true"></i>
																			</span>
																			<span v-show="descending">
																				<i class="fa fa-sort-down" aria-hidden="true"></i>
																			</span>
																			<span v-show="!ascending && !descending">
																				<i class="fa fa-sort" aria-hidden="true" style="opacity: 0.4;"></i>
																			</span>
																		</a>
																	</th>

																	<th>
																		Managers
																	</th>

																	<th>
																		Actions
																	</th>
																</tr>
															</thead>
															<tbody>
																<tr 
																	v-for="(warehouse, warehouseIndex) in allWarehouseManagers" 
																	:key="'warehouse-index-' + warehouseIndex + '-warehouse-id-' + warehouse.id" 
																>
																	<td>
																		{{ warehouse.name | capitalize }}
																	</td>

																	<td>
																		<span
																			v-for="(warehouseManager, warehouseManagerIndex) in warehouse.managers" 
																			:key="'warehouse-' + warehouseIndex + '-warehouse-manager-' + warehouseManagerIndex + '-manager-id-' + warehouseManager.id" 
																		>
																			{{ warehouseManager.first_name + ' ' + warehouseManager.last_name | capitalize }}

																			<span v-show="warehouseManagerIndex+1 < warehouse.managers.length">, </span>
																		</span>

																		<span v-show="!warehouse.managers.length">
																			NA
																		</span>
																	</td>
																	
																	<td> 
																		<button type="button" 
																				class="btn btn-grd-info btn-icon"  
																				@click="showContentDetails(warehouse)" 
																				data-toggle="tooltip" data-placement="top" title="View Details"
																		>
																			<i class="fa fa-eye"></i>
																		</button>
 																		

																		<button type="button" 
																				class="btn btn-grd-primary btn-icon" 
																				@click="openContentEditForm(warehouse)" 
																				v-if="userHasPermissionTo('update-warehouse-manager')" 
																				data-toggle="tooltip" data-placement="top" title="Edit"
																		>
																			<i class="fa fa-edit"></i>
																		</button>

																		<button type="button" 
																				class="btn btn-grd-danger btn-icon" 
																				data-toggle="tooltip" data-placement="top" title="Delete"
																				v-show="warehouse.managers.length" 
																				@click="openContentDeleteForm(warehouse)" 
																				v-if="userHasPermissionTo('delete-warehouse-manager')"
																		>
																			<i class="fa fa-trash"></i>
																		</button>
																	</td>
															    
																</tr>

																<tr 
															  		v-show="!allWarehouseManagers.length"
															  	>
														    		<td :colspan="3">
															      		<div class="alert alert-danger" role="alert">
															      			Sorry, No data found.
															      		</div>
															    	</td>
															  	</tr>
															</tbody>
															<tfoot>
																<tr>
																	<th>
																		<a 
																			href="javascript:void(0)" 
																			@click="changeNamesOrder"
																		> 
																			Warehouse
																			<span v-show="ascending">
																				<i class="fa fa-sort-up" aria-hidden="true"></i>
																			</span>
																			<span v-show="descending">
																				<i class="fa fa-sort-down" aria-hidden="true"></i>
																			</span>
																			<span v-show="!ascending && !descending">
																				<i class="fa fa-sort" aria-hidden="true" style="opacity: 0.4;"></i>
																			</span>
																		</a>
																	</th>

																	<th>
																		Managers
																	</th>

																	<th>
																		Actions
																	</th>
																</tr>
															</tfoot>
														</table>
													</div>

													<div class="row d-flex align-items-center">
														<div class="col-sm-2 col-4">
															<select 
																class="form-control" 
																v-model.number="perPage" 
																@change="changeNumberContents()"
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
																class="btn btn-primary btn-sm" 
																data-toggle="tooltip" data-placement="top" title="Reload"
																@click="query === '' ? fetchAllWarehouseManagers() : searchData()"
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
																@paginate="query === '' ? fetchAllWarehouseManagers() : searchData()"
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
		<div class="modal fade" id="warehouse-manager-createOrEdit-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" v-if="userHasPermissionTo('update-warehouse-manager')">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">
							Update {{ singleWarehouseData.name }} Managers
						</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
						
					<form 	
						class="form-horizontal" 
						v-on:submit.prevent="updateWarehouseManagers()" 
						autocomplete="off" 
					>

						<input type="hidden" name="_token" :value="csrf">

						<div class="modal-body">
							<div class="form-row">
								<div class="form-group col-md-12">
									<label for="inputFirstName">Warehouse Name</label>
									
									<multiselect 
                              			v-model="singleWarehouseData"
                              			placeholder="Warehouse Name" 
                                  		label="name" 
                                  		track-by="id" 
                                  		:options="[]" 
                                  		:custom-label="objectNameWithCapitalized" 
                                  		:required="true" 
                                  		:disabled = "true"
                                  		class="form-control p-0 is-valid" 
                                  		:allow-empty="false"
                                  		selectLabel = "Press/Click"
                                  		deselect-label="Can't remove single value"
                              		>
                                	</multiselect>
								</div>
							</div>
							
							<div class="form-row">
								<div class="form-group col-md-12">
									<label for="inputFirstName">Manager Name</label>
									
									<multiselect 
	                          			v-model="singleWarehouseData.managers" 
	                          			placeholder="Manager Name" 
	                              		label="user_name" 
	                              		track-by="id" 
	                              		:custom-label="objectFullNameWithCapitalized" 
	                              		:options="allManagers" 
	                              		:multiple="true" 
	                              		:close-on-select="false" 
	                              		class="form-control p-0" 
                                  		:class="!errors.manager_names  ? 'is-valid' : 'is-invalid'"
	                              		selectLabel = "Press/Click"
	                              		deselect-label="Can't remove single value" 
	                              		@close="validateFormInput('manager_names')"
	                          		>
	                            	</multiselect>

									<div class="invalid-feedback">
							        	{{ errors.manager_names }}
							  		</div>
								</div>
							</div>
							
							<div class="form-row" v-show="! allManagers.length">
								<div class="col-md-12">
									<p class="text-danger text-center">
										Sorry There is a problem with fetching managers. You may not have permissions to select managers
									</p>
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
									Update
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>

		<!-- View Modal -->
		<div class="modal fade" id="warehouse-manager-view-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">{{ singleWarehouseData.name }} Manager Details</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="form-row"> 
						    <div class="form-group col-md-6 text-right">
								<label class="font-weight-bold">Warehouse Name: </label>
							</div>
							<div class="form-group col-md-6 text-left">
								{{ singleWarehouseData.name | capitalize }}
							</div>
						</div>

						<div class="form-row"> 
						    <div class="form-group col-md-6 text-right">
								<label class="font-weight-bold">Managers Name: </label>
							</div>
							<div class="form-group col-md-6 text-left">
								<span
									v-for="(warehouseManager, warehouseManagerIndex) in singleWarehouseData.managers" 
									:key="'warehouse-' + warehouseManagerIndex + 'manager-details'" 
								>
									{{ warehouseManager.first_name + ' ' + warehouseManager.last_name | capitalize }}

									<span v-show="warehouseManagerIndex+1 < singleWarehouseData.managers.length">, </span>
								</span>

								<span v-show="!singleWarehouseData.managers.length">
									NA
								</span>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary btn-block btn-sm" data-dismiss="modal">
							Close
						</button>
					</div>
				</div>
			</div>
		</div>

		<delete-confirmation-modal 
			v-if="userHasPermissionTo('delete-warehouse-manager')" 
			:csrf="csrf" 
			:submit-method-name="'deleteWarehouseManagers'" 
			:content-to-delete="singleWarehouseData"
			:restoration-message="'This action can not be retrieved later !'" 
			
			@deleteWarehouseManagers="deleteWarehouseManagers($event)" 
		></delete-confirmation-modal>
	</div>

</template>

<script type="text/javascript">

	import axios from 'axios';
	import Multiselect from 'vue-multiselect';

    let singleWarehouseData = {
    	managers : []
    };

	export default {

	    components: { 
			multiselect : Multiselect,
		},

	    data() {

	        return {

	        	query : '',
	        	error : '',
    			perPage : 10,
	        	loading : false,

	        	ascending : false,
	      		descending : false,

	        	submitForm : true,

	        	allManagers : [],
	        	allWarehouseManagers : [],

	        	pagination: {
		        	current_page: 1
		      	},

	        	singleWarehouseData : singleWarehouseData,

	        	errors : {

	        	},

	            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

	        }

		},
		
		created(){

			this.fetchAllManagers();
			this.fetchAllWarehouseManagers();

		},

		watch : {

			query : function(val){
				if (val==='') {
					this.fetchAllWarehouseManagers();
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

			fetchAllManagers() {
				
				this.query = '';
				this.error = '';
				this.loading = true;
				this.allManagers = [];
				
				axios
					.get('/api/managers/')
					.then(response => {
						if (response.status == 200) {
							this.allManagers = response.data;
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
			fetchAllWarehouseManagers() {
				
				this.query = '';
				this.error = '';
				this.loading = true;
				this.allWarehouseManagers = [];
				
				axios
					.get('/api/warehouse-managers/' + this.perPage + "?page=" + this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							this.allWarehouseManagers = response.data.data;
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
				this.allWarehouseManagers = [];
				this.pagination.current_page = 1;
				
				axios
				.get(
					"/api/search-warehouse-managers/" + this.query + "/" + this.perPage + "?page=" + this.pagination.current_page
				)
				.then(response => {
					this.allWarehouseManagers = response.data.all.data;
					this.pagination = response.data.all;
				})
				.catch(e => {
					this.error = e.toString();
				});

			},
			showContentDetails(object) {	
				this.singleWarehouseData = object;
				$('#warehouse-manager-view-modal').modal('show');
			},
			openContentEditForm(object) {
				this.submitForm = true;
				this.singleWarehouseData = object;
				$('#warehouse-manager-createOrEdit-modal').modal('show');
			},
			openContentDeleteForm(object) {	
				this.singleWarehouseData = object;
				$('#delete-confirmation-modal').modal('show');
			},
			updateWarehouseManagers() {
				
				if (! this.verifyUserInput()) {
					this.submitForm = false;
					return;
				}

				axios
					.put('/warehouse-managers/' + this.singleWarehouseData.id + '/' + this.perPage, this.singleWarehouseData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Warehouse manager has been updated", "Success");
							this.allWarehouseManagers = response.data.data;
							this.query !== '' ? this.searchData() : '';
							$('#warehouse-manager-createOrEdit-modal').modal('hide');
						}
					})
					.catch(error => {
						if (error.response.status == 422) {
							for (var x in error.response.data.errors) {
								this.$toastr.w(error.response.data.errors[x], "Warning");
							}
				      	}
					});

			},
			deleteWarehouseManagers() {
				
				if (this.singleWarehouseData.managers.length < 1) {
					this.submitForm =false;
					return;
				}

				axios
					.delete('/warehouse-managers/' + this.singleWarehouseData.id + '/' + this.perPage, this.singleWarehouseData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Manager has been deleted", "Success");
							this.allWarehouseManagers = response.data.data;
							this.query !== '' ? this.searchData() : '';
							$('#delete-confirmation-modal').modal('hide');
						}
					})
					.catch(error => {
						if (error.response.status == 422) {
							for (var x in error.response.data.errors) {
								this.$toastr.w(error.response.data.errors[x], "Warning");
							}
				      	}
					});

			},
			verifyUserInput() {

				this.validateFormInput('manager_names');

				if (Object.keys(this.errors).length > 0) {
					return false;
				}

				return true;

			},
            changeNumberContents() {

				this.pagination.current_page = 1;

				if (this.query === '') {
					this.fetchAllWarehouseManagers();
				}
				else {
					this.searchData();
				}

    		},
    		objectNameWithCapitalized ({ name }) {
		      	if (name) {
				    name = name.toString();
					const words = name.split(" ");

					for (let i = 0; i < words.length; i++) {
						
						if (words[i]) {

					    	words[i] = words[i][0].toUpperCase() + words[i].substr(1);
						
						}
						
					}

					return words.join(" ");
		      	}
		      	else 
		      		return ''
		    },
		    objectFullNameWithCapitalized ({ first_name, last_name }) {

		    	return this.$options.filters.capitalize(`${first_name} ${last_name}`);

		    },
		    changeNamesOrder() {

				if (this.ascending) {

					this.ascending = false;
					this.descending = true;

					this.descendingAlphabets('name');

				}
				else if (this.descending) {

					this.ascending = true;
					this.descending = false;

					this.ascendingAlphabets('name');

				}
				else {

					this.ascending = true;
					this.descending = false;

					this.ascendingAlphabets('name');

				}
				
			},
			ascendingAlphabets(columnValue) {
				this.allWarehouseManagers.sort(
			 		function(a, b){
						var x = a[columnValue] ? a[columnValue].toLowerCase() : '';
						var y = b[columnValue] ? b[columnValue].toLowerCase() : '';
						if (x < y) {return -1;}
						if (x > y) {return 1;}
						return 0;
					}
				);
			},
			descendingAlphabets(columnValue) {
				this.allWarehouseManagers.sort(
			 		function(a, b){
						var x = a[columnValue] ? a[columnValue].toLowerCase() : '';
						var y = b[columnValue] ? b[columnValue].toLowerCase() : '';
						if (x > y) {return -1;}
						if (x < y) {return 1;}
						return 0;
					}
				);
			},
			validateFormInput (formInputName) {
				
				this.submitForm = false;

				switch(formInputName) {

					case 'manager_names' :

						if (this.singleWarehouseData.managers.length < 1) {
							this.errors.manager_names = 'One manager is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'manager_names');
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