
<template v-if="userHasPermissionTo('view-product-asset-index')">
	<div class="pcoded-content">
		<breadcrumb 
			:icon="'variations'"
			:title="'variations'" 
			:message="'All our variations'"
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
											  		v-if="userHasPermissionTo('create-product-asset-index') || userHasPermissionTo('create-product-asset')" 
											  		:query="query" 
											  		:caller-page="'variation'" 
											  		:required-permission = "'product-asset'" 
											  		
											  		@showContentCreateForm="showContentCreateForm" 
											  		@searchData="pagination.current_page = 1; searchData($event)" 
											  		@fetchAllContents="pagination.current_page = 1; fetchAllContents()"
											  	></search-and-addition-option>
											</div>
											
											<div class="col-sm-12 col-lg-12">
										  		<loading v-show="loading"></loading>

										  		<tab 
										  			v-show="query === '' && ! loading" 
										  			:tab-names="['current', 'trashed']" 
										  			:current-tab="currentTab" 

										  			@showCurrentContents="showCurrentContents" 
										  			@showTrashedContents="showTrashedContents" 
										  		></tab>

										  		<!-- 
										  		<table-with-soft-delete-option 
										  			:query="query" 
										  			:per-page="perPage"  
										  			:column-names="['name', 'type', 'parent']" 
										  			:column-values-to-show="['name', 'variation_type_name', 'variation_parent_name']" 
										  			:contents-to-show = "contentsToShow" 
										  			:pagination = "pagination" 
										  			:required-permission = "'product-asset'" 

										  			@showContentDetails="showContentDetails($event)" 
										  			@openContentEditForm="openContentEditForm($event)" 
										  			@openContentDeleteForm="openContentDeleteForm($event)" 
										  			@openContentRestoreForm="openContentRestoreForm($event)" 
										  			@changeNumberContents="changeNumberContents($event)" 
										  			@fetchAllContents="fetchAllContents" 
										  			@searchData="searchData" 
										  		>	
										  		</table-with-soft-delete-option>
										  		-->

										  		<div 
										  			class="tab-content card-block pl-0 pr-0" 
										  			v-show="!loading"
										  		>
													<div class="table-responsive">
														<table class="table table-striped table-bordered nowrap text-center">
															<thead>
																<tr>
																	<th>
																		<a 
																			href="javascript:void(0)" 
																			@click="changeContentsOrder('name')"
																		> 
																			Variation
																			<span v-show="currentSorting==='name' && ascending">
																				<i class="fa fa-sort-up" aria-hidden="true"></i>
																			</span>
																			<span v-show="currentSorting==='name' && descending">
																				<i class="fa fa-sort-down" aria-hidden="true"></i>
																			</span>
																			<span v-show="currentSorting!=='name'">
																				<i class="fa fa-sort" aria-hidden="true" style="opacity: 0.4;"></i>
																			</span>
																		</a>
																	</th>

																	<th>
																		Type
																	</th>

																	<th>Parents</th>

																	<th>Actions</th>
																</tr>
															</thead>

															<tbody>
																<tr 
																	v-for="(content, contentIndex) in contentsToShow" 
																	:key="'variation-index-' + contentIndex + 'variation-id-' + content.id" 
																	:class="content.id==singleAssetData.id ? 'highlighted' : ''"
																>
																	<td>{{ content.name | capitalize }}</td>

																	<td>
																		{{ content.type ? content.type.name : 'NA' | capitalizeEachWord }}
																	</td>
																		
																	<td>
																		<ul v-if="content.parent">
																			<tree-item
																				:item="content.parent"
																			></tree-item>
																		</ul>

																		<span v-else>
																			--
																		</span>
																	</td>
																	
																	<td>
																		<button type="button" 
																				class="btn waves-effect waves-dark btn-primary btn-outline-primary btn-icon" 
																				v-tooltip.bottom-end="'Edit'" 
																				v-show="! content.deleted_at" 
																				@click="openContentEditForm(content)" 
																				v-if="userHasPermissionTo('update-product-asset')"
																		>
																			<i class="fa fa-edit"></i>
																		</button>

																		<button type="button" 
																				class="btn waves-effect waves-dark btn-danger btn-outline-danger btn-icon" 
																				v-tooltip.bottom-end="'Delete'" 
																				v-show="! content.deleted_at" 
																				@click="openContentDeleteForm(content)" 
																				v-if="userHasPermissionTo('delete-product-asset')"
																		>
																			<i class="fa fa-trash"></i>
																		</button>

																		<button type="button" 
																				class="btn waves-effect waves-dark btn-warning btn-outline-warning btn-icon" 
																				v-tooltip.bottom-end="'Restore'" 
																				v-show="content.deleted_at" 
																				@click="openContentRestoreForm(content)" 
																				v-if="userHasPermissionTo('delete-product-asset')"
																		>
																			<i class="fa fa-undo"></i>
																		</button>
																	</td>
																</tr>

																<tr 
															  		v-show="! contentsToShow.length"
															  	>
														    		<td colspan="5">
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
																			@click="changeContentsOrder('name')"
																		> 
																			Variation
																			<span v-show="currentSorting==='name' && ascending">
																				<i class="fa fa-sort-up" aria-hidden="true"></i>
																			</span>
																			<span v-show="currentSorting==='name' && descending">
																				<i class="fa fa-sort-down" aria-hidden="true"></i>
																			</span>
																			<span v-show="currentSorting!=='name'">
																				<i class="fa fa-sort" aria-hidden="true" style="opacity: 0.4;"></i>
																			</span>
																		</a>
																	</th>

																	<th>
																		Type
																	</th>

																	<th>Parents</th>

																	<th>Actions</th>
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
																class="btn waves-effect waves-dark btn-primary btn-outline-primary btn-sm" 
																v-tooltip.bottom-end="'Reload'" 
																@click="pagination.current_page = 1; query === '' ? fetchAllContents() : searchData()"
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
																@paginate="query === '' ? fetchAllContents() : searchData()"
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

	<!-- 
		<asset-create-or-edit-modal 
			:create-mode="createMode" 
			:caller-page="'variation'" 
			:single-asset-data="singleAssetData" 
			:csrf="csrf"

			@storeAsset="storeAsset($event)" 
			@updateAsset="updateAsset($event)" 
		></asset-create-or-edit-modal>
 	-->

 		<!--Create Or Edit Modal -->
		<div class="modal fade" id="asset-createOrEdit-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" v-if="userHasPermissionTo('create-product-asset') || userHasPermissionTo('update-product-asset')">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">
							{{ createMode ? 'Create' : 'Edit' }} Variation
						</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
						
					<form 	
						class="form-horizontal" 
						v-on:submit.prevent="createMode ? storeAsset() : updateAsset()" 
						autocomplete="off" 
					>

						<input type="hidden" name="_token" :value="csrf">

						<div class="modal-body">
							<div class="form-row">
								<div class="form-group col-md-12">
									<label for="inputUsername">Variation Type</label>
									<multiselect 
                              			v-model="singleAssetData.type"
                              			placeholder="Variation Type" 
                                  		label="name" 
                                  		track-by="id" 
                                  		:options="allVariationTypes" 
                                  		:custom-label="objectNameWithCapitalized" 
                                  		:required="true" 
                                  		class="form-control p-0" 
                                  		:class="!errors.asset.variation_type  ? 'is-valid' : 'is-invalid'"
                                  		:allow-empty="false"
                                  		selectLabel = "Press/Click"
                                  		deselect-label="Can't remove single value"
                                  		@close="validateFormInput('variation_type')"
                              		>
                                	</multiselect>

									<div 
										class="invalid-feedback" 
										style="display: block;" 
										v-show="errors.asset.variation_type"
									>
							        	{{ errors.asset.variation_type }}
							  		</div>
								</div>
							</div>

							<div class="form-row">
								<div class="form-group col-md-12">
									<label for="inputFirstName">Name</label>
									<input type="text" 
										class="form-control" 
										v-model="singleAssetData.name" 
										placeholder="Name should be unique" 
										:class="!errors.asset.name ? 'is-valid' : 'is-invalid'" 
										@change="validateFormInput('name')" 
										required="true" 
									>

									<div class="invalid-feedback">
							        	{{ errors.asset.name }}
							  		</div>
								</div>
							</div>

							<div class="form-row" v-if="singleAssetData.type && singleAssetData.type.hasOwnProperty('variations') && singleAssetData.type.variations.length">
								<div class="form-group col-md-12">
									<label for="inputUsername">Parent Variation</label>
									
									<!-- 
										<multiselect 
	                              			v-model="singleAssetData.parent"
	                              			placeholder="Parent Variation" 
	                                  		label="name" 
	                                  		track-by="id" 
	                                  		:options="singleAssetData.type.variations" 
	                                  		:custom-label="objectNameWithCapitalized" 
	                                  		class="form-control p-0 is-valid" 
	                                  		selectLabel = "Press/Click"
	                                  		deselect-label="Press/Click to remove" 
	                                  		@change="validateFormInput('variation_parent_id')" 
	                              		>
	                                	</multiselect> 
	                                -->

                                	<treeselect
                                		v-model="singleAssetData.parent" 
										:options="singleAssetData.type.variations"
										:show-count="true" 
										:normalizer="treeSelectCustomFunction" 
										:valueFormat="'object'"
										@select="validateFormInput('variation_parent_id')" 
										class="form-control p-0 is-valid" 
										placeholder="Variation Parent"
									/>

                                	<div 
										class="invalid-feedback" 
										style="display: block;" 
										v-show="errors.asset.variation_parent_id"
									>
							        	{{ errors.asset.variation_parent_id }}
							  		</div>
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
								<button type="button" class="btn waves-effect waves-dark btn-secondary btn-outline-secondary float-left" data-dismiss="modal">
									Close
								</button>
								<button type="submit" class="btn waves-effect waves-dark btn-primary btn-outline-primary float-right" :disabled="!submitForm">
									Save
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>

		<delete-confirmation-modal 
			v-if="userHasPermissionTo('delete-product-asset')" 
			:csrf="csrf" 
			:submit-method-name="'deleteAsset'" 
			:content-to-delete="singleAssetData"
			:restoration-message="'But once you think, you can restore this item !'" 
			
			@deleteAsset="deleteAsset($event)" 
		></delete-confirmation-modal>

		<restore-confirmation-modal 
			v-if="userHasPermissionTo('delete-product-asset')" 
			:csrf="csrf" 
			:submit-method-name="'restoreAsset'" 
			:content-to-restore="singleAssetData"
			:restoration-message="'This will restore all related items !'" 

			@restoreAsset="restoreAsset($event)" 
		></restore-confirmation-modal>

		<!-- 
		<asset-view-modal 
			:caller-page="'variation'" 
			:asset-to-view="singleAssetData" 
			:properties-to-show="['name']"
		></asset-view-modal>
 		-->

 		<!-- View Modal -->
		<div class="modal fade" id="asset-view-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Variation Details</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">

						<div class="form-row"> 
						    <div class="form-group col-md-6 text-right">
								<label class="font-weight-bold">Name: </label>
							</div>
							<div class="form-group col-md-6 text-left">
								{{ singleAssetData.name | capitalizeEachWord }}
							</div>
						</div>

						<div class="form-row"> 
						    <div class="form-group col-md-6 text-right">
								<label class="font-weight-bold">Parent Variation: </label>
							</div>
							<div class="form-group col-md-6 text-left">
								{{ singleAssetData.parent ? $options.filters.capitalizeEachWord(singleAssetData.parent.name) : 'NA' }}
							</div>
						</div>

						<div class="form-row"> 
						    <div class="form-group col-md-6 text-right">
								<label class="font-weight-bold">Variation Type: </label>
							</div>
							<div class="form-group col-md-6 text-left">
								{{ singleAssetData.type ? $options.filters.capitalizeEachWord(singleAssetData.type.name) : 'NA' }}
							</div>
						</div>

					</div>
					<div class="modal-footer">
						<button type="button" class="btn waves-effect waves-dark btn-secondary btn-outline-secondary btn-block btn-sm" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript">

	import axios from 'axios';
	import Multiselect from 'vue-multiselect';
	import Treeselect from '@riophae/vue-treeselect'
	// import the styles
  	import '@riophae/vue-treeselect/dist/vue-treeselect.css'

    let singleAssetData = {
    	type : {},
    };

    // demo data
    /*let variationData = {
    	name: "Rip Mango",
    	parent: {
    		name: "Mango",
    		parent: {
    			name: "Fruits",
    			parent: { 
    				name: "Healthy Foods", 
    				parent : null,
    			}, 		
			},
    		
    	}
    	
    };*/

	export default {

	    components: { 
	    	Treeselect,
			multiselect : Multiselect,
		},

	    data() {

	        return {

	        	query : '',
	        	error : '',
    			perPage : 10,
	        	loading : false,
	        	
	        	currentSorting : '',
	        	currentTab : 'current',

	        	createMode : true,
	        	submitForm : true,

	        	ascending : false,
	      		descending : false,

	        	contentsToShow : [],
	        	allVariationTypes : [],
	        	allFetchedContents : [],

	        	pagination: {
		        	current_page: 1
		      	},

		      	// variationData : variationData,
	        	singleAssetData : singleAssetData,

	        	errors : {
					asset : {},
				},

	            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

	        }

		},
		
		created(){

			this.fetchAllContents();
			this.fetchAllVariationTypes();

		},

		filters: {

			capitalize: function (value) {
				if (!value) return ''
					value = value.toString()
				return value.charAt(0).toUpperCase() + value.slice(1)
			},

			capitalizeEachWord: function (value) {
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

		watch: {
			'singleAssetData.type': function (object) {
				if (object && Object.keys(object).length > 0) {
					this.singleAssetData.variation_type_id = object.id;
				}
			},
			'singleAssetData.parent': function (object) {
				if (object && Object.keys(object).length > 0) {
					if (object.id != this.singleAssetData.id) {
						this.submitForm = true;
						this.singleAssetData.variation_parent_id = object.id;
						this.$delete(this.errors.asset, 'variation_parent_id');
					}
					else {
						this.submitForm = false;
						this.errors.asset.variation_parent_id = 'Same type is invalid';
					}
				}
				else {
					this.$delete(this.singleAssetData, 'variation_parent_id');
				}
			},
		},
		
		methods : {

			fetchAllContents() {
				
				this.query = '';
				this.error = '';
				this.loading = true;
				this.allFetchedContents = [];
				
				axios
					.get('/api/variations/' + this.perPage + "?page=" + this.pagination.current_page)
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
			fetchAllVariationTypes() {

				this.query = '';
				this.error = '';
				this.loading = true;
				this.allVariationTypes = [];
				
				axios
					.get('/api/variation-types/')
					.then(response => {
						if (response.status == 200) {
							this.allVariationTypes = response.data.data;
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
				// this.pagination.current_page = 1;
				
				axios
				.get(
					"/api/search-variations/" + this.query + "/" + this.perPage + "?page=" + this.pagination.current_page
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
				this.singleAssetData = object;
				$('#asset-view-modal').modal('show');
			},
			showContentCreateForm() {
				this.createMode = true;
	        	this.submitForm = true;
	        	
				this.errors = {
					asset : {},
				};

				this.singleAssetData = {
					type : {}
				};

				$('#asset-createOrEdit-modal').modal('show');
			},
			openContentEditForm(object) {
				this.createMode = false;
				this.submitForm = true;

				this.errors = {
					asset : {},
				};

				this.singleAssetData = object;

				this.singleAssetData.type.variations = this.allVariationTypes.find(
					(variationType) => variationType.name == object.type.name && variationType.id == object.type.id
				).variations;

				$('#asset-createOrEdit-modal').modal('show');
			},
			openContentDeleteForm(object) {	
				this.singleAssetData = object;
				$('#delete-confirmation-modal').modal('show');
			},
			openContentRestoreForm(object) {	
				this.singleAssetData = object;
				$('#restore-confirmation-modal').modal('show');
			},
			storeAsset() {
				
				if (!this.verifyUserInput()) {
					this.submitForm = false;
					return;
				}

				axios
					.post('/variations/' + this.perPage, this.singleAssetData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("New variation has been created", "Success");
							
							this.pagination.current_page = 1; 
							this.allFetchedContents = response.data;
							this.query !== '' ? this.searchData() : this.showSelectedTabContents();

							$('#asset-createOrEdit-modal').modal('hide');
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
						this.fetchAllVariationTypes();
					});

			},
			updateAsset() {
				
				if (!this.verifyUserInput()) {
					this.submitForm = false;
					return;
				}

				axios
					.put('/variations/' + this.singleAssetData.id + '/' + this.perPage, this.singleAssetData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Variation has been updated", "Success");

							this.pagination.current_page = 1; 
							this.allFetchedContents = response.data;
							this.query !== '' ? this.searchData() : this.showSelectedTabContents();

							$('#asset-createOrEdit-modal').modal('hide');
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
						this.fetchAllVariationTypes();
					});

			},
			deleteAsset(singleAssetData) {
				
				axios
					.delete('/variations/' + this.singleAssetData.id + '/' + this.perPage)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Variation has been deleted", "Success");

							this.pagination.current_page = 1; 
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
					})
					.finally(response => {
						this.fetchAllVariationTypes();
					});

			},
			restoreAsset(singleAssetData) {
				
				axios
					.patch('/variations/' + this.singleAssetData.id + '/' + this.perPage)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Variation has been restored", "Success");

							this.pagination.current_page = 1; 
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
					})
					.finally(response => {
						this.fetchAllVariationTypes();
					});

			},
            changeNumberContents() {
				this.pagination.current_page = 1;

				if (this.query === '') {
					this.fetchAllContents();
				}
				else {
					this.searchData();
				}
    		},
    		showSelectedTabContents() {
				
				if (this.currentTab=='current') {
					this.contentsToShow = this.allFetchedContents.current ? this.allFetchedContents.current.data : [];
					this.pagination = this.allFetchedContents.current ?? {};
				}
				else {
					this.contentsToShow = this.allFetchedContents.trashed ? this.allFetchedContents.trashed.data : [];
					this.pagination = this.allFetchedContents.trashed ?? {};
				}

			},
			showCurrentContents() {
				this.currentTab = 'current';
				this.showSelectedTabContents();
			},
			showTrashedContents() {
				this.currentTab = 'trashed';
				this.showSelectedTabContents();
			},
			verifyUserInput() {
				this.validateFormInput('name');
				this.validateFormInput('variation_type');
				this.validateFormInput('variation_parent_id');

				if (this.errors.asset.constructor === Object && Object.keys(this.errors.asset).length == 0) {
					return true;
				}

				return false;
			},
			treeSelectCustomFunction(node) {
				return {
					id: node.id,
					label: node.name,
					children: node.childs,
				}
			},
			objectNameWithCapitalized ({ name }) {
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
		      	else 
		      		return ''
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
			validateFormInput (formInputName) {
				
				this.submitForm = false;

				switch(formInputName) {

					case 'name' :

						if (!this.singleAssetData.name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.asset.name = 'No special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.asset, 'name');
						}

						break;

					case 'variation_type' :

						if (!this.singleAssetData.type || Object.keys(this.singleAssetData.type).length == 0) {
							this.errors.asset.variation_type = 'Type is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.asset, 'variation_type');
						}

						break;

					case 'variation_parent_id' :

						if (this.singleAssetData.parent && Object.keys(this.singleAssetData.parent).length && (this.singleAssetData.parent.id == this.singleAssetData.id || this.singleAssetData.parent.name.toLowerCase() == this.singleAssetData.name.toLowerCase())) {
							this.errors.asset.variation_parent_id = 'Same type is invalid';
						}
						else if (this.singleAssetData.parent && this.singleAssetData.id && this.singleAssetData.id < this.singleAssetData.parent.id && this.singleAssetData.parent.variation_parent_id) {
							this.errors.asset.variation_parent_id = 'Newer child type cant be parent';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.asset, 'variation_parent_id');
						}

						break;

				}
	 
			}
            
		}
  	}

</script>

<style scoped>
	@import '~vue-multiselect/dist/vue-multiselect.min.css';
</style>