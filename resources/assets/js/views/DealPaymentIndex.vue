
<template v-if="userHasPermissionTo('view-merchant-payment-index')">

	<div class="pcoded-content">

		<breadcrumb 
			:title="'payment'" 
			:message="'All payments of ' + merchantName + ' for the deal ' + dealDate"
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
											  		v-if="userHasPermissionTo('view-merchant-payment-index') || userHasPermissionTo('create-merchant-payment')" 
											  		:query="query" 
											  		:caller-page="'payment'" 
											  		:required-permission="'merchant-payment'"
											  		
											  		@showContentCreateForm="showContentCreateForm" 
											  		@searchData="searchData($event)" 
											  		@fetchAllContents="fetchAllPayments"
											  	></search-and-addition-option>
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
																			Invoice
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
																			@click="changeIntegersOrder('previous_due')"
																		> 
																			Due
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
																			@click="changeIntegersOrder('total_rent')"
																		> 
																			Rent
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
																			@click="changeIntegersOrder('discount')"
																		> 
																			Discount
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
																			@click="changeIntegersOrder('net_payable')"
																		> 
																			Payable
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
																			@click="changeIntegersOrder('paid_amount')"
																		> 
																			Paid
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
																	
																	<th v-if="userHasPermissionTo('update-merchant-payment') || userHasPermissionTo('delete-merchant-payment')">
																		Actions
																	</th>
																</tr>
															</thead>

															<tbody>
																<tr 
																	v-for="(content, contentIndex) in contentsToShow" 
																	:key="'content-key-' + contentIndex + '-content-' + content.id" 
																>
																	<td>
																		{{ content.invoice_no | capitalize }}
																	</td>

																	<td>
																		{{ content.previous_due }}
																	</td>

																	<td>
																		{{ content.total_rent }}
																	</td>

																	<td>
																		{{ content.discount }}
																	</td>

																	<td>
																		{{ content.net_payable }}
																	</td>

																	<td>
																		{{ content.paid_amount }}
																	</td>
																	
																	<td>
																		<button type="button" 
																				class="btn btn-grd-info btn-icon"  
																				@click="showContentDetails(content)"
																		>
																			<i class="fa fa-eye"></i>
																		</button>
 																		

																		<button type="button" 
																				class="btn btn-grd-primary btn-icon" 
																				
																				@click="openContentEditForm(content)" 
																				v-if="userHasPermissionTo('update-merchant-payment')" 
																		>
																			<i class="fa fa-edit"></i>
																		</button>

																		<button type="button" 
																				class="btn btn-grd-danger btn-icon" 
																				
																				@click="openContentDeleteForm(content)" 
																				v-if="userHasPermissionTo('delete-merchant-payment')" 
																		>
																			<i class="fa fa-trash"></i>
																		</button>
																	</td>
																</tr>

																<tr 
															  		v-show="! contentsToShow.length"
															  	>
														    		<td colspan="6">
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
																			@click="changeIntegersOrder('previous_due')"
																		> 
																			Due
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
																			@click="changeIntegersOrder('total_rent')"
																		> 
																			Rent
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
																			@click="changeIntegersOrder('discount')"
																		> 
																			Discount
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
																			@click="changeIntegersOrder('net_payable')"
																		> 
																			Payable
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
																			@click="changeIntegersOrder('paid_amount')"
																		> 
																			Paid
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
																	
																	<th v-if="userHasPermissionTo('update-merchant-payment') || userHasPermissionTo('delete-merchant-payment')">
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
																@click="query === '' ? fetchAllPayments() : searchData()"
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
																@paginate="query === '' ? fetchAllPayments() : searchData()"
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

		<!-- 
			<asset-create-or-edit-modal 
				v-if="userHasPermissionTo('create-merchant-payment') || userHasPermissionTo('update-merchant-payment')" 
				:create-mode="createMode" 
				:caller-page="'storage type'" 
				:single-asset-data="singlePaymentData" 
				:csrf="csrf"

				@storeAsset="storeAsset($event)" 
				@updateAsset="updateAsset($event)" 
			></asset-create-or-edit-modal>
	 	-->

		<!-- View Modal -->
		<div class="modal fade" id="merchant-payment-view-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Deal ({{ dealDate }}) payment ({{ singlePaymentData.invoice_no }}) Details</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="modal-body">
						<div class="form-row">
							<label class="col-sm-6 col-form-label font-weight-bold text-right">
								Invoice No :
							</label>

							<label class="col-sm-6 col-form-label">
								{{ singlePaymentData.invoice_no | capitalize }}
							</label>
						</div>

						<div class="form-row">
							<label class="col-sm-6 col-form-label font-weight-bold text-right">
								Previous Due :
							</label>

							<label class="col-sm-6 col-form-label">
								{{ singlePaymentData.previous_due }}
							</label>
						</div>

						<div class="form-row">
							<label class="col-sm-6 col-form-label font-weight-bold text-right">
								Total Rent :
							</label>

							<label class="col-sm-6 col-form-label">
								{{ singlePaymentData.total_rent }}
							</label>
						</div>

						<div class="form-row">
							<label class="col-sm-6 col-form-label font-weight-bold text-right">
								Discount :
							</label>

							<label class="col-sm-6 col-form-label">
								{{ singlePaymentData.discount }} %
							</label>
						</div>

						<div class="form-row">
							<label class="col-sm-6 col-form-label font-weight-bold text-right">
								Net Payeble :
							</label>

							<label class="col-sm-6 col-form-label">
								{{ singlePaymentData.net_payable }}
							</label>
						</div>

						<div class="form-row">
							<label class="col-sm-6 col-form-label font-weight-bold text-right">
								Paid Amount :
							</label>

							<label class="col-sm-6 col-form-label">
								{{ singlePaymentData.paid_amount }}
							</label>
						</div>

						<div class="form-row">
							<label class="col-sm-6 col-form-label font-weight-bold text-right">
								Due :
							</label>

							<label class="col-sm-6 col-form-label">
								{{ singlePaymentData.current_due }}
							</label>
						</div>

						<div class="form-row">
							<label class="col-sm-6 col-form-label font-weight-bold text-right">
								Paid at :
							</label>

							<label class="col-sm-6 col-form-label">
								{{ singlePaymentData.paid_at }}
							</label>
						</div>

						<div class="form-row" v-if="singlePaymentData.hasOwnProperty('rents') && singlePaymentData.rents.length">
							<div class="col-md-6" v-for="(rent, rentIndex) in singlePaymentData.rents" :key="'rent-index-' + rentIndex + '-rent-' + rent.id">
								<div class="card card-body">
									<div class="form-row">
										<label class="col-sm-6 col-form-label font-weight-bold text-right">
											Space Name :
										</label>

										<label class="col-sm-6 col-form-label">
											{{ rent.dealt_space ? rent.dealt_space.name : 'NA' | capitalize }}
										</label>
									</div>

									<div class="form-row">
										<label class="col-sm-6 col-form-label font-weight-bold text-right">
											Issued From :
										</label>

										<label class="col-sm-6 col-form-label">
											{{ rent.issued_from }}
										</label>
									</div>

									<div class="form-row">
										<label class="col-sm-6 col-form-label font-weight-bold text-right">
											Expired At :
										</label>

										<label class="col-sm-6 col-form-label">
											{{ rent.expired_at }}
										</label>
									</div>

									<div class="form-row">
										<label class="col-sm-6 col-form-label font-weight-bold text-right">
											Rent :
										</label>

										<label class="col-sm-6 col-form-label">
											{{ rent.rent }}
										</label>
									</div>	
								</div>
							</div>
						</div>

						<div class="form-row" v-else>
							<div 
								class="col-md-12 text-center" 
							>
								<p class="text-danger">
									No Payment Found.
								</p>
							</div>
						</div>	
					</div>

					<div class="modal-footer">
						<button type="button" class="btn btn-secondary btn-sm btn-block" data-dismiss="modal">
							Close
						</button>
					</div>
				</div>
			</div>
		</div>

		<delete-confirmation-modal 
			v-if="userHasPermissionTo('delete-merchant-payment')" 
			:csrf="csrf" 
			:submit-method-name="'deleteAsset'" 
			:content-to-delete="singlePaymentData"
			:restoration-message="'But once you think, you can restore this item !'" 
			
			@deleteAsset="deleteAsset($event)" 
		></delete-confirmation-modal>

	</div>

