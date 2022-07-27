
<template>
	<div class="pcoded-content">
		<breadcrumb 
			:icon="'delivery-companies'"
			:title="'delivery company'" 
			:message="'All our companies for delivery'"
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
											  		:query="query" 
											  		:caller-page="'Delivery Company'" 
											  		:required-permission = "'logistic-asset'" 
											  		:disable-add-button="formSubmitted" 
											  		
											  		@showContentCreateForm="showContentCreateForm" 
											  		@searchData="pagination.current_page = 1; searchData($event)" 
											  		@fetchAllContents="pagination.current_page = 1; fetchAllContents()"
											  	></search-and-addition-option>
											</div>
											
											<div class="col-sm-12 col-lg-12">
										  		<loading v-show="loading"></loading>

										  		<tab 
										  			v-show="query === '' && ! loading" 
										  			:tab-names="['current', 'trashed']" 
										  			:current-tab="currentTab" 

										  			@showCurrentContents="showcontentsToShow" 
										  			@showTrashedContents="showTrashedContents" 
										  		></tab>

										  		<!-- 
										  		<div class="tab-content card-block pl-0 pr-0">
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
																			@click="changePricesOrder"
																		> 
																			Commission
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
																		Actions
																	</th>
																</tr>
															</thead>

															<tbody>
																<tr 
																	v-for="content in contentsToShow" 
																	:key="'content-key-' + content.id" 
																	:class="content.id==singleAssetData.id ? 'highlighted' : ''"
																>
																	<td>
																		{{ content.name | capitalize }}
																	</td>

																	<td>
																		{{ content.commission }}
																	</td>
																	
																	<td> 
																		<button type="button" 
																				class="btn btn-grd-info btn-icon" 
																				v-tooltip.bottom-end="'View Details'"  
																				@click="showContentDetails(content)"
																		>
																			<i class="fa fa-eye"></i>
																		</button>
 																		
																		<button type="button" 
																				class="btn btn-grd-primary btn-icon" 
																				v-tooltip.bottom-end="'Edit'" 
																				v-show="!content.deleted_at" 
																				@click="openContentEditForm(content)" 
																				v-if="userHasPermissionTo('update-logistic-asset')" 
																		>
																			<i class="fa fa-edit"></i>
																		</button>

																		<button type="button" 
																				class="btn btn-grd-danger btn-icon" 
																				v-tooltip.bottom-end="'Delete'" 
																				v-show="!content.deleted_at" 
																				@click="openContentDeleteForm(content)" 
																				v-if="userHasPermissionTo('delete-logistic-asset')" 
																		>
																			<i class="fa fa-trash"></i>
																		</button>

																		<button type="button" 
																				class="btn btn-grd-warning btn-icon" 
																				v-tooltip.bottom-end="'Restore'" 
																				v-show="content.deleted_at" 
																				@click="openContentRestoreForm(content)" 
																				v-if="userHasPermissionTo('delete-logistic-asset')" 
																		>
																			<i class="fa fa-undo"></i>
																		</button>
																	</td>
															    
																</tr>

																<tr 
															  		v-show="! contentsToShow.length"
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
																			@click="changePricesOrder"
																		> 
																			Commission
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
																class="btn waves-effect waves-dark btn-primary btn-outline-primary btn-sm" 
																v-tooltip.bottom-end="'Reload'" 
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
										  		-->

										  		<table-with-soft-delete-option 
										  			:query="query" 
										  			:loading="loading"  
										  			:per-page="perPage"  
										  			:pagination = "pagination" 
										  			:form-submitted="formSubmitted" 
										  			:column-names="['name', 'commission']" 
										  			:required-permission = "'logistic-asset'" 
										  			:column-values-to-show="['name', 'commission']" 
										  			:contents-to-show = "contentsToShow" 
										  			:current-content="singleAssetData"

										  			@showContentDetails="showContentDetails($event)" 
										  			@openContentEditForm="openContentEditForm($event)" 
										  			@openContentDeleteForm="openContentDeleteForm($event)" 
										  			@openContentRestoreForm="openContentRestoreForm($event)" 
										  			@changeNumberContents="changeNumberContents($event)" 
										  			@fetchAllContents="fetchAllContents" 
										  			@searchData="searchData" 
										  		>	
										  		</table-with-soft-delete-option>
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

		<!-- Modal -->
		<div class="modal fade" id="asset-createOrEdit-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" v-if="userHasPermissionTo('create-logistic-asset') || userHasPermissionTo('update-logistic-asset')">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">
							{{ createMode ? 'Create' : 'Edit' }} {{ singleAssetData.name | capitalize }}
						</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
						
					<form 	
						class="form-horizontal" 
						v-on:submit.prevent="verifyUserInput" 
						autocomplete="off" 
					>

						<input type="hidden" name="_token" :value="csrf">

						<div class="modal-body">
							<div class="form-row">
								<div class="form-group col-md-12">
									<label for="inputFirstName">Name</label>
									<input type="text" 
										class="form-control" 
										v-model="singleAssetData.name" 
										placeholder="Name should be unique" 
										required="true" 
										:class="!errors.asset.name  ? 'is-valid' : 'is-invalid'" 
										@change="validateFormInput('name')" 
									>

									<div class="invalid-feedback">
							        	{{ errors.asset.name }}
							  		</div>
								</div>
							</div>

							<div class="form-row">
								<div class="form-group col-md-12">
									<label for="inputFirstName">Commission</label>
									<div class="input-group mb-0">
										<input type="number" 
											class="form-control" 
											required="true" 
											v-model="singleAssetData.commission" 
											:class="!errors.asset.commission  ? 'is-valid' : 'is-invalid'" 
											@change="validateFormInput('commission')" 
										>
										<div class="input-group-append">
											<span class="input-group-text">%</span>
										</div>
									</div>

									<div 
										style="display: block;" 
										class="invalid-feedback" 
										v-show="errors.asset.commission"
									>
							        	{{ errors.asset.commission }}
							  		</div>
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
								<button type="button" class="btn waves-effect waves-dark btn-secondary btn-outline-secondary float-left" data-dismiss="modal">
									Close
								</button>
								<button type="submit" class="btn waves-effect waves-dark btn-primary btn-outline-primary float-right" :disabled="!submitForm || formSubmitted">
									{{ createMode ? 'Save' : 'Update' }}
								</button>
							</div>
						</div>

					</form>
				</div>
			</div>
		</div>

		<!-- View Modal -->
		<div class="modal fade" id="asset-view-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">{{ singleAssetData.name | capitalize }} Details</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">

						<div class="form-row"> 
						    <div class="form-group col-md-6 text-right">
								<label class="font-weight-bold">Name:</label>
							</div>
							<div class="form-group col-md-6 text-left">
								{{ singleAssetData.name | capitalize }}
							</div>
						</div>

						<div class="form-row"> 
						    <div class="form-group col-md-6 text-right">
								<label class="font-weight-bold">Commission:</label>
							</div>
							<div class="form-group col-md-6 text-left">
								{{ singleAssetData.commission }} %
							</div>
						</div>

						<div class="form-row"> 
						    <div class="form-group col-md-6 text-right">
								<label class="font-weight-bold">Service Schedule:</label>
							</div>
							<div class="form-group col-md-6 text-left">
								
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn waves-effect waves-dark btn-secondary btn-outline-secondary btn-block btn-sm" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>

		<delete-confirmation-modal 
			v-if="userHasPermissionTo('delete-logistic-asset')" 
			:csrf="csrf" 
			:submit-method-name="'deleteAsset'" 
			:content-to-delete="singleAssetData"
			:restoration-message="'But once you think, you can restore this item !'" 
			
			@deleteAsset="deleteAsset($event)" 
		></delete-confirmation-modal>

		<restore-confirmation-modal 
			v-if="userHasPermissionTo('delete-logistic-asset')" 
			:csrf="csrf" 
			:submit-method-name="'restoreAsset'" 
			:content-to-restore="singleAssetData"
			:restoration-message="'This will restore all related items !'" 

			@restoreAsset="restoreAsset($event)" 
		></restore-confirmation-modal>
	</div>

