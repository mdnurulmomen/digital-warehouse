
<template>
	<li>
		<div
			:class="{'font-weight-bold': ! hasParent}" 
			data-toggle="tooltip" data-placement="top" title="Parents" 
			@click="toggle"
		>
			{{ item.name | capitalize }}
			<span class="font-weight-bold" v-if="hasParent">[{{ isOpen ? '-' : '+' }}]</span>
		</div>
		<ul v-show="isOpen" v-if="hasParent">
			<tree-item 
				class=""
				:item="item.parent"
			></tree-item>
		</ul>
	</li>
</template>

<script type="text/javascript">

	export default {

	    props: {

          	item: Object

        },

	    data() {

	        return {

	        	isOpen: false,

	        }

		},

		computed: {

			hasParent: function() {

				return this.item.parent;

			}

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

			toggle: function() {
				if (this.hasParent) {
					this.isOpen = ! this.isOpen;
				}
			},
		}
  	}

</script>