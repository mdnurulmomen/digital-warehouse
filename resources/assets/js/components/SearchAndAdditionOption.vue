<template v-if="$route.name=='packaging-packages' || $route.name=='delivery-companies' || (userHasPermissionTo('view-' + requiredPermission + '-index') || userHasPermissionTo('create-' + requiredPermission))">
	<section>
		<div class="row d-flex align-items-center">										  	
	  		<div class="col-md-4 col-sm-6 d-flex align-items-center form-group">	
				<div class="mr-2">
					<span>
						{{ search != '' ? ('Searched ' + callerPage + ' List') : (callerPage + ' List')
			  			| capitalize }}
					</span>
				</div>

				<div class="dropdown">
					<i class="fa fa-download fa-lg dropdown-toggle p-1" data-toggle="dropdown" v-tooltip.bottom-end="'Download ' + callerPage" v-show="($route.name=='products' || $route.name=='product-categories') && userHasPermissionTo('view-' + requiredPermission + '-index')"></i>
  					
  					<div 
  						class="dropdown-menu"
  						v-show="($route.name=='products' || $route.name=='product-categories') && userHasPermissionTo('view-' + requiredPermission + '-index')"
  					>
						<download-excel 
			  				class="btn waves-effect waves-dark btn-default btn-outline-default p-1 dropdown-item active"
							:data="contentsToDownload"
							:fields="dataToExport" 
							:worksheet="callerPage + ' Sheet'"
							:name="(search != '' ? ('searched-' + callerPage) : (callerPage + '-list-')) + currentDate + '-page-' + pagination.current_page + '.xls'"
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
  						v-show="($route.name=='products' || $route.name=='product-categories') && userHasPermissionTo('create-' + requiredPermission)" 
  						v-tooltip.bottom-end="'Upload ' + callerPage" 
  						data-toggle="modal" 
						:data-target="'#'+ callerPage +'-importing-modal'" 
  					>	
  					</i>
  				</div>

				<div class="ml-auto d-md-none" v-tooltip.bottom-end="'Create New'">
					<button 
			  			class="btn waves-effect waves-light btn-success btn-outline-success btn-sm ml-auto d-sm-block d-md-none d-lg-none" 
			  			:disabled="disableAddButton" 
			  			@click="$emit('showContentCreateForm')" 
			  			v-if="userHasPermissionTo('create-' + requiredPermission)"
		  			>
		  				<i class="fa fa-plus"></i>
		  				New {{ callerPage | capitalize }}
		  			</button>
				</div>
	  		</div>
	  		
	  		<div 
		  		class="was-validated form-group" 
		  		:class="userHasPermissionTo('create-' + requiredPermission) ? 'col-md-4 col-sm-6' : 'col-md-8 col-sm-6'" 
		  		v-if="$route.name=='delivery-companies' || $route.name=='packaging-packages' || userHasPermissionTo('view-' + requiredPermission + '-index')"
	  		>
	  			<input 	type="text" 
				  		v-model="search" 
				  		pattern="[^'!#$%^()\x22]+" 
				  		class="form-control" 
				  		placeholder="Search"
			  	>
			  	<div class="invalid-feedback">
			  		Please search with releavant input
			  	</div>
	  		</div>

	  		<div 
	  			class="col-md-4 col-sm-6 form-group text-right d-none d-md-block d-lg-block" 
	  			v-if="userHasPermissionTo('create-' + requiredPermission)"
	  		>
	  			<div v-tooltip.bottom-end="'Create New'">
		  			<button 
			  			class="btn waves-effect waves-light btn-success btn-outline-success btn-sm" 
			  			:disabled="disableAddButton" 
			  			@click="$emit('showContentCreateForm')"
		  			>
		  				<i class="fa fa-plus"></i>
		  				New {{ callerPage | capitalize }}
		  			</button>
	  			</div>
	  		</div>
	  	</div>

	  	<div class="row" v-if="userHasPermissionTo('create-' + requiredPermission)">
	  		<!-- The Modal -->
	  		<import-excel 
	  			:caller-page="callerPage" 

	  			@importExcelFile="importExcelFile($event)" 
	  		/>
	  	</div>
	</section>
</template>

<script type="text/javascript">
	
	export default {

		props: {
			// Required string
			query: {
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
			},
			contentsToDownload: {
		    	type: Array,
		      	// required: true,
		      	default() {
		      		return [];	
		      	} 
	      	},
	      	dataToExport: {
		    	type: Object,
		      	// required: true,
		      	default() {
		      		return {};
		      	}
	      	},
	      	pagination: {
		    	type: Object,
		      	// required: true,
		      	default() {
		      		return {};
		      	}
	      	},
		},

		computed: {

			currentDate: function() {

				let date = new Date();
				return date.getFullYear() + '-' +  (date.getMonth() + 1) + '-' + date.getDate();

			},

		},

		watch : {

			search : function(val){

				if (val==='') {
					this.$emit('fetchAllContents');
				}
				else {
					this.$emit('searchData', this.search);
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

		data() {
			return {

				search : this.query

			};
		},

		methods : {

			importExcelFile(fileToExport) {

				this.$emit('importExcelFile', fileToExport);

				// this.formSubmitted = true;

			},

		}

	}

</script>