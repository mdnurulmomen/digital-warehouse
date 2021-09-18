
<template v-if="userHasPermissionTo('view-product-index')">

	<div class="pcoded-content">
		<breadcrumb 
			:title="'products'" 
			:message="'All our products'"
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
											  		v-if="userHasPermissionTo('view-product-index') || userHasPermissionTo('create-product')" 
											  		:query="query" 
											  		:caller-page="'products'" 
											  		:required-permission = "'product'" 
											  		:disable-add-button="allProductCategories.length==0 ? true : false" 
											  		
											  		@showContentCreateForm="showContentCreateForm" 
											  		@searchData="searchData($event)" 
											  		@fetchAllContents="fetchAllProducts"
											  	></search-and-addition-option>
											</div>
											
											<div class="col-sm-12 col-lg-12">
										  		<tab 
										  			v-show="query === ''" 
										  			:tab-names="['retail', 'bulk']" 
										  			:current-tab="'retail'" 

										  			@showRetailContents="showRetailContents" 
										  			@showBulkContents="showBulkContents" 
										  		></tab>

												<!-- 
													<table-with-soft-delete-option 
														:query="query" 
														:per-page="perPage"  
														:column-names="['name']" 
														:column-values-to-show="['name']" 
														:contents-to-show = "productsToShow" 
														:pagination = "pagination"

														@showContentDetails="showContentDetails($event)" 
														@openContentEditForm="openContentEditForm($event)" 
														@openContentDeleteForm="openContentDeleteForm($event)" 
														@openContentRestoreForm="openContentRestoreForm($event)" 
														@changeNumberContents="changeNumberContents($event)" 
														@fetchAllProducts="fetchAllProducts" 
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
																		<th>Name</th>
																		<!-- <th>SKU</th> -->
																		<th>Actions</th>
																	</tr>
																</thead>

																<tbody>
																	<tr v-for="content in productsToShow" :key="'content-' + content.id"
																	>
																		<td>{{ content.name | capitalize }}</td>
																		
																		<!-- <td>{{ content.sku }}</td> -->
																		
																		<td>
																			<button 
																				type="button" 
																				class="btn btn-grd-info btn-icon"  
																				@click="showContentDetails(content)"
																			>
																				<i class="fa fa-eye"></i>
																			</button>

																			<button 
																				type="button" 
																				class="btn btn-grd-primary btn-icon"  
																				@click="openContentEditForm(content)" 
																				v-if="userHasPermissionTo('update-product')"
																			>
																				<i class="fa fa-edit"></i>
																			</button>

																			<button 
																				type="button" 
																				class="btn btn-grd-success btn-icon"  
																				@click="goProductMerchants(content)" 
																				v-if="userHasPermissionTo('view-merchant-product-index')"
																			>
																				<i class="fa fa-users" aria-hidden="true"></i>
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
																		<!-- <th>SKU</th> -->
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
																@click="query === '' ? fetchAllProducts() : searchData()"
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
																@paginate="query === '' ? fetchAllProducts() : searchData()"
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
			:single-asset-data="singleProductData" 
			:csrf="csrf"

			@storeProduct="storeProduct($event)" 
			@updateAsset="updateAsset($event)" 
		></asset-create-or-edit-modal>
 	-->

 		<!--Create Or Edit Modal -->
		<div class="modal fade" id="product-createOrEdit-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" v-if="userHasPermissionTo('create-product') || userHasPermissionTo('update-product')">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">
							{{ createMode ? 'Store' : 'Update' }} Product
						</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
						
					<form 	
						class="form-horizontal" 
						v-on:submit.prevent="createMode ? storeProduct() : updateAsset()" 
						autocomplete="off" 
						novalidate="true" 
					>
						<input type="hidden" name="_token" :value="csrf">

						<div class="modal-body">

							<!-- <transition-group name="fade"> -->

							        <div class="form-row">
							        	<div class="form-group col-md-6">
							        		<label for="inputUsername">Product Type</label>
								        	<multiselect 
		                              			v-model="productMode" 
		                              			class="form-control p-0 is-valid" 
		                              			placeholder="Product Mode" 
		                                  		:custom-label="nameWithCapitalized" 
		                                  		:options="['bulk product', 'retail product']" 
		                                  		:required="true" 
		                                  		:allow-empty="false" 
		                                  		@input="setProductMode" 
		                                  		:disabled="singleProductData.product_immutability" 
		                              		>
		                                	</multiselect>
							        	</div>

							        	<div class="form-group col-md-6">
											<label for="inputUsername">Product Category</label>
											<multiselect 
		                              			v-model="singleProductData.category" 
		                              			class="form-control p-0" 
		                              			placeholder="Product Category" 
		                              			:class="!errors.product.product_category ? 'is-valid' : 'is-invalid'" 
		                                  		label="name" 
		                                  		track-by="id" 
		                                  		:custom-label="objectNameWithCapitalized" 
		                                  		:options="allProductCategories" 
		                                  		:required="true" 
		                                  		:allow-empty="false"
		                                  		selectLabel = "Press/Click"
		                                  		deselect-label="Can't remove single value" 
		                                  		:disabled="productMode=='bulk product'" 
		                                  		@input="setProductCategory"
		                              		>
		                                	</multiselect>
		                                	<div class="invalid-feedback">
										    	{{ errors.product.product_category }}
										    </div>
										</div>
							        </div>
							        	
									<div class="form-row">
										<div class="form-group col-md-6">
											<!-- Product Name with Model, ex : Samsung Guru Music -->
											<label for="inputFirstName">Name</label>
											<input type="text" 
												class="form-control" 
												v-model="singleProductData.name" 
												placeholder="Name should be unique" 
												:class="!errors.product.product_name ? 'is-valid' : 'is-invalid'" 
												@change="validateFormInput('product_name')" 
												required="true" 
											>

											<div class="invalid-feedback">
									        	{{ errors.product.product_name }}
									  		</div>
										</div>

										<div class="form-group col-md-6">
											<label for="inputFirstName">Qty Type</label>
											<input type="text" 
												class="form-control" 
												v-model="singleProductData.quantity_type" 
												:placeholder="productMode=='bulk product' ? 'Box / Cartoon' : 'Kg / Meter'" 
												:class="! errors.product.product_quantity_type ? 'is-valid' : 'is-invalid'" 
												@change="validateFormInput('product_quantity_type')" 
												required="true" 
											>

											<div class="invalid-feedback">
									        	{{ errors.product.product_quantity_type }}
									  		</div>
										</div>
										
										<!-- 
										<div class="form-group col-md-6">
											<label for="inputUsername">Merchant</label>
											<multiselect 
		                              			v-model="singleProductData.merchant"
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
		                                  		:class="!errors.product.product_merchant_id  ? 'is-valid' : 'is-invalid'"
		                                  		@close="validateFormInput('product_merchant_id')" 
		                                  		@input="setProductMerchant"
		                              		>
		                                	</multiselect>

		                                	<div class="invalid-feedback">
										    	{{ errors.product.product_merchant_id }}
										    </div>
										</div>
										 -->
									</div>

									<div class="form-row">
										<!-- 
										<div class="form-group col-md-6">
											<label for="inputFirstName">SKU/Barcode</label>
											<input type="text" 
												class="form-control" 
												v-model="singleProductData.sku" 
												placeholder="Unique code" 
												:class="!errors.product.product_sku  ? 'is-valid' : 'is-invalid'" 
												@change="validateFormInput('product_sku')" 
												required="true" 
											>

											<div class="invalid-feedback">
									        	{{ errors.product.product_sku }}
									  		</div>
										</div>
 										-->
									</div>

									<div class="form-row">
										<!-- 
										<div class="form-group col-md-6">
											<label for="inputFirstName">Price</label>
											<input type="number" 
												class="form-control" 
												v-model.number="singleProductData.price" 
												placeholder="Product price" 
												:readonly="productMode=='bulk product'" 
												:class="!errors.product.product_price ? 'is-valid' : 'is-invalid'"
												@change="validateFormInput('product_price')" 
												required="true" 
											>

											<div class="invalid-feedback">
									        	{{ errors.product.product_price }}
									  		</div>
										</div>
 										-->
									</div>

									<div class="form-row">
										<!-- 
										<div class="form-group col-md-6">
											<label for="inputFirstName">Qty</label>
											<input type="number" 
												class="form-control" 
												v-model.number="singleProductData.initial_quantity" 
												placeholder="Product initial qty" 
												:class="!errors.product.product_initial_quantity  ? 'is-valid' : 'is-invalid'" 
												:readonly="!createMode" 
												@change="validateFormInput('product_initial_quantity')" 
												required="true" 
											>

											<div class="invalid-feedback">
									        	{{ errors.product.product_initial_quantity }}
									  		</div>
										</div>
										<div class="form-group col-md-4">
											<label for="inputFirstName">Available quantity</label>
											<input type="number" 
												class="form-control" 
												v-model.number="singleProductData.available_quantity" 
												placeholder="Product available quantity" 
												:class="!errors.product.product_available_quantity  ? 'is-valid' : 'is-invalid'" 
												@change="validateFormInput('product_available_quantity')" 
												required="true" 
											>

											<div class="invalid-feedback">
									        	{{ errors.product.product_available_quantity }}
									  		</div>
										</div>
										-->
									</div>

									<div class="form-row">
										<div class="col-md-12">
											<div class="card">
										    	<div class="card-body">
										    		<div class="form-row d-flex align-items-center text-center">
														<div class="form-group col-md-6">
															<img class="img-fluid" 
																:src="singleProductData.preview || ''"
																alt="Product Picture" 
															>
														</div>
														<div class="form-group col-md-6">
															<div class="custom-file">
															    <input type="file" 
															    	class="form-control custom-file-input" 
																	:class="!errors.product.preview  ? 'is-valid' : 'is-invalid'" 
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

									<div class="form-control form-group">
										<div class="form-row mt-2">
											<div class="col-md-12 text-center">
												<toggle-button 
													v-model="singleProductData.has_serials" 
													:width=200 
													:sync="true"
													:color="{checked: 'orange', unchecked: 'green'}"
													:labels="{checked: 'Has Serial', unchecked: 'No Serial'}" 
													:disabled="productMode=='bulk product' || singleProductData.product_immutability" 
												/>
											</div>
										</div>
									</div>

									<div class="form-control form-group">
										<div class="form-row mt-3">
											<div class="form-group col-md-12 text-center">
												<toggle-button 
													v-model="singleProductData.has_variations" 
													:width=200 
													:sync="true"
													:color="{checked: 'green', unchecked: 'blue'}"
													:labels="{checked: 'Has Variation', unchecked: 'No Variation'}" 
													:disabled="productMode=='bulk product' || singleProductData.product_immutability || allVariationTypes.length==0" 
													@change="resetProductVariations()"
												/>
											</div>
										</div>
										
										<div class="form-row" v-show="singleProductData.has_variations">
											<div class="form-group col-md-4">
												<label for="inputFirstName">Variation Type</label>
												<multiselect 
			                              			v-model="singleProductData.variation_type"
			                              			placeholder="Choose Type" 
			                                  		label="name" 
			                                  		track-by="id" 
			                                  		:custom-label="objectNameWithCapitalized" 
			                                  		:options="allVariationTypes" 
			                                  		:required="true" 
			                                  		:allow-empty="false"
			                                  		selectLabel = "Press/Click"
			                                  		deselect-label="Can't remove single value" 
			                                  		class="form-control p-0" 
			                                  		:class="!errors.product.product_variation_type  ? 'is-valid' : 'is-invalid'" 
			                                  		:disabled="singleProductData.product_immutability"
			                                  		@close="validateFormInput('product_variation_type')" 
			                                  		@input="setProductVariation"
			                              		>
			                                	</multiselect>
			                                	<div class="invalid-feedback">
											    	{{ errors.product.product_variation_type }}
											    </div>
											</div>

											<div 
												class="form-group col-md-8" 
												v-if="singleProductData.variations && singleProductData.variations.length"
											>
												<div 
													class="card" 
													v-for="(productVariation, index) in singleProductData.variations" 
													:key="'product-variation-index' + index + 'A'"
												>
													<div class="card-body">
														<div 
															class="form-row" 
															v-if="singleProductData.variations.length == errors.product.variations.length"
														>
															<div class="form-group col-md-12">
																<label for="inputFirstName">Variation</label>
																
																<!-- 
																<multiselect 
							                              			v-model="productVariation.variation"
							                              			placeholder="Select Variation" 
							                                  		label="name" 
							                                  		track-by="id" 
							                                  		:custom-label="objectNameWithCapitalized" 
							                                  		:options="availableVariations" 
							                                  		:required="true" 
							                                  		:allow-empty="false"
							                                  		selectLabel = "Press/Click"
							                                  		deselect-label="Can't remove single value" 
							                                  		:disabled="productVariation.variation_immutability" 
							                                  		class="form-control p-0" 
							                                  		:class="!errors.product.variations[index].product_variation_id ? 'is-valid' : 'is-invalid'"
							                                  		@close="validateFormInput('product_variation_id')" 
							                              		>
							                                	</multiselect>
 																-->

 																<treeselect
							                                		v-model="productVariation.variation"
																	:options="availableVariations" 
																	:show-count="true" 
																	:normalizer="treeSelectCustomFunction" 
																	:valueFormat="'object'" 
																	:required="true" 
																	:disabled="productVariation.variation_immutability" 
																	@select="validateFormInput('variation_parent_id')"
																	@close="validateFormInput('product_variation_id')"  
																	class="form-control p-0" 
							                                  		:class="!errors.product.variations[index].product_variation_id ? 'is-valid' : 'is-invalid'"
																	placeholder="Select Variation" 
																/>

							                                	<div class="invalid-feedback">
															    	{{ errors.product.variations[index].product_variation_id }}
															    </div>
															</div>

															<!-- 
																<div 
																	class="form-group col-md-12" 
																	v-if="productVariation.variation && productVariation.variation.hasOwnProperty('variation_childs') && productVariation.variation.variation_childs.length"
																>
																	<div class="form-row ml-3 mr-3">
																		<div class="form-group col-md-12">
																			<label for="inputFirstName">Sub-Variation</label>
																			<multiselect 
										                              			v-model="productVariation.variation.sub_variation"
										                              			placeholder="Select Sub-Variation" 
										                                  		label="name" 
										                                  		track-by="id" 
										                                  		:custom-label="objectNameWithCapitalized" 
										                                  		:options="productVariation.variation.variation_childs" 
										                                  		selectLabel = "Press to select"
										                                  		deselect-label="Press to remove" 
										                                  		:disabled="productVariation.variation_immutability" 
										                                  		class="form-control is-valid p-0" 
										                              		>
										                                	</multiselect>
																		</div>
																	</div> 
																</div>
															 -->

															<div class="form-group col-md-12">
													    		<div class="d-flex form-row text-center">
																	<div class="col-md-8">
																		<img class="img-fluid" 
																			:src="productVariation.preview || ''"
																			alt="Variation Picture" 
																		>
																	</div>
																	<div class="col-md-4 align-self-center">
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

															<!-- 
															<div 
																class="form-group col-md-3"
																v-if="singleProductData.variations[index] && errors.product.variations[index]"
															>
																<label for="inputFirstName">Qty</label>
																<input type="number" 
																	class="form-control" 
																	v-model.number="singleProductData.variations[index].initial_quantity" 
																	:min="singleProductData.variations[index].requested_quantity"
																	:max="singleProductData.initial_quantity" 
																	placeholder="Product Qty" 
																	:class="!errors.product.variations[index].product_variation_quantity ? 'is-valid' : 'is-invalid'" 
																	@change="validateFormInput('product_variation_quantity')" 
																	required="true" 
																>

																<div class="invalid-feedback">
														        	{{ errors.product.variations[index].product_variation_quantity }}
														  		</div>
															</div>
															-->
															<!-- 
															<div 
																class="form-group col-md-4"
																v-if="singleProductData.variations[index] && errors.product.variations[index]"
															>
																<label for="inputFirstName">Price</label>
																<input type="number" 
																	class="form-control" 
																	v-model.number="singleProductData.variations[index].price" 
																	placeholder="Product Price" 
																	:class="!errors.product.variations[index].product_variation_price ? 'is-valid' : 'is-invalid'" 
																	@change="validateFormInput('product_variation_price')" 
																	required="true" 
																>

																<div class="invalid-feedback">
														        	{{ errors.product.variations[index].product_variation_price }}
														  		</div>
															</div>

															<div 
																class="form-group col-md-4"
																v-if="singleProductData.variations[index] && errors.product.variations[index]"
															>
																<label for="inputFirstName">SKU</label>
																<input type="text" 
																	class="form-control is-valid" 
																	v-model="singleProductData.variations[index].sku" 
																	placeholder="Unique SKU" 
																>
															</div>
															 -->									
														</div>
													</div>
												</div>

												<div class="form-row">
													<div class="form-group col-md-6">
														<button 
															type="button" 
															class="btn waves-effect waves-light hor-grd btn-grd-primary btn-sm btn-block" 
															@click="addMoreVariation()"
														>
															Add Variation
														</button>
													</div>
													<div class="form-group col-md-6">
														<button 
															type="button" 
															class="btn waves-effect waves-light hor-grd btn-grd-info btn-sm btn-block" 
															:disabled="singleProductData.variations[singleProductData.variations.length-1].variation_immutability || singleProductData.variations.length < 3"
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
										<div class="col-sm-12 text-right" v-show="!submitForm">
											<span class="text-danger small mb-1">
										  		Please input required fields
										  	</span>
										</div>

										<div class="col-sm-12">
						                  	<button type="button" class="btn btn-secondary float-left" data-dismiss="modal">
						                  		Close
						                  	</button>
											<button type="submit" class="btn btn-primary float-right" :disabled="!submitForm">
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
									          	<button type="button" class="btn btn-outline-secondary btn-sm btn-round" v-on:click="nextPage">
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
										v-for="(productSpace, index) in singleProductData.addresses" 
										:key="'product-space-' + index"
									>
										<div 
											class="card"
											v-if="singleProductData.addresses[index] && errors.product.addresses[index]"
										>
											<div class="card-body">

												<div class="form-row ml-5 mr-5">
													<div class="form-group col-md-12 text-center">
														<label for="inputFirstName">
															Required Space Type {{ index + 1 }}
														</label>
														<multiselect 
					                              			v-model="singleProductData.addresses[index].type"
					                                  		:options="['containers', 'shelves', 'units']" 
					                                  		:custom-label="nameWithCapitalized" 
					                                  		:required="true" 
					                                  		:allow-empty="false"
					                              			placeholder="Containers / Shelves / Units" 
					                                  		:class="!errors.product.addresses[index].product_space_type  ? 'is-valid' : 'is-invalid'" 
					                                  		:disabled="singleProductData.addresses.length > (index+1)" 
					                                  		@input="setProductSpaceType(index)" 
					                                  		@close="validateFormInput('product_space_type')"
					                              		>
					                                	</multiselect>
					                                	<div 
						                                	class="invalid-feedback" 
						                                	style="display: block;" 
						                                	v-show="errors.product.addresses[index].product_space_type"
					                                	>
													    	{{ errors.product.addresses[index].product_space_type }}
													    </div>
													</div>
												</div>

												<div 
													class="form-row" 
													v-show="singleProductData.addresses[index].type=='containers'"
												>
													<div class="form-group col-md-12">
														<label for="inputFirstName">Select Containers</label>
														<multiselect 
					                              			v-model="singleProductData.addresses[index].containers"
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
					                                  		:class="!errors.product.addresses[index].product_containers  ? 'is-valid' : 'is-invalid'" 
					                                  		:disabled="singleProductData.addresses.length > (index+1)"
					                                  		@close="validateFormInput('product_containers')" 
					                              		>
					                                	</multiselect>
					                                	<div 
						                                	class="invalid-feedback" 
						                                	style="display: block;" 
						                                	v-show="errors.product.addresses[index].product_containers"
					                                	>
													    	{{ errors.product.addresses[index].product_containers }}
													    </div>
													</div>
												</div>

												<div 
													class="form-row" 
													v-show="singleProductData.addresses[index].type=='shelves'"
												>
													<div class="form-group col-md-6">
														<label for="inputFirstName">Select Parent Container</label>
														<multiselect 
					                              			v-model="singleProductData.addresses[index].container"
					                              			placeholder="Parent Container" 
					                              			label="name" 
					                                  		track-by="id" 
					                                  		:options="emptyShelfContainers" 
					                                  		:required="true" 
					                                  		:allow-empty="false"
					                                  		:class="!errors.product.addresses[index].product_container ? 'is-valid' : 'is-invalid'" 
					                                  		:disabled="singleProductData.addresses.length > (index+1)"
					                                  		@input="setAvailableShelves(index)"
					                                  		@close="validateFormInput('product_container')" 
					                              		>
					                                	</multiselect>
					                                	<div 
						                                	class="invalid-feedback" 
						                                	style="display: block;" 
						                                	v-show="errors.product.addresses[index].product_container"
					                                	>
													    	{{ errors.product.addresses[index].product_container }}
													    </div>
													</div>

													<div 
														class="form-group col-md-6" 
														v-if="singleProductData.addresses[index].container"
													>
														<label for="inputFirstName">Select Shelves</label>
														<multiselect 
					                              			v-model="singleProductData.addresses[index].container.shelves"
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
					                                  		:class="!errors.product.addresses[index].product_shelves ? 'is-valid' : 'is-invalid'" 
					                                  		:disabled="singleProductData.addresses.length > (index+1)"
					                                  		@close="validateFormInput('product_shelves')" 
					                              		>
					                                	</multiselect>
					                                	<div 
						                                	class="invalid-feedback" 
						                                	style="display: block;" 
						                                	v-show="errors.product.addresses[index].product_shelves"
					                                	>
													    	{{ errors.product.addresses[index].product_shelves }}
													    </div>
													</div>
												</div>

												<div class="form-row" v-show="singleProductData.addresses[index].type=='units'">
													<div class="form-group col-md-4">
														<label for="inputFirstName">Select Parent Container</label>
														<multiselect 
					                              			v-model="singleProductData.addresses[index].container"
					                              			placeholder="Parent Container" 
					                              			label="name" 
					                                  		track-by="id" 
					                                  		:options="emptyUnitContainers" 
					                                  		:required="true" 
					                                  		:allow-empty="false"
					                                  		:class="!errors.product.addresses[index].product_container  ? 'is-valid' : 'is-invalid'" 
					                                  		:disabled="singleProductData.addresses.length > (index+1)"
					                                  		@input="setAvailableUnitShelves(index)" 
					                                  		@close="validateFormInput('product_container')" 
					                              		>
					                                	</multiselect>
					                                	<div 
						                                	class="invalid-feedback" 
						                                	style="display: block;" 
						                                	v-show="errors.product.addresses[index].product_container"
					                                	>
													    	{{ errors.product.addresses[index].product_container }}
													    </div>
													</div>

													<div 
														class="form-group col-md-4" 
														v-if="singleProductData.addresses[index].container"
													>
														<label for="inputFirstName">Select Parent Shelf</label>
														<multiselect 
					                              			v-model="singleProductData.addresses[index].container.shelf"
					                              			placeholder="Parent Shelf" 
					                              			label="name" 
					                                  		track-by="id" 
					                                  		:options="emptyUnitShelves" 
					                                  		:required="true" 
					                                  		:allow-empty="false"
					                                  		:class="!errors.product.addresses[index].product_shelf  ? 'is-valid' : 'is-invalid'" 
					                                  		:disabled="singleProductData.addresses.length > (index+1)"
					                                  		@input="setAvailableUnits(index)" 
					                                  		@close="validateFormInput('product_shelf')" 
					                              		>
					                                	</multiselect>
					                                	<div 
						                                	class="invalid-feedback" 
						                                	style="display: block;" 
						                                	v-show="errors.product.addresses[index].product_shelf"
					                                	>
													    	{{ errors.product.addresses[index].product_shelf }}
													    </div>
													</div>

													<div 
														class="form-group col-md-4" 
														v-if="singleProductData.addresses[index].container && singleProductData.addresses[index].container.shelf"
													>
														<label for="inputFirstName">Select Units</label>
														<multiselect 
					                              			v-model="singleProductData.addresses[index].container.shelf.units"
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
					                                  		:class="!errors.product.addresses[index].product_units ? 'is-valid' : 'is-invalid'" 
					                                  		:disabled="singleProductData.addresses.length > (index+1)"
					                                  		@close="validateFormInput('product_units')" 
					                              		>
					                                	</multiselect>
					                                	<div 
						                                	class="invalid-feedback" 
						                                	style="display: block;" 
						                                	v-show="errors.product.addresses[index].product_units"
					                                	>
													    	{{ errors.product.addresses[index].product_units }}
													    </div>
													</div>
												</div>

											</div>
										</div>
									</div>

									<div 
										class="col-md-12 text-center" 
										v-show="!singleProductData.addresses.length"
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
												<button type="submit" class="btn btn-primary float-right" :disabled="!submitForm">
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
		<delete-confirmation-modal 
			:csrf="csrf" 
			:submit-method-name="'deleteAsset'" 
			:content-to-delete="singleProductData"
			:restoration-message="'But once you think, you can restore this item !'" 
			
			@deleteAsset="deleteAsset($event)" 
		></delete-confirmation-modal>

		<restore-confirmation-modal 
			:csrf="csrf" 
			:submit-method-name="'restoreAsset'" 
			:content-to-restore="singleProductData"
			:restoration-message="'This will restore all related items !'" 

			@restoreAsset="restoreAsset($event)" 
		></restore-confirmation-modal>
 	-->

 		<!-- Modal -->
		<div class="modal fade" id="product-view-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">{{ singleProductData.name | capitalize }} Details</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="modal-body">
						<div class="form-row d-flex">
							<div class="col-md-4 align-self-center text-center">
								<img :src="singleProductData.preview" class="img-fluid" alt="Product Preview" width="150px">
							</div>

							<div class="col-md-8">
								<div class="form-row">
									<label class="col-sm-4 col-form-label font-weight-bold text-right">
										Type :
									</label>
									<label class="col-sm-8 col-form-label">
										{{ singleProductData.category ? singleProductData.category.name : 'Bulk Product' | capitalize }}
									</label>
								</div>

								<div class="form-row">
									<label class="col-sm-4 col-form-label font-weight-bold text-right">
										Name :
									</label>
									<label class="col-sm-8 col-form-label">
										{{ singleProductData.name | capitalize }}
									</label>
								</div>

								<div class="form-row">
									<label class="col-sm-4 col-form-label font-weight-bold text-right">
										Quantity Type :
									</label>
									<label class="col-sm-8 col-form-label">
										{{ singleProductData.quantity_type | capitalize }}
									</label>
								</div>

								<div class="form-row">
									<label class="col-sm-4 col-form-label font-weight-bold text-right">Has Serial :</label>
									<label class="col-sm-6 form-control-plaintext">
										<span :class="[singleProductData.has_serials ? 'badge-info' : 'badge-primary', 'badge']">{{ singleProductData.has_serials ? 'Available' : 'NA' }}</span>
									</label>
								</div>

								<div class="form-row">
									<label class="col-sm-4 col-form-label font-weight-bold text-right">Has Variation :</label>
									<label class="col-sm-6 form-control-plaintext">
										<span :class="[singleProductData.has_variations ? 'badge-info' : 'badge-primary', 'badge']">{{ singleProductData.has_variations ? 'Available' : 'NA' }}</span>
									</label>
								</div>

								<div class="form-row" v-if="singleProductData.has_variations && singleProductData.variations.length">
									<label class="col-sm-4 col-form-label font-weight-bold text-right">
										Variations :
									</label>
									<div class="col-sm-6 col-form-label">
										<div 
											class="form-group text-center" 
											v-for="(productVariation, variationIndex) in singleProductData.variations" 
											:key="'product-variation-' + variationIndex"
										>
											<img class="img-fluid" 
												:src="productVariation.preview || ''"
												:alt="productVariation.variation ? productVariation.variation.name : 'NA' + 'Picture'" 
												width="100px"
											>

											<p>
												
												{{ productVariation.variation ? productVariation.variation.name : 'NA' | capitalize }}
												
												<!-- 
													<span v-show="productVariation.variation && productVariation.variation.sub_variation">
														(
															{{ (productVariation.variation && productVariation.variation.sub_variation) ? productVariation.variation.sub_variation.name : 'NA'  }}
														)
													</span>
												-->

											</p>


											<!-- <span v-show="singleProductData.variations.length > variationIndex+1">, </span> -->
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="modal-footer">
						<button type="button" class="btn btn-secondary btn-sm btn-block" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>

	</div>

