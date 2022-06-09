<template>
	<!-- Contact Form Started  -->
	<section class="contact" id="send-us-a-message">
		<div class="container">
			<div class="row g-4">
				<div class="col-lg-7 col-12" data-aos="fade-up" data-aos-duration="1000">
					<form
					class="row g-3 form-bg form-horizontal" 
					@submit.prevent="submitContactQuery()" 
					autocomplete="off" 
					>
					<input type="hidden" name="_token" :value="csrf">

					<div class="col-12">
						<h4>Send Us a Message</h4>
					</div>

					<div class="col-md-6 col-sm-12 form-group">
						<label class="form-label">First Name</label>
						<input 
							type="text" 
							class="form-control" 
							v-model="singleContactQueryData.first_name" 
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
						<label class="form-label">Last Name</label>
						<input 
							type="text" 
							class="form-control" 
							v-model="singleContactQueryData.last_name" 
							placeholder="Place Your Last Name" 
							:class="!errors.last_name ? 'is-valid' : 'is-invalid'" 
							:readonly="formSubmitted" 
							@change="validateFormInput('last_name')" 
						/>

						<div class="invalid-feedback">
				        	{{ errors.last_name }}
				  		</div>
					</div>
					
					<div class="col-md-6 col-sm-12 form-group">
						<label class="form-label">Email*</label>
						<input
						type="email"
						class="form-control"
						v-model="singleContactQueryData.email" 
						placeholder="Place Your Email" 
						:class="!errors.email ? 'is-valid' : 'is-invalid'" 
						:readonly="formSubmitted" 
						@change="validateFormInput('email')" 
						/>

						<div class="invalid-feedback">
				        	{{ errors.email }}
				  		</div>
					</div>

					<div class="col-md-6 col-sm-12 form-group">
						<label class="form-label">Contact No*</label>
						<input
						type="tel"
						class="form-control"
						v-model="singleContactQueryData.contact_no" 
						placeholder="Place Your Contact No" 
						:class="!errors.contact_no ? 'is-valid' : 'is-invalid'" 
						:readonly="formSubmitted" 
						@change="validateFormInput('contact_no')" 
						/>

						<div class="invalid-feedback">
				        	{{ errors.contact_no }}
				  		</div>
					</div>

					<div class="col-12 form-group">
						<label class="form-label">Subject*</label>
						<input 
						type="text"
						class="form-control"
						v-model="singleContactQueryData.subject" 
						placeholder="Place Your Email" 
						:class="!errors.subject ? 'is-valid' : 'is-invalid'" 
						:readonly="formSubmitted" 
						@change="validateFormInput('subject')" 
						/>

						<div class="invalid-feedback">
				        	{{ errors.subject }}
				  		</div>
					</div>

					<div class="col-12 form-group mb-3">
						<label class="form-label">Message</label>
						<textarea 
						rows="4" 
						class="form-control" 
						:class="!errors.message ? 'is-valid' : 'is-invalid'"
						v-model="singleContactQueryData.message"
						:readonly="formSubmitted" 
						>{{ singleContactQueryData.message }}</textarea>

						<div class="invalid-feedback">
				        	{{ errors.message }}
				  		</div>
					</div>

					<div class="col-sm-12 text-center" v-show="contactQueryFormFailureMessages.length">
						<span class="text-danger" v-for="contactQueryMessage in contactQueryFormFailureMessages">
					  		{{ contactQueryMessage }}
					  	</span>
					</div>

					<div class="col-sm-12 text-center" v-show="contactQueryFormSuccessMessage">
						<span style="color: #23b8bf">
					  		{{ contactQueryFormSuccessMessage }}
					  	</span>
					</div>

					<div class="col-sm-12 text-right" v-show="!submitForm">
						<span class="text-danger small">
					  		**Please input required fields
					  	</span>
					</div>

					<div class="col-12 text-end">
						<button type="submit" class="more-btn w-100 btn btn-default" :disabled="formSubmitted">Send A Message</button>
					</div>
				</form>
			</div>
			<div class="col-lg-5 col-12">
				<div class="row g-4">
					<div class="col-12" data-aos="fade-up" data-aos-duration="1000">
						<div class="address">
							<h4 style="color: #23b8bf">Office Address</h4>
							<p>HB Tower, Unit-401, House-1/A, Road 23</p>
							<p>Gulshan - 1, Dhaka - 1212</p>
							<p class="link-text"><a href="tel:+8801971234727">+880 1971-234727</a></p>
							<p class="link-text"><a href="mailto:contact@gudam.com.bd">contact@gudam.com.bd</a></p>
						</div>
						<div class="map h-100" data-aos="zoom-in" data-aos-duration="1000">
							<iframe id="gmap_canvas" src="https://maps.google.com/maps?q=Kiva%20Han%20Gulshan%201&amp;t=&amp;z=15&amp;ie=UTF8&amp;iwloc=&amp;output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" style="width:100%;height:100%"></iframe>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- Contact Form End  -->
