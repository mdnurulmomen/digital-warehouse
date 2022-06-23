
<template v-if="userHasPermissionTo('view-requisition-index')">
	<div class="pcoded-content">
		<breadcrumb 
			:icon="'requisitions'"
			:title="merchantProduct.product.name + ' requisitions'" 
			:message="'All ' + merchantProduct.merchant.first_name + ' ' + merchantProduct.merchant.last_name + ' requisitions for ' + merchantProduct.product.name"
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
											<addition-search-export-option
												v-if="userHasPermissionTo('view-requisition-index') || userHasPermissionTo('create-requisition')" 
										  		:search-attributes="searchAttributes" 
										  		:caller-page="'requisition'" 
										  		:required-permission = "'requisition'" 
										  		:disable-add-button="formSubmitted ? true : false" 
										  		:data-to-export="dataToExport" 
										  		:contents-to-download="requisitionsToShow" 
										  		:pagination="pagination" 
										  		:current-tab="currentTab"
										  		
										  		@showContentCreateForm="showContentCreateForm" 
										  		@searchData="searchData($event)" 
										  		@fetchAllContents="fetchAllRequisitions"
											/>
											
											<div class="col-sm-12 col-lg-12">	
												<loading v-show="loading"></loading>
												
										  		<tab 
										  			v-show="searchAttributes.search === '' && ! searchAttributes.dateFrom && ! searchAttributes.dateTo && ! loading /* && ! searchAttributes.showPendingRequisitions && ! searchAttributes.showCancelledRequisitions && ! searchAttributes.showDispatchedRequisitions && ! searchAttributes.showProduct */" 
										  			:tab-names="['pending', 'dispatched', 'cancelled']" 
										  			:current-tab="'pending'" 

										  			@showPendingContents="showPendingContents" 
										  			@showDispatchedContents="showDispatchedContents" 
										  			@showCancelledContents="showCancelledContents" 
										  		></tab>

 												<div class="tab-content" v-show="!loading">
													<div class="card">
														<div class="table-responsive">
															<table class="table table-striped table-bordered nowrap text-center">
																<thead>
																	<tr>
																		<th>Subject</th>
																		<th>Status</th>
																		<th>Confirmation</th>
																		<th>Actions</th>
																	</tr>
																</thead>
																
																<tbody>
																	<tr 
																		v-for="(content, index) in requisitionsToShow" :key="'tab-' + currentTab + '-content-index-' + index + '-content-' + content.id" 
																		:class="content.id==singleRequisitionData.id ? 'highlighted' : ''" 
																	>
																		<td>
																			{{ content.subject | capitalize }}
																		</td>

																		<td>
																			<span :class="[content.status==1 && content.dispatch.has_approval==1 ? 'badge-success' : content.status==1 && content.dispatch.has_approval==0 ? 'badge-warning' : content.status==0 ? 'badge-danger' : 'badge-default', 'badge']">
																				
																				{{ content.status==1 && content.dispatch.has_approval==1 ? 'Dispatched' : content.status==1 && content.dispatch.has_approval==0 ? 'Recommended' : content.status==0 ? 'Pending' : 'Cancelled' }}

																			</span>
																		</td>

																		<td>
																			<span 
																			v-if="content.status==1 && content.dispatch.has_approval==1"
																			:class="[!unconfirmed(content) ? 'badge-success' : 'badge-danger', 'badge']">
																				{{ !unconfirmed(content) ? 'Confirmed' : 'Not Confirmed' }}
																			</span>
																			<span v-else 
																			class="badge badge-secondary" 
																			>
																				NA
																			</span>
																		</td>
																		
																		<td>
																			<button 
																				type="button" 
																				class="btn btn-grd-info btn-icon" 
																				v-tooltip.bottom-end="'View Details'"  
																				@click="showContentDetails(content)"
																			>
																				<i class="fa fa-eye"></i>
																			</button>

																			<!-- 
																			<button 
																				type="button" 
																				class="btn btn-grd-warning btn-icon" 
																				v-tooltip.bottom-end="'Recommended Dispatch'" 
																				@click="showDispatchRecommendationForm(content)" 
																				v-show="content.status==0" 
																				v-if="userHasPermissionTo('recommend-dispatch')"
																			>
																				<i class="fa fa-truck"></i>
																			</button>

																			<button 
																				type="button" 
																				class="btn btn-grd-warning btn-icon" 
																				v-tooltip.bottom-end="'Approve Dispatch'"  
																				@click="showDispatchApprovalForm(content)" 
																				v-show="content.status==1 && content.dispatch.has_approval==0" 
																				v-if="userHasPermissionTo('approve-dispatch')"
																			>
																				<i class="fa fa-check-circle"></i>
																			</button>

																			<button 
																				type="button" 
																				class="btn btn-grd-danger btn-icon" 
																				v-tooltip.bottom-end="'Cancel Requisition'" 
																				@click="openRequisitionCancelForm(content)" 
																				v-show="content.status==0" 
																				v-if="userHasPermissionTo('update-requisition')"
																			>
																				<i class="fa fa-trash"></i>
																			</button> 
																			-->
																		</td>
																	</tr>
																	<tr 
																  		v-show="!requisitionsToShow.length"
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
																		<th>Name</th>
																		<th>Status</th>
																		<th>Confirmation</th>
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
																v-tooltip.bottom-end="'Reload'" 
																@click="pagination.current_page = 1; searchAttributes.search === '' ? fetchAllRequisitions() : searchData()"
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
																@paginate="searchAttributes.search === '' ? fetchAllRequisitions() : searchData()"
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
		<div class="modal fade" id="requisition-createOrEdit-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">
							Make Requisition
						</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
						
					<form 	
						class="form-horizontal" 
						v-on:submit.prevent="storeRequisition()" 
						autocomplete="off" 
						novalidate="true" 
					>
						<input type="hidden" name="_token" :value="csrf">

						<div class="modal-body">
							<transition-group name="fade">
								<div 
									class="row" 
									v-bind:key="'requisition-modal-step-' + 1" 
									v-show="!loading && step==1"
								>								  	
									<h2 class="mx-auto mb-4 lead">Requisition Profile</h2>

							        <div class="col-md-12">
							        	<!-- 
							        	<div class="form-row">
							        		<div class="form-group col-md-12">
							        			<label for="inputFirstName">Select Merchant</label>
													
												<multiselect 
			                              			v-model="singleRequisitionData.merchant"
			                              			placeholder="Merchant Name" 
			                              			label="name" 
			                                  		track-by="id" 
			                                  		:custom-label="objectNameWithCapitalized" 
			                                  		:options="allMerchants" 
			                                  		:required="true" 
			                                  		:allow-empty="false" 
			                                  		class="form-control p-0" 
			                                  		:class="!errors.merchant_id ? 'is-valid' : 'is-invalid'" 
			                                  		@input="setRequisitionMerchant()"
			                                  		@close="validateFormInput('merchant_id')" 
			                              		>
			                                	</multiselect>
			                                	
			                                	<div class="invalid-feedback">
											    	{{ errors.merchant_id }}
											    </div>
							        		</div>
							        	</div> 
							        	-->

							        	<div class="form-row">
							        		<div class="form-group col-md-12">
							        			<label for="inputFirstName">Select Merchant</label>
													
												<multiselect 
			                              			v-model="singleRequisitionData.merchant"
			                              			placeholder="Merchant Name" 
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
							        	</div>

										<div class="form-row">
											<div class="form-group col-md-12">
												<label for="inputFirstName">Subject</label>
												
												<input type="text" 
													class="form-control" 
													v-model="singleRequisitionData.subject" 
													placeholder="Relevant Subject" 
													:class="!errors.subject  ? 'is-valid' : 'is-invalid'" 
													@input="validateFormInput('subject')" 
													required="true" 
												>

												<div class="invalid-feedback">
										        	{{ errors.subject }}
										  		</div>
											</div>
										</div>

										<div class="form-row">
											<label class="form-group col-md-4">
												Requisition Date
											</label>

											<div class="form-group col-md-8">
												<v-date-picker 
													v-model="singleRequisitionData.created_at" 
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

							        <div class="col-md-12 card-footer">
								    	<div class="form-row">
									    	<div class="col-sm-12 text-right">
								          		<div class="text-danger small mb-1" v-show="!submitForm">
											  		Please input required fields
									          	</div>

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
									v-bind:key="'requisition-modal-step' + 2" 
									v-show="!loading && step==2"
								>
									<h2 class="mx-auto mb-4 lead">Required Products</h2>

									<div 
										class="card col-md-12" 
										v-if="singleRequisitionData.products.length && singleRequisitionData.products.length==errors.products.length"
									>
										<div 
											class="card-body" 
											v-for="(requiredProduct, productIndex) in singleRequisitionData.products" 
											:key="'required-product-' + productIndex"
										>
											<div class="form-row">
												<!-- 
												<div class="form-group col-md-6">
													<label for="inputFirstName">Select Product</label>
													
													<multiselect 
				                              			v-model="requiredProduct.product"
				                              			placeholder="Product Name" 
				                              			label="name" 
				                                  		track-by="id" 
				                                  		:custom-label="objectNameWithCapitalized" 
				                                  		:options="merchantAllProducts" 
				                                  		:required="true" 
				                                  		:allow-empty="false" 
				                                  		class="form-control p-0" 
				                                  		:class="!errors.products[productIndex].product_id ? 'is-valid' : 'is-invalid'" 
				                                  		:disabled="singleRequisitionData.products.length > (productIndex+1)"
				                                  		@input="setRequiredProduct(productIndex)"
				                                  		@close="validateFormInput('product_id')" 
				                              		>
				                                	</multiselect>
				                                	
				                                	<div class="invalid-feedback">
												    	{{ errors.products[productIndex].product_id }}
												    </div>
												</div> 
												-->

												<div class="form-group col-md-6">
													<label for="inputFirstName">Select Product</label>
													
													<multiselect 
				                              			v-model="requiredProduct.product"
				                              			placeholder="Product Name" 
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

												<div class="form-group col-md-6" v-if="requiredProduct.product">
													<label for="inputFirstName">Total Quantity</label>

													<div class="input-group mb-0">
														<input type="number" 
															class="form-control"  
															v-model.number="requiredProduct.total_quantity"  
															placeholder="Product Total Quantity" 
															:class="!errors.products[productIndex].product_quantity  ? 'is-valid' : 'is-invalid'" 
															@change="validateFormInput('product_quantity')" 
															required="true" 
															min="0" 
															:max="requiredProduct.product.available_quantity - requiredProduct.product.requested_quantity"
														>
														<div class="input-group-append">
															<span class="input-group-text" id="basic-addon2">
																{{ requiredProduct.product.product ? requiredProduct.product.product.quantity_type : 'Unit' | capitalize }}
															</span>
														</div>
													</div>

													<div 
														class="invalid-feedback" 
														style="display: block;" 
														v-show="errors.products[productIndex].product_quantity" 
													>
											        	{{ errors.products[productIndex].product_quantity }}
											  		</div>
												</div>
											</div>

											<div class="card" v-if="requiredProduct.product && requiredProduct.product.has_variations && requiredProduct.product.variations.length">
												<div class="card-body">
													<div 
														class="form-row" 
														v-for="(productVariation, variationIndex) in requiredProduct.product.variations" 
														:key="'required-product-variation-' + productIndex + variationIndex"
													>	
														<div class="form-group col-md-6">
															<label for="inputFirstName">Select Variation</label>
															<multiselect 
						                              			v-model="requiredProduct.product.variations[variationIndex]"
						                              			class="form-control p-0 is-valid" 
						                              			placeholder="Variation Name" 
						                              			label="variation_name" 
						                                  		track-by="id" 
						                                  		:custom-label="objectNameWithCapitalized" 
						                                  		:options="requiredProduct.product.variations" 
						                                  		:required="true" 
						                                  		:allow-empty="false" 
						                                  		:disabled="true"
						                              		>
						                                	</multiselect>
														</div>

														<div class="form-group col-md-6">
															<label for="inputFirstName">
																Variation Quantity
															</label>

															<div class="input-group mb-0">
																<input type="number" 
																	class="form-control" 
																	v-model.number="requiredProduct.product.variations[variationIndex].required_quantity" 
																	placeholder="Variation Quantity" 
																	:class="!errors.products[productIndex].variation_quantities[variationIndex] ? 'is-valid' : 'is-invalid'" 
																	min="0" 
																	:max="productVariation.available_quantity - productVariation.requested_quantity" 
																	@change="validateFormInput('variations_total_quantity')" 
																>
																<div class="input-group-append">
																	<span class="input-group-text" id="basic-addon2">
																		{{ requiredProduct.product.product ? requiredProduct.product.product.quantity_type : 'Unit' | capitalize }}
																	</span>
																</div>
															</div>

															<div 
																class="invalid-feedback" 
																style="display: block;" 
																v-show="errors.products[productIndex].variation_quantities[variationIndex]" 
															>
													        	{{ errors.products[productIndex].variation_quantities[variationIndex] }}
													  		</div>
														</div>
													</div>
												</div>
											</div>

											<div 
												class="form-row" 
												v-if="requiredProduct.product && requiredProduct.total_quantity"
											>
												<div class="form-group col-md-12 text-center">
													<toggle-button 
														v-model="requiredProduct.packaging_service" 
														:value="false" 
														:width=200 
														:color="{checked: 'green', unchecked: 'red'}"
														:labels="{checked: 'Want Packaging', unchecked: 'No Packaging'}" 
														@change="setPackagingService(productIndex)"
													/>
												</div>

												<div class="col-md-12" v-if="requiredProduct.packaging_service">
													<div class="form-row">
														<div class="col-md-3"></div>
														<div class="col-md-6">
															<multiselect 
						                              			v-model="requiredProduct.preferred_package" 
						                              			class="form-control p-0 is-valid" 
						                              			placeholder="Choose Package" 
						                              			label="name" 
						                                  		track-by="id" 
						                                  		:options="allPackagingPackages" 
						                                  		:custom-label="customPackagingPackageName" 
						                              		>
						                                	</multiselect>
														</div>
														<div class="col-md-3"></div>
													</div>
												</div>
											</div>

											<div class="form-row">
												<div class="form-group col-md-12 text-center">
													<div 
														class="invalid-feedback" 
														style="display: block;" 
														v-show="errors.products[productIndex].variations_total_quantity"
													>
											        	{{ errors.products[productIndex].variations_total_quantity }}
											  		</div>
												</div>
											</div>
										</div>

										<!-- 
										<div class="form-row">
											<div class="form-group col-md-6">
												<button 
													type="button" 
													class="btn waves-effect waves-light hor-grd btn-grd-primary btn-sm btn-block" 
													v-tooltip.bottom-end="'Add Product'" 
													:disabled="singleRequisitionData.products.length >= merchantAllProducts.length"
													@click="addMoreProduct()"
												>
													More Product
												</button>
											</div>
											<div class="form-group col-md-6">
												<button 
													type="button" 
													class="btn waves-effect waves-light hor-grd btn-grd-info btn-sm btn-block" 
													v-tooltip.bottom-end="'Remove Product'" 
													:disabled="singleRequisitionData.products.length < 2"
													@click="removeProduct()"
												>
													Remove Product
												</button>
											</div>
										</div> 
										-->
									</div>

									<div class="form-group col-md-12">
										<label for="inputFirstName">Short Note</label>
										<ckeditor 
			                              	class="form-control" 
			                              	:editor="editor" 
			                              	v-model="singleRequisitionData.description"
			                            >
		                              	</ckeditor>
									</div>

									<div class="col-md-12 card-footer">
							        	<div class="form-row">
											<div class="col-sm-12 text-right">
								          		<div class="text-danger small mb-1" v-show="!submitForm">
											  		Please input required fields
									          	</div>
								          	</div>
								          	<div class="col-md-12">
								          		<button type="button" class="btn btn-outline-secondary btn-sm btn-round float-left" v-tooltip.bottom-end="'Previous'"  v-on:click="step-=1">
							                    	<i class="fa fa-2x fa-angle-double-left" aria-hidden="true"></i>
							                  	</button>
								          		<button type="button" class="btn btn-outline-secondary btn-sm btn-round float-right" v-tooltip.bottom-end="'Next'"  v-on:click="nextPage">
							                    	<i class="fa fa-2x fa-angle-double-right" aria-hidden="true"></i>
							                  	</button>
								          	</div>
										</div>
									</div>
								</div>

								<div 
									class="row" 
									v-bind:key="'requisition-modal-step' + 3" 
									v-show="!loading && step==3"
								>
									<h2 class="mx-auto mb-4 lead">Product Serials</h2>

									<div 
										class="col-md-12"  
										v-if="singleRequisitionData.products.length"
									>
										<div 
											class="card card-body" 
											v-for="(requiredProduct, productIndex) in singleRequisitionData.products" 
											:key="'required-product-' + productIndex + '-serials'"
										>
											<div 
												class="form-row"
												v-if="requiredProduct.product && requiredProduct.product.has_serials && ! requiredProduct.product.has_variations && requiredProduct.total_quantity > 0"
											>
												<div class="form-group col-md-4">
													<label for="inputFirstName">Selected Product</label>
													
													<multiselect 
				                              			v-model="requiredProduct.product" 
				                              			class="form-control p-0 is-valid" 
				                              			placeholder="Product Name" 
				                              			label="name" 
				                                  		track-by="id" 
				                                  		:custom-label="objectNameWithCapitalized" 
				                                  		:options="[]" 
				                                  		:required="true" 
				                                  		:allow-empty="false"
				                                  		:disabled="true"
				                              		>
				                                	</multiselect>
												</div>

												<div class="form-group col-md-8">
													<label for="inputFirstName">Product Serials</label>
													
													<multiselect 
				                              			v-model="requiredProduct.serials" 
				                              			class="form-control p-0" 
				                              			:class="errors.products[productIndex].product_serials ? 'is-invalid' : 'is-valid'" 
				                              			placeholder="Product Serials" 
				                              			:multiple="true" 
				                              			:close-on-select="false" 
				                              			:max="requiredProduct.total_quantity" 
				                              			label="name" 
				                                  		track-by="id" 
				                                  		:custom-label="objectNameWithCapitalized" 
				                                  		:options="requiredProduct.product.serials" 
				                                  		@close="validateFormInput('product_serials')"
				                              		>
				                                	</multiselect>
				                                	 
				                                	<div 
														class="invalid-feedback" 
													>
											        	{{ errors.products[productIndex].product_serials }}
											  		</div>
												</div>
											</div>

											<div 
												class="form-row"
												v-else-if="requiredProduct.product && requiredProduct.product.has_serials && requiredProduct.product.has_variations && requiredProduct.total_quantity > 0"
											>
												<div class="form-group col-md-12">
													<div class="card">
														<div class="card-header">
															{{ requiredProduct.product.name | capitalize }} Serials
														</div>

														<div class="card-body">
															<div 
																class="form-row" 
																v-for="(productVariation, variationIndex) in requiredProduct.product.variations" 
																:key="'required-product-' + productIndex + '-variation-' + variationIndex + '-serials'" 
																v-show="productVariation.required_quantity > 0"
															>	
																<div class="form-group col-md-4">
																	<label for="inputFirstName">Selected Variation</label>
																	
																	<multiselect 
								                              			v-model="productVariation.variation" 
								                              			class="form-control p-0 is-valid" 
								                              			placeholder="Variation Name" 
								                              			label="name" 
								                                  		track-by="id" 
								                                  		:options="[]" 
								                                  		:disabled="true"
								                              		>
								                                	</multiselect>
																</div>

																<div class="form-group col-md-8">
																	<label for="inputFirstName">
																		Variation Serials
																	</label>

																	<multiselect 
								                              			v-model="requiredProduct.product.variations[variationIndex].required_serials" 
								                              			class="form-control p-0 is-valid" 
								                              			:class="errors.products[productIndex].variation_serials[variationIndex] ? 'is-invalid' : 'is-valid'"
								                              			placeholder="Variation Serials" 
								                              			:multiple="true" 
								                              			:close-on-select="false" 
								                              			:max="productVariation.required_quantity" 
								                              			label="name" 
								                                  		track-by="id" 
								                                  		:custom-label="objectNameWithCapitalized" 
								                                  		:options="requiredProduct.product.variations[variationIndex].serials" 
								                                  		@close="validateFormInput('product_serials')"
								                              		>
								                                	</multiselect>

																	 
																	<div 
																		class="invalid-feedback" 
																	>
															        	{{ errors.products[productIndex].variation_serials[variationIndex]}}
															  		</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="col-md-12 card-footer">
							        	<div class="form-row">
											<div class="col-sm-12 text-right">
								          		<div class="text-danger small mb-1" v-show="!submitForm">
											  		Please input required fields
									          	</div>
								          	</div>
								          	<div class="col-md-12">
								          		<button type="button" class="btn btn-outline-secondary btn-sm btn-round float-left" v-tooltip.bottom-end="'Previous'"  v-on:click="step-=1">
							                    	<i class="fa fa-2x fa-angle-double-left" aria-hidden="true"></i>
							                  	</button>
								          		<button type="button" class="btn btn-outline-secondary btn-sm btn-round float-right" v-tooltip.bottom-end="'Next'"  v-on:click="nextPage">
							                    	<i class="fa fa-2x fa-angle-double-right" aria-hidden="true"></i>
							                  	</button>
								          	</div>
										</div>
									</div>
								</div>

								<div 
									class="row" 
									v-bind:key="'requisition-modal-step-' + 4" 
									v-show="!loading && step==4"
								>	
									<h2 class="mx-auto mb-4 lead">Delivery Details</h2>

							        <div class="col-md-12">
										<div class="form-row">
											<div class="form-group col-md-12 text-center">
												<toggle-button 
													v-model="singleRequisitionData.delivery_service" 
													:value="false" 
													:width=200 
													:color="{checked: 'green', unchecked: 'blue'}"
													:labels="{checked: 'Delivery Service', unchecked: 'Self Agent'}" 
													@change="setRequisitionAgent"
												/>
											</div>
										</div>

										<div 
											class="form-row" 
											v-if="! singleRequisitionData.delivery_service && singleRequisitionData.agent"
										>	
											<div class="form-group col-md-12" v-show="merchantAllAgents.length">
												<label for="inputFirstName">Select From Previous Agent</label>
												<multiselect 
			                              			v-model="singleRequisitionData.previous_agent" 
			                              			class="form-control p-0 is-valid" 
			                              			placeholder="Agent Name" 
			                              			label="name" 
			                                  		track-by="id" 
			                                  		:custom-label="objectNameWithCapitalized" 
			                                  		:options="merchantAllAgents" 
			                                  		@input="resetMerchantAgent()"
			                              		>
			                                	</multiselect>
											</div>

											<div class="form-group col-md-6">
												<label for="inputFirstName">Agent Name</label>
												<input type="text" 
													class="form-control" 
													v-model="singleRequisitionData.agent.name" 
													placeholder="Agent Name" 
													:class="!errors.agent.agent_name ? 'is-valid' : 'is-invalid'" 
													@input="validateFormInput('agent_name')" 
													@change="resetSelectedAgent()"
												>

												<div class="invalid-feedback">
										        	{{ errors.agent.agent_name }}
										  		</div>
											</div>

											<div class="form-group col-md-6">
												<label for="inputFirstName">Mobile</label>
												<input type="text" 
													class="form-control" 
													v-model="singleRequisitionData.agent.mobile" 
													placeholder="Agent Name" 
													:class="!errors.agent.agent_mobile  ? 'is-valid' : 'is-invalid'" 
													@input="validateFormInput('agent_mobile')" 
													@change="resetSelectedAgent()"
												>

												<div class="invalid-feedback">
										        	{{ errors.agent.agent_mobile }}
										  		</div>
											</div>

											<div class="form-group col-md-6">
												<label for="inputFirstName">Agent Code</label>
												<input type="text" 
													class="form-control" 
													v-model="singleRequisitionData.agent.code" 
													placeholder="Secret Code" 
													:class="!errors.agent.agent_code  ? 'is-valid' : 'is-invalid'" 
													@input="validateFormInput('agent_code')" 
													@change="resetSelectedAgent()"
												>

												<div class="invalid-feedback">
										        	{{ errors.agent.agent_code }}
										  		</div>
											</div>
											<!-- 
											<div class="form-group col-md-6">
												<label for="inputFirstName">Present</label>
												<toggle-button 
													v-model="singleRequisitionData.agent.presence_confirmation" 
													:width=200 
													:color="{checked: 'red', unchecked: 'green'}"
													:labels="{checked: 'Now', unchecked: 'Later'}" 
												/>
											</div>
											-->
										</div>

										<div class="form-row" v-else-if="singleRequisitionData.delivery_service">
											<div class="form-group col-md-12">
												<label for="inputFirstName">Address</label>
												<ckeditor 
					                              	class="form-control" 
					                              	:editor="editor" 
					                              	v-model="singleRequisitionData.delivery.address" 
					                              	@blur="validateFormInput('delivery_address')"
					                            >
				                              	</ckeditor>

				                              	<div 
					                              	class="invalid-feedback" 
					                              	style="display : block" 
					                              	v-show="errors.delivery.delivery_address"
				                              	>
										        	{{ errors.delivery.delivery_address }}
										  		</div>
											</div>
										</div>
							        </div>

							        <div class="col-md-12 card-footer">
							        	<div class="form-row">
											<div class="col-sm-12 text-right" v-show="!submitForm">
												<span class="text-danger small mb-1">
											  		Please input required fields
											  	</span>
											</div>
											<div class="col-sm-12">
												<button type="button" class="btn btn-outline-secondary btn-sm btn-round float-left" v-on:click="requisitionHasSerialProduct() ? step-=1 : step-=2">
							                    	<i class="fa fa-2x fa-angle-double-left" aria-hidden="true"></i>
							                  	</button>
												<button type="submit" class="btn btn-primary float-right" :disabled="!submitForm || formSubmitted">
													Request
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

 		<!--Dispatch Modal -->
		<div class="modal fade" id="dispatch-createOrEdit-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" v-if="userHasPermissionTo('create-requisition') || userHasPermissionTo('update-requisition')">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">
							{{ (singleDispatchData.requisition.status==0 && ! userHasPermissionTo('approve-dispatch')) ? 'Recommend' : 'Approve' }} {{ singleDispatchData.requisition.subject | capitalize }}
						</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
						
					<form 	
						class="form-horizontal" 
						v-on:submit.prevent="makeDispatch()" 
						autocomplete="off" 
						novalidate="true" 
					>
						<input type="hidden" name="_token" :value="csrf">

						<div class="modal-body">
							<transition-group name="fade">
								<div 
									class="row" 
									v-bind:key="'dispatch-modal-step-' + 1" 
									v-show="!loading && step==1"
								>								  	
									<h2 class="mx-auto mb-4 lead">Requisition Profile</h2>

							        <div class="col-md-12">	
										<div class="form-row">
											<!-- 
											<div class="form-group col-md-12">
												<label for="inputFirstName">
													Selected Requisition
												</label>
												<multiselect 
			                              			v-model="singleDispatchData.requisition"
			                              			placeholder="Requisition Subject" 
			                                  		track-by="id" 
			                                  		:custom-label="objectNameWithCapitalized" 
			                                  		:options="availableRequisitions" 
			                                  		:required="true" 
			                                  		:allow-empty="false" 
			                                  		class="form-control p-0" 
			                                  		:class="!errors.requisition_id ? 'is-valid' : 'is-invalid'" 
			                                  		@close="validateFormInput('requisition_id')" 
			                              		>
			                                	</multiselect>
			                                	<div class="invalid-feedback">
											    	{{ errors.requisition_id }}
											    </div>
											</div>
											 -->
											 
											<div class="form-group col-md-12">
												<label for="inputFirstName">
													Selected Requisition
												</label>
												<multiselect 
			                              			v-model="singleDispatchData.requisition"
			                              			placeholder="Requisition Subject" 
			                                  		track-by="id" 
			                                  		:custom-label="objectNameWithCapitalized" 
			                                  		:options="[]" 
			                                  		:required="true" 
			                                  		:allow-empty="false" 
			                                  		class="form-control p-0 is-valid" 
			                              		>
			                                	</multiselect>
											</div>
										</div>

										<div class="form-row">
											<label class="col-sm-4 col-form-label form-group">
												Requested On
											</label>

											<label class="col-sm-8 col-form-label form-group">
												{{ singleDispatchData.requisition.created_at }}
											</label>
										</div>

										<div class="form-row" v-show="singleDispatchData.requisition.status==0">
											<label class="form-group col-md-3">
												{{ userHasPermissionTo('approve-dispatch') ? 'Dispatch' : 'Recommendation' }} Date
											</label>

											<div class="form-group col-md-9">
												<v-date-picker 
													v-model="singleDispatchData.requisition.updated_at" 
													color="red" 
													is-dark
													is-inline 
													:min-date="singleDispatchData.requisition.created_at" 
													:max-date="new Date()" 
													:model-config="{ type: 'string', mask: 'YYYY-MM-DD' }" 
													:attributes="[ { key: 'today', dot: true } ]" 
												/>
											</div>
										</div>

										<div class="form-row" v-show="singleDispatchData.requisition.status==1">
											<label class="col-sm-4 col-form-label form-group">
												Recommended On
											</label>

											<label class="col-sm-8 col-form-label form-group">
												{{ singleDispatchData.requisition.updated_at }}
											</label>
										</div>
									</div>

									<div class="col-md-12 card-footer">
								    	<div class="form-row">
									    	<div class="col-sm-12 text-right">
								          		<div class="text-danger small mb-1" v-show="!submitForm">
											  		Please input required fields
									          	</div>
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
									v-bind:key="'dispatch-modal-step-' + 2" 
									v-show="!loading && step==2"
								>								  	
									<h2 class="mx-auto mb-4 lead">Required Products</h2>

									<div 
										class="col-md-12" 
										v-if="Object.keys(singleDispatchData.requisition).length > 0 && singleDispatchData.requisition.products && singleDispatchData.requisition.products.length"
									>
										<div 
											class="card card-body" 
											v-for="(requiredProduct, productIndex) in singleDispatchData.requisition.products" 
											:key="'required-product-' + productIndex"
										>
											<div class="form-row">
												<div class="form-group col-md-4">
													<label for="inputFirstName">
														Product
													</label>
													<multiselect 
				                              			v-model="singleDispatchData.requisition.products[productIndex]"
				                              			placeholder="Product Name" 
				                              			label="product_name" 
				                                  		track-by="merchant_product_id" 
				                                  		class="form-control p-0 is-valid" 
				                                  		:custom-label="objectNameWithCapitalized" 
				                                  		:options="singleDispatchData.requisition.products"
				                                  		:disabled="true"
				                              		>
				                                	</multiselect>
												</div>

												<div class="form-group col-md-4">
													<label for="inputFirstName">
														Total Required
													</label>
													
													<div class="input-group mb-0">
														<input 
															type="number" 
															class="form-control is-valid" 
															v-model.number="requiredProduct.quantity" 
															placeholder="Product Total Quantity" 
															readonly="true"
														>
														<div class="input-group-append">
															<span class="input-group-text">
																{{ requiredProduct.quantity_type }}
															</span>
														</div>
													</div>
												</div>

												<div class="form-group col-md-4">
													<label for="inputFirstName">
														Total Available
													</label>
													
													<div class="input-group mb-0">
														<input 
															type="number" 
															class="form-control" 
															:class="requiredProduct.quantity > requiredProduct.available_quantity ? 'is-invalid' : 'is-valid'"
															v-model.number="requiredProduct.available_quantity" 
															readonly="true"
														>
														<div class="input-group-append">
															<span class="input-group-text">
																{{ requiredProduct.quantity_type }}
															</span>
														</div>
													</div>
												</div>
											</div>

											<div class="form-row" v-if="! requiredProduct.has_variations && requiredProduct.available_stocks && requiredProduct.available_stocks.length">
												<div class="form-group col-md-4">
													<label for="inputFirstName">
														Select Stock (batch) Code
													</label>

													<multiselect 
				                              			v-model="requiredProduct.selected_stock"
				                              			placeholder="Stock Code" 
				                              			label="stock_code" 
				                                  		track-by="id" 
				                                  		class="form-control p-0" 
				                                  		:class="! errors.products[productIndex].product_stock_code ? 'is-valid' : 'is-invalid'"  
				                                  		:custom-label="objectNameWithCapitalized" 
				                                  		:options="requiredProduct.available_stocks" 
				                                  		@close="validateFormInput('product_stock_code')"
				                              		>
				                                	</multiselect>

				                                	<div class="invalid-feedback">
												    	{{ errors.products[productIndex].product_stock_code }}
												    </div>
												</div>

												<div class="form-group col-md-4">
													<label for="inputFirstName">
														Mfg. Date
													</label>

													<p class="form-control">
														{{ requiredProduct.selected_stock ? requiredProduct.selected_stock.manufactured_at : '--' }}
													</p>
												</div>

												<div class="form-group col-md-4">
													<label for="inputFirstName">
														Exp. Date
													</label>

													<p class="form-control">
														{{ requiredProduct.selected_stock ? requiredProduct.selected_stock.expired_at : '--' }}
													</p>
												</div>
											</div>

											<div class="card card-body" v-if="requiredProduct.has_serials && ! requiredProduct.has_variations && requiredProduct.hasOwnProperty('serials') && requiredProduct.serials.length">
												<div 
													class="form-row" 
													v-for="(requiredProductSerial, productSerialIndex) in requiredProduct.serials" 
														:key="'required-product-name-' + requiredProduct.product_name + '-serial-' + productSerialIndex"
												>
													<div class="col-md-6 form-group">
														<label for="inputFirstName">
															Serial # {{ productSerialIndex + 1 }}
														</label>
														
														<input 
															type="text" 
															class="form-control" 
															v-model="requiredProductSerial.serial.serial_no" 
															readonly="true"
														>
													</div>

													<div class="col-md-6 form-group">
														<label for="inputFirstName">
															Status
														</label>

														<span :class="[requiredProductSerial.serial.has_dispatched ? 'badge-danger' : 'badge-success', 'badge form-control d-block']">
															{{ requiredProductSerial.serial.has_dispatched ? 'NA' : 'Available' }}
														</span>
													</div>
												</div>
											</div>

											<div class="card" v-if="requiredProduct.has_variations && requiredProduct.hasOwnProperty('variations') && requiredProduct.variations.length">
												<div class="card-body">
													<div 
														class="form-row" 
														v-for="(productVariation, variationIndex) in requiredProduct.variations" 
														:key="'required-product-' + productIndex + 'variation-' + variationIndex"
													>	
														<div class="col-sm-12">
															<div class="form-row">
																<div class="form-group col-md-4">
																	<label for="inputFirstName">
																		Selected Variation
																	</label>
																	<multiselect 
								                              			v-model="requiredProduct.variations[variationIndex]"
								                              			placeholder="Variation Name" 
								                              			label="variation_name" 
								                                  		track-by="product_variation_id" 
								                                  		class="form-control p-0 is-valid" 
								                                  		:custom-label="objectNameWithCapitalized" 
								                                  		:options="singleDispatchData.requisition.products"
								                                  		:disabled="true"
								                              		>
								                                	</multiselect>
																</div>

																<div class="form-group col-md-4">
																	<label for="inputFirstName">
																		Required
																	</label>
																	
																	<div class="input-group mb-0">
																		<input 
																			type="number" 
																			class="form-control" 
																			v-model.number="requiredProduct.variations[variationIndex].quantity" 
																			placeholder="Variation Quantity" 
																			readonly="true" 
																		>
																		<div class="input-group-append">
																			<span class="input-group-text">
																				{{ requiredProduct.quantity_type }}
																			</span>
																		</div>
																	</div>
																</div>

																<div class="form-group col-md-4">
																	<label for="inputFirstName">
																		Available
																	</label>
																	
																	<div class="input-group mb-0">
																		<input 
																			type="number" 
																			class="form-control" 
																			v-model.number="productVariation.available_quantity" 
																			placeholder="Dispatch Quantity" 
																			readonly="true" 
																		>
																		<div class="input-group-append">
																			<span class="input-group-text">
																				{{ requiredProduct.quantity_type }}
																			</span>
																		</div>
																	</div>
																</div>
															</div>
														</div>

														<div class="col-sm-12" v-if="productVariation.available_stocks && productVariation.available_stocks.length">
															<div class="form-row">
																<div class="form-group col-md-4">
																	<label for="inputFirstName">
																		Select Stock (batch) Code
																	</label>

																	<multiselect 
								                              			v-model="productVariation.selected_stock"
								                              			placeholder="Stock Code" 
								                              			label="stock_code" 
								                                  		track-by="id" 
								                                  		class="form-control p-0" 
								                                  		:class="! errors.products[productIndex].variation_stock_codes[variationIndex] ? 'is-valid' : 'is-invalid'"  
								                                  		:custom-label="objectNameWithCapitalized" 
								                                  		:options="productVariation.available_stocks" 
								                                  		@close="validateFormInput('variation_stock_codes')"
								                              		>
								                                	</multiselect>

								                                	<div class="invalid-feedback">
																    	{{ errors.products[productIndex].variation_stock_codes[variationIndex] }}
																    </div>
																</div>

																<div class="form-group col-md-4">
																	<label for="inputFirstName">
																		Mfg. Date
																	</label>

																	<p class="form-control">
																		{{ productVariation.selected_stock ? productVariation.selected_stock.manufactured_at : '--' }}
																	</p>
																</div>

																<div class="form-group col-md-4">
																	<label for="inputFirstName">
																		Exp. Date
																	</label>

																	<p class="form-control">
																		{{ productVariation.selected_stock ? productVariation.selected_stock.expired_at : '--' }}
																	</p>
																</div>
															</div>
														</div>

														<div class="col-sm-12">
															<div class="form-row" v-if="productVariation.has_serials">
																<div class="form-group col-md-4">
																	<label for="inputFirstName">
																		{{ productVariation.variation_name | capitalize }} Serials
																	</label>
																</div>

																<div 
																	class="form-group col-md-8" 
																	v-for="(requiredProductVariationSerial, productVariationSerialIndex) in productVariation.serials" 
																		:key="'required-product-name-' + requiredProduct.product_name + 'variation-name-' + productVariation.variation_name + '-serial-' + productVariationSerialIndex"
																>
																	<div class="form-row">
																		<div class="col-md-6">
																			<label for="inputFirstName">
																				Serial # {{ productVariationSerialIndex + 1 }}
																			</label>
																			
																			<input 
																				type="text" 
																				class="form-control" 
																				v-model="requiredProductVariationSerial.serial.serial_no" 
																				readonly="true"
																			>
																		</div>

																		<div class="col-md-6">
																			<label for="inputFirstName">
																				Status
																			</label>

																			<div class="form-control border-0">
																				<span :class="[requiredProductVariationSerial.serial.has_dispatched ? 'badge-danger' : 'badge-success', 'badge']">
																					{{ requiredProductVariationSerial.serial.has_dispatched ? 'NA' : 'Available' }}
																				</span>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>

													
													<!-- 
													<div 
														class="form-row text-center" 
														v-show="errors.products[productIndex].variation_total_dispatched_quantity" 
													>
														<div class="invalid-feedback" 
															style="display:block;"
														>
													    	{{ 
													    		errors.products[productIndex].variation_total_dispatched_quantity
													    	}}
													    </div>

													</div>
												 	-->
												</div>
											</div>
										
											<!-- 
											<div class="form-row">
												<div class="form-group col-md-12 text-center">
													<div 
														class="invalid-feedback" 
														style="display: block;" 
														v-show="errors.products[productIndex].variation_total_dispatched_quantity"
													>
											        	{{ errors.products[productIndex].variation_total_dispatched_quantity }}
											  		</div>
												</div>
											</div>
									 		-->

									 		<div class="card" v-if="requiredProduct.packaging_service">
									 			<div class="card-body">
											 		<div class="form-row">
														<div class="form-group col-md-12 text-center">
															<toggle-button 
																v-model="requiredProduct.packaging_service" 
																:value="false" 
																:width=200 
																:color="{checked: 'green', unchecked: 'red'}"
																:labels="{checked: 'Want Packaging', unchecked: 'No Packaging'}" 
																:disabled="true"
															/>
														</div>

														<div class="col-md-12">
															<div class="form-row">
																<div class="col-md-4">
																	<label for="inputFirstName">
																		Preferred Package
																	</label>
																	<multiselect 
								                              			v-model="requiredProduct.preferred_package" 
								                              			class="form-control p-0 is-valid" 
								                              			:placeholder="requiredProduct.preferred_package ? 'Preferred Package' : 'No Preferred Package'" 
								                              			label="name" 
								                                  		track-by="id" 
								                                  		:options="[]" 
								                                  		:disabled="true"
								                              		>
								                                	</multiselect>
																</div>

																<div class="col-md-4">
																	<label for="inputFirstName">
																		Choose Package
																	</label>
																	<multiselect 
								                              			v-model="requiredProduct.dispatched_package" 
								                              			class="form-control p-0" 
								                              			:class="!errors.products[productIndex].dispatched_package ? 'is-valid' : 'is-invalid'" 
								                              			placeholder="Choose Package" 
								                              			label="name" 
								                                  		track-by="id" 
								                                  		:options="allPackagingPackages" 
								                                  		@input="validateFormInput('dispatched_package')"
								                              		>
								                                	</multiselect>
								                                	<div class="invalid-feedback">
																    	{{ errors.products[productIndex].dispatched_package }}
																    </div>
																</div>

																<div class="col-md-4">
																	<label for="inputFirstName">
																		Package Quantity
																	</label>
																	<input 
																		type="number" 
																		v-model.number="requiredProduct.dispatched_package_quantity" 
																		class="form-control" 
																		:class="!errors.products[productIndex].dispatched_package_quantity ? 'is-valid' : 'is-invalid'" 
																		placeholder="Package Total Quantity" 
																		@change="validateFormInput('dispatched_package_quantity')"
																	>
																	<div class="invalid-feedback">
																    	{{ errors.products[productIndex].dispatched_package_quantity }}
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
										class="form-group col-md-12" 
										v-if="Object.keys(singleDispatchData.requisition).length > 0"
									>
										<label for="inputFirstName">Short Note</label>
										<ckeditor 
			                              	class="form-control" 
			                              	:editor="editor" 
			                              	v-model="singleDispatchData.requisition.description" 
			                              	:disabled="true"
			                            >
		                              	</ckeditor>
									</div>
										
									<div class="col-md-12 card-footer">
							        	<div class="form-row">
											<div class="col-sm-12 text-right">
								          		<div class="text-danger small mb-1" v-show="!submitForm">
											  		Please input required fields
									          	</div>
								          	</div>
								          	<div class="col-md-12">
								          		<button 
									          		type="button" 
									          		class="btn btn-outline-secondary btn-sm btn-round float-left" 
									          		v-tooltip.bottom-end="'Previous'"
									          		v-on:click="step-=1" 
								          		>
							                    	<i class="fa fa-2x fa-angle-double-left" aria-hidden="true"></i>
							                  	</button>

								          		<button 
									          		type="button" 
									          		v-on:click="nextPage" 
									          		class="btn btn-outline-secondary btn-sm btn-round float-right" 
									          		v-tooltip.bottom-end="'Next'"
								          		>
							                    	<i class="fa fa-2x fa-angle-double-right" aria-hidden="true"></i>
							                  	</button>
								          	</div>
										</div>
									</div>
								</div>

								<div 
									class="row" 
									v-bind:key="'dispatch-modal-step-' + 3" 
									v-show="!loading && step==3"
								>
									<h2 class="mx-auto mb-4 lead">Released Addresses</h2>

									<div 
										class="col-md-12" 
										v-if="Object.keys(singleDispatchData.requisition).length > 0 && singleDispatchData.requisition.products && singleDispatchData.requisition.products.length"
									>
										<div 
											class="card"
											v-for="(requiredProduct, productIndex) in singleDispatchData.requisition.products" 
											:key="'required-product-index-' + productIndex"
										>
											<div class="card-header">
												{{ requiredProduct.product_name | capitalize }}
											</div>

											<div 
											class="card-body" 
											v-for="(requiredProductAddress, productAddressIndex) in requiredProduct.addresses" 
											:key="'required-product-index-' + productIndex + '-address-index' + productAddressIndex" 
											>
												<div class="form-row ml-5 mr-5">
													<div class="form-group col-md-12 text-center">
														<label for="inputFirstName">
															Released Space Type {{ productAddressIndex + 1 }}
														</label>
														<multiselect 
					                              			v-model="requiredProductAddress.type" 
					                              			class="form-control p-0 is-valid" 
					                                  		:options="['containers', 'shelves', 'units']" 
					                                  		:custom-label="nameWithCapitalized" 
					                                  		:required="true" 
					                                  		:allow-empty="false"
					                              			placeholder="Containers / Shelves / Units" 
					                                  		:disabled="true" 
					                              		>
					                                	</multiselect>
													</div>
												</div>

												<div 
													class="form-row" 
													v-if="requiredProductAddress.type=='containers'"
												>
													<div class="form-group col-md-12">
														<label for="inputFirstName">
															Released Containers
														</label>
														<multiselect 
					                              			v-model="requiredProduct.addresses[productAddressIndex].released_containers"
					                              			placeholder="Select Contaners" 
					                              			label="name" 
					                                  		track-by="id" 
					                                  		class="form-control p-0 is-valid" 
					                                  		:options="requiredProduct.addresses[productAddressIndex].containers" 
					                                  		:multiple="true" 
					                                  		:close-on-select="false" 
					                                  		:clear-on-select="false" 
					                                  		:preserve-search="true" 
					                                  		:disabled="(requiredProduct.available_quantity > requiredProduct.quantity && requiredProduct.addresses[productAddressIndex].containers.length < 2) || requiredProduct.available_quantity == requiredProduct.quantity"
					                              		>
					                                	</multiselect>
													</div>
												</div>

												<div 
													class="form-row" 
													v-if="requiredProductAddress.type=='shelves'"
												>
													<div class="form-group col-md-6">
														<label for="inputFirstName">
															Selected Container
														</label>
														<multiselect 
					                              			v-model="requiredProduct.addresses[productAddressIndex].container" 
					                              			placeholder="Parent Contaner" 
					                              			class="form-control p-0 is-valid" 
					                              			label="name" 
					                                  		track-by="id" 
					                                  		:options="[]" 
					                                  		:disabled="true"
					                              		>
					                                	</multiselect>
													</div>

													<div 
														class="form-group col-md-6" 
														v-if="requiredProduct.addresses[productAddressIndex].container"
													>
														<label for="inputFirstName">
															Released Shelves
														</label>
														<multiselect 
					                              			v-model="requiredProduct.addresses[productAddressIndex].container.released_shelves"
					                              			placeholder="Select Shelves" 
					                              			class="form-control p-0 is-valid" 
					                              			label="name" 
					                                  		track-by="id" 
					                                  		:options="requiredProduct.addresses[productAddressIndex].container.shelves" 
					                                  		:multiple="true" 
					                                  		:close-on-select="false" 
					                                  		:clear-on-select="false" 
					                                  		:preserve-search="true" 
					                                  		:disabled="(requiredProduct.available_quantity > requiredProduct.quantity && requiredProduct.addresses[productAddressIndex].container.shelves.length < 2 && requiredProduct.addresses.length < 2) || requiredProduct.available_quantity == requiredProduct.quantity"
					                              		>
					                                	</multiselect>
													</div>
												</div>

												<div class="form-row" v-if="requiredProductAddress.type=='units'">
													<div class="form-group col-md-4">
														<label for="inputFirstName">
															Selected Container
														</label>
														<multiselect 
					                              			v-model="requiredProduct.addresses[productAddressIndex].container"
					                              			placeholder="Parent Contaner" 
					                              			class="form-control p-0 is-valid" 
					                              			label="name" 
					                                  		track-by="id" 
					                                  		:options="[]" 
					                                  		:disabled="true"
					                              		>
					                                	</multiselect>
													</div>

													<div 
														class="form-group col-md-4" 
														v-if="requiredProduct.addresses[productAddressIndex].container"
													>
														<label for="inputFirstName">
															Selected Shelf
														</label>
														<multiselect 
					                              			v-model="requiredProduct.addresses[productAddressIndex].container.shelf"
					                              			placeholder="Parent Shelf" 
					                              			class="form-control p-0 is-valid" 
					                              			label="name" 
					                                  		track-by="id" 
					                                  		:options="[]" 
					                                  		:disabled="true"
					                              		>
					                                	</multiselect>
													</div>

													<div 
														class="form-group col-md-4" 
														v-if="requiredProduct.addresses[productAddressIndex].container && requiredProduct.addresses[productAddressIndex].container.shelf"
													>
														<label for="inputFirstName">
															Released Units
														</label>
														<multiselect 
					                              			v-model="requiredProduct.addresses[productAddressIndex].container.shelf.released_units"
					                              			placeholder="Select Units" 
					                              			class="form-control p-0 is-valid" 
					                              			label="name" 
					                                  		track-by="id" 
					                                  		:options="requiredProduct.addresses[productAddressIndex].container.shelf.units" 
					                                  		:multiple="true" 
					                                  		:close-on-select="false" 
					                                  		:clear-on-select="false" 
					                                  		:preserve-search="true" 
					                                  		:disabled="(requiredProduct.available_quantity > requiredProduct.quantity && requiredProduct.addresses[productAddressIndex].container.shelf.units.length < 2) || requiredProduct.available_quantity == requiredProduct.quantity"
					                              		>
					                                	</multiselect>
													</div>
												</div>
											</div>

											<div class="card-footer">
												<div class="form-row">
													<div class="form-group col-md-12">
														<button 
															type="button" 
															class="btn waves-effect waves-light hor-grd btn-grd-info btn-sm btn-block" 
															v-tooltip.bottom-end="'Remove Space'" 
															:disabled="requiredProduct.addresses.length==1" 
															v-show="requiredProduct.available_quantity > requiredProduct.quantity"
															@click="removeSpace(productIndex)"
														>
															Remove Space
														</button>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="col-md-12 card-footer">
							        	<div class="form-row">
											<div class="col-sm-12 text-right">
								          		<div class="text-danger small mb-1" v-show="!submitForm">
											  		Please input required fields
									          	</div>
								          	</div>
								          	<div class="col-md-12">
								          		<button 
									          		type="button" 
									          		class="btn btn-outline-secondary btn-sm btn-round float-left" 
									          		v-tooltip.bottom-end="'Previous'"
									          		v-on:click="step-=1" 
								          		>
							                    	<i class="fa fa-2x fa-angle-double-left" aria-hidden="true"></i>
							                  	</button>
								          		<button 
									          		type="button" 
									          		class="btn btn-outline-secondary btn-sm btn-round float-right" 
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
									v-bind:key="'dispatch-modal-step-' + 4" 
									v-show="!loading && step==4"
								>	
									<h2 class="mx-auto mb-4 lead">Deployment Details</h2>	
									
									<div class="form-group col-md-12 text-center">
										<span :class="[singleDispatchData.requisition.delivery ? 'badge-primary' : 'badge-info', 'badge']">{{ singleDispatchData.requisition.delivery ? 'Delivery Service' : 'Agent Service' }}</span>
									</div>

									<div 
										class="col-sm-12" 
										v-if="!singleDispatchData.requisition.delivery && singleDispatchData.requisition.agent"
									>
										<div class="form-row">
											<div class="form-group col-md-6">
												<label for="inputFirstName">Agent Name</label>
												<input type="text" 
													class="form-control" 
													v-model="singleDispatchData.requisition.agent.name" 
													placeholder="Agent Name" 
													readonly="true" 
												>
											</div>

											<div class="form-group col-md-6">
												<label for="inputFirstName">Agent Mobile</label>
												<input type="text" 
													class="form-control" 
													v-model="singleDispatchData.requisition.agent.mobile" 
													placeholder="Agent Mobile" 
													readonly="true" 
												>
											</div>

											<div class="form-group col-md-6">
												<label for="inputFirstName">Agent Code</label>
												<input type="text" 
													class="form-control" 
													v-model="singleDispatchData.requisition.agent.code" 
													placeholder="Agent Code" 
													readonly="true" 
												>
											</div>

											<div class="form-group col-md-6">
												<label for="inputFirstName">Collection Point</label>
												<ckeditor 
					                              	class="form-control" 
					                              	:editor="editor" 
					                              	v-model="singleDispatchData.agent.collection_point" 
			                                  		:class="!errors.agent.collection_point ? 'is-valid' : 'is-invalid'" 
			                                  		@blur="validateFormInput('collection_point')" 
					                            >
				                              	</ckeditor>
				                              	<div class="invalid-feedback">
											    	{{ errors.agent.collection_point }}
											    </div>
											</div>

											<!-- 
											<div class="form-group col-md-6">
												<label for="inputFirstName">Presence</label>
													
												<span :class="[singleDispatchData.requisition.agent.presence_confirmation ? 'badge-success' : 'badge-danger', 'badge']">
													{{ singleDispatchData.requisition.agent.presence_confirmation ? 'Present' : 'Not Yet' }}
												</span>
											</div>											
 											-->
										</div>

										<!-- 
										<div class="form-row d-flex">
											<div class="form-group col-md-6">
												<img class="img-fluid" 
													:src="singleDispatchData.agent.agent_receipt || ''"
													alt="agent_receipt" 
												>
											</div>

											<div class="form-group col-md-6  align-self-center">
												<div class="custom-file">
												    <input 
												    	type="file" 
												    	class="form-control custom-file-input" 
														:class="!errors.agent.agent_receipt  ? 'is-valid' : 'is-invalid'" 
											    	 	@change="onAgentReceiptChange" 
											    	 	accept="image/*"
												    >
												    <label class="custom-file-label">
												    	Agent Receipt...
												    </label>
												    <div class="invalid-feedback">
												    	{{ errors.agent.agent_receipt }}
												    </div>
											  	</div>
											</div>
										</div>
										-->
									</div>

									<div 
										class="col-sm-12" 
										v-if="singleDispatchData.requisition.delivery && ! singleDispatchData.requisition.agent"
									>
										<div class="form-row">
											<div class="form-group col-md-12">
												<label for="inputFirstName">Address</label>
												<ckeditor 
					                              	class="form-control" 
					                              	:editor="editor" 
					                              	v-model="singleDispatchData.requisition.delivery.address" 
					                              	:disabled="true" 
					                            >
				                              	</ckeditor>
											</div>
										</div>

										<div class="form-row d-flex">
											<div class="form-group col-md-6 text-center">
												<img class="img-fluid" 
													:src="singleDispatchData.delivery.delivery_receipt || ''"
													alt="delivery_receipt" 
												>
											</div>

											<div class="form-group col-md-6 align-self-center">
												<div class="custom-file">
												    <input type="file" 
												    	class="form-control custom-file-input" 
														:class="!errors.delivery.delivery_receipt  ? 'is-valid' : 'is-invalid'" 
											    	 	@change="onDeliveryReceiptChange" 
											    	 	accept="image/*"
												    >
												    <label class="custom-file-label">Delivery Receipt...</label>
												    <div class="invalid-feedback">
												    	{{ errors.delivery.delivery_receipt }}
												    </div>
											  	</div>
											</div>
										</div>

										<div class="form-row">
											<div class="form-group col-md-6">
												<label for="inputFirstName">
													Delivery Price
												</label>
												<input 
													type="number" 
													class="form-control" 
													v-model.number="singleDispatchData.delivery.delivery_price" 
													placeholder="Delivery Price" 
													:class="!errors.delivery.delivery_price ? 'is-valid' : 'is-invalid'" 
													@change="validateFormInput('delivery_price')" 
												>
												<div class="invalid-feedback">
											    	{{ errors.delivery.delivery_price }}
											    </div>
											</div>
										</div>
									</div>

									<div class="col-md-12 card-footer">
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
													v-on:click="(userHasPermissionTo('approve-dispatch') && singleDispatchData.requisition.products.some( requiredProduct => requiredProduct.available_quantity - requiredProduct.quantity > 0 )) ? step-=1 : step-=2"
												>
							                    	<i class="fa fa-2x fa-angle-double-left" aria-hidden="true"></i>
							                  	</button>
							                  	
							                  	<button 
								                  	type="button" 
								                  	class="btn btn-danger btn-sm btn-round" 
								                  	v-tooltip.bottom-end="'Cancel Requisition'" 
								                  	@click="openRequisitionCancelForm(singleDispatchData.requisition)"
							                  	>
													{{ singleDispatchData.requisition.status==0 ? 'Cancel Requisition' : singleDispatchData.requisition.status==1 && singleDispatchData.requisition.dispatch.has_approval==0 ? 'Cancel Dispatch' : '' }} 
												</button>

												<button 
													type="submit" 
													class="btn btn-primary btn-sm btn-round" 
													:disabled="!submitForm || nondispatchable || formSubmitted"
												>
													{{ singleDispatchData.requisition.status==0 ? 'Recommend Dispatch' : singleDispatchData.requisition.status==1 && singleDispatchData.requisition.dispatch.has_approval==0 ? 'Approve Dispatch' : '' }}
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

 		<!-- Requisition View Modal -->
		<div class="modal fade" id="requisition-view-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Requisition Details</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="modal-body">
						<div class="card">
							<div class="card-body">
								
								<ul class="nav nav-tabs tabs justify-content-center" role="tablist">
									<li class="nav-item">
										<a class="nav-link active" data-toggle="tab" href="#requisition-profile" role="tab">
											Requisition
										</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" data-toggle="tab" href="#requisition-product" role="tab">
											Product
										</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" data-toggle="tab" href="#requisition-dispatch" role="tab">
											Dispatch
										</a>
									</li>
								</ul>

								<div class="tab-content tabs card-block">
									<div class="tab-pane active" id="requisition-profile" role="tabpanel">	
										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Subject :
											</label>
											<label class="col-sm-6 col-form-label">
												{{ singleRequisitionData.subject | capitalize }}
											</label>
										</div>

										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">Status :</label>
											<label class="col-sm-6 col-form-label">
												<span :class="[singleRequisitionData.status==1 && singleRequisitionData.dispatch.has_approval==1 ? 'badge-success' : singleRequisitionData.status==1 && singleRequisitionData.dispatch.has_approval==0 ? 'badge-warning' : singleRequisitionData.status==0 ? 'badge-danger' : 'badge-default', 'badge']">
																				
													{{ singleRequisitionData.status==1 && singleRequisitionData.dispatch.has_approval==1 ? 'Dispatched' : singleRequisitionData.status==1 && singleRequisitionData.dispatch.has_approval==0 ? 'Recommended' : singleRequisitionData.status==0 ? 'Pending' : 'Cancelled' }}

												</span>
											</label>
										</div>

										<div class="form-row" v-if="singleRequisitionData.hasOwnProperty('cancellation_reason')">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">Cancellation Reason :</label>
											<label class="col-sm-6 col-form-label">
												<span v-html="singleRequisitionData.cancellation_reason"></span>
											</label>
										</div>

										<div 
											class="form-row" 
											v-if="singleRequisitionData.creator"
										>
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Created By :
											</label>
											<label class="col-sm-6 col-form-label">
												{{ singleRequisitionData.creator.user_name | capitalize }}
											</label>
										</div>

										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Requested on :
											</label>
											<label class="col-sm-6 col-form-label">
												{{ singleRequisitionData.created_at }}
											</label>
										</div>
									</div>

									<div class="tab-pane" id="requisition-product" role="tabpanel">
										<div 
											class="form-row" 
											v-if="singleRequisitionData.products && singleRequisitionData.products.length"
										>
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Product Detail :
											</label>
											<div class="col-sm-6">
												<div class="form-row">
													
													<div 
														class="col-md-12 ml-auto" 
														v-for="(requiredProduct, productIndex) in singleRequisitionData.products" 
														:key="'view-required-product-index-'  + productIndex + '-required-product-' + requiredProduct.id"
													>
														<div class="card">
															<div class="card-body">
																<div class="form-row">
																	<label class="col-sm-6 col-form-label font-weight-bold text-right">
																		Product Name :
																	</label>
																	<label class="col-sm-6 col-form-label">
																		{{ requiredProduct.product_name | capitalize }}
																	</label>
																</div>

																<div class="form-row">
																	<label class="col-sm-6 col-form-label font-weight-bold text-right">
																		Total Quantity :
																	</label>
																	<label class="col-sm-6 col-form-label">
																		{{ requiredProduct.quantity }} 
																		{{ requiredProduct.quantity_type }}
																	</label>
																</div>

																<div class="form-row" v-show="! requiredProduct.has_variations && requiredProduct.selected_stock_code">
																	<label class="col-sm-6 col-form-label font-weight-bold text-right">
																		Selected Stock Code :
																	</label>
																	<label class="col-sm-6 col-form-label">
																		{{ requiredProduct.selected_stock_code }} 
																	</label>
																</div>

																<div class="form-row" v-if="requiredProduct.has_serials && ! requiredProduct.has_variations && requiredProduct.hasOwnProperty('serials') && requiredProduct.serials.length">
																	<label class="col-sm-6 col-form-label font-weight-bold text-right">
																		Product Serials :
																	</label>
																	<label class="col-sm-6 col-form-label">
																		<span v-for="(productSerial, productSerialIndex) in requiredProduct.serials" :key="'required-product-' + productIndex + '-product-serial-index-' + productSerialIndex + '-product-serial-' + productSerial.serial.serial_no">

																			{{ productSerial.serial.serial_no }}

																			<span v-show="(productSerialIndex+1) < requiredProduct.serials.length">, </span>
																		</span>
																	</label>
																</div>

																<div class="form-row">
																	<label class="col-sm-6 col-form-label font-weight-bold text-right">
																		Variations :
																	</label>
																	<label class="col-sm-6 col-form-label">
																		<span :class="[requiredProduct.has_variations ? 'badge-success' : 'badge-danger', 'badge']">{{ requiredProduct.has_variations ? 'Yes' : 'No' }}
																		</span>
																	</label>
																</div>

																<div class="form-row" v-if="requiredProduct.has_variations && requiredProduct.variations">
																	<div 
																		class="col-sm-12" 
																		v-for="(productVariation, variationIndex) in requiredProduct.variations" 
																		:key="'required-product-variation-' + productVariation.id + variationIndex"
																	>
																		<div class="form-row">
																			<label class="col-sm-6 col-form-label font-weight-bold text-right">
																				 {{ productVariation.variation_name | capitalize }} :
																			</label>
																			<label class="col-sm-6 col-form-label">
																				{{ productVariation.quantity }}

																				{{ requiredProduct.quantity_type }}
																			</label>
																		</div>

																		<div class="form-row" v-show="productVariation.selected_stock_code">
																			<label class="col-sm-6 col-form-label font-weight-bold text-right">
																				Selected Stock Code :
																			</label>
																			<label class="col-sm-6 col-form-label">
																				{{ productVariation.selected_stock_code }} 
																			</label>
																		</div>

																		<div class="form-row" v-if="productVariation.has_serials && productVariation.serials.length">
																			<label class="col-sm-6 col-form-label font-weight-bold text-right">
																				{{ productVariation.variation_name | capitalize }} Serials :
																			</label>
																			<label class="col-sm-6 col-form-label">
																				<span v-for="(variationSerial, variationSerialIndex) in productVariation.serials" :key="'required-product-' + productIndex + '-variation-index-' + variationIndex + '-product-variation-serial-index-' + variationSerialIndex + '-product-variation-serial-' + variationSerial.serial.serial_no">

																					{{ variationSerial.serial.serial_no }}

																					<span v-show="(variationSerialIndex+1) < productVariation.serials.length">, </span>
																				</span>
																			</label>
																		</div>
																	</div>
																</div>

																<div class="form-row">
																	<label class="col-sm-6 col-form-label font-weight-bold text-right">
																		Packaging Service :
																	</label>
																	<label class="col-sm-6 col-form-label">
																		<span :class="[requiredProduct.packaging_service ? 'badge-success' : 'badge-danger', 'badge']">
																			{{ requiredProduct.packaging_service ? 'Yes' : 'NA' }}
																		</span>
																	</label>
																</div>

																<div class="form-row" v-if="requiredProduct.packaging_service && requiredProduct.hasOwnProperty('preferred_package') && requiredProduct.preferred_package">
																	<label class="col-sm-6 col-form-label font-weight-bold text-right">
																		Preferred Package :
																	</label>
																	<label class="col-sm-6 col-form-label">
																		{{ requiredProduct.preferred_package.name | capitalize }}
																	</label>
																</div>

																<div class="form-row" v-else-if="requiredProduct.hasOwnProperty('preferred_package') && !requiredProduct.preferred_package">
																	<label class="col-sm-6 col-form-label font-weight-bold text-right">
																		Preferred Package :
																	</label>
																	<label class="col-sm-6 col-form-label">
																		<span class="badge badge-info">
																			NA
																		</span>
																	</label>
																</div>

																<div class="form-row" v-if="requiredProduct.packaging_service && requiredProduct.hasOwnProperty('dispatched_package') && requiredProduct.dispatched_package">
																	<label class="col-sm-6 col-form-label font-weight-bold text-right">
																		Dispatched Package :
																	</label>
																	<label class="col-sm-6 col-form-label">
																		{{ requiredProduct.dispatched_package.name | capitalize }}
																	</label>
																</div>

																<div class="form-row" v-if="requiredProduct.packaging_service && requiredProduct.hasOwnProperty('dispatched_package') && requiredProduct.dispatched_package">
																	<label class="col-sm-6 col-form-label font-weight-bold text-right">
																		Package Quantity :
																	</label>
																	<label class="col-sm-6 col-form-label">
																		{{ requiredProduct.dispatched_package.quantity }}
																	</label>
																</div>
															</div>
														</div>
													</div>

												</div>
											</div>
										</div>

										<div class="form-row" v-if="singleRequisitionData.description">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Product Note :
											</label>
											<label class="col-sm-6 col-form-label">
												<span v-html="singleRequisitionData.description"></span>
											</label>
										</div>
									</div>

									<div class="tab-pane" id="requisition-dispatch" role="tabpanel">
										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Service :
											</label>
											<label class="col-sm-6 col-form-label">
												<span :class="[singleRequisitionData.delivery ? 'badge-success' : 'badge-info', 'badge']">{{ singleRequisitionData.delivery ? 'Delivery Service' : 'Agent Service' }}</span>
											</label>
										</div>

										<div class="form-row" v-if="singleRequisitionData.delivery">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Delivery Address :
											</label>
											<label class="col-sm-6 col-form-label">
												<span v-html="singleRequisitionData.delivery.address"></span>
											</label>
										</div>

										<div class="form-row" v-if="singleRequisitionData.status==1 && singleRequisitionData.hasOwnProperty('dispatch') && singleRequisitionData.dispatch.hasOwnProperty('delivery')">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Delivery Receipt :
											</label>
											<label class="col-sm-6 col-form-label">
												<img 
													class="img-fluid" 
													:src="singleRequisitionData.dispatch.delivery.receipt_preview || ''"
													alt="delivery receipt" 
												>
											</label>
										</div>

										<div class="form-row" v-show="singleRequisitionData.agent">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Agent Name :
											</label>
											<label class="col-sm-6 col-form-label">
												{{ singleRequisitionData.agent ? singleRequisitionData.agent.name : 'NA' }}
											</label>
										</div>

										<div class="form-row" v-show="singleRequisitionData.agent">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Agent Mobile :
											</label>
											<label class="col-sm-6 col-form-label">
												{{ singleRequisitionData.agent ? singleRequisitionData.agent.mobile : 'NA' }}
											</label>
										</div>

										<div class="form-row" v-show="singleRequisitionData.agent">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Agent Code :
											</label>
											<label class="col-sm-6 col-form-label">
												{{ singleRequisitionData.agent ? singleRequisitionData.agent.code : 'NA' }}
											</label>
										</div>

										<!-- 
										<div class="form-row" v-show="singleRequisitionData.agent">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Agent Presence :
											</label>
											<label class="col-sm-6 col-form-label">
												
												<span :class="[(singleRequisitionData.agent && singleRequisitionData.agent.presence_confirmation) ? 'badge-success' : 'badge-danger', 'badge']">
													{{ (singleRequisitionData.agent && singleRequisitionData.agent.presence_confirmation) ? 'Present' : 'Not Yet' }}
												</span>

											</label>
										</div>
										-->

										<div class="form-row" v-if="singleRequisitionData.status!=0 && singleRequisitionData.hasOwnProperty('updater')">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												{{ singleRequisitionData.status==1 ? 'Recommended' : singleRequisitionData.status==-1 ? 'Cancelled' : '' }} on :
											</label>
											<label class="col-sm-6 col-form-label">
												{{ singleRequisitionData.updated_at }}
											</label>
										</div>

										<div class="form-row" v-if="singleRequisitionData.status!=0 && singleRequisitionData.hasOwnProperty('updater')">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												{{ singleRequisitionData.status==1 ? 'Recommended' : singleRequisitionData.status==-1 ? 'Cancelled' : '' }} By :
											</label>
											<label class="col-sm-6 col-form-label">
												{{ singleRequisitionData.updater.user_name | capitalize }}
											</label>
										</div>

										<div class="form-row" v-if="singleRequisitionData.status==1 && singleRequisitionData.hasOwnProperty('dispatch') && singleRequisitionData.dispatch.has_approval != 0 && singleRequisitionData.dispatch.hasOwnProperty('updater')">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												{{ singleRequisitionData.dispatch.has_approval==1 ? 'Approved' : 'Cancelled' }}  On :
											</label>
											<label class="col-sm-6 col-form-label">
												{{ singleRequisitionData.dispatch.updated_at }}
											</label>
										</div>

										<div class="form-row" v-if="singleRequisitionData.status==1 && singleRequisitionData.hasOwnProperty('dispatch') && singleRequisitionData.dispatch.has_approval != 0 && singleRequisitionData.dispatch.hasOwnProperty('updater')">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												{{ singleRequisitionData.dispatch.has_approval==1 ? 'Approved' : 'Cancelled' }}  By :
											</label>
											<label class="col-sm-6 col-form-label">
												{{ singleRequisitionData.dispatch.updater.user_name | capitalize }}
											</label>
										</div>

										<div class="form-row" v-if="singleRequisitionData.status==1 && singleRequisitionData.hasOwnProperty('dispatch') && singleRequisitionData.dispatch.has_approval==1">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Received :
											</label>
											<label class="col-sm-6 col-form-label">
												<span :class="[!unconfirmed(singleRequisitionData) ? 'badge-success' : 'badge-danger', 'badge']">
													{{ !unconfirmed(singleRequisitionData) ? 'Confirmed' : 'Not Confirmed' }}
												</span>
											</label>
										</div>

										<div class="form-row" v-if="singleRequisitionData.status==1 && singleRequisitionData.hasOwnProperty('dispatch') && singleRequisitionData.dispatch.hasOwnProperty('agent')">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Collection Point :
											</label>
											<label class="col-sm-6 col-form-label">
												<span v-html="singleRequisitionData.dispatch.agent.collection_point"></span>
											</label>
										</div>

										<!-- <div class="form-row" v-else>
											<label class="col-sm-12 col-form-label text-center">
												<span :class="[singleRequisitionData.status==1 ? 'badge-warning' : singleRequisitionData.status==0 ? 'badge-danger' : 'badge-secondary', 'badge']">
													{{ singleRequisitionData.status==1 ? 'Recommended' : singleRequisitionData.status==0 ? 'Not Recommended Yet' : 'Cancelled' }}
												</span>
											</label>
										</div> -->
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
							v-tooltip.bottom-end="'Print'"  
							@click="print"
						>
							Print
						</button>
					</div>
				</div>
			</div>
		</div>

		<!-- Cancel Requisitions -->
		<div class="modal fade" id="cancel-confirmation-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" v-if="userHasPermissionTo('update-requisition')">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<form 
						class="form-horizontal" 
						v-on:submit.prevent="cancelRequisition()" 
						autocomplete="off"
					>
						
						<input type="hidden" name="_token" :value="csrf">

						<div class="modal-header bg-danger">
							<h5 class="modal-title" id="exampleModalLongTitle">Confirm Cancellation</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
							</button>
						</div>

						<div class="modal-body text-center">
							<h4 class="text-danger">
								Want to cancel {{ singleRequisitionData.subject }} ?
							</h4>

							<h6 class="sub-heading text-secondary form-group">
								Remember : You can not retrieve this action !
							</h6>

							<div 
								class="form-group col-sm-12 text-left" 
								v-if="errors.hasOwnProperty('cancellation')"
							>
								<label for="inputFirstName">Cancellation Reason</label>
								<ckeditor 
	                              	class="form-control" 
	                              	:editor="editor" 
	                              	v-model="singleRequisitionData.cancellation_reason" 
                              		:class="!errors.cancellation.reason ? 'is-valid' : 'is-invalid'" 
                              		@blur="validateFormInput('cancellation_reason')" 
	                            >
                              	</ckeditor>
                              	<div class="invalid-feedback">
							    	{{ errors.cancellation.reason }}
							    </div>
							</div>
						</div>

						<div class="modal-footer">
							<button type="button" class="btn btn-secondary mr-auto" data-dismiss="modal">Close</button>
							<button type="submit" class="btn btn-danger">Cancel Requisition</button>
						</div>	
					</form>
				</div>
			</div>
		</div>

		<!-- Printing Section -->
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
								{{ general_settings.app_name | capitalize }} Requisition Invoice
							</h5>
						</div>

						<div class="col-6">
							<qr-code 
							:text="singleRequisitionData.subject || ''"
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
									Subject :
								</label>
								<label class="col-6 col-form-label">
									{{ singleRequisitionData.subject | capitalize }}
								</label>
							</div>

							<div class="form-row">
								<label class="col-6 col-form-label font-weight-bold">Status :</label>

								<label class="col-6 col-form-label">									
									<span :class="[singleRequisitionData.status==1 && singleRequisitionData.dispatch.has_approval==1 ? 'badge-success' : singleRequisitionData.status==1 && singleRequisitionData.dispatch.has_approval==0 ? 'badge-warning' : singleRequisitionData.status==0 ? 'badge-danger' : 'badge-default', 'badge']">
																	
										{{ singleRequisitionData.status==1 && singleRequisitionData.dispatch.has_approval==1 ? 'Dispatched' : singleRequisitionData.status==1 && singleRequisitionData.dispatch.has_approval==0 ? 'Recommended' : singleRequisitionData.status==0 ? 'Pending' : 'Cancelled' }}

									</span>	
								</label>
							</div>

							<div 
								class="form-row" 
								v-if="singleRequisitionData.creator"
							>
								<label class="col-6 col-form-label font-weight-bold">
									Created By :
								</label>
								<label class="col-6 col-form-label">
									{{ singleRequisitionData.creator.user_name | capitalize }}
								</label>
							</div>

							<div class="form-row">
								<label class="col-6 col-form-label font-weight-bold">
									Requested on :
								</label>

								<label class="col-6 col-form-label">
									{{ singleRequisitionData.created_at }}
								</label>
							</div>

							<div class="form-row" v-if="singleRequisitionData.status!=0 && singleRequisitionData.hasOwnProperty('updater')">
								<label class="col-6 col-form-label font-weight-bold">
									{{ singleRequisitionData.status==1 ? 'Recommended' : singleRequisitionData.status==-1 ? 'Cancelled' : '' }} on :
								</label>
								<label class="col-6 col-form-label">
									{{ singleRequisitionData.updated_at }}
								</label>
							</div>

							<div class="form-row" v-if="singleRequisitionData.status!=0 && singleRequisitionData.hasOwnProperty('updater')">
								<label class="col-6 col-form-label font-weight-bold">
									{{ singleRequisitionData.status==1 ? 'Recommended' : singleRequisitionData.status==-1 ? 'Cancelled' : '' }} By :
								</label>
								<label class="col-6 col-form-label">
									{{ singleRequisitionData.updater.user_name | capitalize }}
								</label>
							</div>

							<div class="form-row" v-if="singleRequisitionData.status==1 && singleRequisitionData.hasOwnProperty('dispatch') && singleRequisitionData.dispatch.has_approval != 0 && singleRequisitionData.dispatch.hasOwnProperty('updater')">
								<label class="col-6 col-form-label font-weight-bold">
									{{ singleRequisitionData.dispatch.has_approval==1 ? 'Approved' : 'Cancelled' }}  On :
								</label>
								<label class="col-6 col-form-label">
									{{ singleRequisitionData.dispatch.updated_at }}
								</label>
							</div>

							<div class="form-row" v-if="singleRequisitionData.status==1 && singleRequisitionData.hasOwnProperty('dispatch') && singleRequisitionData.dispatch.has_approval != 0 && singleRequisitionData.dispatch.hasOwnProperty('updater')">
								<label class="col-6 col-form-label font-weight-bold">
									{{ singleRequisitionData.dispatch.has_approval==1 ? 'Approved' : 'Cancelled' }}  By :
								</label>
								<label class="col-6 col-form-label">
									{{ singleRequisitionData.dispatch.updater.user_name | capitalize }}
								</label>
							</div>

							<div class="form-row" v-if="singleRequisitionData.status==1 && singleRequisitionData.hasOwnProperty('dispatch') && singleRequisitionData.dispatch.has_approval==1">
								<label class="col-6 col-form-label font-weight-bold">
									Received :
								</label>
								<label class="col-6 col-form-label">
									<span :class="[!unconfirmed(singleRequisitionData) ? 'badge-success' : 'badge-danger', 'badge']">
										{{ !unconfirmed(singleRequisitionData) ? 'Confirmed' : 'Not Confirmed' }}
									</span>
								</label>
							</div>

							<div class="form-row" v-if="singleRequisitionData.status==1 && singleRequisitionData.hasOwnProperty('dispatch') && singleRequisitionData.dispatch.hasOwnProperty('agent')">
								<label class="col-6 col-form-label font-weight-bold">
									Collection Point :
								</label>
								<label class="col-6 col-form-label">
									<span v-html="singleRequisitionData.dispatch.agent.collection_point"></span>
								</label>
							</div>
						</div>

						<div class="col-6">
							<div class="form-row">
								<label class="col-6 col-form-label font-weight-bold text-right">
									Service :
								</label>
								<label class="col-6 col-form-label">
									<span :class="[singleRequisitionData.delivery ? 'badge-success' : 'badge-info', 'badge']">{{ singleRequisitionData.delivery ? 'Delivery Service' : 'Agent Service' }}</span>
								</label>
							</div>
							
							<div class="form-row" v-show="singleRequisitionData.agent">
								<label class="col-6 col-form-label font-weight-bold text-right">
									Agent Name :
								</label>
								<label class="col-6 col-form-label">
									{{ singleRequisitionData.agent ? singleRequisitionData.agent.name : 'NA' }}
								</label>
							</div>

							<div class="form-row" v-show="singleRequisitionData.agent">
								<label class="col-6 col-form-label font-weight-bold text-right">
									Agent Mobile :
								</label>
								<label class="col-6 col-form-label">
									{{ singleRequisitionData.agent ? singleRequisitionData.agent.mobile : 'NA' }}
								</label>
							</div>

							<div class="form-row" v-show="singleRequisitionData.agent">
								<label class="col-6 col-form-label font-weight-bold text-right">
									Agent Code :
								</label>
								<label class="col-6 col-form-label">
									{{ singleRequisitionData.agent ? singleRequisitionData.agent.code : 'NA' }}
								</label>
							</div>

							<div class="form-row" v-if="singleRequisitionData.hasOwnProperty('cancellation_reason')">
								<label class="col-6 col-form-label font-weight-bold text-right">Cancellation Reason :</label>

								<label class="col-6 col-form-label">
									<span v-html="singleRequisitionData.cancellation_reason"></span>
								</label>
							</div>

							<div class="form-row" v-if="singleRequisitionData.delivery">
								<label class="col-6 col-form-label font-weight-bold text-right">
									Delivery Address :
								</label>
								<label class="col-6 col-form-label">
									<span v-html="singleRequisitionData.delivery.address"></span>
								</label>
							</div>

							<div class="form-row" v-if="singleRequisitionData.status==1 && singleRequisitionData.hasOwnProperty('dispatch') && singleRequisitionData.dispatch.hasOwnProperty('delivery')">
								<label class="col-6 col-form-label font-weight-bold text-right">
									Delivery Receipt :
								</label>
								<label class="col-6 col-form-label">
									<img 
										class="img-fluid" 
										:src="singleRequisitionData.dispatch.delivery.receipt_preview || ''"
										alt="delivery receipt" 
									>
								</label>
							</div>
						</div>
					</div>

					<div 
						class="form-row" 
						v-if="singleRequisitionData.products && singleRequisitionData.products.length"
					>
						<label class="col-12 col-form-label font-weight-bold">Products :</label>
						
						<div class="col-12">
							<table class="table table-striped table-bordered nowrap text-center">
								<thead>
									<tr>
										<th>Name</th>
										<th>Qty</th>
										<th>Serials</th>
										<th>Variations</th>
										<th>Packaging</th>
									</tr>
								</thead>

								<tbody>
									<div 
										v-for="(requiredProduct, requiredProductIndex) in singleRequisitionData.products" 
										:key="'printing-required-product-index-' + requiredProductIndex + '-required-product-' + requiredProduct.id"
									>
										<div >
											<tr>
												<td>
													{{ requiredProduct.product_name | capitalize }}
												</td>

												<td>{{ requiredProduct.quantity || '--' }}</td>

												<td>
													<div v-if="requiredProduct.has_serials && ! requiredProduct.has_variations && requiredProduct.hasOwnProperty('serials') && requiredProduct.serials.length"
													>
														<span v-for="(productSerial, productSerialIndex) in requiredProduct.serials" :key="'required-product-' + requiredProductIndex + '-product-serial-index-' + productSerialIndex + '-product-serial-' + productSerial.serial.serial_no">

															{{ productSerial.serial.serial_no }}

															<span v-show="(productSerialIndex+1) < requiredProduct.serials.length">, </span>
														</span>
													</div>

													<div class="form-row" v-if="requiredProduct.has_serials && requiredProduct.has_variations && requiredProduct.variations">
														<div 
															class="col-sm-12" 
															v-for="(productVariation, variationIndex) in requiredProduct.variations" 
															:key="'printing-required-product-variation-' + productVariation.id + variationIndex"
														>
															<div class="form-row" v-if="productVariation.has_serials && productVariation.serials.length">
																<label class="col-6 col-form-label font-weight-bold text-right">
																	{{ productVariation.variation_name | capitalize }} Serials :
																</label>
																<label class="col-6 col-form-label">
																	<span v-for="(variationSerial, variationSerialIndex) in productVariation.serials" :key="'required-product-' + requiredProductIndex + '-variation-index-' + variationIndex + '-product-variation-serial-index-' + variationSerialIndex + '-product-variation-serial-' + variationSerial.serial.serial_no">

																		{{ variationSerial.serial.serial_no }}

																		<span v-show="(variationSerialIndex+1) < productVariation.serials.length">, </span>
																	</span>
																</label>
															</div>
														</div>
													</div>
												</td>

												<td>
													<span :class="[requiredProduct.has_variations ? 'badge-success' : 'badge-danger', 'badge']">{{ requiredProduct.has_variations ? 'Yes' : 'No' }}
													</span>

													<div class="form-row" v-if="requiredProduct.has_variations && requiredProduct.variations">
														<div 
															class="col-sm-12" 
															v-for="(productVariation, variationIndex) in requiredProduct.variations" 
															:key="'required-product-variation-' + productVariation.id + variationIndex"
														>
															<div class="form-row">
																<label class="col-6 col-form-label font-weight-bold text-right">
																	 {{ productVariation.variation_name | capitalize }} :
																</label>
																<label class="col-6 col-form-label">
																	{{ productVariation.quantity }}
																</label>
															</div>
														</div>
													</div>
												</td>

												<td>
													<span :class="[requiredProduct.packaging_service ? 'badge-success' : 'badge-danger', 'badge']">
														{{ requiredProduct.packaging_service ? 'Yes' : 'NA' }}
													</span>

													<div class="form-row" v-show="requiredProduct.packaging_service">
														<label class="col-6 col-form-label font-weight-bold text-right">
															Packaging Service :
														</label>
														<label class="col-6 col-form-label">
															<span :class="[requiredProduct.packaging_service ? 'badge-success' : 'badge-danger', 'badge']">
																{{ requiredProduct.packaging_service ? 'Yes' : 'NA' }}
															</span>
														</label>
													</div>

													<div class="form-row" v-if="requiredProduct.packaging_service && requiredProduct.hasOwnProperty('preferred_package')">
														<label class="col-6 col-form-label font-weight-bold text-right">
															Preferred Package :
														</label>
														<label class="col-6 col-form-label">
															{{ requiredProduct.preferred_package ? requiredProduct.preferred_package.name : '--' | capitalize }}
														</label>
													</div>

													<div class="form-row" v-if="requiredProduct.packaging_service && requiredProduct.hasOwnProperty('dispatched_package') && requiredProduct.dispatched_package">
														<label class="col-6 col-form-label font-weight-bold text-right">
															Dispatched Package :
														</label>
														<label class="col-6 col-form-label">
															{{ requiredProduct.dispatched_package.name | capitalize }}
														</label>
													</div>

													<div class="form-row" v-if="requiredProduct.packaging_service && requiredProduct.hasOwnProperty('dispatched_package') && requiredProduct.dispatched_package">
														<label class="col-6 col-form-label font-weight-bold text-right">
															Package Quantity :
														</label>
														<label class="col-6 col-form-label">
															{{ requiredProduct.dispatched_package.quantity }}
														</label>
													</div>
												</td>
											</tr>
										</div>
										<!-- <td></td> -->
									</div>
								</tbody>
							</table>							
						</div>	
					</div>

					<!-- 
					<div class="form-row" v-if="singleRequisitionData.description">
						<label class="col-6 col-form-label font-weight-bold text-right">
							Product Note :
						</label>
						<label class="col-6 col-form-label">
							<span v-html="singleRequisitionData.description"></span>
						</label>
					</div> 
					-->
				</div>
			</div>
		</div>
		<!-- Printing Section -->
	</div>

