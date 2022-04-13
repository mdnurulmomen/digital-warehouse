<template>
	<!-- View Modal -->
	<div class="modal fade" id="container-view-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">
						{{ callerPage | capitalize }} Details
					</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<div class="modal-body text-center">			
					<div class="card">
						<div class="card-body">
							<h4 class="card-title">Container</h4>
							<div class="form-row">
								<label class="col-sm-6 col-form-label font-weight-bold text-right">Name :</label>
								<label class="col-sm-6 form-control-plaintext">{{ singleAssetData.name | capitalize }}</label>
							</div>

							<div class="form-row">
								<label class="col-sm-6 col-form-label font-weight-bold text-right">Recognizing Code :</label>
								<label class="col-sm-6 form-control-plaintext">{{ singleAssetData.code | capitalize }}</label>
							</div>

							<div class="form-row">
								<label class="col-sm-6 col-form-label font-weight-bold text-right">Storage Type :</label>
								<label class="col-sm-6 form-control-plaintext">{{ singleAssetData.storage_type ? singleAssetData.storage_type.name : 'NA' | capitalize }}</label>
							</div>

							<div class="form-row">
								<label class="col-sm-6 col-form-label font-weight-bold text-right">Length :</label>
								<label class="col-sm-6 form-control-plaintext">{{ singleAssetData.length + ' ' + general_settings.default_measure_unit }}</label>
							</div>

							<div class="form-row">
								<label class="col-sm-6 col-form-label font-weight-bold text-right">Width :</label>
								<label class="col-sm-6 form-control-plaintext">{{ singleAssetData.width + ' ' + general_settings.default_measure_unit }}</label>
							</div>

							<div class="form-row">
								<label class="col-sm-6 col-form-label font-weight-bold text-right">Length :</label>
								<label class="col-sm-6 form-control-plaintext">{{ singleAssetData.height + ' ' + general_settings.default_measure_unit }}</label>
							</div>

							<!-- 
							<div class="form-row">
								<label class="col-sm-6 col-form-label font-weight-bold text-right">Default Storing Price :</label>
								<label class="col-sm-6 form-control-plaintext">{{ singleAssetData.storing_price }}</label>
							</div>
							<div class="form-row">
								<label class="col-sm-6 col-form-label font-weight-bold text-right">Default Selling Price :</label>
								<label class="col-sm-6 form-control-plaintext">{{ singleAssetData.selling_price }}</label>
							</div>
							-->
							
							<div class="form-row">
								<label class="col-sm-6 col-form-label font-weight-bold text-right">Has Shelf :</label>
								<label class="col-sm-6 form-control-plaintext">
									<span :class="[singleAssetData.has_shelve ? 'badge-success' : 'badge-danger', 'badge']">{{ singleAssetData.has_shelve ? 'Available' : 'NA' }}</span>
								</label>
							</div>
						</div>
					</div>

					<!-- shelf -->
					<div class="card" v-if="singleAssetData.has_shelve">
						<div class="card-body">
							<h4 class="card-title">Container Shelf</h4>
							<div class="form-row">
								<label class="col-sm-6 col-form-label font-weight-bold text-right"># Shelves :</label>
								<label class="col-sm-6 form-control-plaintext">{{ singleAssetData.shelf.quantity }}</label>
							</div>
							<!-- 
							<div class="form-row">
								<label class="col-sm-6 col-form-label font-weight-bold text-right">Shelf Default Selling Price :</label>
								<label class="col-sm-6 form-control-plaintext">{{ singleAssetData.shelf.selling_price }}</label>
							</div>
							<div class="form-row">
								<label class="col-sm-6 col-form-label font-weight-bold text-right">Shelf Default Storing Price :</label>
								<label class="col-sm-6 form-control-plaintext">{{ singleAssetData.shelf.storing_price }}</label>
							</div>
							 -->
							<div class="form-row">
								<label class="col-sm-6 col-form-label font-weight-bold text-right">Has Units :</label>
								<label class="col-sm-6 form-control-plaintext">
									<span :class="[singleAssetData.shelf.has_units ? 'badge-success' : 'badge-danger', 'badge']">{{ singleAssetData.shelf.has_units ? 'Available' : 'NA' }}</span>
								</label>
							</div>
						</div>
					</div>

					<!-- unit -->
					<div class="card" v-if="singleAssetData.has_shelve && singleAssetData.shelf.has_units">
						<div class="card-body">
							<h4 class="card-title">Shelf Unit</h4>
							<div class="form-row">
								<label class="col-sm-6 col-form-label font-weight-bold text-right"># Units :</label>
								<label class="col-sm-6 form-control-plaintext">{{ singleAssetData.shelf.unit.quantity }}</label>
							</div>
							<!-- 
							<div class="form-row">
								<label class="col-sm-6 col-form-label font-weight-bold text-right">Unit Default Selling Price :</label>
								<label class="col-sm-6 form-control-plaintext">{{ singleAssetData.shelf.unit.selling_price }}</label>
							</div>
							<div class="form-row">
								<label class="col-sm-6 col-form-label font-weight-bold text-right">Unit Default Storing Price :</label>
								<label class="col-sm-6 form-control-plaintext">{{ singleAssetData.shelf.unit.storing_price }}</label>
							</div>
							 -->
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
			singleAssetData : {
				type : Object,
				required : true,
				default : {
					profile_preview : {}
				}
			},
			general_settings : {
				type : Object,
				required : true,
				default : {}
			},
			/*
			propertiesToShow : {
				type : Array,
				required : true,
				default : []
			}
			*/

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

			/*
			getPropertyValue(property) {
				if (property.match(/name/gi)) {
					return this.$options.filters.capitalize(this.assetToView.name);
				}
				else if (property.match(/code/gi)) {
					return this.$options.filters.capitalize(this.assetToView.code);
				}
			}
			*/

		}

	}

</script>