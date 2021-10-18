
<template v-if="userHasPermissionTo('view-merchant-payment-index')">

	<div class="pcoded-content">

		<breadcrumb 
			:title="'payment'" 
			:message="'All payments of ' + merchantName + ' for the deal ' + dealName"
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
											  		v-if="userHasPermissionTo('create-merchant-payment')" 
											  		:query="query" 
											  		:caller-page="'payment'" 
											  		:required-permission="'merchant-payment'"
											  		
											  		@showContentCreateForm="showContentCreateForm" 
											  		@searchData="searchData($event)" 
											  		@fetchAllContents="setDealAllPayments()"
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
																	v-for="(content, contentIndex) in dealAllPayments" 
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
															  		v-show="! dealAllPayments.length"
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
																@click="query === '' ? setDealAllPayments() : searchData()"
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
																@paginate="query === '' ? setDealAllPayments() : searchData()"
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
		<div class="modal fade" id="merchant-payment-createOrEdit-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" v-if="userHasPermissionTo('create-merchant-payment') || userHasPermissionTo('update-merchant-payment')">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">
							{{ createMode ? 'Create New ' : 'Update ' }} Payment {{ createMode ? '' : (singlePaymentData.invoice_no) }}
						</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
						
					<form 	
						class="form-horizontal" 
						v-on:submit.prevent="createMode ? createDealPayment() : updateDealPayment()" 
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
									<h2 class="mx-auto mb-4 lead">Dealt Spaces</h2>

									<div class="col-md-12">
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
																	<label class="col-sm-6 col-form-label font-weight-bold text-right">
																		Warehouse :
																	</label>
																	<label class="col-sm-6 col-form-label">
																		{{ merchantWarehouse.name ? $options.filters.capitalize(merchantWarehouse.name) : 'NA' }}
																	</label>
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
																				<h6 class="text-center">Containers</h6>

																				<div class="form-row">
																					<label class="col-sm-6 col-form-label font-weight-bold text-right">
																						Container Name :
																					</label>
																					<label class="col-sm-6 col-form-label">
																						{{ warehouseContainer.name | capitalize }}
																					</label>
																				</div>

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
																				 
																				<div class="form-row" v-show="createMode">
																					<label class="col-sm-6 col-form-label font-weight-bold text-right">
																						Current Rent :
																					</label>
																					<label class="col-sm-6 col-form-label">
																						{{ warehouseContainer.selected_rent ? warehouseContainer.selected_rent.rent : 'NA' }}
																					</label>
																				</div>

																				<div class="form-row">
																					<label class="col-sm-6 col-form-label font-weight-bold text-right">
																						Rent Period :
																					</label>
																					<label class="col-sm-6 col-form-label">
																						{{ warehouseContainer.selected_rent ? warehouseContainer.selected_rent.name : 'NA' | capitalize }}
																					</label>
																				</div>

																				<div class="form-row">
																					<label class="col-sm-6 col-form-label font-weight-bold text-right">
																						# Installment :
																					</label>
																					
																				</div>
																			</div>
																		</div>

																		<div 
																			class="card" 
																			v-if="warehouseSpace.hasOwnProperty('type') && warehouseSpace.type.includes('shelves') && warehouseSpace.hasOwnProperty('container') &&  warehouseSpace.container.hasOwnProperty('warehouse_container')"
																		>
																			<div class="card-body">
																				<h6 class="text-center">Shelves</h6>
																				
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

																				<div class="form-row">
																					<label class="col-sm-6 col-form-label font-weight-bold text-right">
																						Rent Period :
																					</label>
																					<label class="col-sm-6 col-form-label">
																						{{ (warehouseSpace.container && warehouseSpace.container.selected_rent) ? warehouseSpace.container.selected_rent.name : 'NA' | capitalize }}
																					</label>
																				</div>

																				<div class="form-row" v-show="createMode">
																					<label class="col-sm-6 col-form-label font-weight-bold text-right">
																						Current Rent :
																					</label>
																					<label class="col-sm-6 col-form-label">
																						{{ warehouseSpace.container.selected_rent ? warehouseSpace.container.selected_rent.rent : 'NA' }}
																					</label>
																				</div>

																				<div class="form-row">
																					<label class="col-sm-6 col-form-label font-weight-bold text-right">
																						# Installment :
																					</label>
																					
																				</div>
																			</div>
																		</div>

																		<div 
																			class="card" 
																			v-if="warehouseSpace.hasOwnProperty('type') && warehouseSpace.type.includes('units') && warehouseSpace.hasOwnProperty('container') && warehouseSpace.container.hasOwnProperty('warehouse_container')"
																		>
																			<div class="card-body">
																				<h6 class="text-center">Units</h6>

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

																				<div class="form-row">
																					<label class="col-sm-6 col-form-label font-weight-bold text-right">
																						Rent Period :
																					</label>
																					<label class="col-sm-6 col-form-label">
																						{{ (warehouseSpace.container && warehouseSpace.container.selected_rent) ? warehouseSpace.container.selected_rent.name : 'NA' | capitalize }}
																					</label>
																				</div>
																				 
																				<div class="form-row" v-show="createMode">
																					<label class="col-sm-6 col-form-label font-weight-bold text-right">
																						Current Rent :
																					</label>
																					<label class="col-sm-6 col-form-label">
																						{{ warehouseSpace.container.selected_rent ? warehouseSpace.container.selected_rent.rent : 'NA' }}
																					</label>
																				</div>

																				<div class="form-row">
																					<label class="col-sm-6 col-form-label font-weight-bold text-right">
																						# Installment :
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

										<div class="form-row" v-else>
											<div 
												class="col-md-12 text-center" 
											>
												<p class="text-danger">
													No Space Found.
												</p>
											</div>
										</div>
									</div>

									<div class="col-md-12 card-footer pb-0">
										<div class="form-row">
									    	<div class="form-group col-sm-12 text-right">
								          		<div class="text-danger small mb-1" v-show="!submitForm">
											  		Please input required fields
									          	</div>
									          	<button type="button" class="btn btn-outline-secondary btn-sm btn-round" v-on:click="nextPage">
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
										<div class="form-row">
											<div class="form-group col-md-6">
												<label for="inputFirstName">Total Rent</label>
												<input type="number" 
													class="form-control is-valid" 
													v-model.number="singlePaymentData.total_rent" 
													placeholder="Total Rent" 
													:disabled="true"
												>
											</div>

											<div class="form-group col-md-6">
												<label for="inputFirstName">Discount</label>
												<div class="input-group mb-1">
													<input type="number" 
														class="form-control" 
														v-model.number="singlePaymentData.discount" 
														placeholder="Discount" 
														:min="0" 
														:max="100" 
														:class="! errors.discount ? 'is-valid' : 'is-invalid'" 
														@change="resetTotalRent()"
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
											<div class="form-group col-md-6">
												<label for="inputFirstName">Last Due</label>
												<input type="number" 
													class="form-control is-valid" 
													v-model.number="singlePaymentData.previous_due" 
													placeholder="Previous Due" 
													:disabled="true"
												>
											</div>

											<div class="form-group col-md-6">
												<label for="inputFirstName">Net Payable</label>
												<input type="number" 
													class="form-control is-valid" 
													v-model.number="singlePaymentData.net_payable" 
													placeholder="Net Payable" 
													:disabled="true"
												>
											</div>
										</div>

										<div class="form-row">
											<div class="form-group col-md-6">
												<label for="inputFirstName">Paid Amount</label>
												<input type="number" 
													class="form-control" 
													v-model.number="singlePaymentData.paid_amount" 
													placeholder="Paid Amount" 
													:class="! errors.paid_amount ? 'is-valid' : 'is-invalid'" 
													@change="validateFormInput('paid_amount')" 
													required="true" 
												>
												<div class="invalid-feedback">
											    	{{ errors.paid_amount }}
											    </div>
											</div>

											<div class="form-group col-md-6">
												<label for="inputFirstName">Current Due</label>
												<input type="number" 
													class="form-control is-valid" 
													:value="singlePaymentData.net_payable - singlePaymentData.paid_amount" 
													placeholder="Previous Dues" 
													:disabled="true"
												>
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
												<button type="button" class="btn btn-outline-secondary btn-sm btn-round float-left" v-on:click="step-=1">
							                    	<i class="fa fa-2x fa-angle-double-left" aria-hidden="true"></i>
							                  	</button>
												<button 
													type="submit" 
													class="btn btn-primary float-right" 
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
		<div class="modal fade" id="merchant-payment-view-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Payment ({{ singlePaymentData.invoice_no }}) Details</h5>
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

						<div 
							class="form-row" 
							v-if="singlePaymentData.hasOwnProperty('rents') && singlePaymentData.rents.length"
						>
							<div 
								class="col-md-6" 
								v-for="(paymentRent, paymentRentIndex) in singlePaymentData.rents" 
								:key="'payment-rent-' + paymentRentIndex + '-id-' + paymentRent.id"
							>
								<div class="card card-body">
									<div class="form-row">
										<label class="col-sm-6 col-form-label font-weight-bold text-right">
											Space Type :
										</label>

										<label class="col-sm-6 col-form-label">
											{{ paymentRent.dealt_space ? (paymentRent.dealt_space.type.includes('WarehouseContainerStatus') ? 'Container' :(paymentRent.dealt_space.type.includes('WarehouseContainerShelfStatus') ? 'Shelf' : 'Unit')) : 'NA' }}
										</label>	
									</div>

									<div class="form-row">
										<label class="col-sm-6 col-form-label font-weight-bold text-right">
											Space Name :
										</label>

										<label class="col-sm-6 col-form-label">
											{{ paymentRent.dealt_space ? paymentRent.dealt_space.name : 'NA' | capitalize }}
										</label>	
									</div>
								
									<div class="form-row">
										<label class="col-sm-6 col-form-label font-weight-bold text-right">
											Rent :
										</label>

										<label class="col-sm-6 col-form-label">
											{{ paymentRent.rent }}
										</label>	
									</div>
								
									<div class="form-row">
										<label class="col-sm-6 col-form-label font-weight-bold text-right">
											Issued from :
										</label>

										<label class="col-sm-6 col-form-label">
											{{ paymentRent.issued_from }}
										</label>	
									</div>
								
									<div class="form-row">
										<label class="col-sm-6 col-form-label font-weight-bold text-right">
											Expired At :
										</label>

										<label class="col-sm-6 col-form-label">
											{{ paymentRent.expired_at }}
										</label>	
									</div>

									<div class="form-row">
										<label class="col-sm-6 col-form-label font-weight-bold text-right">
											# Installments :
										</label>

										<label class="col-sm-6 col-form-label">
											{{ paymentRent.number_installment }}
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
    	
    	previous_due : 0,
		total_rent : 0, // generated from selected spaces
		discount : 0,	// percentage 
		net_payable : 0,
		paid_amount : 0,
		current_due : 0,
		// merchant_deal_id : null,
		// paid_at : null,
		rents : [
			{
				// issued_from : null,
				// expired_at : null,
				// rent : 0,
				// number_installment : 0,
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
			dealName:{
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

	        	step : 1,
	        	query : '',
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
	        	dealAllPayments : [],
	        	// dealtSpacesAndRents : [],

	        	pagination: {
		        	current_page: 1
		      	},

		      	errors : {

		      	},

	        	dealTotalCurrentRent : 0,

	        	singlePaymentData : singlePaymentData,

	            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

	        }

		},
		
		created(){

			// this.fetchAllPayments();			
			
			// this.fetchDealtSpaceLatestRents();

			this.setDealAllPayments();

		},

		mounted(){

			// this.setDealTotalPayments();

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
				this.dealAllPayments = [];
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
			/*
			fetchDealtSpaceLatestRents() {
				
				this.query = '';
				this.error = '';
				this.loading = true;
				this.dealtSpacesAndRents = [];
				
				axios
					.get('/api/deal-payments/' + this.deal.id + '/' + this.perPage + "?page=" + this.pagination.current_page)
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
			setDealTotalPayments() {

				this.dealTotalCurrentRent = 0;
				
				if (this.deal.warehouses.length > 0) {

					this.deal.warehouses.forEach(
						(merchantWarehouse, merchantWarehouseIndex) => {
							merchantWarehouse.spaces.forEach(
								(warehouseSpace, warehouseSpaceIndex) => {
									if (warehouseSpace.type.includes('containers') && warehouseSpace.hasOwnProperty('containers') && warehouseSpace.containers.length) {

										warehouseSpace.containers.forEach(
											(warehouseContainer, warehouseContainerIndex) => {
												
												this.dealTotalCurrentRent += warehouseContainer.selected_rent ? warehouseContainer.selected_rent.rent : 0;

											}
										);

									}

									else if (warehouseSpace.type.includes('shelves') && warehouseSpace.hasOwnProperty('container') && warehouseSpace.container.hasOwnProperty('shelves') && warehouseSpace.container.shelves.length) {

										this.dealTotalCurrentRent += warehouseSpace.container.hasOwnProperty('selected_rent') ? warehouseSpace.container.selected_rent.rent : 0;

									}

									else if (warehouseSpace.type.includes('units') && warehouseSpace.hasOwnProperty('container') && warehouseSpace.container.hasOwnProperty('shelf') && warehouseSpace.container.shelf.hasOwnProperty('units') && warehouseSpace.container.shelf.units.length) {

										this.dealTotalCurrentRent += warehouseSpace.container.hasOwnProperty('selected_rent') ? warehouseSpace.container.selected_rent.rent : 0;

									}
								}
							);
						}
					);	

				}

			},
			searchData(emitedValue=false) {

				if (emitedValue) {
					this.query=emitedValue;
				}

				this.error = '';
				this.dealAllPayments = [];
				// this.allFetchedContents = [];
				this.pagination.current_page = 1;
				
				axios
				.get(
					"/api/search-deal-payments/" + this.deal.id + '/' + this.query + "/" + this.perPage + "?page=" + this.pagination.current_page
				)
				.then(response => {
					// this.allFetchedContents = response.data;
					this.dealAllPayments = response.data.all.data;
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

				this.step = 1;
				this.createMode = true;

				this.singlePaymentData = {
					
					previous_due : this.dealAllPayments[this.dealAllPayments.length-1].current_due,
					total_rent : this.dealTotalCurrentRent, // generated from selected spaces
					discount : 0,	// percentage 
					net_payable : this.dealAllPayments[this.dealAllPayments.length-1].current_due + this.dealTotalCurrentRent,
					paid_amount : 0,
					current_due : 0,
					merchant_deal_id : this.deal.id,

					// rents : this.dealAllPayments[this.dealAllPayments.length-1].rents,

				};

				this.errors = {
		      		
		      	};

				$('#merchant-payment-createOrEdit-modal').modal('show');

			},
			openContentEditForm(object) {
				
				this.step = 1;
				this.createMode = false;

				this.singlePaymentData = object;

				$('#merchant-payment-createOrEdit-modal').modal('show');
				
			},
			openContentDeleteForm(object) {	
				this.singlePaymentData = object;
				$('#delete-confirmation-modal').modal('show');
			},
			createDealPayment() {
				
				if (! this.verifyUserInput()) {
					
					this.submitForm = false;
					return;

				}

				this.formSubmitted = true;

				axios
					.post('/deal-payments/' + this.perPage, this.singlePaymentData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("New payment has been added", "Success");
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
					})
					.finally(response => {
						this.formSubmitted = false;
					});

			},
			updateDealPayment() {
				
				if (! this.verifyUserInput()) {
					
					this.submitForm = false;
					return;

				}

				this.formSubmitted = true;

				axios
					.put('/deal-payments/' + this.singlePaymentData.id + '/' + this.perPage, this.singlePaymentData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Payment has been updated", "Success");
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
					})
					.finally(response => {
						this.formSubmitted = false;
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
			verifyUserInput() {

				this.validateFormInput('discount');
				this.validateFormInput('paid_amount');

				if (this.errors.constructor === Object && Object.keys(this.errors).length < 1) {

					return true;
				
				}

				return false;
		
			},
			setDealAllPayments() {

				this.dealAllPayments = this.deal.payments;

			},
            changeNumberContents() {

				this.pagination.current_page = 1;
				// this.perPage = expectedContentsPerPage;

				if (this.query === '') {
					// this.fetchAllPayments();
					this.setDealAllPayments();
				}
				else {
					this.searchData();
				}
				
    		},
    		setContentPagination(response) {

    			this.dealAllPayments = response.data.data;
				this.pagination = response.data;

			},
			nextPage() {

				if (this.step > 1) {
					return;
				}

				this.step++;

			},
			resetTotalRent() {
				
				this.validateFormInput('discount');
				
				if (! this.errors.discount) {

					this.singlePaymentData.total_rent = this.dealTotalCurrentRent - (this.dealTotalCurrentRent * this.singlePaymentData.discount / 100);

					this.singlePaymentData.net_payable = this.singlePaymentData.previous_due + this.singlePaymentData.total_rent;

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

				this.dealAllPayments.sort(
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

				this.dealAllPayments.sort(
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

				this.dealAllPayments.sort(
			 		function(a, b){
						return a[columnValue] - b[columnValue];
					}
				);

			},
			descendingIntegers(columnValue) {

				this.dealAllPayments.sort(
			 		function(a, b){
						return b[columnValue] - a[columnValue];
					}
				);

			},
			validateFormInput(formInputName) {

				this.submitForm = false;

				switch(formInputName) {

					case 'paid_amount' : 
						
						if(! this.singlePaymentData.paid_amount || this.singlePaymentData.paid_amount < 1){
							
							this.errors.paid_amount = 'Paid amount is required';

						}
						else {

							this.submitForm = true;
							this.$delete(this.errors, 'paid_amount');

						}

						break;

					case 'discount' : 
						
						if(this.singlePaymentData.discount && (this.singlePaymentData.discount < 0 || this.singlePaymentData.discount > 100)){

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

<style scoped>
	@import '~vue-multiselect/dist/vue-multiselect.min.css';

	.fade-enter-active {
  		transition: all .3s ease;
	}
	.fade-leave-active {
  		transition: all .8s cubic-bezier(1.0, 0.5, 0.8, 1.0);
	}
	.fade-enter, .fade-leave-to {
  		transform: translateX(10px);
  		opacity: 0;
	}
</style>