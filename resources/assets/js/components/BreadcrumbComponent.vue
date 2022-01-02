<template>
	<div class="page-header card">
		<div class="row align-items-end">
			<div class="col-lg-8">
				<div class="page-header-title">
					<i class="bg-c-blue" :class="icon" aria-hidden="true"></i>
					<div class="d-inline">
						<h5>{{ title | capitalize }}</h5>
						<span>{{ message | capitalize }}</span>
					</div>
				</div>
			</div>
			<div class="col-lg-4">
				<div class="page-header-breadcrumb d-flex justify-content-center">
					<ul class=" breadcrumb breadcrumb-title">
						<li class="breadcrumb-item">
							<router-link :to="{ name: 'home' }">
								<i class="feather icon-home"></i>
							</router-link>
						</li>
						<li class="breadcrumb-item">
							<router-link :to="{ name: this.$route.name }">
								{{ this.$route.name | capitalize }}
							</router-link>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript">
	
	export default {

		props : ['title', 'message', 'icon'],

		filters : {
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
		}

	}

</script>