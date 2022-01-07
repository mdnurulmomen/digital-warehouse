
<template v-if="userHasPermissionTo('view-warehouse-index')">
	<div class="pcoded-content">
		<breadcrumb 
			:icon="'fas fa-warehouse'"
			:title="'warehouses'" 
			:message="'All our warehouses'"
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
											  		v-if="userHasPermissionTo('view-warehouse-index') || userHasPermissionTo('create-warehouse')"
											  		:query="query" 
											  		:caller-page="'warehouse'" 
											  		:required-permission="'warehouse'" 
											  		:disable-add-button="(allContainers.length==0 || allRentPeriods.length==0 || allStorageTypes.length==0 || allWarehouseOwners.length==0 || formSubmitted) ? true : false" 
											  		
											  		@showContentCreateForm="showContentCreateForm" 
											  		@searchData="searchData($event)" 
											  		@fetchAllContents="fetchAllWarehouses"
											  	></search-and-addition-option>
											</div>
											
											<div class="col-sm-12 col-lg-12">
										  		<tab 
										  			v-show="query === ''" 
										  			:tab-names="['approved', 'pending', 'trashed']" 
										  			:current-tab="currentTab" 

										  			@showApprovedContents="showApprovedContents" 
										  			@showPendingContents="showPendingContents" 
										  			@showTrashedContents="showTrashedContents" 
										  		></tab>

										  		<loading v-show="loading"></loading>

										  		<table-with-soft-delete-option 
										  			:query="query" 
										  			:loading="loading"  
										  			:per-page="perPage"  
										  			:form-submitted="formSubmitted" 
										  			:required-permission="'warehouse'" 
										  			:column-names="['name', 'email', 'mobile', 'status']" 
										  			:column-values-to-show="['name', 'email', 'mobile', 'status']" 
										  			:contents-to-show = "contentsToShow" 
										  			:pagination = "pagination" 
										  			:current-content="singleWarehouseData"

										  			@showContentDetails="showContentDetails($event)" 
										  			@openContentEditForm="openContentEditForm($event)" 
										  			@openContentDeleteForm="openContentDeleteForm($event)" 
										  			@openContentRestoreForm="openContentRestoreForm($event)" 
										  			@changeNumberContents="changeNumberContents($event)" 
										  			@fetchAllContents="fetchAllWarehouses" 
										  			@searchData="searchData" 
										  		>	
										  		</table-with-soft-delete-option>
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

		<!-- modal-createOrEdit-warehouse -->
		<div 
			class="modal fade" id="warehouse-createOrEdit-modal" 
			v-if="userHasPermissionTo('create-warehouse') || userHasPermissionTo('update-warehouse')"
		>
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
					  	<h4 class="modal-title">
					  		{{ createMode ? 'Create Warehouse' : 'Edit ' + singleWarehouseData.name }}
					  	</h4>
					  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					    	<span aria-hidden="true">&times;</span>
						</button>
					</div>


					<div class="modal-body">
						<div class="row" v-show="!loading">
							<div class="col-sm-12">
							  	<div class="card">
								    <div class="card-header text-center">
								      	<div class="progress">
								        	<div class="progress-bar bg-info" style="width:20%" v-show="step>=1">
								          		Profile
								        	</div>
								        	<div class="progress-bar bg-danger" style="width:10%" v-show="step>=2">
								          		Contract
								        	</div>
								        	<div class="progress-bar bg-warning" style="width:10%" v-show="step>=3">
								          		Features
								        	</div>
								        	<div class="progress-bar bg-primary" style="width:20%" v-show="step>=4">
								          		Storages
								        	</div>
								        	<div class="progress-bar bg-success" style="width:30%" v-show="step>=5">
								          		Containers
								        	</div>
								        	<div class="progress-bar bg-default" style="width:10%" v-show="step==6">
								          		Permissions
								        	</div>
								      	</div>
								    </div>
							  	</div>
							</div>
						</div>
						
				  		<!-- form start -->
						<form 
							class="form-horizontal" 
							v-on:submit.prevent="createMode ? storeWarehouse() : updateWarehouse()" 
							novalidate
						>

							<input type="hidden" name="_token" :value="csrf">

							<transition-group name="fade">
								<!-- Warehouse Profile -->
								<div 
									class="row" 
									v-bind:key="1" 
									v-show="!loading && step==1"
								>								  	
							        <h2 class="mx-auto mb-4 lead">Warehouse Profile</h2>
							      
						          	<div class="col-sm-12">
				                    	<div class="card">
									    	<div class="card-body">
									    		<div class="form-row d-flex align-items-center">
													<div class="form-group col-md-6">
														<img class="img-fluid" 
															:src="singleWarehouseData.site_map_preview || ''"
															alt="site_map_preview" 
														>
													</div>
													
													<div class="form-group col-md-6">
														<div class="custom-file">
														    <input type="file" 
														    	class="form-control custom-file-input" 
																:class="!errors.warehouse.site_map_preview  ? 'is-valid' : 'is-invalid'" 
													    	 	@change="onSiteMapChange" 
													    	 	accept="image/*"
														    >
														    <label class="custom-file-label">Choose Preview...</label>
														    <div class="invalid-feedback">
														    	{{ errors.warehouse.site_map_preview }}
														    </div>
													  	</div>
													</div>
												</div>
												<div class="form-row">
													<div class="form-group col-md-12 text-right">
														<toggle-button 
															v-model="singleWarehouseData.active" 
															:width=100 
															:sync="true"
															:color="{checked: 'green', unchecked: 'red'}"
															:labels="{checked: 'Active', unchecked: 'Pending'}"
														/>
													</div>
												</div>
									    	</div>
									  	</div>

									  	<div class="form-row">
											<div class="col-md-6 form-group">
												<label>Name</label>
												<input type="text" 
													class="form-control" 
													v-model="singleWarehouseData.name" 
													placeholder="Name should be unique" 
													:class="!errors.warehouse.name  ? 'is-valid' : 'is-invalid'" 
													@blur="validateFormInput('name')" 
													required="true" 
												>

												<div class="invalid-feedback">
										        	{{ errors.warehouse.name }}
										  		</div>
											</div>

											<div class="col-md-6 form-group">
												<label for="phone">Mobile</label>
												<input type="tel" 
													class="form-control" 
													v-model="singleWarehouseData.mobile" 
													placeholder="Mobile should be unique" 
													autocomplete="new-password" 
													:class="!errors.warehouse.mobile ? 'is-valid' : 'is-invalid'" 
													@blur="validateFormInput('mobile')" 
													required="true" 
												>

												<div class="invalid-feedback">
										        	{{ errors.warehouse.mobile }}
										  		</div>
											</div>
											
											<!-- 
											<div class="col-md-6 form-group">
												<label>Short Code (for recognizing)</label>
												<input type="text" 
													class="form-control" 
													v-model="singleWarehouseData.code" 
													placeholder="Code should be unique" 
													:class="!errors.warehouse.code  ? 'is-valid' : 'is-invalid'" 
													@blur="validateFormInput('code')" 
													required="true" 
												>

												<div class="invalid-feedback">
										        	{{ errors.warehouse.code }}
										  		</div>
											</div>
 											-->
										</div>

			                            <div class="form-row">    
											<div class="col-md-6 form-group">
												<label>Username</label>
												<input type="text" 
													class="form-control" 
													v-model="singleWarehouseData.user_name" 
													placeholder="Username should be unique" 
													:class="!errors.warehouse.user_name  ? 'is-valid' : 'is-invalid'" 
													@blur="validateFormInput('user_name')" 
													required="true" 
												>

												<div class="invalid-feedback">
										        	{{ errors.warehouse.user_name }}
										  		</div>
											</div>
											<div class="col-md-6 form-group">
												<label>Email</label>
												<input type="email" 
													class="form-control" 
													v-model="singleWarehouseData.email" 
													placeholder="Email should be unique" 
													:class="!errors.warehouse.email  ? 'is-valid' : 'is-invalid'" 
													@blur="validateFormInput('email')" 
													required="true" 
												>

												<div class="invalid-feedback">
										        	{{ errors.warehouse.email }}
										  		</div>
											</div>	
				                        </div>

				                        <div class="form-row">
											<div class="col-md-12 form-group">
												<label>Location</label>
												<!-- 
												<vue-google-autocomplete
					                    			id="restaurantEditAddress"
                									classname="form-control"
					                        		placeholder="Start typing"
					                        		v-on:placechanged="getAddressData"
							                        types="(cities)"
							                        country="bd"
					                    		>
					                    		</vue-google-autocomplete>

					                    		<div class="invalid-feedback">
										        	{{ errors.warehouse.user_name }}
										  		</div>
												-->
											</div>  
				                        </div>

				                        <div class="form-row">
											<div class="form-group col-sm-12 text-right">
												<a data-toggle="collapse" href="#user-password">
													<i class="fa fa-2x" :class="createMode ? 'fa-angle-up' : 'fa-angle-down'" @click="changeArrow" data-toggle="tooltip" data-placement="top" title="Show Password"></i>
												</a>
											</div>
										</div>

										<div id="user-password" class="panel-collapse collapse" :class="createMode ? 'show' : ''">
					                        <div class="form-row">
					                      		<div class="col-md-6 form-group">
													<label>Password</label>
													<input type="password" 
														class="form-control" 
														v-model="singleWarehouseData.password" 
														placeholder="Password length should be min 8" 
														autocomplete="new-password" 
														:class="!errors.warehouse.password  ? 'is-valid' : 'is-invalid'" 
														@blur="validateFormInput('password')" 
														:required="createMode" 
													>

													<div class="invalid-feedback">
											        	{{ errors.warehouse.password }}
											  		</div>
												</div>

					                          	<div class="col-md-6 form-group">
													<label>Confirm Password</label>
													<input type="password" 
														class="form-control" 
														v-model="singleWarehouseData.password_confirmation" 
														placeholder="Confirm your password" 
														autocomplete="new-password" 
														:class="!errors.warehouse.password_confirmation  ? 'is-valid' : 'is-invalid'" 
														@blur="validateFormInput('password_confirmation')" 
														:required="createMode" 
													>

													<div class="invalid-feedback">
											        	{{ errors.warehouse.password_confirmation }}
											  		</div>
												</div>
					                        </div>
										</div>
						          	</div>  
							        
						          	<div class="col-sm-12 p-3 border text-right">
						          		<div class="text-danger small" v-show="!submitForm">
									  		Please input required fields
							          	</div>
							          	<button 
							          	type="button" 
							          	v-on:click="nextPage" 
							          	class="btn btn-outline-secondary btn-sm btn-round" 
							          	data-toggle="tooltip" data-placement="top" title="Next"
							          	>
					                    	<i class="fa fa-2x fa-angle-double-right" aria-hidden="true"></i>
					                  	</button>
						          	</div>
								</div>

								<!-- Warehouse Owner -->
								<div 
									class="row" 
									v-bind:key="2" 
									v-show="step==2"
								>								  	
							        <h2 class="ml-auto mr-auto mb-4 lead">Owner & Deal</h2>
							      
						          	<div class="col-sm-12">
						          		<div class="form-row">	
		                              		<div class="col-md-12 form-group">
			                              		<label>Owner</label>
				                              	
			                              		<!-- <div class="d-flex flex-row align-content-center"> -->
		                              				<multiselect 
			                                  			v-model="ownerObject"
			                                  			placeholder="Warehouse Owner" 
				                                  		label="user_name" 
				                                  		track-by="id" 
				                                  		:options="allWarehouseOwners" 
				                                  		:custom-label="objectNameWithCapitalized" 
				                                  		:required="true" 
				                                  		class="form-control p-0" 
				                                  		:class="!errors.warehouse.owner  ? 'is-valid' : 'is-invalid'"
				                                  		:allow-empty="false"
				                                  		selectLabel = "Press/Click"
				                                  		deselect-label="Can't remove single value" 
				                                  		@input="setWarehouseOwner()"
				                                  		@close="validateFormInput('owner')"
			                                  		>
				                                	</multiselect>
		                              				<!-- 
		                              				<button type="button" class="btn btn-secondary ml-1" data-toggle="modal" data-target="#user-createOrEdit-modal">
				                                    <i class="fa fa-plus-circle" aria-hidden="true"></i>
				                                    	owner
				                                	</button>
				                                	 -->
			                              		<!-- </div> -->
			                              		<div 
				                                  	class="invalid-feedback" 
				                                  	style="display: block;" 
				                                  	v-show="errors.warehouse.owner"
				                                >
											        {{ errors.warehouse.owner }}
											  	</div>
		                              		</div>
			                            </div>
				                          
				                        <div class="form-row">
				                          	<div class="col-md-12 form-group">
												<label>Warehouse Deal (rental/contractual)</label>
				                              	<ckeditor 
					                              	class="form-control" 
					                              	:editor="editor" 
					                              	v-model="singleWarehouseData.warehouse_deal"
					                              	:class="!errors.warehouse.deal  ? 'is-valid' : 'is-invalid'"
					                              	@blur="validateFormInput('deal')"
					                            >
				                              	</ckeditor>

				                              	<div 
				                                  	class="invalid-feedback" 
				                                >
											        {{ errors.warehouse.deal }}
											  	</div>
					                        </div>
				                        </div> 
						          	</div>  
							        
						          	<div class="col-sm-12 p-3 border">
						          		<div class="row">
					          				<div class="col-6">
							                  	<button 
							                  		type="button" 
							                  		v-on:click="step-=1" 
							                  		class="btn btn-outline-secondary btn-sm btn-round" 
							                  		data-toggle="tooltip" data-placement="top" title="Previous"
							                  	>
								                    <i class="fa fa-2x fa-angle-double-left" aria-hidden="true"></i>
							                  	</button>
							                </div>
						          			
						          			<div class="col-6 text-right">
							                	<div class="text-danger small" 
							                		v-show="!submitForm"
							                	>
											  		Please input required fields
									          	</div>
							                  	<button 
							                  		type="button" 
							                  		v-on:click="nextPage" 
							                  		class="btn btn-outline-secondary btn-sm btn-round" 
							                  		data-toggle="tooltip" data-placement="top" title="Next"
							                  	>
								                    <i class="fa fa-2x fa-angle-double-right" aria-hidden="true"></i>
							                  	</button>
								          	</div>
						          		</div>
						          	</div>
								</div>

								<!-- Previews & Features -->
								<div 
									class="row" 
									v-bind:key="3" 
									v-show="step==3"
								>								  	
							        <h2 class="ml-auto mr-auto mb-4 lead">Previews & Features</h2>
							      
						          	<div class="col-sm-12">
				                    	<div class="card">
									    	<div class="card-body">

									    		<div class="form-row" v-if="singleWarehouseData.previews.length">
													<div 
														class="form-group col-sm-4"
														v-for="(warehousePreview, index) in singleWarehouseData.previews" 
														:key="'D' + index + warehousePreview.id"
													>
														<button 
															type="button" 
															style="right:0;z-index:99;" 
															class="close position-absolute text-danger" 
															data-toggle="tooltip" data-placement="top" title="Remove Preview"
															@click="removeWarehousePreview(index)"
														>
															<i class="fa fa-times-circle" aria-hidden="true"></i>
														</button>
														<img 
															:src="warehousePreview.preview || ''" 
															class="img-fluid position-relative w-100 h-100" alt="warehouse preview" 
														>
													</div>
												</div>

												<div class="form-row" v-else>
													<div class="form-group col-md-12 text-center">
														<span class="text-danger">No previews yet</span>	
													</div>
												</div>

												<div class="form-row">	
													<div class="form-group col-md-12 text-center">
														<div class="custom-file">
														    <input type="file" 
														    	class="form-control custom-file-input" 
																:class="!errors.warehouse.preview ? 'is-valid' : 'is-invalid'" 
													    	 	@change="onWarehousePreviewChange" 
													    	 	accept="image/*"
														    >
														    <label class="custom-file-label">Choose Preview...</label>
														    <div class="invalid-feedback">
														    	{{ errors.warehouse.preview }}
														    </div>
													  	</div>
													</div>
												</div>

									    	</div>
									  	</div>

									  	<div class="form-row">
				                          	<div class="col-md-12 form-group">
												<label>Warehouse Features</label>
				                              	<ckeditor 
					                              	class="form-control" 
					                              	:editor="editor" 
					                              	v-model="singleWarehouseData.feature.features"
					                              	:class="!errors.warehouse.feature  ? 'is-valid' : 'is-invalid'"
					                              	@blur="validateFormInput('warehouse_feature')"
					                            >
				                              	</ckeditor>

				                              	<div 
				                                  	class="invalid-feedback" 
				                                >
											        {{ errors.warehouse.feature }}
											  	</div>
					                        </div>
				                        </div>
						          	</div>  

						          	<div class="col-sm-12 p-3 border">
						          		<div class="row">
					          				<div class="col-6">
							                  	<button 
							                  		type="button" 
							                  		v-on:click="step-=1"
							                  		class="btn btn-outline-secondary btn-sm btn-round" 
							                  		data-toggle="tooltip" data-placement="top" title="Previous"
							                  	>
								                    <i class="fa fa-2x fa-angle-double-left" aria-hidden="true"></i>
							                  	</button>
							                </div>
						          			
						          			<div class="col-6 text-right">
							                	<div class="text-danger small" 
							                		v-show="!submitForm"
							                	>
											  		Please input required fields
									          	</div>
							                  	<button 
							                  		type="button" 
							                  		v-on:click="nextPage" 
							                  		class="btn btn-outline-secondary btn-sm btn-round" 
							                  		data-toggle="tooltip" data-placement="top" title="Next"
							                  	>
								                    <i class="fa fa-2x fa-angle-double-right" aria-hidden="true"></i>
							                  	</button>
								          	</div>
						          		</div>
						          	</div>
								</div>

								<!-- Warehouse Storages -->
								<div 
									class="row" 
									v-bind:key="4" 
									v-show="step==4"
								>								  	
							        <h2 class="ml-auto mr-auto mb-4 lead">Storages</h2>
							      
						          	<div class="col-sm-12">
						          		<div class="form-row">	
		                              		<div 
			                              		class="col-md-12 form-group" 
			                              		v-if="singleWarehouseData.storages.length"
		                              		>
			                              		<transition-group name="new-storage">
				                              		<div 
				                              			class="card" 
				                              			v-for="(warehouseStorageType, index) in singleWarehouseData.storages" 
				                              			:key="'E' + index + warehouseStorageType.id"
				                              		>
												    	<div class="card-body">

												    		<div class="form-row">
												    			<div class="form-group col-sm-12">
												    				<label>Storage Type</label>
												    				<multiselect 
							                                  			v-model="singleWarehouseData.storages[index].storage_type"
							                                  			placeholder="Storage Type" 
								                                  		label="name" 
								                                  		track-by="id" 
								                                  		:options="allStorageTypes" 
								                                  		:custom-label="objectNameWithCapitalized" 
								                                  		:required="true" 
								                                  		class="form-control p-0" 
								                                  		:class="!errors.warehouse.storage_types[index] ? 'is-valid' : 'is-invalid'"
								                                  		:allow-empty="false"
								                                  		selectLabel = "Press/Click"
								                                  		deselect-label="Can't remove single value"
								                                  		@close="validateFormInput('storage_type')"
							                                  		>
								                                	</multiselect>

								                                	<div 
									                                	class="invalid-feedback" 
									                                	style="display: block;" 
									                                	v-show="errors.warehouse.storage_types[index]"
								                                	>
																    	{{ errors.warehouse.storage_types[index] }}
																    </div>
												    			</div>
												    		</div>

												    		<div 
												    			class="form-row" 
												    			v-if="singleWarehouseData.storages[index].previews && singleWarehouseData.storages[index].previews.length"
												    		>
																<div 
																	class="form-group col-sm-4" 
																	v-for="(warehouseStoragePreview, index2) in singleWarehouseData.storages[index].previews" 
																	:key="index + index2 + warehouseStoragePreview.preview"
																>	
																	<button 
																		type="button" 
																		style="right:0;z-index:9;" 
																		class="close position-absolute text-danger" 
																		@click="removeStoragePreview(index, index2)" 
																		data-toggle="tooltip" data-placement="top" title="Remove Preview"
																	>
																		<i class="fa fa-times-circle" aria-hidden="true"></i>
																	</button>
																	<img 
																		:src="warehouseStoragePreview.preview || ''" 
																		class="img-fluid position-relative w-100 h-100" alt="storage preview" 
																	/>
																</div>
															</div>

															<div class="form-row" v-else>
																<div class="form-group col-md-12 text-center">
																	<span class="text-danger">No previews yet</span>	
																</div>
															</div>

															<div class="form-row">
																<div class="form-group col-sm-12 text-center">	
																	<div class="custom-file">
																	    <input type="file" 
																	    	class="form-control custom-file-input" 
																			:class="!errors.warehouse.storage_preview ? 'is-valid' : 'is-invalid'" 
																    	 	@change="onStoragePreviewChange($event, index)" 
																    	 	accept="image/*"
																	    >
																	    <label class="custom-file-label">Choose Preview...</label>
																	    <div class="invalid-feedback">
																	    	{{ errors.warehouse.storage_preview }}
																	    </div>
																  	</div>	
																</div>
															</div>

															<div class="form-row" v-if="singleWarehouseData.storages[index].feature">
																<div class="col-md-12 form-group">
																	<label>Features</label>

																	<ckeditor 
										                              	class="form-control" 
										                              	:editor="editor" 
										                              	v-model="singleWarehouseData.storages[index].feature.features"
										                              	:class="!errors.warehouse.storage_features[index]  ? 'is-valid' : 'is-invalid'"
										                              	@blur="validateFormInput('storage_feature')"
										                            >
									                              	</ckeditor>

																	<div class="invalid-feedback">
																        {{ errors.warehouse.storage_features[index] }}
																  	</div>
																</div>
															</div>

												    	</div>
												  	</div>
											  	</transition-group>
		                              		</div>

											<div class="col-sm-12 form-group text-center" v-else>
												<span class="text-danger">No storages found yet</span>	
											</div>
											
		                              		<div class="col-sm-12 form-group">
											  	<div class="form-row">
													<div class="form-group col-sm-6">
														<button 
															type="button"  
															@click="addStorage" 
															class="btn btn-success btn-block btn-sm" 
															data-toggle="tooltip" data-placement="top" title="More Storage"
														>
															More Storage
														</button>	
													</div>

													<div class="form-group col-sm-6">
														<button 
															type="button" 
															@click="removeStorage" 
															class="btn btn-danger btn-block btn-sm" 
															:disabled="singleWarehouseData.storages.length<=1" 
															data-toggle="tooltip" data-placement="top" title="Remove Storage"
														>
															Remove Storage
														</button>	
													</div>
												</div>
		                              		</div>
			                            </div>   
						          	</div>  
							        
						          	<div class="col-sm-12 p-3 border">
						          		<div class="row">
					          				<div class="col-6">
							                  	<button type="button" 
							                  		v-on:click="step-=1" 
							                  		class="btn btn-outline-secondary btn-sm btn-round" 
							                  		data-toggle="tooltip" data-placement="top" title="Previous"
							                  	>
								                    <i class="fa fa-2x fa-angle-double-left" aria-hidden="true"></i>
							                  	</button>
							                </div>
						          			
						          			<div class="col-6 text-right">
							                	<div class="text-danger small" 
							                		v-show="!submitForm"
							                	>
											  		Please input required fields
									          	</div>
							                  	<button type="button" 
							                  		v-on:click="nextPage"
							                  		class="btn btn-outline-secondary btn-sm btn-round" 
							                  		data-toggle="tooltip" data-placement="top" title="Next"
							                  	>
								                    <i class="fa fa-2x fa-angle-double-right" aria-hidden="true"></i>
							                  	</button>
								          	</div>
						          		</div>
						          	</div>
								</div>

								<!-- Warehouse Containers -->
								<div 
									class="row" 
									v-bind:key="5" 
									v-show="step==5"
								>								  	
							        <h2 class="ml-auto mr-auto mb-4 lead">Containers</h2>
							      
						          	<div class="col-sm-12">
						          		<div class="form-row">	
		                              		<div 
		                              			class="col-md-12 form-group"
	                              		 		v-if="singleWarehouseData.containers.length && singleWarehouseData.containers.length==errors.warehouse.containers.length"
		                              		>
			                              		<transition-group name="new-container">
				                              		<div 
				                              			class="card" 
				                              			v-for="(warehouseContainer, containerIndex) in singleWarehouseData.containers" 
				                              			:key="'F' + containerIndex + warehouseContainer.id"
				                              		>
												    	<div class="card-body">
												    		<div class="form-row">
												    			<div class="form-group col-sm-6">
												    				<label>Container Type</label>

												    				<multiselect 
							                                  			v-model="singleWarehouseData.containers[containerIndex].container"
							                                  			placeholder="Container Type" 
								                                  		label="name" 
								                                  		track-by="id" 
								                                  		:options="allContainers" 
								                                  		:custom-label="objectNameWithCapitalized" 
								                                  		:required="true" 
								                                  		class="form-control p-0" 
								                                  		:class="!errors.warehouse.containers[containerIndex].container ? 'is-valid' : 'is-invalid'"
								                                  		:allow-empty="false"
								                                  		selectLabel = "Press/Click"
								                                  		deselect-label="Can't remove single value"
								                                  		@close="validateFormInput('container')" 
								                                  		@input="changeContainerRents(containerIndex)"
							                                  		>
								                                	</multiselect>

								                                	<div 
									                                	class="invalid-feedback" 
									                                	style="display: block;" 
									                                	v-show="errors.warehouse.containers[containerIndex].container"
								                                	>
																    	{{ errors.warehouse.containers[containerIndex].container }}
																    </div>
												    			</div>

												    			<div class="col-sm-6 form-group">	
												    				<label for="phone">Quantity</label>

																	<input type="number" 
																		class="form-control" 
																		v-model.number="singleWarehouseData.containers[containerIndex].quantity" 
																		placeholder="Lenght of container" 
																		:min="singleWarehouseData.containers[containerIndex].engaged_quantity + singleWarehouseData.containers[containerIndex].partially_engaged" 
																		:class="!errors.warehouse.containers[containerIndex].container_quantity ? 'is-valid' : 'is-invalid'" 
																		@blur="validateFormInput('container_quantity')" 
																		required="true" 
																	>

																	<div class="invalid-feedback">
															        	{{ errors.warehouse.containers[containerIndex].container_quantity }}
															  		</div>
												    			</div>
												    		</div>

												    		<div 
												    			class="form-row"
												    			v-if="singleWarehouseData.containers.length==errors.warehouse.containers.length"
												    		>	
																<div class="col-sm-12 form-group">
																	<label for="phone">Rents</label>
																	<ul class="nav nav-tabs tabs justify-content-center" role="tablist">
																		<li 
																			class="nav-item"
																			v-for="(rentPeriod, rentIndex) in allRentPeriods" 
																			:key="'y-' + rentPeriod.id + rentPeriod.name" 
																		>
																			<a 
																				class="nav-link" 
																				data-toggle="tab" 
																				:href="'#' + rentPeriod.name + containerIndex" 
																				role="tab" 
																				aria-selected="false" 
																				:class="{'active': rentIndex === 0}"
																			>
																				{{ rentPeriod.name | capitalize }}
																			</a>
																		</li>
																	</ul>

																	<div class="tab-content tabs card-block">
																		<div 
																			class="tab-pane" 
																			role="tabpanel" 
																			v-for="(rentPeriod, rentIndex) in allRentPeriods" 
																			:id="rentPeriod.name + containerIndex" 
																			:key="'x-' + rentPeriod.id + rentPeriod.name" 
																			:class="{'active': rentIndex === 0}"
																		>
																			<div class="d-flex">
																	    		<!-- container -->
																    			<div class="mr-1 p-2 border w-100">
																    				<div class="form-row">
																    					<div class="sub-title">Container</div>
																    				</div>

																		    		<div 
																			    		class="form-row" 
																			    		v-if="singleWarehouseData.containers[containerIndex].rents.hasOwnProperty('container_rent_' + rentPeriod.name)">
																		    			<!-- 
																		    			<div class="col-sm-12 form-group">	
																		    				<label for="phone">Storing Price</label>
																							<input 
																								type="number" 
																								class="form-control" 
																								v-model.number="singleWarehouseData.containers[containerIndex].rents['container_storing_price_' + rentPeriod.name]" 
																								placeholder="Storing price" 
																								:class="!errors.warehouse.containers[containerIndex]['container_storing_price_' + rentPeriod.name] ? 'is-valid' : 'is-invalid'" 
																								min="0" 
																							>
																							<div class="invalid-feedback">
																					        	{{ errors.warehouse.containers[containerIndex]['container_storing_price_' + rentPeriod.name] }}
																					  		</div>
																		    			</div> 
																		    			-->
																		    			<div class="col-sm-12 form-group">	
																		    				<label for="phone">Rent</label>

																							<input 
																								type="number" 
																								class="form-control" 
																								v-model.number="singleWarehouseData.containers[containerIndex].rents['container_rent_' + rentPeriod.name]['rent']" 
																								placeholder="Rent price" 
																								:class="! errors.warehouse.containers[containerIndex]['container_rent_' + rentPeriod.name] ? 'is-valid' : 'is-invalid'" 
																								min="0" 
																							>

																							<div class="invalid-feedback">
																					        	{{ errors.warehouse.containers[containerIndex]['container_rent_' + rentPeriod.name] }}
																					  		</div>
																		    			</div>
																		    		</div>
																    			</div>

																    			<!-- shelf -->
																    			<div 
																    				class="mr-1 p-2 border w-100" 
																    				v-show="singleWarehouseData.containers[containerIndex].container.has_shelve"
																    			>
																		    		<div class="form-row">
																    					<div class="sub-title">Shelf</div>
																    				</div>

																		    		<div 
																			    		class="form-row" 
																			    		v-if="singleWarehouseData.containers[containerIndex].rents.hasOwnProperty('shelf_rent_' + rentPeriod.name)"
																		    		>
																		    			<!-- 
																		    			<div class="col-sm-12 form-group">	
																		    				<label for="phone">Storing Price</label>
																							<input 
																								type="number" 
																								class="form-control" 
																								v-model.number="singleWarehouseData.containers[containerIndex].rents['shelf_storing_price_' + rentPeriod.name]" 
																								placeholder="Storing price" 
																								:class="!errors.warehouse.containers[containerIndex]['shelf_storing_price_' + rentPeriod.name] ? 'is-valid' : 'is-invalid'" 
																								min="0" 
																							>
																							<div class="invalid-feedback">
																					        	{{ errors.warehouse.containers[containerIndex]['shelf_storing_price_' + rentPeriod.name] }}
																					  		</div>
																		    			</div> 
																		    			-->
																		    			<div class="col-sm-12 form-group">	
																		    				<label for="phone">Rent</label>
																							<input 
																								type="number" 
																								class="form-control" 
																								v-model.number="singleWarehouseData.containers[containerIndex].rents['shelf_rent_' + rentPeriod.name]['rent']" 
																								placeholder="Rent price" 
																								:class="!errors.warehouse.containers[containerIndex]['shelf_rent_' + rentPeriod.name] ? 'is-valid' : 'is-invalid'" 
																								min="0" 
																							>

																							<div class="invalid-feedback">
																					        	{{ errors.warehouse.containers[containerIndex]['shelf_rent_' + rentPeriod.name] }}
																					  		</div>
																		    			</div>
																		    		</div>
																    			</div>
																    			
																    			<!-- unit -->
																    			<div 
																    				class="mr-1 p-2 border w-100" 
																    				v-show="singleWarehouseData.containers[containerIndex].container.has_shelve && singleWarehouseData.containers[containerIndex].container.shelf && singleWarehouseData.containers[containerIndex].container.shelf.has_units"
																    			>
																		    		<div class="form-row">
																    					<div class="sub-title">Unit</div>
																    				</div>

																		    		<div 
																			    		class="form-row" 
																			    		v-if="singleWarehouseData.containers[containerIndex].rents.hasOwnProperty('unit_rent_' + rentPeriod.name)"
																		    		>
																		    			<!-- 
																		    			<div class="col-sm-12 form-group">	
																		    				<label for="phone">Storing Price</label>
																							<input 
																								type="number" 
																								class="form-control" 
																								v-model.number="singleWarehouseData.containers[containerIndex].rents['unit_storing_price_' + rentPeriod.name]" 
																								placeholder="Storing price"
																								:class="!errors.warehouse.containers[containerIndex]['unit_storing_price_' + rentPeriod.name] ? 'is-valid' : 'is-invalid'" 
																								min="0" 
																							>
																							<div class="invalid-feedback">
																					        	{{ errors.warehouse.containers[containerIndex]['unit_storing_price_' + rentPeriod.name] }}
																					  		</div>
																		    			</div> 
																		    			-->
																		    			<div class="col-sm-12 form-group">	
																		    				<label for="phone">Rent</label>

																							<input 
																								type="number" 
																								class="form-control" 
																								v-model.number="singleWarehouseData.containers[containerIndex].rents['unit_rent_' + rentPeriod.name]['rent']" 
																								placeholder="Rent price" 
																								:class="!errors.warehouse.containers[containerIndex]['unit_rent_' + rentPeriod.name] ? 'is-valid' : 'is-invalid'" 
																								min="0" 
																							>

																							<div class="invalid-feedback">
																					        	{{ errors.warehouse.containers[containerIndex]['unit_rent_' + rentPeriod.name] }}
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
												    		<div class="invalid-feedback text-center" style="display: block;" v-show="errors.warehouse.containers[containerIndex].container_price">
												    			{{ errors.warehouse.containers[containerIndex].container_price }}
												    		</div> 
												    		-->
															
															<div 
																class="invalid-feedback text-center" 
																style="display: block;" v-show="errors.warehouse.containers[containerIndex].rent"
															>
												    			{{ errors.warehouse.containers[containerIndex].rent }}
												    		</div>

												    	</div>
												  	</div>
											  	</transition-group>	
		                              		</div>

		                              		<div class="col-sm-12 form-group text-center">
		                              			<div 
				                                	class="invalid-feedback" 
				                                	style="display: block;" 
				                                	v-show="errors.warehouse.container"
			                                	>
											    	{{ errors.warehouse.container }}
											    </div>
		                              		</div>

		                              		<div class="col-md-12 form-group">
											  	<div class="form-row">
													<div class="form-group col-sm-6">
														<button 
															type="button"  
															@click="addContainer" 
															class="btn btn-success btn-block btn-sm" 
															data-toggle="tooltip" data-placement="top" title="Add Container"
														>
															More Container
														</button>	
													</div>
													<div class="form-group col-sm-6">
														<button 
															type="button" 
															class="btn btn-danger btn-block btn-sm" 
															data-toggle="tooltip" data-placement="top" title="Remove Container"
															@click="removeContainer" 
															:disabled="singleWarehouseData.containers.length < 2 || singleWarehouseData.containers[singleWarehouseData.containers.length-1].engaged_quantity > 1 || singleWarehouseData.containers[singleWarehouseData.containers.length-1].partially_engaged > 1"
														>
															Remove Container
														</button>	
													</div>
												</div>
		                              		</div>
			                            </div>   
						          	</div>

						          	<div class="col-sm-12 p-3 border">
						          		<div class="row">
					          				<div class="col-6">
							                  	<button 
							                  		type="button" 
							                  		v-on:click="step-=1" 
							                  		class="btn btn-outline-secondary btn-sm btn-round" 
							                  		data-toggle="tooltip" data-placement="top" title="Previous"
							                  	>
								                    <i class="fa fa-2x fa-angle-double-left" aria-hidden="true"></i>
							                  	</button>
							                </div>
						          			
						          			<div class="col-6 text-right">
							                	<div class="text-danger small" 
							                		v-show="!submitForm"
							                	>
											  		Please input required fields
									          	</div>
							                  	<button 
							                  		type="button" 
							                  		v-on:click="nextPage"
							                  		class="btn btn-outline-secondary btn-sm btn-round" 
							                  		data-toggle="tooltip" data-placement="top" title="Next"
							                  	>
								                    <i class="fa fa-2x fa-angle-double-right" aria-hidden="true"></i>
							                  	</button>
								          	</div>
						          		</div>
						          	</div>
								</div>

								<!-- Roles & Permissions -->
								<div 
									class="row" 
									v-bind:key="6" 
									v-show="step==6"
								>								  	
							        <h2 class="ml-auto mr-auto mb-4 lead">Roles & Permissions</h2>
							      	
							      	<div class="col-md-12">
										<div class="form-row" v-show="allRoles.length">
											<div class="form-group col-md-12">
												<label for="inputUsername">Role</label>
												<multiselect 
				                          			v-model="singleWarehouseData.roles" 
				                              		label="name" 
				                              		track-by="id" 
				                              		:custom-label="objectNameWithCapitalized" 
				                              		:options="allRoles" 
				                              		:multiple="true" 
				                              		:close-on-select="false" 
				                              		class="form-control p-0 is-valid" 
				                              		placeholder="Parent Category" 
				                              		selectLabel = "Press/Click"
				                              		deselect-label="Can't remove single value" 
				                              		@close="checkAvailableCheckboxes"
				                          		>
				                            	</multiselect>
											</div>
										</div>

										<div class="form-row">
											<div class="form-group col-md-12" v-show="! allRoles.length">
												<p class="text-center">You may not have permissions to assign roles</p>
											</div>
										</div>

										<div class="form-row" v-show="allPermissions.length">
											<div class="form-group col-md-12">
												<label for="inputUsername">Special Permissions</label>
												<div class="row">
													<!-- Approvable Models -->
													<!-- 
													<div 
														class="col-md-6" 
														v-for="model in modelCRUDableAndApproveable" 
														:key="'approve-model-permission-name-' + model"
													>
														<p class="font-weight-bold mt-4 mb-3">{{ modelName(model) }}</p>

														<div class="form-check">
															<input 
																type="checkbox" 
																:checked="permissionExists('create-' + model)" 
																@change="insertPermission('create-' + model, $event)" 
																:ref="'create-' + model.toLowerCase()"
															>
															<label>{{ modelName('create-' + model) }}</label>
														</div>

														<div class="form-check">
															<input 
																type="checkbox" 
																:checked="permissionExists('update-' + model)" 
																@change="insertPermission('update-' + model, $event)" 
																:ref="'update-' + model.toLowerCase()"
															>
															<label>{{ modelName('update/Approve-' + model) }}</label>
														</div>

														<div class="form-check">
															<input 
																type="checkbox" 
																:checked="permissionExists('delete-' + model)" 
																@change="insertPermission('delete-' + model, $event)" 
																:ref="'delete-' + model.toLowerCase()"
															>
															<label>{{ modelName('delete-' + model) }}</label>
														</div>

														<div class="form-check">
															<input 
																type="checkbox" 
																:checked="permissionExists('view-' + model + '-index')" 
																@change="insertPermission('view-' + model + '-index', $event)" 
																:ref="'view-' + model.toLowerCase() + '-index'"
															>
															<label>{{ modelName('view-' + model + '-list') }}</label>
														</div>
 
														<div class="form-check">
															<input 
																type="checkbox" 
																:checked="permissionExists('approve-' + model)" 
																@change="insertPermission('approve-' + model, $event)" 
																:ref="'approve-' + model.toLowerCase()"
															>
															<label>{{ modelName('approve-' + model) }}</label>
														</div>
													</div> 
													-->

													<!-- CRUD Models -->
													<div 
														class="col-md-6" 
														v-for="model in modelsCRUDable" 
														:key="'crud-model-permission-name-' + model"
													>
														<p class="font-weight-bold mt-4 mb-3">{{ modelName(model) }}</p>

														<!-- create -->
														<div class="form-check">
															<input 
																type="checkbox" 
																:checked="permissionExists('create-' + model)" 
																@change="insertPermission('create-' + model, $event)" 
																:ref="'create-' + model.toLowerCase()"
															>
															<label>{{ modelName('create-' + model) }}</label>
														</div>

														<!-- update -->
														<div class="form-check">
															<input 
																type="checkbox" 
																:checked="permissionExists('update-' + model)" 
																@change="insertPermission('update-' + model, $event)" 
																:ref="'update-' + model.toLowerCase()"
															>
															<label>{{ modelName('update-' + model) }}</label>
														</div>

														<!-- delete -->
														<div class="form-check">
															<input 
																type="checkbox" 
																:checked="permissionExists('delete-' + model)" 
																@change="insertPermission('delete-' + model, $event)" 
																:ref="'delete-' + model.toLowerCase()"
															>
															<label>{{ modelName('delete-' + model) }}</label>
														</div>

														<!-- view -->
														<div class="form-check">
															<input 
																type="checkbox" 
																:checked="permissionExists('view-' + model + '-index')" 
																@change="insertPermission('view-' + model + '-index', $event)" 
																:ref="'view-' + model.toLowerCase() + '-index'"
															>
															<label>{{ modelName('view-' + model + '-list') }}</label>
														</div>
													</div>

													<!-- CUD Models -->
												<div 
													class="col-md-6" 
													v-for="model in modelsCreateableUpdatableAndDeletable" 
													:key="'crud-model-permission-name-' + model"
												>
													<p class="font-weight-bold mt-4 mb-3">{{ modelName(model) }}</p>

													<!-- create -->
													<div class="form-check">
														<input 
															type="checkbox" 
															:checked="permissionExists('create-' + model)" 
															@change="insertPermission('create-' + model, $event)" 
															:ref="'create-' + model.toLowerCase()"
														>
														<label>{{ modelName('create-' + model) }}</label>
													</div>

													<!-- update -->
													<div class="form-check">
														<input 
															type="checkbox" 
															:checked="permissionExists('update-' + model)" 
															@change="insertPermission('update-' + model, $event)" 
															:ref="'update-' + model.toLowerCase()"
														>
														<label>{{ modelName('update-' + model) }}</label>
													</div>

													<!-- delete -->
													<div class="form-check">
														<input 
															type="checkbox" 
															:checked="permissionExists('delete-' + model)" 
															@change="insertPermission('delete-' + model, $event)" 
															:ref="'delete-' + model.toLowerCase()"
														>
														<label>{{ modelName('delete-' + model) }}</label>
													</div>

													<!-- view is public-->
												</div>

													<!-- Viewable and Updatable CRUD -->
													<div 
														class="col-md-6" 
														v-for="model in modelsViewableAndUpdatable" 
														:key="'view-update-model-permission-name-' + model"
													>
														<p class="font-weight-bold mt-4 mb-3">{{ modelName(model) }}</p>

														<!-- update -->
														<div class="form-check">
															<input 
																type="checkbox" 
																:checked="permissionExists('update-' + model)" 
																@change="insertPermission('update-' + model, $event)" 
																:ref="'update-' + model.toLowerCase()"
															>
															<label>{{ modelName('update-' + model) }}</label>
														</div>

														<!-- view -->
														<div class="form-check">
															<input 
																type="checkbox" 
																:checked="permissionExists('view-' + model + '-index')" 
																@change="insertPermission('view-' + model + '-index', $event)" 
																:ref="'view-' + model.toLowerCase() + '-index'"
															>
															<label>{{ modelName('view-' + model + '-list') }}</label>
														</div>
													</div>

													<!-- Viewable and Makeable -->
													<div 
														class="col-md-6" 
														v-for="model in modelsViewableRecommendableAndApproveable" 
														:key="'crud-model-permission-name-' + model"
													>
														<p class="font-weight-bold mt-4 mb-3">{{ modelName(model) }}</p>

														<!-- recommend -->
														<div class="form-check">
															<input 
																type="checkbox" 
																:checked="permissionExists('recommend-' + model)" 
																@change="insertPermission('recommend-' + model, $event)" 
																:ref="'recommend-' + model.toLowerCase()"
															>
															<label>{{ modelName('recommend-' + model) }}</label>
														</div>

														<!-- update -->
														<div class="form-check">
															<input 
																type="checkbox" 
																:checked="permissionExists('approve-' + model)" 
																@change="insertPermission('approve-' + model, $event)" 
																:ref="'approve-' + model.toLowerCase()"
															>
															<label>{{ modelName('update/Approve-' + model) }}</label>
														</div>
														
														<!-- view -->
														<div class="form-check">
															<input 
																type="checkbox" 
																:checked="permissionExists('view-' + model + '-index')" 
																@change="insertPermission('view-' + model + '-index', $event)" 
																:ref="'view-' + model.toLowerCase() + '-index'"
															>
															<label>{{ modelName('view-' + model + '-list') }}</label>
														</div>
													</div>

													<!-- Viewable -->
													<div 
														class="col-md-6" 
														v-for="model in modelsViewable" 
														:key="'view-model-permission-name-' + model"
													>
														<p class="font-weight-bold mt-4 mb-3">{{ modelName(model) }}</p>

														<!-- view -->
														<div class="form-check">
															<input 
																type="checkbox" 
																:checked="permissionExists('view-' + model + '-index')" 
																@change="insertPermission('view-' + model + '-index', $event)" 
																:ref="'view-' + model.toLowerCase() + '-index'"
															>
															<label>{{ modelName('view-' + model + '-list') }}</label>
														</div>
													</div>

													<!-- Viewable 2 -->
													<div 
														class="col-md-6" 
														v-for="model in modelsViewable2" 
														:key="'view2-model-permission-name-' + model"
													>
														<p class="font-weight-bold mt-4 mb-3">{{ modelName(model) }}</p>

														<!-- view -->
														<div class="form-check">
															<input 
																type="checkbox" 
																:checked="permissionExists('view-' + model)" 
																@change="insertPermission('view-' + model, $event)" 
																:ref="'view-' + model.toLowerCase()"
															>
															<label>{{ modelName('view-' + model) }}</label>
														</div>
													</div>
												</div>
											</div>
										</div>

										<div class="form-row">
											<div class="form-group col-md-12" v-show="! allPermissions.length">
												<p class="text-center">You may not have permissions to select permissions</p>
											</div>
										</div>
									</div> 
							        
						          	<div class="col-sm-12 p-3 border">
						          		<div class="row">

					          				<div class="col-6">
							                  	<button type="button" 
							                  		class="btn btn-outline-secondary btn-sm btn-round" 
							                  		v-on:click="step-=1" 
							                  		data-toggle="tooltip" data-placement="top" title="Previous"
							                  	>
								                    <i class="fa fa-2x fa-angle-double-left" aria-hidden="true"></i>
							                  	</button>
							                </div>
						          			
						          			<div class="col-6 text-right">
							                	<div class="text-danger small" 
							                		v-show="!submitForm"
							                	>
											  		Please input required fields
									          	</div>
							                  	<button 
							                  		type="submit" 
							                  		class="btn btn-primary btn-sm btn-round" 
							                  		:disabled="formSubmitted"
							                  	>
								                    {{ createMode ? 'Save' : 'Update' }} Warehouse
							                  	</button>
								          	</div>

						          		</div>
						          	</div>
								</div>
							</transition-group>
						</form>
						<!-- form end -->
					</div>
				</div>
				<!-- /.modal-content -->
			</div>
			<!-- /.modal-dialog -->
		</div>
		<!-- /modal-createOrEdit-warehouse -->

		<!-- View Modal -->
		<div class="modal fade" id="warehouse-view-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">
							Warehouse Details
						</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="modal-body">

						<div class="card">
							<ul class="nav nav-pills mx-auto" role="tablist">
								<li class="nav-item">
									<a class="nav-link active show" data-toggle="tab" href="#profile" role="tab" aria-selected="false">
										Profile
									</a>
								</li>
								
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" href="#owner" role="tab" aria-selected="false">
										Deal
									</a>
								</li>

								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" href="#features" role="tab" aria-selected="true">
										Features
									</a>
								</li>

								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" href="#storage-details" role="tab" aria-selected="false">
										Storage
									</a>
								</li>

								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" href="#container-details" role="tab" aria-selected="false">
										Container
									</a>
								</li>

								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" href="#roles-permissions" role="tab" aria-selected="false">
										Permissions
									</a>
								</li>
							</ul>
							
							<div class="tab-content tabs card-block">
								<div class="tab-pane active" id="profile" role="tabpanel">	
						    		<div class="form-group form-row text-center">
										<div class="col-md-12 text-center">
											<img class="img-fluid" 
												:src="singleWarehouseData.site_map_preview || ''"
												alt="site_map_preview" 
											>
										</div>
									</div>
									<div class="form-group form-row">
										<label class="col-sm-6 col-form-label font-weight-bold text-right">Name :</label>
										<label class="col-sm-6 col-form-label">{{ singleWarehouseData.name | capitalize }}</label>
									</div>
									<!-- 
									<div class="form-group form-row">
										<label class="col-sm-6 col-form-label font-weight-bold text-right">Code :</label>
										<label class="col-sm-6 col-form-label">{{ singleWarehouseData.code }}</label>
									</div>
									 -->
									<div class="form-group form-row">
										<label class="col-sm-6 col-form-label font-weight-bold text-right">Username :</label>
										<label class="col-sm-6 col-form-label">{{ singleWarehouseData.user_name | capitalize }}</label>
									</div>
									<div class="form-group form-row">
										<label class="col-sm-6 col-form-label font-weight-bold text-right">Email :</label>
										<label class="col-sm-6 col-form-label">{{ singleWarehouseData.email }}</label>
									</div>
									<div class="form-group form-row">
										<label class="col-sm-6 col-form-label font-weight-bold text-right">Mobile :</label>
										<label class="col-sm-6 col-form-label">{{ singleWarehouseData.mobile }}</label>
									</div>
									<div class="form-group form-row">
										<label class="col-sm-6 col-form-label font-weight-bold text-right">
											Status :
										</label>
										<label class="col-sm-6 col-form-label">
											<span :class="[singleWarehouseData.active ? 'badge-success' : 'badge-danger', 'badge']">
												{{ singleWarehouseData.active ? 'Active' : 'Pending' }}
											</span>
										</label>
									</div>
									<div class="form-group form-row">
										<label class="col-sm-6 col-form-label font-weight-bold text-right">Location :</label>
										<label class="col-sm-6 col-form-label"></label>
									</div>
								</div>

								<div class="tab-pane" id="owner" role="tabpanel" v-if="singleWarehouseData.owner">
									<div class="form-group form-row">
										<label class="col-sm-6 col-form-label font-weight-bold text-right">Owner name :</label>
										<label class="col-sm-6 col-form-label">{{ getOwnerFullName(singleWarehouseData.owner) | capitalize }}</label>
									</div>
									<div class="form-group form-row">
										<label class="col-sm-6 col-form-label font-weight-bold text-right">Username :</label>
										<label class="col-sm-6 col-form-label">{{ singleWarehouseData.owner.user_name | capitalize }}</label>
									</div>
									<div class="form-group form-row">
										<label class="col-sm-6 col-form-label font-weight-bold text-right">Email :</label>
										<label class="col-sm-6 col-form-label">{{ singleWarehouseData.owner.email }}</label>
									</div>
									<div class="form-group form-row">
										<label class="col-sm-6 col-form-label font-weight-bold text-right">Mobile :</label>
										<label class="col-sm-6 col-form-label">{{ singleWarehouseData.owner.mobile }}</label>
									</div>
									<div class="form-group form-row">
										<label class="col-sm-6 col-form-label font-weight-bold text-right">Deal Details :</label>
										<label class="col-sm-6 col-form-label" v-html="singleWarehouseData.warehouse_deal"></label>
									</div>
								</div>

								<div class="tab-pane" id="features" role="tabpanel">	
									<div class="form-group form-row">
										<label class="col-sm-6 col-form-label font-weight-bold text-right">Warehouse Features:</label>
										<label class="col-sm-6 col-form-label" v-html="singleWarehouseData.feature.features"></label>
									</div>
									<div class="form-group form-row">
										<label class="col-sm-6 col-form-label font-weight-bold text-right">
											Warehouse Previews:
										</label>
							    		<div class="col-sm-6">
											<div class="form-row" v-show="singleWarehouseData.previews.length">
												<div 
													class="form-group col-sm-6" 
													v-for="(warehousePreview, index) in singleWarehouseData.previews" 
													:key="'A' + index + warehousePreview.id"
												>
													<img class="img-fluid" 
														:src="warehousePreview.preview || ''"
														alt="warehouse preview" 
													>
												</div>
											</div>
										</div>
									</div>
								</div>
								
								<div class="tab-pane" id="storage-details" role="tabpanel">
									<div v-if="singleWarehouseData.storages.length">
										<div 
											class="card" 
											v-for="(warehouseStorageType, index) in singleWarehouseData.storages" 
									    	:key="'B' + index + warehouseStorageType.id"
										>
									    	<div class="card-body">
												<div class="form-group form-row">
													<label class="col-sm-6 col-form-label font-weight-bold text-right">
														Storage Name:
													</label>
													<label class="col-sm-6 col-form-label">
														{{ warehouseStorageType.storage_type.name | capitalize }}
													</label>
												</div>

												<!-- 
												<div class="form-group form-row">
													<label class="col-sm-6 col-form-label font-weight-bold text-right">Storage Code:</label>
													<label class="col-sm-6 col-form-label" v-html="warehouseStorageType.storage_type.code"></label>
												</div>
												 -->

												<div class="form-group form-row">
													<label class="col-sm-6 col-form-label font-weight-bold text-right">
														Storage Features:
													</label>
													<label class="col-sm-6 col-form-label" v-html="warehouseStorageType.feature.features"></label>
												</div>
												
									    		<div class="form-group form-row">
													<label class="col-sm-6 col-form-label font-weight-bold text-right">
														Storage Previews:
													</label>
										    		<div class="col-sm-6">
														<div class="form-row" v-show="warehouseStorageType.previews.length">
															<div 
																class="form-group col-sm-6" 
																v-for="(storagePreview, index) in warehouseStorageType.previews" 
																:key="'C' + index + storagePreview.id"
															>
																<img class="img-fluid" 
																	:src="storagePreview.preview || ''"
																	alt="storage preview" 
																>
															</div>
														</div>
													</div>
												</div>
									    	</div>
									    </div>
									</div>

									<div class="form-group form-row" v-else>
										<label class="col-sm-12 col-form-label text-danger text-center">
											No storage found
										</label>
									</div>
								</div>

								<div class="tab-pane" id="container-details" role="tabpanel">
									<div v-if="singleWarehouseData.containers.length">
										<div 
											class="card" 
											v-for="(warehouseContainer, containerIndex) in singleWarehouseData.containers" 
									    	:key="'warehouse-container-' + containerIndex + warehouseContainer.id"
										>
									    	<div class="card-body">
												<div class="form-group form-row">
													<label class="col-sm-6 col-form-label font-weight-bold text-right">
														Container Type:
													</label>
													<label class="col-sm-6 col-form-label">
														{{ warehouseContainer.container.name | capitalize }}
													</label>
												</div>

												<div class="form-group form-row">
													<label class="col-sm-6 col-form-label font-weight-bold text-right">
														Total Quantity:
													</label>
													<label class="col-sm-6 col-form-label">
														{{ warehouseContainer.quantity }}
													</label>
												</div>

												<div class="form-group form-row">
													<label class="col-sm-6 col-form-label font-weight-bold text-right">
														Rented Quantity:
													</label>
													<label class="col-sm-6 col-form-label">
														<ul>
															<li>
																Full : {{ warehouseContainer.engaged_quantity }}
															</li>

															<li>
																Partial : {{ warehouseContainer.partially_engaged }}
															</li>
														</ul>
													</label>
												</div>
												
									    		<div class="form-group form-row">
													<label class="col-sm-6 col-form-label font-weight-bold text-right">
														Rents:
													</label>
													<div class="col-sm-6">
														<ul class="nav nav-tabs tabs justify-content-center" role="tablist">
															<li 
																class="nav-item"
																v-for="(rentPeriod, rentIndex) in allRentPeriods" 
																:key="'l-rent-period-id-' + rentPeriod.id + 'rent-period-name-' + rentPeriod.name" 
															>
																<a 
																	class="nav-link" 
																	data-toggle="tab" 
																	:href="'#rent-period-name-' + rentPeriod.name + '-container-index-' + containerIndex" 
																	role="tab" 
																	aria-selected="false" 
																	:class="{'active': rentIndex === 0}"
																>
																	{{ rentPeriod.name | capitalize }}
																</a>
															</li>
														</ul>

														<div class="tab-content tabs card-block">
															<div 
																class="tab-pane" 
																:id="'rent-period-name-' + rentPeriod.name + '-container-index-' + containerIndex" 
																role="tabpanel" 
																v-for="(rentPeriod, rentIndex) in allRentPeriods" 
																:key="'d-rent-period-id' + rentPeriod.id + 'rent-period-name-' + rentPeriod.name" 
																:class="{'active': rentIndex === 0}"
															>
																<div class="d-flex">
														    		<!-- container -->
													    			<div class="mr-1 p-2 border w-100">
													    				<div class="form-row">
													    					<div class="sub-title">Container</div>
													    				</div>
															    		<div 
																    		class="form-row" 
																    		v-if="singleWarehouseData.containers[containerIndex].rents.hasOwnProperty('container_rent_' + rentPeriod.name)"
															    		>
															    			<!-- 
															    			<div class="col-sm-12 form-group">	
															    				<label for="phone">Storing Price : </label>
															    				<label class="col-form-label">
																					{{ singleWarehouseData.containers[containerIndex].rents['container_storing_price_' + rentPeriod.name] || 'NA' }}
																				</label>
															    			</div> 
															    			-->
															    			<div class="col-sm-12 form-group">	
															    				<label for="phone">Rent : </label>
															    				<label class="col-form-label">
																					{{ singleWarehouseData.containers[containerIndex].rents['container_rent_' + rentPeriod.name]['rent'] || 'NA' }}
																				</label>
															    			</div>
															    		</div>
													    			</div>
													    			<!-- shelf -->
													    			<div 
													    				class="mr-1 p-2 border w-100" 
													    				v-show="singleWarehouseData.containers[containerIndex].container.has_shelve"
													    			>
															    		<div class="form-row">
													    					<div class="sub-title">Shelf</div>
													    				</div>

															    		<div 
																    		class="form-row" 
																    		v-if="singleWarehouseData.containers[containerIndex].rents.hasOwnProperty('shelf_rent_' + rentPeriod.name)"
															    		>
															    			<!-- 
															    			<div class="col-sm-12 form-group">	
															    				<label for="phone">Storing Price : </label>
															    				<label class="col-form-label">
																					{{ singleWarehouseData.containers[containerIndex].rents['shelf_storing_price_' + rentPeriod.name] || 'NA' }}
																				</label>
															    			</div> 
															    			-->
															    			<div class="col-sm-12 form-group">	
															    				<label for="phone">Rent : </label>
															    				<label class="col-form-label">
																					{{ singleWarehouseData.containers[containerIndex].rents['shelf_rent_' + rentPeriod.name]['rent'] || 'NA' }}
																				</label>
															    			</div>
															    		</div>
													    			</div>
													    			<!-- unit -->
													    			<div 
													    				class="mr-1 p-2 border w-100" 
													    				v-show="singleWarehouseData.containers[containerIndex].container.has_shelve && singleWarehouseData.containers[containerIndex].container.shelf && singleWarehouseData.containers[containerIndex].container.shelf.has_units"
													    			>
															    		<div class="form-row">
													    					<div class="sub-title">Unit</div>
													    				</div>

															    		<div 
																    		class="form-row" 
																    		v-if="singleWarehouseData.containers[containerIndex].rents.hasOwnProperty('unit_rent_' + rentPeriod.name)"
															    		>
															    			<!-- 
															    			<div class="col-sm-12 form-group">	
															    				<label for="phone">Storing Price : </label>
															    				<label class="col-form-label">
																					{{ singleWarehouseData.containers[containerIndex].rents['unit_storing_price_' + rentPeriod.name] || 'NA' }}
																				</label>
															    			</div> 
															    			-->
															    			<div class="col-sm-12 form-group">	
															    				<label for="phone">Rent : </label>
															    				<label class="col-form-label">
																					{{ singleWarehouseData.containers[containerIndex].rents['unit_rent_' + rentPeriod.name]['rent'] || 'NA' }}
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
									</div>

									<div class="form-group form-row" v-else>
										<label class="col-sm-12 col-form-label text-danger text-center">
											No container found
										</label>
									</div>
								</div>

								<div class="tab-pane" id="roles-permissions" role="tabpanel">
									<div class="form-group form-row">
										<label class="col-sm-6 col-form-label font-weight-bold text-right">Roles :</label>
										<label 
											class="col-sm-6 col-form-label" 
											v-show="singleWarehouseData.roles.length"
										>
											<span v-for="role in singleWarehouseData.roles">
												{{ role.name | capitalize }}
												<span v-show="singleWarehouseData.roles.length > 1">, </span>
											</span>
										</label>
										<label 
											class="col-sm-6 col-form-label" 
											v-show="!singleWarehouseData.roles.length"
										>
											NA
										</label>
									</div>
									<div class="form-group form-row">
										<label class="col-sm-6 col-form-label font-weight-bold text-right">
											Permissions :
										</label>
										<label 
											class="col-sm-6 col-form-label" 
											v-show="singleWarehouseData.permissions.length"
										>
											<span v-for="permission in singleWarehouseData.permissions">
												{{ permission.name | capitalize }}
												<span v-show="singleWarehouseData.permissions.length > 1">, </span>
											</span>
										</label>
										<label 
											class="col-sm-6 col-form-label" 
											v-show="!singleWarehouseData.permissions.length"
										>
											NA
										</label>
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

		<delete-confirmation-modal 
			v-if="userHasPermissionTo('delete-warehouse')"
			:csrf="csrf" 
			:form-submitted="formSubmitted"
			:submit-method-name="'deleteWarehouse'" 
			:content-to-delete="singleWarehouseData"
			:restoration-message="'But once you think, you can restore this item !'" 
			
			@deleteWarehouse="deleteWarehouse($event)" 
		></delete-confirmation-modal>

		<restore-confirmation-modal 
			v-if="userHasPermissionTo('delete-warehouse')"
			:csrf="csrf" 
			:form-submitted="formSubmitted"
			:submit-method-name="'restoreWarehouse'" 
			:content-to-restore="singleWarehouseData"
			:restoration-message="'This will restore all related items !'" 

			@restoreWarehouse="restoreWarehouse($event)" 
		></restore-confirmation-modal>
	</div>
