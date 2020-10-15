<template>
	<div class="page-header card">
		<div class="row align-items-end">
			<div class="col-lg-8">
				<div class="page-header-title">
					<i class="feather icon-clipboard bg-c-blue"></i>
					<div class="d-inline">
						<h5>{{ title | capitalize }}</h5>
						<span>{{ message }}</span>
					</div>
				</div>
			</div>
			<div class="col-lg-4">
				<div class="page-header-breadcrumb">
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

		props : ['title', 'message'],

		filters : {
			capitalize: function (value) {
				if (!value) return ''
				value = value.toString()
				return value.charAt(0).toUpperCase() + value.slice(1)
			}
		}

	}

</script>