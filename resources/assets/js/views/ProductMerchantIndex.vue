
<template v-if="userHasPermissionTo('view-merchant-product-index')">
	<div class="pcoded-content">
		<breadcrumb 
			:icon="'merchants'"
			:title="product.name + ' merchants'" 
			:message="'All our merchants for ' + product.name | capitalize"
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
											<div class="col-sm-12 sub-title">
											  	<search-and-addition-option 
											  		v-if="userHasPermissionTo('view-merchant-product-index') || userHasPermissionTo('create-merchant-product')" 
											  		:query="query" 
											  		:caller-page="'merchants'" 
											  		:required-permission = "'merchant-product'" 
											  		:disable-add-button="allMerchants.length==0 ? true : false" 
											  		
											  		@showContentCreateForm="showProductMerchantCreateForm" 
											  		@searchData="pagination.current_page = 1; searchData($event)" 
											  		@fetchAllContents="pagination.current_page = 1; fetchProductAllMerchants()"
											  	></search-and-addition-option>
											</div>
											
											<div class="col-sm-12 col-lg-12">
												<loading v-show="loading"></loading>

 												<div 
 													class="tab-content card-block pl-0 pr-0" 
 													v-show="!loading"
 												>
													<div class="card">
														<div class="table-responsive">
															<table class="table table-striped table-bordered nowrap text-center">
																<thead>
																	<tr>
																		<th>Merchant</th>
																		<th>Manufacturer/Brand</th>
																		<th>SKU</th>
																		<th>Available</th>
																		<th>Actions</th>
																	</tr>
																</thead>

																<tbody>
																	<tr 
																		v-for="productMerchant in productAllMerchants" 
																		:key="'product-id' + product.id + '-product-merchant-id-' + productMerchant.id" 
																		:class="productMerchant.id==singleMerchantProductData.id ? 'highlighted' : ''"
																	>
																		<td>
																			{{ getMerchantFullName(productMerchant) }}
																		</td>
																		
																		<td>
																			{{ productMerchant.manufacturer ? productMerchant.manufacturer.name : 'Own Product' | capitalize }}
																		</td>

																		<td>{{ productMerchant.sku }}</td>

																		<td>{{ productMerchant.available_quantity + ' ' + product.quantity_type }}</td>
																		
																		<td>
																			<button 
																				type="button" 
																				class="btn waves-effect waves-dark btn-info btn-outline-info btn-icon" 
																				v-tooltip.bottom-end="'View Details'"  
																				@click="showProductMerchantDetails(productMerchant)"
																			>
																				<i class="fa fa-eye"></i>
																			</button>

																			<button 
																				type="button" 
																				class="btn waves-effect waves-dark btn-primary btn-outline-primary btn-icon" 
																				v-tooltip.bottom-end="'Edit'"  
																				@click="openProductMerchantEditForm(productMerchant)" 
																				v-if="userHasPermissionTo('update-merchant-product')"
																			>
																				<i class="fa fa-edit"></i>
																			</button>

																			<button 
																				type="button" 
																				class="btn waves-effect waves-dark btn-danger btn-outline-danger btn-icon" 
																				v-tooltip.bottom-end="'Delete'"  
																				@click="openProductMerchantDeleteForm(productMerchant)" 
																				v-if="userHasPermissionTo('delete-merchant-product')" 
																				:disabled="productMerchant.product_immutability"
																			>
																				<i class="fa fa-trash"></i>
																			</button>

																			<button 
																				type="button" 
																				class="btn waves-effect waves-dark btn-warning btn-outline-warning btn-icon" 
																				v-tooltip.bottom-end="'Requisitions'"  
																				@click="goProductRequisitions(productMerchant)" 
																				v-if="userHasPermissionTo('view-requisition-index')"
																			>
																				<i class="fa fa-truck" aria-hidden="true"></i>
																			</button>

																			<button 
																				type="button" 
																				class="btn waves-effect waves-dark btn-success btn-outline-success btn-icon" 
																				v-tooltip.bottom-end="'Stock'"  
																				@click="goProductStore(productMerchant)" 
																				v-if="userHasPermissionTo('view-product-stock-index')"
																			>
																				<img src="/icons/cms/stocks.png" style="width: 17px">
																			</button>
																		</td>
																	</tr>

																	<tr 
																  		v-show="! productAllMerchants.length"
																  	>
															    		<td colspan="5">
																      		<div class="alert alert-danger text-center" role="alert">
																      			Sorry, No data found.
																      		</div>
																    	</td>
																  	</tr>
																</tbody>

																<tfoot>
																	<tr>	
																		<th>Merchant</th>
																		<th>Manufacturer/Brand</th>
																		<th>SKU</th>
																		<th>Available</th>
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
																class="btn waves-effect waves-dark btn-primary btn-outline-primary btn-sm" 
																v-tooltip.bottom-end="'Reload'" 
																@click="pagination.current_page = 1; query === '' ? fetchProductAllMerchants() : searchData()"
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
																@paginate="query === '' ? fetchProductAllMerchants() : searchData()"
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
		<div class="modal fade" id="product-createOrEdit-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" v-if="userHasPermissionTo('create-merchant-product') || userHasPermissionTo('update-merchant-product')">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">
							{{ product.name | capitalize }} {{ createMode ? ' New ' : ' Update ' | capitalize }} Merchant
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
										<div class="form-group col-sm-6">
							        		<label for="inputUsername">Selected Product</label>
								        	<multiselect 
		                              			v-model="product" 
		                              			class="form-control p-0 is-valid" 
		                              			placeholder="Product Name" 
		                                  		:custom-label="objectNameWithCapitalized" 
		                                  		:options="[]" 
		                                  		:allow-empty="false" 
		                                  		:disabled="true" 
		                              		>
		                                	</multiselect>
							        	</div>
							        	
							        	<div class="form-group col-md-6">
											<label for="inputUsername">Merchant</label>
											<multiselect 
		                              			v-model="singleMerchantProductData.merchant"
		                              			placeholder="Merchant" 
		                                  		label="user_name" 
		                                  		track-by="id" 
		                                  		:custom-label="objectNameWithCapitalized" 
		                                  		:options="allMerchants" 
		                                  		:required="true" 
		                                  		:allow-empty="false"
		                                  		selectLabel = "Press/Click"
		                                  		deselect-label="Can't remove single value" 
		                                  		class="form-control p-0" 
		                                  		:class="!errors.product_merchant_id  ? 'is-valid' : 'is-invalid'"
		                                  		@close="validateFormInput('product_merchant_id')" 
		                                  		@input="setProductMerchant"
		                              		>
		                                	</multiselect>

		                                	<div class="invalid-feedback">
										    	{{ errors.product_merchant_id }}
										    </div>
										</div>
									</div>

							        <div class="form-row">
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

										<div class="form-group col-md-6">
											<label for="inputFirstName">UPC</label>
											<input type="number" 
												class="form-control" 
												v-model="singleMerchantProductData.upc" 
												placeholder="Universal Product Code" 
												:class="!errors.product_upc ? 'is-valid' : 'is-invalid'" 
												@change="validateFormInput('product_upc')" 
												min="100000000001" 
												max="999999999999"
											>

											<div class="invalid-feedback">
									        	{{ errors.product_upc }}
									  		</div>
										</div> 
							        </div>

									<div class="form-row">
										<div class="form-group col-md-6">
											<label for="inputFirstName">Product SKU (max:15)</label>
											<input type="text" 
												class="form-control" 
												v-model="singleMerchantProductData.sku" 
												placeholder="SKU should be unique" 
												:class="!errors.product_sku ? 'is-valid' : 'is-invalid'" 
												@change="validateFormInput('product_sku')" 
												maxlength="15" 
											>

											<div class="invalid-feedback">
									        	{{ errors.product_sku }}
									  		</div>
										</div>

										<div class="form-group col-md-6">
											<label for="inputFirstName">Selling Price (unit)</label>

									  		<div class="input-group mb-0">
												<input 
													type="number" 
													class="form-control" 
													v-model.number="singleMerchantProductData.selling_price" 
													placeholder="Product Selling Price" 
													:class="!errors.product_price  ? 'is-valid' : 'is-invalid'" 
													@change="validateFormInput('product_price')" 
													:disabled="product.category ? false : true"
												>
												<div class="input-group-append">
													<span class="input-group-text" id="basic-addon2">
														{{ general_settings.official_currency_name || 'BDT' | capitalize }}
													</span>
												</div>
											</div>

											<div 
												class="invalid-feedback" 
												style="display: block;" 
												v-show="errors.product_price"
											>
									        	{{ errors.product_price }}
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
													:class="!errors.discount ? 'is-valid' : 'is-invalid'" 
													@change="validateFormInput('discount')" 
													:disabled="product.category ? false : true"
											    >
											    <div class="input-group-append">
											      	<span class="input-group-text">%</span>
											    </div>
											</div>

											<div 
												class="invalid-feedback" 
												style="display: block;" 
												v-show="errors.discount"
											>
									        	{{ errors.discount }}
									  		</div>
										</div>

										<div class="form-group col-md-6">
											<label for="inputFirstName">Warning Qty</label>
											
											<div class="input-group mb-0">
												<input type="number" 
													class="form-control is-valid" 
													v-model.number="singleMerchantProductData.warning_quantity" 
													placeholder="Product Warning Qty" 
												>
												<div class="input-group-append">
													<span class="input-group-text" id="basic-addon2">
														{{ general_settings.official_currency_name || 'BDT' | capitalize }}
													</span>
												</div>
											</div>
										</div>
									</div>

									<div class="form-row">
										<div class="col-md-12">
											<div class="card">
										    	<div class="card-body">
										    		<div class="form-row d-flex align-items-center text-center">
														<div class="form-group col-md-6">
															<img 
																width="100px"   
																class="img-fluid" 
																:src="showPreview(singleMerchantProductData.preview)"
																alt="Product Preview" 
															>
														</div>
														
														<div class="form-group col-md-6">
															<div class="custom-file">
															    <input type="file" 
															    	class="form-control custom-file-input" 
																	:class="!errors.preview  ? 'is-valid' : 'is-invalid'" 
														    	 	@change="onProductPreviewChange" 
														    	 	accept="image/*"
															    >
															    <label class="custom-file-label" for="validatedCustomFile">Choose Picture...</label>
															    <div class="invalid-feedback">
															    	{{ errors.preview }}
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
				                              	@input="validateFormInput('description')"
				                            >
			                              	</ckeditor>

			                              	<div 
			                              		class="invalid-feedback" 
			                              		style="display: block;" 
			                              		v-show="errors.description"
			                              	>
										    	{{ errors.description }}
										    </div>
										</div>
									</div>

									<div class="form-control form-group" v-if="product.hasOwnProperty('has_serials') && product.has_serials">
										<div class="form-row mt-2">
											<div class="col-md-12 text-center">
												<toggle-button 
													v-model="product.has_serials" 
													:width=200 
													:sync="true"
													:color="{checked: 'orange', unchecked: 'green'}"
													:labels="{checked: 'Has Serial', unchecked: 'No Serial'}" 
													:disabled="true" 
												/>
											</div>
										</div>
									</div>

									<div class="form-control form-group" v-if="product.has_variations && product.hasOwnProperty('variations') && product.variations.length">
										<div class="form-row mt-3">
											<div class="form-group col-md-12 text-center">
												<toggle-button 
													v-model="product.has_variations" 
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
			                              			v-model="product.variation_type"
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

											<div class="form-group col-md-9" v-if="singleMerchantProductData.hasOwnProperty('variations') && errors.hasOwnProperty('variations') && singleMerchantProductData.variations.length == errors.variations.length">
												<div 
													class="card" 
													v-for="(merchantProductVariation, index) in singleMerchantProductData.variations" 
													:key="'merchant-product-variation-index-' + index + 'A'"
												>
													<div class="card-body">
														<div 
															class="form-row" 
															v-if="singleMerchantProductData.variations.length == errors.variations.length"
														>	
															<div class="form-group col-md-12">
																<div class="form-row">
																	<div class="form-group col-md-6">
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
									                                  		:class="!errors.variations[index].product_variation_id ? 'is-valid' : 'is-invalid'" 
									                                  		:required="true" 
							                                  				:allow-empty="false" 
																			@close="changeProductVariation(index)"
									                              		>
									                                	</multiselect>

									                                	<div class="invalid-feedback">
																	    	{{ errors.variations[index].product_variation_id }}
																	    </div>
																	</div>

																	<div class="form-group col-sm-6">
																		<label for="inputFirstName">UPC</label>
																		<input type="number" 
																			class="form-control" 
																			v-model="merchantProductVariation.upc" 
																			placeholder="Universal Product Code" 
																			:class="!errors.variations[index].product_variation_upc ? 'is-valid' : 'is-invalid'" 
																			@change="validateFormInput('product_variation_upc')" 
																			min="100000000001" 
																			max="999999999999" 
																		>

																		<div class="invalid-feedback">
																        	{{ errors.variations[index].product_variation_upc }}
																  		</div>
																	</div> 
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

																  		<div class="input-group mb-0">
																			<input 
																				type="number" 
																				class="form-control" 
																				v-model.number="merchantProductVariation.selling_price" 
																				placeholder="Variation Selling Price" 
																				:class="!errors.variations[index].product_variation_price ? 'is-valid' : 'is-invalid'" 
																				@change="validateFormInput('product_variation_price')" 
																			>
																			<div class="input-group-append">
																				<span class="input-group-text" id="basic-addon2">
																					{{ general_settings.official_currency_name || 'BDT' | capitalize }}
																				</span>
																			</div>
																		</div>

																		<div class="invalid-feedback" style="display: block;" v-show="errors.variations[index].product_variation_price">
																        	{{ errors.variations[index].product_variation_price }}
																  		</div>
																	</div>

																	<div class="form-group col-md-6">
																		<label for="inputFirstName">Variation SKU (max:15)</label>
																
																		<input type="text" 
																			class="form-control is-valid" 
																			v-model="merchantProductVariation.sku" 
																			placeholder="Variation Unique SKU" 
																			maxlength="15" 
																		>
																	</div>
																</div>
															</div>

															<div class="form-group col-md-12">
																<div class="form-row text-center d-flex">
																	<div class="col-md-6 form-group">
																		<img 
																			width="100px" 
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
																				:class="!errors.variations[index].preview  ? 'is-valid' : 'is-invalid'" 
																	    	 	@change="onVariationPreviewChange($event, index)" 
																	    	 	accept="image/*"
																		    >
																		    <label class="custom-file-label" for="validatedCustomFile">Choose Picture...</label>
																		    <div class="invalid-feedback">
																		    	{{ errors.variations[index].preview }}
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
															class="btn waves-effect waves-light hor-grd btn-primary btn-outline-primary btn-sm btn-block" 
															v-tooltip.bottom-end="'Create New'" 
															:disabled="singleMerchantProductData.variations.length >= product.variations.length" 
															@click="addMoreVariation()"
														>
															Add Variation
														</button>
													</div>

													<div class="form-group col-md-6">
														<button 
															type="button" 
															class="btn waves-effect waves-light hor-grd btn-info btn-outline-info btn-sm btn-block" 
															v-tooltip.bottom-end="'Remove Variation'" 
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
						                  	<button type="button" class="btn waves-effect waves-dark btn-secondary btn-outline-secondary float-left" data-dismiss="modal">
						                  		Close
						                  	</button>
											<button type="submit" class="btn waves-effect waves-dark btn-primary btn-outline-primary float-right" :disabled="!submitForm || formSubmitted">
												{{ createMode ? 'Save' : 'Update' }}
											</button>
										</div>
									</div>
										
							    	<!-- 
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
 									-->

							    	<!-- 
								    <div 
										class="row" 
										v-bind:key="2" 
										v-show="!loading && step==2"
									>
										<h2 class="mx-auto mb-4 lead">Store Product</h2>

										<div 
											class="col-md-12"
											v-for="(productSpace, index) in singleMerchantProductData.addresses" 
											:key="'product-space-' + index"
										>
											<div 
												class="card"
												v-if="singleMerchantProductData.addresses[index] && errors.addresses[index]"
											>
												<div class="card-body">

													<div class="form-row ml-5 mr-5">
														<div class="form-group col-md-12 text-center">
															<label for="inputFirstName">
																Required Space Type {{ index + 1 }}
															</label>
															<multiselect 
						                              			v-model="singleMerchantProductData.addresses[index].type"
						                                  		:options="['containers', 'shelves', 'units']" 
						                                  		:custom-label="nameWithCapitalized" 
						                                  		:required="true" 
						                                  		:allow-empty="false"
						                              			placeholder="Containers / Shelves / Units" 
						                                  		:class="!errors.addresses[index].product_space_type  ? 'is-valid' : 'is-invalid'" 
						                                  		:disabled="singleMerchantProductData.addresses.length > (index+1)" 
						                                  		@input="setProductSpaceType(index)" 
						                                  		@close="validateFormInput('product_space_type')"
						                              		>
						                                	</multiselect>
						                                	<div 
							                                	class="invalid-feedback" 
							                                	style="display: block;" 
							                                	v-show="errors.addresses[index].product_space_type"
						                                	>
														    	{{ errors.addresses[index].product_space_type }}
														    </div>
														</div>
													</div>

													<div 
														class="form-row" 
														v-show="singleMerchantProductData.addresses[index].type=='containers'"
													>
														<div class="form-group col-md-12">
															<label for="inputFirstName">Select Containers</label>
															<multiselect 
						                              			v-model="singleMerchantProductData.addresses[index].containers"
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
						                                  		:class="!errors.addresses[index].product_containers  ? 'is-valid' : 'is-invalid'" 
						                                  		:disabled="singleMerchantProductData.addresses.length > (index+1)"
						                                  		@close="validateFormInput('product_containers')" 
						                              		>
						                                	</multiselect>
						                                	<div 
							                                	class="invalid-feedback" 
							                                	style="display: block;" 
							                                	v-show="errors.addresses[index].product_containers"
						                                	>
														    	{{ errors.addresses[index].product_containers }}
														    </div>
														</div>
													</div>

													<div 
														class="form-row" 
														v-show="singleMerchantProductData.addresses[index].type=='shelves'"
													>
														<div class="form-group col-md-6">
															<label for="inputFirstName">Select Parent Container</label>
															<multiselect 
						                              			v-model="singleMerchantProductData.addresses[index].container"
						                              			placeholder="Parent Container" 
						                              			label="name" 
						                                  		track-by="id" 
						                                  		:options="emptyShelfContainers" 
						                                  		:required="true" 
						                                  		:allow-empty="false"
						                                  		:class="!errors.addresses[index].product_container ? 'is-valid' : 'is-invalid'" 
						                                  		:disabled="singleMerchantProductData.addresses.length > (index+1)"
						                                  		@input="setAvailableShelves(index)"
						                                  		@close="validateFormInput('product_container')" 
						                              		>
						                                	</multiselect>
						                                	<div 
							                                	class="invalid-feedback" 
							                                	style="display: block;" 
							                                	v-show="errors.addresses[index].product_container"
						                                	>
														    	{{ errors.addresses[index].product_container }}
														    </div>
														</div>

														<div 
															class="form-group col-md-6" 
															v-if="singleMerchantProductData.addresses[index].container"
														>
															<label for="inputFirstName">Select Shelves</label>
															<multiselect 
						                              			v-model="singleMerchantProductData.addresses[index].container.shelves"
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
						                                  		:class="!errors.addresses[index].product_shelves ? 'is-valid' : 'is-invalid'" 
						                                  		:disabled="singleMerchantProductData.addresses.length > (index+1)"
						                                  		@close="validateFormInput('product_shelves')" 
						                              		>
						                                	</multiselect>
						                                	<div 
							                                	class="invalid-feedback" 
							                                	style="display: block;" 
							                                	v-show="errors.addresses[index].product_shelves"
						                                	>
														    	{{ errors.addresses[index].product_shelves }}
														    </div>
														</div>
													</div>

													<div class="form-row" v-show="singleMerchantProductData.addresses[index].type=='units'">
														<div class="form-group col-md-4">
															<label for="inputFirstName">Select Parent Container</label>
															<multiselect 
						                              			v-model="singleMerchantProductData.addresses[index].container"
						                              			placeholder="Parent Container" 
						                              			label="name" 
						                                  		track-by="id" 
						                                  		:options="emptyUnitContainers" 
						                                  		:required="true" 
						                                  		:allow-empty="false"
						                                  		:class="!errors.addresses[index].product_container  ? 'is-valid' : 'is-invalid'" 
						                                  		:disabled="singleMerchantProductData.addresses.length > (index+1)"
						                                  		@input="setAvailableUnitShelves(index)" 
						                                  		@close="validateFormInput('product_container')" 
						                              		>
						                                	</multiselect>
						                                	<div 
							                                	class="invalid-feedback" 
							                                	style="display: block;" 
							                                	v-show="errors.addresses[index].product_container"
						                                	>
														    	{{ errors.addresses[index].product_container }}
														    </div>
														</div>

														<div 
															class="form-group col-md-4" 
															v-if="singleMerchantProductData.addresses[index].container"
														>
															<label for="inputFirstName">Select Parent Shelf</label>
															<multiselect 
						                              			v-model="singleMerchantProductData.addresses[index].container.shelf"
						                              			placeholder="Parent Shelf" 
						                              			label="name" 
						                                  		track-by="id" 
						                                  		:options="emptyUnitShelves" 
						                                  		:required="true" 
						                                  		:allow-empty="false"
						                                  		:class="!errors.addresses[index].product_shelf  ? 'is-valid' : 'is-invalid'" 
						                                  		:disabled="singleMerchantProductData.addresses.length > (index+1)"
						                                  		@input="setAvailableUnits(index)" 
						                                  		@close="validateFormInput('product_shelf')" 
						                              		>
						                                	</multiselect>
						                                	<div 
							                                	class="invalid-feedback" 
							                                	style="display: block;" 
							                                	v-show="errors.addresses[index].product_shelf"
						                                	>
														    	{{ errors.addresses[index].product_shelf }}
														    </div>
														</div>

														<div 
															class="form-group col-md-4" 
															v-if="singleMerchantProductData.addresses[index].container && singleMerchantProductData.addresses[index].container.shelf"
														>
															<label for="inputFirstName">Select Units</label>
															<multiselect 
						                              			v-model="singleMerchantProductData.addresses[index].container.shelf.units"
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
						                                  		:class="!errors.addresses[index].product_units ? 'is-valid' : 'is-invalid'" 
						                                  		:disabled="singleMerchantProductData.addresses.length > (index+1)"
						                                  		@close="validateFormInput('product_units')" 
						                              		>
						                                	</multiselect>
						                                	<div 
							                                	class="invalid-feedback" 
							                                	style="display: block;" 
							                                	v-show="errors.addresses[index].product_units"
						                                	>
														    	{{ errors.addresses[index].product_units }}
														    </div>
														</div>
													</div>

												</div>
											</div>
										</div>

										<div 
											class="col-md-12 text-center" 
											v-show="!singleMerchantProductData.addresses.length"
										>
											<p class="text-danger">
												No Space Found.
											</p>
										</div>

										<div class="col-md-12">
											<div class="form-row">
												<div class="form-group col-md-6">
													<button 
														type="button" 
														class="btn waves-effect waves-light hor-grd btn-grd-primary btn-sm btn-block" 
														@click="addMoreSpace()"
													>
														Add Space
													</button>
												</div>
												<div class="form-group col-md-6">
													<button 
														type="button" 
														class="btn waves-effect waves-light hor-grd btn-grd-info btn-sm btn-block" 
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
													<button type="button" class="btn btn-outline-secondary btn-sm btn-round float-left" v-on:click="step-=1">
								                    	<i class="fa fa-2x fa-angle-double-left" aria-hidden="true"></i>
								                  	</button>
													<button type="submit" class="btn waves-effect waves-dark btn-primary btn-outline-primary float-right" :disabled="!submitForm">
														{{ createMode ? 'Save' : 'Update' }}
													</button>
												</div>
											</div>
										</div>
									</div> 
									-->
							<!-- </transition-group> -->
						</div>
					</form>
				</div>
			</div>
		</div>

		<!-- 
		<restore-confirmation-modal 
			:csrf="csrf" 
			:submit-method-name="'restoreAsset'" 
			:content-to-restore="singleMerchantProductData"
			:restoration-message="'This will restore all related items !'" 

			@restoreAsset="restoreAsset($event)" 
		></restore-confirmation-modal>
 		-->

 		<!-- View Modal -->
		<div class="modal fade" id="merchant-product-view-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">{{ product.name | capitalize }} Details</h5>
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

									<li class="nav-item" v-show="product.has_serials">
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
													width="100px"
													:src="showPreview(singleMerchantProductData.preview)" 
													class="img-fluid" 
													alt="Product Preview" 
												>
											</div>

											<div class="col-md-8">
												<div class="form-row">
													<label class="col-sm-4 col-form-label font-weight-bold">
														Merchant :
													</label>
													<label class="col-sm-8 col-form-label">
														{{ singleMerchantProductData.merchant ? singleMerchantProductData.merchant.user_name : 'None' | capitalize }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-sm-4 col-form-label font-weight-bold">
														Manufacturer/Brand :
													</label>
													<label class="col-sm-8 col-form-label">
														{{ singleMerchantProductData.manufacturer ? singleMerchantProductData.manufacturer.name : 'own product' | capitalize }}
													</label>
												</div>

												<div class="form-row" v-show="singleMerchantProductData.sku">
													<label class="col-sm-4 col-form-label font-weight-bold">
														Product SKU :
													</label>
													<label class="col-sm-8 col-form-label">
														{{ singleMerchantProductData.sku }}
													</label>
												</div>

												<div 
													class="form-row" 
													v-show="singleMerchantProductData.upc"
												>
													<label class="col-4 col-form-label font-weight-bold">
														UPC :
													</label>
													<label class="col-8 col-form-label">
														{{ singleMerchantProductData.upc }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-sm-4 col-form-label font-weight-bold">
														Description :
													</label>
													<label class="col-sm-8 col-form-label">
														<span v-html="singleMerchantProductData.description"></span>
													</label>
												</div>

												<div class="form-row">
													<label class="col-sm-4 col-form-label font-weight-bold">
														Warning Qty :
													</label>
													<label class="col-sm-8 col-form-label">
														{{ singleMerchantProductData.warning_quantity + ' ' + product.quantity_type }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-sm-4 col-form-label font-weight-bold">
														Available Qty :
													</label>
													<label class="col-sm-8 col-form-label">
														{{ singleMerchantProductData.available_quantity + ' ' + product.quantity_type }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-sm-4 col-form-label font-weight-bold">
														Dispatched Qty :
													</label>
													<label class="col-sm-8 col-form-label">
														{{ singleMerchantProductData.dispatched_quantity + ' ' + product.quantity_type }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-sm-4 col-form-label font-weight-bold">
														Pending Requested Qty :
													</label>
													<label class="col-sm-8 col-form-label">
														{{ singleMerchantProductData.requested_quantity + ' ' + product.quantity_type }}
													</label>
												</div>	

												<div 
													class="form-row" 
													v-show="singleMerchantProductData.selling_price"
												>
													<label class="col-sm-4 col-form-label font-weight-bold">
														Selling Price (unit) :
													</label>
													<label class="col-sm-8 col-form-label">
														{{ singleMerchantProductData.selling_price }} 
														{{ general_settings.official_currency_name || 'BDT' | capitalize }}
													</label>
												</div>

												<div class="form-row">
													<label class="col-sm-4 col-form-label font-weight-bold">
														Discount :
													</label>
													<label class="col-sm-8 col-form-label">
														{{ singleMerchantProductData.discount || 0 }} %
													</label>
												</div>

												<div class="form-row">
													<label class="col-sm-4 col-form-label font-weight-bold">Serials :</label>
													<label class="col-sm-6 form-control-plaintext">
														<span :class="[product.has_serials ? 'badge-info' : 'badge-primary', 'badge']">{{ product.has_serials ? 'Available' : 'NA' }}</span>
													</label>
												</div>

												<div class="form-row">
													<label class="col-sm-4 col-form-label font-weight-bold">Variation :</label>
													<label class="col-sm-6 form-control-plaintext">
														<span :class="[product.has_variations ? 'badge-info' : 'badge-primary', 'badge']">{{ product.has_variations ? 'Available' : 'NA' }}</span>
													</label>
												</div>

												<div class="form-row">
													<label class="col-sm-4 col-form-label font-weight-bold">
														Created on :
													</label>
													<label class="col-sm-8 col-form-label">
														{{ singleMerchantProductData.created_at }}
													</label>
												</div>

												<div class="form-row" v-if="product.has_variations && singleMerchantProductData.hasOwnProperty('variations') && singleMerchantProductData.variations.length">
													<label class="col-sm-4 col-form-label font-weight-bold">
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
																					:src="showPreview(merchantProductVariation.preview)"
																					:alt="merchantProductVariation.variation ? merchantProductVariation.variation.name : 'NA' + 'Preview'" 
																					width="100px"
																				>

																				<p>
																					{{ merchantProductVariation.variation ? merchantProductVariation.variation.name : 'NA' | capitalize }}
																				</p>
																			</div>
																		</div>

																		<div class="form-row">
																			<label class="col-sm-4 col-form-label font-weight-bold">
																				Variation :
																			</label>
																			<label class="col-sm-8 col-form-label">
																				{{ merchantProductVariation.variation ? merchantProductVariation.variation.name : 'NA' | capitalize }}
																			</label>
																		</div>

																		<div class="form-row">
																			<label class="col-sm-4 col-form-label font-weight-bold">
																				SKU :
																			</label>
																			<label class="col-sm-8 col-form-label">
																				{{ merchantProductVariation.sku }}
																			</label>
																		</div>

																		<div 
																			class="form-row" 
																			v-show="merchantProductVariation.upc"
																		>
																			<label class="col-4 col-form-label font-weight-bold">
																				UPC :
																			</label>
																			<label class="col-8 col-form-label">
																				{{ merchantProductVariation.upc }}
																			</label>
																		</div>

																		<div 
																			class="form-row"
																			v-show="merchantProductVariation.selling_price"
																		>
																			<label class="col-sm-4 col-form-label font-weight-bold">
																				Selling Price (unit) :
																			</label>
																			<label class="col-sm-8 col-form-label">
																				{{ merchantProductVariation.selling_price || 0 }}
																				{{ general_settings.official_currency_name || 'BDT' | capitalize }}
																			</label>
																		</div>

																		<div class="form-row">
																			<label class="col-4 col-form-label font-weight-bold">
																				Available Qty :
																			</label>
																			<label class="col-8 col-form-label">
																				{{ (merchantProductVariation.available_quantity + merchantProductVariation.previous_quantity) }} {{ singleMerchantProductData.product ? singleMerchantProductData.product.quantity_type : 'unit' }}
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

									<div class="tab-pane" id="product-serial" role="tabpanel" v-show="product.has_serials">
										<div class="form-row">
											<label class="col-sm-4 col-form-label font-weight-bold">
												Serials :
											</label>
											<div class="col-sm-8 col-form-label">
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

									<div class="tab-pane" id="product-address" role="tabpanel">
										<div 
											class="form-row" 
											v-if="singleMerchantProductData.hasOwnProperty('addresses') && singleMerchantProductData.addresses.length"
										>
											<label class="col-sm-4 col-form-label font-weight-bold">
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
																	<label class="col-sm-4 col-form-label font-weight-bold">
																		Warehouse :
																	</label>
																	<label class="col-sm-8 col-form-label">
																		{{ containerAddress.warehouse_container ? $options.filters.capitalize(containerAddress.warehouse_container.warehouse.name) : 'NA' }}
																	</label>
																</div>

																<div class="form-row">
																	<label class="col-sm-4 col-form-label font-weight-bold">
																		Container Type :
																	</label>
																	<label class="col-sm-8 col-form-label">
																		{{ containerAddress.warehouse_container ? $options.filters.capitalize(containerAddress.warehouse_container.container.name) : 'NA' }}
																	</label>
																</div>

																<div class="form-row">
																	<label class="col-sm-4 col-form-label font-weight-bold">
																		Container # :
																	</label>
																	<label class="col-sm-8 col-form-label">
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
																	<label class="col-sm-4 col-form-label font-weight-bold">
																		Container Type :
																	</label>
																	<label class="col-sm-8 col-form-label">
																		{{ stockAddress.container.warehouse_container.container.name | capitalize }}
																	</label>
																</div>

																<div class="form-row">
																	<label class="col-sm-4 col-form-label font-weight-bold">
																		Container # :
																	</label>
																	<label class="col-sm-8 col-form-label">
																		{{ stockAddress.container.name.substring(stockAddress.container.name.indexOf("-")+1) }}
																	</label>
																</div>

																<div 
																	class="form-row"
																>
																	<label class="col-sm-4 col-form-label font-weight-bold">
																		Shelf # :
																	</label>
																	<label class="col-sm-8 col-form-label">

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
																	<label class="col-sm-4 col-form-label font-weight-bold">
																		Container Type :
																	</label>
																	<label class="col-sm-8 col-form-label">
																		{{ stockAddress.container.warehouse_container.container.name | capitalize }}
																	</label>
																</div>

																<div class="form-row">
																	<label class="col-sm-4 col-form-label font-weight-bold">
																		Container # :
																	</label>
																	<label class="col-sm-8 col-form-label">
																		{{ stockAddress.container.name.substring(stockAddress.container.name.indexOf("-")+1) }}
																	</label>
																</div>

																<div class="form-row">
																	<label class="col-sm-4 col-form-label font-weight-bold">
																		Shelf # :
																	</label>
																	<label class="col-sm-8 col-form-label">
																		{{ stockAddress.container.shelf.name.substring(stockAddress.container.shelf.name.lastIndexOf("-")+1) }}
																	</label>
																</div>

																<div class="form-row">
																	<label class="col-sm-4 col-form-label font-weight-bold">
																		Unit # :
																	</label>
																	<label class="col-sm-8 col-form-label">

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
						<button type="button" class="btn waves-effect waves-dark btn-secondary btn-outline-secondary btn-sm btn-block" data-dismiss="modal">Close</button>
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
							<h4 class="text-danger">Want to delete '{{ singleMerchantProductData.hasOwnProperty('merchant') ? singleMerchantProductData.merchant.user_name : '' | capitalize }}' ?</h4>
							<h6 class="sub-heading text-secondary">Warning : You can not restore this item !</h6>
						</div>

						<div class="modal-footer">
							<button type="button" class="btn waves-effect waves-dark btn-secondary btn-outline-secondary mr-auto" data-dismiss="modal">Close</button>
							<button type="submit" class="btn waves-effect waves-dark btn-danger btn-outline-danger">Delete</button>
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

			product:{
				type: Object,
				required: true,
			},

		},

	    data() {

	        return {

	        	// step : 1,
	        	query : '',
	        	error : '',
    			perPage : 10,
	        	loading : false,

	        	editor: ClassicEditor,

	        	createMode : true,
	        	submitForm : true,
	        	formSubmitted : false,

	        	// allVariationTypes : [],
	        	
	        	allMerchants : [],
	        	allVariations : [],
	        	allManufacturers : [],
	        	productAllMerchants : [],

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
					
					// variations : [],
					
					/*
						addresses : [
							{}
						],
					*/
					
				},

				general_settings : JSON.parse(window.localStorage.getItem('general_settings')),

	            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

	        }

		},
		
		created(){

			this.fetchAllMerchants();
			this.fetchAllManufacturers();
			// this.setProductVariation();
			// this.fetchAllContainers();
			this.fetchProductAllMerchants();

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

			fetchProductAllMerchants() {

				this.query = '';
				this.error = '';
				this.loading = true;
				this.productAllMerchants = [];
				
				axios
					.get('/api/product-merchants/' + this.product.id + '/' + this.perPage + "?page=" + this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							this.productAllMerchants = response.data.data;
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
				
				if (! this.userHasPermissionTo('view-merchant-index')) {

					this.error = 'You do not have permission to view merchants';
					return;

				}

				this.query = '';
				this.error = '';
				this.loading = true;
				this.allMerchants = [];
				
				axios
					.get('/api/merchants/')
					.then(response => {
						if (response.status == 200) {
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
	        	
				this.singleMerchantProductData = {};

				this.errors = {
						
					// variations : [],

				};

				this.setProductVariation();

				$('#product-createOrEdit-modal').modal('show');
			},
			openProductMerchantEditForm(object) {
				// this.step = 1;
				this.createMode = false;
	        	this.submitForm = true;
	        	this.formSubmitted = false;
				
				this.resetErrorProductVariations(object);
				this.allVariations = this.product.variations;

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

				this.singleMerchantProductData.product_id = this.product.id;

				axios
					.post('/product-merchants/' + this.perPage, this.singleMerchantProductData)
					.then(response => {

						if (response.status == 200) {

							this.$toastr.s("New merchant has been stored", "Success");
							
							this.pagination.current_page = 1; 
							this.productAllMerchants = response.data.data;
							
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
				this.singleMerchantProductData.product_id = this.product.id;

				axios
					.put('/product-merchants/' + this.singleMerchantProductData.id + '/' + this.perPage, this.singleMerchantProductData)
					.then(response => {

						if (response.status == 200) {

							this.$toastr.s("Merchant has been updated", "Success");
							
							this.pagination.current_page = 1; 
							this.productAllMerchants = response.data.data;

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
					.delete('/product-merchants/' + this.singleMerchantProductData.id + '/' + this.perPage)
					.then(response => {

						if (response.status == 200) {

							this.$toastr.s("Merchant has been deleted", "Success");
							
							this.pagination.current_page = 1; 
							this.productAllMerchants = response.data.data;

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
					this.query=emitedValue;
				}

				this.error = '';
				this.productAllMerchants = [];
				// this.pagination.current_page = 1;
				
				axios
				.get(
					"/api/search-product-merchants/" + this.product.id + '/' + this.query + "/" + this.perPage + "?page=" + this.pagination.current_page
				)
				.then(response => {
					this.productAllMerchants = response.data.all.data;
					this.pagination = response.data.all;
				})
				.catch(e => {
					this.error = e.toString();
				});

			},
			changeNumberContents() {
				
				this.pagination.current_page = 1;

				if (this.query === '') {
					this.fetchProductAllMerchants();
				}
				else {
					this.searchData();
				}

    		},
    		goProductRequisitions(object) {

				// console.log(object);
				this.$router.push({ name: 'product-requisitions', params: { productId: this.product.id , merchantId: object.merchant ? object.merchant.id : null, merchantProduct: object }});

			},
			goProductStore(object) {

				// console.log(object);
				this.$router.push({ name: 'product-stocks', params: { product: this.product, merchant: object.merchant, productMerchant: object }});

			},
			verifyUserInput() {
				
				this.validateFormInput('product_sku');
				this.validateFormInput('product_price');
				this.validateFormInput('discount');
				this.validateFormInput('product_merchant_id');
				this.validateFormInput('product_upc');
				this.validateFormInput('description');
				
				// this.validateFormInput('product_initial_quantity');
				// this.validateFormInput('product_available_quantity');
				// this.validateFormInput('product_quantity_type');

				if (this.product.has_variations) {
					
					this.validateFormInput('product_variation_id');
					// this.validateFormInput('product_variation_type');
					// this.validateFormInput('product_variation_quantity');
					this.validateFormInput('product_variation_price');
					this.validateFormInput('product_variation_upc');
					// this.validateFormInput('product_variation_total_quantity');

				}

				if (this.errors.constructor === Object && ((! this.product.has_variations && Object.keys(this.errors).length < 1) || (this.product.has_variations && Object.keys(this.errors).length < 2)) && ! this.errorInArray(this.errors.variations)) {
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

						if (this.errors.constructor === Object && Object.keys(this.errors).length < 3 && !this.errorInArray(this.errors.variations) && !this.errorInArray(this.errors.addresses)) {

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

			/*
				nextPage() {
				
					if (this.step==1) {
						
						this.validateFormInput('product_sku');
						this.validateFormInput('product_price');
						this.validateFormInput('discount');
						this.validateFormInput('product_merchant_id');

						// this.validateFormInput('product_initial_quantity');
						// this.validateFormInput('product_available_quantity');
						// this.validateFormInput('product_quantity_type');

						if (this.product.has_variations) {
							
							// this.validateFormInput('product_variation_type');
							this.validateFormInput('product_variation_id');
							// this.validateFormInput('product_variation_quantity');
							this.validateFormInput('product_variation_price');
							// this.validateFormInput('product_variation_total_quantity');

						}

						if (this.errors.constructor === Object && Object.keys(this.errors).length < 3 && !this.errorInArray(this.errors.variations)) {
							this.step += 1;
							this.submitForm = true;
						}
						else {
							this.submitForm = false;
						}

					}

				},
			*/
	
			/*
				addMoreSpace() {
					if (this.singleMerchantProductData.addresses.length < 3) {

						this.singleMerchantProductData.addresses.push({});
						this.errors.addresses.push({});

					}
				},
				removeSpace() {
						
					if (this.singleMerchantProductData.addresses.length > 1) {

						this.singleMerchantProductData.addresses.pop();
						this.errors.addresses.pop();
					
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
			setProductMerchant() {
				
				if (this.singleMerchantProductData.merchant && Object.keys(this.singleMerchantProductData.merchant).length > 0) {
					
					this.singleMerchantProductData.merchant_id = this.singleMerchantProductData.merchant.id;
					
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

				};

				if (object.has_variations && object.hasOwnProperty('variations') && object.variations.length) {

					this.errors.variations = [];

					object.variations.forEach(
						(merchantProductVariation, merchantProductVariationIndex) => {
							
							this.errors.variations.push({});

						}
					);

				}

			},
			changeProductVariation(merchantProductVariationIndex) {

				if (this.singleMerchantProductData.variations.length > merchantProductVariationIndex && this.singleMerchantProductData.variations[merchantProductVariationIndex].hasOwnProperty('variation')) {
					
					this.singleMerchantProductData.variations[merchantProductVariationIndex].product_variation_id = this.singleMerchantProductData.variations[merchantProductVariationIndex].variation.id;

					this.validateFormInput('product_variation_id');

				} else {
					
				}

			},
			setProductVariation() {
				
				if (this.product.has_variations && this.product.hasOwnProperty('variations') && this.product.variations.length) {
					
					// this.singleMerchantProductData.variation_type_id = this.singleMerchantProductData.variation_type.id;
					
					// this.singleMerchantProductData.variations = [
					// 	{}
					// ];

					// this.errors.variations = [
					// 	{}
					// ];
					
					this.$set(this.singleMerchantProductData, 'variations', [ {} ]);
					this.$set(this.errors, 'variations', [ {} ]);

					this.allVariations = this.product.variations;

				}
				else {

					this.$delete(this.singleMerchantProductData, 'variations');
					this.$delete(this.errors, 'variations');

				}
				
			},
			addMoreVariation() {
				
				if (this.singleMerchantProductData.variations.length < this.product.variations.length) {
					
					// this.$set(this.singleMerchantProductData.variations, this.singleMerchantProductData.variations.length, {});
					// this.$set(this.errors.variations, this.errors.variations.length, {});

					// Vue.set(this.singleMerchantProductData.variations, this.singleMerchantProductData.variations.length, {});
					// Vue.set(this.errors.variations, this.errors.variations.length, {});

					this.singleMerchantProductData.variations.push({});
					this.errors.variations.push({});

				}

			},
			removeVariation() {

				if (this.singleMerchantProductData.variations.length > 1) {	
					
					this.singleMerchantProductData.variations.pop();
					this.errors.variations.pop();

					this.validateFormInput('product_variation_id');

					if (! this.errorInArray(this.errors.variations)) {
						this.submitForm = true;
					}

				}

			},
			getMerchantFullName(productMerchant) {

				if (productMerchant.merchant && (productMerchant.merchant.first_name || productMerchant.merchant.last_name)) {

					return this.$options.filters.capitalize(productMerchant.merchant.first_name + ' ' + productMerchant.merchant.last_name);

				}

				return this.$options.filters.capitalize(productMerchant.merchant.user_name);

			},
		/*
			resetEmptyContainers({engaged, id, name, container_shelf_statuses, warehouse_container_id}) {

				const existingContainer = currentContainer => 
					currentContainer.id==id && currentContainer.name==name && currentContainer.warehouse_container_id==warehouse_container_id;

				if (!this.emptyContainers.some(existingContainer)) {

					this.emptyContainers.push({engaged, id, name, warehouse_container_id});
				}

				if (container_shelf_statuses) {

					if (!this.emptyShelfContainers.some(existingContainer)) {

						this.emptyShelfContainers.push({engaged, id, name, container_shelf_statuses, warehouse_container_id});
					}

					if (container_shelf_statuses.some(shelf=>shelf.hasOwnProperty('container_shelf_unit_statuses') && shelf.container_shelf_unit_statuses.length)) {

						if (!this.emptyUnitContainers.some(existingContainer)) {

							this.emptyUnitContainers.push({engaged, id, name, container_shelf_statuses, warehouse_container_id});
						}

					}
				
				}

			},
		*/
		/*
			resetEmptyShelves({engaged, id, name, warehouse_container_id, warehouse_container_status_id}) {

				console.log(engaged);
				console.log(id);
				console.log(name);
				console.log(warehouse_container_id);
				console.log(warehouse_container_status_id);

				const existingShelf = currentContainer => 
					currentContainer.id==id && currentContainer.name==name && currentContainer.warehouse_container_id==warehouse_container_id && currentContainer.warehouse_container_status_id==warehouse_container_status_id;

				if (!this.emptyShelves.some(existingShelf)) {

					this.emptyShelves.push({engaged, id, name, warehouse_container_id, warehouse_container_status_id});
				
				}

				if (container_shelf_statuses.some(shelf=>shelf.hasOwnProperty('container_shelf_unit_statuses') && shelf.container_shelf_unit_statuses.length)) {

					if (!this.emptyUnitContainers.some(existingContainer)) {

						this.emptyUnitContainers.push({engaged, id, name, container_shelf_statuses, warehouse_container_id});
					}

				}	

			},
		*/
		/*
			setAvailableShelvesAndUnits() {

				this.singleMerchantProductData.addresses.forEach(
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
		*/
		/*	
			setAvailableShelves(index) {
				// console.log('container if has been triggered');
				if (this.singleMerchantProductData.addresses[index].container && Object.keys(this.singleMerchantProductData.addresses[index].container).length > 0) {
					this.$delete(this.singleMerchantProductData.addresses[index].container, 'shelves');
					this.emptyShelves = this.singleMerchantProductData.addresses[index].container.container_shelf_statuses;
				}
			},
			setAvailableUnitShelves(index) {
				// console.log('container if has been triggered');
				if (this.singleMerchantProductData.addresses[index].container && Object.keys(this.singleMerchantProductData.addresses[index].container).length > 0) {
					this.$delete(this.singleMerchantProductData.addresses[index].container, 'shelf');
					this.emptyUnitShelves = this.singleMerchantProductData.addresses[index].container.container_shelf_statuses;
				}
			},
			setAvailableUnits(index) {
				// console.log('shelf if has been triggered');
				if (this.singleMerchantProductData.addresses[index].container && Object.keys(this.singleMerchantProductData.addresses[index].container).length > 0 && Object.keys(this.singleMerchantProductData.addresses[index].container.shelf).length > 0) {
					this.$delete(this.singleMerchantProductData.addresses[index].container.shelf, 'units');
					this.emptyUnits = this.singleMerchantProductData.addresses[index].container.shelf.container_shelf_unit_statuses;
				}
			},
			setProductSpaceType(index) {
				// resetting
				this.$delete(this.singleMerchantProductData.addresses[index], 'container');
				this.$delete(this.singleMerchantProductData.addresses[index], 'containers');

				this.errors.addresses[index] = {};

				this.resetAvailableSpaces();
		
			},
			setAvailableSpaces() {
				
				// this.emptyContainers = [ ...this.allContainers.emptyContainers ];
				// this.emptyShelfContainers = [ ...this.allContainers.emptyShelfContainers ];
				// this.emptyUnitContainers = [ ...this.allContainers.emptyUnitContainers ];

				this.emptyContainers = JSON.parse( JSON.stringify( this.allContainers.emptyContainers ) );
				this.emptyShelfContainers = JSON.parse( JSON.stringify( this.allContainers.emptyShelfContainers ) );
				this.emptyUnitContainers = JSON.parse( JSON.stringify( this.allContainers.emptyUnitContainers ) );

			},
			resetAvailableSpaces() {

				this.setAvailableSpaces();

				this.singleMerchantProductData.addresses.forEach(

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
		*/
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

                	this.$delete(this.errors, 'preview');

		      	}
		      	else{

		      		this.errors.preview = 'File should be image';

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
                	
                	this.$delete(this.errors.variations[index], 'preview');

		      	}
		      	else{

		      		this.errors.variations[index].preview = 'File should be image';

		      	}

		      	evnt.target.value = '';
		      	return;

			},
			showPreview(imagePath = 'default') {
				
				/*
				if (! imagePath) {
					return this.product.preview ? '/' + this.product.preview : '';
				}
				*/

				if (imagePath != null && imagePath.startsWith('data:')) {
					return imagePath;
				}
				else {
					return '/' + imagePath;
				}

				// return '';

			},
			validateFormInput (formInputName) {

				this.submitForm = false;

				switch(formInputName) {

					case 'product_merchant_id' :

						if (!this.singleMerchantProductData.merchant || Object.keys(this.singleMerchantProductData.merchant).length == 0) {
							this.errors.product_merchant_id = 'Merchant is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'product_merchant_id');
						}

						break;

					case 'product_upc' :

						if (this.singleMerchantProductData.upc && ! this.singleMerchantProductData.upc.match(/^\d+$/g)) {
							this.errors.product_upc = 'No alphabets, only numeric';
						}
						else if (this.singleMerchantProductData.upc && this.singleMerchantProductData.toString().length != 12) {
							this.errors.product_upc = 'Invalid code, should be 12 digits';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'product_upc');
						}

						break;

					case 'product_sku' :
 
						if (this.singleMerchantProductData.sku && ! this.singleMerchantProductData.sku.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.product_sku = 'No special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'product_sku');
						}

						break;

					case 'product_price' :

						if (this.singleMerchantProductData.selling_price && this.singleMerchantProductData.selling_price < 0 /*this.product.category && (! this.singleMerchantProductData.selling_price || this.singleMerchantProductData.selling_price < 0)*/) {
							this.errors.product_price = 'Invalid selling price';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'product_price');
						}

						break;

					case 'discount' :

						if (this.product.category && (this.singleMerchantProductData.discount > 100 || this.singleMerchantProductData.discount < 0)) {
							this.errors.discount = 'Discount should be between 0 to 100';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'discount');
						}

						break; 

					case 'description' :

						if (this.singleMerchantProductData.description && ! (/^(.|\s)*[a-zA-Z]+(.|\s)*$/).test(this.singleMerchantProductData.description)) {
							this.errors.description = 'No special character';
						}
						else if (this.singleMerchantProductData.description && this.singleMerchantProductData.description.length > 65500) {
							this.errors.description = 'Too long description (max:65500)';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'description');
						}

						break;

				/*
					case 'product_sku' :

						if (this.singleMerchantProductData.sku && !this.singleMerchantProductData.sku.match(/^[a-zA-Z0-9]*$/g)) {
							this.errors.product_sku = 'Invalid code';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'product_sku');
						}

						break;
				*/

				/*
					case 'product_initial_quantity' :

						if (!this.singleMerchantProductData.initial_quantity || this.singleMerchantProductData.initial_quantity < 1) {
							this.errors.product_initial_quantity = 'Qty is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'product_initial_quantity');
						}

						break;

					case 'product_available_quantity' :

						if (!this.singleMerchantProductData.available_quantity || this.singleMerchantProductData.available_quantity < 0 || this.singleMerchantProductData.available_quantity > this.singleMerchantProductData.initial_quantity) {
							this.errors.product_available_quantity = 'Qty is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'product_available_quantity');
						}

						break;

					case 'product_quantity_type' :

						if (!this.singleMerchantProductData.quantity_type) {
							this.errors.product_quantity_type = 'Qty type is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'product_quantity_type');
						}

						break;

					case 'product_variation_type' : 
						
						if (!this.singleMerchantProductData.has_variations) {
							this.submitForm = true;
							this.$delete(this.errors, 'product_variation_type');
						}
						else if (this.singleMerchantProductData.has_variations && (!this.singleMerchantProductData.variation_type || Object.keys(this.singleMerchantProductData.variation_type).length == 0)) {
							
							this.errors.product_variation_type = 'Variation type is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'product_variation_type');
						}

						break;
				*/

					case 'product_variation_id' :
						
						if (this.product.has_variations && this.product.hasOwnProperty('variations') && this.product.variations.length) {
														
							const noVariation = (merchantProductVariation) => ! merchantProductVariation.hasOwnProperty('variation') || ! merchantProductVariation.variation || Object.keys(merchantProductVariation.variation).length == 0;

							if (this.singleMerchantProductData.variations.some(noVariation)) {
										
								this.errors.variations[this.singleMerchantProductData.variations.findIndex(noVariation)].product_variation_id = 'Variation is required';

							}
							else {

								this.singleMerchantProductData.variations.forEach(
									
									(merchantProductVariation, index) => {
										
										/*
											if (merchantProductVariation.hasOwnProperty('product_variation_id') && this.singleMerchantProductData.variations.filter(obj => obj.variation.id === merchantProductVariation.product_variation_id).length > 0) {

												this.errors.variations[index].product_variation_id = 'Same Variation selected';

											}
											else if (this.singleMerchantProductData.variations.filter(obj => obj.variation.id === merchantProductVariation.variation.id).length > 1) {

												this.errors.variations[index].product_variation_id = 'Same Variation selected';

											}
										*/

										if (this.singleMerchantProductData.variations.filter(obj => obj.product_variation_id === merchantProductVariation.product_variation_id).length > 1) {
										
											this.errors.variations[index].product_variation_id = 'Same Variation selected';

										}
										else {
											this.$delete(this.errors.variations[index], 'product_variation_id');
										}
										
									}

								);								

							}
							
							if (!this.errorInArray(this.errors.variations)) {
								this.submitForm = true;
							}

						}
						else {
							this.submitForm = true;
							this.$delete(this.errors, 'variations');
						}

						break;

					/*
					case 'product_variation_quantity' :

						if (this.singleMerchantProductData.has_variations) {

							this.singleMerchantProductData.variations.forEach(
								(productVariation, index) => {

									if (!productVariation.hasOwnProperty('initial_quantity') || productVariation.initial_quantity < 1) {
										this.errors.variations[index].product_variation_quantity = 'Variation quantity is required';
									}
									else if (productVariation.initial_quantity < productVariation.requested_quantity) {
										this.errors.variations[index].product_variation_quantity = 'Qty cant be less than required quantity';
									}
									else {
										this.$delete(this.errors.variations[index], 'product_variation_quantity');
									}

								}
								
							);
								
							if (!this.errorInArray(this.errors.variations)) {
								this.submitForm = true;
							}
							
						}
						else {
							this.submitForm = true;
							this.errors.variations = [
								{},
							];
						}

						break;
					*/

					case 'product_variation_price' :

						if (this.product.has_variations && this.product.hasOwnProperty('variations') && this.product.variations.length) {

							this.singleMerchantProductData.variations.forEach(
								(productVariation, index) => {
									
									if (productVariation.selling_price && productVariation.selling_price < 0 /*! productVariation.selling_price || productVariation.selling_price < 0*/) {
										
										this.errors.variations[index].product_variation_price = 'Invalid selling price';

									}
									else {
										this.$delete(this.errors.variations[index], 'product_variation_price');
									}

								}
							);
							
							if (!this.errorInArray(this.errors.variations)) {
								this.submitForm = true;
							}
						}
						else {
							this.submitForm = true;
							// this.errors.variations = [];
							this.$delete(this.errors, 'variations');
						}

						break;

					case 'product_variation_upc' :

						if (this.singleMerchantProductData.product && this.singleMerchantProductData.product.has_variations && this.singleMerchantProductData.hasOwnProperty('variations') && this.singleMerchantProductData.variations.length) {

							this.singleMerchantProductData.variations.forEach(
								(productVariation, index) => {
									
									if (productVariation.upc && ! productVariation.upc.match(/^\d+$/g)) {
										
										this.errors.variations[index].product_variation_upc = 'No alphabets, only numeric';

									}
									else if (productVariation.upc && productVariation.toString().length != 12) {
										this.errors.variations[index].product_variation_upc = 'Invalid code, should be 12 digits';
									}
									else {
										this.$delete(this.errors.variations[index], 'product_variation_upc');
									}

								}
							);
							
							if (!this.errorInArray(this.errors.variations)) {
								this.submitForm = true;
							}
						}
						else {
							this.submitForm = true;
							// this.errors.variations = [];
							this.$delete(this.errors, 'variations');
						}

						break;
					

					/*
					case 'product_variation_total_quantity' :

						if (this.singleMerchantProductData.has_variations) {

							if (!this.errorInArray(this.errors.variations)) {

								let variationTotalQuantity = this.singleMerchantProductData.variations.reduce(
									(value, currentObject) => {
										return value + currentObject.initial_quantity;
									}, 
								0);

								if (variationTotalQuantity !== this.singleMerchantProductData.initial_quantity) {
									this.errors.variations[this.singleMerchantProductData.variations.length-1].product_variation_quantity = 'Total variation qty should be equal to qty';
								}
								else {
									this.$delete(this.errors.variations[this.singleMerchantProductData.variations.length-1], 'product_variation_quantity');
								}
							}

						}
						else {
							this.submitForm = true;
							this.errors.variations = [
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
									this.errors.addresses[index].product_space_type = 'Space type is required';
								}
								else if (this.singleMerchantProductData.addresses.filter((obj) => obj.type === productSpace.type).length > 1) {

									this.errors.addresses[index].product_space_type = 'Same type selected';
								}
								else {
									this.$delete(this.errors.addresses[index], 'product_space_type');
								}

							}

						);
						
						if (!this.errorInArray(this.errors.addresses)) {
							this.submitForm = true;
						}

						break;

					case 'product_containers' :

						this.singleMerchantProductData.addresses.forEach(
							
							(productSpace, index) => {

								if (productSpace.type=='containers' && (!productSpace.containers || productSpace.containers.length == 0)) {
									this.errors.addresses[index].product_containers = 'Container is required';
								}
								else{
									this.$delete(this.errors.addresses[index], 'product_containers');
								}

							}

						);

						if (!this.errorInArray(this.errors.addresses)) {
							this.submitForm = true;
						}

						break;

					case 'product_container' :

						this.singleMerchantProductData.addresses.forEach(
							
							(productSpace, index) => {

								if ((productSpace.type=='shelves' || productSpace.type=='units') && (!productSpace.container || Object.keys(productSpace.container).length==0)) {
									this.errors.addresses[index].product_container = 'Container is required';
								}
								else{
									this.$delete(this.errors.addresses[index], 'product_container');
								}

							}
						);

						if (!this.errorInArray(this.errors.addresses)) {
							this.submitForm = true;
						}

						break;

					case 'product_shelves' : 

						this.singleMerchantProductData.addresses.forEach(
							
							(productSpace, index) => {

								if (productSpace.type=='shelves' && (!productSpace.container || !productSpace.container.shelves || productSpace.container.shelves.length == 0)) {
									this.errors.addresses[index].product_shelves = 'Shelf is required';
								}
								else{
									this.$delete(this.errors.addresses[index], 'product_shelves');
								}

							}
						);

						if (!this.errorInArray(this.errors.addresses)) {
							this.submitForm = true;
						}

						break;

					case 'product_shelf' :

						this.singleMerchantProductData.addresses.forEach(
							
							(productSpace, index) => {

								if (productSpace.type=='units' && (!productSpace.container || !productSpace.container.shelf || Object.keys(productSpace.container.shelf).length==0)) {
									this.errors.addresses[index].product_shelf = 'Shelf is required';
								}
								else{
									this.$delete(this.errors.addresses[index], 'product_shelf');
								}

							}
						);

						if (!this.errorInArray(this.errors.addresses)) {
							this.submitForm = true;
						}

						break;


					case 'product_units' :

						this.singleMerchantProductData.addresses.forEach(
							
							(productSpace, index) => {

								if (productSpace.type=='units' && (!productSpace.container || !productSpace.container.shelf || !productSpace.container.shelf.units || productSpace.container.shelf.units.length == 0)) {
									this.errors.addresses[index].product_units = 'Unit is required';
								}
								else{
									this.$delete(this.errors.addresses[index], 'product_units');
								}

							}
						);

						if (!this.errorInArray(this.errors.addresses)) {
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