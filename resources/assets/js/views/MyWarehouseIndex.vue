
<template>
	<div class="pcoded-content">
		<breadcrumb 
			:icon="'warehouses'"
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
											  	<div class="row d-flex align-items-center text-center">
											  		<div class="col-sm-3 form-group">	
															My Warehouses List
											  		</div>

											  		<div class="col-sm-9 was-validated form-group">
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
										  			:tab-names="['approved', 'pending', 'trashed']" 
										  			:current-tab="currentTab" 

										  			@showApprovedContents="showApprovedContents" 
										  			@showPendingContents="showPendingContents" 
										  			@showTrashedContents="showTrashedContents" 
										  		></tab>
 												
										  		<!-- 
										  		<table-with-soft-delete-option 
										  			:query="query" 
										  			:per-page="perPage"  
										  			:column-names="['name', 'email', 'mobile', 'status']" 
										  			:column-values-to-show="['name', 'email', 'mobile', 'status']" 
										  			:contents-to-show = "contentsToShow" 
										  			:pagination = "pagination" 
										  			:required-permission="'warehouse'" 

										  			@showContentDetails="showContentDetails($event)" 
										  			@openContentEditForm="openContentEditForm($event)" 
										  			@openContentDeleteForm="openContentDeleteForm($event)" 
										  			@openContentRestoreForm="openContentRestoreForm($event)" 
										  			@changeNumberContents="changeNumberContents($event)" 
										  			@fetchAllContents="fetchAllWarehouses" 
										  			@searchData="searchData" 
										  		>	
										  		</table-with-soft-delete-option>
 												-->
										  		
										  		<div class="tab-content card-block pl-0 pr-0">
													<div class="table-responsive">
														<table class="table table-striped table-bordered nowrap text-center">
															<thead>
																<tr>
																	<th 
																		v-for="columnName in columnNames" 
																		:key="columnName" 
																	>
																		<a href="javascript:void(0)" @click="changeContentsOrder(columnName)"> 
																			{{ columnName | capitalize }}
																			<span v-show="currentSorting.toUpperCase() === columnName.toUpperCase() && ascending">
																				<i class="fa fa-sort-up" aria-hidden="true"></i>
																			</span>
																			<span v-show="currentSorting.toUpperCase() === columnName.toUpperCase() && descending">
																				<i class="fa fa-sort-down" aria-hidden="true"></i>
																			</span>
																			<span v-show="currentSorting.toUpperCase() !== columnName.toUpperCase()">
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
																v-for="(warehouse, warehouseIndex) in contentsToShow" 
																:key="'my-warehouses-index-' + warehouseIndex + '-warehouse-' + warehouse.id" 
																:class="warehouse.id==singleWarehouseData.id ? 'highlighted' : ''"
																>
																	<td>
																		{{ warehouse.name }}
																	</td>

																	<td>
																		{{ warehouse.email }}
																	</td>

																	<td>
																		{{ warehouse.mobile }}
																	</td>

																	<td>
																		<span :class="[warehouse.active ? 'badge-success' : 'badge-danger', 'badge']">
																			{{ warehouse.active ? 'Active' : 'Pending' }}
																		</span>
																	</td>
																	
																	<td>
																		<button type="button" 
																				class="btn btn-grd-info btn-icon" 
																				v-tooltip.bottom-end="'View Details'"  
																				@click="showContentDetails(warehouse)" 
																		>
																			<i class="fa fa-eye"></i>
																		</button>
																	</td>
															    
																</tr>
																<tr 
															  		v-show="!contentsToShow.length"
															  	>
														    		<td :colspan="columnNames.length+1">
															      		<div class="alert alert-danger" role="alert">
															      			Sorry, No data found.
															      		</div>
															    	</td>
															  	</tr>

															</tbody>
															<tfoot>
																<tr>	
																	<th 
																		v-for="columnName in columnNames" 
																		:key="'table-footer-' + columnName" 
																	>
																		<a href="javascript:void(0)" @click="changeContentsOrder(columnName)"> 
																			{{ columnName | capitalize }}
																			<span v-show="currentSorting.toUpperCase() === columnName.toUpperCase() && ascending">
																				<i class="fa fa-sort-up" aria-hidden="true"></i>
																			</span>
																			<span v-show="currentSorting.toUpperCase() === columnName.toUpperCase() && descending">
																				<i class="fa fa-sort-down" aria-hidden="true"></i>
																			</span>
																			<span v-show="currentSorting.toUpperCase() !== columnName.toUpperCase()">
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
																@change="changeNumberContents(perPage)"
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
																v-tooltip.bottom-end="'Reload'" 
																@click="pagination.current_page = 1; query === '' ? fetchAllWarehouses() : searchData()"
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
																@paginate="query === '' ? fetchAllWarehouses() : searchData()"
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
										<label class="col-sm-6 col-form-label">{{ singleWarehouseData.name }}</label>
									</div>
									<!-- 
									<div class="form-group form-row">
										<label class="col-sm-6 col-form-label font-weight-bold text-right">Code :</label>
										<label class="col-sm-6 col-form-label">{{ singleWarehouseData.code }}</label>
									</div>
									 -->
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
	</div>
