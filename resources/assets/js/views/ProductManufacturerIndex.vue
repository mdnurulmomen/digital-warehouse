
<template v-if="userHasPermissionTo('view-product-asset-index')">
	<div class="pcoded-content">
		<breadcrumb 
			:icon="'manufacturers'"
			:title="'product manufacturers'" 
			:message="'All our product-manufacturers'"
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
											  		v-if="userHasPermissionTo('view-product-asset-index') || userHasPermissionTo('create-product-asset')" 
											  		:query="query" 
											  		:caller-page="'product manufacturer'" 
											  		:disable-add-button="formSubmitted" 
											  		:required-permission = "'product-asset'" 
											  		
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

										  			@showCurrentContents="showCurrentContents" 
										  			@showTrashedContents="showTrashedContents" 
										  		></tab>

										  		<table-with-soft-delete-option 
										  			:query="query" 
										  			:loading="loading" 
										  			:per-page="perPage" 
										  			:column-names="['name']" 
										  			:column-values-to-show="['name']" 
										  			:contents-to-show = "contentsToShow" 
										  			:pagination = "pagination" 
										  			:required-permission="'product-asset'" 
										  			:form-submitted="formSubmitted" 
										  			:current-content="singleAssetData"

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
		 
		<asset-create-or-edit-modal 
			v-if="userHasPermissionTo('create-product-asset') || userHasPermissionTo('update-product-asset')" 
			:csrf="csrf" 
			:create-mode="createMode" 
			:caller-page="'manufacturer'" 
			:form-submitted="formSubmitted"
			:single-asset-data="singleAssetData" 

			@storeAsset="storeAsset($event)" 
			@updateAsset="updateAsset($event)" 
		></asset-create-or-edit-modal>

 		<!--Create Or Edit Modal -->
		<!-- 
		<div class="modal fade" id="asset-createOrEdit-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" v-if="userHasPermissionTo('create-product-asset') || userHasPermissionTo('update-product-asset')">
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
										:class="!errors.manufacturer.name  ? 'is-valid' : 'is-invalid'" 
										@change="validateFormInput('name')" 
										required="true" 
									>

									<div class="invalid-feedback">
							        	{{ errors.manufacturer.name }}
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
								<button type="button" class="btn btn-secondary float-left" data-dismiss="modal">
									Close
								</button>
								<button type="submit" class="btn btn-primary float-right" :disabled="!submitForm">
									{{ createMode ? 'Save' : 'Update' }}
								</button>
							</div>
						</div>

					</form>
				</div>
			</div>
		</div> 
		-->

		<delete-confirmation-modal 
			v-if="userHasPermissionTo('delete-product-asset')" 
			:csrf="csrf" 
			:form-submitted="formSubmitted"
			:submit-method-name="'deleteAsset'" 
			:content-to-delete="singleAssetData"
			:restoration-message="'But once you think, you can restore this item !'" 
			
			@deleteAsset="deleteAsset($event)" 
		></delete-confirmation-modal>

		<restore-confirmation-modal 
			v-if="userHasPermissionTo('delete-product-asset')" 
			:csrf="csrf" 
			:form-submitted="formSubmitted"
			:submit-method-name="'restoreAsset'" 
			:content-to-restore="singleAssetData"
			:restoration-message="'This will restore all related items !'" 

			@restoreAsset="restoreAsset($event)" 
		></restore-confirmation-modal>
 
		<!-- 
		<asset-view-modal 
			:caller-page="'manufacturer'" 
			:asset-to-view="singleAssetData" 
			:properties-to-show="['name']"
		></asset-view-modal> 
		-->	

 		<!-- View Modal -->
		<!-- 
		<div class="modal fade" id="product-manufacturer-view-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
								{{ singleAssetData.name | capitalize }}
							</div>
						</div>

					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary btn-block btn-sm" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div> 
		-->
	</div>
</template>

<script type="text/javascript">

    let singleAssetData = {
    	// manufacturer : {},
    };

	export default {

	    data() {

	        return {

	        	query : '',
	        	error : '',
    			perPage : 10,
	        	loading : false,
	        	currentTab : 'current',

	        	createMode : true,
	        	// submitForm : true,
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
					.get('/api/manufacturers/' + this.perPage + "?page=" + this.pagination.current_page)
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
				// this.pagination.current_page = 1;
				
				axios
				.get(
					"/api/search-manufacturers/" + this.query + "/" + this.perPage + "?page=" + this.pagination.current_page
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
			/*
			showContentDetails(object) {	
				this.singleAssetData = object;
				$('#product-manufacturer-view-modal').modal('show');
			},
			*/
			showContentCreateForm() {
				this.createMode = true;
	        	// this.submitForm = true;
	        	this.formSubmitted = false;

				this.singleAssetData = {
					// manufacturer : {},
				};

				$('#asset-createOrEdit-modal').modal('show');
			},
			openContentEditForm(object) {
				this.createMode = false;
				// this.submitForm = true;
				this.formSubmitted = false;

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
			storeAsset(singleAssetData) {
				
				this.formSubmitted = true;

				axios
					.post('/manufacturers/' + this.perPage, this.singleAssetData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("New manufacturer has been created", "Success");

							this.pagination.current_page = 1; 
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
					.put('/manufacturers/' + this.singleAssetData.id + '/' + this.perPage, this.singleAssetData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Category has been updated", "Success");

							this.pagination.current_page = 1; 
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
					.delete('/manufacturers/' + singleAssetData.id + '/' + this.perPage, singleAssetData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Category has been deleted", "Success");

							this.pagination.current_page = 1; 
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
					.patch('/manufacturers/' + singleAssetData.id + '/' + this.perPage, singleAssetData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Category has been restored", "Success");

							this.pagination.current_page = 1; 
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
			/*
			goCategoryProducts(productCategory) {
				
				this.$router.push({ name: 'manufacturer-products', params: { manufacturer: productCategory, categoryName: productCategory.name.replace(/ /g,"-") }});
			},
			*/
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
					this.contentsToShow = this.allFetchedContents.current ? this.allFetchedContents.current.data : [];
					this.pagination = this.allFetchedContents.current;
				}
				else {
					this.contentsToShow = this.allFetchedContents.trashed ? this.allFetchedContents.trashed.data : [];
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
			/*
			verifyUserInput() {
				this.validateFormInput('name');

				if (this.errors.manufacturer.constructor === Object && Object.keys(this.errors.manufacturer).length == 0) {
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

					case 'name' :

						if (!this.singleAssetData.name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.manufacturer.name = 'No special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.manufacturer, 'name');
						}

						break;

				}
	 
			}
			*/
            
		}
  	}

</script>