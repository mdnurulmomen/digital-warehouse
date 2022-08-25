
<template>
	<div class="pcoded-content">
		<breadcrumb 
			:icon="'rents'"
			:title="'payment'" 
			:message="'All rents of ' + merchant.user_name.replace(/ /g,'-') + ' for the deal ' + deal.name"
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
											  		@fetchAllContents="setDealAllRents()"
											  	></search-and-addition-option> 
											  	-->

											  	<div class="row form-group">
											  		<div class="col-md-4 col-sm-6 d-flex align-items-center form-group">
											  			<div class="mr-2">
											  				<span>
													  			{{ 
													  				( /* searchAttributes.showPendingRequisitions || searchAttributes.showCancelledRequisitions || searchAttributes.showDispatchedRequisitions || searchAttributes.showProduct || */ searchAttributes.search || searchAttributes.dateFrom || searchAttributes.dateTo) ? 'Searched Rents List' : 'Rents List'
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
																	worksheet="Rents sheet"
																	:name="((searchAttributes.search != '' || searchAttributes.dateFrom || searchAttributes.dateTo) ? 'searched-rents-' : (currentTab + '-rents-list-')) + today + '-page-' + pagination.current_page + '.xls'"
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
																		data-target="#payment-custom-search"
																		:class="{ 'active': Object.keys(searchAttributes.dates).length > 0 }"
																	>
																		<i class="fa fa-ellipsis-v fa-lg p-2"></i>
																	</a>
																</li>
															</ul>
													  	</div>
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
																			@click="changeNamesOrder"
																		> 
																			ID
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
																		{{ content.name | capitalize }}
																	</td>

																	<td>
																		{{ content.total_rent }}
																		{{ general_settings.official_currency_name | capitalize }}
																	</td>

																	<td>
																		{{ content.discount }} %
																	</td>

																	<td>
																		{{ content.total_paid_amount }}
																		{{ general_settings.official_currency_name | capitalize }}
																	</td>

																	<td>
																		{{ (content.net_payable - content.total_paid_amount) }}
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
																	</td>
																</tr>

																<tr 
															  		v-show="! dealAllRents.length"
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
																			ID
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
																@click="pagination.current_page = 1; searchAttributes.search === '' ? fetchAllRents() : searchData()"
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
																@paginate="searchAttributes.search === '' ? fetchAllRents() : searchData()"
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

		<!-- View Modal -->
		<div class="modal fade" id="merchant-payment-view-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Rent ({{ singleRentData.name | capitalize }}) Details</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="modal-body">
						<div class="form-row">
							<label class="col-6 col-form-label font-weight-bold text-right">
								ID :
							</label>

							<label class="col-6 col-form-label">
								{{ singleRentData.name | capitalize }}
							</label>
						</div>

						<div class="form-row">
							<label class="col-6 col-form-label font-weight-bold text-right">
								# Installment :
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
								{{ singleRentData.total_rent }} {{ general_settings.official_currency_name | capitalize }}
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

						<div class="form-row">
							<label class="col-6 col-form-label font-weight-bold text-right">
								Net Payable :
							</label>

							<label class="col-6 col-form-label">
								{{ singleRentData.net_payable }} {{ general_settings.official_currency_name | capitalize }}
							</label>
						</div>

						<div class="form-row">
							<label class="col-6 col-form-label font-weight-bold text-right">
								Total Paid :
							</label>

							<label class="col-6 col-form-label">
								{{ singleRentData.total_paid_amount }} {{ general_settings.official_currency_name | capitalize }}
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

						<div 
							class="form-row" 
							v-if="singleRentData.hasOwnProperty('spaceRents') && singleRentData.spaceRents.length"
						>
							<div 
								class="col-sm-6" 
								v-for="(paymentRent, paymentRentIndex) in singleRentData.spaceRents" 
								:key="'payment-rent-' + paymentRentIndex + '-id-' + paymentRent.id"
							>
								<div class="card card-body">
									<div class="form-row">
										<label class="col-6 col-form-label font-weight-bold text-right">
											Space Type :
										</label>

										<label class="col-6 col-form-label">
											{{ paymentRent.dealt_space ? (paymentRent.dealt_space.type.includes('WarehouseContainerStatus') ? 'Container' :(paymentRent.dealt_space.type.includes('WarehouseContainerShelfStatus') ? 'Shelf' : 'Unit')) : 'NA' }}
										</label>	
									</div>

									<div class="form-row">
										<label class="col-6 col-form-label font-weight-bold text-right">
											Space Name :
										</label>

										<label class="col-6 col-form-label">
											{{ paymentRent.dealt_space ? paymentRent.dealt_space.name : 'NA' | capitalize }}
										</label>	
									</div>
								
									<div class="form-row">
										<label class="col-6 col-form-label font-weight-bold text-right">
											Rent :
										</label>

										<label class="col-6 col-form-label">
											{{ paymentRent.rent }} {{ general_settings.official_currency_name | capitalize }}
										</label>	
									</div>
								</div>
							</div>
						</div>
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
									{{ singleRentData.total_rent }} {{ general_settings.official_currency_name | capitalize }}
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
									{{ singleRentData.total_paid_amount }} {{ general_settings.official_currency_name | capitalize }}
								</label>
							</div>

							<div class="form-row">
								<label class="col-6 col-form-label font-weight-bold">
									Due :
								</label>

								<label class="col-6 col-form-label">
									{{ (singleRentData.net_payable - singleRentData.total_paid_amount) }} {{ general_settings.official_currency_name | capitalize }}
								</label>
							</div>
						</div>
						
						<div class="col-6">
							<div class="form-row">
								<label class="col-6 col-form-label font-weight-bold text-right">
									Merchant Name :
								</label>

								<label class="col-6 col-form-label">
									{{ merchant.user_name.replace(/ /g,"-") | capitalize }}
								</label>
							</div>

							<div class="form-row">
								<label class="col-6 col-form-label font-weight-bold text-right">
									# Installment :
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
						<div class="col-12">
							<table class="table table-striped table-bordered nowrap text-center">
								<thead>
									<tr>
										<th>Space Type</th>
										<th>Space Name</th>
										<th>Rent</th>
									</tr>
								</thead>

								<tbody>
									<tr 
										v-for="(paymentRent, paymentRentIndex) in singleRentData.spaceRents" 
										:key="'payment-rent-' + paymentRentIndex + '-id-' + paymentRent.id"
									>	
										<td>
											{{ (paymentRent.dealt_space ? (paymentRent.dealt_space.type.includes('WarehouseContainerStatus') ? 'Container' : (paymentRent.dealt_space.type.includes('WarehouseContainerShelfStatus') ? 'Shelf' : 'Unit')) : 'NA') }}
										</td>

										<td>
											{{ paymentRent.dealt_space ? paymentRent.dealt_space.name : 'NA' | capitalize }}
										</td>
										
										<td>
											{{ paymentRent.rent }} {{ general_settings.official_currency_name | capitalize }}
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
		<div class="modal fade" id="payment-custom-search" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
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
	</div>
</template>

<script type="text/javascript">

	import axios from 'axios';
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
		// merchant_space_deal_id : null,
		// created_at : null,
		spaceRents : [
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
			dealId:{
				type: Number,
				required: true,
			},

		},

	    data() {

	        return {

	        	error : '',
    			perPage : 10,
	        	loading : false,

	        	ascending : false,
	      		descending : false,

	        	dealAllRents : [],

	        	pagination: {
		        	current_page: 1
		      	},

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

					ID: 'name',

					"# Installment": 'number_installment',

					"Issued From": 'date_from',

					"Expired At": 'date_to',

					"Total Rent": 'total_rent',

					Discount: 'discount',

					"Net Payable": 'net_payable',

					"Total Paid": 'total_paid_amount',

					"Due": {
						
						callback: (object) => {
							
							return (object.net_payable - object.total_paid_amount);

						},

					},

					"Created at": 'created_at',

					"Rents": {
						field: "spaceRents",
						callback: (spaceRents) => {

							if (spaceRents && spaceRents.length) {
								
								var infosToReturn = '';

								spaceRents.forEach(
					
									(paymentRent, paymentRentIndex) => {

										infosToReturn += "Space Type: " + (paymentRent.dealt_space ? (paymentRent.dealt_space.type.includes('WarehouseContainerStatus') ? 'Container' :(paymentRent.dealt_space.type.includes('WarehouseContainerShelfStatus') ? 'Shelf' : 'Unit')) : 'NA') + "\n Space Name: "  + this.$options.filters.capitalize(paymentRent.dealt_space.name) + "\n Rent: " + paymentRent.rent + "\n";

									}
									
								);

								return infosToReturn;

							}
							else {
								return 'No Rents Found.'
							}

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
					windowTitle: 'Rent Details' 

				},

	            merchant : JSON.parse(window.localStorage.getItem('merchant')),

	            general_settings : JSON.parse(window.localStorage.getItem('general_settings')),

	            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

	        }

		},
		
		created(){

			this.fetchAllRents();

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

					this.fetchAllRents();
					// this.setDealAllRents();

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

					this.fetchAllRents();
					// this.setDealAllRents();

				}
				else {

					this.searchData();
						
				}

			},

			'searchAttributes.dateTo' : function(val){
				
				this.pagination.current_page = 1; 

				if (this.searchAttributes.search==='' && ! this.searchAttributes.dateTo && ! this.searchAttributes.dateFrom) {

					this.fetchAllRents();
					// this.setDealAllRents();

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

			fetchAllRents() {
				
				this.error = '';
				this.loading = true;
				this.dealAllRents = [];
				this.searchAttributes.search = '';
				
				axios
					.get('/api/my-space-deal/'  + this.deal.id + '/rents/' + this.perPage + "?page=" + this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
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
			
			searchData() {

				this.error = '';
				this.loading = true;
				this.dealAllRents = [];
				// this.allFetchedContents = [];
				// this.pagination.current_page = 1;
				this.searchAttributes.merchant_space_deal_id = this.deal.id;
				
				axios
				.post("/search-my-space-deal-rents/" + this.perPage + "?page=" + this.pagination.current_page, this.searchAttributes)
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
				$('#merchant-payment-view-modal').modal('show');
			},
			/*
			setDealAllRents() {

				this.dealAllRents = this.deal.rents;

			},
			*/
            changeNumberContents() {

				this.pagination.current_page = 1;
				// this.perPage = expectedContentsPerPage;

				if (this.searchAttributes.search === '') {
					
					this.fetchAllRents();
					// this.setDealAllRents();
				
				}
				else {
					this.searchData();
				}
				
    		},
    		setContentPagination(response) {

    			this.dealAllRents = response.data.data;
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

				// this.printingStyles.name = `${ this.singleRentData.name } Details`;
				this.printingStyles.windowTitle = this.$options.filters.capitalize(`${ this.singleRentData.name } Details`);

				// this.$set(this, 'paymentToPrint', paymentToPrint);
				// this.paymentToPrint = paymentToPrint;

				JsBarcode(".barcode").init();

				this.$htmlToPaper('sectionToPrint', this.printingStyles);

				$('#merchant-payment-view-modal').modal('hide');

			},
            
		}
  	}

</script>

<style scoped>
	
</style>