</template>

<script type="text/javascript">

	import axios from 'axios';

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

	    data() {

	        return {

	        	query : '',
	        	error : '',
    			perPage : 10,
	        	loading : false,
	        	currentTab : 'approved',

	        	ascending : false,
	      		descending : false,
	      		currentSorting : '',

	        	allRentPeriods : [],

	        	contentsToShow : [],
	        	allFetchedWarehouses : [],

	        	pagination: {
		        	current_page: 1
		      	},

		      	columnNames : ['name', 'email', 'mobile', 'status'],

		      	// currentOwner : {},

	        	singleWarehouseData : singleWarehouseData,

	            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

	        }

		},
		
		created(){

			// this.getCurrentUser();
			this.fetchAllWarehouses();
			this.fetchAllRentPeriods();

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

		watch : {

			query : function(val){

				this.pagination.current_page = 1; 

				if (val==='') {

					this.fetchAllWarehouses();

				}
				else {

					this.searchData();

				}

			},

		},
		
		methods : {

		/*
			getCurrentUser() {

				axios
					.get('/api/current-owner/')
					.then(response => {
						if (response.status == 200) {
							this.currentOwner = response.data.user;
							this.fetchAllWarehouses();
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

			fetchAllWarehouses() {
				
				this.query = '';
				this.error = '';
				this.loading = true;
				this.allFetchedWarehouses = [];
				
				axios
					.get('/my-warehouses/' + this.perPage + "?page=" + this.pagination.current_page)
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
			fetchAllRentPeriods() {

				if (! this.userHasPermissionTo('view-asset-index')) {

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
						this.loading = false;
					});

			},
			searchData() {

				this.error = '';
				this.allFetchedWarehouses = [];
				// this.pagination.current_page = 1;
				
				axios
				.get('/search-my-warehouses/' + this.query + "/" +  this.perPage + "?page=" + this.pagination.current_page)
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
            changeNumberContents() {
				this.pagination.current_page = 1;

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
				else if (this.currentTab=='trashed') {
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
			getOwnerFullName(owner) {

				if (!owner.first_name && !owner.last_name) {
					return 'NA';
				}

				return owner.first_name || '' + ' ' + owner.last_name || '';

			},
			changeContentsOrder(columnName) {

				this.currentSorting = columnName;

				if (columnName.match(/name/gi)) {

					const nameExists = (object) => object.hasOwnProperty('name');
					const firstNameExists = (object) => object.hasOwnProperty('first_name');

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

				}
				else if (columnName.match(/email/gi)) {
					
					if (this.ascending) {
						this.ascending = false;
						this.descending = true;
						this.descendingAlphabets('email');
					}
					else if (this.descending) {
						this.ascending = true;
						this.descending = false;
						this.ascendingAlphabets('email');
					}
					else {
						this.ascending = true;
						this.descending = false;
						this.ascendingAlphabets('email');
					}

				}
				else if (columnName.match(/mobile/gi)) {

					if (this.ascending) {
						this.ascending = false;
						this.descending = true;
						this.descendingAlphabets('mobile');
					}
					else if (this.descending) {
						this.ascending = true;
						this.descending = false;
						this.ascendingAlphabets('mobile');
					}
					else {
						this.ascending = true;
						this.descending = false;
						this.ascendingAlphabets('mobile');
					}

				}

				else if (columnName.match(/status/gi)) {
					
					if (this.ascending) {
						this.ascending = false;
						this.descending = true;
						this.descendingNumeric('active');
					}
					else if (this.descending) {
						this.ascending = true;
						this.descending = false;
						this.ascendingNumeric('active');
					}
					else {
						this.ascending = true;
						this.descending = false;
						this.ascendingNumeric('active');
					}
					
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
			ascendingNumeric(columnValue) {
				this.contentsToShow.sort(
			 		function(a, b){
						return a.columnValue - b.columnValue;
					}
				);
			},
			descendingNumeric(columnValue) {
				this.contentsToShow.sort(
			 		function(a, b){
						return b.columnValue - a.columnValue;
					}
				);
			},
            
		}
  	}

</script>

<style scoped>
	
	/*@import '~vue-multiselect/dist/vue-multiselect.min.css';*/

	.modal { 
		overflow: auto !important; 
	}
	.modal-body {
		word-break: break-all;
	}

</style>