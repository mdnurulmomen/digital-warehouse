<template>
	<!-- Gudam Network Section Started  -->
	<section class="network text-center" data-aos="fade-up" data-aos-duration="1000">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-12">
					<h3 class="title mb-5">Gudam Fulfillment Network</h3>
					<img
					:src="'/images/map.gif'"
					alt="img"
					class="my-5 img-fluid"
					/>

					<router-link class="hero-btn text-white" aria-current="page" :to="{ name: 'warehouses' }" style="background-color: #23b8bf;">
			            <span>Get Started</span>
		          	</router-link>
				</div>
			</div>
		</div>
	</section>
	<!-- Gudam Network Section End  -->
</template>

<script>
export default {

  data () {
    return {

    }
  }
}
</script>

<style lang="css" scoped>
</style>