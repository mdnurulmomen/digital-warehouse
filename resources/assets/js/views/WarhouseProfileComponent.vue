
<template>

	<div class="pcoded-content">

		<div class="page-header card">
			<div class="row align-items-end">
				<div class="col-lg-8">
					<div class="page-header-title">
						<i class="feather icon-warhouse bg-c-blue"></i>
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
						      					<a class="nav-link " data-toggle="tab" href="#warhouseFeatureAndPreviews" role="tab" aria-selected="true">
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

							      					<div class="row" v-if="warhouse">
							      					 
							      						<div class="col-sm-3">
															<div class="card">
												              	<div class="card-body box-profile">
												                	<div class="text-center">
												                  		<img class="profile-warhouse-img img-fluid img-circle" :src="warhouse.site_map_preview || ''" alt="User Picture">
												                	</div>

												                	<h3 class="profile-username text-center">
												                		{{ warhouse.user_name }}
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
																	                  v-model="warhouse.name" 
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
																	                  	v-model="warhouse.code" 
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
																                  		v-model="warhouse.email" 
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
																	                  	v-model="warhouse.mobile" 
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
																	                  	v-model="warhouse.user_name" 
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

						      				<!-- warhouse deal -->
						      				<div class="tab-pane" id="deal" role="tabpanel">
						      					
						      					<form class="form-horizontal" v-on:submit.prevent="warehouseDealUpdation">
				      		
										      		<input type="hidden" name="_token" :value="csrf">
										            
										            <div class="card">
										            	
											            <div 
												            class="card-body box-profile" 
												            v-if="warhouse.owner"
											            >  

											              	<div class="form-group row">	
							                              		<label class="col-sm-3 col-form-label text-right">
							                              			Owner
							                              		</label>	
							                              		<div class="col-md-9">
						                              				<multiselect 
							                                  			v-model="warhouse.owner"
							                                  			placeholder="Warhouse Owner" 
								                                  		label="user_name" 
								                                  		track-by="id" 
								                                  		:options="allWarhouseOwners" 
								                                  		:required="true" 
								                                  		:class="!errors.owner  ? 'is-valid' : 'is-invalid'"
								                                  		:allow-empty="false"
								                                  		selectLabel = "Press/Click"
								                                  		deselect-label="Can't remove single value"
								                                  		@close="validateFormInput('owner')" 
								                                  		@input="setWarhouseOwner()"
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
																	Warhouse Deal (rental/contractual)
																</label>
									                          	<div class="col-md-9">
									                              	<ckeditor 
										                              	class="form-control" 
										                              	:editor="editor" 
										                              	v-model="warhouse.warhouse_deal"
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
						      				<div class="tab-pane" id="warhouseFeatureAndPreviews" role="tabpanel">
						      					
						      					<form 
						      						class="form-horizontal" 
						      						v-on:submit.prevent="warhouseFeatureAndPreviewsUpdation"
						      					>
				      		
										      		<input type="hidden" name="_token" :value="csrf">

							                    	<div class="card">

												    	<div class="card-body box-profile">

												    		<div 
													    		class="form-group row" 
													    		v-if="warhouse.previews && warhouse.previews.length"
												    		>
																<div 
																	class="col-sm-4"
																	v-for="(warhousePreview, index) in warhouse.previews" 
																	:key="'D' + index + warhousePreview.id"
																>
																	<button 
																		type="button" 
																		class="close position-absolute text-danger" 
																		style="right:0;z-index:99;" 
																		@click="removeWarhousePreview(index)"
																	>
																		<i class="fa fa-times-circle" aria-hidden="true"></i>
																	</button>
																	<img 
																		:src="warhousePreview.preview || ''" 
																		class="img-fluid position-relative w-100 h-100" alt="warhouse preview" 
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
																    	 	@change="onWarhousePreviewChange" 
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
																	Warhouse Features
																</label>
																<div class="col-sm-9">
									                              	<ckeditor 
										                              	class="form-control" 
										                              	:editor="editor" 
										                              	v-model="warhouse.feature.features"
										                              	:class="!errors.feature  ? 'is-valid' : 'is-invalid'"
										                              	@blur="validateFormInput('warhouse_feature')"
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
													    	v-for="(warhouseStorageType, index) in warhouse.storages" 
					                              			:key="'E' + index + warhouseStorageType.id"
				                              			>

												    		<div class="form-group row">
												    			
											    				<label class="col-sm-3 col-form-label text-right">
											    					Storage Type
											    				</label>

											    				<div class="col-sm-9">
												    				<multiselect 
							                                  			v-model="warhouse.storages[index].storage_type"
							                                  			placeholder="Storage Type" 
								                                  		label="name" 
								                                  		track-by="id" 
								                                  		:options="allStorageTypes" 
								                                  		:required="true" 
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
												    			v-if="warhouse.storages[index].previews && warhouse.storages[index].previews.length"
												    		>
																<div 
																	class="form-group col-sm-4" 
																	v-for="(warhouseStoragePreview, index2) in warhouse.storages[index].previews" 
																	:key="index + index2 + warhouseStoragePreview.preview"
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
																		:src="warhouseStoragePreview.preview || ''" 
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
																v-if="warhouse.storages[index].feature && warhouse.storages.length==errors.storage_features.length"
															>
																	
																<label class="col-sm-3 col-form-label text-right">
																	Features
																</label>

																<div class="col-sm-9">
																	
																	<ckeditor 
										                              	class="form-control" 
										                              	:editor="editor" 
										                              	v-model="warhouse.storages[index].feature.features"
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
								                    	v-if="warhouse.containers.length==errors.containers.length"
							                    	>

												    	<div 
												    		class="card-body box-profile" 
												    		v-for="(warhouseContainer, index) in warhouse.containers" 
				                              				:key="'F' + index + warhouseContainer.id"
												    	>
												    		
												    		<div class="form-row">
												    			<div class="form-group col-sm-6">
												    				<label>Container Type</label>
												    				<multiselect 
							                                  			v-model="warhouse.containers[index].container"
							                                  			placeholder="Container Type" 
								                                  		label="name" 
								                                  		track-by="id" 
								                                  		:options="allContainers" 
								                                  		:required="true" 
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
																		v-model.number="warhouse.containers[index].quantity" 
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

												    		<div 
												    			class="form-row"
												    			v-if="warhouse.containers.length==errors.containers.length"
												    		>	
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
																								v-model.number="warhouse.containers[index].rents['container_storing_price_' + rentPeriod.name]" 
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
																								v-model.number="warhouse.containers[index].rents['container_selling_price_' + rentPeriod.name]" 
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
																    				v-show="warhouse.containers[index].container.has_shelve"
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
																								v-model.number="warhouse.containers[index].rents['shelf_storing_price_' + rentPeriod.name]" 
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
																								v-model.number="warhouse.containers[index].rents['shelf_selling_price_' + rentPeriod.name]" 
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
																    				v-show="warhouse.containers[index].container.has_shelve && warhouse.containers[index].container.shelf.has_units"
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
																								v-model.number="warhouse.containers[index].rents['unit_storing_price_' + rentPeriod.name]" 
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
																								v-model.number="warhouse.containers[index].rents['unit_selling_price_' + rentPeriod.name]" 
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

						      				<!-- warhouse password -->
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

	        	warhouse : {

					owner : {},
			    	previews : [],

			    	feature : {
			    		features : ''
			    	},
			    	
			    	storages : [
			    		{  	// warhouse_storage_types
							previews : [],
							storage_type : {

							},
							feature : {
								features : ''
							},
						},
			    	],

			    	containers : [
						{	/*warhouse_containers*/	
							
							// quantity : '',
							// container_id : ''
							// engaged_quantity : '',
							container : {	 // containers	
								// name : 0,
								// has_shelve : 0,
								shelf : { 	// warhouse_container_shelfs
									// has_units : true,
									// quantity : 0,  // total number warhouse_container_shelfs
									unit :  {    // warhouse_container_shelf_units
										// quantity : 0,  // length of warhouse_container_shelf_units	
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
	        	allWarhouseOwners : [],

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
			this.fetchWarhouseData();
			this.fetchAllWarhouseOwners();
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

			fetchWarhouseData() {
				
				this.error = '';
				this.loading = true;

				axios
					.get('/api/profile')
					.then(response => {
						// handle success
						this.warhouse = response.data.data || {};

						this.errors = {
		        			storage_features : [],
		        			storage_types : [],
		        			containers : [],
			        	};

			        	this.warhouse.containers.forEach(
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
			fetchAllWarhouseOwners() {
				
				this.error = '';
				this.loading = true;
				this.allWarhouseOwners = [];
				
				axios
					.get('/api/owners/')
					.then(response => {
						if (response.status == 200) {
							this.allWarhouseOwners = response.data;
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
					.put('/warhouse-profile/', this.warhouse)
					.then(response => {
						if (response.status == 200) {
							this.warhouse = response.data.data || {};
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
					.put('/warhouse-deal/', this.warhouse)
					.then(response => {
						if (response.status == 200) {
							this.warhouse = response.data.data || {};
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
			onWarhousePreviewChange(evnt) {
				let files = evnt.target.files || evnt.dataTransfer.files;

                // Only process image files.
		      	if (files.length && files[0].type.match('image.*')) {
                	this.submitForm = true;
                	this.$delete(this.errors, 'preview');
                	this.createImage(files[0], 'warhouse_preview');
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

                	if (previewName === 'warhouse_preview') {

      					var warhouse_preview = {};
						warhouse_preview['preview'] = evnt.target.result;

                		this.warhouse.previews.push(warhouse_preview); 
                	}
                	else if (previewName === 'storage_preview') {

                		var warhouse_storage_preview = {};
						warhouse_storage_preview['preview'] = evnt.target.result;

						if (!this.warhouse.storages[index].previews) {
							this.warhouse.storages[index].previews = [];
						}

                		this.warhouse.storages[index].previews.push(warhouse_storage_preview);
                	}
                	else {

                    	this.warhouse.site_map_preview = evnt.target.result;
                	}
                };

                reader.readAsDataURL(file);
            },
            removeWarhousePreview(index) {
            	this.warhouse.previews.splice(index, 1);
            },
            removeStoragePreview(storageIndex, previewIndex) {
            	this.warhouse.storages[storageIndex].previews.splice(previewIndex, 1);
            },
			getOwnerFullName(owner) {
				if (!owner.first_name && !owner.last_name) {
					return 'NA';
				}

				return owner.first_name || '' + ' ' + owner.last_name || '';
			},
			addStorage() {
				let warhouseNewStorage = {
					previews : [],
					storage_type : {},
					feature : {
						features : ''
					},
				};

				this.warhouse.storages.push(warhouseNewStorage);
			},
			removeStorage() {
				if (this.warhouse.storages.length > 1) {

					this.warhouse.storages.pop();
					this.errors.storage_types.pop();
					this.errors.storage_features.pop();

					// this.warhouse.storages.pop(this.warhouse.storages[this.warhouse.storages.length-1]);
					// this.errors.storage_types[this.warhouse.storages.length-1] = null;
					// this.errors.storage_features[this.warhouse.storages.length-1] = null;
				}
			},
			addContainer() {
				let warhouseNewContainer = {
					// quantity : 0,
					// container_id : 0,
					container : {},
					quantity : 1,
					rents : {}

				};

				this.errors.containers.push({});
				this.warhouse.containers.push(warhouseNewContainer);
			},
			removeContainer() {
				if (this.warhouse.containers.length > 1) {
					this.warhouse.containers.pop();
					this.errors.containers.pop();
				}
			},
			changeContainerRents(index) {
				this.warhouse.containers[index].rents = {};
				this.errors.containers[index] = {};
			},
			setWarhouseOwner() {
				if (Object.keys(this.warhouse.owner).length > 0) {
					this.warhouse.warhouse_owner_id = this.warhouse.owner.id;
				}
			},
            validateFormInput (formInputName) {
				
				this.submitForm = false;

				switch(formInputName) {

					case 'site_map_preview' :

						if (!this.warhouse.site_map_preview) {
							this.errors.site_map_preview = 'Site map is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'site_map_preview');
						}

						break;

					case 'name' :

						if (this.warhouse.name && !this.warhouse.name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.name = 'No special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'name');
						}

						break;

					case 'code' :

						if (!this.warhouse.code) {
							this.errors.code = 'Code is required';
						}
						else if (!this.warhouse.code.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.code = 'No special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'code');
						}

						break;

					case 'email' :

						if (!this.warhouse.email) {
							this.errors.email = 'Email is required';
						}
						else if (!this.warhouse.email.match(/[a-z0-9._+-]+@[a-z0-9.-]+\.[a-z]{2,}$/g)) {
							this.errors.email = 'Invalid email address';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'email');
						}

						break;

					case 'mobile' :

						if (!this.warhouse.mobile) {
							this.errors.mobile = 'Mobile is required';
						}
						else if (!this.warhouse.mobile.match(/\+?(88)?0?1[123456789][0-9]{8}\b/g)) {
							this.errors.mobile = 'Invalid mobile number';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'mobile');
						}

						break;

					case 'user_name' :

						if (!this.warhouse.user_name) {
							this.errors.user_name = 'Username is required';
						}
						else if (!this.warhouse.user_name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
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

						if (Object.keys(this.warhouse.owner).length === 0 || !this.warhouse.warhouse_owner_id) {
							this.errors.owner = 'Owner is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'owner');
						}

						break;

					case 'deal' :

						if (!this.warhouse.warhouse_deal) {
							this.errors.deal = 'Deal detail is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'deal');
						}

						break;

					case 'warhouse_preview' :

						if (!this.warhouse.previews.length) {
							this.errors.preview = 'Preview is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'preview');
						}

						break;

					case 'warhouse_feature' :

						if (!this.warhouse.feature.features) {
							this.errors.feature = 'Warhouse feature is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'feature');
						}

						break;

					case 'storage_type' :

						for (let index = 0; index < this.warhouse.storages.length; index++) {
							
							if (Object.keys(this.warhouse.storages[index].storage_type).length === 0) {
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

						for (let index = 0; index < this.warhouse.storages.length; index++) {
							
							if (!this.warhouse.storages[index].previews || !this.warhouse.storages[index].previews.length) {
								this.errors.storage_preview = 'Storage preview is required';
							}
							else{
								this.submitForm = true;
								this.$delete(this.errors, 'storage_preview');
							}

						}

						break;

					case 'storage_feature' :

						for (let index = 0; index < this.warhouse.storages.length; index++) {
							
							if (!this.warhouse.storages[index].feature.features) {
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

						for (let index = 0; index < this.warhouse.containers.length; index++) {
							
							if (Object.keys(this.warhouse.containers[index].container).length < 2) {
								this.errors.containers[index].container = 'Container type is required';
							}
							else{
								this.submitForm = true;
								this.$delete(this.errors.containers[index], 'container');
							}

						}

						break;

					case 'container_quantity' :

						for (let index = 0; index < this.warhouse.containers.length; index++) {

							if (!this.warhouse.containers[index].quantity || this.warhouse.containers[index].quantity < 1) {
								this.errors.containers[index].container_quantity = 'Container quantity is required';
							}
							else{
								this.submitForm = true;
								this.$delete(this.errors.containers[index], 'container_quantity');
							}

						}

						break;

					case 'container_price' :

						this.warhouse.containers.forEach(
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

						this.warhouse.containers.forEach(
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

						this.warhouse.containers.forEach(
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