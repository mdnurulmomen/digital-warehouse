
<template v-if="userHasPermissionTo('view-product-stock-index')">
	<div class="pcoded-content">
		<breadcrumb 
			:icon="'stocks'"
			:title="'stocks'" 
			:message="'All our stocks'"
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
																	worksheet="Stocks sheet"
																	:name="((searchAttributes.search != '' || searchAttributes.dateFrom || searchAttributes.dateTo) ? 'searched-stocks-' : ('stocks-list-')) + currentTime + '-page-' + pagination.current_page + '.xls'"
													  			>
													  				Excel
																</download-excel>
										  						
										  						<!-- 
										  						<download-excel 
										  							type="csv"
													  				class="btn waves-effect waves-dark btn-default btn-outline-default p-1 dropdown-item disabled"
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
													  			class="btn waves-effect waves-dark btn-success btn-outline-success btn-sm" 
													  			v-tooltip.bottom-end="'Create New'" 
											  					v-if="userHasPermissionTo('create-product-stock')"
													  			@click="showStockCreateForm()" 
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
												  			class="btn waves-effect waves-dark btn-success btn-outline-success btn-sm" 
												  			v-tooltip.bottom-end="'Create New'" 
										  					v-if="userHasPermissionTo('create-product-stock')"
												  			@click="showStockCreateForm()" 
											  			>
											  				<i class="fa fa-plus"></i>
											  				New Stock
											  			</button>
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
																		<th>Invoice</th>
																		<th>Keeper</th>
																		<th>Approval</th>
																		<th>Warehouse</th>
																		<th>Actions</th>
																	</tr>
																</thead>
																<tbody>
																	<tr 
																		v-for="(stock, stockIndex) in allStocks" 
																		:key="'stock-index-' + stockIndex + '-stock-' + stock.id" 
																		:class="stock.id==singleStockData.id ? 'highlighted' : ''"
																	>
																		<td>
																			{{ stock.created_at }}
																		</td>

																		<td>
																			{{ stock.invoice_no }}
																		</td>

																		<td>
																			{{ getFullName(stock.keeper) }}
																		</td>

																		<td>
																			<span :class="[stock.has_approval==1 ? 'badge-success' : stock.has_approval==-1 ? 'badge-secondary' : 'badge-danger', 'badge']">
																				{{ stock.has_approval==1 ? 'Approved' : stock.has_approval==-1 ? 'Cancelled' : stock.has_approval==0 ? 'Pending' : 'NA' }}
																			</span>
																		</td>

																		<td>
																			{{ stock.warehouse ? stock.warehouse.name : 'NA' }}
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
																			
																			<!-- Approve -->
																			<button 
																				type="button" 
																				class="btn waves-effect waves-dark btn-warning btn-outline-warning btn-icon" 
																				v-tooltip.bottom-end="'Approve Stock'"  
																				@click="openStockEditForm(stock)" 
																				v-if="! stock.has_approval && userHasPermissionTo('update-product-stock')"
																			>
																				<i class="fa fa-check"></i>
																			</button>

																			<button 
																				type="button" 
																				class="btn waves-effect waves-dark btn-primary btn-outline-primary btn-icon" 
																				v-tooltip.bottom-end="'Edit'"  
																				@click="openStockEditForm(stock)" 
																				v-if="stock.has_approval==1 && userHasPermissionTo('update-product-stock')"
																			>
																				<i class="fa fa-edit"></i>
																			</button>

																			<button 
																				type="button" 
																				class="btn waves-effect waves-dark btn-danger btn-outline-danger btn-icon" 
																				v-tooltip.bottom-end="'Delete'" 
																				:disabled="formSubmitted || stock.products.some(stockedProduct=>stockedProduct.stock_quantity > stockedProduct.available_quantity) || (stock.hasOwnProperty('variations') && stock.variations.some(stockedVariation => stockedVariation.available_quantity < stockedVariation.stock_quantity))"  
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
															    		<td colspan="6">
																      		<div class="alert alert-danger text-center" role="alert">
																      			Sorry, No data found.
																      		</div>
																    	</td>
																  	</tr>
																</tbody>
																<tfoot>
																	<tr>	
																		<th>Date</th>
																		<th>Invoice</th>
																		<th>Keeper</th>
																		<th>Approval</th>
																		<th>Warehouse</th>
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
																@click="pagination.current_page = 1; searchAttributes.search === '' ? fetchAllStocks() : searchData()"
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
																@paginate="searchAttributes.search === '' ? fetchAllStocks() : searchData()"
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
		<div class="modal fade" id="stock-createOrEdit-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" v-if="userHasPermissionTo('create-product-stock') || userHasPermissionTo('approve-product-stock') || userHasPermissionTo('update-product-stock')">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">
							{{ createMode ? 'Create New Stock' : singleStockData.has_approval==1 ? 'Update Stock' : 'Approve Stock' }}
						</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
						
					<form 	
						class="form-horizontal" 
						@submit.prevent="createMode ? storeStock() : updateStock()" 
						autocomplete="off" 
						novalidate="true" 
					>
						<input type="hidden" name="_token" :value="csrf">

						<div class="modal-body">

							<transition-group name="fade">  		
								<div 
									class="row" 
									v-bind:key="'product-modal-step-' + 1" 
									v-show="! loading && step==1"
								>
									<h2 class="mx-auto mb-4 lead">Stock Profile</h2>

									<div class="col-md-12">
										<div class="form-row">
 											<div class="col-sm-12 form-group">
												<label class="col-form-label font-weight-bold">
													Select Merchant :
												</label>
												
												<multiselect 
			                              			v-model="singleStockData.merchant"
			                                  		:options="allMerchants" 
			                                  		:custom-label="objectNameWithCapitalized" 
			                                  		:required="true" 
			                                  		:allow-empty="false" 
			                                  		label="name" 
			                                  		track-by="id" 
			                              			placeholder="Select Merchant" 
			                              			class="form-control p-0" 
			                                  		:class="! errors.merchant  ? 'is-valid' : 'is-invalid'"  
			                                  		@close="validateFormInput('merchant')" 
			                              		>
			                                	</multiselect>

			                                	<div class="invalid-feedback">
											    	{{ errors.merchant }}
											    </div>
 											</div>

 											<div class="col-sm-12 form-group">
												<label class="col-form-label font-weight-bold">
													Select Warehouse :
												</label>
												
												<multiselect 
			                              			v-model="singleStockData.warehouse"
			                                  		:options="allWarehouses" 
			                                  		:custom-label="objectNameWithCapitalized" 
			                                  		:required="true" 
			                                  		:allow-empty="false" 
			                                  		label="name" 
			                                  		track-by="id" 
			                              			placeholder="Select Warehouse" 
			                              			class="form-control p-0" 
			                                  		:class="! errors.warehouse  ? 'is-valid' : 'is-invalid'"  
			                                  		@close="validateFormInput('warehouse')"  
			                              		>
			                                	</multiselect>
			                                	
			                                	<div class="invalid-feedback">
											    	{{ errors.warehouse }}
											    </div>
 											</div>

 											<div class="col-sm-12 form-group">
 												<div class="form-row">
													<label class="form-group col-md-4">
														Stock Date :
													</label>

													<div class="form-group col-md-8">
														<p v-show="singleStockData.created_at">
															{{ singleStockData.created_at }}
														</p>

														<v-date-picker 
															v-model="singleStockData.created_at" 
															color="red" 
															is-dark
															is-inline
															:max-date="new Date()" 
															:model-config="{ type: 'string', mask: 'YYYY-MM-DD' }" 
															:attributes="[ { key: 'today', dot: true, dates: new Date() } ]" 
														/>
													</div>
												</div>
 											</div>

 											<!-- 
 											<div 
 												class="col-sm-12 form-group"
 												v-show="singleStockData.has_approval"
 											>
												<label class="col-form-label font-weight-bold">
													Stocked Date : 
												</label>
												
												<label class="col-form-label">
													{{ singleStockData.created_at }}
												</label>
 											</div> 
 											-->

 											<div 
	 											class="col-sm-12 form-group" 
	 											v-show="userHasPermissionTo('update-product-stock')"
 											>
 												<div class="form-row">
													<label class="form-group col-md-4">
														Approval Date :
													</label>

													<div class="form-group col-md-8">
														<p v-show="singleStockData.updated_at">
															{{ singleStockData.updated_at }}
														</p>

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
 											</div>
										</div>

										<div class="form-row card-footer">
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
								    v-bind:key="'product-modal-step-' + 2" 
								    v-show="step==2"
							    >
							    	<h2 class="mx-auto mb-4 lead">
							    		{{ singleStockData.merchant ? singleStockData.merchant.user_name : 'Merchant ' | capitalize }} Products
							    	</h2>

							    	<div 
							    		class="col-sm-12" 
							    		v-if="singleStockData.products.length == errors.products.length"
							    	>
							    		<div id="product-accordion">
								    		<div 
								    			class="card" 
								    			v-for="(stockedProduct, stockedProductIndex) in singleStockData.products" 
												:key="'stocked-product-index-' + stockedProductIndex + '-product-' + stockedProduct.id"
								    		>
												<div 
													class="card-header" 
													:id="'heading-stocking-product-index-' + stockedProductIndex + '-product-' + stockedProduct.id"
												>
													<p class="mb-0">
														<button type="button" class="btn btn-link pl-0" data-toggle="collapse" :data-target="'#stocked-product-index-' + stockedProductIndex + '-product-' + stockedProduct.id" aria-expanded="true" :aria-controls="'stocked-product-index-' + stockedProductIndex + '-product-' + stockedProduct.id">
															{{ (stockedProduct.merchant_product && stockedProduct.merchant_product.product) ? stockedProduct.merchant_product.product.name : 'Product Name' }} ({{ stockedProduct.merchant_product && stockedProduct.merchant_product.manufacturer ? stockedProduct.merchant_product.manufacturer.name : 'Own Product' }})
														</button>
													</p>
												</div>

												<div :id="'stocked-product-index-' + stockedProductIndex + '-product-' + stockedProduct.id" class="collapse show" :aria-labelledby="'heading-stocking-product-index-' + stockedProductIndex + '-product-' + stockedProduct.id" data-parent="#product-accordion">
													<div class="card-body">
														<div class="form-row">
															<div class="col-md-12 form-group text-center">
																<img 
																	:src="stockedProduct.merchant_product ? showPreview(stockedProduct.merchant_product.preview, stockedProduct.merchant_product.product.preview) : ''" 
																	class="img-fluid" 
																	alt="Product Preview" 
																	width="100px"
																>
															</div>

															<div class="col-md-12">
																<div class="row">
																	<div class="col-md-4 form-group">
																		<label class="col-form-label font-weight-bold">
																			Select Product :
																		</label>

																		<multiselect 
									                              			v-model="stockedProduct.merchant_product"
									                                  		:options="allProducts" 
									                                  		:custom-label="objectNameWithCapitalized" 
									                                  		:required="true" 
									                                  		:allow-empty="false" 
									                                  		label="name" 
									                                  		track-by="id" 
									                              			placeholder="Select Product" 
									                              			class="form-control p-0" 
									                                  		:class="! errors.products[stockedProductIndex].product ? 'is-valid' : 'is-invalid'" 
									                                  		:disabled="Boolean(! createMode && stockedProduct.stock_quantity && stockedProduct.hasOwnProperty('available_quantity') && stockedProduct.stock_quantity != stockedProduct.available_quantity)" 
									                                  		@input="validateFormInput('product'); resetProductProperties(stockedProductIndex)" 
									                              		>
									                                	</multiselect>

																		<div class="invalid-feedback">
																        	{{ errors.products[stockedProductIndex].product }}
																  		</div>
																	</div>

																	<div class="col-md-4 form-group">
																		<label class="col-form-label font-weight-bold">
																			Stock Qty :
																		</label>

																		<div class="input-group mb-0">
																			<input type="number" 
																				class="form-control" 
																				v-model.number="stockedProduct.stock_quantity" 
																				placeholder="Product Quantity" 
																				:class="! errors.products[stockedProductIndex].product_stock_quantity ? 'is-valid' : 'is-invalid'" 
																				@keydown.enter.prevent="nextPage" 
																				@change="validateFormInput('product_stock_quantity')" 
																				:readonly="stockedProduct.available_quantity==0" 
																				:min="createMode ? 1 : stockedProduct.available_quantity" 
																				required="true" 
																			>
																			<div class="input-group-append">
																				<span class="input-group-text">
																					{{ (stockedProduct.merchant_product && stockedProduct.merchant_product.product) ? stockedProduct.merchant_product.product.quantity_type : 'Unit' }}
																				</span>
																			</div>
																		</div>

																		<div class="invalid-feedback" 
																			style="display: block;" 
																			v-show="errors.products[stockedProductIndex].product_stock_quantity"
																		>
																        	{{ errors.products[stockedProductIndex].product_stock_quantity }}
																  		</div>
																	</div>

																	<div class="col-md-4 form-group">
																		<label class="col-form-label font-weight-bold">
																			Stock Code :
																		</label>

																		<input 
																			type="text" 
																			class="form-control is-valid" 
																			v-model.number="stockedProduct.stock_code" 
																			placeholder="Unique Code" 
																			@keydown.enter.prevent="nextPage" 
																			@change="stockedProduct.stock_code=removeWhiteSpace(stockedProduct.stock_code)" 
																			maxlength="12" 
																		>
																	</div>
																</div>
															</div>
														</div>

														<div class="form-row mt-3">
															<div class="form-group col-md-12 text-center">
																<toggle-button 
																	v-model="stockedProduct.has_variations" 
																	:width=150 
																	:sync="true"
																	:color="{checked: 'green', unchecked: 'blue'}"
																	:labels="{checked: 'Has Variation', unchecked: 'No Variation'}" 
																	:disabled="true" 
																/>
															</div>

															<div class="form-group col-md-12 text-center">
																<toggle-button 
																	v-model="stockedProduct.has_serials" 
																	:width=150 
																	:sync="true"
																	:color="{checked: 'green', unchecked: 'blue'}"
																	:labels="{checked: 'Has Serials', unchecked: 'No Serials'}" 
																	:disabled="true" 
																/>
															</div>

															<div 
																class="col-md-12" 
																v-if="stockedProduct.has_variations && stockedProduct.variations && errors.products[stockedProductIndex].variations && stockedProduct.variations.length==errors.products[stockedProductIndex].variations.length"
															>
																<div 
																	class="form-row" 
																	v-for="(stockedVariation, stockedVariationIndex) in stockedProduct.variations" 
																	:key="'stocked-product-index-' + stockedProductIndex + '-product-' + stockedProduct.id + '-product-variation-index-' + stockedVariationIndex + '-product-variation-' + stockedVariation.id"
																>	
																	<div class="col-sm-12">
																		<div class="form-row">	
																			<div class="form-group col-md-4">
																				<label for="inputFirstName">Variaiton</label>
																				<multiselect 
											                              			v-model="stockedVariation.variation"
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

																			<div class="form-group col-md-4">
																				<label for="inputFirstName">Variation Qty</label>

																				<div class="input-group mb-0">
																					<input type="number" 
																						class="form-control" 
																						v-model.number="stockedVariation.stock_quantity" 
																						placeholder="Variation Qty" 
																						:class="! errors.products[stockedProductIndex].variations[stockedVariationIndex].product_variation_quantity ? 'is-valid' : 'is-invalid'" 
																						:min="createMode ? 1 : stockedVariation.available_quantity" 
																						@keydown.enter.prevent="nextPage" 
																					>
																					<div class="input-group-append">
																						<span class="input-group-text">
																							{{ (stockedProduct.merchant_product && stockedProduct.merchant_product.product) ? stockedProduct.merchant_product.product.quantity_type : 'Unit' }}
																						</span>
																					</div>
																				</div>

																				<div 
																					class="invalid-feedback" 
																					style="display: block;" 
																					v-show="errors.products[stockedProductIndex].variations[stockedVariationIndex].product_variation_quantity"
																				>
																		        	{{ errors.products[stockedProductIndex].variations[stockedVariationIndex].product_variation_quantity }}
																		  		</div>
																			</div>

																			<div class="form-group col-md-4">
																				<label for="inputFirstName">Stock Code</label>
																				
																				<input type="text" 
																					class="form-control is-valid" 
																					v-model.number="stockedVariation.stock_code" 
																					placeholder="Unique Code" 
																					@keydown.enter.prevent="nextPage" 
																					@change="stockedVariation.stock_code=removeWhiteSpace(stockedVariation.stock_code)" 
																					maxlength="12" 
																				>
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
															</div>
														</div>
													</div>
												</div>
								    		</div>
										</div>
							    	</div>

							    	<div class="col-md-12">
										<div class="form-row">
											<div class="form-group col-md-6">
												<button 
													type="button" 
													class="btn waves-effect waves-light hor-grd btn-primary btn-outline-primary btn-sm btn-block" 
													v-tooltip.bottom-end="'More Product'" 
													@click="addMoreProduct()"
												>
													Add Product
												</button>
											</div>
											<div class="form-group col-md-6">
												<button 
													type="button" 
													class="btn waves-effect waves-light hor-grd btn-info btn-outline-info btn-sm btn-block" 
													v-tooltip.bottom-end="'Remove Product'" 
													:disabled="(singleStockData.products.length < 2 || (singleStockData.products[singleStockData.products.length-1].stock_quantity != singleStockData.products[singleStockData.products.length-1].available_quantity))" 
													@click="removeProduct()"
												>
													Remove Product
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
											<div class="col-sm-12 d-flex justify-content-between">
												<button 
													type="button" 
													class="btn waves-effect waves-dark btn-secondary btn-outline-secondary btn-sm btn-round" 
													v-tooltip.bottom-end="'Previous'" 
													v-on:click="step-=1"
												>
							                    	<i class="fa fa-2x fa-angle-double-left" aria-hidden="true"></i>
							                  	</button>

												<button 
													type="button" 
													class="btn waves-effect waves-dark btn-secondary btn-outline-secondary btn-sm btn-round" 
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
								    v-show="step==3"
							    >
							    	<h2 class="mx-auto mb-4 lead">
							    		Product Serials
							    	</h2>

							    	<div 
							    		class="col-sm-12" 
							    		v-if="singleStockData.products.length == errors.products.length"
							    	>
							    		<div id="serial-accordion">
								    		<div 
								    			class="card" 
								    			v-for="(stockedProduct, stockedProductIndex) in singleStockData.products" 
												:key="'stocked-product-index-' + stockedProductIndex + '-product-' + stockedProduct.id + '-serial-section'" 
												v-show="stockedProduct.has_serials" 
								    		>
												<div 
													class="card-header" 
													:id="'serial-stocked-product-index-' + stockedProductIndex + '-product-' + stockedProduct.id"
												>
													<p class="mb-0">
														<button type="button" class="btn btn-link pl-0" data-toggle="collapse" :data-target="'#stocked-product-index-' + stockedProductIndex + '-product-' + stockedProduct.id" aria-expanded="true" :aria-controls="'stocked-product-index-' + stockedProductIndex + '-product-' + stockedProduct.id">
															{{ (stockedProduct.merchant_product && stockedProduct.merchant_product.product) ? stockedProduct.merchant_product.product.name : 'Product Name' }} ({{ stockedProduct.merchant_product && stockedProduct.merchant_product.manufacturer ? stockedProduct.merchant_product.manufacturer.name : 'Own Product' }}) Serials
														</button>
													</p>
												</div>

												<div :id="'stocked-product-index-' + stockedProductIndex + '-product-' + stockedProduct.id" class="collapse show" :aria-labelledby="'serial-stocked-product-index-' + stockedProductIndex + '-product-' + stockedProduct.id" data-parent="#serial-accordion">
													<div class="card-body">
														<div class="form-row mt-3">
															<div 
																class="col-md-12" 
																v-if="stockedProduct.has_variations && stockedProduct.has_serials && stockedProduct.variations && errors.products[stockedProductIndex].variations && stockedProduct.variations.length==errors.products[stockedProductIndex].variations.length"
															>
																<div 
																	class="form-row" 
																	v-for="(stockedVariation, stockedVariationIndex) in stockedProduct.variations" 
																	:key="'stocked-product-index-' + stockedProductIndex + '-product-' + stockedProduct.id + '-product-variation-index-' + stockedVariationIndex + '-product-variation-' + stockedVariation.id"
																>	
																	<div class="col-sm-12">
																		<div class="form-row">
																			<div 
																				class="col-md-12" 
																				v-if="stockedVariation.stock_quantity > 0 && stockedVariation.hasOwnProperty('serials')"
																			>
																				<div class="card">
																					<div class="card-header text-center">
																						{{ stockedVariation.variation.name | capitalize }} Serials
																					</div>

																					<div class="card-body">
																						<div class="form-row">
																							<div 
																								class="col-sm-12 form-group" 
																							>
																								<label for="inputFirstName">
																									Serial No.
																								</label>

																								<div class="input-group mb-0">
																									<input 
																										type="text" 
																										class="form-control" 
																										:placeholder="stockedVariation.variation.name + ' Serial Number' | capitalize" 
																										v-model="variationNewSerial[stockedVariationIndex]"
																										:class="! errors.products[stockedProductIndex].variations[stockedVariationIndex].product_variation_serial ? 'is-valid' : 'is-invalid'" 
																										@keydown.enter.prevent="addVariationSerial(stockedProductIndex, stockedVariationIndex)" 
																										:disabled="stockedVariation.serials.length>=stockedVariation.stock_quantity && stockedVariation.serials.every(variationSerial=>variationSerial.serial_no)"
																									>

																									<div class="input-group-append">
																										<span 
																											class="input-group-text waves-effect waves-light btn-grd-primary"
																											v-tooltip.bottom-end="'Insert Serial'" 
																											@click="addVariationSerial(stockedProductIndex, stockedVariationIndex)"
																										>
																											Enlist
																										</span>
																									</div>
																								</div>

																								<div 
																									class="invalid-feedback" 
																									style="display: block;" 
																									v-show="errors.products[stockedProductIndex].variations[stockedVariationIndex].product_variation_serial"
																								>
																						        	{{ errors.products[stockedProductIndex].variations[stockedVariationIndex].product_variation_serial }}
																						  		</div>
																							</div>

																							<div class="col-sm-12 form-group border border-success p-4 rounded-sm">
																								<ul id="merchant-product-variation-serials">
																									<li 
																										v-for="(stockedVariationSerial, stockedVariationSerialIndex) in stockedVariation.serials"
																										:key="'stocked-product-index-' + stockedProductIndex + '-product-' + stockedProduct.id + 'product-variation-index-' + stockedVariationIndex + '-product-variation-' + stockedVariation.id + '-serial-index-' + stockedVariationSerialIndex"
																									>	
																										{{ stockedVariationSerial.serial_no }}

																										<i 
																											class="fa fa-close text-danger p-2" 
																											v-tooltip.bottom-end="'Remove'" 
																											v-show="stockedVariationSerial.serial_no && ! stockedVariationSerial.has_requisitions && ! stockedVariationSerial.has_dispatched"
																											:disabled="stockedVariationSerial.has_requisitions || stockedVariationSerial.has_dispatched" 
																											@click="removeVariationSerial(stockedProductIndex, stockedVariationIndex, stockedVariationSerialIndex)"
																										>	
																										</i>

																										<span 
																										v-show="stockedVariationSerial.has_requisitions || stockedVariationSerial.has_dispatched"
																										:class="[stockedVariationSerial.has_requisitions ? 'badge badge-warning' : stockedVariationSerial.has_dispatched ? 'badge badge-danger' : '']"
																										>
																											{{ stockedVariationSerial.has_requisitions ? 'Requested' : stockedVariationSerial.has_dispatched ? 'Dispatched' : '' }}
																										</span>
																									</li>
																								</ul>
																							</div>

																							<div 
																								class="invalid-feedback text-center" 
																								style="display: block;" 
																								v-show="errors.products[stockedProductIndex].variations[stockedVariationIndex].product_variation_serials"
																							>
																					        	{{ errors.products[stockedProductIndex].variations[stockedVariationIndex].product_variation_serials }}
																					  		</div>
																						</div>
																					</div>
																				</div>
																			</div>
																		</div>
																	</div> 	
																</div>
															</div>

															<div 
																class="col-md-12"
																v-else-if="stockedProduct.has_serials && ! stockedProduct.has_variations && stockedProduct.stock_quantity > 0"
															>		
																<div class="form-row">
																	<div class="col-sm-12 form-group">
																		<label for="inputFirstName">
																			Serial No.
																		</label>

																		<div class="input-group mb-0">
																			<input 
																				type="text" 
																				class="form-control" 
																				v-model="productNewSerial" 
																				:placeholder="stockedProduct.merchant_product.product.name + 'Serial number' | capitalize" 
																				:class="! errors.products[stockedProductIndex].product_serial ? 'is-valid' : 'is-invalid'" 
																				@keydown.enter.prevent="addProductSerial(stockedProductIndex)"  
																				:disabled="stockedProduct.serials.length >= stockedProduct.stock_quantity && stockedProduct.serials.every(productSerial=>productSerial.serial_no)"
																			>

																			<div class="input-group-append">
																				<span 
																					class="input-group-text waves-effect waves-light btn-grd-primary"
																					v-tooltip.bottom-end="'Insert Serial'" 
																					@click="addProductSerial(stockedProductIndex)"
																				>
																					Enlist
																				</span>
																			</div>
																		</div>

																		<div 
																			class="invalid-feedback" 
																			style="display: block;" 
																			v-show="errors.products[stockedProductIndex].product_serial"
																		>
																        	{{ errors.products[stockedProductIndex].product_serial }}
																  		</div>
																	</div>

																	<div class="col-sm-12 form-group border border-success p-4 rounded-sm">
																		<ul id="merchant-product-serials">
																			<li 
																				v-for="(productSerial, productSerialIndex) in stockedProduct.serials"
																				:key="'stocked-product-index-' + stockedProductIndex + '-product-' + stockedProduct.id + 'product-serial-index-' + productSerialIndex + '-product-serial-' + productSerial.id"
																			>	
																				{{ productSerial.serial_no }}

																				<i 
																					class="fa fa-close text-danger p-2" 
																					v-tooltip.bottom-end="'Remove'" 
																					v-show="productSerial.serial_no && ! productSerial.has_requisitions && ! productSerial.has_dispatched"
																					:disabled="productSerial.has_requisitions || productSerial.has_dispatched" 
																					@click="removeProductSerial(stockedProductIndex, productSerialIndex)"
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

																	<div 
																		class="invalid-feedback text-center" 
																		style="display: block;" 
																		v-show="errors.products[stockedProductIndex].product_serials"
																	>
															        	{{ errors.products[stockedProductIndex].product_serials }}
															  		</div>
																</div>
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
											<div class="col-sm-12 d-flex justify-content-between">
												<button 
													type="button" 
													class="btn waves-effect waves-dark btn-secondary btn-outline-secondary btn-sm btn-round" 
													v-tooltip.bottom-end="'Previous'" 
													v-on:click="step-=1"
												>
							                    	<i class="fa fa-2x fa-angle-double-left" aria-hidden="true"></i>
							                  	</button>

												<button 
													type="button" 
													class="btn waves-effect waves-dark btn-secondary btn-outline-secondary btn-sm btn-round" 
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
									v-show="step==4"
								>
									<h2 class="mx-auto mb-4 lead">Manufacturing & Expiring Date</h2>

									<div 
										class="col-sm-12" 
										v-if="singleStockData.products.length == errors.products.length"
									>
										<div id="product-date-accordion">
								    		<div 
								    			class="card" 
								    			v-for="(stockedProduct, stockedProductIndex) in singleStockData.products" 
												:key="'stocked-product-index-' + stockedProductIndex + '-product-' + stockedProduct.id + '-manufacturing-and-expiring-date-picker'"
								    		>
												<div 
													class="card-header" 
													:id="'heading-stocked-product-index-' + stockedProductIndex + '-product-' + stockedProduct.id + '-manufacturing-and-expiring-date-picker'"
												>
													<p class="mb-0">
														<button type="button" class="btn btn-link pl-0" data-toggle="collapse" :data-target="'#stocked-product-index-' + stockedProductIndex + '-product-' + stockedProduct.id + '-manufacturing-and-expiring-date-picker'" aria-expanded="true" :aria-controls="'stocked-product-index-' + stockedProductIndex + '-product-' + stockedProduct.id + '-manufacturing-and-expiring-date-picker'">
															{{ (stockedProduct.merchant_product && stockedProduct.merchant_product.product) ? stockedProduct.merchant_product.product.name : 'Product Name' }} ({{ stockedProduct.merchant_product && stockedProduct.merchant_product.manufacturer ? stockedProduct.merchant_product.manufacturer.name : 'Own Product' }})
														</button>
													</p>
												</div>

												<div :id="'stocked-product-index-' + stockedProductIndex + '-product-' + stockedProduct.id + '-manufacturing-and-expiring-date-picker'" class="collapse show" :aria-labelledby="'heading-stocked-product-index-' + stockedProductIndex + '-product-' + stockedProduct.id + '-manufacturing-and-expiring-date-picker'" data-parent="#product-date-accordion">
													<div class="card-body">
														<div class="row">
															<!-- Product (No Variation) Mfg. & Exp. Date -->
															<div class="col-md-12" v-show="! stockedProduct.has_variations">
																<div class="form-row">
																	<div class="col-md-6 form-group">
																		<label class="d-block col-form-label font-weight-bold">
																			Manufacturing Date : 
																			<span v-show="stockedProduct.manufactured_at">
																				{{ stockedProduct.manufactured_at }}
																			</span>
																		</label>
																		
																		<v-date-picker 
																			v-model="stockedProduct.manufactured_at" 
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
																			v-show="errors.products[stockedProductIndex].manufacturing_date"
																		>
																        	{{ errors.products[stockedProductIndex].manufacturing_date }}
																  		</div>
																  	</div>

																  	<div class="col-md-6 form-group">
																		<label class="d-block col-form-label font-weight-bold">
																			Expiring Date : 
																			<span v-show="stockedProduct.expired_at">
																				{{ stockedProduct.expired_at }}
																			</span>
																		</label>
																		
																		<v-date-picker 
																			v-model="stockedProduct.expired_at" 
																			color="red" 
																			is-dark
																			is-inline 
																			:min-date="stockedProduct.manufactured_at" 
																			:model-config="{ type: 'string', mask: 'YYYY-MM-DD' }" 
																			:attributes="[ { key: 'today', dot: true, dates: new Date() } ]" 
																			@input="validateFormInput('expiring_date')"
																		/>

																		<div class="invalid-feedback" 
																			style="display: block;" 
																			v-show="errors.products[stockedProductIndex].expiring_date"
																		>
																        	{{ errors.products[stockedProductIndex].expiring_date }}
																  		</div>
																  	</div>
																</div>
															</div>

															<!-- Variations Mfg. & Exp. Date -->
															<div 
																class="col-md-12" 
																v-if="stockedProduct.has_variations && stockedProduct.variations && errors.products[stockedProductIndex].variations && stockedProduct.variations.length==errors.products[stockedProductIndex].variations.length"
															>
																<div 
																	class="card" 
																	v-for="(stockedVariation, stockedVariationIndex) in stockedProduct.variations" 
																	:key="'product-variation-index-' + stockedVariationIndex + '-date-picker'"
																>	
																	<div 
																		class="card-header" 
																		v-if="stockedVariation.stock_quantity > 0"
																	>
																		<h5>
																			{{ stockedVariation.variation ? stockedVariation.variation.name : '' | capitalize }}
																		</h5>
																	</div>

																	<div 
																		class="card-body"
																		v-if="stockedVariation.stock_quantity > 0"
																	>
																		<!-- 
																		<div class="form-row">
																			<div class="form-group col-md-12 text-center">
																				<img 
																					:src="showPreview(stockedVariation.preview)" 
																					class="img-fluid" 
																					:alt="$options.filters.capitalize(stockedVariation.variation.name) + ' Preview'" 
																					width="100px"
																				>
																				<p class="text-center">{{ stockedVariation.variation.name | capitalize }}</p>
																			</div>
																		</div> 
																		-->

																		<div class="form-row">
																			<div class="form-group col-md-6">
																				<label class="d-block" for="inputFirstName">
																					Manufacturing Date : 
																					<span v-show="stockedVariation.manufactured_at">
																						{{ stockedVariation.manufactured_at }}
																					</span>
																				</label>
																				
																				<v-date-picker 
																					v-model="stockedVariation.manufactured_at" 
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
																					v-show="errors.products[stockedProductIndex].variations[stockedVariationIndex].manufacturing_date"
																				>
																		        	{{ errors.products[stockedProductIndex].variations[stockedVariationIndex].manufacturing_date }}
																		  		</div>
																			</div>

																			<div class="form-group col-md-6">
																				<label class="d-block" for="inputFirstName">
																					Expiring Date : 
																					<span v-show="stockedVariation.expired_at">
																						{{ stockedVariation.expired_at }}
																					</span>
																				</label>
																				<v-date-picker 
																					v-model="stockedVariation.expired_at" 
																					color="red" 
																					is-dark
																					is-inline 
																					:min-date="stockedVariation.manufactured_at" 
																					:model-config="{ type: 'string', mask: 'YYYY-MM-DD' }" 
																					:attributes="[ { key: 'today', dot: true, dates: new Date() } ]" 
																					@input="validateFormInput('expiring_date')"
																				/>

																				<div class="invalid-feedback" 
																					style="display: block;" 
																					v-show="errors.products[stockedProductIndex].variations[stockedVariationIndex].expiring_date"
																				>
																		        	{{ errors.products[stockedProductIndex].variations[stockedVariationIndex].expiring_date }}
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

										<div class="row card-footer">
											<div class="col-sm-12 text-right" v-show="!submitForm">
												<span class="text-danger small mb-1">
											  		Please input required fields
											  	</span>
											</div>
											<div class="col-sm-12 d-flex justify-content-between">
												<button 
													type="button" 
													class="btn waves-effect waves-dark btn-secondary btn-outline-secondary btn-sm btn-round" 
													v-tooltip.bottom-end="'Previous'" 
													v-on:click="stockHasSerial() ? step-=1 : step-=2"
												>
							                    	<i class="fa fa-2x fa-angle-double-left" aria-hidden="true"></i>
							                  	</button>

												<button 
													type="button" 
													class="btn waves-effect waves-dark btn-secondary btn-outline-secondary btn-sm btn-round" 
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
									v-bind:key="'product-modal-step-' + 5" 
									v-show="step==5"
								>
									<h2 class="mx-auto mb-4 lead">Purchase Informations</h2>

									<div 
										class="col-sm-12" 
										v-if="singleStockData.products.length == errors.products.length"
									>
										<div id="product-purchase-accordion">
								    		<div 
								    			class="card" 
								    			v-for="(stockedProduct, stockedProductIndex) in singleStockData.products" 
												:key="'stocked-product-index-' + stockedProductIndex + '-product-' + stockedProduct.id + '-vendor-and-location-picker'"
								    		>
												<div 
													class="card-header" 
													:id="'heading-stocked-product-index-' + stockedProductIndex + '-product-' + stockedProduct.id + '-vendor-and-location-picker'"
												>
													<p class="mb-0">
														<button type="button" class="btn btn-link pl-0" data-toggle="collapse" :data-target="'#stocked-product-index-' + stockedProductIndex + '-product-' + stockedProduct.id + '-vendor-and-location-picker'" aria-expanded="true" :aria-controls="'stocked-product-index-' + stockedProductIndex + '-product-' + stockedProduct.id + '-vendor-and-location-picker'">
															{{ (stockedProduct.merchant_product && stockedProduct.merchant_product.product) ? stockedProduct.merchant_product.product.name : 'Product Name' }} ({{ stockedProduct.merchant_product && stockedProduct.merchant_product.manufacturer ? stockedProduct.merchant_product.manufacturer.name : 'Own Product' }})
														</button>
													</p>
												</div>

												<div :id="'stocked-product-index-' + stockedProductIndex + '-product-' + stockedProduct.id + '-vendor-and-location-picker'" class="collapse show" :aria-labelledby="'heading-stocked-product-index-' + stockedProductIndex + '-product-' + stockedProduct.id + '-vendor-and-location-picker'" data-parent="#product-date-accordion">
													<div class="card-body">
														<div class="row">
															<div class="col-md-12">
																<div class="form-row">
																	<div class="col-sm-6 form-group">
																		<label class="col-form-label font-weight-bold">
																			Select Vendor :
																		</label>
																		
																		<multiselect 
									                              			v-model="stockedProduct.vendor"
									                                  		:options="allVendors" 
									                                  		:custom-label="objectNameWithCapitalized" 
									                                  		:required="true" 
									                                  		:allow-empty="false"
									                              			placeholder="Select Vendor" 
									                              			class="form-control p-0" 
									                                  		:class="!errors.products[stockedProductIndex].vendor ? 'is-valid' : 'is-invalid'" 
									                                  		@input="setPurchaseVendor(stockedProductIndex)" 
									                                  		@close="validateFormInput('vendor')"
									                              		>
									                                	</multiselect>

									                                	<div class="invalid-feedback">
																	    	{{ errors.products[stockedProductIndex].vendor }}
																	    </div>
																	</div>

																	<div class="col-sm-6 form-group">
																		<label class="col-form-label font-weight-bold">
																			Select Location :
																		</label>
																		
																		<multiselect 
									                              			v-model="stockedProduct.location"
									                                  		:options="allLocations" 
									                                  		:custom-label="objectNameWithCapitalized" 
									                                  		:required="true" 
									                                  		:allow-empty="false"
									                              			placeholder="Select Location" 
									                              			class="form-control p-0" 
									                                  		:class="!errors.products[stockedProductIndex].location ? 'is-valid' : 'is-invalid'" 
									                                  		@input="setPurchaseLocation(stockedProductIndex)" 
									                                  		@close="validateFormInput('location')"
									                              		>
									                                	</multiselect>

									                                	<div class="invalid-feedback">
																	    	{{ errors.products[stockedProductIndex].location }}
																	    </div>
																	</div>
																</div>

																<div class="row" v-show="! stockedProduct.has_variations">
																	<div class="col-sm-12">
																		<div class="form-row form-group">
																			<label class="col-form-label font-weight-bold text-md-right">
																				Buying Price :
																			</label>
																			<div class="col-sm-12">

																				<div class="input-group mb-0">
																					<input 
																						type="number" 
																						v-model.number="stockedProduct.unit_buying_price" 
																						class="form-control" 
																						:class="!errors.products[stockedProductIndex].unit_buying_price ? 'is-valid' : 'is-invalid'" 
																						placeholder="Product Buying Price" 
																						:disabled="stockedProduct.has_variations" 
																						@change="validateFormInput('unit_buying_price')" 
																						@keydown.enter.prevent="nextPage" 
																						min="1" 
																					>
																					<div class="input-group-append">
																						<span class="input-group-text">
																							{{ general_settings.official_currency_name || 'BDT' | capitalize }} 
																							/ 
																							{{ (stockedProduct.merchant_product && stockedProduct.merchant_product.product) ? stockedProduct.merchant_product.product.quantity_type : 'Unit' | capitalize }}
																						</span>
																					</div>
																				</div>

																				<div 
																					class="invalid-feedback" 
																					style="display: block;" 
																					v-show="errors.products[stockedProductIndex].unit_buying_price" 
																				>
																			    	{{ errors.products[stockedProductIndex].unit_buying_price }}
																			    </div>
																			</div>
																		</div>
																	</div>
																</div>

																<div class="row" v-if="stockedProduct.has_variations && stockedProduct.variations && errors.products[stockedProductIndex].variations && stockedProduct.variations.length==errors.products[stockedProductIndex].variations.length">
																	<div 
																		class="form-group col-md-12" 
																		v-if="stockedProduct.hasOwnProperty('variations') && stockedProduct.variations.length"
																	>
																		<div 
																			class="form-row" 
																			v-for="(stockedVariation, stockedVariationIndex) in stockedProduct.variations" 
																			:key="'product-variation-index-' + stockedVariationIndex + '-vendor-segment'"
																		>
																			<div 
																				class="form-group col-md-12" 
																				v-show="stockedVariation.stock_quantity > 0"
																			>
																				<label for="inputFirstName">{{ stockedVariation.variation ? stockedVariation.variation.name : '' | capitalize }} Buying Price</label>

																				<div class="input-group mb-0">
																					<input 
																						type="number" 
																						v-model.number="stockedVariation.unit_buying_price" 
																						class="form-control" 
																						:class="!errors.products[stockedProductIndex].variations[stockedVariationIndex].unit_buying_price ? 'is-valid' : 'is-invalid'" 
																						placeholder="Variation Buying Price" 
																						@change="validateFormInput('unit_buying_price')" 
																						@keydown.enter.prevent="nextPage" 
																						min="1" 
																					>
																					<div class="input-group-append">
																						<span class="input-group-text">
																							{{ general_settings.official_currency_name || 'BDT' | capitalize }}
																							/ 
																							{{ (stockedProduct.merchant_product && stockedProduct.merchant_product.product) ? stockedProduct.merchant_product.product.quantity_type : 'Unit' | capitalize }}
																						</span>
																					</div>
																				</div>

																				<div 
																					class="invalid-feedback" 
																					style="display: block;" 
																					v-show="errors.products[stockedProductIndex].variations[stockedVariationIndex].unit_buying_price"
																				>
																			    	{{ errors.products[stockedProductIndex].variations[stockedVariationIndex].unit_buying_price }}
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

										<div class="row card-footer">
											<div class="col-sm-12 text-right" v-show="!submitForm">
												<span class="text-danger small mb-1">
											  		Please input required fields
											  	</span>
											</div>
											<div class="col-sm-12 d-flex justify-content-between">
												<button 
													type="button" 
													class="btn waves-effect waves-dark btn-secondary btn-outline-secondary btn-sm btn-round" 
													v-tooltip.bottom-end="'Previous'" 
													v-on:click="stockHasPerishableProduct() ? step-=1 : stockHasSerial() ? step-=2 : step-=3"
												>
							                    	<i class="fa fa-2x fa-angle-double-left" aria-hidden="true"></i>
							                  	</button>

												<button 
													type="button" 
													class="btn waves-effect waves-dark btn-secondary btn-outline-secondary btn-sm btn-round" 
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
									v-bind:key="'product-modal-step-' + 6" 
									v-show="step==6" 
								>
									<h2 class="mx-auto mb-4 lead">Store Stock</h2>

									<div 
										class="col-md-12" 
										v-if="singleStockData.products.length == errors.products.length"
									>
										<div id="store-accordion">
											<div 
												class="card" 
												v-for="(stockedProduct, stockedProductIndex) in singleStockData.products" 
												:key="'store-product-stock-index-' + stockedProductIndex + '-product-stock-' + stockedProduct.id"
											>
												<div class="card-header" :id="'heading-store-product-stock-index-' + stockedProductIndex + '-product-stocked-' + stockedProduct.id">	
													<p class="mb-0">
														<button type="button" class="btn btn-link pl-0" data-toggle="collapse" :data-target="'#collapse-store-product-stock-index-' + stockedProductIndex + '-product-stock-' + stockedProduct.id" aria-expanded="true" :aria-controls="'collapse-store-product-stock-index-' + stockedProductIndex + '-product-stock-' + stockedProduct.id">
															{{ (stockedProduct.merchant_product && stockedProduct.merchant_product.product) ? stockedProduct.merchant_product.product.name : 'Product Name' }} ({{ stockedProduct.merchant_product && stockedProduct.merchant_product.manufacturer ? stockedProduct.merchant_product.manufacturer.name : 'Own Product' }}) Store
														</button>
													</p>
												</div>

												<div :id="'collapse-store-product-stock-index-' + stockedProductIndex + '-product-stock-' + stockedProduct.id" class="collapse show" :aria-labelledby="'heading-store-product-stock-index-' + stockedProductIndex + '-product-stocked-' + stockedProduct.id" data-parent="#store-accordion">
													<div class="card-body">
														<div class="form-row">
															<div 
																class="col-md-12"
																v-for="(stockSpace, spaceIndex) in stockedProduct.addresses" 
																:key="'product-stock-index-' + stockedProductIndex + '-product-stock-' + stockedProduct.id  + 'product-space-' + spaceIndex"
															>
																<div 
																	class="card"
																	v-if="stockSpace && errors.products[stockedProductIndex].addresses[spaceIndex]"
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
											                                  		:class="! errors.products[stockedProductIndex].addresses[spaceIndex].product_space_type ? 'is-valid' : 'is-invalid'" 
											                                  		:disabled="stockedProduct.addresses.length > (spaceIndex+1)" 
											                                  		@input="setProductSpaceType(stockedProductIndex, spaceIndex)" 
											                              		>
											                                	</multiselect>
											                                	<div class="invalid-feedback">
																			    	{{ errors.products[stockedProductIndex].addresses[spaceIndex].product_space_type }}
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
											                                  		:option-height="104" 
											                                  		class="form-control p-0" 
											                                  		:class="! errors.products[stockedProductIndex].addresses[spaceIndex].product_containers ? 'is-valid' : 'is-invalid'" 
											                                  		:disabled="stockedProduct.addresses.length > (spaceIndex+1)" 
											                                  		@input="validateFormInput('product_containers')" 
											                              		>
											                              			<template slot="option" slot-scope="props">
																						<div class="option__desc">
																							<span class="option__title">{{ props.option.name | capitalize }}</span>
																							
																							<span :class="[props.option.occupied > 0.0 ? 'badge-warning' : 'badge-success', 'badge']">
																								{{ props.option.occupied > 0.0 ? 'Occupied' : 'Empty' }}
																							</span>
																						</div>
																					</template>
											                                	</multiselect>
											                                	<div class="invalid-feedback">
																			    	{{ errors.products[stockedProductIndex].addresses[spaceIndex].product_containers }}
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
											                                  		:option-height="104" 
											                                  		class="form-control p-0" 
											                                  		:class="! errors.products[stockedProductIndex].addresses[spaceIndex].product_container ? 'is-valid' : 'is-invalid'" 
											                                  		:disabled="stockedProduct.addresses.length > (spaceIndex+1)"
											                                  		@input="setAvailableShelves(stockedProductIndex, spaceIndex)"
											                              		>
											                              			<template slot="option" slot-scope="props">
																						<div class="option__desc">
																							<span class="option__title">{{ props.option.name | capitalize }}</span>
																							
																							<span :class="[props.option.occupied > 0.0 ? 'badge-warning' : 'badge-success', 'badge']">
																								{{ props.option.occupied > 0.0 ? 'Occupied' : 'Empty' }}
																							</span>
																						</div>
																					</template>
											                                	</multiselect>
											                                	<div class="invalid-feedback">
																			    	{{ errors.products[stockedProductIndex].addresses[spaceIndex].product_container }}
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
											                                  		:option-height="104" 
											                                  		class="form-control p-0" 
											                                  		:class="! errors.products[stockedProductIndex].addresses[spaceIndex].product_shelves ? 'is-valid' : 'is-invalid'" 
											                                  		:disabled="stockedProduct.addresses.length > (spaceIndex+1)" 
											                                  		@input="validateFormInput('product_shelves')" 
											                              		>
											                              			<template slot="option" slot-scope="props">
																						<div class="option__desc">
																							<span class="option__title">{{ props.option.name | capitalize }}</span>
																							
																							<span :class="[props.option.occupied > 0.0 ? 'badge-warning' : 'badge-success', 'badge']">
																								{{ props.option.occupied > 0.0 ? 'Occupied' : 'Empty' }}
																							</span>
																						</div>
																					</template>
											                                	</multiselect>
											                                	<div class="invalid-feedback">
																			    	{{ errors.products[stockedProductIndex].addresses[spaceIndex].product_shelves }}
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
											                                  		:class="!errors.products[stockedProductIndex].addresses[spaceIndex].product_shelves ? 'is-valid' : 'is-invalid'" 
											                                  		:disabled="stockedProduct.addresses.length > (spaceIndex+1)" 
											                              		>
											                                	</multiselect>
											                                	<div class="invalid-feedback">
																			    	{{ errors.products[stockedProductIndex].addresses[spaceIndex].product_shelves }}
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
											                                  		:option-height="104" 
											                                  		class="form-control p-0" 
											                                  		:class="!errors.products[stockedProductIndex].addresses[spaceIndex].product_container  ? 'is-valid' : 'is-invalid'" 
											                                  		:disabled="stockedProduct.addresses.length > (spaceIndex+1)"
											                                  		@input="setAvailableUnitShelves(stockedProductIndex, spaceIndex)" 
											                              		>
											                              			<template slot="option" slot-scope="props">
																						<div class="option__desc">
																							<span class="option__title">{{ props.option.name | capitalize }}</span>
																							
																							<span :class="[props.option.occupied > 0.0 ? 'badge-warning' : 'badge-success', 'badge']">
																								{{ props.option.occupied > 0.0 ? 'Occupied' : 'Empty' }}
																							</span>
																						</div>
																					</template>
											                                	</multiselect>
											                                	<div class="invalid-feedback">
																			    	{{ errors.products[stockedProductIndex].addresses[spaceIndex].product_container }}
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
											                                  		:option-height="104" 
											                                  		class="form-control p-0" 
											                                  		:class="!errors.products[stockedProductIndex].addresses[spaceIndex].product_shelf  ? 'is-valid' : 'is-invalid'" 
											                                  		:disabled="stockedProduct.addresses.length > (spaceIndex+1)"
											                                  		@input="setAvailableUnits(stockedProductIndex, spaceIndex)" 
											                              		>
											                              			<template slot="option" slot-scope="props">
																						<div class="option__desc">
																							<span class="option__title">{{ props.option.name | capitalize }}</span>
																							
																							<span :class="[props.option.occupied > 0.0 ? 'badge-warning' : 'badge-success', 'badge']">
																								{{ props.option.occupied > 0.0 ? 'Occupied' : 'Empty' }}
																							</span>
																						</div>
																					</template>
											                                	</multiselect>
											                                	<div class="invalid-feedback">
																			    	{{ errors.products[stockedProductIndex].addresses[spaceIndex].product_shelf }}
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
											                                  		:class="!errors.products[stockedProductIndex].addresses[spaceIndex].product_shelf  ? 'is-valid' : 'is-invalid'" 
											                                  		:disabled="stockedProduct.addresses.length > (spaceIndex+1)"
											                                  		@input="setAvailableUnits(stockedProductIndex, spaceIndex)" 
											                              		>
											                                	</multiselect>
											                                	<div class="invalid-feedback">
																			    	{{ errors.products[stockedProductIndex].addresses[spaceIndex].product_shelf }}
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
											                                  		:option-height="104" 
											                                  		class="form-control p-0" 
											                                  		:class="!errors.products[stockedProductIndex].addresses[spaceIndex].product_units ? 'is-valid' : 'is-invalid'" 
											                                  		:disabled="stockedProduct.addresses.length > (spaceIndex+1)" 
											                                  		@input="validateFormInput('product_units')" 
											                              		>
											                              			<template slot="option" slot-scope="props">
																						<div class="option__desc">
																							<span class="option__title">{{ props.option.name | capitalize }}</span>
																							
																							<span :class="[props.option.occupied > 0.0 ? 'badge-warning' : 'badge-success', 'badge']">
																								{{ props.option.occupied > 0.0 ? 'Occupied' : 'Empty' }}
																							</span>
																						</div>
																					</template>
											                                	</multiselect>
											                                	<div class="invalid-feedback">
																			    	{{ errors.products[stockedProductIndex].addresses[spaceIndex].product_units }}
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
											                                  		:class="!errors.products[stockedProductIndex].addresses[spaceIndex].product_units ? 'is-valid' : 'is-invalid'" 
											                                  		:disabled="stockedProduct.addresses.length > (spaceIndex+1)" 
											                              		>
											                                	</multiselect>
											                                	<div class="invalid-feedback">
																			    	{{ errors.products[stockedProductIndex].addresses[spaceIndex].product_units }}
																			    </div>
																			</div>
																		</div>
																	</div>
																</div>
															</div>

															<div 
																class="col-md-12 text-center"  
																v-show="! stockedProduct.hasOwnProperty('addresses') || ! stockedProduct.addresses.length"
															>
																<p class="text-danger">
																	No Space Found.
																</p>

																<p 
																	class="text-danger" 
																	v-show="errors.products[stockedProductIndex].product_address"
																>
																	{{ errors.products[stockedProductIndex].product_address }}
																</p>
															</div>

															<div class="col-md-12">
																<div class="form-row">
																	<div class="form-group col-md-6">
																		<button 
																			type="button" 
																			class="btn waves-effect waves-light hor-grd btn-primary btn-outline-primary btn-sm btn-block" 
																			v-tooltip.bottom-end="'More Space'" 
																			@click="addMoreSpace(stockedProductIndex)"
																		>
																			Add Space
																		</button>
																	</div>
																	<div class="form-group col-md-6">
																		<button 
																			type="button" 
																			class="btn waves-effect waves-light hot-grd btn-info btn-outline-info btn-sm btn-block" 
																			v-tooltip.bottom-end="'Remove Space'" 
																			:disabled="createMode ? stockedProduct.addresses.length < 2 : stockedProduct.addresses.length < 1" 
																			@click="removeSpace(stockedProductIndex)"
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
													class="btn waves-effect waves-dark btn-secondary btn-outline-secondary btn-sm btn-round float-left" 
													v-on:click="merchantHasPurchaseSupport() ? step-=1 : stockHasPerishableProduct() ? step-=2 : stockHasSerial() ? step-=3 : step-=4"
												>
							                    	<i class="fa fa-2x fa-angle-double-left" aria-hidden="true"></i>
							                  	</button>

												<button 
													type="submit" 
													:class="[singleStockData.has_approval ? 'btn-primary btn-outline-primary' : 'btn-warning btn-outline-warning', 'btn waves-effect waves-dark', 'float-right']" 
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
		<div class="modal fade" id="stock-view-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Stock Details</h5>
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
										<a class="nav-link active" data-toggle="tab" href="#stock-profile" role="tab">
											Stock
										</a>
									</li>

									<li class="nav-item">
										<a class="nav-link" data-toggle="tab" href="#stock-products" role="tab">
											Products
										</a>
									</li>

									<li class="nav-item">
										<a class="nav-link" data-toggle="tab" href="#product-address" role="tab">
											Address
										</a>
									</li>
								</ul>

								<div class="tab-content card-block">
									
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
														v-for="(productVariation, stockedVariationIndex) in product.variations" 
														:key="'product-variation-' + stockedVariationIndex"
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

 									<div class="tab-pane active" id="stock-profile" role="tabpanel">
										<!-- 
										<div class="form-row">
											<label class="col-6 col-form-label font-weight-bold text-md-right">
												Product Name :
											</label>

											<label class="col-6 col-form-label">
												{{ product.name | capitalize }}
											</label>
										</div> 
										-->

										<div class="form-row">
											<label class="col-6 col-form-label font-weight-bold text-md-right">
												Merchant :
											</label>

											<label class="col-6 col-form-label">
												{{ getFullName(singleStockData.merchant) | capitalize }}
											</label>
										</div>

										<div class="form-row">
											<label class="col-6 col-form-label font-weight-bold text-md-right">
												Invoice :
											</label>

											<label class="col-6 col-form-label">
												{{ singleStockData.invoice_no }}
											</label>
										</div>

										<div class="form-row">
											<label class="col-6 col-form-label font-weight-bold text-md-right">
												Stocked on :
											</label>

											<label class="col-6 col-form-label">
												{{ singleStockData.created_at }}
											</label>
										</div>

										<div class="form-row">
											<label class="col-6 col-form-label font-weight-bold text-md-right">
												Stocked at :
											</label>

											<label class="col-6 col-form-label">
												{{ singleStockData.hasOwnProperty('warehouse') ? singleStockData.warehouse.name : 'NA' }}
											</label>
										</div>

										<div class="form-row" v-if="singleStockData.hasOwnProperty('keeper')">
											<label class="col-6 col-form-label font-weight-bold text-md-right">
												Stored By :
											</label>

											<label class="col-6 col-form-label">
												{{ singleStockData.keeper.user_name | capitalize }}
											</label>
										</div>

										<div class="form-row">
											<label class="col-6 col-form-label font-weight-bold text-md-right">
												Approval :
											</label>

											<label class="col-6 col-form-label">
												<span :class="[singleStockData.has_approval==1 ? 'badge-success' : singleStockData.has_approval==-1 ? 'badge-danger' : 'badge-secondary', 'badge']">
													{{ singleStockData.has_approval==1 ? 'Approved' : singleStockData.has_approval==-1 ? 'Cancelled' : 'Pending' }}
												</span>
											</label>
										</div>

										<div class="form-row" v-if="singleStockData.has_approval">
											<label class="col-6 col-form-label font-weight-bold text-md-right">
												{{ singleStockData.has_approval==1 ? 'Approved' : 'Cancelled' }} By :
											</label>

											<label class="col-6 col-form-label">
												{{ singleStockData.approver.user_name | capitalize }}
											</label>
										</div>

										<div class="form-row" v-if="singleStockData.has_approval">
											<label class="col-6 col-form-label font-weight-bold text-md-right">
												{{ singleStockData.has_approval==1 ? 'Approved' : 'Cancelled' }} on :
											</label>

											<label class="col-6 col-form-label">
												{{ singleStockData.updated_at }}
											</label>
										</div>
									</div>

									<div class="tab-pane" id="stock-products" role="tabpanel">
										<div 
											class="card"
											v-for="(stockedProduct, stockedProductIndex) in singleStockData.products" 
											:key="'stock-product-index-' + stockedProductIndex + '-stocked-product-' + stockedProduct.id + '-B'"
										>
											<div class="card-header">
												{{ (stockedProduct.merchant_product && stockedProduct.merchant_product.product) ? stockedProduct.merchant_product.product.name : 'Product' | capitalize }}
												({{ (stockedProduct.merchant_product && stockedProduct.merchant_product.manufacturer) ? stockedProduct.merchant_product.manufacturer.name : 'Own Product' | capitalize }})
											</div>

											<div class="card-body">
												<div class="form-row" v-show="! stockedProduct.has_variations">
													<label class="col-sm-6 col-form-label font-weight-bold text-right">
														Product-Stock (Batch) Code :
													</label>

													<label class="col-sm-6 col-form-label">
														{{ stockedProduct.stock_code | capitalize }}

														<i class="fa fa-print fa-lg text-danger" aria-hidden="true" @click="printStockCode([ stockedProduct ])" v-tooltip.bottom-end="'Print'"></i>
													</label>
												</div>

												<div 
													class="form-row" 
													v-show="stockedProduct.manufactured_at"
												>
													<label class="col-sm-6 col-form-label font-weight-bold text-right">
														Stock Manufacturing Code :
													</label>

													<label class="col-sm-6 col-form-label">
														{{ stockedProduct.manufactured_at }}
													</label>
												</div>

												<div 
													class="form-row" 
													v-show="stockedProduct.expired_at"
												>
													<label class="col-sm-6 col-form-label font-weight-bold text-right">
														Stock Expiring Code :
													</label>

													<label class="col-sm-6 col-form-label">
														{{ stockedProduct.expired_at }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-6 col-form-label font-weight-bold text-md-right">
														Stock Quantity :
													</label>

													<div class="col-sm-6 col-form-label">
														
														{{ stockedProduct.stock_quantity }} {{ (stockedProduct.merchant_product && stockedProduct.merchant_product.product) ? stockedProduct.merchant_product.product.quantity_type : 'Unit' }}
														
														<div class="form-row" v-if="stockedProduct.has_variations && stockedProduct.hasOwnProperty('variations') && stockedProduct.variations.length">
															<div 
																class="col-md-12" 
																v-for="(stockedProductVariation, stockedProductVariationIndex) in stockedProduct.variations" 
																:key="'stock-product-index-' + stockedProductIndex + '-stocked-product-' + stockedProduct.id + 'product-variation-index-' + stockedProductVariationIndex + '-B'"
															>
																<div class="card">
																	<div class="card-body">
																		<div class="form-row">
																			<label class="col-form-label font-weight-bold text-right">
																				{{ stockedProductVariation.variation.name | capitalize }} :
																			</label>

																			<label class="col-form-label">
																				{{ stockedProductVariation.stock_quantity }} {{ (stockedProduct.merchant_product && stockedProduct.merchant_product.product) ? stockedProduct.merchant_product.product.quantity_type : 'Unit' }}
																			</label>
																		</div>

																		<div class="form-row">
																			<label class="col-form-label font-weight-bold text-right">
																				Available Quantity :
																			</label>
																			<label class="col-form-label">
																				{{ stockedProductVariation.available_quantity }} {{ (stockedProduct.merchant_product && stockedProduct.merchant_product.product) ? stockedProduct.merchant_product.product.quantity_type : 'Unit' }}
																			</label>
																		</div>

																		<div class="form-row">
																			<label class="col-form-label font-weight-bold text-right">
																				Buying Price :
																			</label>
																			<label class="col-form-label">
																				{{ stockedProductVariation.unit_buying_price }}
																				{{ general_settings.official_currency_name || 'BDT' | capitalize }}
																			</label>
																		</div>

																		<div class="form-row">
																			<label class="col-form-label font-weight-bold text-right">
																				Stock (Batch) Code :
																			</label>
																			<label class="col-form-label">
																				{{ stockedProductVariation.stock_code | capitalize }}
																			</label>
																		</div>

																		<div 
																			class="form-row" 
																			v-show="stockedProductVariation.manufactured_at"
																		>
																			<label class="col-form-label font-weight-bold text-right">
																				Manufacturing Date :
																			</label>
																			<label class="col-form-label">
																				{{ stockedProductVariation.manufactured_at }}
																			</label>
																		</div>

																		<div 
																			class="form-row" 
																			v-show="stockedProductVariation.expired_at"
																		>
																			<label class="col-form-label font-weight-bold text-right">
																				Expiring Date :
																			</label>
																			<label class="col-form-label">
																				{{ stockedProductVariation.expired_at }}
																			</label>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>

												<div class="form-row" v-show="! stockedProduct.has_variations">
													<label class="col-6 col-form-label font-weight-bold text-md-right">
														Available Quantity :
													</label>
													
													<label class="col-sm-6 col-form-label">
														{{ stockedProduct.available_quantity }} {{ (stockedProduct.merchant_product && stockedProduct.merchant_product.product) ? stockedProduct.merchant_product.product.quantity_type : 'Unit' }}
													</label>
												</div>

												<div class="form-row" v-show="stockedProduct.vendor_id">
													<label class="col-sm-6 col-form-label font-weight-bold text-md-right">
														Vendor :
													</label>
													<label class="col-sm-6 col-form-label">
														{{ stockedProduct.vendor ? stockedProduct.vendor.name : '--' | capitalize }}
													</label>
												</div>

												<div class="form-row" v-show="stockedProduct.location_id">
													<label class="col-sm-6 col-form-label font-weight-bold text-md-right">
														Location :
													</label>
													<label class="col-sm-6 col-form-label">
														{{ stockedProduct.location ? stockedProduct.location.name : '--' | capitalize }}
													</label>
												</div>

												<div class="form-row" v-show="! stockedProduct.has_variations">
													<label class="col-sm-6 col-form-label font-weight-bold text-right">
														Buying Price :
													</label>
													<label class="col-sm-6 col-form-label">
														{{ stockedProduct.unit_buying_price }}
														{{ general_settings.official_currency_name || 'BDT' | capitalize }}
													</label>
												</div>

												<div class="form-row" v-show="stockedProduct.has_serials">
													<label class="col-6 col-form-label font-weight-bold text-md-right">
														Serials :
													</label>
													<div class="col-6 col-form-label">
														<ol 
															v-if="stockedProduct.has_serials && stockedProduct.hasOwnProperty('serials') && stockedProduct.serials.length"
														>
															<li v-for="(stockedProductSerial, stockedProductIndex) in stockedProduct.serials">
																{{ stockedProductSerial.serial_no }}

																<span :class="[stockedProductSerial.has_dispatched ? 'badge badge-danger' : stockedProductSerial.has_requisitions ? 'badge badge-warning' : '']">
																	{{ stockedProductSerial.has_dispatched ? 'Dispatched' : stockedProductSerial.has_requisitions ? 'Requested' : '' }}
																</span>

																<i class="fa fa-print fa-lg text-danger" aria-hidden="true" @click="printStockCode([ { has_serials : stockedProduct.has_serials, has_variations : stockedProduct.has_variations, stock_code : stockedProduct.stock_code, merchant_product : stockedProduct.merchant_product, serials : [ { serial_no : stockedProductSerial.serial_no } ] } ])" v-tooltip.bottom-end="'Print'"></i>

																<span v-show="(stockedProductIndex + 1) < stockedProduct.serials.length">
																	, 
																</span> 
															</li>	
														</ol>
														
														<div class="form-row" v-if="stockedProduct.hasOwnProperty('variations') && stockedProduct.variations.length">
															<div 
																class="col-md-12" 
																v-for="(stockedProductVariation, stockedProductVariationIndex) in stockedProduct.variations" 
																:key="'stock-product-index-' + stockedProductIndex + '-stocked-product-' + stockedProduct.id + 'product-variation-index-' + stockedProductVariationIndex + '-B'"
															>
																<div class="form-row">
																	<label class="col-form-label font-weight-bold text-right">
																		{{ stockedProductVariation.variation.name | capitalize }} |
																	</label>

																	<label class="col-form-label">
																		<!-- 
																		{{ stockedProductVariation.stock_quantity }}
																		{{ stockedProduct.merchant_product ? stockedProduct.merchant_product.product.quantity_type : 'Unit'}} 
																		-->
																		<ol 
																			v-if="stockedProduct.has_serials && stockedProductVariation.serials.length"
																		>
																			<li v-for="(stockedProductVariationSerial, stockedProductVariationIndex) in stockedProductVariation.serials">
																				{{ stockedProductVariationSerial.serial_no }}
																				
																				<span :class="[stockedProductVariationSerial.has_dispatched ? 'badge badge-danger' : stockedProductVariationSerial.has_requisitions ? 'badge badge-warning' : '']">
																					{{ stockedProductVariationSerial.has_dispatched ? 'Dispatched' : stockedProductVariationSerial.has_requisitions ? 'Requested' : '' }}
																				</span>

																				<i class="fa fa-print fa-lg text-danger" aria-hidden="true" @click="printStockCode([ { has_serials : stockedProduct.has_serials, has_variations : stockedProduct.has_variations, merchant_product : stockedProduct.merchant_product, variations : [ { stock_code : stockedProductVariation.stock_code, variation : stockedProductVariation.variation, serials : [ { serial_no : stockedProductVariationSerial.serial_no } ] } ] } ])" v-tooltip.bottom-end="'Print'"></i>

																				<span v-show="(stockedProductVariationIndex + 1) < stockedProductVariation.serials.length">
																					, 
																				</span> 
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
																		{{ stockedVariation.available_quantity }}
																	</label>
																</div>
																-->
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="tab-pane" id="product-address" role="tabpanel">
										<div 
											class="form-row" 
											v-if="singleStockData.hasOwnProperty('products') && singleStockData.products.length"
										>
											<!-- <h2 class="mx-auto mb-4 lead">Address Detail</h2> -->

											<div class="col-md-12">
												<div class="form-row">
													<label class="col-sm-4 col-form-label font-weight-bold text-md-right">
														Address Detail :
													</label>
													<label class="col-sm-8 col-form-label">
														{{ singleStockData.warehouse ? singleStockData.warehouse.name : '' }}
													</label>
												</div>
											</div>

											<div 
												class="col-md-12" 
												v-for="(stockedProduct, stockedProductIndex) in singleStockData.products" 
												:key="'stocked-product-index-' + stockedProductIndex + '-stocked-product-' + stockedProduct.id + '-B'"
											>
												<div class="form-row">
													<label class="col-sm-4 col-form-label font-weight-bold text-right">
														{{ stockedProduct.merchant_product && stockedProduct.merchant_product.product ? stockedProduct.merchant_product.product.name : 'Product Name' | capitalize }} ({{ stockedProduct.merchant_product && stockedProduct.merchant_product.manufacturer ? stockedProduct.merchant_product.manufacturer.name : 'Own Product' | capitalize }})
													</label>
												</div>

												<div 
													class="form-row" 
													v-if="stockedProduct.hasOwnProperty('addresses') && stockedProduct.addresses.length"
												>
													<div class="col-sm-12">
														<div class="form-row">
															<div 
																class="col-md-6 ml-auto" 
																v-for="(stockAddress, stockAddressIndex) in stockedProduct.addresses" 
																:key="'stocked-product-index-' + stockedProductIndex + '-stocked-product-' + stockedProduct.id + '-stock-address-' + stockAddress.type + stockAddressIndex"
															>
																<div 
																	class="card" 
																	v-if="stockAddress.hasOwnProperty('type') && stockAddress.type.includes('containers')"
																>
																	<div 
																		class="card-body" 
																		v-for="containerAddress in stockAddress.containers" 
																		:key="'stocked-product-index-' + stockedProductIndex + '-stocked-product-' + stockedProduct.id + '-container-address-' + containerAddress.id + 'address-index-' + stockAddressIndex + '-stock-id-' + stockedProduct.id"
																	>
																		<h6>Container Address</h6>

																		<!-- 
																		<div class="form-row">
																			<label class="col-6 col-form-label font-weight-bold text-md-right">
																				Warehouse :
																			</label>
																			<label class="col-6 col-form-label">
																				{{ containerAddress.warehouse_container ? $options.filters.capitalize(containerAddress.warehouse_container.warehouse.name) : 'NA' }}
																			</label>
																		</div> 
																		-->

																		<div class="form-row">
																			<label class="col-6 col-form-label font-weight-bold text-md-right">
																				Container Type :
																			</label>
																			<label class="col-6 col-form-label">
																				{{ containerAddress.warehouse_container ? $options.filters.capitalize(containerAddress.warehouse_container.container.name) : 'NA' }}
																			</label>
																		</div>

																		<div class="form-row">
																			<label class="col-6 col-form-label font-weight-bold text-md-right">
																				Container # :
																			</label>
																			<label class="col-6 col-form-label">
																				{{ containerAddress.name.substring(containerAddress.name.lastIndexOf("-") + 1) }}
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
																			<label class="col-6 col-form-label font-weight-bold text-md-right">
																				Container Type :
																			</label>
																			<label class="col-6 col-form-label">
																				{{ stockAddress.container.warehouse_container.container.name | capitalize }}
																			</label>
																		</div>

																		<div class="form-row">
																			<label class="col-6 col-form-label font-weight-bold text-md-right">
																				Container # :
																			</label>
																			<label class="col-6 col-form-label">
																				{{ stockAddress.container.name.substring(stockAddress.container.name.lastIndexOf("-")+1) }}
																			</label>
																		</div>

																		<div 
																			class="form-row"
																		>
																			<label class="col-6 col-form-label font-weight-bold text-md-right">
																				Shelf # :
																			</label>
																			<label class="col-6 col-form-label">
																				<ul id="shelf-addresses">
																					<li 
																						v-for="shelfAddress in stockAddress.container.shelves" 
																						:key="'stocked-product-index-' + stockedProductIndex + '-stocked-product-' + stockedProduct.id + '-shelf-address-' + shelfAddress.id"
																					>

																						{{ /* shelfAddress.name.substring(shelfAddress.name.lastIndexOf("-")+1) */ 
																							shelfAddress.name
																						}}
																						
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
																			<label class="col-6 col-form-label font-weight-bold text-md-right">
																				Container Type :
																			</label>
																			<label class="col-6 col-form-label">
																				{{ stockAddress.container.warehouse_container.container.name | capitalize }}
																			</label>
																		</div>

																		<div class="form-row">
																			<label class="col-6 col-form-label font-weight-bold text-md-right">
																				Container # :
																			</label>
																			<label class="col-6 col-form-label">
																				{{ stockAddress.container.name.substring(stockAddress.container.name.lastIndexOf("-") + 1) }}
																			</label>
																		</div>

																		<div class="form-row">
																			<label class="col-6 col-form-label font-weight-bold text-md-right">
																				Shelf # :
																			</label>
																			<label class="col-6 col-form-label">
																				{{ /*stockAddress.container.shelf.name.substring(stockAddress.container.shelf.name.lastIndexOf("-")+1)*/
																					stockAddress.container.shelf.name
																				}}
																			</label>
																		</div>

																		<div class="form-row">
																			<label class="col-6 col-form-label font-weight-bold text-md-right">
																				Unit # :
																			</label>
																			<label class="col-6 col-form-label">

																				<ul id="unit-addresses">
																					<li 
																						v-for="unitAddress in stockAddress.container.shelf.units" 
																						:key="'stocked-product-index-' + stockedProductIndex + '-stocked-product-' + stockedProduct.id + '-unit-address-' + unitAddress.id"
																					>

																						{{ /*unitAddress.name.substring(unitAddress.name.lastIndexOf("-")+1)*/
																							unitAddress.name 
																						}}
																						
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
													<div class="col-md-12 text-center">
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
								:src="'/' + general_settings.logo" 
								:alt="general_settings.app_name + ' Logo'" 
								width="60px"
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
									{{ getFullName(singleStockData.merchant) | capitalize }}
								</label>
							</div>
						</div>
					</div>

					<div class="form-row" v-if="singleStockData.has_approval">
						<label class="col-6 col-form-label font-weight-bold">
							Stocked Products :
						</label>

						<div class="col-12">
							<table class="table table-striped table-bordered nowrap text-center">
								<thead>
									<tr>
										<th>Name</th>
										<th>Variation</th>
										<th>Stock Qty</th>
										<!-- 
										<th>Buying Price ({{ general_settings.official_currency_name || 'BDT' | capitalize }})</th>
										-->
										<th>Serials</th>
										<!-- <th>Total</th> -->
									</tr>
								</thead>

								<tbody>
									<div 
										v-for="(stockedProduct, stockedProductIndex) in singleStockData.products" 
										:key="'printing-stock-product-index-' + stockedProductIndex + '-stocked-product-' + stockedProduct.id + '-B'"
									>
										<div v-if="! stockedProduct.has_variations">
											<tr>
												<td>
													{{ (stockedProduct.merchant_product && stockedProduct.merchant_product.product) ? stockedProduct.merchant_product.product.name : 'Product' | capitalize }}
													({{ (stockedProduct.merchant_product && stockedProduct.merchant_product.manufacturer) ? stockedProduct.merchant_product.manufacturer.name : 'Own Product' | capitalize }})

													<p class="mt-2 mb-1" v-show="stockedProduct.manufactured_at">
														Mfg Date : {{ stockedProduct.manufactured_at }}
													</p>

													<p v-show="stockedProduct.expired_at">
														Exp Date : {{ stockedProduct.expired_at }}
													</p>
												</td>

												<td>--</td>

												<td>
													{{ stockedProduct.stock_quantity  }}
													{{ (stockedProduct.merchant_product && stockedProduct.merchant_product.product) ? stockedProduct.merchant_product.product.quantity_type : 'Unit' }}
												</td>

												<!-- 
												<td>
													{{ stockedProduct.unit_buying_price }}
													{{ general_settings.official_currency_name || 'BDT' | capitalize }}
												</td> 
												-->

												<td>
													<ol 
														v-if="stockedProduct.has_serials && stockedProduct.hasOwnProperty('serials') && stockedProduct.serials.length"
													>
														<li v-for="(productSerial, productSerialIndex) in stockedProduct.serials">
															
															{{ productSerial.serial_no }}

															<span :class="[productSerial.has_dispatched ? 'badge badge-danger' : productSerial.has_requisitions ? 'badge badge-warning' : '']">
																{{ productSerial.has_dispatched ? 'Dispatched' : productSerial.has_requisitions ? 'Requested' : '' }}
															</span>

															<span v-show="(productSerialIndex + 1) < stockedProduct.serials.length">, </span> 
														</li>	
													</ol>

													<span v-else>
														--
													</span>
												</td>
											</tr>
										</div>
										
										<div v-else-if="stockedProduct.has_variations && stockedProduct.hasOwnProperty('variations') && stockedProduct.variations.length">
											<tr 
												v-for="(stockedProductVariation, stockedProductVariationIndex) in stockedProduct.variations" 
												:key="'printing-stock-product-index-' + stockedProductIndex + '-stocked-product-' + stockedProduct.id + 'product-variation-index-' + stockedProductVariationIndex + '-B'"
											>	
												<td>
													{{ (stockedProduct.merchant_product && stockedProduct.merchant_product.product) ? stockedProduct.merchant_product.product.name : 'Product' | capitalize }}
													({{ (stockedProduct.merchant_product && stockedProduct.merchant_product.manufacturer) ? stockedProduct.merchant_product.manufacturer.name : 'Own Product' | capitalize }})
												</td>

												<td>
													{{ stockedProductVariation.variation.name | capitalize }}
													 
													<p class="mb-1 mt-2" v-show="stockedProductVariation.manufactured_at">
														Mfg Date : {{ stockedProductVariation.manufactured_at }}
													</p>

													<p v-show="stockedProductVariation.expired_at">
														Exp Date : {{ stockedProductVariation.expired_at }}
													</p>
												</td>
												
												<td>

													{{ stockedProductVariation.stock_quantity }} 
													{{ (stockedProduct.merchant_product && stockedProduct.merchant_product.product) ? stockedProduct.merchant_product.product.quantity_type : 'Unit' }}
												</td>

												<!-- 
												<td>
													{{ stockedProductVariation.unit_buying_price }}
													{{ general_settings.official_currency_name || 'BDT' | capitalize }}
												</td>
												 -->

												<td>
													<ol 
														v-if="stockedProduct.has_serials && stockedProductVariation.hasOwnProperty('serials') && stockedProductVariation.serials.length"
													>
														<li v-for="(variationSerial, stockedVariationIndex) in stockedProductVariation.serials">
															
															{{ variationSerial.serial_no }}

															<span :class="[variationSerial.has_dispatched ? 'badge badge-danger' : variationSerial.has_requisitions ? 'badge badge-warning' : '']">
																{{ variationSerial.has_dispatched ? 'Dispatched' : variationSerial.has_requisitions ? 'Requested' : '' }}
															</span>

															<span v-show="(stockedVariationIndex + 1) < stockedProductVariation.serials.length">, </span> 
														</li>	
													</ol>

													<span v-else>
														--
													</span>
												</td>
											</tr>
										</div>
									</div>
								</tbody>
							</table>							
						</div>
					</div>

					<!-- 
					<div 
						class="form-row mt-2" 
						v-if="singleStockData.hasOwnProperty('products') && singleStockData.products.length"
					>
						<label class="col-12 col-form-label font-weight-bold text-md-right">
							Address Detail :
						</label>

						<div 
							class="card" 
							v-for="(stockedProduct, stockedProductIndex) in singleStockData.products" 
							:key="'stocked-product-index-' + stockedProductIndex + '-stocked-product-' + stockedProduct.id + '-s'"
						>
							<label class="card-header">
								{{ stockedProduct.merchant_product && stockedProduct.merchant_product.product ? stockedProduct.merchant_product.product.name : 'Product Name' | capitalize }} ({{ stockedProduct.merchant_product && stockedProduct.merchant_product.manufacturer ? stockedProduct.merchant_product.manufacturer.name : 'Own Product' | capitalize }})
							</label>

							<div 
								class="card-body" 
								v-if="stockedProduct.hasOwnProperty('addresses') && stockedProduct.addresses.length"
							>
								<div class="form-row">
									<div 
										class="col-md-6 ml-auto" 
										v-for="(stockAddress, stockAddressIndex) in stockedProduct.addresses" 
										:key="'stocked-product-index-' + stockedProductIndex + '-stocked-product-' + stockedProduct.id + '-stock-address-' + stockAddress.type + stockAddressIndex"
									>
										<div 
											class="card" 
											v-if="stockAddress.hasOwnProperty('type') && stockAddress.type.includes('containers')"
										>
											<div 
												class="card-body" 
												v-for="containerAddress in stockAddress.containers" 
												:key="'stocked-product-index-' + stockedProductIndex + '-stocked-product-' + stockedProduct.id + '-container-address-' + containerAddress.id + 'address-index-' + stockAddressIndex + '-stock-id-' + stockedProduct.id"
											>
												<h6>Container Address</h6>

												<div class="form-row">
													<label class="col-6 col-form-label font-weight-bold text-md-right">
														Warehouse :
													</label>
													<label class="col-6 col-form-label">
														{{ containerAddress.warehouse_container ? $options.filters.capitalize(containerAddress.warehouse_container.warehouse.name) : 'NA' }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-6 col-form-label font-weight-bold text-md-right">
														Container Type :
													</label>
													<label class="col-6 col-form-label">
														{{ containerAddress.warehouse_container ? $options.filters.capitalize(containerAddress.warehouse_container.container.name) : 'NA' }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-6 col-form-label font-weight-bold text-md-right">
														Container # :
													</label>
													<label class="col-6 col-form-label">
														{{ containerAddress.name.substring(containerAddress.name.lastIndexOf("-")+1) }}
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
													<label class="col-6 col-form-label font-weight-bold text-md-right">
														Container Type :
													</label>
													<label class="col-6 col-form-label">
														{{ stockAddress.container.warehouse_container.container.name | capitalize }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-6 col-form-label font-weight-bold text-md-right">
														Container # :
													</label>
													<label class="col-6 col-form-label">
														{{ stockAddress.container.name.substring(stockAddress.container.name.lastIndexOf("-")+1) }}
													</label>
												</div>

												<div 
													class="form-row"
												>
													<label class="col-6 col-form-label font-weight-bold text-md-right">
														Shelf # :
													</label>
													<label class="col-6 col-form-label">

														<ul id="shelf-addresses">
															<li 
																v-for="shelfAddress in stockAddress.container.shelves" 
																:key="'stocked-product-index-' + stockedProductIndex + '-stocked-product-' + stockedProduct.id + '-shelf-address-' + shelfAddress.id"
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
													<label class="col-6 col-form-label font-weight-bold text-md-right">
														Container Type :
													</label>
													<label class="col-6 col-form-label">
														{{ stockAddress.container.warehouse_container.container.name | capitalize }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-6 col-form-label font-weight-bold text-md-right">
														Container # :
													</label>
													<label class="col-6 col-form-label">
														{{ stockAddress.container.name.substring(stockAddress.container.name.lastIndexOf("-")+1) }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-6 col-form-label font-weight-bold text-md-right">
														Shelf # :
													</label>
													<label class="col-6 col-form-label">
														{{ stockAddress.container.shelf.name.substring(stockAddress.container.shelf.name.lastIndexOf("-")+1) }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-6 col-form-label font-weight-bold text-md-right">
														Unit # :
													</label>
													<label class="col-6 col-form-label">

														<ul id="unit-addresses">
															<li 
																v-for="unitAddress in stockAddress.container.shelf.units" 
																:key="'stocked-product-index-' + stockedProductIndex + '-stocked-product-' + stockedProduct.id + '-unit-address-' + unitAddress.id"
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

							<div class="card-body" v-else>
								<div class="form-row">
									<div class="col-md-12 text-center">
										<p class="text-danger">
											No Space Found.
										</p>
									</div>
								</div>
							</div>
						</div>
					</div> 
					-->
				</div>
			</div>
		</div>

		<div id="stockCodeToPrint" class="d-none">
			<div 
				v-for="(stockedProduct, stockedProductIndex) in stockProductsToPrint"
				:key="'printing-product-index-' + stockedProductIndex + '-product-' + stockedProduct.id + '-printing-product-serial-index-' + stockedProductIndex + '-product-' + stockedProduct.id + '-c'"
			> 
				<div v-show="! stockedProduct.has_serials && ! stockedProduct.has_variations">
					<div 
						style="border:1px dotted; text-align:center;" 
						v-for="productQuantity in stockedProduct.stock_quantity" 
						:style="productQuantity / 4 >= 1 && productQuantity % 4 == 1 ? 'display: block; page-break-before: always; position: relative;' : ''" 
						:key="'printing-product-quantity-index-' + productQuantity + '-product-' + stockedProduct.id"
					>
						<h6 style="margin-bottom:0px; margin-top:0px;font-size: 80%;">
							{{ (stockedProduct.merchant_product && stockedProduct.merchant_product.product) ? stockedProduct.merchant_product.product.name : '' | capitalize }} Stock-Code :
						</h6>

						<svg  
					  		class="barcode" 
					        :jsbarcode-value="stockedProduct.stock_code" 
					        jsbarcode-width="1" 
					        jsbarcode-height="15" 
					        jsbarcode-margin="2" 
					        jsbarcode-textmargin="0" 
					        jsbarcode-textalign="center" 
					        jsbarcode-fontsize="10" 
					        style='display: block;width: 100%;'
					    >
					    </svg>
					</div>
				</div>

				<div v-show="! stockedProduct.has_serials && stockedProduct.has_variations">
					<div 
						v-for="(productVariation, productVariationIndex) in stockedProduct.variations"
						:key="'printing-product-variation-index-' + productVariationIndex + '-variation-' + productVariation.id"
					>
						<div 
							style="border:1px dotted; text-align:center;" 
							v-for="productVariationQuantityIndex in productVariation.stock_quantity" 
							:style="productVariationQuantityIndex / 4 >= 1 && productVariationQuantityIndex % 4 == 1 ? 'display: block; page-break-before: always; position: relative;' : ''" 
							:key="'printing-product-variation-index-' + productVariationIndex + '-variation-' + productVariation.id + '-quantity-' + productVariationQuantityIndex"
						>
							<h6 style="margin-bottom:0px; margin-top:0px;font-size: 80%;">
								<!-- {{ productVariationQuantityIndex }} -->

								{{ (productVariationQuantityIndex + '.' + productVariation.variation ? productVariation.variation.name : '') | capitalize }} Stock-Code :
							</h6>

							<svg  
						  		class="barcode" 
						        :jsbarcode-value="productVariation.stock_code" 
						        jsbarcode-width="1" 
						        jsbarcode-height="15" 
						        jsbarcode-margin="2" 
						        jsbarcode-textmargin="0" 
						        jsbarcode-textalign="center" 
						        jsbarcode-fontsize="10" 
						        style='display: block;width: 100%;'
						    >
						    </svg>
						</div>
					</div>
				</div>

				<div v-show="stockedProduct.has_serials && ! stockedProduct.has_variations">
					<div 
						style="border:1px dotted;" 
						v-for="(productSerial, productSerialIndex) in stockedProduct.serials" 
						:style="(productSerialIndex + 1) / 2 >= 1 && (productSerialIndex + 1) % 2 == 1 ? 'display: block; page-break-before: always; position: relative;' : ''" 
						:key="'printing-product-serial-index-' + productSerialIndex + '-product-' + productSerial.id"
					>
						<div style="text-align:center;">
							<h6 style="margin-bottom:0px; margin-top:0px;font-size: 80%;">
								{{ (stockedProduct.merchant_product && stockedProduct.merchant_product.product) ? stockedProduct.merchant_product.product.name : '' | capitalize }} Stock-Code :
							</h6>

							<svg  
						  		class="barcode" 
						        :jsbarcode-value="stockedProduct.stock_code" 
						        jsbarcode-width="1" 
						        jsbarcode-height="15" 
						        jsbarcode-margin="3" 
						        jsbarcode-textmargin="0" 
						        jsbarcode-textalign="center" 
						        jsbarcode-fontsize="10" 
						        style='display: block;width: 100%;'
						    >
						    </svg>
						</div>

						<div style="text-align:center;">
							<h6 style="margin-bottom:0px; margin-top:0px;font-size: 80%;">
								Serial :
							</h6>

							<svg  
						  		class="barcode" 
						        :jsbarcode-value="productSerial.serial_no" 
						        jsbarcode-width="1" 
						        jsbarcode-height="15" 
						        jsbarcode-margin="3" 
						        jsbarcode-textmargin="0" 
						        jsbarcode-textalign="center" 
						        jsbarcode-fontsize="10" 
						        style='display: block;width: 100%;'
						    >
						    </svg>
						</div>
					</div>
				</div>

				<div v-show="stockedProduct.has_serials && stockedProduct.has_variations">
					<div 
						v-for="(productVariation, productVariationIndex) in stockedProduct.variations"
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
							        jsbarcode-height=252
							        last-char= ">"
							    >    
						        </svg> 
							</label>
						</div> 
						-->

						<div 
							style="border:1px dotted;" 
							v-for="(productVariationSerial, productVariationSerialIndex) in productVariation.serials" 
							:style="(productVariationSerialIndex + 1) / 2 >= 1 && (productVariationSerialIndex + 1) % 2 == 1 ? 'display: block; page-break-before: always; position: relative;' : ''" 
							:key="'printing-product-variation-index-' + productVariationIndex + '-variation-' + productVariation.id + '-serial-index-' + productVariationSerialIndex + '-serial-' + productVariationSerial.id" 
						>
							<div style="text-align:center;">
								<h6 style="margin-bottom:0px; margin-top:0px;font-size: 80%;">
									<!-- {{ productVariationSerialIndex }} -->

									{{ (productVariationSerialIndex + '.' + productVariation.variation ? productVariation.variation.name : '') | capitalize }} Stock-Code :
								</h6>

								<svg  
							  		class="barcode" 
							        :jsbarcode-value="productVariation.stock_code" 
							        jsbarcode-width="1" 
							        jsbarcode-height="15" 
							        jsbarcode-margin="3" 
							        jsbarcode-textmargin="0" 
							        jsbarcode-textalign="center" 
							        jsbarcode-fontsize="10" 
							        style='display: block;width: 100%;'
							    >
							    </svg>
							</div>

							<div style="text-align:center;">
								<h6 style="margin-bottom:0px; margin-top:0px;font-size: 80%;">
									Serial :
								</h6>

								<svg  
							  		class="barcode" 
							        :jsbarcode-value="productVariationSerial.serial_no" 
							        jsbarcode-width="1" 
							        jsbarcode-height="15" 
							        jsbarcode-margin="3" 
							        jsbarcode-textmargin="0" 
							        jsbarcode-textalign="center" 
							        jsbarcode-fontsize="10" 
							        style='display: block;width: 100%;'
							    >
							    </svg>
							</div>
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

	export default {

	    components: { 
			multiselect : Multiselect,
		},

	    data() {

	        return {

	        	step : 1,
	        	error : '',
	        	// search : '',
    			perPage : 10,
	        	loading : false,

	        	createMode : true,
	        	submitForm : true,
	        	formSubmitted : false,

	        	allStocks : [],

	        	productNewSerial : '',
	        	variationNewSerial : [],

	        	// allContainers : [],
	        	allProducts : [],
	        	allMerchants : [],
	        	allWarehouses : [],
	        	allDealtEmptyWarehouses : [],
	        	
	        	emptyContainers : [],
	        	emptyShelfContainers : [],
	        	emptyUnitContainers : [],

	        	emptyShelves : [],
	        	emptyUnitShelves : [],

	        	emptyUnits : [],

	        	allVendors : [],
	        	allLocations : [],

	        	pagination: {
		        	current_page: 1
		      	},

	        	singleStockData : {
					
	        		// invoice_no:null,
	        		// keeper : {},
	        		// has_approval : false,
	        		// approver : {},
	        		// warehouse_id : null,
	        		// created_at : null,
	        		// updated_at : null,

	        		merchant : {},

	        		warehouse : {},

	        		products : [
	        			{
	        				// stock_quantity : null,
	        				// available_quantity : null,
	        				// has_variations : null,
			        		// variations : this.productMerchant.hasOwnProperty('variations') && this.productMerchant.variations.length ? JSON.parse(JSON.stringify(this.productMerchant.variations)) : [],
	        				// has_serials : null,
	        				// serials : [],
			        		// merchant_product_id : this.productMerchant.id,
							
							addresses : [
								{},
							],
							
	        			}
	        		],
	        	},

	        	stockProductsToPrint : [],

	        	errors : {
					// mercahnt : null,
					// warehouse : null,
					products : [
						{
							addresses : [
								{}
							]
						}
					],
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

					"Merchant": {
						field: "merchant",
						callback: (merchant) => {
							
							return this.$options.filters.capitalize(`${merchant.first_name} ${merchant.last_name} (${merchant.user_name})`);
							
						},
					},

					/*
					"Products": {
						
						callback: (object) => {
							let productNames = '';

							object.products.forEach(
								stockedProduct => {

									productNames += this.$options.filters.capitalize(stockedProduct.merchant_product.product.name) + '(' + this.$options.filters.capitalize(stockedProduct.merchant_product.manufacturer ? stockedProduct.merchant_product.manufacturer.name : 'Own Product') + ')' + "\n";

									// productNames += this.$options.filters.capitalize(`${stockedProduct.merchant_product.product.name} (${stockedProduct.merchant_product.manufacturer} ? ${stockedProduct.merchant_product.manufacturer.name} : Own Product)`);

								}
							);

							return productNames;
							
						},
					},
					*/

					"Stocked Products": {
						
						callback: (object) => {

							let stockDetailToReturn = '';

							object.products.forEach(
								(stockedProduct, stockedProductIndex) => {

									stockDetailToReturn += "Product: " + this.$options.filters.capitalize(stockedProduct.merchant_product.product.name) + '(' + this.$options.filters.capitalize(stockedProduct.merchant_product.manufacturer ? stockedProduct.merchant_product.manufacturer.name : 'Own Product') + ')' + "\n";

									stockDetailToReturn += `Qty:${stockedProduct.stock_quantity} ${stockedProduct.merchant_product.product.quantity_type}
									`;

									if (stockedProduct.has_serials && ! stockedProduct.has_variations && stockedProduct.serials.length) {

										stockDetailToReturn +=  this.$options.filters.capitalize(`${stockedProduct.merchant_product.product.name} Serials:
										`);

										stockedProduct.serials.forEach(
								
											(stockedProductSerial, stockedProductSerialIndex) => {

												if (stockedProductSerial.serial_no) {

													stockDetailToReturn +=  (`${stockedProductSerialIndex +1}. ${stockedProductSerial.serial_no}` + (stockedProductSerial.has_dispatched ? ' (Dispatched)' : '') + "\n");

												}

											}
											
										);

									}

									if (stockedProduct.has_variations && stockedProduct.hasOwnProperty('variations') && stockedProduct.variations.length) {

										stockDetailToReturn +=  `Variations:
										`;

										stockedProduct.variations.forEach(
							
											(stockedProductVariation, stockedProductVariationIndex) => {

												if (stockedProductVariation.hasOwnProperty('variation') && stockedProductVariation.variation.hasOwnProperty('name')) {

													stockDetailToReturn +=  this.$options.filters.capitalize(`Variation ${stockedProductVariationIndex+1} : ${stockedProductVariation.variation.name}, 
														Qty: ${stockedProductVariation.stock_quantity}  ${stockedProduct.merchant_product.product.quantity_type}
													`);

												}

												if (stockedProduct.has_serials && stockedProductVariation.hasOwnProperty('serials') && stockedProductVariation.serials.length) {

													stockDetailToReturn +=  this.$options.filters.capitalize(`${stockedProductVariation.variation.name} Serials:
													`);

													stockedProductVariation.serials.forEach(
								
														(stockedProductVariationSerial, stockedProductVariationSerialIndex) => {

															if (stockedProductVariationSerial.serial_no) {

																stockDetailToReturn +=  (`${stockedProductVariationSerialIndex +1}. ${stockedProductVariationSerial.serial_no}` + (stockedProductVariationSerial.has_dispatched ? ' (Dispatched)' : '') + "\n");

															}

														}
														
													);

												}

												stockDetailToReturn +=  "\n"

											}
											
										);

									}

									stockDetailToReturn +=  "\n"

								}

							);

							return stockDetailToReturn;

						},

					},

					"Stock": {
						callback: (object) => {
							var stockInfosToReturn = '';

							stockInfosToReturn += 'Warehouse:' + (object.hasOwnProperty('warehouse') ? this.$options.filters.capitalize(object.warehouse.name) : 'NA') + '(' + object.created_at + ')' + "\n";

							stockInfosToReturn += 'Keeper:' + this.$options.filters.capitalize(object.keeper.first_name + ' ' + object.keeper.last_name) + '\n'

							return stockInfosToReturn;
							
						},
					},

					"Approval": {
						callback: (object) => {
							
							if (object.has_approval==1) {
								return "Approved (" + object.updated_at + ').' + "\n" + 'Approver:' + (object.approver ? this.$options.filters.capitalize(object.approver.first_name + ' ' + object.approver.last_name) : '');
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

					this.fetchAllStocks();

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

					this.fetchAllStocks();

				}
				else {

					this.searchData();
						
				}

			},

			'searchAttributes.dateTo' : function(val){
				
				this.pagination.current_page = 1; 

				if (this.searchAttributes.search==='' && ! this.searchAttributes.dateTo && ! this.searchAttributes.dateFrom) {

					this.fetchAllStocks();

				}
				else {

					this.searchData();
						
				}

			},

		},
		
		filters: {

			capitalize: function (value) {
				if (!value) return ''

				const words = value.toString().split(" ");

				for (let i = 0; i < words.length; i++) {
				    
					if (words[i]) {

				    	words[i] = words[i][0].toUpperCase() + words[i].substr(1);

					}
				    
				}

				return words.join(" ");
			}

		},

		created() {
			
			// this.fetchMerchantWarehouseAllSpaces();

			// this.fetchWarehouseAllContainers();

			this.fetchAllStocks();

			this.fetchAllMerchants();

			this.fetchAllWarehouses();
			
			// this.resetErrorObject();

			// this.configureProductErrorsWithPropData();

		},
		
		methods: {

			fetchAllStocks() {

				this.error = '';
				this.loading = true;
				this.allStocks = [];
				this.searchAttributes.search = '';
				
				axios
					.get('/api/stocks/' + this.perPage + "?page=" + this.pagination.current_page)
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
			fetchAllWarehouses() {

				this.error = '';
				this.loading = true;
				this.allWarehouses = [];
				this.searchAttributes.search = '';

				if (! this.userHasPermissionTo('view-warehouse-index')) {

					this.error = 'You do not have permission to view warehouse';
					return;
					
				}
				
				axios
					.get('/api/warehouses/')
					.then(response => {
						if (response.status == 200) {
							// console.log(response);
							this.allWarehouses = response.data;
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
			fetchAllMerchants() {

				this.error = '';
				this.loading = true;
				this.allMerchants = [];
				this.searchAttributes.search = '';

				if (! this.userHasPermissionTo('view-merchant-index')) {

					this.error = 'You do not have permission to view merchants';
					return;
					
				}
				
				axios
					.get('/api/merchants/')
					.then(response => {
						if (response.status == 200) {
							// console.log(response);
							this.allMerchants = response.data;
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
			fetchAllVendors() {

				this.error = '';
				this.loading = true;
				this.allVendors = [];
				this.searchAttributes.search = '';
				
				axios
					.get('/api/vendors/')
					.then(response => {
						if (response.status == 200) {
							// console.log(response);
							this.allVendors = response.data;
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
			fetchAllLocations() {

				this.error = '';
				this.loading = true;
				this.allLocations = [];
				this.searchAttributes.search = '';
				
				axios
					.get('/api/locations/')
					.then(response => {
						if (response.status == 200) {
							// console.log(response);
							this.allLocations = response.data;
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

			fetchMerchantWarehouseAllSpaces() {
				
				this.error = '';
				this.loading = true;
				this.searchAttributes.search = '';
				this.allDealtEmptyWarehouses = [];

				this.validateFormInput('merchant');
				this.validateFormInput('warehouse');

				if (this.errors.warehouse || this.errors.merchant) {

					return;

				}

				axios
					.get('/api/dealt-warehouses/' + this.singleStockData.merchant.id + '/' + this.singleStockData.warehouse.id + '/all')
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
						this.resetWarehouseSpaces();
					});

			},
			fetchMerchantAllProducts() {
				
				this.error = '';
				this.loading = true;
				this.searchAttributes.search = '';
				this.allProducts = [];

				this.validateFormInput('merchant');

				if (this.errors.merchant) {

					return;

				}

				else if (! this.userHasPermissionTo('view-merchant-product-index')) {

					this.error = 'You do not have permission to view merchant-products';
					return;

				}

				axios
					.get('/api/merchant-all-products/' + this.singleStockData.merchant.id)
					.then(response => {
						if (response.status == 200) {
							
							this.allProducts = response.data.data;
					
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
								this.errors.addresses.push({});
							}
						);

					// }
					
						else {

							this.product.addresses.forEach(
								(productAddress, index) => {
									this.errors.addresses.push({});
								}
							);

						}
					

				}
				else {

					this.errors.addresses = [
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
    		showStockDetails(object) {		
				// this.singleStockData = { ...object };
				this.singleStockData = Object.assign({}, this.singleStockData, object);
				$('#stock-view-modal').modal('show');
			},
			showStockCreateForm() {

				this.step = 1;
				this.createMode = true;
	        	this.submitForm = true;
	        	this.formSubmitted = false;

				// singleStockData configuration
				this.singleStockData = {
					
				 	// invoice_no:null,
	        		// keeper : {},
	        		// has_approval : false,
	        		// approver : {},
	        		// warehouse_id : null,
	        		// created_at : null,
	        		// updated_at : null,

	        		merchant : this.singleStockData.merchant ?? {}, 	// last merchant

	        		warehouse : this.singleStockData.warehouse ?? {},  // last warehouse

	        		products : [
	        			{
	        				// stock_quantity : null,
	        				// available_quantity : null,
	        				// has_variations : null,
			        		// variations : this.productMerchant.hasOwnProperty('variations') && this.productMerchant.variations.length ? JSON.parse(JSON.stringify(this.productMerchant.variations)) : [],
	        				// has_serials : null,
	        				// serials : [],
			        		// merchant_product_id : this.productMerchant.id,
							
							addresses : [
								{},
							],
							
	        			}
	        		],

	        	};

	        	this.errors = {
					// mercahnt : null,
					// warehouse : null,
					products : [
						{
							addresses : [
								{},
							]
						}
					],
				};

				// new errors configuration
				// this.resetErrorObject();

				$('#stock-createOrEdit-modal').modal('show');
			},
			openStockEditForm(object) {

				this.step = 1;
	        	this.submitForm = true;
				this.createMode = false;
	        	this.formSubmitted = false;

	        	this.resetErrorObject(object);
				
				this.singleStockData = JSON.parse(JSON.stringify(object));

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
					.post('/stocks/' + this.perPage, this.singleStockData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Stock has been stored", "Success");

							this.pagination.current_page = 1; 
							this.searchAttributes.search !== '' ? this.searchData() : this.setAvailableContents(response);

							$('#stock-createOrEdit-modal').modal('hide');

							let singleStockUpdatedData = this.allStocks.find(
								stock => stock.id == this.singleStockData.id
							);

							if (typeof singleStockUpdatedData !== 'undefined') {

								// this.$set(this.singleStockData, 'products', singleStockUpdatedData.products);

								this.printStockCode(singleStockUpdatedData.products);

							}
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
						// this.fetchMerchantWarehouseAllSpaces();
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
					.put('/stocks/' + this.singleStockData.id + '/' + this.perPage, this.singleStockData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Stock has been updated", "Success");

							this.pagination.current_page = 1; 
							this.searchAttributes.search !== '' ? this.searchData() : this.setAvailableContents(response);

							$('#stock-createOrEdit-modal').modal('hide');

							let singleStockUpdatedData = this.allStocks.find(
								stock => stock.id == this.singleStockData.id
							);

							if (typeof singleStockUpdatedData !== 'undefined') {

								// this.$set(this.singleStockData, 'products', singleStockUpdatedData.products);

								this.printStockCode(singleStockUpdatedData.products);

							}
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
						// this.printStockCode(this.singleStockData.products);
						// this.fetchMerchantWarehouseAllSpaces();
						// this.fetchWarehouseAllContainers();
					});

			},
			deleteStock(singleAssetData) {
				
				this.formSubmitted = true;

				axios
					.delete('/stocks/' + this.singleStockData.id + '/' + this.perPage, this.singleStockData)
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
						// this.fetchMerchantWarehouseAllSpaces();
						// this.fetchWarehouseAllContainers();
					});

			},
			searchData() {

				this.error = '';
				this.allStocks = [];
				// this.pagination.current_page = 1;
				
				axios
				.post("/api/search-stocks/" + this.perPage + "?page=" + this.pagination.current_page, this.searchAttributes)
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

				if (this.errors.constructor === Object && Object.keys(this.errors).length < 2 && ! this.errorInProductsArray(this.errors.products)) {

					return true;
				
				}

				return false;
		
			},
			errorInProductsArray(array = []) {

				const productError = (product) => {

					return (! product.hasOwnProperty('variations') && Object.keys(product).length > 1) || (product.hasOwnProperty('variations') && Object.keys(product).length > 2) || (product.hasOwnProperty('variations') && this.errorInVariationsArray(product.variations)) || this.errorInAddressesArray(product.addresses)

				}; 

				if (array.length) {
					return array.some(productError);
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
					
					this.validateFormInput('merchant');
					this.validateFormInput('warehouse');

					if (this.errors.constructor === Object && Object.keys(this.errors).length < 2) {

						if (! this.allProducts.length || (this.singleStockData.hasOwnProperty('merchant') && this.allProducts[0].hasOwnProperty('merchant_id') && this.allProducts[0].merchant_id != this.singleStockData.merchant.id)) {	// already loaded / not

							this.fetchMerchantAllProducts();
							this.fetchMerchantWarehouseAllSpaces();

						}
						
						this.submitForm = true;
						this.step++;

					}
					else {
					
						this.submitForm = false;
					
					}

				}
				else if (this.step == 2) {
				
					this.validateFormInput('product');
					this.validateFormInput('product_stock_quantity');
					this.validateFormInput('product_variation_quantity');
					this.validateFormInput('product_variation_total_quantity');

					if (this.errors.constructor === Object && Object.keys(this.errors).length < 2 && ! this.errorInProductsArray(this.errors.products)) {

						this.singleStockData.products.forEach(
						
							(stockedProduct, stockedProductIndex) => {	

								if (stockedProduct.has_serials) {

									if (stockedProduct.has_variations) {

										this.setProductVariationSerialObjects(stockedProduct);

									}
									else {

										this.setProductSerialObjects(stockedProduct);
										
									}							

									// this.step += 1;

								}
								/*
								else {

									this.step += 2;

								}
								*/

							}

						);

						this.submitForm = true;
						
						if (this.stockHasSerial()) {
							
							this.step += 1;		// 3
						
						}
						else {

							if (this.stockHasPerishableProduct()) {

								this.step += 2;		// 4
							
							}
							else {

								if (this.merchantHasPurchaseSupport()) {

									this.step += 3;	 // 5

								}
								else {

									this.step += 4;		// 6

								}

							}
						
						}

						if (this.merchantHasPurchaseSupport() && this.userHasPermissionTo('view-product-asset-index') && (this.allVendors.length == 0 || this.allLocations.length == 0)) {

							this.fetchAllVendors();
							this.fetchAllLocations();

						}
						
						// this.resetWarehouseSpaces();
						// this.fetchWarehouseAllContainers(this.singleStockData.warehouse.id);
					
						/*
						if (this.productMerchant.has_serials) {

							if (this.productMerchant.has_variations) {
								
								this.validateFormInput('product_variation_serial');

							}
							else {
								
								this.validateFormInput('product_serial');

							}

						}
						*/

					}
					else {

						this.submitForm = false;
						
					}

				}
				else if (this.step == 3) {

					// this.validateFormInput('product_serial');
					// this.validateFormInput('product_variation_serial');
					
					this.validateFormInput('product_serials');
					this.validateFormInput('product_variation_serials');

					if (this.errors.constructor === Object && Object.keys(this.errors).length < 2 && ! this.errorInProductsArray(this.errors.products)) {

						this.submitForm = true;

						if (this.stockHasPerishableProduct()) {

							this.step += 1;		// 4
						
						}
						else {

							if (this.merchantHasPurchaseSupport()) {

								this.step += 2;	 // 5

							}
							else {

								this.step += 3;		// 6

							}


						}

					}
					else {

						this.submitForm = false;
						
					}

				}
				else if (this.step == 4) {

					this.validateFormInput('manufacturing_date');
					this.validateFormInput('expiring_date');

					if (this.errors.constructor === Object && Object.keys(this.errors).length < 2 && ! this.errorInProductsArray(this.errors.products)) {

						this.submitForm = true;
						
						if (this.merchantHasPurchaseSupport()) {

							this.step += 1;	 // 5

						}
						else {

							this.step += 2;		// 6

						}

					}
					else {

						this.submitForm = false;
						
					}

				}
				else if (this.step == 5) {

					this.validateFormInput('vendor');
					this.validateFormInput('location');
					this.validateFormInput('unit_buying_price');

					if (this.errors.constructor === Object && Object.keys(this.errors).length < 2 && ! this.errorInProductsArray(this.errors.products)) {

						this.step += 1;
						this.submitForm = true;

					}
					else {

						this.submitForm = false;
						
					}

				}
				else {

					this.submitForm = false;

				}

			},
			stockHasSerial() {

				return this.singleStockData.products.some(stockProduct=>stockProduct.has_serials);

			},
			stockHasPerishableProduct() {

				return this.singleStockData.products.some(
					stockedProduct => stockedProduct.merchant_product && stockedProduct.merchant_product.product && stockedProduct.merchant_product.product.category && stockedProduct.merchant_product.product.category.is_perishable
				)

			},
			merchantHasPurchaseSupport() {

				return this.singleStockData.merchant && this.singleStockData.merchant.support_deal.purchase_support;

			},
			/*
			setProductProperties()
			{	
				this.singleStockData.primary_quantity = this.singleStockData.stock_quantity;

				if (this.singleStockData.hasOwnProperty('variations') && this.singleStockData.variations.length) {

					this.singleStockData.variations.forEach(
						(stockedVariation, stockVariationIndex) => {
							stockedVariation.primary_quantity = stockedVariation.stock_quantity;
						}
					);

				}

				// this.singleStockData.product_id = this.product.id;			

			},
			*/
			resetProductProperties(stockedProductIndex){

				if (this.singleStockData.products.length > stockedProductIndex && this.errors.products.length > stockedProductIndex) {

					this.singleStockData.products[stockedProductIndex].stock_quantity = 0,
					// this.singleStockData.products[stockedProductIndex].available_quantity = 0,

					this.singleStockData.products[stockedProductIndex].addresses = [ {} ];
					
					this.errors.products[stockedProductIndex].addresses = [ {} ];

					if (this.singleStockData.products[stockedProductIndex].merchant_product) {

						let merchantProduct = this.singleStockData.products[stockedProductIndex].merchant_product;
						this.singleStockData.products[stockedProductIndex].merchant_product_id = merchantProduct.id;

						// variations
						if (merchantProduct.has_variations) {

							this.singleStockData.products[stockedProductIndex].has_variations = true;
							this.$set(this.singleStockData.products[stockedProductIndex], 'variations', []);
							this.$set(this.errors.products[stockedProductIndex], 'variations', []);
							
							// this.singleStockData.products[stockedProductIndex].variations = [];
							// this.errors.products[stockedProductIndex].variations = [];

							merchantProduct.variations.forEach(
								
								(merchantProductVariation, merchantProductVariationIndex) => {
									this.errors.products[stockedProductIndex].variations.push({});

									this.singleStockData.products[stockedProductIndex].variations.push(
										{
											id : merchantProductVariation.id,
											// stock_quantity : 0,
											// available_quantity : 0,
											merchant_product_variation_id : merchantProductVariation.id,
											has_serials : merchantProduct.has_serials,
											variation : merchantProductVariation.variation,
											// serials : []
										}
									);
								}

							);

						}
						else {

							this.singleStockData.products[stockedProductIndex].has_variations = false;
							this.$delete(this.singleStockData.products[stockedProductIndex], 'variations');
							this.$delete(this.errors.products[stockedProductIndex], 'variations');							

						}

						// serials
						if (merchantProduct.product.has_serials) {

							this.singleStockData.products[stockedProductIndex].has_serials = true;

							if (this.singleStockData.products[stockedProductIndex].has_variations) {

								this.$delete(this.singleStockData.products[stockedProductIndex], 'serials');
								this.$delete(this.errors.products[stockedProductIndex], 'product_serial');

								this.singleStockData.products[stockedProductIndex].variations.forEach(
									(stockedProductVariation, stockedProductVariationIndex) => { 
										this.$set(stockedProductVariation, 'serials', []);
									}
								);
							
							}
							else {

								this.singleStockData.products[stockedProductIndex].serials = [];
								// this.errors.products[stockedProductIndex].product_serial = null;

							}

						}
						else {

							this.singleStockData.products[stockedProductIndex].has_serials = false;
							this.$delete(this.singleStockData.products[stockedProductIndex], 'serials');
							this.$delete(this.errors.products[stockedProductIndex], 'product_serial');
						
						}

					}

				}

			},
			removeWhiteSpace(properties) {

				if (properties) {
					
					// properties = properties.trim();
					return properties.trim();

				}

			},
			addMoreProduct()
			{
				if (this.singleStockData.products.length < this.allProducts.length) {

					this.singleStockData.products.push({
						// stock_quantity : null,
        				// available_quantity : null,
        				// has_variations : null,
		        		// variations : this.productMerchant.hasOwnProperty('variations') && this.productMerchant.variations.length ? JSON.parse(JSON.stringify(this.productMerchant.variations)) : [],
        				// has_serials : null,
        				// serials : [],
		        		// merchant_product_id : this.productMerchant.id,
						
						addresses : [
							{},
						],
					});

					this.errors.products.push({
						addresses : [
							{},
						],
					});

				}
			},
			removeProduct() {
					
				if (this.singleStockData.products.length > 1) {

					this.singleStockData.products.pop();
					this.errors.products.pop();
				
				}
				
				/*
				else if (! this.createMode && this.singleStockData.products.length > stockedProductIndex && this.singleStockData.products[stockedProductIndex].addresses.length > 0) {

					this.singleStockData.products[stockedProductIndex].addresses.pop();
					this.errors.products[stockedProductIndex].addresses.pop();

				}
				*/

				if (!this.errorInProductsArray(this.errors.products)) {
					this.submitForm = true;
				}
				
			},
			addMoreSpace(stockedProductIndex) {

				if (this.singleStockData.products.length > stockedProductIndex && this.errors.products.length > stockedProductIndex && this.singleStockData.products[stockedProductIndex].addresses.length < 3) {

					this.singleStockData.products[stockedProductIndex].addresses.push({});
					this.errors.products[stockedProductIndex].addresses.push({});

				}

			},
			removeSpace(stockedProductIndex) {
					
				if (this.createMode && this.singleStockData.products.length > stockedProductIndex && this.errors.products.length > stockedProductIndex && this.singleStockData.products[stockedProductIndex].addresses.length > 1) {

					this.singleStockData.products[stockedProductIndex].addresses.pop();
					this.errors.products[stockedProductIndex].addresses.pop();
				
				}
				else if (! this.createMode && this.singleStockData.products.length > stockedProductIndex && this.errors.products.length > stockedProductIndex && this.singleStockData.products[stockedProductIndex].addresses.length > 0) {

					this.singleStockData.products[stockedProductIndex].addresses.pop();
					this.errors.products[stockedProductIndex].addresses.pop();

				}

				if (!this.errorInAddressesArray(this.errors.products[stockedProductIndex].addresses)) {
					this.submitForm = true;
				}
				
			},
			resetErrorObject(object) {

				// new errors initialization
				this.errors = {

					// mercahnt : null,
					// warehouse : null,
					
					products : [
						/*
						{
							addresses : [
								{}
							]
						}
						*/
					],
					
				};

				object.products.forEach(
					(stockedProduct, stockedProductIndex) => {

						this.errors.products.push({
							addresses : []
						});

						/*
						if (! stockedProduct.has_serials || (stockedProduct.has_variations && stockedProduct.has_serials)) {

							this.$delete(this.errors.products[stockedProductIndex], 'product_serial');

						}
						*/

						// if (stockedProduct.addresses.length) {

							// this.$set(this.errors.products[stockedProductIndex], 'addresses', []);	

						stockedProduct.addresses.forEach(
							(productAddress, index) => {
								this.errors.products[stockedProductIndex].addresses.push({});
							}
						);
							
						// }
						/*
						else {

							this.errors.products[stockedProductIndex].addresses = [
								{},
							];

						}
						*/
						
						if (stockedProduct.has_variations && stockedProduct.hasOwnProperty('variations') && stockedProduct.variations.length) {

							this.$set(this.errors.products[stockedProductIndex], 'variations', []);	

							stockedProduct.variations.forEach(

								(stockedProductVariation, stockedProductVariationIndex) => {

									// this.errors.products[stockedProductIndex].push({});
									this.errors.products[stockedProductIndex].variations.push({});
									
									/*
									if (stockedProduct.has_serials) {

										// this.errors.products[stockedProductIndex].variations[stockedProductVariationIndex].product_variation_serial = [];

										// this.$set(this.errors.products[stockedProductIndex].variations[stockedProductVariationIndex], 'product_variation_serial', []);
										// this.$delete(this.errors, 'product_serial');

									}
									*/
								
								}
							
							);
						
						}

					}
				);

			},
			setAvailableShelvesAndUnits() {

				this.resetWarehouseSpaces();

				this.singleStockData.products.forEach(
					(stockedProduct, stockedProductIndex) => {
						stockedProduct.addresses.forEach(
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
					}
				);

			},
			setAvailableContents(response) {
				this.pagination = response.data;
				this.allStocks = response.data.data;
			},
			setAvailableShelves(stockedProductIndex, spaceIndex) {
				// console.log('container if has been triggered');
				if (this.singleStockData.products.length > stockedProductIndex && this.singleStockData.products[stockedProductIndex].addresses[spaceIndex].container && Object.keys(this.singleStockData.products[stockedProductIndex].addresses[spaceIndex].container).length > 0) {
					
					this.$delete(this.singleStockData.products[stockedProductIndex].addresses[spaceIndex].container, 'shelves');
					this.emptyShelves = this.singleStockData.products[stockedProductIndex].addresses[spaceIndex].container.container_shelf_statuses;
				
				}
			},
			setAvailableUnitShelves(stockedProductIndex, spaceIndex) {
				// console.log('container if has been triggered');
				if (this.singleStockData.products[stockedProductIndex].addresses[spaceIndex].container && Object.keys(this.singleStockData.products[stockedProductIndex].addresses[spaceIndex].container).length > 0) {

					this.$delete(this.singleStockData.products[stockedProductIndex].addresses[spaceIndex].container, 'shelf');
					this.emptyUnitShelves = this.singleStockData.products[stockedProductIndex].addresses[spaceIndex].container.container_shelf_statuses;
				
				}
			},
			setAvailableUnits(stockedProductIndex, spaceIndex) {
				// console.log('shelf if has been triggered');
				if (this.singleStockData.products[stockedProductIndex].addresses[spaceIndex].container && Object.keys(this.singleStockData.products[stockedProductIndex].addresses[spaceIndex].container).length > 0 && Object.keys(this.singleStockData.products[stockedProductIndex].addresses[spaceIndex].container.shelf).length > 0) {

					this.$delete(this.singleStockData.products[stockedProductIndex].addresses[spaceIndex].container.shelf, 'units');
					this.emptyUnits = this.singleStockData.products[stockedProductIndex].addresses[spaceIndex].container.shelf.container_shelf_unit_statuses;
				
				}
			},
			setProductSpaceType(stockedProductIndex, spaceIndex) {
				
				if (this.singleStockData.products.length > stockedProductIndex && this.singleStockData.products[stockedProductIndex].addresses.length > spaceIndex && this.errors.products.length > stockedProductIndex && this.errors.products[stockedProductIndex].addresses.length > spaceIndex) {

					// resetting
					this.$delete(this.singleStockData.products[stockedProductIndex].addresses[spaceIndex], 'container');
					this.$delete(this.singleStockData.products[stockedProductIndex].addresses[spaceIndex], 'containers');

					this.errors.products[stockedProductIndex].addresses[spaceIndex] = {};

				}

				// this.resetAvailableSpaces();	// Disabling omitting used spaces to use is again
		
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

				this.singleStockData.products.forEach(
					(stockedProduct, stockedProductIndex) => {

						stockedProduct.addresses.forEach(

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
					}
				);

			},
			checkVariationSerial(stockedProductIndex, stockedVariationIndex)
			{
				if (! this.variationNewSerial[stockedVariationIndex] || this.variationNewSerial[stockedVariationIndex].length < 4) {

					this.errors.products[stockedProductIndex].variations[stockedVariationIndex].product_variation_serial ? this.errors.products[stockedProductIndex].variations[stockedVariationIndex].product_variation_serial = 'Serial minimum length is four(4)' : this.$set(this.errors.products[stockedProductIndex].variations[stockedVariationIndex], 'product_variation_serial', 'Serial minimum length is four(4)');

				}
				else if (this.variationNewSerial[stockedVariationIndex] && ! this.variationNewSerial[stockedVariationIndex].match(/^[a-zA-Z0-9-_]+$/)) {

					// this.errors.variations[stockedVariationIndex].product_variation_serial[i] = 'Invalid serial number';

					// this.$set(this.errors.variations[stockedVariationIndex].product_variation_serial, stockedVariationIndex, 'Invalid serial number');

					this.errors.products[stockedProductIndex].variations[stockedVariationIndex].product_variation_serial ? this.errors.products[stockedProductIndex].variations[stockedVariationIndex].product_variation_serial = 'Invalid serial number' : this.$set(this.errors.products[stockedProductIndex].variations[stockedVariationIndex], 'product_variation_serial', 'Invalid serial number');

					// this.$set(this.errors.variations[stockedVariationIndex].product_variation_serial, 'Invalid serial number');

				}
				else if (this.variationNewSerial[stockedVariationIndex] && this.singleStockData.products.some(stockingProduct=>stockingProduct.has_serials && stockingProduct.has_variations && stockingProduct.variations.some(stockingProductVariation => stockingProductVariation.serials.some(variationSerial => variationSerial.serial_no == this.variationNewSerial[stockedVariationIndex]))) /*&& this.singleStockData.products[stockedProductIndex].variations[stockedVariationIndex].serials.some((variationSerial) => variationSerial.serial_no == this.variationNewSerial[stockedVariationIndex])*/) {

					// this.$set(this.errors.variations[stockedVariationIndex].product_variation_serial, stockedVariationIndex, 'Duplicate serial number');
					
					this.errors.products[stockedProductIndex].variations[stockedVariationIndex].product_variation_serial ? this.errors.products[stockedProductIndex].variations[stockedVariationIndex].product_variation_serial = 'Duplicate serial number' : this.$set(this.errors.products[stockedProductIndex].variations[stockedVariationIndex], 'product_variation_serial', 'Duplicate serial number');
					
					// this.$set(this.errors.variations[stockedVariationIndex].product_variation_serial, 'Duplicate serial number');

				}
				else {

					this.$delete(this.errors.products[stockedProductIndex].variations[stockedVariationIndex], 'product_variation_serial');

				}
			},
			addProductSerial(stockedProductIndex) {

				this.validateFormInput('product_serial');

				if (! this.errors.products[stockedProductIndex].product_serial && this.singleStockData.products[stockedProductIndex].serials.some(productSerial=>! productSerial.serial_no || productSerial.serial_no == '')) {

					let emptyIndex = this.singleStockData.products[stockedProductIndex].serials.findIndex(productSerial=> ! productSerial.serial_no || productSerial.serial_no == '');

					if (emptyIndex > -1) {

						// this.$set(this.errors.products[stockedProductIndex].variations[stockedVariationIndex], 'product_variation_serial', 'Duplicate serial number');

						this.singleStockData.products[stockedProductIndex].serials[emptyIndex].serial_no ? this.singleStockData.products[stockedProductIndex].serials[emptyIndex].serial_no = this.productNewSerial : this.$set(this.singleStockData.products[stockedProductIndex].serials[emptyIndex], 'serial_no', this.productNewSerial);
						
						this.productNewSerial = '';

					}
				
				}

			},
			addVariationSerial(stockedProductIndex, stockedVariationIndex) {

				this.checkVariationSerial(stockedProductIndex, stockedVariationIndex);

				if (! this.errors.products[stockedProductIndex].variations[stockedVariationIndex].product_variation_serial && this.singleStockData.products[stockedProductIndex].variations[stockedVariationIndex].serials.some(variationSerial => Object.keys(variationSerial).length == 0 || ! variationSerial.serial_no || variationSerial.serial_no == '')) {

					let emptyIndex = this.singleStockData.products[stockedProductIndex].variations[stockedVariationIndex].serials.findIndex(variationSerial => Object.keys(variationSerial).length == 0 || ! variationSerial.serial_no || variationSerial.serial_no == '');				

					if (emptyIndex > -1) {

						this.singleStockData.products[stockedProductIndex].variations[stockedVariationIndex].serials[emptyIndex].serial_no ? this.singleStockData.products[stockedProductIndex].variations[stockedVariationIndex].serials[emptyIndex].serial_no = this.variationNewSerial[stockedVariationIndex] : this.$set(this.singleStockData.products[stockedProductIndex].variations[stockedVariationIndex].serials[emptyIndex], 'serial_no', this.variationNewSerial[stockedVariationIndex]);

						// this.singleStockData.products[stockedProductIndex].variations[stockedVariationIndex].serials[emptyIndex].serial_no = this.variationNewSerial[stockedVariationIndex];
						
						// this.$set(this.singleStockData.products[stockedProductIndex].variations[stockedVariationIndex].serials[emptyIndex], 'serial_no', this.variationNewSerial[stockedVariationIndex]);

						this.variationNewSerial[stockedVariationIndex] = '';

					}
				
				}

			},
			removeVariationSerial(stockedProductIndex, stockedVariationIndex, stockedVariationSerialIndex) {

				if (this.singleStockData.products.length > stockedProductIndex && this.singleStockData.products[stockedProductIndex].has_variations && this.singleStockData.products[stockedProductIndex].variations.length > stockedVariationIndex && this.singleStockData.products[stockedProductIndex].has_serials && this.singleStockData.products[stockedProductIndex].variations[stockedVariationIndex].serials.length > stockedVariationSerialIndex && ! this.singleStockData.products[stockedProductIndex].variations[stockedVariationIndex].serials[stockedVariationSerialIndex].has_requisitions) {

					this.$delete(this.singleStockData.products[stockedProductIndex].variations[stockedVariationIndex].serials[stockedVariationSerialIndex], 'serial_no');

				}

			},
			removeProductSerial(stockedProductIndex, stockedProductSerialIndex) {

				if (this.singleStockData.products.length > stockedProductIndex && this.singleStockData.products[stockedProductIndex].has_serials && ! this.singleStockData.products[stockedProductIndex].has_variations && this.singleStockData.products[stockedProductIndex].serials.length > stockedProductSerialIndex && ! this.singleStockData.products[stockedProductIndex].serials[stockedProductSerialIndex].has_requisitions) {

					this.$delete(this.singleStockData.products[stockedProductIndex].serials[stockedProductSerialIndex], 'serial_no');

				}

			},
			setProductSerialObjects(stockedProduct) {

				if (stockedProduct.stock_quantity > 0 && stockedProduct.serials.length < stockedProduct.stock_quantity) {
					
					let difference = stockedProduct.stock_quantity - stockedProduct.serials.length;

					for (let i = 0; i < difference; i++) {
				  		stockedProduct.serials.push({});
					}

				}
				else if (stockedProduct.stock_quantity > 0 && stockedProduct.serials.length > stockedProduct.stock_quantity) {

					stockedProduct.serials.splice(this.singleStockData.stock_quantity, );
					// this.errors.product_serial.splice(stockedProduct.stock_quantity, );

				}

			},
			setProductVariationSerialObjects(stockedProduct) {

				stockedProduct.variations.forEach(
								
					(stockedProductVariation, index) => {

						if (stockedProductVariation.stock_quantity > 0) {

							if (stockedProductVariation.serials.length < stockedProductVariation.stock_quantity) {

								let difference = stockedProductVariation.stock_quantity - stockedProductVariation.serials.length;

								for (let i = 0; i < difference; i++) {
							  		stockedProductVariation.serials.push({});
								}

							}
							else if (stockedProductVariation.serials.length > stockedProductVariation.stock_quantity) {

								stockedProductVariation.serials.splice(stockedProductVariation.stock_quantity, );
								// this.errors.variations[index].product_variation_serial.splice(stockedProductVariation.stock_quantity, );

							}
							

						}

					}
					
				);

			},
			changeNumberContents() {
				
				this.pagination.current_page = 1;

				if (this.searchAttributes.search === '') {
					this.fetchAllStocks();
				}
				else {
					this.searchData();
				}
				
    		},
    		objectNameWithCapitalized ({ name, variation, user_name, product }) {
		      	
		      	if (name) {
				    
				    const words = name.toString().split(" ");

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
		      	else if (user_name) {
				    
				    const words = user_name.toString().split(" ");

					for (let i = 0; i < words.length; i++) {
					    words[i] = words[i][0].toUpperCase() + words[i].substr(1);
					}

					return words.join(" ");
		      	}
		      	else if (product) {

		      		const words = product.name ? product.name.toString().split(" ") : [];

					for (let i = 0; i < words.length; i++) {
					    words[i] = words[i][0].toUpperCase() + words[i].substr(1);
					}

					return words.join(" ");	
		      	}
		      	else 
		      		return ''
		      	
		    },
			nameWithCapitalized (name) {
				
				if (!name) return ''

				const words = name.toString().split(" ");

				for (let i = 0; i < words.length; i++) {
				    words[i] = words[i][0].toUpperCase() + words[i].substr(1);
				}

				return words.join(" ");

		    },
		    printInvoice() {

				// this.printingStyles.name = `${ this.singleStockData.subject } Details`;
				this.printingStyles.windowTitle = this.singleStockData.invoice_no +  ' Stock Details';

				this.$htmlToPaper('sectionToPrint', this.printingStyles);

				$('#stock-view-modal').modal('hide');

			},
			printStockCode(productsToPrint = []) {

				// this.stockProductsToPrint = productsToPrint;
			
				this.$set(this, 'stockProductsToPrint', productsToPrint);
		        	
				this.$nextTick(function () {

					JsBarcode(".barcode").init();
					this.$htmlToPaper('stockCodeToPrint', this.printingStyles);

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
            showPreview(imagePath = 'default', alternativePreview) {
				
				if (! imagePath) { // if null
					return ('/' + alternativePreview);
				}
				else if (imagePath.startsWith('data:')) {
					return imagePath;
				}
				else {
					return '/' + imagePath;
				}

				// return '';

			},
			getFullName (keeper) {

				if (keeper.first_name || keeper.last_name) {
					return keeper.first_name + ' ' + keeper.last_name;
				}
				else {
					return keeper.user_name;
				}

			},
			setPurchaseVendor(stockedProductIndex) {

				if (this.singleStockData.products.length > stockedProductIndex && this.singleStockData.products[stockedProductIndex].vendor) {

					this.singleStockData.products[stockedProductIndex].vendor_id = this.singleStockData.products[stockedProductIndex].vendor.id;

				}

			},
			setPurchaseLocation(stockedProductIndex) {

				if (this.singleStockData.products.length > stockedProductIndex && this.singleStockData.products[stockedProductIndex].location) {

					this.singleStockData.products[stockedProductIndex].location_id = this.singleStockData.products[stockedProductIndex].location.id;

				}

			},
			validateFormInput (formInputName) {

				this.submitForm = false;

				switch(formInputName) {

					case 'warehouse' :

						if (! this.singleStockData.warehouse || Object.keys(this.singleStockData.warehouse).length == 0) {
							this.errors.warehouse = 'Warehouse is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'warehouse');
						}

						break;

					case 'merchant' :

						if (! this.singleStockData.merchant || Object.keys(this.singleStockData.merchant).length == 0) {
							this.errors.merchant = 'Merchant is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'merchant');
						}

						break;

					case 'product' : 

						this.singleStockData.products.forEach(
							(stockedProduct, stockedProductIndex) => {

								if (! stockedProduct.merchant_product || Object.keys(stockedProduct.merchant_product).length == 0) {
									this.errors.products[stockedProductIndex].product = 'Product is required';
								}
								else if (this.singleStockData.products.some((productTostock, productIndexTostock)=>productTostock.merchant_product_id==stockedProduct.merchant_product_id && productIndexTostock != stockedProductIndex )) {

									this.errors.products[stockedProductIndex].product = 'Same product selected';

								}
								else {
									this.$delete(this.errors.products[stockedProductIndex], 'product');
								}

							}
						);

						if (! this.errorInProductsArray(this.errors.products)) {
							this.submitForm = true;	
						}

						break;

					case 'product_stock_quantity' :

						this.singleStockData.products.forEach(
							(stockedProduct, stockedProductIndex) => {
								if (! stockedProduct.stock_quantity || stockedProduct.stock_quantity < 1) {
									this.errors.products[stockedProductIndex].product_stock_quantity = 'Stock quantity is required';
								}
								else if (! this.createMode && stockedProduct.stock_quantity && stockedProduct.available_quantity && stockedProduct.stock_quantity < stockedProduct.available_quantity) {

									this.errors.products[stockedProductIndex].product_stock_quantity = 'Stock quantity is less than minimum ' + stockedProduct.available_quantity;

								}
								/*
								else if(! this.createMode && ((stockedProduct.primary_quantity - stockedProduct.stock_quantity) > this.allStocks[this.allStocks.length-1].available_quantity)){

									this.errors.products[stockedProductIndex].product_stock_quantity = 'Stock quantity is less than minimum ' + (stockedProduct.primary_quantity - this.allStocks[this.allStocks.length-1].available_quantity);
								}
								*/
								else{
									// this.submitForm = true;
									// this.errors.products[stockedProductIndex].product_stock_quantity = null;
									this.$delete(this.errors.products[stockedProductIndex], 'product_stock_quantity');
								}								
							}
						);

						if (! this.errorInProductsArray(this.errors.products)) {
							this.submitForm = true;	
						}

						break;

					case 'product_variation_quantity' :

						this.singleStockData.products.forEach(
							
							(stockedProduct, stockedProductIndex) => {

								if (stockedProduct.has_variations) {

									stockedProduct.variations.forEach(

										(stockedProductVariation, stockedProductVariationIndex) => {

											if (stockedProductVariation.stock_quantity < 0) {
												this.errors.products[stockedProductIndex].variations[stockedProductVariationIndex].product_variation_quantity = 'Variation quantity is invalid';
											}
											/*
											else if(! this.createMode && ((stockedProductVariation.primary_quantity - stockedProductVariation.stock_quantity) > this.allStocks[this.allStocks.length-1].variations[stockedProductVariationIndex].available_quantity)){

												this.errors.products[stockedProductIndex].variations[stockedProductVariationIndex].product_variation_quantity = 'Variation quantity is less than minimum ' + (stockedProductVariation.primary_quantity-this.allStocks[this.allStocks.length-1].variations[stockedProductVariationIndex].available_quantity);
											}
											*/
											else if (! this.createMode && stockedProductVariation.stock_quantity && stockedProductVariation.available_quantity && stockedProductVariation.stock_quantity < stockedProductVariation.available_quantity) {

												this.errors.products[stockedProductIndex].variations[stockedProductVariationIndex].product_variation_quantity = 'Variation quantity is less than minimum ' + stockedProductVariation.available_quantity;

											}
											else {
												// this.errors.products[stockedProductIndex].variations[index].product_variation_quantity = null;
												this.$delete(this.errors.products[stockedProductIndex].variations[stockedProductVariationIndex], 'product_variation_quantity');
											}

										}
										
									);
										
									/*
									if (! this.errorInVariationsArray(this.errors.products[stockedProductIndex].variations)) {
										this.submitForm = true;
									}
									*/
									
								}
								else {
									// this.submitForm = true;
									this.errors.products[stockedProductIndex].variations = [];
								}

							}

						);

						if (! this.errorInProductsArray(this.errors.products)) {
							this.submitForm = true;	
						}

						break;
					
					
					case 'product_variation_total_quantity' :

						this.singleStockData.products.forEach(

							(stockedProduct, stockedProductIndex) => {

								if (stockedProduct.has_variations) {

									if (! this.errorInVariationsArray(this.errors.products[stockedProductIndex].variations)) {

										let variationTotalQuantity = stockedProduct.variations.reduce(
											(value, currentObject) => {
												return value + (currentObject.stock_quantity || 0);
											}, 
										0);

										if (variationTotalQuantity !== stockedProduct.stock_quantity) {
											this.errors.products[stockedProductIndex].variations[stockedProduct.variations.length-1].product_variation_quantity = 'Total variation qty should be equal to qty';
										}
										else {
										
											// this.errors.products[stockedProductIndex].variations[stockedProduct.variations.length-1].product_variation_quantity = null;
										
											this.$delete(this.errors.products[stockedProductIndex].variations[stockedProduct.variations.length-1], 'product_variation_quantity');
											
										}

									}

								}
								else {
									// this.submitForm = true;
									this.errors.products[stockedProductIndex].variations = [];
								}

							}

						);

						if (! this.errorInProductsArray(this.errors.products)) {
							this.submitForm = true;	
						}

						break;
					
				
					case 'product_address' : 

						this.singleStockData.products.forEach(

							(stockedProduct, stockedProductIndex) => {

								if (this.createMode && stockedProduct.addresses.length < 1) {
									// this.submitForm = false;
									this.errors.products[stockedProductIndex].product_address = 'Address is required';
								}
								else {
									// this.submitForm = true;
									// this.errors.products[stockedProductIndex].product_address = null;
									this.$delete(this.errors.products[stockedProductIndex], 'product_address');
								}

							}

						);

						if (! this.errorInProductsArray(this.errors.products)) {
							this.submitForm = true;	
						}

						break;

					case 'product_space_type' :

						this.singleStockData.products.forEach(
							
							(stockedProduct, stockedProductIndex) => {

								stockedProduct.addresses.forEach(

									(productSpace, productSpaceIndex) => {

										if (! productSpace.type) {

											this.errors.products[stockedProductIndex].addresses[productSpaceIndex].product_space_type = 'Space type is required';

										}
										/*
											else if (this.singleStockData.addresses.filter((obj) => obj.type === productSpace.type).length > 1) {

												this.errors.addresses[productSpaceIndex].product_space_type = 'Same type selected';
											}
										*/
										else {
											// this.errors.products[stockedProductIndex].addresses[productSpaceIndex].product_space_type = null;
											this.$delete(this.errors.products[stockedProductIndex].addresses[productSpaceIndex], 'product_space_type');
										}

									}

								);

							}

						);

						if (! this.errorInProductsArray(this.errors.products)) {
							this.submitForm = true;
						}

						break;

					case 'product_containers' :

						this.singleStockData.products.forEach(
							(stockedProduct, stockedProductIndex) => {
								stockedProduct.addresses.forEach(
									
									(productSpace, productSpaceIndex) => {

										if (productSpace.type=='containers' && (! productSpace.containers || productSpace.containers.length == 0)) {
											this.errors.products[stockedProductIndex].addresses[productSpaceIndex].product_containers = 'Container is required';
										}
										else{

											// this.errors.products[stockedProductIndex].addresses[productSpaceIndex].product_containers = null;
											
											this.$delete(this.errors.products[stockedProductIndex].addresses[productSpaceIndex], 'product_containers');
										}

									}

								);

							}

						);

						if (! this.errorInProductsArray(this.errors.products)) {
							this.submitForm = true;
						}

						break;

					case 'product_container' :

						this.singleStockData.products.forEach(
							(stockedProduct, stockedProductIndex) => {
								stockedProduct.addresses.forEach(
									
									(productSpace, productSpaceIndex) => {

										if ((productSpace.type=='shelves' || productSpace.type=='units') && (!productSpace.container || Object.keys(productSpace.container).length==0)) {
											this.errors.products[stockedProductIndex].addresses[productSpaceIndex].product_container = 'Container is required';
										}
										else{
											// this.errors.products[stockedProductIndex].addresses[productSpaceIndex].product_container = null;
											this.$delete(this.errors.products[stockedProductIndex].addresses[productSpaceIndex], 'product_container');
										}

									}
								);
								
							}
						);

						if (! this.errorInProductsArray(this.errors.products)) {
							this.submitForm = true;
						}

						break;

					case 'product_shelves' : 

						this.singleStockData.products.forEach(
							(stockedProduct, stockedProductIndex) => {
								stockedProduct.addresses.forEach(
									
									(productSpace, productSpaceIndex) => {

										if (productSpace.type=='shelves' && (! productSpace.container || ! productSpace.container.shelves || productSpace.container.shelves.length == 0)) {
											this.errors.products[stockedProductIndex].addresses[productSpaceIndex].product_shelves = 'Shelf is required';
										}
										else{
											// this.errors.products[stockedProductIndex].addresses[productSpaceIndex].product_shelves = null;
											this.$delete(this.errors.products[stockedProductIndex].addresses[productSpaceIndex], 'product_shelves');
										}

									}

								);

							}
						);

						if (! this.errorInProductsArray(this.errors.products)) {
							this.submitForm = true;
						}

						break;

					case 'product_shelf' :

						this.singleStockData.products.forEach(
							(stockedProduct, stockedProductIndex) => {
								stockedProduct.addresses.forEach(
									
									(productSpace, productSpaceIndex) => {

										if (productSpace.type=='units' && (! productSpace.container || ! productSpace.container.shelf || Object.keys(productSpace.container.shelf).length==0)) {
											this.errors.products[stockedProductIndex].addresses[productSpaceIndex].product_shelf = 'Shelf is required';
										}
										else{
											// this.errors.products[stockedProductIndex].addresses[productSpaceIndex].product_shelf = null;
											this.$delete(this.errors.products[stockedProductIndex].addresses[productSpaceIndex], 'product_shelf');
										}

									}

								);

							}
						);

						if (! this.errorInProductsArray(this.errors.products)) {
							this.submitForm = true;
						}

						break;


					case 'product_units' :

						this.singleStockData.products.forEach(
							(stockedProduct, stockedProductIndex) => {
								stockedProduct.addresses.forEach(
									
									(productSpace, productSpaceIndex) => {

										if (productSpace.type=='units' && (! productSpace.container || ! productSpace.container.shelf || !productSpace.container.shelf.units || productSpace.container.shelf.units.length == 0)) {
											this.errors.products[stockedProductIndex].addresses[productSpaceIndex].product_units = 'Unit is required';
										}
										else{

											this.$delete(this.errors.products[stockedProductIndex].addresses[productSpaceIndex], 'product_units');
										}

									}
								);

							}
						);

						if (! this.errorInProductsArray(this.errors.products)) {
							this.submitForm = true;
						}

						break;

					case 'product_serial' :

						this.singleStockData.products.forEach(
							(stockedProduct, stockedProductIndex) => {
								
								if (stockedProduct.has_serials && ! stockedProduct.has_variations) {
									
									if (stockedProduct.serials.some(productSerial=> ! productSerial.serial_no) && (! this.productNewSerial || this.productNewSerial == '')) {

										this.errors.products[stockedProductIndex].product_serial ? this.errors.products[stockedProductIndex].product_serial = 'Product serial is required' : this.$set(this.errors.products[stockedProductIndex], 'product_serial', 'Product serial is required');
									
									}
									else if (this.productNewSerial && this.productNewSerial.length < 4) {

										this.errors.products[stockedProductIndex].product_serial ? this.errors.products[stockedProductIndex].product_serial = 'Product serial length should be minimum 4' : this.$set(this.errors.products[stockedProductIndex], 'product_serial', 'Product serial length should be minimum 4');

									}
									else if (this.productNewSerial && ! this.productNewSerial.match(/^[a-zA-Z0-9-_]+$/)) {

										this.errors.products[stockedProductIndex].product_serial ? this.errors.products[stockedProductIndex].product_serial = 'Invalide serial number' : this.$set(this.errors.products[stockedProductIndex], 'product_serial', 'Invalide serial number');

									}
									else if (stockedProduct.serials.some((productSerial) => productSerial.serial_no == this.productNewSerial)) {

										this.errors.products[stockedProductIndex].product_serial ? this.errors.products[stockedProductIndex].product_serial = 'Duplicate serial number' : this.$set(this.errors.products[stockedProductIndex], 'product_serial', 'Duplicate serial number');

									}
									else {

										// this.errors.product_serial[i] = null;
										// this.$set(this.errors.product_serial, i, null);
										this.$delete(this.errors.products[stockedProductIndex], 'product_serial');

									}

								}
								else {

									// this.submitForm = true;
									// this.errors.product_serial = [];
									this.$delete(this.errors.products[stockedProductIndex], 'product_serial');
								
								}
							
							}
						
						);

						if (! this.errorInProductsArray(this.errors.products)) {
							this.submitForm = true;
						}

						break;
					
					case 'product_variation_serial' :

						this.singleStockData.products.forEach(
							(stockedProduct, stockedProductIndex) => {
								
								if (stockedProduct.has_serials && stockedProduct.has_variations && stockedProduct.hasOwnProperty('variations') && stockedProduct.variations.length) {
									
									stockedProduct.variations.forEach(
									
										(stockedVariation, stockVariationIndex) => {

											if (stockedVariation.stock_quantity > 0) {

												if (! this.errors.products[stockedProductIndex].variations[stockVariationIndex].product_variation_serial && stockedVariation.serials.some(variationSerial=> ! variationSerial.serial_no || variationSerial.serial_no == '')) {

													// this.errors.variations[stockVariationIndex].product_variation_serial[i] = 'Variation serial is required';

													// this.$set(this.errors.variations[stockVariationIndex].product_variation_serial, stockVariationIndex, 'Variation serial is required');

													this.errors.products[stockedProductIndex].variations[stockVariationIndex].product_variation_serial ? this.errors.products[stockedProductIndex].variations[stockVariationIndex].product_variation_serial = 'Variation serial is required' : this.$set(this.errors.products[stockedProductIndex].variations[stockVariationIndex], 'product_variation_serial', 'Variation serial is required');

													// this.$set(this.errors.variations[stockVariationIndex].product_variation_serial, 'Variation serial is required');

												}
												else {

													// this.errors.variations[stockVariationIndex].product_variation_serial[stockVariationIndex] = null;

													this.$delete(this.errors.products[stockedProductIndex].variations[stockVariationIndex], 'product_variation_serial');

												}
												
											}
											else {

												// this.errors.variations = [];
												this.$delete(this.errors.products[stockedProductIndex].variations[stockVariationIndex], 'product_variation_serial');

											}

										}

									);

								}
								else {

									// this.submitForm = true;

									if (! stockedProduct.has_variations) {

										this.errors.products[stockedProductIndex].variations = [];
										// this.$delete(this.errors.products[stockedProductIndex], 'variations');

									}
									else {	
										
										stockedProduct.variations.forEach(

											(stockedVariation, stockVariationIndex) => {

												// this.errors.variations[stockVariationIndex].product_variation_serial = [];
												this.$delete(this.errors.products[stockedProductIndex].variations[stockVariationIndex], 'product_variation_serial');
												this.$delete(this.errors.products[stockedProductIndex].variations[stockVariationIndex], 'product_variation_serials');

											}

										);	

									}
								
								}

							}

						);

						if (! this.errorInProductsArray(this.errors.products)) {
							this.submitForm = true;
						}

						break;

					case 'product_serials' :

						this.singleStockData.products.forEach(
							(stockedProduct, stockedProductIndex) => {
								
								if (stockedProduct.has_serials && ! stockedProduct.has_variations) {
									
									if (stockedProduct.serials.some(productSerial=> ! productSerial.serial_no)) {

										this.errors.products[stockedProductIndex].product_serials ? this.errors.products[stockedProductIndex].product_serials = 'Serials are less than  required' : this.$set(this.errors.products[stockedProductIndex], 'product_serials', 'Serials are less than  required');
									
									}
									else {

										this.$delete(this.errors.products[stockedProductIndex], 'product_serials');

									}

								}
								else {

									this.$delete(this.errors.products[stockedProductIndex], 'product_serials');
								
								}
							
							}
						
						);

						if (! this.errorInProductsArray(this.errors.products)) {
							this.submitForm = true;
						}

						break;
					
					case 'product_variation_serials' :

						this.singleStockData.products.forEach(
							(stockedProduct, stockedProductIndex) => {
								
								if (stockedProduct.has_serials && stockedProduct.has_variations && stockedProduct.hasOwnProperty('variations') && stockedProduct.variations.length) {
									
									stockedProduct.variations.forEach(
									
										(stockedVariation, stockVariationIndex) => {

											if (stockedVariation.stock_quantity > 0) {

												if (stockedVariation.serials.some(variationSerial=> ! variationSerial.serial_no || variationSerial.serial_no == '')) {

													this.errors.products[stockedProductIndex].variations[stockVariationIndex].product_variation_serials ? this.errors.products[stockedProductIndex].variations[stockVariationIndex].product_variation_serials = 'Serials are less than  required' : this.$set(this.errors.products[stockedProductIndex].variations[stockVariationIndex], 'product_variation_serials', 'Serials are less than  required');

												}
												else {

													this.$delete(this.errors.products[stockedProductIndex].variations[stockVariationIndex], 'product_variation_serials');

												}
												
											}
											else {

												this.$delete(this.errors.products[stockedProductIndex].variations[stockVariationIndex], 'product_variation_serials');

											}

										}

									);

								}
								else {

									// this.submitForm = true;

									if (! stockedProduct.has_variations) {

										this.errors.products[stockedProductIndex].variations = [];
										// this.$delete(this.errors.products[stockedProductIndex], 'variations');

									}
									else {	
										
										stockedProduct.variations.forEach(

											(stockedVariation, stockVariationIndex) => {

												// this.errors.variations[stockVariationIndex].product_variation_serial = [];
												this.$delete(this.errors.products[stockedProductIndex].variations[stockVariationIndex], 'product_variation_serial');
												this.$delete(this.errors.products[stockedProductIndex].variations[stockVariationIndex], 'product_variation_serials');

											}

										);	

									}
								
								}

							}

						);

						if (! this.errorInProductsArray(this.errors.products)) {
							this.submitForm = true;
						}

						break;

					case 'manufacturing_date' : 

						this.singleStockData.products.forEach(
							
								(stockProduct, stockProductIndex) => {

									if (! stockProduct.has_variations && stockProduct.merchant_product && stockProduct.merchant_product.product && stockProduct.merchant_product.product.category && stockProduct.merchant_product.product.category.is_perishable && ! stockProduct.manufactured_at) {
										
										this.errors.products[stockProductIndex].manufacturing_date = 'Mfg. date is required';

									}
									else if (stockProduct.has_variations && stockProduct.merchant_product && stockProduct.merchant_product.product && stockProduct.merchant_product.product.category && stockProduct.merchant_product.product.category.is_perishable) {

										stockProduct.variations.forEach(
										
											(stockedVariation, stockVariationIndex) => {

												if (stockedVariation.stock_quantity > 0 && ! stockedVariation.manufactured_at) {

													this.errors.products[stockProductIndex].variations[stockVariationIndex].manufacturing_date = 'Mfg. date is required';

												}
												else {

													this.$delete(this.errors.products[stockProductIndex].variations[stockVariationIndex], 'manufacturing_date');

												}

											}

										);

									}
									else {
										
										// this.submitForm = true;
										this.$delete(this.errors.products[stockProductIndex], 'manufacturing_date');

									}

								}

						);

						if (! this.errorInProductsArray(this.errors.products)) {

							this.submitForm = true;

						}

						break;

					case 'expiring_date' : 

						this.singleStockData.products.forEach(
							
								(stockProduct, stockProductIndex) => {

									if (! stockProduct.has_variations && stockProduct.merchant_product && stockProduct.merchant_product.product && stockProduct.merchant_product.product.category && stockProduct.merchant_product.product.category.is_perishable && ! stockProduct.expired_at) {
										
										this.errors.products[stockProductIndex].expiring_date = 'Exp. date is required';

									}
									else if (stockProduct.has_variations && stockProduct.merchant_product && stockProduct.merchant_product.product && stockProduct.merchant_product.product.category && stockProduct.merchant_product.product.category.is_perishable) {

										stockProduct.variations.forEach(
										
											(stockedVariation, stockVariationIndex) => {

												if (stockedVariation.stock_quantity > 0 && ! stockedVariation.expired_at) {

													this.errors.products[stockProductIndex].variations[stockVariationIndex].expiring_date = 'Exp. date is required';

												}
												else {

													this.$delete(this.errors.products[stockProductIndex].variations[stockVariationIndex], 'expiring_date');

												}

											}

										);

									}
									else {
										
										// this.submitForm = true;
										this.$delete(this.errors.products[stockProductIndex], 'expiring_date');

									}

								}
								
						);

						if (! this.errorInProductsArray(this.errors.products)) {

							this.submitForm = true;

						}

						break;

					case 'vendor': 

						if (this.singleStockData.merchant && this.singleStockData.merchant.support_deal && this.singleStockData.merchant.support_deal.purchase_support) {

							this.singleStockData.products.forEach(

								(stockedProduct, stockedProductIndex) => {

									if (! stockedProduct.vendor || ! stockedProduct.vendor_id) {

										this.errors.products[stockedProductIndex].vendor = 'Vendor is required';

									}
									else {

										this.$delete(this.errors.products[stockedProductIndex], 'vendor');

									}

								}

							);

						}
						else {

							this.singleStockData.products.forEach(

								(stockedProduct, stockedProductIndex) => {

									this.$delete(this.errors.products[stockedProductIndex], 'vendor');

								}

							);

						}

						if (! this.errorInProductsArray(this.errors.products)) {

							this.submitForm = true;

						}

						break;

					case 'location': 

						if (this.singleStockData.merchant && this.singleStockData.merchant.support_deal && this.singleStockData.merchant.support_deal.purchase_support) {

							this.singleStockData.products.forEach(

								(stockedProduct, stockedProductIndex) => {

									if (! stockedProduct.location || ! stockedProduct.location_id) {

										this.errors.products[stockedProductIndex].location = 'Location is required';

									}
									else {

										this.$delete(this.errors.products[stockedProductIndex], 'location');

									}

								}

							);

						}
						else {

							this.singleStockData.products.forEach(

								(stockedProduct, stockedProductIndex) => {

									this.$delete(this.errors.products[stockedProductIndex], 'location');

								}

							);

						}

						if (! this.errorInProductsArray(this.errors.products)) {

							this.submitForm = true;

						}

						break;

					case 'unit_buying_price':

						this.singleStockData.products.forEach(

							(stockedProduct, stockedProductIndex) => {

								if (stockedProduct.has_variations) {

									stockedProduct.variations.forEach(
										(stockedVariation, stockedVariationIndex) => {

											if (this.singleStockData.merchant && this.singleStockData.merchant.support_deal && this.singleStockData.merchant.support_deal.purchase_support && stockedVariation.stock_quantity > 0 && ! stockedVariation.unit_buying_price) {

												this.errors.products[stockedProductIndex].variations[stockedVariationIndex].unit_buying_price = 'Buying price is required';

											}
											else {

												this.$delete(this.errors.products[stockedProductIndex].variations[stockedVariationIndex], 'unit_buying_price');

											}

										}
									);

								}
								else {

									if (this.singleStockData.merchant && this.singleStockData.merchant.support_deal && this.singleStockData.merchant.support_deal.purchase_support && ! stockedProduct.unit_buying_price) {

										this.errors.products[stockedProductIndex].unit_buying_price = 'Buying price is required';

									}
									else {

										this.$delete(this.errors.products[stockedProductIndex], 'unit_buying_price');

									}

								}

							}

						);

						if (! this.errorInProductsArray(this.errors.products)) {

							this.submitForm = true;

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