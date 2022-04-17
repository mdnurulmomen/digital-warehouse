<template>	
	<!-- Modal -->
	<div class="modal fade" id="mail-create-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
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
					@submit.prevent="verifyUserInput" 
					autocomplete="off" 
				>
					<input type="hidden" name="_token" :value="csrf">

					<div class="modal-body">
						<div class="form-row">
							<div class="form-group col-md-12">
								<label for="inputFirstName">Email Address</label>
								<input type="text" 
									class="form-control" 
									v-model="newEmail" 
									placeholder="New Email Address" 
									:class="!errors.email ? 'is-valid' : 'is-invalid'" 
									@change="validateFormInput('email')" 
									@keydown.enter.prevent="validateFormInput('email');addRecipientMail()"
								>

								<div class="invalid-feedback">
						        	{{ errors.email }}
						  		</div>
							</div>

							<div class="form-group col-md-12">
								<label for="inputFirstName">Recipients</label>

								<div class="border border-success p-4 rounded-sm">
									<ul 
										id="recipient-email" 
										v-if="singleAssetData.recipients.length"
									>
										<li 
											v-for="(recipient, recipientIndex) in singleAssetData.recipients"
											:key="'recipient-email-index-' + recipientIndex + '-recipient-' + recipient"
										>	
											{{ recipient }}

											<i 
												class="fa fa-close text-danger p-2" 
												v-tooltip.bottom-end="'Remove Mail'" 
												v-show="recipient"
												@click="removeRecipientMail(recipientIndex)"
											>	
											</i>
										</li>
									</ul>
								</div>
							</div>

							<div class="form-group col-md-12">
								<label for="inputFirstName">Subject</label>
								<input type="text" 
									class="form-control" 
									v-model="singleAssetData.subject" 
									placeholder="Email Subject" 
									:class="!errors.subject ? 'is-valid' : 'is-invalid'" 
									@change="validateFormInput('subject')" 
									@keydown.enter.prevent="validateFormInput('subject')" 
								>

								<div class="invalid-feedback">
						        	{{ errors.subject }}
						  		</div>
							</div>

							<div class="form-group col-md-12">
								<label for="inputFirstName">Body</label>

								<ckeditor 
	                              	class="form-control" 
	                              	:editor="editor" 
	                              	v-model="singleAssetData.body"
	                              	:class="!errors.body ? 'is-valid' : 'is-invalid'"
	                              	@blur="validateFormInput('body')"
	                            >
                              	</ckeditor>

								<div class="invalid-feedback">
						        	{{ errors.body }}
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
							<button type="button" class="btn btn-secondary float-left" data-dismiss="modal">
								Close
							</button>
							<button type="submit" class="btn btn-primary float-right" :disabled="! submitForm || formSubmitted">
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

	import CKEditor from '@ckeditor/ckeditor5-vue';
	import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
	
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

		components: { 
			ckeditor: CKEditor.component,
		},

		data() {

			return {

				newEmail : '',

				submitForm : true,

				errors : {

				},

				editor: ClassicEditor,
				
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

				this.validateFormInput('email');
				this.validateFormInput('subject');
				this.validateFormInput('body');
            	
            	if (Object.keys(this.errors).length !== 0 && this.errors.constructor === Object) {
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
            addRecipientMail() {

				// this.validateFormInput('email');

				if (this.newEmail && ! this.errors.hasOwnProperty('email')) {

					let emptyIndex = this.singleAssetData.recipients.findIndex(recipient=> ! recipient || recipient == '');

					if (emptyIndex > -1) {

						this.singleAssetData.recipients[emptyIndex] = this.newEmail;
						this.newEmail = '';

					}
					else {

						this.singleAssetData.recipients.push(this.newEmail);
						this.newEmail = '';

					}
				
				}

			},
			removeRecipientMail(recipientMailIndex) {

				if (this.singleAssetData.recipients.length > recipientMailIndex) {

					this.singleAssetData.recipients.splice(recipientMailIndex, 1);
					// this.$set(this.singleAssetData.recipients, 'recipientMailIndex', '');

				}

			},
			validateFormInput (formInputName) {
				
				this.submitForm = false;

				switch(formInputName) {

					case 'email' :

						if (! this.newEmail && ! this.singleAssetData.recipients.length) {
							this.errors.email = 'Recipient is required';
						}
						else if (this.newEmail && ! this.newEmail.match(/^[a-zA-Z0-9.!#$%&’*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/g)) {
							this.errors.email = 'Invalid email';
						}
						else if (this.newEmail && this.singleAssetData.recipients.some(recipient=>recipient==this.newEmail)) {
							this.errors.email = 'Same recipient address';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'email');
						}

						break;

					case 'subject' :

						if (! this.singleAssetData.subject) {
							this.errors.subject = 'Subject is required';
						}
						else if (! this.singleAssetData.subject.match(/^[a-zA-Z ]{2,30}$/g)) {
							this.errors.subject = 'No special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'subject');
						}

						break;

					case 'body' :

						if (! this.singleAssetData.body) {
							this.errors.body = 'Body is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'body');
						}

						break;

				}
	 
			}

		}

	}

</script>