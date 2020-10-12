<template>

	<ul class="nav nav-tabs md-tabs" role="tablist">
		<li class="nav-item" v-for="tabName in tabNames" :key="tabName">
		    <a 	class="" 
		    	:class="[tabName.toUpperCase() === currentTab.toUpperCase() ? 'active' : '', ' nav-link']"
				data-toggle="tab" 
				aria-selected="true" 
				@click="$emit(getMethodName(tabName))"
			>
				{{ tabName }}
			</a>
			<div class="slide"></div>
		</li>
	</ul>

</template>

<script type="text/javascript">
	
	export default {

		props : {

			tabNames : {
				type : Array,
				required : true
			},
			currentTab : {
				type : String,
				required : true
			}

		},

		methods : {

			getMethodName(tabName) {
				return 'show'+tabName+'Contents';
			}

		}

	}

</script>