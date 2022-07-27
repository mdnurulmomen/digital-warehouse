
<template>
	<div class="pcoded-content">
		<breadcrumb 
			:icon="'products'"
			:title="'products'" 
			:message="'All your products'"
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
											<div class="col-sm-12 sub-title">
											  	<my-product-search-export-option
											  		:search-attributes="searchAttributes" 
											  		:caller-page="'my-products'" 
											  		:data-to-export="dataToExport" 
											  		:contents-to-download="productsToShow" 
											  		:pagination="pagination" 
											  		:current-tab="currentTab"
											  		
											  		@searchData="pagination.current_page = 1; searchData($event)" 
											  		@fetchAllContents="pagination.current_page = 1; fetchAllProducts()"
												/>
											</div>
											
											<div class="col-sm-12 col-lg-12">
												<loading v-show="loading"></loading>

												<tab 
										  			v-show="searchAttributes.search === '' && ! searchAttributes.dateFrom && ! searchAttributes.dateTo && ! loading" 
										  			:tab-names="['retail', 'bulk']" 
										  			:current-tab="'retail'" 

										  			@showRetailContents="showRetailContents" 
										  			@showBulkContents="showBulkContents" 
										  		></tab>

 												<div 
	 												class="tab-content card-block pl-0 pr-0" 
	 												v-show="!loading"
 												>
													<div class="card">
														<div class="table-responsive">
															<table class="table table-striped table-bordered nowrap text-center">
																<thead>
																	<tr>
																		<th>Name</th>
																		<th>SKU</th>
																		<!-- <td>{{ last requested at }}</td> -->
																		<th>Actions</th>
																	</tr>
																</thead>
																<tbody>

																	<tr 
																		v-for="content in productsToShow" 
																		:key="'content-' + content.id" 
																		:class="content.id==singleProductData.id ? 'highlighted' : ''"
																	>
																		<td>{{ content.name | capitalize }}</td>
																		<td>{{ content.sku }}</td>
																		<!-- <td>{{ last requested at }}</td> -->
																		
																		<td>
																			<button 
																				type="button" 
																				class="btn waves-effect waves-dark btn-info btn-outline-info btn-icon" 
																				v-tooltip.bottom-end="'View Details'"  
																				@click="showContentDetails(content)"
																			>
																				<i class="fa fa-eye"></i>
																			</button>

																			<button 
																				type="button" 
																				class="btn waves-effect waves-dark btn-success btn-outline-success btn-icon" 
																				v-tooltip.bottom-end="'Stocks'"  
																				@click="goProductStore(content)" 
																			>
																				<img src="/icons/cms/stocks.png" style="width: 17px">
																			</button>
																		</td>
																    
																	</tr>
																	<tr 
																  		v-show="!productsToShow.length"
																  	>
															    		<td colspan="3">
																      		<div class="alert alert-danger" role="alert">
																      			Sorry, No data found.
																      		</div>
																    	</td>
																  	</tr>

																</tbody>
																<tfoot>
																	<tr>	
																		<th>Name</th>
																		<th>SKU</th>
																		<th>Actions</th>
																	</tr>
																</tfoot>
															</table>
														</div>
													</div>
													<div class="row d-flex align-items-center align-content-center">
														<div class="col-sm-2">
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
														<div class="col-sm-2">
															<button 
																type="button" 
																class="btn waves-effect waves-dark btn-primary btn-outline-primary btn-sm" 
																v-tooltip.bottom-end="'Reload'" 
																@click="pagination.current_page = 1; searchAttributes.search === '' ? fetchAllProducts() : searchData()"
															>
																Reload
																<i class="fa fa-sync"></i>
															</button>
														</div>
														<div class="col-sm-8">
															<pagination
																v-if="pagination.last_page > 1"
																:pagination="pagination"
																:offset="5"
																@paginate="searchAttributes.search === '' ? fetchAllProducts() : searchData()"
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
		<div class="modal fade" id="merchant-product-view-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">{{ singleProductData.hasOwnProperty('product') ? singleProductData.product.name : 'NA' | capitalize }} Details</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="modal-body">
						<div class="card">
							<div class="card-body">
								<ul 
									class="nav nav-tabs tabs justify-content-center" role="tablist" 
									v-show="singleProductData.has_serials"
								>
									<li class="nav-item">
										<a class="nav-link active" data-toggle="tab" href="#merchant-product-profile" role="tab">
											Profile
										</a>
									</li>

									<li class="nav-item" v-show="singleProductData.hasOwnProperty('product') && singleProductData.product.has_serials">
										<a class="nav-link" data-toggle="tab" href="#product-serial" role="tab">
											Serials
										</a>
									</li>
								</ul>

								<div :class="singleProductData.has_serials ? 'tab-content tabs' : ''" class="card-block">
									<div class="tab-pane active" id="merchant-product-profile" role="tabpanel">
										<div class="form-row d-flex">
											<div class="col-md-4 align-self-center text-center">
												<img 
													width="100px" 
													:src="'/' + singleProductData.preview" 
													class="img-fluid" 
													alt="Product Preview" 
												>
											</div>

											<div class="col-md-8">
												<div class="form-row">
													<label class="col-4 col-form-label font-weight-bold">
														Merchant :
													</label>
													<label class="col-8 col-form-label">
														{{ merchant.user_name || 'Merchant' | capitalize }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-4 col-form-label font-weight-bold">
														Manufacturer/Brand Name :
													</label>
													<label class="col-8 col-form-label">
														{{ singleProductData.manufacturer ? singleProductData.manufacturer.name : 'own product' | capitalize }}
													</label>
												</div>

												<div class="form-row" v-show="singleProductData.sku">
													<label class="col-4 col-form-label font-weight-bold">
														Product SKU :
													</label>
													<label class="col-8 col-form-label">
														{{ singleProductData.sku }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-4 col-form-label font-weight-bold">
														Description :
													</label>
													<label class="col-8 col-form-label">
														<span v-html="singleProductData.description"></span>
													</label>
												</div>

												<div class="form-row">
													<label class="col-4 col-form-label font-weight-bold">
														Warning Qty :
													</label>
													<label class="col-8 col-form-label">
														{{ singleProductData.warning_quantity }}
														{{ singleProductData.product ? singleProductData.product.quantity_type : 'unit' }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-4 col-form-label font-weight-bold">
														Starting Qty :
													</label>
													<label class="col-8 col-form-label">
														{{ singleProductData.previous_quantity }}
														{{ singleProductData.product ? singleProductData.product.quantity_type : 'unit' }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-4 col-form-label font-weight-bold">
														Stocked Qty :
													</label>
													<label class="col-8 col-form-label">
														{{ (singleProductData.available_quantity + singleProductData.dispatched_quantity) }}
														{{ singleProductData.product ? singleProductData.product.quantity_type : 'unit' }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-4 col-form-label font-weight-bold">
														Dispatched Qty :
													</label>
													<label class="col-8 col-form-label">
														{{ singleProductData.dispatched_quantity }}
														{{ singleProductData.product ? singleProductData.product.quantity_type : 'unit' }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-4 col-form-label font-weight-bold">
														Pending Requested Qty :
													</label>
													<label class="col-8 col-form-label">
														{{ singleProductData.requested_quantity }}
														{{ singleProductData.product ? singleProductData.product.quantity_type : 'unit' }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-4 col-form-label font-weight-bold">
														Available Qty :
													</label>
													<label class="col-8 col-form-label">
														{{ (singleProductData.available_quantity + singleProductData.previous_quantity) }}
														{{ singleProductData.product ? singleProductData.product.quantity_type : 'unit' }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-4 col-form-label font-weight-bold">
														Unit Max Price :
													</label>
													<label class="col-8 col-form-label">
														{{ singleProductData.unit_max_price }}
														{{ general_settings.official_currency_name || 'BDT' | capitalize }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-4 col-form-label font-weight-bold">
														Unit Min Price :
													</label>
													<label class="col-8 col-form-label">
														{{ singleProductData.unit_min_price }}
														{{ general_settings.official_currency_name || 'BDT' | capitalize }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-4 col-form-label font-weight-bold">
														Unit Avg Price :
													</label>
													<label class="col-8 col-form-label">
														{{ singleProductData.unit_avg_price }}
														{{ general_settings.official_currency_name || 'BDT' | capitalize }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-4 col-form-label font-weight-bold">
														Stock Total Cost :
													</label>
													<label class="col-8 col-form-label">
														{{ singleProductData.stock_total_cost }}
														{{ general_settings.official_currency_name || 'BDT' | capitalize }}
													</label>
												</div>

												<div 
													class="form-row"
													v-show="singleProductData.hasOwnProperty('product') && ! singleProductData.product.has_variations"
												>
													<label class="col-4 col-form-label font-weight-bold">
														Selling Price (unit) :
													</label>
													<label class="col-8 col-form-label">
														{{ singleProductData.selling_price }} 
														{{ general_settings.official_currency_name || 'BDT' | capitalize }}
													</label>
												</div>

												<div 
													class="form-row"
													v-show="singleProductData.hasOwnProperty('product') && ! singleProductData.product.has_variations"
												>
													<label class="col-4 col-form-label font-weight-bold">
														Discount :
													</label>
													<label class="col-8 col-form-label">
														{{ singleProductData.discount || 0 }} %
													</label>
												</div>

												<div class="form-row">
													<label class="col-4 col-form-label font-weight-bold">Serials :</label>
													<label class="col-sm-6 form-control-plaintext">
														<span :class="[singleProductData.hasOwnProperty('product') && singleProductData.product.has_serials ? 'badge-info' : 'badge-primary', 'badge']">{{ singleProductData.hasOwnProperty('product') && singleProductData.product.has_serials ? 'Available' : 'NA' }}</span>
													</label>
												</div>

												<div class="form-row">
													<label class="col-4 col-form-label font-weight-bold">Variation :</label>
													<label class="col-sm-6 form-control-plaintext">
														<span :class="[singleProductData.hasOwnProperty('product') && singleProductData.product.has_variations ? 'badge-info' : 'badge-primary', 'badge']">{{ singleProductData.hasOwnProperty('product') && singleProductData.product.has_variations ? 'Available' : 'NA' }}</span>
													</label>
												</div>

												<div class="form-row">
													<label class="col-4 col-form-label font-weight-bold">
														Created on :
													</label>
													<label class="col-8 col-form-label">
														{{ singleProductData.created_at }}
													</label>
												</div>

												<div class="form-row" v-if="singleProductData.hasOwnProperty('product') && singleProductData.product.has_variations && singleProductData.hasOwnProperty('variations') && singleProductData.variations.length">
													<label class="col-4 col-form-label font-weight-bold">
														Variations :
													</label>
													<div class="col-sm-12">
														<div class="form-row">
															<div 
																class="col-md-6 ml-auto" 
																v-for="(merchantProductVariation, merchantProductVariationIndex) in singleProductData.variations" 
																:key="'viewing-merchant-product-variation-index-' + merchantProductVariationIndex + '-viewing-variation-' + merchantProductVariation.id"
															>
																<div class="card">
																	<div class="card-body">
																		<div class="form-row">
																			<div class="col-sm-12 text-center">
																				<img 
																					class="img-fluid" 
																					:src="'/' + merchantProductVariation.preview || ''"
																					:alt="merchantProductVariation.variation ? merchantProductVariation.variation.name : 'NA' + 'Preview'" 
																					width="100px"
																				>

																				<p>
																					{{ merchantProductVariation.variation ? merchantProductVariation.variation.name : 'NA' | capitalize }}
																				</p>
																			</div>
																		</div>

																		<div class="form-row">
																			<label class="col-4 col-form-label font-weight-bold">
																				Variation :
																			</label>
																			<label class="col-8 col-form-label">
																				{{ merchantProductVariation.variation ? merchantProductVariation.variation.name : 'NA' | capitalize }}
																			</label>
																		</div>

																		<div class="form-row">
																			<label class="col-4 col-form-label font-weight-bold">
																				SKU :
																			</label>
																			<label class="col-8 col-form-label">
																				{{ merchantProductVariation.sku }}
																			</label>
																		</div>

																		<div class="form-row">
																			<label class="col-4 col-form-label font-weight-bold">
																				Selling Price (unit) :
																			</label>
																			<label class="col-8 col-form-label">
																				{{ merchantProductVariation.selling_price }}
																				{{ general_settings.official_currency_name || 'BDT' | capitalize }}
																			</label>
																		</div>

																		<div class="form-row">
																			<label class="col-4 col-form-label font-weight-bold">
																				Available Qty :
																			</label>
																			<label class="col-8 col-form-label">
																				{{ (merchantProductVariation.available_quantity + merchantProductVariation.previous_quantity) }} {{ singleProductData.product ? singleProductData.product.quantity_type : 'unit' }}
																			</label>
																		</div>

																		<div class="form-row">
																			<label class="col-4 col-form-label font-weight-bold">
																				Unit Max Price :
																			</label>
																			<label class="col-8 col-form-label">
																				{{ merchantProductVariation.unit_max_price }}
																				{{ general_settings.official_currency_name || 'BDT' | capitalize }}
																			</label>
																		</div>

																		<div class="form-row">
																			<label class="col-4 col-form-label font-weight-bold">
																				Unit Min Price :
																			</label>
																			<label class="col-8 col-form-label">
																				{{ merchantProductVariation.unit_min_price }}
																				{{ general_settings.official_currency_name || 'BDT' | capitalize }}
																			</label>
																		</div>

																		<div class="form-row">
																			<label class="col-4 col-form-label font-weight-bold">
																				Unit Avg Price :
																			</label>
																			<label class="col-8 col-form-label">
																				{{ merchantProductVariation.unit_avg_price }}
																				{{ general_settings.official_currency_name || 'BDT' | capitalize }}
																			</label>
																		</div>

																		<div class="form-row">
																			<label class="col-4 col-form-label font-weight-bold">
																				Variation-Stock Total Cost :
																			</label>
																			<label class="col-8 col-form-label">
																				{{ merchantProductVariation.stock_total_cost }}
																				{{ general_settings.official_currency_name || 'BDT' | capitalize }}
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

									<div class="tab-pane" id="product-serial" role="tabpanel" v-show="singleProductData.hasOwnProperty('product') && singleProductData.product.has_serials">
										<div class="form-row">
											<label class="col-4 col-form-label font-weight-bold">
												{{ (searchAttributes.dateFrom || searchAttributes.dateTo) ? 'Stocked' : 'Available' }} Serials :
											</label>
											<div class="col-8 col-form-label">
												<ol 
													v-if="singleProductData.hasOwnProperty('serials') && singleProductData.serials.length"
												>
													<li v-for="(productSerial, productIndex) in singleProductData.serials">
														{{ productSerial.serial_no }}

														<span :class="[productSerial.has_dispatched ? 'badge badge-danger' : productSerial.has_requisitions ? 'badge badge-warning' : '']">
															{{ productSerial.has_dispatched ? 'Dispatched' : productSerial.has_requisitions ? 'Requested' : '' }}
														</span>

														<span v-show="(productIndex + 1) < singleProductData.serials.length">, </span> 
													</li>	
												</ol>
												
												<div class="form-row" v-if="singleProductData.hasOwnProperty('variations') && singleProductData.variations.length">
													<div 
														class="col-md-12" 
														v-for="(merchantProductVariation, variationIndex) in singleProductData.variations" 
														:key="'viewing-merchant-product-variation-index-' + variationIndex + '-viewing-variation-' + merchantProductVariation.id"
													>
														<div class="form-row">
															<label class="col-form-label font-weight-bold text-right">
																{{ merchantProductVariation.variation ? merchantProductVariation.variation.name : 'NA' | capitalize }} |
															</label>

															<label class="col-form-label text-left">
																{{ merchantProductVariation.stock_quantity }}
																<ol 
																	v-if="merchantProductVariation.hasOwnProperty('serials') && merchantProductVariation.serials.length"
																>
																	<li v-for="(variationSerial, variationIndex) in merchantProductVariation.serials">
																		{{ variationSerial.serial_no }}

																		<span :class="[variationSerial.has_dispatched ? 'badge badge-danger' : variationSerial.has_requisitions ? 'badge badge-warning' : '']">
																			{{ variationSerial.has_dispatched ? 'Dispatched' : variationSerial.has_requisitions ? 'Requested' : '' }}
																		</span>

																		<span v-show="(variationIndex + 1) < merchantProductVariation.serials.length">, </span> 
																	</li>	
																</ol>
															</label>
														</div>
														
														<!-- 
														<div class="form-row">
															<label class="col-form-label font-weight-bold text-right">
																Available Qty :
															</label>
															<label class="col-form-label text-left">
																{{ merchantProductVariation.available_quantity }}
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

					<div class="modal-footer">
						<button type="button" class="btn waves-effect waves-dark btn-secondary btn-outline-secondary mr-auto" data-dismiss="modal">Close</button>
						<button 
							type="button" 
							class="btn waves-effect waves-dark btn-primary btn-outline-primary" 
							v-tooltip.bottom-end="'Print'"  
							@click="print"
						>
							Print
						</button>
					</div>
				</div>
			</div>
		</div>

		<!-- Printing Section -->
		<div id="sectionToPrint" class="d-none">
			<div class="card">
				<div class="card-header">
					<div class="form-row d-flex">
						<div class="col-6">
							<img 
								width="60px" 
								class="img-fluid" 
								:src="'/' + general_settings.logo" 
								:alt="general_settings.app_name + ' Logo'"
							>
							
							<h5>
								Merchant Product Details
							</h5>
						</div>

						<div class="col-6 align-self-center" v-show="singleProductData.sku">
							<qr-code 
							:text="singleProductData.sku || ''"
							:size="50" 
							class="float-right"
							></qr-code>
						</div>
					</div>
				</div>
			</div>

			<div class="card card-body form-group">
				<h5 class="card-title">{{ singleProductData.product ? singleProductData.product.name : 'Product' | capitalize }}</h5>
				
				<h6 class="card-subtitle mb-2 text-muted">
					{{ singleProductData.manufacturer ? singleProductData.manufacturer.name : 'own product' | capitalize }}
				</h6>

				<div class="card-text">
					<div class="form-row">
						<div class="col-6">
							<div class="form-row">
								<label class="col-4 col-form-label font-weight-bold">
									Merchant :
								</label>
								<label class="col-8 col-form-label">
									{{ merchant.user_name || 'Merchant' | capitalize }}
								</label>
							</div>

							<!-- 
							<div class="form-row">
								<label class="col-4 col-form-label font-weight-bold">
									Manufacturer/Brand Name :
								</label>
								<label class="col-8 col-form-label">
									
								</label>
							</div> 

							<div class="form-row">
								<label class="col-4 col-form-label font-weight-bold">
									Product SKU :
								</label>
								<label class="col-8 col-form-label">
									{{ singleProductData.sku }}
								</label>
							</div>
							-->

							<div 
								class="form-row"
								v-show="singleProductData.hasOwnProperty('product') && ! singleProductData.product.has_variations"
							>
								<label class="col-4 col-form-label font-weight-bold">
									Selling Price (unit) :
								</label>
								<label class="col-8 col-form-label">
									{{ singleProductData.selling_price }}
									{{ general_settings.official_currency_name || 'BDT' | capitalize }}
								</label>
							</div>

							<div 
								class="form-row"
								v-show="singleProductData.hasOwnProperty('product') && ! singleProductData.product.has_variations"
							>
								<label class="col-4 col-form-label font-weight-bold">
									Discount :
								</label>
								<label class="col-8 col-form-label">
									{{ singleProductData.discount || 0 }} %
								</label>
							</div>

							<div class="form-row" v-show="singleProductData.description">
								<label class="col-4 col-form-label font-weight-bold">
									Description :
								</label>
								<label class="col-8 col-form-label">
									<span v-html="singleProductData.description"></span>
								</label>
							</div>
						</div>

						<div class="col-6">
							<div class="form-row">
								<label class="col-4 col-form-label font-weight-bold">
									Warning Qty :
								</label>
								<label class="col-8 col-form-label">
									{{ singleProductData.warning_quantity }}
									{{ singleProductData.product ? singleProductData.product.quantity_type : 'unit' }}
								</label>
							</div>

							<div class="form-row">
								<label class="col-4 col-form-label font-weight-bold">Serials :</label>
								<label class="col-8 form-control-plaintext">
									<span :class="[singleProductData.hasOwnProperty('product') && singleProductData.product.has_serials ? 'badge-info' : 'badge-primary', 'badge']">{{ singleProductData.hasOwnProperty('product') && singleProductData.product.has_serials ? 'Available' : 'NA' }}</span>
								</label>
							</div>

							<div class="form-row">
								<label class="col-4 col-form-label font-weight-bold">Variation :</label>
								<label class="col-8 form-control-plaintext">
									<span :class="[singleProductData.hasOwnProperty('product') && singleProductData.product.has_variations ? 'badge-info' : 'badge-primary', 'badge']">{{ singleProductData.hasOwnProperty('product') && singleProductData.product.has_variations ? 'Available' : 'NA' }}</span>
								</label>
							</div>

							<div class="form-row">
								<label class="col-4 col-form-label font-weight-bold">
									Created on :
								</label>
								<label class="col-8 col-form-label">
									{{ singleProductData.created_at }}
								</label>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div 
				class="card card-body form-group"
				v-show="singleProductData.hasOwnProperty('product') && singleProductData.product.has_variations && singleProductData.hasOwnProperty('variations') && singleProductData.variations.length"
			>
				<div class="form-row" v-if="singleProductData.hasOwnProperty('product') && singleProductData.product.has_variations && singleProductData.hasOwnProperty('variations') && singleProductData.variations.length">
					<label class="col-4 col-form-label font-weight-bold">
						Variations :
					</label>
					<div class="col-sm-12">
						<div class="form-row">
							<div 
								class="col-6" 
								v-for="(merchantProductVariation, merchantProductVariationIndex) in singleProductData.variations" 
								:key="'printing-merchant-product-variation-index-' + merchantProductVariationIndex + '-printing-variation-' + merchantProductVariation.id"
							>
								<div class="card">
									<div class="card-body">
										<!-- 
										<div class="form-row">
											<div class="col-sm-12 text-center">
												<img 
													class="img-fluid" 
													:src="'/' + merchantProductVariation.preview || ''"
													:alt="merchantProductVariation.variation ? merchantProductVariation.variation.name : 'NA' + 'Preview'" 
													width="100px"
												>

												<p>
													{{ merchantProductVariation.variation ? merchantProductVariation.variation.name : 'NA' | capitalize }}
												</p>
											</div>
										</div> 
										-->

										<div class="form-row">
											<label class="col-4 col-form-label font-weight-bold">
												Variation :
											</label>
											<label class="col-8 col-form-label">
												{{ merchantProductVariation.variation ? merchantProductVariation.variation.name : 'NA' | capitalize }}
											</label>
										</div>

										<div class="form-row">
											<label class="col-4 col-form-label font-weight-bold">
												SKU :
											</label>
											<label class="col-8 col-form-label">
												{{ merchantProductVariation.sku }}
											</label>
										</div>

										<div class="form-row">
											<label class="col-4 col-form-label font-weight-bold">
												Selling Price (unit) :
											</label>
											<label class="col-8 col-form-label">
												{{ merchantProductVariation.selling_price }}
												{{ general_settings.official_currency_name || 'BDT' | capitalize }}
											</label>
										</div>

										<div class="form-row">
											<label class="col-4 col-form-label font-weight-bold">
												Available Qty :
											</label>
											<label class="col-8 col-form-label">
												{{ (merchantProductVariation.available_quantity + merchantProductVariation.previous_quantity) }} {{ singleProductData.product ? singleProductData.product.quantity_type : 'unit'  }}
											</label>
										</div>

										<div class="form-row">
											<label class="col-4 col-form-label font-weight-bold">
												Unit Max Price :
											</label>
											<label class="col-8 col-form-label">
												{{ merchantProductVariation.unit_max_price }}
												{{ general_settings.official_currency_name || 'BDT' | capitalize }}
											</label>
										</div>

										<div class="form-row">
											<label class="col-4 col-form-label font-weight-bold">
												Unit Min Price :
											</label>
											<label class="col-8 col-form-label">
												{{ merchantProductVariation.unit_min_price }}
												{{ general_settings.official_currency_name || 'BDT' | capitalize }}
											</label>
										</div>

										<div class="form-row">
											<label class="col-4 col-form-label font-weight-bold">
												Unit Avg Price :
											</label>
											<label class="col-8 col-form-label">
												{{ merchantProductVariation.unit_avg_price }}
												{{ general_settings.official_currency_name || 'BDT' | capitalize }}
											</label>
										</div>

										<div class="form-row">
											<label class="col-4 col-form-label font-weight-bold">
												Stock Total Cost :
											</label>
											<label class="col-8 col-form-label">
												{{ merchantProductVariation.stock_total_cost }}
												{{ general_settings.official_currency_name || 'BDT' | capitalize }}
											</label>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="card card-body" v-show="singleProductData.has_serials">
				<h5 class="card-title">Available Serials</h5>

				<ol 
					class="form-row card-text"
					v-if="singleProductData.hasOwnProperty('serials') && singleProductData.serials.length"
				>
					<li 
						class="col-6 list-group-item"
						v-for="(productSerial, productSerialIndex) in singleProductData.serials"
					>
						
						{{ ((productSerialIndex + 1) + '. ' + productSerial.serial_no) }}

						<span :class="[productSerial.has_dispatched ? 'badge badge-danger' : productSerial.has_requisitions ? 'badge badge-warning' : '']">
							{{ productSerial.has_dispatched ? 'Dispatched' : productSerial.has_requisitions ? 'Requested' : '' }}
						</span>
															
						<!-- 
							<span v-show="(productIndex + 1) < singleProductData.serials.length">, </span>  
						-->
					</li>	
				</ol>
				
				<div 
					class="form-row" 
					v-else-if="singleProductData.hasOwnProperty('variations') && singleProductData.variations.length"
				>
					<div 
						class="col-md-12 card card-body" 
						v-for="(merchantProductVariation, variationIndex) in singleProductData.variations" 
						:key="'printing-product-variation-index-' + variationIndex + '-printing-variation-' + merchantProductVariation.id"
					>
						<h5 class="card-title">{{ merchantProductVariation.variation ? merchantProductVariation.variation.name : 'NA' | capitalize }}</h5>
						
						<h6 class="card-subtitle mb-2 text-muted">{{ merchantProductVariation.stock_quantity }}</h6>
						
						<div class="card-text">
							<ol 
								class="form-row"
								v-if="merchantProductVariation.hasOwnProperty('serials') && merchantProductVariation.serials.length"
							>
								<li 
									class="col-6 list-group-item"
									v-for="(variationSerial, variationSerialIndex) in merchantProductVariation.serials"
								>
									{{ ((variationSerialIndex + 1) + '. ' + variationSerial.serial_no) }}

									<span :class="[variationSerial.has_dispatched ? 'badge badge-danger' : variationSerial.has_requisitions ? 'badge badge-warning' : '']">
										{{ variationSerial.has_dispatched ? 'Dispatched' : variationSerial.has_requisitions ? 'Requested' : '' }}
									</span>

									<!-- 
										<span v-show="(variationIndex + 1) < merchantProductVariation.serials.length">, </span>  
									-->
								</li>	
							</ol>
						</div>
					</div>
				</div>
			</div>

			<div class="card card-body form-group">
				<h5 class="card-title">Stocks</h5>
				
				<h6 class="card-subtitle mb-2 text-muted">
					{{ searchAttributes.dateFrom ? searchAttributes.dateFrom : singleProductData.created_at }} -- {{ searchAttributes.dateTo ? searchAttributes.dateTo : currentTime }}
				</h6>

				<div class="card-text">
					<div class="form-row">
						<div class="col-6">
							<div class="form-row">
								<label class="col-4 col-form-label font-weight-bold">
									Starting Qty :
								</label>
								<label class="col-8 col-form-label">
									{{ singleProductData.previous_quantity }}
									{{ singleProductData.product ? singleProductData.product.quantity_type : 'unit' }}
								</label>
							</div>

							<div class="form-row">
								<label class="col-4 col-form-label font-weight-bold">
									Stocked Qty :
								</label>
								<label class="col-8 col-form-label">
									{{ (singleProductData.available_quantity + singleProductData.dispatched_quantity) }}
									{{ singleProductData.product ? singleProductData.product.quantity_type : 'unit' }}
								</label>
							</div>

							<div class="form-row">
								<label class="col-4 col-form-label font-weight-bold">
									Dispatched Qty :
								</label>
								<label class="col-8 col-form-label">
									{{ singleProductData.dispatched_quantity }}
									{{ singleProductData.product ? singleProductData.product.quantity_type : 'unit' }}
								</label>
							</div>

							<div class="form-row">
								<label class="col-4 col-form-label font-weight-bold">
									Pending Requested Qty :
								</label>
								<label class="col-8 col-form-label">
									{{ singleProductData.requested_quantity }}
									{{ singleProductData.product ? singleProductData.product.quantity_type : 'unit' }}
								</label>
							</div>

							<div class="form-row">
								<label class="col-4 col-form-label font-weight-bold">
									Available Qty :
								</label>
								<label class="col-8 col-form-label">
									{{ (singleProductData.available_quantity + singleProductData.previous_quantity) }}
									{{ singleProductData.product ? singleProductData.product.quantity_type : 'unit' }}
								</label>
							</div>
						</div>
							
						<div class="col-6">
							<div class="form-row">
								<label class="col-4 col-form-label font-weight-bold">
									Unit Max Price :
								</label>
								<label class="col-8 col-form-label">
									{{ singleProductData.unit_max_price }}
									{{ general_settings.official_currency_name || 'BDT' | capitalize }}
								</label>
							</div>

							<div class="form-row">
								<label class="col-4 col-form-label font-weight-bold">
									Unit Min Price :
								</label>
								<label class="col-8 col-form-label">
									{{ singleProductData.unit_min_price }}
									{{ general_settings.official_currency_name || 'BDT' | capitalize }}
								</label>
							</div>

							<div class="form-row">
								<label class="col-4 col-form-label font-weight-bold">
									Unit Avg Price :
								</label>
								<label class="col-8 col-form-label">
									{{ singleProductData.unit_avg_price }}
									{{ general_settings.official_currency_name || 'BDT' | capitalize }}
								</label>
							</div>

							<div class="form-row">
								<label class="col-4 col-form-label font-weight-bold">
									Stock Total Cost :
								</label>
								<label class="col-8 col-form-label">
									{{ singleProductData.stock_total_cost }}
									{{ general_settings.official_currency_name || 'BDT' | capitalize }}
								</label>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- Printing Section -->

		<!-- Delete Modal -->
		<div class="modal fade" id="delete-confirmation-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<form 
						class="form-horizontal" 
						v-on:submit.prevent="deleteProductMerchant" 
						autocomplete="off"
					>
						<input type="hidden" name="_token" :value="csrf">

						<div class="modal-header bg-danger">
							<h5 class="modal-title" id="exampleModalLongTitle">Delete Confirmation</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
							</button>
						</div>

						<div class="modal-body text-center">
							<h4 class="text-danger">Want to delete '{{ singleProductData.hasOwnProperty('product') ? singleProductData.product.name : '' | capitalize }}' ?</h4>
							<h6 class="sub-heading text-secondary">Warning : You can not restore this item !</h6>
						</div>

						<div class="modal-footer">
							<button type="button" class="btn waves-effect waves-dark btn-secondary btn-outline-secondary mr-auto" data-dismiss="modal">Close</button>
							<button type="submit" class="btn waves-effect waves-dark btn-primary btn-outline-primary">Delete</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript">

	import axios from 'axios';
	import Multiselect from 'vue-multiselect';
	import CKEditor from '@ckeditor/ckeditor5-vue';
	import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

    let singleProductData = {
    	// name : null,
    	// description : null,
    	// sku : null,
    	// selling_price : null,
    	// initial_quantity : null,
    	// available_quantity : null,
    	// quantity_type : null,
    	// has_variations : null,
    	
    	// product : {},
    	// variations : [],
		
		/*
			addresses : [
				{},
			],
		*/
	
    	// product_category_id : null,
    	// merchant_id : null,
    	// category : {},
    	// merchant : {},
    };

	export default {

	    components: { 
			multiselect : Multiselect,
			ckeditor: CKEditor.component,
		},

	    data() {

	        return {

	        	// step : 1,
	        	// query : '',
	        	error : '',
    			perPage : 10,
	        	loading : false,

	        	currentTab : 'retail',

	        	productsToShow : [],
	        	allFetchedProducts : [],

	        	pagination: {
		        	current_page: 1
		      	},

	        	singleProductData : singleProductData,

				searchAttributes : {

	        		dates : {},
	        		search : '',
		        	dateTo : null,
		        	dateFrom : null

	        	},

				dataToExport: {

					"Product": {
						field: "product",
						callback: (product) => {
							return this.$options.filters.capitalize(product.name)
						},
					},
					
					"Manufacturer": {
						field: "manufacturer",
						callback: (manufacturer) => {
							if (manufacturer) {
								return this.$options.filters.capitalize(manufacturer.name);
							}
							else{
								return 'Own Product'
							}
						},
					},

					SKU: 'sku',

					// Price: 'selling_price',

					"Discount": {
						field: "discount",
						callback: (discount) => {
							if (discount) {
								return discount + ' %';
							}
							else{
								return 0  + ' %';
							}
						},
					},

					'Starting Qty': {
						callback: (object) => {
							if (object) {
								return object.previous_quantity
							}
							else{
								return 0;
							}
						},
					},

					
					'Stocked Qty': {
						callback: (object) => {
							if (object) {
								return (object.available_quantity + object.dispatched_quantity)
							}
							else{
								return 0;
							}
						},
					},

					'Dispatched': 'dispatched_quantity',

					'Pending Requests': 'requested_quantity',

					// "Available Qty": 'available_quantity',
					
					'Available Qty': {
						callback: (object) => {
							if (object) {
								return (object.previous_quantity + object.available_quantity)
							}
							else{
								return 0;
							}
						},
					},

					"Qty Type": {
						field: "product",
						callback: (product) => {
							return this.$options.filters.capitalize(product.quantity_type)
						},
					},

					'Unit Max Price' : 'unit_max_price',

					'Unit Min Price' : 'unit_min_price',

					'Unit Avg Price' : 'unit_avg_price',

					'Stock Total Cost' : 'stock_total_cost', 

					'Currency' : {
						callback: (object) => {
							return this.general_settings ? this.general_settings.official_currency_name : 'BDT'
						},
					},

					"Variations": {

						callback: (object) => {

							let infosToReturn = '';

							if (object.has_variations && object.variations.length) {

								object.variations.forEach(
					
									(objectVariation, variationIndex) => {

										infosToReturn += ((variationIndex + 1) + '. ' + this.$options.filters.capitalize(objectVariation.variation.name) + ", \n" + 'Available Qty: ' + ((objectVariation.available_quantity + objectVariation.previous_quantity) + ' ' + (object.product ? object.product.quantity_type : 'unit')) + ", \n" + 'Unit Max Price: ' + objectVariation.unit_max_price + ", \n" + 'Unit Min Price: ' + objectVariation.unit_min_price + ", \n" + 'Unit Avg Price: ' + objectVariation.unit_avg_price + ", \n"   + 'Variation-Stock Total Cost: ' + objectVariation.stock_total_cost + ' ' + this.general_settings.official_currency_name  + "\n\n");

									}
									
								);


							}
							else {

								infosToReturn += 'NA';

							}
							
							return infosToReturn;

						},
					},

					"Serials": {

						callback: (object) => {

							let infosToReturn = '';

							// infosToReturn += (object.available_quantity ?? 0) + ' ';

							if (object.has_serials && ! object.has_variations && object.serials.length) {

								infosToReturn += (((this.searchAttributes.dateFrom || this.searchAttributes.dateTo) ? 'Stocked' : 'Available') + ` Serials:
																`);

								object.serials.forEach(
			
									(objectSerial, objectSerialIndex) => {

										infosToReturn +=  (objectSerialIndex + 1) + ". " + objectSerial.serial_no + (objectSerial.has_dispatched ? ' (Dispatched)' : '')  + "\n";					

									}
									
								);

							}

							if (object.has_variations && object.variations.length) {

								object.variations.forEach(
					
									(objectVariation, variationIndex) => {

										if (object.has_serials && objectVariation.serials.length) {

											infosToReturn += ((this.$options.filters.capitalize(objectVariation.variation.name) + (this.searchAttributes.dateFrom || this.searchAttributes.dateTo) ? 'Stocked' : 'Available') + " Serials:\n");

											objectVariation.serials.forEach(
						
												(variationSerial, variationSerialIndex) => {

													infosToReturn +=  (variationSerialIndex + 1) + ". " + variationSerial.serial_no + (variationSerial.has_dispatched ? ' (Dispatched)' : '') + "\n";					

												}
												
											);

										}

										infosToReturn += "\n";

									}
									
								);

							}

							else {

								infosToReturn += "NA";	
							
							}
							
							return infosToReturn;

						},
					},

					"Qty Type": {
						callback: (object) => {
							if (object) {
								return object.product ? object.product.quantity_type : 'Unit'
							}
							else{
								return;
							}
						},
					},

					/*
					"Warning Qty": {
						callback: (object) => {
							return (object.warning_quantity + ' ' + object.product ? object.product.quantity_type : ' Unit');
						},
					},
					*/

					"Created": 'created_at',
					
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
					windowTitle: 'Requisition Details' 

				},

				merchant : JSON.parse(window.localStorage.getItem('merchant')),

				general_settings : JSON.parse(window.localStorage.getItem('general_settings')),

	            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

	        }

		},
		
		created(){

			this.fetchMyAllProducts();

		},

		computed: {

			merchantFullName: function() {

				return this.merchant ? (this.merchant.first_name + ' ' + this.merchant.last_name) : 'No Name';

			},

			currentTime: function() {

				let date = new Date();
				// return date.getFullYear() + '/' +  (date.getMonth() + 1) + '/' + date.getDate() + ' ' + date.getHours() + ':' + date.getMinutes();
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
		
		methods : {

			fetchMyAllProducts() {

				// this.query = '';
				this.error = '';
				this.loading = true;
				this.allFetchedProducts = [];
				this.searchAttributes.search = '';
				
				axios
					.get('/api/my-products/' + this.perPage + "?page=" + this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							this.allFetchedProducts = response.data;
							this.showSelectedTabProducts();
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
			showContentDetails(object) {		
				this.singleProductData = { ...object };
				// this.singleProductData = Object.assign({}, this.singleProductData, object);
				$('#merchant-product-view-modal').modal('show');
			},
			searchData(emitedObject=false) {

				if (Object.keys(emitedObject).length) {
					this.searchAttributes=emitedObject;
				}

				this.error = '';
				this.allFetchedProducts = [];
				// this.pagination.current_page = 1;
				
				axios
				.post('/search-my-products/' + this.perPage + "?page=" + this.pagination.current_page, this.searchAttributes)
				.then(response => {
					this.allFetchedProducts = response.data;
					this.productsToShow = this.allFetchedProducts.all.data;
					this.pagination = this.allFetchedProducts.all;
				})
				.catch(e => {
					this.error = e.toString();
				});

			},
			changeNumberContents() {
				
				this.pagination.current_page = 1;

				if (this.searchAttributes.search === '' && ! this.searchAttributes.dateTo && ! this.searchAttributes.dateFrom) {
					this.fetchMyAllProducts();
				}
				else {
					this.searchData();
				}

    		},
    		showRetailContents() {
				this.currentTab = 'retail';
				this.showSelectedTabProducts();
			},
			showBulkContents() {
				this.currentTab = 'bulk';
				this.showSelectedTabProducts();
			},
    		showSelectedTabProducts() {
				
				if (this.currentTab=='retail') {
					this.productsToShow = this.allFetchedProducts.retail ? this.allFetchedProducts.retail.data : [];
					this.pagination = this.allFetchedProducts.retail;
				}
				else {
					this.productsToShow = this.allFetchedProducts.bulk ? this.allFetchedProducts.bulk.data : [];
					this.pagination = this.allFetchedProducts.bulk;
				}

			},
			goProductRequisitions(object) {

				// console.log(object);
				this.$router.push({ name: 'product-requisitions', params: { productId: object.product ? object.product.id : null , merchantId: this.merchant.id, merchantProduct: object }});

			},
			goProductStore(object) {

				// console.log(object);
				this.$router.push({ name: 'my-product-stocks', params: { productMerchant: object, product: object.product, merchantProductId: object.id }});

			},
			showPreview(imagePath = 'default') {
				
				if (imagePath != null && imagePath.startsWith('data:')) {
					return imagePath;
				}
				else {
					return '/' + imagePath;
				}

				// return '';

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
            print() {

				// this.printingStyles.name = `${ this.singleProductData.subject } Details`;
				this.printingStyles.windowTitle = this.$options.filters.capitalize(`${ this.singleProductData.product.name } Details`);

				this.$htmlToPaper('sectionToPrint', this.printingStyles);

				$('#product-view-modal').modal('hide');

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