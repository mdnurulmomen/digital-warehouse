
<template v-if="userHasPermissionTo('view-merchant-product-index')">

	<div class="pcoded-content">

		<breadcrumb 
			:icon="'fab fa-product-hunt'"
			:title="merchantFullName + ' products'" 
			:message="'All our products for ' + merchantFullName + ' (' + merchant.user_name + ')' | capitalize"
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
												<div class="row form-group">
											  		<div class="col-sm-6 d-flex align-items-center form-group">
											  			<div class="mr-2">
											  				<span>
													  			{{ 
													  				( searchAttributes.search || searchAttributes.dateFrom || searchAttributes.dateTo) ? 'Searched Products List' : 'Products List'
													  			}}
											  				</span>
											  			</div>

											  			<div class="ml-auto ml-sm-0">
											  				<!-- 
											  				<div>
											  					<i class="fa fa-print fa-lg p-2" aria-hidden="true"></i>
											  				</div> 
											  				-->

											  				<div class="dropdown">
										  						<i class="fas fa-download fa-lg dropdown-toggle" data-toggle="dropdown"></i>
											  					
											  					<div class="dropdown-menu">
										  							<download-excel 
														  				class="btn btn-default p-1 dropdown-item active"
																		:data="productsToShow"
																		:fields="dataToExport" 
																		:worksheet="merchant.user_name + '-products-sheet'"
																		:name="merchant.user_name + '-' + ((searchAttributes.search != '' || searchAttributes.dateFrom || searchAttributes.dateTo) ? 'searched-products-' : (currentTab + '-products-list-')) + currentTime + '-page-' + pagination.current_page + '.xls'"
														  			>
														  				Excel
																	</download-excel>
											  						
											  						<!-- 
											  						<download-excel 
											  							type="csv"
														  				class="btn btn-default p-1 dropdown-item disabled"
																		:data="allProducts"
																		:fields="dataToExport" 
																		worksheet="Products sheet"
																		:name="((searchAttributes.search != '' || searchAttributes.dateFrom || searchAttributes.dateTo) ? 'searched-products-' : (currentTab + '-products-list-')) + currentTime + '-page-' + pagination.current_page + '.xls'"
														  			>
														  				CSV
																	</download-excel> 
																	-->
											  					</div>
											  				</div>
											  			</div>
											  		</div>

											  		<div class="col-sm-6 was-validated d-flex align-items-center form-group">
											  			<div class="ml-sm-auto mr-3">
										  					<input 	
																type="text" 
														  		class="form-control" 
														  		pattern="[^'!#$%^()\x22]+" 
														  		v-model="searchAttributes.search" 
														  		placeholder="Search Products"
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
																		data-target="#product-custom-search"
																		:class="{ 'active': Object.keys(searchAttributes.dates).length > 0 }"
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
											  		v-if="userHasPermissionTo('view-merchant-product-index') || userHasPermissionTo('create-merchant-product')" 
											  		:query="query" 
											  		:caller-page="'products'" 
											  		:required-permission = "'merchant-product'" 
											  		:disable-add-button="allProducts.length==0 ? true : false" 
											  		
											  		@showContentCreateForm="showProductMerchantCreateForm" 
											  		@searchData="searchData($event)" 
											  		@fetchAllContents="fetchMerchantAllProducts"
											  	></search-and-addition-option> 
											  	-->
											</div>
											
											<div class="col-sm-12 col-lg-12">
												<loading v-show="loading"></loading>

												<tab 
										  			v-show="searchAttributes.search === '' && ! searchAttributes.dateFrom && ! searchAttributes.dateTo && ! loading /* && ! searchAttributes.showPendingRequisitions && ! searchAttributes.showCancelledRequisitions && ! searchAttributes.showDispatchedRequisitions && ! searchAttributes.showProduct */" 
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
																		<th>Product</th>
																		<th>Manufacturer/Brand</th>
																		<th>SKU</th>
																		<th>Stock</th>
																		<th>Actions</th>
																	</tr>
																</thead>

																<tbody>
																	<tr v-for="merchantProduct in productsToShow" 
																		:key="'merchant-product-id' + merchantProduct.id + '-product-id-' + merchantProduct.product.id + '-merchant-id-' + merchantProduct.merchant.id" 
																		:class="merchantProduct.id==singleMerchantProductData.id ? 'highlighted' : ''"
																	>
																		<td>
																			{{ merchantProduct.product ? merchantProduct.product.name : 'NA' | capitalize }}
																		</td>
																		
																		<td>
																			{{ merchantProduct.manufacturer ? merchantProduct.manufacturer.name : 'Own Product' | capitalize }}
																		</td>

																		<td>
																			{{ merchantProduct.sku }}
																		</td>

																		<td>
																			{{ merchantProduct.available_quantity }}
																			{{ merchantProduct.hasOwnProperty('product') ? ' ' + merchantProduct.product.quantity_type : ' unit'  }}
																		</td>
																		
																		<td>
																			<button 
																				type="button" 
																				class="btn btn-grd-info btn-icon" 
																				data-toggle="tooltip" data-placement="top" title="View Details"  
																				@click="showProductMerchantDetails(merchantProduct)"
																			>
																				<i class="fa fa-eye"></i>
																			</button>

																			<button 
																				type="button" 
																				class="btn btn-grd-primary btn-icon" 
																				data-toggle="tooltip" data-placement="top" title="Edit"  
																				@click="openProductMerchantEditForm(merchantProduct)" 
																				v-if="userHasPermissionTo('update-merchant-product')"
																			>
																				<i class="fa fa-edit"></i>
																			</button>

																			<button 
																				type="button" 
																				class="btn btn-grd-danger btn-icon" 
																				data-toggle="tooltip" data-placement="top" title="Delete"  
																				@click="openProductMerchantDeleteForm(merchantProduct)" 
																				v-if="userHasPermissionTo('delete-merchant-product')" 
																				:disabled="merchantProduct.product_immutability"
																			>
																				<i class="fa fa-trash"></i>
																			</button>

																			<button 
																				type="button" 
																				class="btn btn-grd-warning btn-icon" 
																				data-toggle="tooltip" data-placement="top" title="Requisitions"  
																				@click="goProductRequisitions(merchantProduct)" 
																				v-if="userHasPermissionTo('view-requisition-index')"
																			>
																				<i class="fa fa-truck" aria-hidden="true"></i>
																			</button>

																			<button 
																				type="button" 
																				class="btn btn-grd-success btn-icon" 
																				data-toggle="tooltip" data-placement="top" title="Stocks"  
																				@click="goProductStore(merchantProduct)" 
																				v-if="userHasPermissionTo('view-product-stock-index')"
																			>
																				<i class='fas fa-store-alt'></i>
																			</button>
																		</td>
																	</tr>

																	<tr 
																  		v-show="! productsToShow.length"
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
																		<th>Product</th>
																		<th>Manufacturer/Brand</th>
																		<th>SKU</th>
																		<th>Stock</th>
																		<th>Actions</th>
																	</tr>
																</tfoot>
															</table>
														</div>
													</div>

													<div class="row d-flex align-items-center">
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
																@click="searchAttributes.search === '' ? fetchMerchantAllProducts() : searchData()"
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
																@paginate="searchAttributes.search === '' ? fetchMerchantAllProducts() : searchData()"
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

 		<!--Create Or Edit Modal -->
		<div class="modal fade" id="product-createOrEdit-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" v-if="userHasPermissionTo('create-merchant-product') || userHasPermissionTo('update-merchant-product')">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">
							{{ merchantFullName | capitalize }} {{ createMode ? ' New ' : ' Update ' | capitalize }} Product
						</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
						
					<form 	
						class="form-horizontal" 
						v-on:submit.prevent="createMode ? storeProductMerchant() : updateProductMerchant()" 
						autocomplete="off" 
						novalidate="true" 
					>
						<input type="hidden" name="_token" :value="csrf">

						<div class="modal-body">
									<div class="form-row">
										<div class="form-group col-sm-12">
							        		<label for="inputUsername">Selected Merchant</label>
								        	<multiselect 
		                              			v-model="merchant" 
		                              			class="form-control p-0 is-valid" 
		                              			placeholder="Product Name" 
		                                  		:custom-label="objectNameWithCapitalized" 
		                                  		:options="[]" 
		                                  		:allow-empty="false" 
		                                  		:disabled="true" 
		                              		>
		                                	</multiselect>
							        	</div>
									</div>

							        <div class="form-row">
							        	<div class="form-group col-md-6">
											<label for="inputUsername">Product Name</label>
											<multiselect 
		                              			v-model="singleMerchantProductData.product"
		                              			placeholder="Merchant Product" 
		                                  		label="user_name" 
		                                  		track-by="id" 
		                                  		:custom-label="objectNameWithCapitalized" 
		                                  		:options="allProducts" 
		                                  		:required="true" 
		                                  		:allow-empty="false"
		                                  		selectLabel = "Press/Click"
		                                  		deselect-label="Can't remove single value" 
		                                  		class="form-control p-0" 
		                                  		:class="! errors.product.merchant_product_id  ? 'is-valid' : 'is-invalid'"
		                                  		@close="validateFormInput('merchant_product_id')" 
		                                  		@input="setMerchantProduct"
		                              		>
		                                	</multiselect>

		                                	<div class="invalid-feedback">
										    	{{ errors.product.merchant_product_id }}
										    </div>
										</div>

										<div class="form-group col-md-6">
											<label for="inputUsername">Manufacturer/Brand Name</label>

											<multiselect 
		                              			v-model="singleMerchantProductData.manufacturer"
		                              			placeholder="Manufacturer" 
		                                  		label="name" 
		                                  		track-by="id" 
		                                  		:custom-label="objectNameWithCapitalized" 
		                                  		:options="allManufacturers" 
		                                  		selectLabel = "Press/Click"
		                                  		deselect-label="Press/Click To Remove" 
		                                  		class="form-control p-0 is-valid" 
		                                  		@input="setProductManufacturer"
		                              		>
		                                	</multiselect>
										</div>
							        </div>

									<div class="form-row">
										<div class="form-group col-md-6">
											<label for="inputFirstName">Product Code/SKU</label>
											<input type="text" 
												class="form-control" 
												v-model="singleMerchantProductData.sku" 
												placeholder="SKU should be unique" 
												:class="!errors.product.product_sku ? 'is-valid' : 'is-invalid'" 
												@change="validateFormInput('product_sku')" 
												required="true" 
											>

											<div class="invalid-feedback">
									        	{{ errors.product.product_sku }}
									  		</div>
										</div>

										<div class="form-group col-md-6">
											<label for="inputFirstName">Selling Price (unit)</label>
											<input type="number" 
												class="form-control" 
												v-model.number="singleMerchantProductData.selling_price" 
												placeholder="Product Selling Price" 
												:class="!errors.product.product_price  ? 'is-valid' : 'is-invalid'" 
												@change="validateFormInput('product_price')" 
												:disabled="singleMerchantProductData.hasOwnProperty('product') && singleMerchantProductData.product.product_category_id ? false : true"
											>

											<div class="invalid-feedback">
									        	{{ errors.product.product_price }}
									  		</div>
										</div>
									</div>

									<div class="form-row">
										<div class="form-group col-md-6">
											<label for="inputFirstName">Discount</label>

											<div class="input-group mb-0">
											    <input 
											    	type="number" 
											    	class="form-control" 
													v-model.number="singleMerchantProductData.discount" 
													placeholder="Product Discount" 
													:class="!errors.product.discount ? 'is-valid' : 'is-invalid'" 
													@change="validateFormInput('discount')" 
													:disabled="singleMerchantProductData.hasOwnProperty('product') && singleMerchantProductData.product.product_category_id ? false : true"
											    >
											    <div class="input-group-append">
											      	<span class="input-group-text">%</span>
											    </div>
											</div>

											<div 
												class="invalid-feedback" 
												style="display: block;" 
												v-show="errors.product.discount"
											>
									        	{{ errors.product.discount }}
									  		</div>
										</div>

										<div class="form-group col-md-6">
											<label for="inputFirstName">Warning Quantity</label>
											<input type="number" 
												class="form-control is-valid" 
												v-model.number="singleMerchantProductData.warning_quantity" 
												placeholder="Product Warning Quantity" 
											>
										</div>
									</div>

									<div class="form-row">
										<div class="col-md-12">
											<div class="card">
										    	<div class="card-body">
										    		<div class="form-row d-flex align-items-center text-center">
														<div class="form-group col-md-6">
															<img 
																class="img-fluid" 
																ref="merchantProductPreview" 
																:src="showPreview(singleMerchantProductData.preview)"
																alt="Product Preview" 
															>
														</div>
														
														<div class="form-group col-md-6">
															<div class="custom-file">
															    <input type="file" 
															    	class="form-control custom-file-input" 
																	:class="! errors.product.preview  ? 'is-valid' : 'is-invalid'" 
														    	 	@change="onProductPreviewChange" 
														    	 	accept="image/*"
															    >
															    <label class="custom-file-label" for="validatedCustomFile">Choose Picture...</label>
															    <div class="invalid-feedback">
															    	{{ errors.product.preview }}
															    </div>
														  	</div>
														</div>
													</div>
										    	</div>
										  	</div>
										</div>
									</div>

									<div class="form-row">
										<div class="form-group col-md-12">
											<label for="inputFirstName">Description</label>
											<ckeditor 
				                              	class="form-control" 
				                              	:editor="editor" 
				                              	v-model="singleMerchantProductData.description"
				                            >
			                              	</ckeditor>
										</div>
									</div>

									<div class="form-control form-group" v-if="singleMerchantProductData.hasOwnProperty('product') && singleMerchantProductData.product.has_serials">
										<div class="form-row mt-2">
											<div class="col-md-12 text-center">
												<toggle-button 
													v-model="singleMerchantProductData.product.has_serials" 
													:width=200 
													:sync="true"
													:color="{checked: 'orange', unchecked: 'green'}"
													:labels="{checked: 'Has Serial', unchecked: 'No Serial'}" 
													:disabled="true" 
												/>
											</div>
										</div>
									</div>

									<div class="form-control form-group" v-if="singleMerchantProductData.hasOwnProperty('product') && singleMerchantProductData.product.has_variations">
										<div class="form-row mt-3">
											<div class="form-group col-md-12 text-center">
												<toggle-button 
													v-model="singleMerchantProductData.product.has_variations" 
													:width=200 
													:sync="true"
													:color="{checked: 'green', unchecked: 'blue'}"
													:labels="{checked: 'Has Variation', unchecked: 'No Variation'}" 
													:disabled="true" 
												/>
											</div>
										</div>
										
										<div class="form-row">
											<div class="form-group col-md-3">
												<label for="inputFirstName">Variation Type</label>
												<multiselect 
			                              			v-model="singleMerchantProductData.product.variation_type"
			                              			placeholder="Choose Type" 
			                                  		label="name" 
			                                  		track-by="id" 
			                                  		:custom-label="objectNameWithCapitalized" 
			                                  		:options="[]" 
			                                  		:required="true" 
			                                  		:allow-empty="false"
			                                  		class="form-control p-0 is-valid" 
			                                  		:disabled="true"
			                              		>
			                                	</multiselect>
											</div>

											<div class="form-group col-md-9" v-if="singleMerchantProductData.hasOwnProperty('variations') && errors.product.hasOwnProperty('variations') && singleMerchantProductData.variations.length == errors.product.variations.length">
												<div 
													class="card" 
													v-for="(merchantProductVariation, index) in singleMerchantProductData.variations" 
													:key="'merchant-product-variation-index-' + index + '-merchant-product-variation-' + merchantProductVariation.id + '-A'"
												>
													<div class="card-body">
														<div 
															class="form-row" 
															v-if="singleMerchantProductData.variations.length == errors.product.variations.length"
														>	
															<div class="form-group col-md-12">
																<label for="inputFirstName">Variaiton</label>

																<multiselect 
							                              			v-model="merchantProductVariation.variation"
							                              			placeholder="Select Variation" 
							                                  		label="name" 
							                                  		track-by="id" 
							                                  		:custom-label="objectNameWithCapitalized" 
							                                  		:options="allVariations" 
							                                  		:disabled="singleMerchantProductData.variations[index].variation_immutability" 
							                                  		class="form-control p-0" 
							                                  		:class="! errors.product.variations[index].product_variation_id ? 'is-valid' : 'is-invalid'" 
							                                  		:required="true" 
					                                  				:allow-empty="false"
							                                  		@close="validateFormInput('product_variation_id')" 
							                              		>
							                                	</multiselect>

							                                	<div class="invalid-feedback">
															    	{{ errors.product.variations[index].product_variation_id }}
															    </div>
															</div> 

															<!-- 
															<div 
																class="form-group col-md-12" 
																v-if="merchantProductVariation.variation && merchantProductVariation.variation.hasOwnProperty('sub_variation')"
															>
																<div class="form-row ml-3 mr-3">
																	<div class="form-group col-md-12">
																		<label for="inputFirstName">Sub-Variation</label>
																		<multiselect 
									                              			v-model="merchantProductVariation.variation.sub_variation"
									                              			placeholder="Select Sub-Variation" 
									                                  		label="name" 
									                                  		track-by="id" 
									                                  		:custom-label="objectNameWithCapitalized" 
									                                  		:options="[]" 
									                                  		selectLabel = "Press to select"
									                                  		deselect-label="Press to remove" 
									                                  		:disabled="true" 
									                                  		class="form-control is-valid p-0" 
									                              		>
									                                	</multiselect>
																	</div>
																</div> 
															</div> 
															-->
															
															<div class="form-group col-md-12">
																<div class="form-row">
																	<div class="form-group col-md-6">
																		<label for="inputFirstName">Selling Price (unit)</label>

																		<input type="number" 
																			class="form-control" 
																			v-model.number="merchantProductVariation.selling_price" 
																			placeholder="Variation Selling Price" 
																			:class="!errors.product.variations[index].product_variation_price ? 'is-valid' : 'is-invalid'" 
																			@change="validateFormInput('product_variation_price')" 
																			required="true" 
																		>

																		<div class="invalid-feedback">
																        	{{ errors.product.variations[index].product_variation_price }}
																  		</div>	
																	</div>

																	<div class="form-group col-md-6">
																		<label for="inputFirstName">Variation SKU</label>
																
																		<input type="text" 
																			class="form-control is-valid" 
																			v-model="merchantProductVariation.sku" 
																			placeholder="Variation Unique SKU" 
																		>
																	</div>
																</div>
															</div>

															<div class="form-group col-md-12">
																<div class="form-row text-center d-flex">
																	<div class="col-md-6 form-group">
																		<img 
																			class="img-fluid" 
																			:src="showPreview(merchantProductVariation.preview)"
																			alt="Variation Preview" 
																			:ref="'merchantProductVariationPreview-' + index" 
																		>
																	</div>
																	<div class="col-md-6 form-group align-self-center">
																		<div class="custom-file">
																		    <input type="file" 
																		    	class="form-control custom-file-input" 
																				:class="!errors.product.variations[index].preview  ? 'is-valid' : 'is-invalid'" 
																	    	 	@change="onVariationPreviewChange($event, index)" 
																	    	 	accept="image/*"
																		    >
																		    <label class="custom-file-label" for="validatedCustomFile">Choose Picture...</label>
																		    <div class="invalid-feedback">
																		    	{{ errors.product.variations[index].preview }}
																		    </div>
																	  	</div>
																	</div>
																</div>
															</div>							
														</div>
													</div>
												</div>

												<div class="form-row">
													<div class="form-group col-md-6">
														<button 
															type="button" 
															class="btn waves-effect waves-light hor-grd btn-grd-primary btn-sm btn-block" 
															data-toggle="tooltip" data-placement="top" title="Add Variation" 
															:disabled="singleMerchantProductData.hasOwnProperty('product') && singleMerchantProductData.product.has_variations && singleMerchantProductData.product.variations && singleMerchantProductData.variations.length >= singleMerchantProductData.product.variations.length" 
															@click="addMoreVariation()"
														>
															Add Variation
														</button>
													</div>

													<div class="form-group col-md-6">
														<button 
															type="button" 
															class="btn waves-effect waves-light hor-grd btn-grd-info btn-sm btn-block" 
															data-toggle="tooltip" data-placement="top" title="Remove Variation" 
															:disabled="singleMerchantProductData.variations[singleMerchantProductData.variations.length-1].variation_immutability || singleMerchantProductData.variations.length < 2"
															@click="removeVariation()"
														>
															Remove Variation
														</button>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="form-row card-footer">
										<div class="col-sm-12 text-right" v-show="! submitForm">
											<span class="text-danger small mb-1">
										  		Please input required fields
										  	</span>
										</div>

										<div class="col-sm-12">
						                  	<button type="button" class="btn btn-secondary float-left" data-dismiss="modal">
						                  		Close
						                  	</button>
											<button type="submit" class="btn btn-primary float-right" :disabled="! submitForm || formSubmitted">
												{{ createMode ? 'Save' : 'Update' }}
											</button>
										</div>
									</div>
							<!-- </transition-group> -->
						</div>
					</form>
				</div>
			</div>
		</div>

 		<!-- View Modal -->
		<div class="modal fade" id="merchant-product-view-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">{{ singleMerchantProductData.hasOwnProperty('product') ? singleMerchantProductData.product.name : 'NA' | capitalize }} Details</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="modal-body">
						<div class="card">
							<div class="card-body">
								<ul class="nav nav-tabs tabs justify-content-center" role="tablist">
									<li class="nav-item">
										<a class="nav-link active" data-toggle="tab" href="#merchant-product-profile" role="tab">
											Profile
										</a>
									</li>

									<li class="nav-item" v-show="singleMerchantProductData.hasOwnProperty('product') && singleMerchantProductData.product.has_serials">
										<a class="nav-link" data-toggle="tab" href="#product-serial" role="tab">
											Serials
										</a>
									</li>

									<li class="nav-item">
										<a class="nav-link" data-toggle="tab" href="#product-address" role="tab">
											Store
										</a>
									</li>
								</ul>

								<div class="tab-content tabs card-block">
									<div class="tab-pane active" id="merchant-product-profile" role="tabpanel">
										<div class="form-row d-flex">
											<div class="col-md-4 align-self-center text-center">
												<img 
													:src="'/' + singleMerchantProductData.preview" 
													class="img-fluid" 
													alt="Product Preview" 
													width="150px"
												>
											</div>

											<div class="col-md-8">
												<div class="form-row">
													<label class="col-4 col-form-label font-weight-bold">
														Merchant Name :
													</label>
													<label class="col-8 col-form-label">
														{{ singleMerchantProductData.merchant ? singleMerchantProductData.merchant.user_name : 'None' | capitalize }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-4 col-form-label font-weight-bold">
														Manufacturer/Brand Name :
													</label>
													<label class="col-8 col-form-label">
														{{ singleMerchantProductData.manufacturer ? singleMerchantProductData.manufacturer.name : 'own product' | capitalize }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-4 col-form-label font-weight-bold">
														Product Code/SKU :
													</label>
													<label class="col-8 col-form-label">
														{{ singleMerchantProductData.sku }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-4 col-form-label font-weight-bold">
														Description :
													</label>
													<label class="col-8 col-form-label">
														<span v-html="singleMerchantProductData.description"></span>
													</label>
												</div>

												<div class="form-row">
													<label class="col-4 col-form-label font-weight-bold">
														Warning Quantity :
													</label>
													<label class="col-8 col-form-label">
														{{ singleMerchantProductData.warning_quantity }}
														{{ singleMerchantProductData.product ? singleMerchantProductData.product.quantity_type : 'unit' }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-4 col-form-label font-weight-bold">
														Available Quantity :
													</label>
													<label class="col-8 col-form-label">
														{{ singleMerchantProductData.available_quantity }}
														{{ singleMerchantProductData.product ? singleMerchantProductData.product.quantity_type : 'unit' }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-4 col-form-label font-weight-bold">
														Dispatched Quantity :
													</label>
													<label class="col-8 col-form-label">
														{{ singleMerchantProductData.dispatched_quantity }}
														{{ singleMerchantProductData.product ? singleMerchantProductData.product.quantity_type : 'unit' }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-4 col-form-label font-weight-bold">
														Pending Requested Quantity :
													</label>
													<label class="col-8 col-form-label">
														{{ singleMerchantProductData.requested_quantity }}
														{{ singleMerchantProductData.product ? singleMerchantProductData.product.quantity_type : 'unit' }}
													</label>
												</div>	

												<div class="form-row">
													<label class="col-4 col-form-label font-weight-bold">
														Selling Price (unit) :
													</label>
													<label class="col-8 col-form-label">
														{{ singleMerchantProductData.selling_price }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-4 col-form-label font-weight-bold">
														Discount :
													</label>
													<label class="col-8 col-form-label">
														{{ singleMerchantProductData.discount || 0 }} %
													</label>
												</div>

												<div class="form-row">
													<label class="col-4 col-form-label font-weight-bold">Has Serials :</label>
													<label class="col-sm-6 form-control-plaintext">
														<span :class="[singleMerchantProductData.hasOwnProperty('product') && singleMerchantProductData.product.has_serials ? 'badge-info' : 'badge-primary', 'badge']">{{ singleMerchantProductData.hasOwnProperty('product') && singleMerchantProductData.product.has_serials ? 'Available' : 'NA' }}</span>
													</label>
												</div>

												<div class="form-row">
													<label class="col-4 col-form-label font-weight-bold">Has Variation :</label>
													<label class="col-sm-6 form-control-plaintext">
														<span :class="[singleMerchantProductData.hasOwnProperty('product') && singleMerchantProductData.product.has_variations ? 'badge-info' : 'badge-primary', 'badge']">{{ singleMerchantProductData.hasOwnProperty('product') && singleMerchantProductData.product.has_variations ? 'Available' : 'NA' }}</span>
													</label>
												</div>

												<div class="form-row">
													<label class="col-4 col-form-label font-weight-bold">
														Created on :
													</label>
													<label class="col-8 col-form-label">
														{{ singleMerchantProductData.created_at }}
													</label>
												</div>

												<div class="form-row" v-if="singleMerchantProductData.hasOwnProperty('product') && singleMerchantProductData.product.has_variations && singleMerchantProductData.hasOwnProperty('variations') && singleMerchantProductData.variations.length">
													<label class="col-4 col-form-label font-weight-bold">
														Variations :
													</label>
													<div class="col-sm-12">
														<div class="form-row">
															<div 
																class="col-md-6 ml-auto" 
																v-for="(merchantProductVariation, merchantProductVariationIndex) in singleMerchantProductData.variations" 
																:key="'merchant-product-variation-index-' + merchantProductVariationIndex + '-variation-' + merchantProductVariation.id"
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
																			</label>
																		</div>

																		<div class="form-row">
																			<label class="col-4 col-form-label font-weight-bold">
																				Available Qty :
																			</label>
																			<label class="col-8 col-form-label">
																				{{ merchantProductVariation.available_quantity }} {{ singleMerchantProductData.product ? singleMerchantProductData.product.quantity_type : 'unit' }}
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

									<div class="tab-pane" id="product-serial" role="tabpanel" v-show="singleMerchantProductData.hasOwnProperty('product') && singleMerchantProductData.product.has_serials">
										<div class="form-row">
											<label class="col-4 col-form-label font-weight-bold">
												Serials :
											</label>
											<div class="col-8 col-form-label">
												<ol 
													v-if="singleMerchantProductData.hasOwnProperty('serials') && singleMerchantProductData.serials.length"
												>
													<li v-for="(productSerial, productIndex) in singleMerchantProductData.serials">
														{{ productSerial.serial_no }}

														<span :class="[productSerial.has_dispatched ? 'badge badge-danger' : productSerial.has_requisitions ? 'badge badge-warning' : '']">
															{{ productSerial.has_dispatched ? 'Dispatched' : productSerial.has_requisitions ? 'Requested' : '' }}
														</span>

														<span v-show="(productIndex + 1) < singleMerchantProductData.serials.length">, </span> 
													</li>	
												</ol>
												
												<div class="form-row" v-if="singleMerchantProductData.hasOwnProperty('variations') && singleMerchantProductData.variations.length">
													<div 
														class="col-md-12" 
														v-for="(merchantProductVariation, variationIndex) in singleMerchantProductData.variations" 
														:key="'product-variation-index-' + variationIndex + '-C'"
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
																Available Quantity :
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

									<div class="tab-pane" id="product-address" role="tabpanel">
										<div 
											class="form-row" 
											v-if="singleMerchantProductData.hasOwnProperty('addresses') && singleMerchantProductData.addresses.length"
										>
											<label class="col-4 col-form-label font-weight-bold">
												Address Detail :
											</label>
											<div class="col-sm-12">
												<div class="form-row">
													<div 
														class="col-md-6 ml-auto" 
														v-for="(stockAddress, addressIndex) in singleMerchantProductData.addresses" 
														:key="'stock-address-' + stockAddress.type + addressIndex"
													>
														<div 
															class="card" 
															v-if="stockAddress.hasOwnProperty('type') && stockAddress.type.includes('containers')"
														>
															<div 
																class="card-body" 
																v-for="containerAddress in stockAddress.containers" 
																:key="'container-address-' + containerAddress.id + 'address-index-' + addressIndex + '-stock-id-' + singleMerchantProductData.id"
															>
																<h6>Container Address</h6>

																<div class="form-row">
																	<label class="col-4 col-form-label font-weight-bold">
																		Warehouse :
																	</label>
																	<label class="col-8 col-form-label">
																		{{ containerAddress.warehouse_container ? $options.filters.capitalize(containerAddress.warehouse_container.warehouse.name) : 'NA' }}
																	</label>
																</div>

																<div class="form-row">
																	<label class="col-4 col-form-label font-weight-bold">
																		Container Type :
																	</label>
																	<label class="col-8 col-form-label">
																		{{ containerAddress.warehouse_container ? $options.filters.capitalize(containerAddress.warehouse_container.container.name) : 'NA' }}
																	</label>
																</div>

																<div class="form-row">
																	<label class="col-4 col-form-label font-weight-bold">
																		Container # :
																	</label>
																	<label class="col-8 col-form-label">
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
																	<label class="col-4 col-form-label font-weight-bold">
																		Container Type :
																	</label>
																	<label class="col-8 col-form-label">
																		{{ stockAddress.container.warehouse_container.container.name | capitalize }}
																	</label>
																</div>

																<div class="form-row">
																	<label class="col-4 col-form-label font-weight-bold">
																		Container # :
																	</label>
																	<label class="col-8 col-form-label">
																		{{ stockAddress.container.name.substring(stockAddress.container.name.indexOf("-")+1) }}
																	</label>
																</div>

																<div 
																	class="form-row"
																>
																	<label class="col-4 col-form-label font-weight-bold">
																		Shelf # :
																	</label>
																	<label class="col-8 col-form-label">

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
																	<label class="col-4 col-form-label font-weight-bold">
																		Container Type :
																	</label>
																	<label class="col-8 col-form-label">
																		{{ stockAddress.container.warehouse_container.container.name | capitalize }}
																	</label>
																</div>

																<div class="form-row">
																	<label class="col-4 col-form-label font-weight-bold">
																		Container # :
																	</label>
																	<label class="col-8 col-form-label">
																		{{ stockAddress.container.name.substring(stockAddress.container.name.indexOf("-")+1) }}
																	</label>
																</div>

																<div class="form-row">
																	<label class="col-4 col-form-label font-weight-bold">
																		Shelf # :
																	</label>
																	<label class="col-8 col-form-label">
																		{{ stockAddress.container.shelf.name.substring(stockAddress.container.shelf.name.lastIndexOf("-")+1) }}
																	</label>
																</div>

																<div class="form-row">
																	<label class="col-4 col-form-label font-weight-bold">
																		Unit # :
																	</label>
																	<label class="col-8 col-form-label">

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
												v-show="!singleMerchantProductData.hasOwnProperty('addresses') || !singleMerchantProductData.addresses.length"
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
						<button type="button" class="btn btn-secondary mr-auto" data-dismiss="modal">Close</button>
						<button 
							type="button" 
							class="btn btn-danger" 
							data-toggle="tooltip" data-placement="top" title="Print"  
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
				<div class="card-body">
					<div class="form-row">
						<label class="col-4 col-form-label font-weight-bold">
							Merchant Name :
						</label>
						<label class="col-8 col-form-label">
							{{ singleMerchantProductData.merchant ? singleMerchantProductData.merchant.user_name : 'None' | capitalize }}
						</label>
					</div>

					<div class="form-row">
						<label class="col-4 col-form-label font-weight-bold">
							Manufacturer/Brand Name :
						</label>
						<label class="col-8 col-form-label">
							{{ singleMerchantProductData.manufacturer ? singleMerchantProductData.manufacturer.name : 'own product' | capitalize }}
						</label>
					</div>

					<div class="form-row">
						<label class="col-4 col-form-label font-weight-bold">
							Product Code/SKU :
						</label>
						<label class="col-8 col-form-label">
							{{ singleMerchantProductData.sku }}
						</label>
					</div>

					<div class="form-row">
						<label class="col-4 col-form-label font-weight-bold">
							Description :
						</label>
						<label class="col-8 col-form-label">
							<span v-html="singleMerchantProductData.description"></span>
						</label>
					</div>

					<div class="form-row">
						<label class="col-4 col-form-label font-weight-bold">
							Warning Quantity :
						</label>
						<label class="col-8 col-form-label">
							{{ singleMerchantProductData.warning_quantity }}
							{{ singleMerchantProductData.product ? singleMerchantProductData.product.quantity_type : 'unit' }}
						</label>
					</div>

					<div class="form-row">
						<label class="col-4 col-form-label font-weight-bold">
							Available Quantity :
						</label>
						<label class="col-8 col-form-label">
							{{ singleMerchantProductData.available_quantity }}
							{{ singleMerchantProductData.product ? singleMerchantProductData.product.quantity_type : 'unit' }}
						</label>
					</div>

					<div class="form-row">
						<label class="col-4 col-form-label font-weight-bold">
							Dispatched Quantity :
						</label>
						<label class="col-8 col-form-label">
							{{ singleMerchantProductData.dispatched_quantity }}
							{{ singleMerchantProductData.product ? singleMerchantProductData.product.quantity_type : 'unit' }}
						</label>
					</div>

					<div class="form-row">
						<label class="col-4 col-form-label font-weight-bold">
							Pending Requested Quantity :
						</label>
						<label class="col-8 col-form-label">
							{{ singleMerchantProductData.requested_quantity }}
							{{ singleMerchantProductData.product ? singleMerchantProductData.product.quantity_type : 'unit' }}
						</label>
					</div>	

					<div class="form-row">
						<label class="col-4 col-form-label font-weight-bold">
							Selling Price (unit) :
						</label>
						<label class="col-8 col-form-label">
							{{ singleMerchantProductData.selling_price }}
						</label>
					</div>

					<div class="form-row">
						<label class="col-4 col-form-label font-weight-bold">
							Discount :
						</label>
						<label class="col-8 col-form-label">
							{{ singleMerchantProductData.discount || 0 }} %
						</label>
					</div>

					<div class="form-row">
						<label class="col-4 col-form-label font-weight-bold">Has Serials :</label>
						<label class="col-8 form-control-plaintext">
							<span :class="[singleMerchantProductData.hasOwnProperty('product') && singleMerchantProductData.product.has_serials ? 'badge-info' : 'badge-primary', 'badge']">{{ singleMerchantProductData.hasOwnProperty('product') && singleMerchantProductData.product.has_serials ? 'Available' : 'NA' }}</span>
						</label>
					</div>

					<div class="form-row">
						<label class="col-4 col-form-label font-weight-bold">Has Variation :</label>
						<label class="col-8 form-control-plaintext">
							<span :class="[singleMerchantProductData.hasOwnProperty('product') && singleMerchantProductData.product.has_variations ? 'badge-info' : 'badge-primary', 'badge']">{{ singleMerchantProductData.hasOwnProperty('product') && singleMerchantProductData.product.has_variations ? 'Available' : 'NA' }}</span>
						</label>
					</div>

					<div class="form-row">
						<label class="col-4 col-form-label font-weight-bold">
							Created on :
						</label>
						<label class="col-8 col-form-label">
							{{ singleMerchantProductData.created_at }}
						</label>
					</div>

					<div class="form-row" v-if="singleMerchantProductData.hasOwnProperty('product') && singleMerchantProductData.product.has_variations && singleMerchantProductData.hasOwnProperty('variations') && singleMerchantProductData.variations.length">
						<label class="col-4 col-form-label font-weight-bold">
							Variations :
						</label>
						<div class="col-sm-12">
							<div class="form-row">
								<div 
									class="col-md-6 ml-auto" 
									v-for="(merchantProductVariation, merchantProductVariationIndex) in singleMerchantProductData.variations" 
									:key="'merchant-product-variation-index-' + merchantProductVariationIndex + '-variation-' + merchantProductVariation.id"
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
												</label>
											</div>

											<div class="form-row">
												<label class="col-4 col-form-label font-weight-bold">
													Available Qty :
												</label>
												<label class="col-8 col-form-label">
													{{ merchantProductVariation.available_quantity }} {{ singleMerchantProductData.product ? singleMerchantProductData.product.quantity_type : 'unit'  }}
												</label>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="form-row">
						<label class="col-4 col-form-label font-weight-bold">
							Serials :
						</label>
						<div class="col-8 col-form-label">
							<ol 
								v-if="singleMerchantProductData.hasOwnProperty('serials') && singleMerchantProductData.serials.length"
							>
								<li v-for="(productSerial, productIndex) in singleMerchantProductData.serials">
									{{ productSerial.serial_no }}

									<span :class="[productSerial.has_dispatched ? 'badge badge-danger' : productSerial.has_requisitions ? 'badge badge-warning' : '']">
										{{ productSerial.has_dispatched ? 'Dispatched' : productSerial.has_requisitions ? 'Requested' : '' }}
									</span>
																		
									<span v-show="(productIndex + 1) < singleMerchantProductData.serials.length">, </span> 
								</li>	
							</ol>
							
							<div class="form-row" v-if="singleMerchantProductData.hasOwnProperty('variations') && singleMerchantProductData.variations.length">
								<div 
									class="col-md-12" 
									v-for="(merchantProductVariation, variationIndex) in singleMerchantProductData.variations" 
									:key="'product-variation-index-' + variationIndex + '-C'"
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
											Available Quantity :
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
		<!-- Printing Section -->

		<!-- Filter Modal -->
		<div class="modal fade" id="product-custom-search" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
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
							@click="resetSearchingDates()" 
							data-toggle="tooltip" data-placement="top" title="Reset"
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

		<!-- 		
		<delete-confirmation-modal 
			v-if="userHasPermissionTo('delete-merchant-product')" 
			:csrf="csrf" 
			:submit-method-name="'deleteProductMerchant'" 
			:content-to-delete="singleMerchantProductData"
			:restoration-message="'Warning : You can not restore this item !'" 
			
			@deleteProductMerchant="deleteProductMerchant($event)" 
		></delete-confirmation-modal> 
		-->

		<!-- Delete Modal -->
		<div class="modal fade" id="delete-confirmation-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
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
							<h4 class="text-danger">Want to delete '{{ singleMerchantProductData.hasOwnProperty('product') ? singleMerchantProductData.product.name : '' | capitalize }}' ?</h4>
							<h6 class="sub-heading text-secondary">Warning : You can not restore this item !</h6>
						</div>

						<div class="modal-footer">
							<button type="button" class="btn btn-secondary mr-auto" data-dismiss="modal">Close</button>
							<button type="submit" class="btn btn-danger">Delete</button>
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

    let singleMerchantProductData = {
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

	        	// step : 1,
	        	// query : '',
	        	error : '',
    			perPage : 10,
	        	loading : false,

	        	currentTab : 'retail',

	        	editor: ClassicEditor,

	        	createMode : true,
	        	submitForm : true,
	        	formSubmitted : false,

	        	// allVariationTypes : [],
	        	
	        	allProducts : [],
	        	allVariations : [],
	        	allManufacturers : [],

	        	productsToShow : [],
	        	merchantAllProducts : [],

	        	// allContainers : [],
	        	
	        	// emptyContainers : [],
	        	// emptyShelfContainers : [],
	        	// emptyUnitContainers : [],

	        	// emptyShelves : [],
	        	// emptyUnitShelves : [],

	        	// emptyUnits : [],

	        	pagination: {
		        	current_page: 1
		      	},

	        	singleMerchantProductData : singleMerchantProductData,

	        	errors : {
					product : {
						// variations : [],
						
						/*
							addresses : [
								{}
							],
						*/
					},
				},

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

					Price: 'selling_price',

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

					"Available Qty": 'available_quantity',

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

					"Variations": {

						callback: (object) => {

							let infosToReturn = '';

							if (object.has_variations && object.variations.length) {

								object.variations.forEach(
					
									(objectVariation, variationIndex) => {

										infosToReturn += ((variationIndex + 1) + '. ' + this.$options.filters.capitalize(objectVariation.variation.name) + ", \n" + 'Available Qty: ' + (objectVariation.available_quantity + ' ' + (object.product ? object.product.quantity_type : 'unit')) + "\n\n");

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

								infosToReturn += `Serials:
								`;

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

											infosToReturn += this.$options.filters.capitalize(objectVariation.variation.name) + " Serials:\n";

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

					/*
					"Warning Qty": {
						callback: (object) => {
							return (object.warning_quantity + ' ' + object.product ? object.product.quantity_type : ' Unit');
						},
					},
					*/

					'Total Dispatched': 'dispatched_quantity',

					'Pending Requests': 'requested_quantity',

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

	            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

	        }

		},
		
		created(){

			this.fetchAllProducts();
			this.fetchAllManufacturers();
			// this.setProductVariation();
			// this.fetchAllContainers();
			this.fetchMerchantAllProducts();

		},

		computed: {

			merchantFullName: function() {

				return this.merchant ? (this.merchant.first_name + ' ' + this.merchant.last_name) : 'No Name';

			},

			currentTime: function() {

				let date = new Date();
				return date.getFullYear() + '/' +  (date.getMonth() + 1) + '/' + date.getDate() + ' ' + date.getHours() + ':' + date.getMinutes();

			},

		},

		watch : {

			'searchAttributes.search' : function(val){
				
				if (this.searchAttributes.search==='' && ! this.searchAttributes.dateTo && ! this.searchAttributes.dateFrom) {

					this.fetchAllRequisitions();

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

					this.fetchAllRequisitions();

				}
				else {

					this.searchData();
						
				}

			},

			'searchAttributes.dateTo' : function(val){
				
				if (this.searchAttributes.search==='' && ! this.searchAttributes.dateTo && ! this.searchAttributes.dateFrom) {

					this.fetchAllRequisitions();

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

			fetchMerchantAllProducts() {

				// this.query = '';
				this.error = '';
				this.loading = true;
				this.merchantAllProducts = [];
				this.searchAttributes.search = '';
				
				axios
					.get('/api/merchant-products/' + this.merchant.id + '/' + this.perPage + "?page=" + this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							this.merchantAllProducts = response.data;
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
			fetchAllProducts() {
				
				if (! this.userHasPermissionTo('view-product-index')) {

					this.error = 'You do not have permission to view products';
					return;

				}

				this.query = '';
				this.error = '';
				this.loading = true;
				this.allProducts = [];
				
				axios
					.get('/api/products/')
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
			fetchAllManufacturers() {
				
				if (! this.userHasPermissionTo('view-product-asset-index')) {

					this.error = 'You do not have permission to view manufacturers';
					return;

				}

				this.query = '';
				this.error = '';
				this.loading = true;
				this.allManufacturers = [];
				
				axios
					.get('/api/manufacturers/')
					.then(response => {
						if (response.status == 200) {
							this.allManufacturers = response.data;
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
				fetchAllContainers() {
					
					this.query = '';
					this.error = '';
					// this.loading = true;
					this.allContainers = [];
					this.emptyContainers = [];
					this.emptyShelfContainers = [];
					this.emptyUnitContainers = [];

					axios
						.get('/api/warehouse-containers')
						.then(response => {
							if (response.status == 200) {
								
								this.allContainers = response.data;
								this.setAvailableSpaces();
								
								
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
			showProductMerchantDetails(object) {		
				// this.singleMerchantProductData = { ...object };
				// this.singleMerchantProductData = Object.assign({}, this.singleMerchantProductData, object);
				this.singleMerchantProductData = JSON.parse(JSON.stringify(object));
				$('#merchant-product-view-modal').modal('show');
			},
			openProductMerchantDeleteForm(object) {	
				this.singleMerchantProductData = object;
				$('#delete-confirmation-modal').modal('show');
			},
			showProductMerchantCreateForm() {
				// this.step = 1;
				this.createMode = true;
	        	this.submitForm = true;
	        	this.formSubmitted = false;
	        	
				this.singleMerchantProductData = {
					merchant_id : this.merchant.id
				};

				this.errors = {
					
					product : {
						
						// variations : [],
					
					},

				};

				// this.setProductVariation();

				$('#product-createOrEdit-modal').modal('show');
			},
			openProductMerchantEditForm(object) {
				// this.step = 1;
				this.createMode = false;
	        	this.submitForm = true;
	        	this.formSubmitted = false;
				
				this.resetErrorProductVariations(object);
				this.allVariations = object.product.variations;

				this.singleMerchantProductData = JSON.parse(JSON.stringify(object));

				$('#product-createOrEdit-modal').modal('show');
			},
			storeProductMerchant() {
				
				if (!this.verifyUserInput()) {
					// this.submitForm = false;
					// this.formSubmitted = false;
					return;
				}

				this.formSubmitted = true;
				// this.singleMerchantProductData.product_id = this.product.id;

				axios
					.post('/merchant-products/' + this.perPage, this.singleMerchantProductData)
					.then(response => {

						if (response.status == 200) {

							this.$toastr.s("New merchant has been stored", "Success");
							
							this.merchantAllProducts = response.data;
							this.searchAttributes.search !== '' ? this.searchData() : this.showSelectedTabProducts();
							
							if (this.query) {

								this.searchData();

							}

							$('#product-createOrEdit-modal').modal('hide');
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
						// this.fetchAllContainers();
					});

			},
			updateProductMerchant() {
				
				if (!this.verifyUserInput()) {
					// this.submitForm = false;
					// this.formSubmitted = false;
					return;
				}

				this.formSubmitted = true;
				// this.singleMerchantProductData.product_id = this.product.id;

				axios
					.put('/merchant-products/' + this.singleMerchantProductData.id + '/' + this.perPage, this.singleMerchantProductData)
					.then(response => {

						if (response.status == 200) {

							this.$toastr.s("Merchant has been updated", "Success");
							
							this.merchantAllProducts = response.data;
							this.searchAttributes.search !== '' ? this.searchData() : this.showSelectedTabProducts();

							if (this.query) {

								this.searchData();

							}

							$('#product-createOrEdit-modal').modal('hide');
							
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
						// this.fetchAllContainers();
					});

			},
			deleteProductMerchant() {

				// this.singleMerchantProductData.product_id = this.product.id;

				axios
					.delete('/merchant-products/' + this.singleMerchantProductData.id + '/' + this.perPage)
					.then(response => {

						if (response.status == 200) {

							this.$toastr.s("Merchant has been deleted", "Success");
							
							this.merchantAllProducts = response.data;
							this.searchAttributes.search !== '' ? this.searchData() : this.showSelectedTabProducts();

							if (this.query) {

								this.searchData();

							}

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
						// this.fetchAllContainers();
					});

			},
			searchData(emitedValue=false) {

				if (emitedValue) {
					this.searchAttributes.search=emitedValue;
				}

				this.error = '';
				this.merchantAllProducts = [];
				this.pagination.current_page = 1;

				this.searchAttributes.merchant_id = this.merchant.id;
				
				axios
				.post('/search-merchant-products/' + this.perPage, this.searchAttributes)
				.then(response => {
					this.merchantAllProducts = response.data;
					this.productsToShow = this.merchantAllProducts.all.data;
					this.pagination = this.merchantAllProducts.all;
				})
				.catch(e => {
					this.error = e.toString();
				});

			},
			changeNumberContents() {
				
				this.pagination.current_page = 1;

				if (this.searchAttributes.search === '') {
					this.fetchMerchantAllProducts();
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
					this.productsToShow = this.merchantAllProducts.retail.data;
					this.pagination = this.merchantAllProducts.retail;
				}
				else {
					this.productsToShow = this.merchantAllProducts.bulk.data;
					this.pagination = this.merchantAllProducts.bulk;
				}

			},
			goProductRequisitions(object) {

				// console.log(object);
				this.$router.push({ name: 'product-requisitions', params: { productId: object.product ? object.product.id : null , merchantId: this.merchant.id, merchantProduct: object }});

			},
			goProductStore(object) {

				// console.log(object);
				this.$router.push({ name: 'product-stocks', params: { product: object.product, merchantName: this.merchant.user_name.replace(/ /g,"-"), productMerchant: object }});

			},
			verifyUserInput() {
				
				this.validateFormInput('product_sku');
				this.validateFormInput('product_price');
				this.validateFormInput('discount');
				this.validateFormInput('merchant_product_id');
				
				// this.validateFormInput('product_initial_quantity');
				// this.validateFormInput('product_available_quantity');
				// this.validateFormInput('product_quantity_type');

				if (this.singleMerchantProductData.product.has_variations) {
					
					this.validateFormInput('product_variation_id');
					// this.validateFormInput('product_variation_type');
					// this.validateFormInput('product_variation_quantity');
					this.validateFormInput('product_variation_price');
					// this.validateFormInput('product_variation_total_quantity');

				}

				if (this.errors.product.constructor === Object && ((! this.singleMerchantProductData.product.has_variations && Object.keys(this.errors.product).length < 1) || (this.singleMerchantProductData.product.has_variations && Object.keys(this.errors.product).length < 2)) && ! this.errorInArray(this.errors.product.variations)) {
					// this.step += 1;
					this.submitForm = true;
					return true;
				}
				else {
					this.submitForm = false;
					return false;
				}

				/*
						this.validateFormInput('product_space_type');
						this.validateFormInput('product_container');
						this.validateFormInput('product_containers');
						this.validateFormInput('product_shelf');
						this.validateFormInput('product_shelves');
						this.validateFormInput('product_units');

						if (this.errors.product.constructor === Object && Object.keys(this.errors.product).length < 3 && !this.errorInArray(this.errors.product.variations) && !this.errorInArray(this.errors.product.addresses)) {

							return true;
						
						}

						return false;
				*/
			},
			errorInArray(array = []) {
				const variationError = (variation) => {
	        							return Object.keys(variation).length > 0
	        						}; 

				if (array.length) {
					return array.some(variationError);
				}

				return false;
			},
			nextPage() {
				
				if (this.step==1) {
					
					this.validateFormInput('product_sku');
					this.validateFormInput('product_price');
					this.validateFormInput('discount');
					this.validateFormInput('merchant_product_id');

					// this.validateFormInput('product_initial_quantity');
					// this.validateFormInput('product_available_quantity');
					// this.validateFormInput('product_quantity_type');

					if (this.singleMerchantProductData.product.has_variations) {
						
						// this.validateFormInput('product_variation_type');
						this.validateFormInput('product_variation_id');
						// this.validateFormInput('product_variation_quantity');
						this.validateFormInput('product_variation_price');
						// this.validateFormInput('product_variation_total_quantity');

					}

					if (this.errors.product.constructor === Object && Object.keys(this.errors.product).length < 3 && !this.errorInArray(this.errors.product.variations)) {
						this.step += 1;
						this.submitForm = true;
					}
					else {
						this.submitForm = false;
					}

				}

			},	
			/*
				addMoreSpace() {
					if (this.singleMerchantProductData.addresses.length < 3) {

						this.singleMerchantProductData.addresses.push({});
						this.errors.product.addresses.push({});

					}
				},
				removeSpace() {
						
					if (this.singleMerchantProductData.addresses.length > 1) {

						this.singleMerchantProductData.addresses.pop();
						this.errors.product.addresses.pop();
					
					}
					
				},
			*/
			objectNameWithCapitalized ({ name, user_name, variation }) {
		      	if (name) {
				    name = name.toString()
				    return name.charAt(0).toUpperCase() + name.slice(1)
		      	}
		      	else if (user_name) {
		      		user_name = user_name.toString()
				    return user_name.charAt(0).toUpperCase() + user_name.slice(1)
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
			/*
				nameWithCapitalized (name) {
					
					if (!name) return ''

					const words = name.split(" ");

					for (let i = 0; i < words.length; i++) {
					    words[i] = words[i][0].toUpperCase() + words[i].substr(1);
					}

					return words.join(" ");

			    },
				setProductCategory() {
					// console.log('category has been triggered');
					if (this.singleMerchantProductData.category && Object.keys(this.singleMerchantProductData.category).length > 0) {
						this.singleMerchantProductData.product_category_id = this.singleMerchantProductData.category.id;
					}
				},
			*/
			setMerchantProduct() {
				
				if (this.singleMerchantProductData.hasOwnProperty('product') && Object.keys(this.singleMerchantProductData.product).length > 0) {
					
					this.setProductVariation();
					this.singleMerchantProductData.product_id = this.singleMerchantProductData.product.id;
					
				}

			},
			setProductManufacturer() {
				
				if (this.singleMerchantProductData.manufacturer && Object.keys(this.singleMerchantProductData.manufacturer).length > 0) {
					
					this.singleMerchantProductData.manufacturer_id = this.singleMerchantProductData.manufacturer.id;
					
				}
				else {

					this.$delete(this.singleMerchantProductData, 'manufacturer_id');

				}

			},
			resetErrorProductVariations(object) {

				this.errors = {

					product : {

					},

				};

				if (object.product.has_variations && object.hasOwnProperty('variations') && object.variations.length) {

					this.errors.product.variations = [];

					object.variations.forEach(
						(merchantProductVariation, merchantProductVariationIndex) => {
							
							this.errors.product.variations.push({});

						}
					);

				}

			},
			setProductVariation() {
				
				if (this.singleMerchantProductData.product.has_variations && this.singleMerchantProductData.product.hasOwnProperty('variations') && this.singleMerchantProductData.product.variations.length) {
					
					// this.singleMerchantProductData.variation_type_id = this.singleMerchantProductData.variation_type.id;
					
					// this.singleMerchantProductData.variations = [
					// 	{}
					// ];

					// this.errors.product.variations = [
					// 	{}
					// ];
					
					this.$set(this.singleMerchantProductData, 'variations', [ {} ]);
					this.$set(this.errors.product, 'variations', [ {} ]);

					this.allVariations = this.singleMerchantProductData.product.variations;

				}
				else {

					this.$delete(this.singleMerchantProductData, 'variations');
					this.$delete(this.errors.product, 'variations');

				}
				
			},
			addMoreVariation() {
				
				if (this.singleMerchantProductData.variations.length < this.singleMerchantProductData.product.variations.length) {
					
					// this.$set(this.singleMerchantProductData.variations, this.singleMerchantProductData.variations.length, {});
					// this.$set(this.errors.product.variations, this.errors.product.variations.length, {});

					// Vue.set(this.singleMerchantProductData.variations, this.singleMerchantProductData.variations.length, {});
					// Vue.set(this.errors.product.variations, this.errors.product.variations.length, {});

					this.singleMerchantProductData.variations.push({});
					this.errors.product.variations.push({});

				}

			},
			removeVariation() {

				if (this.singleMerchantProductData.variations.length > 1) {	
					
					this.singleMerchantProductData.variations.pop();
					this.errors.product.variations.pop();

					if (! this.errorInArray(this.errors.product.variations)) {
						this.submitForm = true;
					}

				}

			},
			onProductPreviewChange(evnt) {
				
				let files = evnt.target.files || evnt.dataTransfer.files;

                // Only process image files.
		      	if (files.length && files[0].type.match('image.*')) {
                	
		      		let reader = new FileReader();
	                reader.onload = (evnt) => {

	                    this.singleMerchantProductData.preview = evnt.target.result; 
                    	// this.$refs.merchantProductPreview.attributes[1]['value'] = evnt.target.result;
						// console.log(this.$refs.merchantProductPreview);
	                    
	                };
	                
	                reader.readAsDataURL(files[0]);                    

                	this.$delete(this.errors.product, 'preview');

		      	}
		      	else{

		      		this.errors.product.preview = 'File should be image';

		      	}

		      	evnt.target.value = '';
		      	return;

			},
			onVariationPreviewChange(evnt, index) {
				
				let files = evnt.target.files || evnt.dataTransfer.files;

                // Only process image files.
		      	if (files.length && files[0].type.match('image.*')) {
                	
		      		let reader = new FileReader();
	                
	                reader.onload = (evnt) => {

	                    this.singleMerchantProductData.variations[index].preview = evnt.target.result;
                    	// this.$refs['merchantProductVariationPreview-' + index][0]['attributes'][1]['value'] = evnt.target.result;
						// console.log(this.$refs['merchantProductVariationPreview-' + index][0]);

	                };

	                reader.readAsDataURL(files[0]);
                	
                	this.$delete(this.errors.product.variations[index], 'preview');

		      	}
		      	else{

		      		this.errors.product.variations[index].preview = 'File should be image';

		      	}

		      	evnt.target.value = '';
		      	return;

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

				// this.printingStyles.name = `${ this.singleMerchantProductData.subject } Details`;
				this.printingStyles.windowTitle = this.$options.filters.capitalize(`${ this.singleMerchantProductData.product.name } Details`);

				this.$htmlToPaper('sectionToPrint', this.printingStyles);

				$('#product-view-modal').modal('hide');

			},
			validateFormInput (formInputName) {

				this.submitForm = false;

				switch(formInputName) {

					case 'merchant_product_id' :

						if (! this.singleMerchantProductData.hasOwnProperty('product') || Object.keys(this.singleMerchantProductData.product).length == 0) {
							this.errors.product.merchant_product_id = 'Product is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.product, 'merchant_product_id');
						}

						break;

					case 'product_sku' :

						if (! this.singleMerchantProductData.sku) {
							this.errors.product.product_sku = 'Product SKU is required';
						}
						else if (! this.singleMerchantProductData.sku.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.product.product_sku = 'No special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.product, 'product_sku');
						}

						break;

					case 'product_price' :

						if (this.singleMerchantProductData.hasOwnProperty('product') && this.singleMerchantProductData.product.category && (! this.singleMerchantProductData.selling_price || this.singleMerchantProductData.selling_price < 0)) {
							this.errors.product.product_price = 'Price is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.product, 'product_price');
						}

						break;

					case 'discount' :

						if (this.singleMerchantProductData.hasOwnProperty('product') && this.singleMerchantProductData.product.category && (this.singleMerchantProductData.discount > 100 || this.singleMerchantProductData.discount < 0)) {
							this.errors.product.discount = 'Discount should be between 0 to 100';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.product, 'discount');
						}

						break;

				/*
					case 'product_sku' :

						if (this.singleMerchantProductData.sku && !this.singleMerchantProductData.sku.match(/^[a-zA-Z0-9]*$/g)) {
							this.errors.product.product_sku = 'Invalid code';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.product, 'product_sku');
						}

						break;
				*/

				/*
					case 'product_initial_quantity' :

						if (!this.singleMerchantProductData.initial_quantity || this.singleMerchantProductData.initial_quantity < 1) {
							this.errors.product.product_initial_quantity = 'Qty is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.product, 'product_initial_quantity');
						}

						break;

					case 'product_available_quantity' :

						if (!this.singleMerchantProductData.available_quantity || this.singleMerchantProductData.available_quantity < 0 || this.singleMerchantProductData.available_quantity > this.singleMerchantProductData.initial_quantity) {
							this.errors.product.product_available_quantity = 'Quantity is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.product, 'product_available_quantity');
						}

						break;

					case 'product_quantity_type' :

						if (!this.singleMerchantProductData.quantity_type) {
							this.errors.product.product_quantity_type = 'Qty type is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.product, 'product_quantity_type');
						}

						break;

					case 'product_variation_type' : 
						
						if (!this.singleMerchantProductData.has_variations) {
							this.submitForm = true;
							this.$delete(this.errors.product, 'product_variation_type');
						}
						else if (this.singleMerchantProductData.has_variations && (!this.singleMerchantProductData.variation_type || Object.keys(this.singleMerchantProductData.variation_type).length == 0)) {
							
							this.errors.product.product_variation_type = 'Variation type is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.product, 'product_variation_type');
						}

						break;
				*/

					case 'product_variation_id' :
						
						if (this.singleMerchantProductData.hasOwnProperty('product') && this.singleMerchantProductData.product.has_variations && this.singleMerchantProductData.hasOwnProperty('variations') && this.singleMerchantProductData.variations.length) {
							
							const noVariation = (merchantProductVariation) => ! merchantProductVariation.hasOwnProperty('variation') || ! merchantProductVariation.variation || Object.keys(merchantProductVariation.variation).length == 0;

							if (this.singleMerchantProductData.variations.some(noVariation)) {
										
								this.errors.product.variations[this.singleMerchantProductData.variations.findIndex(noVariation)].product_variation_id = 'Variation is required';

							}
							else {

								this.singleMerchantProductData.variations.forEach(
									
									(merchantProductVariation, index) => {
										
										if (merchantProductVariation.hasOwnProperty('product_variation_id') && /* this.singleMerchantProductData.variations.filter(obj => obj.variation.id === merchantProductVariation.product_variation_id).length > 0 */ this.singleMerchantProductData.variations.filter(productVariation => productVariation.product_variation_id === merchantProductVariation.product_variation_id).length > 1) {

											 this.errors.product.variations[index].product_variation_id = 'Same Variation selected';

										}
										else if (this.singleMerchantProductData.variations.filter(obj => obj.variation.id === merchantProductVariation.variation.id).length > 1) {

											 this.errors.product.variations[index].product_variation_id = 'Same Variation selected';

										}
										else {
											this.$delete(this.errors.product.variations[index], 'product_variation_id');
										}
										
									}

								);								

							}
							
							if (!this.errorInArray(this.errors.product.variations)) {
								this.submitForm = true;
							}

						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.product, 'variations');
						}

						break;

					/*
					case 'product_variation_quantity' :

						if (this.singleMerchantProductData.has_variations) {

							this.singleMerchantProductData.variations.forEach(
								(productVariation, index) => {

									if (!productVariation.hasOwnProperty('initial_quantity') || productVariation.initial_quantity < 1) {
										this.errors.product.variations[index].product_variation_quantity = 'Variation quantity is required';
									}
									else if (productVariation.initial_quantity < productVariation.requested_quantity) {
										this.errors.product.variations[index].product_variation_quantity = 'Quantity cant be less than required quantity';
									}
									else {
										this.$delete(this.errors.product.variations[index], 'product_variation_quantity');
									}

								}
								
							);
								
							if (!this.errorInArray(this.errors.product.variations)) {
								this.submitForm = true;
							}
							
						}
						else {
							this.submitForm = true;
							this.errors.product.variations = [
								{},
							];
						}

						break;
					*/

					case 'product_variation_price' :

						if (this.singleMerchantProductData.hasOwnProperty('product') && this.singleMerchantProductData.product.has_variations && this.singleMerchantProductData.hasOwnProperty('variations') && this.singleMerchantProductData.variations.length) {

							this.singleMerchantProductData.variations.forEach(
								(productVariation, index) => {
									
									if (! productVariation.selling_price || productVariation.selling_price < 0) {
										
										this.errors.product.variations[index].product_variation_price = 'Variation selling price is required';

									}
									else {
										this.$delete(this.errors.product.variations[index], 'product_variation_price');
									}

								}
							);
							
							if (!this.errorInArray(this.errors.product.variations)) {
								this.submitForm = true;
							}
						}
						else {
							this.submitForm = true;
							// this.errors.product.variations = [];
							this.$delete(this.errors.product, 'variations');
						}

						break;
					

					/*
					case 'product_variation_total_quantity' :

						if (this.singleMerchantProductData.has_variations) {

							if (!this.errorInArray(this.errors.product.variations)) {

								let variationTotalQuantity = this.singleMerchantProductData.variations.reduce(
									(value, currentObject) => {
										return value + currentObject.initial_quantity;
									}, 
								0);

								if (variationTotalQuantity !== this.singleMerchantProductData.initial_quantity) {
									this.errors.product.variations[this.singleMerchantProductData.variations.length-1].product_variation_quantity = 'Total variation qty should be equal to qty';
								}
								else {
									this.$delete(this.errors.product.variations[this.singleMerchantProductData.variations.length-1], 'product_variation_quantity');
								}
							}

						}
						else {
							this.submitForm = true;
							this.errors.product.variations = [
								{}, {}
							];
						}

						break;
					*/

				/*
					case 'product_space_type' :

						this.singleMerchantProductData.addresses.forEach(
							
							(productSpace, index) => {

								if (!productSpace.type) {
									this.errors.product.addresses[index].product_space_type = 'Space type is required';
								}
								else if (this.singleMerchantProductData.addresses.filter((obj) => obj.type === productSpace.type).length > 1) {

									this.errors.product.addresses[index].product_space_type = 'Same type selected';
								}
								else {
									this.$delete(this.errors.product.addresses[index], 'product_space_type');
								}

							}

						);
						
						if (!this.errorInArray(this.errors.product.addresses)) {
							this.submitForm = true;
						}

						break;

					case 'product_containers' :

						this.singleMerchantProductData.addresses.forEach(
							
							(productSpace, index) => {

								if (productSpace.type=='containers' && (!productSpace.containers || productSpace.containers.length == 0)) {
									this.errors.product.addresses[index].product_containers = 'Container is required';
								}
								else{
									this.$delete(this.errors.product.addresses[index], 'product_containers');
								}

							}

						);

						if (!this.errorInArray(this.errors.product.addresses)) {
							this.submitForm = true;
						}

						break;

					case 'product_container' :

						this.singleMerchantProductData.addresses.forEach(
							
							(productSpace, index) => {

								if ((productSpace.type=='shelves' || productSpace.type=='units') && (!productSpace.container || Object.keys(productSpace.container).length==0)) {
									this.errors.product.addresses[index].product_container = 'Container is required';
								}
								else{
									this.$delete(this.errors.product.addresses[index], 'product_container');
								}

							}
						);

						if (!this.errorInArray(this.errors.product.addresses)) {
							this.submitForm = true;
						}

						break;

					case 'product_shelves' : 

						this.singleMerchantProductData.addresses.forEach(
							
							(productSpace, index) => {

								if (productSpace.type=='shelves' && (!productSpace.container || !productSpace.container.shelves || productSpace.container.shelves.length == 0)) {
									this.errors.product.addresses[index].product_shelves = 'Shelf is required';
								}
								else{
									this.$delete(this.errors.product.addresses[index], 'product_shelves');
								}

							}
						);

						if (!this.errorInArray(this.errors.product.addresses)) {
							this.submitForm = true;
						}

						break;

					case 'product_shelf' :

						this.singleMerchantProductData.addresses.forEach(
							
							(productSpace, index) => {

								if (productSpace.type=='units' && (!productSpace.container || !productSpace.container.shelf || Object.keys(productSpace.container.shelf).length==0)) {
									this.errors.product.addresses[index].product_shelf = 'Shelf is required';
								}
								else{
									this.$delete(this.errors.product.addresses[index], 'product_shelf');
								}

							}
						);

						if (!this.errorInArray(this.errors.product.addresses)) {
							this.submitForm = true;
						}

						break;


					case 'product_units' :

						this.singleMerchantProductData.addresses.forEach(
							
							(productSpace, index) => {

								if (productSpace.type=='units' && (!productSpace.container || !productSpace.container.shelf || !productSpace.container.shelf.units || productSpace.container.shelf.units.length == 0)) {
									this.errors.product.addresses[index].product_units = 'Unit is required';
								}
								else{
									this.$delete(this.errors.product.addresses[index], 'product_units');
								}

							}
						);

						if (!this.errorInArray(this.errors.product.addresses)) {
							this.submitForm = true;
						}

						break;
				*/

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