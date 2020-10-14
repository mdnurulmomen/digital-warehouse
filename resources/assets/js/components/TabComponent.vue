<template>

	<ul class="nav nav-tabs md-tabs" role="tablist">
		<li class="nav-item" v-for="tabName in tabNames" :key="tabName">
		    <a 	class="" 
		    	:class="[tabName.toUpperCase() === currentTab.toUpperCase() ? 'active' : '', ' nav-link']"
				data-toggle="tab" 
				aria-selected="true" 
				@click="$emit(getMethodName(tabName))"
			>
				{{ tabName | capitalize }}
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

		filters: {
			capitalize: function (value) {
				if (!value) return ''
				value = value.toString()
				return value.charAt(0).toUpperCase() + value.slice(1)
			}
		},

		methods : {

			getMethodName(tabName) {
				return 'show'+this.getCapitalizedName(tabName)+'Contents';
			},
			getCapitalizedName(tabName) {
				if (!tabName) return '';
				tabName = tabName.toString();
				return tabName.charAt(0).toUpperCase() + tabName.slice(1);
			}

		}

	}

</script>