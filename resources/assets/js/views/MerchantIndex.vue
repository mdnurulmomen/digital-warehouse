
<template v-if="userHasPermissionTo('view-merchant-index')">

	<div class="pcoded-content">
		<breadcrumb 
			:icon="'merchants'"
			:title="'merchants'" 
			:message="'All our warehouse-merchants'"
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
											  		v-if="userHasPermissionTo('view-merchant-index') || userHasPermissionTo('create-merchant')" 
											  		:query="query" 
											  		:caller-page="'merchant'" 
											  		:required-permission = "'merchant'" 
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
										  			:tab-names="['approved', 'pending', 'trashed']" 
										  			:current-tab="currentTab" 

										  			@showApprovedContents="showApprovedContents" 
										  			@showPendingContents="showPendingContents" 
										  			@showTrashedContents="showTrashedContents" 
										  		></tab>

										  		<table-with-soft-delete-option 
										  			:query="query" 
										  			:loading="loading" 
										  			:per-page="perPage" 
										  			:column-names="['username', 'mobile', '# deals', '# products']" 
										  			:column-values-to-show="['user_name', 'mobile', 'number_space_deals', 'number_products']" 
										  			:contents-to-show = "contentsToShow" 
										  			:pagination = "pagination" 
										  			:required-permission = "'merchant'" 
										  			:form-submitted="formSubmitted" 
										  			:current-content="singleUserDetails"

										  			@showContentDetails="showContentDetails($event)" 
										  			@openContentEditForm="openContentEditForm($event)" 
										  			@openContentDeleteForm="openContentDeleteForm($event)" 
										  			@openContentRestoreForm="openContentRestoreForm($event)" 
										  			@goMerchantDeals="goMerchantDeals($event)" 
										  			@goMerchantProducts="goMerchantProducts($event)" 
										  			@goToSupportDealRents="goToSupportDealRents($event)" 
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

		<user-profile-create-or-edit-modal 
			v-if="userHasPermissionTo('create-merchant') || userHasPermissionTo('update-merchant')" 
			:csrf="csrf" 
			:create-mode="createMode" 
			:user="'merchant'" 
			:form-submitted="formSubmitted" 
			:single-user-details="singleUserDetails" 
			@storeUser="storeUser($event)" 
			@updateUser="updateUser($event)" 
		></user-profile-create-or-edit-modal>

		<delete-confirmation-modal 
			v-if="userHasPermissionTo('delete-merchant')" 
			:csrf="csrf" 
			:form-submitted="formSubmitted"
			:submit-method-name="'deleteUser'" 
			:content-to-delete="singleUserDetails"
			:restoration-message="'But once you think, you can restore this item !'" 
			@deleteUser="deleteUser($event)" 
		></delete-confirmation-modal>

		<restore-confirmation-modal 
			v-if="userHasPermissionTo('delete-merchant')" 
			:csrf="csrf" 
			:form-submitted="formSubmitted"
			:submit-method-name="'restoreUser'" 
			:content-to-restore="singleUserDetails"
			:restoration-message="'This will restore all related items !'" 
			@restoreUser="restoreUser($event)" 
		></restore-confirmation-modal>

		<user-profile-view-modal 
			:user="'merchant'" 
			:profile-to-view="singleUserDetails" 
			:properties-to-show="['first Name', 'last Name', 'username', 'email', 'mobile', 'status', 'registered at', '# deals', '# products']"
		></user-profile-view-modal>
	</div>
</template>