</template>

<script type="text/javascript">

	import axios from 'axios';

    let singlePaymentData = {
    	
    	// previous_due : 0,
		// total_rent : 0, // generated from selected spaces
		// discount : 0,	// percentage 
		// net_payable : 0,
		// paid_amount : 0,
		// current_due : 0,
		// merchant_deal_id : null,
		// paid_at : null,
		rents : [
			{
				// issued_from : null,
				// expired_at : null,
				// rent : 0,
				// dealt_space_id : null,
				// merchant_payment_id : null
			},
		]

    };

	export default {

		props: {

			deal:{
				type: Object,
				required: true,
			},
			dealDate:{
				type: String,
				required: true,
			},
			merchantName:{
				type: String,
				required: true,
			},

		},

	    data() {

	        return {

	        	query : '',
	        	error : '',
    			perPage : 10,
	        	loading : false,
	        	// currentTab : 'current',

	        	ascending : false,
	      		descending : false,

	        	createMode : true,

	        	// allFetchedContents : [],
	        	contentsToShow : [],

	        	pagination: {
		        	current_page: 1
		      	},

	        	singlePaymentData : singlePaymentData,

	            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

	        }

		},
		
		created(){

			this.fetchAllPayments();			

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

			fetchAllPayments() {
				
				this.query = '';
				this.error = '';
				this.loading = true;
				this.contentsToShow = [];
				// this.allFetchedContents = [];
				
				axios
					.get('/api/deal-payments/' + this.deal.id + '/' + this.perPage + "?page=" + this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							// this.allFetchedContents = response.data;
							this.setContentPagination(response);
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
				this.contentsToShow = [];
				// this.allFetchedContents = [];
				this.pagination.current_page = 1;
				
				axios
				.get(
					"/api/search-deal-payments/" + this.deal.id + '/' + this.query + "/" + this.perPage + "?page=" + this.pagination.current_page
				)
				.then(response => {
					// this.allFetchedContents = response.data;
					this.contentsToShow = response.data.all.data;
					this.pagination = response.data.all;
				})
				.catch(e => {
					this.error = e.toString();
				});

			},
			showContentDetails(object) {	
				this.singlePaymentData = object;
				$('#merchant-payment-view-modal').modal('show');
			},
			showContentCreateForm() {

				this.createMode = true;

				this.singlePaymentData = {
					// previous_due : 0,
					// total_rent : 0, // generated from selected spaces
					// discount : 0,	// percentage 
					// net_payable : 0,
					// paid_amount : 0,
					// current_due : 0,
					// merchant_deal_id : null,
					// paid_at : null,
					rents : [
						{
							// issued_from : null,
							// expired_at : null,
							// rent : 0,
							// dealt_space_id : null,
							// merchant_payment_id : null
						},
					]
				};

				$('#merchant-payment-createOrEdit-modal').modal('show');

			},
			openContentEditForm(object) {
				this.createMode = false;
				this.singlePaymentData = object;
				$('#merchant-payment-createOrEdit-modal').modal('show');
			},
			openContentDeleteForm(object) {	
				this.singlePaymentData = object;
				$('#delete-confirmation-modal').modal('show');
			},
			storeAsset(singlePaymentData) {
				
				axios
					.post('/deal-payments/' + this.perPage, singlePaymentData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("New storage type has been created", "Success");
							// this.allFetchedContents = response.data;
							this.query !== '' ? this.searchData() : this.setContentPagination(response);
							$('#merchant-payment-createOrEdit-modal').modal('hide');
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
			updateAsset(singlePaymentData) {
				
				axios
					.put('/deal-payments/' + singlePaymentData.id + '/' + this.perPage, singlePaymentData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Storage type has been updated", "Success");
							// this.allFetchedContents = response.data;
							this.query !== '' ? this.searchData() : this.setContentPagination(response);
							$('#merchant-payment-createOrEdit-modal').modal('hide');
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
			deleteAsset(singlePaymentData) {
				
				axios
					.delete('/deal-payments/' + singlePaymentData.id + '/' + this.perPage)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Storage type has been deleted", "Success");
							// this.allFetchedContents = response.data;
							this.query !== '' ? this.searchData() : this.setContentPagination(response);
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
            changeNumberContents() {
				this.pagination.current_page = 1;
				// this.perPage = expectedContentsPerPage;

				if (this.query === '') {
					this.fetchAllPayments();
				}
				else {
					this.searchData();
				}
    		},
    		setContentPagination(response) {

    			this.contentsToShow = response.data.data;
				this.pagination = response.data;

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
			changeIntegersOrder(columnName) {

				if (this.ascending) {

					this.ascending = false;
					this.descending = true;

					this.descendingIntegers(columnName);

				}
				else if (this.descending) {

					this.ascending = true;
					this.descending = false;

					this.ascendingIntegers(columnName);

				}
				else {

					this.ascending = true;
					this.descending = false;

					this.ascendingAlphabets('name');

				}
				
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
			ascendingIntegers(columnValue) {

				this.contentsToShow.sort(
			 		function(a, b){
						return a[columnValue] - b[columnValue];
					}
				);

			},
			descendingIntegers(columnValue) {

				this.contentsToShow.sort(
			 		function(a, b){
						return b[columnValue] - a[columnValue];
					}
				);

			},
            
		}
  	}

</script>