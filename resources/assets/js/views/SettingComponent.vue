
<template>

	<div class="pcoded-content">

		<div class="page-header card">
			<div class="row align-items-end">
				<div class="col-lg-8">
					<div class="page-header-title">
						<i class="feather icon-settings bg-c-blue"></i>
						<div class="d-inline">
							<h5>Settings</h5>
							<span>You will have every settings with navigating tabs</span>
						</div>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="page-header-breadcrumb">
						<ul class=" breadcrumb breadcrumb-title">
							<li class="breadcrumb-item">
								<router-link :to="{ name: 'home' }" class="waves-effect waves-dark">
									<i class="feather icon-home"></i>
								</router-link>
							</li>
							<li class="breadcrumb-item">
								<router-link :to="{ name: 'settings' }" class="waves-effect waves-dark">
									Settings
								</router-link>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>		

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
										  	
										  	<div class="col-lg-12 col-xl-12">

												<!-- <div class="sub-title">Default</div> -->

												<ul class="nav nav-tabs md-tabs" role="tablist">
													<li class="nav-item">
														<a class="nav-link active" data-toggle="tab" href="#payment" role="tab">
															<span class="sub-title">Payment</span>
														</a>
														<div class="slide"></div>
													</li>
													<li class="nav-item">
														<a class="nav-link" data-toggle="tab" href="#contact" role="tab">
															<span class="sub-title">Contact</span>
														</a>
														<div class="slide"></div>
													</li>
													<li class="nav-item">
														<a class="nav-link" data-toggle="tab" href="#warehouse" role="tab">
															<span class="sub-title">Warehouse</span>
														</a>
														<div class="slide"></div>
													</li>
													<li class="nav-item">
														<a class="nav-link" data-toggle="tab" href="#others" role="tab">
															<span class="sub-title">System</span>
														</a>
														<div class="slide"></div>
													</li>
												</ul>
												
												<div class="tab-content card-block">

													<div class="tab-pane fade show active" id="payment">	
														<div class="row">
															<div  
																v-show="!loading" 
																class="col-sm-12"
															>		
																<form class="form-horizontal" v-on:submit.prevent="updatePaymentSetting"
														      	>
														      		<input 
															      		type="hidden" 
															      		name="_token" 
															      		:value="csrf"
														      		>

													              	<div class="form-group row">
														              	<div class="col-6">
														              		<div class="row">
															              		<label class="col-sm-4 col-form-label text-right">
															              			Bank Name
															              		</label>
																                <div class="col-sm-8">
																                  	<input 
																                  		type="text" 
																                  		class="form-control" 
																                  		v-model="applicationSettings.official_bank_name" 
																                  		placeholder="Islami Bank" 
																                  		required="true" 
																                  		:class="!errors.applicationSettings.official_bank_name  ? 'is-valid' : 'is-invalid'"
																						@keyup="validateFormInput('official_bank_name')"
																                  	>
																                  	<div class="invalid-feedback">
																			        	{{ errors.applicationSettings.official_bank_name }}
																			  		</div>
																                </div>
														              		</div>
														              	</div>
														                <div class="col-6">
														              		<div class="row">
															              		<label class="col-sm-4 col-form-label text-right">
															              			Merchant Name
															              		</label>
																                <div class="col-sm-8">
																                  	<input 
																                  		type="text" 
																                  		class="form-control" 
																                  		v-model="applicationSettings.official_merchant_name" 
																                  		placeholder="BKash" 
																                  		required="true" 
																                  		:class="!errors.applicationSettings.official_merchant_name  ? 'is-valid' : 'is-invalid'"
																						@keyup="validateFormInput('official_merchant_name')"
																                  	>
																                  	<div class="invalid-feedback">
																			        	{{ errors.applicationSettings.official_merchant_name }}
																			  		</div>
																                </div>
														              		</div>
														              	</div>
													              	</div>

													              	<div class="form-group row">
													              		<div class="col-6">
														              		<div class="row">
															              		<label class="col-sm-4 col-form-label text-right">
															              			Bank Acc. Name
															              		</label>
																                <div class="col-sm-8">
																                  	<input 
																                  		type="text" 
																                  		class="form-control" 
																                  		v-model="applicationSettings.official_bank_account_name" 
																                  		placeholder="Account Holder Name" 
																                  		required="true" 
																                  		:class="!errors.applicationSettings.official_bank_account_name  ? 'is-valid' : 'is-invalid'"
																						@keyup="validateFormInput('official_bank_account_name')"
																                  	>
																                  	<div class="invalid-feedback">
																			        	{{ errors.applicationSettings.official_bank_account_name }}
																			  		</div>
																                </div>
														              		</div>
														              	</div>
														              	<div class="col-6">
														              		<div class="row">
															              		<label class="col-sm-4 col-form-label text-right">
															              			Bank Acc. Number
															              		</label>
																                <div class="col-sm-8">
																                  	<input 
																                  		type="text" 
																                  		class="form-control" 
																                  		v-model="applicationSettings.official_bank_account_number" 
																                  		placeholder="Bank Account Number" 
																                  		required="true" 
																                  		:class="!errors.applicationSettings.official_bank_account_number  ? 'is-valid' : 'is-invalid'"
																						@keyup="validateFormInput('official_bank_account_number')"
																                  	>
																                  	<div class="invalid-feedback">
																			        	{{ errors.applicationSettings.official_bank_account_number }}
																			  		</div>
																                </div>
														              		</div>
														              	</div>
													              	</div>

													              	<div class="form-group row">
													              		<div class="col-6">
														              		<div class="row">
															              		<label class="col-sm-4 col-form-label text-right">
															              			Merchant Acc. Number
															              		</label>
																                <div class="col-sm-8">
																                  	<input 
																                  		type="text" 
																                  		class="form-control" 
																                  		v-model="applicationSettings.official_merchant_account_number" 
																                  		placeholder="Merchant Account Number" 
																                  		required="true" 
																                  		:class="!errors.applicationSettings.official_merchant_account_number  ? 'is-valid' : 'is-invalid'"
																						@keyup="validateFormInput('official_merchant_account_number')"
																                  	>
																                  	<div class="invalid-feedback">
																			        	{{ errors.applicationSettings.official_merchant_account_number }}
																			  		</div>
																                </div>
														              		</div>
														              	</div>
														              	<div class="col-6">
														              		<div class="row">
														              			<label class="col-sm-4 col-form-label text-right">
														              				Vat Rate
														              			</label>
																                <div class="col-sm-8">
																                  	<div class="input-group mb-0">
																						<input type="number" class="form-control" v-model.number="applicationSettings.vat_percentage" 
																						min="0" 
																						max="100" 
																						step=".1" 
																						placeholder="Vat Rate" 
																						required="true"
																						:class="!errors.applicationSettings.vat_percentage  ? 'is-valid' : 'is-invalid'"
																						@keyup="validateFormInput('vat_percentage')">
																						<span class="input-group-append" id="basic-addon3">
																							<label class="input-group-text">%</label>
																						</span>
																					</div>
																                	<div class="invalid-feedback" 
																                		v-show="errors.applicationSettings.vat_percentage"
																                	>
																			        	{{ errors.applicationSettings.vat_percentage }}
																			  		</div>
																                </div>
														              		</div>
														              	</div>
													              	</div>
														        	
													              	<div class="card-footer text-center">
														            	<div class="col-sm-12">
																			<span 
																				class="text-danger p-0 m-0 small" 
																				v-show="!submitForm"
																			>
																		  		Please input all required fields
																		  	</span>
																		</div>
														              	<button 
															              	type="submit" 
															              	:disabled="loading || !submitForm" 
															              	class="btn btn-primary"
														              	>
														              		Update Payment Setting
														              	</button>
														            </div>

														      	</form>
															</div>
														</div>
													</div>

													<div class="tab-pane fade show fade" id="contact">	
														<div class="row">
															<div  
																v-show="!loading" 
																class="col-sm-12"
															>		
														      	<form 
															      	class="form-horizontal" 
															      	v-on:submit.prevent="updateContactSetting"
														      	>
														      		
														      		<input 
															      		type="hidden" 
															      		name="_token" 
															      		:value="csrf"
														      		>
														            
													              	<div class="form-group row">
														              	<div class="col-6">
														              		<div class="row">
															              		<label class="col-sm-4 col-form-label text-right">
															              			Customer Care Number
															              		</label>
																                <div class="col-sm-8">
																                  <input 
																                  	type="tel" 
																                  	class="form-control" 
																                  	v-model="applicationSettings.official_customer_care_number" 
																                  	placeholder="Official Customer Care Number" 
																                  	required="true"  
																                  	:class="!errors.applicationSettings.official_customer_care_number  ? 'is-valid' : 'is-invalid'"
																					@keyup="validateFormInput('official_customer_care_number')"
																                  >
																                  <div class="invalid-feedback">
																		        	{{ errors.applicationSettings.official_customer_care_number }}
																			  	  </div>
																                </div>
														              		</div>
														              	</div>
														                <div class="col-6">
														              		<div class="row">
															              		<label class="col-sm-4 col-form-label text-right">
															              			Mail Address
															              		</label>
																                <div class="col-sm-8">
																                  <input 
															                  		type="email" 
																                  	class="form-control" 
																                  	v-model="applicationSettings.official_mail_address" 
																                  	placeholder="Mail Address" 
																                  	required="true"  
																                  	:class="!errors.applicationSettings.official_mail_address  ? 'is-valid' : 'is-invalid'"
																					@keyup="validateFormInput('official_mail_address')"
																                  >
																                  	<div class="invalid-feedback">
																			        	{{ errors.applicationSettings.official_mail_address }}
																			  		</div>
																                </div>
														              		</div>
														              	</div>
													              	</div>
													              	<div class="form-group row">
													              		<label class="col-sm-2 col-form-label text-right">
													              			Contact Address
													              		</label>
														                <div class="col-sm-10">
														                  	<ckeditor 
												                              	class="form-control" 
												                              	:class="!errors.applicationSettings.official_contact_address  ? 'is-valid' : 'is-invalid'"
												                              	:editor="editor" 
												                              	v-model="applicationSettings.official_contact_address" 
												                              	placeholder="Contact Address" 
												                              	@blur="validateFormInput('official_contact_address')"
												                            >
											                              	</ckeditor>
											                              	<div class="invalid-feedback">
																	        	{{ errors.applicationSettings.official_contact_address }}
																	  		</div>
														                </div>
													              	</div>
														            
														            <div class="card-footer text-center">
														            	<div class="col-sm-12">
																			<span 
																				class="text-danger p-0 m-0 small" 
																				v-show="!submitForm"
																			>
																		  		Please input all required fields
																		  	</span>
																		</div>
														              	<button 
															              	type="submit" 
															              	:disabled="loading || !submitForm" 
															              	class="btn btn-primary"
														              	>
														              		Update Contact Setting
														              	</button>
														            </div>
														        	
														      	</form>
															    
															</div>
														</div>
													</div>

													<div class="tab-pane fade show fade" id="warehouse">	
														<div class="row">
															<div  v-show="!loading" class="col-sm-12">
																<div class="">
																	
															      	<form 
																      	class="form-horizontal" 
																      	v-on:submit.prevent="updateWarehouseSetting"
															      	>
															      		<input 
																      		type="hidden" 
																      		name="_token" 
																      		:value="csrf"
															      		>
															            
														              	<div class="form-group row">
															              	<div class="col-6">
															              		<div class="row">
																              		<label class="col-sm-4 col-form-label text-right">
																              			Default Selling Price
																              		</label>
																	                <div class="col-sm-8">
																	                  <input 
																	                  	type="number" 
																						class="form-control" 
																						v-model.number="applicationSettings.default_selling_price" 
																						min="0" 
																						step="1" 
																						required="true"
																						placeholder="Default Price" 		
																						:class="!errors.applicationSettings.default_selling_price  ? 'is-valid' : 'is-invalid'"
																						@keyup="validateFormInput('default_selling_price')"
																	                  >
																	                  	<div class="invalid-feedback">
																				        	{{ errors.applicationSettings.default_selling_price }}
																				  		</div>
																	                </div>
															              		</div>
															              	</div>
															                <div class="col-6">
															              		<div class="row">
																              		<label class="col-sm-4 col-form-label text-right">
																              			Default Storing Price
																              		</label>
																	                <div class="col-sm-8">
																	                  <input 
																	                  	type="number" 
																						class="form-control" 
																						v-model.number="applicationSettings.default_storing_price" 
																						min="0" 
																						step="1" 
																						required="true"
																						placeholder="Default Price" 		
																						:class="!errors.applicationSettings.default_storing_price  ? 'is-valid' : 'is-invalid'"
																						@keyup="validateFormInput('default_storing_price')"
																	                  >
																	                  	<div class="invalid-feedback">
																				        	{{ errors.applicationSettings.default_storing_price }}
																				  		</div>
																	                </div>
															              		</div>
															              	</div>
														              	</div>

														              	<div class="form-group row">
															              	<div class="col-sm-6">
															              		<div class="row">
																              		<label class="col-sm-4 col-form-label text-right">
																              			Default Length
																              		</label>
																	                <div class="col-sm-8">
																	                  <input 
																	                  	type="number" 
																						class="form-control" 
																						v-model.number="applicationSettings.default_length" 
																						min="0" 
																						step="1" 
																						required="true"
																						placeholder="Default Price" 		
																						:class="!errors.applicationSettings.default_length  ? 'is-valid' : 'is-invalid'"
																						@keyup="validateFormInput('default_length')"
																	                  >
																	                  	<div class="invalid-feedback">
																				        	{{ errors.applicationSettings.default_length }}
																				  		</div>
																	                </div>
															              		</div>
															              	</div>
															              	<div class="col-sm-6">
															              		<div class="row">
																              		<label class="col-sm-4 col-form-label text-right">
																              			Default Width
																              		</label>
																	                <div class="col-sm-8">
																	                  <input 
																	                  	type="number" 
																						class="form-control" 
																						v-model.number="applicationSettings.default_width" 
																						min="0" 
																						step="1" 
																						required="true"
																						placeholder="Default Price" 		
																						:class="!errors.applicationSettings.default_width  ? 'is-valid' : 'is-invalid'"
																						@keyup="validateFormInput('default_width')"
																	                  >
																	                  	<div class="invalid-feedback">
																				        	{{ errors.applicationSettings.default_width }}
																				  		</div>
																	                </div>
															              		</div>
															              	</div>
														              	</div>
														              	<div class="form-group row">
														              		<div class="col-sm-6">
															              		<div class="row">
																              		<label class="col-sm-4 col-form-label text-right">
																              			Default Height
																              		</label>
																	                <div class="col-sm-8">
																	                  <input 
																	                  	type="number" 
																						class="form-control" 
																						v-model.number="applicationSettings.default_height" 
																						min="0" 
																						step="1" 
																						required="true"
																						placeholder="Default Price" 		
																						:class="!errors.applicationSettings.default_height  ? 'is-valid' : 'is-invalid'"
																						@keyup="validateFormInput('default_height')"
																	                  >
																	                  	<div class="invalid-feedback">
																				        	{{ errors.applicationSettings.default_height }}
																				  		</div>
																	                </div>
															              		</div>
															              	</div>
															              	<div class="col-sm-6">
															              		<div class="row">
																              		<label class="col-sm-4 col-form-label text-right">
																              			Default Measurement Unit
																              		</label>
																	                <div class="col-sm-8">
																	                  <input 
																	                  	type="text" 
																						class="form-control" 
																						v-model="applicationSettings.default_measure_unit_id" 
																						required="true"
																						placeholder="Default Unit" 		
																						:class="!errors.applicationSettings.default_measure_unit_id  ? 'is-valid' : 'is-invalid'"
																						@keyup="validateFormInput('default_measure_unit_id')"
																	                  >
																	                  	<div class="invalid-feedback">
																				        	{{ errors.applicationSettings.default_measure_unit_id }}
																				  		</div>
																	                </div>
															              		</div>
															              	</div>
														              	</div>

															            <div class="card-footer text-center">
															            	<div class="col-sm-12">
																				<span 
																					class="text-danger p-0 m-0 small" 
																					v-show="!submitForm"
																				>
																			  		Please input all required fields
																			  	</span>
																			</div>
															              	<button 
																              	type="submit" 
																              	:disabled="loading || !submitForm" 
																              	class="btn btn-primary"
															              	>
															              		Update Delivery Setting
															              	</button>
															            </div>
															        	
															      	</form>
															    </div>
															</div>
														</div>
													</div>

													<div class="tab-pane fade show fade" id="others">	
														<div class="row">

															<div  
																v-show="!loading" 
																class="col-sm-12"
															>
																<div class="">
																	
															      	<form 
																      	class="form-horizontal" 
																      	v-on:submit.prevent="updateSystemSetting"
															      	>	
															      		<input 
																      		type="hidden" 
																      		name="_token" 
																      		:value="csrf"
															      		>
															            
													              		<div class="row d-flex align-items-center text-center mb-4">
														              		<label class="col-sm-4 col-form-label text-right">
														              			App Logo
														              		</label>
														              		<div class="col-sm-4">
														                  		<img 
															                  		class="profile-user-img img-fluid" 
															                  		:src="applicationSettings.application_logo || 'uploads/application/application_logo.png'" 
															                  		alt="Application logo"
														                  		>
														                	</div>
															                <div class="col-sm-4">
															                  	<div class="input-group">
																                    <div class="custom-file">
																                        <input 
																                        	type="file" 
																                        	class="custom-file-input" 
																                        	v-on:change="onLogoChange" 
																                        	accept="image/*"
																                        >
																                        <label class="custom-file-label">
																                        	Change Logo
																                        </label>
																                    </div>
															                    </div>
															                </div>
														            	</div>

													                	<div class="row d-flex align-items-center text-center">
														              		<label class="col-sm-4 col-form-label text-right">
														              			Panel Favicon
														              		</label>
														              		<div class="col-sm-4">
														                  		<img 
															                  		class="profile-user-img img-fluid" 
															                  		:src="applicationSettings.application_favicon || 'uploads/application/application_favicon.png'" 
															                  		alt="Application favicon"
														                  		>
														                	</div>
															                <div class="col-sm-4">
															                  	<div class="input-group">
																                    <div class="custom-file">
																                        <input 
																                        	type="file" 
																                        	class="custom-file-input" 
																                        	v-on:change="onFaviconChange" 
																                        	accept="image/*"
																                        >
																                        <label class="custom-file-label">
																                        	Change Favicon
																                        </label>
																                    </div>
															                    </div>
															                </div>
														            	</div>
															            
															            <div class="card-footer text-center">
															            	<div class="col-sm-12">
																				<span 
																					class="text-danger p-0 m-0 small" 
																					v-show="!submitForm"
																				>
																			  		Please input one file at least
																			  	</span>
																			</div>
															              	<button 
																              	type="submit" 
																              	:disabled="loading || !submitForm" 
																              	class="btn btn-primary"
															              	>
															              		Update Media Settings
															              	</button>
															            </div>
															        	
															      	</form>
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
			</div>
		</div>

		


	</div>

