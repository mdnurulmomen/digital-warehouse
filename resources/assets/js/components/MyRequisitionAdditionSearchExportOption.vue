<template>
	<div class="col-sm-12">
	  	<div class="row form-group">
	  		<div class="col-md-4 col-sm-6 d-flex align-items-center form-group">
	  			<div class="mr-2">
	  				<span>
			  			{{ 
			  				( /* searchAttributes.showPendingRequisitions || searchAttributes.showCancelledRequisitions || searchAttributes.showDispatchedRequisitions || searchAttributes.showProduct || */ searchAttributes.search || searchAttributes.dateFrom || searchAttributes.dateTo) ? ('Searched ' + callerPage + 's List') : (callerPage + 's List')
			  			| capitalize }}
	  				</span>
	  			</div>

	  			<div class="dropdown">
						<i class="fas fa-download fa-lg dropdown-toggle" data-toggle="dropdown"></i>
  					
  					<div class="dropdown-menu">
							<download-excel 
			  				class="btn btn-default p-1 dropdown-item active"
							:data="contentsToDownload"
							:fields="dataToExport" 
							:worksheet="callerPage + 's Sheet'"
							:name="((searchAttributes.search != '' || searchAttributes.dateFrom || searchAttributes.dateTo) ? ('searched-' + callerPage + 's') : (currentTab + '-' + callerPage + 's' + '-list-')) + currentDate + '-page-' + pagination.current_page + '.xls'"
			  			>
			  				Excel
						</download-excel>
  						
  						<!-- 
  						<download-excel 
  							type="csv"
			  				class="btn btn-default p-1 dropdown-item disabled"
							:data="contentsToDownload"
							:fields="dataToExport" 
							worksheet="Requisitions sheet"
							:name="((searchAttributes.search != '' || searchAttributes.dateFrom || searchAttributes.dateTo) ? 'searched-requisitions-' : (currentTab + '-requisitions-list-')) + currentDate + '-page-' + pagination.current_page + '.xls'"
			  			>
			  				CSV
						</download-excel> 
						-->
  					</div>
  				</div>

	  			<div class="ml-auto d-sm-none">
	  				<button 
	  					type="button" 
			  			class="btn btn-success btn-outline-success btn-sm" 
			  			v-tooltip.bottom-end="'Create New'" 
			  			:disabled="disableAddButton" 
			  			@click="$emit('showContentCreateForm')" 
		  			>
		  				<i class="fa fa-plus"></i>
		  				New {{ callerPage | capitalize }}
		  			</button>
	  			</div>
	  		</div>

	  		<div class="col-md-4 col-sm-6 was-validated text-center d-flex align-items-center form-group">
	  			<div class="mx-sm-auto w-75">
  					<input 	
						type="text" 
				  		class="form-control" 
				  		pattern="[^'!#$%^()\x22]+" 
				  		v-model="searchAttributes.search" 
				  		:placeholder="'Search ' + callerPage + 's'"
			  		>

			  		<div class="invalid-feedback">
				  		Please search with releavant input
				  	</div>
	  			</div>

				<div class="ml-auto ml-sm-0">
					<ul class="nav nav-pills">
						<li class="nav-item">
							<a 
								href="javascript:void(0)"
								class="nav-link p-1"
								@click="setTodayDate()" 
								:class="{ 'active': searchAttributes.dateFrom == currentDate && ! searchAttributes.dateTo }"
							>
								Today
							</a>
						</li>

						<li class="nav-item">
							<a 
								href="javascript:void(0)"
								class="nav-link p-0" 
								data-toggle="modal" 
								:data-target="'#'+ callerPage +'-custom-search'"
								:class="{ 'active': Object.keys(searchAttributes.dates).length > 0 }"
							>
								<i class="fa fa-ellipsis-v fa-lg p-2"></i>
							</a>
						</li>
					</ul>
			  	</div>
			</div>

			<div class="col-md-4 text-right d-none d-md-block">
	  			<button 
	  				type="button" 
		  			class="btn btn-success btn-outline-success btn-sm" 
		  			v-tooltip.bottom-end="'Create New'" 
		  			:disabled="disableAddButton" 
		  			@click="$emit('showContentCreateForm')" 
	  			>
	  				<i class="fa fa-plus"></i>
	  				New {{ callerPage | capitalize }}
	  			</button>
			</div>
	  	</div>

	  	<!-- Filter Modal -->
		<div class="modal fade" :id="callerPage + '-custom-search'" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLongTitle">Custom Search</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="form-row">
							<div class="col-12 text-center">
								<p>
									Timelaps
								</p>
								 
								<v-date-picker 
									v-model="searchAttributes.dates" 
									color="red" 
									is-dark
									is-range 
									is-inline
									:max-date="new Date()" 
									:model-config="{ type: 'string', mask: 'YYYY-MM-DD' }"
									:attributes="[ { key: 'today', dot: true, dates: new Date() } ]" 
									@input="setSearchingDates()"
								/> 
							</div>					
						</div>
					</div>
					
					<div class="modal-footer">
						<button 
							type="button" 
							class="btn btn-success" 
							@click="resetSearchingDates()" 
							v-tooltip.bottom-end="'Reset'"
						>
	                  		Reset
	                  	</button>

						<button type="button" class="btn btn-primary ml-auto" data-dismiss="modal">
	                  		See Results
	                  	</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript">
	
	export default {

		props : {
			searchAttributes: {
		      	type: Object,
		      	required: true
		    },
		    dataToExport: {
		    	type: Object,
		      	required: true
	      	},
	      	contentsToDownload: {
		    	type: Array,
		      	required: true
	      	},
	      	pagination: {
		    	type: Object,
		      	required: true
	      	},
	      	currentTab: {
		    	type: String,
		      	required: true
	      	},
	      	callerPage: {
		    	type: String,
		      	required: true
	      	},
			disableAddButton : {
				type : Boolean,
				required : false,
				default : false
			}
		},

		computed: {

			currentDate: function() {

				let date = new Date();
				return date.getFullYear() + '-' +  (date.getMonth() + 1) + '-' + date.getDate();

			},

		},

		watch : {

			'searchAttributes.search' : function(val){
				
				if (this.searchAttributes.search==='' && ! this.searchAttributes.dateTo && ! this.searchAttributes.dateFrom) {

					this.$emit('fetchAllContents');

				}
				else {

					let format = /[`!@#$%^&*+\=\[\]{};':"\\|,.<>\/?~]/;

					if (! format.test(val)) {

						this.$emit('searchData', this.searchAttributes);
					
					}

				}

			},
			
			'searchAttributes.dateFrom' : function(val){
				
				if (this.searchAttributes.search==='' && ! this.searchAttributes.dateTo && ! this.searchAttributes.dateFrom) {

					this.$emit('fetchAllContents');

				}
				else {

					this.$emit('searchData', this.searchAttributes);
						
				}

			},

			'searchAttributes.dateTo' : function(val){
				
				if (this.searchAttributes.search==='' && ! this.searchAttributes.dateTo && ! this.searchAttributes.dateFrom) {

					this.$emit('fetchAllContents');

				}
				else {

					this.$emit('searchData', this.searchAttributes);
						
				}

			},

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

		methods: {

			resetSearchingDates(){

            	this.searchAttributes.dates = {};
				this.searchAttributes.dateTo = null;
				this.searchAttributes.dateFrom = null;				

            },
            setSearchingDates(){

            	if (Object.keys(this.searchAttributes.dates).length > 0 && this.searchAttributes.dates.hasOwnProperty('start') && this.searchAttributes.dates.hasOwnProperty('end')) {

					this.searchAttributes.dateTo = this.searchAttributes.dates.end;
					this.searchAttributes.dateFrom = this.searchAttributes.dates.start;
						
				}
				else {

					this.resetSearchingDates();

				}

            },
            setTodayDate() {
            	
            	if (this.searchAttributes.dateFrom != this.currentDate || this.searchAttributes.dateTo) {
	            	
	            	// this.searchAttributes.dateTo = null; 
	            	this.searchAttributes.dates = {};
	            	this.searchAttributes.dateTo = null;
	            	this.searchAttributes.dateFrom = this.currentDate;

            	}
            	else {

	            	this.searchAttributes.dateFrom = null

            	}

            },

		}

	}

</script>