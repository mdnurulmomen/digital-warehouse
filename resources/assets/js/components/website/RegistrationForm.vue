<template>
	<!-- Registration Form Started  -->
	<section class="registration" id="registration">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="reg-form">
						<ul
						class="nav nav-pills justify-content-evenly gap-3 mb-5"
						id="pills-tab"
						role="tablist"
						>
						<li class="nav-item w-100" role="presentation">
							<a
							type="button"
							class="tab-btn active"
							id="pills-owner-tab"
							data-bs-toggle="pill"
							data-bs-target="#pills-owner"
							role="tab" 
							@click="resetMerchantErrors()" 
							>Owner</a
							>
						</li>
						<li class="nav-item w-100" role="presentation">
							<a
							type="button"
							class="tab-btn"
							id="pills-merchant-tab"
							data-bs-toggle="pill"
							data-bs-target="#pills-merchant"
							role="tab" 
							@click="resetOwnerErrors()" 
							>Merchant</a
							>
						</li>
					</ul>
					<div class="tab-content" id="pills-tabContent">
						<div
						class="tab-pane fade show active"
						id="pills-owner"
						role="tabpanel"
						aria-labelledby="pills-owner-tab"
						>
						<div class="row">
							<div class="col-md-6">
								<img
								class="img-fluid reg-img"
								:src="'/website/registrations/owner-reg.jpg'"
								alt=""
								/>
							</div>
							<div class="col-md-6">
								<form
								class="row g-3 form-bg form-horizontal" 
								@submit.prevent="submitOwnerRegistrationForm()" 
								autocomplete="off" 
								>
								<input type="hidden" name="_token" :value="csrf">

								<div class="col-md-6 col-sm-12 form-group">
									<label class="form-label">First Name</label>
									<input 
									type="text" 
									class="form-control" 
									v-model="singleOwnerRegistrationData.first_name" 
									placeholder="Place Your First Name" 
									:class="!errors.owner.first_name ? 'is-valid' : 'is-invalid'" 
									:readonly="formSubmitted" 
									@change="validateFormInput('owner_first_name')" 
									/>
									<div class="invalid-feedback">
										{{ errors.owner.first_name }}
									</div>
								</div>

								<div class="col-md-6 col-sm-12 form-group">
									<label class="form-label">Last Name</label>
									<input 
									type="text" 
									class="form-control" 
									v-model="singleOwnerRegistrationData.last_name" 
									placeholder="Place Your Last Name" 
									:class="!errors.owner.last_name ? 'is-valid' : 'is-invalid'" 
									:readonly="formSubmitted" 
									@change="validateFormInput('owner_last_name')" 
									/>

									<div class="invalid-feedback">
										{{ errors.owner.last_name }}
									</div>
								</div>

								<div class="col-md-6 col-sm-12 form-group">
									<label class="form-label">Email*</label>
									<input
									type="email"
									class="form-control"
									v-model="singleOwnerRegistrationData.email" 
									placeholder="Place Your Email" 
									:class="!errors.owner.email ? 'is-valid' : 'is-invalid'" 
									:readonly="formSubmitted" 
									@change="validateFormInput('owner_email')" 
									/>

									<div class="invalid-feedback">
										{{ errors.owner.email }}
									</div>
								</div>

								<div class="col-md-6 col-sm-12 form-group">
									<label class="form-label">Mobile*</label>
									<input
									type="tel"
									class="form-control"
									v-model="singleOwnerRegistrationData.mobile" 
									placeholder="Place Your Contact No" 
									:class="!errors.owner.mobile ? 'is-valid' : 'is-invalid'" 
									:readonly="formSubmitted" 
									@change="validateFormInput('owner_mobile')" 
									/>

									<div class="invalid-feedback">
										{{ errors.owner.mobile }}
									</div>
								</div>

								<div class="col-md-12 col-sm-12 form-group">
									<label class="form-label">Username</label>
									<input 
									type="text" 
									class="form-control" 
									autocomplete="new-password" 
									v-model="singleOwnerRegistrationData.user_name" 
									placeholder="Username should be unique" 
									:class="!errors.owner.user_name ? 'is-valid' : 'is-invalid'" 
									:readonly="formSubmitted" 
									@change="validateFormInput('owner_user_name')" 
									/>
									<div class="invalid-feedback">
										{{ errors.owner.user_name }}
									</div>
								</div>

								<div class="col-md-12 col-sm-12 form-group">
									<label class="form-label">Password*</label>
									<input
									type="password"
									class="form-control" 
									autocomplete="new-password" 
									v-model="singleOwnerRegistrationData.password" 
									placeholder="Minimum length 8" 
									:class="!errors.owner.password ? 'is-valid' : 'is-invalid'" 
									:readonly="formSubmitted" 
									@change="validateFormInput('owner_password')" 
									/>

									<div class="invalid-feedback">
										{{ errors.owner.password }}
									</div>
								</div>

								<div class="col-md-12 col-sm-12 form-group">
									<label class="form-label">Confirm Password*</label>
									<input
									type="password"
									class="form-control" 
									autocomplete="nope" 
									v-model="singleOwnerRegistrationData.password_confirmation" 
									placeholder="Confirm Your Password" 
									:class="!errors.owner.password_confirmation ? 'is-valid' : 'is-invalid'" 
									:readonly="formSubmitted" 
									@change="validateFormInput('owner_password_confirmation')" 
									/>

									<div class="invalid-feedback">
										{{ errors.owner.password_confirmation }}
									</div>
								</div>

								<div class="col-md-12 col-sm-12 form-group">
									<label class="form-label">
										Number of Warehouses You Want to Rent
									</label>
										<!-- 
										<select 
											class="form-control" 
											:class="!errors.owner.number_rentable_warehouses ? 'is-valid' : 'is-invalid'"
											v-model="singleOwnerRegistrationData.number_rentable_warehouses"
											:readonly="formSubmitted" 
											>
											<option value="" selected="selected"></option>
											<option value="warehouses-1">1 warehouse</option>
											<option value="warehouses-2">2 - 4 warehouses</option>
											<option value="warehouses-3">5 - 10 warehouses</option>
											<option value="warehouses-4">10+ warehouses</option>
										</select> 
									-->

									<input 
									type="number" 
									class="form-control" 
									v-model="singleOwnerRegistrationData.number_rentable_warehouses" 
									placeholder="Number Rentable Warehouses" 
									:class="!errors.owner.number_rentable_warehouses ? 'is-valid' : 'is-invalid'" 
									:readonly="formSubmitted" 
									@change="validateFormInput('number_rentable_warehouses')" 
									/>

									<div class="invalid-feedback">
										{{ errors.owner.number_rentable_warehouses }}
									</div>
								</div>

								<div class="col-md-12 col-sm-12 form-group mb-3">
									<label class="form-label">
										Warehouse Average Size *
									</label>
									<select 
									class="form-control" 
									:class="!errors.owner.available_size ? 'is-valid' : 'is-invalid'"
									v-model="singleOwnerRegistrationData.available_size"
									:readonly="formSubmitted" 
									>
									<option value="" selected="selected"></option>
									<option value="< 10,000 sq ft">< 10,000 sq ft</option>
									<option value="10,000 - 50,000 sq ft">10,000 - 50,000 sq ft</option>
									<option value="50,000+ sq ft">50,000+ sq ft</option>
								</select>

								<div class="invalid-feedback">
									{{ errors.owner.available_size }}
								</div>
							</div>

							<div class="col-md-12 col-sm-12 text-center" v-show="Object.keys(registrationFormFailureMessage).length > 0">
								<ul>
									<li 
									class="text-danger text-start" 
									v-for="x in registrationFormFailureMessage"
									>
									{{ x[0] }}
								</li>
							</ul>
						</div>

						<div class="col-md-12 col-sm-12 text-center" v-show="registrationFormSuccessMessage && Object.keys(registrationFormFailureMessage).length == 0 && Object.keys(errors.owner).length == 0 && Object.keys(errors.merchant).length == 0">
							<span style="color: #23b8bf">
								{{ registrationFormSuccessMessage }}
							</span>
						</div>

						<div class="col-md-12 col-sm-12 text-center" v-show="! submitForm">
							<span class="text-danger small">
								**Please input required fields
							</span>
						</div>

						<div class="col-md-12 col-sm-12 text-end">
							<button type="submit" class="more-btn w-100 btn btn-default" :disabled="formSubmitted">
								Rent My Warehouse
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div
		class="tab-pane fade"
		id="pills-merchant"
		role="tabpanel"
		aria-labelledby="pills-merchant-tab"
		>
		<div class="row">
			<div class="col-md-6">
				<form
				class="row g-3 form-bg form-horizontal" 
				@submit.prevent="submitMerchantRegistrationForm()" 
				autocomplete="off" 
				>
				<input type="hidden" name="_token" :value="csrf">

				<div class="col-md-6 col-sm-12 form-group">
					<label class="form-label">First Name</label>
					<input 
					type="text" 
					class="form-control" 
					v-model="singleMerchantRegistrationData.first_name" 
					placeholder="Place Your First Name" 
					:class="!errors.merchant.first_name ? 'is-valid' : 'is-invalid'" 
					:readonly="formSubmitted" 
					@change="validateFormInput('merchant_first_name')" 
					/>
					<div class="invalid-feedback">
						{{ errors.merchant.first_name }}
					</div>
				</div>

				<div class="col-md-6 col-sm-12 form-group">
					<label class="form-label">Last Name</label>
					<input 
					type="text" 
					class="form-control" 
					v-model="singleMerchantRegistrationData.last_name" 
					placeholder="Place Your Last Name" 
					:class="!errors.merchant.last_name ? 'is-valid' : 'is-invalid'" 
					:readonly="formSubmitted" 
					@change="validateFormInput('merchant_last_name')" 
					/>

					<div class="invalid-feedback">
						{{ errors.merchant.last_name }}
					</div>
				</div>

				<div class="col-md-12 col-sm-12 form-group">
					<label class="form-label">Company</label>
					<input 
					type="text"
					class="form-control"
					v-model="singleMerchantRegistrationData.company_name" 
					placeholder="Place Your Company Name" 
					:class="!errors.merchant.company_name ? 'is-valid' : 'is-invalid'" 
					:readonly="formSubmitted" 
					@change="validateFormInput('company_name')" 
					/>

					 
					<div class="invalid-feedback">
						{{ errors.merchant.company_name }}
					</div> 
				</div>

				<div class="col-md-12 col-sm-12 form-group">
					<label class="form-label">Email*</label>
					<input
					type="email"
					class="form-control"
					v-model="singleMerchantRegistrationData.email" 
					placeholder="Place Your Email" 
					:class="!errors.merchant.email ? 'is-valid' : 'is-invalid'" 
					:readonly="formSubmitted" 
					@change="validateFormInput('merchant_email')" 
					/>

					<div class="invalid-feedback">
						{{ errors.merchant.email }}
					</div>
				</div>

				<div class="col-md-6 col-sm-12 form-group">
					<label class="form-label">Mobile*</label>
					<input
					type="tel"
					class="form-control"
					v-model="singleMerchantRegistrationData.mobile" 
					placeholder="Place Your Contact No" 
					:class="!errors.merchant.mobile ? 'is-valid' : 'is-invalid'" 
					:readonly="formSubmitted" 
					@change="validateFormInput('merchant_mobile')" 
					/>

					<div class="invalid-feedback">
						{{ errors.merchant.mobile }}
					</div>
				</div>

				<div class="col-md-6 col-sm-12 form-group">
					<label class="form-label">Username*</label>
					<input 
					type="text" 
					class="form-control" 
					v-model="singleMerchantRegistrationData.user_name" 
					placeholder="Username should be unique" 
					:class="!errors.merchant.user_name ? 'is-valid' : 'is-invalid'" 
					:readonly="formSubmitted" 
					@change="validateFormInput('merchant_user_name')" 
					/>
					<div class="invalid-feedback">
						{{ errors.merchant.user_name }}
					</div>
				</div>

				<div class="col-md-12 col-sm-12 form-group">
					<label class="form-label">Password*</label>
					<input
					type="password"
					class="form-control" 
					autocomplete="new-password" 
					v-model="singleMerchantRegistrationData.password" 
					placeholder="Minimum length 8" 
					:class="!errors.merchant.password ? 'is-valid' : 'is-invalid'" 
					:readonly="formSubmitted" 
					@change="validateFormInput('merchant_password')" 
					/>

					<div class="invalid-feedback">
						{{ errors.merchant.password }}
					</div>
				</div>

				<div class="col-md-12 col-sm-12 form-group">
					<label class="form-label">Confirm Password*</label>
					<input
					type="password"
					class="form-control" 
					autocomplete="new-password" 
					v-model="singleMerchantRegistrationData.password_confirmation" 
					placeholder="Confirm Your Password" 
					:class="!errors.merchant.password_confirmation ? 'is-valid' : 'is-invalid'" 
					:readonly="formSubmitted" 
					@change="validateFormInput('merchant_password_confirmation')" 
					/>

					<div class="invalid-feedback">
						{{ errors.merchant.password_confirmation }}
					</div>
				</div>

				<div class="col-md-12 col-sm-12 form-group">
					<label class="form-label
					form-label-quote">
					Select Warehouse
				</label>
				<select 
				class="form-control is-valid" 
				v-model="singleMerchantRegistrationData.warehouse_id" 
				:readonly="formSubmitted" 
				>
				<option value="1" selected>Warehouse - 1</option>
			</select>

			<!-- 
			<div class="invalid-feedback">
				{{ errors.merchant.warehouse }}
			</div> 
			-->
		</div>

		<div class="col-md-12 col-sm-12 form-group">
			<label class="form-label form-label-quote">
				How you want to store your products? *
			</label>
			<select 
			class="form-control" 
			v-model="singleMerchantRegistrationData.container_type_id" 
			:class="!errors.merchant.container_type ? 'is-valid' : 'is-invalid'" 
			:readonly="formSubmitted" 
			@change="validateFormInput('container_type')" 
			>
			<option disabled selected>Required Space Type</option>
			<option value="1">Grey Space</option>
			<option value="2">Rack</option>
			<option value="3">Pallet</option>
		</select>

		<div class="invalid-feedback">
			{{ errors.merchant.container_type }}
		</div>
	</div>

	<div class="col-md-12 col-sm-12 form-group">
		<label class="form-label form-label-quote">
			Select your preferred rack specification * (LxWxH)
		</label>
		<select
		class="form-control is-valid" 
		v-model="singleMerchantRegistrationData.container_id" 
		:readonly="formSubmitted" 
		>
		<option disabled selected>Required Rack Size</option>
		<option value="1">56X28X17cm (100 Pcs Available)</option>
		<option value="2">86X38X17cm (190 Pcs Available)</option>
		<option value="3">76X48X17cm (50 Pcs Available)</option>
	</select>

			<!-- 
			<div class="invalid-feedback">
				{{ errors.merchant.container }}
			</div> 
		-->
	</div>

	<div class="col-md-12 col-sm-12 form-group mb-3">
		<label class="form-label form-label-quote">
			Roughly how many racks do you need ? *
		</label>

		<input 
		type="number" 
		class="form-control" 
		v-model="singleMerchantRegistrationData.quantity" 
		placeholder="Required Rack Number" 
		:class="!errors.merchant.quantity ? 'is-valid' : 'is-invalid'" 
		:readonly="formSubmitted" 
		@change="validateFormInput('quantity')" 
		/>

		<div class="invalid-feedback">
			{{ errors.merchant.quantity }}
		</div>
	</div> 


	<div class="col-md-12 col-sm-12 text-center" v-show="Object.keys(registrationFormFailureMessage).length > 0">
		<ul>
			<li 
			class="text-danger text-start" 
			v-for="x in registrationFormFailureMessage"
			>
			{{ x[0] }}
		</li>
	</ul>
