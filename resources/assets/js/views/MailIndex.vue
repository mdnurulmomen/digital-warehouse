
<template v-if="userHasPermissionTo('view-mail-index')">
	<div class="pcoded-content">
		<breadcrumb 
			:icon="'mails'"
			:title="'mails'" 
			:message="'All mails sent from panel'"
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
											  		v-if="userHasPermissionTo('view-mail-index') || userHasPermissionTo('create-mail')" 
											  		:query="query" 
											  		:caller-page="'mail'" 
											  		:disable-add-button="formSubmitted" 
											  		:required-permission="'mail'" 
											  		
											  		@showContentCreateForm="showContentCreateForm" 
											  		@searchData="pagination.current_page = 1; searchData($event)" 
											  		@fetchAllContents="pagination.current_page = 1; fetchAllContents()"
											  	></search-and-addition-option>
											</div>
											
											<div class="col-sm-12 col-lg-12">
										  		<loading v-show="loading"></loading>

										  		<tab 
										  			v-show="query === '' && ! loading" 
										  			:tab-names="['manual', 'automated', 'failed']" 
										  			:current-tab="currentTab" 

										  			@showManualContents="showManualContents" 
										  			@showAutomatedContents="showAutomatedContents" 
										  			@showFailedContents="showFailedContents" 
										  		></tab> 
										  		 
										  		<table-with-delete-option 
										  			:query="query" 
										  			:per-page="perPage"  
										  			:loading="loading"  
										  			:column-names="['subject', 'body', 'sender']" 
										  			:column-values-to-show="['subject', 'body', 'sender']" 
										  			:contents-to-show = "contentsToShow" 
										  			:pagination = "pagination" 
										  			:required-permission = "'mail'" 
										  			:form-submitted="formSubmitted" 
										  			:current-content="singleAssetData"

										  			@showContentDetails="openContentViewForm($event)" 
										  			@openContentDeleteForm="openContentDeleteForm($event)" 
										  			@changeNumberContents="changeNumberContents($event)" 
										  			@fetchAllContents="fetchAllContents" 
										  			@searchData="searchData" 
										  		>	
										  		</table-with-delete-option>
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

		<mail-create-modal 
			v-if="userHasPermissionTo('create-mail')" 
			:csrf="csrf" 
			:create-mode="createMode" 
			:caller-page="'mail'" 
			:form-submitted="formSubmitted"
			:single-asset-data="singleAssetData" 

			@storeAsset="storeAsset($event)" 
		></mail-create-modal>

		<delete-confirmation-modal 
			v-if="userHasPermissionTo('delete-mail')" 
			:csrf="csrf" 
			:form-submitted="formSubmitted"
			:submit-method-name="'deleteAsset'" 
			:content-to-delete="singleAssetData"
			:restoration-message="'Once you delete, you can not restore this item !'" 
			
			@deleteAsset="deleteAsset($event)" 
		></delete-confirmation-modal>
		 
		<mail-view-modal 
			:caller-page="'mail'" 
			:asset-to-view="singleAssetData" 
			:properties-to-show="['subject', 'body', 'sender', 'recipients']"
		></mail-view-modal>
	</div>
</template>

<script type="text/javascript">

	import MailViewModal from '../components/MailViewModal.vue';
	import MailCreateModal from '../components/MailCreateModal.vue';

    let singleAssetData = {
    	recipients : [],
    	subject : '',
    	body : '',
    	sender : {},
    };

	export default {

	    components : {
	    
	    	MailViewModal,
	    	MailCreateModal,
	    
	    },

	    data() {

	        return {

	        	query : '',
	        	error : '',
    			perPage : 10,
	        	loading : false,
	        	currentTab : 'manual',

	        	// ascending : false,
	      		// descending : false,

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
					.get('/api/mails/' + this.perPage + "?page=" + this.pagination.current_page)
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
					"/api/search-mails/" + this.query + "/" + this.perPage + "?page=" + this.pagination.current_page
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
			openContentViewForm(object) {	
				this.singleAssetData = object;
				$('#mail-view-modal').modal('show');
			},
			showContentCreateForm() {
				this.createMode = true;
				this.formSubmitted = false;
				this.singleAssetData = {
					recipients : [],
			    	subject : '',
    				body : '',
			    	sender : {},
				};
				$('#mail-create-modal').modal('show');
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
					.post('/mails/' + this.perPage, singleAssetData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("New mail has been created", "Success");
							this.allFetchedContents = response.data;

							this.pagination.current_page = 1;
							this.query !== '' ? this.searchData() : this.showSelectedTabContents();

							$('#mail-create-modal').modal('hide');
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
					.delete('/mails/' + this.singleAssetData.id + '/' + this.perPage)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Mail has been deleted", "Success");
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
				
				if (this.currentTab=='manual') {
					this.contentsToShow = this.allFetchedContents.manual ? this.allFetchedContents.manual.data : [];
					this.pagination = this.allFetchedContents.manual;
				}
				else if (this.currentTab=='automated') {
					this.contentsToShow = this.allFetchedContents.automated ? this.allFetchedContents.automated.data : [];
					this.pagination = this.allFetchedContents.automated;
				}
				else {
					this.contentsToShow = this.allFetchedContents.failed ? this.allFetchedContents.failed.data : [];
					this.pagination = this.allFetchedContents.failed;
				}

			},
			showManualContents() {
				this.currentTab = 'manual';
				this.showSelectedTabContents();
			},
			showAutomatedContents() {
				this.currentTab = 'automated';
				this.showSelectedTabContents();
			},
			showFailedContents() {
				this.currentTab = 'failed';
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
			*/
            
		}
  	}

</script>