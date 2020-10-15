
<template>

	<div class="pcoded-content">

		<div class="page-header card">
			<div class="row align-items-end">
				<div class="col-lg-8">
					<div class="page-header-title">
						<i class="feather icon-user bg-c-blue"></i>
						<div class="d-inline">
							<h5>Profile</h5>
							<span>Here, you may update your profile info</span>
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

												                	<p class="text-muted text-center">Role Name</p>

												                	<div class="row">
														                <div class="col-sm-12">
														                  	<div class="input-group">
															                    <div class="custom-file">
															                        <input type="file" class="custom-file-input" id="exampleInputFile" @change="onImageChange" accept="image/*">
															                        <label class="custom-file-label" for="exampleInputFile">
															                        	Change Picture
															                        </label>
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
																                  <input type="text" class="form-control" v-model="user.first_name" placeholder="First Name">
																                </div>
														              		</div>
														              	</div>
														                <div class="col-6">
														                	<div class="row">
															              		<label for="inputLastName3" class="col-sm-4 col-form-label text-right">Last Name</label>
																                <div class="col-sm-8">
																                  	<input type="text" class="form-control" v-model="user.last_name" placeholder="Last Name">
																                </div>
														                	</div>
														              	</div>
													              	</div>
													              	<div class="form-group row">
														              	<div class="col-6">
														              		<div class="row">
															              		<label for="inputEmail3" class="col-sm-4 col-form-label text-right">Email</label>
																                <div class="col-sm-8">
																                  <input type="email" class="form-control" v-model="user.email" placeholder="Email" required="true">
																                </div>
														              		</div>
														              	</div>
														                <div class="col-6">
														                	<div class="row">
														                		<label for="inputMobile3" class="col-sm-4 col-form-label text-right">Mobile</label>
																                <div class="col-sm-8">
																                  	<input type="tel" class="form-control" v-model="user.mobile" placeholder="Mobile" required="true">
																                </div>
														                	</div>
														              	</div>
													              	</div>
													              	<div class="form-group row">
														              	<div class="col-6">
														              		<div class="row">
															              		<label for="inputEmail3" class="col-sm-4 col-form-label text-right">Username</label>
																                <div class="col-sm-8">
																                  <input type="text" class="form-control" v-model="user.user_name" placeholder="Unique Username" required="true">
																                </div>
														              		</div>
														              	</div>
													              	</div>
													            </div>
													            <!-- /.card-body -->
													            <div class="card-footer text-center">
													              	<button type="submit" :disabled="loading" class="btn btn-primary">
													              		Update Profile
													              	</button>
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
											                  	<input type="password" class="form-control" id="inputPassword3" v-model="password.current_password" placeholder="Current Password" required="true">
											                </div>
										              	</div>
										              	<div class="form-group row">
										              		<label for="inputNewPassword3" class="col-sm-3 col-form-label text-right">New Password</label>
											                <div class="col-sm-9">
											                  	<input type="password" class="form-control" id="inputNewPassword3" v-model="password.password" placeholder="New Password" required="true">
											                </div>
										              	</div>
										              	<div class="form-group row">
										              		<label for="inputConfirmPassword3" class="col-sm-3 col-form-label text-right">Confirm Password</label>
											                <div class="col-sm-9">
											                  	<input type="password" class="form-control" id="inputConfirmPassword3" v-model="password.password_confirmation" placeholder="Confirm Password" required="true">
											                </div>
										              	</div>

										            </div>
										            <!-- /.card-body -->
										            <div class="card-footer text-center">
										              	<button type="submit" :disabled="loading" class="btn btn-primary">Update Password</button>
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

	        	newProfilePicture : null,

	        	errors : {
	        		user : {},
	        	},

	        	loading : false,
	        	submitForm : true,
	            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
	        }
		},
		created(){
			this.fetchUserData();
		},
		computed : {
			fullName : function() {
				if (!this.user.first_name && !this.user.last_name) {
					return 'No Name';
				}

				return (this.user.first_name +' '+ this.user.last_name);
			}
		},
		methods : {
			fetchUserData() {
				this.loading = true;
				axios
					.get('/api/profile')
					.then(response => {
						// handle success
						this.user = response.data || {};
					})
					.catch(function (error) {
					    // handle error
					    this.$toastr.w(error.response.data.errors[x], "Warning");
					})
					.then(() => {
			    		// always executed
			    		this.loading = false;
				  	});
			},
			onImageChange(evnt){
				let files = evnt.target.files || evnt.dataTransfer.files;

                // Only process image files.
		      	if (files.length && files[0].type.match('image.*')) {
                	this.createImage(files[0]);
		      	}

		      	evnt.target.value = '';
		      	return;
			},
			createImage(file) {
                let reader = new FileReader();
                reader.onload = (evnt) => {
                    this.newProfilePicture = this.user.profile_preview.preview = evnt.target.result;
                };
                reader.readAsDataURL(file);
            },
            profileUpdation() {

				if (!this.user.email || !this.user.mobile || !this.user.user_name) {
					this.submitForm = false;
					return;
				}

				this.loading = true;
				this.submitForm = true;
				this.user.profile_preview.preview = this.newProfilePicture;

				axios
					.put('/profile', this.user)
					.then(response => {
						// handle success
						if (response.status == 200) {
							this.newProfilePicture = null;
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
					})
					.then(() => {
						// always execute
				    	this.loading = false;
				  	});
			},
			passwordUpdation() {

				if (!this.password.current_password || !this.password.password || !this.password.password_confirmation) {

					this.submitForm = false;
					return;
				}

				this.loading = true;
				this.submitForm = true;

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
					})
					.then(() => {
					    // always executed
					    this.loading = false;
				  	});
			}
		}
  	}

</script>