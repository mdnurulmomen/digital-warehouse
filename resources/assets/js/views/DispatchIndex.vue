
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
 												<div class="row d-flex align-items-center text-center">										  	
											  		<div class="col-sm-3 form-group">	
															Dispatches List
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
																		<th>Confirmation</th>
																		<th>Actions</th>
																	</tr>
																</thead>
																<tbody>

																	<tr 
																		v-for="dispatchedReq in allDispatchedRequisition" 
																		:key="'dispatch-' + dispatchedReq.id"
																	>
																		<td>
																			{{ dispatchedReq.subject }}
																		</td>
																		<td>
																			{{ dispatchedReq.dispatch.released_at }}
																		</td>
																		<td>
																			<span 
																			:class="[!unconfirmed(dispatchedReq) ? 'badge-success' : 'badge-danger', 'badge']">
																				{{ !unconfirmed(dispatchedReq) ? 'Confirmed' : 'Not Yet' }}
																			</span>
																		</td>
																		<td>
																			<button 
																				type="button" 
																				class="btn btn-grd-info btn-icon"  
																				@click="showDispatchDetails(dispatchedReq)"
																			>
																				<i class="fas fa-eye"></i>
																			</button>
																		</td>
																	</tr>
																	<tr 
																  		v-show="!allDispatchedRequisition.length"
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
																		<th>Requisition</th>
																		<th>Released at</th>
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
																@click="query === '' ? fetchAllDispatches() : searchData()"
															>
																Reload
																<i class="fas fa-sync"></i>
															</button>
														</div>
														<div class="col-sm-8 col-12 text-right form-group">
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
			                              			v-model="singleDispatchedReqData.requisition"
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
											v-if="singleDispatchedReqData.hasOwnProperty('requisition') && Object.keys(singleDispatchedReqData.requisition).length > 0"
										>
											<div class="form-group col-md-12">
												<label for="inputFirstName">Description</label>
												<ckeditor 
					                              	class="form-control" 
					                              	:editor="editor" 
					                              	v-model="singleDispatchedReqData.requisition.description" 
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
										v-if="singleDispatchedReqData.hasOwnProperty('requisition') && Object.keys(singleDispatchedReqData.requisition).length > 0 && singleDispatchedReqData.requisition.products && singleDispatchedReqData.requisition.products.length"
									>
										<div 
											class="card card-body" 
											v-for="(requiredProduct, productIndex) in singleDispatchedReqData.requisition.products" 
											:key="'required-product-' + productIndex"
										>
											<div class="form-row">
												<div class="form-group col-md-4">
													<label for="inputFirstName">
														Product
													</label>
													<multiselect 
				                              			v-model="singleDispatchedReqData.requisition.products[productIndex]"
				                              			placeholder="Product Name" 
				                              			label="product_name" 
				                                  		track-by="product_id" 
				                                  		:custom-label="objectNameWithCapitalized" 
				                                  		:options="singleDispatchedReqData.requisition.products"
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

											<div class="card" v-if="requiredProduct.has_variations && requiredProduct.variations.length">
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
						                                  		:options="singleDispatchedReqData.requisition.products"
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
									v-bind:key="3" 
									v-show="!loading && step==3"
								>
									<h2 class="mx-auto mb-4 lead">Released Address</h2>

									<div 
										class="col-md-12" 
										v-if="singleDispatchedReqData.hasOwnProperty('requisition') && Object.keys(singleDispatchedReqData.requisition).length > 0 && singleDispatchedReqData.requisition.products && singleDispatchedReqData.requisition.products.length"
									>
										<div 
											class="card"
											v-for="(requiredProduct, productIndex) in singleDispatchedReqData.requisition.products" 
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
					                              			placeholder="Select Containers" 
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
					                              			placeholder="Parent Container" 
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
					                              			placeholder="Parent Container" 
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
										<span :class="[(singleDispatchedReqData.hasOwnProperty('requisition')
                                           && singleDispatchedReqData.requisition.delivery) ? 'badge-success' : 'badge-info', 'badge']">
											{{ (singleDispatchedReqData.hasOwnProperty('requisition')
                                           && singleDispatchedReqData.requisition.delivery) ? 'Delivery Service' : 'Agent Service' }}
										</span>
									</div>

									<div 
										class="col-sm-12" 
										v-if="singleDispatchedReqData.hasOwnProperty('requisition')
                                           && !singleDispatchedReqData.requisition.delivery && singleDispatchedReqData.requisition.agent"
									>
										<div class="form-row">
											<div class="form-group col-md-6">
												<label for="inputFirstName">Agent Name</label>
												<input type="text" 
													class="form-control" 
													v-model="singleDispatchedReqData.requisition.agent.name" 
													placeholder="Agent Name" 
													readonly="true" 
												>
											</div>

											<div class="form-group col-md-6">
												<label for="inputFirstName">Agent Mobile</label>
												<input type="text" 
													class="form-control" 
													v-model="singleDispatchedReqData.requisition.agent.mobile" 
													placeholder="Agent Mobile" 
													readonly="true" 
												>
											</div>

											<div class="form-group col-md-6">
												<label for="inputFirstName">Agent Code</label>
												<input type="text" 
													class="form-control" 
													v-model="singleDispatchedReqData.requisition.agent.code" 
													placeholder="Agent Code" 
													readonly="true" 
												>
											</div>
										</div>

										<!-- 
										<div class="form-row d-flex">
											<div class="form-group col-md-6">
												<img class="img-fluid" 
													:src="singleDispatchedReqData.agent.agent_receipt || ''"
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
										-->
									</div>

									<div 
										class="col-sm-12" 
										v-if="singleDispatchedReqData.hasOwnProperty('requisition')
                                           && singleDispatchedReqData.requisition.delivery && !singleDispatchedReqData.requisition.agent"
									>
										<div class="form-row">
											<div class="form-group col-md-12">
												<label for="inputFirstName">Address</label>
												<ckeditor 
					                              	class="form-control" 
					                              	:editor="editor" 
					                              	v-model="singleDispatchedReqData.requisition.delivery.address" 
					                              	:disabled="true" 
					                            >
				                              	</ckeditor>
											</div>
										</div>

										<div class="form-row d-flex">
											<div class="form-group col-md-6 text-center">
												<img class="img-fluid" 
													:src="singleDispatchedReqData.delivery.delivery_receipt || ''"
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
													v-model="singleDispatchedReqData.delivery.address" 
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
													v-model.number="singleDispatchedReqData.delivery.delivery_price" 
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
												{{ singleDispatchedReqData.subject }}
											</label>
										</div>

										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Description :
											</label>
											<label class="col-sm-6 col-form-label">
												<span v-html="singleDispatchedReqData.description"></span>
											</label>
										</div>

										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Requested on :
											</label>
											<label class="col-sm-6 col-form-label">
												{{ singleDispatchedReqData.created_at }}
											</label>
										</div>
									</div>

									<div class="tab-pane" id="requisition-product" role="tabpanel">
										<div 
											class="form-row" 
											v-if="singleDispatchedReqData.products && singleDispatchedReqData.products.length"
										>
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Product Detail :
											</label>
											<div class="col-sm-6">
												<div class="form-row">
													
													<div 
														class="col-md-12 ml-auto" 
														v-for="(dispatchedProduct, productIndex) in singleDispatchedReqData.products" 
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

									<div class="tab-pane" id="requisition-dispatch" role="tabpanel">

										<div class="form-row" v-if="singleDispatchedReqData.dispatch">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Dispatched on :
											</label>
											<label class="col-sm-6 col-form-label">
												{{ singleDispatchedReqData.dispatch.released_at }}
											</label>
										</div>

										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Service :
											</label>
											<label class="col-sm-6 col-form-label">
												<span :class="[singleDispatchedReqData.delivery ? 'badge-success' : 'badge-info', 'badge']">{{ singleDispatchedReqData.delivery ? 'Delivery Service' : 'Agent Service' }}</span>
											</label>
										</div>

										<div class="form-row" v-if="singleDispatchedReqData.delivery">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Delivery Address :
											</label>
											<label class="col-sm-6 col-form-label">
												<span v-html="singleDispatchedReqData.delivery.address"></span>
											</label>
										</div>

										<div class="form-row" v-if="singleDispatchedReqData.dispatch && singleDispatchedReqData.dispatch.delivery">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Delivery Receipt :
											</label>
											<label class="col-sm-6 col-form-label">
												<img 
													class="img-fluid" 
													:src="singleDispatchedReqData.dispatch.delivery.receipt_preview || ''"
													alt="delivery_receipt" 
												>
											</label>
										</div>

										<div class="form-row" v-if="singleDispatchedReqData.agent">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Agent Name :
											</label>
											<label class="col-sm-6 col-form-label">
												{{ singleDispatchedReqData.agent.name  }}
											</label>
										</div>

										<div class="form-row" v-if="singleDispatchedReqData.agent">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Agent Mobile :
											</label>
											<label class="col-sm-6 col-form-label">
												{{ singleDispatchedReqData.agent.mobile }}
											</label>
										</div>

										<div class="form-row" v-if="singleDispatchedReqData.agent">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Agent Code :
											</label>
											<label class="col-sm-6 col-form-label">
												{{ singleDispatchedReqData.agent.code }}
											</label>
										</div>

										<div class="form-row">
											<label class="col-sm-6 col-form-label font-weight-bold text-right">
												Received :
											</label>
											<label class="col-sm-6 col-form-label">
												<span :class="[!unconfirmed(singleDispatchedReqData) ? 'badge-success' : 'badge-danger', 'badge']">{{ !unconfirmed(singleDispatchedReqData) ? 'Confirmed' : 'Not Yet' }}</span>
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

    let singleDispatchedReqData = {

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
	        	
	        	editor: ClassicEditor,

	        	submitForm : true,

	        	// dispatchesToShow : [],
	        	allDispatchedRequisition : [],
	        	
	        	allRequisitions : [],

	        	pagination: {
		        	current_page: 1
		      	},

	        	singleDispatchedReqData : singleDispatchedReqData,

	        	errors : {
					// products : [],
					delivery : {},
					// agent : {}
				},

	            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('dispatch'),

	        }

		},
		
		created(){
			
			this.fetchAllDispatches();
			this.fetchAllRequisitions();

			Echo.private(`product-received`)
		    .listen('ProductReceived', (e) => {
		        
		        // console.log(e);
		        this.$toastr.s("Dispatched product has been received", "Success");
		    	
		    	let index = this.allDispatchedRequisition.findIndex(requisition => requisition.id === e.id && requisition.merchant_id === e.merchant_id);

		    	if (index > -1) {

	    			Vue.set(this.allDispatchedRequisition, index, e);
		    	
		    	}

		    });

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
				this.allDispatchedRequisition = [];
				
				axios
					.get('/api/dispatches/' + this.perPage + "?page=" + this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							this.allDispatchedRequisition = response.data.data;
							this.pagination = response.data;
							// this.dispatchesToShow = this.allDispatchedRequisition.pending.data;
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
				this.allDispatchedRequisition = [];
				this.pagination.current_page = 1;
				
				axios
				.get(
					"/api/search-dispatches/" + this.query + "/" + this.perPage + "?page=" + this.pagination.current_page
				)
				.then(response => {
					this.allDispatchedRequisition = response.data.all.data;
					// this.dispatchesToShow = this.allDispatchedRequisition.all.data;
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
				this.singleDispatchedReqData = {};  // reseting
				this.singleDispatchedReqData = { ...object };
				// this.singleDispatchedReqData = Object.assign({}, this.singleDispatchedReqData, object);
				$('#dispatch-view-modal').modal('show');
			},
			showDispatchCreateForm() {
				this.step = 1;
	        	this.submitForm = true;
	        	
				this.singleDispatchedReqData = {};  // reseting

				this.singleDispatchedReqData = {

					requisition : {},

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
					.post('/dispatches/' + this.perPage, this.singleDispatchedReqData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Requisition has been dispatched", "Success");
							this.query !== '' ? this.searchData() : this.allDispatchedRequisition = response.data.data;
							
							// this.allDispatchedRequisition = response.data;
							// this.query !== '' ? this.searchData() : this.showSelectedTabProducts();
							
							let index = this.allRequisitions.findIndex(requisition => requisition.id == this.singleDispatchedReqData.requisition.id && requisition.merchant_id == this.singleDispatchedReqData.requisition.merchant_id);

					    	if (index > -1) {

					    		this.allRequisitions.splice(index, 1);

					    	}

							$('#dispatch-createOrEdit-modal').modal('hide');
						}
					})
					.catch(error => {
						console.log(error);
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
				// this.validateFormInput('agent_receipt');

				if (this.errors.constructor === Object && Object.keys(this.errors).length < 4 && Object.keys(this.errors.delivery).length == 0) {

					return true;
				
				}

				return false;
			},
			unconfirmed(object) {

				if (object.hasOwnProperty('dispatch') && ((object.dispatch.hasOwnProperty('agent') && !object.dispatch.agent.receiving_confirmation) || (object.dispatch.hasOwnProperty('delivery') && !object.dispatch.delivery.receiving_confirmation))) {

					return true; 	// not confirmed

				}

				return false;  // confirmed
				
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
				if (!this.errors.requisition_id && this.step < 4) {
					
					if (this.step==2) {

						const productRemains = this.singleDispatchedReqData.requisition.products.some(
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
		/*
			errorInArray(array = []) {
				
				if (array.length) {

					return array.some(element => Object.keys(element).length > 0);

				}

				return false;
			},
		*/
			setProductReleasedAddresses() {

				this.singleDispatchedReqData.requisition.products.forEach(
							
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
			removeSpace(productIndex) {	
				
				if (this.singleDispatchedReqData.requisition.products.length && this.singleDispatchedReqData.requisition.products[productIndex].addresses.length > 1) {
						
					this.singleDispatchedReqData.requisition.products[productIndex].addresses.pop();
				
				}

			},
			nameWithCapitalized (name) {
				if (!name) return ''
			    name = name.toString()
			    return name.charAt(0).toUpperCase() + name.slice(1)
		    },
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

				// console.log(this.singleDispatchedReqData.requisition.products);

				if (this.singleDispatchedReqData.requisition.products && this.singleDispatchedReqData.requisition.products.length) {

					this.errors.products = [];

					this.singleDispatchedReqData.requisition.products.forEach(
							
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
			createImage(file, previewName) {
                let reader = new FileReader();
                
                reader.onload = (evnt) => {

                	// if (previewName === 'delivery_receipt') {

                		this.singleDispatchedReqData.delivery.delivery_receipt = evnt.target.result;
                	
                	// }
                	// else {

                 		// this.singleDispatchedReqData.agent.agent_receipt = evnt.target.result;
                	
                	// }
                
                };

                reader.readAsDataURL(file);
            },
			validateFormInput (formInputName) {

				this.submitForm = false;

				switch(formInputName) {

					case 'requisition_id' :

						if (!this.singleDispatchedReqData.hasOwnProperty('requisition') || Object.keys(this.singleDispatchedReqData.requisition).length==0) {
							this.errors.requisition_id = 'Requisition is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'requisition_id');
						}

						break;

					/*
					case 'total_dispatched_quantity' :

						if (this.singleDispatchedReqData.requisition.products && this.singleDispatchedReqData.requisition.products.length) {

							this.singleDispatchedReqData.requisition.products.forEach(
							
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

						if (this.singleDispatchedReqData.requisition.products && this.singleDispatchedReqData.requisition.products.length) {

							this.singleDispatchedReqData.requisition.products.forEach(
							
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

						if (this.singleDispatchedReqData.requisition.hasOwnProperty('delivery')) {

							if (Object.keys(this.singleDispatchedReqData.delivery).length==0 || !this.singleDispatchedReqData.delivery.address) {
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

						if (this.singleDispatchedReqData.hasOwnProperty('requisition') && this.singleDispatchedReqData.requisition.hasOwnProperty('delivery')) {

							if (Object.keys(this.singleDispatchedReqData.delivery).length==0 || !this.singleDispatchedReqData.delivery.delivery_price || this.singleDispatchedReqData.delivery.delivery_price < 1) {
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

						if (this.singleDispatchedReqData.hasOwnProperty('requisition') && this.singleDispatchedReqData.requisition.hasOwnProperty('delivery')) {

							if (Object.keys(this.singleDispatchedReqData.delivery).length==0 || !this.singleDispatchedReqData.delivery.delivery_receipt) {
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

						if (this.singleDispatchedReqData.requisition.hasOwnProperty('agent')) {

							if (Object.keys(this.singleDispatchedReqData.agent).length==0 || !this.singleDispatchedReqData.agent.agent_receipt) {
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

					case 'description' :

						if (this.singleDispatchedReqData.description && !this.singleDispatchedReqData.description.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.description = 'No special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'description');
						}

						break;

					

					case 'product_quantity' :

						this.singleDispatchedReqData.products.forEach(
							
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
					this.dispatchesToShow = this.allDispatchedRequisition.pending.data;
					this.pagination = this.allDispatchedRequisition.pending;
				}
				else {
					this.dispatchesToShow = this.allDispatchedRequisition.dispatched.data;
					this.pagination = this.allDispatchedRequisition.dispatched;
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
				if (this.singleDispatchedReqData.products.length < 3) {

					this.singleDispatchedReqData.products.push({});
					this.errors.product_id.push(null);
					this.errors.product_quantity.push(null);

				}
			},
			removeProduct() {
					
				if (this.singleDispatchedReqData.products.length > 1) {

					this.singleDispatchedReqData.products.pop();
					this.errors.product_id.pop();
					this.errors.product_quantity.pop();
				
				}
				
			},
		*/
			
		/*
			setRequiredProduct(index) {
				if (this.singleDispatchedReqData.products[index].product && Object.keys(this.singleDispatchedReqData.products[index].product).length > 0) {
					this.singleDispatchedReqData.products[index].id = this.singleDispatchedReqData.products[index].product.id;
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