<script type="text/javascript">

    let singleUserDetails = {
    	active : false,
    	profile_preview : {},
    	support_deal : {}
  		// roles : [],
		// permissions : [],
    };

	export default {

	    data() {

	        return {

	        	query : '',
	        	error : '',
    			perPage : 10,
	        	loading : false,
	        	currentTab : 'approved',

	        	createMode : true,
	        	formSubmitted : false,

	        	allFetchedContents : [],
	        	contentsToShow : [],

	        	pagination: {
		        	current_page: 1
		      	},

	        	singleUserDetails : singleUserDetails,

	            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

	        }

		},
		
		created(){

			this.fetchAllContents();

		},
		
		methods : {

			fetchAllContents() {
				
				this.query = '';
				this.error = '';
				this.loading = true;
				this.allFetchedContents = [];
				
				axios
					.get('/api/merchants/' + this.perPage + "?page=" + this.pagination.current_page)
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
					"/api/search-merchants/" + this.query + "/" + this.perPage + "?page=" + this.pagination.current_page
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
			goMerchantDeals(object) {

				// console.log(object);
				this.$router.push({ name: 'space-deals', params: { merchant: object, merchantId:object.id }});

			},
			goMerchantProducts(object) {

				// console.log(object);
				this.$router.push({ name: 'merchant-products', params: { merchant: object, merchantId:object.id }});

			},
			goToSupportDealRents(object) {

				// console.log(object);
				this.$router.push({ name: 'support-deal-rents', params: { merchantName:object.user_name.replace(/ /g,"-"), deal:object.support_deal, dealId:object.support_deal.id }});

			},
			showContentDetails(object) {	
				this.singleUserDetails = object;
				$('#user-profile-view-modal').modal('show');
			},
			showContentCreateForm() {
				this.createMode = true;
				this.formSubmitted = false;	
				this.singleUserDetails = {
					active : false,
					profile_preview : {},
					support_deal : {}
					// roles : [],
					// permissions : [],
				};
				$('#user-createOrEdit-modal').modal('show');
			},
			openContentEditForm(object) {
				this.createMode = false;
				this.formSubmitted = false;	
				this.singleUserDetails = object;
				$('#user-createOrEdit-modal').modal('show');
			},
			openContentDeleteForm(object) {	
				this.formSubmitted = false;	
				this.singleUserDetails = object;
				$('#delete-confirmation-modal').modal('show');
			},
			openContentRestoreForm(object) { 
				this.formSubmitted = false;	
				this.singleUserDetails = object;
				$('#restore-confirmation-modal').modal('show');
			},
			storeUser(singleUserDetails) {
				
				this.formSubmitted = true;	

				axios
					.post('/merchants/' + this.perPage, singleUserDetails)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("New merchant has been created", "Success");
							
							this.allFetchedContents = response.data;
							this.pagination.current_page = 1; 
							this.query !== '' ? this.searchData() : this.showSelectedTabContents();
							
							$('#user-createOrEdit-modal').modal('hide');
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
			updateUser(singleUserDetails) {
				
				this.formSubmitted = true;	

				axios
					.put('/merchants/' + singleUserDetails.id + '/' + this.perPage, singleUserDetails)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("merchant has been updated", "Success");

							this.allFetchedContents = response.data;
							this.pagination.current_page = 1;
							this.query !== '' ? this.searchData() : this.showSelectedTabContents();

							$('#user-createOrEdit-modal').modal('hide');
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
			deleteUser(singleUserDetails) {
				
				this.formSubmitted = true;	

				axios
					.delete('/merchants/' + singleUserDetails.id + '/' + this.perPage, singleUserDetails)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("merchant has been deleted", "Success");

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
			restoreUser(singleUserDetails) {
				
				this.formSubmitted = true;	

				axios
					.patch('/merchants/' + singleUserDetails.id + '/' + this.perPage, singleUserDetails)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("merchant has been restored", "Success");

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
				
				if (this.currentTab=='approved') {
					this.contentsToShow = this.allFetchedContents.approved ? this.allFetchedContents.approved.data : [];
					this.pagination = this.allFetchedContents.approved;
				}
				else if (this.currentTab=='pending') {
					this.contentsToShow = this.allFetchedContents.pending ? this.allFetchedContents.pending.data : [];
					this.pagination = this.allFetchedContents.pending;
				}
				else {
					this.contentsToShow = this.allFetchedContents.trashed ? this.allFetchedContents.trashed.data : [];
					this.pagination = this.allFetchedContents.trashed;
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
			showTrashedContents() {
				this.currentTab = 'trashed';
				this.showSelectedTabContents();
			},
            
		}
  	}

</script>