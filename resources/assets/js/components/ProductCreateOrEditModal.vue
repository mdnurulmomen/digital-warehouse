<template>
	<div class="modal fade" id="product-createOrEdit-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" v-if="userHasPermissionTo('create-product') || userHasPermissionTo('update-product')">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">
						{{ createMode ? 'Store' : 'Update' }} Product
					</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<form 	
					class="form-horizontal" 
					v-on:submit.prevent="verifyUserInput()" 
					autocomplete="off" 
					novalidate="true" 
				>
					<input type="hidden" name="_token" :value="csrf">

				<div class="modal-body">
					<div class="form-row">
						<div class="form-group col-md-6">
							<label for="inputUsername">Product Type</label>
							<multiselect 
								v-model="productMode" 
								class="form-control p-0 is-valid" 
								placeholder="Product Mode" 
								:custom-label="nameWithCapitalized" 
								:options="['bulk product', 'retail product']" 
								:required="true" 
								:allow-empty="false" 
								@input="setProductMode()" 
								:disabled="singleProductData.product_immutability" 
								>
							</multiselect>
						</div>

						<div class="form-group col-md-6">
							<label for="inputUsername">Product Category</label>

							<treeselect
							v-model="singleProductData.category"
							class="form-control p-0" 
							placeholder="Select Variation" 
							:class="! errors.product_category ? 'is-valid' : 'is-invalid'"
							:options="allProductCategories" 
							:show-count="true" 
							:normalizer="treeSelectCustomFunction" 
							:valueFormat="'object'" 
							:required="true" 
							:disabled="productMode=='bulk product'" 
							@input="setProductCategory()"
							/>

							<div class="invalid-feedback">
								{{ errors.product_category }}
							</div>
						</div>
					</div>

				<div class="form-row">
					<div class="form-group col-md-6">
						
						<label for="inputFirstName">Name</label>
						<input type="text" 
						class="form-control" 
						v-model="singleProductData.name" 
						placeholder="Name should be unique" 
						:class="!errors.product_name ? 'is-valid' : 'is-invalid'" 
						@change="validateFormInput('product_name')" 
						required="true" 
						>

						<div class="invalid-feedback">
							{{ errors.product_name }}
						</div>
					</div>

					<div class="form-group col-md-6">
						<label for="inputFirstName">Qty Type</label>
						<input type="text" 
						class="form-control" 
						v-model="singleProductData.quantity_type" 
						:placeholder="productMode=='bulk product' ? 'Box / Cartoon' : 'Kg / Meter'" 
						:class="! errors.product_quantity_type ? 'is-valid' : 'is-invalid'" 
						@change="validateFormInput('product_quantity_type')" 
						required="true" 
						>

						<div class="invalid-feedback">
							{{ errors.product_quantity_type }}
						</div>
					</div>
				</div>

				<div class="form-row">
					<div class="col-md-12">
						<div class="card">
							<div class="card-body">
								<div class="form-row d-flex align-items-center text-center">
									<div class="form-group col-md-6">
										<img 
										width="100px" 
										class="img-fluid" 
										:src="singleProductData.preview || ''"
										alt="Product Picture" 
										>
									</div>
									<div class="form-group col-md-6">
										<div class="custom-file">
											<input type="file" 
											class="form-control custom-file-input" 
											:class="!errors.preview  ? 'is-valid' : 'is-invalid'" 
											@change="onProductPreviewChange" 
											accept="image/*"
											>
											<label class="custom-file-label" for="validatedCustomFile">Choose Picture...</label>
											<div class="invalid-feedback">
												{{ errors.preview }}
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="form-control form-group">
					<div class="form-row mt-2">
						<div class="col-md-12 text-center">
							<toggle-button 
							v-model="singleProductData.has_serials" 
							:width=200 
							:sync="true"
							:color="{checked: 'orange', unchecked: 'green'}"
							:labels="{checked: 'Has Serial', unchecked: 'No Serial'}" 
							:disabled="productMode=='bulk product' || singleProductData.product_immutability" 
							/>
						</div>
					</div>
				</div>

				<div class="form-control form-group">
					<div class="form-row mt-3">
						<div class="form-group col-md-12 text-center">
							<toggle-button 
							v-model="singleProductData.has_variations" 
							:width=200 
							:sync="true"
							:color="{checked: 'green', unchecked: 'blue'}"
							:labels="{checked: 'Has Variation', unchecked: 'No Variation'}" 
							:disabled="productMode=='bulk product' || singleProductData.product_immutability || allVariationTypes.length==0" 
							@change="resetProductVariations()"
							/>
						</div>
					</div>

					<div class="form-row" v-show="singleProductData.has_variations">
						<div class="form-group col-md-3">
							<label for="inputFirstName">Variation Type</label>
							<multiselect 
								v-model="singleProductData.variation_type"
								placeholder="Choose Type" 
								label="name" 
								track-by="id" 
								:custom-label="objectNameWithCapitalized" 
								:options="allVariationTypes" 
								:required="true" 
								:allow-empty="false"
								selectLabel = "Press/Click"
								deselect-label="Can't remove single value" 
								class="form-control p-0" 
								:class="!errors.product_variation_type  ? 'is-valid' : 'is-invalid'" 
								:disabled="singleProductData.product_immutability"
								@close="validateFormInput('product_variation_type')" 
								>
							</multiselect>
						<div class="invalid-feedback">
							{{ errors.product_variation_type }}
						</div>
					</div>

					<div 
					class="form-group col-md-9" 
					v-if="singleProductData.variations && singleProductData.variations.length"
					>
					<div 
					class="card" 
					v-for="(productVariation, index) in singleProductData.variations" 
					:key="'product-variation-index' + index + 'A'"
					>
					<div class="card-body">
						<div 
						class="form-row" 
						v-if="singleProductData.variations.length == errors.variations.length && singleProductData.variation_type && singleProductData.variation_type.variations"
						>
						<div class="form-group col-md-12">
							<div class="form-row">
								<div class="col-sm-12">
									<label for="inputFirstName">Variation</label>

									<treeselect
										v-model="productVariation.variation"
										:options="singleProductData.variation_type.variations" 
										:show-count="true" 
										:normalizer="treeSelectCustomFunction" 
										:valueFormat="'object'" 
										:required="true" 
										:disabled="productVariation.variation_immutability" 
										@input="validateFormInput('product_variation_id')"  
										class="form-control p-0" 
										:class="! errors.variations[index].product_variation_id ? 'is-valid' : 'is-invalid'"
										placeholder="Select Variation" 
									/>

									<div class="invalid-feedback">
										{{ errors.variations[index].product_variation_id }}
									</div>
								</div>
							</div>
						</div>

						<div class="form-group col-md-12">
							<div class="d-flex form-row text-center">
								<div class="col-md-8">
									<img 
									width="100px" 
									class="img-fluid" 
									:src="productVariation.preview || ''"
									alt="Variation Picture" 
									>
								</div>
								<div class="col-md-4 align-self-center">
									<div class="custom-file">
										<input type="file" 
										class="form-control custom-file-input" 
										:class="!errors.variations[index].preview  ? 'is-valid' : 'is-invalid'" 
										@change="onVariationPreviewChange($event, index)" 
										accept="image/*"
										>
										<label class="custom-file-label" for="validatedCustomFile">Choose Picture...</label>
										<div class="invalid-feedback">
											{{ errors.variations[index].preview }}
										</div>
									</div>
								</div>
							</div>
						</div>									
					</div>

					<div class="form-row" v-else>
						<div class="form-group col-md-12">
							<div class="form-row">
								<div class="col-sm-12">
									<label for="inputFirstName">Variation</label>

									<treeselect
										v-model="productVariation.variation"
										:options="[]" 
										:show-count="true" 
										:normalizer="treeSelectCustomFunction" 
										:valueFormat="'object'" 
										:required="true" 
										:disabled="productVariation.variation_immutability" 
										@input="validateFormInput('product_variation_id')"  
										class="form-control p-0 is-valid" 
										placeholder="Select Variation" 
									/>
								</div>
							</div>
						</div>

						<div class="form-group col-md-12">
							<div class="d-flex form-row text-center">
								<div class="col-md-8">
									<img 
									width="100px" 
									class="img-fluid" 
									:src="productVariation.preview || ''"
									alt="Variation Picture" 
									>
								</div>
								<div class="col-md-4 align-self-center">
									<div class="custom-file">
										<input type="file" 
										class="form-control custom-file-input" 
										accept="image/*"
										>
										<label class="custom-file-label" for="validatedCustomFile">Choose Picture...</label>
									</div>
								</div>
							</div>
						</div>									
					</div>
				</div>
			</div>

			<div class="form-row">
				<div class="form-group col-md-6">
					<button 
					type="button" 
					class="btn waves-effect waves-light hor-grd btn-primary btn-outline-primary btn-sm btn-block" 
					v-tooltip.bottom-end="'Add Variation'" 
					@click="addMoreVariation()"
					>
					Add Variation
				</button>
			</div>
			<div class="form-group col-md-6">
				<button 
				type="button" 
				class="btn waves-effect waves-light hor-grd btn-info btn-outline-info btn-sm btn-block" 
				v-tooltip.bottom-end="'Remove Variation'" 
				:disabled="singleProductData.variations[singleProductData.variations.length-1].variation_immutability || singleProductData.variations.length < 3"
				@click="removeVariation()"
				>
				Remove Variation
			</button>
		</div>
	</div>
