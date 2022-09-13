
<template v-if="userHasPermissionTo('view-merchant-payment-index')">
	<div class="pcoded-content">
		<breadcrumb 
			:icon="'deal-rents'"
			:title="'rent'" 
			:message="('All rents of ' + merchantName + ' for the deal ' + (deal.name ? deal.name : ''))"
		></breadcrumb>			

		<div class="pcoded-inner-content">
			<div class="main-body">
				<div class="page-wrapper">	
					<div class="page-body">

						<!-- <loading v-show="loading"></loading> -->

						<alert v-show="error" :error="error"></alert>
				
					  	<div class="row">
							<div class="col-sm-12">
							  	<div class="card">
									<div class="card-block">
										<div class="row">
											<div class="col-sm-12">
											  	<!-- 
											  	<search-and-addition-option 
											  		v-if="userHasPermissionTo('create-merchant-payment')" 
											  		:query="query" 
											  		:caller-page="'payment'" 
											  		:required-permission="'merchant-payment'"
											  		
											  		@showContentCreateForm="showContentCreateForm" 
											  		@searchData="searchData($event)" 
											  		@fetchAllContents="setDealAllPayments()"
											  	></search-and-addition-option> 
											  	-->

											  	<div class="row form-group">
											  		<div class="col-md-4 col-sm-6 d-flex align-items-center form-group">
											  			<div class="mr-2">
											  				<span>
													  			{{ 
													  				( /* searchAttributes.showPendingRequisitions || searchAttributes.showCancelledRequisitions || searchAttributes.showDispatchedRequisitions || searchAttributes.showProduct || */ searchAttributes.search || searchAttributes.dateFrom || searchAttributes.dateTo) ? 'Searched Rent List' : 'Rent List'
													  			}}
											  				</span>
											  			</div>

											  			<div class="dropdown">
									  						<i class="fa fa-download fa-lg dropdown-toggle" data-toggle="dropdown" v-tooltip.bottom-end="'Download Rents'"></i>
										  					
										  					<div class="dropdown-menu">
									  							<download-excel 
													  				class="btn waves-effect waves-dark btn-default btn-outline-default p-1 dropdown-item active"
																	:data="dealAllRents"
																	:fields="dataToExport" 
																	worksheet="Rents sheet"
																	:name="((searchAttributes.search != '' || searchAttributes.dateFrom || searchAttributes.dateTo) ? 'searched-rents-' : ('rents-list-')) + today + '-page-' + pagination.current_page + '.xls'"
													  			>
													  				Excel
																</download-excel>
										  						
										  						<!-- 
										  						<download-excel 
										  							type="csv"
													  				class="btn waves-effect waves-dark btn-default btn-outline-default p-1 dropdown-item disabled"
																	:data="dealAllRents"
																	:fields="dataToExport" 
																	worksheet="Payments sheet"
																	:name="((searchAttributes.search != '' || searchAttributes.dateFrom || searchAttributes.dateTo) ? 'searched-payments-' : (currentTab + '-payments-list-')) + today + '-page-' + pagination.current_page + '.xls'"
													  			>
													  				CSV
																</download-excel> 
																-->
										  					</div>
										  				</div>

											  			<div class="ml-auto d-sm-none">
											  				<button 
											  					v-if="userHasPermissionTo('create-merchant-payment')"
													  			class="btn waves-effect waves-dark btn-success btn-outline-success btn-outline-success btn-sm" 
													  			v-tooltip.bottom-end="'Create New'" 
													  			@click="showContentCreateForm()"
												  			>
												  				<i class="fa fa-plus"></i>
												  				Next Rent
												  			</button>
											  			</div>
											  		</div>

											  		<div class="col-md-4 col-sm-6 was-validated text-center d-flex align-items-center form-group">
											  			<div class="mx-sm-auto w-75">
										  					<input 	
																type="text" 
														  		class="form-control" 
														  		pattern="[^'!#$%^()\x22]+" 
														  		v-model="searchAttributes.search" 
														  		placeholder="Search Rents"
													  		>

													  		<div class="invalid-feedback">
														  		Please search with releavant input
														  	</div>
											  			</div>

														<div class="ml-auto ml-sm-0">
															<ul class="nav nav-pills">
																<li class="nav-item">
																	<a 
																		href="javascript:void(0)"
																		class="nav-link p-1" 
																		@click="setTodayDate()" 
																		:class="{ 'active': searchAttributes.dateFrom == today && ! searchAttributes.dateTo }"
																	>
																		Today
																	</a>
																</li>

																<li class="nav-item">
																	<a 
																		href="javascript:void(0)"
																		class="nav-link p-0" 
																		data-toggle="modal" 
																		data-target="#rent-custom-search"
																		:class="{ 'active': Object.keys(searchAttributes.dates).length > 0 }"
																	>
																		<i class="fa fa-ellipsis-v fa-lg p-2"></i>
																	</a>
																</li>
															</ul>
													  	</div>
													</div>

													<div class="col-md-4 text-right d-none d-md-block">
														<button 
										  					v-if="userHasPermissionTo('create-merchant-payment')"
												  			class="btn waves-effect waves-dark btn-success btn-outline-success btn-outline-success btn-sm" 
												  			v-tooltip.bottom-end="'Create New'" 
												  			@click="showContentCreateForm()"
											  			>
											  				<i class="fa fa-plus"></i>
											  				Next Rent
											  			</button>
													</div>
											  	</div>
											</div>
											
											<div class="col-sm-12 col-lg-12">
												<loading v-show="loading"></loading>	

										  		<div v-show="!loading">
											  		<div class="table-responsive">
														<table class="table table-striped table-bordered nowrap text-center">
															<thead>
																<tr>
																	<th>
																		<a 
																			href="javascript:void(0)" 
																			@click="changeIntegersOrder('rents')"
																		> 
																			# Rents
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

																	<!-- 
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
																	-->

																	<th>
																		<a 
																			href="javascript:void(0)" 
																			@click="changeIntegersOrder('total_rent')"
																		> 
																			Amount
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
																			@click="changeIntegersOrder('total_paid_amount')"
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
																	v-for="(content, contentIndex) in dealAllRents" 
																	:key="'content-key-' + contentIndex + '-content-' + content.id" 
																	:class="content.id==singleRentData.id ? 'highlighted' : ''"
																>
																	<td>
																		{{ content.number_installment || 0 }}
																	</td>

																	<!-- 
																	<td>
																		{{ content.previous_due }}
																	</td> 
																	-->

																	<td>
																		{{ content.total_rent }}
																		{{ general_settings.official_currency_name | capitalize }}
																	</td>

																	<td>
																		{{ content.discount }}%
																	</td>

																	<td>
																		{{ content.net_payable }}
																		{{ general_settings.official_currency_name | capitalize }}
																	</td>

																	<td>
																		{{ content.total_paid_amount || 0 }}
																		{{ general_settings.official_currency_name | capitalize }}
																	</td>
																	
																	<td>
																		<button type="button" 
																				class="btn waves-effect waves-dark btn-info btn-outline-info btn-icon" 
																				v-tooltip.bottom-end="'View Details'"  
																				@click="showContentDetails(content)"
																		>
																			<i class="fa fa-eye"></i>
																		</button>
 																		

																		<button type="button" 
																				class="btn waves-effect waves-dark btn-primary btn-outline-primary btn-icon" 
																				v-tooltip.bottom-end="'Edit'" 
																				@click="openContentEditForm(content)" 
																				v-if="userHasPermissionTo('update-merchant-payment')" 
																		>
																			<i class="fa fa-edit"></i>
																		</button>

																		<button type="button" 
																				class="btn waves-effect waves-dark btn-danger btn-outline-danger btn-icon" 
																				v-tooltip.bottom-end="'Delete'" 
																				@click="openContentDeleteForm(content)" 
																				v-if="userHasPermissionTo('delete-merchant-payment')" 
																		>
																			<i class="fa fa-trash"></i>
																		</button>

																		<button type="button" 
																				class="btn waves-effect waves-dark btn-success btn-outline-success btn-icon" 
																				v-tooltip.bottom-end="'Partial Payments'" 
																				@click="goToPartialPayments(content)"  
																		>
																			<i class="fa fa-money" aria-hidden="true"></i>
																		</button>
																	</td>
																</tr>

																<tr 
															  		v-show="! dealAllRents || ! dealAllRents.length"
															  	>
														    		<td colspan="7">
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

																	<!-- 
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
																	-->

																	<th>
																		<a 
																			href="javascript:void(0)" 
																			@click="changeIntegersOrder('total_rent')"
																		> 
																			Amount
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
																			@click="changeIntegersOrder('total_paid_amount')"
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
																class="btn waves-effect waves-dark btn-primary btn-outline-primary btn-sm" 
																v-tooltip.bottom-end="'Reload'" 
																@click="pagination.current_page = 1; searchAttributes.search === '' ? fetchAllPayments() : searchData()"
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
																@paginate="searchAttributes.search === '' ? fetchAllPayments() : searchData()"
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

	 	<!--Create, Edit or Approve Modal -->
		<div class="modal fade" id="merchant-rent-createOrEdit-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" v-if="userHasPermissionTo('create-merchant-payment') || userHasPermissionTo('update-merchant-payment')">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">
							{{ createMode ? 'Make New ' : 'Update Rent ' }} {{ createMode ? 'Rent' : singleRentData.name | capitalize }} 
						</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
						
					<form 	
						class="form-horizontal" 
						v-on:submit.prevent="createMode ? createDealRent() : updateDealRent()" 
						autocomplete="off" 
						novalidate="true" 
					>
						<input type="hidden" name="_token" :value="csrf">

						<div class="modal-body">
							<transition-group name="fade">	
								<div 
									class="row" 
									v-bind:key="'merchant-deal-modal-step-' + 1" 
									v-show="! loading && step==1" 
								>
									<h2 class="mx-auto mb-4 lead">Dealt Supports</h2>

									<div class="col-md-12">
										<div class="card">
											<div class="card-block">
												<div class="form-row"> 
												    <label class="col-6 col-form-label font-weight-bold text-right">
														Support Deal ID :
													</label>
													<label class="col-6 col-form-label">
														{{ deal.name | capitalize }}
													</label>
												</div>

												<div class="form-row"> 
												    <label class="col-6 col-form-label font-weight-bold text-right">
														<label class="font-weight-bold">E Commerce Fulfillment Support :</label>
													</label>
													<div class="col-6 col-form-label">
														<span :class="[deal.e_commerce_fulfillment_support ? 'badge-success' : 'badge-danger', 'badge']">
															
															{{ deal.e_commerce_fulfillment_support ? 'Enabled' : 'Disabled' }}
															
														</span>
													</div>
												</div>

												<div 
													class="form-row" 
													v-show="deal.e_commerce_fulfillment_support"
												> 
												    <label class="col-6 col-form-label font-weight-bold text-right">
														Fulfillment Support Charge :
													</label>
													<label class="col-6 col-form-label">
														{{ deal.e_commerce_fulfillment_charge }}
														{{ general_settings.official_currency_name || 'BDT' | capitalize }}
													</label>
												</div>

												<div class="form-row"> 
												    <label class="col-6 col-form-label font-weight-bold text-right">
														Purchase Support :
													</label>
													<div class="col-6 col-form-label">
														<span :class="[deal.purchase_support ? 'badge-success' : 'badge-danger', 'badge']">
															
															{{ deal.purchase_support ? 'Enabled' : 'Disabled' }}
															
														</span>
													</div>
												</div>

												<div 
													class="form-row" 
													v-show="deal.purchase_support"
												> 
												    <label class="col-6 col-form-label font-weight-bold text-right">
														Purchase Support Charge :
													</label>
													<label class="col-6 col-form-label">
														{{ deal.purchase_support_charge }}
														{{ general_settings.official_currency_name || 'BDT' | capitalize }}
													</label>
												</div>

												<div class="form-row"> 
												    <label class="col-6 col-form-label font-weight-bold text-right">
														POS Support :
													</label>
													<div class="col-6 col-form-label">
														<span :class="[deal.pos_support ? 'badge-success' : 'badge-danger', 'badge']">
															
															{{ deal.pos_support ? 'Enabled' : 'Disabled' }}
															
														</span>
													</div>
												</div>

												<div 
													class="form-row" 
													v-show="deal.pos_support"
												> 
												    <label class="col-6 col-form-label font-weight-bold text-right">
														POS Charge :
													</label>
													<label class="col-6 col-form-label">
														{{ deal.pos_support_charge }}
														{{ general_settings.official_currency_name || 'BDT' | capitalize }}
													</label>
												</div>

												<div 
													class="form-row" 
													v-show="deal.pos_support"
												> 
												    <label class="col-6 col-form-label font-weight-bold text-right">
														# Outlets :
													</label>
													<label class="col-6 col-form-label">
														{{ deal.number_outlets }}
													</label>
												</div>
											</div>
										</div>
									</div>

									<div class="col-sm-12 form-group mb-0 card-footer">
										<div class="row">
											<div class="col-sm-12 text-right" v-show="!submitForm">
												<span class="text-danger small">
											  		Please input required fields
											  	</span>
											</div>
											<div class="col-sm-12">
							                  	<button type="button" class="btn waves-effect waves-dark btn-secondary btn-outline-secondary" data-toggle="tooltip" data-placement="top" title="Close Modal" data-dismiss="modal">Close</button>

												<button type="button" class="btn waves-effect waves-dark btn-secondary btn-outline-secondary btn-sm btn-round float-right" data-toggle="tooltip" data-placement="top" title="Next" v-on:click="nextPage">
							                    	<i class="fa fa-2x fa-angle-double-right" aria-hidden="true"></i>
							                  	</button>
											</div>
										</div>
						          	</div>
								</div>

								<div 
									class="row" 
									v-bind:key="'merchant-deal-modal-step-' + 2" 
									v-show="!loading && step==2" 
								>
									<!-- payment -->
									<div class="col-md-12">
										<div class="card">
											<div class="card-block">
												<div class="form-row form-group">
													<label class="col-sm-6 col-form-label font-weight-bold text-right">
														Rent Period :
													</label>
													<label class="col-sm-6 col-form-label">
														{{ deal.rent_period ? deal.rent_period.name : '--' | capitalize }}
													</label>
												</div>

												<div class="form-row">
													<div class="form-group col-md-6">
														<label for="inputFirstName">Current Rent / Rent</label>
														<div class="input-group">
															<input 
																type="text" 
																class="form-control" 
																required="true" 
																v-model="dealCurrentRent" 
																:readonly="true"
																aria-label="Amount Per Installment" 
															>
															<div class="input-group-append">
																<span class="input-group-text">{{ general_settings.official_currency_name | capitalize }}</span>
															</div>
														</div>
													</div>

													<div class="form-group col-md-6">
														<label for="inputFirstName"># Installment</label>
														<div class="input-group mb-1">
															<input type="number" 
																class="form-control" 
																v-model.number="singleRentData.number_installment" 
																placeholder="Number Installment" 
																required="true" 
																:min="1" 
																:class="! errors.number_installment ? 'is-valid' : 'is-invalid'" 
																@change="resetTotalRent();setRentExpiryDate()"
															>
															<div class="input-group-append">
																<span class="input-group-text"> {{ (deal.rent_period && deal.rent_period.name) ? deal.rent_period.name : 'NA' }} </span>
															</div>
														</div>
														<div class="invalid-feedback" style="display:block" v-show="errors.number_installment">
													    	{{ errors.number_installment }}
													    </div>
													</div>
												</div>

												<div class="form-row">
													<div class="col-sm-6 form-group">
														<label for="inputFirstName">Issue From</label>
														<div class="date">
															<datepicker 
																v-model="singleRentData.date_from" 
																:initialView="'month'"
																:minimumView="'day'" 
																:maximumView="'year'" 
																placeholder="Start Date" 
																:disabled="true" 
																@input="setRentExpiryDate" 
															>	
															</datepicker>
														</div>
													</div>

													<div class="col-sm-6 form-group">
														<label for="inputFirstName">Expired at</label>
														<div class="date">
															<datepicker 
																v-model="singleRentData.date_to" 
																:disabled="true"
															>	
															</datepicker>
														</div>
													</div>
												</div>
											</div>

											<div class="card-body">
												<div class="form-row">
													<div class="form-group col-md-6">
														<label for="inputFirstName">Total Rent</label>

														<div class="input-group mb-0">
									    					<input type="number" 
																class="form-control is-valid" 
																required="true" 
																v-model.number="singleRentData.total_rent" 
																placeholder="Total Rent" 
																:readonly="true"
															>

									    					<div class="input-group-append">
									    						<span class="input-group-text">
									    							{{ general_settings.official_currency_name | capitalize }}
									    						</span>
									    					</div>
									    				</div>
													</div>

													<div class="form-group col-md-6">
														<label for="inputFirstName">Discount</label>
														<div class="input-group mb-1">
															<input type="number" 
																class="form-control" 
																v-model.number="singleRentData.discount" 
																placeholder="Discount" 
																:class="! errors.discount ? 'is-valid' : 'is-invalid'" 
																@change="resetTotalRent()" 
																:min="0" 
																:max="100" 
															>
															<div class="input-group-append">
																<span class="input-group-text"> % </span>
															</div>
														</div>
														<div class="invalid-feedback" style="display:block" v-show="errors.discount">
													    	{{ errors.discount }}
													    </div>
													</div>
												</div>

												<div class="form-row">
													<!-- 
													<div class="form-group col-md-6">
														<label for="inputFirstName">Last Due</label>

														<div class="input-group mb-0">
									    					<input type="number" 
																class="form-control is-valid" 
																v-model.number="singleRentData.previous_due" 
																placeholder="Previous Due" 
																:disabled="true"
															>

									    					<div class="input-group-append">
									    						<span class="input-group-text">
									    							{{ general_settings.official_currency_name }}
									    						</span>
									    					</div>
									    				</div>
													</div> 
													-->

													<div class="form-group col-md-6">
														<label for="inputFirstName">Net Payable</label>

														<div class="input-group mb-0">
									    					<input type="number" 
																class="form-control is-valid" 
																v-model.number="singleRentData.net_payable" 
																placeholder="Net Payable" 
																:readonly="true"
															>

									    					<div class="input-group-append">
									    						<span class="input-group-text">
									    							{{ general_settings.official_currency_name | capitalize }}
									    						</span>
									    					</div>
									    				</div>
													</div>
													
													<div class="form-group col-md-6">
														<label for="inputFirstName">Paid Amount</label>

														<div class="input-group mb-0">
									    					<input type="number" 
																class="form-control is-valid" 
																v-model.number="singleRentData.total_paid_amount" 
																placeholder="Paid Amount" 
																:class="! errors.total_paid_amount ? 'is-valid' : 'is-invalid'" 
																@change="validateFormInput('total_paid_amount')" 
																:readonly="! createMode && singleRentData.payments && singleRentData.payments.length > 1" 
																required="true" 
																min="1" 
															>

									    					<div class="input-group-append">
									    						<span class="input-group-text">
									    							{{ general_settings.official_currency_name | capitalize }}
									    						</span>
									    					</div>
									    				</div>

									    				<div 
									    					class="invalid-feedback" 
									    					style="display:block;" 
									    					v-show="errors.total_paid_amount" 
									    				>
													    	{{ errors.total_paid_amount }}
													    </div>
													</div>
												</div>

												<div class="form-row">
													<div class="form-group col-md-6">
														<label for="inputFirstName">Current Due</label>

														<div class="input-group mb-0">
									    					<input type="number" 
																class="form-control is-valid" 
																:value="singleRentData.net_payable - singleRentData.total_paid_amount" 
																placeholder="Previous Dues" 
																:readonly="true"
															>

									    					<div class="input-group-append">
									    						<span class="input-group-text">
									    							{{ general_settings.official_currency_name | capitalize }}
									    						</span>
									    					</div>
									    				</div>
													</div>
												</div>
											</div>
										</div>

									</div>

									<div class="col-sm-12 card-footer">
										<div class="form-row">
											<div class="col-sm-12 text-right" v-show="!submitForm">
												<span class="text-danger small mb-1">
											  		Please input required fields
											  	</span>
											</div>
											<div class="col-sm-12">
												<button type="button" class="btn waves-effect waves-dark btn-secondary btn-outline-secondary btn-sm btn-round float-left" v-tooltip.bottom-end="'Previous'"  v-on:click="step-=1">
							                    	<i class="fa fa-2x fa-angle-double-left" aria-hidden="true"></i>
							                  	</button>

												<button 
													type="submit" 
													class="btn waves-effect waves-dark btn-primary btn-outline-primary float-right" 
													:disabled="!submitForm || formSubmitted"
												>
													{{ createMode ? 'Make ' : 'Update ' }} Payment
												</button>
											</div>
										</div>
									</div>
								</div>
							</transition-group>
						</div>
					</form>
				</div>
			</div>
		</div>

		<!-- View Modal -->
		<div class="modal fade" id="merchant-rent-view-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Rent Details</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="modal-body">
						<div class="card">
							<ul class="nav nav-tabs justify-content-center">
								<li class="nav-item">
									<a class="nav-link active" data-toggle="tab" href="#rent-profile" role="tab">
										Profile
									</a>
								</li>

								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" href="#rent-payments" role="tab">
										Payments
									</a>
								</li>

								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" href="#support-rents" role="tab">
										Support Rents
									</a>
								</li>
							</ul>
							
							<div class="tab-content card-block">
								<div class="tab-pane fade in active show" id="rent-profile">
									<div class="form-row">
										<label class="col-6 col-form-label font-weight-bold text-right">
											Id :
										</label>

										<label class="col-6 col-form-label">
											{{ singleRentData.name }}
										</label>
									</div>

									<div class="form-row">
										<label class="col-6 col-form-label font-weight-bold text-right">
											# Installments :
										</label>

										<label class="col-6 col-form-label">
											{{ singleRentData.number_installment }}
										</label>
									</div>

									<div class="form-row">
										<label class="col-6 col-form-label font-weight-bold text-right">
											Date From :
										</label>

										<label class="col-6 col-form-label">
											{{ singleRentData.date_from }}
										</label>
									</div>

									<div class="form-row">
										<label class="col-6 col-form-label font-weight-bold text-right">
											Date To :
										</label>

										<label class="col-6 col-form-label">
											{{ singleRentData.date_to }}
										</label>
									</div>

									<div class="form-row">
										<label class="col-6 col-form-label font-weight-bold text-right">
											Total Rent :
										</label>

										<label class="col-6 col-form-label">
											{{ singleRentData.total_rent }} {{ general_settings.official_currency_name }}
										</label>
									</div>

									<div class="form-row">
										<label class="col-6 col-form-label font-weight-bold text-right">
											Discount :
										</label>

										<label class="col-6 col-form-label">
											{{ singleRentData.discount }} %
										</label>
									</div>

									<!-- 
									<div class="form-row">
										<label class="col-6 col-form-label font-weight-bold text-right">
											Last Due :
										</label>

										<label class="col-6 col-form-label">
											{{ singleRentData.previous_due }} {{ general_settings.official_currency_name }}
										</label>
									</div> 
									-->

									<div class="form-row">
										<label class="col-6 col-form-label font-weight-bold text-right">
											Net Payeble :
										</label>

										<label class="col-6 col-form-label">
											{{ singleRentData.net_payable }} {{ general_settings.official_currency_name | capitalize }}
										</label>
									</div>

									<div class="form-row">
										<label class="col-6 col-form-label font-weight-bold text-right">
											Total Paid Amount :
										</label>

										<label class="col-6 col-form-label">
											{{ singleRentData.total_paid_amount || 0 }} {{ general_settings.official_currency_name | capitalize }}
										</label>
									</div>

									<div class="form-row">
										<label class="col-6 col-form-label font-weight-bold text-right">
											Due :
										</label>

										<label class="col-6 col-form-label">
											{{ (singleRentData.net_payable - singleRentData.total_paid_amount) }} {{ general_settings.official_currency_name | capitalize }}
										</label>
									</div>

									<div class="form-row">
										<label class="col-6 col-form-label font-weight-bold text-right">
											Created at :
										</label>

										<label class="col-6 col-form-label">
											{{ singleRentData.created_at }}
										</label>
									</div>
								</div>

								<div id="rent-payments" class="tab-pane fade">
									<div 
										class="form-row"
										v-if="singleRentData.hasOwnProperty('payments') && singleRentData.payments.length"
									>
										<div class="card card-body">
											<div class="form-row">
												<div 
													class="col-sm-6"
													v-for="(rentPayment, rentPaymentIndex) in singleRentData.payments" 
													:key="'merchant-payment-index-' + rentPaymentIndex + '-merchant-payment-' + rentPayment.id"
												>
													<div class="form-row">
														<label class="col-sm-6 col-form-label font-weight-bold text-right">
															Invoice No :
														</label>

														<label class="col-sm-6 col-form-label">
															{{ rentPayment.invoice_no | capitalize }}
														</label>
													</div>

													<div class="form-row">
														<label class="col-sm-6 col-form-label font-weight-bold text-right">
															Paid Amount :
														</label>

														<label class="col-sm-6 col-form-label">
															{{ rentPayment.total_paid_amount || 0 }}
														</label>
													</div>

													<div class="form-row">
														<label class="col-sm-6 col-form-label font-weight-bold text-right">
															Paid at :
														</label>

														<label class="col-sm-6 col-form-label">
															{{ rentPayment.paid_at }}
														</label>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>

								<div id="support-rents" class="tab-pane fade">
									<div class="form-row"> 
									    <label class="col-6 col-form-label font-weight-bold text-right">
											E-Commerce Fulfillment Support :
										</label>
										<div class="col-6 col-form-label">
											<span :class="[singleRentData.deal && singleRentData.deal.e_commerce_fulfillment_support ? 'badge-success' : 'badge-danger', 'badge']">
												
												{{ singleRentData.deal && singleRentData.deal.e_commerce_fulfillment_support ? 'Enabled' : 'Disabled' }}
												
											</span>
										</div>
									</div>

									<div class="form-row">
										<label class="col-6 col-form-label font-weight-bold text-right">
											E-Commerce Support Charge :
										</label>

										<label class="col-6 col-form-label">
											{{ singleRentData.deal ? singleRentData.deal.e_commerce_fulfillment_charge : 0 }}

											{{ general_settings.official_currency_name || 'BDT' | capitalize }}
										</label>
									</div>

									<div class="form-row"> 
									    <label class="col-6 col-form-label font-weight-bold text-right">
											Purchase Support :
										</label>
										<div class="col-6 col-form-label">
											<span :class="[singleRentData.deal && singleRentData.deal.purchase_support ? 'badge-success' : 'badge-danger', 'badge']">
												
												{{ singleRentData.deal && singleRentData.deal.purchase_support ? 'Enabled' : 'Disabled' }}
												
											</span>
										</div>
									</div>

									<div class="form-row">
										<label class="col-6 col-form-label font-weight-bold text-right">
											Purchase Support Charge :
										</label>

										<label class="col-6 col-form-label">
											{{ singleRentData.deal ? singleRentData.deal.purchase_support_charge : 0 }}

											{{ general_settings.official_currency_name || 'BDT' | capitalize }}
										</label>
									</div>

									<div class="form-row"> 
									    <label class="col-6 col-form-label font-weight-bold text-right">
											POS Support :
										</label>
										<div class="col-6 col-form-label">
											<span :class="[singleRentData.deal && singleRentData.deal.pos_support ? 'badge-success' : 'badge-danger', 'badge']">
												
												{{ singleRentData.deal && singleRentData.deal.pos_support ? 'Enabled' : 'Disabled' }}
												
											</span>
										</div>
									</div>

									<div class="form-row">
										<label class="col-6 col-form-label font-weight-bold text-right">
											POS Support Charge :
										</label>

										<label class="col-6 col-form-label">
											{{ singleRentData.deal ? singleRentData.deal.pos_support_charge : 0 }}

											{{ general_settings.official_currency_name || 'BDT' | capitalize }}
										</label>
									</div>
								</div>
							</div>
						</div>

						<!-- 
						<div class="form-row">
							<div class="col-md-12 text-center">
								<label class="font-weight-bold">
									Spaces Details :
								</label>
							</div>
						</div>

						<div 
							class="form-row" 
							v-if="deal.hasOwnProperty('warehouses') && Array.isArray(deal.warehouses) && deal.warehouses.length"
						>
							<div class="col-sm-12">
								<div 
									class="form-row" 
									v-for="(merchantWarehouse, merchantWarehouseIndex) in deal.warehouses" 
									:key="'rent-warehouse-' + merchantWarehouseIndex + '-warehouse-id-' + merchantWarehouse.id + '-create-or-edit'"
								>
									<div class="col-md-12">
										<div class="card">
											<div class="card-title">
												<div class="form-row">
													<div class="col-md-12 pl-3">
														<label class="font-weight-bold col-form-label">
															{{ merchantWarehouse.name ? $options.filters.capitalize(merchantWarehouse.name) : 'NA' }}
														</label>
													</div>
												</div>
											</div>
												
											<div class="card-body pt-0">
												<div class="form-row" v-if="merchantWarehouse.hasOwnProperty('spaces') && Array.isArray(merchantWarehouse.spaces) && merchantWarehouse.spaces.length">
													<div 
														class="col-md-6 ml-auto" 
														v-for="(warehouseSpace, warehouseSpaceIndex) in merchantWarehouse.spaces" 
														:key="'rent-warehouse-' + merchantWarehouseIndex + '-warehouse-id-' + merchantWarehouse.id + '-space-' + warehouseSpaceIndex + '-view'"
													>
														<div 
															class="card" 
															v-if="warehouseSpace.hasOwnProperty('type') && warehouseSpace.type.includes('containers') && warehouseSpace.hasOwnProperty('containers') && warehouseSpace.containers.length"
														>
															<div 
																class="card-body" 
																v-for="(warehouseContainer, warehouseContainerIndex) in warehouseSpace.containers" 
																:key="'rent-warehouse-' + merchantWarehouseIndex + '-warehouse-id-' + merchantWarehouse.id + '-space-' + warehouseSpaceIndex + '-container-' + warehouseContainerIndex + '-id-' + warehouseContainer.id"
															>
																<h6 class="text-center">Containers Details</h6>

																<div class="form-row">
																	<label class="col-sm-6 col-form-label font-weight-bold text-right">
																		Container Type :
																	</label>
																	<label class="col-sm-6 col-form-label">
																		{{ warehouseContainer.hasOwnProperty('warehouse_container') && warehouseContainer.warehouse_container.hasOwnProperty('container') ? $options.filters.capitalize(warehouseContainer.warehouse_container.container.name) : 'NA' }}
																	</label>
																</div>

																<div class="form-row">
																	<label class="col-sm-6 col-form-label font-weight-bold text-right">
																		Container # :
																	</label>
																	<label class="col-sm-6 col-form-label">
																		{{ warehouseContainer.name ? warehouseContainer.name.substring(warehouseContainer.name.lastIndexOf("-")+1) : 'NA' }}
																	</label>
																</div>
															</div>
														</div>

														<div 
															class="card" 
															v-if="warehouseSpace.hasOwnProperty('type') && warehouseSpace.type.includes('shelves') && warehouseSpace.hasOwnProperty('container') &&  warehouseSpace.container.hasOwnProperty('warehouse_container')"
														>
															<div class="card-body">
																<h6 class="text-center">Shelves Details</h6>
																
																<div class="form-row">
																	<label class="col-sm-6 col-form-label font-weight-bold text-right">
																		Container Type :
																	</label>
																	<label class="col-sm-6 col-form-label">
																		{{ warehouseSpace.container.warehouse_container.container.name | capitalize }}
																	</label>
																</div>

																<div class="form-row">
																	<label class="col-sm-6 col-form-label font-weight-bold text-right">
																		Container # :
																	</label>
																	<label class="col-sm-6 col-form-label">
																		{{ warehouseSpace.container.name ? warehouseSpace.container.name.substring(warehouseSpace.container.name.lastIndexOf("-")+1) : 'NA' }}
																	</label>
																</div>

																<div 
																	class="form-row" 
																	v-if="warehouseSpace.container.hasOwnProperty('shelves') && warehouseSpace.container.shelves.length"
																>
																	<label class="col-sm-6 col-form-label font-weight-bold text-right">
																		Shelf # :
																	</label>
																	<label class="col-sm-6 col-form-label">
																		<ul id="shelf-addresses">
																			<li 
																				v-for="shelfAddress in warehouseSpace.container.shelves" 
																				:key="'shelf-address-' + shelfAddress.id"
																			>

																				{{ shelfAddress.name ? shelfAddress.name.substring(shelfAddress.name.lastIndexOf("-")+1) : 'NA' }}
																				
																			</li>
																		</ul>
																	</label>
																</div>
															</div>
														</div>

														<div 
															class="card" 
															v-if="warehouseSpace.hasOwnProperty('type') && warehouseSpace.type.includes('units') && warehouseSpace.hasOwnProperty('container') && warehouseSpace.container.hasOwnProperty('warehouse_container')"
														>
															<div class="card-body">
																<h6 class="text-center">Units Details</h6>

																<div class="form-row">
																	<label class="col-sm-6 col-form-label font-weight-bold text-right">
																		Container Type :
																	</label>
																	<label class="col-sm-6 col-form-label">
																		{{ warehouseSpace.container.warehouse_container.container.name | capitalize }}
																	</label>
																</div>

																<div class="form-row">
																	<label class="col-sm-6 col-form-label font-weight-bold text-right">
																		Container # :
																	</label>
																	<label class="col-sm-6 col-form-label">
																		{{ warehouseSpace.container.name ? warehouseSpace.container.name.substring(warehouseSpace.container.name.lastIndexOf("-")+1) : 'NA' }}
																	</label>
																</div>

																<div class="form-row">
																	<label class="col-sm-6 col-form-label font-weight-bold text-right">
																		Shelf # :
																	</label>
																	<label class="col-sm-6 col-form-label">
																		{{ warehouseSpace.container.shelf.name ? warehouseSpace.container.shelf.name.substring(warehouseSpace.container.shelf.name.lastIndexOf("-")+1) : 'NA' }}
																	</label>
																</div>

																<div class="form-row" v-if="warehouseSpace.container.shelf.hasOwnProperty('units') && warehouseSpace.container.shelf.units.length">
																	<label class="col-sm-6 col-form-label font-weight-bold text-right">
																		Unit # :
																	</label>
																	<label class="col-sm-6 col-form-label">
																		<ul id="unit-addresses">
																			<li 
																				v-for="unitAddress in warehouseSpace.container.shelf.units" 
																				:key="'unit-address-' + unitAddress.id"
																			>

																				{{ unitAddress.name ? unitAddress.name.substring(unitAddress.name.lastIndexOf("-")+1) : 'NA' }}
																				
																			</li>
																		</ul>
																	</label>
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
						-->
					</div>

					<div class="modal-footer">
						<button type="button" class="btn waves-effect waves-dark btn-secondary btn-outline-secondary btn-sm" data-dismiss="modal">Close</button>
						<button type="button" class="btn waves-effect waves-dark btn-danger btn-outline-danger btn-sm ml-auto" v-tooltip.bottom-end="'Print'" @click="print">Print</button>
					</div>
				</div>
			</div>
		</div>

		<!-- Printing Modal -->
		<div id="sectionToPrint" class="d-none">
			<div class="card">
				<div class="card-header">
					<div class="form-row">
						<div class="col-6">
							<img 
								class="img-fluid" 
								width="60px" 
								:src="'/' + general_settings.logo" 
								:alt="general_settings.app_name + ' Logo'"
							>
							
							<h5>
								{{ general_settings.app_name | capitalize }} Rent Details
							</h5>
						</div>

						<div class="col-6">
							<qr-code 
								:text="singleRentData.name || ''"
								:size="50" 
								class="float-right"
							></qr-code>
						</div> 
					</div>
				</div>

				<div class="card-body">
					<div class="form-row form-group">
						<div class="col-6">
							<div class="form-row">
								<label class="col-6 col-form-label font-weight-bold">
									Total Rent :
								</label>

								<label class="col-6 col-form-label">
									{{ singleRentData.total_rent }} {{ general_settings.official_currency_name }}
								</label>
							</div>

							<div class="form-row">
								<label class="col-6 col-form-label font-weight-bold">
									Discount :
								</label>

								<label class="col-6 col-form-label">
									{{ singleRentData.discount }} %
								</label>
							</div>

							<!-- 
							<div class="form-row">
								<label class="col-6 col-form-label font-weight-bold">
									Last Due :
								</label>

								<label class="col-6 col-form-label">
									{{ singleRentData.previous_due }} {{ general_settings.official_currency_name }}
								</label>
							</div> 
							-->

							<div class="form-row">
								<label class="col-6 col-form-label font-weight-bold">
									Net Payeble :
								</label>

								<label class="col-6 col-form-label">
									{{ singleRentData.net_payable }} {{ general_settings.official_currency_name | capitalize }}
								</label>
							</div>

							<div class="form-row">
								<label class="col-6 col-form-label font-weight-bold">
									Total Paid Amount :
								</label>

								<label class="col-6 col-form-label">
									{{ singleRentData.total_paid_amount || 0 }} {{ general_settings.official_currency_name | capitalize }}
								</label>
							</div>

							<div class="form-row">
								<label class="col-6 col-form-label font-weight-bold">
									Due :
								</label>

								<label class="col-6 col-form-label">
									{{ singleRentData.net_payable - singleRentData.total_paid_amount }} {{ general_settings.official_currency_name | capitalize }}
								</label>
							</div>
						</div>
						
						<div class="col-6">
							<div class="form-row">
								<label class="col-6 col-form-label font-weight-bold text-right">
									Merchant Name :
								</label>

								<label class="col-6 col-form-label">
									{{ merchantName | capitalize }}
								</label>
							</div>

							<div class="form-row">
								<label class="col-6 col-form-label font-weight-bold text-right">
									# Installments :
								</label>

								<label class="col-6 col-form-label">
									{{ singleRentData.number_installment }}
								</label>
							</div>

							<div class="form-row">
								<label class="col-6 col-form-label font-weight-bold text-right">
									Rent from :
								</label>

								<label class="col-6 col-form-label">
									{{ singleRentData.date_from }}
								</label>
							</div>

							<div class="form-row">
								<label class="col-6 col-form-label font-weight-bold text-right">
									Rent To :
								</label>

								<label class="col-6 col-form-label">
									{{ singleRentData.date_to }}
								</label>
							</div>

							<div class="form-row">
								<label class="col-6 col-form-label font-weight-bold text-right">
									Created at :
								</label>

								<label class="col-6 col-form-label">
									{{ singleRentData.created_at }}
								</label>
							</div>
						</div>
					</div>

					<div class="form-row">
						<label class="col-sm-6 col-form-label font-weight-bold">
							Support Rents :
						</label>

						<div class="col-12">
							<table class="table table-striped table-bordered nowrap text-center">
								<thead>
									<tr>
										<th>E-Commerce Support</th>
										<th>Purchase Support</th>
										<th>POS Support</th>
									</tr>
								</thead>

								<tbody>
									<tr>	
										<td>
											{{ singleRentData.deal ? singleRentData.deal.e_commerce_fulfillment_charge : 0 }}
											{{ general_settings.official_currency_name || 'BDT' | capitalize }}
										</td>

										<td>
											{{ singleRentData.deal ? singleRentData.deal.purchase_support_charge : 0 }}
											{{ general_settings.official_currency_name || 'BDT' | capitalize }}
										</td>
										
										<td>
											{{ singleRentData.deal ? singleRentData.deal.pos_support_charge : 0 }}
											{{ general_settings.official_currency_name || 'BDT' | capitalize }}
										</td>
									</tr>
								</tbody>
							</table>							
						</div>
					</div>

					<div 
						class="form-row" 
						v-if="singleRentData.hasOwnProperty('payments') && singleRentData.payments.length"
					>
						<label class="col-sm-6 col-form-label font-weight-bold">
							Payments :
						</label>

						<div class="col-12">
							<table class="table table-striped table-bordered nowrap text-center">
								<thead>
									<tr>
										<th>Invoice</th>
										<th>Paid Amount</th>
										<th>Paid At</th>
									</tr>
								</thead>

								<tbody>
									<tr 
										v-for="(rentPayment, rentPaymentIndex) in singleRentData.payments" 
										:key="'rent-rent-' + rentPaymentIndex + '-id-' + rentPayment.id"
									>	
										<td>
											{{ rentPayment.invoice_no }}
										</td>

										<td>
											{{ rentPayment.paid_amount }}
										</td>

										<td>
											{{ rentPayment.paid_at }}
										</td>
									</tr>
								</tbody>
							</table>							
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- Printing Modal Ends -->

		<!-- Filter Modal -->
		<div class="modal fade" id="rent-custom-search" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLongTitle">Custom Search</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="form-row">
							<div class="col-12 text-center">
								<p>
									Timelaps
								</p>
								 
								<v-date-picker 
									v-model="searchAttributes.dates" 
									color="red" 
									is-dark
									is-range 
									is-inline
									:max-date="new Date()" 
									:model-config="{ type: 'string', mask: 'YYYY-MM-DD' }"
									:attributes="[ { key: 'today', dot: true, dates: new Date() } ]" 
									@input="setSearchingDates()"
								/> 
							</div>					
						</div>
					</div>
					
					<div class="modal-footer">
						<button type="button" class="btn waves-effect waves-dark btn-success btn-outline-success" v-tooltip.bottom-end="'Reset'"  @click="resetSearchingDates()">
	                  		Reset
	                  	</button>

						<button type="button" class="btn waves-effect waves-dark btn-primary btn-outline-primary ml-auto" data-dismiss="modal">
	                  		See Results
	                  	</button>
					</div>
				</div>
			</div>
		</div>

		<delete-confirmation-modal 
			v-if="userHasPermissionTo('delete-merchant-payment')" 
			:csrf="csrf" 
			:submit-method-name="'deleteAsset'" 
			:content-to-delete="singleRentData"
			:restoration-message="'But once you think, you can restore this item !'" 
			
			@deleteAsset="deleteAsset($event)" 
		></delete-confirmation-modal>
	</div>