</template>

<script type="text/javascript">

	import axios from 'axios';
	import Multiselect from 'vue-multiselect';
	import CKEditor from '@ckeditor/ckeditor5-vue';
	import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

    let singleWarehouseData = {
    	owner : {},
    	previews : [],
    	
    	feature : {
    		features : ''
    	},
    	
    	storages : [
    		{  	// warehouse_storage_types
				previews : [],
				storage_type : {

				},
				feature : {
					features : ''
				},
			},
    	],

    	containers : [
			{	/*warehouse_containers*/	
				
				// quantity : '',
				// container_id : ''
				// engaged_quantity : '',
				container : {	 // containers	
					// name : 0,
					// has_shelve : 0,
					shelf : { 	// warehouse_container_shelfs
						// has_units : true,
						// quantity : 0,  // total number warehouse_container_shelfs
						unit :  {    // warehouse_container_shelf_units
							// quantity : 0,  // length of warehouse_container_shelf_units	
						},	
					}, 
				},

		    	rents : {
		    		
		    	}

			},
    	],

    	roles : [],
    	permissions : []

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
	        	currentTab : 'approved',

	        	editor: ClassicEditor,

	        	createMode : true,

	        	allRentPeriods : [],

	        	allContainers : [],
	        	contentsToShow : [],
	        	allStorageTypes : [],
	        	allWarehouseOwners : [],
	        	allFetchedWarehouses : [],

	        	pagination: {
		        	current_page: 1
		      	},

		      	ownerObject : {},

	        	singleWarehouseData : singleWarehouseData,

	        	errors : {
	        		warehouse : {
	        			storage_features : [],
	        			storage_types : [],
	        			containers : [
	        				{
		        				// quantity : null,
		        				// container_id : null,
	        				},
	        			],
	        		},
	        	},

	        	submitForm : true,
	        	formSubmitted : false,

	            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

	            allRoles : [],
				allPermissions : [],

				// modelCRUDableAndApproveable : [
	                // 'Product-Stock',
	            // ],

				modelsCRUDable : [
	            	'Role',
	            	'Product',
	            	'Manager',
	            	'Merchant',
	                'Warehouse',
	                'Requisition',
	            	'Product-Asset',
	                'Product-Stock',
	            	'Warehouse-Owner',
	                'Warehouse-Asset',
	            	// 'WarehouseProduct',   // Product-Stock
	                // 'Product-Category',  // Product-Asset
	            	// 'Product-Manufacturer',  // Product-Asset
	            	// 'WarehouseDeliveryCompany'
	                'Merchant-Deal',
	                'Merchant-Product',
	                'Merchant-Payment',
	            ],

	            modelsCreateableUpdatableAndDeletable : [
	                'Logistic-Asset',
	            ],            

	            modelsViewableAndUpdatable : [
	                'Application-Setting'  // view / update
	            ],

	            modelsViewableRecommendableAndApproveable : [
	                'Dispatch',  // view / make
	            ],

	            modelsViewable : [
	                'Permission',  // view
	            ],

	            modelsViewable2 : [
	                'General-Dashboard-One',  // view
	                'General-Dashboard-Two'  // view
	            ],

	        }

		},
		
		created(){

			this.fetchAllWarehouses();

			if (this.userHasPermissionTo('view-role-index')) {

				this.fetchAllRoles();

			}

			if (this.userHasPermissionTo('view-permission-index')) {

				this.fetchAllPermissions();

			}

			if (this.userHasPermissionTo('view-warehouse-asset-index')) {

				this.fetchAllRentPeriods();
				this.fetchAllStorageTypes();
				this.fetchAllContainerTypes();

			}

			if (this.userHasPermissionTo('view-warehouse-owner-index')) {

				this.fetchAllWarehouseOwners();

			}

		},

		filters: {

			capitalize: function (value) {
				if (!value) return ''

				const words = value.split(/[\s-]/);

				for (let i = 0; i < words.length; i++) {
				    
					if (words[i]) {

				    	words[i] = words[i][0].toUpperCase() + words[i].substr(1);

					}
				    
				}

				return words.join(" ");
			}

		},

		watch: {

			'singleWarehouseData.containers': {
	            handler: function(newContainers) {

	            	newContainers.forEach(
						(container, index) => {

							if (this.errors.warehouse.containers[index]) {

								if (! container.container.has_shelve) {

									this.allRentPeriods.forEach(
										rentPeriod => {

											// this.$delete(this.errors.warehouse.containers[index], 'shelf_storing_price_'+ rentPeriod.name);
											this.$delete(this.errors.warehouse.containers[index], 'shelf_rent_'+ rentPeriod.name);

											// this.$delete(this.errors.warehouse.containers[index], 'unit_storing_price_'+ rentPeriod.name);
											this.$delete(this.errors.warehouse.containers[index], 'unit_rent_'+ rentPeriod.name);

										}
									);

								}

								else if (container.container.has_shelve && container.container.shelf && ! container.container.shelf.has_units) {

									this.allRentPeriods.forEach(
										rentPeriod => {

											// this.$delete(this.errors.warehouse.containers[index], 'unit_storing_price_'+ rentPeriod.name);
											this.$delete(this.errors.warehouse.containers[index], 'unit_rent_'+ rentPeriod.name);

										}
									);

								}

							}

						}
					);

	            },

	            deep: true
	        }

		},
		
		methods : {

			fetchAllRoles() {

				if (! this.userHasPermissionTo('view-role-index')) {

					this.error = 'You do not have permission to view roles';
					return;

				}
				
				this.query = '';
				this.error = '';
				this.loading = true;
				this.allRoles = [];
				
				axios
					.get('/api/roles/')
					.then(response => {
						if (response.status == 200) {
							this.allRoles = response.data;
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
			fetchAllPermissions() {

				if (! this.userHasPermissionTo('view-permission-index')) {

					this.error = 'You do not have permission to view permissions';
					return;
					
				}
				
				this.query = '';
				this.error = '';
				this.loading = true;
				this.allPermissions = [];
				
				axios
					.get('/api/permissions/')
					.then(response => {
						if (response.status == 200) {
							this.allPermissions = response.data;
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
			fetchAllWarehouses() {
				
				this.query = '';
				this.error = '';
				this.loading = true;
				this.allFetchedWarehouses = [];
				
				axios
					.get('/api/warehouses/' + this.perPage + "?page=" + this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							this.allFetchedWarehouses = response.data;
							this.showSelectedTabContents();
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
			fetchAllWarehouseOwners() {
				
				if (! this.userHasPermissionTo('view-warehouse-owner-index')) {

					this.error = 'You do not have permission to view owners';
					return;
					
				}

				this.error = '';
				this.loading = true;
				this.allWarehouseOwners = [];
				
				axios
					.get('/api/owners/')
					.then(response => {
						if (response.status == 200) {
							this.allWarehouseOwners = response.data;
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
			fetchAllStorageTypes() {

				if (! this.userHasPermissionTo('view-warehouse-asset-index')) {

					this.error = 'You do not have permission to view storage types';
					return;
					
				}
				
				this.error = '';
				this.loading = true;
				this.allStorageTypes = [];
				
				axios
					.get('/api/storage-types/')
					.then(response => {
						if (response.status == 200) {
							this.allStorageTypes = response.data;
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
			fetchAllContainerTypes() {

				if (! this.userHasPermissionTo('view-warehouse-asset-index')) {

					this.error = 'You do not have permission to view container types';
					return;
					
				}
				
				this.error = '';
				this.loading = true;
				this.allContainers = [];
				
				axios
					.get('/api/containers/')
					.then(response => {
						if (response.status == 200) {
							this.allContainers = response.data;
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
			fetchAllRentPeriods() {

				if (! this.userHasPermissionTo('view-warehouse-asset-index')) {

					this.error = 'You do not have permission to view rent-periods';
					return;
					
				}
				
				this.error = '';
				this.loading = true;
				this.allRentPeriods = [];
				
				axios
					.get('/api/rent-periods/')
					.then(response => {
						if (response.status == 200) {
							this.allRentPeriods = response.data;
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
						this.resetContainersRentProperties();
						this.loading = false;
					});

			},
			searchData(emitedValue=false) {

				if (emitedValue) {
					this.query=emitedValue;
				}

				this.error = '';
				this.allFetchedWarehouses = [];
				this.pagination.current_page = 1;
				
				axios
				.get(
					"/api/search-warehouses/" + this.query + "/" + this.perPage + "?page=" + this.pagination.current_page
				)
				.then(response => {
					this.allFetchedWarehouses = response.data;
					this.contentsToShow = this.allFetchedWarehouses.all.data;
					this.pagination = this.allFetchedWarehouses.all;
				})
				.catch(e => {
					this.error = e.toString();
				});

			},
			showContentDetails(object) {
				this.singleWarehouseData = object;
				$('#warehouse-view-modal').modal('show');
			},
			showContentCreateForm() {
				
				this.step = 1;
				this.createMode = true;
				this.submitForm = true;
				this.formSubmitted = false;

				this.ownerObject = {};

				this.errors = {
	        		warehouse : {
	        			storage_features : [],
	        			storage_types : [],
	        			containers : [
	        				{
		        				// quantity : null,
		        				// container_id : null,
	        				},
	        			],
	        		},
	        	};
	        	
				this.singleWarehouseData = {
					// owner : {},
					previews : [],
			    	
			    	feature : {
			    		features : ''
			    	},

			    	storages : [  
			    		{	// warehouse_storage_types
							previews : [],
							storage_type : {},
							feature : {
								features : ''
							},
						},
			    	],

			    	containers : [
						{	/*warehouse_containers*/	
							
							quantity : 1,
							// container_id : ''
							// engaged_quantity : '',
							
							container : {	 // containers	
								// name : 0,
								// has_shelve : 0,
								shelf : { 	// warehouse_container_shelfs
									// has_units : true,
									// quantity : 0,  // total number warehouse_container_shelfs
									unit :  {    // warehouse_container_shelf_units
										// quantity : 0,  // length of warehouse_container_shelf_units	
									},	
								}, 
							},

					    	rents : {
					    		
					    	}

						},
			    	],

			    	roles : [],
    				permissions : []
				};

				this.resetContainersRentProperties();

				this.resetAllPermissions();

				$('#warehouse-createOrEdit-modal').modal('show');

			},
			openContentEditForm(object) {

				const objectExists = currentObject => currentObject.id == object.warehouse_owner_id;
				// const existingStorages = currentObject => currentObject.deleted_at === null;

				this.step = 1;
				this.submitForm = true;
				this.createMode = false;
				this.formSubmitted = false;

				this.ownerObject = 	this.allWarehouseOwners.find(objectExists);
				
				this.errors = {
	        		warehouse : {
	        			storage_features : [],
	        			storage_types : [],
	        			containers : [],
	        		},
	        	};

	        	object.containers.forEach(
	        		container => {
	        			this.errors.warehouse.containers.push({});
	        		}
	        	);

				
				this.singleWarehouseData = { ...object };

				this.resetAllPermissions();
				this.disableExistingRolePermissions();

				$('#warehouse-createOrEdit-modal').modal('show');
				
			},
			openContentDeleteForm(object) {
				this.singleWarehouseData = object;
				$('#delete-confirmation-modal').modal('show');
			},
			openContentRestoreForm(object) {
				this.singleWarehouseData = object;
				$('#restore-confirmation-modal').modal('show');
			},
			storeWarehouse() {

				// this.insertDefaultPermissions(['view-warehouse-asset-index', 'view-warehouse-owner-index']);

				this.formSubmitted = true;

				axios
					.post('/warehouses/' + this.perPage, this.singleWarehouseData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("New warehouse has been created", "Success");

							this.allFetchedWarehouses = response.data;
							this.query !== '' ? this.searchData() : this.showSelectedTabContents();

							$('#warehouse-createOrEdit-modal').modal('hide');
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
					});

			},
			updateWarehouse() {

				// this.insertDefaultPermissions(['view-warehouse-asset-index', 'view-warehouse-owner-index']);

				this.formSubmitted = true;
				
				axios
					.put('/warehouses/' + this.singleWarehouseData.id + '/' + this.perPage, this.singleWarehouseData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Warehouse has been updated", "Success");

							this.allFetchedWarehouses = response.data;
							this.query !== '' ? this.searchData() : this.showSelectedTabContents();

							$('#warehouse-createOrEdit-modal').modal('hide');
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
					});

			},
			deleteWarehouse() {
				
				if (this.singleWarehouseData.containers.some(warehouseContainer => warehouseContainer.partially_engaged || warehouseContainer.engaged_quantity)) {

					this.$toastr.e("Warehouse is in use", "Error");
					return;

				}

				this.formSubmitted = true;

				axios
					.delete('/warehouses/' + this.singleWarehouseData.id + '/' + this.perPage)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Warehouse has been deleted", "Success");

							this.allFetchedWarehouses = response.data;
							this.query !== '' ? this.searchData() : this.showSelectedTabContents();

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
						this.formSubmitted = false;
					});

			},
			restoreWarehouse() {
				
				this.formSubmitted = true;

				axios
					.patch('/warehouses/' + this.singleWarehouseData.id + '/' + this.perPage)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Warehouse has been restored", "Success");

							this.allFetchedWarehouses = response.data;
							this.query !== '' ? this.searchData() : this.showSelectedTabContents();
							
							$('#restore-confirmation-modal').modal('hide');
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
					});

			},
            changeNumberContents(expectedContentsPerPage) {

				this.pagination.current_page = 1;
				this.perPage = expectedContentsPerPage;

				if (this.query === '') {
					this.fetchAllWarehouses();
				}
				else {
					this.searchData();
				}

    		},
    		showSelectedTabContents() {
				
				if (this.currentTab=='approved') {
					this.contentsToShow = this.allFetchedWarehouses.approved.data;
					this.pagination = this.allFetchedWarehouses.approved;
				}
				else if (this.currentTab=='pending') {
					this.contentsToShow = this.allFetchedWarehouses.pending.data;
					this.pagination = this.allFetchedWarehouses.pending;
				}
				else {
					this.contentsToShow = this.allFetchedWarehouses.trashed.data;
					this.pagination = this.allFetchedWarehouses.trashed;
				}

			},
			showApprovedContents() {
				this.currentTab = 'approved';
				this.showSelectedTabContents();
			},
			showPendingContents() {
				this.currentTab = 'pending';
				this.showSelectedTabContents();
			},
			showTrashedContents() {
				this.currentTab = 'trashed';
				this.showSelectedTabContents();
			},
			setWarehouseOwner() {
				if (this.ownerObject && Object.keys(this.ownerObject).length > 0) {
					this.singleWarehouseData.warehouse_owner_id = this.ownerObject.id;
				}
			},
			objectNameWithCapitalized ({ name, user_name }) {
		      	if (name) {
				    name = name.toString();
					const words = name.split(" ");

					for (let i = 0; i < words.length; i++) {
						
						if (words[i]) {

					    	words[i] = words[i][0].toUpperCase() + words[i].substr(1);
						
						}
						
					}

					return words.join(" ");
		      	}
		      	else if (user_name) {
				    user_name = user_name.toString();
					const words = user_name.split(" ");

					for (let i = 0; i < words.length; i++) {
						
						if (words[i]) {

					    	words[i] = words[i][0].toUpperCase() + words[i].substr(1);
						
						}
						
					}

					return words.join(" ");
		      	}
		      	else 
		      		return ''
		    },
			nextPage() {

				if (this.step==1) {
					this.validateFormInput('site_map_preview');
					this.validateFormInput('name');
					// this.validateFormInput('code');
					this.validateFormInput('user_name');
					this.validateFormInput('email');
					this.validateFormInput('mobile');
					this.validateFormInput('password');
					this.validateFormInput('password_confirmation');
				}
				else if (this.step==2) {
					this.validateFormInput('owner');
					this.validateFormInput('deal');
				}
				else if (this.step==3) {
					this.validateFormInput('warehouse_preview');
					this.validateFormInput('warehouse_feature');
				}
				else if (this.step==4) {
					this.validateFormInput('storage_type');
					this.validateFormInput('storage_preview');
					this.validateFormInput('storage_feature');
				}
				else if (this.step==5) {
					this.validateFormInput('container');
		        	this.validateFormInput('container_quantity');

		        	this.validateFormInput('rent');

		        	/*
		        	this.validateFormInput('container_price');
		        	this.validateFormInput('shelf_price');
		        	this.validateFormInput('unit_price');
		        	*/
				}

				const errorInArray = (element) => {
										return element !== null
									};

	        	const containerError = (container) => {
	        							return Object.keys(container).length > 0
	        						};

			/*
				if (this.errors.warehouse.constructor === Object && Object.keys(this.errors.warehouse).length <= 3 && this.step < 4) {

					this.step++;
					this.submitForm = true;
				}
				else if (this.errors.warehouse.constructor === Object && Object.keys(this.errors.warehouse).length <= 3 && this.step < 5 && !this.errors.warehouse.storage_features.some(errorInArray) && !this.errors.warehouse.storage_types.some(errorInArray)) {
					
					this.step++;
					this.submitForm = true;
				}
			*/

				if (this.errors.warehouse.constructor === Object && Object.keys(this.errors.warehouse).length <= 3 && !this.errors.warehouse.storage_features.some(errorInArray) && !this.errors.warehouse.storage_types.some(errorInArray) && !this.errors.warehouse.containers.some(containerError) && this.step < 6) {
					
					this.step++;
					this.submitForm = true;
				}
				else {
					this.submitForm = false;
				}

			},
			modelName(name) {
				if (!name) return ''

				const words = name.split("-");

				for (let i = 0; i < words.length; i++) {

					if (words[i]) {

				    	words[i] = words[i][0].toUpperCase() + words[i].substr(1);

					}

				}

				return words.join(" ");
			},
			userHasPermissionThroughRole(permissionName) {

				permissionName = permissionName.toLowerCase();

				if (this.singleWarehouseData.roles.length) {

					return this.singleWarehouseData.roles.some(

						userRole => userRole.permissions.some(

							rolePermission => rolePermission.name == permissionName
							
						)

					);

				}

				return false;

			},
			userHasPermission(permissionName) {

				permissionName = permissionName.toLowerCase();

				if (this.singleWarehouseData.permissions.length) {

					return this.singleWarehouseData.permissions.some(
						permission => permission.name === permissionName
					);

				}

				return false;

			},
			permissionExists(permissionName) {

				if (this.userHasPermission(permissionName) || this.userHasPermissionThroughRole(permissionName)) {

					return true;
				
				}

				return false;

			},
			getExpectedPermission(permissionName) {

				permissionName = permissionName.toLowerCase();

				if (this.allPermissions.length) {

					return this.allPermissions.find(
						permission => permission.name === permissionName
					);
				
				}

				return;

			},
			insertPermission(permissionName, event) {

				if (event.target.checked && (!this.singleWarehouseData.permissions.length || !this.permissionExists(permissionName))) {

					this.submitForm = true;
					let permission = this.getExpectedPermission(permissionName);

					if (permission) {
				   		
				   		this.singleWarehouseData.permissions.push(permission);
						
						if (permissionName.includes('create') || permissionName.includes('update') || permissionName.includes('delete') || permissionName.includes('recommend')) {

							let viewPermission = permissionName.replace(/create|update|delete|recommend/, "view").toLowerCase();
							
							if (this.getExpectedPermission(viewPermission + '-index') && ! this.$refs[viewPermission + '-index'][0].checked) {

								// this.$refs[viewPermission + '-index'][0].disabled = false;
								this.$refs[viewPermission + '-index'][0].click();

							}

						}

						if (permissionName.includes('approve')) {

							let viewPermission = permissionName.replace("approve", "recommend").toLowerCase();
							
							if (! this.$refs[viewPermission][0].checked) {

								this.$refs[viewPermission][0].click();

							}

							if (! this.$refs['update-requisition'][0].checked) {

								this.$refs['update-requisition'][0].click();

							}

						}

						this.setRelatedPermissions(permissionName);

					}

				}
				else if (!event.target.checked && this.permissionExists(permissionName)) {

					permissionName = permissionName.toLowerCase();

					let uncheckedPermissionIndex = this.singleWarehouseData.permissions.findIndex(
						permission => permission.name==permissionName
					);

					if (uncheckedPermissionIndex > -1) {
						this.singleWarehouseData.permissions.splice(uncheckedPermissionIndex, 1);
					}

					/*
						let modelName = permissionName.replace(/create|update|delete|recommend/, "");

						if (! modelName.includes('view') && ! this.permissionExists('create' + modelName) && ! this.permissionExists('update' + modelName) && ! this.permissionExists('delete' + modelName) && ! this.permissionExists('recommend' + modelName)) {

							let viewPermission = permissionName.replace(/create|update|delete|recommend/, "view").toLowerCase();
							this.$refs[viewPermission + '-index'][0].disabled = false;
						
						}
					*/

				}

			},
			checkAvailableCheckboxes() {

				this.singleWarehouseData.permissions = [];

				// reseting all permissions
				this.resetAllPermissions();
				this.disableExistingRolePermissions();

			},
			resetAllPermissions() {

				for (var ref in this.$refs) {				// as this.$refs is an object not an array
					
					this.$refs[ref][0].disabled = false;	

				}

			},
			disableExistingRolePermissions() {

				if (this.singleWarehouseData.roles.length) {

					this.singleWarehouseData.roles.forEach(
						
						userRole => {
							
							for (var ref in this.$refs) {

								const samePermission = (permission) => permission.name == ref;

								if (userRole.permissions.some(samePermission)) {

									this.$refs[ref][0].disabled = true;

									if (this.singleWarehouseData.permissions.some(userPermission=>userPermission.name == ref)) {

										this.singleWarehouseData.permissions.splice(this.singleWarehouseData.permissions.findIndex(userPermission=>userPermission.name == ref), 1);

									}

								}

							}

						}

					);

				}

			},
			/*
			insertDefaultPermissions(array = []) {
				
				if (array.length) {
					
					array.forEach(
		        		permissionName => {
							
							let permission = this.getExpectedPermission(permissionName);

							if (permission && ! this.singleWarehouseData.roles.some(warehouseRole=>warehouseRole.permissions.some(rolePermission=>rolePermission.name == permission.name)) && ! this.singleWarehouseData.permissions.some(warehousePermission => warehousePermission.name == permission.name)) {

					   			this.singleWarehouseData.permissions.push(permission);
					   			this.$refs[permissionName][0].disabled = true;

							}
		        			
		        		}
		        	);

				}

			},
			*/
			setRelatedPermissions(permissionName) {

				let permissionRefName = permissionName.toLowerCase();

				if (permissionRefName === 'create-product' || permissionRefName === 'update-product') {

					if (! this.$refs['view-product-asset-index'][0].checked) {

						this.$refs['view-product-asset-index'][0].click();

					}

				}
				else if (permissionRefName === 'create-warehouse' || permissionRefName === 'update-warehouse') {

					if (! this.$refs['view-warehouse-asset-index'][0].checked) {

						this.$refs['view-warehouse-asset-index'][0].click();

					}

					if (! this.$refs['view-warehouse-owner-index'][0].checked) {

						this.$refs['view-warehouse-owner-index'][0].click();

					}

				}
				else if (permissionRefName === 'create-product-stock' || permissionRefName === 'update-product-stock') {

					if (! this.$refs['view-merchant-index'][0].checked) {

						this.$refs['view-merchant-index'][0].click();

					}

					if (! this.$refs['view-product-index'][0].checked) {

						this.$refs['view-product-index'][0].click();

					}

					if (! this.$refs['view-merchant-product-index'][0].checked) {

						this.$refs['view-merchant-product-index'][0].click();

					}

				}

				else if (permissionRefName === 'create-merchant-product' || permissionRefName === 'update-merchant-product') {

					if (! this.$refs['view-merchant-index'][0].checked) {

						this.$refs['view-merchant-index'][0].click();

					}

					if (! this.$refs['view-product-index'][0].checked) {

						this.$refs['view-product-index'][0].click();

					}

				}

				else if (permissionRefName === 'create-merchant-deal' || permissionRefName === 'update-merchant-deal') {

					if (! this.$refs['view-merchant-index'][0].checked) {

						this.$refs['view-merchant-index'][0].click();

					}

					if (! this.$refs['view-merchant-payment-index'][0].checked) {

						this.$refs['view-merchant-payment-index'][0].click();

					}

				}

				else if (permissionRefName === 'recommend-dispatch' || permissionRefName === 'approve-dispatch') {

					if (! this.$refs['view-requisition-index'][0].checked) {

						this.$refs['view-requisition-index'][0].click();

					}

				}

				else if (permissionRefName === 'create-role' || permissionRefName === 'update-role' || permissionRefName === 'delete-role' || permissionRefName === 'view-role-index') {

					if (! this.$refs['view-permission-index'][0].checked) {

						this.$refs['view-permission-index'][0].click();

					}

				}

			},
			resetContainersRentProperties() {

				this.singleWarehouseData.containers.forEach(
					warehouseContainer => {

						this.allRentPeriods.forEach(
							rentPeriod => {
								
								this.$set(warehouseContainer.rents, `container_rent_${rentPeriod.name}`, { rent: null, rent_period_id: null, active: false });

								if (warehouseContainer.container.has_shelve && warehouseContainer.container.shelf) {
									
									this.$set(warehouseContainer.rents, `shelf_rent_${rentPeriod.name}`, { rent: null, rent_period_id: null, active: false });

									if (warehouseContainer.container.shelf.has_units && warehouseContainer.container.shelf.unit) {

										this.$set(warehouseContainer.rents, `unit_rent_${rentPeriod.name}`, { rent: null, rent_period_id: null, active: false });

									}

								}

							}
						);

					}
				);

			},
			validateFormInput (formInputName) {
				
				this.submitForm = false;

				switch(formInputName) {

					case 'site_map_preview' :

						if (!this.singleWarehouseData.site_map_preview) {
							this.errors.warehouse.site_map_preview = 'Site map is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.warehouse, 'site_map_preview');
						}

						break;

					case 'name' :

						if (this.singleWarehouseData.name && !this.singleWarehouseData.name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.warehouse.name = 'No special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.warehouse, 'name');
						}

						break;

					/*
					case 'code' :

						if (!this.singleWarehouseData.code) {
							this.errors.warehouse.code = 'Code is required';
						}
						else if (!this.singleWarehouseData.code.match(/^[-\w\.\@]{3,}$/g)) {
							this.errors.warehouse.code = 'No space or special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.warehouse, 'code');
						}

						break;
					*/

					case 'user_name' :

						if (!this.singleWarehouseData.user_name) {
							this.errors.warehouse.user_name = 'Username is required';
						}
						else if (!this.singleWarehouseData.user_name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.warehouse.user_name = 'No special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.warehouse, 'user_name');
						}

						break;

					case 'email' :

						if (!this.singleWarehouseData.email) {
							this.errors.warehouse.email = 'Email is required';
						}
						else if (!this.singleWarehouseData.email.match(/[a-z0-9._+-]+@[a-z0-9.-]+\.[a-z]{2,}$/g)) {
							this.errors.warehouse.email = 'Invalid email address';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.warehouse, 'email');
						}

						break;

					case 'mobile' :

						if (!this.singleWarehouseData.mobile) {
							this.errors.warehouse.mobile = 'Mobile is required';
						}
						else if (!this.singleWarehouseData.mobile.match(/\+?(88)?0?1[123456789][0-9]{8}\b/g)) {
							this.errors.warehouse.mobile = 'Invalid mobile number';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.warehouse, 'mobile');
						}

						break;

					case 'password' :

						if (this.createMode && !this.singleWarehouseData.password) {
							this.errors.warehouse.password = 'Password is required';
						}
						else if (this.singleWarehouseData.password && this.singleWarehouseData.password.length < 8) {
							this.errors.warehouse.password = 'Minimum length should be 8';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.warehouse, 'password');
						}

						break;

					case 'password_confirmation' :

						if (this.singleWarehouseData.password && !this.singleWarehouseData.password_confirmation) {
							this.errors.warehouse.password_confirmation = 'Password is required';
						}
						else if (this.singleWarehouseData.password && this.singleWarehouseData.password !== this.singleWarehouseData.password_confirmation) {
							this.errors.warehouse.password_confirmation = "Password doesn't match";
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.warehouse, 'password_confirmation');
						}

						break;

					case 'owner' :

						if (! this.ownerObject || Object.keys(this.ownerObject).length === 0 || ! this.singleWarehouseData.warehouse_owner_id) {
							this.errors.warehouse.owner = 'Owner is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.warehouse, 'owner');
						}

						break;

					case 'deal' :

						if (!this.singleWarehouseData.warehouse_deal) {
							this.errors.warehouse.deal = 'Deal detail is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.warehouse, 'deal');
						}

						break;

					case 'warehouse_preview' :

						if (!this.singleWarehouseData.previews.length) {
							this.errors.warehouse.preview = 'Preview is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.warehouse, 'preview');
						}

						break;

					case 'warehouse_feature' :

						if (!this.singleWarehouseData.feature.features) {
							this.errors.warehouse.feature = 'Warehouse feature is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.warehouse, 'feature');
						}

						break;

					case 'storage_type' :

						for (let index = 0; index < this.singleWarehouseData.storages.length; index++) {
							
							if (Object.keys(this.singleWarehouseData.storages[index].storage_type).length === 0) {
								this.errors.warehouse.storage_types[index] = 'Storage type is required';
							}
							else if (this.singleWarehouseData.storages.filter(currentStorage=>currentStorage.storage_type.id==this.singleWarehouseData.storages[index].storage_type.id).length > 1) {

								this.errors.warehouse.storage_types[index] = 'Same storage type selected';

							}
							else{
								this.submitForm = true;
								this.errors.warehouse.storage_types[index] = null;
								// this.$delete(this.errors.warehouse, 'storage_feature');
							}

						}

						break;

					case 'storage_preview' :

						for (let index = 0; index < this.singleWarehouseData.storages.length; index++) {
							
							if (!this.singleWarehouseData.storages[index].previews || !this.singleWarehouseData.storages[index].previews.length) {
								this.errors.warehouse.storage_preview = 'Storage preview is required';
							}
							else{
								this.submitForm = true;
								this.$delete(this.errors.warehouse, 'storage_preview');
							}

						}

						break;

					case 'storage_feature' :

						for (let index = 0; index < this.singleWarehouseData.storages.length; index++) {
							
							if (!this.singleWarehouseData.storages[index].feature.features) {
								this.errors.warehouse.storage_features[index] = 'Storage feature is required';
							}
							else{
								this.submitForm = true;
								this.errors.warehouse.storage_features[index] = null;
								// this.$delete(this.errors.warehouse, 'storage_feature');
							}

						}

						break;

					case 'container' :

						if (this.singleWarehouseData.containers.length==0) {
							
							this.errors.warehouse.container = 'Container is required';

						}
						else {

							this.$delete(this.errors.warehouse, 'container');

							for (let containerIndex = 0; containerIndex < this.singleWarehouseData.containers.length; containerIndex++) {
								
								if (Object.keys(this.singleWarehouseData.containers[containerIndex].container).length < 2) {
									this.errors.warehouse.containers[containerIndex].container = 'Container type is required';
								}
								else if (this.singleWarehouseData.containers.filter(currentContainer=>currentContainer.container.id==this.singleWarehouseData.containers[containerIndex].container.id).length > 1) {

									this.errors.warehouse.containers[containerIndex].container = 'Same Container type selected';

								}
								else{
									this.submitForm = true;
									this.$delete(this.errors.warehouse.containers[containerIndex], 'container');
								}

							}
							
						}						

						break;

					case 'container_quantity' :

						for (let containerIndex = 0; containerIndex < this.singleWarehouseData.containers.length; containerIndex++) {

							if (!this.singleWarehouseData.containers[containerIndex].quantity || this.singleWarehouseData.containers[containerIndex].quantity < 1) {
								this.errors.warehouse.containers[containerIndex].container_quantity = 'Container quantity is required';
							}
							else if (this.singleWarehouseData.containers[containerIndex].quantity < (this.singleWarehouseData.containers[containerIndex].engaged_quantity + this.singleWarehouseData.containers[containerIndex].partially_engaged)) {

								this.errors.warehouse.containers[containerIndex].container_quantity = 'Less than minimum engaged quantity.(min: '+ (this.singleWarehouseData.containers[containerIndex].engaged_quantity + this.singleWarehouseData.containers[containerIndex].partially_engaged) +')';

							}
							else{
								this.submitForm = true;
								this.$delete(this.errors.warehouse.containers[containerIndex], 'container_quantity');
							}

						}

						break;

					/*
					case 'container_price' :

						this.singleWarehouseData.containers.forEach(
							(container, containerIndex) => {

								if (Object.keys(container.rents).length === 0) {
									this.errors.warehouse.containers[containerIndex].container_price = 'One rent price is required';
								}

								else {

									this.$delete(this.errors.warehouse.containers[containerIndex], 'container_price');

									this.allRentPeriods.forEach(
										rentPeriod => {
											if ((container.rents['container_storing_price_' + rentPeriod.name] && !container.rents['container_rent_' + rentPeriod.name]) || (!container.rents['container_storing_price_' + rentPeriod.name] && container.rents['container_rent_' + rentPeriod.name])) {

												this.submitForm = false;

												if (container.rents['container_storing_price_' + rentPeriod.name]) {

													this.errors.warehouse.containers[containerIndex]['container_rent_' + rentPeriod.name] = 'Both field to be filled';

												}
												else {

													this.errors.warehouse.containers[containerIndex]['container_storing_price_' + rentPeriod.name] = 'Both field to be filled';

												}


											}
											else {
												this.submitForm = true;

												this.$delete(this.errors.warehouse.containers[containerIndex], 'container_rent_'+ rentPeriod.name);
												// this.$delete(this.errors.warehouse.containers[containerIndex], 'container_storing_price_'+ rentPeriod.name);

												// this.errors.warehouse.containers[index]['container_rent_' + rentPeriod.name] = null;
												// this.errors.warehouse.containers[index]['container_storing_price_' + rentPeriod.name] = null;
											}
										}
									);

								}

							}
						);

						break;
					*/

					case 'rent' :

						this.singleWarehouseData.containers.forEach(
							
							(warehouseContainer, containerIndex) => {

								if (! warehouseContainer.hasOwnProperty('rents') || Object.keys(warehouseContainer.rents).length === 0 || (Object.keys(warehouseContainer.rents).length === 1 && warehouseContainer.rents[Object.keys(warehouseContainer.rents)[0]]['rent'] < 1)) {

									this.errors.warehouse.containers[containerIndex]['rent'] = 'One rent is required';

								}
								else if (Object.values(warehouseContainer.rents).some(object => object.rent < 0)) {

									this.errors.warehouse.containers[containerIndex]['rent'] = 'Negative rent is invalid';

								}
								else if (Object.values(warehouseContainer.rents).some(object => object.active && (! object.rent || object.rent < 1 || object.rent == ''))) {

									this.allRentPeriods.some(
										rentPeriod => {

											if (warehouseContainer.rents[`container_rent_${rentPeriod.name}`]['active'] && (! warehouseContainer.rents[`container_rent_${rentPeriod.name}`]['rent'] || warehouseContainer.rents[`container_rent_${rentPeriod.name}`]['rent']=='')) {

												this.errors.warehouse.containers[containerIndex]['rent'] = `Container ${rentPeriod.name} rent is required`;

											}
											else if (warehouseContainer.container.has_shelve && warehouseContainer.rents[`shelf_rent_${rentPeriod.name}`]['active'] && (! warehouseContainer.rents[`shelf_rent_${rentPeriod.name}`]['rent'] || warehouseContainer.rents[`shelf_rent_${rentPeriod.name}`]['rent']=='')) {
												
												this.errors.warehouse.containers[containerIndex]['rent'] = `Shelf ${rentPeriod.name} rent is required`;

											}
											else if (warehouseContainer.container.has_shelve && warehouseContainer.container.shelf.has_units && warehouseContainer.rents[`unit_rent_${rentPeriod.name}`]['active'] && (! warehouseContainer.rents[`unit_rent_${rentPeriod.name}`]['rent'] || warehouseContainer.rents[`unit_rent_${rentPeriod.name}`]['rent']=='')) {

												this.errors.warehouse.containers[containerIndex]['rent'] = `Unit ${rentPeriod.name} rent is required`;

											}
										}
									);

								}
								else if (Object.values(warehouseContainer.rents).every(item=> ! item.rent || item.rent == '' || item.rent == 0)) {

									this.errors.warehouse.containers[containerIndex]['rent'] = 'One rent is required';

								}
								else {

									this.submitForm = true;
									this.$delete(this.errors.warehouse.containers[containerIndex], 'rent');

								}

								/*
								
								this.allRentPeriods.forEach(
									rentPeriod => {
										if ((container.rents['shelf_storing_price_' + rentPeriod.name] && !container.rents['shelf_rent_' + rentPeriod.name]) || (!container.rents['shelf_storing_price_' + rentPeriod.name] && container.rents['shelf_rent_' + rentPeriod.name])) {

											this.submitForm = false;

											if (container.rents['shelf_storing_price_' + rentPeriod.name]) {

												this.errors.warehouse.containers[containerIndex]['shelf_rent_' + rentPeriod.name] = 'Both field to be filled';

											}
											else {

												this.errors.warehouse.containers[containerIndex]['shelf_storing_price_' + rentPeriod.name] = 'Both field to be filled';

											}


										}
										else {
											this.submitForm = true;

											this.$delete(this.errors.warehouse.containers[containerIndex], 'shelf_rent_'+ rentPeriod.name);
											this.$delete(this.errors.warehouse.containers[containerIndex], 'shelf_storing_price_'+ rentPeriod.name);

											// this.errors.warehouse.containers[index]['shelf_rent_' + rentPeriod.name] = null;
											// this.errors.warehouse.containers[index]['shelf_storing_price_' + rentPeriod.name] = null;
										}
									}
								);
								
								*/

							}
						);

						break;

					/*
					case 'unit_price' :

						this.singleWarehouseData.containers.forEach(
							(container, containerIndex) => {

								this.allRentPeriods.forEach(
									rentPeriod => {
										if ((container.rents['unit_storing_price_' + rentPeriod.name] && !container.rents['unit_rent_' + rentPeriod.name]) || (!container.rents['unit_storing_price_' + rentPeriod.name] && container.rents['unit_rent_' + rentPeriod.name])) {

											this.submitForm = false;

											if (container.rents['unit_storing_price_' + rentPeriod.name]) {

												this.errors.warehouse.containers[containerIndex]['unit_rent_' + rentPeriod.name] = 'Both field to be filled';

											}
											else {

												this.errors.warehouse.containers[containerIndex]['unit_storing_price_' + rentPeriod.name] = 'Both field to be filled';

											}

										}
										else {
											this.submitForm = true;

											this.$delete(this.errors.warehouse.containers[containerIndex], 'unit_rent_'+ rentPeriod.name);
											
											// this.$delete(this.errors.warehouse.containers[containerIndex], 'unit_storing_price_'+ rentPeriod.name);

											// this.errors.warehouse.containers[index]['unit_rent_' + rentPeriod.name] = null;
											// this.errors.warehouse.containers[index]['unit_storing_price_' + rentPeriod.name] = null;
										}
									}
								);

							}
						);

						break;
					*/

				}
	 
			},
			onSiteMapChange(evnt) {
				let files = evnt.target.files || evnt.dataTransfer.files;

                // Only process image files.
		      	if (files.length && files[0].type.match('image.*')) {
                	this.submitForm = true;
                	this.$delete(this.errors.warehouse, 'site_map_preview');
                	this.createImage(files[0]);
		      	}
		      	else{
		      		this.errors.warehouse.site_map_preview = 'File should be image';
		      	}

		      	evnt.target.value = '';
		      	return;
			},
			onWarehousePreviewChange(evnt) {
				let files = evnt.target.files || evnt.dataTransfer.files;

                // Only process image files.
		      	if (files.length && files[0].type.match('image.*')) {
                	this.submitForm = true;
                	this.$delete(this.errors.warehouse, 'preview');
                	this.createImage(files[0], 'warehouse_preview');
		      	}
		      	else{
		      		this.errors.warehouse.preview = 'File should be image';
		      	}

		      	evnt.target.value = '';
		      	return;
			},
			onStoragePreviewChange(evnt, index) {
				let files = evnt.target.files || evnt.dataTransfer.files;

                // Only process image files.
		      	if (files.length && files[0].type.match('image.*')) {
                	this.submitForm = true;
                	this.$delete(this.errors.warehouse, 'storage_preview');
                	this.createImage(files[0], 'storage_preview', index);
		      	}
		      	else{
		      		this.errors.warehouse.storage_preview = 'File should be image';
		      	}

		      	evnt.target.value = '';
		      	return;
			},
			createImage(file, previewName, index=null) {
                let reader = new FileReader();
                
                reader.onload = (evnt) => {

                	if (previewName === 'warehouse_preview') {

      					var warehouse_preview = {};
						warehouse_preview['preview'] = evnt.target.result;

                		this.singleWarehouseData.previews.push(warehouse_preview); 
                	}
                	else if (previewName === 'storage_preview') {

                		var warehouse_storage_preview = {};
						warehouse_storage_preview['preview'] = evnt.target.result;

						if (!this.singleWarehouseData.storages[index].previews) {
							this.singleWarehouseData.storages[index].previews = [];
						}

                		this.singleWarehouseData.storages[index].previews.push(warehouse_storage_preview);
                	}
                	else {

                    	this.singleWarehouseData.site_map_preview = evnt.target.result;
                	}
                };

                reader.readAsDataURL(file);
            },
            removeWarehousePreview(index) {
            	this.singleWarehouseData.previews.splice(index, 1);
            },
            removeStoragePreview(storageIndex, previewIndex) {
            	this.singleWarehouseData.storages[storageIndex].previews.splice(previewIndex, 1);
            },
            getAddressData(addressData, placeResultData, id) {
				// console.log(addressData);
			},
			getOwnerFullName(owner) {
				if (!owner.first_name && !owner.last_name) {
					return 'NA';
				}

				return owner.first_name || '' + ' ' + owner.last_name || '';
			},
			changeArrow (event){
			
				if (event.target.classList.contains("fa-angle-up")) {
					event.target.classList.replace("fa-angle-up", "fa-angle-down");
				}
				else {
					event.target.classList.replace("fa-angle-down", "fa-angle-up");
				}
			
			},
			addStorage() {
				let warehouseNewStorage = {
					previews : [],
					storage_type : {},
					feature : {
						features : ''
					},
				};

				this.singleWarehouseData.storages.push(warehouseNewStorage);
			},
			removeStorage() {
				if (this.singleWarehouseData.storages.length > 1) {

					this.singleWarehouseData.storages.pop();
					this.errors.warehouse.storage_types.pop();
					this.errors.warehouse.storage_features.pop();

					// this.singleWarehouseData.storages.pop(this.singleWarehouseData.storages[this.singleWarehouseData.storages.length-1]);
					// this.errors.warehouse.storage_types[this.singleWarehouseData.storages.length-1] = null;
					// this.errors.warehouse.storage_features[this.singleWarehouseData.storages.length-1] = null;
				}
			},
			addContainer() {
				let warehouseNewContainer = {
					// quantity : 0,
					// container_id : 0,
					container : {},
					quantity : 1,
					rents : {}

				};

				this.errors.warehouse.containers.push({});
				this.singleWarehouseData.containers.push(warehouseNewContainer);
			},
			removeContainer() {
				if (this.singleWarehouseData.containers.length > 1 && !this.singleWarehouseData.containers[this.singleWarehouseData.containers.length-1].engaged_quantity && !this.singleWarehouseData.containers[this.singleWarehouseData.containers.length-1].partially_engaged) {
					this.singleWarehouseData.containers.pop();
					this.errors.warehouse.containers.pop();
				}
			},
			changeContainerRents(index) {
				this.singleWarehouseData.containers[index].rents = {};
				this.errors.warehouse.containers[index] = {};

				this.allRentPeriods.forEach(
					rentPeriod => {
						
						this.$set(this.singleWarehouseData.containers[index].rents, `container_rent_${rentPeriod.name}`, { rent: null, rent_period_id: null, active: false });

						if (this.singleWarehouseData.containers[index].container.has_shelve && this.singleWarehouseData.containers[index].container.shelf) {
							
							this.$set(this.singleWarehouseData.containers[index].rents, `shelf_rent_${rentPeriod.name}`, { rent: null, rent_period_id: null, active: false });

							if (this.singleWarehouseData.containers[index].container.shelf.has_units && this.singleWarehouseData.containers[index].container.shelf.unit) {

								this.$set(this.singleWarehouseData.containers[index].rents, `unit_rent_${rentPeriod.name}`, { rent: null, rent_period_id: null, active: false });

							}

						}

					}
				);

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

	.new-storage-enter-active, .new-storage-leave-active, .new-container-enter-active, .new-container-leave-active {
		transition: opacity .5s;
	}
	.new-storage-enter, .new-storage-leave-to, .new-container-enter, .new-container-leave-to {
		opacity: 0;
	}

	.modal { 
		overflow: auto !important; 
	}
	.modal-body {
		word-break: break-all;
	}
</style>