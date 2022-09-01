<template>
	
	<!-- Modal -->
	<div class="modal fade" id="user-profile-view-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
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
								style="max-width: 125px;" 
							>
						</div>
					</div>

					<div 
						class="form-row"
						v-if="$route.name=='merchants' && profileToView.hasOwnProperty('support_deal')"
					>
						<div class="col-sm-12">
							<div class="card">
								<div class="card-body">
									<ul class="nav nav-tabs tabs justify-content-center">
										<li class="nav-item">
											<a class="nav-link active" data-toggle="tab" href="#profile" role="tab">
												Profile
											</a>
										</li>

										<li class="nav-item">
											<a class="nav-link" data-toggle="tab" href="#support-deal" role="tab">
												Supports
											</a>
										</li>
									</ul>

									<div class="tab-content tabs card-block">
										<div id="profile" class="tab-pane active">
											<div 
												class="form-row" 
												v-for="property in propertiesToShow" 
												:key="property"
											> 
											    <div class="col-6 col-form-label font-weight-bold text-right">
													<label class="font-weight-bold">{{ property + ': ' | capitalize }}</label>
												</div>
												<div class="col-6 col-form-label">
													{{ getPropertyValue(property) }}
												</div>
											</div>
										</div>

										<div id="support-deal" class="tab-pane fade in">
											<div class="form-row"> 
											    <div class="col-6 col-form-label font-weight-bold text-right">
													<label class="font-weight-bold">Support Deal ID:</label>
												</div>
												<div class="col-6 col-form-label">
													{{ profileToView.support_deal.name | capitalize }}
												</div>
											</div>

											<div class="form-row"> 
											    <div class="col-6 col-form-label font-weight-bold text-right">
													<label class="font-weight-bold">Rent Period:</label>
												</div>
												<div class="col-6 col-form-label">
													{{ profileToView.support_deal.rent_period ? profileToView.support_deal.rent_period.name : '' | capitalize }}
												</div>
											</div>

											<div class="form-row"> 
											    <div class="col-6 col-form-label font-weight-bold text-right">
													<label class="font-weight-bold">Sale Percentage:</label>
												</div>
												<div class="col-6 col-form-label">
													{{ profileToView.support_deal.sale_percentage }}%
												</div>
											</div>

											<div class="form-row"> 
											    <div class="col-6 col-form-label font-weight-bold text-right">
													<label class="font-weight-bold">E Commerce Fulfillment Support:</label>
												</div>
												<div class="col-6 col-form-label">
													<span :class="[profileToView.support_deal.e_commerce_fulfillment_support ? 'badge-success' : 'badge-danger', 'badge']">
														
														{{ profileToView.support_deal.e_commerce_fulfillment_support ? 'Enabled' : 'Disabled' }}
														
													</span>
												</div>
											</div>

											<div class="form-row" 
												v-show="profileToView.support_deal.e_commerce_fulfillment_support"
											> 
											    <div class="col-6 col-form-label font-weight-bold text-right">
													<label class="font-weight-bold">Charge:</label>
												</div>
												<div class="col-6 col-form-label">
													{{ profileToView.support_deal.e_commerce_fulfillment_charge }}
													{{ general_settings.official_currency_name || 'BDT' | capitalize }}
												</div>
											</div>

											<div class="form-row"> 
											    <div class="col-6 col-form-label font-weight-bold text-right">
													<label class="font-weight-bold">Purchase Support:</label>
												</div>
												<div class="col-6 col-form-label">
													<span :class="[profileToView.support_deal.purchase_support ? 'badge-success' : 'badge-danger', 'badge']">
														
														{{ profileToView.support_deal.purchase_support ? 'Enabled' : 'Disabled' }}
														
													</span>
												</div>
											</div>

											<div class="form-row" 
												v-show="profileToView.support_deal.purchase_support"
											> 
											    <div class="col-6 col-form-label font-weight-bold text-right">
													<label class="font-weight-bold">Charge:</label>
												</div>
												<div class="col-6 col-form-label">
													{{ profileToView.support_deal.purchase_support_charge }}
													{{ general_settings.official_currency_name || 'BDT' | capitalize }}
												</div>
											</div>

											<div class="form-row"> 
											    <div class="col-6 col-form-label font-weight-bold text-right">
													<label class="font-weight-bold">POS Support:</label>
												</div>
												<div class="col-6 col-form-label">
													<span :class="[profileToView.support_deal.pos_support ? 'badge-success' : 'badge-danger', 'badge']">
														
														{{ profileToView.support_deal.pos_support ? 'Enabled' : 'Disabled' }}
														
													</span>
												</div>
											</div>

											<div class="form-row" 
												v-show="profileToView.support_deal.pos_support"
											> 
											    <div class="col-6 col-form-label font-weight-bold text-right">
													<label class="font-weight-bold">Charge:</label>
												</div>
												<div class="col-6 col-form-label">
													{{ profileToView.support_deal.pos_support_charge }}
													{{ general_settings.official_currency_name || 'BDT' | capitalize }}
												</div>
											</div>

											<div class="form-row" 
												v-show="profileToView.support_deal.pos_support"
											> 
											    <div class="col-6 col-form-label font-weight-bold text-right">
													<label class="font-weight-bold"># Outlets:</label>
												</div>
												<div class="col-6 col-form-label">
													{{ profileToView.support_deal.number_outlets }}
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="form-row" v-else>
						<div class="col-sm-12">
							<div 
								class="form-row" 
								v-for="property in propertiesToShow" 
								:key="property"
							> 
							    <div class="col-6 col-form-label font-weight-bold text-right">
									<label class="font-weight-bold">{{ property + ': ' | capitalize }}</label>
								</div>
								<div class="col-6 col-form-label">
									{{ getPropertyValue(property) }}
								</div>
							</div>
						</div>	
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn waves-effect waves-dark btn-secondary btn-outline-secondary btn-block btn-sm" data-dismiss="modal">
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

				const words = value.split(/[\s-]/);

				for (let i = 0; i < words.length; i++) {
					
					if (words[i]) {

				    	words[i] = words[i][0].toUpperCase() + words[i].substr(1);

					}
					
				}

				return words.join(" ");
			}
		},

		data() {
			return {
				general_settings : JSON.parse(window.localStorage.getItem('general_settings')),
			}
		},

		methods : {

			getPropertyValue(property) {

				if (property.match(/first name/gi)) {
					return this.$options.filters.capitalize(this.profileToView.first_name) || 'NA';
				}
				
				else if (property.match(/last name/gi)) {
					return this.$options.filters.capitalize(this.profileToView.last_name) || 'NA';
				}
				
				else if (property.match(/username/gi)) {
					return this.$options.filters.capitalize(this.profileToView.user_name);
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

				else if (property.match(/deals/gi)) {
					return this.profileToView.space_deals_count || 0;
				}

				else if (property.match(/products/gi)) {
					return this.profileToView.products_count || 0;
				}
				
				/*
				else if (property.match(/roles/gi)) {
					let roles = [];
					
					if (this.profileToView.roles.length) {

						for (var i = 0; i < this.profileToView.roles.length; i++) {
							roles.push(this.$options.filters.capitalize(' ' + this.profileToView.roles[i].name));
						}

					}
					else {

						roles.push('NA');

					}

					return roles.toString();
				}

				else if (property.match(/permissions/gi)) {
					let permissions = [];
					
					if (this.profileToView.permissions.length) {

						for (var i = 0; i < this.profileToView.permissions.length; i++) {
							permissions.push(this.$options.filters.capitalize(' ' + this.profileToView.permissions[i].name));
						}

					}
					else {

						permissions.push('NA');

					}

					return permissions.toString();
				}
				*/
				
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