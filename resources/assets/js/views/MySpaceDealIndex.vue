
<template>
	<div class="pcoded-content">
		<breadcrumb 
			:icon="'deals'"
			:title="fullName + ' deals'" 
			:message="'All our deals with ' + fullName"
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
												<div class="row form-group">
											  		<div class="col-md-4 col-sm-6 d-flex align-items-center form-group">
											  			<div class="mr-2">
											  				<span>
													  			{{ 
													  				( /* searchAttributes.showPendingRequisitions || searchAttributes.showCancelledRequisitions || searchAttributes.showDispatchedRequisitions || searchAttributes.showProduct || */ searchAttributes.search || searchAttributes.dateFrom || searchAttributes.dateTo) ? 'Searched Deals List' : 'Deals List'
													  			}}
											  				</span>
											  			</div>

											  			<div class="dropdown">
									  						<i class="fa fa-download fa-lg dropdown-toggle" data-toggle="dropdown" v-tooltip.bottom-end="'Download Deals'"></i>
										  					
										  					<div class="dropdown-menu">
									  							<download-excel 
													  				class="btn waves-effect waves-dark btn-default btn-outline-default p-1 dropdown-item active"
																	:data="merchantAllDeals"
																	:fields="dataToExport" 
																	worksheet="Deals sheet"
																	:name="((searchAttributes.search != '' || searchAttributes.dateFrom || searchAttributes.dateTo) ? 'searched-deals-' : ('deals-list-')) + today + '-page-' + pagination.current_page + '.xls'"
													  			>
													  				Excel
																</download-excel>
										  						
										  						<!-- 
										  						<download-excel 
										  							type="csv"
													  				class="btn waves-effect waves-dark btn-default btn-outline-default p-1 dropdown-item disabled"
																	:data="merchantAllDeals"
																	:fields="dataToExport" 
																	worksheet="Deals sheet"
																	:name="((searchAttributes.search != '' || searchAttributes.dateFrom || searchAttributes.dateTo) ? 'searched-deals-' : (currentTab + '-deals-list-')) + today + '-page-' + pagination.current_page + '.xls'"
													  			>
													  				CSV
																</download-excel> 
																-->
										  					</div>
										  				</div>
											  		</div>

											  		<div class="col-md-8 col-sm-6 was-validated text-center d-flex align-items-center form-group">
											  			<div class="mx-sm-auto w-75">
										  					<input 	
																type="text" 
														  		class="form-control" 
														  		pattern="[^'!#$%^()\x22]+" 
														  		v-model="searchAttributes.search" 
														  		placeholder="Search Deals"
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
																		data-target="#deal-custom-search"
																		:class="{ 'active': searchAttributes.dates && Object.keys(searchAttributes.dates).length > 0 }"
																	>
																		<i class="fa fa-ellipsis-v fa-lg p-2"></i>
																	</a>
																</li>
															</ul>
													  	</div>
													</div>
											  	</div>

											  	<!-- 
											  	<search-and-addition-option 
											  		v-if="userHasPermissionTo('view-merchant-deal-index') || userHasPermissionTo('create-merchant-deal')"
											  		:query="query" 
											  		:caller-page="'deals'" 
											  		:required-permission="'merchant-deal'" 
											  		:disable-add-button="(allWarehouseSpaces.length==0 || formSubmitted) ? true : false" 									  
											  		@showContentCreateForm="showDealCreateForm" 
											  		@searchData="searchData($event)" 
											  		@fetchAllContents="fetchMerchantAllDeals"
											  	></search-and-addition-option> 
											  	-->
											</div>
											
											<div class="col-sm-12 col-lg-12">
												<loading v-show="loading"></loading>	

 												<div v-show="! loading">
													<div class="card">
														<div class="table-responsive">
															<table class="table table-striped table-bordered nowrap text-center">
																<thead>
																	<tr>
																		<th>Name</th>
																		<th>Status</th>
																		<th>Auto Renewal</th>
																		<th>Rent Package</th>
																		<th>Exp Date</th>
																		<th>Actions</th>
																	</tr>
																</thead>
																<tbody>
																	<tr 
																	v-for="merchantDeal in merchantAllDeals" 
																	:key="'merchant-deal-' + merchantDeal.id" 
																	:class="merchantDeal.id==myDealData.id ? 'highlighted' : ''"
																	>
																		<td>{{ merchantDeal.name | capitalize }}</td>

																		<td>
																			<span :class="[merchantDeal.active ? 'badge-primary' : 'badge-secondary', 'badge']">
																				{{ merchantDeal.active ? 'Active' : 'Inactive' }}
																			</span>
																		</td>
																		 
																		<td>
																			<span :class="[merchantDeal.auto_renewal ? 'badge-warning' : 'badge-secondary', 'badge']">
																				{{ merchantDeal.auto_renewal ? 'Enabled' : 'NA' }}
																			</span>
																		</td>	

																		<td>
																			{{ (merchantDeal.rent_period && merchantDeal.rent_period.name) ? merchantDeal.rent_period.name : 'No Package' | capitalize }}
																		</td>

																		<td v-if="merchantDeal.rents[merchantDeal.rents.length-1] && merchantDeal.rents[merchantDeal.rents.length-1].date_to">
																			{{ merchantDeal.rents[merchantDeal.rents.length-1].date_to }}
																			<span 
																			v-show="checkExpiryDate(merchantDeal.rents[merchantDeal.rents.length-1].date_to)"
																			:class="[checkExpiryDate(merchantDeal.rents[merchantDeal.rents.length-1].date_to) ? 'badge-danger' : 'badge-success', 'badge']"
																			>
																				{{ checkExpiryDate(merchantDeal.rents[merchantDeal.rents.length-1].date_to) ? 'Expired' : '' }}
																			</span>
																		</td>

																		<td v-else>
																			--
																		</td>
																		
																		<td>
																			<button 
																				type="button" 
																				class="btn waves-effect waves-dark btn-info btn-outline-info btn-icon" 
																				v-tooltip.bottom-end="'View Details'"  
																				@click="showDealDetails(merchantDeal)"
																			>
																				<i class="fa fa-eye"></i>
																			</button>

																			<button 
																				type="button" 
																				class="btn waves-effect waves-dark btn-warning btn-outline-warning btn-icon" 
																				v-tooltip.bottom-end="'Rents'" 
																				@click="goToDealRents(merchantDeal)" 
																			>
																				<img src="/icons/cms/deal-rents.png" width="22px">
																			</button>
																		</td>
																	</tr>

																	<tr 
																  		v-show="!merchantAllDeals.length"
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
																		<th>Name</th>
																		<th>Status</th>
																		<th>Auto Renewal</th>
																		<th>Rent Package</th>
																		<th>Exp Date</th>
																		<th>Actions</th>
																	</tr>
																</tfoot>
															</table>
														</div>
													</div>

													<div class="row d-flex align-items-center align-content-center">
														<div class="col-sm-2 col-4">
															<select 
																class="form-control" 
																v-model.number="perPage" 
																@change="changeNumberContents"
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
																@click="pagination.current_page = 1; query === '' ? fetchMerchantAllDeals() : searchData()"
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
																@paginate="query === '' ? fetchMerchantAllDeals() : searchData()"
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

 		<!-- Search Modal -->
		<div class="modal fade" id="deal-custom-search" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
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

 		<!-- View Modal -->
		<div class="modal fade" id="merchant-deal-view-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">{{ merchant.name }} Deal ({{ myDealData.name | capitalize }}) Details</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="modal-body">
						<div class="card">
							<div class="card-body">
								
								<ul class="nav nav-tabs tabs justify-content-center" role="tablist">
									<li class="nav-item">
										<a class="nav-link active" data-toggle="tab" href="#merchant-deal-profile" role="tab">
											Deal
										</a>
									</li>

									<li class="nav-item">
										<a class="nav-link" data-toggle="tab" href="#merchant-deal-spaces" role="tab">
											Spaces
										</a>
									</li>

									<li class="nav-item">
										<a class="nav-link" data-toggle="tab" href="#merchant-deal-rents" role="tab">
											Rents
										</a>
									</li>
								</ul>

								<div class="tab-content tabs card-block">
 									<div class="tab-pane active" id="merchant-deal-profile" role="tabpanel">
										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Merchant Name :
											</label>

											<label class="col-sm-6 col-form-label">
												{{ merchant ? fullName : 'NA' | capitalize }}
											</label>
										</div>

										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Deal Name :
											</label>

											<label class="col-sm-6 col-form-label">
												{{ myDealData.name | capitalize }}
											</label>
										</div>

										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Rent Package :
											</label>

											<label class="col-sm-6 col-form-label">
												{{ (myDealData.rent_period && myDealData.rent_period.name) ? myDealData.rent_period.name : 'NA' | capitalize }}
											</label>
										</div>
										
										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Status :
											</label>

											<label class="col-sm-6 col-form-label">
												<span :class="[myDealData.active ? 'badge-success' : 'badge-danger', 'badge']">
													{{ myDealData.active ? 'Active' : 'Inactive' }}
												</span>
											</label>
										</div>

										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Auto Renewal :
											</label>

											<label class="col-sm-6 col-form-label">
												<span :class="[myDealData.auto_renewal ? 'badge-warning' : 'badge-secondary', 'badge']">
													{{ myDealData.auto_renewal ? 'Enabled' : 'Disabled' }}
												</span>
											</label>
										</div> 

										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Created On :
											</label>

											<label class="col-sm-6 col-form-label">
												{{ myDealData.created_at }}
											</label>
										</div>
									</div>

									<div class="tab-pane" id="merchant-deal-spaces" role="tabpanel">
										<div 
											class="form-row" 
											v-if="myDealData.hasOwnProperty('warehouses') && Array.isArray(myDealData.warehouses) && myDealData.warehouses.length"
										>
											<label class="col-sm-12 col-form-label font-weight-bold">
												Dealt Spaces :
											</label>

											<div class="col-sm-12">
												<div 
													class="form-row" 
													v-for="(merchantWarehouse, merchantWarehouseIndex) in myDealData.warehouses" 
													:key="'rent-warehouse-' + merchantWarehouseIndex + '-warehouse-id-' + merchantWarehouse.id + '-view'"
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

																				<div class="form-row">
																					<label class="col-sm-6 col-form-label font-weight-bold text-right">
																						Status :
																					</label>
																					<label class="col-sm-6 col-form-label">
																						<span :class="[warehouseContainer.occupied==1 ? 'badge-danger' : warehouseContainer.occupied==0.5 ? 'badge-warning' : 'badge-success', 'badge']">
																							{{ warehouseContainer.occupied==1 ? 'Packed' : warehouseContainer.occupied==0.5 ? 'Used Partially' : 'Empty' }}
																						</span>
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
																								v-for="shelf in warehouseSpace.container.shelves" 
																								:key="'shelf-address-' + shelf.id"
																							>

																								{{ shelf.name ? shelf.name.substring(shelf.name.lastIndexOf("-")+1) : 'NA' }}
																								
																								<span :class="[shelf.occupied==1 ? 'badge-danger' : shelf.occupied==0.5 ? 'badge-warning' : 'badge-success', 'badge']">
																									{{ shelf.occupied==1 ? 'Packed' : shelf.occupied==0.5 ? 'Used Partially' : 'Empty' }}
																								</span>	
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
																								v-for="unit in warehouseSpace.container.shelf.units" 
																								:key="'unit-address-' + unit.id"
																							>

																								{{ unit.name ? unit.name.substring(unit.name.lastIndexOf("-")+1) : 'NA' }}
																								
																								<span :class="[unit.occupied==1 ? 'badge-danger' : unit.occupied==0.5 ? 'badge-warning' : 'badge-success', 'badge']">
																									{{ unit.occupied==1 ? 'Packed' : unit.occupied==0.5 ? 'Used Partially' : 'Empty' }}
																								</span>
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

									<div class="tab-pane" id="merchant-deal-rents" role="tabpanel">
										<div class="form-row" v-if="myDealData.hasOwnProperty('rents') && myDealData.rents.length">
											<div 
												class="col-md-12 card card-body" 
												v-for="(dealRent, dealRentIndex) in myDealData.rents" :key="'merchant-payment-index-' + dealRentIndex + '-merchant-payment-' + dealRent.id"
											>
												<div class="form-row">
													<label class="col-sm-6 col-form-label font-weight-bold text-right">
														Id :
													</label>

													<label class="col-sm-6 col-form-label">
														{{ dealRent.name | capitalize }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-sm-6 col-form-label font-weight-bold text-right">
														# Installment :
													</label>

													<label class="col-sm-6 col-form-label">
														{{ dealRent.number_installment }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-sm-6 col-form-label font-weight-bold text-right">
														Issued From :
													</label>

													<label class="col-sm-6 col-form-label">
														{{ dealRent.date_from }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-sm-6 col-form-label font-weight-bold text-right">
														Expiring At :
													</label>

													<label class="col-sm-6 col-form-label">
														{{ dealRent.date_to }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-sm-6 col-form-label font-weight-bold text-right">
														Total Rent :
													</label>

													<label class="col-sm-6 col-form-label">
														{{ dealRent.total_rent + ' ' + general_settings.official_currency_name | capitalize }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-sm-6 col-form-label font-weight-bold text-right">
														Discount :
													</label>

													<label class="col-sm-6 col-form-label">
														{{ dealRent.discount }} %
													</label>
												</div>

												<div class="form-row">
													<label class="col-sm-6 col-form-label font-weight-bold text-right">
														Net Payeble :
													</label>

													<label class="col-sm-6 col-form-label">
														{{ dealRent.net_payable + ' ' + general_settings.official_currency_name | capitalize }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-sm-6 col-form-label font-weight-bold text-right">
														Paid Amount :
													</label>

													<label class="col-sm-6 col-form-label">
														{{ dealRent.total_paid_amount + ' ' + general_settings.official_currency_name | capitalize }} 
													</label>
												</div>

												<div class="form-row">
													<label class="col-sm-6 col-form-label font-weight-bold text-right">
														Due :
													</label>

													<label class="col-sm-6 col-form-label">
														{{ (dealRent.net_payable - dealRent.total_paid_amount) + ' ' + general_settings.official_currency_name | capitalize }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-sm-6 col-form-label font-weight-bold text-right">
														Created at :
													</label>

													<label class="col-sm-6 col-form-label">
														{{ dealRent.created_at }}
													</label>
												</div>

												<div class="form-row" v-if="dealRent.hasOwnProperty('spaceRents') && dealRent.spaceRents.length">
													<div 
														class="col-md-6" 
														v-for="(rent, rentIndex) in dealRent.spaceRents" 
														:key="'merchant-payment-' + dealRentIndex + '-rent-index-' + rentIndex + '-rent-' + rent.id"
													>
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
																	Rent :
																</label>

																<label class="col-sm-6 col-form-label">
																	{{ rent.rent }}
																</label>
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
													No Deal Found.
												</p>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="modal-footer">
						<button type="button" class="btn waves-effect waves-dark btn-secondary btn-outline-secondary btn-sm btn-block" data-dismiss="modal">
							Close
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript">

	import axios from 'axios';
	// import Multiselect from 'vue-multiselect';
	import Datepicker from 'vuejs-datepicker';

	export default {

	    components: { 
	    	Datepicker,
			// multiselect : Multiselect,
		},

	    data() {

	        return {

	        	query : '',
	        	error : '',
    			perPage : 10,
	        	loading : false,

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

	        	merchantAllDeals : [],
	        	
	        	pagination: {
		        	current_page: 1
		      	},

	        	myDealData : {

	        		active : true,
	        		auto_renewal : true, // Renting is normally auto-renewal
	        		rent_period : {},
	        		rent_period_id : null,
					merchant_id : null,
					// created_at : null,

					warehouses : [
						{							
							id : null,
							name : null,
							
							/*
								emptyContainers : [
										{
											id : 1,
											name : null,  // cnt-1 / cnt-2
											rents:[
												{
													id: null,
													name : null, // daily / monthly
													rent : 1,
													selling_price :1
												}
											]
										},
								],
							
								emptyContainerShelve : [
										{
											container:{
												id:null,
												name:null,  // cnt-1 / cnt-2
												shelves:[
													{
														id:null,
														name : null  // cnt-1-shl-1 / cnt-2-shl-2
													}
												]
											},
											rents:[
												{
													id:null,
													name:null,   // daily / monthly
													rent:null,
													selling_price:null
												}
											]
										},
								],
							
								emptyContainerShefUnits : [
										{
											container:{
												id:null,
												name:null,
												shelf:{
													id:null,
													name:null,		// cnt-1-shl-1 / cnt-2-shl-2
													units : [
														{
															id : null,
															name : null		// cnt-1-shl-1-unt-1 / cnt-2-shl-2unt-1
														}
													]
												}
											},
											rents:[
												{
													id : null,
													name : null,	   // daily / monthly
													rent : null,
													selling_price : null
												}
											]
										}
								],
							*/

							spaces : [
								/*
									{
										type:'containers',
										containers : [
											{
												id : null,
												name:null,
												rents:[
													{
														id : null,
														name : null,
														rent : null,
														selling_price : null
													},
												],
												selected_rent:{
													id : null,
													name : null,
													rent : null,
													selling_price : null
												},
												rent_period_id : null,
											},
										]
									},
									{
										type : 'shelves',
										container : {
											id : null,
											name : null,
											shelves : [
												{
													id : null,
													name : null
												}
											],
											rents:[
												{
													id : null,
													name : null,
													rent : null,
													selling_price : null
												}
											],
											selected_rent:{
												id : null,
												name : null,
												rent : null,
												selling_price : null
											},
											rent_period_id : null
										},
									},
									{
										type : 'units',
										container:{
											id:1,
											name:Name,
											shelf:{
												id:1,
												name:Name,
												units:[
													{
														id:1,
														name:name
													}
												]
											},
											rents:[
												{
													id:null,
													name:null,
													rent:null,
													selling_price :null
												}
											],
											selected_rent : {
												id:null,
												name:null,
												rent:null,
												selling_price:null
											},
											rent_period_id : 1
										},
									}
								*/
							],
						},
					],

					rents : [
						{
							number_installment : 1,	
							date_from : null,
							date_to : null,
							total_rent : 0, // generated from selected spaces
							discount : 0,	// percentage 
							net_payable : 0,
							total_paid_amount : 0,
							merchant_deal_id : null,
							paid_at : null,
							
							rents : [
								{
									rent : 0,
									dealt_space_id : null,
									merchant_payment_id : null
								},
							]
						},
					]
	        	},

				dataToExport: {

					Deal: 'name',

					"Status": {
						
						field: "active",
						
						callback: (value) => {
							
							if (value) {
								return 'Active';
							}
							else {
								return 'Deactive';
							}

						},

					},

					"Renewal": {
						
						field: "auto_renewal",
						
						callback: (value) => {
							
							if (value) {
								return 'Auto-Renewal';
							}
							else {
								return 'NA';
							}

						},

					},

					"Rent Package": {
						
						field: "rent_period",
						
						callback: (rent_period) => {
							
							if (rent_period && rent_period.name) {
								return this.$options.filters.capitalize(rent_period.name);
							}
							else {
								return 'No Package';
							}

						},

					},

					"Spaces": {

						field: "warehouses",

						callback: (warehouses) => {

							if (warehouses) {
								
								var infosToReturn = '';

								warehouses.forEach(
					
									(merchantWarehouse, merchantWarehouseIndex) => {

										infosToReturn += "Warehouse : " + merchantWarehouse.name + "\n";

										if (merchantWarehouse.hasOwnProperty('spaces') && merchantWarehouse.spaces.length) {

											merchantWarehouse.spaces.forEach(
						
												(warehouseSpace, paymentRentIndex) => {

													if (warehouseSpace.type=='containers') {
														
														infosToReturn +=  "Space Type : Containers\n";

														warehouseSpace.containers.forEach(
															(warehouseContainer, warehouseContainerIndex) => {

																infosToReturn +=  "Containers: " + "\n";
																infosToReturn +=  (warehouseContainerIndex+1) + '. ' + this.$options.filters.capitalize(warehouseContainer.name) + ", \n"

															}
														);

													}
													else if (warehouseSpace.type=='shelves') {
														
														infosToReturn +=  "Space Type : Shelves\n";
														infosToReturn +=  "Container Name : " + this.$options.filters.capitalize(warehouseSpace.container.name) + "\n";

														infosToReturn +=  "Shelves: \n";
														
														warehouseSpace.container.shelves.forEach(
															(warehouseShelf, warehouseShelfIndex) => {

																infosToReturn +=  (warehouseShelfIndex+1) + '. ' + this.$options.filters.capitalize(warehouseShelf.name) + ", \n";

															}
														);

													}
													else if (warehouseSpace.type=='units') {
														
														infosToReturn +=  "Space Type : Units\n";
														infosToReturn +=  "Container Name : " + this.$options.filters.capitalize(warehouseSpace.container.name) + "\n";
														infosToReturn +=  "Shelf Name : " + this.$options.filters.capitalize(warehouseSpace.container.shelf.name) + "\n";
														infosToReturn +=  "Units: " + "\n";

														warehouseSpace.container.shelf.units.forEach(
															(warehouseUnit, warehouseUnitIndex) => {

																infosToReturn +=  (warehouseUnitIndex+1) + '. ' + this.$options.filters.capitalize(warehouseUnit.name) + ", \n";

															}
														);

													}				

												}
												
											);

										}

									}
									
								);

								return infosToReturn;

							}
							else {
								return 'No Deal.'
							}

						},

					},

					"Expiring Date" : {

						field: "rents",
						
						callback: (rents) => {

							return rents[rents.length-1].date_to;

						}

					},
					
				},

				merchant : JSON.parse(window.localStorage.getItem('merchant')),

				general_settings : JSON.parse(window.localStorage.getItem('general_settings')),

	            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

	        }

		},

		computed: {
			// a computed getter
			fullName: function () {
				
				return `${this.merchant.first_name} ${this.merchant.last_name} ( ${this.merchant.user_name} )`;
			},
			today: function() {

				let date = new Date();
				return date.getFullYear() + '/' +  (date.getMonth() + 1) + '/' + date.getDate();

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

		watch : {

			'searchAttributes.search' : function(val){
				
				this.pagination.current_page = 1;

				if (this.searchAttributes.search==='' && ! this.searchAttributes.dateTo && ! this.searchAttributes.dateFrom) {

					this.fetchMerchantAllDeals();

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

					this.fetchMerchantAllDeals();

				}
				else {

					this.searchData();
						
				}

			},

			'searchAttributes.dateTo' : function(val){
				
				this.pagination.current_page = 1;

				if (this.searchAttributes.search==='' && ! this.searchAttributes.dateTo && ! this.searchAttributes.dateFrom) {

					this.fetchMerchantAllDeals();

				}
				else {

					this.searchData();
						
				}

			},

		},

		created() {

			this.fetchMerchantAllDeals();

		},
		
		methods: {

			fetchMerchantAllDeals() {

				this.query = '';
				this.error = '';
				this.loading = true;
				this.merchantAllDeals = [];
				
				axios
					.get('/api/my-space-deals/' + this.perPage + "?page=" + this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							// console.log(response);
							this.setMerchantDealsPagination(response);
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
    		showDealDetails(object) {		
				// this.myDealData = { ...object };
				this.myDealData = Object.assign({}, this.myDealData, object);
				$('#merchant-deal-view-modal').modal('show');
			},
			goToDealRents(object) {

				// console.log(object);
				this.$router.push({ name: 'my-space-deal-rents', params: { deal:object, dealId:object.id }});

			},
			searchData() {

				this.error = '';
				this.merchantAllDeals = [];
				// this.pagination.current_page = 1;
				// this.searchAttributes.merchant_id = this.merchant.id;
				
				axios
				.post('/search-my-space-deals/' + this.perPage + "?page=" + this.pagination.current_page, this.searchAttributes)
				.then(response => {
					this.merchantAllDeals = response.data.all.data;
					this.pagination = response.data.all;
				})
				.catch(e => {
					this.error = e.toString();
				});

			},
			changeNumberContents() {
				
				this.pagination.current_page = 1;

				if (this.searchAttributes.search === '') {

					this.fetchMerchantAllDeals();

				}
				else {

					this.searchData();

				}

    		},
    		checkExpiryDate(date){

    			var givenDate = new Date(date);
				var currentDate = new Date(this.today);

    			return givenDate < currentDate;

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
			customFormatter(date) {
				var moment = require('moment');
				date = moment(date).format('YYYY-MM-DD');
		      	return date;
		    },
		    setMerchantDealsPagination(response) {

				this.pagination = response.data;
				this.merchantAllDeals = response.data.data;

			},
            
		}
  	}

</script>

<style scoped>
	.date {
		background: #f2f2f2;
		border: 1px solid #ddd;
		padding: 1em 1em 1em;
	}
</style>