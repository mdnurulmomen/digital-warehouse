
<template v-if="userHasPermissionTo('view-warehouse-asset-index')">
	<div class="pcoded-content">
		<breadcrumb 
			:title="'rent-periods'" 
			:message="'All our warehouse rent-periods'"
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
											  	<search-and-addition-option 
											  		v-if="userHasPermissionTo('view-warehouse-asset-index') || userHasPermissionTo('create-warehouse-asset')" 
											  		:query="query" 
											  		:caller-page="'rent period'" 
											  		:disable-add-button="formSubmitted" 
											  		:required-permission="'warehouse-asset'" 
											  		
											  		@showContentCreateForm="showContentCreateForm" 
											  		@searchData="searchData($event)" 
											  		@fetchAllContents="fetchAllContents"
											  	></search-and-addition-option>
											</div>
											
											<div class="col-sm-12 col-lg-12">
										  		<tab 
										  			v-show="query === ''" 
										  			:tab-names="['current', 'trashed']" 
										  			:current-tab="currentTab" 

										  			@showCurrentContents="showCurrentContents" 
										  			@showTrashedContents="showTrashedContents" 
										  		></tab>

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
																			Name
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
																		<a 
																			href="javascript:void(0)" 
																			@click="changeDaysOrder"
																		> 
																			# Days
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

																	<th v-if="userHasPermissionTo('update-warehouse-asset') || userHasPermissionTo('delete-warehouse-asset')">
																		Actions
																	</th>
																</tr>
															</thead>
															<tbody>

																<tr 
																	v-for="content in contentsToShow" 
																	:key="'content-key-' + content.id" 
																>
																	<td>
																		{{ content.name | capitalize }}
																	</td>

																	<td>
																		{{ content.number_days }} Days
																	</td>
																	
																	<td v-if="userHasPermissionTo('update-warehouse-asset') || userHasPermissionTo('delete-warehouse-asset')">
																		
																		<!-- 
																		<button type="button" 
																				class="btn btn-grd-info btn-icon" 
																				data-toggle="tooltip" data-placement="top" title="View Details"  
																				@click="$emit('showContentDetails', content)"
																		>
																			<i class="fa fa-eye"></i>
																		</button>
 																		-->

																		<button type="button" 
																				class="btn btn-grd-primary btn-icon" 
																				data-toggle="tooltip" data-placement="top" title="Edit" 
																				v-show="! content.deleted_at" 
																				:disabled="formSubmitted" 
																				@click="openContentEditForm(content)" 
																				v-if="userHasPermissionTo('update-warehouse-asset')" 
																		>
																			<i class="fa fa-edit"></i>
																		</button>

																		<button type="button" 
																				class="btn btn-grd-danger btn-icon" 
																				data-toggle="tooltip" data-placement="top" title="Delete" 
																				v-show="!content.deleted_at" 
																				:disabled="formSubmitted" 
																				@click="openContentDeleteForm(content)" 
																				v-if="userHasPermissionTo('delete-warehouse-asset')" 
																		>
																			<i class="fa fa-trash"></i>
																		</button>

																		<button type="button" 
																				class="btn btn-grd-warning btn-icon" 
																				data-toggle="tooltip" data-placement="top" title="Restore" 
																				v-show="content.deleted_at" 
																				:disabled="formSubmitted" 
																				@click="openContentRestoreForm(content)" 
																				v-if="userHasPermissionTo('delete-warehouse-asset')" 
																		>
																			<i class="fa fa-undo"></i>
																		</button>
																	</td>
															    
																</tr>
																<tr 
															  		v-show="!contentsToShow.length"
															  	>
														    		<td :colspan="(userHasPermissionTo('update-warehouse-asset') || userHasPermissionTo('delete-warehouse-asset')) ? 3 : 2">
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
																			Name
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
																		<a 
																			href="javascript:void(0)" 
																			@click="changeDaysOrder"
																		> 
																			# Days
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

																	<th v-if="userHasPermissionTo('update-warehouse-asset') || userHasPermissionTo('delete-warehouse-asset')">
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
																@click="query === '' ? fetchAllContents() : searchData()"
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
																@paginate="query === '' ? fetchAllContents() : searchData()"
															>
															</pagination>
														</div>
													</div>
										  		</div>

										  		<!-- 
										  		<table-with-soft-delete-option 
										  			:query="query" 
										  			:per-page="perPage"  
										  			:column-names="['name']" 
										  			:column-values-to-show="['name']" 
										  			:contents-to-show = "contentsToShow" 
										  			:pagination = "pagination"

										  			@showContentDetails="showContentDetails($event)" 
										  			@openContentEditForm="openContentEditForm($event)" 
										  			@openContentDeleteForm="openContentDeleteForm($event)" 
										  			@openContentRestoreForm="openContentRestoreForm($event)" 
										  			@changeNumberContents="changeNumberContents($event)" 
										  			@fetchAllContents="fetchAllContents" 
										  			@searchData="searchData" 
										  		>	
										  		</table-with-soft-delete-option>
 												-->
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

		<asset-create-or-edit-modal 
			v-if="userHasPermissionTo('create-warehouse-asset') || userHasPermissionTo('update-warehouse-asset')" 
			:csrf="csrf" 
			:create-mode="createMode" 
			:caller-page="'rent period'" 
			:form-submitted="formSubmitted"
			:single-asset-data="singleAssetData" 

			@storeAsset="storeAsset($event)" 
			@updateAsset="updateAsset($event)" 
		></asset-create-or-edit-modal>

		<delete-confirmation-modal 
			v-if="userHasPermissionTo('delete-warehouse-asset')" 
			:csrf="csrf" 
			:form-submitted="formSubmitted"
			:submit-method-name="'deleteAsset'" 
			:content-to-delete="singleAssetData"
			:restoration-message="'But once you think, you can restore this item !'" 
			
			@deleteAsset="deleteAsset($event)" 
		></delete-confirmation-modal>

		<restore-confirmation-modal 
			v-if="userHasPermissionTo('delete-warehouse-asset')" 
			:csrf="csrf" 
			:form-submitted="formSubmitted"
			:submit-method-name="'restoreAsset'" 
			:content-to-restore="singleAssetData"
			:restoration-message="'This will restore all related items !'" 

			@restoreAsset="restoreAsset($event)" 
		></restore-confirmation-modal>

		<!-- 
		<asset-view-modal 
			:caller-page="'rent period'" 
			:asset-to-view="singleAssetData" 
			:properties-to-show="['name']"
		></asset-view-modal>
 		-->

	</div>

