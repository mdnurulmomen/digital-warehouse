<template v-if="userHasPermissionTo('view-' + requiredPermission + '-index') || userHasPermissionTo('create-' + requiredPermission)">
	
	<div class="row d-flex align-items-center">										  	
  		<div class="col-sm-6 col-md-3 form-group d-flex align-items-center">	
				<span>
					{{ callerPage | capitalize }} List
				</span>

				<button 
		  			class="btn btn-success btn-outline-success btn-sm ml-auto d-sm-block d-md-none d-lg-none" 
		  			data-toggle="tooltip" data-placement="top" title="Create New" 
		  			:disabled="disableAddButton" 
		  			@click="$emit('showContentCreateForm')" 
		  			v-if="userHasPermissionTo('create-' + requiredPermission)"
	  			>
	  				<i class="fa fa-plus"></i>
	  				New {{ callerPage | capitalize }}
	  			</button>
  		</div>
  		<div class="col-sm-6 col-md-6 was-validated form-group" v-if="userHasPermissionTo('view-' + requiredPermission + '-index')">
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
  		<div class="col-sm-6 col-md-3 form-group" v-if="userHasPermissionTo('create-' + requiredPermission)">
  			<button 
	  			class="btn btn-success btn-outline-success btn-sm ml-auto d-none d-sm-none d-md-block d-lg-block" 
	  			data-toggle="tooltip" data-placement="top" title="Create New" 
	  			:disabled="disableAddButton" 
	  			@click="$emit('showContentCreateForm')"
  			>
  				<i class="fa fa-plus"></i>
  				New {{ callerPage | capitalize }}
  			</button>
  		</div>
  	</div>

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
			}
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
		}

	}

</script>