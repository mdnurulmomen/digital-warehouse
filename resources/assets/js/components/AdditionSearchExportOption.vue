<template v-if="userHasPermissionTo('view-' + requiredPermission + '-index') || userHasPermissionTo('create-' + requiredPermission)">
	<div class="col-sm-12">
	  	<div class="row form-group">
	  		<div class="col-md-4 col-sm-6 d-flex align-items-center form-group">
	  			<div class="mr-2">
	  				<span>
			  			{{ 
			  				( /* searchAttributes.showPendingRequisitions || searchAttributes.showCancelledRequisitions || searchAttributes.showDispatchedRequisitions || searchAttributes.showProduct || */ searchAttributes.search || searchAttributes.dateFrom || searchAttributes.dateTo) ? ('Searched ' + callerPage + ' List') : (callerPage + ' List')
			  			| capitalize }}
	  				</span>
	  			</div>

	  			<div class="dropdown">
					<i class="fa fa-download fa-lg dropdown-toggle p-1" data-toggle="dropdown" v-tooltip.bottom-end="'Download ' + callerPage"></i>
  					
  					<div class="dropdown-menu">
						<download-excel 
			  				class="btn waves-effect waves-dark btn-default btn-outline-default p-1 dropdown-item active"
							:data="contentsToDownload"
							:fields="dataToExport" 
							:worksheet="callerPage + ' Sheet'"
							:name="((searchAttributes.search != '' || searchAttributes.dateFrom || searchAttributes.dateTo) ? ('searched-' + callerPage) : (currentTab + '-' + callerPage + '-list-')) + currentDate + '-page-' + pagination.current_page + '.xls'"
			  			>
			  				Excel
						</download-excel>
  						
  						<!-- 
  						<download-excel 
  							type="csv"
			  				class="btn waves-effect waves-dark btn-default btn-outline-default p-1 dropdown-item disabled"
							:data="contentsToDownload"
							:fields="dataToExport" 
							worksheet="Requisitions sheet"
							:name="((searchAttributes.search != '' || searchAttributes.dateFrom || searchAttributes.dateTo) ? 'searched-requisitions-' : (currentTab + '-requisitions-list-')) + currentDate + '-page-' + pagination.current_page + '.xls'"
			  			>
			  				CSV
						</download-excel> 
						-->
  					</div>

  					<i 
  						class="fa fa-upload fa-lg" 
  						v-show="$route.name=='merchant-products' && userHasPermissionTo('create-' + requiredPermission)"
  						data-toggle="modal" 
  						v-tooltip.bottom-end="'Upload ' + callerPage" 
						:data-target="'#'+ callerPage +'-importing-modal'"
  					>	
  					</i>
  				</div>

	  			<div class="ml-auto d-sm-none" v-tooltip.bottom-end="'Create New'">
	  				<button 
	  					type="button" 
			  			class="btn waves-effect waves-light btn-success btn-outline-success btn-sm" 
			  			:disabled="disableAddButton" 
			  			@click="$emit('showContentCreateForm')" 
			  			v-if="userHasPermissionTo('create-' + requiredPermission) && $route.name != 'merchant-products'"
		  			>
		  				<i class="fa fa-plus"></i>
		  				New {{ callerPage | capitalize }}
		  			</button>

		  			<div 
		  				class="dropdown" 
		  				v-if="userHasPermissionTo('create-' + requiredPermission) && $route.name=='merchant-products'"
		  			>
		  				<button class="btn btn-success btn-outline-success btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
		  					New {{ callerPage | capitalize }}
		  					<span class="caret"></span>
		  				</button>
		  				<ul class="dropdown-menu">
		  					<li class="dropdown-item">
		  						<button 
					  				type="button" 
						  			class="btn waves-effect waves-light btn-success btn-outline-success btn-sm" 
						  			:disabled="disableAddButton" 
						  			@click="$emit('showContentCreateForm')" 
					  			>
					  				<i class="fa fa-plus"></i>
					  				Single {{ callerPage | capitalize }}
					  			</button>
		  					</li>

		  					<li class="dropdown-item">
		  						<button 
					  				type="button" 
						  			class="btn waves-effect waves-light btn-success btn-outline-success btn-sm" 
						  			:disabled="disableAddButton" 
						  			@click="$emit('showMultipleContentCreateForm')" 
					  			>
					  				<i class="fa fa-plus"></i>
					  				<i class="fa fa-plus"></i>
					  				Multiple {{ callerPage | capitalize }}
					  			</button>
		  					</li>
		  				</ul>
		  			</div>
	  			</div>
	  		</div>

	  		<div class="col-md-4 col-sm-6 was-validated text-center d-flex align-items-center form-group">
	  			<div class="mx-sm-auto w-75">
  					<input 	
						type="text" 
				  		class="form-control" 
				  		pattern="[^'!#$%^()\x22]+" 
				  		v-model="searchAttributes.search" 
				  		:placeholder="'Search ' + callerPage"
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
	  			<div v-tooltip.bottom-end="'Create New'">
		  			<button 
		  				type="button" 
			  			class="btn waves-effect waves-light btn-success btn-outline-success btn-sm" 
			  			:disabled="disableAddButton" 
			  			@click="$emit('showContentCreateForm')" 
			  			v-if="userHasPermissionTo('create-' + requiredPermission) && $route.name != 'merchant-products'"
		  			>
		  				<i class="fa fa-plus"></i>
		  				New {{ callerPage | capitalize }}
		  			</button>

		  			<div 
		  				class="dropdown" 
		  				v-if="userHasPermissionTo('create-' + requiredPermission) && $route.name=='merchant-products'"
		  			>
		  				<button class="btn btn-success btn-outline-success btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
		  					New {{ callerPage | capitalize }}
		  					<span class="caret"></span>
		  				</button>
		  				<ul class="dropdown-menu">
		  					<li class="dropdown-item">
		  						<button 
					  				type="button" 
						  			class="btn waves-effect waves-light btn-success btn-outline-success btn-sm" 
						  			:disabled="disableAddButton" 
						  			@click="$emit('showContentCreateForm')" 
					  			>
					  				<i class="fa fa-plus"></i>
					  				Single {{ callerPage | capitalize }}
					  			</button>
		  					</li>

		  					<li class="dropdown-item">
		  						<button 
					  				type="button" 
						  			class="btn waves-effect waves-light btn-success btn-outline-success btn-sm" 
						  			:disabled="disableAddButton" 
						  			@click="$emit('showMultipleContentCreateForm')" 
					  			>
					  				<i class="fa fa-plus"></i>
					  				<i class="fa fa-plus"></i>
					  				Multiple {{ callerPage | capitalize }}
					  			</button>
		  					</li>
		  				</ul>
		  			</div>
	  			</div>
			</div>
	  	</div>

	  	<div class="row">
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
								class="btn waves-effect waves-light btn-success btn-outline-success" 
								@click="resetSearchingDates()" 
								v-tooltip.bottom-end="'Reset'"
							>
		                  		Reset
		                  	</button>

							<button type="button" class="btn waves-effect waves-dark btn-primary btn-outline-primary ml-auto" data-dismiss="modal">
		                  		See Results
		                  	</button>
						</div>
					</div>
				</div>
			</div>
	  	</div>

	  	<div class="row">
	  		<!-- The Modal -->
	  		<import-excel 
	  			:caller-page="callerPage" 

	  			@importExcelFile="importExcelFile($event)" 
	  		/>
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
	      	requiredPermission: {
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
				
				this.pagination.current_page = 1; 

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
				
				this.pagination.current_page = 1; 

				if (this.searchAttributes.search==='' && ! this.searchAttributes.dateTo && ! this.searchAttributes.dateFrom) {

					this.$emit('fetchAllContents');

				}
				else {

					this.$emit('searchData', this.searchAttributes);
						
				}

			},

			'searchAttributes.dateTo' : function(val){
				
				this.pagination.current_page = 1; 

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

            	if (this.searchAttributes.dates && Object.keys(this.searchAttributes.dates).length > 0 && this.searchAttributes.dates.hasOwnProperty('start') && this.searchAttributes.dates.hasOwnProperty('end')) {

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
            importExcelFile(fileToExport) {

				this.$emit('importExcelFile', fileToExport);

				/*
				this.formSubmitted = true;
				 */

			},

		}

	}

</script>