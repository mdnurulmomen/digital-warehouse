<template>
	<!-- Modal -->
	<div class="modal fade" id="user-createOrEdit-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">
						{{ createMode ? 'Create' : 'Edit' }} {{ user | capitalize }}
						<span v-show="!createMode">
							({{ singleUserDetails.user_name }})
						</span>
					</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
					
				<form 	
					class="form-horizontal" 
					v-on:submit.prevent="submitUserForm" 
					autocomplete="off" 
				>
					<input type="hidden" name="_token" :value="csrf">

					<div class="modal-body">
						<transition-group name="fade">
							<div 
								class="row" 
								v-bind:key="'user-modal-step-' + 1" 
								v-show="step==1"
							>
								<h2 class="mx-auto mb-4 lead">User Profile</h2>

								<div class="col-md-12">
									<div class="card">
								    	<div class="card-body">
								    		<div class="form-row d-flex align-items-center text-center">
												<div class="form-group col-md-6">
													<img class="profile-user-img img-fluid img-circle" 
														:src="singleUserDetails.profile_preview.preview || ''"
														alt="User Picture" 
													>
												</div>
												<div class="form-group col-md-6">
													<div class="custom-file">
													    <input type="file" 
													    	class="form-control custom-file-input" 
															:class="!errors.profile_preview  ? 'is-valid' : 'is-invalid'" 
												    	 	@change="onImageChange" 
												    	 	accept="image/*"
													    >
													    <label class="custom-file-label" for="validatedCustomFile">Choose Picture...</label>
													    <div class="invalid-feedback">
													    	{{ errors.profile_preview }}
													    </div>
												  	</div>
												</div>
											</div>
											<div class="form-row">
												<div class="form-group col-md-12 text-right">
													<toggle-button 
														v-model="singleUserDetails.active" 
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
										<div class="form-group col-md-6">
											<label for="inputFirstName">First Name</label>
											<input type="text" 
												class="form-control" 
												v-model="singleUserDetails.first_name" 
												placeholder="First Name" 
												:class="!errors.first_name  ? 'is-valid' : 'is-invalid'" 
												@change="validateFormInput('first_name')"
											>

											<div class="invalid-feedback">
									        	{{ errors.first_name }}
									  		</div>
										</div>
										<div class="form-group col-md-6">
											<label for="inputLastName">Last Name</label>
											<input type="text" 
												class="form-control" 
												v-model="singleUserDetails.last_name" 
												placeholder="Last Name" 
												:class="!errors.last_name  ? 'is-valid' : 'is-invalid'" 
												@change="validateFormInput('last_name')"
											>

											<div class="invalid-feedback">
									        	{{ errors.last_name }}
									  		</div>
										</div>
									</div>

									<div class="form-row">
										<div class="form-group col-md-6">
											<label for="inputEmail4">Email</label>
											<input type="email" 
												class="form-control" 
												v-model="singleUserDetails.email" 
												placeholder="First Name" 
												required="true" 
												:class="!errors.email  ? 'is-valid' : 'is-invalid'" 
												@change="validateFormInput('email')"
											>

											<div class="invalid-feedback">
									        	{{ errors.email }}
									  		</div>
										</div>
										<div class="form-group col-md-6">
											<label for="inputMobile4">Mobile</label>
											<input type="text" 
												class="form-control" 
												v-model="singleUserDetails.mobile" 
												placeholder="Mobile Number" 
												required="true" 
												:class="!errors.mobile  ? 'is-valid' : 'is-invalid'" 
												@change="validateFormInput('mobile')"
											>

											<div class="invalid-feedback">
									        	{{ errors.mobile }}
									  		</div>
										</div>
									</div>

									<div class="form-row">
										<div class="form-group col-md-12">
											<label for="inputUsername">Username</label>
											<input type="text" 
												class="form-control" 
												v-model="singleUserDetails.user_name" 
												placeholder="Username" 
												required="true" 
												:class="!errors.user_name  ? 'is-valid' : 'is-invalid'" 
												@change="validateFormInput('user_name')"
											>

											<div class="invalid-feedback">
									        	{{ errors.user_name }}
									  		</div>
										</div>
									</div>

									<div class="form-row">
										<div class="form-group col-sm-12 text-right">
											<a data-toggle="collapse" href="#user-password">
												<i class="fa fa-2x" :class="createMode ? 'fa-angle-up' : 'fa-angle-down'" data-toggle="tooltip" data-placement="top" title="Password" @click="changeArrow"></i>
											</a>
										</div>
									</div>

									<div id="user-password" class="panel-collapse collapse" :class="createMode ? 'show' : ''">
										<div class="form-row">
											<div class="form-group col-md-6">
												<label for="inputPassword4">Password</label>
												<input type="password" 
													class="form-control" 
													v-model="singleUserDetails.password" 
													minlength="8" 
													placeholder="Password" 
													:required="createMode ? true : false" 
													:class="!errors.password  ? 'is-valid' : 'is-invalid'" 
													@change="validateFormInput('password')" 
													autocomplete="new-password"
												>

												<div class="invalid-feedback">
										        	{{ errors.password }}
										  		</div>
											</div>
											<div class="form-group col-md-6">
												<label for="inputPassword4">Confirm Password</label>
												<input type="password" 
													class="form-control" 
													v-model="singleUserDetails.password_confirmation" 
													placeholder="Confirm Password" 
													:required="createMode ? true : false" 
													:class="!errors.password_confirmation  ? 'is-valid' : 'is-invalid'" 
													@change="validateFormInput('password_confirmation')"
												>

												<div class="invalid-feedback">
										        	{{ errors.password_confirmation }}
										  		</div>
											</div>
										</div>
									</div>
								</div>

								<!-- 
								<div class="col-sm-12 form-group mb-0 text-right card-footer">
					          		<div class="text-danger small mb-1" v-show="!submitForm">
								  		Please input required fields
						          	</div>
						          	
						          	<button type="button" class="btn btn-outline-secondary btn-sm btn-round" data-toggle="tooltip" data-placement="top" title="Next" v-on:click="nextPage">
				                    	<i class="fa fa-2x fa-angle-double-right" aria-hidden="true"></i>
				                  	</button>
					          	</div> 
					          	-->

					          	<div class="col-sm-12 form-group mb-0 card-footer" v-if="$route.name != 'merchants'">
									<div class="flex-column">
										<div class="col-sm-12 text-right" v-show="!submitForm">
											<span class="text-danger small">
										  		Please input required fields
										  	</span>
										</div>
										<div class="col-sm-12">
						                  	<button type="button" class="btn waves-effect waves-dark btn-secondary btn-outline-secondary" data-toggle="tooltip" data-placement="top" title="Close Modal" data-dismiss="modal">Close</button>

											<button type="submit" class="btn waves-effect waves-dark btn-primary btn-outline-primary float-right" :disabled="! submitForm || formSubmitted">
												{{ createMode ? 'Save' : 'Update' }}
											</button>
										</div>
									</div>
					          	</div>

					          	<div class="col-sm-12 form-group mb-0 card-footer" v-else>
									<div class="row">
										<div class="col-sm-12 text-right" v-show="!submitForm">
											<span class="text-danger small">
										  		Please input required fields
										  	</span>
										</div>
										<div class="col-sm-12">
						                  	<button type="button" class="btn waves-effect waves-dark btn-secondary btn-outline-secondary" data-toggle="tooltip" data-placement="top" title="Close Modal" data-dismiss="modal">Close</button>

											<button type="button" class="btn waves-effect waves-dark btn-secondary btn-outline-secondary btn-sm btn-round float-right" data-toggle="tooltip" data-placement="top" title="Next" v-on:click="nextPage">
						                    	<i class="fa fa-2x fa-angle-double-right" aria-hidden="true"></i>
						                  	</button>
										</div>
									</div>
					          	</div>
							</div>

							 
							<div 
								class="row" 
								v-if="$route.name == 'merchants' && singleUserDetails.hasOwnProperty('support_deal')"
								v-bind:key="'user-modal-step-' + 2" 
								v-show="step==2"
							>
								<h2 class="mx-auto mb-4 lead">Support Deals</h2>

								<div class="col-md-12">
									<div class="form-row">
										<div class="form-group col-md-12">
											<label for="inputUsername">Sale Percentage</label>
											<div class="input-group mb-1">
												<input type="number" 
													class="form-control" 
													v-model.number="singleUserDetails.support_deal.sale_percentage" 
													placeholder="Sale Percentage" 
													:class="! errors.sale_percentage ? 'is-valid' : 'is-invalid'" 
													@change="validateFormInput('sale_percentage')" 
													:min="0" 
													:max="100" 
												>
												<div class="input-group-append">
													<span class="input-group-text"> % </span>
												</div>
											</div>
											<div 
												class="invalid-feedback" 
												style="display:block" 
												v-show="errors.sale_percentage"
											>
										    	{{ errors.sale_percentage }}
										    </div>
										</div>
									</div>

									<div class="form-row">
										<div class="form-group col-md-12">
											<label for="inputUsername">Rent Period</label>
											<multiselect 
												v-model="singleUserDetails.support_deal.rent_period" 
												class="form-control p-0" 
												:class="errors.rent_period ? 'is-invalid' : 'is-valid'"
												placeholder="Rent Period" 
												:custom-label="objectNameWithCapitalized" 
												:options="allRentPeriods" 
												:required="true" 
												:allow-empty="false" 
												@input="setRentPeriod()" 
											>
											</multiselect>
											<div 
												class="invalid-feedback" 
												style="display:block" 
												v-show="errors.rent_period"
											>
										    	{{ errors.rent_period }}
										    </div>
										</div>
									</div>

									<div class="form-row">
										<label class="col-6 col-form-label font-weight-bold">
											E-Commerce Fullfillment :
										</label>
										<div class="col-6 col-form-label">
											<toggle-button 
												v-model="singleUserDetails.support_deal.e_commerce_fulfillment_support" 
												:width=150 
												:sync="true"
												:color="{checked: '#229bbf', unchecked: '#6c757d'}"
												:labels="{checked: 'Enabled', unchecked: 'Disabled'}"  
											/>
										</div>
									</div>

									<div class="form-row">
										<div class="form-group col-md-12">
											<label for="inputUsername">Fulfilment Charge</label>
											<div class="input-group mb-1">
												<input type="number" 
													class="form-control" 
													v-model.number="singleUserDetails.support_deal.e_commerce_fulfillment_charge" 
													placeholder="Fulfilment Charge" 
													:min="0" 
													:max="100" 
													:class="! errors.fulfillment_charge ? 'is-valid' : 'is-invalid'" 
													:disabled="! singleUserDetails.support_deal.e_commerce_fulfillment_support"
													@change="validateFormInput('fulfillment_charge')" 
												>
												<div class="input-group-append">
													<span class="input-group-text"> 
														{{ general_settings.official_currency_name | capitalize }}
													</span>
												</div>
											</div>
											<div 
												class="invalid-feedback" 
												style="display:block" 
												v-show="errors.fulfillment_charge"
											>
										    	{{ errors.fulfillment_charge }}
										    </div>
										</div>
									</div>

									<div class="form-row">
										<label class="col-6 col-form-label font-weight-bold">
											Purchase Support :
										</label>
										<div class="col-6 col-form-label">
											<toggle-button 
												v-model="singleUserDetails.support_deal.purchase_support" 
												:width=150 
												:sync="true"
												:color="{checked: '#229bbf', unchecked: '#6c757d'}"
												:labels="{checked: 'Enabled', unchecked: 'Disabled'}"  
											/>
										</div>
									</div>

									<div class="form-row">
										<div class="form-group col-md-12">
											<label for="inputUsername">Purchase Support Charge</label>
											<div class="input-group mb-1">
												<input type="number" 
													class="form-control" 
													v-model.number="singleUserDetails.support_deal.purchase_support_charge" 
													placeholder="Purchase Suppoprt Charge" 
													:class="! errors.purchase_support_charge ? 'is-valid' : 'is-invalid'" 
													:disabled="! singleUserDetails.support_deal.purchase_support"
													@change="validateFormInput('purchase_support_charge')" 
													:min="0" 
													:max="100" 
												>
												<div class="input-group-append">
													<span class="input-group-text"> 
														{{ general_settings.official_currency_name | capitalize }}
													</span>
												</div>
											</div>
											<div 
												class="invalid-feedback" 
												style="display:block" 
												v-show="errors.purchase_support_charge"
											>
										    	{{ errors.purchase_support_charge }}
										    </div>
										</div>
									</div>

									<div class="form-row">
										<label class="col-6 col-form-label font-weight-bold">
											POS Support :
										</label>
										<div class="col-6 col-form-label">
											<toggle-button 
												v-model="singleUserDetails.support_deal.pos_support" 
												:width=150 
												:sync="true"
												:color="{checked: '#229bbf', unchecked: '#6c757d'}"
												:labels="{checked: 'Enabled', unchecked: 'Disabled'}"  
											/>
										</div>
									</div>

									<div class="form-row">
										<div class="form-group col-md-12">
											<label for="inputUsername">POS Support Charge</label>
											<div class="input-group mb-1">
												<input type="number" 
													class="form-control" 
													v-model.number="singleUserDetails.support_deal.pos_support_charge" 
													placeholder="POS Suppoprt Charge" 
													:class="! errors.pos_support_charge ? 'is-valid' : 'is-invalid'" 
													:disabled="! singleUserDetails.support_deal.pos_support"
													@change="validateFormInput('pos_support_charge')" 
													:min="0" 
													:max="100" 
												>
												<div class="input-group-append">
													<span class="input-group-text"> 
														{{ general_settings.official_currency_name | capitalize }}
													</span>
												</div>
											</div>
											<div 
												class="invalid-feedback" 
												style="display:block" 
												v-show="errors.pos_support_charge"
											>
										    	{{ errors.pos_support_charge }}
										    </div>
										</div>
									</div>

									<div class="form-row">
										<div class="form-group col-md-12">
											<label for="inputUsername"># Outlets</label>
											<input 
												type="number" 
												class="form-control" 
												v-model="singleUserDetails.support_deal.number_outlets" 
												placeholder="Number Outlets" 
												:class="! errors.number_outlets  ? 'is-valid' : 'is-invalid'" 
												@change="validateFormInput('number_outlets')" 
												:disabled="! singleUserDetails.support_deal.pos_support" 
												min="0" 
											>
											<div class="invalid-feedback">
									        	{{ errors.number_outlets }}
									  		</div>
										</div>
									</div>

									<!-- 
									<div class="form-row" v-show="allRoles.length">
										<div class="form-group col-md-12">
											<label for="inputUsername">Role</label>
											<multiselect 
			                          			v-model="singleUserDetails.roles" 
			                              		label="name" 
			                              		track-by="id" 
			                              		:custom-label="objectNameWithCapitalized" 
			                              		:options="allRoles" 
			                              		:multiple="true" 
			                              		:close-on-select="false" 
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
												<div 
													class="col-md-6" 
													v-for="model in modelsCRUDable" 
													:key="'crud-model-permission-name-' + model"
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
														<label>{{ modelName('update-' + model) }}</label>
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
												</div>
	
												<div 
													class="col-md-6" 
													v-for="model in modelsCreateableUpdatableAndDeletable" 
													:key="'crud-model-permission-name-' + model"
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
														<label>{{ modelName('update-' + model) }}</label>
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
												</div>

												<div 
													class="col-md-6" 
													v-for="model in modelsViewableAndUpdatable" 
													:key="'view-update-model-permission-name-' + model"
												>
													<p class="font-weight-bold mt-4 mb-3">{{ modelName(model) }}</p>

													<div class="form-check">
														<input 
															type="checkbox" 
															:checked="permissionExists('update-' + model)" 
															@change="insertPermission('update-' + model, $event)" 
															:ref="'update-' + model.toLowerCase()"
														>
														<label>{{ modelName('update-' + model) }}</label>
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
												</div>

												<div 
													class="col-md-6" 
													v-for="model in modelsViewableRecommendableAndApproveable" 
													:key="'view-make-model-permission-name-' + model"
												>
													<p class="font-weight-bold mt-4 mb-3">{{ modelName(model) }}</p>

													<div class="form-check">
														<input 
															type="checkbox" 
															:checked="permissionExists('recommend-' + model)" 
															@change="insertPermission('recommend-' + model, $event)" 
															:ref="'recommend-' + model.toLowerCase()"
														>
														<label>{{ modelName('recommend-' + model) }}</label>
													</div>

													<div class="form-check">
														<input 
															type="checkbox" 
															:checked="permissionExists('approve-' + model)" 
															@change="insertPermission('approve-' + model, $event)" 
															:ref="'approve-' + model.toLowerCase()"
														>
														<label>{{ modelName('update/Approve-' + model) }}</label>
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
												</div>

												<div 
													class="col-md-6" 
													v-for="model in modelsViewable" 
													:key="'view-model-permission-name-' + model"
												>
													<p class="font-weight-bold mt-4 mb-3">{{ modelName(model) }}</p>

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

												<div 
													class="col-md-6" 
													v-for="model in modelsViewable2" 
													:key="'view2-model-permission-name-' + model"
												>
													<p class="font-weight-bold mt-4 mb-3">{{ modelName(model) }}</p>

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

												<div 
													class="col-md-6" 
													v-for="model in modelsViewableAndDeletable" 
													:key="'view-and-deletable-model-permission-name-' + model"
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
												</div>
											</div>
										</div>
									</div>

									<div class="form-row">
										<div class="form-group col-md-12" v-show="! allPermissions.length">
											<p class="text-center">You may not have permissions to select permissions</p>
										</div>
									</div> 
									-->
								</div>

								<div class="col-sm-12 form-group mb-0 card-footer">
									<div class="flex-column">
										<div class="col-sm-12 text-right" v-show="!submitForm">
											<span class="text-danger small">
										  		Please input required fields
										  	</span>
										</div>
										<div class="col-sm-12">
											<button type="button" class="btn btn-outline-secondary btn-sm btn-round" data-toggle="tooltip" data-placement="top" title="Previous" v-on:click="step--">
						                    	<i class="fa fa-2x fa-angle-double-left" aria-hidden="true"></i>
						                  	</button>
											<button type="submit" class="btn waves-effect waves-dark btn-primary btn-outline-primary float-right" :disabled="! submitForm || formSubmitted">
												{{ createMode ? 'Save' : 'Update' }}
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

