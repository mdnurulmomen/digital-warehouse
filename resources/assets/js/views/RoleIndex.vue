
<template v-if="userHasPermissionTo('view-role-index')">

	<div class="pcoded-content">

		<breadcrumb 
			:title="'roles'" 
			:message="'All our roles'"
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
											  		v-if="userHasPermissionTo('view-role-index') || userHasPermissionTo('create-role')" 
											  		:query="query" 
											  		:caller-page="'role'" 
											  		:required-permission = "'role'" 
											  		:disable-add-button="allPermissions.length==0 ? true : false" 
											  		
											  		@showContentCreateForm="showContentCreateForm" 
											  		@searchData="searchData($event)" 
											  		@fetchAllContents="fetchAllRoles"
											  	></search-and-addition-option>
											</div>
											
											<div class="col-sm-12 col-lg-12">
										  		<!-- 
										  		<tab 
										  			v-show="query === ''" 
										  			:tab-names="['current', 'trashed']" 
										  			:current-tab="currentTab" 

										  			@showCurrentContents="showCurrentContents" 
										  			@showTrashedContents="showTrashedContents" 
										  		></tab>
										  		 -->

										  		<div class="tab-content card-block">
											  		<div class="table-responsive">
														<table class="table table-striped table-bordered nowrap text-center">
															<thead>
																<tr>
																	<th>
																		<a 
																			href="javascript:void(0)" 
																			@click="changeNamesOrder"
																		> 
																			Name
																			<span v-show="ascending">
																				<i class="fa fa-sort-up" aria-hidden="true"></i>
																			</span>
																			<span v-show="descending">
																				<i class="fa fa-sort-down" aria-hidden="true"></i>
																			</span>
																			<span v-show="!ascending && !descending">
																				<i class="fa fa-sort" aria-hidden="true" style="opacity: 0.4;"></i>
																			</span>
																		</a>
																	</th>
																	<th>
																		Actions
																	</th>
																</tr>
															</thead>
															<tbody>

																<tr 
																	v-for="content in contentsToShow" 
																	:key="'content-key-' + content.id" 
																>
																	<td>
																		{{ content.name | capitalize }}
																	</td>
																	
																	<td> 
																		<button type="button" 
																				class="btn btn-grd-info btn-icon"  
																				@click="showContentDetails(content)" 
																		>
																			<i class="fa fa-eye"></i>
																		</button>
 																		

																		<button type="button" 
																				class="btn btn-grd-primary btn-icon" 
																				v-show="!content.deleted_at" 
																				@click="openContentEditForm(content)" 
																				v-if="userHasPermissionTo('update-role')"
																		>
																			<i class="fa fa-edit"></i>
																		</button>

																		<button type="button" 
																				class="btn btn-grd-danger btn-icon" 
																				v-show="!content.deleted_at" 
																				@click="openContentDeleteForm(content)" 
																				v-if="userHasPermissionTo('delete-role')"
																		>
																			<i class="fa fa-trash"></i>
																		</button>
																	</td>
															    
																</tr>
																<tr 
															  		v-show="!contentsToShow.length"
															  	>
														    		<td :colspan="2">
															      		<div class="alert alert-danger" role="alert">
															      			Sorry, No data found.
															      		</div>
															    	</td>
															  	</tr>

															</tbody>
															<tfoot>
																<tr>	
																	<th>
																		<a 
																			href="javascript:void(0)" 
																			@click="changeNamesOrder"
																		> 
																			Name
																			<span v-show="ascending">
																				<i class="fa fa-sort-up" aria-hidden="true"></i>
																			</span>
																			<span v-show="descending">
																				<i class="fa fa-sort-down" aria-hidden="true"></i>
																			</span>
																			<span v-show="!ascending && !descending">
																				<i class="fa fa-sort" aria-hidden="true" style="opacity: 0.4;"></i>
																			</span>
																		</a>
																	</th>
																	<th>
																		Actions
																	</th>
																</tr>
															</tfoot>
														</table>
													</div>

													<div class="row d-flex align-items-center">
														<div class="col-sm-2 col-4">
															<select 
																class="form-control" 
																v-model.number="perPage" 
																@change="changeNumberContents()"
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
																@click="query === '' ? fetchAllRoles() : searchData()"
															>
																Reload
																<i class="fa fa-sync"></i>
															</button>
														</div>
														<div class="col-sm-8 col-12 text-right form-group">
															<pagination
																v-if="pagination.last_page > 1"
																:pagination="pagination"
																:offset="5"
																@paginate="query === '' ? fetchAllRoles() : searchData()"
															>
															</pagination>
														</div>
													</div>
										  		</div>

										  		<!-- 
										  		<table-with-soft-delete-option 
										  			:query="query" 
										  			:per-page="perPage"  
										  			:column-names="['name']" 
										  			:column-values-to-show="['name']" 
										  			:contents-to-show = "contentsToShow" 
										  			:pagination = "pagination"

										  			@showContentDetails="showContentDetails($event)" 
										  			@openContentEditForm="openContentEditForm($event)" 
										  			@openContentDeleteForm="openContentDeleteForm($event)" 
										  			@openContentRestoreForm="openContentRestoreForm($event)" 
										  			@changeNumberContents="changeNumberContents($event)" 
										  			@fetchAllContents="fetchAllRoles" 
										  			@searchData="searchData" 
										  		>	
										  		</table-with-soft-delete-option>
 												-->
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

	<!-- 	
		<asset-create-or-edit-modal 
			:create-mode="createMode" 
			:caller-page="'role'" 
			:single-asset-data="singleRoleData" 
			:csrf="csrf"

			@storeAsset="storeRole($event)" 
			@updateAsset="updateRole($event)" 
		></asset-create-or-edit-modal>
 	-->

 		<!--Create Or Edit Modal -->
		<div class="modal fade" id="role-createOrEdit-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" v-if="userHasPermissionTo('create-role') || userHasPermissionTo('update-role')">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">
							{{ createMode ? 'Create' : 'Edit' }} Role
						</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
						
					<form 	
						class="form-horizontal" 
						v-on:submit.prevent="createMode ? storeRole() : updateRole()" 
						autocomplete="off" 
					>

						<input type="hidden" name="_token" :value="csrf">

						<div class="modal-body">
							<div class="form-row">
								<div class="form-group col-md-12">
									<label for="inputFirstName">Name</label>
									<input type="text" 
										class="form-control" 
										v-model="singleRoleData.name" 
										placeholder="Name should be unique" 
										:class="!errors.name  ? 'is-valid' : 'is-invalid'" 
										@change="validateFormInput('name')" 
										required="true" 
									>

									<div class="invalid-feedback">
							        	{{ errors.name }}
							  		</div>
								</div>
							</div>
							
							<div class="form-row" v-show="allPermissions.length">
								<div class="form-group col-md-12">
									<label for="inputUsername">Special Permissions</label>
									<div class="row">
										<!-- Approvable Models -->
										<div 
											class="col-md-6" 
											v-for="model in modelCRUDableAndApproveable" 
											:key="'approve-model-permission-name-' + model"
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
												<label>{{ modelName('update/Approve-' + model) }}</label>
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

											<!-- approve -->
											<!-- 
											<div class="form-check">
												<input 
													type="checkbox" 
													:checked="permissionExists('approve-' + model)" 
													@change="insertPermission('approve-' + model, $event)" 
													:ref="'approve-' + model.toLowerCase()"
												>
												<label>{{ modelName('approve-' + model) }}</label>
											</div>
												-->
										</div>

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
											:key="'view-make-model-permission-name-' + model"
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
							
							<div class="form-row" v-show="! allPermissions.length">
								<div class="col-md-12">
									<p class="text-danger text-center">
										Sorry There is a problem with fetching permissions. You may not have permissions to select permissions
									</p>
								</div>
							</div>
						</div>

						<div class="modal-footer flex-column">
							<div class="col-sm-12 text-right" v-show="!submitForm">
								<span class="text-danger small">
							  		Please input required fields
							  	</span>
							</div>
							<div class="col-sm-12">
								<button type="button" class="btn btn-secondary float-left" data-dismiss="modal">
									Close
								</button>
								<button type="submit" class="btn btn-primary float-right" :disabled="!submitForm">
									{{ createMode ? 'Save' : 'Update' }}
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>

		<delete-confirmation-modal 
			v-if="userHasPermissionTo('delete-role')" 
			:csrf="csrf" 
			:submit-method-name="'deleteRole'" 
			:content-to-delete="singleRoleData"
			:restoration-message="'You can not retrieve this role again !'" 
			
			@deleteRole="deleteRole($event)" 
		></delete-confirmation-modal>

	<!-- 
		<restore-confirmation-modal 
			:csrf="csrf" 
			:submit-method-name="'restoreRole'" 
			:content-to-restore="singleRoleData"
			:restoration-message="'This will restore all related items !'" 

			@restoreRole="restoreRole($event)" 
		></restore-confirmation-modal>
 	
		<asset-view-modal 
			:caller-page="'role'" 
			:asset-to-view="singleRoleData" 
			:properties-to-show="['name']"
		></asset-view-modal>
 	-->

 		<!-- View Modal -->
		<div class="modal fade" id="asset-view-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Role Details</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="form-row"> 
						    <div class="form-group col-md-6 text-right">
								<label class="font-weight-bold">Role Name: </label>
							</div>
							<div class="form-group col-md-6 text-left">
								{{ singleRoleData.name | capitalize }}
							</div>
						</div>

						<div class="form-row"> 
						    <div class="col-md-6 text-right">
								<label class="font-weight-bold">Permissions: </label>
							</div>
							<div class="col-md-12">
								<div class="row">
									<!-- Approvable Models -->
									<div 
										class="col-md-6" 
										v-for="model in modelCRUDableAndApproveable" 
										:key="'approve-model-permission-name-' + model"
									>
										<p class="font-weight-bold mt-4 mb-3">{{ modelName(model) }}</p>

										<!-- create -->
										<p class="m-0">
											<span v-show="permissionExists('create-' + model)" class="text-success">
												<i class="fa fa-check" aria-hidden="true"></i>
											</span>
											<span v-show="!permissionExists('create-' + model)" class="text-danger">
												<i class="fa fa-times" aria-hidden="true"></i>
											</span>
											{{ modelName('create-' + model) }}
										</p>

										<!-- update -->
										<p class="m-0">
											<span v-show="permissionExists('update-' + model)" class="text-success">
												<i class="fa fa-check" aria-hidden="true"></i>
											</span>
											<span v-show="!permissionExists('update-' + model)" class="text-danger">
												<i class="fa fa-times" aria-hidden="true"></i>
											</span>
											{{ modelName('update/Approve-' + model) }}
										</p>

										<!-- delete -->
										<p class="m-0">
											<span v-show="permissionExists('delete-' + model)" class="text-success">
												<i class="fa fa-check" aria-hidden="true"></i>
											</span>
											<span v-show="!permissionExists('delete-' + model)" class="text-danger">
												<i class="fa fa-times" aria-hidden="true"></i>
											</span>
											{{ modelName('delete-' + model) }}
										</p>

										<!-- view -->
										<p class="m-0">
											<span v-show="permissionExists('view-' + model + '-index')" class="text-success">
												<i class="fa fa-check" aria-hidden="true"></i>
											</span>
											<span v-show="!permissionExists('view-' + model + '-index')" class="text-danger">
												<i class="fa fa-times" aria-hidden="true"></i>
											</span>
											{{ modelName('view-' + model + '-list') }}
										</p>
									</div>

									<!-- CRUD Models -->
									<div 
										class="col-md-6" 
										v-for="model in modelsCRUDable" 
										:key="'crud-model-permission-name-' + model"
									>
										<p class="font-weight-bold mt-4 mb-3">{{ modelName(model) }}</p>

										<!-- create -->
										<p class="m-0">
											<span v-show="permissionExists('create-' + model)" class="text-success">
												<i class="fa fa-check" aria-hidden="true"></i>
											</span>
											<span v-show="!permissionExists('create-' + model)" class="text-danger">
												<i class="fa fa-times" aria-hidden="true"></i>
											</span>
											{{ modelName('create-' + model) }}
										</p>

										<!-- update -->
										<p class="m-0">
											<span v-show="permissionExists('update-' + model)" class="text-success">
												<i class="fa fa-check" aria-hidden="true"></i>
											</span>
											<span v-show="!permissionExists('update-' + model)" class="text-danger">
												<i class="fa fa-times" aria-hidden="true"></i>
											</span>
											{{ modelName('update-' + model) }}
										</p>

										<!-- delete -->
										<p class="m-0">
											<span v-show="permissionExists('delete-' + model)" class="text-success">
												<i class="fa fa-check" aria-hidden="true"></i>
											</span>
											<span v-show="!permissionExists('delete-' + model)" class="text-danger">
												<i class="fa fa-times" aria-hidden="true"></i>
											</span>
											{{ modelName('delete-' + model) }}
										</p>

										<!-- view -->
										<p class="m-0">
											<span v-show="permissionExists('view-' + model + '-index')" class="text-success">
												<i class="fa fa-check" aria-hidden="true"></i>
											</span>
											<span v-show="!permissionExists('view-' + model + '-index')" class="text-danger">
												<i class="fa fa-times" aria-hidden="true"></i>
											</span>
											{{ modelName('view-' + model + '-list') }}
										</p>
									</div>

									<!-- Viewable and Updatable CRUD -->
									<div 
										class="col-md-6" 
										v-for="model in modelsViewableAndUpdatable" 
										:key="'view-update-model-permission-name-' + model"
									>
										<p class="font-weight-bold mt-4 mb-3">{{ modelName(model) }}</p>

										<!-- view -->
										<p class="m-0">
											<span v-show="permissionExists('view-' + model + '-index')" class="text-success">
												<i class="fa fa-check" aria-hidden="true"></i>
											</span>
											<span v-show="!permissionExists('view-' + model + '-index')" class="text-danger">
												<i class="fa fa-times" aria-hidden="true"></i>
											</span>
											{{ modelName('view-' + model + '-list') }}
										</p>

										<!-- update -->
										<p class="m-0">
											<span v-show="permissionExists('update-' + model)" class="text-success">
												<i class="fa fa-check" aria-hidden="true"></i>
											</span>
											<span v-show="!permissionExists('update-' + model)" class="text-danger">
												<i class="fa fa-times" aria-hidden="true"></i>
											</span>
											{{ modelName('update-' + model) }}
										</p>
									</div>

									<!-- Viewable and Makeable -->
									<div 
										class="col-md-6" 
										v-for="model in modelsViewableRecommendableAndApproveable" 
										:key="'view-make-model-permission-name-' + model"
									>
										<p class="font-weight-bold mt-4 mb-3">{{ modelName(model) }}</p>

										<!-- view -->
										<p class="m-0">
											<span v-show="permissionExists('view-' + model + '-index')" class="text-success">
												<i class="fa fa-check" aria-hidden="true"></i>
											</span>
											<span v-show="!permissionExists('view-' + model + '-index')" class="text-danger">
												<i class="fa fa-times" aria-hidden="true"></i>
											</span>
											{{ modelName('view-' + model + '-list') }}
										</p>

										<!-- recommend -->
										<p class="m-0">
											<span v-show="permissionExists('recommend-' + model)" class="text-success">
												<i class="fa fa-check" aria-hidden="true"></i>
											</span>
											<span v-show="!permissionExists('recommend-' + model)" class="text-danger">
												<i class="fa fa-times" aria-hidden="true"></i>
											</span>
											{{ modelName('recommend-' + model) }}
										</p>

										<!-- approve -->
										<p class="m-0">
											<span v-show="permissionExists('approve-' + model)" class="text-success">
												<i class="fa fa-check" aria-hidden="true"></i>
											</span>
											<span v-show="!permissionExists('approve-' + model)" class="text-danger">
												<i class="fa fa-times" aria-hidden="true"></i>
											</span>
											{{ modelName('approve-' + model) }}
										</p>
									</div>

									<!-- Viewable -->
									<div 
										class="col-md-6" 
										v-for="model in modelsViewable" 
										:key="'view-model-permission-name-' + model"
									>
										<p class="font-weight-bold mt-4 mb-3">{{ modelName(model) }}</p>

										<!-- view -->
										<p class="m-0">
											<span v-show="permissionExists('view-' + model + '-index')" class="text-success">
												<i class="fa fa-check" aria-hidden="true"></i>
											</span>
											<span v-show="!permissionExists('view-' + model + '-index')" class="text-danger">
												<i class="fa fa-times" aria-hidden="true"></i>
											</span>
											{{ modelName('view-' + model + '-list') }}
										</p>
									</div>

									<!-- Viewable 2 -->
									<div 
										class="col-md-6" 
										v-for="model in modelsViewable2" 
										:key="'view2-model-permission-name-' + model"
									>
										<p class="font-weight-bold mt-4 mb-3">{{ modelName(model) }}</p>

										<!-- view -->
										<p class="m-0">
											<span v-show="permissionExists('view-' + model)" class="text-success">
												<i class="fa fa-check" aria-hidden="true"></i>
											</span>
											<span v-show="!permissionExists('view-' + model)" class="text-danger">
												<i class="fa fa-times" aria-hidden="true"></i>
											</span>
											{{ modelName('view-' + model) }}
										</p>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary btn-block btn-sm" data-dismiss="modal">
							Close
						</button>
					</div>
				</div>
			</div>
		</div>

	</div>

