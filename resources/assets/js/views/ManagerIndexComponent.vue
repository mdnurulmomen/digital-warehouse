
<template>

	<div class="pcoded-content">

		<breadcrumb 
			:title="'managers'" 
			:message="'All our warhouse managers'"
		></breadcrumb>			

		<div class="pcoded-inner-content">
			<div class="main-body">
				<div class="page-wrapper">	
					<div class="page-body">

						<loading v-show="loading"></loading>
				
					  	<div class="row" v-show="!loading">
							<div class="col-sm-12">
							  	<div class="card">
									<div class="card-block">
										<div class="row">											

											<div class="col-sm-12 sub-title">
											  	<search-and-addition-option 
											  		:query="query" 
											  		:caller-page="'manager'" 
											  		
											  		@showContentCreateForm="showContentCreateForm" 
											  		@searchData="searchData($event)" 
											  		@fetchAllContents="fetchAllContents"
											  	></search-and-addition-option>
											</div>
											
											<div class="col-sm-12 col-lg-12">

										  		<tab 
										  			v-show="query === ''" 
										  			:tab-names="['approved', 'pending', 'trashed']" 
										  			:current-tab="currentTab" 

										  			@showApprovedContents="showApprovedContents" 
										  			@showPendingContents="showPendingContents" 
										  			@showTrashedContents="showTrashedContents" 
										  		></tab>

										  		<table-with-soft-delete-option 
										  			:query="query" 
										  			:per-page="perPage"  
										  			:column-names="['name', 'username', 'email', 'mobile', 'status']" 
										  			:column-values-to-show="['full_name', 'user_name', 'email', 'mobile', 'status']" 
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

		<user-profile-create-or-edit-modal 
			:create-mode="createMode" 
			:user="'manager'" 
			:single-user-details="singleUserDetails" 
			:csrf="csrf"

			@storeUser="storeUser($event)" 
			@updateUser="updateUser($event)" 
		></user-profile-create-or-edit-modal>

		<delete-confirmation 
			:csrf="csrf" 
			:submit-method-name="'deleteUser'" 
			:content-to-delete="singleUserDetails"
			:restoration-message="'But once you think, you can restore this item !'" 
			
			@deleteUser="deleteUser($event)" 
		></delete-confirmation>

		<restore-confirmation 
			:csrf="csrf" 
			:submit-method-name="'restoreUser'" 
			:content-to-restore="singleUserDetails"
			:restoration-message="'This will restore all related items !'" 
			
			@restoreUser="restoreUser($event)" 
		></restore-confirmation>

		<user-profile-view-modal 
			:user="'manager'" 
			:profile-to-view="singleUserDetails" 
			:properties-to-show="['first Name', 'last Name', 'username', 'email', 'mobile', 'status', 'registered at']"
		></user-profile-view-modal>

	</div>

</template>

<script type="text/javascript">

	import axios from 'axios';

    let singleUserDetails = {
    	active : false,
    	profile_preview : {}
    };

	export default {

	    data() {

	        return {

	        	query : '',
    			perPage : 10,
	        	loading : false,
	        	currentTab : 'approved',

	        	createMode : true,

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
				this.loading = true;
				
				axios
					.get('/api/managers/' + this.perPage + "?page=" + this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							this.allFetchedContents = response.data;
							this.showSelectedTabContents();
						}
					})
					.catch(error => {
						console.log(error);
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
					.then(response => {
						this.loading = false;
					});

			},
			searchData(emitedValue=false) {

				if (emitedValue) {
					this.query=emitedValue;
				}

				this.pagination.current_page = 1;
				
				axios
				.get(
					"/api/search-managers/" + this.query + "/" + this.perPage + "?page=" + this.pagination.current_page
				)
				.then(response => {
					this.allFetchedContents = response.data;
					this.contentsToShow = this.allFetchedContents.all.data;
					this.pagination = response.data.all;
				})
				.catch(e => {
					console.log(e);
				});

			},
			showContentDetails(object) {	
				this.singleUserDetails = object;
				$('#user-profile-view-modal').modal('show');
			},
			showContentCreateForm() {
				this.createMode = true;
				this.singleUserDetails = {
					active : false,
					profile_preview : {}
				};
				$('#user-createOrEdit-modal').modal('show');
			},
			openContentEditForm(object) {
				this.createMode = false;
				this.singleUserDetails = object;
				$('#user-createOrEdit-modal').modal('show');
			},
			openContentDeleteForm(object) {	
				this.singleUserDetails = object;
				$('#delete-confirmation-modal').modal('show');
			},
			openContentRestoreForm(object) {	
				this.singleUserDetails = object;
				$('#restore-confirmation-modal').modal('show');
			},
			storeUser(singleUserDetails) {
				
				axios
					.post('/managers/' + this.perPage, singleUserDetails)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("New manager has been created", "Success");
							this.allFetchedContents = response.data;
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
					});

			},
			updateUser(singleUserDetails) {
				
				axios
					.put('/managers/' + singleUserDetails.id + '/' + this.perPage, singleUserDetails)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Manager has been updated", "Success");
							this.allFetchedContents = response.data;
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
					});

			},
			deleteUser(singleUserDetails) {
				
				axios
					.delete('/managers/' + singleUserDetails.id + '/' + this.perPage, singleUserDetails)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Manager has been deleted", "Success");
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
			restoreUser(singleUserDetails) {
				
				axios
					.patch('/managers/' + singleUserDetails.id + '/' + this.perPage, singleUserDetails)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Manager has been restored", "Success");
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
				
				if (this.currentTab=='approved') {
					this.contentsToShow = this.allFetchedContents.approved.data;
					this.pagination = this.allFetchedContents.approved;
				}
				else if (this.currentTab=='pending') {
					this.contentsToShow = this.allFetchedContents.pending.data;
					this.pagination = this.allFetchedContents.pending;
				}
				else {
					this.contentsToShow = this.allFetchedContents.trashed.data;
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