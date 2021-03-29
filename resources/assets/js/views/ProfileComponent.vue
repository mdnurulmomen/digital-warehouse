
<template>

	<div class="pcoded-content">

		<div class="page-header card">
			<div class="row align-items-end">
				<div class="col-lg-8">
					<div class="page-header-title">
						<i class="feather icon-user bg-c-blue"></i>
						<div class="d-inline">
							<h5>Profile</h5>
							<span>
								Here, you may update your profile basic info's
							</span>
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
								<router-link :to="{ name: 'profile' }" class="waves-effect waves-dark">
									Profile
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

						      			<ul class="nav nav-tabs md-tabs" role="tablist">
						      				<li class="nav-item">
						      					<a class="nav-link active show" data-toggle="tab" href="#profile" role="tab" aria-selected="true">
						      						<span class="sub-title">Profile</span>
						      					</a>
						      					<div class="slide"></div>
						      				</li>
						      				<li class="nav-item">
						      					<a class="nav-link" data-toggle="tab" href="#password" role="tab" aria-selected="false">
						      						<span class="sub-title">Password</span>
						      					</a>
						      					<div class="slide"></div>
						      				</li>
						      			</ul>

						      			<div class="tab-content card-block">
						      				<div class="tab-pane active show" id="profile" role="tabpanel">
						      					
						      					<!-- form start -->
											  	<form class="form-horizontal" v-on:submit.prevent="profileUpdation">

										      		<input type="hidden" name="_token" :value="csrf">

							      					<div class="row" v-if="user">
							      						<div class="col-sm-3">
															<div class="card">
												              	<div class="card-body box-profile">
												                	<div class="text-center">
												                  		<img class="profile-user-img img-fluid img-circle" :src="user.profile_preview.preview || ''" alt="User Picture">
												                	</div>

												                	<h3 class="profile-username text-center">
												                		{{ user.user_name }}
												                	</h3>

												                	<p class="text-muted text-center">
												                		<span v-for="userRole in user.roles">
												                			{{ userRole.name | capitalize }}
												                			<span v-show="user.roles.length > 1">
												                				, 
												                			</span> 
												                		</span>
												                	</p>

												                	<div class="row">
														                <div class="col-sm-12">
														                  	<div class="input-group">
															                    <div class="custom-file">
															                        <input 
																                        type="file" 
																                        class="custom-file-input" 
																                        @change="onImageChange" 
																                        accept="image/*"
															                        >
															                        <label class="custom-file-label" for="exampleInputFile">
															                        	Change Picture
															                        </label>
															                    </div>
															                    <div class="invalid-feedback" style="display: block;" v-show="errors.user.profile_preview">
																		        	{{ errors.user.profile_preview }}
																		  		</div>
														                    </div>
														                </div>
													            	</div>
												              	</div>
												              	<!-- /.card-body -->
												            </div>
														</div>
														<div class="col-sm-9">
															<div class="card">
													            <div class="card-body box-profile">
													              	<div class="form-group row">
														              	<div class="col-6">
														              		<div class="row">
															              		<label for="inputFirstName3" class="col-sm-4 col-form-label text-right">First Name</label>
																                <div class="col-sm-8">
																                  	<input 
																	                  type="text" 
																	                  class="form-control" 
																	                  v-model="user.first_name" 
																	                  placeholder="First Name" 
																	                  :class="!errors.user.first_name  ? 'is-valid' : 'is-invalid'" 
																	                  @change="validateFormInput('first_name')"
																					>
																					<div class="invalid-feedback">
																			        	{{ errors.user.first_name }}
																			  		</div>
																                </div>
														              		</div>
														              	</div>
														                <div class="col-6">
														                	<div class="row">
															              		<label for="inputLastName3" class="col-sm-4 col-form-label text-right">Last Name</label>
																                <div class="col-sm-8">
																                  	<input 
																	                  	type="text" 
																	                  	class="form-control" 
																	                  	v-model="user.last_name" 
																	                  	placeholder="Last Name" 
																	                  	:class="!errors.user.last_name  ? 'is-valid' : 'is-invalid'" 
																	                  	@change="validateFormInput('last_name')"
																                  	>
																                  	<div class="invalid-feedback">
																			        	{{ errors.user.last_name }}
																			  		</div>
																                </div>
														                	</div>
														              	</div>
													              	</div>
													              	<div class="form-group row">
														              	<div class="col-6">
														              		<div class="row">
															              		<label for="inputEmail3" class="col-sm-4 col-form-label text-right">Email</label>
																                <div class="col-sm-8">
																                  	<input 
																                  		type="email" 
																                  		class="form-control" 
																                  		v-model="user.email" 
																                  		placeholder="Email" required="true" 
																                  		:class="!errors.user.email  ? 'is-valid' : 'is-invalid'" 
														                  				@change="validateFormInput('email')"
																                  	>
																                  	<div class="invalid-feedback">
																			        	{{ errors.user.email }}
																			  		</div>
																                </div>
														              		</div>
														              	</div>
														                <div class="col-6">
														                	<div class="row">
														                		<label for="inputMobile3" class="col-sm-4 col-form-label text-right">Mobile</label>
																                <div class="col-sm-8">
																                  	<input 
																	                  	type="tel" 
																	                  	class="form-control" 
																	                  	v-model="user.mobile" 
																	                  	placeholder="Mobile" 
																	                  	required="true" 
																	                  	:class="!errors.user.mobile  ? 'is-valid' : 'is-invalid'" 
														                  				@change="validateFormInput('mobile')"
																                  	>
																                  	<div class="invalid-feedback">
																			        	{{ errors.user.mobile }}
																			  		</div>
																                </div>
														                	</div>
														              	</div>
													              	</div>
													              	<div class="form-group row">
														              	<div class="col-6">
														              		<div class="row">
															              		<label for="inputEmail3" class="col-sm-4 col-form-label text-right">Username</label>
																                <div class="col-sm-8">
																                  	<input 
																	                  	type="text" 
																	                  	class="form-control" 
																	                  	v-model="user.user_name" 
																	                  	placeholder="Unique Username" 
																	                  	required="true" 
																	                  	:class="!errors.user.user_name  ? 'is-valid' : 'is-invalid'" 
													                  					@change="validateFormInput('user_name')"
																                  	>
																                  	<div class="invalid-feedback">
																			        	{{ errors.user.user_name }}
																			  		</div>
																                </div>
														              		</div>
														              	</div>
													              	</div>
													            </div>
													            <!-- /.card-body -->
													            <div class="card-footer text-center">
																	<div class="col-sm-12" v-show="!submitForm">
																		<span class="text-danger small">
																	  		Please input required fields
																	  	</span>
																	</div>
																	<div class="col-sm-12">
																		<button type="submit" class="btn btn-primary" :disabled="!submitForm">
																			Update Profile
																		</button>
																	</div>
																</div>
													        	<!-- /.card-footer -->
														    </div>
														</div>
							      					</div>
							      				</form>
						      				</div>
						      				<div class="tab-pane" id="password" role="tabpanel">
						      					
						      					<form class="form-horizontal" v-on:submit.prevent="passwordUpdation">
				      		
										      		<input type="hidden" name="_token" :value="csrf">
										            
										            <div class="card-body box-profile">  

										              	<div class="form-group row">
										              		<label for="inputPassword3" class="col-sm-3 col-form-label text-right">Current Password</label>
											                <div class="col-sm-9">
											                  	<input 
												                  	type="password" 
												                  	class="form-control" 
												                  	v-model="password.current_password" 
												                  	placeholder="Current Password" 
												                  	required="true" 
												                  	:class="!errors.user.current_password  ? 'is-valid' : 'is-invalid'" 
									                  				@change="validateFormInput('current_password')"
											                  	>
											                  	<div class="invalid-feedback">
														        	{{ errors.user.current_password }}
														  		</div>
											                </div>
										              	</div>
										              	<div class="form-group row">
										              		<label for="inputNewPassword3" class="col-sm-3 col-form-label text-right">New Password</label>
											                <div class="col-sm-9">
											                  	<input 
												                  	type="password" 
												                  	class="form-control" 
												                  	v-model="password.password" 
												                  	placeholder="New Password" 
												                  	required="true" 
												                  	:class="!errors.user.password  ? 'is-valid' : 'is-invalid'" 
									                  				@change="validateFormInput('password')"
											                  	>
											                  	<div class="invalid-feedback">
														        	{{ errors.user.password }}
														  		</div>
											                </div>
										              	</div>
										              	<div class="form-group row">
										              		<label for="inputConfirmPassword3" class="col-sm-3 col-form-label text-right">Confirm Password</label>
											                <div class="col-sm-9">
											                  	<input 
												                  	type="password" 
												                  	class="form-control" 
												                  	v-model="password.password_confirmation" 
												                  	placeholder="Confirm Password" 
												                  	required="true" 
												                  	:class="!errors.user.password_confirmation  ? 'is-valid' : 'is-invalid'" 
									                  				@change="validateFormInput('password_confirmation')"
											                  	>
											                  	<div class="invalid-feedback">
														        	{{ errors.user.password_confirmation }}
														  		</div>
											                </div>
										              	</div>

										            </div>
										            <!-- /.card-body -->
										            <div class="card-footer text-center">
														<div class="col-sm-12" v-show="!submitForm">
															<span class="text-danger small">
														  		Please input required fields
														  	</span>
														</div>
														<div class="col-sm-12">
															<button type="submit" class="btn btn-primary" :disabled="!submitForm">
																Update Password
															</button>
														</div>
													</div>
										        	<!-- /.card-footer -->
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

