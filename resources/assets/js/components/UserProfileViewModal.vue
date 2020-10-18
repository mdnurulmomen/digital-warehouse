<template>
	
	<!-- Modal -->
	<div class="modal fade" id="user-profile-view-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">{{ user | capitalize }} Details</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">

					<div class="card">
						<div class="card-body text-center">	
							<img class="profile-user-img img-fluid img-circle" 
								:src="profileToView.profile_preview.preview || ''"
								alt="Profile Picture" 
							>
						</div>
					</div>

					<div 
						class="form-row" 
						v-for="property in propertiesToShow" 
						:key="property"
					> 
					    <div class="form-group col-md-6 text-right">
							<label class="font-weight-bold">{{ property | capitalize }}</label>
						</div>
						<div class="form-group col-md-6 text-left">
							{{ getPropertyValue(property) }}
						</div>
					</div>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>

</template>

<script type="text/javascript">
	
	export default {

		props : {

			user : {
				type : String,
				required : true,
				default : 'User'
			},
			profileToView : {
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
				value = value.toString()
				return value.charAt(0).toUpperCase() + value.slice(1)
			}
		},

		methods : {

			getPropertyValue(property) {

				if (property.match(/first name/gi)) {
					return this.profileToView.first_name || 'NA';
				}
				else if (property.match(/last name/gi)) {
					return this.profileToView.last_name || 'NA';
				}
				else if (property.match(/username/gi)) {
					return this.profileToView.user_name;
				}
				else if (property.match(/email/gi)) {
					return this.profileToView.email;
				}
				else if (property.match(/mobile/gi)) {
					return this.profileToView.mobile;
				}
				else if (property.match(/status/gi)) {
					return this.profileToView.active ? 'Approved' : 'Pending';
				}
				else if (property.match(/registered/gi)) {
					return this.profileToView.created_at ? this.getReadableDate(this.profileToView.created_at) : 'No Date';
				}

			},

			getReadableDate(timestamp) {
				return new Date(timestamp).toDateString();
			}

		}

	}

</script>