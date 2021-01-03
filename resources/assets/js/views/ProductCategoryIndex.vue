
<template>

	<div class="pcoded-content">

		<breadcrumb 
			:title="'product categories'" 
			:message="'All our product categories'"
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
											  		:query="query" 
											  		:caller-page="'product categories'" 
											  		
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
			:single-asset-data="singleAssetData" 
			:csrf="csrf"

			@storeAsset="storeAsset($event)" 
			@updateAsset="updateAsset($event)" 
		></asset-create-or-edit-modal>
 	-->

 		<!--Create Or Edit Modal -->
		<div class="modal fade" id="asset-createOrEdit-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">
							{{ createMode ? 'Create' : 'Edit' }} Category
						</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
						
					<form 	
						class="form-horizontal" 
						v-on:submit.prevent="createMode ? storeAsset() : updateAsset()" 
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
										:class="!errors.asset.name  ? 'is-valid' : 'is-invalid'" 
										@change="validateFormInput('name')" 
										required="true" 
									>

									<div class="invalid-feedback">
							        	{{ errors.asset.name }}
							  		</div>
								</div>
							</div>
							<div class="form-row">
								<div class="form-group col-md-12">
									<label for="inputUsername">Parent Category</label>
									<multiselect 
                              			v-model="singleAssetData.category"
                              			placeholder="Parent Category" 
                                  		label="name" 
                                  		track-by="id" 
                                  		:options="allCategories" 
                                  		:required="true" 
                                  		:allow-empty="false"
                                  		selectLabel = "Press/Click"
                                  		deselect-label="Can't remove single value"
                              		>
                                	</multiselect>
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

		<delete-confirmation-modal 
			:csrf="csrf" 
			:submit-method-name="'deleteAsset'" 
			:content-to-delete="singleAssetData"
			:restoration-message="'But once you think, you can restore this item !'" 
			
			@deleteAsset="deleteAsset($event)" 
		></delete-confirmation-modal>

		<restore-confirmation-modal 
			:csrf="csrf" 
			:submit-method-name="'restoreAsset'" 
			:content-to-restore="singleAssetData"
			:restoration-message="'This will restore all related items !'" 

			@restoreAsset="restoreAsset($event)" 
		></restore-confirmation-modal>

	<!-- 
		<asset-view-modal 
			:caller-page="'variation'" 
			:asset-to-view="singleAssetData" 
			:properties-to-show="['name']"
		></asset-view-modal>
 	-->

 		<!-- Modal -->
		<div class="modal fade" id="asset-view-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Category Details</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">

						<div class="form-row"> 
						    <div class="form-group col-md-6 text-right">
								<label class="font-weight-bold">Name</label>
							</div>
							<div class="form-group col-md-6 text-left">
								{{ singleAssetData.name }}
							</div>
						</div>

						<div class="form-row"> 
						    <div class="form-group col-md-6 text-right">
								<label class="font-weight-bold">Parent Category</label>
							</div>
							<div class="form-group col-md-6 text-left">
								{{ singleAssetData.category ? singleAssetData.category.name : 'None' }}
							</div>
						</div>

					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>

	</div>

</template>

<script type="text/javascript">

	import axios from 'axios';
	import Multiselect from 'vue-multiselect';

    let singleAssetData = {
    	// category : {},
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
	        	currentTab : 'current',

	        	createMode : true,
	        	submitForm : true,

	        	allCategories : [],
	        	allFetchedContents : [],
	        	contentsToShow : [],

	        	pagination: {
		        	current_page: 1
		      	},

	        	singleAssetData : singleAssetData,

	        	errors : {
					asset : {},
				},

	            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

	        }

		},
		
		created(){

			this.fetchAllContents();
			this.fetchProductAllCategories();
		
		},

		watch: {
			'singleAssetData.category': function (object) {
				if (object && Object.keys(object).length > 0) {
					this.singleAssetData.parent_category_id = object.id;
				}
			},
		},
		
		methods : {

			fetchAllContents() {
				
				this.query = '';
				this.error = '';
				this.loading = true;
				this.allFetchedContents = [];
				
				axios
					.get('/api/product-categories/' + this.perPage + "?page=" + this.pagination.current_page)
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
			fetchProductAllCategories() {
				
				this.query = '';
				this.error = '';
				this.loading = true;
				this.allCategories = [];
				
				axios
					.get('/api/product-categories/')
					.then(response => {
						if (response.status == 200) {
							this.allCategories = response.data;
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
					"/api/search-product-categories/" + this.query + "/" + this.perPage + "?page=" + this.pagination.current_page
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
	        	this.submitForm = true;
	        	
				this.errors = {
					asset : {},
				};

				this.singleAssetData = {
					// category : {},
				};

				$('#asset-createOrEdit-modal').modal('show');
			},
			openContentEditForm(object) {
				this.submitForm = true;
				this.createMode = false;

				this.errors = {
					asset : {},
				};

				this.singleAssetData = object;

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
			storeAsset() {
				
				if (!this.verifyUserInput()) {
					this.submitForm = false;
					return;
				}

				axios
					.post('/product-categories/' + this.perPage, this.singleAssetData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("New category has been created", "Success");
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
					});

			},
			updateAsset() {
				
				if (!this.verifyUserInput()) {
					this.submitForm = false;
					return;
				}

				axios
					.put('/product-categories/' + this.singleAssetData.id + '/' + this.perPage, this.singleAssetData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Category has been updated", "Success");
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
					});

			},
			deleteAsset(singleAssetData) {
				
				axios
					.delete('/product-categories/' + singleAssetData.id + '/' + this.perPage, singleAssetData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Category has been deleted", "Success");
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
					});

			},
			restoreAsset(singleAssetData) {
				
				axios
					.patch('/product-categories/' + singleAssetData.id + '/' + this.perPage, singleAssetData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Category has been restored", "Success");
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
					});

			},
            changeNumberContents(expectedContentsPerPage) {
				this.pagination.current_page = 1;
				this.perPage = expectedContentsPerPage;

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
			verifyUserInput() {
				this.validateFormInput('name');

				if (this.errors.asset.constructor === Object && Object.keys(this.errors.asset).length == 0) {
					return true;
				}

				return false;
			},
			validateFormInput (formInputName) {
				
				this.submitForm = false;

				switch(formInputName) {

					case 'name' :

						if (!this.singleAssetData.name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.asset.name = 'No special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.asset, 'name');
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