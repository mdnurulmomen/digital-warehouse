<template>
	
	<!-- Delete Modal -->
	<div class="modal fade" id="restore-confirmation-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<form 
					class="form-horizontal" 
					v-on:submit.prevent="$emit(submitMethodName, contentToRestore)" 
					autocomplete="off"
				>
					
					<input type="hidden" name="_token" :value="csrf">

					<div class="modal-header bg-warning">
						<h5 class="modal-title" id="exampleModalLongTitle">Restore Confirmation</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body text-center">
						<h4 class="text-warning">Want to restore '{{ contentToRestore.user_name || contentToRestore.name | capitalize }}' ?</h4>
						<h6 class="sub-heading text-secondary">{{ restorationMessage }}</h6>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary mr-auto" data-dismiss="modal">Close</button>
						<button 
							type="submit" 
							class="btn btn-warning" 
							:disabled="formSubmitted"
						>
							Restore
						</button>
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
			formSubmitted : {
				type : Boolean,
				default: false
			},
			submitMethodName : {
				type : String,
				required : true,
				default : 'restoreContent'
			},
			restorationMessage : {
				type : String,
				required : false,
				default : 'This will restore all related items !',
			},
			contentToRestore : {
				type : Object,
				required : true
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

	}

</script>