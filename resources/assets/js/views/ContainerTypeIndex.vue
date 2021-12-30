
<template v-if="userHasPermissionTo('view-warehouse-asset-index')">
	<div class="pcoded-content">
		<breadcrumb 
			:title="'containers'" 
			:message="'All our containers'"
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
											  		v-if="userHasPermissionTo('view-warehouse-asset-index') || userHasPermissionTo('create-warehouse-asset')" 
											  		:query="query" 
											  		:caller-page="'container'" 
											  		:disable-add-button="formSubmitted" 
											  		:required-permission="'warehouse-asset'" 
											  		
											  		@showContentCreateForm="showContentCreateForm" 
											  		@searchData="searchData" 
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

										  		<table-with-soft-delete-option 
										  			:query="query" 
										  			:per-page="perPage" 
										  			:form-submitted="formSubmitted"
										  			:column-names="['name', 'Recognizing Code']" 
										  			:column-values-to-show="['name', 'code']" 
										  			:contents-to-show = "contentsToShow" 
										  			:pagination = "pagination" 
										  			:required-permission="'warehouse-asset'" 
										  			:current-content="singleAssetData"

										  			@showContentDetails="showContentDetails" 
										  			@openContentEditForm="openContentEditForm" 
										  			@openContentDeleteForm="openContentDeleteForm" 
										  			@openContentRestoreForm="openContentRestoreForm" 
										  			@changeNumberContents="changeNumberContents" 
										  			@fetchAllContents="fetchAllContents" 
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
	
	<!-- 
		<asset-create-or-edit-modal 
			:create-mode="createMode" 
			:caller-page="'container'" 
			:single-asset-data="singleAssetData" 
			:csrf="csrf"

			@storeAsset="storeAsset" 
			@updateAsset="updateAsset" 
		></asset-create-or-edit-modal>
 	-->

 		<!-- modal-createOrEdit-container -->
		<div class="modal fade" id="container-createOrEdit-modal" v-if="userHasPermissionTo('create-warehouse-asset') || userHasPermissionTo('update-warehouse-asset')" >
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					
					<div class="modal-header">
					  	<h4 class="modal-title">
					  		{{ createMode ? 'Create' : 'Edit' }} Container
					  	</h4>
					  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					    	<span aria-hidden="true">&times;</span>
						</button>
					</div>
			  		<!-- form start -->
					<form 
						class="form-horizontal" 
						v-on:submit.prevent="createMode ? storeContainer() : updateContainer()" 
					>
						<div class="modal-body">

							<input type="hidden" name="_token" :value="csrf">							

				    		<div class="d-flex">
				    			<transition name="branches">
						    		<!-- container -->
					    			<div class="mr-1 p-2 border w-100">
							    		<div class="form-row">
											<div class="form-group col-md-12">
												<label for="inputFirstName">Name</label>
												<input type="text" 
													class="form-control" 
													v-model="singleAssetData.name" 
													placeholder="Name should be unique" 
													:class="!errors.container.name  ? 'is-valid' : 'is-invalid'" 
													@blur="validateFormInput('name')" 
													required="true" 
												>

												<div class="invalid-feedback">
										        	{{ errors.container.name }}
										  		</div>
											</div>
										</div>

										<div class="form-row">
											<div class="form-group col-md-12">
												<label for="inputFirstName">Recognizing Code</label>
												<input type="text" 
													class="form-control" 
													v-model="singleAssetData.code" 
													placeholder="Code should be unique" 
													:class="!errors.container.code  ? 'is-valid' : 'is-invalid'" 
													@blur="validateFormInput('code')" 
													:disabled="singleAssetData.warehouses_count > 0"
												>

												<div class="invalid-feedback">
										        	{{ errors.container.code }}
										  		</div>
											</div>
										</div>
						    		 
						    			<!-- container measurement -->
							    		<div class="form-row">
							    			<div class="col-sm-12 form-group">	
							    				<label for="phone">Cntnr. Length</label>
												
							    				<div class="input-group mb-0">
							    					<input type="number" 
														class="form-control" 
														v-model.number="singleAssetData.length" 
														placeholder="Lenght of container" 
														:class="!errors.container.length ? 'is-valid' : 'is-invalid'" 
														@blur="validateFormInput('container_length')" 
														required="true" 
													>

							    					<div class="input-group-append">
							    						<span class="input-group-text">
							    							{{ general_settings.default_measure_unit }}
							    						</span>
							    					</div>
							    				</div>

												<div 
													style="display: block;" 
													class="invalid-feedback" 
													v-show="errors.container.length"
												>
										        	{{ errors.container.length }}
										  		</div>
							    			</div>
							    		
							    			<div class="col-sm-12 form-group">	
							    				<label for="phone">Cntnr. Width</label>

							    				<div class="input-group mb-0">
													<input type="number" 
														class="form-control" 
														v-model.number="singleAssetData.width" 
														placeholder="Lenght of container" 
														:class="!errors.container.width ? 'is-valid' : 'is-invalid'" 
														@blur="validateFormInput('container_width')" 
														required="true" 
													>
							    					<div class="input-group-append">
							    						<span class="input-group-text">
							    							{{ general_settings.default_measure_unit }}
							    						</span>
							    					</div>
							    				</div>

												<div 
													style="display: block;" 
													class="invalid-feedback" 
													v-show="errors.container.width"
												>
										        	{{ errors.container.width }}
										  		</div>
							    			</div>
							    		
							    			<div class="col-sm-12 form-group">	
							    				<label for="phone">Cntnr. Height</label>

							    				<div class="input-group mb-0">
													<input type="number" 
														class="form-control" 
														v-model.number="singleAssetData.height" 
														placeholder="Lenght of container" 
														:class="!errors.container.height ? 'is-valid' : 'is-invalid'" 
														@blur="validateFormInput('container_height')" 
														required="true" 
													>
							    					<div class="input-group-append">
							    						<span class="input-group-text">
							    							{{ general_settings.default_measure_unit }}
							    						</span>
							    					</div>
							    				</div>

												<div 
													style="display: block;" 
													class="invalid-feedback" 
													v-show="errors.container.height"
												>
										        	{{ errors.container.height }}
										  		</div>
							    			</div>
							    		</div>
						    		
							    		<!-- container price -->
										<!-- 
							    		<div class="form-row">
							    			<div class="col-sm-12 form-group">	
							    				<label for="phone">Storing Price</label>
												<input type="number" 
													class="form-control" 
													v-model.number="singleAssetData.storing_price" 
													placeholder="Lenght of container" 
													:class="!errors.container.storing_price ? 'is-valid' : 'is-invalid'" 
													@blur="validateFormInput('container_storing_price')" 
													required="true" 
												>
												<div class="invalid-feedback">
										        	{{ errors.container.storing_price }}
										  		</div>
							    			</div>

							    			<div class="col-sm-12 form-group">	
							    				<label for="phone">Default Selling Price (e-commerce fulfillment)</label>
												<input type="number" 
													class="form-control" 
													v-model.number="singleAssetData.selling_price" 
													placeholder="Lenght of container" 
													:class="!errors.container.selling_price ? 'is-valid' : 'is-invalid'" 
													@blur="validateFormInput('container_selling_price')" 
													required="true" 
												>

												<div class="invalid-feedback">
										        	{{ errors.container.selling_price }}
										  		</div>
							    			</div>
							    		</div>
							    		 -->
						    		
					    				<!-- container has shelf -->
							    		<div class="form-row">
							    			<div class="col-sm-12 form-group text-center">
												<toggle-button 
													v-model="singleAssetData.has_shelve" 
													:width=150 
													:sync="true"
													:color="{checked: 'green', unchecked: 'red'}"
													:labels="{checked: 'Has Shelfs', unchecked: 'No Shelf'}" 
													:disabled="!createMode"
												/>
							    			</div>
							    		</div>
						    		
					    			</div>
					    		</transition>
					    		
					    		<transition name="branches">
					    			<!-- shelf -->
					    			<div class="mr-1 ml-1 p-2 border 100" v-if="singleAssetData.has_shelve">
							    		<!-- shelf price -->
										<!-- 
							    		<div class="form-row">
							    			<div class="col-sm-12 form-group">	
							    				<label for="phone">Default Storing Price</label>
												<input type="number" 
													class="form-control" 
													v-model.number="singleAssetData.shelf.storing_price" 
													placeholder="Lenght of container" 
													:class="!errors.container.shelf.storing_price ? 'is-valid' : 'is-invalid'" 
													@blur="validateFormInput('shelf_storing_price')" 
													required="true" 
												>
												<div class="invalid-feedback">
										        	{{ errors.container.shelf.storing_price }}
										  		</div>
							    			</div>
							    			<div class="col-sm-12 form-group">	
							    				<label for="phone">Default Selling Price</label>
												<input type="number" 
													class="form-control" 
													v-model.number="singleAssetData.shelf.selling_price" 
													placeholder="Lenght of container" 
													:class="!errors.container.shelf.selling_price ? 'is-valid' : 'is-invalid'" 
													@blur="validateFormInput('shelf_selling_price')" 
													required="true" 
												>
												<div class="invalid-feedback">
										        	{{ errors.container.shelf.selling_price }}
										  		</div>
							    			</div>
							    		</div>
							    		 -->
							    		<!-- quantity of shelf in each container -->
							    		<div class="form-row">
							    			<div class="col-sm-12 form-group">
							    				<label for="phone">Shelves Quantity</label>
												<input type="number" 
													class="form-control" 
													v-model.number="singleAssetData.shelf.quantity" 
													placeholder="Quantity of shelves" 
													:class="!errors.container.shelf.quantity ? 'is-valid' : 'is-invalid'" 
													@blur="validateFormInput('shelf_quantity')" 
													required="true" 
													:readonly="!createMode"
												>

												<div class="invalid-feedback">
										        	{{ errors.container.shelf.quantity }}
										  		</div>
							    			</div>
							    		</div>
							    		<!-- shelf has unit -->
							    		<div class="form-row">
							    			<div class="col-sm-12 form-group text-center">
							    				<toggle-button 
													v-model="singleAssetData.shelf.has_units" 
													:width=150 
													:sync="true"
													:color="{checked: 'green', unchecked: 'red'}"
													:labels="{checked: 'Has Units', unchecked: 'No Units'}" 
													:disabled="!createMode"
												/>
							    			</div>
							    		</div>
						    			
					    			</div>
					    		</transition>

					    		<transition name="branches">
					    			<!-- unit -->
					    			<div class="ml-1 p-2 border w-100" v-if="singleAssetData.has_shelve && singleAssetData.shelf.has_units">
							    		<!-- container price -->
						    			<!-- 
						    			<div class="col-sm-12 form-group">	
						    				<label for="phone">Default Storing Price</label>
											<input type="number" 
												class="form-control" 
												v-model.number="singleAssetData.shelf.unit.storing_price" 
												placeholder="Lenght of container" 
												:class="!errors.container.shelf.unit.storing_price ? 'is-valid' : 'is-invalid'" 
												@blur="validateFormInput('shelf_unit_storing_price')" 
												required="true" 
											>
											<div class="invalid-feedback">
									        	{{ errors.container.shelf.unit.storing_price }}
									  		</div>
						    			</div>

						    			<div class="col-sm-12 form-group">	
						    				<label for="phone">Default Selling Price (including e-commerce fulfillment)</label>
											<input type="number" 
												class="form-control" 
												v-model.number="singleAssetData.shelf.unit.selling_price" 
												placeholder="Lenght of container" 
												:class="!errors.container.shelf.unit.selling_price ? 'is-valid' : 'is-invalid'" 
												@blur="validateFormInput('shelf_unit_selling_price')" 
												required="true" 
											>

											<div class="invalid-feedback">
									        	{{ errors.container.shelf.unit.selling_price }}
									  		</div>
						    			</div>
										-->
						    			<!-- quantity of unit in each shelf -->
							    		<div class="form-row">
							    			<div class="col-sm-12 form-group">
							    				<label for="phone">Unit Quantity</label>
												<input type="number" 
													class="form-control" 
													v-model.number="singleAssetData.shelf.unit.quantity" 
													placeholder="Quantity of container" 
													:class="!errors.container.shelf.unit.quantity ? 'is-valid' : 'is-invalid'" 
													@blur="validateFormInput('unit_quantity')" 
													required="true" 
													:readonly="!createMode"
												>

												<div class="invalid-feedback">
										        	{{ errors.container.shelf.unit.quantity }}
										  		</div>
							    			</div>
							    		</div>
								    		
					    			</div>
					    		</transition>
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
								<button 
									type="submit" 
									class="btn btn-primary float-right" 
									:disabled="!submitForm || formSubmitted"
								>
									{{ createMode ? 'Save' : 'Update' }}
								</button>
							</div>
						</div>

					</form>
					<!-- form end -->
				</div>
				<!-- /.modal-content -->
			</div>
			<!-- /.modal-dialog -->
		</div>
		<!-- /modal-createOrEdit-container -->

 		<!-- View Modal -->
		<div class="modal fade" id="container-view-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">
							Container Details
						</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="modal-body text-center">	
								
						<div class="card">
							<div class="card-body">
								<h4 class="card-title">Container</h4>
								<div class="form-row">
									<label class="col-sm-6 col-form-label font-weight-bold text-right">Name :</label>
									<label class="col-sm-6 form-control-plaintext">{{ singleAssetData.name | capitalize }}</label>
								</div>

								<div class="form-row">
									<label class="col-sm-6 col-form-label font-weight-bold text-right">Recognizing Code :</label>
									<label class="col-sm-6 form-control-plaintext">{{ singleAssetData.code | capitalize }}</label>
								</div>

								<div class="form-row">
									<label class="col-sm-6 col-form-label font-weight-bold text-right">Length :</label>
									<label class="col-sm-6 form-control-plaintext">{{ singleAssetData.length + ' ' + general_settings.default_measure_unit }}</label>
								</div>

								<div class="form-row">
									<label class="col-sm-6 col-form-label font-weight-bold text-right">Width :</label>
									<label class="col-sm-6 form-control-plaintext">{{ singleAssetData.width + ' ' + general_settings.default_measure_unit }}</label>
								</div>

								<div class="form-row">
									<label class="col-sm-6 col-form-label font-weight-bold text-right">Length :</label>
									<label class="col-sm-6 form-control-plaintext">{{ singleAssetData.height + ' ' + general_settings.default_measure_unit }}</label>
								</div>

								<!-- 
								<div class="form-row">
									<label class="col-sm-6 col-form-label font-weight-bold text-right">Default Storing Price :</label>
									<label class="col-sm-6 form-control-plaintext">{{ singleAssetData.storing_price }}</label>
								</div>
								<div class="form-row">
									<label class="col-sm-6 col-form-label font-weight-bold text-right">Default Selling Price :</label>
									<label class="col-sm-6 form-control-plaintext">{{ singleAssetData.selling_price }}</label>
								</div>
								-->
								
								<div class="form-row">
									<label class="col-sm-6 col-form-label font-weight-bold text-right">Has Shelf :</label>
									<label class="col-sm-6 form-control-plaintext">
										<span :class="[singleAssetData.has_shelve ? 'badge-success' : 'badge-danger', 'badge']">{{ singleAssetData.has_shelve ? 'Available' : 'NA' }}</span>
									</label>
								</div>
							</div>
						</div>

						<!-- shelf -->
						<div class="card" v-if="singleAssetData.has_shelve">
							<div class="card-body">
								<h4 class="card-title">Container Shelf</h4>
								<div class="form-row">
									<label class="col-sm-6 col-form-label font-weight-bold text-right"># Shelves :</label>
									<label class="col-sm-6 form-control-plaintext">{{ singleAssetData.shelf.quantity }}</label>
								</div>
								<!-- 
								<div class="form-row">
									<label class="col-sm-6 col-form-label font-weight-bold text-right">Shelf Default Selling Price :</label>
									<label class="col-sm-6 form-control-plaintext">{{ singleAssetData.shelf.selling_price }}</label>
								</div>
								<div class="form-row">
									<label class="col-sm-6 col-form-label font-weight-bold text-right">Shelf Default Storing Price :</label>
									<label class="col-sm-6 form-control-plaintext">{{ singleAssetData.shelf.storing_price }}</label>
								</div>
								 -->
								<div class="form-row">
									<label class="col-sm-6 col-form-label font-weight-bold text-right">Has Units :</label>
									<label class="col-sm-6 form-control-plaintext">
										<span :class="[singleAssetData.shelf.has_units ? 'badge-success' : 'badge-danger', 'badge']">{{ singleAssetData.shelf.has_units ? 'Available' : 'NA' }}</span>
									</label>
								</div>
							</div>
						</div>

						<!-- unit -->
						<div class="card" v-if="singleAssetData.has_shelve && singleAssetData.shelf.has_units">
							<div class="card-body">
								<h4 class="card-title">Shelf Unit</h4>
								<div class="form-row">
									<label class="col-sm-6 col-form-label font-weight-bold text-right"># Units :</label>
									<label class="col-sm-6 form-control-plaintext">{{ singleAssetData.shelf.unit.quantity }}</label>
								</div>
								<!-- 
								<div class="form-row">
									<label class="col-sm-6 col-form-label font-weight-bold text-right">Unit Default Selling Price :</label>
									<label class="col-sm-6 form-control-plaintext">{{ singleAssetData.shelf.unit.selling_price }}</label>
								</div>
								<div class="form-row">
									<label class="col-sm-6 col-form-label font-weight-bold text-right">Unit Default Storing Price :</label>
									<label class="col-sm-6 form-control-plaintext">{{ singleAssetData.shelf.unit.storing_price }}</label>
								</div>
								 -->
							</div>
						</div>
					</div>

					<div class="modal-footer">
						<button type="button" class="btn btn-secondary btn-block btn-sm" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>

		<delete-confirmation-modal 
			v-if="userHasPermissionTo('delete-warehouse-asset')" 
			:csrf="csrf" 
			:form-submitted="formSubmitted"
			:submit-method-name="'deleteAsset'" 
			:content-to-delete="singleAssetData"
			:restoration-message="'But once you think, you can restore this item !'" 
			
			@deleteAsset="deleteAsset" 
		></delete-confirmation-modal>

		<restore-confirmation-modal 
			v-if="userHasPermissionTo('delete-warehouse-asset')" 
			:csrf="csrf" 
			:form-submitted="formSubmitted"
			:submit-method-name="'restoreAsset'" 
			:content-to-restore="singleAssetData"
			:restoration-message="'This will automatically be added to all related items !'" 

			@restoreAsset="restoreAsset" 
		></restore-confirmation-modal>

		<asset-view-modal 
			:caller-page="'container'" 
			:asset-to-view="singleAssetData" 
			:properties-to-show="['name']"
		></asset-view-modal>

	</div>

