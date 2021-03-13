<template>
	
	<!-- Modal -->
	<div class="modal fade" id="user-createOrEdit-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">
						{{ createMode ? 'Create' : 'Edit' }} {{ user | capitalize }}
						<span v-show="!createMode">
							({{ singleUserDetails.user_name }})
						</span>
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

						<div class="card">
					    	<div class="card-body">
					    		<div class="form-row d-flex align-items-center">
									<div class="form-group col-md-6">
										<img class="profile-user-img img-fluid img-circle" 
											:src="singleUserDetails.profile_preview.preview || ''"
											alt="User Picture" 
										>
									</div>
									<div class="form-group col-md-6">
										<div class="custom-file">
										    <input type="file" 
										    	class="form-control custom-file-input" 
												:class="!errors.user.profile_preview  ? 'is-valid' : 'is-invalid'" 
									    	 	@change="onImageChange" 
									    	 	accept="image/*"
										    >
										    <label class="custom-file-label" for="validatedCustomFile">Choose Picture...</label>
										    <div class="invalid-feedback">
										    	{{ errors.user.profile_preview }}
										    </div>
									  	</div>
									</div>
								</div>
								<div class="form-row">
									<div class="form-group col-md-12 text-right">
										<toggle-button 
											v-model="singleUserDetails.active" 
											:width=100 
											:sync="true"
											:color="{checked: 'green', unchecked: 'red'}"
											:labels="{checked: 'Active', unchecked: 'Pending'}"
										/>
									</div>
								</div>
					    	</div>
					  	</div>

						<div class="form-row">
							<div class="form-group col-md-6">
								<label for="inputFirstName">First Name</label>
								<input type="text" 
									class="form-control" 
									v-model="singleUserDetails.first_name" 
									placeholder="First Name" 
									:class="!errors.user.first_name  ? 'is-valid' : 'is-invalid'" 
									@change="validateFormInput('first_name')"
								>

								<div class="invalid-feedback">
						        	{{ errors.user.first_name }}
						  		</div>
							</div>
							<div class="form-group col-md-6">
								<label for="inputLastName">Last Name</label>
								<input type="text" 
									class="form-control" 
									v-model="singleUserDetails.last_name" 
									placeholder="Last Name" 
									:class="!errors.user.last_name  ? 'is-valid' : 'is-invalid'" 
									@change="validateFormInput('last_name')"
								>

								<div class="invalid-feedback">
						        	{{ errors.user.last_name }}
						  		</div>
							</div>
						</div>
						<div class="form-row">
							<div class="form-group col-md-6">
								<label for="inputEmail4">Email</label>
								<input type="email" 
									class="form-control" 
									v-model="singleUserDetails.email" 
									placeholder="First Name" 
									required="true" 
									:class="!errors.user.email  ? 'is-valid' : 'is-invalid'" 
									@change="validateFormInput('email')"
								>

								<div class="invalid-feedback">
						        	{{ errors.user.email }}
						  		</div>
							</div>
							<div class="form-group col-md-6">
								<label for="inputMobile4">Mobile</label>
								<input type="text" 
									class="form-control" 
									v-model="singleUserDetails.mobile" 
									placeholder="Mobile Number" 
									required="true" 
									:class="!errors.user.mobile  ? 'is-valid' : 'is-invalid'" 
									@change="validateFormInput('mobile')"
								>

								<div class="invalid-feedback">
						        	{{ errors.user.mobile }}
						  		</div>
							</div>
						</div>
						<div class="form-row">
							<div class="form-group col-md-12">
								<label for="inputUsername">Username</label>
								<input type="text" 
									class="form-control" 
									v-model="singleUserDetails.user_name" 
									placeholder="Username" 
									required="true" 
									:class="!errors.user.user_name  ? 'is-valid' : 'is-invalid'" 
									@change="validateFormInput('user_name')"
								>

								<div class="invalid-feedback">
						        	{{ errors.user.user_name }}
						  		</div>
							</div>
						</div>
						
						<div class="form-row">
							<div class="form-group col-sm-12 text-right">
								<a data-toggle="collapse" href="#user-password">
									<i class="fa fa-2x" :class="createMode ? 'fa-angle-up' : 'fa-angle-down'" @click="changeArrow"></i>
								</a>
							</div>
						</div>
							
						<div id="user-password" class="panel-collapse collapse" :class="createMode ? 'show' : ''">
							<div class="form-row">
								<div class="form-group col-md-6">
									<label for="inputPassword4">Password</label>
									<input type="password" 
										class="form-control" 
										v-model="singleUserDetails.password" 
										minlength="8" 
										placeholder="Password" 
										:required="createMode ? true : false" 
										:class="!errors.user.password  ? 'is-valid' : 'is-invalid'" 
										@change="validateFormInput('password')" 
										autocomplete="new-password"
									>

									<div class="invalid-feedback">
							        	{{ errors.user.password }}
							  		</div>
								</div>
								<div class="form-group col-md-6">
									<label for="inputPassword4">Confirm Password</label>
									<input type="password" 
										class="form-control" 
										v-model="singleUserDetails.password_confirmation" 
										placeholder="Confirm Password" 
										:required="createMode ? true : false" 
										:class="!errors.user.password_confirmation  ? 'is-valid' : 'is-invalid'" 
										@change="validateFormInput('password_confirmation')"
									>

									<div class="invalid-feedback">
							        	{{ errors.user.password_confirmation }}
							  		</div>
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
			user : {
				type : String,
				required : true
			},
			singleUserDetails : {
				type : Object,
				required : true
			},

		},

		data() {

			return {

				submitForm : true,
				
				errors : {
					user : {},
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

			onImageChange(evnt) {
				
				let files = evnt.target.files || evnt.dataTransfer.files;

                // Only process image files.
		      	if (files.length && files[0].type.match('image.*')) {
                	this.createImage(files[0]);
                	this.$delete(this.errors.user, 'profile_preview');
		      	}
		      	else{
		      		this.errors.user.profile_preview = 'File should be image';
		      	}

		      	evnt.target.value = '';
		      	return;

			},
			createImage(file) {

                let reader = new FileReader();
                reader.onload = (evnt) => {
                    this.singleUserDetails.profile_preview.preview = evnt.target.result;
                };
                reader.readAsDataURL(file);

            },
            verifyUserInput() {

				this.validateFormInput('user_name');
				this.validateFormInput('mobile');
				this.validateFormInput('email');

				if (this.createMode || this.singleUserDetails.password || this.singleUserDetails.password_confirmation) {
					this.validateFormInput('password');
					this.validateFormInput('password_confirmation');
				}
            	
            	if (Object.keys(this.errors.user).length !== 0 && this.errors.user.constructor === Object) {
					this.submitForm = false;
					return;
				}
				else {

	            	if (this.createMode) {
	            		
	            		this.$emit('storeUser', this.singleUserDetails);
	            	}

	            	else {

	            		this.$emit('updateUser', this.singleUserDetails)
	            	}

				}

            },
			validateFormInput (formInputName) {
				
				this.submitForm = false;

				switch(formInputName) {

					case 'first_name' :

						if (!this.singleUserDetails.first_name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.user.first_name = 'No special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.user, 'first_name');
						}

						break;

					case 'last_name' :

						if (!this.singleUserDetails.last_name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.user.last_name = 'No special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.user, 'last_name');
						}

						break;

					case 'user_name' :

						if (!this.singleUserDetails.user_name) {
							this.errors.user.user_name = 'Username is required';
						}
						else if (!this.singleUserDetails.user_name.match(/^[-\w\.\$@\*\!]{3,30}$/g)) {
							this.errors.user.user_name = 'No special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.user, 'user_name');
						}

						break;

					case 'mobile' :

						if (!this.singleUserDetails.mobile) {
							this.errors.user.mobile = 'Mobile is required';
						}
						else if (!this.singleUserDetails.mobile.match(/\+?(88)?0?1[123456789][0-9]{8}\b/g)) {
							this.errors.user.mobile = 'Invalid mobile number';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.user, 'mobile');
						}

						break;

					case 'email' :

						if (!this.singleUserDetails.email) {
							this.errors.user.email = 'Email is required';
						}
						else if (!this.singleUserDetails.email.match(/[a-z0-9._+-]+@[a-z0-9.-]+\.[a-z]{2,}$/g)) {
							this.errors.user.email = 'Invalid email address';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.user, 'email');
						}

						break;

					case 'password' :

						if (this.createMode && !this.singleUserDetails.password) {
							this.errors.user.password = 'Password is required';
						}
						else if (this.singleUserDetails.password.length < 8) {
							this.errors.user.password = 'Minimum length should be 8';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.user, 'password');
						}

						break;

					case 'password_confirmation' :

						if (this.singleUserDetails.password && !this.singleUserDetails.password_confirmation) {
							this.errors.user.password_confirmation = 'Password is required';
						}
						else if (this.singleUserDetails.password && this.singleUserDetails.password !== this.singleUserDetails.password_confirmation) {
							this.errors.user.password_confirmation = "Password doesn't match";
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.user, 'password_confirmation');
						}

						break;

				}
	 
			},
			changeArrow (event){
			
				if (event.target.classList.contains("fa-angle-up")) {
					event.target.classList.replace("fa-angle-up", "fa-angle-down");
				}
				else {
					event.target.classList.replace("fa-angle-down", "fa-angle-up");
				}
			
			}

		}

	}

</script>