<template>
	<div class="page-header card">
		<div class="row align-items-end">
			<div class="col-lg-8">
				<div class="row">
					<div class="d-inline mr-2">
						<img :src="'icons/cms/' + icon + '.png'">
					</div>

					<div class="d-inline">
						<h5>{{ title | capitalize }}</h5>
					</div>
				</div>

				<div class="row">
					<span>{{ message | capitalize }}</span>
				</div>
			</div>

			<div class="col-lg-4">
				<div class="page-header-breadcrumb text-lg-right text-center">
					<ul class=" breadcrumb breadcrumb-title">
						<li class="breadcrumb-item">
							<router-link :to="{ name: 'home' }">
								<i class="feather icon-home"></i>
							</router-link>
						</li>
						<li class="breadcrumb-item">
							<router-link :to="{ name: $route.name }">
								{{ $route.name | capitalize }}
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