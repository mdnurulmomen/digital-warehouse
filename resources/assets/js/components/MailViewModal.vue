<template>
	<!-- Modal -->
	<div class="modal fade" id="mail-view-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">{{ callerPage | capitalize }} Details</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div 
						class="form-row" 
						v-for="property in propertiesToShow" 
						:key="property"
					> 
					    <div class="form-group col-6 text-right">
							<label class="font-weight-bold">{{ property | capitalize }}: </label>
						</div>
						<div class="form-group col-6">
							{{ getPropertyValue(property) }}
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary btn-block btn-sm" data-dismiss="modal">
						Close
					</button>
				</div>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript">
	
	export default {

		props : {

			callerPage : {
				type : String,
				required : true,
				default : 'User'
			},
			assetToView : {
				type : Object,
				required : true,
				default : {
					profile_preview : {}
				}
			},
			propertiesToShow : {
				type : Array,
				required : true,
				default : []
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

			getFullName(object) {
				if (! object.first_name && ! object.last_name) {
					return this.$options.filters.capitalize(object.user_name) || 'NA' ;
				}

				return this.$options.filters.capitalize(object.first_name) + ' ' + this.$options.filters.capitalize(object.last_name);
			},
			getPropertyValue(property) {
				
				if (property.match(/subject/gi)) {
					return this.$options.filters.capitalize(this.assetToView.subject);
				}
				else if (property.match(/body/gi)) {
					return this.$options.filters.capitalize(this.assetToView.body);
				}
				else if (property.match(/sender/gi)) {
					return this.assetToView.sender ? this.getFullName(this.assetToView.sender) : 'NA';
				}
				else if (property.match(/recipients/gi)) {
					
					let recipients = '';

					if (this.assetToView.recipients && this.assetToView.recipients.length) {
						
						this.assetToView.recipients.forEach(
							(recipient)=> {
								recipients += (recipient.recipient + ', ');
							}
						);

					}

					return recipients;

				}
			},

		}

	}

</script>