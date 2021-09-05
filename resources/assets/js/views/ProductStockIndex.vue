
<template v-if="userHasPermissionTo('view-product-stock-index')">

	<div class="pcoded-content">

		<breadcrumb 
			:title="product.name + ' stocks'" 
			:message="'All' + product.name + 'stocks for ' + productMerchant.merchant ? productMerchant.merchant.name : ''"
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
											  		v-if="userHasPermissionTo('view-product-stock-index') || userHasPermissionTo('create-product-stock')"
											  		:query="query" 
											  		:caller-page="'stocks'" 
											  		:required-permission="'product-stock'" 
											  		:disable-add-button="allDealtEmptyWarehouses.length==0 ? true : false" 									  
											  		@showContentCreateForm="showStockCreateForm" 
											  		@searchData="searchData($event)" 
											  		@fetchAllContents="fetchProductAllStocks"
											  	></search-and-addition-option>
											</div>
											
											<div class="col-sm-12 col-lg-12">

									  		<!-- 
										  		<table-with-soft-delete-option 
										  			:query="query" 
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

 												<div class="tab-content card-block">
													<div class="card">
														<div class="table-responsive">
															<table class="table table-striped table-bordered nowrap text-center">
																<thead>
																	<tr>
																		<th>Date</th>
																		<th>Stock Qty</th>
																		<th>Available Qty</th>
																		<th>Approved</th>
																		<th>Actions</th>
																	</tr>
																</thead>
																<tbody>
																	<tr v-for="stock in allStocks" :key="'stock-' + stock.id"
																	>
																		<td>
																			{{ stock.created_at }}
																		</td>

																		<td>
																			{{ stock.stock_quantity }}
																		</td>

																		<td>
																			{{ stock.available_quantity }}
																		</td>

																		<td>
																			<span :class="[stock.has_approval==1 ? 'badge-success' : stock.has_approval==-1 ? 'badge-danger' : 'badge-secondary', 'badge']">
																				{{ stock.has_approval==1 ? 'Approved' : stock.has_approval==-1 ? 'Cancelled' : 'NA' }}
																			</span>
																		</td>
																		
																		<td>
																			<button 
																				type="button" 
																				class="btn btn-grd-info btn-icon"  
																				@click="showStockDetails(stock)"
																			>
																				<i class="fa fa-eye"></i>
																			</button>
																			<!-- Approve -->
																			<button 
																				type="button" 
																				class="btn btn-grd-warning btn-icon"  
																				@click="openStockEditForm(stock)" 
																				v-if="! stock.has_approval && userHasPermissionTo('update-product-stock')"
																			>
																				<i class="fa fa-check"></i>
																			</button>

																			<button 
																				type="button" 
																				class="btn btn-grd-primary btn-icon"  
																				@click="openStockEditForm(stock)" 
																				v-if="stock.has_approval==1 && userHasPermissionTo('update-product-stock')"
																			>
																				<i class="fa fa-edit"></i>
																			</button>

																			<button 
																				type="button" 
																				class="btn btn-grd-danger btn-icon" 
																				:disabled="formSubmitted || stock.stock_quantity > stock.available_quantity || (stock.hasOwnProperty('variations') && stock.variations.some(stockVariation => stockVariation.available_quantity < stockVariation.stock_quantity))"  
																				@click="openStockDeleteForm(stock)" 
																				v-if="userHasPermissionTo('delete-product-stock')" 
																			>
																				<i class="fa fa-trash"></i>
																			</button>
																		</td>
																	</tr>

																	<tr 
																  		v-show="!allStocks.length"
																  	>
															    		<td colspan="5">
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

																		<th>Available Qty</th>

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
																@click="query === '' ? fetchProductAllStocks() : searchData()"
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
																@paginate="query === '' ? fetchProductAllStocks() : searchData()"
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
							{{ createMode ? 'Create ' + product.name + ' Stock' : singleStockData.has_approval==1 ? 'Update ' + product.name + ' Stock' : 'Approve ' + product.name + ' Stock' }}
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
										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Product Category :
											</label>
											<label class="col-sm-6 col-form-label text-left">
												{{ product.category ? product.category.name : 'Bulk Product' }}
											</label>
										</div>

										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Merchant :
											</label>
											<label class="col-sm-6 col-form-label text-left">
												{{ productMerchant.merchant ? productMerchant.merchant.user_name : 'None' | capitalize }}
											</label>
										</div>

										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Name :
											</label>
											<label class="col-sm-6 col-form-label text-left">
												{{ product.name | capitalize }}
											</label>
										</div>

										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Manufacturer :
											</label>
											<label class="col-sm-6 col-form-label text-left">
												{{ productMerchant.manufacturer_name ? productMerchant.manufacturer_name : 'Own Product' | capitalize }}
											</label>
										</div>

										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												SKU Code :
											</label>
											<label class="col-sm-6 col-form-label text-left">
												{{ productMerchant.sku }}
											</label>
										</div>

										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Price :
											</label>
											<label class="col-sm-6 col-form-label text-left">
												{{ productMerchant.selling_price || 'NA' }}
											</label>
										</div>

										<!-- 
										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Description :
											</label>
											<label class="col-sm-6 col-form-label text-left">
												<span v-html="product.description"></span>
											</label>
										</div>
 										-->

 										<div class="form-row form-group">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Select Warehouse :
											</label>
											<div class="col-sm-6">
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

 										<div class="form-row form-group">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Stock Qty :
											</label>
											<div class="col-sm-6">
												<div class="input-group mb-0">
													<input type="number" 
														class="form-control" 
														v-model.number="singleStockData.stock_quantity" 
														placeholder="Product initial qty" 
														:class="!errors.stock.product_stock_quantity  ? 'is-valid' : 'is-invalid'" 
														@change="validateFormInput('product_stock_quantity')" 
														required="true" 
														:readonly="! createMode && singleStockData.primary_quantity > allStocks[allStocks.length-1].available_quantity" 
														:min="createMode ? 1 : singleStockData.primary_quantity - allStocks[allStocks.length-1].available_quantity"
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

									<!-- 
										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Available Qty:
											</label>
											<label class="col-sm-6 col-form-label text-left">
												{{ product.available_quantity }}
												{{ product.quantity_type }}
											</label>
										</div>
 									-->

										<div class="form-row mt-2">
											<div class="form-group col-md-12 text-center">
												<toggle-button 
													v-model="product.has_variations" 
													:width=150 
													:sync="true"
													:color="{checked: 'green', unchecked: 'blue'}"
													:labels="{checked: 'Has Variation', unchecked: 'No Variation'}" 
													:disabled="true" 
												/>
											</div>
										</div>

										<div class="form-row" v-if="product.has_variations">
											<div 
												class="form-group col-md-12" 
												v-if="singleStockData.hasOwnProperty('variations') && singleStockData.variations.length"
											>
												<div 
													class="form-row" 
													v-for="(stockVariation, variationIndex) in singleStockData.variations" 
													:key="'product-variation-index-' + variationIndex + 'A'"
												>	
													<div class="form-group col-md-6">
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
														class="form-group col-md-6"
													>
														<label for="inputFirstName">Variation Qty</label>
														
														<input type="number" 
															class="form-control" 
															v-model.number="singleStockData.variations[variationIndex].stock_quantity" 
															placeholder="Variation Qty" 
															:class="!errors.stock.variations[variationIndex].product_variation_quantity ? 'is-valid' : 'is-invalid'" 
															@change="validateFormInput('product_variation_quantity')" 
															required="true" 
															:readonly="! createMode && allStocks.length && stockVariation.primary_quantity > allStocks[allStocks.length-1].variations[variationIndex].available_quantity" 
															:min="createMode ? 1 : stockVariation.primary_quantity - allStocks[allStocks.length-1].variations[variationIndex].available_quantity"
														>

														<div class="invalid-feedback">
												        	{{ errors.stock.variations[variationIndex].product_variation_quantity }}
												  		</div>
													</div>
													
													<!-- 
													<div class="form-group col-md-3">
														<label for="inputFirstName">Price</label>
														<label class="col-form-label text-left">
															{{ productVariation.selling_price }}
														</label>
													</div>

													<div class="form-group col-md-3">
														<label for="inputFirstName">SKU</label>
														<label class="col-form-label text-left">
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
									          	<button type="button" class="btn btn-outline-secondary btn-sm btn-round" v-on:click="nextPage">
							                    	<i class="fa fa-2x fa-angle-double-right" aria-hidden="true"></i>
							                  	</button>
								          	</div>
								    	</div>
									</div>
							    </div>

							    <div 
								    class="row" 
								    v-bind:key="'product-modal-step-' + 2" 
								    v-show="!loading && step==2"
							    >
							    	<h2 class="mx-auto mb-4 lead">{{ product.name | capitalize }} Serials</h2>

							    	<div 
										class="col-md-12" 
										v-if="product.has_serials && product.has_variations && singleStockData.variations.length && step==2"
									>
										<div 
											class="form-row" 
											v-for="(stockedVariation, stockedVariationIndex) in singleStockData.variations" 
											:key="'product-variation-index-' + stockedVariationIndex + 'B'"
										>	
											<div class="card col-md-12" v-if="stockedVariation.stock_quantity > 0 && stockedVariation.hasOwnProperty('serials')">
												<div class="card-header">
													{{ stockedVariation.variation.name | capitalize }} Serials
												</div>
												
												<div class="card-body">
													<div 
														class="form-group" 
														v-for="productVariationStockIndex in stockedVariation.stock_quantity" 
														:key="'product-variation-' + stockedVariation.variation.name + '-serial-' + productVariationStockIndex"
													>	
														<label for="inputFirstName">
															# {{ stockedVariation.variation.name + ' Serial ' + productVariationStockIndex | capitalize }}
														</label>

														<input 
															type="text" 
															class="form-control" 
															v-model="stockedVariation.serials[productVariationStockIndex-1].serial_no" 
															placeholder="Variation Serial number" 
															:class="!errors.stock.variations[stockedVariationIndex].product_variation_serials[productVariationStockIndex-1] ? 'is-valid' : 'is-invalid'" 
															@change="validateFormInput('product_variation_serials')" 
															required="true" 
															:readonly="stockedVariation.serials[productVariationStockIndex-1].has_requisitions || stockedVariation.serials[productVariationStockIndex-1].has_dispatched"
														>

														<div class="invalid-feedback">
												        	{{ errors.stock.variations[stockedVariationIndex].product_variation_serials[productVariationStockIndex-1] }}
												  		</div>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div 
										class="col-md-12"
										v-else-if="product.has_serials && ! product.has_variations && singleStockData.stock_quantity > 0 && step==2"
									>
										<div 
											class="form-row" 
											v-for="stockedProductIndex in singleStockData.stock_quantity" 
											:key="'product-serial-' + stockedProductIndex"
										>
											<div class="col-sm-12 form-group">
												<label for="inputFirstName">
													# Serial {{ stockedProductIndex }}
												</label>
												
												<input 
													type="text" 
													class="form-control" 
													v-model="singleStockData.serials[stockedProductIndex-1].serial_no" 
													placeholder="Product Serial number" 
													:class="!errors.stock.product_serials[stockedProductIndex-1] ? 'is-valid' : 'is-invalid'" 
													@change="validateFormInput('product_serials')" 
													required="true" 
													:readonly="singleStockData.serials[stockedProductIndex-1].has_requisitions || singleStockData.serials[stockedProductIndex-1].has_dispatched"
												>

												<div class="invalid-feedback">
										        	{{ errors.stock.product_serials[stockedProductIndex-1] }}
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
												<button type="button" class="btn btn-outline-secondary btn-sm btn-round" v-on:click="step-=1">
							                    	<i class="fa fa-2x fa-angle-double-left" aria-hidden="true"></i>
							                  	</button>
												<button type="button" class="btn btn-outline-secondary btn-sm btn-round" v-on:click="nextPage">
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
													:disabled="singleStockData.addresses.length > 2" 
													@click="addMoreSpace()"
												>
													Add Space
												</button>
											</div>
											<div class="form-group col-md-6">
												<button 
													type="button" 
													class="btn waves-effect waves-light hor-grd btn-grd-info btn-sm btn-block" 
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
												<button type="button" class="btn btn-outline-secondary btn-sm btn-round float-left" v-on:click="product.has_serials ? step-=1 : step-=2">
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
						<h5 class="modal-title" id="exampleModalLabel">{{ this.product.name }} Stock Details</h5>
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

									<li class="nav-item" v-show="product.has_serials">
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
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Type :
											</label>
											<label class="col-sm-6 col-form-label text-left">
												{{ product.category ? product.category.name : 'Bulk Product' }}
											</label>
										</div>

										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Merchant :
											</label>
											<label class="col-sm-6 col-form-label text-left">
												{{ product.merchant ? product.merchant.user_name : 'None' }}
											</label>
										</div>

										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Name :
											</label>
											<label class="col-sm-6 col-form-label text-left">
												{{ product.name }}
											</label>
										</div>

										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												SKU Code :
											</label>
											<label class="col-sm-6 col-form-label text-left">
												{{ product.sku }}
											</label>
										</div>

										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Price :
											</label>
											<label class="col-sm-6 col-form-label text-left">
												{{ product.price || 'NA' }}
											</label>
										</div>

										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Description :
											</label>
											<label class="col-sm-6 col-form-label text-left">
												<span v-html="product.description"></span>
											</label>
										</div>

										
										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Available Qty:
											</label>
											<label class="col-sm-6 col-form-label text-left">
												{{ product.available_quantity }}
												{{ product.quantity_type }}
											</label>
										</div>
 										

										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">Has Variation :</label>
											<label class="col-sm-6 form-control-plaintext">
												<span :class="[product.has_variations ? 'badge-success' : 'badge-danger', 'badge']">{{ product.has_variations ? 'Available' : 'NA' }}</span>
											</label>
										</div>

										<div class="form-row" v-if="product.has_variations && product.variations.length">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
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
																	<label class="col-sm-6 col-form-label font-weight-bold text-right">
																		Name :
																	</label>
																	<label class="col-sm-6 col-form-label text-left">
																		{{ productVariation.variation ? productVariation.variation.name : 'NA' }}
																	</label>
																</div>

																<div class="form-row">
																	<label class="col-sm-6 col-form-label font-weight-bold text-right">
																		SKU :
																	</label>
																	<label class="col-sm-6 col-form-label text-left">
																		{{ productVariation.sku }}
																	</label>
																</div>

																<div class="form-row">
																	<label class="col-sm-6 col-form-label font-weight-bold text-right">Price :</label>
																	<label class="col-sm-6 col-form-label text-left">
																		{{ productVariation.price }}
																	</label>
																</div>

																
																<div class="form-row">
																	<label class="col-sm-6 col-form-label font-weight-bold text-right">Available Qty :</label>
																	<label class="col-sm-6 col-form-label text-left">
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
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Product Name :
											</label>

											<label class="col-sm-6 col-form-label text-left">
												{{ product.name | capitalize }}
											</label>
										</div>

										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Stock Quantity :
											</label>

											<div class="col-sm-6 col-form-label text-left">
												
												{{ singleStockData.stock_quantity }}
												
												<div class="form-row" v-if="singleStockData.hasOwnProperty('variations') && singleStockData.variations.length">
													<div 
														class="col-md-12" 
														v-for="(stockVariation, variationIndex) in singleStockData.variations" 
															:key="'product-variation-index-' + variationIndex + 'B'"
													>
														<div class="form-row">
															<label class="col-form-label font-weight-bold text-right">
																{{ stockVariation.variation.name | capitalize }} :
															</label>

															<label class="col-form-label text-left">
																{{ stockVariation.stock_quantity }}
															</label>
														</div>
														
														<!-- 
														<div class="form-row">
															<label class="col-form-label font-weight-bold text-right">
																Available Quantity :
															</label>
															<label class="col-form-label text-left">
																{{ stockVariation.available_quantity }}
															</label>
														</div>
														-->
													</div>
												</div>
											</div>
										</div>

										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Available Quantity (then) :
											</label>
											
											<label class="col-sm-6 col-form-label text-left">
												{{ singleStockData.available_quantity }}
											</label>
										</div>

										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Stocked on :
											</label>

											<label class="col-sm-6 col-form-label text-left">
												{{ singleStockData.created_at }}
											</label>
										</div>

										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Stocked at :
											</label>

											<label class="col-sm-6 col-form-label text-left">
												{{ singleStockData.hasOwnProperty('warehouse') ? singleStockData.warehouse.name : '' }}
											</label>
										</div>

										<div class="form-row" v-if="singleStockData.hasOwnProperty('keeper')">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Stored By :
											</label>

											<label class="col-sm-6 col-form-label text-left">
												{{ singleStockData.keeper.user_name | capitalize }}
											</label>
										</div>

										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Approval :
											</label>

											<label class="col-sm-6 col-form-label text-left">
												<span :class="[singleStockData.has_approval==1 ? 'badge-success' : singleStockData.has_approval==-1 ? 'badge-danger' : 'badge-secondary', 'badge']">
													{{ singleStockData.has_approval==1 ? 'Approved' : singleStockData.has_approval==-1 ? 'Cancelled' : 'NA' }}
												</span>
											</label>
										</div>

										<div class="form-row" v-if="singleStockData.has_approval">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												{{ singleStockData.has_approval==1 ? 'Approved' : 'Cancelled' }} By :
											</label>

											<label class="col-sm-6 col-form-label text-left">
												{{ singleStockData.approver.user_name | capitalize }}
											</label>
										</div>

										<div class="form-row" v-if="singleStockData.has_approval">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												{{ singleStockData.has_approval==1 ? 'Approved' : 'Cancelled' }} on :
											</label>

											<label class="col-sm-6 col-form-label text-left">
												{{ singleStockData.updated_at }}
											</label>
										</div>
									</div>

									<div class="tab-pane" id="product-serial" role="tabpanel" v-show="singleStockData.has_serials">
										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Serials :
											</label>
											<div class="col-sm-6 col-form-label text-left">
												<ol 
													v-if="singleStockData.has_serials && singleStockData.hasOwnProperty('serials') && singleStockData.serials.length"
												>
													<li v-for="(productSerial,productIndex) in singleStockData.serials">
														{{ productSerial.serial_no }}
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

															<label class="col-form-label text-left">
																{{ stockVariation.stock_quantity }}
																<ol 
																	v-if="singleStockData.has_serials && stockVariation.serials.length"
																>
																	<li v-for="(variationSerial, variationIndex) in stockVariation.serials">
																		{{ variationSerial.serial_no }}
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
															<label class="col-form-label text-left">
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
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
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
																	<label class="col-sm-6 col-form-label font-weight-bold text-right">
																		Warehouse :
																	</label>
																	<label class="col-sm-6 col-form-label text-left">
																		{{ containerAddress.warehouse_container ? $options.filters.capitalize(containerAddress.warehouse_container.warehouse.name) : 'NA' }}
																	</label>
																</div>

																<div class="form-row">
																	<label class="col-sm-6 col-form-label font-weight-bold text-right">
																		Container Type :
																	</label>
																	<label class="col-sm-6 col-form-label text-left">
																		{{ containerAddress.warehouse_container ? $options.filters.capitalize(containerAddress.warehouse_container.container.name) : 'NA' }}
																	</label>
																</div>

																<div class="form-row">
																	<label class="col-sm-6 col-form-label font-weight-bold text-right">
																		Container # :
																	</label>
																	<label class="col-sm-6 col-form-label text-left">
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
																	<label class="col-sm-6 col-form-label font-weight-bold text-right">
																		Container Type :
																	</label>
																	<label class="col-sm-6 col-form-label text-left">
																		{{ stockAddress.container.warehouse_container.container.name | capitalize }}
																	</label>
																</div>

																<div class="form-row">
																	<label class="col-sm-6 col-form-label font-weight-bold text-right">
																		Container # :
																	</label>
																	<label class="col-sm-6 col-form-label text-left">
																		{{ stockAddress.container.name.substring(stockAddress.container.name.indexOf("-")+1) }}
																	</label>
																</div>

																<div 
																	class="form-row"
																>
																	<label class="col-sm-6 col-form-label font-weight-bold text-right">
																		Shelf # :
																	</label>
																	<label class="col-sm-6 col-form-label text-left">

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
																	<label class="col-sm-6 col-form-label font-weight-bold text-right">
																		Container Type :
																	</label>
																	<label class="col-sm-6 col-form-label text-left">
																		{{ stockAddress.container.warehouse_container.container.name | capitalize }}
																	</label>
																</div>

																<div class="form-row">
																	<label class="col-sm-6 col-form-label font-weight-bold text-right">
																		Container # :
																	</label>
																	<label class="col-sm-6 col-form-label text-left">
																		{{ stockAddress.container.name.substring(stockAddress.container.name.indexOf("-")+1) }}
																	</label>
																</div>

																<div class="form-row">
																	<label class="col-sm-6 col-form-label font-weight-bold text-right">
																		Shelf # :
																	</label>
																	<label class="col-sm-6 col-form-label text-left">
																		{{ stockAddress.container.shelf.name.substring(stockAddress.container.shelf.name.lastIndexOf("-")+1) }}
																	</label>
																</div>

																<div class="form-row">
																	<label class="col-sm-6 col-form-label font-weight-bold text-right">
																		Unit # :
																	</label>
																	<label class="col-sm-6 col-form-label text-left">

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

	export default {

	    components: { 
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
	        	query : '',
	        	error : '',
    			perPage : 10,
	        	loading : false,

	        	createMode : true,
	        	submitForm : true,
	        	formSubmitted : false,

	        	allDealtEmptyWarehouses : [],

	        	allStocks : [],

	        	// allContainers : [],
	        	
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
	        		product_id : this.product.id,
	        		variations : this.product.hasOwnProperty('variations') && this.product.variations.length ? JSON.parse(JSON.stringify(this.product.variations)) : [],
					addresses : [
						{},
					],

	        	},

	        	errors : {
					stock : {
						// warehouse : {},
						variations : [],
						addresses : [],
						product_serials : [],
					},
				},

	            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

	        }

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

				this.query = '';
				this.error = '';
				this.loading = true;
				this.allStocks = [];
				
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
				
				this.query = '';
				this.error = '';
				this.loading = true;
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
						product_serials : [],
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
	        		product_id : this.product.id,
	        		variations : this.product.hasOwnProperty('variations') && this.product.variations.length ? JSON.parse(JSON.stringify(this.product.variations)) : [],
	        		warehouse : this.singleStockData.warehouse ?? {},  // last warehouse
					addresses : this.singleStockData.addresses,  // last address / initial address

	        	};

	        	if (this.product.has_serials && this.product.has_variations && this.product.hasOwnProperty('variations') && this.product.variations.length) {

					this.$delete(this.singleStockData, 'serials');

					this.product.variations.forEach(
					
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
				
				if (!this.verifyUserInput()) {
					this.submitForm = false;
					return;
				}

				this.formSubmitted = true;

				axios
					.post('/product-stocks/' + this.perPage, this.singleStockData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Stock has been stored", "Success");
							this.query !== '' ? this.searchData() : this.setAvailableContents(response);
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
							this.query !== '' ? this.searchData() : this.setAvailableContents(response);
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
							this.query !== '' ? this.searchData() : this.setAvailableContents(response);
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
						// this.fetchWarehouseAllContainers();
					});

			},
			searchData(emittedValue) {

				if (emittedValue) {
					this.query = emittedValue;
				}

				this.error = '';
				this.allStocks = [];
				this.pagination.current_page = 1;
				
				axios
				.get(
					"/api/search-product-stocks/" + this.product.id + '/' + this.query + "/" + this.perPage + "?page=" + this.pagination.current_page
				)
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

				if (this.errors.stock.constructor === Object && Object.keys(this.errors.stock).length < 4 && !this.errorInVariationsArray(this.errors.stock.variations) && !this.errorInAddressesArray(this.errors.stock.addresses)) {

					return true;
				
				}

				return false;
		
			},
			errorInProductSerialsArray(array = []) {

				const serialError = (serial) => {
					return serial && serial.length > 0
				}; 

				if (array.length) {
					return array.some(serialError);
				}

				return false;

			},
			errorInVariationsArray(array = []) {

				const variationSerialError = (serial) => {
					return serial != null
				};

				const variationError = (variation) => {

					return (! this.product.has_serials && this.product.has_variations && Object.keys(variation).length > 0) || (this.product.has_serials && ! this.product.has_variations && Object.keys(variation).length > 0) || (this.product.has_serials && this.product.has_variations && Object.keys(variation).length > 1) || (variation.hasOwnProperty('product_variation_serials') && variation.product_variation_serials.length && variation.product_variation_serials.some(variationSerialError))

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

					if (this.product.has_variations) {
						
						this.validateFormInput('product_variation_quantity');
						this.validateFormInput('product_variation_total_quantity');

					}

					if (this.errors.stock.constructor === Object && Object.keys(this.errors.stock).length < 4 && ! this.errorInVariationsArray(this.errors.stock.variations)) {

						if (this.product.has_serials) {

							if (this.product.has_variations) {

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

						this.submitForm = true;
						this.resetWarehouseSpaces();
						// this.fetchWarehouseAllContainers(this.singleStockData.warehouse.id);
					
					}
					else {
					
						this.submitForm = false;
					
					}

				}

				else if (this.step == 2) {

					if (this.product.has_serials) {

						if (this.product.has_variations) {
							
							this.validateFormInput('product_variation_serials');

						}
						else {
							
							this.validateFormInput('product_serials');

						}

					}

					if (this.errors.stock.constructor === Object && Object.keys(this.errors.stock).length < 4 && ! this.errorInProductSerialsArray(this.errors.stock.product_serials) && ! this.errorInVariationsArray(this.errors.stock.variations)) {

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
						variations : [],
						addresses : [],
						product_serials : [],
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

				if (this.product.category && this.product.has_variations && this.product.hasOwnProperty('variations') && this.product.variations.length) {
	
					this.product.variations.forEach(

						(productVariation, stockVariationIndex) => {

							this.errors.stock.variations.push({});

							if (this.product.has_serials) {

								// this.errors.stock.variations[stockVariationIndex].product_variation_serials = [];
								this.$set(this.errors.stock.variations[stockVariationIndex], 'product_variation_serials', []);

							}

						}

					);				

				}

			},
			setAvailableShelvesAndUnits() {

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

									// containers with empty shelves
									var selectedContainerIndex = this.emptyShelfContainers.findIndex(
										(currentContainer) => 
											currentContainer.id == selectedContainer.id && currentContainer.name == selectedContainer.name && currentContainer.warehouse_container_id == selectedContainer.warehouse_container_id
										
									);

									// console.log('Container Index in emptyShelfContainers : ' + selectedContainerIndex);

									if (selectedContainerIndex > -1) {

										this.emptyShelfContainers.splice(selectedContainerIndex, 1);
									
									}


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

							// downward

							// for every container-shelves with empty units
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
			setProductSerialObjects() {

				if (this.singleStockData.stock_quantity > 0 && this.singleStockData.serials.length < this.singleStockData.stock_quantity) {
					
					let difference = this.singleStockData.stock_quantity - this.singleStockData.serials.length;

					for (let i = 0; i < difference; i++) {
				  		this.singleStockData.serials.push({});
					}

				}
				else if (this.singleStockData.stock_quantity > 0 && this.singleStockData.serials.length > this.singleStockData.stock_quantity) {

					this.singleStockData.serials.splice(this.singleStockData.stock_quantity, );
					this.errors.stock.product_serials.splice(this.singleStockData.stock_quantity, );

				}

			},
			setProductVariationSerialObjects() {

				this.singleStockData.variations.forEach(
					
					(productVariation, index) => {

						if (productVariation.stock_quantity > 0 && productVariation.serials.length < productVariation.stock_quantity) {
							
							let difference = productVariation.stock_quantity - productVariation.serials.length;

							for (let i = 0; i < difference; i++) {
						  		productVariation.serials.push({});
							}

						}
						else if (productVariation.stock_quantity > 0 && productVariation.serials.length > productVariation.stock_quantity) {

							productVariation.serials.splice(productVariation.stock_quantity, );
							this.errors.stock.variations[index].product_variation_serials.splice(productVariation.stock_quantity, );

						}

					}
					
				);

			},
			changeNumberContents() {
				
				this.pagination.current_page = 1;

				if (this.query === '') {
					this.fetchProductAllStocks();
				}
				else {
					this.searchData();
				}
    		},
    		objectNameWithCapitalized ({ name }) {
		      	
		      	if (name) {
				    name = name.toString()
				    
				    const words = name.split(" ");

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

				const words = name.split(" ");

				for (let i = 0; i < words.length; i++) {
				    words[i] = words[i][0].toUpperCase() + words[i].substr(1);
				}

				return words.join(" ");

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

						if (this.product.has_variations) {

							this.singleStockData.variations.forEach(
								(productVariation, index) => {

									if (productVariation.stock_quantity < 0) {
										this.errors.stock.variations[index].product_variation_quantity = 'Variation quantity is invalid';
									}
									else if(! this.createMode && ((productVariation.primary_quantity - productVariation.stock_quantity) > this.allStocks[this.allStocks.length-1].variations[index].available_quantity)){

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

						if (this.product.has_variations) {

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

					case 'product_serials' :

						if (this.product.has_serials && ! this.product.has_variations) {
							
							for (let i = 0; i < this.singleStockData.stock_quantity; i++) {
								
								if (! this.singleStockData.serials[i] || Object.keys(this.singleStockData.serials[i]).length < 1 || ! this.singleStockData.serials[i].hasOwnProperty('serial_no')  || this.singleStockData.serials[i].serial_no.length < 1) {

									// this.errors.stock.product_serials[i] = 'Serial number is required';
									this.$set(this.errors.stock.product_serials, i, 'Serial number is required');
								
								}
								else if (! this.singleStockData.serials[i].serial_no.match(/^[a-zA-Z0-9-_]+$/)) {

									// this.errors.stock.product_serials[i] = 'Invalide serial number';
									this.$set(this.errors.stock.product_serials, i, 'Invalide serial number');

								}
								else if (this.singleStockData.serials.filter((value) => value.serial_no === this.singleStockData.serials[i].serial_no).length > 1) {

									this.$set(this.errors.stock.product_serials, i, 'Duplicate serial number');

								}
								else {

									// this.errors.stock.product_serials[i] = null;
									this.$set(this.errors.stock.product_serials, i, null);

								}

							}

							if (! this.errorInProductSerialsArray(this.errors.stock.product_serials)) {

								this.submitForm = true;

							}

						}
						else {

							this.submitForm = true;
							this.errors.stock.product_serials = [];
						
						}

						break;
					
					case 'product_variation_serials' :


						if (this.product.has_serials && this.product.has_variations && this.singleStockData.hasOwnProperty('variations') && this.singleStockData.variations.length) {
							
							this.singleStockData.variations.forEach(
							
								(stockVariation, stockVariationIndex) => {

									this.$set(this.errors.stock.variations[stockVariationIndex], 'product_variation_serials', []);

									for (let i = 0; i < stockVariation.stock_quantity; i++) {
										
										if (! stockVariation.serials[i] || Object.keys(stockVariation.serials[i]).length < 1 || ! stockVariation.serials[i].hasOwnProperty('serial_no') || stockVariation.serials[i].serial_no.length < 1) {

											// this.errors.stock.variations[stockVariationIndex].product_variation_serials[i] = 'Serial number is required';

											this.$set(this.errors.stock.variations[stockVariationIndex].product_variation_serials, i, 'Serial number is required');

										}
										else if (! stockVariation.serials[i].serial_no.match(/^[a-zA-Z0-9-_]+$/)) {

											// this.errors.stock.variations[stockVariationIndex].product_variation_serials[i] = 'Invalide serial number';

											this.$set(this.errors.stock.variations[stockVariationIndex].product_variation_serials, i, 'Invalide serial number');

										}
										else if (stockVariation.serials.filter((value) => value.serial_no === stockVariation.serials[i].serial_no).length > 1) {

											this.$set(this.errors.stock.variations[stockVariationIndex].product_variation_serials, i, 'Duplicate serial number');

										}
										else {

											// this.errors.stock.variations[stockVariationIndex].product_variation_serials[i] = null;

											this.$set(this.errors.stock.variations[stockVariationIndex].product_variation_serials, i, null);

										}

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

									this.errors.stock.variations[stockVariationIndex].product_variation_serials = [];

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