</template>

<script>
	export default {
		props : {

			csrf : {
				type : String,
				required : true
			},
			singleContactQueryData : {
				type : Object,
				required : true
			},
			formSubmitted : {
				type : Boolean,
				default: false
			},
			contactQueryFormSuccessMessage : {
				type : String,
				default: null
			},
			contactQueryFormFailureMessages : {
				type : Array,
				default: []
			},

		},

		data () {
			return {
				errors : {}, 
				submitForm : true,
			}
		},

		methods: {

			submitContactQuery() {

				this.validateFormInput('first_name');
				this.validateFormInput('last_name');
				this.validateFormInput('email');
				this.validateFormInput('contact_no');
				this.validateFormInput('subject');
				this.validateFormInput('message');

				if (Object.keys(this.errors).length > 0) {
					this.submitForm = false;
					return;
				}
				else {
					this.$emit('submitContactQuery', this.singleContactQueryData);
				}

			},

		    validateFormInput (formInputName) {

				this.submitForm = false;

				switch(formInputName) {
				
					case 'first_name' : 
						
						if (! this.singleContactQueryData.first_name && ! this.singleContactQueryData.last_name) {
							
							this.errors.first_name = 'First or last name is required'

						}
						else if (this.singleContactQueryData.first_name && ! this.singleContactQueryData.first_name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {

							this.errors.first_name = 'No special characters';

						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'first_name');
							this.$delete(this.errors, 'last_name');
						}

						break;

					case 'last_name' : 
						
						if (! this.singleContactQueryData.first_name && ! this.singleContactQueryData.last_name) {
							
							this.errors.last_name = 'First or last name is required'

						}
						else if (this.singleContactQueryData.last_name && ! this.singleContactQueryData.last_name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {

							this.errors.last_name = 'No special characters';

						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'first_name');
							this.$delete(this.errors, 'last_name');
						}

						break;

					case 'email' : 
						
						if (! this.singleContactQueryData.email) {
							this.errors.email = 'Email is required';
						}
						else if (! this.singleContactQueryData.email.match(/[a-z0-9._+-]+@[a-z0-9.-]+\.[a-z]{2,}$/g)) {
							this.errors.email = 'Invalid email address';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'email');
						}

						break;

					case 'contact_no' :

						if (! this.singleContactQueryData.contact_no) {
							this.errors.contact_no = 'Mobile is required';
						}
						else if (! this.singleContactQueryData.contact_no.match(/\+?(88)?0?1[123456789][0-9]{8}\b/g)) {
							this.errors.contact_no = 'Invalid contact number';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'contact_no');
						}

						break;

					case 'subject' :

						if (!this.singleContactQueryData.subject) {
							this.errors.subject = 'Subject is required';
						}
						else if (! this.singleContactQueryData.subject.match(/^[A-Za-z0-9\s\-_,\.;:()]+$/g)) {
							this.errors.subject = 'No special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'subject');
						}

						break;

					case 'message' :

						if (! this.singleContactQueryData.message) {
							this.errors.message = 'Message is required';
						}
						else if (! this.singleContactQueryData.message.match(/^[A-Za-z0-9\s\-_,\.;:()]+$/g)) {
							this.errors.message = 'No special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'message');
						}

						break;
				}
			}

		}
	}
</script>

<style lang="css" scoped>
</style>