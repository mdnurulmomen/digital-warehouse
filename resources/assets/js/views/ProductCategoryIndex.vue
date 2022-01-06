
<template v-if="userHasPermissionTo('view-product-asset-index')">
	<div class="pcoded-content">
		<breadcrumb 
			:icon="'fa fa-list-alt'"
			:title="'product categories'" 
			:message="'All our product categories'"
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
											  		v-if="userHasPermissionTo('view-product-asset-index') || userHasPermissionTo('create-product-asset')" 
											  		:query="query" 
											  		:caller-page="'product category'" 
											  		:required-permission = "'product-asset'" 
											  		
											  		@showContentCreateForm="showContentCreateForm" 
											  		@searchData="searchData($event)" 
											  		@fetchAllContents="fetchAllContents"
											  	></search-and-addition-option>
											</div>
											
											<div class="col-sm-12 col-lg-12">
										  		<tab 
										  			v-show="query === ''" 
										  			:tab-names="['current', 'trashed']" 
										  			:current-tab="currentTab" 

										  			@showCurrentContents="showCurrentContents" 
										  			@showTrashedContents="showTrashedContents" 
										  		></tab>

										  		<!-- 
										  		<table-with-soft-delete-option 
										  			:query="query" 
										  			:per-page="perPage"  
										  			:column-names="['name', 'parents', '# products']" 
										  			:column-values-to-show="['name', 'parents', 'products_count']" 
										  			:contents-to-show = "contentsToShow" 
										  			:pagination = "pagination" 
										  			:required-permission="'product-asset'" 

										  			@showContentDetails="showContentDetails($event)" 
										  			@openContentEditForm="openContentEditForm($event)" 
										  			@openContentDeleteForm="openContentDeleteForm($event)" 
										  			@openContentRestoreForm="openContentRestoreForm($event)" 
										  			@changeNumberContents="changeNumberContents($event)" 
										  			@fetchAllContents="fetchAllContents" 
										  			@searchData="searchData" 
										  			@goCategoryProducts="goCategoryProducts($event)" 
										  		>	
										  		</table-with-soft-delete-option>
 												-->

 												<loading v-show="loading"></loading>

										  		<div class="tab-content card-block pl-0 pr-0" v-show="!loading">
													<div class="table-responsive">
														<table class="table table-striped table-bordered nowrap text-center">
															<thead>
																<tr>
																	<th>
																		<a href="javascript:void(0)" @click="changeContentsOrder('name')"> 
																			Name
																			<span 
																				v-show="currentSorting==='name' && ascending"
																			>
																				<i class="fa fa-sort-up" aria-hidden="true"></i>
																			</span>

																			<span 
																				v-show="currentSorting === 'name' && descending"
																			>
																				<i class="fa fa-sort-down" aria-hidden="true"></i>
																			</span>

																			<span 
																				v-show="currentSorting !== 'name'"
																			>
																				<i class="fa fa-sort" aria-hidden="true" style="opacity: 0.4;"></i>
																			</span>
																		</a>
																	</th>

																	<th>
																		Parents
																	</th>

																	<th>
																		<a href="javascript:void(0)" @click="changeContentsOrder('products_count')"> 
																			# Products
																			<span 
																				v-show="currentSorting==='products_count' && ascending"
																			>
																				<i class="fa fa-sort-up" aria-hidden="true"></i>
																			</span>

																			<span 
																				v-show="currentSorting === 'products_count' && descending"
																			>
																				<i class="fa fa-sort-down" aria-hidden="true"></i>
																			</span>

																			<span 
																				v-show="currentSorting !== 'products_count'"
																			>
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
																	v-for="(content, contentIndex) in contentsToShow" 
																	:key="'category-index-' + contentIndex + '-category-' + content.id" 
																	:class="content.id==singleAssetData.id ? 'highlighted' : ''"
																>
																	<td> {{ content.name | capitalize }} </td>
																	
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
																		{{ content.products_count }} 
																	</td>
																	
																	<td>
																		<button type="button" 
																				class="btn btn-grd-info btn-icon" 
																				data-toggle="tooltip" data-placement="top" title="View Details"  
																				@click="showContentDetails(content)" 
																		>
																			<i class="fa fa-eye"></i>
																		</button>

																		<button type="button" 
																				class="btn btn-grd-primary btn-icon" 
																				data-toggle="tooltip" data-placement="top" title="Edit" 
																				v-show="! content.deleted_at" 
																				@click="openContentEditForm(content)" 
																				v-if="userHasPermissionTo('update-product-asset')"
																		>
																			<i class="fa fa-edit"></i>
																		</button>

																		<button type="button" 
																				class="btn btn-grd-danger btn-icon" 
																				data-toggle="tooltip" data-placement="top" title="Delete" 
																				v-show="! content.deleted_at" 
																				@click="openContentDeleteForm(content)" 
																				v-if="userHasPermissionTo('delete-product-asset')" 
																		>
																			<i class="fa fa-trash"></i>
																		</button>

																		<button type="button" 
																				class="btn btn-grd-warning btn-icon" 
																				data-toggle="tooltip" data-placement="top" title="Restore" 
																				v-show="content.deleted_at" 
																				@click="openContentRestoreForm(content)" 
																				v-if="userHasPermissionTo('delete-product-asset')"
																		>
																			<i class="fa fa-undo"></i>
																		</button>

																		<button type="button" 
																				class="btn btn-grd-warning btn-icon" 
																				data-toggle="tooltip" data-placement="top" title="Products" 
																				@click="goCategoryProducts(content)" 
																				v-if="userHasPermissionTo('view-product-index')"
																		>
																			<i class="fab fa-product-hunt"></i>
																		</button>
																	</td>
																</tr>
																
																<tr 
															  		v-show="! contentsToShow.length"
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
																	<th>
																		<a href="javascript:void(0)" @click="changeContentsOrder('name')"> 
																			Name
																			<span 
																				v-show="currentSorting==='name' && ascending"
																			>
																				<i class="fa fa-sort-up" aria-hidden="true"></i>
																			</span>

																			<span 
																				v-show="currentSorting === 'name' && descending"
																			>
																				<i class="fa fa-sort-down" aria-hidden="true"></i>
																			</span>

																			<span 
																				v-show="currentSorting !== 'name'"
																			>
																				<i class="fa fa-sort" aria-hidden="true" style="opacity: 0.4;"></i>
																			</span>
																		</a>
																	</th>

																	<th>
																		Parents
																	</th>

																	<th>
																		<a href="javascript:void(0)" @click="changeContentsOrder('products_count')"> 
																			# Products
																			<span 
																				v-show="currentSorting==='products_count' && ascending"
																			>
																				<i class="fa fa-sort-up" aria-hidden="true"></i>
																			</span>

																			<span 
																				v-show="currentSorting === 'products_count' && descending"
																			>
																				<i class="fa fa-sort-down" aria-hidden="true"></i>
																			</span>

																			<span 
																				v-show="currentSorting !== 'products_count'"
																			>
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
																data-toggle="tooltip" data-placement="top" title="Reload" 
																@click="query === '' ? fetchAllContents() : searchData()"
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
		<div class="modal fade" id="asset-createOrEdit-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" v-if="userHasPermissionTo('create-product-asset') || userHasPermissionTo('update-product-asset')">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">
							{{ createMode ? 'Create' : 'Edit' }} Category
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
									<label for="inputFirstName">Name</label>
									<input type="text" 
										class="form-control" 
										v-model="singleAssetData.name" 
										placeholder="Name should be unique" 
										:class="!errors.asset.name  ? 'is-valid' : 'is-invalid'" 
										@change="validateFormInput('name')" 
										required="true" 
									>

									<div class="invalid-feedback">
							        	{{ errors.asset.name }}
							  		</div>
								</div>
							</div>

							<div class="form-row" v-if="categoriesToShow.length">
								<div class="form-group col-md-12">
									<label for="inputUsername">Parent Category</label>
									<!-- 
									<multiselect 
                              			v-model="singleAssetData.parent"
                              			placeholder="Parent Category" 
                                  		label="name" 
                                  		track-by="id" 
                                  		class="form-control p-0 is-valid" 
                                  		:custom-label="objectNameWithCapitalized" 
                                  		:options="categoriesToShow" 
                                  		:required="true" 
                                  		:allow-empty="false"
                                  		selectLabel = "Press/Click"
                                  		deselect-label="Can't remove single value"
                              		>
                                	</multiselect> 
                                	-->

                                	<treeselect
                                		v-model="singleAssetData.parent" 
										:options="categoriesToShow"
										:show-count="true" 
										:normalizer="treeSelectCustomFunction" 
										:valueFormat="'object'"
										@select="validateFormInput('parent_category_id')" 
										class="form-control p-0 is-valid" 
										placeholder="Parent Category"
									/>

									<div 
										class="invalid-feedback" 
										style="display: block;"
										v-show="errors.asset.parent_category_id" 
									>
							        	{{ errors.asset.parent_category_id }}
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
		<div class="modal fade" id="asset-view-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Category Details</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">

						<div class="form-row"> 
						    <div class="form-group col-md-6 text-right">
								<label class="font-weight-bold">Name:</label>
							</div>
							<div class="form-group col-md-6 text-left">
								{{ singleAssetData.name | capitalize }}
							</div>
						</div>

						<div class="form-row"> 
						    <div class="form-group col-md-6 text-right">
								<label class="font-weight-bold">Parent Category:</label>
							</div>
							<div class="form-group col-md-6 text-left">
								{{ singleAssetData.parent ? singleAssetData.parent.name : '--' | capitalize }}
							</div>
						</div>

					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary btn-block btn-sm" data-dismiss="modal">Close</button>
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
    	// parent : {},
    };

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

	        	ascending : false,
	      		descending : false,
	      		currentSorting : '',
	        	currentTab : 'current',

	        	createMode : true,
	        	submitForm : true,

	        	allCategories : [],
	        	categoriesToShow : [],

	        	allFetchedContents : [],
	        	contentsToShow : [],

	        	pagination: {
		        	current_page: 1
		      	},

	        	singleAssetData : singleAssetData,

	        	errors : {
					asset : {},
				},

	            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

	        }

		},
		
		created(){

			this.fetchAllContents();
			this.fetchProductAllCategories();			

		},

		watch: {
			'singleAssetData.parent': function (object) {
				if (object && Object.keys(object).length > 0) {
					this.singleAssetData.parent_category_id = object.id;
					this.validateFormInput('parent_category_id');
				}
			},
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

			fetchAllContents() {
				
				this.query = '';
				this.error = '';
				this.loading = true;
				this.allFetchedContents = [];
				
				axios
					.get('/api/product-categories/' + this.perPage + "?page=" + this.pagination.current_page)
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
			fetchProductAllCategories() {
				
				this.query = '';
				this.error = '';
				this.loading = true;
				this.allCategories = [];
				
				axios
					.get('/api/product-categories/')
					.then(response => {
						if (response.status == 200) {
							this.allCategories = response.data.data;
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
					"/api/search-product-categories/" + this.query + "/" + this.perPage + "?page=" + this.pagination.current_page
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
	        	
				this.categoriesToShow = [ ...this.allCategories ];

				this.errors = {
					asset : {},
				};

				this.singleAssetData = {
					// parent : {},
				};

				$('#asset-createOrEdit-modal').modal('show');
			},
			openContentEditForm(object) {
				this.submitForm = true;
				this.createMode = false;

				this.categoriesToShow = [ ...this.allCategories ];

				if (this.categoriesToShow.findIndex(category => category.id==object.id && category.name==object.name) > -1) {

					this.categoriesToShow.splice(
						this.categoriesToShow.findIndex(
							category => category.id==object.id && category.name==object.name
						), 1
					);

				}

				this.errors = {
					asset : {},
				};

				this.singleAssetData = object;

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
					.post('/product-categories/' + this.perPage, this.singleAssetData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("New category has been created", "Success");

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
						this.fetchProductAllCategories();
					});

			},
			updateAsset() {
				
				if (!this.verifyUserInput()) {
					this.submitForm = false;
					return;
				}

				axios
					.put('/product-categories/' + this.singleAssetData.id + '/' + this.perPage, this.singleAssetData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Category has been updated", "Success");

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
						this.fetchProductAllCategories();
					});

			},
			deleteAsset(singleAssetData) {
				
				axios
					.delete('/product-categories/' + singleAssetData.id + '/' + this.perPage, singleAssetData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Category has been deleted", "Success");

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
						this.fetchProductAllCategories();
					});

			},
			restoreAsset(singleAssetData) {
				
				axios
					.patch('/product-categories/' + singleAssetData.id + '/' + this.perPage, singleAssetData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Category has been restored", "Success");

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
						this.fetchProductAllCategories();
					});

			},
			goCategoryProducts(productCategory) {
				// console.log(object);
				this.$router.push({ name: 'category-products', params: { category: productCategory, categoryName: productCategory.name.replace(/ /g,"-") }});
			},
            changeNumberContents(expectedContentsPerPage) {
				this.pagination.current_page = 1;
				this.perPage = expectedContentsPerPage;

				if (this.query === '') {
					this.fetchAllContents();
				}
				else {
					this.searchData();
				}
    		},
    		showSelectedTabContents() {
				
				if (this.currentTab=='current') {
					this.contentsToShow = this.allFetchedContents.current.data;
					this.pagination = this.allFetchedContents.current;
				}
				else {
					this.contentsToShow = this.allFetchedContents.trashed.data;
					this.pagination = this.allFetchedContents.trashed;
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
				this.validateFormInput('parent_category_id');

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
				    name = name.toString()
				    return name.charAt(0).toUpperCase() + name.slice(1)
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

						this.contentsToShow.some(nameExists) ? this.descendingAlphabets('name') : this.contentsToShow.some(firstNameExists) ? this.descendingAlphabets('first_name') : this.descendingAlphabets('last_name');

					}
					else if (this.descending) {

						this.ascending = true;
						this.descending = false;

						this.contentsToShow.some(nameExists) ? this.ascendingAlphabets('name') : this.contentsToShow.some(firstNameExists) ? this.ascendingAlphabets('first_name') : this.ascendingAlphabets('last_name');

					}
					else {

						this.ascending = true;
						this.descending = false;

						this.contentsToShow.some(nameExists) ? this.ascendingAlphabets('name') : this.contentsToShow.some(firstNameExists) ? this.ascendingAlphabets('first_name') : this.ascendingAlphabets('last_name');

					}

				}

				else if (columnName.match(/products_count/gi)) {
					
					if (this.ascending) {
						this.ascending = false;
						this.descending = true;
						this.descendingNumeric('products_count');
					}
					else if (this.descending) {
						this.ascending = true;
						this.descending = false;
						this.ascendingNumeric('products_count');
					}
					else {
						this.ascending = true;
						this.descending = false;
						this.ascendingNumeric('products_count');
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
						return a[columnValue] - b[columnValue];
					}
				);
			},
			descendingNumeric(columnValue) {
				this.contentsToShow.sort(
			 		function(a, b){
						return b[columnValue] - a[columnValue];
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

					case 'parent_category_id' :

						if (this.singleAssetData.parent && this.singleAssetData.parent.id==this.singleAssetData.id && this.singleAssetData.parent.name==this.singleAssetData.name) {
							this.errors.asset.parent_category_id = 'Parent category cant be same';
						}
						else if (this.singleAssetData.parent && this.singleAssetData.id && this.singleAssetData.id < this.singleAssetData.parent.id && this.singleAssetData.parent.parent_category_id) {
							this.errors.asset.parent_category_id = 'Newer child category cant be parent';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.asset, 'parent_category_id');
						}

						break;

				}
	 
			},
			/*
			selectedCategoryChilds(currentCategoryId, categoriesToSearch = []) {

				if (categoriesToSearch.some(categoryToSearch => categoryToSearch.id==currentCategoryId)) {
					
					return categoriesToSearch.find(categoryToSearch => categoryToSearch.id == currentCategoryId).childs;

				}
				else {

					for (var i = categoriesToSearch.length - 1; i >= 0; i--) {
						
						let currentCategoryChilds = this.selectedCategoryChilds(currentCategoryId, categoriesToSearch[i].childs);

						if (Array.isArray(currentCategoryChilds)) {

							return currentCategoryChilds;

						}

					}

					return false;

				}

			},
			checkCategoryInvalidParent() {

				let currentCategoryChilds = this.selectedCategoryChilds(this.singleAssetData.id, this.allCategories);

				if (Array.isArray(currentCategoryChilds) && currentCategoryChilds.length && this.singleAssetData.parent) {

					return currentCategoryChilds.some(childToCheck => childToCheck.id==this.singleAssetData.parent.id);

				}

				return false;

			}
			*/
            
		}
  	}

</script>

<style scoped>
	@import '~vue-multiselect/dist/vue-multiselect.min.css';
</style>