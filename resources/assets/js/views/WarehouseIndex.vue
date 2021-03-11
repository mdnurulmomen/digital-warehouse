
<template>

	<div class="pcoded-content">

		<breadcrumb 
			:title="'warehouses'" 
			:message="'All our warehouses'"
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
											  		:caller-page="'warehouse'" 
											  		
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

										  		<table-with-soft-delete-option 
										  			:query="query" 
										  			:per-page="perPage"  
										  			:column-names="['name', 'code', 'email', 'mobile', 'status']" 
										  			:column-values-to-show="['name', 'code', 'email', 'mobile', 'status']" 
										  			:contents-to-show = "contentsToShow" 
										  			:pagination = "pagination"

										  			@showContentDetails="showContentDetails($event)" 
										  			@openContentEditForm="openContentEditForm($event)" 
										  			@openContentDeleteForm="openContentDeleteForm($event)" 
										  			@openContentRestoreForm="openContentRestoreForm($event)" 
										  			@changeNumberContents="changeNumberContents($event)" 
										  			@fetchAllWarehouses="fetchAllWarehouses" 
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
		<div class="modal fade" id="warehouse-createOrEdit-modal">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
					  	<h4 class="modal-title">
					  		{{ createMode ? 'Create' : 'Edit' }} Warehouse
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
								        	<div class="progress-bar bg-danger" style="width:20%" v-show="step>=2">
								          		Contract Info
								        	</div>
								        	<div class="progress-bar bg-warning" style="width:20%" v-show="step>=3">
								          		Features
								        	</div>
								        	<div class="progress-bar bg-primary" style="width:20%" v-show="step>=4">
								          		Storages
								        	</div>
								        	<div class="progress-bar bg-success" style="width:20%" v-show="step==5">
								          		Containers
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
						>

							<input type="hidden" name="_token" :value="csrf">

							<transition-group name="fade">
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

											<div class="col-md-6 form-group">
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
													<i class="fa fa-2x" :class="createMode ? 'fa-angle-up' : 'fa-angle-down'" @click="changeArrow"></i>
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
							          	<button type="button" class="btn btn-outline-secondary btn-sm btn-round" v-on:click="nextPage">
					                    	<i class="fa fa-2x fa-angle-double-right" aria-hidden="true"></i>
					                  	</button>
						          	</div>
								</div>

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
				                                  		:required="true" 
				                                  		:class="!errors.warehouse.owner  ? 'is-valid' : 'is-invalid'"
				                                  		:allow-empty="false"
				                                  		selectLabel = "Press/Click"
				                                  		deselect-label="Can't remove single value"
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
							                  	<button type="button" 
							                  		class="btn btn-outline-secondary btn-sm btn-round" 
							                  		v-on:click="step-=1"
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
															class="close position-absolute text-danger" 
															style="right:0;z-index:99;" 
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
							                  	<button type="button" 
							                  		class="btn btn-outline-secondary btn-sm btn-round" 
							                  		v-on:click="step-=1"
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
									v-bind:key="4" 
									v-show="step==4"
								>								  	
							        <h2 class="ml-auto mr-auto mb-4 lead">Storages</h2>
							      
						          	<div class="col-sm-12">
						          		<div class="form-row">	
		                              		<div class="col-md-12 form-group" v-if="singleWarehouseData.storages.length">

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
								                                  		:required="true" 
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
																		class="close position-absolute text-danger" 
																		style="right:0;z-index:9;" 
																		@click="removeStoragePreview(index, index2)"
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

											  	<div class="form-row">
													<div class="form-group col-sm-6">
														<button 
															type="button"  
															class="btn btn-success btn-block btn-sm" 
															@click="addStorage"
														>
															More Storage
														</button>	
													</div>
													<div class="form-group col-sm-6">
														<button 
															type="button" 
															class="btn btn-danger btn-block btn-sm" 
															@click="removeStorage" 
															:disabled="singleWarehouseData.storages.length===1"
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
							                  		class="btn btn-outline-secondary btn-sm btn-round" 
							                  		v-on:click="step-=1"
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
									v-bind:key="5" 
									v-show="step==5"
								>								  	
							        <h2 class="ml-auto mr-auto mb-4 lead">Containers</h2>
							      
						          	<div class="col-sm-12">
						          		<div class="form-row">	
		                              		<div class="col-md-12 form-group" v-if="singleWarehouseData.containers.length && singleWarehouseData.containers.length==errors.warehouse.containers.length">

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
								                                  		:required="true" 
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
																			:key="'y' + rentPeriod.id + rentPeriod.name" 
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
																			:id="rentPeriod.name+ containerIndex" 
																			role="tabpanel" 
																			v-for="(rentPeriod, rentIndex) in allRentPeriods" 
																			:key="'x' + rentPeriod.id + rentPeriod.name" 
																			:class="{'active': rentIndex === 0}"
																		>
																			<div class="d-flex">
																	    		<!-- container -->
																    			<div class="mr-1 p-2 border w-100">
																    				<div class="form-row">
																    					<div class="sub-title">Container</div>
																    				</div>
																		    		<div class="form-row">
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
																		    			<div class="col-sm-12 form-group">	
																		    				<label for="phone">Selling Price</label>
																							<input 
																								type="number" 
																								class="form-control" 
																								v-model.number="singleWarehouseData.containers[containerIndex].rents['container_selling_price_' + rentPeriod.name]" 
																								placeholder="Selling price" 
																								:class="!errors.warehouse.containers[containerIndex]['container_selling_price_' + rentPeriod.name] ? 'is-valid' : 'is-invalid'" 
																								min="0" 
																							>
																							<div class="invalid-feedback">
																					        	{{ errors.warehouse.containers[containerIndex]['container_selling_price_' + rentPeriod.name] }}
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

																		    		<div class="form-row">
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
																		    			<div class="col-sm-12 form-group">	
																		    				<label for="phone">Selling Price</label>
																							<input 
																								type="number" 
																								class="form-control" 
																								v-model.number="singleWarehouseData.containers[containerIndex].rents['shelf_selling_price_' + rentPeriod.name]" 
																								placeholder="Selling price" 
																								:class="!errors.warehouse.containers[containerIndex]['shelf_selling_price_' + rentPeriod.name] ? 'is-valid' : 'is-invalid'" 
																								min="0" 
																							>
																							<div class="invalid-feedback">
																					        	{{ errors.warehouse.containers[containerIndex]['shelf_selling_price_' + rentPeriod.name] }}
																					  		</div>
																		    			</div>
																		    		</div>
																    			</div>
																    			<!-- unit -->
																    			<div 
																    				class="mr-1 p-2 border w-100" 
																    				v-show="singleWarehouseData.containers[containerIndex].container.has_shelve && singleWarehouseData.containers[containerIndex].container.shelf.has_units"
																    			>
																		    		<div class="form-row">
																    					<div class="sub-title">Unit</div>
																    				</div>

																		    		<div class="form-row">
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
																		    			<div class="col-sm-12 form-group">	
																		    				<label for="phone">Selling Price</label>
																							<input 
																								type="number" 
																								class="form-control" 
																								v-model.number="singleWarehouseData.containers[containerIndex].rents['unit_selling_price_' + rentPeriod.name]" 
																								placeholder="Selling price" 
																								:class="!errors.warehouse.containers[containerIndex]['unit_selling_price_' + rentPeriod.name] ? 'is-valid' : 'is-invalid'" 
																								min="0" 
																							>
																							<div class="invalid-feedback">
																					        	{{ errors.warehouse.containers[containerIndex]['unit_selling_price_' + rentPeriod.name] }}
																					  		</div>
																		    			</div>
																		    		</div>
																    			</div>
																    		</div>
																		</div>
																	</div>
																</div>
												    		</div>

												    		<div class="invalid-feedback text-center" style="display: block;" v-show="errors.warehouse.containers[containerIndex].container_price">
												    			{{ errors.warehouse.containers[containerIndex].container_price }}
												    		</div>

												    	</div>
												  	</div>

											  	</transition-group>

											  	<div class="form-row">
													<div class="form-group col-sm-6">
														<button 
															type="button"  
															class="btn btn-success btn-block btn-sm" 
															@click="addContainer"
														>
															More Container
														</button>	
													</div>
													<div class="form-group col-sm-6">
														<button 
															type="button" 
															class="btn btn-danger btn-block btn-sm" 
															@click="removeContainer" 
															:disabled="singleWarehouseData.containers.length==1"
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
							                  	<button type="button" 
							                  		class="btn btn-outline-secondary btn-sm btn-round" 
							                  		v-on:click="step-=1"
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
										<label class="col-sm-6 col-form-label">{{ singleWarehouseData.name }}</label>
									</div>
									<div class="form-group form-row">
										<label class="col-sm-6 col-form-label font-weight-bold text-right">Code :</label>
										<label class="col-sm-6 col-form-label">{{ singleWarehouseData.code }}</label>
									</div>
									<div class="form-group form-row">
										<label class="col-sm-6 col-form-label font-weight-bold text-right">Username :</label>
										<label class="col-sm-6 col-form-label">{{ singleWarehouseData.user_name }}</label>
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
										<label class="col-sm-6 col-form-label font-weight-bold text-right">Location :</label>
										<label class="col-sm-6 col-form-label"></label>
									</div>
								</div>

								<div class="tab-pane" id="owner" role="tabpanel" v-if="singleWarehouseData.owner">
									<div class="form-group form-row">
										<label class="col-sm-6 col-form-label font-weight-bold text-right">Owner name :</label>
										<label class="col-sm-6 col-form-label">{{ getOwnerFullName(singleWarehouseData.owner) }}</label>
									</div>
									<div class="form-group form-row">
										<label class="col-sm-6 col-form-label font-weight-bold text-right">Username :</label>
										<label class="col-sm-6 col-form-label">{{ singleWarehouseData.owner.user_name }}</label>
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
													<label class="col-sm-6 col-form-label" v-html="warehouseStorageType.storage_type.name"></label>
												</div>

												<div class="form-group form-row">
													<label class="col-sm-6 col-form-label font-weight-bold text-right">Storage Code:</label>
													<label class="col-sm-6 col-form-label" v-html="warehouseStorageType.storage_type.code"></label>
												</div>

												<div class="form-group form-row">
													<label class="col-sm-6 col-form-label font-weight-bold text-right">Storage Features:</label>
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
														{{ warehouseContainer.container.name }}
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
														Completely Engaged:
													</label>
													<label class="col-sm-6 col-form-label">
														{{ warehouseContainer.engaged_quantity }}
													</label>
												</div>

												<div class="form-group form-row">
													<label class="col-sm-6 col-form-label font-weight-bold text-right">
														Partially Engaged:
													</label>
													<label class="col-sm-6 col-form-label">
														{{ warehouseContainer.partially_engaged }}
													</label>
												</div>
												
									    		<div class="form-group form-row">
													<label class="col-sm-6 col-form-label font-weight-bold text-right">
														Rents:
													</label>
													<div class="col-sm-6 ">
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
															    		<div class="form-row">
															    			<div class="col-sm-12 form-group">	
															    				<label for="phone">Storing Price : </label>
															    				<label class="col-form-label">
																					{{ singleWarehouseData.containers[containerIndex].rents['container_storing_price_' + rentPeriod.name] || 'NA' }}
																				</label>
															    			</div>
															    			<div class="col-sm-12 form-group">	
															    				<label for="phone">Selling Price : </label>
															    				<label class="col-form-label">
																					{{ singleWarehouseData.containers[containerIndex].rents['container_selling_price_' + rentPeriod.name] || 'NA' }}
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

															    		<div class="form-row">
															    			<div class="col-sm-12 form-group">	
															    				<label for="phone">Storing Price : </label>
															    				<label class="col-form-label">
																					{{ singleWarehouseData.containers[containerIndex].rents['shelf_storing_price_' + rentPeriod.name] || 'NA' }}
																				</label>
															    			</div>
															    			<div class="col-sm-12 form-group">	
															    				<label for="phone">Selling Price : </label>
															    				<label class="col-form-label">
																					{{ singleWarehouseData.containers[containerIndex].rents['shelf_selling_price_' + rentPeriod.name] || 'NA' }}
																				</label>
															    			</div>
															    		</div>
													    			</div>
													    			<!-- unit -->
													    			<div 
													    				class="mr-1 p-2 border w-100" 
													    				v-show="singleWarehouseData.containers[containerIndex].container.has_shelve && singleWarehouseData.containers[containerIndex].container.shelf.has_units"
													    			>
															    		<div class="form-row">
													    					<div class="sub-title">Unit</div>
													    				</div>

															    		<div class="form-row">
															    			<div class="col-sm-12 form-group">	
															    				<label for="phone">Storing Price : </label>
															    				<label class="col-form-label">
																					{{ singleWarehouseData.containers[containerIndex].rents['unit_storing_price_' + rentPeriod.name] || 'NA' }}
																				</label>
															    			</div>
															    			<div class="col-sm-12 form-group">	
															    				<label for="phone">Selling Price : </label>
															    				<label class="col-form-label">
																					{{ singleWarehouseData.containers[containerIndex].rents['unit_selling_price_' + rentPeriod.name] || 'NA' }}
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
			:csrf="csrf" 
			:submit-method-name="'deleteWarehouse'" 
			:content-to-delete="singleWarehouseData"
			:restoration-message="'But once you think, you can restore this item !'" 
			
			@deleteWarehouse="deleteWarehouse($event)" 
		></delete-confirmation-modal>

		<restore-confirmation-modal 
			:csrf="csrf" 
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

	        }

		},
		
		created(){

			this.fetchAllWarehouses();
			this.fetchAllRentPeriods();
			this.fetchAllStorageTypes();
			this.fetchAllContainerTypes();
			this.fetchAllWarehouseOwners();

		},

		filters: {
			capitalize: function (value) {
				if (!value) return ''
				value = value.toString()
				return value.charAt(0).toUpperCase() + value.slice(1)
			}
		},

		watch: {
			ownerObject: function (object) {
				if (Object.keys(object).length > 0) {
					this.singleWarehouseData.warehouse_owner_id = object.id;
				}
			},
			'singleWarehouseData.containers': {
	            handler: function(newContainers) {

	            	newContainers.forEach(
						(container, index) => {

							if (this.errors.warehouse.containers[index]) {

								if (!container.container.has_shelve) {

									this.allRentPeriods.forEach(
										rentPeriod => {

											this.$delete(this.errors.warehouse.containers[index], 'shelf_storing_price_'+ rentPeriod.name);
											this.$delete(this.errors.warehouse.containers[index], 'shelf_selling_price_'+ rentPeriod.name);

											this.$delete(this.errors.warehouse.containers[index], 'unit_storing_price_'+ rentPeriod.name);
											this.$delete(this.errors.warehouse.containers[index], 'unit_selling_price_'+ rentPeriod.name);

										}
									);

								}

								else if (container.container.has_shelve && !container.container.shelf.has_units) {

									this.allRentPeriods.forEach(
										rentPeriod => {

											this.$delete(this.errors.warehouse.containers[index], 'unit_storing_price_'+ rentPeriod.name);
											this.$delete(this.errors.warehouse.containers[index], 'unit_selling_price_'+ rentPeriod.name);

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
				};

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
	        	
	        	this.validateFormInput('container');
	        	this.validateFormInput('container_quantity');

	        	this.validateFormInput('container_price');
	        	this.validateFormInput('shelf_price');
	        	this.validateFormInput('unit_price');

				const errorInArray = (element) => {
										return element !== null
									};

	        	const containerError = (container) => {
	        							return Object.keys(container).length > 0
	        						};

				if (this.errors.warehouse.constructor === Object && (Object.keys(this.errors.warehouse).length > 3 || this.errors.warehouse.storage_features.some(errorInArray) || this.errors.warehouse.storage_types.some(errorInArray) || this.errors.warehouse.containers.some(containerError))) {
					
					this.submitForm = false;
					return;
				}

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
					});

			},
			updateWarehouse() {
	        	
	        	this.validateFormInput('container');
	        	this.validateFormInput('container_quantity');

	        	this.validateFormInput('container_price');
	        	this.validateFormInput('shelf_price');
	        	this.validateFormInput('unit_price');

				const errorInArray = (element) => {
										return element !== null
									};

	        	const containerError = (container) => {
	        							return Object.keys(container).length > 0
	        						};

				if (this.errors.warehouse.constructor === Object && (Object.keys(this.errors.warehouse).length > 3 || this.errors.warehouse.storage_features.some(errorInArray) || this.errors.warehouse.storage_types.some(errorInArray) || this.errors.warehouse.containers.some(containerError))) {
					
					this.submitForm = false;
					return;
				}

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
					});

			},
			deleteWarehouse() {
				
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
					});

			},
			restoreWarehouse() {
				
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
			nextPage() {
				if (this.step==1) {
					this.validateFormInput('site_map_preview');
					this.validateFormInput('name');
					this.validateFormInput('code');
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

				const errorInArray = (element) => {
										return element !== null
									};

				if (this.errors.warehouse.constructor === Object && Object.keys(this.errors.warehouse).length <= 3 && this.step <= 4) {

					this.step++;
					this.submitForm = true;
				}
				else if (this.errors.warehouse.constructor === Object && Object.keys(this.errors.warehouse).length <= 3 && this.step >= 4 && !this.errors.warehouse.storage_features.some(errorInArray) && !this.errors.warehouse.storage_types.some(errorInArray)) {
					
					this.step++;
					this.submitForm = true;
				}
				else {
					this.submitForm = false;
				}
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

						if (Object.keys(this.ownerObject).length === 0 && !this.singleWarehouseData.warehouse_owner_id) {
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

						for (let index = 0; index < this.singleWarehouseData.containers.length; index++) {
							
							if (Object.keys(this.singleWarehouseData.containers[index].container).length < 2) {
								this.errors.warehouse.containers[index].container = 'Container type is required';
							}
							else if (this.singleWarehouseData.containers.filter(currentContainer=>currentContainer.container.id==this.singleWarehouseData.containers[index].container.id).length > 1) {

								this.errors.warehouse.containers[index].container = 'Same Container type selected';

							}
							else{
								this.submitForm = true;
								this.$delete(this.errors.warehouse.containers[index], 'container');
							}

						}

						break;

					case 'container_quantity' :

						for (let index = 0; index < this.singleWarehouseData.containers.length; index++) {

							if (!this.singleWarehouseData.containers[index].quantity || this.singleWarehouseData.containers[index].quantity < 1) {
								this.errors.warehouse.containers[index].container_quantity = 'Container quantity is required';
							}
							else{
								this.submitForm = true;
								this.$delete(this.errors.warehouse.containers[index], 'container_quantity');
							}

						}

						break;

					case 'container_price' :

						this.singleWarehouseData.containers.forEach(
							(container, index) => {

								if (Object.keys(container.rents).length === 0) {
									this.errors.warehouse.containers[index].container_price = 'One Price is required';
								}

								else {

									this.$delete(this.errors.warehouse.containers[index], 'container_price');

									this.allRentPeriods.forEach(
										rentPeriod => {
											if ((container.rents['container_storing_price_' + rentPeriod.name] && !container.rents['container_selling_price_' + rentPeriod.name]) || (!container.rents['container_storing_price_' + rentPeriod.name] && container.rents['container_selling_price_' + rentPeriod.name])) {

												this.submitForm = false;

												if (container.rents['container_storing_price_' + rentPeriod.name]) {

													this.errors.warehouse.containers[index]['container_selling_price_' + rentPeriod.name] = 'Both field to be filled';

												}
												else {

													this.errors.warehouse.containers[index]['container_storing_price_' + rentPeriod.name] = 'Both field to be filled';

												}


											}
											else {
												this.submitForm = true;

												this.$delete(this.errors.warehouse.containers[index], 'container_selling_price_'+ rentPeriod.name);
												this.$delete(this.errors.warehouse.containers[index], 'container_storing_price_'+ rentPeriod.name);

												// this.errors.warehouse.containers[index]['container_selling_price_' + rentPeriod.name] = null;
												// this.errors.warehouse.containers[index]['container_storing_price_' + rentPeriod.name] = null;
											}
										}
									);

								}

							}
						);

						break;

					case 'shelf_price' :

						this.singleWarehouseData.containers.forEach(
							(container, index) => {

								this.allRentPeriods.forEach(
									rentPeriod => {
										if ((container.rents['shelf_storing_price_' + rentPeriod.name] && !container.rents['shelf_selling_price_' + rentPeriod.name]) || (!container.rents['shelf_storing_price_' + rentPeriod.name] && container.rents['shelf_selling_price_' + rentPeriod.name])) {

											this.submitForm = false;

											if (container.rents['shelf_storing_price_' + rentPeriod.name]) {

												this.errors.warehouse.containers[index]['shelf_selling_price_' + rentPeriod.name] = 'Both field to be filled';

											}
											else {

												this.errors.warehouse.containers[index]['shelf_storing_price_' + rentPeriod.name] = 'Both field to be filled';

											}


										}
										else {
											this.submitForm = true;

											this.$delete(this.errors.warehouse.containers[index], 'shelf_selling_price_'+ rentPeriod.name);
											this.$delete(this.errors.warehouse.containers[index], 'shelf_storing_price_'+ rentPeriod.name);

											// this.errors.warehouse.containers[index]['shelf_selling_price_' + rentPeriod.name] = null;
											// this.errors.warehouse.containers[index]['shelf_storing_price_' + rentPeriod.name] = null;
										}
									}
								);

							}
						);

						break;

					case 'unit_price' :

						this.singleWarehouseData.containers.forEach(
							(container, index) => {

								this.allRentPeriods.forEach(
									rentPeriod => {
										if ((container.rents['unit_storing_price_' + rentPeriod.name] && !container.rents['unit_selling_price_' + rentPeriod.name]) || (!container.rents['unit_storing_price_' + rentPeriod.name] && container.rents['unit_selling_price_' + rentPeriod.name])) {

											this.submitForm = false;

											if (container.rents['unit_storing_price_' + rentPeriod.name]) {

												this.errors.warehouse.containers[index]['unit_selling_price_' + rentPeriod.name] = 'Both field to be filled';

											}
											else {

												this.errors.warehouse.containers[index]['unit_storing_price_' + rentPeriod.name] = 'Both field to be filled';

											}

										}
										else {
											this.submitForm = true;

											this.$delete(this.errors.warehouse.containers[index], 'unit_selling_price_'+ rentPeriod.name);
											this.$delete(this.errors.warehouse.containers[index], 'unit_storing_price_'+ rentPeriod.name);

											// this.errors.warehouse.containers[index]['unit_selling_price_' + rentPeriod.name] = null;
											// this.errors.warehouse.containers[index]['unit_storing_price_' + rentPeriod.name] = null;
										}
									}
								);

							}
						);

						break;

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
				if (this.singleWarehouseData.containers.length > 1) {
					this.singleWarehouseData.containers.pop();
					this.errors.warehouse.containers.pop();
				}
			},
			changeContainerRents(index) {
				this.singleWarehouseData.containers[index].rents = {};
				this.errors.warehouse.containers[index] = {};
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