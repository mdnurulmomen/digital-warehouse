<template>	
	<!-- Modal -->
	<div class="modal fade" id="asset-createOrEdit-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">
						{{ createMode ? 'Create' : 'Edit' }} {{ callerPage | capitalize }}
						<!-- 
						<span v-show="!createMode">
							({{ singleAssetData.code }})
						</span>
						 -->
					</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
					
				<form 	
					class="form-horizontal" 
					v-on:submit.prevent="verifyUserInput" 
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
									:disabled="singleAssetData.spaces_count > 0"
									required="true" 
								>

								<div class="invalid-feedback">
						        	{{ errors.asset.name }}
						  		</div>
							</div>
						</div>
						
						
						<div class="form-row" v-if="currentRouteName=='rent-periods'">
							<div class="form-group col-md-12">
								<label for="inputUsername">Number Days</label>
								
								<div class="input-group mb-0">
									<input type="number" 
										class="form-control" 
										v-model="singleAssetData.number_days" 
										placeholder="Number should be unique" 
										:class="!errors.asset.number_days  ? 'is-valid' : 'is-invalid'" 
										@change="validateFormInput('number_days')" 
										:disabled="singleAssetData.spaces_count > 0" 
										required="true" 
										min="1" 
									>

									<div class="input-group-append">
										<span class="input-group-text">Days</span>
									</div>
								</div>								

								<div 
									style="display: block;" 
									class="invalid-feedback" 
									v-show="errors.asset.number_days"
								>
						        	{{ errors.asset.number_days }}
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
						<div class="col-sm-12 pl-0 pr-0">
							<button type="button" class="btn waves-effect waves-dark btn-secondary btn-outline-secondary float-left" data-dismiss="modal">
								Close
							</button>
							<button type="submit" class="btn waves-effect waves-dark btn-primary btn-outline-primary float-right" :disabled="! submitForm || formSubmitted">
								{{ createMode ? 'Save' : 'Update' }}
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript">
	
	export default {

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

		},

		data() {

			return {

				submitForm : true,
				
				errors : {
					asset : {},
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

            verifyUserInput() {

				this.validateFormInput('name');
				this.validateFormInput('number_days');
            	
            	if (Object.keys(this.errors.asset).length !== 0 && this.errors.asset.constructor === Object) {
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

					case 'name' :

						if (! this.singleAssetData.name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.asset.name = 'No space or special character';
						}
						else if (this.currentRouteName=='rent-periods' && ! this.singleAssetData.name.match(/^[a-zA-Z0-9-_]+$/)) {
							this.errors.asset.name = 'No space or special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.asset, 'name');
						}

						break;
					
					case 'number_days' :

						if (this.currentRouteName=='rent-periods' && (! this.singleAssetData.number_days || this.singleAssetData.number_days < 1)) {
							this.errors.asset.number_days = 'Day count is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.asset, 'number_days');
						}

						break;

				}
	 
			}

		}

	}

</script>