</div>
</div>
</div>

<div class="form-row card-footer">
	<div class="col-sm-12 text-right" v-show="!submitForm">
		<span class="text-danger small mb-1">
			Please input required fields
		</span>
	</div>

	<div class="col-sm-12">
		<button type="button" class="btn waves-effect waves-dark btn-secondary btn-outline-secondary float-left" data-dismiss="modal">
			Close
		</button>
		<button type="submit" class="btn waves-effect waves-dark btn-primary btn-outline-primary float-right" :disabled="!submitForm || formSubmitted">
			{{ createMode ? 'Save' : 'Update' }}
		</button>
	</div>
</div>	

</div>

</form>
</div>
</div>
</div>
</template>

<script type="text/javascript">
	
	import Multiselect from 'vue-multiselect';
	import Treeselect from '@riophae/vue-treeselect';
  	import '@riophae/vue-treeselect/dist/vue-treeselect.css';			// import the styles

	export default {

		components : {
			Treeselect, 
			multiselect : Multiselect,
		},

		props : {
			csrf: {
				type: String,
				required: true
			},
			errors: {
				type: Object,
				default() {

					return {

						variations : [],
						
						/*
						addresses : [
							{}
						],
						*/
						
					};

				}
			},
			singleProductData: {
				type: Object,
				required: true
			},
			createMode: {
				type: Boolean,
				default: true
			},
			formSubmitted: {
				type: Boolean,
				default: false
			},
			productMode: {
				type: String,
				default: 'retail product'
			},
			allProductCategories: {
				type: Array,
				required: true
			},
			allVariationTypes: {
				type: Array,
				required: true
			}
		},

		data() {

			return {

				submitForm : true,
				
			}

		},

		methods : {

			nameWithCapitalized (name) {

				if (!name) return ''

					const words = name.split(" ");

				for (let i = 0; i < words.length; i++) {
					words[i] = words[i][0].toUpperCase() + words[i].substr(1);
				}

				return words.join(" ");

			},
			objectNameWithCapitalized ({ name, user_name }) {
				if (name) {
					name = name.toString()
					return name.charAt(0).toUpperCase() + name.slice(1)
				}
				else if (user_name) {
					user_name = user_name.toString()
					return user_name.charAt(0).toUpperCase() + user_name.slice(1)
				}
				else 
					return ''
			},
			onProductPreviewChange(evnt) {

				let files = evnt.target.files || evnt.dataTransfer.files;

                // Only process image files.
                if (files.length && files[0].type.match('image.*')) {
                	
                	let reader = new FileReader();
                	reader.onload = (evnt) => {

                		this.singleProductData.preview = evnt.target.result;

                	};
                	reader.readAsDataURL(files[0]);

                	this.$delete(this.errors, 'preview');
                }
                else{

                	this.errors.preview = 'File should be image';

                }

                evnt.target.value = '';
                return;

            },
            onVariationPreviewChange(evnt, index) {

            	let files = evnt.target.files || evnt.dataTransfer.files;

                // Only process image files.
                if (files.length && files[0].type.match('image.*')) {
                	
                	let reader = new FileReader();
                	reader.onload = (evnt) => {

                		this.singleProductData.variations[index].preview = evnt.target.result;

                	};
                	reader.readAsDataURL(files[0]);

                	this.$delete(this.errors.variations[index], 'preview');
                }
                else{

                	this.errors.variations[index].preview = 'File should be image';

                }

                evnt.target.value = '';
                return;

            }, 
            addMoreVariation() {
            	this.singleProductData.variations.push({});
            	this.errors.variations.push({});
            },
            removeVariation() {
            	if (this.singleProductData.variations.length > 2) {	
            		this.singleProductData.variations.pop();
            		this.errors.variations.pop();

            		if (!this.errorInArray(this.errors.variations)) {
            			this.submitForm = true;
            		}

            	}
            },
            treeSelectCustomFunction(node) {
            	return {
            		id: node.id,
            		label: node.name,
            		children: node.childs,
            	}
            },
            setProductCategory() {
				// console.log('category has been triggered');
				if (this.singleProductData.category && Object.keys(this.singleProductData.category).length > 0) {
					this.singleProductData.product_category_id = this.singleProductData.category.id;
				}

				this.validateFormInput('product_category');
			},
			setProductMerchant() {
				// console.log('merchant has been triggered');
				if (this.singleProductData.merchant && Object.keys(this.singleProductData.merchant).length > 0) {
					this.singleProductData.merchant_id = this.singleProductData.merchant.id;
				}
			},
			resetProductVariations() {

				if (this.singleProductData.has_variations) {

					this.singleProductData.variations = [
						{}, {}
					];

					this.errors.variations = [
						{}, {}
					];

				}
				else {

					this.singleProductData.variation_type = {};
					this.singleProductData.variations = [];
					this.errors.variations = [];

					this.$delete(this.errors, 'product_variation_type');

				}

			},
			setProductMode() {
				if (this.productMode=='bulk product') {
					this.singleProductData.has_variations = false;
					this.$delete(this.singleProductData, 'category');
					this.$delete(this.singleProductData, 'product_category_id');
				}
			},
			errorInArray(array = []) {
				const variationError = (variation) => {
					return Object.keys(variation).length > 0
				}; 

				if (array.length) {
					return array.some(variationError);
				}

				return false;
			},
			verifyUserInput() {

				this.validateFormInput('product_name');
				this.validateFormInput('product_category');
				
				this.validateFormInput('product_quantity_type');

				if (this.singleProductData.has_variations) {
					
					this.validateFormInput('product_variation_type');
					this.validateFormInput('product_variation_id');

				}

				if (this.errors.constructor === Object && Object.keys(this.errors).length < 2 && !this.errorInArray(this.errors.variations)) {
					
					this.submitForm = true;
					this.createMode ? this.$emit('storeProduct', this.singleProductData) : this.$emit('updateAsset', this.singleProductData)
					
				}
				else {
					this.submitForm = false;
					return false;
				}

			},
			validateFormInput (formInputName) {

				this.submitForm = false;

				switch(formInputName) {

					case 'product_category' : 
					
					if (this.productMode=='retail product' && (! this.singleProductData.hasOwnProperty('product_category_id') || ! this.singleProductData.product_category_id)) {
						
						this.submitForm = false;
						this.errors.product_category = 'Product category is required'

					}
					else{
						this.submitForm = true;
						this.$delete(this.errors, 'product_category');
					}

					break;

					case 'product_name' :

					if (!this.singleProductData.name || !this.singleProductData.name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
						this.errors.product_name = 'No special character';
					}
					else{
						this.submitForm = true;
						this.$delete(this.errors, 'product_name');
					}

					break;

					case 'product_quantity_type' :

					if (!this.singleProductData.quantity_type) {
						this.errors.product_quantity_type = 'Qty type is required';
					}
					else{
						this.submitForm = true;
						this.$delete(this.errors, 'product_quantity_type');
					}

					break;

					case 'product_variation_type' : 
					
					if (!this.singleProductData.has_variations) {
						this.submitForm = true;
						this.$delete(this.errors, 'product_variation_type');
					}
					else if (this.singleProductData.has_variations && (!this.singleProductData.variation_type || Object.keys(this.singleProductData.variation_type).length == 0)) {
						
						this.errors.product_variation_type = 'Variation type is required';
					}
					else{
						this.submitForm = true;
						this.$delete(this.errors, 'product_variation_type');
					}

					break;

					case 'product_variation_id' :
					
					if (this.singleProductData.has_variations) {
						
						this.singleProductData.variations.forEach(
							(productVariation, index) => {
								if (! productVariation.variation || Object.keys(productVariation.variation).length == 0) {
									
									this.errors.variations[index].product_variation_id = 'Variation is required';

								}
								else if (this.singleProductData.variations.filter(obj => obj.variation && obj.variation.id == productVariation.variation.id).length > 1) {

									this.errors.variations[index].product_variation_id = 'Same Variation selected';
								}
								else {
									this.$delete(this.errors.variations[index], 'product_variation_id');
								}
							}
							);
						
						if (!this.errorInArray(this.errors.variations)) {
							this.submitForm = true;
						}

					}
					else {
						this.submitForm = true;
						this.errors.variations = [];
					}

					break;

				}

			},

		}

	}

</script>

<style>
	@import '~vue-multiselect/dist/vue-multiselect.min.css';
</style>