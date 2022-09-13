
<template>
	<div class="pcoded-content">
		<breadcrumb 
			:icon="'stocks'"
			:title="product.name + ' stocks'" 
			:message="('All ' + product.name + ' stocks for ' + (merchant ? merchant.user_name : ''))"
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
											<div class="col-sm-12">
											  	<div class="row form-group">
											  		<div class="col-md-4 col-sm-6 d-flex align-items-center form-group">
											  			<div class="mr-2">
											  				<span>
													  			{{ 
													  				( /* searchAttributes.showPendingStocks || searchAttributes.showCancelledStocks || searchAttributes.showDispatchedStocks || searchAttributes.showProduct || */ searchAttributes.search || searchAttributes.dateFrom || searchAttributes.dateTo) ? 'Searched Stocks List' : 'Stocks List'
													  			}}
											  				</span>
											  			</div>

											  			<div class="dropdown">
									  						<i class="fa fa-download fa-lg dropdown-toggle" data-toggle="dropdown" v-tooltip.bottom-end="'Download Stocks'"></i>
										  					
										  					<div class="dropdown-menu">
									  							<download-excel 
													  				class="btn waves-effect waves-dark btn-default btn-outline-default p-1 dropdown-item active"
																	:data="allStocks"
																	:fields="dataToExport" 
																	:worksheet="product.name + ' (' + merchant.user_name + ') ' + 'Sheet'"
																	:name="((searchAttributes.search != '' || searchAttributes.dateFrom || searchAttributes.dateTo) ? ('searched-' + merchant.user_name + '-' + product.name + '-stock-list-') : (merchant.user_name + '-' + product.name + '-stock-list-')) + currentTime + '-page-' + pagination.current_page + '.xls'"
													  			>
													  				Excel
																</download-excel>
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
														  		placeholder="Search Stocks"
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
																		:class="{ 'active': searchAttributes.dateFrom == currentTime && ! searchAttributes.dateTo }"
																	>
																		Today
																	</a>
																</li>

																<li class="nav-item">
																	<a 
																		href="javascript:void(0)"
																		class="nav-link p-0" 
																		data-toggle="modal" 
																		data-target="#stock-custom-search"
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

 												<div class="tab-content" v-show="!loading">
													<div class="card">
														<div class="table-responsive">
															<table class="table table-striped table-bordered nowrap text-center">
																<thead>
																	<tr>
																		<th>Date</th>
																		<th>Stock Qty</th>
																		<!-- <th>Available Qty</th> -->
																		<th>Approved</th>
																		<th>Actions</th>
																	</tr>
																</thead>
																<tbody>
																	<tr 
																		v-for="stock in allStocks" 
																		:key="'merchant-product-stock-' + stock.id" 
																		:class="stock.id==singleStockData.id ? 'highlighted' : ''"
																	>
																		<td>
																			{{ stock.created_at }}
																		</td>

																		<td>
																			{{ stock.stock_quantity + ' ' + product.quantity_type }}
																		</td>

																		<td>
																			<span :class="[stock.has_approval==1 ? 'badge-success' : stock.has_approval==-1 ? 'badge-danger' : 'badge-secondary', 'badge']">
																				{{ stock.has_approval==1 ? 'Approved' : stock.has_approval==-1 ? 'Cancelled' : 'NA' }}
																			</span>
																		</td>
																		
																		<td>
																			<button 
																				type="button" 
																				class="btn waves-effect waves-dark btn-info btn-outline-info btn-icon" 
																				v-tooltip.bottom-end="'View Details'"  
																				@click="showStockDetails(stock)"
																			>
																				<i class="fa fa-eye"></i>
																			</button>
																		</td>
																	</tr>

																	<tr 
																  		v-show="! allStocks.length"
																  	>
															    		<td colspan="4">
																      		<div class="alert alert-danger text-center" role="alert">
																      			Sorry, No data found.
																      		</div>
																    	</td>
																  	</tr>
																</tbody>
																<tfoot>
																	<tr>	
																		<th>Date</th>
																		<th>Stock Qty</th>
																		<!-- <th>Available Qty</th> -->
																		<th>Approved</th>
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
																@click="pagination.current_page = 1; searchAttributes.search === '' ? fetchProductAllStocks() : searchData()"
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
																@paginate="searchAttributes.search === '' ? fetchProductAllStocks() : searchData()"
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
		<div class="modal fade" id="stock-view-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">{{ product.name | capitalize }} Stock Details</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="modal-body">
						<div class="card">
							<div class="card-body">
								<ul class="nav nav-tabs tabs justify-content-center" role="tablist" v-show="productMerchant.has_serials">
									<li class="nav-item">
										<a class="nav-link active" data-toggle="tab" href="#product-stock" role="tab">
											Stock
										</a>
									</li>

									<li class="nav-item" v-show="productMerchant.has_serials">
										<a class="nav-link" data-toggle="tab" href="#product-serial" role="tab">
											Serials
										</a>
									</li>
								</ul>

								<div class="card-block" :class="productMerchant.has_serials ? 'tab-content tabs' : ''">
 									<div class="tab-pane active" id="product-stock" role="tabpanel">
										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-md-right">
												Product Name :
											</label>

											<label class="col-sm-6 col-form-label">
												{{ product.name | capitalize }} ({{ productMerchant.manufacturer ? productMerchant.manufacturer.name : 'Mr. Manufacturer' | capitalize }})
											</label>
										</div>

										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-md-right">
												Merchant Name :
											</label>

											<label class="col-sm-6 col-form-label">
												{{ merchant ? merchant.first_name + ' ' + merchant.last_name : 'Mr. Merchant' | capitalize }}
											</label>
										</div>

										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-md-right">
												Stock Invoice :
											</label>

											<label class="col-sm-6 col-form-label">
												{{ singleStockData.invoice_no | capitalize }}
											</label>
										</div>

										<div class="form-row" v-show="! productMerchant.has_variations">
											<label class="col-sm-6 col-form-label font-weight-bold text-md-right">
												Product-Stock (Batch) Code :
											</label>

											<div class="col-sm-6">
												<label class="col-form-label">
													{{ singleStockData.stock_code | capitalize }}
												</label>
												
												<!-- 
												<barcode 
													:value="singleStockData.stock_code" 
													format= "CODE128"
													background="#ccffff"
													lineColor= "#000000"
													width=1
													height=25
													fontSize=25 
													class="d-block" 
													style="overflow: auto;"
												>
											    	Barcode Not Working.
											  	</barcode> 
											  	-->
												
												<!-- <svg id="view-stock-code" class="d-block"/> -->

											  	<!-- 
											  	<svg 
											  		class="barcode" 
											  		id="view-stock-code" 
											        :jsbarcode-format="'CODE128'" 
											        :jsbarcode-width=1 
											        :jsbarcode-height=252
											        :jsbarcode-value="singleStockData.stock_code"
											    >    
										        </svg> 
										    	-->
											</div>
										</div>

										<div 
											class="form-row" 
											v-show="singleStockData.manufactured_at"
										>
											<label class="col-sm-6 col-form-label font-weight-bold text-md-right">
												Manufacturing Date :
											</label>

											<label class="col-sm-6 col-form-label">
												{{ singleStockData.manufactured_at }}
											</label>
										</div>

										<div 
											class="form-row" 
											v-show="singleStockData.expired_at"
										>
											<label class="col-sm-6 col-form-label font-weight-bold text-md-right">
												Expiring Date :
											</label>

											<label class="col-sm-6 col-form-label">
												{{ singleStockData.expired_at }}
											</label>
										</div>

										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-md-right">
												Stock Quantity :
											</label>

											<div class="col-sm-6 col-form-label">
												
												{{ singleStockData.stock_quantity + ' ' + product.quantity_type }}
												
												<div class="form-row" v-if="singleStockData.hasOwnProperty('variations') && singleStockData.variations.length">
													<div 
														class="col-md-12" 
														v-for="(stockVariation, variationIndex) in singleStockData.variations" 
															:key="'product-variation-index-' + variationIndex + 'B'"
													>
														<div class="card">
															<div class="card-body">
																<div class="form-row">
																	<label class="col-form-label font-weight-bold text-right">
																		{{ stockVariation.variation.name | capitalize }} :
																	</label>

																	<label class="col-form-label">
																		{{ stockVariation.stock_quantity + ' ' + product.quantity_type }}
																	</label>
																</div>

																<div class="form-row">
																	<label class="col-form-label font-weight-bold text-right">
																		Available Quantity :
																	</label>
																	<label class="col-form-label">
																		{{ stockVariation.available_quantity + ' ' + product.quantity_type }}
																	</label>
																</div>

																<div class="form-row">
																	<label class="col-form-label font-weight-bold text-right">
																		Buying Price :
																	</label>
																	<label class="col-form-label">
																		{{ stockVariation.unit_buying_price }}
																		{{ general_settings.official_currency_name || 'BDT' | capitalize }}
																	</label>
																</div>

																<div class="form-row">
																	<label class="col-form-label font-weight-bold text-right">
																		Stock (Batch) Code :
																	</label>
																	<label class="col-form-label">
																		{{ stockVariation.stock_code | capitalize }}
																	</label>
																</div>

																<div 
																	class="form-row" 
																	v-show="stockVariation.manufactured_at"
																>
																	<label class="col-form-label font-weight-bold text-right">
																		Manufacturing Date :
																	</label>
																	<label class="col-form-label">
																		{{ stockVariation.manufactured_at }}
																	</label>
																</div>

																<div 
																	class="form-row" 
																	v-show="stockVariation.expired_at"
																>
																	<label class="col-form-label font-weight-bold text-right">
																		Expiring Date :
																	</label>
																	<label class="col-form-label">
																		{{ stockVariation.expired_at }}
																	</label>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>

										<div class="form-row" v-show="! productMerchant.has_variations">
											<label class="col-sm-6 col-form-label font-weight-bold text-md-right">
												Available Quantity :
											</label>
											
											<label class="col-sm-6 col-form-label">
												{{ singleStockData.available_quantity + ' ' + product.quantity_type }}
											</label>
										</div>

										<div class="form-row" v-show="! productMerchant.has_variations">
											<label class="col-sm-6 col-form-label font-weight-bold text-md-right">
												Buying Price :
											</label>
											<label class="col-sm-6 col-form-label">
												{{ singleStockData.unit_buying_price }}
												{{ general_settings.official_currency_name || 'BDT' | capitalize }}
											</label>
										</div>

										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-md-right">
												Stocked on :
											</label>

											<label class="col-sm-6 col-form-label">
												{{ singleStockData.created_at }}
											</label>
										</div>

										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-md-right">
												Stocked at :
											</label>

											<label class="col-sm-6 col-form-label">
												{{ singleStockData.hasOwnProperty('warehouse') ? singleStockData.warehouse.name : '' }}
											</label>
										</div>

										<div class="form-row" v-if="singleStockData.hasOwnProperty('keeper')">
											<label class="col-sm-6 col-form-label font-weight-bold text-md-right">
												Stored By :
											</label>

											<label class="col-sm-6 col-form-label">
												{{ singleStockData.keeper.user_name | capitalize }}
											</label>
										</div>

										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-md-right">
												Approval :
											</label>

											<label class="col-sm-6 col-form-label">
												<span :class="[singleStockData.has_approval==1 ? 'badge-success' : singleStockData.has_approval==-1 ? 'badge-danger' : 'badge-secondary', 'badge']">
													{{ singleStockData.has_approval==1 ? 'Approved' : singleStockData.has_approval==-1 ? 'Cancelled' : 'NA' }}
												</span>
											</label>
										</div>

										<div class="form-row" v-if="singleStockData.has_approval">
											<label class="col-sm-6 col-form-label font-weight-bold text-md-right">
												{{ singleStockData.has_approval==1 ? 'Approved' : 'Cancelled' }} By :
											</label>

											<label class="col-sm-6 col-form-label">
												{{ singleStockData.approver.user_name | capitalize }}
											</label>
										</div>

										<div class="form-row" v-if="singleStockData.has_approval">
											<label class="col-sm-6 col-form-label font-weight-bold text-md-right">
												{{ singleStockData.has_approval==1 ? 'Approved' : 'Cancelled' }} on :
											</label>

											<label class="col-sm-6 col-form-label">
												{{ singleStockData.updated_at }}
											</label>
										</div>
									</div>

									<div class="tab-pane" id="product-serial" role="tabpanel" v-show="singleStockData.has_serials">
										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-md-right">
												Serials :
											</label>
											<div class="col-sm-6 col-form-label">
												<ol 
													v-if="singleStockData.has_serials && singleStockData.hasOwnProperty('serials') && singleStockData.serials.length"
												>
													<li v-for="(productSerial, productIndex) in singleStockData.serials">
														{{ productSerial.serial_no }}

														<span :class="[productSerial.has_dispatched ? 'badge badge-danger' : productSerial.has_requisitions ? 'badge badge-warning' : '']">
															{{ productSerial.has_dispatched ? 'Dispatched' : productSerial.has_requisitions ? 'Requested' : '' }}
														</span>

														<span v-show="(productIndex + 1) < singleStockData.serials.length">, </span> 
													</li>	
												</ol>
												
												<div class="form-row" v-if="singleStockData.hasOwnProperty('variations') && singleStockData.variations.length">
													<div 
														class="col-md-12" 
														v-for="(stockVariation, variationIndex) in singleStockData.variations" 
														:key="'product-variation-index-' + variationIndex + '-C'"
													>
														<div class="form-row">
															<label class="col-form-label font-weight-bold text-right">
																{{ stockVariation.variation.name | capitalize }} |
															</label>

															<label class="col-form-label">
																{{ stockVariation.stock_quantity + ' ' + product.quantity_type }}
																<ol 
																	v-if="singleStockData.has_serials && stockVariation.serials.length"
																>
																	<li v-for="(variationSerial, variationIndex) in stockVariation.serials">
																		{{ variationSerial.serial_no }}

																		<span :class="[variationSerial.has_dispatched ? 'badge badge-danger' : variationSerial.has_requisitions ? 'badge badge-warning' : '']">
																			{{ variationSerial.has_dispatched ? 'Dispatched' : variationSerial.has_requisitions ? 'Requested' : '' }}
																		</span>

																		<span v-show="(variationIndex + 1) < stockVariation.serials.length">, </span> 
																	</li>	
																</ol>
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

					<div class="modal-footer">
						<button type="button" class="btn waves-effect waves-dark btn-secondary btn-outline-secondary mr-auto" data-dismiss="modal">
							Close
						</button>

						<button 
							type="button" 
							class="btn waves-effect waves-dark btn-danger btn-outline-danger" 
							v-tooltip.bottom-end="'Print'"  
							@click="printInvoice()"
						>
							Print
						</button>
					</div>
				</div>
			</div>
		</div>

		<!-- Modal -->
		<div class="modal fade" id="stock-custom-search" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
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
						<button 
						type="button" 
						class="btn waves-effect waves-dark btn-success btn-outline-success" 
						v-tooltip.bottom-end="'Reset'"  
						@click="resetSearchingDates()"
						>
	                  		Reset
	                  	</button>

						<button type="button" class="btn waves-effect waves-dark btn-primary btn-outline-primary ml-auto" data-dismiss="modal">
	                  		See Results
	                  	</button>
					</div>
				</div>
			</div>
		</div>

		<div id="sectionToPrint" class="d-none">
			<div class="card">
				<div class="card-header">
					<div class="form-row d-flex">
						<div class="col-6">
							<img 
								class="img-fluid" 
								width="60px" 
								:src="'/' + general_settings.logo" 
								:alt="$options.filters.capitalize(general_settings.app_name) + ' Logo'" 
							>
							
							<h5>
								{{ general_settings.app_name | capitalize }} Stock Invoice
							</h5>
						</div>

						<div class="col-6 align-self-center">
							<qr-code 
							:text="singleStockData.invoice_no || ''"
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
									Stocked at :
								</label>

								<label class="col-6 col-form-label">
									{{ singleStockData.hasOwnProperty('warehouse') ? singleStockData.warehouse.name : '' }}
								</label>
							</div>

							<div class="form-row">
								<label class="col-6 col-form-label font-weight-bold">
									Stocked on :
								</label>

								<label class="col-6 col-form-label">
									{{ singleStockData.created_at }}
								</label>
							</div>

							<div class="form-row" v-if="singleStockData.hasOwnProperty('keeper')">
								<label class="col-6 col-form-label font-weight-bold">
									Stored By :
								</label>

								<label class="col-6 col-form-label">
									{{ singleStockData.keeper.user_name | capitalize }}
								</label>
							</div>

							<div class="form-row">
								<label class="col-6 col-form-label font-weight-bold">
									Approval :
								</label>

								<label class="col-6 col-form-label">
									<span :class="[singleStockData.has_approval==1 ? 'badge-success' : singleStockData.has_approval==-1 ? 'badge-danger' : 'badge-secondary', 'badge']">
										{{ singleStockData.has_approval==1 ? 'Approved' : singleStockData.has_approval==-1 ? 'Cancelled' : 'NA' }}
									</span>
								</label>
							</div>

							<div class="form-row" v-if="singleStockData.has_approval">
								<label class="col-6 col-form-label font-weight-bold">
									{{ singleStockData.has_approval==1 ? 'Approved' : 'Cancelled' }} By :
								</label>

								<label class="col-6 col-form-label">
									{{ singleStockData.approver.user_name | capitalize }}
								</label>
							</div>

							<div class="form-row" v-if="singleStockData.has_approval">
								<label class="col-6 col-form-label font-weight-bold">
									{{ singleStockData.has_approval==1 ? 'Approved' : 'Cancelled' }} on :
								</label>

								<label class="col-6 col-form-label">
									{{ singleStockData.updated_at }}
								</label>
							</div>
						</div>
						
						<div class="col-6">
							<div class="form-row">
								<label class="col-6 col-form-label font-weight-bold text-right">
									Merchant Name :
								</label>

								<label class="col-6 col-form-label">
									{{ merchant ? merchant.first_name + ' ' + merchant.last_name : 'Mr. Merchant' | capitalize }}
								</label>
							</div>

							<!-- 
							<div class="form-row" v-show="productMerchant.has_variations">
								<label class="col-6 col-form-label font-weight-bold text-right">
									Product Name :
								</label>

								<label class="col-6 col-form-label">
									{{ product.name | capitalize }} ({{ productMerchant.manufacturer ? productMerchant.manufacturer.name : 'Mr. Manufacturer' | capitalize }})
								</label>
							</div> 
							-->

							<div 
								class="form-row" 
								v-show="singleStockData.manufactured_at && ! productMerchant.has_variations"
							>
								<label class="col-6 col-form-label font-weight-bold text-right">
									Mfg. Date :
								</label>

								<label class="col-6 col-form-label">
									{{ singleStockData.manufactured_at }}
								</label>
							</div>

							<div 
								class="form-row" 
								v-show="singleStockData.expired_at && ! productMerchant.has_variations"
							>
								<label class="col-6 col-form-label font-weight-bold text-right">
									Exp. Date :
								</label>

								<label class="col-6 col-form-label">
									{{ singleStockData.expired_at }}
								</label>
							</div>
						</div>
					</div>

					<!-- 
					<div class="form-row" v-show="! productMerchant.has_variations">
						<label class="col-6 col-form-label font-weight-bold text-right">
							Available Quantity :
						</label>
						
						<label class="col-6 col-form-label">
							{{ singleStockData.available_quantity + ' ' + product.quantity_type }}
						</label>
					</div> 
					-->

					<!-- 
					<div class="form-row">
						<div class="col-12">
							<label class="col-form-label font-weight-bold">
								Description :
							</label>
						</div>
					</div> 
					-->

					<div class="form-row">
						<div class="col-12">
							<table class="table table-striped table-bordered nowrap text-center">
								<thead>
									<tr>
										<th>Name</th>
										<th>Variation</th>
										<th>Stock Qty ({{ product.quantity_type }})</th>
										<!-- 
										<th>Stock (Batch) Code</th>
										<th>Buying Price ({{ general_settings.official_currency_name || 'BDT' | capitalize }})</th>
										-->
										<th>Serials</th>
										<!-- <th>Total</th> -->
									</tr>
								</thead>

								<tbody>
									<div v-if="! productMerchant.has_variations">
										<tr>
											<td>
												{{ product.name | capitalize }} ({{ productMerchant.manufacturer ? productMerchant.manufacturer.name : 'Mr. Manufacturer' | capitalize }})
											</td>

											<td>--</td>

											<td>{{ singleStockData.stock_quantity  }}</td>

											<td>
												<ol 
													v-if="singleStockData.has_serials && singleStockData.hasOwnProperty('serials') && singleStockData.serials.length"
												>
													<li v-for="(productSerial, productIndex) in singleStockData.serials">
														{{ productSerial.serial_no }}

														<span :class="[productSerial.has_dispatched ? 'badge badge-danger' : productSerial.has_requisitions ? 'badge badge-warning' : '']">
															{{ productSerial.has_dispatched ? 'Dispatched' : productSerial.has_requisitions ? 'Requested' : '' }}
														</span>

														<span v-show="(productIndex + 1) < singleStockData.serials.length">, </span> 
													</li>	
												</ol>

												<span v-else>
													--
												</span>
											</td>
										</tr>

										<!-- <td></td> -->
									</div>

									<div v-else-if="productMerchant.has_variations && singleStockData.hasOwnProperty('variations') && singleStockData.variations.length">
										<tr 
											v-for="(stockVariation, variationIndex) in singleStockData.variations" 
											:key="'product-variation-index-' + variationIndex + 'B'"
										>	
											<td>
												{{ product.name | capitalize }} ({{ productMerchant.manufacturer ? productMerchant.manufacturer.name : 'Mr. Manufacturer' | capitalize }})
											</td>

											<td>
												{{ stockVariation.variation.name | capitalize }}
												 
												<p v-show="stockVariation.manufactured_at">
													Mfg Date : {{ stockVariation.manufactured_at }}
												</p>

												<p v-show="stockVariation.expired_at">
													Exp Date : {{ stockVariation.expired_at }}
												</p>
											</td>
											
											<td>{{ stockVariation.stock_quantity }}</td>

											<td>
												<ol 
													v-if="singleStockData.has_serials && stockVariation.hasOwnProperty('serials') && stockVariation.serials.length"
												>
													<li v-for="(variationSerial, variationIndex) in stockVariation.serials">
														{{ variationSerial.serial_no }}

														<span :class="[variationSerial.has_dispatched ? 'badge badge-danger' : variationSerial.has_requisitions ? 'badge badge-warning' : '']">
															{{ variationSerial.has_dispatched ? 'Dispatched' : variationSerial.has_requisitions ? 'Requested' : '' }}
														</span>

														<span v-show="(variationIndex + 1) < stockVariation.serials.length">, </span> 
													</li>	
												</ol>

												<span v-else>
													--
												</span>
											</td>

											<!-- <td></td> -->
										</tr>
									</div>
								</tbody>
							</table>							
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript">

	export default {

	    props: {

			product:{
				type: Object,
				required: true,
			},
			productMerchant:{
				type: Object,
				required: true,
			},
			merchantProductId:{
				type: Number,
				required: true,
			},

		},

	    data() {

	        return {

	        	error : '',
    			perPage : 10,
	        	loading : false,

	        	allStocks : [],

	        	pagination: {
		        	current_page: 1
		      	},

	        	singleStockData : {

	        		serials : [],
	        		// product_id : this.product.id,
	        		merchant_product_id : this.productMerchant.id,
	        		variations : this.productMerchant.hasOwnProperty('variations') && this.productMerchant.variations.length ? JSON.parse(JSON.stringify(this.productMerchant.variations)) : [],

	        	},

	        	productToPrint : {},

				searchAttributes : {

	        		dates : {},
	        		search : '',
		        	dateTo : null,
		        	dateFrom : null,

	        	},

	        	printingStyles : {
				    
				    name: "_blank",
				    
				    specs: [
				        'fullscreen=yes',
				        'titlebar=yes',
				        'scrollbars=yes'
				    ],

				    styles: [
				    	"/css/bootstrap.min.css",
				    ],

				    timeout: 1000, // default timeout before the print window appears
					autoClose: true, // if false, the window will not close after printing
					windowTitle: 'Product Stock Details' 

				},

	        	dataToExport: {

					"Invoice": 'invoice_no',

					"Product": {
						field: "stock_quantity",
						callback: (stock_quantity) => {
							
							return this.productMerchant.hasOwnProperty('product') ? this.$options.filters.capitalize(`${this.product.name}`) : 'NA';
							
						},
					},

					"Manufacturer/Brand": {
						field: "stock_quantity",
						callback: (stock_quantity) => {
							
							return this.productMerchant.manufacturer ? this.$options.filters.capitalize(this.productMerchant.manufacturer.name) : 'Own Product';
							
						},
					},

					"Merchant": {
						field: "stock_quantity",
						callback: (stock_quantity) => {
							
							// return this.productMerchant.hasOwnProperty('merchant') ? `${this.merchant.first_name} ${this.merchant.last_name}` : 'NA';
							
							return this.$options.filters.capitalize(this.merchant.user_name) || 'NA';
							
						},
					},

					"Stock Quantity": 'stock_quantity',

					"Qty Type" : {

						field: "stock_quantity",
						callback: (stock_quantity) => {
							
							return `${this.product.quantity_type}`;
							
						},

					},

					"Variations & Serials": {
						
						callback: (object) => {

							var stockDetailToReturn = '';

							if (object.has_serials && ! object.has_variations && object.serials.length) {

								stockDetailToReturn += `Product Serials
											`;

								object.serials.forEach(
					
									(stockedProductSerial, stockedProductSerialIndex) => {

										if (stockedProductSerial.serial_no) {

											stockDetailToReturn +=  `${stockedProductSerialIndex+1}. ${stockedProductSerial.serial_no} ` + (stockedProductSerial.has_dispatched ? ' (Dispatched)' : '') + "\n" ;

										}

									}
									
								);

							}

							if (object.hasOwnProperty('variations') && object.variations.length) {

								object.variations.forEach(
					
									(stockedProductVariation, stockedProductVariationIndex) => {

										if (stockedProductVariation.hasOwnProperty('variation') && stockedProductVariation.variation.hasOwnProperty('name')) {

											stockDetailToReturn +=  this.$options.filters.capitalize(`Variation ${stockedProductVariationIndex+1}: ${stockedProductVariation.variation.name}, 
											Qty: ${stockedProductVariation.stock_quantity}  ${this.product.quantity_type}
											`);

										}

										if (object.has_serials && stockedProductVariation.hasOwnProperty('serials') && stockedProductVariation.serials.length) {

											stockDetailToReturn += `Serials:
											`;

											stockedProductVariation.serials.forEach(
						
												(stockedProductVariationSerial, stockedProductVariationSerialIndex) => {

													if (stockedProductVariationSerial.serial_no) {

														stockDetailToReturn +=  `${stockedProductVariationSerialIndex+1}. ${stockedProductVariationSerial.serial_no} ` + (stockedProductVariationSerial.has_dispatched ? '(Dispatched)' : '') + "\n" ;

													}

												}
												
											);

										}

										stockDetailToReturn += "\n";

									}
									
								);

							}

							if (! object.has_serials && ! object.has_variations) {

								stockDetailToReturn += 'NA';

							}

							return stockDetailToReturn;

						},

					},

					"Stock": {
						callback: (object) => {
							var stockInfosToReturn = '';

							stockInfosToReturn += 'Stocked at:' + (object.hasOwnProperty('warehouse') ? this.$options.filters.capitalize(object.warehouse.name) : 'NA') + "\n";

							stockInfosToReturn +=  'Stocked on:' + object.created_at + "\n";

							stockInfosToReturn += 'Keeper:' + this.$options.filters.capitalize(object.keeper.first_name + ' ' + object.keeper.last_name) + '\n'

							return stockInfosToReturn;
							
						},
					},

					"Approval": {
						callback: (object) => {
							
							if (object.has_approval==1) {
								return "Approved on: " + object.updated_at + ".\n" + 'Approver:' + (object.approver ? this.$options.filters.capitalize(object.approver.first_name + ' ' + object.approver.last_name) : '');
							}
							else {
								return 'Pending.';
							}

						},
					},
					
				},

	            merchant : JSON.parse(window.localStorage.getItem('merchant')),

	            general_settings : JSON.parse(window.localStorage.getItem('general_settings')),

	            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

	        }

		},

		computed: {

			currentTime: function() {

				let date = new Date();
				return date.getFullYear() + '/' +  (date.getMonth() + 1) + '/' + date.getDate();

			},

		},

		watch : {

			'searchAttributes.search' : function(val){
				
				this.pagination.current_page = 1;

				if (this.searchAttributes.search==='' && ! this.searchAttributes.dateTo && ! this.searchAttributes.dateFrom) {

					this.fetchProductAllStocks();

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

					this.fetchProductAllStocks();

				}
				else {

					this.searchData();
						
				}

			},

			'searchAttributes.dateTo' : function(val){
				
				this.pagination.current_page = 1;

				if (this.searchAttributes.search==='' && ! this.searchAttributes.dateTo && ! this.searchAttributes.dateFrom) {

					this.fetchProductAllStocks();

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

		created() {
			
			this.fetchProductAllStocks();

		},
		
		methods: {

			fetchProductAllStocks() {

				this.error = '';
				this.loading = true;
				this.allStocks = [];
				this.searchAttributes.search = '';
				
				axios
					.get('/api/my-product-stocks/' + this.merchantProductId + '/' + this.perPage + "?page=" + this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							// console.log(response);
							this.setAvailableContents(response);
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
    		showStockDetails(object) {		
				// this.singleStockData = { ...object };
				this.singleStockData = Object.assign({}, this.singleStockData, object);
				
				/*
				JsBarcode("#view-stock-code", "MPSKIVAHANPRODUCTONESERIAL0", {
					format: "CODE128",
					background:"#ccffff",
					lineColor: "#000000",
					width:1,
					height:25,
				});
				*/

				// JsBarcode("#view-stock-code").init();

				$('#stock-view-modal').modal('show');
			},
			searchData() {

				this.error = '';
				this.allStocks = [];
				
				axios
				.post("/api/search-my-product-stocks/" + this.productMerchant.id + "/" + this.perPage + "?page=" + this.pagination.current_page, this.searchAttributes)
				.then(response => {
					this.allStocks = response.data.all.data;
					this.pagination = response.data.all;
				})
				.catch(e => {
					this.error = e.toString();
				});

			},
			changeNumberContents() {
				
				this.pagination.current_page = 1;

				if (this.searchAttributes.search === '') {
					this.fetchProductAllStocks();
				}
				else {
					this.searchData();
				}

    		},
    		setAvailableContents(response) {
				this.pagination = response.data;
				this.allStocks = response.data.data;
			},
		    printInvoice() {

				this.printingStyles.name = `${ this.singleStockData.subject } Details`;
				this.printingStyles.windowTitle = this.$options.filters.capitalize(`${ this.product.name } - ${ this.merchant.user_name } Stock Details`);

				// JsBarcode(".barcode").init();

				/*
				JsBarcode("#print-stock-code", this.singleStockData.stock_code, 
					{
						width:1, 
						height:25
					}
				);
				*/

				this.$htmlToPaper('sectionToPrint', this.printingStyles);

				$('#stock-view-modal').modal('hide');

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
            	
            	if (this.searchAttributes.dateFrom != this.currentTime || this.searchAttributes.dateTo) {
	            	
	            	// this.searchAttributes.dateTo = null; 
	            	this.searchAttributes.dates = {};
	            	this.searchAttributes.dateTo = null;
	            	this.searchAttributes.dateFrom = this.currentTime;

            	}
            	else {

	            	this.searchAttributes.dateFrom = null

            	}

            },
            showPreview(imagePath = 'default', variationIndex = -1) {
				
				if (! imagePath || imagePath == '') { // null or empty
					
					if (variationIndex > -1) {

						return ('/' + this.product.variations[variationIndex].preview);

					}
					else {

						return ('/' + this.product.preview);

					}

				}
				else if (imagePath == 'default') {
					return '/' + this.productMerchant.preview;
				}
				else {
					return '/' + imagePath || '';
				}

				// return '';

			}
            
		}
  	}

</script>