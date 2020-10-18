<template>
	<div class="alert alert-danger text-center mt-4" 
		role="alert" 
	>
	  	{{ error }}
	</div>
</template>

<script type="text/javascript">
	
	export default {

		props : {
			error : {
				type : String,
				required : true,
			}
		}

	}

</script>