</template>

<script type="text/javascript">

	import axios from 'axios';

    let singleRoleData = {
    	permissions : [],
    };

	export default {

	    data() {

	        return {

	        	query : '',
	        	error : '',
    			perPage : 10,
	        	loading : false,
	        	// currentTab : 'current',

	        	ascending : false,
	      		descending : false,

	        	createMode : true,
	        	submitForm : true,

	        	allFetchedContents : [],
	        	contentsToShow : [],

	        	allPermissions : [],

	        	modelCRUDableAndApproveable : [
	                'Product-Stock',
	            ],

				modelsCRUDable : [
	        		'Role',
	            	'Product',
	            	'Manager',
	            	'Merchant',
	                'Warehouse',
	                'Merchant-Deal',
	            	'Product-Asset',
	                // 'Product-Stock',
	            	'Warehouse-Owner',
	                'Warehouse-Asset',
	                'Delivery-Company',
	                'Merchant-Product',
	                'Merchant-Payment',
	        	],

	        	modelsViewableAndUpdatable : [
	        		'Application-Setting',  // view / update
                	'Requisition', // view / update(cancel)
	        	],

	        	modelsViewableRecommendableAndApproveable : [
	        		'Dispatch',  // view / recommend
	        	],

	        	modelsViewable : [
	        		'Permission',  // view
	        	],

	        	modelsViewable2 : [
	        		'General-Dashboard-One',  // view
	        		'General-Dashboard-Two',  // view
	        	],

	        	pagination: {
		        	current_page: 1
		      	},

	        	singleRoleData : singleRoleData,

	        	errors : {
					
				},

	            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

	        }

		},
		
		created(){			

			this.fetchAllRoles();

			if (this.userHasPermissionTo('view-permission-index')) {

				this.fetchAllPermissions();

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

			fetchAllRoles() {
				
				this.query = '';
				this.error = '';
				this.loading = true;
				this.allFetchedContents = [];
				
				axios
					.get('/api/roles/' + this.perPage + "?page=" + this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							this.allFetchedContents = response.data;
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
			searchData(emitedValue=false) {

				if (emitedValue) {
					this.query=emitedValue;
				}

				this.error = '';
				this.allFetchedContents = [];
				this.pagination.current_page = 1;
				
				axios
				.get(
					"/api/search-roles/" + this.query + "/" + this.perPage + "?page=" + this.pagination.current_page
				)
				.then(response => {
					this.allFetchedContents = response.data;
					this.contentsToShow = this.allFetchedContents.all.data;
					this.pagination = response.data.all;
				})
				.catch(e => {
					this.error = e.toString();
				});

			},
			showContentDetails(object) {	
				this.singleRoleData = object;
				$('#asset-view-modal').modal('show');
			},
			showContentCreateForm() {
				this.createMode = true;
				this.singleRoleData = {
					permissions : [],
				};
				$('#role-createOrEdit-modal').modal('show');
			},
			openContentEditForm(object) {
				this.createMode = false;
				this.singleRoleData = object;
				$('#role-createOrEdit-modal').modal('show');
			},
			openContentDeleteForm(object) {	
				this.singleRoleData = object;
				$('#delete-confirmation-modal').modal('show');
			},
		/*
			openContentRestoreForm(object) {	
				this.singleRoleData = object;
				$('#restore-confirmation-modal').modal('show');
			},
		*/
			storeRole() {
				
				if (!this.verifyUserInput()) {
					this.submitForm = false;
					return;
				}

				axios
					.post('/roles/' + this.perPage, this.singleRoleData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("New role has been created", "Success");
							this.allFetchedContents = response.data;
							this.query !== '' ? this.searchData() : this.showSelectedTabContents();
							$('#role-createOrEdit-modal').modal('hide');
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
			updateRole() {
				
				if (!this.verifyUserInput()) {
					this.submitForm = false;
					return;
				}

				axios
					.put('/roles/' + this.singleRoleData.id + '/' + this.perPage, this.singleRoleData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Role has been updated", "Success");
							this.allFetchedContents = response.data;
							this.query !== '' ? this.searchData() : this.showSelectedTabContents();
							$('#role-createOrEdit-modal').modal('hide');
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
			deleteRole(singleRoleData) {
				
				axios
					.delete('/roles/' + this.singleRoleData.id + '/' + this.perPage)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Role has been deleted", "Success");
							this.allFetchedContents = response.data;
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
		/*
			restoreRole(singleRoleData) {
				
				axios
					.patch('/roles/' + this.singleRoleData.id + '/' + this.perPage)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Role has been restored", "Success");
							this.allFetchedContents = response.data;
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
		*/
            changeNumberContents() {
				this.pagination.current_page = 1;
				// this.perPage = expectedContentsPerPage;

				if (this.query === '') {
					this.fetchAllRoles();
				}
				else {
					this.searchData();
				}
    		},
    		showSelectedTabContents() {
				
				// if (this.currentTab=='current') {
					this.contentsToShow = this.allFetchedContents.current.data;
					this.pagination = this.allFetchedContents.current;
				// }
				// else {
				// 	this.contentsToShow = this.allFetchedContents.trashed.data;
				// 	this.pagination = this.allFetchedContents.trashed;
				// }

			},
			/*
			showCurrentContents() {
				this.currentTab = 'current';
				this.showSelectedTabContents();
			},
			showTrashedContents() {
				this.currentTab = 'trashed';
				this.showSelectedTabContents();
			},
			*/
			verifyUserInput() {
				this.validateFormInput('name');
				this.validateFormInput('permission');

				if (this.errors.constructor === Object && Object.keys(this.errors).length == 0) {
					return true;
				}

				return false;
			},
			changeNamesOrder() {

				if (this.ascending) {

					this.ascending = false;
					this.descending = true;

					this.descendingAlphabets('name');

				}
				else if (this.descending) {

					this.ascending = true;
					this.descending = false;

					this.ascendingAlphabets('name');

				}
				else {

					this.ascending = true;
					this.descending = false;

					this.ascendingAlphabets('name');

				}
				
			},
			ascendingAlphabets(columnValue) {
				this.contentsToShow.sort(
			 		function(a, b){
						var x = a[columnValue] ? a[columnValue].toLowerCase() : '';
						var y = b[columnValue] ? b[columnValue].toLowerCase() : '';
						if (x < y) {return -1;}
						if (x > y) {return 1;}
						return 0;
					}
				);
			},
			descendingAlphabets(columnValue) {
				this.contentsToShow.sort(
			 		function(a, b){
						var x = a[columnValue] ? a[columnValue].toLowerCase() : '';
						var y = b[columnValue] ? b[columnValue].toLowerCase() : '';
						if (x > y) {return -1;}
						if (x < y) {return 1;}
						return 0;
					}
				);
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
			permissionExists(permissionName) {

				permissionName = permissionName.toLowerCase();

				if (this.singleRoleData.permissions.length) {

					return this.singleRoleData.permissions.some(
						permission => permission.name === permissionName
					);

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

				if (event.target.checked && (! this.singleRoleData.permissions.length || ! this.permissionExists(permissionName))) {

					this.submitForm = true;
					let permission = this.getExpectedPermission(permissionName);

					if (permission) {
				   		
				   		this.singleRoleData.permissions.push(permission);
						
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
					/*
						else {

							this.error = 'There is a problem with fetching permissions. You may not have permission to view permissions';
						
						}
					*/

				}
				else if (! event.target.checked && this.permissionExists(permissionName)) {

					permissionName = permissionName.toLowerCase();

					let uncheckedPermissionIndex = this.singleRoleData.permissions.findIndex(
						permission => permission.name==permissionName
					);

					if (uncheckedPermissionIndex > -1) {
						this.singleRoleData.permissions.splice(uncheckedPermissionIndex, 1);
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

					if (! this.$refs['view-delivery-company-index'][0].checked) {

						this.$refs['view-delivery-company-index'][0].click();

					}

				}

			},
			validateFormInput (formInputName) {
				
				this.submitForm = false;

				switch(formInputName) {

					case 'name' :

						if (!this.singleRoleData.name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.name = 'No special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'name');
						}

						break;

					case 'permission' :

						if (this.singleRoleData.permissions.length < 1) {
							this.errors.permission = 'One permission is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'permission');
						}

						break;

				}
	 
			}
            
		}
  	}

</script>