</template>

<script type="text/javascript">

	import Multiselect from 'vue-multiselect';
	
	export default {

		components: { 
			multiselect : Multiselect,
		},

		props : {

			csrf : {
				type : String,
				required : true
			},
			createMode : {
				type : Boolean,
				required : true
			},
			user : {
				type : String,
				required : true
			},
			singleUserDetails : {
				type : Object,
				required : true
			},
			formSubmitted : {
				type : Boolean,
				default: false
			},

		},

		data() {

			return {

				step : 1,

				submitForm : true,

				allRentPeriods : [],
				
				errors : {
				
				},

				general_settings : JSON.parse(window.localStorage.getItem('general_settings')),

				// modelCRUDableAndApproveable : [
	                // 'Product-Stock',
	            // ],

				/*
				allRoles : [],
				allPermissions : [],
				
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

	            modelsViewableAndDeletable : [
	                'Mail',  // AppMail / view / delete
	            ],
	            */

			}

		},

		/*
		created(){

			if (this.userHasPermissionTo('view-role-index')) {

				this.fetchAllRoles();

			}

			if (this.userHasPermissionTo('view-permission-index')) {

				this.fetchAllPermissions();

			}
		
		},
		*/
		
		watch: {

			singleUserDetails: function(newUserDetail, oldUserDetail) { // watch it
				
				this.step = 1;
				
				// this.resetAllPermissions();
				// this.disableExistingRolePermissions();

			}

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

			/*
			fetchAllRoles() {
				
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
			*/
			fetchAllRentPeriods() {
				
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
						
					});

			},
			nextPage() {
				
				if (this.step==1) {
					
					this.validateFormInput('user_name');
					this.validateFormInput('mobile');
					this.validateFormInput('email');
					this.validateFormInput('password');
					this.validateFormInput('password_confirmation');
	            	
	            	if (Object.keys(this.errors).length !== 0 && this.errors.constructor === Object) {
						this.submitForm = false;
						return;
					}
					else {
						this.fetchAllRentPeriods();
						this.step++;
					}

				}

			},
			/*
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

				if (this.singleUserDetails.roles.length) {

					return this.singleUserDetails.roles.some(

						userRole => userRole.permissions.some(

							rolePermission => rolePermission.name == permissionName
							
						)

					);

				}

				return false;

			},
			userHasPermission(permissionName) {

				permissionName = permissionName.toLowerCase();

				if (this.singleUserDetails.permissions.length) {

					return this.singleUserDetails.permissions.some(
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

				if (event.target.checked && (!this.singleUserDetails.permissions.length || !this.permissionExists(permissionName))) {

					this.submitForm = true;
					let permission = this.getExpectedPermission(permissionName);

					if (permission) {
				   		
				   		this.singleUserDetails.permissions.push(permission);
						
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

					let uncheckedPermissionIndex = this.singleUserDetails.permissions.findIndex(
						permission => permission.name==permissionName
					);

					if (uncheckedPermissionIndex > -1) {
						this.singleUserDetails.permissions.splice(uncheckedPermissionIndex, 1);
					}

				}

			},
			checkAvailableCheckboxes() {

				this.singleUserDetails.permissions = [];

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

				if (this.singleUserDetails.roles.length) {

					this.singleUserDetails.roles.forEach(
						
						userRole => {
							
							for (var ref in this.$refs) {

								const samePermission = (permission) => permission.name == ref;

								if (userRole.permissions.some(samePermission)) {

									this.$refs[ref][0].disabled = true;

									if (this.singleUserDetails.permissions.some(userPermission=>userPermission.name == ref)) {

										this.singleUserDetails.permissions.splice(this.singleUserDetails.permissions.findIndex(userPermission=>userPermission.name == ref), 1);

									}

								}

							}

						}

					);

				}

			},*/
			onImageChange(evnt) {
				
				let files = evnt.target.files || evnt.dataTransfer.files;

                // Only process image files.
		      	if (files.length && files[0].type.match('image.*')) {
                	this.createImage(files[0]);
                	this.$delete(this.errors, 'profile_preview');
		      	}
		      	else{
		      		this.errors.profile_preview = 'File should be image';
		      	}

		      	evnt.target.value = '';
		      	return;

			},
			createImage(file) {

                let reader = new FileReader();
                reader.onload = (evnt) => {
                    this.singleUserDetails.profile_preview.preview = evnt.target.result;
                };
                reader.readAsDataURL(file);

            },
            submitUserForm() {

				if (this.$route.name=='merchants') {

					this.validateFormInput('sale_percentage');
					this.validateFormInput('fulfillment_charge');
					this.validateFormInput('purchase_support_charge');
					this.validateFormInput('pos_support_charge');
					this.validateFormInput('number_outlets');
					this.validateFormInput('rent_period');

				}
				else {

					this.validateFormInput('user_name');
					this.validateFormInput('mobile');
					this.validateFormInput('email');
					this.validateFormInput('password');
					this.validateFormInput('password_confirmation');

				}

            	
            	if (Object.keys(this.errors).length !== 0 && this.errors.constructor === Object) {
					this.submitForm = false;
					return;
				}
				else {

	            	if (this.createMode) {
	            		
	            		this.$emit('storeUser', this.singleUserDetails);
	            	}

	            	else {

	            		this.$emit('updateUser', this.singleUserDetails)
	            	}

				}

            },
            objectNameWithCapitalized ({ name }) {
		      	if (name) {
				    name = name.toString()
				    return name.charAt(0).toUpperCase() + name.slice(1)
		      	}
		      	else 
		      		return ''
		    },
		    setRentPeriod() {
		    	
		    	if (this.singleUserDetails.support_deal.rent_period) {

		    		this.singleUserDetails.support_deal.rent_period_id = this.singleUserDetails.support_deal.rent_period.id;

		    	}

		    },
		    /*
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
			*/
			validateFormInput (formInputName) {
				
				this.submitForm = false;

				switch(formInputName) {

					case 'first_name' :

						if (!this.singleUserDetails.first_name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.first_name = 'No special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'first_name');
						}

						break;

					case 'last_name' :

						if (!this.singleUserDetails.last_name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.last_name = 'No special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'last_name');
						}

						break;

					case 'user_name' :

						if (!this.singleUserDetails.user_name) {
							this.errors.user_name = 'Username is required';
						}
						else if (!this.singleUserDetails.user_name.match(/^[-\w\.\$@\*\!]{3,30}$/g)) {
							this.errors.user_name = 'No special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'user_name');
						}

						break;

					case 'mobile' :

						if (!this.singleUserDetails.mobile) {
							this.errors.mobile = 'Mobile is required';
						}
						else if (!this.singleUserDetails.mobile.match(/\+?(88)?0?1[123456789][0-9]{8}\b/g)) {
							this.errors.mobile = 'Invalid mobile number';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'mobile');
						}

						break;

					case 'email' :

						if (!this.singleUserDetails.email) {
							this.errors.email = 'Email is required';
						}
						else if (!this.singleUserDetails.email.match(/[a-z0-9._+-]+@[a-z0-9.-]+\.[a-z]{2,}$/g)) {
							this.errors.email = 'Invalid email address';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'email');
						}

						break;

					case 'password' :

						if (this.createMode && ! this.singleUserDetails.password) {
							this.errors.password = 'Password is required';
						}
						else if (this.singleUserDetails.password && this.singleUserDetails.password.length < 8) {
							this.errors.password = 'Minimum length should be 8';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'password');
						}

						break;

					case 'password_confirmation' :

						if (this.singleUserDetails.password && ! this.singleUserDetails.password_confirmation) {
							this.errors.password_confirmation = 'Password is required';
						}
						else if (this.singleUserDetails.password && this.singleUserDetails.password !== this.singleUserDetails.password_confirmation) {
							this.errors.password_confirmation = "Password doesn't match";
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'password_confirmation');
						}

						break;

					case 'sale_percentage' :

						if (this.$route.name=='merchants' && this.singleUserDetails.support_deal.sale_percentage && (this.singleUserDetails.support_deal.sale_percentage < 0 || this.singleUserDetails.support_deal.sale_percentage > 100)) {

							this.errors.sale_percentage = "Percantage value is invalid";

						}
						else {
							this.submitForm = true;
							this.$delete(this.errors, 'sale_percentage');
						}

						break;

					case 'rent_period' : 

						if (this.$route.name=='merchants' && (this.singleUserDetails.support_deal.e_commerce_fulfillment_charge > 0 || this.singleUserDetails.support_deal.purchase_support_charge > 0 || this.singleUserDetails.support_deal.pos_support_charge > 0) && ! this.singleUserDetails.support_deal.rent_period_id) {

							this.errors.rent_period = "Rent period is required";

						}
						else {

							this.submitForm = true;
							this.$delete(this.errors, 'rent_period');

						}

						break;

					case 'fulfillment_charge' :

						if (this.$route.name=='merchants' && this.singleUserDetails.support_deal.e_commerce_fulfillment_support && this.singleUserDetails.support_deal.e_commerce_fulfillment_charge < 0) {

							this.errors.fulfillment_charge = "Fulfilment charge is invalid";

						}
						else {
							this.submitForm = true;
							this.$delete(this.errors, 'fulfillment_charge');
						}

						break;

					case 'purchase_support_charge' :

						if (this.$route.name=='merchants' && this.singleUserDetails.support_deal.purchase_support && this.singleUserDetails.support_deal.purchase_support_charge < 0) {

							this.errors.purchase_support_charge = "Purchase support charge is invalid";

						}
						else {
							this.submitForm = true;
							this.$delete(this.errors, 'purchase_support_charge');
						}

						break;

					case 'pos_support_charge' :

						if (this.$route.name=='merchants' && this.singleUserDetails.support_deal.pos_support && this.singleUserDetails.support_deal.pos_support_charge < 0) {

							this.errors.pos_support_charge = "POS charge is invalid";

						}
						else {
							this.submitForm = true;
							this.$delete(this.errors, 'pos_support_charge');
						}

						break;

					case 'number_outlets' :

						if (this.$route.name=='merchants' && this.singleUserDetails.support_deal.pos_support && (! this.singleUserDetails.support_deal.number_outlets || this.singleUserDetails.support_deal.number_outlets < 1)) {

							this.errors.number_outlets = "Outlet number is required";

						}
						else {
							this.submitForm = true;
							this.$delete(this.errors, 'number_outlets');
						}

						break;

				}
	 
			},
			changeArrow (event){
			
				if (event.target.classList.contains("fa-angle-up")) {
					event.target.classList.replace("fa-angle-up", "fa-angle-down");
				}
				else {
					event.target.classList.replace("fa-angle-down", "fa-angle-up");
				}
			
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

</style>