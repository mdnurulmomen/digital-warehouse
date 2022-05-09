
<template v-if="userHasPermissionTo('view-product-stock-index')">
	<div class="pcoded-content">
		<breadcrumb 
			:icon="'fa fa-store'"
			:title="product.name + ' stocks'" 
			:message="'All ' + product.name + ' stocks for ' + productMerchant.merchant ? productMerchant.merchant.name : ''"
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
													  				( /* searchAttributes.showPendingStocks || searchAttributes.showCancelledStocks || searchAttributes.showDispatchedStocks || searchAttributes.showProduct || */ searchAttributes.search || searchAttributes.dateFrom || searchAttributes.dateTo) ? 'Searched Stocks List' : 'Stocks List'
													  			}}
											  				</span>
											  			</div>

											  			<div class="dropdown">
									  						<i class="fas fa-download fa-lg dropdown-toggle" data-toggle="dropdown" v-tooltip.bottom-end="'Download Stocks'"></i>
										  					
										  					<div class="dropdown-menu">
									  							<download-excel 
													  				class="btn btn-default p-1 dropdown-item active"
																	:data="allStocks"
																	:fields="dataToExport" 
																	:worksheet="product.name + ' (' + productMerchant.merchant.user_name + ') ' + 'Sheet'"
																	:name="((searchAttributes.search != '' || searchAttributes.dateFrom || searchAttributes.dateTo) ? ('searched-' + productMerchant.merchant.user_name + '-' + product.name + '-stock-list-') : (productMerchant.merchant.user_name + '-' + product.name + '-stock-list-')) + currentTime + '-page-' + pagination.current_page + '.xls'"
													  			>
													  				Excel
																</download-excel>
										  						
										  						<!-- 
										  						<download-excel 
										  							type="csv"
													  				class="btn btn-default p-1 dropdown-item disabled"
																	:data="allStocks"
																	:fields="dataToExport" 
																	worksheet="Stocks sheet"
																	:name="((searchAttributes.search != '' || searchAttributes.dateFrom || searchAttributes.dateTo) ? 'searched-stocks-' : ('stocks-list-')) + currentTime + '-page-' + pagination.current_page + '.xls'"
													  			>
													  				CSV
																</download-excel> 
																-->
										  					</div>
										  				</div>

											  			<div class="ml-auto d-sm-none">
											  				<button 
											  					type="button" 
													  			class="btn btn-success btn-outline-success btn-sm" 
													  			v-tooltip.bottom-end="allDealtEmptyWarehouses.length==0 ? 'No Space' : 'Create New'" 
											  					v-if="userHasPermissionTo('create-product-stock')"
													  			@click="showStockCreateForm()" 
													  			:disabled="allDealtEmptyWarehouses.length==0 ? true : false" 
												  			>
												  				<i class="fa fa-plus"></i>
												  				New Stock
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

													<div class="col-md-4 text-right d-none d-md-block">
											  			<button 
											  				type="button" 
												  			class="btn btn-success btn-outline-success btn-sm" 
												  			v-tooltip.bottom-end="allDealtEmptyWarehouses.length==0 ? 'No Space' : 'Create New'" 
										  					v-if="userHasPermissionTo('create-product-stock')"
												  			@click="showStockCreateForm()" 
												  			:disabled="allDealtEmptyWarehouses.length==0 ? true : false" 
											  			>
											  				<i class="fa fa-plus"></i>
											  				New Stock
											  			</button>
													</div>
											  	</div>
											</div>

										  	<!-- 
											<div class="col-sm-12 sub-title">
											  	<search-and-addition-option 
											  		v-if="userHasPermissionTo('view-product-stock-index') || userHasPermissionTo('create-product-stock')"
											  		:search="searchAttributes.search" 
											  		:caller-page="'stocks'" 
											  		:required-permission="'product-stock'" 
											  		:disable-add-button="allDealtEmptyWarehouses.length==0 ? true : false" 									  
											  		@showContentCreateForm="showStockCreateForm" 
											  		@searchData="searchData($event)" 
											  		@fetchAllContents="fetchProductAllStocks"
											  	></search-and-addition-option> 
											</div>
										  	-->
											
											<div class="col-sm-12 col-lg-12">
									  			<!-- 
										  		<table-with-soft-delete-option 
										  			:search="searchAttributes.search" 
										  			:per-page="perPage"  
										  			:column-names="['name']" 
										  			:column-values-to-show="['name']" 
										  			:contents-to-show = "allStocks" 
										  			:pagination = "pagination"

										  			@showStockDetails="showStockDetails($event)" 
										  			@openStockEditForm="openStockEditForm($event)" 
										  			@openContentDeleteForm="openContentDeleteForm($event)" 
										  			@openContentRestoreForm="openContentRestoreForm($event)" 
										  			@changeNumberContents="changeNumberContents($event)" 
										  			@fetchProductAllStocks="fetchProductAllStocks" 
										  			@searchData="searchData" 
										  		>	
										  		</table-with-soft-delete-option>
 												-->

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

																		<!-- 
																		<td>
																			{{ stock.available_quantity + ' ' + product.quantity_type }}
																		</td> 
																		-->

																		<td>
																			<span :class="[stock.has_approval==1 ? 'badge-success' : stock.has_approval==-1 ? 'badge-danger' : 'badge-secondary', 'badge']">
																				{{ stock.has_approval==1 ? 'Approved' : stock.has_approval==-1 ? 'Cancelled' : 'NA' }}
																			</span>
																		</td>
																		
																		<td>
																			<button 
																				type="button" 
																				class="btn btn-grd-info btn-icon" 
																				v-tooltip.bottom-end="'View Details'"  
																				@click="showStockDetails(stock)"
																			>
																				<i class="fa fa-eye"></i>
																			</button>
																			<!-- Approve -->
																			<button 
																				type="button" 
																				class="btn btn-grd-warning btn-icon" 
																				v-tooltip.bottom-end="'Approve Stock'"  
																				@click="openStockEditForm(stock)" 
																				v-if="! stock.has_approval && userHasPermissionTo('update-product-stock')"
																			>
																				<i class="fa fa-check"></i>
																			</button>

																			<button 
																				type="button" 
																				class="btn btn-grd-primary btn-icon" 
																				v-tooltip.bottom-end="'Edit'"  
																				@click="openStockEditForm(stock)" 
																				v-if="stock.has_approval==1 && userHasPermissionTo('update-product-stock')"
																			>
																				<i class="fa fa-edit"></i>
																			</button>

																			<button 
																				type="button" 
																				class="btn btn-grd-danger btn-icon" 
																				v-tooltip.bottom-end="'Delete'" 
																				:disabled="formSubmitted || stock.stock_quantity > stock.available_quantity || (stock.hasOwnProperty('variations') && stock.variations.some(stockVariation => stockVariation.available_quantity < stockVariation.stock_quantity))"  
																				@click="openStockDeleteForm(stock)" 
																				v-if="userHasPermissionTo('delete-product-stock')" 
																			>
																				<i class="fa fa-trash"></i>
																			</button>
																		</td>
																	</tr>

																	<tr 
																  		v-show="! allStocks.length"
																  	>
															    		<td colspan="4">
																      		<div class="alert alert-danger" role="alert">
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
																class="btn btn-primary btn-sm" 
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

	<!-- 
		<asset-create-or-edit-modal 
			:create-mode="createMode" 
			:caller-page="'variation'" 
			:single-asset-data="singleStockData" 
			:csrf="csrf"

			@storeStock="storeStock($event)" 
			@updateStock="updateStock($event)" 
		></asset-create-or-edit-modal>
 	-->

 		<!--Create, Edit or Approve Modal -->
		<div class="modal fade" id="stock-createOrEdit-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" v-if="userHasPermissionTo('create-product-stock') || userHasPermissionTo('approve-product-stock') || userHasPermissionTo('update-product-stock')">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">
							{{ createMode ? 'Create ' + product.name + ' Stock' : singleStockData.has_approval==1 ? 'Update ' + product.name + ' Stock' : 'Approve ' + product.name + ' Stock' | capitalize }}
						</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
						
					<form 	
						class="form-horizontal" 
						v-on:submit.prevent="createMode ? storeStock() : updateStock()" 
						autocomplete="off" 
						novalidate="true" 
					>
						<input type="hidden" name="_token" :value="csrf">

						<div class="modal-body">
							<transition-group name="fade">   		
								<div 
									class="row" 
									v-bind:key="'product-modal-step-' + 1" 
									v-show="!loading && step==1"
								>
									<h2 class="mx-auto mb-4 lead">Product Profile</h2>

									<div class="col-md-12">
										<div class="row d-flex">
											<div class="col-md-4 text-center align-self-center">
												<img 
													:src="showPreview(productMerchant.preview)" 
													class="img-fluid" 
													alt="Product Preview" 
													width="150px"
												>
											</div>

											<div class="col-md-8">
												<div class="form-row">
													<label class="col-sm-4 col-form-label font-weight-bold text-md-right">
														Product Category :
													</label>
													<label class="col-sm-8 col-form-label">
														{{ product.category ? product.category.name : 'bulk product' | capitalize }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-sm-4 col-form-label font-weight-bold text-md-right">
														Name :
													</label>
													<label class="col-sm-8 col-form-label">
														{{ product.name | capitalize }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-sm-4 col-form-label font-weight-bold text-md-right">
														Merchant :
													</label>
													<label class="col-sm-8 col-form-label">
														{{ productMerchant.merchant ? productMerchant.merchant.user_name : 'None' | capitalize }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-sm-4 col-form-label font-weight-bold text-md-right">
														Manufacturer :
													</label>
													<label class="col-sm-8 col-form-label">
														{{ productMerchant.manufacturer_name ? productMerchant.manufacturer_name : 'Own Product' | capitalize }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-sm-4 col-form-label font-weight-bold text-md-right">
														SKU Code :
													</label>
													<label class="col-sm-8 col-form-label">
														{{ productMerchant.sku }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-sm-4 col-form-label font-weight-bold text-md-right">
														Warning Quantity :
													</label>
													<label class="col-sm-8 col-form-label">
														{{ productMerchant.warning_quantity + ' ' + product.quantity_type }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-sm-4 col-form-label font-weight-bold text-md-right">
														Available Quantity :
													</label>
													<label class="col-sm-8 col-form-label">
														{{ productMerchant.available_quantity + ' ' + product.quantity_type }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-sm-4 col-form-label font-weight-bold text-md-right">
														Price :
													</label>
													<label class="col-sm-8 col-form-label">
														{{ productMerchant.selling_price || 'NA' }}
														{{ general_settings.official_currency_name || 'BDT' | capitalize }}
													</label>
												</div>

												<!-- 
												<div class="form-row">
													<label class="col-sm-4 col-form-label font-weight-bold text-right">
														Description :
													</label>
													<label class="col-sm-8 col-form-label">
														<span v-html="product.description"></span>
													</label>
												</div>
		 										-->

		 										<div class="form-row form-group">
													<label class="col-sm-4 col-form-label font-weight-bold text-md-right">
														Select Warehouse :
													</label>
													<div class="col-sm-8">
														<multiselect 
					                              			v-model="singleStockData.warehouse"
					                                  		:options="allDealtEmptyWarehouses" 
					                                  		:custom-label="objectNameWithCapitalized" 
					                                  		:required="true" 
					                                  		:allow-empty="false" 
					                                  		label="name" 
					                                  		track-by="id" 
					                              			placeholder="Select Warehouse" 
					                              			class="form-control p-0" 
					                                  		:class="!errors.stock.warehouse  ? 'is-valid' : 'is-invalid'"  
					                                  		@close="validateFormInput('warehouse')" 
					                                  		@input="singleStockData.addresses = [ {} ]" 
					                              		>
					                                	</multiselect>
					                                	<div class="invalid-feedback">
													    	{{ errors.stock.warehouse }}
													    </div>
													</div>
												</div>

												<div 
													class="form-row form-group" 
													v-show="!singleStockData.has_approval"
												>
													<label class="col-sm-4 col-form-label font-weight-bold text-md-right">
														Stock Date :
													</label>
													<div class="col-sm-8">
														<v-date-picker 
															v-model="singleStockData.created_at" 
															color="red" 
															is-dark
															is-inline 
															:min-date="productMerchant.created_at" 
															:max-date="new Date()" 
															:model-config="{ type: 'string', mask: 'YYYY-MM-DD' }" 
															:attributes="[ { key: 'today', dot: true, dates: new Date() } ]" 
														/>
													</div>
												</div>

												<div class="form-row" v-show="singleStockData.has_approval==1">
													<label class="col-sm-4 col-form-label form-group">
														Stocked Date
													</label>

													<label class="col-sm-8 col-form-label form-group">
														{{ singleStockData.created_at }}
													</label>
												</div>

												<div 
													class="form-row" 
													v-show="userHasPermissionTo('update-product-stock')"
												>
													<label class="col-sm-4 col-form-label font-weight-bold form-group text-md-right">
														Approval Date :
													</label>
													<div class="col-sm-8 form-group">
														<v-date-picker 
															v-model="singleStockData.updated_at" 
															color="red" 
															is-dark
															is-inline 
															:min-date="singleStockData.created_at" 
															:max-date="new Date()" 
															:model-config="{ type: 'string', mask: 'YYYY-MM-DD' }" 
															:attributes="[ { key: 'today', dot: true, dates: new Date() } ]" 
														/>
													</div>
												</div>

		 										<div class="form-row form-group">
													<label class="col-sm-4 col-form-label font-weight-bold text-md-right">
														Stock Qty :
													</label>
													<div class="col-sm-8">
														<div class="input-group mb-0">
															<input type="number" 
																class="form-control" 
																v-model.number="singleStockData.stock_quantity" 
																placeholder="Product initial qty" 
																:class="!errors.stock.product_stock_quantity  ? 'is-valid' : 'is-invalid'" 
																@change="validateFormInput('product_stock_quantity')" 
																required="true" 
																:readonly="! createMode && allStocks.length && singleStockData.primary_quantity > allStocks[allStocks.length-1].available_quantity" 
																:min="createMode ? 1 : allStocks.length ? singleStockData.primary_quantity - allStocks[allStocks.length-1].available_quantity : 0"
															>
															<div class="input-group-append">
																<span class="input-group-text">
																	{{ product.quantity_type }}
																</span>
															</div>
														</div>
														<div class="invalid-feedback" 
															style="display: block;" 
															v-show="errors.stock.product_stock_quantity"
														>
												        	{{ errors.stock.product_stock_quantity }}
												  		</div>
													</div>
												</div>

												<div class="form-row form-group" v-show="! productMerchant.has_variations">
													<label class="col-sm-4 col-form-label font-weight-bold text-md-right">
														Buying Price (unit) :
													</label>
													<div class="col-sm-8">
														<div class="input-group mb-0">
															<input type="number" 
																class="form-control is-valid" 
																v-model.number="singleStockData.unit_buying_price" 
																placeholder="Unit Buying Price" 
																min="0"
															>
															<div class="input-group-append">
																<span class="input-group-text">
																	{{ general_settings.official_currency_name || 'BDT' | capitalize }}
																</span>
															</div>
														</div>
													</div>
												</div>

												<!-- 
												<div class="form-row">
													<label class="col-sm-4 col-form-label font-weight-bold text-right">
														Available Qty:
													</label>
													<label class="col-sm-8 col-form-label">
														{{ product.available_quantity }}
														{{ product.quantity_type }}
													</label>
												</div>
		 										-->

												<div class="form-row mt-3">
													<div class="form-group col-md-12 text-center">
														<toggle-button 
															v-model="productMerchant.has_variations" 
															:width=150 
															:sync="true"
															:color="{checked: 'green', unchecked: 'blue'}"
															:labels="{checked: 'Has Variation', unchecked: 'No Variation'}" 
															:disabled="true" 
														/>
													</div>
												</div>

												<div class="form-row" v-if="productMerchant.has_variations">
													<div 
														class="form-group col-md-12" 
														v-if="singleStockData.hasOwnProperty('variations') && singleStockData.variations.length"
													>
														<div 
															class="form-row" 
															v-for="(stockVariation, variationIndex) in singleStockData.variations" 
															:key="'product-variation-index-' + variationIndex + 'A'"
														>	
															<div class="form-group col-md-4">
																<label for="inputFirstName">Variaiton</label>
																<multiselect 
							                              			v-model="singleStockData.variations[variationIndex].variation"
							                              			placeholder="Select Variation" 
							                                  		label="name" 
							                                  		track-by="id" 
							                                  		class="form-control p-0 is-valid" 
							                                  		:custom-label="objectNameWithCapitalized" 
							                                  		:options="[]" 
							                                  		:disabled="true"
							                              		>
							                                	</multiselect>
															</div>

															<div 
																class="form-group col-md-4"
															>
																<label for="inputFirstName">Variation Qty</label>
																
																<input type="number" 
																	class="form-control" 
																	v-model.number="singleStockData.variations[variationIndex].stock_quantity" 
																	placeholder="Variation Qty" 
																	:class="!errors.stock.variations[variationIndex].product_variation_quantity ? 'is-valid' : 'is-invalid'" 
																	@change="validateFormInput('product_variation_quantity')" 
																	required="true" 
																	:readonly="! createMode && allStocks.length && allStocks[allStocks.length-1].variations.length > variationIndex && stockVariation.primary_quantity > allStocks[allStocks.length-1].variations[variationIndex].available_quantity" 
																	:min="createMode ? 1 : allStocks[allStocks.length-1].variations.length > variationIndex ? (stockVariation.primary_quantity - allStocks[allStocks.length-1].variations[variationIndex].available_quantity) : 1"
																>

																<div class="invalid-feedback">
														        	{{ errors.stock.variations[variationIndex].product_variation_quantity }}
														  		</div>
															</div>

															<div 
																class="form-group col-md-4"
															>
																<label for="inputFirstName">Buying Price (unit)</label>

																<div class="input-group mb-0">
																	<input 
																		type="number" 
																		class="form-control is-valid" 
																		v-model.number="singleStockData.variations[variationIndex].unit_buying_price" 
																		placeholder="Variation Buying Price" 
																		min="1"
																	>
																	<div class="input-group-append">
																		<span class="input-group-text">
																			{{ general_settings.official_currency_name || 'BDT' | capitalize }}
																		</span>
																	</div>
																</div>
															</div>
															
															<!-- 
															<div class="form-group col-md-3">
																<label for="inputFirstName">Price</label>
																<label class="col-form-label">
																	{{ productVariation.selling_price }}
																</label>
															</div>

															<div class="form-group col-md-3">
																<label for="inputFirstName">SKU</label>
																<label class="col-form-label">
																	{{ productVariation.sku }}
																</label>
															</div>
		 													-->
														</div>
													</div>
												</div>
										    	 
										    	<div class="form-row">
											    	<div class="form-group col-sm-12 mb-2 text-right card-footer">
										          		<div class="text-danger small mb-1" v-show="!submitForm">
													  		Please input required fields
											          	</div>
											          	<button type="button" class="btn btn-outline-secondary btn-sm btn-round" v-on:click="nextPage" v-tooltip.bottom-end="'Next'">
									                    	<i class="fa fa-2x fa-angle-double-right" aria-hidden="true"></i>
									                  	</button>
										          	</div>
										    	</div>
											</div>	
										</div>
									</div>
							    </div>

							    <div 
									class="row" 
									v-bind:key="'product-modal-step-' + 2" 
									v-show="!loading && step==2"
								>
									<h2 class="mx-auto mb-4 lead">Manufacturing & Expiring Date</h2>

									<div class="col-md-12">
										<div class="row">
											<!-- Product (No Variation) Mfg. & Exp. Date -->
											<div class="col-md-12" v-show="! productMerchant.has_variations">
												<div class="form-row d-flex">
													<div class="col-md-4 text-center align-self-center">
														<img 
															:src="showPreview(productMerchant.preview)" 
															class="img-fluid" 
															alt="Product Preview" 
															width="150px"
														>
													</div>

													<div class="col-md-8">
														<div class="form-row form-group">
															<label class="col-sm-4 col-form-label font-weight-bold text-md-right">
																Manufacturing Date :
															</label>
															<div class="col-sm-8">
																<v-date-picker 
																	v-model="singleStockData.manufactured_at" 
																	color="red" 
																	is-dark
																	is-inline 
																	:max-date="new Date()" 
																	:model-config="{ type: 'string', mask: 'YYYY-MM-DD' }" 
																	:attributes="[ { key: 'today', dot: true, dates: new Date() } ]" 
																	@input="validateFormInput('manufacturing_date')"
																/>

																<div class="invalid-feedback" 
																	style="display: block;" 
																	v-show="errors.stock.manufacturing_date"
																>
														        	{{ errors.stock.manufacturing_date }}
														  		</div>
															</div>
														</div>

														<div class="form-row form-group">
															<label class="col-sm-4 col-form-label font-weight-bold text-md-right">
																Expiring Date :
															</label>
															<div class="col-sm-8">
																<v-date-picker 
																	v-model="singleStockData.expired_at" 
																	color="red" 
																	is-dark
																	is-inline 
																	:min-date="singleStockData.manufactured_at" 
																	:model-config="{ type: 'string', mask: 'YYYY-MM-DD' }" 
																	:attributes="[ { key: 'today', dot: true, dates: new Date() } ]" 
																	@input="validateFormInput('expiring_date')"
																/>

																<div class="invalid-feedback" 
																	style="display: block;" 
																	v-show="errors.stock.expiring_date"
																>
														        	{{ errors.stock.expiring_date }}
														  		</div>
															</div>
														</div>
													</div>
												</div>
											</div>

											<!-- Variations Mfg. & Exp. Date -->
											<div 
												class="col-md-12" 
												v-if="productMerchant.has_variations && singleStockData.hasOwnProperty('variations') && singleStockData.variations.length"
											>
												<div 
													class="card" 
													v-for="(stockVariation, variationIndex) in singleStockData.variations" 
													:key="'product-variation-index-' + variationIndex + '-date-picker'"
												>	
													<div 
														class="card-header" 
														v-show="stockVariation.stock_quantity > 0"
													>
														<h5>
															{{ stockVariation.variation ? stockVariation.variation.name : '' | capitalize }}
														</h5>
													</div>

													<div 
														class="card-body"
														v-show="stockVariation.stock_quantity > 0"
													>
														<div class="form-row">
															<div class="form-group col-md-12 text-center">
																<img 
																	:src="showPreview(stockVariation.preview)" 
																	class="img-fluid" 
																	:alt="stockVariation.variation.name + ' Preview'" 
																	width="150px"
																>
																<p class="text-center">{{ stockVariation.variation.name | capitalize }}</p>
															</div>
														</div>

														<div class="form-row">
															<div class="form-group col-md-6">
																<label class="d-block" for="inputFirstName">Manufacturing Date</label>
																
																<v-date-picker 
																	v-model="stockVariation.manufactured_at" 
																	color="red" 
																	is-dark
																	is-inline 
																	:max-date="new Date()" 
																	:model-config="{ type: 'string', mask: 'YYYY-MM-DD' }" 
																	:attributes="[ { key: 'today', dot: true, dates: new Date() } ]" 
																	@input="validateFormInput('manufacturing_date')"
																/>

																<div class="invalid-feedback" 
																	style="display: block;" 
																	v-show="errors.stock.variations[variationIndex].manufacturing_date"
																>
														        	{{ errors.stock.variations[variationIndex].manufacturing_date }}
														  		</div>
															</div>

															<div class="form-group col-md-6">
																<label class="d-block" for="inputFirstName">Expiring Date</label>
																<v-date-picker 
																	v-model="stockVariation.expired_at" 
																	color="red" 
																	is-dark
																	is-inline 
																	:model-config="{ type: 'string', mask: 'YYYY-MM-DD' }" 
																	:attributes="[ { key: 'today', dot: true, dates: new Date() } ]" 
																	@input="validateFormInput('expiring_date')"
																/>

																<div class="invalid-feedback" 
																	style="display: block;" 
																	v-show="errors.stock.variations[variationIndex].expiring_date"
																>
														        	{{ errors.stock.variations[variationIndex].expiring_date }}
														  		</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-sm-12 text-right" v-show="!submitForm">
												<span class="text-danger small mb-1">
											  		Please input required fields
											  	</span>
											</div>
											<div class="col-sm-12 d-flex justify-content-between">
												<button 
													type="button" 
													class="btn btn-outline-secondary btn-sm btn-round" 
													v-tooltip.bottom-end="'Previous'" 
													v-on:click="step-=1"
												>
							                    	<i class="fa fa-2x fa-angle-double-left" aria-hidden="true"></i>
							                  	</button>

												<button 
													type="button" 
													class="btn btn-outline-secondary btn-sm btn-round" 
													v-tooltip.bottom-end="'Next'" 
													v-on:click="nextPage"
												>
							                    	<i class="fa fa-2x fa-angle-double-right" aria-hidden="true"></i>
							                  	</button>
											</div>
										</div>
									</div>
							    </div>

							    <div 
								    class="row" 
								    v-bind:key="'product-modal-step-' + 3" 
								    v-show="!loading && step==3"
							    >
							    	<h2 class="mx-auto mb-4 lead">{{ product.name | capitalize }} Serials</h2>

							    	<div 
										class="col-md-12" 
										v-if="productMerchant.has_serials && productMerchant.has_variations && singleStockData.variations.length && step==3"
									>
										<div 
											class="form-row" 
											v-for="(stockedVariation, stockedVariationIndex) in singleStockData.variations" 
											:key="'product-variation-index-' + stockedVariationIndex + '-B'"
										>	
											<div 
												class="card col-md-12" 
												v-if="stockedVariation.stock_quantity > 0 && stockedVariation.hasOwnProperty('serials')"
											>
												<div class="card-header text-center">
													{{ stockedVariation.variation.name | capitalize }} Serials
												</div>
												
												<div class="card-body">
													<div class="form-row">
														<div class="col-sm-12 form-group">
															<label for="inputFirstName">
																Serial No.
															</label>

															<div class="input-group mb-0">
																<input 
																	type="text" 
																	class="form-control" 
																	placeholder="Variation Serial number" 
																	v-model="variationNewSerial[stockedVariationIndex]"
																	:class="! errors.stock.variations[stockedVariationIndex].product_variation_serial ? 'is-valid' : 'is-invalid'" 
																	@keydown.enter.prevent="addVariationSerial(stockedVariationIndex)" 
																	@change="validateFormInput('product_variation_serial')" 
																	required="true" 
																	:disabled="stockedVariation.serials.length>=stockedVariation.stock_quantity && stockedVariation.serials.every(variationSerial=>variationSerial.serial_no)"
																>

																<div class="input-group-append">
																	<span class="input-group-text" id="basic-addon2">
																		<button 
																			type="button" 
																			class="btn btn-primary" 
																			v-tooltip.bottom-end="'Insert Serial'" 
																			@click="addVariationSerial(stockedVariationIndex)"
																		>
																			Enlist
																		</button>
																	</span>
																</div>
															</div>

															<div 
																class="invalid-feedback" 
																style="display: block;" 
																v-show="errors.stock.variations[stockedVariationIndex].product_variation_serial"
															>
													        	{{ errors.stock.variations[stockedVariationIndex].product_variation_serial }}
													  		</div>
														</div>

														<div class="col-sm-12 form-group border border-success p-4 rounded-sm">
															<ul id="merchant-product-variation-serials">
																<li 
																	v-for="(productVariationSerial, productVariationSerialIndex) in stockedVariation.serials"
																	:key="'product-variation-index-' + stockedVariationIndex + '-product-variation-' + stockedVariation.variation.name + '-serial-' + productVariationSerialIndex"
																>	
																	{{ productVariationSerial.serial_no }}

																	<i 
																		class="fa fa-close text-danger p-2" 
																		v-tooltip.bottom-end="'Remove'" 
																		v-show="productVariationSerial.serial_no && ! productVariationSerial.has_requisitions && ! productVariationSerial.has_dispatched"
																		:disabled="productVariationSerial.has_requisitions || productVariationSerial.has_dispatched" 
																		@click="removeVariationSerial(stockedVariationIndex, productVariationSerialIndex)"
																	>	
																	</i>

																	<span 
																		v-show="productVariationSerial.has_requisitions || productVariationSerial.has_dispatched"
																		:class="[productVariationSerial.has_requisitions ? 'badge badge-warning' : productVariationSerial.has_dispatched ? 'badge badge-danger' : '']"
																	>
																		{{ productVariationSerial.has_requisitions ? 'Requested' : productVariationSerial.has_dispatched ? 'Dispatched' : '' }}
																	</span>
																</li>
															</ul>
														</div>
													</div>

													<div class="form-row">
														<div class="col-sm-12">
															<div 
																class="invalid-feedback text-center" 
																style="display: block;" 
																v-show="errors.stock.variations[stockedVariationIndex].product_total_serials"
															>
													        	{{ errors.stock.variations[stockedVariationIndex].product_total_serials }}
													  		</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div 
										class="col-md-12"
										v-else-if="productMerchant.has_serials && ! productMerchant.has_variations && singleStockData.stock_quantity > 0 && step==3"
									>		
										<div class="form-row">
											<div class="col-sm-12 form-group">
												<label for="inputFirstName">
													Serial No.
												</label>

												<div class="input-group mb-0">
													<input 
														type="text" 
														required="true" 
														class="form-control" 
														v-model="productNewSerial" 
														placeholder="Product Serial number" 
														:class="! errors.stock.product_serial ? 'is-valid' : 'is-invalid'" 
														@keydown.enter.prevent="addProductSerial()" 
														@change="validateFormInput('product_serial')" 
														:disabled="singleStockData.serials.length >= singleStockData.stock_quantity && singleStockData.serials.every(productSerial=>productSerial.serial_no)"
													>

													<div class="input-group-append">
														<span class="input-group-text" id="basic-addon2">
															<button 
																type="button" 
																class="btn btn-primary" 
																v-tooltip.bottom-end="'Insert Serial'" 
																@click="addProductSerial()"
															>
																Enlist
															</button>
														</span>
													</div>
												</div>

												<div 
													class="invalid-feedback" 
													style="display: block;" 
													v-show="errors.stock.product_serial"
												>
										        	{{ errors.stock.product_serial }}
										  		</div>
											</div>

											<div class="col-sm-12 form-group border border-success p-4 rounded-sm">
												<ul id="merchant-product-variation-serials">
													<li 
														v-for="(productSerial, productSerialIndex) in singleStockData.serials"
														:key="'product-' + productMerchant.product.name + '-serial-' + productSerialIndex"
													>	
														{{ productSerial.serial_no }}

														<i 
															class="fa fa-close text-danger p-2" 
															v-tooltip.bottom-end="'Remove'" 
															v-show="productSerial.serial_no && ! productSerial.has_requisitions && ! productSerial.has_dispatched"
															:disabled="productSerial.has_requisitions || productSerial.has_dispatched" 
															@click="removeProductSerial(productSerialIndex)"
														>	
														</i>

														<span 
															v-show="productSerial.has_requisitions || productSerial.has_dispatched"
															:class="[productSerial.has_requisitions ? 'badge badge-warning' : productSerial.has_dispatched ? 'badge badge-danger' : '']"
															>
															{{ productSerial.has_requisitions ? 'Requested' : productSerial.has_dispatched ? 'Dispatched' : '' }}
														</span>
													</li>
												</ul>
											</div>
										</div>

										<div class="form-row">
											<div class="col-sm-12">
												<div 
													class="invalid-feedback text-center" 
													style="display: block;" 
													v-show="errors.stock.product_total_serials"
												>
										        	{{ errors.stock.product_total_serials }}
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
											<div class="col-sm-12 d-flex justify-content-between">
												<button 
													type="button" 
													class="btn btn-outline-secondary btn-sm btn-round" 
													v-tooltip.bottom-end="'Previous'" 
													v-on:click="step-=1"
												>
							                    	<i class="fa fa-2x fa-angle-double-left" aria-hidden="true"></i>
							                  	</button>

												<button 
													type="button" 
													class="btn btn-outline-secondary btn-sm btn-round" 
													v-tooltip.bottom-end="'Next'" 
													v-on:click="nextPage"
												>
							                    	<i class="fa fa-2x fa-angle-double-right" aria-hidden="true"></i>
							                  	</button>
											</div>
										</div>
									</div>
							    </div>
						     
							    <div 
									class="row" 
									v-bind:key="'product-modal-step-' + 4" 
									v-show="!loading && step==4" 
								>
									<h2 class="mx-auto mb-4 lead">Store Stock</h2>

									<div 
										class="col-md-12"
										v-for="(stockSpace, spaceIndex) in singleStockData.addresses" 
										:key="'product-space-' + spaceIndex"
									>
										<div 
											class="card"
											v-if="stockSpace && errors.stock.addresses[spaceIndex]"
										>
											<div class="card-body">

												<div class="form-row ml-5 mr-5">
													<div class="form-group col-md-12 text-center">
														<label for="inputFirstName">
															Required Space Type {{ spaceIndex + 1 }}
														</label>
														<multiselect 
					                              			v-model="stockSpace.type"
					                                  		:options="['containers', 'shelves', 'units']" 
					                                  		:custom-label="nameWithCapitalized" 
					                                  		:required="true" 
					                                  		:allow-empty="false"
					                              			placeholder="Containers / Shelves / Units" 
					                              			class="form-control p-0" 
					                                  		:class="!errors.stock.addresses[spaceIndex].product_space_type  ? 'is-valid' : 'is-invalid'" 
					                                  		:disabled="singleStockData.addresses.length > (spaceIndex+1)" 
					                                  		@input="setProductSpaceType(spaceIndex)" 
					                                  		@close="validateFormInput('product_space_type')"
					                              		>
					                                	</multiselect>
					                                	<div class="invalid-feedback">
													    	{{ errors.stock.addresses[spaceIndex].product_space_type }}
													    </div>
													</div>
												</div>

												<div 
													class="form-row" 
													v-show="stockSpace.type=='containers'"
												>
													<div class="form-group col-md-12">
														<label for="inputFirstName">Select Containers</label>
														<multiselect 
					                              			v-model="stockSpace.containers"
					                              			placeholder="Select Containers" 
					                              			label="name" 
					                                  		track-by="id" 
					                                  		:options="emptyContainers" 
					                                  		:multiple="true" 
					                                  		:close-on-select="false" 
					                                  		:clear-on-select="false" 
					                                  		:preserve-search="true" 
					                                  		:required="true" 
					                                  		:allow-empty="false" 
					                                  		class="form-control p-0" 
					                                  		:class="!errors.stock.addresses[spaceIndex].product_containers  ? 'is-valid' : 'is-invalid'" 
					                                  		:disabled="singleStockData.addresses.length > (spaceIndex+1)"
					                                  		@close="validateFormInput('product_containers')" 
					                              		>
					                                	</multiselect>
					                                	<div class="invalid-feedback">
													    	{{ errors.stock.addresses[spaceIndex].product_containers }}
													    </div>
													</div>
												</div>

												<div 
													class="form-row" 
													v-show="stockSpace.type=='shelves'"
												>
													<div class="form-group col-md-6">
														<label for="inputFirstName">Select Parent Container</label>
														<multiselect 
					                              			v-model="stockSpace.container"
					                              			placeholder="Parent Container" 
					                              			label="name" 
					                                  		track-by="id" 
					                                  		:options="emptyShelfContainers" 
					                                  		:required="true" 
					                                  		:allow-empty="false" 
					                                  		class="form-control p-0" 
					                                  		:class="!errors.stock.addresses[spaceIndex].product_container ? 'is-valid' : 'is-invalid'" 
					                                  		:disabled="singleStockData.addresses.length > (spaceIndex+1)"
					                                  		@input="setAvailableShelves(spaceIndex)"
					                                  		@close="validateFormInput('product_container')" 
					                              		>
					                                	</multiselect>
					                                	<div class="invalid-feedback">
													    	{{ errors.stock.addresses[spaceIndex].product_container }}
													    </div>
													</div>

													<div 
														class="form-group col-md-6" 
														v-if="stockSpace.container"
													>
														<label for="inputFirstName">Select Shelves</label>
														<multiselect 
					                              			v-model="stockSpace.container.shelves"
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
					                                  		:class="!errors.stock.addresses[spaceIndex].product_shelves ? 'is-valid' : 'is-invalid'" 
					                                  		:disabled="singleStockData.addresses.length > (spaceIndex+1)"
					                                  		@close="validateFormInput('product_shelves')" 
					                              		>
					                                	</multiselect>
					                                	<div class="invalid-feedback">
													    	{{ errors.stock.addresses[spaceIndex].product_shelves }}
													    </div>
													</div>

													<div 
														class="form-group col-md-6" 
														v-else 
													>
														<label for="inputFirstName">Select Shelves</label>
														<multiselect 
					                              			placeholder="Select Shelves" 
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
					                                  		:class="!errors.stock.addresses[spaceIndex].product_shelves ? 'is-valid' : 'is-invalid'" 
					                                  		:disabled="singleStockData.addresses.length > (spaceIndex+1)"
					                                  		@close="validateFormInput('product_shelves')" 
					                              		>
					                                	</multiselect>
					                                	<div class="invalid-feedback">
													    	{{ errors.stock.addresses[spaceIndex].product_shelves }}
													    </div>
													</div>
												</div>

												<div class="form-row" v-show="stockSpace.type=='units'">
													<div class="form-group col-md-4">
														<label for="inputFirstName">Select Parent Container</label>
														<multiselect 
					                              			v-model="stockSpace.container"
					                              			placeholder="Parent Container" 
					                              			label="name" 
					                                  		track-by="id" 
					                                  		:options="emptyUnitContainers" 
					                                  		:required="true" 
					                                  		:allow-empty="false" 
					                                  		class="form-control p-0" 
					                                  		:class="!errors.stock.addresses[spaceIndex].product_container  ? 'is-valid' : 'is-invalid'" 
					                                  		:disabled="singleStockData.addresses.length > (spaceIndex+1)"
					                                  		@input="setAvailableUnitShelves(spaceIndex)" 
					                                  		@close="validateFormInput('product_container')" 
					                              		>
					                                	</multiselect>
					                                	<div class="invalid-feedback">
													    	{{ errors.stock.addresses[spaceIndex].product_container }}
													    </div>
													</div>

													<div 
														class="form-group col-md-4" 
														v-if="stockSpace.container"
													>
														<label for="inputFirstName">Select Parent Shelf</label>
														<multiselect 
					                              			v-model="stockSpace.container.shelf"
					                              			placeholder="Parent Shelf" 
					                              			label="name" 
					                                  		track-by="id" 
					                                  		:options="emptyUnitShelves" 
					                                  		:required="true" 
					                                  		:allow-empty="false" 
					                                  		class="form-control p-0" 
					                                  		:class="!errors.stock.addresses[spaceIndex].product_shelf  ? 'is-valid' : 'is-invalid'" 
					                                  		:disabled="singleStockData.addresses.length > (spaceIndex+1)"
					                                  		@input="setAvailableUnits(spaceIndex)" 
					                                  		@close="validateFormInput('product_shelf')" 
					                              		>
					                                	</multiselect>
					                                	<div class="invalid-feedback">
													    	{{ errors.stock.addresses[spaceIndex].product_shelf }}
													    </div>
													</div>

													<div 
														class="form-group col-md-4" 
														v-else
													>
														<label for="inputFirstName">Select Parent Shelf</label>
														<multiselect 
					                              			placeholder="Parent Shelf" 
					                              			label="name" 
					                                  		track-by="id" 
					                                  		:options="[]" 
					                                  		:required="true" 
					                                  		:allow-empty="false" 
					                                  		class="form-control p-0" 
					                                  		:class="!errors.stock.addresses[spaceIndex].product_shelf  ? 'is-valid' : 'is-invalid'" 
					                                  		:disabled="singleStockData.addresses.length > (spaceIndex+1)"
					                                  		@input="setAvailableUnits(spaceIndex)" 
					                                  		@close="validateFormInput('product_shelf')" 
					                              		>
					                                	</multiselect>
					                                	<div class="invalid-feedback">
													    	{{ errors.stock.addresses[spaceIndex].product_shelf }}
													    </div>
													</div>

													<div 
														class="form-group col-md-4" 
														v-if="stockSpace.container && stockSpace.container.shelf"
													>
														<label for="inputFirstName">Select Units</label>
														<multiselect 
					                              			v-model="stockSpace.container.shelf.units"
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
					                                  		:class="!errors.stock.addresses[spaceIndex].product_units ? 'is-valid' : 'is-invalid'" 
					                                  		:disabled="singleStockData.addresses.length > (spaceIndex+1)"
					                                  		@close="validateFormInput('product_units')" 
					                              		>
					                                	</multiselect>
					                                	<div class="invalid-feedback">
													    	{{ errors.stock.addresses[spaceIndex].product_units }}
													    </div>
													</div>

													<div 
														class="form-group col-md-4" 
														v-else
													>
														<label for="inputFirstName">Select Units</label>
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
					                                  		:class="!errors.stock.addresses[spaceIndex].product_units ? 'is-valid' : 'is-invalid'" 
					                                  		:disabled="singleStockData.addresses.length > (spaceIndex+1)"
					                                  		@close="validateFormInput('product_units')" 
					                              		>
					                                	</multiselect>
					                                	<div class="invalid-feedback">
													    	{{ errors.stock.addresses[spaceIndex].product_units }}
													    </div>
													</div>
												</div>

											</div>
										</div>
									</div>

									<div 
										class="col-md-12 text-center" 
										v-show="!singleStockData.addresses.length"
									>
										<p class="text-danger">
											No Space Found.
										</p>

										<p class="text-danger" v-show="errors.stock.product_address">
											{{ errors.stock.product_address }}
										</p>
									</div>

									<div class="col-md-12">
										<div class="form-row">
											<div class="form-group col-md-6">
												<button 
													type="button" 
													class="btn waves-effect waves-light hor-grd btn-grd-primary btn-sm btn-block" 
													v-tooltip.bottom-end="'More Space'" 
													@click="addMoreSpace()"
												>
													Add Space
												</button>
											</div>
											<div class="form-group col-md-6">
												<button 
													type="button" 
													class="btn waves-effect waves-light hor-grd btn-grd-info btn-sm btn-block" 
													v-tooltip.bottom-end="'Remove Space'" 
													:disabled="createMode ? singleStockData.addresses.length < 2 : singleStockData.addresses.length < 1" 
													@click="removeSpace()"
												>
													Remove Space
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
												<button 
													type="button" 
													title="Previous"  
													data-placement="top" 
													data-toggle="tooltip" 
													class="btn btn-outline-secondary btn-sm btn-round float-left" 
													v-on:click="productMerchant.has_serials ? step-=1 : step-=2"
												>
							                    	<i class="fa fa-2x fa-angle-double-left" aria-hidden="true"></i>
							                  	</button>

												<button 
													type="submit" 
													:class="[singleStockData.has_approval ? 'btn-primary' : 'btn-warning', 'btn', 'float-right']" 
													:disabled="!submitForm || formSubmitted"
												>
													{{ createMode ? 'Stock' : singleStockData.has_approval==1 ? 'Update' : 'Approve' }}
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
			v-if="userHasPermissionTo('delete-product-stock')" 
			:csrf="csrf" 
			:submit-method-name="'deleteStock'" 
			:content-to-delete="singleStockData"
			:restoration-message="'You can not restore this item again !'" 
			
			@deleteStock="deleteStock($event)" 
		></delete-confirmation-modal>

		<!-- 
		<restore-confirmation-modal 
			:csrf="csrf" 
			:submit-method-name="'restoreAsset'" 
			:content-to-restore="singleStockData"
			:restoration-message="'This will restore all related items !'" 

			@restoreAsset="restoreAsset($event)" 
		></restore-confirmation-modal>
 		-->

 		<!-- View Modal -->
		<div class="modal fade" id="stock-view-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
								
								<ul class="nav nav-tabs tabs justify-content-center" role="tablist">
									<!-- 
									<li class="nav-item">
										<a class="nav-link" data-toggle="tab" href="#product-profile" role="tab">
											Product
										</a>
									</li>
 									-->
									
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

									<li class="nav-item">
										<a class="nav-link" data-toggle="tab" href="#product-address" role="tab">
											Address
										</a>
									</li>
								</ul>

								<div class="tab-content tabs card-block">
									
									<!-- 
									<div class="tab-pane active" id="product-profile" role="tabpanel">
										<div class="form-row">
											<label class="col-sm-4 col-form-label font-weight-bold text-right">
												Type :
											</label>
											<label class="col-sm-8 col-form-label">
												{{ product.category ? product.category.name : 'Bulk Product' }}
											</label>
										</div>

										<div class="form-row">
											<label class="col-sm-4 col-form-label font-weight-bold text-right">
												Merchant :
											</label>
											<label class="col-sm-8 col-form-label">
												{{ product.merchant ? product.merchant.user_name : 'None' }}
											</label>
										</div>

										<div class="form-row">
											<label class="col-sm-4 col-form-label font-weight-bold text-right">
												Name :
											</label>
											<label class="col-sm-8 col-form-label">
												{{ product.name }}
											</label>
										</div>

										<div class="form-row">
											<label class="col-sm-4 col-form-label font-weight-bold text-right">
												SKU Code :
											</label>
											<label class="col-sm-8 col-form-label">
												{{ product.sku }}
											</label>
										</div>

										<div class="form-row">
											<label class="col-sm-4 col-form-label font-weight-bold text-right">
												Price :
											</label>
											<label class="col-sm-8 col-form-label">
												{{ product.price || 'NA' }}
											</label>
										</div>

										<div class="form-row">
											<label class="col-sm-4 col-form-label font-weight-bold text-right">
												Description :
											</label>
											<label class="col-sm-8 col-form-label">
												<span v-html="product.description"></span>
											</label>
										</div>

										
										<div class="form-row">
											<label class="col-sm-4 col-form-label font-weight-bold text-right">
												Available Qty:
											</label>
											<label class="col-sm-8 col-form-label">
												{{ product.available_quantity }}
												{{ product.quantity_type }}
											</label>
										</div>
 										

										<div class="form-row">
											<label class="col-sm-4 col-form-label font-weight-bold text-right">Has Variation :</label>
											<label class="col-sm-6 form-control-plaintext">
												<span :class="[product.has_variations ? 'badge-success' : 'badge-danger', 'badge']">{{ product.has_variations ? 'Available' : 'NA' }}</span>
											</label>
										</div>

										<div class="form-row" v-if="product.has_variations && product.variations.length">
											<label class="col-sm-4 col-form-label font-weight-bold text-right">
												Variations :
											</label>
											<div class="col-sm-12">
												<div class="form-row">
													
													<div 
														class="col-md-6 ml-auto" 
														v-for="(productVariation, variationIndex) in product.variations" 
														:key="'product-variation-' + variationIndex"
													>
														<div class="card">
															<div class="card-body">
																
																<div class="form-row">
																	<label class="col-sm-4 col-form-label font-weight-bold text-right">
																		Name :
																	</label>
																	<label class="col-sm-8 col-form-label">
																		{{ productVariation.variation ? productVariation.variation.name : 'NA' }}
																	</label>
																</div>

																<div class="form-row">
																	<label class="col-sm-4 col-form-label font-weight-bold text-right">
																		SKU :
																	</label>
																	<label class="col-sm-8 col-form-label">
																		{{ productVariation.sku }}
																	</label>
																</div>

																<div class="form-row">
																	<label class="col-sm-4 col-form-label font-weight-bold text-right">Price :</label>
																	<label class="col-sm-8 col-form-label">
																		{{ productVariation.price }}
																	</label>
																</div>

																
																<div class="form-row">
																	<label class="col-sm-4 col-form-label font-weight-bold text-right">Available Qty :</label>
																	<label class="col-sm-8 col-form-label">
																		{{ productVariation.available_quantity }}
																	</label>
																</div>
 																
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
 									-->

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
												{{ productMerchant.merchant ? productMerchant.merchant.first_name + ' ' + productMerchant.merchant.last_name : 'Mr. Merchant' | capitalize }}
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
											        :jsbarcode-height=25 
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

														<i class="fa fa-print fa-lg text-danger" aria-hidden="true" @click="printStockCode({ has_serials : singleStockData.has_serials, has_variations : singleStockData.has_variations, stock_code : singleStockData.stock_code, serials : [ { serial_no : productSerial.serial_no } ] })" v-tooltip.bottom-end="'Print'"></i>

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

																		<i class="fa fa-print fa-lg text-danger" aria-hidden="true" @click="printStockCode({ has_serials : singleStockData.has_serials, has_variations : singleStockData.has_variations, variations : [ { stock_code : stockVariation.stock_code, variation : stockVariation.variation, serials : [ { serial_no : variationSerial.serial_no } ] } ] })" v-tooltip.bottom-end="'Print'"></i>

																		<span v-show="(variationIndex + 1) < stockVariation.serials.length">, </span> 
																	</li>	
																</ol>
															</label>
														</div>
														
														<!-- 
														<div class="form-row">
															<label class="col-form-label font-weight-bold text-right">
																Available Quantity :
															</label>
															<label class="col-form-label">
																{{ stockVariation.available_quantity }}
															</label>
														</div>
														-->
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="tab-pane" id="product-address" role="tabpanel">
										<div 
											class="form-row" 
											v-if="singleStockData.hasOwnProperty('addresses') && singleStockData.addresses.length"
										>
											<label class="col-sm-4 col-form-label font-weight-bold text-md-right">
												Address Detail :
											</label>
											<div class="col-sm-12">
												<div class="form-row">
													<div 
														class="col-md-6 ml-auto" 
														v-for="(stockAddress, addressIndex) in singleStockData.addresses" 
														:key="'stock-address-' + stockAddress.type + addressIndex"
													>
														<div 
															class="card" 
															v-if="stockAddress.hasOwnProperty('type') && stockAddress.type.includes('containers')"
														>
															<div 
																class="card-body" 
																v-for="containerAddress in stockAddress.containers" 
																:key="'container-address-' + containerAddress.id + 'address-index-' + addressIndex + '-stock-id-' + singleStockData.id"
															>
																<h6>Container Address</h6>

																<div class="form-row">
																	<label class="col-sm-6 col-form-label font-weight-bold text-md-right">
																		Warehouse :
																	</label>
																	<label class="col-sm-6 col-form-label">
																		{{ containerAddress.warehouse_container ? $options.filters.capitalize(containerAddress.warehouse_container.warehouse.name) : 'NA' }}
																	</label>
																</div>

																<div class="form-row">
																	<label class="col-sm-6 col-form-label font-weight-bold text-md-right">
																		Container Type :
																	</label>
																	<label class="col-sm-6 col-form-label">
																		{{ containerAddress.warehouse_container ? $options.filters.capitalize(containerAddress.warehouse_container.container.name) : 'NA' }}
																	</label>
																</div>

																<div class="form-row">
																	<label class="col-sm-6 col-form-label font-weight-bold text-md-right">
																		Container # :
																	</label>
																	<label class="col-sm-6 col-form-label">
																		{{ containerAddress.name.substring(containerAddress.name.indexOf("-")+1) }}
																	</label>
																</div>

															</div>
														</div>

														<div 
															class="card" 
															v-if="stockAddress.hasOwnProperty('type') && stockAddress.type.includes('shelves') && stockAddress.hasOwnProperty('container') &&  stockAddress.container.hasOwnProperty('warehouse_container')"
														>
															<div class="card-body">

																<h6>Shelves Address</h6>
																
																<div class="form-row">
																	<label class="col-sm-6 col-form-label font-weight-bold text-md-right">
																		Container Type :
																	</label>
																	<label class="col-sm-6 col-form-label">
																		{{ stockAddress.container.warehouse_container.container.name | capitalize }}
																	</label>
																</div>

																<div class="form-row">
																	<label class="col-sm-6 col-form-label font-weight-bold text-md-right">
																		Container # :
																	</label>
																	<label class="col-sm-6 col-form-label">
																		{{ stockAddress.container.name.substring(stockAddress.container.name.indexOf("-")+1) }}
																	</label>
																</div>

																<div 
																	class="form-row"
																>
																	<label class="col-sm-6 col-form-label font-weight-bold text-md-right">
																		Shelf # :
																	</label>
																	<label class="col-sm-6 col-form-label">

																		<ul id="shelf-addresses">
																			<li 
																				v-for="shelfAddress in stockAddress.container.shelves" 
																				:key="'shelf-address-' + shelfAddress.id"
																			>

																				{{ shelfAddress.name.substring(shelfAddress.name.lastIndexOf("-")+1) }}
																				
																			</li>
																		</ul>

																	</label>
																</div>

															</div>
														</div>

														<div 
															class="card" 
															v-if="stockAddress.hasOwnProperty('type') && stockAddress.type.includes('units') && stockAddress.hasOwnProperty('container') && stockAddress.container.hasOwnProperty('warehouse_container')"
														>
															<div class="card-body">
																
																<h6>Units Address</h6>

																<div class="form-row">
																	<label class="col-sm-6 col-form-label font-weight-bold text-md-right">
																		Container Type :
																	</label>
																	<label class="col-sm-6 col-form-label">
																		{{ stockAddress.container.warehouse_container.container.name | capitalize }}
																	</label>
																</div>

																<div class="form-row">
																	<label class="col-sm-6 col-form-label font-weight-bold text-md-right">
																		Container # :
																	</label>
																	<label class="col-sm-6 col-form-label">
																		{{ stockAddress.container.name.substring(stockAddress.container.name.indexOf("-")+1) }}
																	</label>
																</div>

																<div class="form-row">
																	<label class="col-sm-6 col-form-label font-weight-bold text-md-right">
																		Shelf # :
																	</label>
																	<label class="col-sm-6 col-form-label">
																		{{ stockAddress.container.shelf.name.substring(stockAddress.container.shelf.name.lastIndexOf("-")+1) }}
																	</label>
																</div>

																<div class="form-row">
																	<label class="col-sm-6 col-form-label font-weight-bold text-md-right">
																		Unit # :
																	</label>
																	<label class="col-sm-6 col-form-label">

																		<ul id="unit-addresses">
																			<li 
																				v-for="unitAddress in stockAddress.container.shelf.units" 
																				:key="'unit-address-' + unitAddress.id"
																			>

																				{{ unitAddress.name.substring(unitAddress.name.lastIndexOf("-")+1) }}
																				
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

										<div class="form-row" v-else>
											<div 
												class="col-md-12 text-center" 
												v-show="!singleStockData.hasOwnProperty('addresses') || !singleStockData.addresses.length"
											>
												<p class="text-danger">
													No Space Found.
												</p>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="modal-footer">
						<button type="button" class="btn btn-secondary mr-auto" data-dismiss="modal">
							Close
						</button>

						<button 
							type="button" 
							class="btn btn-danger" 
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
		<div class="modal fade" id="stock-custom-search" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
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
						class="btn btn-success" 
						v-tooltip.bottom-end="'Reset'"  
						@click="resetSearchingDates()"
						>
	                  		Reset
	                  	</button>

						<button type="button" class="btn btn-primary ml-auto" data-dismiss="modal">
	                  		See Results
	                  	</button>
					</div>
				</div>
			</div>
		</div>

		<div id="productCodeToPrint" class="d-none">
			<div class="card">
				<!-- 
				<div class="card-header">
					<div class="form-row text-center" v-show="! singleStockData.has_variations">
						<label class="col-12 col-form-label font-weight-bold">
							Stock (Batch) Code :
						</label>

						<label class="col-12 col-form-label">
							<svg 
						  		class="barcode" 
						        :jsbarcode-format="'CODE128'" 
						        :jsbarcode-value="singleStockData.stock_code"
						        jsbarcode-width=1 
						        jsbarcode-height=25 
						        last-char= ">"
						    >    
					        </svg> 
						</label>
					</div> 
				</div>
				-->

				<div 
					class="card-body form-group" 
					v-for="(productSerial, productSerialIndex) in productToPrint.serials"
					:key="'printing-product-serial-index-' + productSerialIndex + '-product-' + productSerial.id"
				>
					<div class="form-row text-center">
						<label class="col-12 col-form-label font-weight-bold">
							Stock (Batch) Code :
						</label>

						<label class="col-12 col-form-label">
							<svg 
						  		class="barcode" 
						        :jsbarcode-format="'CODE128'" 
						        :jsbarcode-value="productToPrint.stock_code"
						        jsbarcode-width=1 
						        jsbarcode-height=25 
						        last-char= ">"
						    >    
					        </svg> 
						</label>
					</div>

					<div class="form-row text-center">
						<label class="col-12 col-form-label font-weight-bold">
							Serial :
						</label>

						<label class="col-12 col-form-label">
							<svg 
						  		class="barcode" 
						        :jsbarcode-format="'CODE128'" 
						        :jsbarcode-value="productSerial.serial_no"
						        jsbarcode-width=1 
						        jsbarcode-height=25 
						        last-char= ">"
						    >    
					        </svg> 
						</label>
					</div>
				</div>

				<div 
					class="card-body" 
					v-for="(productVariation, productVariationIndex) in productToPrint.variations"
					:key="'printing-product-variation-index-' + productVariationIndex + '-variation-' + productVariation.id"
				>
					<!-- 
					<div class="form-row text-center" v-show="singleStockData.has_variations">
						<label class="col-12 col-form-label font-weight-bold">
							Stock (Batch) Code :
						</label>

						<label class="col-12 col-form-label">
							<svg 
						  		class="barcode" 
						        :jsbarcode-format="'CODE128'" 
						        :jsbarcode-value="productVariation.stock_code"
						        jsbarcode-width=1 
						        jsbarcode-height=25 
						        last-char= ">"
						    >    
					        </svg> 
						</label>
					</div> 
					-->

					<div 
						class="form-group"
						v-for="(productVariationSerial, productVariationSerialIndex) in productVariation.serials"
						:key="'printing-product-variation-index-' + productVariationIndex + '-variation-' + productVariation.id + '-serial-index-' + productVariationSerialIndex + '-serial-' + productVariationSerial.id"
					>
						<div class="form-row text-center">
							<label class="col-12 col-form-label font-weight-bold">
								Stock (Batch) Code :
							</label>

							<label class="col-12 col-form-label">
								<svg 
							  		class="barcode" 
							        :jsbarcode-format="'CODE128'" 
							        :jsbarcode-value="productVariation.stock_code"
							        jsbarcode-width=1 
							        jsbarcode-height=25 
							        last-char= ">"
							    >    
						        </svg> 
							</label>
						</div>

						<div class="form-row text-center">
							<label class="col-12 col-form-label font-weight-bold">
								Serial :
							</label>

							<label class="col-12 col-form-label">
								<svg 
							  		class="barcode" 
							        :jsbarcode-format="'CODE128'" 
							        :jsbarcode-value="productVariationSerial.serial_no"
							        jsbarcode-width=1 
							        jsbarcode-height=25 
							        last-char= ">"
							    >    
						        </svg> 
							</label>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div id="sectionToPrint" class="d-none">
			<div class="card">
				<div class="card-header">
					<div class="form-row">
						<div class="col-6">
							<img 
								class="img-fluid" 
								:src="'/' + general_settings.application_logo" 
								:alt="general_settings.app_name + ' Logo'"
							>
							
							<h5>
								{{ general_settings.app_name | capitalize }} Stock Invoice
							</h5>
						</div>

						<div class="col-6">
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
									{{ productMerchant.merchant ? productMerchant.merchant.first_name + ' ' + productMerchant.merchant.last_name : 'Mr. Merchant' | capitalize }}
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

											<!-- 
											<td>
												 
												<label class="col-form-label">
													{{ singleStockData.stock_code | capitalize }}
												</label> 
												

												<svg id="print-stock-code"></svg>

											  	<svg 
											  		class="barcode" 
											        :jsbarcode-format="(singleStockData.stock_code && singleStockData.stock_code.length > 12) ? 'CODE128' : 'EAN13'" 
											        :jsbarcode-value="singleStockData.stock_code"
											        jsbarcode-width=1 
											        jsbarcode-height=25 
											        last-char= ">"
											    >    
										        </svg> 
										    	
												 
												<barcode 
													:value="singleStockData.stock_code" 
													format="EAN" 
													background="#ccffff"
													lineColor= "#000000"
													width=1
													height=25
													class="d-block" 
												>
											    	Barcode Not Working.
											  	</barcode> 
											  	
											</td> 
											-->

											<!-- <td>{{ singleStockData.unit_buying_price }}</td> -->

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
											
											<!-- 
											<td>
												 
												<label class="col-form-label">
													{{ singleStockData.stock_code | capitalize }}
												</label> 
												

												<svg id="print-stock-code"></svg>

												<svg 
											  		class="barcode" 
											        :jsbarcode-format="(stockVariation.stock_code && stockVariation.stock_code.length > 12) ? 'CODE128' : 'EAN13'" 
											        :jsbarcode-value="stockVariation.stock_code"
											        jsbarcode-width=1 
											        jsbarcode-height=25 
											        last-char= ">"
											    >    
										        </svg> 

												 
												<barcode 
													:value="stockVariation.stock_code" 
													format="EAN"
													background="#ccffff"
													lineColor= "#000000"
													width=1
													height=25
													class="d-block" 
												>
											    	Barcode Not Working.
											  	</barcode> 
											  	
											</td> 
											-->

											<!-- 
											<td>
												{{ stockVariation.unit_buying_price }}
											</td> 
											-->

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

	import axios from 'axios';
	import Multiselect from 'vue-multiselect';
	// import JsBarcode from 'jsbarcode';

	export default {

	    components: { 
	    	// JsBarcode,
			multiselect : Multiselect,
		},

	    props: {

			product:{
				type: Object,
				required: true,
			},
			productMerchant:{
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
	        	// search : '',
	        	error : '',
    			perPage : 10,
	        	loading : false,

	        	createMode : true,
	        	submitForm : true,
	        	formSubmitted : false,

	        	allStocks : [],

	        	productNewSerial : '',
	        	variationNewSerial : [],

	        	// allContainers : [],
	        	allDealtEmptyWarehouses : [],
	        	
	        	emptyContainers : [],
	        	emptyShelfContainers : [],
	        	emptyUnitContainers : [],

	        	emptyShelves : [],
	        	emptyUnitShelves : [],

	        	emptyUnits : [],

	        	pagination: {
		        	current_page: 1
		      	},

	        	singleStockData : {

	        		serials : [],
	        		// product_id : this.product.id,
	        		merchant_product_id : this.productMerchant.id,
	        		variations : this.productMerchant.hasOwnProperty('variations') && this.productMerchant.variations.length ? JSON.parse(JSON.stringify(this.productMerchant.variations)) : [],
					addresses : [
						{},
					],

	        	},

	        	productToPrint : {},

	        	errors : {
					stock : {
						// warehouse : {},
						variations : [],
						addresses : [],
						// product_serial : [],
					},
				},

				searchAttributes : {

	        		dates : {},
	        		search : '',
		        	dateTo : null,
		        	dateFrom : null,

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
					windowTitle: 'Stock Details' 

				},

	        	dataToExport: {

					"Invoice": 'invoice_no',

					"Product": {
						field: "stock_quantity",
						callback: (stock_quantity) => {
							
							return this.productMerchant.hasOwnProperty('product') ? this.$options.filters.capitalize(`${this.productMerchant.product.name}`) : 'NA';
							
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
							
							// return this.productMerchant.hasOwnProperty('merchant') ? `${this.productMerchant.merchant.first_name} ${this.productMerchant.merchant.last_name}` : 'NA';
							
							return this.productMerchant.hasOwnProperty('merchant') ? this.$options.filters.capitalize(this.productMerchant.merchant.user_name) : 'NA';
							
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
			
			this.fetchMerchantAllWarehouses();

			// this.fetchWarehouseAllContainers();

			this.fetchProductAllStocks();
			
			this.resetErrorObject();

			// this.configureProductErrorsWithPropData();

		},
		
		methods: {

			fetchProductAllStocks() {

				this.error = '';
				this.loading = true;
				this.allStocks = [];
				this.searchAttributes.search = '';
				
				axios
					.get('/api/product-stocks/' + this.productMerchant.id + '/' + this.perPage + "?page=" + this.pagination.current_page)
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
			/*
			fetchWarehouseAllContainers(warehouse) {
				
				this.search = '';
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
							this.resetWarehouseSpaces();
							
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
			fetchMerchantAllWarehouses() {
				
				this.error = '';
				this.loading = true;
				this.searchAttributes.search = '';
				this.allDealtEmptyWarehouses = [];

				axios
					.get('/api/dealt-warehouses/' + this.productMerchant.merchant.id)
					.then(response => {
						if (response.status == 200) {
							
							this.allDealtEmptyWarehouses = response.data.data;
					
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
			configureProductErrorsWithPropData() {

				// new error configuration
				this.errors = {
					stock : {
						variations : [],
						addresses : [],
						product_serial : [],
					},
				};

				if (this.singleStockData.addresses.length) {

					// if (this.singleStockData.addresses.length) {

						this.singleStockData.addresses.forEach(
							(productAddress, index) => {
								this.errors.stock.addresses.push({});
							}
						);

					// }
					
						else {

							this.product.addresses.forEach(
								(productAddress, index) => {
									this.errors.stock.addresses.push({});
								}
							);

						}
					

				}
				else {

					this.errors.stock.addresses = [
						{},
					];

				}

				if (this.product.category && this.product.has_variations && this.product.hasOwnProperty('variations') && this.product.variations.length) {
						
					this.product.variations.forEach(
						(productVariation, index) => {
							this.errors.stock.variations.push({});
						}
					);

				}

			},
		*/	
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
			showStockCreateForm() {

				this.step = 1;
				this.createMode = true;
	        	this.submitForm = true;
	        	this.formSubmitted = false;

				// singleStockData configuration
				this.singleStockData = {

	        		serials : [],
	        		// product_id : this.product.id,
	        		merchant_id : this.productMerchant.merchant_id,
	        		merchant_product_id : this.productMerchant.id,
	        		variations : this.productMerchant.hasOwnProperty('variations') && this.productMerchant.variations.length ? JSON.parse(JSON.stringify(this.productMerchant.variations)) : [],
	        		warehouse : this.singleStockData.warehouse ?? {},  // last warehouse
					addresses : this.singleStockData.addresses,  // last address / initial address

	        	};

	        	if (this.productMerchant.has_serials && this.productMerchant.has_variations && this.productMerchant.hasOwnProperty('variations') && this.productMerchant.variations.length) {

					this.$delete(this.singleStockData, 'serials');

					this.productMerchant.variations.forEach(
					
						(stockVariation, stockVariationIndex) => {								

							// this.singleStockData.variations[stockVariationIndex].serials = [];
							this.$set(this.singleStockData.variations[stockVariationIndex], 'serials', []);

						}

					);

				}

				// new errors configuration
				this.resetErrorObject();

				$('#stock-createOrEdit-modal').modal('show');
			},
			openStockEditForm(object) {

				this.step = 1;
	        	this.submitForm = true;
				this.createMode = false;
	        	this.formSubmitted = false;
				
				this.singleStockData = JSON.parse(JSON.stringify(object));

				this.singleStockData.primary_quantity = this.singleStockData.stock_quantity;

				if (this.singleStockData.hasOwnProperty('variations') && this.singleStockData.variations.length) {

					this.singleStockData.variations.forEach(
						(stockVariation, stockVariationIndex) => {
							stockVariation.primary_quantity = stockVariation.stock_quantity;
						}
					);

				}

				// this.singleStockData.product_id = this.product.id;

				this.resetErrorObject();

				this.setAvailableShelvesAndUnits();

				$('#stock-createOrEdit-modal').modal('show');
			},
			openStockDeleteForm(object) {
				this.singleStockData = object;
				$('#delete-confirmation-modal').modal('show');
			},
			storeStock() {
				
				if (! this.verifyUserInput()) {
					this.submitForm = false;
					return;
				}

				this.formSubmitted = true;

				axios
					.post('/product-stocks/' + this.perPage, this.singleStockData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Stock has been stored", "Success");

							this.pagination.current_page = 1; 
							this.searchAttributes.search !== '' ? this.searchData() : this.setAvailableContents(response);
							
							// this.printStockCode(this.singleStockData);		// as there is no stock code then

							$('#stock-createOrEdit-modal').modal('hide');
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
						this.fetchMerchantAllWarehouses();
						// this.fetchWarehouseAllContainers();
					});

			},
			updateStock() {
				
				if (!this.verifyUserInput()) {
					this.submitForm = false;
					return;
				}

				this.formSubmitted = true;

				axios
					.put('/product-stocks/' + this.singleStockData.id + '/' + this.perPage, this.singleStockData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Stock has been updated", "Success");
							
							this.pagination.current_page = 1; 
							this.searchAttributes.search !== '' ? this.searchData() : this.setAvailableContents(response);

							this.printStockCode(this.singleStockData);

							$('#stock-createOrEdit-modal').modal('hide');
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
						this.fetchMerchantAllWarehouses();
						// this.fetchWarehouseAllContainers();
					});

			},
			deleteStock(singleAssetData) {
				
				this.formSubmitted = true;

				axios
					.delete('/product-stocks/' + this.singleStockData.id + '/' + this.perPage, this.singleStockData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Stock has been deleted", "Success");
							
							this.pagination.current_page = 1; 
							this.searchAttributes.search !== '' ? this.searchData() : this.setAvailableContents(response);
							
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
						this.fetchMerchantAllWarehouses();
						// this.fetchWarehouseAllContainers();
					});

			},
			searchData() {

				this.error = '';
				this.allStocks = [];
				
				axios
				.post("/api/search-product-stocks/" + this.productMerchant.id + "/" + this.perPage + "?page=" + this.pagination.current_page, this.searchAttributes)
				.then(response => {
					this.allStocks = response.data.all.data;
					this.pagination = response.data.all;
				})
				.catch(e => {
					this.error = e.toString();
				});

			},
			verifyUserInput() {

				this.validateFormInput('product_address');
				this.validateFormInput('product_space_type');
				this.validateFormInput('product_container');
				this.validateFormInput('product_containers');
				this.validateFormInput('product_shelf');
				this.validateFormInput('product_shelves');
				this.validateFormInput('product_units');

				if (this.errors.stock.constructor === Object && Object.keys(this.errors.stock).length < 3 && !this.errorInVariationsArray(this.errors.stock.variations) && !this.errorInAddressesArray(this.errors.stock.addresses)) {

					return true;
				
				}

				return false;
		
			},
			errorInVariationsArray(array = []) {

				const variationError = (variation) => {

					return Object.keys(variation).length > 0

				}; 

				if (array.length) {
					return array.some(variationError);
				}

				return false;

			},
			errorInAddressesArray(array = []) {
				const addressError = (address) => {
	        							return Object.keys(address).length > 0
	        						}; 

				if (array.length) {
					return array.some(addressError);
				}

				return false;
			},
			nextPage() {
				
				if (this.step==1) {
					
					this.validateFormInput('warehouse');
					this.validateFormInput('product_stock_quantity');

					if (this.productMerchant.has_variations) {
						
						this.validateFormInput('product_variation_quantity');
						this.validateFormInput('product_variation_total_quantity');

					}

					if (this.errors.stock.constructor === Object && Object.keys(this.errors.stock).length < 3 && ! this.errorInVariationsArray(this.errors.stock.variations)) {

						this.step+=1;

						this.submitForm = true;
						this.resetWarehouseSpaces();
						// this.fetchWarehouseAllContainers(this.singleStockData.warehouse.id);
					
					}
					else {
					
						this.submitForm = false;
					
					}

				}
				else if (this.step == 2) {

					this.validateFormInput('manufacturing_date');
					this.validateFormInput('expiring_date');

					if (this.errors.stock.constructor === Object && Object.keys(this.errors.stock).length < 3 && ! this.errorInVariationsArray(this.errors.stock.variations)) {

						if (this.productMerchant.has_serials) {

							if (this.productMerchant.has_variations) {

								this.setProductVariationSerialObjects();

							}
							else {

								this.setProductSerialObjects();
								
							}							

							this.step += 1;

						}
						else {

							this.step += 2;

						}

					}

				}
				else if (this.step == 3) {

					if (this.productMerchant.has_serials) {

						this.validateFormInput('product_total_serials');

						if (this.productMerchant.has_variations) {
							
							this.validateFormInput('product_variation_serial');

						}
						else {
							
							this.validateFormInput('product_serial');

						}

					}

					if (this.errors.stock.constructor === Object && Object.keys(this.errors.stock).length < 3 && ! this.errorInVariationsArray(this.errors.stock.variations)) {

						this.step += 1;
						this.submitForm = true;

					}
					else {

						this.submitForm = false;
						
					}

				}

			},
			addMoreSpace() {
				if (this.singleStockData.addresses.length < 3) {

					this.singleStockData.addresses.push({});
					this.errors.stock.addresses.push({});

				}
			},
			removeSpace() {
					
				if (this.createMode && this.singleStockData.addresses.length > 1) {

					this.singleStockData.addresses.pop();
					this.errors.stock.addresses.pop();
				
				}
				else if (!this.createMode && this.singleStockData.addresses.length > 0) {

					this.singleStockData.addresses.pop();
					this.errors.stock.addresses.pop();

				}

				if (!this.errorInAddressesArray(this.errors.stock.addresses)) {
					this.submitForm = true;
				}
				
			},
			resetErrorObject() {

				// new errors initialization
				this.errors = {
					stock : {
						addresses : [],
						variations : [],
						// product_serial : [],
					},
				};

				if (this.singleStockData.addresses.length) {

					// if (this.singleStockData.addresses.length) {

						this.singleStockData.addresses.forEach(
							(productAddress, index) => {
								this.errors.stock.addresses.push({});
							}
						);

					// }
					/*
						else {

							this.product.addresses.forEach(
								(productAddress, index) => {
									this.errors.stock.addresses.push({});
								}
							);

						}
					*/
					
				}
				else {

					this.errors.stock.addresses = [
						{},
					];

				}

				if (this.productMerchant.has_variations && this.productMerchant.hasOwnProperty('variations') && this.productMerchant.variations.length) {
	
					this.productMerchant.variations.forEach(

						(productVariation, stockVariationIndex) => {

							this.errors.stock.variations.push({});
							
							/*
							if (this.productMerchant.has_serials) {

								// this.errors.stock.variations[stockVariationIndex].product_variation_serial = [];
								this.$set(this.errors.stock.variations[stockVariationIndex], 'product_variation_serial', []);
								// this.$delete(this.errors.stock, 'product_serial');

							}
							*/	

						}

					);
					

				}

				/*
				if (! this.productMerchant.has_serials || (this.productMerchant.has_variations && this.productMerchant.has_serials)) {

					this.$delete(this.errors.stock, 'product_serial');

				}
				*/

			},
			setAvailableShelvesAndUnits() {

				this.resetWarehouseSpaces();

				this.singleStockData.addresses.forEach(
					space => {

						if (space.type=='containers') {

						}
						else if (space.type=='shelves') {

							let searchedContainer = this.emptyShelfContainers.find(
								container => container.id==space.container.id && container.name==space.container.name && container.warehouse_container_id==space.container.warehouse_container_id
							)

							if (searchedContainer) {

								this.emptyShelves = searchedContainer.container_shelf_statuses;
							}
							
						}
						else if (space.type=='units') {

							const containerExists = container => container.id==space.container.id && container.name==space.container.name && container.warehouse_container_id==space.container.warehouse_container_id;

							if (this.emptyUnitContainers.some(containerExists)) {

								let searchedContainer = this.emptyUnitContainers.find(
									container => container.id==space.container.id && container.name==space.container.name && container.warehouse_container_id==space.container.warehouse_container_id
								);

								if (searchedContainer) {

									this.emptyUnitShelves = searchedContainer.container_shelf_statuses;

									let searchedShelf = searchedContainer.container_shelf_statuses.find(
										shelf => shelf.id==space.container.shelf.id && shelf.name==space.container.shelf.name && shelf.warehouse_container_id==space.container.shelf.warehouse_container_id &&  shelf.warehouse_container_status_id==space.container.shelf.warehouse_container_status_id 
									);

									if (searchedShelf) {

										this.emptyUnits = searchedShelf.container_shelf_unit_statuses;

									}

								}

							}

						}

					}
				);

			},
			setAvailableContents(response) {
				this.pagination = response.data;
				this.allStocks = response.data.data;
			},
			setAvailableShelves(index) {
				// console.log('container if has been triggered');
				if (this.singleStockData.addresses[index].container && Object.keys(this.singleStockData.addresses[index].container).length > 0) {
					this.$delete(this.singleStockData.addresses[index].container, 'shelves');
					this.emptyShelves = this.singleStockData.addresses[index].container.container_shelf_statuses;
				}
			},
			setAvailableUnitShelves(index) {
				// console.log('container if has been triggered');
				if (this.singleStockData.addresses[index].container && Object.keys(this.singleStockData.addresses[index].container).length > 0) {
					this.$delete(this.singleStockData.addresses[index].container, 'shelf');
					this.emptyUnitShelves = this.singleStockData.addresses[index].container.container_shelf_statuses;
				}
			},
			setAvailableUnits(index) {
				// console.log('shelf if has been triggered');
				if (this.singleStockData.addresses[index].container && Object.keys(this.singleStockData.addresses[index].container).length > 0 && Object.keys(this.singleStockData.addresses[index].container.shelf).length > 0) {
					this.$delete(this.singleStockData.addresses[index].container.shelf, 'units');
					this.emptyUnits = this.singleStockData.addresses[index].container.shelf.container_shelf_unit_statuses;
				}
			},
			setProductSpaceType(index) {
				// resetting
				this.$delete(this.singleStockData.addresses[index], 'container');
				this.$delete(this.singleStockData.addresses[index], 'containers');

				this.errors.stock.addresses[index] = {};

				this.resetAvailableSpaces();
		
			},
			resetWarehouseSpaces() {
				
				/*
					this.emptyContainers = [ ...this.allContainers.emptyContainers ];
					this.emptyShelfContainers = [ ...this.allContainers.emptyShelfContainers ];
					this.emptyUnitContainers = [ ...this.allContainers.emptyUnitContainers ];
				*/

				/*
					this.emptyContainers = JSON.parse( JSON.stringify( this.allContainers.emptyContainers ) );
					this.emptyShelfContainers = JSON.parse( JSON.stringify( this.allContainers.emptyShelfContainers ) );
					this.emptyUnitContainers = JSON.parse( JSON.stringify( this.allContainers.emptyUnitContainers ) );
				*/
			
				this.emptyContainers = [];
				this.emptyShelfContainers = [];
				this.emptyUnitContainers = [];
			
				if (this.singleStockData.warehouse && Object.keys(this.singleStockData.warehouse).length && this.allDealtEmptyWarehouses.length) {

					let selectedWarehouse = this.allDealtEmptyWarehouses.findIndex(
						dealtWarehouse => dealtWarehouse.id==this.singleStockData.warehouse.id && dealtWarehouse.name==this.singleStockData.warehouse.name
					);

					if (selectedWarehouse > -1) {

						this.emptyContainers = JSON.parse( JSON.stringify( this.allDealtEmptyWarehouses[selectedWarehouse].emptyContainers ) );
						this.emptyShelfContainers = JSON.parse( JSON.stringify( this.allDealtEmptyWarehouses[selectedWarehouse].emptyShelfContainers ) );
						this.emptyUnitContainers = JSON.parse( JSON.stringify( this.allDealtEmptyWarehouses[selectedWarehouse].emptyUnitContainers ) );

						this.emptyContainers.forEach(
							(emptyContainer, emptyContainerIndex) => {
								if (emptyContainer.hasOwnProperty('container_shelf_statuses') && emptyContainer.container_shelf_statuses.length) {

									! this.emptyShelfContainers.some(shelfContainer => shelfContainer.id==emptyContainer.id && shelfContainer.name==emptyContainer.name && shelfContainer.warehouse_container_id==emptyContainer.warehouse_container_id) ? this.emptyShelfContainers.push(emptyContainer) : '';

									if (emptyContainer.container_shelf_statuses[0].hasOwnProperty('container_shelf_unit_statuses') && emptyContainer.container_shelf_statuses[0].container_shelf_unit_statuses.length) {

										! this.emptyUnitContainers.some(unitContainer => unitContainer.id==emptyContainer.id && unitContainer.name==emptyContainer.name && unitContainer.warehouse_container_id==emptyContainer.warehouse_container_id) ? this.emptyUnitContainers.push(emptyContainer) : '';

									}

								}
							}
						);

						this.emptyShelfContainers.forEach(
							(emptyShelfContainer, emptyShelfContainerIndex) => {
								if (emptyShelfContainer.container_shelf_statuses[0] && emptyShelfContainer.container_shelf_statuses[0].hasOwnProperty('container_shelf_unit_statuses') && emptyShelfContainer.container_shelf_statuses[0].container_shelf_unit_statuses.length) {

									// ! this.emptyUnitContainers.some(unitContainer => unitContainer.id==emptyShelfContainer.id && unitContainer.name==emptyShelfContainer.name && unitContainer.warehouse_container_id==emptyShelfContainer.warehouse_container_id) ? this.emptyUnitContainers.push(emptyShelfContainer) : '';

									if (! this.emptyUnitContainers.some(unitContainer => unitContainer.id==emptyShelfContainer.id && unitContainer.name==emptyShelfContainer.name && unitContainer.warehouse_container_id==emptyShelfContainer.warehouse_container_id)) {

										this.emptyUnitContainers.push(emptyShelfContainer);

									}

								}
							}
						);	

					}

				}

			},
			resetAvailableSpaces() {

				this.resetWarehouseSpaces();

				this.singleStockData.addresses.forEach(

					(productAddress, index) => {
						
						if (productAddress.type=='containers' && productAddress.containers && productAddress.containers.length) {

							// for every selected container
							productAddress.containers.forEach(

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
									// containers with empty shelves
									var selectedContainerIndex = this.emptyShelfContainers.findIndex(
										(currentContainer) => 
											currentContainer.id == selectedContainer.id && currentContainer.name == selectedContainer.name && currentContainer.warehouse_container_id == selectedContainer.warehouse_container_id
										
									);

									// console.log('Container Index in emptyShelfContainers : ' + selectedContainerIndex);

									if (selectedContainerIndex > -1) {

										this.emptyShelfContainers.splice(selectedContainerIndex, 1);
									
									}


									// downward
									// containers with empty units
									var selectedContainerIndex = this.emptyUnitContainers.findIndex(
										(currentContainer) => 
											currentContainer.id == selectedContainer.id && currentContainer.name == selectedContainer.name && currentContainer.warehouse_container_id == selectedContainer.warehouse_container_id
										
									);

									// console.log('Container Index in emptyUnitContainers : ' + selectedContainerIndex);

									if (selectedContainerIndex > -1) {

										this.emptyUnitContainers.splice(selectedContainerIndex, 1);

									}

								}

							);

						}
						else if (productAddress.type=='shelves' && productAddress.container && productAddress.container.shelves && productAddress.container.shelves.length) {

							// same division
							// emptyShelfContainers
							this.emptyShelfContainers.forEach(

								(emptyShelfContainer) => {

									if (emptyShelfContainer.id == productAddress.container.id && emptyShelfContainer.name == productAddress.container.name && emptyShelfContainer.warehouse_container_id == productAddress.container.warehouse_container_id) {
										
										// for every selected shelves
										productAddress.container.shelves.forEach(

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

							// downward
							// for every emptyUnitContainers
							this.emptyUnitContainers.forEach(

								(emptyUnitContainer) => {

									if (emptyUnitContainer.id==productAddress.container.id && emptyUnitContainer.name==productAddress.container.name && emptyUnitContainer.warehouse_container_id==productAddress.container.warehouse_container_id) {

										
										// for every selected shelves
										productAddress.container.shelves.forEach(

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

							// upward
							// for every empty containers
							var selectedContainerIndex = this.emptyContainers.findIndex(
								(currentContainer) => 
									currentContainer.id == productAddress.container.id && currentContainer.name == productAddress.container.name && currentContainer.warehouse_container_id == productAddress.container.warehouse_container_id
							);

							if (selectedContainerIndex > -1) {

								this.emptyContainers.splice(selectedContainerIndex, 1);

							}

						}
						else if (productAddress.type=='units' && productAddress.container && productAddress.container.shelf && productAddress.container.shelf.units && productAddress.container.shelf.units.length) {

							// same division
							// emptyUnitContainers
							this.emptyUnitContainers.forEach(

								(emptyUnitContainer) => {

									if (emptyUnitContainer.id==productAddress.container.id && emptyUnitContainer.name==productAddress.container.name && emptyUnitContainer.warehouse_container_id==productAddress.container.warehouse_container_id) {

										var selectedShelfIndex = emptyUnitContainer.container_shelf_statuses.findIndex(
											(containerShelf) => 
												containerShelf.id == productAddress.container.shelf.id && containerShelf.name == productAddress.container.shelf.name && containerShelf.warehouse_container_status_id == productAddress.container.shelf.warehouse_container_status_id
										);

										if(selectedShelfIndex > -1) {
											
											// for every selected units
											productAddress.container.shelf.units.forEach(

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
							// for every empty containers
							var selectedContainerIndex = this.emptyContainers.findIndex(
								(currentContainer) => 
									currentContainer.id == productAddress.container.id && currentContainer.name == productAddress.container.name && currentContainer.warehouse_container_id == productAddress.container.warehouse_container_id
							);

							if (selectedContainerIndex > -1) {

								this.emptyContainers.splice(selectedContainerIndex, 1);

							}

							// upward
							// for containers with empty shelves
							this.emptyShelfContainers.forEach(

								(emptyShelfContainer) => {

									if (emptyShelfContainer.id == productAddress.container.id && emptyShelfContainer.name == productAddress.container.name && emptyShelfContainer.warehouse_container_id == productAddress.container.warehouse_container_id) {


										var selectedShelfIndex = emptyShelfContainer.container_shelf_statuses.findIndex(
											(currentShelf) => currentShelf.id == productAddress.container.shelf.id && currentShelf.name == productAddress.container.shelf.name && currentShelf.warehouse_container_id == productAddress.container.shelf.warehouse_container_id
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
			},
			addProductSerial() {

				this.validateFormInput('product_serial');

				if (! this.errors.stock.product_serial && this.singleStockData.serials.some(productSerial=>! productSerial.serial_no || productSerial.serial_no == '')) {

					let emptyIndex = this.singleStockData.serials.findIndex(productSerial=> ! productSerial.serial_no || productSerial.serial_no == '');

					if (emptyIndex > -1) {

						this.singleStockData.serials[emptyIndex].serial_no = this.productNewSerial;
						
						this.productNewSerial = '';

					}
				
				}

			},
			addVariationSerial(stockedVariationIndex) {

				this.validateFormInput('product_variation_serial');

				if (! this.errors.stock.variations[stockedVariationIndex].product_variation_serial && this.singleStockData.variations[stockedVariationIndex].serials.some(variationSerial => ! variationSerial.serial_no || variationSerial.serial_no == '')) {

					let emptyIndex = this.singleStockData.variations[stockedVariationIndex].serials.findIndex(variationSerial => ! variationSerial.serial_no || variationSerial.serial_no == '');				

					if (emptyIndex > -1) {

						// this.singleStockData.variations[stockedVariationIndex].serials[emptyIndex].serial_no = this.variationNewSerial[stockedVariationIndex];
						
						this.$set(this.singleStockData.variations[stockedVariationIndex].serials[emptyIndex], 'serial_no', this.variationNewSerial[stockedVariationIndex]);

						this.variationNewSerial[stockedVariationIndex] = '';

					}
				
				}

			},
			removeVariationSerial(stockedVariationIndex, productVariationSerialIndex) {

				if (this.singleStockData.variations.length > stockedVariationIndex && this.singleStockData.variations[stockedVariationIndex].serials.length > productVariationSerialIndex) {

					this.$delete(this.singleStockData.variations[stockedVariationIndex].serials[productVariationSerialIndex], 'serial_no');

				}

			},
			removeProductSerial(productSerialIndex) {

				if (this.singleStockData.serials.length > productSerialIndex) {

					this.$delete(this.singleStockData.serials[productSerialIndex], 'serial_no');

				}

			},
			setProductSerialObjects() {

				if (this.singleStockData.stock_quantity > 0 && this.singleStockData.serials.length < this.singleStockData.stock_quantity) {
					
					let difference = this.singleStockData.stock_quantity - this.singleStockData.serials.length;

					for (let i = 0; i < difference; i++) {
				  		this.singleStockData.serials.push({});
					}

				}
				else if (this.singleStockData.stock_quantity > 0 && this.singleStockData.serials.length > this.singleStockData.stock_quantity) {

					this.singleStockData.serials.splice(this.singleStockData.stock_quantity, );
					// this.errors.stock.product_serial.splice(this.singleStockData.stock_quantity, );

				}

			},
			setProductVariationSerialObjects() {

				this.singleStockData.variations.forEach(
					
					(productVariation, index) => {

						if (productVariation.stock_quantity > 0) {

							if (productVariation.serials.length < productVariation.stock_quantity) {

								let difference = productVariation.stock_quantity - productVariation.serials.length;

								for (let i = 0; i < difference; i++) {
							  		productVariation.serials.push({});
								}

							}
							else if (productVariation.serials.length > productVariation.stock_quantity) {

								productVariation.serials.splice(productVariation.stock_quantity, );
								// this.errors.stock.variations[index].product_variation_serial.splice(productVariation.stock_quantity, );

							}
							

						}

					}
					
				);

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
    		objectNameWithCapitalized ({ name, variation }) {
		      	
		      	if (name) {
				    name = name.toString()
				    
				    const words = name.split(" ");

					for (let i = 0; i < words.length; i++) {
					    words[i] = words[i][0].toUpperCase() + words[i].substr(1);
					}

					return words.join(" ");
		      	}
		      	else if (variation) {
		      		var variation_name = variation.name.toString();
		      		
		      		if (variation.hasOwnProperty('sub_variation') && variation.sub_variation.hasOwnProperty('name')) {

		      			variation_name = variation_name + '-' + variation.sub_variation.name

		      		}

				    return variation_name.charAt(0).toUpperCase() + variation_name.slice(1)
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
		    printInvoice() {

				// this.printingStyles.name = `${ this.singleStockData.subject } Details`;
				this.printingStyles.windowTitle = this.$options.filters.capitalize(`${ this.productMerchant.product.name } - ${ this.productMerchant.merchant.user_name } Stock Details`);

				JsBarcode(".barcode").init();

				this.$htmlToPaper('sectionToPrint', this.printingStyles);

				/*
				JsBarcode("#print-stock-code", this.singleStockData.stock_code, 
					{
						width:1, 
						height:25
					}
				);
				*/

				$('#stock-view-modal').modal('hide');

			},
			printStockCode(productToPrint = {}) {

				// this.printingStyles.name = `${ this.singleStockData.subject } Details`;
				this.printingStyles.windowTitle = this.$options.filters.capitalize(`${ this.productMerchant.product.name } - ${ this.productMerchant.merchant.user_name } Stock Details`);

				this.$set(this, 'productToPrint', productToPrint);
				// this.productToPrint = productToPrint;

				this.$nextTick(function () {

					JsBarcode(".barcode").init();
					
					this.$htmlToPaper('productCodeToPrint', this.printingStyles);

				})


				/*
				JsBarcode("#print-stock-code", this.singleStockData.stock_code, 
					{
						width:1, 
						height:25
					}
				);
				*/

				// $('#stock-view-modal').modal('hide');

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
            showPreview(imagePath = 'default') {
				
				if (! imagePath || imagePath == '') {
					return '/' + this.product.preview;
				}
				else if (imagePath == 'default') {
					return '/' + this.productMerchant.preview;
				}
				else {
					return '/' + imagePath || '';
				}

				// return '';

			},
			validateFormInput (formInputName) {

				this.submitForm = false;

				switch(formInputName) {

					case 'product_stock_quantity' :

						if (!this.singleStockData.stock_quantity || this.singleStockData.stock_quantity < 1) {
							this.errors.stock.product_stock_quantity = 'Stock quantity is required';
						}
						else if(! this.createMode && ((this.singleStockData.primary_quantity - this.singleStockData.stock_quantity) > this.allStocks[this.allStocks.length-1].available_quantity)){

							this.errors.stock.product_stock_quantity = 'Stock quantity is less than minimum ' + (this.singleStockData.primary_quantity - this.allStocks[this.allStocks.length-1].available_quantity);
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.stock, 'product_stock_quantity');
						}

						break;

					case 'product_variation_quantity' :

						if (this.productMerchant.has_variations) {

							this.singleStockData.variations.forEach(
								(productVariation, index) => {

									if (productVariation.stock_quantity < 0) {
										this.errors.stock.variations[index].product_variation_quantity = 'Variation quantity is invalid';
									}
									else if(! this.createMode && this.allStocks[this.allStocks.length-1].variations.length > index && ((productVariation.primary_quantity - productVariation.stock_quantity) > this.allStocks[this.allStocks.length-1].variations[index].available_quantity)){

										this.errors.stock.variations[index].product_variation_quantity = 'Variation quantity is less than minimum ' + (productVariation.primary_quantity-this.allStocks[this.allStocks.length-1].variations[index].available_quantity);
									}
									else {
										this.$delete(this.errors.stock.variations[index], 'product_variation_quantity');
									}

								}
								
							);
								
							if (!this.errorInVariationsArray(this.errors.stock.variations)) {
								this.submitForm = true;
							}
							
						}
						else {
							this.submitForm = true;
							this.errors.stock.variations = [];
						}

						break;
					
					
					case 'product_variation_total_quantity' :

						if (this.productMerchant.has_variations) {

							if (!this.errorInVariationsArray(this.errors.stock.variations)) {

								let variationTotalQuantity = this.singleStockData.variations.reduce(
									(value, currentObject) => {
										return value + (currentObject.stock_quantity || 0);
									}, 
								0);

								if (variationTotalQuantity !== this.singleStockData.stock_quantity) {
									this.errors.stock.variations[this.singleStockData.variations.length-1].product_variation_quantity = 'Total variation qty should be equal to qty';
								}
								else {
									this.$delete(this.errors.stock.variations[this.singleStockData.variations.length-1], 'product_variation_quantity');
								}
							}

						}
						else {
							this.submitForm = true;
							this.errors.stock.variations = [];
						}

						break;

					case 'product_total_serials' :

						if (this.productMerchant.has_variations) {

							this.singleStockData.variations.forEach(
								(stockVariation, stockVariationIndex) => {

									if (stockVariation.stock_quantity > 0 && stockVariation.stock_quantity != stockVariation.serials.length) {

										this.errors.stock.variations[stockVariationIndex].product_total_serials = 'Serials are more or less than quantity';

									}
									else {

										this.$delete(this.errors.stock.variations[stockVariationIndex], 'product_total_serials');

									}

								}
								
							);

							if (!this.errorInVariationsArray(this.errors.stock.variations)) {
								this.submitForm = true;
							}
							
						}
						else {
							
							// this.submitForm = true;
							// this.errors.stock.variations = [];

							if (! this.productMerchant.has_variations && this.singleStockData.stock_quantity > 0 && this.singleStockData.stock_quantity != this.singleStockData.serials.length) {

								this.errors.stock.product_total_serials = 'Serials are more or less than quantity';

							}
							else {

								this.submitForm = true;
								this.$delete(this.errors.stock, 'product_total_serials');

							}

						}

						break;
				
					case 'product_address' : 

						if (this.createMode && this.singleStockData.addresses.length < 1) {
							this.submitForm = false;
							this.errors.stock.product_address = 'Address is required';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.stock, 'product_address');
						}

						break;


					case 'product_space_type' :

						this.singleStockData.addresses.forEach(
							
							(productSpace, index) => {

								if (!productSpace.type) {
									this.errors.stock.addresses[index].product_space_type = 'Space type is required';
								}
							/*
								else if (this.singleStockData.addresses.filter((obj) => obj.type === productSpace.type).length > 1) {

									this.errors.stock.addresses[index].product_space_type = 'Same type selected';
								}
							*/
								else {
									this.$delete(this.errors.stock.addresses[index], 'product_space_type');
								}

							}

						);
						
						if (!this.errorInAddressesArray(this.errors.stock.addresses)) {
							this.submitForm = true;
						}

						break;

					case 'product_containers' :

						this.singleStockData.addresses.forEach(
							
							(productSpace, index) => {

								if (productSpace.type=='containers' && (!productSpace.containers || productSpace.containers.length == 0)) {
									this.errors.stock.addresses[index].product_containers = 'Container is required';
								}
								else{
									this.$delete(this.errors.stock.addresses[index], 'product_containers');
								}

							}

						);

						if (!this.errorInAddressesArray(this.errors.stock.addresses)) {
							this.submitForm = true;
						}

						break;

					case 'product_container' :

						this.singleStockData.addresses.forEach(
							
							(productSpace, index) => {

								if ((productSpace.type=='shelves' || productSpace.type=='units') && (!productSpace.container || Object.keys(productSpace.container).length==0)) {
									this.errors.stock.addresses[index].product_container = 'Container is required';
								}
								else{
									this.$delete(this.errors.stock.addresses[index], 'product_container');
								}

							}
						);

						if (!this.errorInAddressesArray(this.errors.stock.addresses)) {
							this.submitForm = true;
						}

						break;

					case 'product_shelves' : 

						this.singleStockData.addresses.forEach(
							
							(productSpace, index) => {

								if (productSpace.type=='shelves' && (!productSpace.container || !productSpace.container.shelves || productSpace.container.shelves.length == 0)) {
									this.errors.stock.addresses[index].product_shelves = 'Shelf is required';
								}
								else{
									this.$delete(this.errors.stock.addresses[index], 'product_shelves');
								}

							}
						);

						if (!this.errorInAddressesArray(this.errors.stock.addresses)) {
							this.submitForm = true;
						}

						break;

					case 'product_shelf' :

						this.singleStockData.addresses.forEach(
							
							(productSpace, index) => {

								if (productSpace.type=='units' && (!productSpace.container || !productSpace.container.shelf || Object.keys(productSpace.container.shelf).length==0)) {
									this.errors.stock.addresses[index].product_shelf = 'Shelf is required';
								}
								else{
									this.$delete(this.errors.stock.addresses[index], 'product_shelf');
								}

							}
						);

						if (!this.errorInAddressesArray(this.errors.stock.addresses)) {
							this.submitForm = true;
						}

						break;


					case 'product_units' :

						this.singleStockData.addresses.forEach(
							
							(productSpace, index) => {

								if (productSpace.type=='units' && (!productSpace.container || !productSpace.container.shelf || !productSpace.container.shelf.units || productSpace.container.shelf.units.length == 0)) {
									this.errors.stock.addresses[index].product_units = 'Unit is required';
								}
								else{
									this.$delete(this.errors.stock.addresses[index], 'product_units');
								}

							}
						);

						if (!this.errorInAddressesArray(this.errors.stock.addresses)) {
							this.submitForm = true;
						}

						break;

					case 'product_serial' :

						if (this.productMerchant.has_serials && ! this.productMerchant.has_variations) {
							
							if (this.singleStockData.serials.some(productSerial=>! productSerial.serial_no) && (! this.productNewSerial || this.productNewSerial == '')) {

								this.errors.stock.product_serial ? this.errors.stock.product_serial = 'Product serial is required' : this.$set(this.errors.stock, 'product_serial', 'Product serial is required');
							
							}
							else if (this.productNewSerial && this.productNewSerial.length < 4) {

								this.errors.stock.product_serial ? this.errors.stock.product_serial = 'Product serial length should be minimum 4' : this.$set(this.errors.stock, 'product_serial', 'Product serial length should be minimum 4');

							}
							else if (this.productNewSerial && ! this.productNewSerial.match(/^[a-zA-Z0-9-_]+$/)) {

								this.errors.stock.product_serial ? this.errors.stock.product_serial = 'Invalide serial number' : this.$set(this.errors.stock, 'product_serial', 'Invalide serial number');

							}
							else if (this.singleStockData.serials.some((productSerial) => productSerial.serial_no == this.productNewSerial)) {

								this.errors.stock.product_serial ? this.errors.stock.product_serial = 'Duplicate serial number' : this.$set(this.errors.stock, 'product_serial', 'Duplicate serial number');

							}
							else {

								// this.errors.stock.product_serial[i] = null;
								// this.$set(this.errors.stock.product_serial, i, null);
								this.$delete(this.errors.stock, 'product_serial');

							}

							if (Object.keys(this.errors.stock).length < 3) {

								this.submitForm = true;

							}

						}
						else {

							this.submitForm = true;
							// this.errors.stock.product_serial = [];
							this.$delete(this.errors.stock, 'product_serial');
						
						}

						break;
					
					case 'product_variation_serial' :

						if (this.productMerchant.has_serials && this.productMerchant.has_variations && this.singleStockData.hasOwnProperty('variations') && this.singleStockData.variations.length) {
							
							this.singleStockData.variations.forEach(
							
								(stockVariation, stockVariationIndex) => {

									if (stockVariation.stock_quantity > 0) {

										if (stockVariation.serials.some(variationSerial=>! variationSerial.serial_no || variationSerial.serial_no == '') && (! this.variationNewSerial[stockVariationIndex] || this.variationNewSerial[stockVariationIndex].length < 4)) {

											// this.errors.stock.variations[stockVariationIndex].product_variation_serial[i] = 'Variation serial is required';

											// this.$set(this.errors.stock.variations[stockVariationIndex].product_variation_serial, stockVariationIndex, 'Variation serial is required');

											this.errors.stock.variations[stockVariationIndex].product_variation_serial ? this.errors.stock.variations[stockVariationIndex].product_variation_serial = 'Variation serial is required' : this.$set(this.errors.stock.variations[stockVariationIndex], 'product_variation_serial', 'Variation serial is required');

											// this.$set(this.errors.stock.variations[stockVariationIndex].product_variation_serial, 'Variation serial is required');

										}
										else if (this.variationNewSerial[stockVariationIndex] && ! this.variationNewSerial[stockVariationIndex].match(/^[a-zA-Z0-9-_]+$/)) {

											// this.errors.stock.variations[stockVariationIndex].product_variation_serial[i] = 'Invalid serial number';

											// this.$set(this.errors.stock.variations[stockVariationIndex].product_variation_serial, stockVariationIndex, 'Invalid serial number');

											this.errors.stock.variations[stockVariationIndex].product_variation_serial ? this.errors.stock.variations[stockVariationIndex].product_variation_serial = 'Invalid serial number' : this.$set(this.errors.stock.variations[stockVariationIndex], 'product_variation_serial', 'Invalid serial number');

											// this.$set(this.errors.stock.variations[stockVariationIndex].product_variation_serial, 'Invalid serial number');

										}
										else if (this.variationNewSerial[stockVariationIndex] && stockVariation.serials.some((variationSerial) => variationSerial.serial_no == this.variationNewSerial[stockVariationIndex])) {

											// this.$set(this.errors.stock.variations[stockVariationIndex].product_variation_serial, stockVariationIndex, 'Duplicate serial number');
											
											this.errors.stock.variations[stockVariationIndex].product_variation_serial ? this.errors.stock.variations[stockVariationIndex].product_variation_serial = 'Duplicate serial number' : this.$set(this.errors.stock.variations[stockVariationIndex], 'product_variation_serial', 'Duplicate serial number');
											
											// this.$set(this.errors.stock.variations[stockVariationIndex].product_variation_serial, 'Duplicate serial number');

										}
										else {

											// this.errors.stock.variations[stockVariationIndex].product_variation_serial[stockVariationIndex] = null;

											this.$delete(this.errors.stock.variations[stockVariationIndex], 'product_variation_serial');

										}
										
									}
									else {

										// this.errors.stock.variations = [];
										this.$delete(this.errors.stock.variations[stockVariationIndex], 'product_variation_serial');

									}

								}

							);

							if (! this.errorInVariationsArray(this.errors.stock.variations)) {

								this.submitForm = true;

							}

						}
						else {

							this.submitForm = true;

							this.singleStockData.variations.forEach(
							
								(stockVariation, stockVariationIndex) => {

									// this.errors.stock.variations[stockVariationIndex].product_variation_serial = [];
									this.$delete(this.errors.stock.variations[stockVariationIndex], 'product_variation_serial');

								}

							);
						
						}

						break;	

					case 'warehouse' : 

						if (! this.singleStockData.warehouse || ! Object.keys(this.singleStockData.warehouse).length) {
							this.submitForm = false;
							this.errors.stock.warehouse = 'Warehouse is required';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.stock, 'warehouse');
						}

						break;

					case 'manufacturing_date' : 

						if (! this.productMerchant.has_variations && this.productMerchant.product && this.productMerchant.product.category && this.productMerchant.product.category.is_perishable && ! this.singleStockData.manufactured_at) {
							
							this.errors.stock.manufacturing_date = 'Mfg. date is required';

						}
						else if (this.productMerchant.has_variations && this.productMerchant.product && this.productMerchant.product.category && this.productMerchant.product.category.is_perishable) {

							this.singleStockData.variations.forEach(
							
								(stockVariation, stockVariationIndex) => {

									if (stockVariation.stock_quantity > 0 && ! stockVariation.manufactured_at) {

										this.errors.stock.variations[stockVariationIndex].manufacturing_date = 'Mfg. date is required';

									}
									else {

										this.$delete(this.errors.stock.variations[stockVariationIndex], 'manufacturing_date');

									}

								}

							);

							if (! this.errorInVariationsArray(this.errors.stock.variations)) {

								this.submitForm = true;

							}

						}
						else {
							
							this.submitForm = true;
							this.$delete(this.errors.stock, 'manufacturing_date');

						}

						break;

					case 'expiring_date' : 

						if (! this.productMerchant.has_variations && this.productMerchant.product && this.productMerchant.product.category && this.productMerchant.product.category.is_perishable && ! this.singleStockData.expired_at) {

							this.errors.stock.expiring_date = 'Exp. date is required';

						}
						else if (this.productMerchant.has_variations && this.productMerchant.product && this.productMerchant.product.category && this.productMerchant.product.category.is_perishable) {

							this.singleStockData.variations.forEach(
							
								(stockVariation, stockVariationIndex) => {

									if (stockVariation.stock_quantity > 0 && ! stockVariation.expired_at) {

										this.errors.stock.variations[stockVariationIndex].expiring_date = 'Exp. date is required';

									}
									else {

										this.$delete(this.errors.stock.variations[stockVariationIndex], 'expiring_date');

									}

								}

							);

							if (! this.errorInVariationsArray(this.errors.stock.variations)) {

								this.submitForm = true;

							}

						}
						else {
							
							this.submitForm = true;
							this.$delete(this.errors.stock, 'expiring_date');
							
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
</style>