</template>

<script type="text/javascript">

	import axios from 'axios';

    let singleAssetData = {
    	shelf : {
    		unit : {},
    	},
    };

	export default {

	    data() {

	        return {

	        	query : '',
	        	error : '',
    			perPage : 10,
	        	loading : false,
	        	currentTab : 'current',

	        	submitForm : true,
	        	createMode : true,
	        	formSubmitted : false,

	        	allFetchedContents : [],
	        	contentsToShow : [],

	        	pagination: {
		        	current_page: 1
		      	},

	        	singleAssetData : singleAssetData,

	        	errors : {
	        		container : {
	        			shelf : {
	        				unit : {},
	        			},
	        		},
	        	},

	        	general_settings : JSON.parse(window.localStorage.getItem('general_settings')),

	            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

	        }

		},
		
		created(){

			this.fetchAllContents();

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
					.get('/api/containers/' + this.perPage + "?page=" + this.pagination.current_page)
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
			searchData(emitedValue=false) {

				if (emitedValue) {
					this.query=emitedValue;
				}

				this.error = '';
				this.allFetchedContents = [];
				this.pagination.current_page = 1;
				
				axios
				.get(
					"/api/search-containers/" + this.query + "/" + this.perPage + "?page=" + this.pagination.current_page
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
				$('#container-view-modal').modal('show');
			},
			showContentCreateForm() {
				this.submitForm = true;
				this.createMode = true;
				this.formSubmitted = false;
				
				this.singleAssetData = {
					shelf : {
						unit : {},
					},
				};

				this.errors = {
					container : {
						shelf : {
							unit : {},
						},
					},
				};

				$('#container-createOrEdit-modal').modal('show');
			},
			openContentEditForm(object) {
				this.submitForm = true;
				this.createMode = false;
				this.formSubmitted = false;

				this.singleAssetData = object;
				
				this.errors = {
					container : {
						shelf : {
							unit : {},
						},
					},
				};

				$('#container-createOrEdit-modal').modal('show');
			},
			openContentDeleteForm(object) {	
				this.formSubmitted = false;
				this.singleAssetData = object;
				$('#delete-confirmation-modal').modal('show');
			},
			openContentRestoreForm(object) {
				this.formSubmitted = false;
				this.singleAssetData = object;
				$('#restore-confirmation-modal').modal('show');
			},
			storeContainer() {
				
				this.formSubmitted = true;
				
				axios
					.post('/containers/' + this.perPage, this.singleAssetData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("New container has been created", "Success");
							this.allFetchedContents = response.data;
							this.query !== '' ? this.searchData() : this.showSelectedTabContents();
							$('#container-createOrEdit-modal').modal('hide');
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
			updateContainer() {
				
				this.formSubmitted = true;

				axios
					.put('/containers/' + this.singleAssetData.id + '/' + this.perPage, this.singleAssetData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Container has been updated", "Success");
							this.allFetchedContents = response.data;
							this.query !== '' ? this.searchData() : this.showSelectedTabContents();
							$('#container-createOrEdit-modal').modal('hide');
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
			deleteAsset(singleAssetData) {
				
				this.formSubmitted = true;

				axios
					.delete('/containers/' + singleAssetData.id + '/' + this.perPage, singleAssetData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Container has been deleted", "Success");
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
						this.formSubmitted = false;
					});

			},
			restoreAsset(singleAssetData) {
				
				this.formSubmitted = true;

				axios
					.patch('/containers/' + singleAssetData.id + '/' + this.perPage, singleAssetData)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Container has been restored", "Success");
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
						this.formSubmitted = false;
					});

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
			validateFormInput (formInputName) {
				
				this.submitForm = false;

				switch(formInputName) {

					case 'name' :

						if (!this.singleAssetData.name) {
							this.errors.container.name = 'Container name is required';
						}
						else if (!this.singleAssetData.name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.container.name = 'No special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.container, 'name');
						}

						break;

					case 'code' :

						if (this.singleAssetData.code && ! this.singleAssetData.code.match(/^[a-zA-Z0-9-_]+$/)) {
							this.errors.container.code = 'No space or special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.container, 'code');
						}

						break;	

				
					case 'container_length' :

						if (!this.singleAssetData.length || this.singleAssetData.length < 0) {
							this.errors.container.length = 'Container length is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.container, 'length');
						}

						break;

					case 'container_width' :

						if (!this.singleAssetData.width || this.singleAssetData.width < 0) {
							this.errors.container.width = 'Container width is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.container, 'width');
						}

						break;


					case 'container_height' :

						if (!this.singleAssetData.height || this.singleAssetData.height < 0) {
							this.errors.container.height = 'Container height is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.container, 'height');
						}

						break;

					case 'shelf_quantity' :

						if (this.singleAssetData.has_shelve && (!this.singleAssetData.shelf.quantity || this.singleAssetData.shelf.quantity < 1)) {
							this.errors.container.shelf.quantity = 'Shelf quantity is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.container.shelf, 'quantity');
						}

						break;

					case 'unit_quantity' :

						if (this.singleAssetData.shelf.has_units && (!this.singleAssetData.shelf.unit.quantity || this.singleAssetData.shelf.unit.quantity < 1)) {
							this.errors.container.shelf.unit.quantity = 'Unit quantity is required';
						}
						else{
							this.submitForm = true;
							// this.errors.container.shelf.unit.quantity = null;
							this.$delete(this.errors.container.shelf.unit, 'quantity');
						}

						break;
				

				/*
					case 'container_storing_price' :						

						if (!this.singleAssetData.storing_price || this.singleAssetData.storing_price < 0) {
							this.errors.container.storing_price = 'Storing price is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.container, 'storing_price');
						}

						break;

					case 'container_selling_price' :

						if (!this.singleAssetData.selling_price || this.singleAssetData.selling_price < 0) {
							this.errors.container.selling_price = 'Selling price is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.container, 'selling_price');
						}

						break;

					case 'shelf_storing_price' :

						if (this.singleAssetData.has_shelve && (!this.singleAssetData.shelf.storing_price || this.singleAssetData.shelf.storing_price < 0)) {
							this.errors.container.shelf.storing_price = 'Storing price is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.container.shelf, 'storing_price');
						}

						break;

					case 'shelf_selling_price' :

						if (this.singleAssetData.has_shelve && (!this.singleAssetData.shelf.selling_price || this.singleAssetData.shelf.selling_price < 0)) {
							this.errors.container.shelf.selling_price = 'Selling price is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.container.shelf, 'selling_price');
						}

						break;					

					case 'shelf_unit_selling_price' :

						if (this.singleAssetData.shelf.has_units && (!this.singleAssetData.shelf.unit.selling_price || this.singleAssetData.shelf.unit.selling_price < 0)) {
							this.errors.container.shelf.unit.selling_price = 'Selling price is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.container.shelf.unit, 'selling_price');
						}

						break;

					case 'shelf_unit_storing_price' :

						if (this.singleAssetData.shelf.has_units && (!this.singleAssetData.shelf.unit.storing_price || this.singleAssetData.shelf.unit.storing_price < 0)) {
							this.errors.container.shelf.unit.storing_price = 'Storing price is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.container.shelf.unit, 'storing_price');
						}

						break;
					
				*/
					

				}
	 
			},
            
		}
  	}

</script>

<style scoped>
	.branches-enter-active {
  		transition: all 1s ease;
	}
	.branches-leave-active {
  		transition: all 1s cubic-bezier(1.0, 0.5, 0.8, 1.0);
	}
	.branches-enter, .branches-leave-to {
  		transform: translateX(10px);
  		opacity: 0;
	}
</style>