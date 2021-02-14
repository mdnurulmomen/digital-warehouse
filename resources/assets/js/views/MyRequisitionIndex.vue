
<template>

	<div class="pcoded-content">

		<breadcrumb 
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
											  	<search-and-addition-option 
											  		:query="query" 
											  		:caller-page="'requisition'" 
											  		
											  		@showContentCreateForm="showContentCreateForm" 
											  		@searchData="searchData($event)" 
											  		@fetchAllContents="fetchAllRequisitions"
											  	></search-and-addition-option>
											</div>
											
											<div class="col-sm-12 col-lg-12">
										  		
										  		<tab 
										  			v-show="query === ''" 
										  			:tab-names="['pending', 'dispatched']" 
										  			:current-tab="'pending'" 

										  			@showPendingContents="showPendingContents" 
										  			@showDispatchedContents="showDispatchedContents" 
										  		></tab>

 												<div class="tab-content card-block">
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

																	<tr v-for="content in requisitionsToShow" :key="'content-' + content.id"
																	>
																		<td>{{ content.subject }}</td>
																		<td>
																			<span :class="[content.status ? 'badge-success' : 'badge-danger', 'badge']">
																				{{ content.status ? 'Dispatched' : 'Pending' }}
																			</span>
																		</td>
																		<td>
																			<span 
																			v-if="content.status && content.dispatch"
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
																				@click="showContentDetails(content)"
																			>
																				<i class="fas fa-eye"></i>
																				<!-- Details -->
																			</button>

																			<button 
																				type="button" 
																				class="btn btn-grd-danger btn-icon" 
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
																@click="query === '' ? fetchAllRequisitions() : searchData()"
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

										<div class="form-row">
											<div class="form-group col-md-12">
												<label for="inputFirstName">Description</label>
												<ckeditor 
					                              	class="form-control" 
					                              	:editor="editor" 
					                              	v-model="singleRequisitionData.description"
					                            >
				                              	</ckeditor>

				                            <!-- 
				                              	<div 
					                              	class="invalid-feedback" 
					                              	style="display : block" 
					                              	v-show="errors.description"
				                              	>
										        	{{ errors.description }}
										  		</div>
 											-->
											</div>
										</div>
							        </div>

							        <div class="col-md-12">
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
									v-bind:key="'requisition-modal-step' + 2" 
									v-show="!loading && step==2"
								>
									<h2 class="mx-auto mb-4 lead">Required Products</h2>

									<div 
										class="col-md-12" 
										v-if="singleRequisitionData.products.length && singleRequisitionData.products.length==errors.products.length"
									>
										<div 
											class="card card-body" 
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
				                                  		:class="!errors.products[productIndex].product_id ? 'is-valid' : 'is-invalid'" 
				                                  		:disabled="singleRequisitionData.products.length > (productIndex+1)"
				                                  		@input="setRequiredProduct(productIndex)"
				                                  		@close="validateFormInput('product_id')" 
				                              		>
				                                	</multiselect>
				                                	<div 
					                                	class="invalid-feedback" 
					                                	style="display: block;" 
					                                	v-show="errors.products[productIndex].product_id"
				                                	>
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
																min="0" 
																:max="productVariation.available_quantity - productVariation.requested_quantity" 
																@change="validateFormInput('variations_quantity')" 
															>
														</div>
													</div>
												</div>
											</div>

											<div class="form-row">
												<div class="form-group col-md-12 text-center">
													<div 
														class="invalid-feedback" 
														style="display: block;" 
														v-show="errors.products[productIndex].variations_quantity"
													>
											        	{{ errors.products[productIndex].variations_quantity }}
											  		</div>
												</div>
											</div>
										</div>

										<div class="form-row">
											<div class="form-group col-md-6">
												<button 
													type="button" 
													class="btn waves-effect waves-light hor-grd btn-grd-primary btn-sm btn-block" 
													@click="addMoreProduct()"
												>
													More Product
												</button>
											</div>
											<div class="form-group col-md-6">
												<button 
													type="button" 
													class="btn waves-effect waves-light hor-grd btn-grd-info btn-sm btn-block" 
													@click="removeProduct()"
												>
													Remove Product
												</button>
											</div>
										</div>
									</div>

									<div class="col-md-12">
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

							        <div class="col-md-12">
										<div class="form-row">
											<div class="form-group col-md-12 text-center">
												<toggle-button 
													v-model="singleRequisitionData.delivery_service" 
													:value="false" 
													:width=200 
													:color="{checked: 'green', unchecked: 'blue'}"
													:labels="{checked: 'Delivery Service', unchecked: 'Self Agent'}" 
												/>
											</div>
										</div>

										<div 
										class="form-row" 
										v-if="!singleRequisitionData.delivery_service && singleRequisitionData.agent"
										>	
											<div class="form-group col-md-6">
												<label for="inputFirstName">Agent Name</label>
												<input type="text" 
													class="form-control" 
													v-model="singleRequisitionData.agent.name" 
													placeholder="Agent Name" 
													:class="!errors.agent.agent_name  ? 'is-valid' : 'is-invalid'" 
													@input="validateFormInput('agent_name')" 
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

										<div class="form-row" v-if="singleRequisitionData.delivery_service">
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

							        <div class="col-md-12">
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
												<span :class="[singleRequisitionData.status ? 'badge-success' : 'badge-danger', 'badge']">{{ singleRequisitionData.status ? 'Dispatched' : 'Pending' }}</span>
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

										<div class="form-row" v-if="singleRequisitionData.delivery && singleRequisitionData.status && singleRequisitionData.dispatch">
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
										
										<div class="form-row" v-if="singleRequisitionData.status && singleRequisitionData.dispatch">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Received :
											</label>
											<label class="col-sm-6 col-form-label">
												<span :class="[!unconfirmed(singleRequisitionData) ? 'badge-success' : 'badge-danger', 'badge']">{{ !unconfirmed(singleRequisitionData) ? 'Confirmed' : 'Not Yet' }}</span>
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
							// variations_quantity : ''
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
			this.fetchMerchantAllProducts();
			
		},
		
		methods : {
		
			fetchAllRequisitions() {
				
				this.query = '';
				this.error = '';
				this.loading = true;
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
						this.subscribeToChannel();
					});

			},
			fetchMerchantAllProducts() {
				
				this.query = '';
				this.error = '';
				this.loading = true;
				this.merchantAllProducts = [];
				
				axios
					.get('/api/products/')
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
							// product : {},
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
							// variations_quantity : ''
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
							// this.showSelectedTabProducts();
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
					"/api/search-requisitions/" + this.query + "/" + this.perPage + "?page=" + this.pagination.current_page
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

				console.log(this.requisitionsToShow);

				if (this.requisitionsToShow.length || this.allFetchedRequisitions.pending.data.length || this.allFetchedRequisitions.dispatched.data.length || (this.allFetchedRequisitions.all && this.allFetchedRequisitions.all.data.length)) {

					Echo.private(`new-dispatch.` + this.requisitionsToShow[0].merchant_id || this.allFetchedRequisitions.pending.data[0].merchant_id || this.allFetchedRequisitions.dispatched.data[0].merchant_id || this.allFetchedRequisitions.all.data[0].merchant_id)
				    .listen('RequisitionDispatched', (e) => {
				        
				        console.log(e);
				        
				        this.$toastr.w("Requisition has been dispatched", "Warning");

				    	let index = this.requisitionsToShow.findIndex(requisition => requisition.id === e.id);

				    	if (index > -1) {

				    		// 	Vue.set(this.requisitionsToShow, index, e);
				        	this.requisitionsToShow.splice(index, 1);
				        	this.allFetchedRequisitions.pending.data.splice(index, 1);
				        	this.allFetchedRequisitions.dispatched.data.push(e);
				    	
				    	}
				        
				    });

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
			showSelectedTabProducts() {
				
				if (this.currentTab=='pending') {
					this.requisitionsToShow = this.allFetchedRequisitions.pending.data;
					this.pagination = this.allFetchedRequisitions.pending;
				}
				else {
					this.requisitionsToShow = this.allFetchedRequisitions.dispatched.data;
					this.pagination = this.allFetchedRequisitions.dispatched;
				}

			},
			errorInArray(array = []) {
				
				if (array.length) {

					return array.some(element => Object.keys(element).length > 0);

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
					this.validateFormInput('variations_quantity');
				}


				if (!this.errors.subject && !this.errorInArray(this.errors.products)) {
					this.step += 1;
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
			addMoreProduct() {
				if (this.singleRequisitionData.products.length < this.merchantAllProducts.length) {

					this.singleRequisitionData.products.push({});
					this.errors.products.push({});

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
			setRequiredProduct(index) {
				if (this.singleRequisitionData.products[index].product && Object.keys(this.singleRequisitionData.products[index].product).length > 0) {
					this.singleRequisitionData.products[index].id = this.singleRequisitionData.products[index].product.id;
				}
			},
			unconfirmed(object) {

				if (object.status && object.dispatch && ((object.dispatch.hasOwnProperty('agent') && !object.dispatch.agent.receiving_confirmation) || (object.dispatch.hasOwnProperty('delivery') && !object.dispatch.delivery.receiving_confirmation))) {

					return true; 	// not confirmed

				}

				return false;  // confirmed
				
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
									this.errors.products[productIndex].product_quantity = 'Quantity is more than available';
								}
								else{
									// this.errors.products[productIndex].product_quantity = null;
									this.$delete(this.errors.products[productIndex], 'product_quantity');
								}

							}

						);
								
						if (!this.errorInArray(this.errors.products)) {
							this.submitForm = true;
						}

						break;

					case 'variations_quantity' :

						this.singleRequisitionData.products.forEach(

							(requiredProduct, productIndex) => {

								if (requiredProduct.product && requiredProduct.product.has_variations) {

									let totalVariationQuantity = requiredProduct.product.variations.reduce((total, current) => total + current.required_quantity || 0, 0

									);

									// console.log(totalVariationQuantity);

									if (requiredProduct.total_quantity != totalVariationQuantity) {
										this.errors.products[productIndex].variations_quantity = 'Total quantity should be equal to variations quantity';
									}
									else{
										
										// this.submitForm = true;
										this.$delete(this.errors.products[productIndex], 'variations_quantity');

									}

								}
								else {

									// this.submitForm = true;
									this.$delete(this.errors.products[productIndex], 'variations_quantity');

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