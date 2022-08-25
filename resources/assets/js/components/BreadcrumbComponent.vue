<template>
	<div class="page-header card">
		<div class="row align-items-center">
			<div class="col-6">
				<div class="row">
					<div class="col-12 d-flex align-items-center">
						<div class="d-inline mr-2">
							<img :src="'/icons/cms/' + icon + '.png'">
						</div>

						<div class="d-inline">
							<h5>{{ title | capitalize }}</h5>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-12 mt-1">
						<h6 class="text-justify">{{ message | capitalize }}</h6>
					</div>
				</div>
			</div>

			<div class="col-6">
				<div class="text-right">
					<ul>
						<li class="breadcrumb-item d-inline">
							<router-link :to="{ name: 'home' }">
								<i class="feather icon-home"></i>
							</router-link>
						</li>
						<li class="breadcrumb-item d-inline">
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