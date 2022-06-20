<template>
	<!-- Modal Desc -->
	<div class="modal fade" id="job-apply-form" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="staticBackdropLabel">
						Apply for Warehouse Manager <!-- {{ job.name | capitalize }} -->
					</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<!-- Form -->
					<form 
						class="row g-3 form-bg form-bg-quote" 
						@submit.prevent="submitJobApplyForm()" 
						autocomplete="off" 
						enctype="multipart/form-data"
					>
						<div class="col-md-6 col-sm-12 form-group">
							<label class="form-label form-label-quote">First Name *</label>
							<input 
							type="text" 
							class="form-control" 
							v-model="singleApplicantData.first_name" 
							placeholder="Place Your First Name" 
							:class="!errors.first_name ? 'is-valid' : 'is-invalid'" 
							:readonly="formSubmitted" 
							@change="validateFormInput('first_name')"  
							/>

							<div class="invalid-feedback">
								{{ errors.first_name }}
							</div>
						</div>
						<div class="col-md-6 col-sm-12 form-group">
							<label class="form-label form-label-quote">Last Name</label>
							<input 
							type="text" 
							class="form-control" 
							v-model="singleApplicantData.last_name" 
							placeholder="Place Your Last Name" 
							:class="!errors.last_name ? 'is-valid' : 'is-invalid'" 
							:readonly="formSubmitted" 
							@change="validateFormInput('last_name')" 
							/>

							<div class="invalid-feedback">
								{{ errors.last_name }}
							</div>
						</div>
						<div class="col-md-12 col-sm-12 form-group">
							<label class="form-label form-label-quote">Email *</label>
							<input
							type="email"
							class="form-control"
							v-model="singleApplicantData.email" 
							placeholder="Place Your Email" 
							:class="!errors.email ? 'is-valid' : 'is-invalid'" 
							:readonly="formSubmitted" 
							@change="validateFormInput('email')" 
							/>

							<div class="invalid-feedback">
								{{ errors.email }}
							</div>
						</div>
						<div class="col-md-12 col-sm-12 form-group">
							<label class="form-label form-label-quote">Phone *</label>
							<input
							type="tel"
							class="form-control"
							v-model="singleApplicantData.mobile" 
							placeholder="Place Your Contact No" 
							:class="!errors.mobile ? 'is-valid' : 'is-invalid'" 
							:readonly="formSubmitted" 
							@change="validateFormInput('mobile')" 
							/>

							<div class="invalid-feedback">
								{{ errors.mobile }}
							</div>
						</div>
						<div class="col-md-12 col-sm-12 form-group">
							<label class="form-label form-label-quote">Address *</label>
							<input 
							type="text" 
							class="form-control"
							v-model="singleApplicantData.address" 
							placeholder="Place Your Address" 
							:class="!errors.address ? 'is-valid' : 'is-invalid'" 
							:readonly="formSubmitted" 
							@change="validateFormInput('address')" 
							/>

							<div class="invalid-feedback">
								{{ errors.address }}
							</div>
						</div>
						<div class="col-md-12 col-sm-12 form-group">
							<label class="form-label form-label-quote">Highest Education Level *</label>
							<select 
							class="form-control" 
							:class="!errors.educational_highest_level ? 'is-valid' : 'is-invalid'"
							v-model="singleApplicantData.educational_highest_level"
							:readonly="formSubmitted" 
							@change="validateFormInput('educational_highest_level')" 
							>
								<option value="" selected="selected"></option>
								<option value="primary">Primary</option>
								<option value="high-school">High School</option>
								<option value="ssc-o-level">SSC / O Level</option>
								<option value="hsc-o-level">HSC / A Level</option>
								<option value="diploma">Diploma</option>
								<option value="bachelor">Bachelor</option>
								<option value="master">Masters</option>
								<option value="doctorate">PhD / Doctorate</option>
								<option value="no-education">No Education</option>
							</select>

							<div class="invalid-feedback">
								{{ errors.educational_highest_level }}
							</div>
						</div>
						<div class="col-md-12 col-sm-12 form-group mb-3">
							<label class="form-label form-label-quote">CV *</label>
							<input 
							type="file" 
							class="form-control" 
							:class="!errors.resume ? 'is-valid' : 'is-invalid'"
							ref="file"
							@change="uploadResume" 
							accept=".pdf,.doc"
							>

							<div class="invalid-feedback">
								{{ errors.resume }}
							</div>
						</div>

						<div class="col-md-12 col-sm-12 text-center" v-show="Object.keys(jobApplyFormFailureMessage).length > 0">
								<ul>
									<li 
									class="text-danger text-start" 
									v-for="x in jobApplyFormFailureMessage"
									>
									{{ x[0] }}
								</li>
							</ul>
						</div>

						<div class="col-md-12 col-sm-12 text-center" v-show="jobApplyFormSuccessMessage && Object.keys(jobApplyFormFailureMessage).length == 0 && Object.keys(errors).length == 0">
							<span style="color: #23b8bf">
								{{ jobApplyFormSuccessMessage }}
							</span>
						</div>

						<div class="col-md-12 col-sm-12 text-center" v-show="! submitForm">
							<span class="text-danger small">
								**Please input required fields
							</span>
						</div>

						<div class="col-md-12 col-sm-12 form-group text-end">
							<button class="quote-btn w-100" :disabled="formSubmitted">Submit</button>
						</div>
					</form>
					<!-- Form -->
				</div>
			</div>
		</div>
	</div>
	<!-- Modal Desc -->