</template>

<script type="text/javascript">

	import axios from 'axios';
	import CKEditor from '@ckeditor/ckeditor5-vue';
	import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

    let singleAssetData = {
    	
    };

	export default {

		components: { 

			ckeditor: CKEditor.component,
			
		},

	    data() {

	        return {

	        	query : '',
	        	error : '',
    			perPage : 10,
	        	loading : false,
	        	currentTab : 'current',

	        	// 	ascending : false,
	      		// descending : false,

	        	editor: ClassicEditor,
	        	
	        	createMode : true,

	        	allFetchedContents : [],
	        	contentsToShow : [],

	        	pagination: {
		        	current_page: 1
		      	},

	        	singleAssetData : singleAssetData,

	        	errors : {
	        		asset : {},
	        	},

	        	submitForm : true,
	        	formSubmitted : false,

	            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

	        }

		},
		
		created(){

			this.fetchAllContents();			

		},

		watch : {

			query : function(val){
				
				this.pagination.current_page = 1; 

				if (val==='') {
					
					this.fetchAllContents();
				
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

			fetchAllContents() {
				
				this.query = '';
				this.error = '';
				this.loading = true;
				this.formSubmitted = true;
				this.allFetchedContents = [];
				
				axios
					.get('/api/delivery-companies/' + this.perPage + "?page=" + this.pagination.current_page)
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
						this.formSubmitted = false;
					});

			},
			searchData(emittedValue = false) {

				if (emittedValue) {

					this.query = emittedValue;

				}

				this.error = '';
				this.loading = true;
				this.formSubmitted = true;
				this.allFetchedContents = [];
				// this.pagination.current_page = 1;
				
				axios
				.get(
					"/api/search-delivery-companies/" + this.query + "/" + this.perPage + "?page=" + this.pagination.current_page
				)
				.then(response => {
					this.allFetchedContents = response.data;
					this.contentsToShow = this.allFetchedContents.all.data;
					this.pagination = response.data.all;
				})
				.catch(e => {
					this.error = e.toString();
				})
				.finally(response => {
					this.loading = false;
					this.formSubmitted = false;
				});

			},
			showContentDetails(object) {	
				this.singleAssetData = object;
				$('#asset-view-modal').modal('show');
			},
			showContentCreateForm() {
				this.createMode = true;
				
				this.singleAssetData = {};
				
				this.errors = {
					asset : {}
				};

				$('#asset-createOrEdit-modal').modal('show');
			},
			openContentEditForm(object) {
				this.createMode = false;

				this.singleAssetData = object;

				this.errors = {
					asset : {}
				};

				$('#asset-createOrEdit-modal').modal('show');
			},
			openContentDeleteForm(object) {	
				this.singleAssetData = object;
				$('#delete-confirmation-modal').modal('show');
			},
			openContentRestoreForm(object) {	
				this.singleAssetData = object;
				$('#restore-confirmation-modal').modal('show');
			},
			verifyUserInput() {

				this.validateFormInput('name');
				this.validateFormInput('commission');
            	
            	if (this.errors.asset.constructor === Object && Object.keys(this.errors.asset).length !== 0) {
					this.submitForm = false;
					return;
				}
				else {

	            	if (this.createMode) {
	            		
	            		this.storeAsset();
	            	}

	            	else {

	            		this.updateAsset();
	            	}

				}

            },
			storeAsset() {

				this.formSubmitted = true;
				
				axios
					.post('/delivery-companies/' + this.perPage, this.singleAssetData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("New package has been created", "Success");
							this.allFetchedContents = response.data;

							this.pagination.current_page = 1; 
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
			updateAsset() {

				this.formSubmitted = true;
				
				axios
					.put('/delivery-companies/' + this.singleAssetData.id + '/' + this.perPage, this.singleAssetData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Company has been updated", "Success");
							this.allFetchedContents = response.data;

							this.pagination.current_page = 1; 
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
					.delete('/delivery-companies/' + this.singleAssetData.id + '/' + this.perPage)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Company has been deleted", "Success");
							this.allFetchedContents = response.data;

							this.pagination.current_page = 1; 
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
					.patch('/delivery-companies/' + this.singleAssetData.id + '/' + this.perPage)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Company has been restored", "Success");
							this.allFetchedContents = response.data;

							this.pagination.current_page = 1; 
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
					this.contentsToShow = this.allFetchedContents.current ? this.allFetchedContents.current.data : [];
					this.pagination = this.allFetchedContents.current;
				}
				else {
					this.contentsToShow = this.allFetchedContents.trashed ? this.allFetchedContents.trashed.data : [];
					this.pagination = this.allFetchedContents.trashed;
				}

			},
			showcontentsToShow() {
				this.currentTab = 'current';
				this.showSelectedTabContents();
			},
			showTrashedContents() {
				this.currentTab = 'trashed';
				this.showSelectedTabContents();
			},
			/*
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
			changePricesOrder() {

				if (this.ascending) {

					this.ascending = false;
					this.descending = true;

					this.descendingNumeric('commission');

				}
				else if (this.descending) {

					this.ascending = true;
					this.descending = false;

					this.ascendingNumeric('commission');

				}
				else {

					this.ascending = true;
					this.descending = false;

					this.ascendingNumeric('commission');

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
			*/
			validateFormInput (formInputName) {
				
				this.submitForm = false;

				switch(formInputName) {

					case 'name' :

						if (!this.singleAssetData.name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.asset.name = 'No space or special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.asset, 'name');
						}

						break;

					
					case 'commission' :

						if (! this.singleAssetData.commission) {
							this.errors.asset.commission = 'Commission is required';
						}
						else if (this.singleAssetData.commission < 0) {
							this.errors.asset.commission = 'Rate should be between 0 to 100';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.asset, 'commission');
						}

						break;					

				}
	 
			},
            
		}
  	}

</script>