</template>

<script type="text/javascript">

	import axios from 'axios';

	export default {

	    data() {
	        return {
	        	user : {
					first_name : null,
					last_name: null,
					user_name : null,
	        		email : null,
					mobile : null,
					profile_preview : {},
	        	},
	        	password : {},

	        	errors : {
	        		user : {},
	        	},

	        	error : '',
	        	loading : false,

	        	submitForm : true,

	            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
	        }
		},

		created(){
			this.fetchUserData();
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

		computed : {
			fullName : function() {
				if (!this.user.first_name && !this.user.last_name) {
					return 'No Name';
				}

				return (this.user.first_name || '' +' '+ this.user.last_name || '');
			}
		},

		methods : {
			fetchUserData() {
				
				this.error = '';
				this.loading = true;

				axios
					.get('/api/profile')
					.then(response => {
						// handle success
						this.user = response.data || {};
					})
					.catch(error => {
					    // handle error
					    this.error = error.toString();
					})
					.finally(() => {
			    		// always executed
			    		this.loading = false;
				  	});
			},
            profileUpdation() {

				if (!this.user.email || !this.user.mobile || !this.user.user_name) {
					
					this.validateFormInput('user_name');
					this.validateFormInput('mobile');
					this.validateFormInput('email');

					this.submitForm = false;
					return;
				}

				axios
					.put('/profile', this.user)
					.then(response => {
						// handle success
						if (response.status == 200) {
							this.user = response.data || {};
							this.$toastr.s("Profile has been updated", "Success");
						}
					})
					.catch(error => {
						// handle error
						if (error.response.status == 422) {
							for (var x in error.response.data.errors) {
								this.$toastr.w(error.response.data.errors[x], "Warning");
							}
				      	}
					});
			},
			passwordUpdation() {

				if (!this.password.current_password || !this.password.password || !this.password.password_confirmation) {

					this.validateFormInput('current_password');
					this.validateFormInput('password');
					this.validateFormInput('password_confirmation');

					this.submitForm = false;
					return;
				}

				axios
					.post('/password', this.password)
					.then(response => {
						// handle success
						if (response.status == 200) {
							this.password = {};
							this.$toastr.s("Password has been updated", "Success");
						}
					})
					.catch(error => {
						// handle error
						if (error.response.status == 422) {

							for (var x in error.response.data.errors) {
								this.$toastr.w(error.response.data.errors[x], "Warning");
							}
				      	}
				      	else if (error.response.status == 401) {
							
							this.$toastr.e("Wrong Current Password", "Oops");	
				      	}
					});
			},
			onImageChange(evnt){
				let files = evnt.target.files || evnt.dataTransfer.files;

                // Only process image files.
		      	if (files.length && files[0].type.match('image.*')) {
                	this.$delete(this.errors.user, 'profile_preview');
                	this.createImage(files[0]);
		      	}
		      	else {
		      		this.errors.user.profile_preview = 'Please input only image file';
		      	}

		      	evnt.target.value = '';
		      	return;
			},
			createImage(file) {
                let reader = new FileReader();
                reader.onload = (evnt) => {
                    this.user.profile_preview.preview = evnt.target.result;
                };
                reader.readAsDataURL(file);
            },
            validateFormInput (formInputName) {
				
				this.submitForm = false;

				switch(formInputName) {

					case 'first_name' :

						if (!this.user.first_name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.user.first_name = 'No special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.user, 'first_name');
						}

						break;

					case 'last_name' :

						if (!this.user.last_name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.user.last_name = 'No special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.user, 'last_name');
						}

						break;

					case 'user_name' :

						if (!this.user.user_name) {
							this.errors.user.user_name = 'Username is required';
						}
						else if (!this.user.user_name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.user.user_name = 'No special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.user, 'user_name');
						}

						break;

					case 'mobile' :

						if (!this.user.mobile) {
							this.errors.user.mobile = 'Mobile is required';
						}
						else if (!this.user.mobile.match(/\+?(88)?0?1[123456789][0-9]{8}\b/g)) {
							this.errors.user.mobile = 'Invalid mobile number';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.user, 'mobile');
						}

						break;

					case 'email' :

						if (!this.user.email) {
							this.errors.user.email = 'Email is required';
						}
						else if (!this.user.email.match(/[a-z0-9._+-]+@[a-z0-9.-]+\.[a-z]{2,}$/g)) {
							this.errors.user.email = 'Invalid email address';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.user, 'email');
						}

						break;

					case 'current_password' :

						if (!this.password.current_password) {
							this.errors.user.current_password = 'Password is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.user, 'current_password');
						}

						break;

					case 'password' :

						if (!this.password.password) {
							this.errors.user.password = 'Password is required';
						}
						else if (this.password.password.length < 8) {
							this.errors.user.password = 'Minimum length should be 8';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.user, 'password');
						}

						break;

					case 'password_confirmation' :

						if (this.password.password && !this.password.password_confirmation) {
							this.errors.user.password_confirmation = 'Password is required';
						}
						else if (this.password.password && this.password.password !== this.password.password_confirmation) {
							this.errors.user.password_confirmation = "Password doesn't match";
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.user, 'password_confirmation');
						}

						break;

				}
	 
			},
		}
  	}

</script>