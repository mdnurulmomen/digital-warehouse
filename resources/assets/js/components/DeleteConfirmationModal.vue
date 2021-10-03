<template>
	<!-- Delete Modal -->
	<div class="modal fade" id="delete-confirmation-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<form 
					class="form-horizontal" 
					v-on:submit.prevent="$emit(submitMethodName, contentToDelete)" 
					autocomplete="off"
				>
					<input type="hidden" name="_token" :value="csrf">

					<div class="modal-header bg-danger">
						<h5 class="modal-title" id="exampleModalLongTitle">Delete Confirmation</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="modal-body text-center">
						<h4 class="text-danger">Want to delete '{{ (contentToDelete.user_name || contentToDelete.name || (contentToDelete.hasOwnProperty('e_commerce_fulfillment') ? 'the deal' : 'stock')) | capitalize }}' ?</h4>
						<h6 class="sub-heading text-secondary">{{ restorationMessage }}</h6>
					</div>

					<div class="modal-footer">
						<button type="button" class="btn btn-secondary mr-auto" data-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-danger">Delete</button>
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
			submitMethodName : {
				type : String,
				required : true,
				default : 'deleteContent'
			},
			restorationMessage : {
				type : String,
				required : true,
				default : 'But once you think, you can restore this item !'
			},
			contentToDelete : {
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