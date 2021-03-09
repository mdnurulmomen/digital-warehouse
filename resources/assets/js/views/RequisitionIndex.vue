
<template>

	<div class="pcoded-content">

		<breadcrumb 
			:title="'requisitions'" 
			:message="'All our requisitions'"
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

 												<div class="row d-flex align-items-center">										  	
											  		<div class="col-sm-3 text-left">	
											  			Requisitions List
											  		</div>
											  		<div class="col-sm-9 was-validated text-center">
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
											  	</div>
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
																			</button>

																			<button 
																				type="button" 
																				class="btn btn-grd-warning btn-icon"  
																				@click="showProductDispatchForm(content)" 
																				v-show="!content.status"
																			>
																				<i class="fas fa-truck"></i>
																			</button>

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
				                                  		track-by="product_id" 
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
														Total Available
													</label>
													<input 
														type="number" 
														class="form-control" 
														v-model.number="requiredProduct.available_quantity" 
														readonly="true"
													>
												</div>
											</div>

											<div class="card" v-if="requiredProduct.has_variations">
												<div class="card-body">
													<div 
														class="form-row" 
														v-for="(productVariation, variationIndex) in requiredProduct.variations" 
														:key="'required-product-' + productIndex + 'variation-' + variationIndex"
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
									v-bind:key="3" 
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
												{{ requiredProduct.product_name }}
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
					                              			label="name" 
					                                  		track-by="id" 
					                                  		:options="requiredProduct.addresses[productAddressIndex].container.shelves" 
					                                  		:multiple="true" 
					                                  		:close-on-select="false" 
					                                  		:clear-on-select="false" 
					                                  		:preserve-search="true" 
					                                  		:disabled="(requiredProduct.available_quantity > requiredProduct.quantity && requiredProduct.addresses[productAddressIndex].container.shelves.length < 2) || requiredProduct.available_quantity == requiredProduct.quantity"
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
									v-bind:key="'requisition-modal-step-' + 4" 
									v-show="!loading && step==4"
								>	
									<h2 class="mx-auto mb-4 lead">Deployment Details</h2>	
									
									<div class="form-group col-md-12 text-center">
										<span :class="[singleDispatchData.requisition.delivery ? 'badge-success' : 'badge-info', 'badge']">{{ singleDispatchData.requisition.delivery ? 'Delivery Service' : 'Agent Service' }}</span>
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
												<span :class="[singleRequisitionData.status ? 'badge-success' : 'badge-danger', 'badge']">
													{{ singleRequisitionData.status ? 'Dispatched' : 'Pending' }}
												</span>
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
														:key="'required-product-' + requiredProduct.id + productIndex"
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

										<div class="form-row" v-if="singleRequisitionData.status && singleRequisitionData.dispatch">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Dispatched on :
											</label>
											<label class="col-sm-6 col-form-label">
												{{ singleRequisitionData.dispatch.released_at }}
											</label>
										</div>

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

										<div class="form-row" v-if="singleRequisitionData.status && singleRequisitionData.dispatch && singleRequisitionData.dispatch.delivery">
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

										<div class="form-row" v-if="singleRequisitionData.status && singleRequisitionData.dispatch">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Received :
											</label>
											<label class="col-sm-6 col-form-label">
												<span :class="[!unconfirmed(singleRequisitionData) ? 'badge-success' : 'badge-danger', 'badge']">{{ !unconfirmed(singleRequisitionData) ? 'Confirmed' : 'Not Yet' }}</span>
											</label>
										</div>

										<div class="form-row" v-else>
											<label class="col-sm-12 col-form-label text-center">
												<span :class="[singleRequisitionData.status ? 'badge-success' : 'badge-danger', 'badge']">
													{{ singleRequisitionData.status ? 'Dispatched' : 'Not Dispatched Yet' }}
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
			{}
		],

		agent : {},

		delivery : {},
				
    };

    let singleDispatchData = {

		requisition : {},

		delivery : {},

		// agent : {}
				
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
	        	
	        	editor: ClassicEditor,

	        	submitForm : true,

	        	requisitionsToShow : [],
	        	allFetchedRequisitions : [],

	        	availableRequisitions : [],
	        	
	        	// availableProducts : [],

	        	pagination: {
		        	current_page: 1
		      	},

	        	singleDispatchData : singleDispatchData,
	        	singleRequisitionData : singleRequisitionData,

		   		errors : {
					// products : [],
					delivery : {},
					// agent : {}
				},

	            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

	        }

		},
		
		created(){
			
			this.fetchAllRequisitions();
			this.fetchAvailableRequisitions();

			Echo.private(`new-requisition`)
		    .listen('NewRequisitionMade', (e) => {
		        // console.log(e);
		        this.$toastr.w("New requisition arrives", "Warning");
		        this.allFetchedRequisitions.pending?.data.push(e);
		    });

		    Echo.private(`product-received`)
		    .listen('ProductReceived', (e) => {
		        
		        // console.log(e);
		        this.$toastr.s("Dispatched product has been received", "Success");
		    	
		    	let index = this.allFetchedRequisitions.dispatched?.data.findIndex(requisition => requisition.id === e.id && requisition.merchant_id === e.merchant_id);

		    	if (index > -1) {

	    			Vue.set(this.allFetchedRequisitions.dispatched.data, index, e);
		    	
		    	}
		    	else{

		    		let index2 = this.allFetchedRequisitions.pending?.data.findIndex(requisition => requisition.id === e.id && requisition.merchant_id === e.merchant_id);

		    		if (index2 > -1) {

		    			this.allFetchedRequisitions.pending.data.splice(index2, 1);
		    			this.allFetchedRequisitions.dispatched.data.push(e);
			    	
			    	}

		    	}

		    });	    

		},

		watch : {

			query : function(val){
				if (val==='') {
					this.fetchAllRequisitions();
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
			fetchAvailableRequisitions() {
				
				this.query = '';
				this.error = '';
				this.loading = true;
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
			showProductDispatchForm(object) {

				this.step = 1;
	        	this.submitForm = true;
	        	
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
							this.query !== '' ? this.searchData() : this.fetchAllRequisitions();
							
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
						// this.fetchAllRequisitions();
					});

			},
			verifyUserInput() {

				// this.validateFormInput('delivery_address');
				this.validateFormInput('delivery_price');
				this.validateFormInput('delivery_receipt');
				// this.validateFormInput('agent_receipt');

				if (this.errors.constructor === Object && Object.keys(this.errors).length < 2 && Object.keys(this.errors.delivery).length == 0) {

					// console.log('verified');
					return true;
				
				}

				return false;
			},
			searchData() {

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
			changeNumberContents() {
				
				this.pagination.current_page = 1;

				if (this.query === '') {
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
				else {
					this.requisitionsToShow = this.allFetchedRequisitions.dispatched.data;
					this.pagination = this.allFetchedRequisitions.dispatched;
				}

			},
			unconfirmed(object) {

				if (object.status && object.dispatch && ((object.dispatch.hasOwnProperty('agent') && !object.dispatch.agent.receiving_confirmation) || (object.dispatch.hasOwnProperty('delivery') && !object.dispatch.delivery.receiving_confirmation))) {

					return true; 	// not confirmed

				}

				return false;  // confirmed
				
			},
			nextPage() {
				
				if (this.step==1) {
					this.validateFormInput('requisition_id');
				}

				if (!this.errors.requisition_id && this.step < 4) {
					
					if (this.step==2) {

						const productRemains = this.singleDispatchData.requisition.products.some(
							requiredProduct => requiredProduct.available_quantity - requiredProduct.quantity > 0
						);

						if (productRemains) {
							this.step += 1;
						}
						else {
							this.setProductReleasedAddresses();
							this.step += 2;
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

			},
			showPendingContents() {
				this.currentTab = 'pending';
				this.showSelectedTabProducts();
			},
			showDispatchedContents() {
				this.currentTab = 'dispatched';
				this.showSelectedTabProducts();
			},
			removeSpace(productIndex) {	
				
				if (this.singleDispatchData.requisition.products.length && this.singleDispatchData.requisition.products[productIndex].addresses.length > 1) {
						
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
			nameWithCapitalized (name) {
				if (!name) return ''
			    name = name.toString()
			    return name.charAt(0).toUpperCase() + name.slice(1)
		    },
			objectNameWithCapitalized ({ subject, product_name, variation_name }) {
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
		      	else 
		      		return ''
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