</template>

<script type="text/javascript">

	import axios from 'axios';
	// import JsonExcel from "vue-json-excel";
	import Multiselect from 'vue-multiselect';
	import CKEditor from '@ckeditor/ckeditor5-vue';
	import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
	// import DatePicker from 'v-calendar/lib/components/date-picker.umd';

    let singleRequisitionData = {

		products : [
			{
				// product : {},
				// total_quantity : 0
			}
		],

		agent : {},

		delivery : {},
				
    };

    let singleDispatchData = {

		requisition : {},

		// delivery : {},

		// agent : {}
				
    };

	export default {

	    components: { 
	    	// vDatePicker : DatePicker,
	    	// downloadExcel : JsonExcel, 
			multiselect : Multiselect,
			ckeditor: CKEditor.component,
		},

		props: {

			productId:{
				type: Number,
				required: true,
			},
			merchantId:{
				type: Number,
				required: true,
			},
			merchantProduct:{
				type: Object,
				required: true,
			},

		},

	    data() {

	        return {

	        	step : 1,
	        	error : '',
    			perPage : 10,
	        	loading : false,
	        	
	        	currentTab : 'pending',
	        	
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
	        	
	        	editor: ClassicEditor,

	        	submitForm : true,
	        	formSubmitted : false,
	        	createRequisition : true,

	        	requisitionsToShow : [],
	        	allFetchedRequisitions : [],

	        	// availableRequisitions : [],
	        	
	        	allPackagingPackages : [],

	        	// availableProducts : [],

	        	pagination: {
		        	current_page: 1
		      	},

		      	// allMerchants : [],
		      	// merchantAllProducts : [],
	        	merchantAllAgents : [],

	        	singleDispatchData : singleDispatchData,
	        	singleRequisitionData : singleRequisitionData,

		   		errors : {
		   			
		   			// subject : '',
					// description : '',
					// products : [],
					
					products : [
						
						{
							// product_id : '',
							// product_quantity : '',
							// variations_total_quantity : '',
							variation_serials : [],
							variation_quantities : [],
							variation_stock_codes : [],
						}

					],

					agent : {},

					delivery : {}

				},

				dataToExport: {

					"Subject": {
						field: "subject",
						callback: (subject) => {
							if (subject) {
								return this.$options.filters.capitalize(subject);
							}
							else{
								return 'No Subject'
							}
						},
					},

					"Requested On": 'created_at',

					"Products": {
						field: "products",
						callback: (products) => {

							if (products) {
								
								var infosToReturn = '';

								products.forEach(
					
									(requiredProduct, requiredProductIndex) => {

										infosToReturn += "Product " + (requiredProductIndex+1) + " : " + this.$options.filters.capitalize(requiredProduct.product_name) + ",\n";

										infosToReturn += "Qty : " + requiredProduct.quantity + "\n";

										if (requiredProduct.hasOwnProperty('variations') && requiredProduct.variations.length) {

											requiredProduct.variations.forEach(
						
												(requiredProductVariation, requiredProductVariationIndex) => {

													infosToReturn +=  "Selected Variation: " + this.$options.filters.capitalize(requiredProductVariation.variation_name) + ",\n";	

													infosToReturn +=  "Qty: " + requiredProductVariation.quantity + "\n";					

												}
												
											);

										}

										infosToReturn += "\n";

									}
									
								);

								return infosToReturn;

							}
							else {
								return 'No Products.'
							}

						},
					},
					
					"Service": {
						field: "agent",
						callback: (agent) => {
							if (agent) {
								return `Agent Service.
										Agent: ${agent.name}, 
										Mobile: ${agent.mobile}, 
										Code: ${agent.code}`;
							}
							else{
								return 'Delivery Service.'
							}
						},
					},

					"Status": {
						callback: (object) => {
							
							if (object.status==1) {
								return "Recommended.\n" + (object.updater ? (object.updater.first_name + ' ' + object.updater.last_name + ' (' + object.updated_at + ').') : '');
							}
							else if (object.status==-1) {
								return "Cancelled.\n" + (object.updater ? (object.updater.first_name + ' ' + object.updater.last_name + ' (' + object.updated_at + ').') : '');
							}
							else {
								return 'Pending.';
							}

						},
					},

					"Approval": {
						field: "dispatch",
						callback: (dispatch) => {
							if (dispatch) {
								return (dispatch.has_approval==1 ? 'Approved.' : 'Cancelled.') + "\n" + dispatch.updater.first_name + ' ' + dispatch.updater.last_name + '(' + dispatch.updated_at + ').';
							}
							else {
								return 'NA'
							}
						},
					},

					"Collection Point": {
						field: "dispatch",
						callback: (dispatch) => {
							if (dispatch && dispatch.hasOwnProperty('agent')) {
								return dispatch.agent.collection_point;
							}
							else {
								return 'NA'
							}
						},
					},

					"Received": {
						field: "dispatch",
						callback: (dispatch) => {
							if (dispatch) {
								return (dispatch.hasOwnProperty('delivery') && dispatch.delivery.receiving_confirmation==1) ? 'Received.' : (dispatch.hasOwnProperty('agent') && dispatch.agent.receiving_confirmation==1) ?  'Received.' : 'Not Confirmed.';
							}
							else {
								return 'NA'
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
					windowTitle: 'Requisition Details' 

				},

				general_settings : JSON.parse(window.localStorage.getItem('general_settings')),

	            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

	        }

		},
		
		created(){
			
			this.subscribeToChannels();
			this.fetchAllRequisitions();
			this.fetchAllPackagingPackages();
			// this.fetchAvailableRequisitions();
			// this.fetchAllMerchants();
			this.fetchMerchantAllAgents();
			this.removeEngagedSerials();

		},

		computed: {
			
			// a computed getter
			nondispatchable: function () {
				
				if (this.singleDispatchData.requisition.hasOwnProperty('products') && this.singleDispatchData.requisition.products.length) {

					return this.singleDispatchData.requisition.products.some(
						
						requiredProduct => 

							this.singleDispatchData.requisition.products.some( requiredProduct => requiredProduct.available_quantity < requiredProduct.quantity ) || (requiredProduct.has_serials && ! requiredProduct.has_variations && requiredProduct.serials.some( productSerial => productSerial.serial.has_dispatched )) || (requiredProduct.has_serials && requiredProduct.has_variations && requiredProduct.variations.some(productVariation => productVariation.serials.some( variationSerial => variationSerial.serial.has_dispatched)))
												
					);

				}

				return false;

			},

			currentDate: function() {

				let date = new Date();
				return date.getFullYear() + '-' +  (date.getMonth() + 1) + '-' + date.getDate();

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

			fetchAllRequisitions() {

				/*
				if (! this.userHasPermissionTo('view-requisition-index')) {

					this.error = 'You do not have permission to view requisition-list';
					return;

				}
				*/

				this.error = '';
				this.loading = true;
				this.searchAttributes.search = '';
				this.allFetchedRequisitions = [];
				
				axios
					.get('/api/requisitions/' + this.perPage + "?page=" + this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							this.allFetchedRequisitions = response.data;
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
			/*
			fetchAvailableRequisitions() {

				this.error = '';
				this.loading = true;
				this.searchAttributes.search = '';
				this.availableRequisitions = [];
				
				axios
					.get('/api/requisitions/')
					.then(response => {
						if (response.status == 200) {
							this.availableRequisitions = response.data.data;
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
			/*
			fetchAllMerchants() {

				if (! this.userHasPermissionTo('view-merchant-index')) {

					this.error = 'You do not have permission to view merchant-list';
					return;

				}

				this.error = '';
				this.loading = true;
				this.searchAttributes.search = '';
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
			/*
			fetchMerchantAllProducts() {
				
				if (! this.userHasPermissionTo('view-merchant-product-index')) {

					this.error = 'You do not have permission to view merchant product list';
					return;

				}

				// this.query = '';
				this.error = '';
				this.loading = true;
				this.merchantAllProducts = [];
				
				axios
					.get('/api/merchant-products/' + this.merchantId)
					.then(response => {
						if (response.status == 200) {
							this.merchantAllProducts = response.data.data;
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
			fetchMerchantAllAgents() {
				
				if (! this.userHasPermissionTo('view-merchant-index')) {

					this.error = 'You do not have permission to view merchant agent list';
					return;

				}

				// this.query = '';
				this.error = '';
				this.loading = true;
				this.merchantAllAgents = [];
				
				axios
					.get('/api/merchant-agents/' + this.merchantId)
					.then(response => {
						if (response.status == 200) {
							this.merchantAllAgents = Object.values(response.data);
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
			fetchAllPackagingPackages() {

				/*
				if (! this.userHasPermissionTo('view-logistic-asset-index')) {

					this.error = 'You do not have permission to view packaging packages';
					return;

				}
				*/
				
				this.error = '';
				this.loading = true;
				// this.searchAttributes.search = '';
				this.allPackagingPackages = [];
				
				axios
					.get('/api/packaging-packages/')
					.then(response => {
						if (response.status == 200) {
							this.allPackagingPackages = response.data;
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
			showContentCreateForm() {

				this.step = 1;
	        	this.submitForm = true;
	        	this.formSubmitted = false;
	        	this.createRequisition = true;
	        	
				this.singleRequisitionData = {

					merchant : this.merchantProduct.merchant,
					merchant_id : this.merchantId,
					
					products : [
						{
							product : this.merchantProduct,
							total_quantity : 0
						}
					],

					agent : {},

					delivery : {},
							
			    };

				this.errors = {
					
	        		// subject : '',
					// description : '',
					
					products : [
						
						{
							// product_id : '',
							// product_quantity : '',
							// variations_total_quantity : ''
							variation_serials : [],
							variation_quantities : [],
							variation_stock_codes : [],
						}
						
					],

					agent : {},

					delivery : {},

				};

				$('#requisition-createOrEdit-modal').modal('show');
				
			},
			openRequisitionCancelForm(object) {

				this.submitForm = true;
				this.formSubmitted = false;

				this.singleRequisitionData = { ...object };

				this.errors.cancellation = {

				};

				$('#cancel-confirmation-modal').modal('show');

			},
			showDispatchRecommendationForm(object) {

				this.step = 1;
	        	this.submitForm = true;
	        	this.formSubmitted = false;
	        	this.createRequisition = false;
	        	
				this.configureErrorObject(object);

				this.singleRequisitionData = { ...object }; // just for highlightng current row

				this.singleDispatchData = {

					requisition : { ...object },

					// delivery : {},

					// agent : {}
							
			    };

				if (object.delivery && ! object.agent) {

					this.singleDispatchData.delivery = {};
					this.$delete(this.singleDispatchData, 'agent');

					this.errors.delivery = {};
					this.$delete(this.errors, 'agent');

				}
				else if (! object.delivery && object.agent) {

					this.singleDispatchData.agent = {};
					this.$delete(this.singleDispatchData, 'delivery');

					this.errors.agent = {};
					this.$delete(this.errors, 'delivery');

				}

				$('#dispatch-createOrEdit-modal').modal('show');

			},
			showDispatchApprovalForm(object) {
				// this.singleRequisitionData = { ...object };
				
				this.step = 1;
	        	this.submitForm = true;
	        	this.formSubmitted = false;
	        	this.createRequisition = false;
	        	
			/*
				this.singleDispatchData = {

					requisition : { ...object },

					delivery : {},

					// agent : {}
							
			    };

				this.errors = {
					// products : [],
					delivery : {},
					// agent : {},
				};
			*/

				this.configureErrorObject(object);

				this.singleDispatchData = {

					requisition : { ...object },
					
					// delivery : {},

					// agent : {}
							
			    };

				if (object.delivery && ! object.agent) {

					this.singleDispatchData.delivery = object.dispatch.delivery;
					this.singleDispatchData.delivery.delivery_receipt = object.dispatch.delivery.receipt_preview;
					this.$delete(this.singleDispatchData, 'agent');

					this.errors.delivery = {};
					this.$delete(this.errors, 'agent');

				}
				else if (! object.delivery && object.agent) {

					this.singleDispatchData.agent = object.dispatch.agent;
					this.$delete(this.singleDispatchData, 'delivery');

					this.errors.agent = {};
					this.$delete(this.errors, 'delivery');

				}

				$('#dispatch-createOrEdit-modal').modal('show');

			},
			storeRequisition() {
				
				if (!this.verifyRequisitionInput()) {
					this.submitForm = false;
					return;
				}

				this.formSubmitted = true;

				axios
					.post('/requisitions/' + this.perPage, this.singleRequisitionData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("New requisition has been stored", "Success");
							
							this.pagination.current_page = 1; 
							this.allFetchedRequisitions = response.data;
							this.searchAttributes.search !== '' ? this.searchData() : this.showSelectedTabProducts();

							$('#requisition-createOrEdit-modal').modal('hide');
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
						// this.fetchMerchantAllProducts();
						this.removeEngagedSerials();
					});

			},
			removeEngagedSerials() {

				if (! this.merchantProduct.has_variations && this.merchantProduct.has_serials) {

					if (this.singleRequisitionData.products.length && this.singleRequisitionData.products[0].serials) {

						this.merchantProduct.serials = this.merchantProduct.serials.filter(
							productSerial => ! this.singleRequisitionData.products[0].serials.find(serialToRemove => serialToRemove.serial_no == productSerial.serial_no)
						);

					}
					else {

						this.merchantProduct.serials = this.merchantProduct.serials.filter(
							productSerial => ! productSerial.has_requisitions && ! productSerial.has_dispatched
						);

					}

				}
				else if (this.merchantProduct.has_variations && this.merchantProduct.has_serials) {

					this.merchantProduct.variations.forEach(

						(productVariation, productVariationIndex) => {

							if (this.singleRequisitionData.products.length && this.singleRequisitionData.products[0].variations) {

								variationToSearch = this.singleRequisitionData.products[0].variations.find(variationToSearch => variationToSearch.required_quantity && variationToSearch.id == productVariation.id);

								if (variationToSearch) {

									productVariation.serials = productVariation.serials.filter(
										variationSerialToStore => ! variationToSearch.serials.find(serialToRemove => serialToRemove.serial_no == variationSerialToStore.serial_no)
									);						

								}

							}
							else {

								productVariation.serials = productVariation.serials.filter(
									variationSerialToStore => ! variationSerialToStore.has_requisitions && ! variationSerialToStore.has_dispatched
								);

							}

						}

					);

				}

			},
			makeDispatch() {
				
				if (!this.verifyDispatchInput()) {

					this.submitForm = false;
					return;

				}

				this.formSubmitted = true;

				axios
					.post('/dispatches/' + this.perPage, this.singleDispatchData)
					.then(response => {
						if (response.status == 200) {

							this.userHasPermissionTo('approve-dispatch') ? this.$toastr.s("Requisition has been dispatched", "Success") : this.$toastr.i("Requisition has been recommended", "Success");

							this.pagination.current_page = 1; 
							this.allFetchedRequisitions = response.data;
							this.searchAttributes.search !== '' ? this.searchData() : this.showSelectedTabProducts();
							
							$('#dispatch-createOrEdit-modal').modal('hide');
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
						// this.fetchAllRequisitions();
					});

			},
			cancelRequisition() {

				if (! this.singleRequisitionData.hasOwnProperty('cancellation_reason') || ! this.singleRequisitionData.cancellation_reason) {

					this.submitForm = false;
					this.errors.cancellation.reason = 'Cancellation reason is required';
					return;

				}

				this.formSubmitted = true;
				this.$delete(this.errors, 'cancellation');

				axios
					.put('/requisitions/' + this.singleRequisitionData.id + '/' + this.perPage, this.singleRequisitionData)
					.then(response => {
						if (response.status == 200) {

							this.$toastr.s("Requisition has been cancelled", "Success");

							this.pagination.current_page = 1; 
							this.allFetchedRequisitions = response.data;
							this.searchAttributes.search !== '' ? this.searchData() : this.showSelectedTabProducts();
							
							$('#cancel-confirmation-modal').modal('hide');
							$('#dispatch-createOrEdit-modal').modal('hide');
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
						// this.fetchAllRequisitions();
					});

			},
			searchData(emitedObject=false) {

				if (emitedObject) {
					this.searchAttributes=emitedObject;
				}

				this.error = '';
				this.allFetchedRequisitions = [];
				// this.pagination.current_page = 1;
				
				axios
				.post('/search-requisitions/' + this.perPage, this.searchAttributes)
				.then(response => {
					this.allFetchedRequisitions = response.data;
					this.requisitionsToShow = this.allFetchedRequisitions.all.data;
					this.pagination = this.allFetchedRequisitions.all;
				})
				.catch(e => {
					this.error = e.toString();
				});

			},
			verifyDispatchInput() {

				// this.validateFormInput('delivery_address');
				this.validateFormInput('delivery_price');
				this.validateFormInput('delivery_receipt');
				this.validateFormInput('collection_point');
				// this.validateFormInput('agent_receipt');

				if (this.errors.constructor === Object && Object.keys(this.errors).length < 3 && (! this.errors.hasOwnProperty('delivery') || Object.keys(this.errors.delivery).length == 0) && (! this.errors.hasOwnProperty('agent') || Object.keys(this.errors.agent).length == 0)) {

					// console.log('verified');
					return true;
				
				}

				return false;
			},
			verifyRequisitionInput() {

				this.validateFormInput('agent_name');
				this.validateFormInput('agent_mobile');
				this.validateFormInput('agent_code');
				this.validateFormInput('delivery_address');

				if (this.errors.constructor === Object && Object.keys(this.errors.agent).length == 0 && Object.keys(this.errors.delivery).length == 0) {

					return true;
				
				}

				return false;
			},
			configureErrorObject(object) {

				this.errors = {
					
					products : [],
					
					agent : {},
					delivery : {},
					// cancellation : {}
				
				};

				object.products.forEach(
				
					(requiredProduct, requiredProductIndex) => {

						// if (requiredProduct.packaging_service) {
							this.errors.products.push(
								{
									variation_serials : [],
									variation_quantities : [],
									variation_stock_codes : [],
								}
							);
						// }

					}
					
				);

			},
			changeNumberContents() {
				
				this.pagination.current_page = 1;

				if (this.searchAttributes.search === '') {
					this.fetchAllRequisitions();
				}
				else {
					this.searchData();
				}
				
    		},
    		showContentDetails(object) {		
				this.singleRequisitionData = { ...object };
				$('#requisition-view-modal').modal('show');
			},
			showSelectedTabProducts() {
				
				if (this.currentTab=='pending') {
					this.requisitionsToShow = this.allFetchedRequisitions.pending.data;
					this.pagination = this.allFetchedRequisitions.pending;
				}
				else if (this.currentTab=='cancelled') {
					this.requisitionsToShow = this.allFetchedRequisitions.cancelled.data;
					this.pagination = this.allFetchedRequisitions.cancelled;
				}
				else {
					this.requisitionsToShow = this.allFetchedRequisitions.dispatched.data;
					this.pagination = this.allFetchedRequisitions.dispatched;
				}

			},
			unconfirmed(object) {

				if (object.status==1 && object.dispatch.has_approval==1 && ((object.dispatch.hasOwnProperty('agent') && ! object.dispatch.agent.receiving_confirmation) || (object.dispatch.hasOwnProperty('delivery') && ! object.dispatch.delivery.receiving_confirmation))) {

					return true; 	// not confirmed

				}

				return false;  // confirmed
				
			},
			/*
			unapproved(object) {

				if (object.status==1 && object.dispatch.has_approval==0) {

					return true; 	// not confirmed

				}

				return false;  // confirmed
				
			},
			*/
			errorInArray(array = []) {
				
				if (array.length) {

					return array.some(
						product => Object.keys(product).length > 3 || product.variation_quantities.some(productVariation => productVariation != null) || (product.variation_serials.some(productVariation => productVariation != null)) || product.variation_stock_codes.some(variationStockCode => variationStockCode != null)
					);

				}

				return false;
			},
			nextPage() {
				
				if (this.createRequisition) {

					if (this.step==1) {
						this.validateFormInput('subject');
						// this.validateFormInput('merchant_id');
						this.validateFormInput('description');
					}
					else if (this.step == 2) {
						// this.validateFormInput('product_id');
						this.validateFormInput('product_quantity');
						this.validateFormInput('variations_total_quantity');
					}
					else if (this.step == 3) {
						this.validateFormInput('product_serials');
					}


					if (! this.errors.subject && !this.errorInArray(this.errors.products) && this.step < 4) {
						
						// this.fetchMerchantAllAgents();
						// this.fetchMerchantAllProducts();

						if (this.step != 2 || this.requisitionHasSerialProduct()) {

							this.step += 1;
						
						}
						else {

							this.step += 2;

						}	

						this.submitForm = true;
					
					}
					else {
						this.submitForm = false;
					}

				}
				else {

					/*
					if (this.step==1) {
						this.validateFormInput('requisition_id');
					}
					*/

					if (/*! this.errors.requisition_id && */this.step < 4) {
						
						if (this.step==2) {

							this.validateFormInput('product_stock_code');
							this.validateFormInput('variation_stock_codes');
							this.validateFormInput('dispatched_package');
							this.validateFormInput('dispatched_package_quantity');

							if (this.errorInArray(this.errors.products)) {

								this.submitForm = false;
								return;
							}

							if (! this.userHasPermissionTo('approve-dispatch')) {
								this.step += 2;
							}
							else {
								const productRemains = this.singleDispatchData.requisition.products.some(
									requiredProduct => (requiredProduct.available_quantity - requiredProduct.quantity) > 0
								);

								if (productRemains) {
									this.step += 1;
								}
								else {
									this.setProductReleasedAddresses();
									this.step += 2;
								}
							}

						}
						else{
							this.step += 1;
						}

						this.submitForm = true;
						
					}
					else {
						this.submitForm = false;
					}

				}

			},
			showPendingContents() {
				this.currentTab = 'pending';
				this.showSelectedTabProducts();
			},
			showDispatchedContents() {
				this.currentTab = 'dispatched';
				this.showSelectedTabProducts();
			},
			showCancelledContents() {
				this.currentTab = 'cancelled';
				this.showSelectedTabProducts();
			},
			removeSpace(productIndex) {	
				
				if (this.singleDispatchData.requisition.products.length > productIndex && this.singleDispatchData.requisition.products[productIndex].addresses.length > 1) {
						
					this.singleDispatchData.requisition.products[productIndex].addresses.pop();
				
				}

			},
			setProductReleasedAddresses() {

				this.singleDispatchData.requisition.products.forEach(
							
					(requiredProduct, productIndex) => {

						if (requiredProduct.available_quantity == requiredProduct.quantity) {


							requiredProduct.addresses.forEach(
				
								(requiredProductAddress, productAddressIndex) => {

									if (requiredProductAddress.type=='containers') {

										requiredProduct.addresses[productAddressIndex].released_containers = requiredProduct.addresses[productAddressIndex].containers;

									}
									else if (requiredProductAddress.type=='shelves') {

										requiredProduct.addresses[productAddressIndex].container.released_shelves = requiredProduct.addresses[productAddressIndex].container.shelves;

									}
									else if (requiredProductAddress.type=='units') {

										requiredProduct.addresses[productAddressIndex].container.shelf.released_units = requiredProduct.addresses[productAddressIndex].container.shelf.units;

									}

								}
								
							);

						}

					}
					
				);

			},
			print() {

				// this.printingStyles.name = `${ this.singleRequisitionData.subject } Details`;
				this.printingStyles.windowTitle = this.$options.filters.capitalize(`${ this.singleRequisitionData.subject } Details`);

				this.$htmlToPaper('sectionToPrint', this.printingStyles);

				$('#requisition-view-modal').modal('hide');

			},
			nameWithCapitalized (name) {
				if (!name) return ''
			    name = name.toString()
			    return name.charAt(0).toUpperCase() + name.slice(1)
		    },
			objectNameWithCapitalized ({ subject, product_name, variation_name, first_name, last_name, user_name, name, variation, serial_no, stock_code, available_quantity }) {
		      	if (subject) {
				    subject = subject.toString()
				    return subject.charAt(0).toUpperCase() + subject.slice(1)
		      	}
		      	else if (product_name) {
				    product_name = product_name.toString()
				    return product_name.charAt(0).toUpperCase() + product_name.slice(1)
		      	}
		      	else if (variation_name) {
				    variation_name = variation_name.toString()
				    return variation_name.charAt(0).toUpperCase() + variation_name.slice(1)
		      	}
		      	else if (first_name || last_name || user_name) {

		      		if (first_name) {
		      			first_name = first_name.toString();
		      		}
		      		if (last_name) {
		      			last_name = last_name.toString();
		      		}
		      		if (user_name) {
		      			user_name = user_name.toString();
		      		}

		      		return ((first_name ? (first_name.charAt(0).toUpperCase() + first_name.slice(1)) : '') + ' ' + (last_name ? (last_name.charAt(0).toUpperCase() + last_name.slice(1)) : '') + ' ' + (user_name ? (user_name.charAt(0).toUpperCase() + user_name.slice(1)) : ''));

		      	}
		      	else if (name) {
				    name = name.toString()
				    return name.charAt(0).toUpperCase() + name.slice(1)
		      	}
		      	else if (variation) {
		      		name = variation.name.toString()
				    return name.charAt(0).toUpperCase() + name.slice(1)
		      	}
		      	else if (serial_no) {
		      		serial_no = serial_no.toString()
				    return serial_no.charAt(0).toUpperCase() + serial_no.slice(1)
		      	}
		      	else if (stock_code && available_quantity) {
		      		stock_code = stock_code.toString()
				    return stock_code.charAt(0).toUpperCase() + stock_code.slice(1) + '(' + available_quantity + ')'
		      	}
		      	else 
		      		return ''
		    },
		    subscribeToChannels() {

		    	Echo.private(`new-requisition`)
			    .listen('NewRequisitionMade', (e) => {
			        
			        this.$toastr.i("New requisition arrives", "Info");

			        // this.allFetchedRequisitions.pending?.data.push(e);

			        if (this.allFetchedRequisitions.pending && this.allFetchedRequisitions.pending.data && ! this.allFetchedRequisitions.pending.data.some(pendingRequisition=>pendingRequisition.id==e.id)) {

			        	this.allFetchedRequisitions.pending.data.push(e);

			        }

			    });

			    if (this.userHasPermissionTo('view-dispatch-index')) {

				    Echo.private(`product-received`)
				    .listen('ProductReceived', (e) => {				        

				        this.$toastr.s("Dispatched product has been received", "Success");
				    	
				    	let index = this.allFetchedRequisitions.dispatched?.data.findIndex(requisition => requisition.id === e.id && requisition.merchant_id === e.merchant_id);

				    	if (index > -1) {

			    			this.$set(this.allFetchedRequisitions.dispatched.data, index, e);
				    	
				    	}
				    	else{

				    		let index2 = this.allFetchedRequisitions.pending?.data.findIndex(requisition => requisition.id === e.id && requisition.merchant_id === e.merchant_id);

				    		if (index2 > -1) {

				    			this.allFetchedRequisitions.pending.data.splice(index2, 1);
				    			this.allFetchedRequisitions.dispatched.data.push(e);
					    	
					    	}

				    	}

				    });	    

				}

		    },
		    /*
		    onAgentReceiptChange(evnt) {

				let files = evnt.target.files || evnt.dataTransfer.files;

                // Only process image files.
		      	if (files.length && files[0].type.match('image.*')) {
                	this.submitForm = true;
                	this.errors.agent = {};
                	this.createImage(files[0]);
                	// this.$delete(this.errors.agent, 'agent_receipt');
		      	}
		      	else{
		      		this.errors.agent.agent_receipt = 'Receipt is required';
		      	}

		      	evnt.target.value = '';
		      	return;

			},
			*/
			onDeliveryReceiptChange(evnt) {

				let files = evnt.target.files || evnt.dataTransfer.files;

                // Only process image files.
		      	if (files.length && files[0].type.match('image.*')) {
                	this.submitForm = true;
                	this.$delete(this.errors.delivery, 'delivery_receipt');
                	this.createImage(files[0]);
                	// this.createImage(files[0], 'delivery_receipt');
		      	}
		      	else{
		      		this.errors.delivery.delivery_receipt = 'Receipt is required';
		      	}

		      	evnt.target.value = '';
		      	return;

			},
			createImage(file) {
                let reader = new FileReader();
                
                reader.onload = (evnt) => {

                	// if (previewName === 'delivery_receipt') {

                		this.singleDispatchData.delivery.delivery_receipt = evnt.target.result;
                	// }
                	// else {

                 		// this.singleDispatchData.agent.agent_receipt = evnt.target.result;
                	
                	// }
                
                };

                reader.readAsDataURL(file);
            },
            /*
            setRequisitionMerchant() {

            	if (Object.keys(this.singleRequisitionData.merchant).length > 0) {

            		this.singleRequisitionData.merchant_id = this.singleRequisitionData.merchant.id;

            	}

            },
            */
            /*
            setRequiredProduct(index) {
				if (this.singleRequisitionData.products[index].product && Object.keys(this.singleRequisitionData.products[index].product).length > 0) {
					this.singleRequisitionData.products[index].id = this.singleRequisitionData.products[index].product.id;
				}
			},
			*/
            setRequisitionAgent() {
				if (! this.singleRequisitionData.delivery_service) {
					this.singleRequisitionData.agent = {};
					this.$delete(this.singleRequisitionData, 'delivery');
				}
				else {
					this.singleRequisitionData.delivery = {};
					this.$delete(this.singleRequisitionData, 'agent');
				}
			},
			setPackagingService(productIndex) {
				
				if (this.singleRequisitionData.hasOwnProperty('products') && this.singleRequisitionData.products.length && ! this.singleRequisitionData.products[productIndex].packaging_service) {

					this.singleRequisitionData.products[productIndex].preferred_package = {};


				}

			},
			/*
			addMoreProduct() {
				
				if (this.singleRequisitionData.products.length < this.merchantAllProducts.length) {

					this.singleRequisitionData.products.push({ product : {}, total_quantity : 0 });
					
					this.errors.products.push({
						variation_serials : [],
						variation_quantities : [],
						variation_stock_codes : [],
					});

				}

			},
			removeProduct() {
					
				if (this.singleRequisitionData.products.length > 1) {

					this.singleRequisitionData.products.pop();
					this.errors.products.pop();
				
				}
				
			},
			*/
			customPackagingPackageName ({ name, price, description, preview }) {
		      	if (name && price) {
		      		return name.charAt(0).toUpperCase() + name.slice(1) + ' - Each Price (BDT) ' + price 
		    	}
		    	return '';
		    },
			/*
			unconfirmed(object) {

				if (object.status==1 && object.dispatch.has_approval==1 && ((object.dispatch.hasOwnProperty('agent') && !object.dispatch.agent.receiving_confirmation) || (object.dispatch.hasOwnProperty('delivery') && !object.dispatch.delivery.receiving_confirmation))) {

					return true; 	// not confirmed

				}

				return false;  // confirmed
				
			},
			*/
			resetMerchantAgent() {

				if (!this.singleRequisitionData.previous_agent || Object.keys(this.singleRequisitionData.previous_agent).length == 0) {

					this.singleRequisitionData.agent = {};

				}
				else {

					this.singleRequisitionData.agent = {...this.singleRequisitionData.previous_agent};

				}

			},
			requisitionHasSerialProduct() {

				return this.singleRequisitionData.products.some(
					currentProduct => currentProduct.product.has_serials
				);

			},
			resetSelectedAgent() {

    			this.singleRequisitionData.previous_agent = {};

    		},
			validateFormInput (formInputName) {

				this.submitForm = false;

				switch(formInputName) {

					/*
					case 'requisition_id' :

						if (Object.keys(this.singleDispatchData.requisition).length==0) {
							this.errors.requisition_id = 'Requisition is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'requisition_id');
						}

						break;
					*/

					case 'delivery_price' :

						if (this.singleDispatchData.requisition.hasOwnProperty('delivery') && this.singleDispatchData.requisition.delivery) {

							if (Object.keys(this.singleDispatchData.delivery).length==0 || !this.singleDispatchData.delivery.delivery_price || this.singleDispatchData.delivery.delivery_price < 1) {
								this.errors.delivery.delivery_price = 'Price is required';
							}
							else{
								this.submitForm = true;
								this.$delete(this.errors.delivery, 'delivery_price');
							}

						}

						else {

							this.submitForm = true;
							// this.errors.delivery = {};
							this.$delete(this.errors, 'delivery');

						}

						break;

					case 'delivery_receipt' :

						if (this.singleDispatchData.requisition.hasOwnProperty('delivery') && this.singleDispatchData.requisition.delivery) {

							if (Object.keys(this.singleDispatchData.delivery).length==0 || !this.singleDispatchData.delivery.delivery_receipt) {
								this.errors.delivery.delivery_receipt = 'Receipt is required';
							}
							else{
								this.submitForm = true;
								this.$delete(this.errors.delivery, 'delivery_receipt');
							}

						}

						else {

							this.submitForm = true;
							// this.errors.delivery = {};
							this.$delete(this.errors, 'delivery');

						}


						break;

					case 'collection_point' :

						if (! this.singleDispatchData.requisition.delivery && this.singleDispatchData.requisition.agent) {

							if (! this.singleDispatchData.hasOwnProperty('agent') || ! this.singleDispatchData.agent.hasOwnProperty('collection_point') || ! this.singleDispatchData.agent.collection_point) {
								this.errors.agent.collection_point = 'Collection point is required';
							}
							else{
								this.submitForm = true;
								this.$delete(this.errors.agent, 'collection_point');
							}

						}

						else {

							this.submitForm = true;
							this.$delete(this.errors, 'agent');

						}

						break;

					case 'dispatched_package' :

						if (this.singleDispatchData.hasOwnProperty('requisition') && this.singleDispatchData.requisition.hasOwnProperty('products') && this.singleDispatchData.requisition.products && this.singleDispatchData.requisition.products.length) {

							this.singleDispatchData.requisition.products.forEach(
				
								(requiredProduct, requiredProductIndex) => {

									if (requiredProduct.packaging_service && (! requiredProduct.hasOwnProperty('dispatched_package') || ! requiredProduct.dispatched_package || Object.keys(requiredProduct.dispatched_package).length==0)) {

										this.errors.products[requiredProductIndex].dispatched_package = 'Package name is required';

									}
									else {

										this.$delete(this.errors.products[requiredProductIndex], 'dispatched_package');

									}

								}
								
							);

							if (! this.errors.products.some(requiredProductError => Object.keys(requiredProductError).length > 0)) {

								this.submitForm = true;
								
							}

						}

						else {

							this.submitForm = true;
							this.$delete(this.errors, 'products');

						}

						break;

					case 'dispatched_package_quantity' :

						if (this.singleDispatchData.hasOwnProperty('requisition') && this.singleDispatchData.requisition.hasOwnProperty('products') && this.singleDispatchData.requisition.products && this.singleDispatchData.requisition.products.length) {

							this.singleDispatchData.requisition.products.forEach(
				
								(requiredProduct, requiredProductIndex) => {

									if (requiredProduct.packaging_service && (! requiredProduct.hasOwnProperty('dispatched_package_quantity') || requiredProduct.dispatched_package_quantity < 1)) {

										this.errors.products[requiredProductIndex].dispatched_package_quantity = 'Package quantity is required';

									}
									else {

										this.$delete(this.errors.products[requiredProductIndex], 'dispatched_package_quantity');

									}

								}
								
							);

							if (! this.errors.products.some(requiredProductError => Object.keys(requiredProductError).length > 0)) {

								this.submitForm = true;
								
							}

						}

						else {

							this.submitForm = true;
							this.$delete(this.errors, 'products');

						}

						break;

					/*
					case 'agent_receipt' :

						if (this.singleDispatchData.requisition.hasOwnProperty('agent')) {

							if (Object.keys(this.singleDispatchData.agent).length==0 || !this.singleDispatchData.agent.agent_receipt) {
								this.errors.agent.agent_receipt = 'Receipt is required';
							}
							else{
								this.submitForm = true;
								this.$delete(this.errors.agent, 'agent_receipt');
							}

						}

						else {

							this.submitForm = true;
							this.errors.agent = {};

						}


						break;
					*/
				
					/*
					case 'merchant_id' :

						if (! this.singleRequisitionData.merchant || Object.keys(this.singleRequisitionData.merchant).length==0) {
							this.errors.merchant_id = 'Merchant is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'merchant_id');
						}

						break;
					*/
				
					case 'subject' :

						if (!this.singleRequisitionData.subject) {
							this.errors.subject = 'Subject is required';
						}
						else if (!this.singleRequisitionData.subject.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.subject = 'No special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'subject');
						}

						break;
					/*
					case 'description' :

						if (this.singleRequisitionData.description && !this.singleRequisitionData.description.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.description = 'No special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'description');
						}

						break;
					*/

					/*
					case 'product_id' :

						this.singleRequisitionData.products.forEach(
							
							(requiredProduct, productIndex) => {

								if (! requiredProduct.id || ! requiredProduct.product || Object.keys(requiredProduct.product).length==0) {
									this.errors.products[productIndex].product_id = 'Product is required';
								}
								else if (this.singleRequisitionData.products.filter(current => current.id == requiredProduct.id).length > 1) {

									this.errors.products[productIndex].product_id = 'Same product is selected';

								}
								else{
									// this.errors.products[productIndex].product_id = null;
									this.$delete(this.errors.products[productIndex], 'product_id');
								}

							}
						);

						if (!this.errorInArray(this.errors.products)) {
							this.submitForm = true;
						}

						break;
					*/

					case 'product_quantity' :

						this.singleRequisitionData.products.forEach(
							
							(requiredProduct, productIndex) => {

								if (!requiredProduct.total_quantity || requiredProduct.total_quantity < 1) {
									this.errors.products[productIndex].product_quantity = 'Quantity is required';
								}
								else if (requiredProduct.total_quantity > (requiredProduct.product.available_quantity - requiredProduct.product.requested_quantity)) {
									this.errors.products[productIndex].product_quantity = 'Quantity is more than available (max : ' + (requiredProduct.product.available_quantity - requiredProduct.product.requested_quantity) + ').';
								}
								else{
									// this.errors.products[productIndex].product_quantity = null;
									this.$delete(this.errors.products[productIndex], 'product_quantity');
								}

							}

						);

						this.validateFormInput('variations_total_quantity');
								
						if (!this.errorInArray(this.errors.products)) {
							this.submitForm = true;
						}

						break;

					case 'variations_total_quantity' :

						this.singleRequisitionData.products.forEach(

							(requiredProduct, productIndex) => {

								if (requiredProduct.product && requiredProduct.product.has_variations) {

									let variationTotalQuantity = 0;

									requiredProduct.product.variations.forEach(current => variationTotalQuantity += current.required_quantity ?? 0);

									// console.log(variationTotalQuantity);

									if (requiredProduct.total_quantity != variationTotalQuantity) {
										this.errors.products[productIndex].variations_total_quantity = 'Total quantity should be equal to variations quantity';
									}
									else{

										requiredProduct.product.variations.forEach(

											(productVariation, variationIndex) => {

												if (productVariation.required_quantity < 0) {

													this.errors.products[productIndex].variation_quantities[variationIndex] = 'Quantity cant be negative';

												}
												else if (productVariation.required_quantity > 0 && (productVariation.required_quantity > (productVariation.available_quantity - productVariation.requested_quantity))) {

													this.errors.products[productIndex].variation_quantities[variationIndex] = 'Quantity is more than available (max : ' + (productVariation.available_quantity - productVariation.requested_quantity) + ').';

												}
												else {

													this.errors.products[productIndex].variation_quantities[variationIndex] = null;

													// this.errors.products[productIndex].variation_quantities.splice(variationIndex, 1);
													
												}

											}

										);
										
										// this.submitForm = true;
										this.$delete(this.errors.products[productIndex], 'variations_total_quantity');

									}

								}
								else {

									// this.submitForm = true;
									this.errors.products[productIndex].variation_quantities = [];
									this.$delete(this.errors.products[productIndex], 'variations_total_quantity');

								}

							}

						);

						if (!this.errorInArray(this.errors.products)) {
							this.submitForm = true;
						}

						break;

					case 'product_serials' :

						this.singleRequisitionData.products.forEach(
							
							(requiredProduct, productIndex) => {

								if (requiredProduct.product.has_serials && ! requiredProduct.product.has_variations && requiredProduct.total_quantity > 0 && (! requiredProduct.hasOwnProperty('serials') || requiredProduct.serials.length != requiredProduct.total_quantity)) {

									this.errors.products[productIndex].product_serials = 'Product serial is required';
								}
								else if (requiredProduct.product.has_serials &&  requiredProduct.product.has_variations && requiredProduct.total_quantity > 0) {
									
									this.$delete(this.errors.products[productIndex], 'product_serials');

									this.$delete(this.singleRequisitionData.products[productIndex], 'serials');

									requiredProduct.product.variations.forEach(
										(requiredProductVariation, variationIndex) => {

											if (requiredProductVariation.required_quantity > 0 && (! requiredProductVariation.hasOwnProperty('required_serials') || requiredProductVariation.required_serials.length != requiredProductVariation.required_quantity)) {

												this.errors.products[productIndex].variation_serials[variationIndex] = 'Variation serial is required';
											}
											else {
												
												// this.errors.products[productIndex].variation_serials[variationIndex] = null;

												this.$set(this.errors.products[productIndex].variation_serials, variationIndex, null);
											}

										}
									);

								}
								else{
									this.errors.products[productIndex].variation_serials = [];
									this.$delete(this.errors.products[productIndex], 'product_serials');
								}

							}
							
						);

						if (!this.errorInArray(this.errors.products)) {
							this.submitForm = true;
						}

						break;

					case 'agent_name' :

						if (!this.singleRequisitionData.delivery_service) {
							if (!this.singleRequisitionData.agent || !this.singleRequisitionData.agent.name) {
								this.errors.agent.agent_name = 'Agent name is required';
							}
							else if (!this.singleRequisitionData.agent.name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
								this.errors.agent.agent_name = 'No special character';
							}
							else{
								this.submitForm = true;
								this.$delete(this.errors.agent, 'agent_name');
							}
						}
						else {
							this.submitForm = true;
							this.errors.agent = {};
						}


						break;

					case 'agent_mobile' :

						if (!this.singleRequisitionData.delivery_service) {
							
							if (!this.singleRequisitionData.agent || !this.singleRequisitionData.agent.mobile) {
								this.errors.agent.agent_mobile = 'Agent mobile is required';
							}
							else if (!this.singleRequisitionData.agent.mobile.match(/\+?(88)?0?1[123456789][0-9]{8}\b/g)) {
								this.errors.agent.agent_mobile = 'Invalid mobile number';
							}
							else{
								this.submitForm = true;
								this.$delete(this.errors.agent, 'agent_mobile');
							}

						}
						else {
							this.submitForm = true;
							this.errors.agent = {};
						}

						break;

					case 'agent_code' :

						if (!this.singleRequisitionData.delivery_service) {
							
							if (!this.singleRequisitionData.agent || !this.singleRequisitionData.agent.code) {
								this.errors.agent.agent_code = 'Agent code is required';
							}
							else if (this.singleRequisitionData.agent.code.length < 4 || this.singleRequisitionData.agent.code.length > 8) {
								this.errors.agent.agent_code = 'Code length should be between 4 to 8';
							}
							else{
								this.submitForm = true;
								this.$delete(this.errors.agent, 'agent_code');
							}

						}
						else {
							this.submitForm = true;
							this.errors.agent = {};
						}

						break;

					case 'delivery_address' :

						if (this.singleRequisitionData.delivery_service) {

							// console.log('delivery_address');

							if (!this.singleRequisitionData.delivery || !this.singleRequisitionData.delivery.address) {
								this.errors.delivery.delivery_address = 'Delivery address is required';
							}
							else{
								this.submitForm = true;
								this.$delete(this.errors.delivery, 'delivery_address');
							}

						}
						else {
							this.submitForm = true;
							this.errors.delivery = {};
						}

						break;

					case 'product_stock_code' : 

						if (this.singleDispatchData.requisition && this.singleDispatchData.requisition.products && this.singleDispatchData.requisition.products.length) {

							this.singleDispatchData.requisition.products.forEach(
							
								(requiredProduct, requiredProductIndex) => {

									if (! requiredProduct.selected_stock || ! Object.keys(requiredProduct.selected_stock).length > 0) {

										// this.errors.products[requiredProductIndex].product_stock_code = 'Stock code is required';
										
										this.$set(this.errors.products[requiredProductIndex], 'product_stock_code', 'Stock code is required');

									}
									else {

										this.$delete(this.errors.products[requiredProductIndex], 'product_stock_code');

									}				

								}
								
							);

						}

						if (!this.errorInArray(this.errors.products)) {
							this.submitForm = true;
						}

						break;

					case 'variation_stock_codes' : 

						if (this.singleDispatchData.requisition && this.singleDispatchData.requisition.products && this.singleDispatchData.requisition.products.length) {

							this.singleDispatchData.requisition.products.forEach(
							
								(requiredProduct, requiredProductIndex) => {

									if (requiredProduct.has_variations && requiredProduct.variations.length) {
										
										this.$delete(this.errors.products[requiredProductIndex], 'product_stock_code');
										this.$set(this.errors.products[requiredProductIndex], 'variation_stock_codes', []);

										requiredProduct.variations.forEach(
								
											(requiredProductVariation, requiredProductVariationIndex) => {

												if (! requiredProductVariation.selected_stock || ! Object.keys(requiredProductVariation.selected_stock).length > 0) {
													
													this.$set(this.errors.products[requiredProductIndex].variation_stock_codes, requiredProductVariationIndex, 'Stock code is required');

												}
												else {

													this.$set(this.errors.products[requiredProductIndex].variation_stock_codes, requiredProductVariationIndex, null);

												}

											}
										);

									}
									else {

										// this.$delete(this.errors.products[requiredProductIndex], 'variation_stock_codes');
										// this.errors.products[requiredProductIndex].variation_stock_codes = [];
										this.$set(this.errors.products[requiredProductIndex], 'variation_stock_codes', []);

									}
				

								}
								
							);

						}

						if (!this.errorInArray(this.errors.products)) {
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