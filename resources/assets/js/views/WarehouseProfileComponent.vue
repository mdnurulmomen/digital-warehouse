
<template>

	<div class="pcoded-content">

		<div class="page-header card">
			<div class="row align-items-end">
				<div class="col-lg-8">
					<div class="page-header-title">
						<i class="feather icon-warehouse bg-c-blue"></i>
						<div class="d-inline">
							<h5>Profile</h5>
							<span>Here, you may update your all info</span>
						</div>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="page-header-breadcrumb">
						<ul class=" breadcrumb breadcrumb-title">
							<li class="breadcrumb-item">
								<router-link :to="{ name: 'home' }" class="waves-effect waves-dark">
									<i class="feather icon-home"></i>
								</router-link>
							</li>
							<li class="breadcrumb-item">
								<router-link :to="{ name: 'profile' }" class="waves-effect waves-dark">
									Profile
								</router-link>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>		

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

						      			<ul class="nav nav-tabs md-tabs" role="tablist">
						      				<li class="nav-item">
						      					<a class="nav-link active show" data-toggle="tab" href="#profile" role="tab" aria-selected="true">
						      						<span class="sub-title">Profile</span>
						      					</a>
						      					<div class="slide"></div>
						      				</li>
						      				<li class="nav-item">
						      					<a class="nav-link " data-toggle="tab" href="#deal" role="tab" aria-selected="true">
						      						<span class="sub-title">Deal</span>
						      					</a>
						      					<div class="slide"></div>
						      				</li>
						      				<li class="nav-item">
						      					<a class="nav-link " data-toggle="tab" href="#warehouseFeatureAndPreviews" role="tab" aria-selected="true">
						      						<span class="sub-title">Feature</span>
						      					</a>
						      					<div class="slide"></div>
						      				</li>
						      				<li class="nav-item">
						      					<a class="nav-link " data-toggle="tab" href="#storage" role="tab" aria-selected="true">
						      						<span class="sub-title">Storage</span>
						      					</a>
						      					<div class="slide"></div>
						      				</li>
						      				<li class="nav-item">
						      					<a class="nav-link " data-toggle="tab" href="#container" role="tab" aria-selected="true">
						      						<span class="sub-title">Containers</span>
						      					</a>
						      					<div class="slide"></div>
						      				</li>
						      				<li class="nav-item">
						      					<a class="nav-link" data-toggle="tab" href="#password" role="tab" aria-selected="false">
						      						<span class="sub-title">Password</span>
						      					</a>
						      					<div class="slide"></div>
						      				</li>
						      			</ul>

						      			<div class="tab-content card-block">
						      					
						      				<div class="tab-pane active show" id="profile" role="tabpanel">
						      					
						      					<!-- form start -->
											  	<form class="form-horizontal" v-on:submit.prevent="profileUpdation">

										      		<input type="hidden" name="_token" :value="csrf">

							      					<div class="row" v-if="warehouse">
							      					 
							      						<div class="col-sm-3">
															<div class="card">
												              	<div class="card-body box-profile">
												                	<div class="text-center">
												                  		<img class="profile-warehouse-img img-fluid img-circle" :src="warehouse.site_map_preview || ''" alt="User Picture">
												                	</div>

												                	<h3 class="profile-username text-center">
												                		{{ warehouse.user_name }}
												                	</h3>

												                	<div class="row">
														                <div class="col-sm-12">
														                  	<div class="input-group">
															                    <div class="custom-file">
															                        <input 
																                        type="file" 
																                        class="custom-file-input" 
																                        @change="onSiteMapChange" 
																                        accept="image/*"
															                        >
															                        <label class="custom-file-label" for="exampleInputFile">
															                        	Change Picture
															                        </label>
															                    </div>
															                    <div class="invalid-feedback" style="display: block;" v-show="errors.site_map_preview">
																		        	{{ errors.site_map_preview }}
																		  		</div>
														                    </div>
														                </div>
													            	</div>
												              	</div>
												            </div>
														</div>
 													
														<div class="col-sm-9">
															<div class="card">
													            <div class="card-body box-profile">
													              	<div class="form-group row">
														              	<div class="col-sm-6">
														              		<div class="row">
															              		<label for="inputFirstName3" class="col-sm-4 col-form-label text-right">Name</label>
																                <div class="col-sm-8">
																                  	<input 
																	                  type="text" 
																	                  class="form-control" 
																	                  v-model="warehouse.name" 
																	                  placeholder="First Name" 
																	                  :class="!errors.name  ? 'is-valid' : 'is-invalid'" 
																	                  @change="validateFormInput('name')"
																					>
																					<div class="invalid-feedback">
																			        	{{ errors.name }}
																			  		</div>
																                </div>
														              		</div>
														              	</div>
														                <div class="col-sm-6">
														                	<div class="row">
															              		<label for="inputLastName3" class="col-sm-4 col-form-label text-right">Code</label>
																                <div class="col-sm-8">
																                  	<input 
																	                  	type="text" 
																	                  	class="form-control" 
																	                  	v-model="warehouse.code" 
																	                  	placeholder="Last Name" 
																	                  	:class="!errors.code  ? 'is-valid' : 'is-invalid'" 
																	                  	@change="validateFormInput('code')"
																                  	>
																                  	<div class="invalid-feedback">
																			        	{{ errors.code }}
																			  		</div>
																                </div>
														                	</div>
														              	</div>
													              	</div>
													              	<div class="form-group row">
														              	<div class="col-sm-6">
														              		<div class="row">
															              		<label for="inputEmail3" class="col-sm-4 col-form-label text-right">Email</label>
																                <div class="col-sm-8">
																                  	<input 
																                  		type="email" 
																                  		class="form-control" 
																                  		v-model="warehouse.email" 
																                  		placeholder="Email" required="true" 
																                  		:class="!errors.email  ? 'is-valid' : 'is-invalid'" 
														                  				@change="validateFormInput('email')"
																                  	>
																                  	<div class="invalid-feedback">
																			        	{{ errors.email }}
																			  		</div>
																                </div>
														              		</div>
														              	</div>
														                <div class="col-sm-6">
														                	<div class="row">
														                		<label for="inputMobile3" class="col-sm-4 col-form-label text-right">Mobile</label>
																                <div class="col-sm-8">
																                  	<input 
																	                  	type="tel" 
																	                  	class="form-control" 
																	                  	v-model="warehouse.mobile" 
																	                  	placeholder="Mobile" 
																	                  	required="true" 
																	                  	:class="!errors.mobile  ? 'is-valid' : 'is-invalid'" 
														                  				@change="validateFormInput('mobile')"
																                  	>
																                  	<div class="invalid-feedback">
																			        	{{ errors.mobile }}
																			  		</div>
																                </div>
														                	</div>
														              	</div>
													              	</div>
													              	<div class="form-group row">
														              	<div class="col-sm-6">
														              		<div class="row">
															              		<label for="inputEmail3" class="col-sm-4 col-form-label text-right">Username</label>
																                <div class="col-sm-8">
																                  	<input 
																	                  	type="text" 
																	                  	class="form-control" 
																	                  	v-model="warehouse.user_name" 
																	                  	placeholder="Unique Username" 
																	                  	required="true" 
																	                  	:class="!errors.user_name  ? 'is-valid' : 'is-invalid'" 
													                  					@change="validateFormInput('user_name')"
																                  	>
																                  	<div class="invalid-feedback">
																			        	{{ errors.user_name }}
																			  		</div>
																                </div>
														              		</div>
														              	</div>
													              	</div>
													            </div>
													            <!-- /.card-body -->
													            <div class="card-footer text-center">
																	<div class="col-sm-12 mb-1" v-show="!submitForm">
																		<span class="text-danger small">
																	  		Please input required fields
																	  	</span>
																	</div>
																	<div class="col-sm-12">
																		<button type="submit" class="btn btn-primary" :disabled="!submitForm">
																			Update Profile
																		</button>
																	</div>
																</div>
													        	<!-- /.card-footer -->
														    </div>
														</div>

							      					</div>

							      				</form>
							      				<!-- form end -->
						      				</div>

						      				<!-- warehouse deal -->
						      				<div class="tab-pane" id="deal" role="tabpanel">
						      					
						      					<form class="form-horizontal" v-on:submit.prevent="warehouseDealUpdation">
				      		
										      		<input type="hidden" name="_token" :value="csrf">
										            
										            <div class="card">
										            	
											            <div 
												            class="card-body box-profile" 
												            v-if="warehouse.owner"
											            >  

											              	<div class="form-group row">	
							                              		<label class="col-sm-3 col-form-label text-right">
							                              			Owner
							                              		</label>	
							                              		<div class="col-md-9">
						                              				<multiselect 
							                                  			v-model="warehouse.owner"
							                                  			placeholder="Warehouse Owner" 
								                                  		label="user_name" 
								                                  		track-by="id" 
								                                  		:options="allWarehouseOwners" 
								                                  		:required="true" 
								                                  		class="form-control p-0" 
								                                  		:class="!errors.owner  ? 'is-valid' : 'is-invalid'"
								                                  		:allow-empty="false"
								                                  		selectLabel = "Press/Click"
								                                  		deselect-label="Can't remove single value"
								                                  		@close="validateFormInput('owner')" 
								                                  		@input="setWarehouseOwner()"
							                                  		>
								                                	</multiselect>
								                              		
								                              		<div 
									                                  	class="invalid-feedback" 
									                                  	style="display: block;" 
									                                  	v-show="errors.owner"
									                                >
																        {{ errors.owner }}
																  	</div>
							                              		</div>
								                            </div>
									                          
									                        <div class="form-group row">
																<label class="col-sm-3 col-form-label text-right">
																	Warehouse Deal (rental/contractual)
																</label>
									                          	<div class="col-md-9">
									                              	<ckeditor 
										                              	class="form-control" 
										                              	:editor="editor" 
										                              	v-model="warehouse.warehouse_deal"
										                              	:class="!errors.deal  ? 'is-valid' : 'is-invalid'"
										                              	@blur="validateFormInput('deal')"
										                            >
									                              	</ckeditor>

									                              	<div 
									                                  	class="invalid-feedback" 
									                                >
																        {{ errors.deal }}
																  	</div>
										                        </div>
									                        </div> 

											            </div>
											            <!-- /.card-body -->
											            <div class="card-footer text-center">
															<div class="col-sm-12 mb-1" v-show="!submitForm">
																<span class="text-danger small">
															  		Please input required fields
															  	</span>
															</div>
															<div class="col-sm-12">
																<button type="submit" class="btn btn-primary" :disabled="!submitForm">
																	Update Deal
																</button>
															</div>
														</div>
											        	<!-- /.card-footer -->

										            </div>

										      	</form>

						      				</div>

						      				<!-- Warehouse Feature and Previews -->
						      				<div class="tab-pane" id="warehouseFeatureAndPreviews" role="tabpanel">
						      					
						      					<form 
						      						class="form-horizontal" 
						      						v-on:submit.prevent="warehouseFeatureAndPreviewsUpdation"
						      					>
				      		
										      		<input type="hidden" name="_token" :value="csrf">

							                    	<div class="card">

												    	<div class="card-body box-profile">

												    		<div 
													    		class="form-group row" 
													    		v-if="warehouse.previews && warehouse.previews.length"
												    		>
																<div 
																	class="col-sm-4"
																	v-for="(warehousePreview, index) in warehouse.previews" 
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

															<div class="form-group row" v-else>
																<div class="col-md-12 text-center">
																	<span class="text-danger">No previews yet</span>	
																</div>
															</div>

															<div class="form-group row">	
																<div class="col-md-12 text-center">
																	<div class="custom-file">
																	    <input type="file" 
																	    	class="form-control custom-file-input" 
																			:class="!errors.preview ? 'is-valid' : 'is-invalid'" 
																    	 	@change="onWarehousePreviewChange" 
																    	 	accept="image/*"
																	    >
																	    <label class="custom-file-label">Choose Preview...</label>
																	    <div class="invalid-feedback">
																	    	{{ errors.preview }}
																	    </div>
																  	</div>
																</div>
															</div>

														  	<div class="form-group row">
																<label class="col-sm-3 col-form-label text-right">
																	Warehouse Features
																</label>
																<div class="col-sm-9">
									                              	<ckeditor 
										                              	class="form-control" 
										                              	:editor="editor" 
										                              	v-model="warehouse.feature.features"
										                              	:class="!errors.feature  ? 'is-valid' : 'is-invalid'"
										                              	@blur="validateFormInput('warehouse_feature')"
										                            >
									                              	</ckeditor>

									                              	<div 
									                                  	class="invalid-feedback" 
									                                >
																        {{ errors.feature }}
																  	</div>
																</div>
									                        </div>

												    	</div>

												    	<!-- /.card-body -->
											            <div class="card-footer text-center">
															<div class="col-sm-12 mb-1" v-show="!submitForm">
																<span class="text-danger small">
															  		Please input required fields
															  	</span>
															</div>
															<div class="col-sm-12">
																<button type="submit" class="btn btn-primary" :disabled="!submitForm">
																	Update Feature
																</button>
															</div>
														</div>
											        	<!-- /.card-footer -->

												  	</div>
										            
										      	</form>

						      				</div>

						      				<!-- Warehouse Storage -->
						      				<div class="tab-pane" id="storage" role="tabpanel">
						      					
						      					<form class="form-horizontal" v-on:submit.prevent="storageUpdation">
				      		
										      		<input type="hidden" name="_token" :value="csrf">

							                    	<div class="card">

												    	<div 
													    	class="card-body box-profile" 
													    	v-for="(warehouseStorageType, index) in warehouse.storages" 
					                              			:key="'E' + index + warehouseStorageType.id"
				                              			>

												    		<div class="form-group row">
												    			
											    				<label class="col-sm-3 col-form-label text-right">
											    					Storage Type
											    				</label>

											    				<div class="col-sm-9">
												    				<multiselect 
							                                  			v-model="warehouse.storages[index].storage_type"
							                                  			placeholder="Storage Type" 
								                                  		label="name" 
								                                  		track-by="id" 
								                                  		:options="allStorageTypes" 
								                                  		:required="true" 
								                                  		class="form-control p-0" 
								                                  		:class="!errors.storage_types[index] ? 'is-valid' : 'is-invalid'"
								                                  		:allow-empty="false"
								                                  		selectLabel = "Press/Click"
								                                  		deselect-label="Can't remove single value"
								                                  		@close="validateFormInput('storage_type')"
							                                  		>
								                                	</multiselect>

								                                	<div 
									                                	class="invalid-feedback" 
									                                	style="display: block;" 
									                                	v-show="errors.storage_types[index]"
								                                	>
																    	{{ errors.storage_types[index] }}
																    </div>
																</div>
												    			
												    		</div>

												    		<div 
												    			class="form-row" 
												    			v-if="warehouse.storages[index].previews && warehouse.storages[index].previews.length"
												    		>
																<div 
																	class="form-group col-sm-4" 
																	v-for="(warehouseStoragePreview, index2) in warehouse.storages[index].previews" 
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

															<div class="form-group row" v-else>
																<div class="col-md-12 text-center">
																	<span class="text-danger">No previews yet</span>	
																</div>
															</div>

															<div class="form-group row">
																<div class="col-sm-12 text-center">	
																	<div class="custom-file">
																	    <input type="file" 
																	    	class="form-control custom-file-input" 
																			:class="!errors.storage_preview ? 'is-valid' : 'is-invalid'" 
																    	 	@change="onStoragePreviewChange($event, index)" 
																    	 	accept="image/*"
																	    >
																	    <label class="custom-file-label">Choose Preview...</label>
																	    <div class="invalid-feedback">
																	    	{{ errors.storage_preview }}
																	    </div>
																  	</div>	
																</div>
															</div>

															<div 
																class="form-group row" 
																v-if="warehouse.storages[index].feature"
															>
																	
																<label class="col-sm-3 col-form-label text-right">
																	Features
																</label>

																<div class="col-sm-9">
																	
																	<ckeditor 
										                              	class="form-control" 
										                              	:editor="editor" 
										                              	v-model="warehouse.storages[index].feature.features"
										                              	:class="!errors.storage_features[index]  ? 'is-valid' : 'is-invalid'"
										                              	@blur="validateFormInput('storage_feature')"
										                            >
									                              	</ckeditor>

																	<div class="invalid-feedback">
																        {{ errors.storage_features[index] }}
																  	</div>

																</div>
																
															</div>

												    	</div>

												    	<!-- /.card-body -->
											            <div class="card-footer text-center">
															<div class="col-sm-12 mb-1" v-show="!submitForm">
																<span class="text-danger small">
															  		Please input required fields
															  	</span>
															</div>
															<div class="col-sm-12">
																<button type="submit" class="btn btn-primary" :disabled="!submitForm">
																	Update Storage
																</button>
															</div>
														</div>
											        	<!-- /.card-footer -->

												  	</div>
										            
										      	</form>

						      				</div>

						      				<!-- Warehouse Container -->
						      				<div class="tab-pane" id="container" role="tabpanel">
						      					
						      					<form class="form-horizontal" v-on:submit.prevent="containerUpdation">
				      		
										      		<input type="hidden" name="_token" :value="csrf">

							                    	<div 
								                    	class="card" 
								                    	v-if="warehouse.containers.length==errors.containers.length"
							                    	>

												    	<div 
												    		class="card-body box-profile" 
												    		v-for="(warehouseContainer, index) in warehouse.containers" 
				                              				:key="'F' + index + warehouseContainer.id"
												    	>
												    		
												    		<div class="form-row">
												    			<div class="form-group col-sm-6">
												    				<label>Container Type</label>
												    				<multiselect 
							                                  			v-model="warehouse.containers[index].container"
							                                  			placeholder="Container Type" 
								                                  		label="name" 
								                                  		track-by="id" 
								                                  		:options="allContainers" 
								                                  		:required="true" 
								                                  		class="form-control p-0" 
								                                  		:class="!errors.containers[index].container ? 'is-valid' : 'is-invalid'"
								                                  		:allow-empty="false"
								                                  		selectLabel = "Press/Click"
								                                  		deselect-label="Can't remove single value"
								                                  		@close="validateFormInput('container')" 
								                                  		@input="changeContainerRents(index)"
							                                  		>
								                                	</multiselect>

								                                	<div 
									                                	class="invalid-feedback" 
									                                	style="display: block;" 
									                                	v-show="errors.containers[index].container"
								                                	>
																    	{{ errors.containers[index].container }}
																    </div>
												    			</div>

												    			<div class="form-group col-sm-6">	
												    				<label>Quantity</label>
																	<input type="number" 
																		class="form-control" 
																		v-model.number="warehouse.containers[index].quantity" 
																		placeholder="Lenght of container" 
																		:class="!errors.containers[index].container_quantity ? 'is-valid' : 'is-invalid'" 
																		@blur="validateFormInput('container_quantity')" 
																		required="true" 
																	>
																	<div class="invalid-feedback">
															        	{{ errors.containers[index].container_quantity }}
															  		</div>
												    			</div>
												    		</div>

												    		<div class="form-row">	
																<div class="col-sm-12 form-group">

																	<label>Rents</label>
																	
																	<ul class="nav nav-tabs tabs justify-content-center" role="tablist">
																		<li 
																			class="nav-item"
																			v-for="(rentPeriod, rentIndex) in allRentPeriods" 
																			:key="'y' + rentPeriod.id + rentPeriod.name" 
																		>
																			<a 
																				class="nav-link" 
																				data-toggle="tab" 
																				:href="'#' + rentPeriod.name + index" 
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
																			:id="rentPeriod.name+ index" 
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
																								v-model.number="warehouse.containers[index].rents['container_storing_price_' + rentPeriod.name]" 
																								placeholder="Storing price" 
																								:class="!errors.containers[index]['container_storing_price_' + rentPeriod.name] ? 'is-valid' : 'is-invalid'" 
																								min="0" 
																							>
																							<div class="invalid-feedback">
																					        	{{ errors.containers[index]['container_storing_price_' + rentPeriod.name] }}
																					  		</div>
																		    			</div>
																		    			<div class="col-sm-12 form-group">	
																		    				<label for="phone">Selling Price</label>
																							<input 
																								type="number" 
																								class="form-control" 
																								v-model.number="warehouse.containers[index].rents['container_selling_price_' + rentPeriod.name]" 
																								placeholder="Selling price" 
																								:class="!errors.containers[index]['container_selling_price_' + rentPeriod.name] ? 'is-valid' : 'is-invalid'" 
																								min="0" 
																							>
																							<div class="invalid-feedback">
																					        	{{ errors.containers[index]['container_selling_price_' + rentPeriod.name] }}
																					  		</div>
																		    			</div>
																		    		</div>
																    			</div>
																    			<!-- shelf -->
																    			<div 
																    				class="mr-1 p-2 border w-100" 
																    				v-show="warehouse.containers[index].container.has_shelve"
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
																								v-model.number="warehouse.containers[index].rents['shelf_storing_price_' + rentPeriod.name]" 
																								placeholder="Storing price" 
																								:class="!errors.containers[index]['shelf_storing_price_' + rentPeriod.name] ? 'is-valid' : 'is-invalid'" 
																								min="0" 
																							>
																							<div class="invalid-feedback">
																					        	{{ errors.containers[index]['shelf_storing_price_' + rentPeriod.name] }}
																					  		</div>
																		    			</div>
																		    			<div class="col-sm-12 form-group">	
																		    				<label for="phone">Selling Price</label>
																							<input 
																								type="number" 
																								class="form-control" 
																								v-model.number="warehouse.containers[index].rents['shelf_selling_price_' + rentPeriod.name]" 
																								placeholder="Selling price" 
																								:class="!errors.containers[index]['shelf_selling_price_' + rentPeriod.name] ? 'is-valid' : 'is-invalid'" 
																								min="0" 
																							>
																							<div class="invalid-feedback">
																					        	{{ errors.containers[index]['shelf_selling_price_' + rentPeriod.name] }}
																					  		</div>
																		    			</div>
																		    		</div>
																    			</div>
																    			<!-- unit -->
																    			<div 
																    				class="mr-1 p-2 border w-100" 
																    				v-show="warehouse.containers[index].container.has_shelve && warehouse.containers[index].container.shelf.has_units"
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
																								v-model.number="warehouse.containers[index].rents['unit_storing_price_' + rentPeriod.name]" 
																								placeholder="Storing price"
																								:class="!errors.containers[index]['unit_storing_price_' + rentPeriod.name] ? 'is-valid' : 'is-invalid'" 
																								min="0" 
																							>
																							<div class="invalid-feedback">
																					        	{{ errors.containers[index]['unit_storing_price_' + rentPeriod.name] }}
																					  		</div>
																		    			</div>
																		    			<div class="col-sm-12 form-group">	
																		    				<label for="phone">Selling Price</label>
																							<input 
																								type="number" 
																								class="form-control" 
																								v-model.number="warehouse.containers[index].rents['unit_selling_price_' + rentPeriod.name]" 
																								placeholder="Selling price" 
																								:class="!errors.containers[index]['unit_selling_price_' + rentPeriod.name] ? 'is-valid' : 'is-invalid'" 
																								min="0" 
																							>
																							<div class="invalid-feedback">
																					        	{{ errors.containers[index]['unit_selling_price_' + rentPeriod.name] }}
																					  		</div>
																		    			</div>
																		    		</div>
																    			</div>
																    		</div>
																		</div>
																	</div>
																</div>
												    		</div>

												    		<div class="invalid-feedback text-center" style="display: block;" v-show="errors.containers[index].container_price">
												    			{{ errors.containers[index].container_price }}
												    		</div>

												    	</div>

												    	<!-- /.card-body -->
											            <div class="card-footer text-center">
															<div class="col-sm-12 mb-1" v-show="!submitForm">
																<span class="text-danger small">
															  		Please input required fields
															  	</span>
															</div>
															<div class="col-sm-12">
																<button type="submit" class="btn btn-primary" :disabled="!submitForm">
																	Update Container
																</button>
															</div>
														</div>
											        	<!-- /.card-footer -->

												  	</div>
										            
										      	</form>

						      				</div>

						      				<!-- warehouse password -->
						      				<div class="tab-pane" id="password" role="tabpanel">
						      					
						      					<form class="form-horizontal" v-on:submit.prevent="passwordUpdation">
				      		
										      		<input type="hidden" name="_token" :value="csrf">
										            
										            <div class="card-body box-profile">  

										              	<div class="form-group row">
										              		<label for="inputPassword3" class="col-sm-3 col-form-label text-right">
										              			Current Password
										              		</label>
											                <div class="col-sm-9">
											                  	<input 
												                  	type="password" 
												                  	class="form-control" 
												                  	v-model="password.current_password" 
												                  	placeholder="Current Password" 
												                  	required="true" 
												                  	:class="!errors.current_password  ? 'is-valid' : 'is-invalid'" 
									                  				@change="validateFormInput('current_password')"
											                  	>
											                  	<div class="invalid-feedback">
														        	{{ errors.current_password }}
														  		</div>
											                </div>
										              	</div>
										              	<div class="form-group row">
										              		<label for="inputNewPassword3" class="col-sm-3 col-form-label text-right">
										              			New Password
										              		</label>
											                <div class="col-sm-9">
											                  	<input 
												                  	type="password" 
												                  	class="form-control" 
												                  	v-model="password.password" 
												                  	placeholder="New Password" 
												                  	required="true" 
												                  	:class="!errors.password  ? 'is-valid' : 'is-invalid'" 
									                  				@change="validateFormInput('password')"
											                  	>
											                  	<div class="invalid-feedback">
														        	{{ errors.password }}
														  		</div>
											                </div>
										              	</div>
										              	<div class="form-group row">
										              		<label for="inputConfirmPassword3" class="col-sm-3 col-form-label text-right">
										              			Confirm Password
										              		</label>
											                <div class="col-sm-9">
											                  	<input 
												                  	type="password" 
												                  	class="form-control" 
												                  	v-model="password.password_confirmation" 
												                  	placeholder="Confirm Password" 
												                  	required="true" 
												                  	:class="!errors.password_confirmation  ? 'is-valid' : 'is-invalid'" 
									                  				@change="validateFormInput('password_confirmation')"
											                  	>
											                  	<div class="invalid-feedback">
														        	{{ errors.password_confirmation }}
														  		</div>
											                </div>
										              	</div>

										            </div>
										            <!-- /.card-body -->
										            <div class="card-footer text-center">
														<div class="col-sm-12 mb-1" v-show="!submitForm">
															<span class="text-danger small">
														  		Please input required fields
														  	</span>
														</div>
														<div class="col-sm-12">
															<button type="submit" class="btn btn-primary" :disabled="!submitForm">
																Update Password
															</button>
														</div>
													</div>
										        	<!-- /.card-footer -->
										      	</form>

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

