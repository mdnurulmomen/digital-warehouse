
<template v-if="userHasPermissionTo('view-merchant-deal-index')">

	<div class="pcoded-content">

		<breadcrumb 
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
									  						<i class="fas fa-download fa-lg dropdown-toggle" data-toggle="dropdown"></i>
										  					
										  					<div class="dropdown-menu">
									  							<download-excel 
													  				class="btn btn-default p-1 dropdown-item active"
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
													  				class="btn btn-default p-1 dropdown-item disabled"
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

											  			<div class="ml-auto d-sm-none">
											  				<button 
											  					v-if="userHasPermissionTo('create-merchant-deal')"
													  			class="btn btn-success btn-outline-success btn-sm" 
													  			data-toggle="tooltip" data-placement="top" title="Create New" 
													  			@click="showContentCreateForm()"
												  			>
												  				<i class="fa fa-plus"></i>
												  				New Deal
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
																		data-target="#payment-custom-search"
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
										  					v-if="userHasPermissionTo('create-merchant-deal')"
												  			class="btn btn-success btn-outline-success btn-sm" 
												  			data-toggle="tooltip" data-placement="top" title="Create New" 
												  			@click="showDealCreateForm()"
											  			>
											  				<i class="fa fa-plus"></i>
											  				New Deal
											  			</button>
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
											  		@fetchAllContents="fetchAllMerchantDeals"
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
																		<th>E-cmmrc support</th>
																		<th>Auto Renewal</th>
																		<th>Rent Package</th>
																		<th>Actions</th>
																	</tr>
																</thead>
																<tbody>
																	<tr v-for="merchantDeal in merchantAllDeals" :key="'merchant-deal-' + merchantDeal.id"
																	>
																		<td>{{ merchantDeal.name | capitalize }}</td>

																		<td>
																			<span :class="[merchantDeal.active ? 'badge-primary' : 'badge-secondary', 'badge']">
																				{{ merchantDeal.active ? 'Active' : 'Inactive' }}
																			</span>
																		</td>

																		<td>
																			<span :class="[merchantDeal.e_commerce_fulfillment ? 'badge-info' : 'badge-secondary', 'badge']">
																				{{ merchantDeal.e_commerce_fulfillment ? 'Enabled' : 'NA' }}
																			</span>
																		</td>
																		 
																		<td>
																			<span :class="[merchantDeal.auto_renewal ? 'badge-warning' : 'badge-secondary', 'badge']">
																				{{ merchantDeal.auto_renewal ? 'Enabled' : 'NA' }}
																			</span>
																		</td>	

																		<td>
																			{{ (merchantDeal.rent_period && merchantDeal.rent_period.name) ? merchantDeal.rent_period.name : 'No Package' }}
																		</td>
																		
																		<td>
																			<button 
																				type="button" 
																				class="btn btn-grd-info btn-icon" 
																				data-toggle="tooltip" data-placement="top" title="View Details"  
																				@click="showDealDetails(merchantDeal)"
																			>
																				<i class="fa fa-eye"></i>
																			</button>

																			<button 
																				type="button" 
																				class="btn btn-grd-primary btn-icon" 
																				data-toggle="tooltip" data-placement="top" title="Edit"  
																				@click="openDealEditForm(merchantDeal)" 
																				v-if="userHasPermissionTo('update-merchant-deal')" 
																				:disabled="formSubmitted"  
																			>
																				<i class="fa fa-edit"></i>
																			</button>

																			<button 
																				type="button" 
																				class="btn btn-grd-danger btn-icon" 
																				data-toggle="tooltip" data-placement="top" title="Delete" 
																				:disabled="formSubmitted || ! removableDeal(merchantDeal)"  
																				@click="openDealDeleteForm(merchantDeal)" 
																				v-if="userHasPermissionTo('delete-merchant-deal')" 
																			>
																				<i class="fa fa-trash"></i>
																			</button>

																			<button 
																				type="button" 
																				class="btn btn-grd-warning btn-icon" 
																				data-toggle="tooltip" data-placement="top" title="Payments" 
																				:disabled="formSubmitted"  
																				@click="goToDealPayments(merchantDeal)" 
																				v-if="userHasPermissionTo('view-merchant-payment-index')" 
																			>
																				<i class="fa fa-money" aria-hidden="true"></i>
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
																		<th>E-cmmrc support</th>
																		<th>Auto Renewal</th>
																		<th>Rent Package</th>
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
																class="btn btn-primary btn-sm" 
																data-toggle="tooltip" data-placement="top" title="Reload" 
																@click="query === '' ? fetchAllMerchantDeals() : searchData()"
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
																@paginate="query === '' ? fetchAllMerchantDeals() : searchData()"
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
		<div class="modal fade" id="merchantDeal-createOrEdit-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" v-if="userHasPermissionTo('create-merchant-deal') || userHasPermissionTo('update-merchant-deal')">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">
							{{ createMode ? 'Create New ' : 'Update ' }} Deal
						</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
						
					<form 	
						class="form-horizontal" 
						v-on:submit.prevent="createMode ? createMerchantDeal() : updateMerchantDeal()" 
						autocomplete="off" 
						novalidate="true" 
					>
						<input type="hidden" name="_token" :value="csrf">

						<div class="modal-body">
							<transition-group name="fade">		
								<div 
									class="row" 
									v-bind:key="'merchant-deal-modal-step-' + 1" 
									v-show="!loading && step==1"
								>
									<h2 class="mx-auto mb-4 lead">Deal Profile</h2>

									<div class="col-md-12">
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
												E-Commerce Fullfillment :
											</label>
											<div class="col-sm-6">
												<toggle-button 
													v-model="singleMerchantDealData.e_commerce_fulfillment" 
													:width=150 
													:sync="true"
													:color="{checked: '#229bbf', unchecked: '#6c757d'}"
													:labels="{checked: 'Enabled', unchecked: 'Disabled'}"  
												/>
											</div>
										</div>
										
										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Auto Renewal :
											</label>
											<div class="col-sm-6">
												<toggle-button 
													v-model="singleMerchantDealData.auto_renewal" 
													:width=150 
													:sync="true"
													:color="{checked: '#fcba03', unchecked: '#6c757d'}"
													:labels="{checked: 'Auto Renew', unchecked: 'No Renew'}"  
												/>
											</div>
										</div> 

										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Status :
											</label>
											<div class="col-sm-6">
												<toggle-button 
													v-model="singleMerchantDealData.active" 
													:width=150 
													:sync="true"
													:color="{checked: 'green', unchecked: 'red'}"
													:labels="{checked: 'Active', unchecked: 'Deactive'}"  
												/>
											</div>
										</div>

										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Percentage :
											</label>
											<div class="col-sm-6">
												<div class="input-group mb-1">
													<input type="number" 
														class="form-control" 
														v-model.number="singleMerchantDealData.sale_percentage" 
														placeholder="Sale Percentage" 
														:min="0" 
														:max="100" 
														:class="! errors.sale_percentage ? 'is-valid' : 'is-invalid'" 
														:disabled="! singleMerchantDealData.e_commerce_fulfillment"
														@change="validateFormInput('sale_percentage')" 
													>
													<div class="input-group-append">
														<span class="input-group-text"> % </span>
													</div>
												</div>
												<div 
													class="invalid-feedback" 
													style="display:block" 
													v-show="errors.sale_percentage"
												>
											    	{{ errors.sale_percentage }}
											    </div>
											</div>
										</div>
									</div>

									<div class="col-md-12 card-footer mt-4">
										<div class="form-row">
									    	<div class="form-group col-sm-12 text-right">
								          		<div class="text-danger small mb-1" v-show="!submitForm">
											  		Please input required fields
									          	</div>

									          	<button 
										          	type="button" 
										          	v-on:click="nextPage"
										          	class="btn btn-outline-secondary btn-sm btn-round" 
										          	data-toggle="tooltip" data-placement="top" title="Next" 
										          	v-show="singleMerchantDealData.payments.length < 2"
									          	>
							                    	<i class="fa fa-2x fa-angle-double-right" aria-hidden="true"></i>
							                  	</button>

							                  	<button 
													type="submit" 
													class="btn btn-primary float-right" 
													:disabled="!submitForm || formSubmitted"
													v-show="! createMode && singleMerchantDealData.payments.length > 1"
												>
													{{ createMode ? 'Make ' : 'Update ' }} Deal
												</button>
								          	</div>
								    	</div>
									</div>
							    </div>
						     
							    <div 
									class="row" 
									v-bind:key="'merchant-deal-modal-step-' + 2" 
									v-show="! loading && step==2 && singleMerchantDealData.payments.length==1" 
								>
									<h2 class="mx-auto mb-4 lead">Rent Space</h2>

									<div class="col-md-12">
										<div class="form-row">
											<div class="col-md-12 form-group">
												<label for="inputFirstName">
													Rent Package
												</label>

												<multiselect 
													v-model="singleMerchantDealData.rent_period"
													:options="allRentPeriods" 
													:custom-label="objectNameWithCapitalized" 
													:required="true" 
													:allow-empty="false" 
													placeholder="Select Rent Period" 
													class="form-control p-0" 
													:class="! errors.rent_period ? 'is-valid' : 'is-invalid'"  
													@close="validateFormInput('rent_period')" 
													@input="resetRentPackage()" 
													:disabled="singleMerchantDealData.payments.length > 1"
												>
												</multiselect>

												<div class="invalid-feedback">
													{{ errors.rent_period }}
												</div>
											</div>
										</div>
									</div>

									<div 
										class="col-md-12"
										v-for="(merchantWarehouse, merchantWarehouseIndex) in singleMerchantDealData.warehouses" 
										:key="'rent-warehouse-' + merchantWarehouseIndex + '-warehouse-id-' + merchantWarehouse.id"
									>
										<div 
											class="card ml-3 mr-3"
											v-if="merchantWarehouse && errors.warehouses[merchantWarehouseIndex]"
										>
											<div class="card-body">
												<div class="form-row ml-3 mr-3">
													<div class="form-group col-md-12 text-center">
														<label for="inputUsername">Select Warehouse</label>

														<multiselect 
															v-model="singleMerchantDealData.warehouses[merchantWarehouseIndex]"
															:options="availableWarehouseSpaces" 
															:custom-label="objectNameWithCapitalized" 
															:required="true" 
															:allow-empty="false" 
															placeholder="Select Warehouse" 
															class="form-control p-0" 
															:class="! errors.warehouses[merchantWarehouseIndex].warehouse ? 'is-valid' : 'is-invalid'"  
															@close="validateFormInput('warehouse')" 
															@input="resetWarehouseSpaces(merchantWarehouseIndex)" 
															:disabled="singleMerchantDealData.warehouses.length > (merchantWarehouseIndex + 1) || singleMerchantDealData.payments.length > 1 || immutableWarehouse(merchantWarehouse) || ! singleMerchantDealData.hasOwnProperty('rent_period') || Object.keys(singleMerchantDealData.rent_period).length < 1"
														>
														</multiselect>

														<div class="invalid-feedback">
															{{ errors.warehouses[merchantWarehouseIndex].warehouse }}
														</div>
													</div>
												</div>

												<div 
													class="form-row ml-5 mr-5"
													v-if="merchantWarehouse && merchantWarehouse.hasOwnProperty('spaces') && Array.isArray(merchantWarehouse.spaces) && merchantWarehouse.spaces.length==errors.warehouses[merchantWarehouseIndex].spaces.length"
												>
													<div 
														class="col-md-12" 
														v-for="(warehouseSpace, warehouseSpaceIndex) in merchantWarehouse.spaces" 
														:key="'rent-warehouse-' + merchantWarehouseIndex + '-warehouse-id-' + merchantWarehouse.id + '-space-' + warehouseSpaceIndex"
													>
														<div class="form-row card card-body mb-3">
															<div 
																class="col-md-12 text-center mb-2"
															>
																<label for="inputFirstName">
																	Required Space Type {{ warehouseSpaceIndex + 1 }}
																</label>

																<multiselect 
																	v-model="warehouseSpace.type"
																	:options="['containers', 'shelves', 'units']" 
																	:custom-label="nameWithCapitalized" 
																	:required="true" 
																	:allow-empty="false"
																	placeholder="Containers / Shelves / Units" 
																	class="form-control p-0" 
																	:class="! errors.warehouses[merchantWarehouseIndex].spaces[warehouseSpaceIndex].space_type ? 'is-valid' : 'is-invalid'" 
																	
																	:disabled="singleMerchantDealData.warehouses.length > (merchantWarehouseIndex + 1) || merchantWarehouse.spaces.length > (warehouseSpaceIndex + 1) || singleMerchantDealData.payments.length > 1 || ! removableSpace(warehouseSpace)" 

																	@input="setWarehouseSpaces(merchantWarehouseIndex, warehouseSpaceIndex)" 
																	@close="validateFormInput('space_type')"
																>
																</multiselect>
																
																<div class="invalid-feedback">
																	{{ errors.warehouses[merchantWarehouseIndex].spaces[warehouseSpaceIndex].space_type }}
																</div>														
															</div>

															<div class="col-md-12 mt-1">
																<div 
																	class="form-row ml-3 mr-3" 
																	v-show="warehouseSpace.type=='containers'"
																>
																	<div 
																		class="col-md-12" 
																		v-if="warehouseSpace.containers && Array.isArray(warehouseSpace.containers) && warehouseSpace.containers.length"
																	>
																		<div 
																			class="form-row mb-1" 
																			v-for="(warehouseContainer, warehouseContainerIndex) in warehouseSpace.containers"
																			:key="'rent-warehouse-' + merchantWarehouseIndex + '-warehouse-id-' + merchantWarehouse.id + '-space-' + warehouseSpaceIndex + '-container-' + warehouseContainerIndex + '-container-id-' + warehouseContainer.id"
																		>
																			<div class="form-group col-md-12">
																				<label for="inputFirstName">Container {{ warehouseContainerIndex + 1 }}</label>

																				<multiselect 
																					v-model="warehouseSpace.containers[warehouseContainerIndex]"
																					placeholder="Select Container" 
																					label="name" 
																					track-by="id" 
																					:options="emptyContainers" 
																					:clear-on-select="false" 
																					:preserve-search="true" 
																					:required="true" 
																					:allow-empty="false" 
																					class="form-control p-0" 
																					:class="! errors.warehouses[merchantWarehouseIndex].spaces[warehouseSpaceIndex].containers ? 'is-valid' : 'is-invalid'" 
																					:disabled="singleMerchantDealData.warehouses.length > (merchantWarehouseIndex+1) || merchantWarehouse.spaces.length > (warehouseSpaceIndex + 1) || warehouseSpace.containers.length > (warehouseContainerIndex + 1) || singleMerchantDealData.payments.length > 1 || (warehouseContainer.hasOwnProperty('occupied') && warehouseContainer.occupied != 0)" 
																					@input="setContainerRentPeriod(warehouseSpace.containers[warehouseContainerIndex])" 
																					@close="validateFormInput('containers')" 
																				>
																				</multiselect>

																				<div class="invalid-feedback">
																					{{ errors.warehouses[merchantWarehouseIndex].spaces[warehouseSpaceIndex].containers }}
																				</div>
																			</div>

																			<!-- 
																			<div class="form-group col-md-6" v-if="warehouseContainer && Array.isArray(warehouseContainer.rents)">
																				<label for="inputFirstName">Rent Package</label>

																				<multiselect 
																					v-model="warehouseSpace.containers[warehouseContainerIndex].selected_rent"
																					placeholder="Select Rent Periods" 
																					label="name" 
																					track-by="id" 
																					:options="warehouseContainer.rents" 
																					:custom-label="objectNameWithCapitalized" 
																					:clear-on-select="false" 
																					:preserve-search="true" 
																					:required="true" 
																					:allow-empty="false" 
																					class="form-control p-0" 
																					:class="! errors.warehouses[merchantWarehouseIndex].spaces[warehouseSpaceIndex].space_rent_period ? 'is-valid' : 'is-invalid'" 
																					:disabled="singleMerchantDealData.warehouses.length > (merchantWarehouseIndex+1) || merchantWarehouse.spaces.length > (warehouseSpaceIndex + 1) || warehouseSpace.containers.length > (warehouseContainerIndex + 1) || singleMerchantDealData.payments.length > 1 || (warehouseContainer.hasOwnProperty('occupied') && warehouseContainer.occupied != 0)"
																					@close="validateFormInput('space_rent_period')" 
																				>
																				</multiselect>
																				
																				<div class="invalid-feedback">
																					{{ errors.warehouses[merchantWarehouseIndex].spaces[warehouseSpaceIndex].space_rent_period }}
																				</div>
																			</div> 
																			-->
																		</div>
																	</div>

																	<div class="col-md-12 text-center" v-else>
																		<p class="text-danger">
																			{{ errors.warehouses[merchantWarehouseIndex].spaces[warehouseSpaceIndex].containers }}
																		</p>
																	</div>

																	<div class="col-md-12 card-footer mt-2">
																		<div class="form-row text-center">
																			<div class="col-md-6">
																				<button 
																					class="btn waves-effect waves-dark btn-success btn-outline-success btn-icon"
																					data-toggle="tooltip" data-placement="top" title="Add Container"  
																					:disabled="singleMerchantDealData.warehouses.length > merchantWarehouseIndex + 1 || merchantWarehouse.spaces.length > (warehouseSpaceIndex + 1)" 
																					@click="addWarehouseContainers(merchantWarehouseIndex, warehouseSpaceIndex)"
																				>
																					<i 
																						class="fa fa-plus fa-lg" 
																						aria-hidden="true"
																					>	
																					</i>
																				</button>
																			</div>

																			<div class="col-md-6">
																				<button 
																					class="btn waves-effect waves-dark btn-danger btn-outline-danger btn-icon" 
																					data-toggle="tooltip" data-placement="top" title="Remove Container" 
																					:disabled="singleMerchantDealData.warehouses.length > merchantWarehouseIndex + 1 || ! warehouseSpace.spaces || (warehouseSpace.containers && immutableContainer(warehouseSpace.containers[warehouseSpace.containers.length-1]))|| merchantWarehouse.spaces.length > (warehouseSpaceIndex + 1) || (warehouseSpace.containers && warehouseSpace.containers.length==1)" 
																					@click="removeWarehouseContainers(merchantWarehouseIndex, warehouseSpaceIndex)"
																				>
																					<i 
																						class="fa fa-minus fa-lg" 
																						aria-hidden="true" 
																					>	
																					</i>
																				</button>
																			</div>
																		</div>
																	</div>
																</div>
															</div>

															<div class="col-md-12 mt-1">
																<div 
																	class="form-row ml-3 mr-3" 
																	v-show="warehouseSpace.type=='shelves'"
																>
																	<div class="form-group col-md-6">
																		<label for="inputFirstName">Parent Container</label>

																		<multiselect 
																			v-model="warehouseSpace.container"
																			placeholder="Parent Container" 
																			label="name" 
																			track-by="id" 
																			:options="emptyShelfContainers" 
																			:required="true" 
																			:allow-empty="false" 
																			class="form-control p-0" 
																			:class="! errors.warehouses[merchantWarehouseIndex].spaces[warehouseSpaceIndex].parent_container ? 'is-valid' : 'is-invalid'" 
																			:disabled="singleMerchantDealData.warehouses.length > (merchantWarehouseIndex+1) || merchantWarehouse.spaces.length > (warehouseSpaceIndex + 1) || singleMerchantDealData.payments.length > 1 || immutableShelf(warehouseSpace.container)"
																			@input="setContainerAvailableShelves(merchantWarehouseIndex, warehouseSpaceIndex)"
																			@close="validateFormInput('parent_container')" 
																		>
																		</multiselect>

																		<div class="invalid-feedback">
																			{{ errors.warehouses[merchantWarehouseIndex].spaces[warehouseSpaceIndex].parent_container }}
																		</div>
																	</div>

																	<div 
																		class="form-group col-md-6" 
																		v-if="warehouseSpace.container"
																	>
																		<label for="inputFirstName">Select Shelves</label>

																		<multiselect 
																			v-model="warehouseSpace.container.shelves"
																			placeholder="Select Shelves" 
																			label="name" 
																			track-by="id" 
																			:options="emptyShelves" 
																			:multiple="true" 
																			:close-on-select="false" 
																			:clear-on-select="false" 
																			:preserve-search="true" 
																			:required="true" 
																			:allow-empty="false" 
																			class="form-control p-0" 
																			:class="! errors.warehouses[merchantWarehouseIndex].spaces[warehouseSpaceIndex].shelves ? 'is-valid' : 'is-invalid'" 
																			:disabled="singleMerchantDealData.warehouses.length > (merchantWarehouseIndex+1) || merchantWarehouse.spaces.length > (warehouseSpaceIndex + 1) || singleMerchantDealData.payments.length > 1 || immutableShelf(warehouseSpace.container)" 
																			@close="validateFormInput('shelves')" 
																		>
																		</multiselect>

																		<div class="invalid-feedback">
																			{{ errors.warehouses[merchantWarehouseIndex].spaces[warehouseSpaceIndex].shelves }}
																		</div>
																	</div>

																	<div 
																		class="form-group col-md-6" 
																		v-else
																	>
																		<label for="inputFirstName">Select Shelves</label>
																		<p class="text-danger">No Container Selected</p>
																	</div>

																	<!-- 
																	<div 
																		class="form-group col-md-4" 
																		v-if="warehouseSpace.container && Array.isArray(warehouseSpace.container.rents)"
																	>
																		<label for="inputFirstName">Rent Package</label>

																		<multiselect 
																			v-model="warehouseSpace.container.selected_rent"
																			placeholder="Select Rent Periods" 
																			label="name" 
																			track-by="id" 
																			:options="warehouseSpace.container.rents" 
																			:custom-label="objectNameWithCapitalized" 
																			:clear-on-select="false" 
																			:preserve-search="true" 
																			:required="true" 
																			:allow-empty="false" 
																			class="form-control p-0" 
																			:class="! errors.warehouses[merchantWarehouseIndex].spaces[warehouseSpaceIndex].space_rent_period  ? 'is-valid' : 'is-invalid'" 
																			:disabled="singleMerchantDealData.warehouses.length > (merchantWarehouseIndex+1) || merchantWarehouse.spaces.length > (warehouseSpaceIndex + 1) || singleMerchantDealData.payments.length > 1 || immutableShelf(warehouseSpace.container)"
																			@close="validateFormInput('space_rent_period')" 
																		>
																		</multiselect>

																		<div class="invalid-feedback">
																			{{ errors.warehouses[merchantWarehouseIndex].spaces[warehouseSpaceIndex].space_rent_period }}
																		</div>
																	</div> 
																	-->
																</div>
															</div>

															<div class="col-md-12 mt-1">
																<div class="form-row ml-3 mr-3" v-show="warehouseSpace.type=='units'">
																	<div class="form-group col-md-4">
																		<label for="inputFirstName">Parent Container</label>

																		<multiselect 
																			v-model="warehouseSpace.container"
																			placeholder="Parent Container" 
																			label="name" 
																			track-by="id" 
																			:options="emptyUnitContainers" 
																			:required="true" 
																			:allow-empty="false" 
																			class="form-control p-0" 
																			:class="! errors.warehouses[merchantWarehouseIndex].spaces[warehouseSpaceIndex].parent_container  ? 'is-valid' : 'is-invalid'" 
																			:disabled="singleMerchantDealData.warehouses.length > (merchantWarehouseIndex+1) || merchantWarehouse.spaces.length > (warehouseSpaceIndex + 1) || singleMerchantDealData.payments.length > 1 || immutableUnit(warehouseSpace.container)"
																			@input="setContainerAvailableUnitShelves(merchantWarehouseIndex, warehouseSpaceIndex)" 
																			@close="validateFormInput('parent_container')" 
																		>
																		</multiselect>

																		<div class="invalid-feedback">
																			{{ errors.warehouses[merchantWarehouseIndex].spaces[warehouseSpaceIndex].parent_container }}
																		</div>
																	</div>

																	<div 
																		class="form-group col-md-4" 
																		v-if="warehouseSpace.container"
																	>
																		<label for="inputFirstName">Parent Shelf</label>

																		<multiselect 
																			v-model="warehouseSpace.container.shelf"
																			placeholder="Parent Shelf" 
																			label="name" 
																			track-by="id" 
																			:options="emptyUnitShelves" 
																			:required="true" 
																			:allow-empty="false" 
																			class="form-control p-0" 
																			:class="! errors.warehouses[merchantWarehouseIndex].spaces[warehouseSpaceIndex].parent_shelf  ? 'is-valid' : 'is-invalid'" 
																			:disabled="singleMerchantDealData.warehouses.length > (merchantWarehouseIndex+1) || merchantWarehouse.spaces.length > (warehouseSpaceIndex + 1) || singleMerchantDealData.payments.length > 1 || immutableUnit(warehouseSpace.container)"
																			@input="setContainerShelfAvailableUnits(merchantWarehouseIndex, warehouseSpaceIndex)" 
																			@close="validateFormInput('parent_shelf')" 
																		>
																		</multiselect>

																		<div class="invalid-feedback">
																			{{ errors.warehouses[merchantWarehouseIndex].spaces[warehouseSpaceIndex].parent_shelf }}
																		</div>
																	</div>

																	<div 
																		class="form-group col-md-4" 
																		v-if="warehouseSpace.container && warehouseSpace.container.shelf"
																	>
																		<label for="inputFirstName">
																			Select Units
																		</label>
																		
																		<multiselect 
																			v-model="warehouseSpace.container.shelf.units"
																			placeholder="Select Units" 
																			label="name" 
																			track-by="id" 
																			:options="emptyUnits" 
																			:multiple="true" 
																			:close-on-select="false" 
																			:clear-on-select="false" 
																			:preserve-search="true" 
																			:required="true" 
																			:allow-empty="false" 
																			class="form-control p-0" 
																			:class="! errors.warehouses[merchantWarehouseIndex].spaces[warehouseSpaceIndex].units ? 'is-valid' : 'is-invalid'" 
																			:disabled="singleMerchantDealData.warehouses.length > (merchantWarehouseIndex+1) || merchantWarehouse.spaces.length > (warehouseSpaceIndex + 1) || singleMerchantDealData.payments.length > 1 || immutableUnit(warehouseSpace.container)"
																			@close="validateFormInput('units')" 
																		>
																		</multiselect>
																		<div class="invalid-feedback">
																			{{ errors.warehouses[merchantWarehouseIndex].spaces[warehouseSpaceIndex].units }}
																		</div>
																	</div>

																	<div 
																		class="form-group col-md-4" 
																		v-else
																	>
																		<label for="inputFirstName">
																			Select Units
																		</label>
																		
																		<multiselect 
																			placeholder="Select Units" 
																			label="name" 
																			track-by="id" 
																			:options="[]" 
																			:multiple="true" 
																			:close-on-select="false" 
																			:clear-on-select="false" 
																			:preserve-search="true" 
																			:required="true" 
																			:allow-empty="false" 
																			class="form-control p-0" 
																			:class="! errors.warehouses[merchantWarehouseIndex].spaces[warehouseSpaceIndex].units ? 'is-valid' : 'is-invalid'" 
																			:disabled="singleMerchantDealData.warehouses.length > (merchantWarehouseIndex+1) || merchantWarehouse.spaces.length > (warehouseSpaceIndex + 1) || singleMerchantDealData.payments.length > 1 || immutableUnit(warehouseSpace.container)"
																			@close="validateFormInput('units')" 
																		>
																		</multiselect>
																		<div class="invalid-feedback">
																			{{ errors.warehouses[merchantWarehouseIndex].spaces[warehouseSpaceIndex].units }}
																		</div>
																	</div>

																	<!-- 
																	<div 
																		class="form-group col-md-6" 
																		v-if="warehouseSpace.container && Array.isArray(warehouseSpace.container.rents)"
																	>
																		<label for="inputFirstName">Rent Package</label>
																		<multiselect 
																			v-model="warehouseSpace.container.selected_rent"
																			placeholder="Select Rent Periods" 
																			label="name" 
																			track-by="id" 
																			:options="warehouseSpace.container.rents" 
																			:custom-label="objectNameWithCapitalized" 
																			:clear-on-select="false" 
																			:preserve-search="true" 
																			:required="true" 
																			:allow-empty="false" 
																			class="form-control p-0" 
																			:class="! errors.warehouses[merchantWarehouseIndex].spaces[warehouseSpaceIndex].space_rent_period  ? 'is-valid' : 'is-invalid'" 
																			:disabled="singleMerchantDealData.warehouses.length > (merchantWarehouseIndex+1) || merchantWarehouse.spaces.length > (warehouseSpaceIndex + 1) || singleMerchantDealData.payments.length > 1 || immutableUnit(warehouseSpace.container)"
																			@close="validateFormInput('space_rent_period')" 
																		>
																		</multiselect>
																		<div class="invalid-feedback">
																			{{ errors.warehouses[merchantWarehouseIndex].spaces[warehouseSpaceIndex].space_rent_period }}
																		</div>
																	</div> 
																	-->
																</div>
															</div>
														</div>
													</div>

													<div class="col-md-12 text-center">
														<div 
															class="invalid-feedback" 
															style="display: block" 
															v-show="errors.warehouses[merchantWarehouseIndex].space"
														>
															{{ errors.warehouses[merchantWarehouseIndex].space }}
														</div>
													</div>												

													<div class="col-md-12 card-footer mt-2">
														<div class="form-row text-center">
															<div class="col-md-6 text-success">
																<button 
																	type="button" 
																	class="btn btn-outline-primary btn-sm btn-block" 
																	data-toggle="tooltip" data-placement="top" title="Add Space" 
																	:disabled="singleMerchantDealData.warehouses.length > merchantWarehouseIndex + 1" 
																	@click="addWarehouseSpace(merchantWarehouseIndex)"
																>
																	Add Space
																</button>
															</div>
															<div class="col-md-6 text-danger">
																<button 
																	type="button" 
																	class="btn btn-outline-info btn-sm btn-block" 
																	data-toggle="tooltip" data-placement="top" title="Remove Space" 
																	:disabled="singleMerchantDealData.warehouses.length > merchantWarehouseIndex + 1 || merchantWarehouse.spaces.length == 1 || ! removableSpace(merchantWarehouse.spaces[merchantWarehouse.spaces.length-1])" 
																	@click="removeWarehouseSpace(merchantWarehouseIndex)"
																>
																	Remove Space
																</button>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div 
										class="col-md-12 text-center" 
										v-show="! singleMerchantDealData.warehouses.length"
									>
										<p class="text-danger">
											No Warehouse Found.
										</p>
									</div>

									<div class="col-md-12">
										<div class="form-row">
											<div class="form-group col-md-6">
												<button 
													type="button" 
													class="btn waves-effect waves-light hor-grd btn-grd-primary btn-sm btn-block" 
													data-toggle="tooltip" data-placement="top" title="Add Warehouse" 
													:disabled="singleMerchantDealData.warehouses.length >= availableWarehouseSpaces.length" 
													@click="addMoreWarehouse()"
												>
													Add Warehouse
												</button>
											</div>

											<div class="form-group col-md-6">
												<button 
													type="button" 
													class="btn waves-effect waves-light hor-grd btn-grd-info btn-sm btn-block" 
													data-toggle="tooltip" data-placement="top" title="Remove Warehouse" 
													:disabled="singleMerchantDealData.warehouses.length < 2" 
													@click="removeWarehouse()"
												>
													Remove Warehouse
												</button>
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
												<button type="button" class="btn btn-outline-secondary btn-sm btn-round float-left" data-toggle="tooltip" data-placement="top" title="Previous"  v-on:click="step-=1">
							                    	<i class="fa fa-2x fa-angle-double-left" aria-hidden="true"></i>
							                  	</button>

												<button 
													type="button" 
													class="btn btn-outline-secondary btn-sm btn-round float-right" 
													data-toggle="tooltip" data-placement="top" title="Next" 
													v-on:click="nextPage" 
													v-show="singleMerchantDealData.payments.length == 1"
												>
													<i class="fa fa-2x fa-angle-double-right" aria-hidden="true"></i>
												</button>
											</div>
										</div>
									</div>
								</div>
								
								<div 
									class="row" 
									v-bind:key="'merchant-deal-modal-step-' + 3" 
									v-show="! loading && step==3 && singleMerchantDealData.payments.length==1" 
								>
									<h2 class="mx-auto mb-4 lead">{{ createMode ? 'Deal' : 'Last Deal' }}</h2>
									<!-- last payment -->
									<div class="col-md-12">
										<div class="card">
											<div class="card-block">
												<div class="form-row">
													<div class="form-group col-md-12">
														<label for="inputFirstName"># Installment</label>

														<div class="input-group">
															<input 
																type="number" 
																class="form-control is-valid" 
																v-model.number="singleMerchantDealData.payments[0].number_installment" 
																min:1
																placeholder="# rent period to pay" 
																:class="! errors.payment.number_installment ? 'is-valid' : 'is-invalid'" 
																@change="resetTotalRent()"
															>

															<div class="input-group-append">
																<span class="input-group-text">
																	{{ singleMerchantDealData.rent_period.name }}
																</span>
															</div>
														</div>

														<div 
															class="invalid-feedback" 
															style="display: block;"
															v-show="errors.payment.number_installment" 
														>
													    	{{ errors.payment.number_installment }}
													    </div>
													</div>
												</div>

												<div class="form-row">
													<div class="col-sm-6 form-group">
														<label for="inputFirstName">Date From</label>
														<div class="date">
															<datepicker 
																v-model="singleMerchantDealData.payments[0].date_from" 
																:initialView="'month'"
																:minimumView="'day'" 
																:maximumView="'year'" 
																:disabled="singleMerchantDealData.payments.length > 1"
																placeholder="Start Date" 
																@input="changeRentExpiryDate"
															>	
															</datepicker>
														</div>
													</div>

													<div class="col-sm-6 form-group">
														<label for="inputFirstName">Date To</label>
														<div class="date">
															<datepicker 
																v-model="singleMerchantDealData.payments[0].date_to" 
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
														<input type="number" 
															class="form-control is-valid" 
															v-model.number="singleMerchantDealData.payments[0].total_rent" 
															placeholder="Total Rent" 
															:disabled="true"
														>
													</div>

													<div class="form-group col-md-6">
														<label for="inputFirstName">Discount</label>
														<div class="input-group mb-1">
															<input type="number" 
																class="form-control" 
																v-model.number="singleMerchantDealData.payments[0].discount" 
																placeholder="Discount" 
																:min="0" 
																:max="100" 
																:class="! errors.payment.discount ? 'is-valid' : 'is-invalid'" 
																@change="resetTotalRent()"
															>
															<div class="input-group-append">
																<span class="input-group-text"> % </span>
															</div>
														</div>
														<div class="invalid-feedback" style="display:block" v-show="errors.payment.discount">
													    	{{ errors.payment.discount }}
													    </div>
													</div>
												</div>

												<div class="form-row">
													<div class="form-group col-md-6">
														<label for="inputFirstName">Last Due</label>
														<input type="number" 
															class="form-control is-valid" 
															v-model.number="singleMerchantDealData.payments[0].previous_due" 
															placeholder="Previous Due" 
															:disabled="true"
														>
													</div>

													<div class="form-group col-md-6">
														<label for="inputFirstName">Net Payable</label>
														<input type="number" 
															class="form-control is-valid" 
															v-model.number="singleMerchantDealData.payments[0].net_payable" 
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
															v-model.number="singleMerchantDealData.payments[0].paid_amount" 
															placeholder="Paid Amount" 
															:class="! errors.payment.paid_amount ? 'is-valid' : 'is-invalid'" 
															@change="validateFormInput('paid_amount')" 
															required="true" 
														>
														<div class="invalid-feedback">
													    	{{ errors.payment.paid_amount }}
													    </div>
													</div>

													<div class="form-group col-md-6">
														<label for="inputFirstName">Current Due</label>
														<input type="number" 
															class="form-control is-valid" 
															:value="singleMerchantDealData.payments[0].net_payable - singleMerchantDealData.payments[0].paid_amount" 
															placeholder="Previous Dues" 
															:disabled="true"
														>
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
												<button type="button" class="btn btn-outline-secondary btn-sm btn-round float-left" data-toggle="tooltip" data-placement="top" title="Previous"  v-on:click="step-=1">
							                    	<i class="fa fa-2x fa-angle-double-left" aria-hidden="true"></i>
							                  	</button>

												<button 
													type="submit" 
													class="btn btn-primary float-right" 
													:disabled="!submitForm || formSubmitted"
													v-show="singleMerchantDealData.payments.length < 2"
												>
													{{ createMode ? 'Make ' : 'Update ' }} Deal
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

		<delete-confirmation-modal 
			v-if="userHasPermissionTo('delete-merchant-deal')" 
			:csrf="csrf" 
			:form-submitted="formSubmitted" 
			:submit-method-name="'deleteDeal'" 
			:content-to-delete="singleMerchantDealData"
			:restoration-message="'You can not restore this deal again !'" 
			
			@deleteDeal="deleteDeal($event)" 
		></delete-confirmation-modal>

	<!-- 
		<restore-confirmation-modal 
			:csrf="csrf" 
			:submit-method-name="'restoreAsset'" 
			:content-to-restore="singleMerchantDealData"
			:restoration-message="'This will restore all related items !'" 

			@restoreAsset="restoreAsset($event)" 
		></restore-confirmation-modal>
 	-->

 		<!-- Modal -->
		<div class="modal fade" id="deal-custom-search" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
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
						<button type="button" class="btn btn-success" data-toggle="tooltip" data-placement="top" title="Reset"  @click="resetSearchingDates()">
	                  		Reset
	                  	</button>

						<button type="button" class="btn btn-primary ml-auto" data-dismiss="modal">
	                  		See Results
	                  	</button>
					</div>
				</div>
			</div>
		</div>

 		<!-- View Modal -->
		<div class="modal fade" id="merchant-deal-view-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">{{ merchant.name }} Deal ({{ singleMerchantDealData.name | capitalize }}) Details</h5>
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
										<a class="nav-link" data-toggle="tab" href="#merchant-deal-payments" role="tab">
											Deals
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
												{{ singleMerchantDealData.name | capitalize }}
											</label>
										</div>

										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Rent Package :
											</label>

											<label class="col-sm-6 col-form-label">
												{{ (singleMerchantDealData.rent_period && singleMerchantDealData.rent_period.name) ? singleMerchantDealData.rent_period.name : 'NA' | capitalize }}
											</label>
										</div>
										
										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Status :
											</label>

											<label class="col-sm-6 col-form-label">
												<span :class="[singleMerchantDealData.active ? 'badge-success' : 'badge-danger', 'badge']">
													{{ singleMerchantDealData.active ? 'Active' : 'Inactive' }}
												</span>
											</label>
										</div>

										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												E-Commerce Fullfillment :
											</label>

											<label class="col-sm-6 col-form-label">
												<span :class="[singleMerchantDealData.e_commerce_fulfillment ? 'badge-info' : 'badge-secondary', 'badge']">
													{{ singleMerchantDealData.e_commerce_fulfillment ? 'Enabled' : 'Disabled' }}
												</span>
											</label>
										</div>

										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Auto Renewal :
											</label>

											<label class="col-sm-6 col-form-label">
												<span :class="[singleMerchantDealData.auto_renewal ? 'badge-warning' : 'badge-secondary', 'badge']">
													{{ singleMerchantDealData.auto_renewal ? 'Enabled' : 'Disabled' }}
												</span>
											</label>
										</div> 

										<div class="form-row" v-show="singleMerchantDealData.e_commerce_fulfillment">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Sale :
											</label>

											<label class="col-sm-6 col-form-label">
												{{ singleMerchantDealData.sale_percentage }} %
											</label>
										</div>

										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Created On :
											</label>

											<label class="col-sm-6 col-form-label">
												{{ singleMerchantDealData.created_at }}
											</label>
										</div>
									</div>

									<div class="tab-pane" id="merchant-deal-spaces" role="tabpanel">
										<div 
											class="form-row" 
											v-if="singleMerchantDealData.hasOwnProperty('warehouses') && Array.isArray(singleMerchantDealData.warehouses) && singleMerchantDealData.warehouses.length"
										>
											<label class="col-sm-12 col-form-label font-weight-bold">
												Dealt Spaces :
											</label>

											<div class="col-sm-12">
												<div 
													class="form-row" 
													v-for="(merchantWarehouse, merchantWarehouseIndex) in singleMerchantDealData.warehouses" 
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
																							{{ warehouseContainer.occupied==1 ? 'Packed' : warehouseContainer.occupied==0.5 ? 'Packed Partially' : 'Empty' }}
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
																									{{ shelf.occupied==1 ? 'Packed' : shelf.occupied==0.5 ? 'Packed Partially' : 'Empty' }}
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
																									{{ unit.occupied==1 ? 'Packed' : unit.occupied==0.5 ? 'Packed Partially' : 'Empty' }}
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

									<div class="tab-pane" id="merchant-deal-payments" role="tabpanel">
										<div class="form-row" v-if="singleMerchantDealData.hasOwnProperty('payments') && singleMerchantDealData.payments.length">
											<div 
												class="col-md-12 card card-body" 
												v-for="(dealPayment, dealPaymentIndex) in singleMerchantDealData.payments" :key="'merchant-payment-index-' + dealPaymentIndex + '-merchant-payment-' + dealPayment.id"
											>
												<div class="form-row">
													<label class="col-sm-6 col-form-label font-weight-bold text-right">
														Invoice No :
													</label>

													<label class="col-sm-6 col-form-label">
														{{ dealPayment.invoice_no | capitalize }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-sm-6 col-form-label font-weight-bold text-right">
														# Installment :
													</label>

													<label class="col-sm-6 col-form-label">
														{{ dealPayment.number_installment }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-sm-6 col-form-label font-weight-bold text-right">
														Issued From :
													</label>

													<label class="col-sm-6 col-form-label">
														{{ dealPayment.date_from }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-sm-6 col-form-label font-weight-bold text-right">
														Expired At :
													</label>

													<label class="col-sm-6 col-form-label">
														{{ dealPayment.date_to }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-sm-6 col-form-label font-weight-bold text-right">
														Total Rent :
													</label>

													<label class="col-sm-6 col-form-label">
														{{ dealPayment.total_rent }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-sm-6 col-form-label font-weight-bold text-right">
														Discount :
													</label>

													<label class="col-sm-6 col-form-label">
														{{ dealPayment.discount }} %
													</label>
												</div>

												<div class="form-row">
													<label class="col-sm-6 col-form-label font-weight-bold text-right">
														Previous Due :
													</label>

													<label class="col-sm-6 col-form-label">
														{{ dealPayment.previous_due }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-sm-6 col-form-label font-weight-bold text-right">
														Net Payeble :
													</label>

													<label class="col-sm-6 col-form-label">
														{{ dealPayment.net_payable }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-sm-6 col-form-label font-weight-bold text-right">
														Paid Amount :
													</label>

													<label class="col-sm-6 col-form-label">
														{{ dealPayment.paid_amount }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-sm-6 col-form-label font-weight-bold text-right">
														Due :
													</label>

													<label class="col-sm-6 col-form-label">
														{{ dealPayment.current_due }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-sm-6 col-form-label font-weight-bold text-right">
														Paid at :
													</label>

													<label class="col-sm-6 col-form-label">
														{{ dealPayment.paid_at }}
													</label>
												</div>

												<div class="form-row" v-if="dealPayment.hasOwnProperty('rents') && dealPayment.rents.length">
													<div class="col-md-6" v-for="(rent, rentIndex) in dealPayment.rents" :key="'merchant-payment-' + dealPaymentIndex + '-rent-index-' + rentIndex + '-rent-' + rent.id">
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
						<button type="button" class="btn btn-secondary btn-sm btn-block" data-dismiss="modal">
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
	import Multiselect from 'vue-multiselect';
	import Datepicker from 'vuejs-datepicker';

	export default {

	    components: { 
	    	Datepicker,
			multiselect : Multiselect,
		},

	    props: {

			merchant:{
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
	        	query : '',
	        	error : '',
    			perPage : 10,
	        	loading : false,

	        	createMode : true,
	        	submitForm : true,
	        	formSubmitted : false,

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

	        	allRentPeriods : [],
	        	// allContainers : [],
	        	
	        	availableWarehouseSpaces : [],

	        	allWarehouseSpaces : [
	        		{
						// warehouse
						id:null,
						name: null,

						emptyContainers : [
							/*
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
								}
							*/
						],
						emptyContainerShelve : [
							/*
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
								}
							*/
						],
						emptyContainerShefUnits : [
							/*
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
							*/
						]
					}
	        	],

	        	emptyContainers : [],
	        	emptyShelfContainers : [],
	        	emptyUnitContainers : [],

	        	emptyShelves : [],
	        	emptyUnitShelves : [],

	        	emptyUnits : [],

	        	merchantAllDeals : [],
	        	
	        	pagination: {
		        	current_page: 1
		      	},

	        	singleMerchantDealData : {

	        		active : true,
	        		auto_renewal : true, // Renting is normally auto-renewal
	        		e_commerce_fulfillment : false,
	        		sale_percentage : 0,
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

					payments : [
						{
							number_installment : 1,	
							date_from : null,
							date_to : null,
							previous_due : 0,
							total_rent : 0, // generated from selected spaces
							discount : 0,	// percentage 
							net_payable : 0,
							paid_amount : 0,
							current_due : 0,
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

	        	errors : {
	
					// sale_percentage : null,
					// warehouse : null 	// when no warehouse is found
					// rent_period : null,
					
					warehouses : [
						{
							// warehouse : null,
							// space : null,  // when no space is found 
							
							spaces : [
								{
									// type : null,
									
									// containers : null,
									
									// parent_container : null,
									// shelves : null,
									
									// unit_shelf : null,
									// units : null,
									
									// rent : null,
								}
							]
						}
					],

					payment : {
						// discount : 'Discount cant be negative',
						// paid_amount : 'Paid amount is required'
					}
					
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

					"E-Commerce": {
						
						field: "e_commerce_fulfillment",
						
						callback: (value) => {
							
							if (value) {
								return 'Enabled';
							}
							else {
								return 'Disabled';
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

					"Sale Percentage": {
						
						field: "sale_percentage",
						
						callback: (value) => {
							
							if (value) {
								return sale_percentage;
							}
							else {
								return '';
							}

						},

					},

					"Rent Package": {
						
						field: "rent_period",
						
						callback: (rent_period) => {
							
							if (rent_period && rent_period.name) {
								return rent_period.name;
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

																infosToReturn +=  "Containers: " + warehouseContainer.name + "\n";

															}
														);

													}
													else if (warehouseSpace.type=='shelves') {
														
														infosToReturn +=  "Space Type : Shelves\n";
														infosToReturn +=  "Container Name : " + warehouseSpace.container.name + "\n";

														warehouseSpace.container.shelves.forEach(
															(warehouseShelf, warehouseContainerIndex) => {

																infosToReturn +=  "Shelves: " + warehouseShelf.name + "\n";

															}
														);

													}
													else if (warehouseSpace.type=='units') {
														
														infosToReturn +=  "Space Type : Units\n";
														infosToReturn +=  "Container Name : " + warehouseSpace.container.name + "\n";
														infosToReturn +=  "Shelf Name : " + warehouseSpace.container.shelf.name + "\n";

														warehouseSpace.container.shelf.units.forEach(
															(warehouseUnit, warehouseContainerIndex) => {

																infosToReturn +=  "Units: " + warehouseUnit.name + "\n";

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

					Payments: {

						field: "payments",

						callback: (payments) => {

							if (payments) {
								
								var infosToReturn = '';

								payments.forEach(
					
									(dealPayment, dealPaymentIndex) => {

										infosToReturn += "Invoice:" + dealPayment.invoice_no + "\n";
										infosToReturn += "# Installment:" + dealPayment.number_installment + "\n";
										infosToReturn += "Date From:" + dealPayment.date_from + "\n";
										infosToReturn += "Date To:" + dealPayment.date_to + "\n";
										infosToReturn += "Total Rent:" + dealPayment.total_rent + "\n";
										infosToReturn += "Discount:" + dealPayment.discount + "\n";
										infosToReturn += "Due:" + dealPayment.previous_due + "\n";
										infosToReturn += "Payeble:" + dealPayment.net_payable + "\n";
										infosToReturn += "Paid:" + dealPayment.paid_amount + "\n";

										if (dealPayment.hasOwnProperty('rents') && dealPayment.rents.length) {

											infosToReturn +=  '( ';

											dealPayment.rents.forEach(
						
												(paymentRent, paymentRentIndex) => {

													infosToReturn +=  "Space:" + paymentRent.dealt_space ? paymentRent.dealt_space.name : '' + ', Rent:' + paymentRent.rent;					

												}
												
											);

											infosToReturn +=  ').' + "\n";

										}

										infosToReturn +=  "\n\n"

									}
									
								);

								return infosToReturn;

							}
							else {
								return 'No Deal.'
							}

						},

					},
					
				},

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
				
				if (this.searchAttributes.search==='' && ! this.searchAttributes.dateTo && ! this.searchAttributes.dateFrom) {

					this.fetchAllMerchantDeals();

				}
				else {

					let format = /[`!@#$%^&*+\=\[\]{};':"\\|,.<>\/?~]/;

					if (! format.test(val)) {

						this.searchData();
					
					}

				}

			},
			
			'searchAttributes.dateFrom' : function(val){
				
				if (this.searchAttributes.search==='' && ! this.searchAttributes.dateTo && ! this.searchAttributes.dateFrom) {

					this.fetchAllMerchantDeals();

				}
				else {

					this.searchData();
						
				}

			},

			'searchAttributes.dateTo' : function(val){
				
				if (this.searchAttributes.search==='' && ! this.searchAttributes.dateTo && ! this.searchAttributes.dateFrom) {

					this.fetchAllMerchantDeals();

				}
				else {

					this.searchData();
						
				}

			},

		},

		created() {
			
			this.fetchAllRentPeriods();

			this.fetchAllWarehouseContainers();

			// this.fetchWarehouseAllContainers();

			this.fetchAllMerchantDeals();
			
			// this.resetErrorObject();

			// this.configureProductErrorsWithPropData();

		},
		
		methods: {

			fetchAllRentPeriods() {
				
				this.query = '';
				this.error = '';
				// this.loading = true;
				this.searchAttributes.search = '';
				this.allRentPeriods = [];

				axios
					.get('/api/rent-periods/')
					.then(response => {
						if (response.status == 200) {
							
							this.allRentPeriods = response.data;
					
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
						// this.loading = false;
					});

			},
			fetchAllWarehouseContainers() {
				
				this.query = '';
				this.error = '';
				// this.loading = true;
				this.searchAttributes.search = '';
				this.allWarehouseSpaces = [];

				axios
					.get('/api/warehouse-containers/')
					.then(response => {
						if (response.status == 200) {
							
							this.allWarehouseSpaces = response.data.data;
					
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
						// this.loading = false;
					});

			},
			fetchAllMerchantDeals() {

				this.query = '';
				this.error = '';
				this.loading = true;
				this.merchantAllDeals = [];
				
				axios
					.get('/api/merchant-deals/' + this.merchant.id + '/' +  + this.perPage + "?page=" + this.pagination.current_page)
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
			/*
				fetchWarehouseAllContainers(warehouse) {
					
					this.query = '';
					this.error = '';
					// this.loading = true;
					this.allContainers = [];
					this.emptyContainers = [];
					this.emptyShelfContainers = [];
					this.emptyUnitContainers = [];

					axios
						.get('/api/warehouse-containers/' + warehouse)
						.then(response => {
							if (response.status == 200) {
								
								this.allContainers = response.data;
								this.resetWarehouseContainers();
								
								// this.emptyContainers = response.data.emptyContainers;
								// this.emptyShelfContainers = response.data.emptyShelfContainers;
								// this.emptyUnitContainers = response.data.emptyUnitContainers;
						
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
							// this.loading = false;
						});

				},
			*/
				
			/*
				configureProductErrorsWithPropData() {

					// new error configuration
					this.errors = {
						merchantDeal : {
							variations : [],
							warehouses : [],
							product_serials : [],
						},
					};

					if (this.singleMerchantDealData.warehouses.length) {

						// if (this.singleMerchantDealData.warehouses.length) {

							this.singleMerchantDealData.warehouses.forEach(
								(merchantWarehouse, merchantWarehouseIndex) => {
									this.errors.warehouses.push({});
								}
							);

						// }
						
							else {

								this.product.warehouses.forEach(
									(merchantWarehouse, merchantWarehouseIndex) => {
										this.errors.warehouses.push({});
									}
								);

							}
						

					}
					else {

						this.errors.warehouses = [
							{},
						];

					}

					if (this.product.category && this.product.has_variations && this.product.hasOwnProperty('variations') && this.product.variations.length) {
							
						this.product.variations.forEach(
							(productVariation, index) => {
								this.errors.variations.push({});
							}
						);

					}

				},
			*/	
    		showDealDetails(object) {		
				// this.singleMerchantDealData = { ...object };
				this.singleMerchantDealData = Object.assign({}, this.singleMerchantDealData, object);
				$('#merchant-deal-view-modal').modal('show');
			},
			showDealCreateForm() {

				this.step = 1;
				this.createMode = true;
	        	this.submitForm = true;
	        	this.formSubmitted = false;

				// singleMerchantDealData configuration
				this.singleMerchantDealData = {

	        		active : true,
	        		auto_renewal : true, // Renting is normally auto-renewal
	        		e_commerce_fulfillment : false,
	        		sale_percentage : 0,
	        		rent_period : {},
	        		rent_period_id : null,
					merchant_id : this.merchant.id,
					// created_at : null,

					warehouses : [
						{							
							// id : null,
							// name : null,
							
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

							// spaces : [
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
							// ],
						},
					],

					payments : [
						{
							number_installment : 1,
							date_from : this.today,
							date_to : null,
							previous_due : 0,	// as new deal
							total_rent : 0, // generated from selected spaces
							discount : 0,	// percentage 
							net_payable : 0,
							paid_amount : 0,
							current_due : 0,
							// merchant_deal_id : null,
							// paid_at : null,
							rents : [
								{
									rent : 0,
									// dealt_space_id : null,
									// merchant_payment_id : null
								},
							]
						},
					]
	        	};

	        	this.errors = {
	
					// sale_percentage : null,
					// warehouse : null, 	// when no warehouse is found
					// rent_period : null
					
					warehouses : [
						{
							// warehouse : null,
							// space : null 	// when no space is found
							
							spaces : [
								{
									// type : null,
									
									// containers : null,
									
									// parent_container : null,
									// shelves : null,
									
									// unit_shelf : null,
									// units : null,
									
									// rent : null,
								}
							]
						},
					],

					payment : {
						// discount : 'Discount cant be negative',
						// paid_amount : 'Paid amount is required'
					}
					
				};

				// new errors configuration
				// this.resetErrorObject();

				$('#merchantDeal-createOrEdit-modal').modal('show');
			},
			openDealEditForm(object) {

				this.step = 1;
				this.createMode = false;
	        	this.submitForm = true;
	        	this.formSubmitted = false;
				
				this.singleMerchantDealData = JSON.parse(JSON.stringify(object));

				this.errors = {

					warehouses : [],
					payment : {}

				};

				this.resetErrorObject();

				this.setRelatedShelvesAndUnits();

				$('#merchantDeal-createOrEdit-modal').modal('show');
			},
			openDealDeleteForm(object) {
				this.singleMerchantDealData = object;

				if (object.payments.length > 1) {
					this.$toastr.e("Deal has multiple payments", "Error");
					return;
				}

				$('#delete-confirmation-modal').modal('show');
			},
			goToDealPayments(object) {

				// console.log(object);
				this.$router.push({ name: 'deal-payments', params: { merchantName:this.merchant.user_name.replace(/ /g,"-"), dealName:object.name ? object.name.replace(/\s+/g, '-') : object.created_at.replace(/\s+/g, '-'), deal:object }});

			},
			createMerchantDeal() {
				
				if (!this.verifyUserInput()) {
					this.submitForm = false;
					return;
				}

				this.formSubmitted = true;

				axios
					.post('/merchant-deals/' + this.perPage, this.singleMerchantDealData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("New deal has been stored", "Success");
							// this.query !== '' ? this.searchData() : this.setMerchantDealsPagination(response);
							this.searchAttributes.search !== '' ? this.searchData() : this.setMerchantDealsPagination(response);
							$('#merchantDeal-createOrEdit-modal').modal('hide');
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
						this.fetchAllWarehouseContainers();
					});

			},
			updateMerchantDeal() {
				
				if (!this.verifyUserInput()) {
					this.submitForm = false;
					return;
				}

				this.formSubmitted = true;

				axios
					.put('/merchant-deals/' + this.singleMerchantDealData.id + '/' + this.perPage, this.singleMerchantDealData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Deal has been updated", "Success");
							// this.query !== '' ? this.searchData() : this.setMerchantDealsPagination(response);
							this.searchAttributes.search !== '' ? this.searchData() : this.setMerchantDealsPagination(response);
							$('#merchantDeal-createOrEdit-modal').modal('hide');
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
						this.fetchAllWarehouseContainers();
					});

			},
			deleteDeal(singleAssetData) {
				
				if (singleAssetData.payments.length > 1) {
					this.$toastr.e("Deal has multiple payments", "Error");
					return;
				}

				this.formSubmitted = true;

				axios
					.delete('/merchant-deals/' + this.singleMerchantDealData.id + '/' + this.perPage, this.singleMerchantDealData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Deal has been deleted", "Success");
							// this.query !== '' ? this.searchData() : this.setMerchantDealsPagination(response);
							this.searchAttributes.search !== '' ? this.searchData() : this.setMerchantDealsPagination(response);
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
						this.fetchAllWarehouseContainers();
					});

			},
			searchData() {


				this.error = '';
				this.merchantAllDeals = [];
				this.pagination.current_page = 1;
				this.searchAttributes.merchant_id = this.merchant.id;
				
				axios
				.post('/search-merchant-deals/' + this.perPage, this.searchAttributes)
				.then(response => {
					this.merchantAllDeals = response.data.all.data;
					this.pagination = response.data.all;
				})
				.catch(e => {
					this.error = e.toString();
				});

			},
			verifyUserInput() {

				this.validateFormInput('discount');
				this.validateFormInput('paid_amount');
				this.validateFormInput('number_installment');

				if (this.errors.constructor === Object && Object.keys(this.errors.payment).length < 1) {

					return true;
				
				}

				return false;
		
			},
			errorInWarehouseSpacesArray(array = []) {

				const warehouseSpaceError = (warehouseSpace) => {
					return Object.keys(warehouseSpace).length > 0
				};

				if (array.length) {
					return array.some(warehouseSpaceError);
				}

				return false;

			},
			errorInWarehousesArray(array = []) {

				const warehouseError = (warehouse) => {
	        							return Object.keys(warehouse).length > 1 || this.errorInWarehouseSpacesArray(warehouse.spaces)
	        						}; 

				if (array.length) {
					return array.some(warehouseError);
				}

				return false;

			},
			nextPage() {
				
				if (this.step > 2) {
					return;
				}

				else if (this.step == 1) {

					this.validateFormInput('sale_percentage');

					if (this.errors.constructor === Object && Object.keys(this.errors).length < 3) {

						this.step++;
						this.submitForm = true;
					
					}
					else {
					
						this.submitForm = false;
					
					}

				}

				else if (this.step == 2) {

					this.validateFormInput('rent_period');
					this.validateFormInput('warehouse');
					this.validateFormInput('space');

					if (this.singleMerchantDealData.warehouses.length && this.singleMerchantDealData.warehouses.some(merchantWarehouse => merchantWarehouse.hasOwnProperty('spaces') && merchantWarehouse.spaces.length)) {
						
						this.validateFormInput('space_type');
						this.validateFormInput('containers');
						this.validateFormInput('parent_container');
						this.validateFormInput('parent_shelf');
						this.validateFormInput('shelves');
						this.validateFormInput('units');
						this.validateFormInput('space_rent_period');

					}

					if (this.errors.constructor === Object && Object.keys(this.errors).length < 3 && ! this.errorInWarehousesArray(this.errors.warehouses)) {

						this.setTotalRent();
						this.setNetPayable();

						// console.log('Step 2');

						this.step += 1;
						this.submitForm = true;

					}
					else {

						this.submitForm = false;
						
					}

				}

				else {

					this.submitForm = true;
				}

			},
			addWarehouseContainers(merchantWarehouseIndex, selectedSpaceIndex) {
				
				if(this.singleMerchantDealData.warehouses.length > merchantWarehouseIndex && this.errors.warehouses.length > merchantWarehouseIndex && this.singleMerchantDealData.payments.length < 2) {

					if(this.singleMerchantDealData.warehouses[merchantWarehouseIndex].spaces.length > selectedSpaceIndex && this.errors.warehouses[merchantWarehouseIndex].spaces.length > selectedSpaceIndex) {


						if (this.singleMerchantDealData.warehouses[merchantWarehouseIndex].spaces[selectedSpaceIndex].hasOwnProperty('containers') && this.singleMerchantDealData.warehouses[merchantWarehouseIndex].spaces[selectedSpaceIndex].containers.length < this.emptyContainers.length) {
							
							this.singleMerchantDealData.warehouses[merchantWarehouseIndex].spaces[selectedSpaceIndex].containers.push({});
							this.$delete(this.errors.warehouses[merchantWarehouseIndex].spaces[selectedSpaceIndex], 'containers');


						} else {
							
						}

					}

				}

			},
			removeWarehouseContainers(merchantWarehouseIndex, selectedSpaceIndex) {
				
				if(this.singleMerchantDealData.warehouses.length > merchantWarehouseIndex && this.errors.warehouses.length > merchantWarehouseIndex && this.singleMerchantDealData.payments.length < 2) {

					if(this.singleMerchantDealData.warehouses[merchantWarehouseIndex].spaces.length > selectedSpaceIndex && this.errors.warehouses[merchantWarehouseIndex].spaces.length > selectedSpaceIndex && this.removableSpace(this.singleMerchantDealData.warehouses[merchantWarehouseIndex].spaces[selectedSpaceIndex])){

						this.emptyContainers.push(this.singleMerchantDealData.warehouses[merchantWarehouseIndex].spaces[selectedSpaceIndex].containers[this.singleMerchantDealData.warehouses[merchantWarehouseIndex].spaces[selectedSpaceIndex].containers.length-1]);

						this.singleMerchantDealData.warehouses[merchantWarehouseIndex].spaces[selectedSpaceIndex].containers.pop();

						// this.$delete(this.errors.warehouses[merchantWarehouseIndex].spaces[selectedSpaceIndex], 'containers');
						
						this.validateFormInput('containers');

					}
				
				}

			},
			addWarehouseSpace(merchantWarehouseIndex) {

				if (this.singleMerchantDealData.warehouses.length > merchantWarehouseIndex && this.singleMerchantDealData.warehouses[merchantWarehouseIndex].hasOwnProperty('spaces') && this.errors.warehouses.length > merchantWarehouseIndex && this.errors.warehouses[merchantWarehouseIndex].hasOwnProperty('spaces') && this.singleMerchantDealData.payments.length < 2) {

					// const expectedWarehouse = (warehouse) => warehouse.id == this.singleMerchantDealData.warehouses[merchantWarehouseIndex].id && warehouse.name == this.singleMerchantDealData.warehouses[merchantWarehouseIndex].name;

					// let warehouseIndex = this.allWarehouseSpaces.findIndex(expectedWarehouse);

					// if (warehouseIndex > -1 && this.singleMerchantDealData.warehouses[merchantWarehouseIndex].spaces.length < (Object.keys(this.allWarehouseSpaces[warehouseIndex]).length - 2) && this.singleMerchantDealData.payments.length < 2) {

						this.singleMerchantDealData.warehouses[merchantWarehouseIndex].spaces.push( {} );
						this.errors.warehouses[merchantWarehouseIndex].spaces.push( {} );

					// }
					
					this.validateFormInput('space');

				}

			},
			removeWarehouseSpace(merchantWarehouseIndex) {
					
				if (this.singleMerchantDealData.warehouses.length > merchantWarehouseIndex && this.singleMerchantDealData.warehouses[merchantWarehouseIndex].spaces.length > 0 && this.singleMerchantDealData.payments.length < 2 && this.removableSpace(this.singleMerchantDealData.warehouses[merchantWarehouseIndex].spaces[this.singleMerchantDealData.warehouses[merchantWarehouseIndex].spaces.length-1])) {
					
					this.singleMerchantDealData.warehouses[merchantWarehouseIndex].spaces.pop();

					if (this.errors.warehouses.length > merchantWarehouseIndex && this.errors.warehouses[merchantWarehouseIndex].spaces.length > 0) {

						this.errors.warehouses[merchantWarehouseIndex].spaces.pop();

					}
				
				}
				
			},
			removableSpace(warehouseSpace) {

				if (warehouseSpace.type=='containers' && warehouseSpace.hasOwnProperty('containers') && warehouseSpace.containers.some(warehouseContainer => warehouseContainer.occupied == 0.5 || warehouseContainer.occupied == 1.0)) {

					return false;

				}
				else if (warehouseSpace.type=='shelves' && warehouseSpace.hasOwnProperty('container') && warehouseSpace.container.hasOwnProperty('shelves') && warehouseSpace.container.shelves.some(warehouseShelf => warehouseShelf.occupied == 0.5 || warehouseShelf.occupied == 1.0)) {

					return false;

				}
				else if (warehouseSpace.type=='units' && warehouseSpace.hasOwnProperty('container') && warehouseSpace.container.hasOwnProperty('shelf') && warehouseSpace.container.shelf.hasOwnProperty('units') && warehouseSpace.container.shelf.units.some(warehouseUnit => warehouseUnit.occupied == 1.0)) {

					return false;

				}

				return true;

			},
			addMoreWarehouse() {

				if (this.singleMerchantDealData.warehouses.length && this.singleMerchantDealData.warehouses.some(merchantWarehouse => merchantWarehouse.hasOwnProperty('spaces') && merchantWarehouse.spaces.length)) {
					
					this.validateFormInput('space_type');
					this.validateFormInput('containers');
					this.validateFormInput('parent_container');
					this.validateFormInput('parent_shelf');
					this.validateFormInput('shelves');
					this.validateFormInput('units');
					this.validateFormInput('space_rent_period');

				}

				if (this.errors.constructor === Object && Object.keys(this.errors).length < 3 && ! this.errorInWarehousesArray(this.errors.warehouses) && this.singleMerchantDealData.warehouses.length < this.availableWarehouseSpaces.length && this.singleMerchantDealData.payments.length < 2) {

					this.singleMerchantDealData.warehouses.push({});
					this.errors.warehouses.push({ spaces : [ {} ] });

				}

			},
			removeWarehouse() {
					
				if (this.singleMerchantDealData.warehouses.length > 1 && this.singleMerchantDealData.payments.length < 2) {

					this.singleMerchantDealData.warehouses.pop();
					this.errors.warehouses.pop();
				
				}

				// if (!this.errorInWarehousesArray(this.errors.warehouses)) {
				// 	this.submitForm = true;
				// }
				
			},
			removableDeal(merchantDeal) {

				const warehouseIsEngaged = (merchantWarehouse) => {
					/*
					return merchantWarehouse.spaces.some(
						dealtSpace =>  (dealtSpace.type=='containers' && dealtSpace.hasOwnProperty('containers') && dealSpace.containers.some(dealtContainer => dealtContainer.occupied != 0)) || (dealtSpace.type=='shelves' && dealtSpace.hasOwnProperty('container') && dealtSpace.container.hasOwnProperty('shelves') && dealtSpace.container.shelves.some(dealtShelf => dealtShelf.occupied != 0)) || (dealtSpace.type=='units' && dealtSpace.hasOwnProperty('container') && dealtSpace.container.hasOwnProperty('shelf') && dealtSpace.container.shelf.hasOwnProperty('units') && dealtSpace.container.shelf.units.some(dealtUnit => dealtUnit.occupied != 0))
					);
					*/
					
					return merchantWarehouse.spaces.some(
						dealtSpace => ! this.removableSpace(dealtSpace)
					);

				};

				return merchantDeal.payments.length < 2 && ! merchantDeal.warehouses.some(warehouseIsEngaged)

			},
			immutableWarehouse(merchantWarehouse) {

				if (merchantWarehouse.hasOwnProperty('spaces') && merchantWarehouse.spaces.length) {

					/*
					return merchantWarehouse.spaces.some(
						dealtSpace =>  (dealtSpace.type=='containers' && dealtSpace.hasOwnProperty('containers') && dealtSpace.containers.some(dealtContainer => dealtContainer.occupied != 0)) || (dealtSpace.type=='shelves' && dealtSpace.hasOwnProperty('container') && dealtSpace.container.hasOwnProperty('shelves') && dealtSpace.container.shelves.some(dealtShelf => dealtShelf.occupied != 0)) || (dealtSpace.type=='units' && dealtSpace.hasOwnProperty('container') && dealtSpace.container.hasOwnProperty('shelf') && dealtSpace.container.shelf.hasOwnProperty('units') && dealtSpace.container.shelf.units.some(dealtUnit => dealtUnit.occupied != 0))
					);
					*/

					return merchantWarehouse.spaces.some(
						dealtSpace => ! this.removableSpace(dealtSpace)
					);

				}

				return false;

			},
			immutableContainer(warehouseContainer) {

				if (warehouseContainer) {

					return warehouseContainer.occupied == 0.5 || warehouseContainer.occupied == 1.0;

				}

				return false;

			},
			immutableShelf(warehouseContainer) {

				if (warehouseContainer && warehouseContainer.hasOwnProperty('shelves') && warehouseContainer.shelves.length) {

					return warehouseContainer.shelves.some(warehouseShelf => warehouseShelf.occupied == 0.5 || warehouseShelf.occupied == 1.0);

				}

				return false;

			},
			immutableUnit(warehouseContainer) {

				if (warehouseContainer && warehouseContainer.hasOwnProperty('shelf') && warehouseContainer.shelf.hasOwnProperty('units') && warehouseContainer.shelf.units.length) {

					return warehouseContainer.shelf.units.some(warehouseUnit => warehouseUnit.occupied == 0.5 || warehouseUnit.occupied == 1.0);

				}

				return false;

			},
			resetRentPackage() {

				if (this.singleMerchantDealData.rent_period && Object.keys(this.singleMerchantDealData.rent_period).length > 0) {

					this.$set(this.singleMerchantDealData, 'warehouses', [ {} ]);
					this.singleMerchantDealData.rent_period_id = this.singleMerchantDealData.rent_period.id;

					this.setAvailableWarehouses();

				}

			},
			resetWarehouseSpaces(merchantWarehouseIndex){
				
				if (this.singleMerchantDealData.warehouses.some((merchantWarehouse, mrchntWarehouseIndex) => this.singleMerchantDealData.warehouses.some((selectedWarehouse, selectedWarehouseIndex) => selectedWarehouse.id==merchantWarehouse.id && selectedWarehouse.name==merchantWarehouse.name && mrchntWarehouseIndex!=selectedWarehouseIndex))) {

					return;

				}

				else if(this.singleMerchantDealData.warehouses.length > merchantWarehouseIndex) {

					this.$set(this.singleMerchantDealData.warehouses[merchantWarehouseIndex], 'spaces', [ {} ]);

				}

				// Reset Error Object
				// if (this.errors.warehouses.length > merchantWarehouseIndex) {

				this.errors.warehouses[merchantWarehouseIndex].spaces = [ {} ];
				this.$delete(this.errors.warehouses[merchantWarehouseIndex], 'warehouse');

				// }				

			},
			resetErrorObject() {

				if (this.singleMerchantDealData.warehouses.length) {

					this.singleMerchantDealData.warehouses.forEach(
						(merchantWarehouse, merchantWarehouseIndex) => {
							
							this.errors.warehouses.push({ spaces : [] });

							if (merchantWarehouse.spaces.length) {

								merchantWarehouse.spaces?.forEach(

									(dealtSpace, dealtSpaceIndex) => {

										this.errors.warehouses[merchantWarehouseIndex].spaces.push({});

									}

								);

							}

						}
					);
					
				}

			},
			setAvailableWarehouses() {

				this.availableWarehouseSpaces = [];

				this.allWarehouseSpaces.forEach(
					(warehouse, warehouseIndex) => {

						if (warehouse.emptyContainers.some(emptyContainer=>emptyContainer.rents.some(containerRent=>containerRent.rent_period_id==this.singleMerchantDealData.rent_period_id)) || warehouse.emptyShelfContainers.some(emptyShelfContainer=>emptyShelfContainer.rents.some(containerRent=>containerRent.rent_period_id==this.singleMerchantDealData.rent_period_id)) || warehouse.emptyUnitContainers.some(emptyUnitContainer=>emptyUnitContainer.rents.some(containerRent=>containerRent.rent_period_id==this.singleMerchantDealData.rent_period_id))) {

							let newWarehouse = {

								id : warehouse.id,
								name : warehouse.name,

								emptyContainers : [],
								emptyShelfContainers : [],
								emptyUnitContainers : []

							}

							this.availableWarehouseSpaces.push(newWarehouse);

						}

						// containers
						warehouse.emptyContainers.forEach(

							(emptyContainer, warehouseContainerIndex) => {

								if (emptyContainer.rents.some(containerRent=>containerRent.rent_period_id==this.singleMerchantDealData.rent_period_id)) {

									this.availableWarehouseSpaces[this.availableWarehouseSpaces.length-1].emptyContainers.push(emptyContainer);

								}

							}

						);

						// shelf containers
						warehouse.emptyShelfContainers.forEach(

							(emptyShelfContainer, emptyShelfContainerIndex) => {

								if (emptyShelfContainer.rents.some(containerRent=>containerRent.rent_period_id==this.singleMerchantDealData.rent_period_id)) {

									this.availableWarehouseSpaces[this.availableWarehouseSpaces.length-1].emptyShelfContainers.push(emptyShelfContainer);

								}

							}

						);

						// unit containers
						warehouse.emptyUnitContainers.forEach(

							(emptyUnitContainer, emptyShelfContainerIndex) => {

								if (emptyUnitContainer.rents.some(containerRent=>containerRent.rent_period_id==this.singleMerchantDealData.rent_period_id)) {

									this.availableWarehouseSpaces[this.availableWarehouseSpaces.length-1].emptyUnitContainers.push(emptyUnitContainer);

								}

							}

						);

					}
				);

			},
			changeNumberContents() {
				
				this.pagination.current_page = 1;

				if (this.searchAttributes.search === '') {

					this.fetchAllMerchantDeals();

				}
				else {

					this.searchData();

				}

    		},
    		resetSearchingDates(){

            	this.searchAttributes.dates = {};
				this.searchAttributes.dateTo = null;
				this.searchAttributes.dateFrom = null;				

            },
            setSearchingDates(){

            	if (Object.keys(this.searchAttributes.dates).length > 0 && this.searchAttributes.dates.hasOwnProperty('start') && this.searchAttributes.dates.hasOwnProperty('end')) {

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
    		objectNameWithCapitalized ({ name }) {
		      	
		      	if (name) {

				    name = name.toString();
				    return name.charAt(0).toUpperCase() + name.slice(1);

		      	}

		      	else 
		      		return ''

		    },
			nameWithCapitalized (name) {
				
				if (!name) return ''

				const words = name.split(" ");

				for (let i = 0; i < words.length; i++) {
				    words[i] = words[i][0].toUpperCase() + words[i].substr(1);
				}

				return words.join(" ");

		    },
			setRelatedShelvesAndUnits() {

				if (this.singleMerchantDealData.warehouses.length) {

					// last warehouse (which is editable)
					this.resetWarehouseContainers(this.singleMerchantDealData.warehouses.length-1);

					// this.singleMerchantDealData.warehouses.forEach(
						
						// (merchantWarehouse, merchantWarehouseIndex) => {

					this.singleMerchantDealData.warehouses[this.singleMerchantDealData.warehouses.length-1].spaces.forEach(
						
						(warehouseDealtSpace, warehouseDealtSpaceIndex) => {

							if (warehouseDealtSpace.type=='containers') {

							}
							else if (warehouseDealtSpace.type=='shelves') {

								let searchedContainer = this.emptyShelfContainers.find(
									container => container.id==warehouseDealtSpace.container.id && container.name==warehouseDealtSpace.container.name && container.warehouse_container_id==warehouseDealtSpace.container.warehouse_container_id
								)

								if (searchedContainer) {

									this.emptyShelves = searchedContainer.container_shelf_statuses;
								}
								
							}
							else if (warehouseDealtSpace.type=='units') {

								const containerExists = container => container.id==warehouseDealtSpace.container.id && container.name==warehouseDealtSpace.container.name && container.warehouse_container_id==warehouseDealtSpace.container.warehouse_container_id;

								if (this.emptyUnitContainers.some(containerExists)) {

									let searchedContainer = this.emptyUnitContainers.find(
										container => container.id==warehouseDealtSpace.container.id && container.name==warehouseDealtSpace.container.name && container.warehouse_container_id==warehouseDealtSpace.container.warehouse_container_id
									);

									if (searchedContainer) {

										this.emptyUnitShelves = searchedContainer.container_shelf_statuses;

										let searchedShelf = searchedContainer.container_shelf_statuses.find(
											shelf => shelf.id==warehouseDealtSpace.container.shelf.id && shelf.name==warehouseDealtSpace.container.shelf.name && shelf.warehouse_container_id==warehouseDealtSpace.container.shelf.warehouse_container_id &&  shelf.warehouse_container_status_id==warehouseDealtSpace.container.shelf.warehouse_container_status_id 
										);

										if (searchedShelf) {

											this.emptyUnits = searchedShelf.container_shelf_unit_statuses;

										}

									}

								}

							}
							
						}

					);

						// }

					// );

				}

			},	
			setMerchantDealsPagination(response) {

				this.pagination = response.data;
				this.merchantAllDeals = response.data.data;

			},
			setContainerAvailableShelves(merchantWarehouseIndex, selectedSpaceIndex) {
				
				if (this.singleMerchantDealData.warehouses.length > merchantWarehouseIndex && this.singleMerchantDealData.warehouses[merchantWarehouseIndex].spaces.length > selectedSpaceIndex) {

					this.$delete(this.singleMerchantDealData.warehouses[merchantWarehouseIndex].spaces[selectedSpaceIndex].container, 'shelves');

					this.emptyShelves = this.singleMerchantDealData.warehouses[merchantWarehouseIndex].spaces[selectedSpaceIndex].container.container_shelf_statuses;

					this.setShelfOrUnitRentPeriod(this.singleMerchantDealData.warehouses[merchantWarehouseIndex].spaces[selectedSpaceIndex].container);

				}
				else {

					this.emptyShelves = [];

				}

			},
			setContainerAvailableUnitShelves(merchantWarehouseIndex, selectedSpaceIndex) {
				
				if(this.singleMerchantDealData.warehouses.length > merchantWarehouseIndex && this.singleMerchantDealData.warehouses[merchantWarehouseIndex].spaces.length > selectedSpaceIndex){

					if (this.singleMerchantDealData.warehouses[merchantWarehouseIndex].spaces[selectedSpaceIndex].container && Object.keys(this.singleMerchantDealData.warehouses[merchantWarehouseIndex].spaces[selectedSpaceIndex].container).length > 0) {

						this.$delete(this.singleMerchantDealData.warehouses[merchantWarehouseIndex].spaces[selectedSpaceIndex].container, 'shelf');

						this.emptyUnitShelves = this.singleMerchantDealData.warehouses[merchantWarehouseIndex].spaces[selectedSpaceIndex].container.container_shelf_statuses;

						this.setShelfOrUnitRentPeriod(this.singleMerchantDealData.warehouses[merchantWarehouseIndex].spaces[selectedSpaceIndex].container)
						
					}

				}

			},
			setContainerShelfAvailableUnits(merchantWarehouseIndex, selectedSpaceIndex) {
				
				if (this.singleMerchantDealData.warehouses.length > merchantWarehouseIndex && this.singleMerchantDealData.warehouses[merchantWarehouseIndex].spaces.length > selectedSpaceIndex) {

					if (this.singleMerchantDealData.warehouses[merchantWarehouseIndex].spaces[selectedSpaceIndex].container && Object.keys(this.singleMerchantDealData.warehouses[merchantWarehouseIndex].spaces[selectedSpaceIndex].container).length > 0 && Object.keys(this.singleMerchantDealData.warehouses[merchantWarehouseIndex].spaces[selectedSpaceIndex].container.shelf).length > 0) {

						this.$delete(this.singleMerchantDealData.warehouses[merchantWarehouseIndex].spaces[selectedSpaceIndex].container.shelf, 'units');

						this.emptyUnits = this.singleMerchantDealData.warehouses[merchantWarehouseIndex].spaces[selectedSpaceIndex].container.shelf.container_shelf_unit_statuses;

					}

				}

			},
			setWarehouseSpaces(merchantWarehouseIndex, warehouseSpaceIndex) {

				if(this.singleMerchantDealData.warehouses.length > merchantWarehouseIndex && this.singleMerchantDealData.warehouses[merchantWarehouseIndex].spaces.length > warehouseSpaceIndex) {

					// resetting

					if (this.singleMerchantDealData.warehouses[merchantWarehouseIndex].spaces[warehouseSpaceIndex].type=='containers') {
						
						this.$set(this.singleMerchantDealData.warehouses[merchantWarehouseIndex].spaces[warehouseSpaceIndex], 'containers', [ {} ]);

						this.$delete(this.singleMerchantDealData.warehouses[merchantWarehouseIndex].spaces[warehouseSpaceIndex], 'container');


					} else {

						this.$set(this.singleMerchantDealData.warehouses[merchantWarehouseIndex].spaces[warehouseSpaceIndex], 'container', {});

						this.$delete(this.singleMerchantDealData.warehouses[merchantWarehouseIndex].spaces[warehouseSpaceIndex], 'containers');
						
					}

				}
				
				if(this.errors.warehouses.length > merchantWarehouseIndex && this.errors.warehouses[merchantWarehouseIndex].spaces.length > warehouseSpaceIndex){

					this.errors.warehouses[merchantWarehouseIndex].spaces[warehouseSpaceIndex] = {};

				}

				this.resetAvailableSpaces(merchantWarehouseIndex);
		
			},
			resetWarehouseContainers(merchantWarehouseIndex) {
				
				let expectedWarehouseIndex = this.availableWarehouseSpaces.findIndex(
					(warehouse) => (warehouse.id == this.singleMerchantDealData.warehouses[merchantWarehouseIndex].id && warehouse.name == this.singleMerchantDealData.warehouses[merchantWarehouseIndex].name)
				);

				if (expectedWarehouseIndex > -1) {

					this.emptyContainers = JSON.parse( JSON.stringify( this.availableWarehouseSpaces[expectedWarehouseIndex].emptyContainers ) );
					this.emptyShelfContainers = JSON.parse( JSON.stringify( this.availableWarehouseSpaces[expectedWarehouseIndex].emptyShelfContainers ) );
					this.emptyUnitContainers = JSON.parse( JSON.stringify( this.availableWarehouseSpaces[expectedWarehouseIndex].emptyUnitContainers ) );

				}

				else {

					this.emptyContainers = [];
					this.emptyShelfContainers = [];
					this.emptyUnitContainers = [];

				}

			},
			resetAvailableSpaces(merchantWarehouseIndex) {

				this.resetWarehouseContainers(merchantWarehouseIndex);

				if(this.singleMerchantDealData.warehouses.length > merchantWarehouseIndex && this.singleMerchantDealData.warehouses[merchantWarehouseIndex].spaces.length) {

					this.singleMerchantDealData.warehouses[merchantWarehouseIndex].spaces.forEach(

						(warehouseSpace, warehouseSpaceIndex) => {
							
							if (warehouseSpace.type=='containers' && warehouseSpace.containers && warehouseSpace.containers.length) {

								// for every selected container
								warehouseSpace.containers.forEach(

									(selectedContainer) => {

										// same division
										// emptyContainers
										var selectedContainerIndex = this.emptyContainers.findIndex(
											(currentContainer) => 
												currentContainer.id == selectedContainer.id && currentContainer.name == selectedContainer.name && currentContainer.warehouse_container_id == selectedContainer.warehouse_container_id
											
										);

										if (selectedContainerIndex > -1) {

											this.emptyContainers.splice(selectedContainerIndex, 1);
										
										}

										// downward
										// emptyShelfContainers
										var selectedContainerIndex = this.emptyShelfContainers.findIndex(
											(currentContainer) => 
												currentContainer.id == selectedContainer.id && currentContainer.name == selectedContainer.name && currentContainer.warehouse_container_id == selectedContainer.warehouse_container_id
											
										);

										if (selectedContainerIndex > -1) {

											this.emptyShelfContainers.splice(selectedContainerIndex, 1);
										
										}

										// downward
										// emptyUnitContainers
										var selectedContainerIndex = this.emptyUnitContainers.findIndex(
											(currentContainer) => 
												currentContainer.id == selectedContainer.id && currentContainer.name == selectedContainer.name && currentContainer.warehouse_container_id == selectedContainer.warehouse_container_id
											
										);

										if (selectedContainerIndex > -1) {

											this.emptyUnitContainers.splice(selectedContainerIndex, 1);

										}

									}

								);

							}
							else if (warehouseSpace.type=='shelves' && warehouseSpace.container && warehouseSpace.container.shelves && warehouseSpace.container.shelves.length) {

								// same division
								// emptyShelfContainers
								this.emptyShelfContainers.forEach(

									(emptyShelfContainer) => {

										if (emptyShelfContainer.id == warehouseSpace.container.id && emptyShelfContainer.name == warehouseSpace.container.name && emptyShelfContainer.warehouse_container_id == warehouseSpace.container.warehouse_container_id) {
											
											// for every selected shelves
											warehouseSpace.container.shelves.forEach(

												(selectedShelf) => {

													var selectedShelfIndex = emptyShelfContainer.container_shelf_statuses.findIndex(
														(containerShelf) => 
															containerShelf.id == selectedShelf.id && containerShelf.name == selectedShelf.name && containerShelf.warehouse_container_status_id == selectedShelf.warehouse_container_status_id
													);

													if (selectedShelfIndex > -1) {

														emptyShelfContainer.container_shelf_statuses.splice(selectedShelfIndex, 1);
													}

												}

											);

										}

									}
									
								);

								// upward
								// emptyContainers
								var selectedContainerIndex = this.emptyContainers.findIndex(
									(currentContainer) => 
										currentContainer.id == warehouseSpace.container.id && currentContainer.name == warehouseSpace.container.name && currentContainer.warehouse_container_id == warehouseSpace.container.warehouse_container_id
								);

								if (selectedContainerIndex > -1) {

									this.emptyContainers.splice(selectedContainerIndex, 1);

								}

								// downward
								// emptyUnitContainers
								this.emptyUnitContainers.forEach(

									(emptyUnitContainer) => {

										if (emptyUnitContainer.id==warehouseSpace.container.id && emptyUnitContainer.name==warehouseSpace.container.name && emptyUnitContainer.warehouse_container_id==warehouseSpace.container.warehouse_container_id) {
											
											// for every selected shelves
											warehouseSpace.container.shelves.forEach(

												(selectedShelf) => {

													// unit
													var selectedShelfIndex = emptyUnitContainer.container_shelf_statuses.findIndex(
														(containerShelf) => 
															containerShelf.id == selectedShelf.id && containerShelf.name == selectedShelf.name && containerShelf.warehouse_container_status_id == selectedShelf.warehouse_container_status_id
													);

													if (selectedShelfIndex > -1) {

														emptyUnitContainer.container_shelf_statuses.splice(selectedShelfIndex, 1);
													}

												}

											);

										}

									}

								);

							}
							else if (warehouseSpace.type=='units' && warehouseSpace.container && warehouseSpace.container.shelf && warehouseSpace.container.shelf.units && warehouseSpace.container.shelf.units.length) {

								// same division
								// emptyUnitContainers
								this.emptyUnitContainers.forEach(

									(emptyUnitContainer) => {

										if (emptyUnitContainer.id==warehouseSpace.container.id && emptyUnitContainer.name==warehouseSpace.container.name && emptyUnitContainer.warehouse_container_id==warehouseSpace.container.warehouse_container_id) {

											var selectedShelfIndex = emptyUnitContainer.container_shelf_statuses.findIndex(
												(containerShelf) => 
													containerShelf.id == warehouseSpace.container.shelf.id && containerShelf.name == warehouseSpace.container.shelf.name && containerShelf.warehouse_container_status_id == warehouseSpace.container.shelf.warehouse_container_status_id
											);

											if(selectedShelfIndex > -1) {
												
												// for every selected units
												warehouseSpace.container.shelf.units.forEach(

													(selectedUnit) => {

														// unit
														var selectedUnitIndex = emptyUnitContainer.container_shelf_statuses[selectedShelfIndex].container_shelf_unit_statuses.findIndex(
															(shelfUnit) => 
																shelfUnit.id == selectedUnit.id && shelfUnit.name == selectedUnit.name && shelfUnit.warehouse_container_shelf_status_id == selectedUnit.warehouse_container_shelf_status_id
														);

														if (selectedUnitIndex > -1) {

															emptyUnitContainer.container_shelf_statuses[selectedShelfIndex].container_shelf_unit_statuses.splice(selectedUnitIndex, 1);
														}

													}

												);

											}

										}

									}

								);

								// upward
								// emptyContainers
								var selectedContainerIndex = this.emptyContainers.findIndex(
									(currentContainer) => 
										currentContainer.id == warehouseSpace.container.id && currentContainer.name == warehouseSpace.container.name && currentContainer.warehouse_container_id == warehouseSpace.container.warehouse_container_id
								);

								if (selectedContainerIndex > -1) {

									this.emptyContainers.splice(selectedContainerIndex, 1);

								}

								// upward
								// emptyShelfContainers
								this.emptyShelfContainers.forEach(

									(emptyShelfContainer) => {

										if (emptyShelfContainer.id == warehouseSpace.container.id && emptyShelfContainer.name == warehouseSpace.container.name && emptyShelfContainer.warehouse_container_id == warehouseSpace.container.warehouse_container_id) {


											var selectedShelfIndex = emptyShelfContainer.container_shelf_statuses.findIndex(
												(currentShelf) => currentShelf.id == warehouseSpace.container.shelf.id && currentShelf.name == warehouseSpace.container.shelf.name && currentShelf.warehouse_container_status_id == warehouseSpace.container.shelf.warehouse_container_status_id
											)

											if (selectedShelfIndex > -1) {

												emptyShelfContainer.container_shelf_statuses.splice(selectedShelfIndex, 1);

											}

										}

									}
									
								);

							}
						}
					);

				}

			},
			setTotalRent() {

				let totalRent = 0;

				if(this.singleMerchantDealData.warehouses.length > 0) {

					this.singleMerchantDealData.warehouses.forEach(

						(merchantWarehouse, merchantWarehouseIndex) => {
							
							if (merchantWarehouse.spaces.length > 0) {
								
								merchantWarehouse.spaces?.forEach(

									(warehouseSpace, warehouseSpaceIndex) => {
										
										if (warehouseSpace.type=='containers' && warehouseSpace.containers && warehouseSpace.containers.length) {

											// for every selected container
											warehouseSpace.containers.forEach(

												(selectedContainer) => {

													// totalRent += selectedContainer.selected_rent.rent ?? 0;
													
													totalRent += selectedContainer.rents.find(rent => rent.rent_period_id==this.singleMerchantDealData.rent_period_id).rent ?? 0;
													
													// console.log('Containers index : ' + warehouseSpaceIndex + ' rent : ' + totalRent);

												}

											);

										}
										else if (warehouseSpace.type=='shelves' && warehouseSpace.container && warehouseSpace.container.shelves && warehouseSpace.container.shelves.length) {

											// for every selected shelves
											// totalRent += warehouseSpace.container.shelves.length * warehouseSpace.container.selected_rent.rent ?? 0
											
											totalRent += warehouseSpace.container.shelves.length * warehouseSpace.container.rents.find(rent => rent.rent_period_id==this.singleMerchantDealData.rent_period_id).rent ?? 0;

											// console.log('Shelves : ' + totalRent);

										}
										else if (warehouseSpace.type=='units' && warehouseSpace.container && warehouseSpace.container.shelf && warehouseSpace.container.shelf.units && warehouseSpace.container.shelf.units.length) {

											// for every selected shelves
											// totalRent += warehouseSpace.container.shelf.units.length * warehouseSpace.container.selected_rent.rent ?? 0
											
											totalRent += warehouseSpace.container.shelf.units.length * warehouseSpace.container.rents.find(rent => rent.rent_period_id==this.singleMerchantDealData.rent_period_id).rent ?? 0;

											// console.log('Units : ' + totalRent);

										}
										else {
											// console.log('Out of type');
										}
									}
								);

								totalRent = totalRent * this.singleMerchantDealData.payments[0].number_installment;

							}
							else {
								// console.log('Out of spaces length');
							}

						}

					);

				}

				// console.log('Out of warehouses length');

				this.changeRentExpiryDate();
				
				this.singleMerchantDealData.payments[0].total_rent = totalRent - (totalRent * this.singleMerchantDealData.payments[0].discount / 100);

			},
			setNetPayable() {

				this.singleMerchantDealData.payments[0].net_payable = this.singleMerchantDealData.payments[this.singleMerchantDealData.payments.length-1].previous_due + this.singleMerchantDealData.payments[this.singleMerchantDealData.payments.length-1].total_rent;

			},
			resetTotalRent() {
				
				this.validateFormInput('discount');
				this.validateFormInput('number_installment');
				
				if (! this.errors.payment.discount && ! this.errors.payment.number_installment) {

					this.setTotalRent();
					this.setNetPayable();	

				}
				
			},	
			setContainerRentPeriod(merchantContainer) {

				this.$set(merchantContainer, 'selected_rent', {});

				if (merchantContainer && Object.keys(merchantContainer).length > 0 && merchantContainer.rents) {

					merchantContainer.selected_rent = merchantContainer.rents.find(containerRent => containerRent.rent_period_id==this.singleMerchantDealData.rent_period_id);

				}

			},
			setShelfOrUnitRentPeriod(merchantContainer) {

				this.$set(merchantContainer, 'selected_rent', {});

				if (merchantContainer && Object.keys(merchantContainer).length > 0 && merchantContainer.rents) {

					merchantContainer.selected_rent = merchantContainer.rents.find(containerRent => containerRent.rent_period_id==this.singleMerchantDealData.rent_period_id);

				}

			},
			changeRentExpiryDate() {

				var moment = require('moment');

				let daysToIncrease = this.singleMerchantDealData.payments[0].number_installment * this.singleMerchantDealData.rent_period.number_days;

				this.singleMerchantDealData.payments[0].date_from = moment(this.singleMerchantDealData.payments[0].date_from).format('YYYY-MM-DD');

				this.singleMerchantDealData.payments[0].date_to = moment(this.singleMerchantDealData.payments[0].date_from).add(daysToIncrease, 'days').format('YYYY-MM-DD');
				
				
			},
			customFormatter(date) {
				var moment = require('moment');
				date = moment(date).format('YYYY-MM-DD');
		      	return date;
		    },
			validateFormInput (formInputName) {

				this.submitForm = false;

				switch(formInputName) {

					case 'sale_percentage' :

						if(this.singleMerchantDealData.e_commerce_fulfillment && (this.singleMerchantDealData.sale_percentage < 0 || this.singleMerchantDealData.sale_percentage > 100)){

							this.errors.sale_percentage = 'Percentage should be between 0 to 100';
						}
						else {
							
							this.submitForm = true;
							this.$delete(this.errors, 'sale_percentage');
							
						}

						break;

					case 'rent_period' :

						if(! this.singleMerchantDealData.rent_period || Object.keys(this.singleMerchantDealData.rent_period).length === 0){

							this.errors.rent_period = 'Rent period is required';
						}
						else {
							
							this.submitForm = true;
							this.$delete(this.errors, 'rent_period');
							
						}

						break;

					case 'warehouse' :

						if(this.step==2 && (! this.singleMerchantDealData.hasOwnProperty('warehouses') || this.singleMerchantDealData.warehouses.length < 1)){

							this.errors.warehouse = 'Warehouse is required';
						}
						else if (this.step==2 && this.singleMerchantDealData.warehouses.some(merchantWarehouse => ! merchantWarehouse || Object.keys(merchantWarehouse).length === 0)) {

							this.errors.hasOwnProperty('warehouse') ? this.$delete(this.errors, 'warehouse') : '';

							this.errors.warehouses[ this.singleMerchantDealData.warehouses.findIndex(merchantWarehouse => ! merchantWarehouse || Object.keys(merchantWarehouse).length === 0) ].warehouse = 'Warehouse is required';

						}
						else if (this.step==2 && this.singleMerchantDealData.warehouses.some((merchantWarehouse, merchantWarehouseIndex) => this.singleMerchantDealData.warehouses.some((selectedWarehouse, selectedWarehouseIndex) => selectedWarehouse.id==merchantWarehouse.id && selectedWarehouse.name==merchantWarehouse.name && merchantWarehouseIndex!=selectedWarehouseIndex))) {

							this.errors.warehouses[this.errors.warehouses.length-1].warehouse = 'Same warehouse is selected';

						}
						else {

							this.submitForm = true;
							this.errors.hasOwnProperty('warehouse') ? this.$delete(this.errors, 'warehouse') : '';
							this.errors.hasOwnProperty('warehouses') && this.errors.warehouses.length ? this.$delete(this.errors.warehouses[this.errors.warehouses.length-1], 'warehouse') : '';
						
						}

						break;

					case 'space' :

						this.singleMerchantDealData.warehouses.forEach(
							
							(merchantWarehouse, merchantWarehouseIndex) => {

								if(merchantWarehouse && (! merchantWarehouse.hasOwnProperty('spaces') || ! merchantWarehouse.spaces.length)){

									this.errors.warehouses[merchantWarehouseIndex].space = 'Space is required';
								}
								else {

									this.submitForm = true;
									this.$delete(this.errors.warehouses[merchantWarehouseIndex], 'space');
								
								}


							}

						);

						if (!this.errorInWarehousesArray(this.errors.warehouses)) {
							this.submitForm = true;
						}			
					
						break;					
					
					case 'space_type' :

						this.singleMerchantDealData.warehouses.forEach(
							
							(merchantWarehouse, merchantWarehouseIndex) => {

								merchantWarehouse.spaces?.forEach(

									(warehouseSpace, warehouseSpaceIndex) => {

										if (! warehouseSpace.type) {

											this.errors.warehouses[merchantWarehouseIndex].spaces[warehouseSpaceIndex].space_type = 'Space type is required';

										}
									
										else {

											this.$delete(this.errors.warehouses[merchantWarehouseIndex].spaces[warehouseSpaceIndex], 'space_type');

										}

									}

								);

							}

						);
						
						if (!this.errorInWarehousesArray(this.errors.warehouses)) {
							this.submitForm = true;
						}

						break;

					case 'containers' :

						this.singleMerchantDealData.warehouses.forEach(
							
							(merchantWarehouse, merchantWarehouseIndex) => {

								merchantWarehouse.spaces?.forEach(
									(warehouseSpace, warehouseSpaceIndex) => {

										if (warehouseSpace.type=='containers' && (! warehouseSpace.containers || warehouseSpace.containers.length == 0 || warehouseSpace.containers.some(warehouseContainer => ! warehouseContainer || Object.keys(warehouseContainer).length==0))) {
											
											this.errors.warehouses[merchantWarehouseIndex].spaces[warehouseSpaceIndex].containers = 'Container is required';
										}
										else if (warehouseSpace.type=='containers' && warehouseSpace.containers.some((warehouseContainer, warehouseContainerIndex) => warehouseSpace.containers.some((selectedContainer, selectedContainerIndex) => selectedContainer.id==warehouseContainer.id && warehouseContainerIndex != selectedContainerIndex))) {
											
											this.errors.warehouses[merchantWarehouseIndex].spaces[warehouseSpaceIndex].containers = 'Same container is selected';
										}
										else{
											
											this.$delete(this.errors.warehouses[merchantWarehouseIndex].spaces[warehouseSpaceIndex], 'containers');
										}

									}
								);

							}

						);

						if (!this.errorInWarehousesArray(this.errors.warehouses)) {
							this.submitForm = true;
						}

						break;

					case 'parent_container' :

						this.singleMerchantDealData.warehouses.forEach(
							
							(merchantWarehouse, merchantWarehouseIndex) => {

								merchantWarehouse.spaces?.forEach(
									(warehouseSpace, warehouseSpaceIndex) => {

										if ((warehouseSpace.type=='shelves' || warehouseSpace.type=='units') && (! warehouseSpace.container || Object.keys(warehouseSpace.container).length==0)) {
											this.errors.warehouses[merchantWarehouseIndex].spaces[warehouseSpaceIndex].parent_container = 'Container is required';
										}
										else{
											this.$delete(this.errors.warehouses[merchantWarehouseIndex].spaces[warehouseSpaceIndex], 'parent_container');
										}

									}
								);

							}

						);

						if (!this.errorInWarehousesArray(this.errors.warehouses)) {
							this.submitForm = true;
						}

						break;

					case 'shelves' : 

						this.singleMerchantDealData.warehouses.forEach(
							
							(merchantWarehouse, merchantWarehouseIndex) => {

								merchantWarehouse.spaces?.forEach(
									(warehouseSpace, warehouseSpaceIndex) => {

										if (warehouseSpace.type=='shelves' && (! warehouseSpace.container || ! warehouseSpace.container.shelves || warehouseSpace.container.shelves.length == 0)) {
											this.errors.warehouses[merchantWarehouseIndex].spaces[warehouseSpaceIndex].shelves = 'Shelf is required';
										}
										else{
											this.$delete(this.errors.warehouses[merchantWarehouseIndex].spaces[warehouseSpaceIndex], 'shelves');
										}

									}
								);

							}

						);

						if (!this.errorInWarehousesArray(this.errors.warehouses)) {
							this.submitForm = true;
						}

						break;

					case 'parent_shelf' :

						this.singleMerchantDealData.warehouses.forEach(
							
							(merchantWarehouse, merchantWarehouseIndex) => {

								merchantWarehouse.spaces?.forEach(
									(warehouseSpace, warehouseSpaceIndex) => {

										if (warehouseSpace.type=='units' && (! warehouseSpace.container || ! warehouseSpace.container.shelf || Object.keys(warehouseSpace.container.shelf).length==0)) {
											this.errors.warehouses[merchantWarehouseIndex].spaces[warehouseSpaceIndex].parent_shelf = 'Shelf is required';
										}
										else{
											this.$delete(this.errors.warehouses[merchantWarehouseIndex].spaces[warehouseSpaceIndex], 'parent_shelf');
										}

									}
								);

							}

						);

						if (!this.errorInWarehousesArray(this.errors.warehouses)) {
							this.submitForm = true;
						}

						break;

					case 'units' : 

						this.singleMerchantDealData.warehouses.forEach(
							
							(merchantWarehouse, merchantWarehouseIndex) => {

								merchantWarehouse.spaces?.forEach(
									(warehouseSpace, warehouseSpaceIndex) => {

										if (warehouseSpace.type=='units' && (! warehouseSpace.container || ! warehouseSpace.container.shelf || ! warehouseSpace.container.shelf.units || warehouseSpace.container.shelf.units.length == 0)) {
											this.errors.warehouses[merchantWarehouseIndex].spaces[warehouseSpaceIndex].units = 'Unit is required';
										}
										else{
											this.$delete(this.errors.warehouses[merchantWarehouseIndex].spaces[warehouseSpaceIndex], 'units');
										}

									}
								);

							}

						);

						if (!this.errorInWarehousesArray(this.errors.warehouses)) {
							this.submitForm = true;
						}

						break;
						
					case 'space_rent_period' : 

						this.singleMerchantDealData.warehouses.forEach(
							
							(merchantWarehouse, merchantWarehouseIndex) => {

								merchantWarehouse.spaces?.forEach(

									(warehouseSpace, warehouseSpaceIndex) => {

										if (warehouseSpace.type=='containers' && warehouseSpace.containers && warehouseSpace.containers.length && warehouseSpace.containers.some(warehouseContainer => ! warehouseContainer.selected_rent || Object.keys(warehouseContainer.selected_rent).length==0)) {
											this.errors.warehouses[merchantWarehouseIndex].spaces[warehouseSpaceIndex].space_rent_period = 'Rent package is required';
										}
										else if (warehouseSpace.type=='shelves' && warehouseSpace.container && warehouseSpace.container.shelves && warehouseSpace.container.shelves.length && (! warehouseSpace.container.selected_rent || Object.keys(warehouseSpace.container.selected_rent).length==0)) {
											this.errors.warehouses[merchantWarehouseIndex].spaces[warehouseSpaceIndex].space_rent_period = 'Rent package is required';
										}
										else if (warehouseSpace.type=='units' && warehouseSpace.container && warehouseSpace.container.shelf && warehouseSpace.container.shelf.units && warehouseSpace.container.shelf.units.length && (! warehouseSpace.container.selected_rent || Object.keys(warehouseSpace.container.selected_rent).length==0)) {
											this.errors.warehouses[merchantWarehouseIndex].spaces[warehouseSpaceIndex].space_rent_period = 'Rent package is required';
										}
										else{
											this.$delete(this.errors.warehouses[merchantWarehouseIndex].spaces[warehouseSpaceIndex], 'space_rent_period');
										}

									}
									
								);

							}

						);

						if (!this.errorInWarehousesArray(this.errors.warehouses)) {
							this.submitForm = true;
						}

						break;

					case 'paid_amount' : 
						
						if(! this.singleMerchantDealData.payments || ! this.singleMerchantDealData.payments[0].paid_amount || this.singleMerchantDealData.payments[0].paid_amount < 1){
							
							this.errors.payment.paid_amount = 'Paid amount is required';

						}
						else {

							this.submitForm = true;
							this.$delete(this.errors.payment, 'paid_amount');

						}

						break;

					case 'discount' : 
						
						if(this.singleMerchantDealData.payments && this.singleMerchantDealData.payments[0].discount && (this.singleMerchantDealData.payments[0].discount < 0 || this.singleMerchantDealData.payments[0].discount > 100)){

							this.errors.payment.discount = 'Rate should be between 0 to 100';
						}
						else {

							this.submitForm = true;
							this.$delete(this.errors.payment, 'discount');

						}

						break;

					case 'number_installment' : 
						
						if(this.singleMerchantDealData.payments && (! this.singleMerchantDealData.payments[0].number_installment || this.singleMerchantDealData.payments[0].number_installment < 1)){

							this.errors.payment.number_installment = 'Number should be positive';
						}
						else {

							this.submitForm = true;
							this.$delete(this.errors.payment, 'number_installment');

						}

						break;

				}
	 
			},
            
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
	.date {
		background: #f2f2f2;
		border: 1px solid #ddd;
		padding: 1em 1em 1em;
	}
</style>