</template>

<script>

	export default {
		props : {

			csrf : {
				type : String,
				required : true
			},
			errors : {
				type : Object,
				required : true
			},
			formSubmitted : {
				type : Boolean,
				default: false
			},
			singleApplicantData : {
				type : Object,
				required : true
			},
			jobApplyFormSuccessMessage : {
				type : String,
				default: null
			},
			jobApplyFormFailureMessage : {
				type : Object,
				default: {}
			},

		},

		data () {
			return {
				submitForm : true,
			}
		},

		methods : {

			submitJobApplyForm() {

				this.validateFormInput('first_name');
				this.validateFormInput('last_name');
				this.validateFormInput('email');
				this.validateFormInput('mobile');
				this.validateFormInput('address');
				this.validateFormInput('educational_highest_level');
				this.validateFormInput('resume');

				if (Object.keys(this.errors).length > 0) {
					this.submitForm = false;
					return;
				}
				else {

					let formData = new FormData();

					formData.append('resume', this.$refs.file.files[0]);
					formData.append('first_name', this.singleApplicantData.first_name);
					formData.append('last_name', this.singleApplicantData.last_name);
					formData.append('email', this.singleApplicantData.email);
					formData.append('mobile', this.singleApplicantData.mobile);
					formData.append('address', this.singleApplicantData.address);
					formData.append('educational_highest_level', this.singleApplicantData.educational_highest_level);
					formData.append('job_id', this.$route.params.id);

					/*
					const keys = Object.keys(this.singleApplicantData);

					keys.forEach((key, index) => {
					    formData.append(`${key}`, `${this.singleApplicantData[key]}`);
					});
					*/

					this.$emit('submitJobApplyForm', formData);
					
				}

			},

			uploadResume() {

				if (this.$refs.file.files[0].type == 'application/pdf' || this.$refs.file.files[0].type == 'application/msword') {

					this.singleApplicantData.resume = this.$refs.file.files[0];
					this.$delete(this.errors, 'resume');
					this.submitForm = true;

				}
				else {

					this.singleApplicantData.resume = null;
					this.errors.resume =  'Invalid file';

				}

			},

			validateFormInput (formInputName) {

				this.submitForm = false;

				switch(formInputName) {
					
					case 'first_name' : 
					
					if (! this.singleApplicantData.first_name && ! this.singleApplicantData.last_name) {
						
						this.errors.first_name = 'First or last name is required'

					}
					else if (this.singleApplicantData.first_name && ! this.singleApplicantData.first_name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {

						this.errors.first_name = 'No special characters';

					}
					else{
						this.submitForm = true;
						this.$delete(this.errors, 'first_name');
						this.$delete(this.errors, 'last_name');
					}

					break;

					case 'last_name' : 
					
					if (! this.singleApplicantData.first_name && ! this.singleApplicantData.last_name) {
						
						this.errors.last_name = 'First or last name is required'

					}
					else if (this.singleApplicantData.last_name && ! this.singleApplicantData.last_name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {

						this.errors.last_name = 'No special characters';

					}
					else{
						this.submitForm = true;
						this.$delete(this.errors, 'first_name');
						this.$delete(this.errors, 'last_name');
					}

					break;

					case 'email' : 
					
					if (! this.singleApplicantData.email) {
						this.errors.email = 'Email is required';
					}
					else if (! this.singleApplicantData.email.match(/[a-z0-9._+-]+@[a-z0-9.-]+\.[a-z]{2,}$/g)) {
						this.errors.email = 'Invalid email address';
					}
					else{
						this.submitForm = true;
						this.$delete(this.errors, 'email');
					}

					break;

					case 'mobile' :

					if (! this.singleApplicantData.mobile) {
						this.errors.mobile = 'Mobile is required';
					}
					else if (! this.singleApplicantData.mobile.match(/\+?(88)?0?1[123456789][0-9]{8}\b/g)) {
						this.errors.mobile = 'Invalid mobile number';
					}
					else{
						this.submitForm = true;
						this.$delete(this.errors, 'mobile');
					}

					break;

					case 'address' : 
					
					if (! this.singleApplicantData.address) {
						
						this.errors.address = 'Address is required'

					}
					else if (! this.singleApplicantData.address.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {

						this.errors.address = 'No special characters';

					}
					else{
						this.submitForm = true;
						this.$delete(this.errors, 'address');
					}

					break;

					case 'educational_highest_level' : 
					
					if (! this.singleApplicantData.educational_highest_level) {
						
						this.errors.educational_highest_level = 'Education level is required'

					}
					else{
						this.submitForm = true;
						this.$delete(this.errors, 'educational_highest_level');
					}

					break;

					case 'resume' : 
					
					if (! this.singleApplicantData.resume) {
						
						this.errors.resume = 'Resume is required'

					}
					else{
						this.submitForm = true;
						this.$delete(this.errors, 'resume');
					}

					break;	

				}

			}

		}
	}
</script>

<style lang="css" scoped>
</style>