</template>

<script type="text/javascript">

	import Datepicker from 'vuejs-datepicker';

    let singleRentData = {
    	
		number_installment : 1,
		date_from : 1,
		date_to : 1,
		total_rent : 0, // generated from selected spaces
		discount : 0,	// percentage 
    	// previous_due : 0,
		net_payable : 0,
		total_paid_amount : 0,
		// current_due : 0,
		// merchant_support_deal_id : null,
		// paid_at : null,
		rents : [
			{
				// rent : 0,
				// dealt_space_id : null,
				// merchant_payment_id : null
			},
		]

    };

	export default {

		components: { 
	    	Datepicker,
		},

		props: {

			deal:{
				type: Object,
				required: true,
			},
			merchantName:{
				type: String,
				required: true,
			},

		},

	    data() {

	        return {

	        	step : 1,
	        	error : '',
    			perPage : 10,
	        	loading : false,
	        	submitForm : true,
	        	formSubmitted : false,
	        	// currentTab : 'current',

	        	ascending : false,
	      		descending : false,

	        	createMode : true,

	        	// allFetchedContents : [],
	        	dealAllRents : [],
	        	// dealtSpacesAndRents : [],

	        	pagination: {
		        	current_page: 1
		      	},

		      	errors : {

		      	},

	        	dealCurrentRent : 0,

	        	singleRentData : singleRentData, 

	        	searchAttributes : {

	        		dates : {},
	        		search : '',
		        	dateTo : null,
		        	dateFrom : null,
		        	
		        	/*
			        	showPendingRequisitions : false,
			        	showCancelledRequisitions : false,
			        	showDispatchedRequisitions : false,
			        	showProduct : null,
		        	*/

	        	},

	        	dataToExport: {

					"# Installments": 'number_installment',

					"Issued From": 'date_from',

					"Expired At": 'date_to',

					"Total Rent": 'total_rent',

					Discount: 'discount',

					// "Prev. Due": 'previous_due',

					"Net Payable": 'net_payable',

					"Total Paid": 'total_paid_amount',

					Due: {
						callback: (object) => {
							return (object.net_payable - object.total_paid_amount);
						}
					},

					"Payments": {
						field: "payments",
						callback: (payments) => {

							if (payments && payments.length) {
								
								var infosToReturn = '';

								payments.forEach(
					
									(rentPayment, rentPaymentIndex) => {

										infosToReturn += "Invoice No: " + rentPayment.invoice_no + "\n Paid Amount: " + rentPayment.total_paid_amount + "\n Paid at:" + rentPayment.paid_at;

									}
									
								);

								return infosToReturn;

							}
							else {
								return 'No Payments Found.'
							}

						},
					},

					"Support Rent": {
						field: "supportRent",
						callback: (supportRent) => {
	
							var infosToReturn = '';

							infosToReturn += "E-Commerce Support: " + supportRent.e_commerce_fulfillment_charge + "\n Purchase Support: "  + supportRent.purchase_support_charge + "\n POS Support: " + supportRent.pos_support_charge + "\n";

							return infosToReturn;

						},
					},
					
				},

				printingStyles : {
				    
				    // name: 'Test Name',
				    
				    specs: [
				        'fullscreen=yes',
				        // 'titlebar=yes',
				        'scrollbars=yes'
				    ],

				    styles: [
				    	"/css/bootstrap.min.css",
				    ],

				    timeout: 1000, // default timeout before the print window appears
					autoClose: true, // if false, the window will not close after printing
					windowTitle: 'Payment Details' 

				},

	            general_settings : JSON.parse(window.localStorage.getItem('general_settings')),

	            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

	        }

		},

		mounted(){

			this.fetchAllPayments();			
			this.getDealTotalRents();

		},

		computed: {
			// a computed getter
			today: function() {

				let date = new Date();

				return date.getFullYear() + '-' +  (date.getMonth() + 1) + '-' + date.getDate();

			},
		},

		watch : {

			'searchAttributes.search' : function(val){
				
				this.pagination.current_page = 1; 

				if (this.searchAttributes.search==='' && ! this.searchAttributes.dateTo && ! this.searchAttributes.dateFrom) {

					this.fetchAllPayments();

				}
				else {

					let format = /[`!@#$%^&*+\=\[\]{};':"\\|,.<>\/?~]/;

					if (! format.test(val)) {

						this.searchData();
					
					}

				}

			},
			
			'searchAttributes.dateFrom' : function(val){
				
				this.pagination.current_page = 1; 

				if (this.searchAttributes.search==='' && ! this.searchAttributes.dateTo && ! this.searchAttributes.dateFrom) {

					this.fetchAllPayments();

				}
				else {

					this.searchData();
						
				}

			},

			'searchAttributes.dateTo' : function(val){
				
				this.pagination.current_page = 1; 

				if (this.searchAttributes.search==='' && ! this.searchAttributes.dateTo && ! this.searchAttributes.dateFrom) {

					this.fetchAllPayments();

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

			fetchAllPayments() {
				
				this.error = '';
				this.loading = true;
				this.dealAllRents = [];
				this.searchAttributes.search = '';
				// this.allFetchedContents = [];
				
				axios
					.get('/api/support-deal-rents/' + this.deal.id + '/' + this.perPage + "?page=" + this.pagination.current_page)
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
			/*
			fetchDealtSpaceLatestRents() {
				
				this.error = '';
				this.loading = true;
				this.dealtSpacesAndRents = [];
				
				axios
					.get('/api/support-deal-rents/' + this.deal.id + '/' + this.perPage + "?page=" + this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							this.dealtSpacesAndRents = response.data;
							// this.setContentPagination(response);
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
			*/
			getDealTotalRents() {

				this.dealCurrentRent = 0;
				
				this.dealCurrentRent += this.deal.e_commerce_fulfillment_charge;
				this.dealCurrentRent += this.deal.purchase_support_charge;
				this.dealCurrentRent += this.deal.pos_support_charge;

			},
			searchData() {

				this.error = '';
				this.loading = true;
				this.dealAllRents = [];
				// this.allFetchedContents = [];
				// this.pagination.current_page = 1;
				this.searchAttributes.merchant_support_deal_id = this.deal.id;
				
				axios
				.post("/api/search-support-deal-rents/" + this.perPage + "?page=" + this.pagination.current_page, this.searchAttributes)
				.then(response => {
					// this.allFetchedContents = response.data;
					this.dealAllRents = response.data.all.data;
					this.pagination = response.data.all;
				})
				.catch(e => {
					this.error = e.toString();
				})
				.finally(response => {
					this.loading = false;
				});

			},
			showContentDetails(object) {	
				this.singleRentData = object;
				$('#merchant-rent-view-modal').modal('show');
			},
			showContentCreateForm() {

				this.step = 1;
				this.createMode = true;

				let dealLastPayment = this.dealAllRents.length ? this.dealAllRents[this.dealAllRents.length-1] : {};

				this.singleRentData = {

					number_installment : 1,
					date_from : dealLastPayment.date_to ?? this.today,
					date_to : null,
					total_rent : this.dealCurrentRent, // generated from selected spaces
					discount : 0,	// percentage 
					// previous_due : dealLastPayment.current_due ?? 0,	// as new deal
					net_payable : this.dealCurrentRent /* + (dealLastPayment.current_due ?? 0) */,
					total_paid_amount : 0,
					// current_due : 0,
					merchant_support_deal_id : this.deal.id,
					// paid_at : null,
					
					/*
					// gonna get from table when creating at server
					rents : [
						{
							rent : 0,
							// dealt_space_id : null,
							// merchant_payment_id : null
						},
					]
					*/

				};

				this.errors = {
		      		
		      	};

		      	this.setRentExpiryDate();

				$('#merchant-rent-createOrEdit-modal').modal('show');

			},
			openContentEditForm(object) {
				
				this.step = 1;
				this.createMode = false;

				this.singleRentData = object;

				$('#merchant-rent-createOrEdit-modal').modal('show');
				
			},
			openContentDeleteForm(object) {	
				this.singleRentData = object;
				$('#delete-confirmation-modal').modal('show');
			},
			goToPartialPayments(object) {
				this.$router.push({ name: 'rent-payments', params: { rentId:object.id, merchantName:this.merchantName, rent:object }});
			},
			createDealRent() {
				
				if (! this.verifyUserInput()) {
					
					this.submitForm = false;
					return;

				}

				this.formSubmitted = true;

				axios
					.post('/support-deal-rents/' + this.perPage, this.singleRentData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("New rent has been added", "Success");
							// this.allFetchedContents = response.data;
							
							this.pagination.current_page = 1; 
							this.searchAttributes.search !== '' ? this.searchData() : this.setContentPagination(response);

							/*
							if (this.singleRentData.total_paid_amount > 0) {

								this.print();

							}
							*/
							
							$('#merchant-rent-createOrEdit-modal').modal('hide');
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
			updateDealRent() {
				
				if (! this.verifyUserInput()) {
					
					this.submitForm = false;
					return;

				}

				this.formSubmitted = true;

				axios
					.put('/support-deal-rents/' + this.singleRentData.id + '/' + this.perPage, this.singleRentData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Rent has been updated", "Success");
							// this.allFetchedContents = response.data;
							
							this.pagination.current_page = 1; 
							this.searchAttributes.search !== '' ? this.searchData() : this.setContentPagination(response);

							/*
							if (this.singleRentData.total_paid_amount > 0) {

								this.print();

							}
							*/

							$('#merchant-rent-createOrEdit-modal').modal('hide');
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
			deleteAsset(singleRentData) {
				
				axios
					.delete('/support-deal-rents/' + singleRentData.id + '/' + this.perPage)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Storage type has been deleted", "Success");
							// this.allFetchedContents = response.data;
							
							this.pagination.current_page = 1; 
							this.searchAttributes.search !== '' ? this.searchData() : this.setContentPagination(response);
							
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

				this.validateFormInput('discount');
				this.validateFormInput('total_paid_amount');
				this.validateFormInput('number_installment');

				if (this.errors.constructor === Object && Object.keys(this.errors).length < 1) {

					return true;
				
				}

				return false;
		
			},
			/*
			setDealAllPayments() {

				this.dealAllRents = this.deal.rents ? this.deal.rents : [];

			},
			*/
            changeNumberContents() {

				this.pagination.current_page = 1;
				// this.perPage = expectedContentsPerPage;

				if (this.searchAttributes.search === '') {
					this.fetchAllPayments();
					// this.setDealAllPayments();
				}
				else {
					this.searchData();
				}
				
    		},
    		setContentPagination(response) {

    			this.dealAllRents = response.data.data;
				this.pagination = response.data;

			},
			nextPage() {

				if (this.step > 1) {
					return;
				}

				// this.getDealTotalRents();

				this.step++;

			},
			setRentExpiryDate() {

				var moment = require('moment');

				let daysToIncrease = this.singleRentData.number_installment * this.deal.rent_period.number_days;

				this.singleRentData.date_from = moment(this.singleRentData.date_from).format('YYYY-MM-DD');

				this.singleRentData.date_to = moment(this.singleRentData.date_from).add(daysToIncrease, 'days').format('YYYY-MM-DD');
				
				
			},
			resetTotalRent() {
				
				this.validateFormInput('discount');
				this.validateFormInput('number_installment');
				
				if (! this.errors.discount && ! this.errors.number_installment) {

					this.singleRentData.total_rent = this.dealCurrentRent * this.singleRentData.number_installment;

					this.singleRentData.net_payable = this.singleRentData.total_rent = this.singleRentData.total_rent - (this.singleRentData.total_rent * this.singleRentData.discount / 100);

					// this.singleRentData.net_payable = /*this.singleRentData.previous_due + */this.singleRentData.total_rent;

				}
				
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

				this.dealAllRents.sort(
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

				this.dealAllRents.sort(
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

				this.dealAllRents.sort(
			 		function(a, b){
						return a[columnValue] - b[columnValue];
					}
				);

			},
			descendingIntegers(columnValue) {

				this.dealAllRents.sort(
			 		function(a, b){
						return b[columnValue] - a[columnValue];
					}
				);

			},
			resetSearchingDates(){

            	this.searchAttributes.dates = {};
				this.searchAttributes.dateTo = null;
				this.searchAttributes.dateFrom = null;				

            },
            setSearchingDates(){

            	if (this.searchAttributes.dates && Object.keys(this.searchAttributes.dates).length > 0 && this.searchAttributes.dates.hasOwnProperty('start') && this.searchAttributes.dates.hasOwnProperty('end')) {

					this.searchAttributes.dateTo = this.searchAttributes.dates.end;
					this.searchAttributes.dateFrom = this.searchAttributes.dates.start;
						
				}
				else {

					this.resetSearchingDates();

				}

            },
            setTodayDate() {
            	
            	if (this.searchAttributes.dateFrom != this.today || this.searchAttributes.dateTo) {
	            	
	            	// this.searchAttributes.dateTo = null; 
	            	this.searchAttributes.dates = {};
	            	this.searchAttributes.dateTo = null;
	            	this.searchAttributes.dateFrom = this.today;

            	}
            	else {

	            	this.searchAttributes.dateFrom = null

            	}

            },
            print() {

				this.printingStyles.windowTitle = this.$options.filters.capitalize(`Installment Details`);

				// this.$set(this, 'paymentToPrint', paymentToPrint);
				// this.paymentToPrint = paymentToPrint;

				JsBarcode(".barcode").init();

				this.$htmlToPaper('sectionToPrint', this.printingStyles);

				$('#merchant-rent-view-modal').modal('hide');

			},
			validateFormInput(formInputName) {

				this.submitForm = false;

				switch(formInputName) {

					case 'total_paid_amount' : 
						
						if(! this.singleRentData.total_paid_amount || this.singleRentData.total_paid_amount < 1){
							
							this.errors.total_paid_amount = 'Invalid amount';

						}
						else {

							this.submitForm = true;
							this.$delete(this.errors, 'total_paid_amount');

						}

						break;

					case 'number_installment' : 
						
						if(! this.singleRentData.number_installment || this.singleRentData.number_installment < 1){
							
							this.errors.number_installment = 'Rent installment number is required';

						}
						else {

							this.submitForm = true;
							this.$delete(this.errors, 'number_installment');

						}

						break;

					case 'discount' : 
						
						if(this.singleRentData.discount && (this.singleRentData.discount < 0 || this.singleRentData.discount > 100)){

							this.errors.discount = 'Discount should be between 0 to 100';
						}
						else {

							this.submitForm = true;
							this.$delete(this.errors, 'discount');

						}

						break;

				}

			}
            
		}
  	}

</script>