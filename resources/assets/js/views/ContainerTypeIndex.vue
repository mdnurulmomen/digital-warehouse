
<template v-if="userHasPermissionTo('view-warehouse-asset-index')">
	<div class="pcoded-content">
		<breadcrumb 
			:icon="'fas fa-th-list'"
			:title="'containers'" 
			:message="'All our containers'"
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
											  		v-if="userHasPermissionTo('view-warehouse-asset-index') || userHasPermissionTo('create-warehouse-asset')" 
											  		:query="query" 
											  		:caller-page="'container'" 
											  		:disable-add-button="formSubmitted" 
											  		:required-permission="'warehouse-asset'" 
											  		
											  		@showContentCreateForm="showContentCreateForm" 
											  		@searchData="pagination.current_page = 1; searchData($event)" 
											  		@fetchAllContents="pagination.current_page = 1; fetchAllContents()"
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

										  		<loading v-show="loading"></loading>

										  		<table-with-soft-delete-option 
										  			:query="query" 
										  			:loading="loading" 
										  			:per-page="perPage" 
										  			:form-submitted="formSubmitted"
										  			:column-names="['name', 'Recognizing Code', 'Storage Type']" 
										  			:column-values-to-show="['name', 'code', 'storageType']" 
										  			:contents-to-show = "contentsToShow" 
										  			:pagination = "pagination" 
										  			:required-permission="'warehouse-asset'" 
										  			:current-content="singleAssetData"

										  			@showContentDetails="showContentDetails" 
										  			@openContentEditForm="openContentEditForm" 
										  			@openContentDeleteForm="openContentDeleteForm" 
										  			@openContentRestoreForm="openContentRestoreForm" 
										  			@changeNumberContents="changeNumberContents" 
										  			@fetchAllContents="fetchAllContents()" 
										  			@searchData="searchData()" 
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
			:caller-page="'container'" 
			:single-asset-data="singleAssetData" 
			:csrf="csrf"

			@storeAsset="storeAsset" 
			@updateAsset="updateAsset" 
		></asset-create-or-edit-modal>
 		-->

 		<container-type-create-or-edit-modal 
			v-if="userHasPermissionTo('create-warehouse-asset') || userHasPermissionTo('update-warehouse-asset')" 
			:csrf="csrf"
			:create-mode="createMode" 
			:caller-page="'container type'" 
			:form-submitted="formSubmitted"
			:all-storage-types="allStorageTypes"
			:single-asset-data="singleAssetData" 
			:general_settings="general_settings"

			@storeAsset="storeContainer($event)" 
			@updateAsset="updateContainer($event)" 
		></container-type-create-or-edit-modal>

		<delete-confirmation-modal 
			v-if="userHasPermissionTo('delete-warehouse-asset')" 
			:csrf="csrf" 
			:form-submitted="formSubmitted"
			:submit-method-name="'deleteAsset'" 
			:content-to-delete="singleAssetData"
			:restoration-message="'But once you think, you can restore this item !'" 
			
			@deleteAsset="deleteAsset" 
		></delete-confirmation-modal>

		<restore-confirmation-modal 
			v-if="userHasPermissionTo('delete-warehouse-asset')" 
			:csrf="csrf" 
			:form-submitted="formSubmitted"
			:submit-method-name="'restoreAsset'" 
			:content-to-restore="singleAssetData"
			:restoration-message="'This will automatically be added to all related items !'" 

			@restoreAsset="restoreAsset" 
		></restore-confirmation-modal>

		<container-type-view-modal 
			:caller-page="'container'" 
			:single-asset-data="singleAssetData" 
			:general_settings="general_settings"
		></container-type-view-modal>
	</div>
</template>

<script type="text/javascript">

	import axios from 'axios';
	import ContainerTypeViewModal from '../components/ContainerTypeViewModal.vue';
	import ContainerTypeCreateOrEditModal from '../components/ContainerTypeCreateOrEditModal.vue';

    let singleAssetData = {
    	shelf : {
    		unit : {},
    	},
    };

	export default {

	    components:{
	    	ContainerTypeViewModal,
	    	ContainerTypeCreateOrEditModal
	    },

	    data() {

	        return {

	        	query : '',
	        	error : '',
    			perPage : 10,
	        	loading : false,
	        	currentTab : 'current',

	        	// submitForm : true,
	        	createMode : true,
	        	formSubmitted : false,

	        	contentsToShow : [],
	        	allFetchedContents : [],

	        	allStorageTypes : [],

	        	pagination: {
		        	current_page: 1
		      	},

	        	singleAssetData : singleAssetData,

	        	general_settings : JSON.parse(window.localStorage.getItem('general_settings')),

	            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

	        }

		},
		
		created(){

			this.fetchAllContents();
			this.fetchAllStorageTypes();

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
					.get('/api/containers/' + this.perPage + "?page=" + this.pagination.current_page)
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
			fetchAllStorageTypes() {
				
				this.query = '';
				this.error = '';
				this.loading = true;
				this.allStorageTypes = [];
				
				axios
					.get('/api/storage-types/')
					.then(response => {
						if (response.status == 200) {
							this.allStorageTypes = response.data ?? [];
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
					"/api/search-containers/" + this.query + "/" + this.perPage + "?page=" + this.pagination.current_page
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
				$('#container-view-modal').modal('show');
			},
			showContentCreateForm() {
				// this.submitForm = true;
				this.createMode = true;
				this.formSubmitted = false;
				
				this.singleAssetData = {
					shelf : {
						unit : {},
					},
				};

				$('#container-createOrEdit-modal').modal('show');
			},
			openContentEditForm(object) {
				// this.submitForm = true;
				this.createMode = false;
				this.formSubmitted = false;

				this.singleAssetData = object;

				$('#container-createOrEdit-modal').modal('show');
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
			storeContainer() {
				
				this.formSubmitted = true;
				
				axios
					.post('/containers/' + this.perPage, this.singleAssetData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("New container has been created", "Success");
							this.allFetchedContents = response.data;

							this.pagination.current_page = 1;
							this.query !== '' ? this.searchData() : this.showSelectedTabContents();
							
							$('#container-createOrEdit-modal').modal('hide');
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
			updateContainer() {
				
				this.formSubmitted = true;

				axios
					.put('/containers/' + this.singleAssetData.id + '/' + this.perPage, this.singleAssetData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Container has been updated", "Success");
							this.allFetchedContents = response.data;

							this.pagination.current_page = 1;
							this.query !== '' ? this.searchData() : this.showSelectedTabContents();
							
							$('#container-createOrEdit-modal').modal('hide');
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
					.delete('/containers/' + singleAssetData.id + '/' + this.perPage, singleAssetData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Container has been deleted", "Success");
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
					.patch('/containers/' + singleAssetData.id + '/' + this.perPage, singleAssetData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Container has been restored", "Success");
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
			}
            
		}
  	}

</script>

<style scoped>
	@import '~vue-multiselect/dist/vue-multiselect.min.css';

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