</template>

<script type="text/javascript">

	import axios from 'axios';
	import Multiselect from 'vue-multiselect';
	import Treeselect from '@riophae/vue-treeselect'
	// import the styles
  	import '@riophae/vue-treeselect/dist/vue-treeselect.css'

    let singleProductData = {
    	// name : null,
    	// description : null,
    	// sku : null,
    	// price : null,
    	// initial_quantity : null,
    	// available_quantity : null,
    	// quantity_type : null,
    	// has_variations : null,
    	
    	preview : null,
    	variations : [],
		
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
	    	Treeselect,
			multiselect : Multiselect,
		},

	    data() {

	        return {

	        	// step : 1,
	        	query : '',
	        	error : '',
    			perPage : 10,
	        	loading : false,
	        	
	        	currentTab : 'retail',
	        	productMode : 'retail product',

	        	createMode : true,
	        	submitForm : true,

	        	allMerchants : [],
	        	allVariationTypes : [],
	        	availableVariations : [],
	        	allProductCategories : [],

	        	productsToShow : [],
	        	allFetchedProducts : [],

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

	        	singleProductData : singleProductData,

	        	errors : {
					product : {
						variations : [],
						
						/*
						addresses : [
							{}
						],
						*/
					},
				},

	            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

	        }

		},
		
		created(){

			this.fetchAllProducts();

			// if (this.userHasPermissionTo('view-merchant-index')) {

			// 	this.fetchAllMerchants();

			// }

			// this.fetchAllContainers();
			
			if (this.userHasPermissionTo('view-asset-index')) {

				this.fetchAllVariationTypes();
				this.fetchProductAllCategories();

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
		
		methods : {

			fetchAllProducts() {

				this.query = '';
				this.error = '';
				this.loading = true;
				this.allFetchedProducts = [];
				
				axios
					.get('/api/products/' + this.perPage + "?page=" + this.pagination.current_page)
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
			fetchProductAllCategories() {

				if (! this.userHasPermissionTo('view-asset-index')) {

					this.error = 'You do not have permission to view product-categories';
					return;

				}
				
				this.query = '';
				this.error = '';
				this.loading = true;
				this.allProductCategories = [];
				
				axios
					.get('/api/product-categories/')
					.then(response => {
						if (response.status == 200) {
							this.allProductCategories = response.data;
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
			*/
			fetchAllVariationTypes() {

				if (! this.userHasPermissionTo('view-asset-index')) {

					this.error = 'You do not have permission to view variation types';
					return;

				}
				
				this.query = '';
				this.error = '';
				this.loading = true;
				this.allVariationTypes = [];
				
				axios
					.get('/api/variation-types/')
					.then(response => {
						if (response.status == 200) {
							// this.allVariationTypes = response.data;
							this.allVariationTypes = response.data.data.filter(variationType=>variationType.variations.length > 1) ?? [];
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
			searchData(emitedValue=false) {

				if (emitedValue) {
					this.query=emitedValue;
				}

				this.error = '';
				this.allFetchedProducts = [];
				this.pagination.current_page = 1;
				
				axios
				.get(
					"/api/search-products/" + this.query + "/" + this.perPage + "?page=" + this.pagination.current_page
				)
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

				if (this.query === '') {
					this.fetchAllProducts();
				}
				else {
					this.searchData();
				}
    		},
    		showContentDetails(object) {		
				// this.singleProductData = { ...object };
				// this.singleProductData = Object.assign({}, this.singleProductData, object);
				this.singleProductData = JSON.parse(JSON.stringify(object));
				$('#product-view-modal').modal('show');
			},
			showContentCreateForm() {
				// this.step = 1;
				this.createMode = true;
	        	this.submitForm = true;
	        	
				this.singleProductData = {
					
					preview : null,
					
					variations : [],
					
					/*
						addresses : [
							{},
						],
					*/
				};

				this.errors = {
					product : {
						variations : [],
						/*
						addresses : [
							{},
						],
						*/
					},
				};

				$('#product-createOrEdit-modal').modal('show');
			},
			openContentEditForm(object) {
				
				// console.log(object);

				// this.step = 1;
				this.createMode = false;
	        	this.submitForm = true;
				
				this.errors = {
					product : {
						variations : [],
						/* addresses : [], */
					},
				};

				/*
				object.addresses.forEach(
					(productAddress, index) => {
						this.errors.product.addresses.push({});
					}
				);
				*/

				if (object.hasOwnProperty('category') && object.category) {

					this.productMode = 'retail product';

					if (object.has_variations) {
						
						object.variations.forEach(
							(productVariation, index) => {
								this.errors.product.variations.push({});
							}
						);
						
						// this.availableVariations = object.variation_type.variations ?? [];
						
						this.availableVariations = this.allVariationTypes.find(
							(variationType) => variationType.id == object.variation_type.id && variationType.name == object.variation_type.name
						).variations;
					
					}

				}
				else {

					this.productMode = 'bulk product';

				}

				this.singleProductData = JSON.parse(JSON.stringify(object));

				/*this.setAvailableShelvesAndUnits();*/

				$('#product-createOrEdit-modal').modal('show');
			},
			storeProduct() {
				
				if (!this.verifyUserInput()) {
					this.submitForm = false;
					return;
				}

				axios
					.post('/products/' + this.perPage, this.singleProductData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("New product has been stored", "Success");
							this.allFetchedProducts = response.data;
							this.query !== '' ? this.searchData() : this.showSelectedTabProducts();
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
						// this.fetchAllContainers();
					});

			},
			updateAsset() {
				
				if (!this.verifyUserInput()) {
					this.submitForm = false;
					return;
				}

				axios
					.put('/products/' + this.singleProductData.id + '/' + this.perPage, this.singleProductData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Product has been updated", "Success");
							this.allFetchedProducts = response.data;
							this.query !== '' ? this.searchData() : this.showSelectedTabProducts();
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
						// this.fetchAllContainers();
					});

			},
			/*
				goProductStore(object) {

					// console.log(object);
					this.$router.push({ name: 'product-stocks', params: { product: object, productName: object.name }});

				},
			*/
			goProductMerchants(object) {

				// console.log(object);
				this.$router.push({ name: 'product-merchants', params: { product: object, productName: object.name.replace(/ /g,"-") }});

			},
			showSelectedTabProducts() {
				
				if (this.currentTab=='retail') {
					this.productsToShow = this.allFetchedProducts.retail.data;
					this.pagination = this.allFetchedProducts.retail;
				}
				else {
					this.productsToShow = this.allFetchedProducts.bulk.data;
					this.pagination = this.allFetchedProducts.bulk;
				}

			},
			verifyUserInput() {
				
				// this.validateFormInput('product_merchant_id');
				this.validateFormInput('product_name');
				this.validateFormInput('product_category');
				// this.validateFormInput('product_sku');
				// this.validateFormInput('product_price');
				// this.validateFormInput('product_initial_quantity');
				// this.validateFormInput('product_available_quantity');
				this.validateFormInput('product_quantity_type');

				if (this.singleProductData.has_variations) {
					
					this.validateFormInput('product_variation_type');
					this.validateFormInput('product_variation_id');
					// this.validateFormInput('product_variation_quantity');
					// this.validateFormInput('product_variation_price');
					// this.validateFormInput('product_variation_total_quantity');

				}

				if (this.errors.product.constructor === Object && Object.keys(this.errors.product).length < 2 && !this.errorInArray(this.errors.product.variations)) {
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
					
					// this.validateFormInput('product_merchant_id');
					this.validateFormInput('product_name');
					this.validateFormInput('product_category');
					// this.validateFormInput('product_sku');
					// this.validateFormInput('product_price');
					// this.validateFormInput('product_initial_quantity');
					// this.validateFormInput('product_available_quantity');
					this.validateFormInput('product_quantity_type');

					if (this.singleProductData.has_variations) {
						
						this.validateFormInput('product_variation_type');
						this.validateFormInput('product_variation_id');
						// this.validateFormInput('product_variation_quantity');
						// this.validateFormInput('product_variation_price');
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
			showRetailContents() {
				this.currentTab = 'retail';
				this.showSelectedTabProducts();
			},
			showBulkContents() {
				this.currentTab = 'bulk';
				this.showSelectedTabProducts();
			},
			addMoreVariation() {
				this.singleProductData.variations.push({});
				this.errors.product.variations.push({});
			},
			removeVariation() {
				if (this.singleProductData.variations.length > 2) {	
					this.singleProductData.variations.pop();
					this.errors.product.variations.pop();

					if (!this.errorInArray(this.errors.product.variations)) {
						this.submitForm = true;
					}

				}
			},
		/*
			addMoreSpace() {
				if (this.singleProductData.addresses.length < 3) {

					this.singleProductData.addresses.push({});
					this.errors.product.addresses.push({});

				}
			},
			removeSpace() {
					
				if (this.singleProductData.addresses.length > 1) {

					this.singleProductData.addresses.pop();
					this.errors.product.addresses.pop();
				
				}
				
			},
		*/
			objectNameWithCapitalized ({ name, user_name }) {
		      	if (name) {
				    name = name.toString()
				    return name.charAt(0).toUpperCase() + name.slice(1)
		      	}
		      	else if (user_name) {
		      		user_name = user_name.toString()
				    return user_name.charAt(0).toUpperCase() + user_name.slice(1)
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
		    treeSelectCustomFunction(node) {
				return {
					id: node.id,
					label: node.name,
					children: node.childs,
				}
			},
			setProductCategory() {
				// console.log('category has been triggered');
				if (this.singleProductData.category && Object.keys(this.singleProductData.category).length > 0) {
					this.singleProductData.product_category_id = this.singleProductData.category.id;
				}
			},
			setProductMerchant() {
				// console.log('merchant has been triggered');
				if (this.singleProductData.merchant && Object.keys(this.singleProductData.merchant).length > 0) {
					this.singleProductData.merchant_id = this.singleProductData.merchant.id;
				}
			},
			resetProductVariations() {

				if (this.singleProductData.has_variations) {

					this.singleProductData.variations = [
						{}, {}
					];
					this.errors.product.variations = [
						{}, {}
					];

				}
				else {

					this.singleProductData.variations = [];
					this.errors.product.variations = [];

					this.$delete(this.errors.product, 'product_variation_type');

				}

				this.setProductVariation();

			},
			setProductVariation() {
				if (this.singleProductData.has_variations && this.singleProductData.variation_type && Object.keys(this.singleProductData.variation_type).length > 0) {
					// this.singleProductData.variation_type_id = this.singleProductData.variation_type.id;
					// this.singleProductData.variations = [
					// 	{}, {}
					// ];
					this.availableVariations = this.singleProductData.variation_type.variations;
				}
				else {
					// this.singleProductData.variations = [];
					this.availableVariations = [];
				}
			},
			setProductMode() {
				if (this.productMode=='bulk product') {
					this.singleProductData.has_variations = false;
					this.$delete(this.singleProductData, 'category');
					this.$delete(this.singleProductData, 'product_category_id');
				}
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

				this.singleProductData.addresses.forEach(
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
				if (this.singleProductData.addresses[index].container && Object.keys(this.singleProductData.addresses[index].container).length > 0) {
					this.$delete(this.singleProductData.addresses[index].container, 'shelves');
					this.emptyShelves = this.singleProductData.addresses[index].container.container_shelf_statuses;
				}
			},
			setAvailableUnitShelves(index) {
				// console.log('container if has been triggered');
				if (this.singleProductData.addresses[index].container && Object.keys(this.singleProductData.addresses[index].container).length > 0) {
					this.$delete(this.singleProductData.addresses[index].container, 'shelf');
					this.emptyUnitShelves = this.singleProductData.addresses[index].container.container_shelf_statuses;
				}
			},
			setAvailableUnits(index) {
				// console.log('shelf if has been triggered');
				if (this.singleProductData.addresses[index].container && Object.keys(this.singleProductData.addresses[index].container).length > 0 && Object.keys(this.singleProductData.addresses[index].container.shelf).length > 0) {
					this.$delete(this.singleProductData.addresses[index].container.shelf, 'units');
					this.emptyUnits = this.singleProductData.addresses[index].container.shelf.container_shelf_unit_statuses;
				}
			},
			setProductSpaceType(index) {
				// resetting
				this.$delete(this.singleProductData.addresses[index], 'container');
				this.$delete(this.singleProductData.addresses[index], 'containers');

				this.errors.product.addresses[index] = {};

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

				this.singleProductData.addresses.forEach(

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

	                    this.singleProductData.preview = evnt.target.result;
	                    
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

	                    this.singleProductData.variations[index].preview = evnt.target.result;
	                    
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
			validateFormInput (formInputName) {

				this.submitForm = false;

				switch(formInputName) {

					/*
					case 'product_merchant_id' :

						if (!this.singleProductData.merchant || Object.keys(this.singleProductData.merchant).length == 0) {
							this.errors.product.product_merchant_id = 'Merchant is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.product, 'product_merchant_id');
						}

						break;
					*/
				
					case 'product_category' : 
						
						if (this.productMode=='retail product' && (! this.singleProductData.hasOwnProperty('product_category_id') || ! this.singleProductData.product_category_id)) {
							
							this.submitForm = false;
							this.errors.product.product_category = 'Product category is required'

						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.product, 'product_category');
						}

						break;

					case 'product_name' :

						if (!this.singleProductData.name || !this.singleProductData.name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.product.product_name = 'No special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.product, 'product_name');
						}

						break;

					/*
					case 'product_sku' :

						if (this.singleProductData.sku && !this.singleProductData.sku.match(/^[a-zA-Z0-9]*$/g)) {
							this.errors.product.product_sku = 'Invalid code';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.product, 'product_sku');
						}

						break;

					case 'product_price' :

						if (this.productMode==='retail product' && (!this.singleProductData.price || this.singleProductData.price < 0)) {
							this.errors.product.product_price = 'Price is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.product, 'product_price');
						}

						break;
					*/

				/*
					case 'product_initial_quantity' :

						if (!this.singleProductData.initial_quantity || this.singleProductData.initial_quantity < 1) {
							this.errors.product.product_initial_quantity = 'Qty is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.product, 'product_initial_quantity');
						}

						break;

					case 'product_available_quantity' :

						if (!this.singleProductData.available_quantity || this.singleProductData.available_quantity < 0 || this.singleProductData.available_quantity > this.singleProductData.initial_quantity) {
							this.errors.product.product_available_quantity = 'Quantity is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.product, 'product_available_quantity');
						}

						break;

				*/
					case 'product_quantity_type' :

						if (!this.singleProductData.quantity_type) {
							this.errors.product.product_quantity_type = 'Qty type is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.product, 'product_quantity_type');
						}

						break;

					case 'product_variation_type' : 
						
						if (!this.singleProductData.has_variations) {
							this.submitForm = true;
							this.$delete(this.errors.product, 'product_variation_type');
						}
						else if (this.singleProductData.has_variations && (!this.singleProductData.variation_type || Object.keys(this.singleProductData.variation_type).length == 0)) {
							
							this.errors.product.product_variation_type = 'Variation type is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.product, 'product_variation_type');
						}

						break;

					case 'product_variation_id' :
						
						if (this.singleProductData.has_variations) {
							
							this.singleProductData.variations.forEach(
								(productVariation, index) => {
									if (! productVariation.hasOwnProperty('variation') || Object.keys(productVariation.variation).length == 0) {
										
										this.errors.product.variations[index].product_variation_id = 'Variation is required';

									}
									else if (this.singleProductData.variations.filter((obj) => (obj.hasOwnProperty('variation') && obj.variation.id) === productVariation.variation.id).length > 1) {

										this.errors.product.variations[index].product_variation_id = 'Same Variation selected';
									}
									else {
										this.$delete(this.errors.product.variations[index], 'product_variation_id');
									}
								}
							);
							
							if (!this.errorInArray(this.errors.product.variations)) {
								this.submitForm = true;
							}

						}
						else {
							this.submitForm = true;
							this.errors.product.variations = [];
						}

						break;

					/*
					case 'product_variation_quantity' :

						if (this.singleProductData.has_variations) {

							this.singleProductData.variations.forEach(
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

					/*
					case 'product_variation_price' :

						if (this.singleProductData.has_variations) {

							this.singleProductData.variations.forEach(
								(productVariation, index) => {
									if (!productVariation.price || productVariation.price < 0) {
										
										this.errors.product.variations[index].product_variation_price = 'Variation price is required';

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
							this.errors.product.variations = [];
						}

						break;
					*/

					/*
					case 'product_variation_total_quantity' :

						if (this.singleProductData.has_variations) {

							if (!this.errorInArray(this.errors.product.variations)) {

								let variationTotalQuantity = this.singleProductData.variations.reduce(
									(value, currentObject) => {
										return value + currentObject.initial_quantity;
									}, 
								0);

								if (variationTotalQuantity !== this.singleProductData.initial_quantity) {
									this.errors.product.variations[this.singleProductData.variations.length-1].product_variation_quantity = 'Total variation qty should be equal to qty';
								}
								else {
									this.$delete(this.errors.product.variations[this.singleProductData.variations.length-1], 'product_variation_quantity');
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

						this.singleProductData.addresses.forEach(
							
							(productSpace, index) => {

								if (!productSpace.type) {
									this.errors.product.addresses[index].product_space_type = 'Space type is required';
								}
								else if (this.singleProductData.addresses.filter((obj) => obj.type === productSpace.type).length > 1) {

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

						this.singleProductData.addresses.forEach(
							
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

						this.singleProductData.addresses.forEach(
							
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

						this.singleProductData.addresses.forEach(
							
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

						this.singleProductData.addresses.forEach(
							
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

						this.singleProductData.addresses.forEach(
							
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

<style>
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