</template>

<script type="text/javascript">

	import axios from 'axios';

    let singleAssetData = {
    	
    };

	export default {

	    data() {

	        return {

	        	query : '',
	        	error : '',
    			perPage : 10,
	        	loading : false,
	        	currentTab : 'current',

	        	ascending : false,
	      		descending : false,

	        	createMode : true,
	        	formSubmitted : false,

	        	allFetchedContents : [],
	        	contentsToShow : [],

	        	pagination: {
		        	current_page: 1
		      	},

	        	singleAssetData : singleAssetData,

	            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

	        }

		},
		
		created(){

			this.fetchAllContents();			

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

			fetchAllContents() {
				
				this.query = '';
				this.error = '';
				this.loading = true;
				this.allFetchedContents = [];
				
				axios
					.get('/api/rent-periods/' + this.perPage + "?page=" + this.pagination.current_page)
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
			searchData(emitedValue=false) {

				if (emitedValue) {
					this.query=emitedValue;
				}

				this.error = '';
				this.allFetchedContents = [];
				this.pagination.current_page = 1;
				
				axios
				.get(
					"/api/search-rent-periods/" + this.query + "/" + this.perPage + "?page=" + this.pagination.current_page
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
			showContentDetails(object) {	
				this.singleAssetData = object;
				$('#asset-view-modal').modal('show');
			},
			showContentCreateForm() {
				this.createMode = true;
				this.formSubmitted = false;
				this.singleAssetData = {};
				$('#asset-createOrEdit-modal').modal('show');
			},
			openContentEditForm(object) {
				this.createMode = false;
				this.formSubmitted = false;
				this.singleAssetData = object;
				$('#asset-createOrEdit-modal').modal('show');
			},
			openContentDeleteForm(object) {	
				this.formSubmitted = false;
				this.singleAssetData = object;
				$('#delete-confirmation-modal').modal('show');
			},
			openContentRestoreForm(object) {
				this.formSubmitted = false;	
				this.singleAssetData = object;
				$('#restore-confirmation-modal').modal('show');
			},
			storeAsset(singleAssetData) {
				
				this.formSubmitted = true;

				axios
					.post('/rent-periods/' + this.perPage, singleAssetData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("New rent period has been created", "Success");
							this.allFetchedContents = response.data;
							this.query !== '' ? this.searchData() : this.showSelectedTabContents();
							$('#asset-createOrEdit-modal').modal('hide');
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
					});

			},
			updateAsset(singleAssetData) {
				
				this.formSubmitted = true;

				axios
					.put('/rent-periods/' + this.singleAssetData.id + '/' + this.perPage, singleAssetData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Rent period has been updated", "Success");
							this.allFetchedContents = response.data;
							this.query !== '' ? this.searchData() : this.showSelectedTabContents();
							$('#asset-createOrEdit-modal').modal('hide');
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
					});

			},
			deleteAsset(singleAssetData) {
				
				this.formSubmitted = true;

				axios
					.delete('/rent-periods/' + this.singleAssetData.id + '/' + this.perPage)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Rent period has been deleted", "Success");
							this.allFetchedContents = response.data;
							this.query !== '' ? this.searchData() : this.showSelectedTabContents();
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
					});

			},
			restoreAsset(singleAssetData) {
				
				this.formSubmitted = true;

				axios
					.patch('/rent-periods/' + this.singleAssetData.id + '/' + this.perPage)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Rent period has been restored", "Success");
							this.allFetchedContents = response.data;
							this.query !== '' ? this.searchData() : this.showSelectedTabContents();
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
					});

			},
            changeNumberContents() {
				this.pagination.current_page = 1;
				// this.perPage = expectedContentsPerPage;

				if (this.query === '') {
					this.fetchAllContents();
				}
				else {
					this.searchData();
				}
    		},
    		showSelectedTabContents() {
				
				if (this.currentTab=='current') {
					this.contentsToShow = this.allFetchedContents.current.data;
					this.pagination = this.allFetchedContents.current;
				}
				else {
					this.contentsToShow = this.allFetchedContents.trashed.data;
					this.pagination = this.allFetchedContents.trashed;
				}

			},
			showCurrentContents() {
				this.currentTab = 'current';
				this.showSelectedTabContents();
			},
			showTrashedContents() {
				this.currentTab = 'trashed';
				this.showSelectedTabContents();
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
			changeDaysOrder() {

				if (this.ascending) {
					this.ascending = false;
					this.descending = true;
					this.descendingNumeric('number_days');
				}
				else if (this.descending) {
					this.ascending = true;
					this.descending = false;
					this.ascendingNumeric('number_days');
				}
				else {
					this.ascending = true;
					this.descending = false;
					this.ascendingNumeric('number_days');
				}
				
			},
			ascendingNumeric(columnValue) {
				this.contentsToShow.sort(
			 		function(a, b){
						return a[columnValue] - b[columnValue];
					}
				);
			},
			descendingNumeric(columnValue) {
				this.contentsToShow.sort(
			 		function(a, b){
						return b[columnValue] - a[columnValue];
					}
				);
			},
			ascendingAlphabets(columnValue) {
				this.contentsToShow.sort(
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
				this.contentsToShow.sort(
			 		function(a, b){
						var x = a[columnValue] ? a[columnValue].toLowerCase() : '';
						var y = b[columnValue] ? b[columnValue].toLowerCase() : '';
						if (x > y) {return -1;}
						if (x < y) {return 1;}
						return 0;
					}
				);
			},
            
		}
  	}

</script>