</template>

<script type="text/javascript">

	import axios from 'axios';
	import CKEditor from '@ckeditor/ckeditor5-vue';
	import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

	export default {

	    components: { 
			ckeditor: CKEditor.component,
		},

	    data() {
	        return {
	        	editor: ClassicEditor,

	        	applicationSettings : {
	        		
	        	},

	        	newLogo : null,
	        	newFavicon : null,

	        	error : '',
	        	loading : false,

	        	errors : {
	        		applicationSettings : {},
	        	},

	        	submitForm : true,
	            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
	        }
		},
		created(){
			this.fetchSettingData();
		},
		methods : {
			fetchSettingData() {

				this.error = '';
				this.loading = true;

				axios
					.get('/api/application-settings')
					.then(response => {
						this.applicationSettings = response.data || {};
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
					.finally(response=>{
						this.loading = false;
					});
			},
			updatePaymentSetting() {

				this.validateFormInput('official_bank_name');
				this.validateFormInput('official_merchant_name');
				this.validateFormInput('official_bank_account_name');
				this.validateFormInput('official_bank_account_number');
				this.validateFormInput('official_merchant_account_number');
				this.validateFormInput('vat_percentage');

				if (this.errors.applicationSettings.official_bank_name || this.errors.applicationSettings.official_merchant_name || this.errors.applicationSettings.official_bank_account_name || this.errors.applicationSettings.official_bank_account_number || this.errors.applicationSettings.official_merchant_account_number || this.errors.applicationSettings.vat_percentage) {

					this.submitForm = false;
					return;
				}

				axios
					.put('/payment-settings', this.applicationSettings)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Payment settings have been updated", "Success");
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
			updateContactSetting() {

				this.validateFormInput('official_customer_care_number');
				this.validateFormInput('official_mail_address');
				this.validateFormInput('official_contact_address');

				if (this.errors.applicationSettings.official_customer_care_number || this.errors.applicationSettings.official_mail_address || this.errors.applicationSettings.official_contact_address) {

					this.submitForm = false;
					return false;
				}

				axios
					.put('/contact-settings', this.applicationSettings)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Contact settings have been updated", "Success");
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
			updateWarehouseSetting() {

				this.validateFormInput('default_selling_price');
				this.validateFormInput('default_storing_price');
				this.validateFormInput('default_length');
				this.validateFormInput('default_width');
				this.validateFormInput('default_height');
				this.validateFormInput('default_measure_unit_id');


				if (this.errors.applicationSettings.default_selling_price || this.errors.applicationSettings.default_storing_price || this.errors.applicationSettings.default_length || this.errors.applicationSettings.default_width || this.errors.applicationSettings.default_height || this.errors.applicationSettings.default_measure_unit_id) {

					this.submitForm = false;
					return false;
				}

				axios
					.put('/warehouse-settings', this.applicationSettings)
					.then(response => {
						if (response.status == 200) {
							this.$toastr.s("Warehouse settings have been updated", "Success");
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
			updateSystemSetting() {

				if (!this.newLogo && !this.newFavicon) {
					this.submitForm = false;
					return;
				}

				this.applicationSettings.application_logo = this.newLogo;
				this.applicationSettings.application_favicon = this.newFavicon;

				axios
					.put('/system-settings', this.applicationSettings)
					.then(response => {
						if (response.status == 200) {
							
							this.newLogo = this.newFavicon = null;
							this.$toastr.s("System settings have been updated", "Success");

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
			onLogoChange(evnt){
				let files = evnt.target.files || evnt.dataTransfer.files;

                // Only process image files.
		      	if (files.length && files[0].type.match('image.*')) {
		      		this.submitForm = true;
                	this.readImage(files[0], 'logo');
		      	}

		      	evnt.target.value = '';

		      	return;
			},
			onFaviconChange(evnt){
				let files = evnt.target.files || evnt.dataTransfer.files;

                // Only process image files.
		      	if (files.length && files[0].type.match('image.*')) {
		      		this.submitForm = true;
                	this.readImage(files[0], 'favicon');
		      	}

		      	evnt.target.value = '';

		      	return;
			},
			readImage(file, filename) {
                let reader = new FileReader();

                if (filename=='favicon') {
	                reader.onload = (evnt) => {
	                    this.newFavicon = this.applicationSettings.application_favicon = evnt.target.result;
	                };
                }else{
                	reader.onload = (evnt) => {
	                    this.newLogo = this.applicationSettings.application_logo = evnt.target.result;
	                };
                }

                reader.readAsDataURL(file);
            },
            validateFormInput (formInputName) {
				
				this.submitForm = false;

				switch(formInputName) {

					case 'official_bank_name' :

						if (!this.applicationSettings.official_bank_name) {
							this.errors.applicationSettings.official_bank_name = 'Bank name is required';
						}
						else if (!this.applicationSettings.official_bank_name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.applicationSettings.official_bank_name = 'No special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.applicationSettings, 'official_bank_name');
						}

						break;

					case 'official_merchant_name' :

						if (!this.applicationSettings.official_merchant_name) {
							this.errors.applicationSettings.official_merchant_name = 'Merchant name is required';
						}
						else if (!this.applicationSettings.official_merchant_name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.applicationSettings.official_merchant_name = 'No special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.applicationSettings, 'official_merchant_name');
						}

						break;

					case 'official_bank_account_name' :

						if (!this.applicationSettings.official_bank_account_name) {
							this.errors.applicationSettings.official_bank_account_name = 'Account holder name is required';
						}
						else if (!this.applicationSettings.official_bank_account_name.match(/^[_A-z0-9]*((-|_|\s)*[_A-z0-9])*$/g)) {
							this.errors.applicationSettings.official_bank_account_name = 'No special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.applicationSettings, 'official_bank_account_name');
						}

						break;

					case 'official_bank_account_number' :

						if (!this.applicationSettings.official_bank_account_number) {
							this.errors.applicationSettings.official_bank_account_number = 'Account number is required';
						}
						else if (!this.applicationSettings.official_bank_account_number.match(/^[_A-z0-9]*((-|_|\s)*[_A-z0-9])*$/g)) {
							this.errors.applicationSettings.official_bank_account_number = 'No special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.applicationSettings, 'official_bank_account_number');
						}

						break;

					case 'official_merchant_account_number' :

						if (!this.applicationSettings.official_merchant_account_number) {
							this.errors.applicationSettings.official_merchant_account_number = 'Merchant account is required';
						}
						else if (!this.applicationSettings.official_merchant_account_number.match(/\+?(88)?0?1[123456789][0-9]{8}\b/g)) {
							this.errors.applicationSettings.official_merchant_account_number = 'Invalid account number';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.applicationSettings, 'official_merchant_account_number');
						}

						break;

					case 'vat_percentage' :

						if (!this.applicationSettings.vat_percentage) {
							this.errors.applicationSettings.vat_percentage = 'Vat rate is required';
						}
						else if (this.applicationSettings.vat_percentage < 0 || this.applicationSettings.vat_percentage > 100 ) {
							this.errors.applicationSettings.vat_percentage = 'Value should be between 0 and 100';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.applicationSettings, 'vat_percentage');
						}

						break;

					case 'official_customer_care_number' :

						if (!this.applicationSettings.official_customer_care_number) {
							this.errors.applicationSettings.official_customer_care_number = 'Customer care number is required';
						}
						else if (!this.applicationSettings.official_customer_care_number.match(/\+?(88)?0?1[123456789][0-9]{8}\b/g)) {
							this.errors.applicationSettings.official_customer_care_number = 'Invalid customer care number';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.applicationSettings, 'official_customer_care_number');
						}

						break;

					case 'official_mail_address' :

						if (!this.applicationSettings.official_mail_address) {
							this.errors.applicationSettings.official_mail_address = 'Official mail is required';
						}
						else if (!this.applicationSettings.official_mail_address.match(/[a-z0-9._+-]+@[a-z0-9.-]+\.[a-z]{2,}$/g)) {
							this.errors.applicationSettings.official_mail_address = 'Invalid email';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.applicationSettings, 'official_mail_address');
						}

						break;

					case 'official_contact_address' :

						if (!this.applicationSettings.official_contact_address) {
							this.errors.applicationSettings.official_contact_address = 'Official address is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.applicationSettings, 'official_contact_address');
						}

						break;

					case 'default_selling_price' :

						if (!this.applicationSettings.default_selling_price) {
							this.errors.applicationSettings.default_selling_price = 'Selling price is required';
						}
						else if (this.applicationSettings.default_selling_price < 0) {
							this.errors.applicationSettings.default_selling_price = 'Value should be greater than 0';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.applicationSettings, 'default_selling_price');
						}

						break;

					case 'default_storing_price' :

						if (!this.applicationSettings.default_storing_price) {
							this.errors.applicationSettings.default_storing_price = 'Storing price is required';
						}
						else if (this.applicationSettings.default_storing_price < 0) {
							this.errors.applicationSettings.default_storing_price = 'Value should be greater than 0';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.applicationSettings, 'default_storing_price');
						}

						break;

					case 'default_length' :

						if (!this.applicationSettings.default_length) {
							this.errors.applicationSettings.default_length = 'Default length is required';
						}
						else if (this.applicationSettings.default_length < 0) {
							this.errors.applicationSettings.default_length = 'Value should be greater than 0';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.applicationSettings, 'default_length');
						}

						break;

					case 'default_width' :

						if (!this.applicationSettings.default_width) {
							this.errors.applicationSettings.default_width = 'Default width is required';
						}
						else if (this.applicationSettings.default_width < 0) {
							this.errors.applicationSettings.default_width = 'Value should be greater than 0';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.applicationSettings, 'default_width');
						}

						break;

					case 'default_height' :

						if (!this.applicationSettings.default_height) {
							this.errors.applicationSettings.default_height = 'Default height is required';
						}
						else if (this.applicationSettings.default_height < 0) {
							this.errors.applicationSettings.default_height = 'Value should be greater than 0';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.applicationSettings, 'default_height');
						}

						break;

					case 'default_measure_unit_id' :

						if (!this.applicationSettings.default_measure_unit_id) {
							this.errors.applicationSettings.default_measure_unit_id = 'Default unit is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.applicationSettings, 'default_measure_unit_id');
						}

						break;

				}
	 
			},
		}
  	}

</script>