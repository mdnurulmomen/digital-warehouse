
<template>

	<div class="pcoded-content">

		<breadcrumb 
			:title="'dispatches'" 
			:message="'All our dispatches'"
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
												  		
												  		@showDispatchCreateForm="showDispatchCreateForm" 
												  		@searchData="searchData($event)" 
												  		@fetchAllContents="fetchAllDispatches"
												  	></search-and-addition-option>
	 											-->
 												<div class="row d-flex align-items-center">										  	
											  		<div class="col-sm-3 text-left">	
															Dispatches List
											  		</div>
											  		<div class="col-sm-6 was-validated text-center">
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
										  			 
											  		<div class="col-sm-3 text-right">
											  			<button 
												  			class="btn btn-success btn-outline-success btn-sm" 
												  			@click="showDispatchCreateForm"
											  			>
											  				<i class="fa fa-plus"></i>
											  				New Dispatch
											  			</button>
											  		</div>
 													
											  	</div>
											</div>
											
											<div class="col-sm-12 col-lg-12">

 												<div class="tab-dispatch card-block">
													<div class="card">
														<div class="table-responsive">
															<table class="table table-striped table-bordered nowrap text-center">
																<thead>
																	<tr>
																		<th>Requisition</th>
																		<th>Released at</th>
																		<th>Actions</th>
																	</tr>
																</thead>
																<tbody>

																	<tr 
																		v-for="dispatch in allDispatches" 
																		:key="'dispatch-' + dispatch.id"
																	>
																		<td>{{ dispatch.requisition.subject }}</td>
																		<td>{{ dispatch.released_at }}</td>
																		<td>
																			<button 
																				type="button" 
																				class="btn btn-grd-info btn-icon"  
																				@click="showDispatchDetails(dispatch)"
																			>
																				<i class="fas fa-eye"></i>
																			</button>
																		</td>
																    
																	</tr>
																	<tr 
																  		v-show="!allDispatches.length"
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
																		<th>Requisition</th>
																		<th>Released at</th>
																		<th>Actions</th>
																	</tr>
																</tfoot>
															</table>
														</div>
													</div>
													<div class="row d-flex align-items-center align-dispatch-center">
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
																@click="query === '' ? fetchAllDispatches() : searchData()"
															>
																Reload
																<i class="fas fa-sync"></i>
															</button>
														</div>
														<div class="col-sm-8">
															<pagination
																v-if="pagination.last_page > 1"
																:pagination="pagination"
																:offset="5"
																@paginate="query === '' ? fetchAllDispatches() : searchData()"
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
		<div class="modal fade" id="dispatch-createOrEdit-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">
							Make Dispatch
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
									v-bind:key="'requisition-modal-step-' + 1" 
									v-show="!loading && step==1"
								>								  	
									<h2 class="mx-auto mb-4 lead">Requisition Profile</h2>

							        <div class="col-md-12">	
										<div class="form-row">
											<div class="form-group col-md-12">
												<label for="inputFirstName">Select Requisition</label>
												<multiselect 
			                              			v-model="singleDispatchData.requisition"
			                              			placeholder="Requisition Subject" 
			                                  		track-by="id" 
			                                  		:custom-label="objectNameWithCapitalized" 
			                                  		:options="allRequisitions" 
			                                  		:required="true" 
			                                  		:allow-empty="false"
			                                  		:class="!errors.requisition_id ? 'is-valid' : 'is-invalid'" 
			                                  		@close="validateFormInput('requisition_id')" 
			                              		>
			                                	</multiselect>
			                                	<div 
				                                	class="invalid-feedback" 
				                                	style="display: block;" 
				                                	v-show="errors.requisition_id"
			                                	>
											    	{{ errors.requisition_id }}
											    </div>
											</div>
										</div>

										<div 
											class="form-row" 
											v-if="Object.keys(singleDispatchData.requisition).length > 0"
										>
											<div class="form-group col-md-12">
												<label for="inputFirstName">Description</label>
												<ckeditor 
					                              	class="form-control" 
					                              	:editor="editor" 
					                              	v-model="singleDispatchData.requisition.description" 
					                              	:disabled="true"
					                            >
				                              	</ckeditor>
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
									v-bind:key="'requisition-modal-step-' + 2" 
									v-show="!loading && step==2"
								>								  	
									<h2 class="mx-auto mb-4 lead">Requisition Products</h2>

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
				                                  		track-by="product_id" 
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
													<input 
														type="number" 
														class="form-control" 
														v-model.number="requiredProduct.quantity" 
														placeholder="Product Total Quantity" 
														readonly="true"
													>
												</div>

												<div class="form-group col-md-4">
													<label for="inputFirstName">
														Available
													</label>
													<input 
														type="number" 
														class="form-control" 
														v-model.number="requiredProduct.available_quantity" 
														readonly="true"
													>
												</div>

											<!-- 
												<div class="form-group col-md-4">
													<label for="inputFirstName">
														Dispatched Total Quantity
													</label>
													<input 
														type="number" 
														class="form-control" 
														v-model.number="requiredProduct.product_dispatched_quantity" 
														placeholder="Product Total Quantity" 
														:class="!errors.products[productIndex].total_dispatched_quantity ? 'is-valid' : 'is-invalid'" 
														@input="validateFormInput('total_dispatched_quantity')" 
													>
													<div class="invalid-feedback">
												    	{{ errors.products[productIndex].total_dispatched_quantity }}
												    </div>
												</div>
 											-->

											</div>

											<div class="card" v-if="requiredProduct.has_variations">
												<div class="card-body">
													<div 
														class="form-row" 
														v-for="(productVariation, variationIndex) in requiredProduct.variations" 
														:key="'required-product-variation-' + productIndex + variationIndex"
													>	
														<div class="form-group col-md-4">
															<label for="inputFirstName">
																Variation
															</label>
															<multiselect 
						                              			v-model="requiredProduct.variations[variationIndex]"
						                              			placeholder="Variation Name" 
						                              			label="variation_name" 
						                                  		track-by="product_variation_id" 
						                                  		:custom-label="objectNameWithCapitalized" 
						                                  		:options="singleDispatchData.requisition.products"
						                                  		:disabled="true"
						                              		>
						                                	</multiselect>
														</div>
														<div class="form-group col-md-4">
															<label for="inputFirstName">Required</label>
															<input 
																type="number" 
																class="form-control" 
																v-model.number="requiredProduct.variations[variationIndex].quantity" 
																placeholder="Variation Quantity" 
																readonly="true" 
															>
														</div>
														<div class="form-group col-md-4">
															<label for="inputFirstName">Available</label>
															<input 
																type="number" 
																class="form-control" 
																v-model.number="requiredProduct.variations[variationIndex].available_quantity" 
																placeholder="Dispatched Quantity" 
																readonly="true" 
															>
														</div>

														<!-- 
														<div class="form-group col-md-4">
															<label for="inputFirstName">Dispatched Quantity</label>
															<input 
																type="number" 
																class="form-control" 
																v-model.number="requiredProduct.variations[variationIndex].variation_dispatched_quantity" 
																placeholder="Dispatched Quantity" 
																@input="validateFormInput('variation_total_dispatched_quantity')"  
															>
														</div>
													 	-->

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
								          		<button type="button" class="btn btn-outline-secondary btn-sm btn-round float-left" v-on:click="step-=1">
							                    	<i class="fa fa-2x fa-angle-double-left" aria-hidden="true"></i>
							                  	</button>
								          		<button type="button" class="btn btn-outline-secondary btn-sm btn-round float-right" v-on:click="nextPage">
							                    	<i class="fa fa-2x fa-angle-double-right" aria-hidden="true"></i>
							                  	</button>
								          	</div>
										</div>
									</div>
								</div>

								<div 
									class="row" 
									v-bind:key="'requisition-modal-step-' + 3" 
									v-show="!loading && step==3"
								>	
									<h2 class="mx-auto mb-4 lead">Delivery Details</h2>	
									
									<div class="form-group col-md-12 text-center">
										<span :class="[singleDispatchData.requisition.delivery ? 'badge-success' : 'badge-info', 'badge']">
											{{ singleDispatchData.requisition.delivery ? 'Delivery Service' : 'Agent Service' }}
										</span>
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
										</div>

										<div class="form-row d-flex">
											<div class="form-group col-md-6">
												<img class="img-fluid" 
													:src="singleDispatchData.agent.agent_receipt || ''"
													alt="agent_receipt" 
												>
											</div>

											<div class="form-group col-md-6  align-self-center">
												<div class="custom-file">
												    <input type="file" 
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
									</div>

									<div 
										class="col-sm-12" 
										v-if="singleDispatchData.requisition.delivery && !singleDispatchData.requisition.agent"
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
											<!-- 
											<div class="form-group col-md-6">
												<label for="inputFirstName">Destination</label>
												<input 
													type="text" 
													class="form-control" 
													v-model="singleDispatchData.delivery.address" 
													placeholder="Delivery Address" 
													:class="!errors.delivery.delivery_address ? 'is-valid' : 'is-invalid'"
													@change="validateFormInput('delivery_address')"
												>
												<div class="invalid-feedback">
											    	{{ errors.delivery.delivery_address }}
											    </div>
											</div>
 											-->

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
											<div class="col-sm-12">
												<button type="button" class="btn btn-outline-secondary btn-sm btn-round float-left" v-on:click="step-=1">
							                    	<i class="fa fa-2x fa-angle-double-left" aria-hidden="true"></i>
							                  	</button>
												<button type="submit" class="btn btn-primary float-right" :disabled="!submitForm">
													Dispatch
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

 		<!-- View Modal -->
		<div class="modal fade" id="dispatch-view-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Dispatch Details</h5>
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
										<a class="nav-link" data-toggle="tab" href="#requisition-despatch" role="tab">
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
												{{ singleDispatchData.requisition.subject }}
											</label>
										</div>

										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Description :
											</label>
											<label class="col-sm-6 col-form-label">
												<span v-html="singleDispatchData.requisition.description"></span>
											</label>
										</div>

										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Requested on :
											</label>
											<label class="col-sm-6 col-form-label">
												{{ singleDispatchData.requisition.created_at }}
											</label>
										</div>
									</div>

									<div class="tab-pane" id="requisition-product" role="tabpanel">
										<div 
											class="form-row" 
											v-if="singleDispatchData.products && singleDispatchData.products.length"
										>
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Product Detail :
											</label>
											<div class="col-sm-6">
												<div class="form-row">
													
													<div 
														class="col-md-12 ml-auto" 
														v-for="(dispatchedProduct, productIndex) in singleDispatchData.products" 
														:key="'requisition-detail-' + dispatchedProduct.id + productIndex"
													>
														<div class="card">
															<div class="card-body">
																<div class="form-row">
																	<label class="col-sm-6 col-form-label font-weight-bold text-right">
																		Product Name :
																	</label>
																	<label class="col-sm-6 col-form-label">
																		{{ dispatchedProduct.product_name }}
																	</label>
																</div>

																<div class="form-row">
																	<label class="col-sm-6 col-form-label font-weight-bold text-right">
																		Total Quantity :
																	</label>
																	<label class="col-sm-6 col-form-label">
																		{{ dispatchedProduct.quantity }}
																	</label>
																</div>

																<div class="form-row">
																	<label class="col-sm-6 col-form-label font-weight-bold text-right">
																		Variations :
																	</label>
																	<label class="col-sm-6 col-form-label">
																		<span :class="[dispatchedProduct.has_variations ? 'badge-success' : 'badge-danger', 'badge']">{{ dispatchedProduct.has_variations ? 'Yes' : 'No' }}
																		</span>
																	</label>
																</div>

																<div class="form-row" v-if="dispatchedProduct.has_variations && dispatchedProduct.variations && dispatchedProduct.variations.length">
																	<div 
																		class="col-sm-12" 
																		v-for="(productVariation, variationIndex) in dispatchedProduct.variations" 
																		:key="'required-product-variation-' + productVariation.id + variationIndex"
																	>
																		<div class="form-row">
																			<label class="col-sm-6 col-form-label font-weight-bold text-right">
																				 {{ productVariation.variation_name }} :
																			</label>
																			<label class="col-sm-6 col-form-label">
																				{{ productVariation.quantity }}
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

									<div class="tab-pane" id="requisition-despatch" role="tabpanel">

										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Dispatched on :
											</label>
											<label class="col-sm-6 col-form-label">
												{{ singleDispatchData.released_at }}
											</label>
										</div>

										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Service :
											</label>
											<label class="col-sm-6 col-form-label">
												<span :class="[singleDispatchData.requisition.delivery ? 'badge-success' : 'badge-info', 'badge']">{{ singleDispatchData.requisition.delivery ? 'Delivery Service' : 'Agent Service' }}</span>
											</label>
										</div>

										<div class="form-row" v-if="singleDispatchData.requisition.delivery">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Delivery Address :
											</label>
											<label class="col-sm-6 col-form-label">
												<span v-html="singleDispatchData.requisition.delivery.address"></span>
											</label>
										</div>

										<div class="form-row" v-if="singleDispatchData.delivery">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Delivery Receipt :
											</label>
											<label class="col-sm-6 col-form-label">
												<img 
													class="img-fluid" 
													:src="singleDispatchData.delivery.receipt_preview || ''"
													alt="delivery_receipt" 
												>
											</label>
										</div>

										<div class="form-row" v-if="singleDispatchData.requisition.agent">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Agent Name :
											</label>
											<label class="col-sm-6 col-form-label">
												{{ singleDispatchData.requisition.agent.name  }}
											</label>
										</div>

										<div class="form-row" v-if="singleDispatchData.requisition.agent">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Agent Mobile :
											</label>
											<label class="col-sm-6 col-form-label">
												{{ singleDispatchData.requisition.agent.mobile }}
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

    let singleDispatchData = {

		requisition : {},

		delivery : {},

		agent : {}
				
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
	        	
	        	editor: ClassicEditor,

	        	submitForm : true,

	        	// dispatchesToShow : [],
	        	allDispatches : [],
	        	
	        	allRequisitions : [],

	        	pagination: {
		        	current_page: 1
		      	},

	        	singleDispatchData : singleDispatchData,

	        	errors : {
					// products : [],
					delivery : {},
					agent : {}
				},

	            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('dispatch'),

	        }

		},
		
		created(){
			this.fetchAllDispatches();
			this.fetchAllRequisitions();
		},

		watch : {

			query : function(val){
				if (val==='') {
					this.fetchAllDispatches();
				}
				else {
					this.searchData();
				}
			},

		},
		
		methods : {

			fetchAllDispatches() {
				
				this.query = '';
				this.error = '';
				this.loading = true;
				this.allDispatches = [];
				
				axios
					.get('/api/dispatches/' + this.perPage + "?page=" + this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							this.allDispatches = response.data.data;
							this.pagination = response.data;
							// this.dispatchesToShow = this.allDispatches.pending.data;
							// this.showSelectedTabProducts();
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
			fetchAllRequisitions() {
				
				this.query = '';
				this.error = '';
				this.loading = true;
				this.allRequisitions = [];
				
				axios
					.get('/api/requisitions/')
					.then(response => {
						if (response.status == 200) {
							this.allRequisitions = response.data.data;
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
			searchData() {

				this.error = '';
				this.allDispatches = [];
				this.pagination.current_page = 1;
				
				axios
				.get(
					"/api/search-dispatches/" + this.query + "/" + this.perPage + "?page=" + this.pagination.current_page
				)
				.then(response => {
					this.allDispatches = response.data.all.data;
					// this.dispatchesToShow = this.allDispatches.all.data;
					this.pagination = response.data.all;
				})
				.catch(e => {
					this.error = e.toString();
				});

			},
			changeNumberContents() {
				
				this.pagination.current_page = 1;

				if (this.query === '') {
					this.fetchAllDispatches();
				}
				else {
					this.searchData();
				}
				
    		},
    		showDispatchDetails(object) {
    			
    			// console.log(object);

				this.singleDispatchData = { ...object };
				// this.singleDispatchData = Object.assign({}, this.singleDispatchData, object);
				$('#dispatch-view-modal').modal('show');
			},
			showDispatchCreateForm() {
				this.step = 1;
	        	this.submitForm = true;
	        	
				this.singleDispatchData = {

					requisition : {},

					delivery : {},

					agent : {}
							
			    };

				this.errors = {
					// products : [],
					delivery : {},
					agent : {},
				};

				$('#dispatch-createOrEdit-modal').modal('show');
			},
			makeDispatch() {
				
				if (!this.verifyUserInput()) {
					this.submitForm = false;
					return;
				}

				axios
					.post('/dispatches/' + this.perPage, this.singleDispatchData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Requisition has been dispatched", "Success");
							this.query !== '' ? this.searchData() : this.fetchAllDispatches();
							// this.allDispatches = response.data;
							// this.query !== '' ? this.searchData() : this.showSelectedTabProducts();
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
						
					});

			},
			verifyUserInput() {

				this.validateFormInput('requisition_id');
				this.validateFormInput('delivery_price');
				this.validateFormInput('delivery_receipt');
				this.validateFormInput('agent_receipt');

				if (this.errors.constructor === Object && Object.keys(this.errors).length < 4 && Object.keys(this.errors.delivery).length == 0 && Object.keys(this.errors.agent).length == 0) {

					console.log('verified');
					return true;
				
				}

				return false;
			},
			nextPage() {
					
				if (this.step==1) {
					this.validateFormInput('requisition_id');
					// this.setRequiredErrors();
				}
			
			/*
				else if (this.step == 2) {
					// this.validateFormInput('total_dispatched_quantity');
					// this.validateFormInput('variation_total_dispatched_quantity');
				}
			*/

				if (!this.errors.requisition_id && this.step < 3) {
					this.step += 1;
					this.submitForm = true;
				}
				else {
					this.submitForm = false;
				}

			},
		/*
			errorInArray(array = []) {
				
				if (array.length) {

					return array.some(element => Object.keys(element).length > 0);

				}

				return false;
			},
		*/
			objectNameWithCapitalized ({subject, product_name, variation_name }) {
		      	if (product_name) {
				    product_name = product_name.toString()
				    return product_name.charAt(0).toUpperCase() + product_name.slice(1)
		      	}
		      	else if (subject) {
		      		subject = subject.toString()
				    return subject.charAt(0).toUpperCase() + subject.slice(1)
		      	}
		      	else if (variation_name) {
		      		variation_name = variation_name.toString()
				    return variation_name.charAt(0).toUpperCase() + variation_name.slice(1)
		      	}
		      	else 
		      		return ''
		    },
		/*
			setRequiredErrors() {

				// console.log(this.singleDispatchData.requisition.products);

				if (this.singleDispatchData.requisition.products && this.singleDispatchData.requisition.products.length) {

					this.errors.products = [];

					this.singleDispatchData.requisition.products.forEach(
							
						(requiredProduct, index) => {

							this.errors.products.push({});

						}
						
					);

					// console.log('Hitted Inside');

				}

				// console.log('Hitted OutSide');

			},
		*/
			onDeliveryReceiptChange(evnt) {

				let files = evnt.target.files || evnt.dataTransfer.files;

                // Only process image files.
		      	if (files.length && files[0].type.match('image.*')) {
                	this.submitForm = true;
                	this.$delete(this.errors.delivery, 'delivery_receipt');
                	this.createImage(files[0], 'delivery_receipt');
		      	}
		      	else{
		      		this.errors.delivery.delivery_receipt = 'Receipt is required';
		      	}

		      	evnt.target.value = '';
		      	return;

			},
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
			createImage(file, previewName) {
                let reader = new FileReader();
                
                reader.onload = (evnt) => {

                	if (previewName === 'delivery_receipt') {

                		this.singleDispatchData.delivery.delivery_receipt = evnt.target.result;
                	}
                	else {

                    	this.singleDispatchData.agent.agent_receipt = evnt.target.result;
                	}
                };

                reader.readAsDataURL(file);
            },
			validateFormInput (formInputName) {

				this.submitForm = false;

				switch(formInputName) {

					case 'requisition_id' :

						if (Object.keys(this.singleDispatchData.requisition).length==0) {
							this.errors.requisition_id = 'Requisition is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'requisition_id');
						}

						break;

					/*
					case 'total_dispatched_quantity' :

						if (this.singleDispatchData.requisition.products && this.singleDispatchData.requisition.products.length) {

							this.singleDispatchData.requisition.products.forEach(
							
								(requiredProduct, productIndex) => {

									if (!requiredProduct.product_dispatched_quantity || requiredProduct.product_dispatched_quantity < 1) {
										this.errors.products[productIndex].total_dispatched_quantity = 'Total Quantity is required';
									}
									else{
										this.$delete(this.errors.products[productIndex], 'total_dispatched_quantity');
									}

									if (!this.errorInArray(this.errors.products)) {
										this.submitForm = true;
									}

								}
							);

						}
						else {

							this.errors.products = [];

						}

						break;
					*/

					/*
					case 'variation_total_dispatched_quantity' :

						if (this.singleDispatchData.requisition.products && this.singleDispatchData.requisition.products.length) {

							this.singleDispatchData.requisition.products.forEach(
							
								(requiredProduct, productIndex) => {

									if (requiredProduct.has_variations && requiredProduct.variations.length) {

										let totalVariationQuantity = requiredProduct.variations.reduce((total, current) => total + current.variation_dispatched_quantity || 0, 0

										);

										if (requiredProduct.product_dispatched_quantity != totalVariationQuantity) {
											this.errors.products[productIndex].variation_total_dispatched_quantity = 'Total quantity should be equal to variations quantity';
										}
										else{
											
											this.$delete(this.errors.products[productIndex], 'variation_total_dispatched_quantity');

										}

										if (!this.errorInArray(this.errors.products)) {
											this.submitForm = true;
										}

									}
									else {

										this.errors.products[productIndex] = {};

									}
									
								}
							);

						}
						else {

							this.errors.products = [];

						}

						break;
					*/

					/*
					case 'delivery_address' :

						if (this.singleDispatchData.requisition.hasOwnProperty('delivery')) {

							if (Object.keys(this.singleDispatchData.delivery).length==0 || !this.singleDispatchData.delivery.address) {
								this.errors.delivery.delivery_address = 'Address is required';
							}
							else{
								this.submitForm = true;
								this.$delete(this.errors.delivery, 'delivery_address');
							}

						}

						else {

							this.errors.delivery = {};

						}


						break;
					*/

					case 'delivery_price' :

						if (this.singleDispatchData.requisition.hasOwnProperty('delivery')) {

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
							this.errors.delivery = {};

						}


						break;

					case 'delivery_receipt' :

						if (this.singleDispatchData.requisition.hasOwnProperty('delivery')) {

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
							this.errors.delivery = {};

						}


						break;

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

				/*

					case 'description' :

						if (this.singleDispatchData.description && !this.singleDispatchData.description.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.description = 'No special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'description');
						}

						break;

					

					case 'product_quantity' :

						this.singleDispatchData.products.forEach(
							
							(requiredProduct, index) => {

								if (!requiredProduct.quantity || requiredProduct.quantity < 1) {
									this.errors.product_quantity[index] = 'Quantity is required';
								}
								else{
									this.errors.product_quantity[index] = null;
								}

								if (!this.errorInArray(this.errors.product_quantity)) {
									this.submitForm = true;
								}

							}
						);

						break;
				*/

				}
	 
			},

		/*
			showSelectedTabProducts() {
				
				if (this.currentTab=='pending') {
					this.dispatchesToShow = this.allDispatches.pending.data;
					this.pagination = this.allDispatches.pending;
				}
				else {
					this.dispatchesToShow = this.allDispatches.dispatched.data;
					this.pagination = this.allDispatches.dispatched;
				}

			},
		*/
		/*
		*/
		/*
			showPendingContents() {
				this.currentTab = 'pending';
				this.showSelectedTabProducts();
			},
			showDispatchedContents() {
				this.currentTab = 'dispatched';
				this.showSelectedTabProducts();
			},
			addMoreProduct() {
				if (this.singleDispatchData.products.length < 3) {

					this.singleDispatchData.products.push({});
					this.errors.product_id.push(null);
					this.errors.product_quantity.push(null);

				}
			},
			removeProduct() {
					
				if (this.singleDispatchData.products.length > 1) {

					this.singleDispatchData.products.pop();
					this.errors.product_id.pop();
					this.errors.product_quantity.pop();
				
				}
				
			},
		*/
			
		/*
			setRequiredProduct(index) {
				if (this.singleDispatchData.products[index].product && Object.keys(this.singleDispatchData.products[index].product).length > 0) {
					this.singleDispatchData.products[index].id = this.singleDispatchData.products[index].product.id;
				}
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