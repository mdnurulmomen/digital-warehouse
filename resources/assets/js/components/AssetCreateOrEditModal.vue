<template>
	
	<!-- Modal -->
	<div class="modal fade" id="asset-createOrEdit-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
									required="true" 
								>

								<div class="invalid-feedback">
						        	{{ errors.asset.name }}
						  		</div>
							</div>
						</div>
						<!-- 
						<div class="form-row">
							<div class="form-group col-md-12">
								<label for="inputUsername">Short Code (for recognizing)</label>
								<input type="text" 
									class="form-control" 
									v-model="singleAssetData.code" 
									placeholder="Code should be unique" 
									:class="!errors.asset.code  ? 'is-valid' : 'is-invalid'" 
									@change="validateFormInput('code')"
								>

								<div class="invalid-feedback">
						        	{{ errors.asset.code }}
						  		</div>
							</div>
						</div>
						 -->

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

		},

		data() {

			return {

				submitForm : true,
				
				errors : {
					asset : {},
				},

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
				// this.validateFormInput('code');
            	
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

						if (!this.singleAssetData.name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.asset.name = 'No space or special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.asset, 'name');
						}

						break;

					/*
					case 'code' :

						if (this.singleAssetData.code && !this.singleAssetData.code.match(/^[-\w\.\@]{3,}$/g)) {
							this.errors.asset.code = 'No special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.asset, 'code');
						}

						break;
					*/

				}
	 
			}

		}

	}

</script>