
<template>

	<div class="pcoded-content">

		<breadcrumb 
			:icon="'fa fa-truck'"
			:title="'requisitions'" 
			:message="'All your requisitions'"
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
											  	<!-- 
											  	<search-and-addition-option 
											  		:query="query" 
											  		:caller-page="'requisition'" 
											  		
											  		@showContentCreateForm="showContentCreateForm" 
											  		@searchData="searchData($event)" 
											  		@fetchAllContents="fetchAllRequisitions"
											  	></search-and-addition-option>
											  	-->

											  	<div class="row d-flex align-items-center text-center">		  	
											  		<div class="col-sm-3 form-group">	
															My Requisition List
											  		</div>

											  		<div class="col-sm-6 was-validated form-group">
											  			<input 	type="text" 
														  		v-model="query" 
														  		pattern="[^'!#$%^()\x22]+" 
														  		class="form-control" 
														  		placeholder="Search"
													  	>
													  	<div class="invalid-feedback">
													  		Please search with releavant input
													  	</div>
											  		</div>

											  		<div class="col-sm-3 form-group">
											  			<button 
												  			class="btn btn-success btn-outline-success btn-sm" 
												  			data-toggle="tooltip" data-placement="top" title="Create New" 
												  			:disabled="merchantAllProducts.length==0" 
												  			@click="showContentCreateForm()"
											  			>
											  				<i class="fa fa-plus"></i>
											  				New Requisition
											  			</button>
											  		</div>
											  	</div>
											</div>
											
											<div class="col-sm-12 col-lg-12">	
										  		<tab 
										  			v-show="query === ''" 
										  			:tab-names="['pending', 'dispatched', 'cancelled']" 
										  			:current-tab="'pending'" 

										  			@showPendingContents="showPendingContents" 
										  			@showDispatchedContents="showDispatchedContents" 
										  			@showCancelledContents="showCancelledContents" 
										  		></tab>

 												<div class="tab-content card-block pl-0 pr-0">
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
																		v-for="(content, contentIndex) in requisitionsToShow" 
																		:key="'content-index-' + contentIndex + 'content-' + content.id" 
																		:class="content.id==singleRequisitionData.id ? 'highlighted' : ''"
																	>
																		<td>{{ content.subject | capitalize }}</td>
																		
																		<td>
																			<span :class="[content.status==1 ? 'badge-success' : content.status==0 ? 'badge-danger' : 'badge-secondary', 'badge']">
																				{{ content.status==1 ? 'Dispatched' : content.status==0 ? 'Pending' : 'Cancelled' }}
																			</span>
																		</td>
																		
																		<td>
																			<span 
																			v-if="content.status==1 && content.dispatch.has_approval==1"
																			:class="[!unconfirmed(content) ? 'badge-success' : 'badge-danger', 'badge']">
																				{{ !unconfirmed(content) ? 'Confirmed' : 'Not Yet' }}
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
																				data-toggle="tooltip" data-placement="top" title="View Details"  
																				@click="showContentDetails(content)"
																			>
																				<i class="fa fa-eye"></i>
																				<!-- Details -->
																			</button>

																			<button 
																				type="button" 
																				class="btn btn-grd-danger btn-icon" 
																				data-toggle="tooltip" data-placement="top" title="Receive" 
																				v-if="unconfirmed(content)"
																				@click="receiveDispatchedProducts(content)"
																			>
																				<i class="fa fa-check-circle" aria-hidden="true"></i>
																				<!-- Receive -->
																			</button>

																			<!-- 
																			<button 
																				type="button" 
																				class="btn btn-dark btn-icon" 
																				v-show="content.hasOwnProperty('agent')"  
																				@click="confirmAgentPresence(content)"
																			>
																				<i class="fa fa-user-secret "></i>
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
																class="btn btn-primary btn-sm" 
																data-toggle="tooltip" data-placement="top" title="Reload" 
																@click="query === '' ? fetchAllRequisitions() : searchData()"
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
																@paginate="query === '' ? fetchAllRequisitions() : searchData()"
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
		<div class="modal fade" id="requisition-createOrEdit-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
									          	data-toggle="tooltip" data-placement="top" title="Next" 
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

												<div 
													class="form-group col-md-6" 
													v-if="requiredProduct.product"
												>
													<label for="inputFirstName">Total Quantity</label>
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
													<div class="invalid-feedback">
											        	{{ errors.products[productIndex].product_quantity }}
											  		</div>
												</div>
											</div>

											<div class="card" v-if="requiredProduct.product && requiredProduct.product.has_variations">
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
						                              			label="name" 
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
															<input type="number" 
																class="form-control" 
																v-model.number="requiredProduct.product.variations[variationIndex].required_quantity" 
																placeholder="Variation Quantity" 
																:class="!errors.products[productIndex].variation_quantities[variationIndex] ? 'is-valid' : 'is-invalid'" 
																min="0" 
																:max="productVariation.available_quantity - productVariation.requested_quantity" 
																@change="validateFormInput('variations_total_quantity')" 
															>
															<div 
																class="invalid-feedback" 
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

										<div class="form-row">
											<div class="form-group col-md-6">
												<button 
													type="button" 
													class="btn waves-effect waves-light hor-grd btn-grd-primary btn-sm btn-block" 
													data-toggle="tooltip" data-placement="top" title="Add Product" 
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
													data-toggle="tooltip" data-placement="top" title="Remove Product" 
													:disabled="singleRequisitionData.products.length < 2"
													@click="removeProduct()"
												>
													Remove Product
												</button>
											</div>
										</div>
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
								          		<button type="button" class="btn btn-outline-secondary btn-sm btn-round float-left" data-toggle="tooltip" data-placement="top" title="Previous"  v-on:click="step-=1">
							                    	<i class="fa fa-2x fa-angle-double-left" aria-hidden="true"></i>
							                  	</button>
								          		<button type="button" class="btn btn-outline-secondary btn-sm btn-round float-right" data-toggle="tooltip" data-placement="top" title="Next"  v-on:click="nextPage">
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
								                                  		:custom-label="objectNameWithCapitalized" 
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
								          		<button type="button" class="btn btn-outline-secondary btn-sm btn-round float-left" data-toggle="tooltip" data-placement="top" title="Previous"  v-on:click="step-=1">
							                    	<i class="fa fa-2x fa-angle-double-left" aria-hidden="true"></i>
							                  	</button>
								          		<button type="button" class="btn btn-outline-secondary btn-sm btn-round float-right" data-toggle="tooltip" data-placement="top" title="Next"  v-on:click="nextPage">
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
												<button type="submit" class="btn btn-primary float-right" :disabled="!submitForm">
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

		<!-- Modal -->
		<div class="modal fade" id="requisition-view-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">{{ singleRequisitionData.subject | capitalize }} Details</h5>
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
												{{ singleRequisitionData.subject }}
											</label>
										</div>

										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Description :
											</label>
											<label class="col-sm-6 col-form-label">
												<span v-html="singleRequisitionData.description"></span>
											</label>
										</div>

										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">Status :</label>
											<label class="col-sm-6 col-form-label">
												<span :class="[singleRequisitionData.status==1 ? 'badge-success' : singleRequisitionData.status==0 ? 'badge-danger' : 'badge-secondary', 'badge']">{{ singleRequisitionData.status==1 ? 'Dispatched' : singleRequisitionData.status==0 ? 'Pending' : 'Cancelled' }}</span>
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
														:key="'requisition-detail-' + requiredProduct.id + productIndex"
													>
														<div class="card">
															<div class="card-body">
																<div class="form-row">
																	<label class="col-sm-6 col-form-label font-weight-bold text-right">
																		Product Name :
																	</label>
																	<label class="col-sm-6 col-form-label">
																		{{ requiredProduct.product_name }}
																	</label>
																</div>

																<div class="form-row">
																	<label class="col-sm-6 col-form-label font-weight-bold text-right">
																		Total Quantity :
																	</label>
																	<label class="col-sm-6 col-form-label">
																		{{ requiredProduct.quantity }}
																	</label>
																</div>

																<div class="form-row" v-if="requiredProduct.has_serials && ! requiredProduct.has_variations && requiredProduct.hasOwnProperty('serials') && requiredProduct.serials.length">
																	<label class="col-sm-6 col-form-label font-weight-bold text-right">
																		Product Serials :
																	</label>
																	<label class="col-sm-6 col-form-label">
																		<span v-for="(productSerial, productSerialIndex) in requiredProduct.serials">
																			{{ productSerial }}
																			<span v-show="(productSerialIndex + 1) < requiredProduct.serials.length">, </span> 
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
																			</label>
																		</div>

																		<div class="form-row" v-if="productVariation.has_serials && productVariation.hasOwnProperty('serials') && productVariation.serials.length">
																			<label class="col-sm-6 col-form-label font-weight-bold text-right">
																				{{ productVariation.variation_name | capitalize }} Serials :
																			</label>
																			<label class="col-sm-6 col-form-label">
																				<span v-for="(variationSerial, variationSerialIndex) in productVariation.serials">
																					{{ variationSerial }}
																					<span v-show="(variationSerialIndex + 1) < productVariation.serials.length">, </span> 
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

										<div class="form-row" v-if="singleRequisitionData.delivery && singleRequisitionData.status==1 && singleRequisitionData.dispatch.has_approval==1">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Delivery Receipt :
											</label>
											<div class="col-sm-6">
												<img 
													class="img-fluid"  
													:src="singleRequisitionData.dispatch.delivery.receipt_preview"
												>
											</div>
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
										<!-- 
										<div class="form-row" v-if="singleRequisitionData.hasOwnProperty('updater')">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Recommended By :
											</label>
											<label class="col-sm-6 col-form-label">
												{{ singleRequisitionData.updater.user_name | capitalize }}
											</label>
										</div>
										 -->

										<div class="form-row" v-if="singleRequisitionData.status==-1 || (singleRequisitionData.status==1 && singleRequisitionData.dispatch && singleRequisitionData.dispatch.has_approval==1)">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												{{ singleRequisitionData.status==1 && singleRequisitionData.dispatch && singleRequisitionData.dispatch.has_approval==1 ? 'Dispatched ' : 'Cancelled ' }}  By :
											</label>
											<label class="col-sm-6 col-form-label">
												{{ singleRequisitionData.status==1 && singleRequisitionData.dispatch && singleRequisitionData.dispatch.has_approval==1 ? singleRequisitionData.dispatch.updater.user_name : singleRequisitionData.updater.user_name | capitalize }}
											</label>
										</div>

										<div class="form-row" v-if="singleRequisitionData.status==-1 && singleRequisitionData.hasOwnProperty('cancellation_reason') && singleRequisitionData.cancellation_reason">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Cancellation Reason :
											</label>
											<label class="col-sm-6 col-form-label">
												<span v-html="singleRequisitionData.cancellation_reason.reason"></span>
											</label>
										</div>

										<div class="form-row" v-if="singleRequisitionData.status==1 && singleRequisitionData.dispatch.has_approval==1">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Received :
											</label>
											<label class="col-sm-6 col-form-label">
												<span :class="[!unconfirmed(singleRequisitionData) ? 'badge-success' : 'badge-danger', 'badge']">{{ !unconfirmed(singleRequisitionData) ? 'Confirmed' : 'Not Yet' }}</span>
											</label>
										</div>

										<div class="form-row" v-else>
											<label class="col-sm-12 col-form-label text-center">
												<span :class="[singleRequisitionData.status==1 ? 'badge-success' : singleRequisitionData.status==0 ? 'badge-danger' : 'badge-secondary', 'badge']">
													{{ singleRequisitionData.status==1 ? 'Dispatched' : singleRequisitionData.status==0 ? 'Not Dispatched Yet' : 'Cancelled' }}
												</span>
											</label>
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
	import CKEditor from '@ckeditor/ckeditor5-vue';
	import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

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

	export default {

	    components: { 
			multiselect : Multiselect,
			ckeditor: CKEditor.component,
		},

	    data() {

	        return {

	        	step : 1,
	        	query : '',
	        	error : '',
    			perPage : 10,
	        	loading : false,
	        	
	        	currentTab : 'pending',
	        	// currentMerchant : null,

	        	editor: ClassicEditor,

	        	submitForm : true,

	        	requisitionsToShow : [],
	        	allFetchedRequisitions : [],
	        	
	        	merchantAllProducts : [],
	        	merchantAllAgents : [],

	        	allPackagingPackages : [],

	        	pagination: {
		        	current_page: 1
		      	},

	        	singleRequisitionData : singleRequisitionData,

	        	errors : {
					
	        		// subject : '',
					// description : '',
					
					products : [
						
						{
							// product_id : '',
							// product_quantity : '',
							// variations_total_quantity : ''
							variation_serials : [],
							variation_quantities : [],
						}

					],

					agent : {},

					delivery : {},

				},

	            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

	        }

		},
		
		created(){
			
			// this.currentMerchant();
			this.fetchAllRequisitions();
			this.fetchMerchantAllAgents();
			this.fetchMerchantAllProducts();
			this.fetchAllPackagingPackages();
			
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

		watch : {

			query : function(val){
				if (val==='') {
					this.fetchAllContents();
				}
				else {
					this.searchData();
				}
			},

		},
		
		methods : {
		
			fetchAllRequisitions() {
				
				this.query = '';
				this.error = '';
				this.loading = true;
				this.allFetchedRequisitions = [];
				
				axios
					.get('/api/my-requisitions/' + this.perPage + "?page=" + this.pagination.current_page)
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
						this.subscribeToChannel();
						this.loading = false;
					});

			},
			fetchMerchantAllProducts() {
				
				this.query = '';
				this.error = '';
				this.loading = true;
				this.merchantAllProducts = [];
				
				axios
					.get('/api/my-products/')
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
			fetchMerchantAllAgents() {
				
				this.query = '';
				this.error = '';
				this.loading = true;
				this.merchantAllAgents = [];
				
				axios
					.get('/api/my-agents/')
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
				
				this.query = '';
				this.error = '';
				this.loading = true;
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
    		showContentDetails(object) {		
				this.singleRequisitionData = { ...object };
				// this.singleRequisitionData = Object.assign({}, this.singleRequisitionData, object);
				// this.singleRequisitionData = object;
				$('#requisition-view-modal').modal('show');
			},
		/*
			confirmAgentPresence(object) {
				this.singleRequisitionData = { ...object };
				$('#requisition-view-modal').modal('show');
			},
		*/
			showContentCreateForm() {

				this.step = 1;
	        	this.submitForm = true;
	        	
				this.singleRequisitionData = {

					
					products : [
						{
							product : {},
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
							variation_quantities : []
						}
						
					],

					agent : {},

					delivery : {},

				};

				$('#requisition-createOrEdit-modal').modal('show');
				
			},
			storeRequisition() {
				
				if (!this.verifyUserInput()) {
					this.submitForm = false;
					return;
				}

				axios
					.post('/requisitions/' + this.perPage, this.singleRequisitionData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("New requisition has been stored", "Success");
							
							this.allFetchedRequisitions = response.data;
							this.query !== '' ? this.searchData() : this.showSelectedTabProducts();

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
						this.fetchMerchantAllProducts();
					});

			},
			receiveDispatchedProducts(object) {

    			this.singleRequisitionData = { ...object };

    			axios
					.post('/receive-dispatched-products/' + this.perPage, this.singleRequisitionData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Dispatched products has been received", "Success");

							this.allFetchedRequisitions = response.data;
							this.query !== '' ? this.searchData() : this.showSelectedTabProducts();

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
						// this.fetchMerchantAllProducts();
					});

    		},
			searchData(emitedValue=false) {

				if (emitedValue) {
					this.query=emitedValue;
				}

				this.error = '';
				this.allFetchedRequisitions = [];
				this.pagination.current_page = 1;
				
				axios
				.get(
					"/api/search-my-requisitions/" + this.query + "/" + this.perPage + "?page=" + this.pagination.current_page
				)
				.then(response => {
					this.allFetchedRequisitions = response.data;
					this.requisitionsToShow = this.allFetchedRequisitions.all.data;
					this.pagination = this.allFetchedRequisitions.all;
				})
				.catch(e => {
					this.error = e.toString();
				});

			},
			subscribeToChannel(){

				if (this.allFetchedRequisitions.pending.data.length || this.allFetchedRequisitions.dispatched.data.length) {

					let merchantId = this.requisitionsToShow[0]?.merchant_id ?? this.allFetchedRequisitions.pending.data[0]?.merchant_id ?? this.allFetchedRequisitions.dispatched.data[0]?.merchant_id;

			        // console.log('merchant id is : '+ merchantId);
					
					Echo.private(`new-dispatch.` + merchantId)
				    .listen('RequisitionDispatched', (e) => {
				        
				        // console.log(e);

				        this.$toastr.i("Requisition has been dispatched", "Info");

				    	let index = this.requisitionsToShow.findIndex(requisition => requisition.id === e.id && requisition.merchant_id === e.merchant_id);

				    	if (index > -1) {

				    		Vue.set(this.requisitionsToShow, index, e);
				        	// this.requisitionsToShow.splice(index, 1);
				        	this.allFetchedRequisitions.pending?.data.splice(index, 1);
				        	this.allFetchedRequisitions.dispatched?.data.push(e);
				    	
				    	}
				        
				    });

				}

			},
			resetMerchantAgent() {

				if (!this.singleRequisitionData.previous_agent || Object.keys(this.singleRequisitionData.previous_agent).length == 0) {

					this.singleRequisitionData.agent = {};

				}
				else {

					this.singleRequisitionData.agent = {...this.singleRequisitionData.previous_agent};

				}

			},
			verifyUserInput() {

				this.validateFormInput('agent_name');
				this.validateFormInput('agent_mobile');
				this.validateFormInput('agent_code');
				this.validateFormInput('delivery_address');

				if (this.errors.constructor === Object && Object.keys(this.errors.agent).length == 0 && Object.keys(this.errors.delivery).length == 0) {

					return true;
				
				}

				return false;
			},
			changeNumberContents() {
				
				this.pagination.current_page = 1;

				if (this.query === '') {
					this.fetchAllRequisitions();
				}
				else {
					this.searchData();
				}
				
    		},
    		resetSelectedAgent() {

    			this.singleRequisitionData.previous_agent = {};

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
			errorInArray(array = []) {
				
				if (array.length) {

					return array.some(
						product => Object.keys(product).length > 2 || product.variation_quantities.some(productVariation => productVariation != null) || (product.variation_serials.some(productVariation => productVariation != null))
					);

				}

				return false;
			},
			nextPage() {
					
				if (this.step==1) {
					this.validateFormInput('subject');
					this.validateFormInput('description');
				}
				else if (this.step == 2) {
					this.validateFormInput('product_id');
					this.validateFormInput('product_quantity');
					this.validateFormInput('variations_total_quantity');
				}
				else if (this.step == 3) {
					this.validateFormInput('product_serials');
				}


				if (!this.errors.subject && !this.errorInArray(this.errors.products) && this.step < 4) {
					
					if (this.step !=2 || this.requisitionHasSerialProduct()) {

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
			addMoreProduct() {
				if (this.singleRequisitionData.products.length < this.merchantAllProducts.length) {

					this.singleRequisitionData.products.push({ product : {}, total_quantity : 0 });
					
					this.errors.products.push({
						variation_serials : [],
						variation_quantities : []
					});

				}
			},
			removeProduct() {
					
				if (this.singleRequisitionData.products.length > 1) {

					this.singleRequisitionData.products.pop();
					this.errors.products.pop();
				
				}
				
			},
			objectNameWithCapitalized ({ name, variation }) {
		      	if (name) {
				    name = name.toString()
				    return name.charAt(0).toUpperCase() + name.slice(1)
		      	}
		      	else if (variation) {
		      		name = variation.name.toString()
				    return name.charAt(0).toUpperCase() + name.slice(1)
		      	}
		      	else 
		      		return ''
		    },
		    customPackagingPackageName ({ name, price, description, preview }) {
		    	
		    	if (name && price) {
		      		return name.charAt(0).toUpperCase() + name.slice(1) + ' - Each Price (BDT) ' + price 
		    	}
		    	return '';

		    },
			setRequiredProduct(index) {
				if (this.singleRequisitionData.products[index].product && Object.keys(this.singleRequisitionData.products[index].product).length > 0) {
					this.singleRequisitionData.products[index].id = this.singleRequisitionData.products[index].product.id;
				}
			},
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
			unconfirmed(object) {

				if (object.status==1 && object.dispatch.has_approval==1 && ((object.dispatch.hasOwnProperty('agent') && !object.dispatch.agent.receiving_confirmation) || (object.dispatch.hasOwnProperty('delivery') && !object.dispatch.delivery.receiving_confirmation))) {

					return true; 	// not confirmed

				}

				return false;  // confirmed
				
			},
			requisitionHasSerialProduct() {

				return this.singleRequisitionData.products.some(
					currentProduct => currentProduct.product.has_serials
				);

			},
			validateFormInput (formInputName) {

				this.submitForm = false;

				switch(formInputName) {

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

					case 'product_id' :

						this.singleRequisitionData.products.forEach(
							
							(requiredProduct, productIndex) => {

								if (!requiredProduct.id || !requiredProduct.product || Object.keys(requiredProduct.product).length==0) {
									this.errors.products[productIndex].product_id = 'Product is required';
								}
								else if (this.singleRequisitionData.products.filter(current => current.id == requiredProduct.id).length > 1) {

									this.errors.products[productIndex].product_id = 'Same product is selected';

								}
								else{
									// this.errors.products[productIndex].product_id = null;
									this.$delete(this.errors.products[productIndex], 'product_id');
								}

								if (!this.errorInArray(this.errors.products)) {
									this.submitForm = true;
								}

							}
						);

						break;

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

				}
	 
			},

			/*
			currentMerchant(){

				this.query = '';
				this.error = '';
				this.loading = true;
				this.currentMerchant = null;
				
				axios
					.get('/api/current-merchant/')
					.then(response => {
						if (response.status == 200) {
							this.currentMerchant = response.data.user;
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