</div>

<div class="col-md-12 col-sm-12 text-center" v-show="registrationFormSuccessMessage && Object.keys(registrationFormFailureMessage).length == 0 && Object.keys(errors.owner).length == 0 && Object.keys(errors.merchant).length == 0">
	<span style="color: #23b8bf">
		{{ registrationFormSuccessMessage }}
	</span>
</div>

<div class="col-md-12 col-sm-12 text-center" v-show="! submitForm">
	<span class="text-danger small">
		**Please input required fields
	</span>
</div>

<div class="col-md-12 col-sm-12 text-center">
	<button type="submit" class="more-btn w-100 btn btn-default" :disabled="formSubmitted">
		Send Requirements
	</button>
</div>
</form>
</div>
<div class="col-md-6 text-end">
	<img
	class="img-fluid reg-img"
	:src="'/website/registrations/merchant-reg.jpg'"
	alt=""
	/>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</section>
<!-- Registration Form End  -->
</template>

<script>
	export default {
		props : {

			csrf : {
				type : String,
				required : true
			},
			formSubmitted : {
				type : Boolean,
				default: false
			},
			singleOwnerRegistrationData : {
				type : Object,
				required : true
			},
			singleMerchantRegistrationData : {
				type : Object,
				required : true
			},
			registrationFormSuccessMessage : {
				type : String,
				default: null
			},
			registrationFormFailureMessage : {
				type : Object,
				default: {}
			},

		},
		data () {
			return {
				errors : {
					owner : {},
					merchant : {},
				}, 

				submitForm : true,
			}
		},

		methods: {

			submitOwnerRegistrationForm() {

				this.validateFormInput('owner_first_name');
				this.validateFormInput('owner_last_name');
				this.validateFormInput('owner_email');
				this.validateFormInput('owner_mobile');
				this.validateFormInput('owner_user_name');
				this.validateFormInput('owner_password');
				this.validateFormInput('owner_password_confirmation');
				this.validateFormInput('number_rentable_warehouses');
				this.validateFormInput('available_size');

				if (Object.keys(this.errors.owner).length > 0) {
					this.submitForm = false;
					return;
				}
				else {
					this.$emit('submitOwnerRegistrationForm', this.singleOwnerRegistrationData);
				}

			},

			submitMerchantRegistrationForm() {

				this.validateFormInput('merchant_first_name');
				this.validateFormInput('merchant_last_name');
				this.validateFormInput('merchant_email');
				this.validateFormInput('merchant_mobile');
				this.validateFormInput('merchant_user_name');
				this.validateFormInput('merchant_password');
				this.validateFormInput('merchant_password_confirmation');
				this.validateFormInput('company_name');
				// this.validateFormInput('warehouse');
				this.validateFormInput('container_type');
				// this.validateFormInput('container');
				this.validateFormInput('quantity');

				if (Object.keys(this.errors.merchant).length > 0) {
					this.submitForm = false;
					return;
				}
				else {
					this.$emit('submitMerchantRegistrationForm', this.singleMerchantRegistrationData);
				}

			},

			resetOwnerErrors() {
				this.submitForm = true;
				this.errors.owner = {};
				this.$emit('resetParentProps');
			},

			resetMerchantErrors() {
				this.submitForm = true;
				this.errors.merchant = {};
				this.$emit('resetParentProps');
			},

			validateFormInput (formInputName) {

				this.submitForm = false;

				switch(formInputName) {
					
					case 'owner_first_name' : 
					
					if (! this.singleOwnerRegistrationData.first_name && ! this.singleOwnerRegistrationData.last_name) {
						
						this.errors.owner.first_name = 'First or last name is required'

					}
					else if (this.singleOwnerRegistrationData.first_name && ! this.singleOwnerRegistrationData.first_name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {

						this.errors.owner.first_name = 'No special characters';

					}
					else{
						this.submitForm = true;
						this.$delete(this.errors.owner, 'first_name');
						this.$delete(this.errors.owner, 'last_name');
					}

					break;

					case 'owner_last_name' : 
					
					if (! this.singleOwnerRegistrationData.first_name && ! this.singleOwnerRegistrationData.last_name) {
						
						this.errors.owner.last_name = 'First or last name is required'

					}
					else if (this.singleOwnerRegistrationData.last_name && ! this.singleOwnerRegistrationData.last_name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {

						this.errors.owner.last_name = 'No special characters';

					}
					else{
						this.submitForm = true;
						this.$delete(this.errors.owner, 'first_name');
						this.$delete(this.errors.owner, 'last_name');
					}

					break;

					case 'owner_email' : 
					
					if (! this.singleOwnerRegistrationData.email) {
						this.errors.owner.email = 'Email is required';
					}
					else if (! this.singleOwnerRegistrationData.email.match(/[a-z0-9._+-]+@[a-z0-9.-]+\.[a-z]{2,}$/g)) {
						this.errors.owner.email = 'Invalid email address';
					}
					else{
						this.submitForm = true;
						this.$delete(this.errors.owner, 'email');
					}

					break;

					case 'owner_mobile' :

					if (! this.singleOwnerRegistrationData.mobile) {
						this.errors.owner.mobile = 'Mobile is required';
					}
					else if (! this.singleOwnerRegistrationData.mobile.match(/\+?(88)?0?1[123456789][0-9]{8}\b/g)) {
						this.errors.owner.mobile = 'Invalid mobile number';
					}
					else{
						this.submitForm = true;
						this.$delete(this.errors.owner, 'mobile');
					}

					break;

					case 'owner_user_name' :

					if (! this.singleOwnerRegistrationData.user_name) {
						this.errors.owner.user_name = 'Username is required';
					}
					else if (! this.singleOwnerRegistrationData.user_name.match(/^[-\w\.\$@\*\!]{3,30}$/g)) {
						this.errors.owner.user_name = 'No special character';
					}
					else{
						this.submitForm = true;
						this.$delete(this.errors.owner, 'user_name');
					}

					break;

					case 'owner_password' :

					if (!this.singleOwnerRegistrationData.password) {
						this.errors.owner.password = 'Password is required';
					}
					else if (this.singleOwnerRegistrationData.password.length < 8) {
						this.errors.owner.password = 'Minimum length should be 8';
					}
					else{
						this.submitForm = true;
						this.$delete(this.errors.owner, 'password');
					}

					break;

					case 'owner_password_confirmation' :

					if (this.singleOwnerRegistrationData.password && ! this.singleOwnerRegistrationData.password_confirmation) {
						this.errors.owner.password_confirmation = 'Password is required';
					}
					else if (this.singleOwnerRegistrationData.password && this.singleOwnerRegistrationData.password !== this.singleOwnerRegistrationData.password_confirmation) {
						this.errors.owner.password_confirmation = "Password doesn't match";
					}
					else{
						this.submitForm = true;
						this.$delete(this.errors.owner, 'password_confirmation');
					}

					break;

					case 'number_rentable_warehouses' :

					if (! this.singleOwnerRegistrationData.number_rentable_warehouses) {
						this.errors.owner.number_rentable_warehouses = 'Warehouse number is required';
					}
					else if (this.singleOwnerRegistrationData.number_rentable_warehouses < 0) {
						this.errors.owner.number_rentable_warehouses = 'Invalid warehouse number';
					}
					else{
						this.submitForm = true;
						this.$delete(this.errors.owner, 'number_rentable_warehouses');
					}

					break;

					case 'available_size' :

					if (! this.singleOwnerRegistrationData.available_size) {
						this.errors.owner.available_size = 'Warehouse number is required';
					}
					/*
					else if (! this.singleOwnerRegistrationData.available_size < 0) {
						this.errors.owner.available_size = 'Invalid warehouse number';
					}
					*/
					else{
						this.submitForm = true;
						this.$delete(this.errors.owner, 'available_size');
					}

					break;

					case 'merchant_first_name' : 
					
					if (! this.singleMerchantRegistrationData.first_name && ! this.singleMerchantRegistrationData.last_name) {
						
						this.errors.merchant.first_name = 'First or last name is required'

					}
					else if (this.singleMerchantRegistrationData.first_name && ! this.singleMerchantRegistrationData.first_name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {

						this.errors.merchant.first_name = 'No special characters';

					}
					else{
						this.submitForm = true;
						this.$delete(this.errors.merchant, 'first_name');
						this.$delete(this.errors.merchant, 'last_name');
					}

					break;

					case 'merchant_last_name' : 
					
					if (! this.singleMerchantRegistrationData.first_name && ! this.singleMerchantRegistrationData.last_name) {
						
						this.errors.merchant.last_name = 'First or last name is required'

					}
					else if (this.singleMerchantRegistrationData.last_name && ! this.singleMerchantRegistrationData.last_name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {

						this.errors.merchant.last_name = 'No special characters';

					}
					else{
						this.submitForm = true;
						this.$delete(this.errors.merchant, 'first_name');
						this.$delete(this.errors.merchant, 'last_name');
					}

					break;

					case 'merchant_email' : 
					
					if (! this.singleMerchantRegistrationData.email) {
						this.errors.merchant.email = 'Email is required';
					}
					else if (! this.singleMerchantRegistrationData.email.match(/[a-z0-9._+-]+@[a-z0-9.-]+\.[a-z]{2,}$/g)) {
						this.errors.merchant.email = 'Invalid email address';
					}
					else{
						this.submitForm = true;
						this.$delete(this.errors.merchant, 'email');
					}

					break;

					case 'merchant_mobile' :

					if (! this.singleMerchantRegistrationData.mobile) {
						this.errors.merchant.mobile = 'Mobile is required';
					}
					else if (! this.singleMerchantRegistrationData.mobile.match(/\+?(88)?0?1[123456789][0-9]{8}\b/g)) {
						this.errors.merchant.mobile = 'Invalid mobile number';
					}
					else{
						this.submitForm = true;
						this.$delete(this.errors.merchant, 'mobile');
					}

					break;

					case 'merchant_user_name' :

					if (! this.singleMerchantRegistrationData.user_name) {
						this.errors.merchant.user_name = 'Username is required';
					}
					else if (! this.singleMerchantRegistrationData.user_name.match(/^[-\w\.\$@\*\!]{3,30}$/g)) {
						this.errors.merchant.user_name = 'No special character';
					}
					else{
						this.submitForm = true;
						this.$delete(this.errors.merchant, 'user_name');
					}

					break;

					case 'merchant_password' :

					if (!this.singleMerchantRegistrationData.password) {
						this.errors.merchant.password = 'Password is required';
					}
					else if (this.singleMerchantRegistrationData.password.length < 8) {
						this.errors.merchant.password = 'Minimum length should be 8';
					}
					else{
						this.submitForm = true;
						this.$delete(this.errors.merchant, 'password');
					}

					break;

					case 'merchant_password_confirmation' :

					if (this.singleMerchantRegistrationData.password && ! this.singleMerchantRegistrationData.password_confirmation) {
						this.errors.merchant.password_confirmation = 'Password is required';
					}
					else if (this.singleMerchantRegistrationData.password && this.singleMerchantRegistrationData.password !== this.singleMerchantRegistrationData.password_confirmation) {
						this.errors.merchant.password_confirmation = "Password doesn't match";
					}
					else{
						this.submitForm = true;
						this.$delete(this.errors.merchant, 'password_confirmation');
					}

					break;

					case 'company_name' : 
					
					if (this.singleMerchantRegistrationData.company_name && ! this.singleMerchantRegistrationData.company_name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {

						this.errors.merchant.company_name = 'No special characters';

					}
					else{
						this.submitForm = true;
						this.$delete(this.errors.merchant, 'company_name');
					}

					break;

					/*
					case 'warehouse' :

					if (! this.singleMerchantRegistrationData.warehouse) {
						this.errors.merchant.warehouse = 'Warehouse is required';
							// this.$set(this.errors.merchant, 'warehouse', 'Warehouse is required');
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.merchant, 'warehouse');
						}

						break;
					*/

					case 'container_type' :

					if (! this.singleMerchantRegistrationData.container_type_id) {
						this.errors.merchant.container_type = 'Space type is required';
						// this.$set(this.errors.merchant, 'container_type', 'Space type is required');
					}
					else{
						this.submitForm = true;
						this.$delete(this.errors.merchant, 'container_type');
					}

					break;
						
					/*
					case 'container' :

					if (! this.singleMerchantRegistrationData.container) {
						this.errors.merchant.container = 'Space size is required';
					}
					else{
						this.submitForm = true;
						this.$delete(this.errors, 'container');
					}

					break;
					*/

					case 'quantity' :

					if (! this.singleMerchantRegistrationData.quantity) {
						this.errors.merchant.quantity = 'Quantity is required';
						// this.$set(this.errors.merchant, 'quantity', 'Quantity is required');
					}
					else if (this.singleMerchantRegistrationData.quantity < 1) {
						this.errors.merchant.quantity = 'Invalid quantity';
						// this.$set(this.errors.merchant, 'quantity', 'Invalid quantity');
					}
					else {
						this.submitForm = true;
						this.$delete(this.errors.merchant, 'quantity');
					}

					break;
						

				}

			}

		}
		
	}
</script>

<style lang="css" scoped>
</style>