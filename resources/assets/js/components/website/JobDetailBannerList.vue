<template>
	<!-- Carousel Started  -->
	<div class="hero-area">
		<div
		id="carouselExampleIndicators"
		class="carousel slide"
		data-bs-ride="carousel"
		>
			<div class="carousel-inner">
				<div class="carousel-item active bg-jobs"
				style="background-image: url(images/bg-2.png)">
					<div class="container-fluid" data-aos="fade-right" data-aos-duration="1000">
						<div class="row align-items-center" style="height: 200px">
							<div class="col-lg-7 col-9 slider-content-jobs">
								<h1 class="text-white">Warehouse Manager</h1>
								<p class="text-white">Job ID: 1910896 | Gudam</p>
								<button class="hero-btn" @click="$emit('showJobApplyForm')">Apply Now</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<!-- Carousel End  -->
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