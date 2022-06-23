<template>	
	<!-- modal-createOrEdit-container -->
	<div class="modal fade" id="container-createOrEdit-modal" data-backdrop="static" data-keyboard="false" v-if="userHasPermissionTo('create-warehouse-asset') || userHasPermissionTo('update-warehouse-asset')" >
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
					v-on:submit.prevent="verifyUserInput" 
				>
					<div class="modal-body">

						<input type="hidden" name="_token" :value="csrf">							

			    		<div class="d-flex">
			    			<transition name="branches">
					    		<!-- container -->
				    			<div class="mr-1 p-2 border w-100">
						    		<div class="form-row">
										<div class="form-group col-md-12">
											<label for="inputFirstName">Storage Type</label>
											
											<multiselect 
		                              			v-model="singleAssetData.storage_type" 
		                              			class="form-control p-0" 
		                              			:class="errors.container.storage_type ? 'is-invalid' : 'is-valid'"
		                              			placeholder="Container Storage Type" 
		                                  		:custom-label="nameWithCapitalized" 
		                                  		:options="allStorageTypes" 
		                                  		:required="true" 
		                                  		:allow-empty="false" 
		                                  		@input="setContainerStorageType()" 
		                                  		@close="validateFormInput('storage_type')" 
		                              		>
		                                	</multiselect>

											<div 
												class="invalid-feedback" 
												style="display: block;" 
												v-show="errors.container.storage_type"
											>
									        	{{ errors.container.storage_type }}
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
</template>

<script type="text/javascript">

	import Multiselect from 'vue-multiselect';
	
	export default {

		components:{
	    	multiselect : Multiselect
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
			callerPage : {
				type : String,
				required : true
			},
			singleAssetData : {
				type : Object,
				required : true
			},
			formSubmitted : {
				type : Boolean,
				default : false
			},
			general_settings : {
				type : Object,
				required : true
			},
			allStorageTypes : {
				type : Array,
				required : true
			}

		},

		data() {

			return {

				// allStorageTypes : [],

				submitForm : true,
				
				errors : {
	        		container : {
	        			shelf : {
	        				unit : {},
	        			},
	        		},
	        	},

			}

		},

		computed: {

		    currentRouteName() {

		        return this.$route.name;
		    
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

            setContainerStorageType(){

            	if (Object.keys(this.singleAssetData.storage_type).length > 0) {

            		this.singleAssetData.storage_type_id = this.singleAssetData.storage_type.id;

            	}

            },
            nameWithCapitalized ({ name }) {
				
				if (!name) return ''

				const words = name.split(" ");

				for (let i = 0; i < words.length; i++) {
				    words[i] = words[i][0].toUpperCase() + words[i].substr(1);
				}

				return words.join(" ");

		    },
            verifyUserInput() {

				this.validateFormInput('name');
				this.validateFormInput('code');
				this.validateFormInput('storage_type');
				this.validateFormInput('container_length');
				this.validateFormInput('container_width');
				this.validateFormInput('container_height');
				this.validateFormInput('shelf_quantity');
				this.validateFormInput('unit_quantity');
            	
            	if ((this.errors.container.constructor === Object && Object.keys(this.errors.container).length > 1) || (this.errors.container.shelf.constructor === Object && Object.keys(this.errors.container.shelf).length > 1) || (this.errors.container.shelf.unit.constructor === Object && Object.keys(this.errors.container.shelf.unit).length > 0)) {
					this.submitForm = false;
					return;
				}
				else {

	            	if (this.createMode) {
	            		
	            		this.$emit('storeAsset', this.singleAssetData);
	            	}

	            	else {

	            		this.$emit('updateAsset', this.singleAssetData)
	            	}

				}

            },
			validateFormInput (formInputName) {
				
				this.submitForm = false;

				switch(formInputName) {

					case 'storage_type' :

						if (! this.singleAssetData.storage_type) {
							this.errors.container.storage_type = 'Storage name is required';
						}
						else if (! Object.keys(this.singleAssetData.storage_type).length > 0 || ! this.singleAssetData.storage_type_id) {
							this.errors.container.storage_type = 'Storage name is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.container, 'storage_type');
						}

						break;

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

						if (! this.singleAssetData.code) {
							this.errors.container.code = 'Code is required';
						}
						else if (! this.singleAssetData.code.match(/^[a-zA-Z0-9-_]+$/)) {
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

						if (this.singleAssetData.has_shelve && this.singleAssetData.shelf.has_units && (!this.singleAssetData.shelf.unit.quantity || this.singleAssetData.shelf.unit.quantity < 1)) {
							this.errors.container.shelf.unit.quantity = 'Unit quantity is required';
						}
						else{
							this.submitForm = true;
							// this.errors.container.shelf.unit.quantity = null;
							this.$delete(this.errors.container.shelf.unit, 'quantity');
						}

						break;

				}
	 
			}

		}

	}

</script>