</template>

<script type="text/javascript">

	import axios from 'axios';
	import Multiselect from 'vue-multiselect';
	import CKEditor from '@ckeditor/ckeditor5-vue';
	import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

	export default {

		components: { 
			multiselect : Multiselect,
			ckeditor: CKEditor.component,
		},

	    data() {
	        return {
	        	editor: ClassicEditor,

	        	warehouse : {

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

	        	},

	        	password : {},

	        	allContainers : [],
	        	allRentPeriods : [],
	        	allStorageTypes : [],
	        	allWarehouseOwners : [],

	        	errors : {
        		
        			storage_features : [],
        			storage_types : [],
        			containers : [
        				{
	        				// quantity : null,
	        				// container_id : null,
        				},
        			],
        		
	        	},


	        	error : '',
	        	loading : false,

	        	submitForm : true,

	            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
	        }
		},
		created(){
			this.fetchWarehouseData();
			this.fetchAllWarehouseOwners();
			this.fetchAllStorageTypes();
			this.fetchAllContainerTypes();
			this.fetchAllRentPeriods();
		},

		filters: {
			capitalize: function (value) {
				if (!value) return ''
				value = value.toString()
				return value.charAt(0).toUpperCase() + value.slice(1)
			}
		},

		methods : {

			fetchWarehouseData() {
				
				this.error = '';
				this.loading = true;

				axios
					.get('/api/profile')
					.then(response => {
						// handle success
						this.warehouse = response.data.data || {};

						this.errors = {
		        			storage_features : [],
		        			storage_types : [],
		        			containers : [],
			        	};

			        	this.warehouse.containers.forEach(
			        		container => {
			        			this.errors.containers.push({});
			        		}
			        	);
					})
					.catch(error => {
					    // handle error
					    this.error = error.toString();
					})
					.finally(() => {
			    		// always executed
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
					.get('/api/container-types/')
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
            profileUpdation() {

				this.validateFormInput('site_map_preview');
				this.validateFormInput('name');
				this.validateFormInput('code');
				this.validateFormInput('user_name');
				this.validateFormInput('email');
				this.validateFormInput('mobile');

				if (this.errors.constructor === Object && (this.errors.site_map_preview || this.errors.name || this.errors.code || this.errors.user_name|| this.errors.email || this.errors.mobile)) {

					this.submitForm = false;
					return;
				}

				axios
					.put('/warehouse-profile/', this.warehouse)
					.then(response => {
						if (response.status == 200) {
							this.warehouse = response.data.data || {};
							this.$toastr.s("Profile has been updated", "Success");
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
			warehouseDealUpdation() {

				this.validateFormInput('owner');
				this.validateFormInput('deal');

				if (this.errors.constructor === Object && (this.errors.owner || this.errors.deal)) {

					this.submitForm = false;
					return;
				}

				axios
					.put('/warehouse-deal/', this.warehouse)
					.then(response => {
						if (response.status == 200) {
							this.warehouse = response.data.data || {};
							this.$toastr.s("Deal has been updated", "Success");
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
			warehouseFeatureAndPreviewsUpdation() {

				this.validateFormInput('warehouse_preview');
				this.validateFormInput('warehouse_feature');

				if (this.errors.constructor === Object && (this.errors.preview || this.errors.feature)) {

					this.submitForm = false;
					return;
				}

				axios
					.put('/warehouse-feature-previews/', this.warehouse)
					.then(response => {
						if (response.status == 200) {
							this.warehouse = response.data.data || {};
							this.$toastr.s("Feature and Previews has been updated", "Success");
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
			storageUpdation() {

				this.validateFormInput('storage_type');
				this.validateFormInput('storage_preview');
				this.validateFormInput('storage_feature');

				if (this.errors.constructor === Object && (this.errorInArray(this.errors.storage_types) || this.errors.storage_preview || this.errorInArray(this.errors.storage_features))) {

					this.submitForm = false;
					return;
				}

				axios
					.put('/warehouse-storages/', this.warehouse)
					.then(response => {
						if (response.status == 200) {
							this.warehouse = response.data.data || {};
							this.$toastr.s("Storages has been updated", "Success");
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
			containerUpdation() {

				this.validateFormInput('container');
				this.validateFormInput('container_quantity');
				
				this.validateFormInput('container_price');
	        	this.validateFormInput('shelf_price');
	        	this.validateFormInput('unit_price');

				if (this.errors.constructor === Object && this.errorInObject(this.errors.containers)) {

					this.submitForm = false;
					return;
				}

				axios
					.put('/warehouse-containers/', this.warehouse)
					.then(response => {
						if (response.status == 200) {
							this.warehouse = response.data.data || {};
							this.$toastr.s("Container has been updated", "Success");
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
			passwordUpdation() {

				if (!this.password.current_password || !this.password.password || !this.password.password_confirmation) {

					this.validateFormInput('current_password');
					this.validateFormInput('password');
					this.validateFormInput('password_confirmation');

					this.submitForm = false;
					return;
				}

				axios
					.post('/password', this.password)
					.then(response => {
						// handle success
						if (response.status == 200) {
							this.password = {};
							this.$toastr.s("Password has been updated", "Success");
						}
					})
					.catch(error => {
						// handle error
						if (error.response.status == 422) {

							for (var x in error.response.data.errors) {
								this.$toastr.w(error.response.data.errors[x], "Warning");
							}
				      	}
				      	else if (error.response.status == 401) {
							
							this.$toastr.e("Wrong Current Password", "Oops");	
				      	}
					});
			},
			errorInArray(array) {

				const error = (element) => element !== null;

				return array.some(error);

			},
			errorInObject(array) {

				const error = (container) => {
	        							return Object.keys(container).length > 0
	        						};

	        	return array.some(error);

			},
			onSiteMapChange(evnt) {
				let files = evnt.target.files || evnt.dataTransfer.files;

                // Only process image files.
		      	if (files.length && files[0].type.match('image.*')) {
                	this.submitForm = true;
                	this.$delete(this.errors, 'site_map_preview');
                	this.createImage(files[0]);
		      	}
		      	else{
		      		this.errors.site_map_preview = 'File should be image';
		      	}

		      	evnt.target.value = '';
		      	return;
			},
			onWarehousePreviewChange(evnt) {
				let files = evnt.target.files || evnt.dataTransfer.files;

                // Only process image files.
		      	if (files.length && files[0].type.match('image.*')) {
                	this.submitForm = true;
                	this.$delete(this.errors, 'preview');
                	this.createImage(files[0], 'warehouse_preview');
		      	}
		      	else{
		      		this.errors.preview = 'File should be image';
		      	}

		      	evnt.target.value = '';
		      	return;
			},
			onStoragePreviewChange(evnt, index) {
				let files = evnt.target.files || evnt.dataTransfer.files;

                // Only process image files.
		      	if (files.length && files[0].type.match('image.*')) {
                	this.submitForm = true;
                	this.$delete(this.errors, 'storage_preview');
                	this.createImage(files[0], 'storage_preview', index);
		      	}
		      	else{
		      		this.errors.storage_preview = 'File should be image';
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

                		this.warehouse.previews.push(warehouse_preview); 
                	}
                	else if (previewName === 'storage_preview') {

                		var warehouse_storage_preview = {};
						warehouse_storage_preview['preview'] = evnt.target.result;

						if (!this.warehouse.storages[index].previews) {
							this.warehouse.storages[index].previews = [];
						}

                		this.warehouse.storages[index].previews.push(warehouse_storage_preview);
                	}
                	else {

                    	this.warehouse.site_map_preview = evnt.target.result;
                	}
                };

                reader.readAsDataURL(file);
            },
            removeWarehousePreview(index) {
            	this.warehouse.previews.splice(index, 1);
            },
            removeStoragePreview(storageIndex, previewIndex) {
            	this.warehouse.storages[storageIndex].previews.splice(previewIndex, 1);
            },
			getOwnerFullName(owner) {
				if (!owner.first_name && !owner.last_name) {
					return 'NA';
				}

				return owner.first_name || '' + ' ' + owner.last_name || '';
			},
			addStorage() {
				let warehouseNewStorage = {
					previews : [],
					storage_type : {},
					feature : {
						features : ''
					},
				};

				this.warehouse.storages.push(warehouseNewStorage);
			},
			removeStorage() {
				if (this.warehouse.storages.length > 1) {

					this.warehouse.storages.pop();
					this.errors.storage_types.pop();
					this.errors.storage_features.pop();

					// this.warehouse.storages.pop(this.warehouse.storages[this.warehouse.storages.length-1]);
					// this.errors.storage_types[this.warehouse.storages.length-1] = null;
					// this.errors.storage_features[this.warehouse.storages.length-1] = null;
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

				this.errors.containers.push({});
				this.warehouse.containers.push(warehouseNewContainer);
			},
			removeContainer() {
				if (this.warehouse.containers.length > 1) {
					this.warehouse.containers.pop();
					this.errors.containers.pop();
				}
			},
			changeContainerRents(index) {
				this.warehouse.containers[index].rents = {};
				this.errors.containers[index] = {};
			},
			setWarehouseOwner() {
				if (Object.keys(this.warehouse.owner).length > 0) {
					this.warehouse.warehouse_owner_id = this.warehouse.owner.id;
				}
			},
            validateFormInput (formInputName) {
				
				this.submitForm = false;

				switch(formInputName) {

					case 'site_map_preview' :

						if (!this.warehouse.site_map_preview) {
							this.errors.site_map_preview = 'Site map is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'site_map_preview');
						}

						break;

					case 'name' :

						if (this.warehouse.name && !this.warehouse.name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.name = 'No special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'name');
						}

						break;

					case 'code' :

						if (!this.warehouse.code) {
							this.errors.code = 'Code is required';
						}
						else if (!this.warehouse.code.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.code = 'No special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'code');
						}

						break;

					case 'email' :

						if (!this.warehouse.email) {
							this.errors.email = 'Email is required';
						}
						else if (!this.warehouse.email.match(/[a-z0-9._+-]+@[a-z0-9.-]+\.[a-z]{2,}$/g)) {
							this.errors.email = 'Invalid email address';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'email');
						}

						break;

					case 'mobile' :

						if (!this.warehouse.mobile) {
							this.errors.mobile = 'Mobile is required';
						}
						else if (!this.warehouse.mobile.match(/\+?(88)?0?1[123456789][0-9]{8}\b/g)) {
							this.errors.mobile = 'Invalid mobile number';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'mobile');
						}

						break;

					case 'user_name' :

						if (!this.warehouse.user_name) {
							this.errors.user_name = 'Username is required';
						}
						else if (!this.warehouse.user_name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.user_name = 'No special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'user_name');
						}

						break;

					case 'current_password' :

						if (!this.password.current_password) {
							this.errors.current_password = 'Current Password is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'current_password');
						}

						break;

					case 'password' :

						if (!this.password.password) {
							this.errors.password = 'Password is required';
						}
						else if (this.password.password && this.password.password.length < 8) {
							this.errors.password = 'Minimum length should be 8';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'password');
						}

						break;

					case 'password_confirmation' :

						if (this.password.password && !this.password.password_confirmation) {
							this.errors.password_confirmation = 'Password is required';
						}
						else if (this.password.password && this.password.password !== this.password.password_confirmation) {
							this.errors.password_confirmation = "Password doesn't match";
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'password_confirmation');
						}

						break;

					case 'owner' :

						if (Object.keys(this.warehouse.owner).length === 0 || !this.warehouse.warehouse_owner_id) {
							this.errors.owner = 'Owner is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'owner');
						}

						break;

					case 'deal' :

						if (!this.warehouse.warehouse_deal) {
							this.errors.deal = 'Deal detail is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'deal');
						}

						break;

					case 'warehouse_preview' :

						if (!this.warehouse.previews.length) {
							this.errors.preview = 'Preview is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'preview');
						}

						break;

					case 'warehouse_feature' :

						if (!this.warehouse.feature.features) {
							this.errors.feature = 'Warehouse feature is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'feature');
						}

						break;

					case 'storage_type' :

						for (let index = 0; index < this.warehouse.storages.length; index++) {
							
							if (Object.keys(this.warehouse.storages[index].storage_type).length === 0) {
								this.errors.storage_types[index] = 'Storage type is required';
							}
							else{
								this.submitForm = true;
								this.errors.storage_types[index] = null;
								// this.$delete(this.errors, 'storage_feature');
							}

						}

						break;

					case 'storage_preview' :

						for (let index = 0; index < this.warehouse.storages.length; index++) {
							
							if (!this.warehouse.storages[index].previews || !this.warehouse.storages[index].previews.length) {
								this.errors.storage_preview = 'Storage preview is required';
							}
							else{
								this.submitForm = true;
								this.$delete(this.errors, 'storage_preview');
							}

						}

						break;

					case 'storage_feature' :

						for (let index = 0; index < this.warehouse.storages.length; index++) {
							
							if (!this.warehouse.storages[index].feature.features) {
								this.errors.storage_features[index] = 'Storage feature is required';
							}
							else{
								this.submitForm = true;
								this.errors.storage_features[index] = null;
								// this.$delete(this.errors, 'storage_feature');
							}

						}

						break;

					case 'container' :

						for (let index = 0; index < this.warehouse.containers.length; index++) {
							
							if (Object.keys(this.warehouse.containers[index].container).length < 2) {
								this.errors.containers[index].container = 'Container type is required';
							}
							else{
								this.submitForm = true;
								this.$delete(this.errors.containers[index], 'container');
							}

						}

						break;

					case 'container_quantity' :

						for (let index = 0; index < this.warehouse.containers.length; index++) {

							if (!this.warehouse.containers[index].quantity || this.warehouse.containers[index].quantity < 1) {
								this.errors.containers[index].container_quantity = 'Container quantity is required';
							}
							else{
								this.submitForm = true;
								this.$delete(this.errors.containers[index], 'container_quantity');
							}

						}

						break;

					case 'container_price' :

						this.warehouse.containers.forEach(
							(container, index) => {

								if (Object.keys(container.rents).length === 0) {
									this.errors.containers[index].container_price = 'One Price is required';
								}

								else {

									this.$delete(this.errors.containers[index], 'container_price');

									this.allRentPeriods.forEach(
										rentPeriod => {
											if ((container.rents['container_storing_price_' + rentPeriod.name] && !container.rents['container_selling_price_' + rentPeriod.name]) || (!container.rents['container_storing_price_' + rentPeriod.name] && container.rents['container_selling_price_' + rentPeriod.name])) {

												this.submitForm = false;

												if (container.rents['container_storing_price_' + rentPeriod.name]) {

													this.errors.containers[index]['container_selling_price_' + rentPeriod.name] = 'Both field to be filled';

												}
												else {

													this.errors.containers[index]['container_storing_price_' + rentPeriod.name] = 'Both field to be filled';

												}


											}
											else {
												this.submitForm = true;

												this.$delete(this.errors.containers[index], 'container_selling_price_'+ rentPeriod.name);
												this.$delete(this.errors.containers[index], 'container_storing_price_'+ rentPeriod.name);

												// this.errors.containers[index]['container_selling_price_' + rentPeriod.name] = null;
												// this.errors.containers[index]['container_storing_price_' + rentPeriod.name] = null;
											}
										}
									);

								}

							}
						);

						break;

					case 'shelf_price' :

						this.warehouse.containers.forEach(
							(container, index) => {

								this.allRentPeriods.forEach(
									rentPeriod => {
										if ((container.rents['shelf_storing_price_' + rentPeriod.name] && !container.rents['shelf_selling_price_' + rentPeriod.name]) || (!container.rents['shelf_storing_price_' + rentPeriod.name] && container.rents['shelf_selling_price_' + rentPeriod.name])) {

											this.submitForm = false;

											if (container.rents['shelf_storing_price_' + rentPeriod.name]) {

												this.errors.containers[index]['shelf_selling_price_' + rentPeriod.name] = 'Both field to be filled';

											}
											else {

												this.errors.containers[index]['shelf_storing_price_' + rentPeriod.name] = 'Both field to be filled';

											}


										}
										else {
											this.submitForm = true;

											this.$delete(this.errors.containers[index], 'shelf_selling_price_'+ rentPeriod.name);
											this.$delete(this.errors.containers[index], 'shelf_storing_price_'+ rentPeriod.name);

											// this.errors.containers[index]['shelf_selling_price_' + rentPeriod.name] = null;
											// this.errors.containers[index]['shelf_storing_price_' + rentPeriod.name] = null;
										}
									}
								);

							}
						);

						break;

					case 'unit_price' :

						this.warehouse.containers.forEach(
							(container, index) => {

								this.allRentPeriods.forEach(
									rentPeriod => {
										if ((container.rents['unit_storing_price_' + rentPeriod.name] && !container.rents['unit_selling_price_' + rentPeriod.name]) || (!container.rents['unit_storing_price_' + rentPeriod.name] && container.rents['unit_selling_price_' + rentPeriod.name])) {

											this.submitForm = false;

											if (container.rents['unit_storing_price_' + rentPeriod.name]) {

												this.errors.containers[index]['unit_selling_price_' + rentPeriod.name] = 'Both field to be filled';

											}
											else {

												this.errors.containers[index]['unit_storing_price_' + rentPeriod.name] = 'Both field to be filled';

											}

										}
										else {
											this.submitForm = true;

											this.$delete(this.errors.containers[index], 'unit_selling_price_'+ rentPeriod.name);
											this.$delete(this.errors.containers[index], 'unit_storing_price_'+ rentPeriod.name);

											// this.errors.containers[index]['unit_selling_price_' + rentPeriod.name] = null;
											// this.errors.containers[index]['unit_storing_price_' + rentPeriod.name] = null;
										}
									}
								);

							}
						);

						break;

				}
	 
			},
		}
  	}

</script>

<style scoped>
	@import '~vue-multiselect/dist/vue-multiselect.min.css';
</style>