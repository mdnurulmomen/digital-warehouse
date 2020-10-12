<template>
	
	<div class="row d-flex align-items-center">										  	
  		<div class="col-sm-3 text-left">	
				{{ callerPage }} List
  		</div>
  		<div class="col-sm-6 was-validated text-center">
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
  		<div class="col-sm-3 text-right">
  			<button 
	  			class="btn btn-success btn-outline-success btn-sm" 
	  			@click="$emit('showContentCreateForm')"
  			>
  				<i class="fa fa-plus"></i>
  				New {{ callerPage }}
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

		data() {
			return {

				search : this.query

			};
		}

	}

</script>