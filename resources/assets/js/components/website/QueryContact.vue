<template>
	<section class="cta" data-aos="fade-up" data-aos-duration="1000">
		<div class="container">
			<div class="row">
				<h3>Have More Queries?</h3>
			</div>
			
			<router-link class="more-btn" aria-current="page" :to="{ name: 'contact-us' }">
				<span>Contact Us</span>
			</router-link>
		</div>
	</section>
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