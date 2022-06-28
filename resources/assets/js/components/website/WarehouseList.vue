<template>
	<!-- Gudam Details Start  -->
	<section class="gudam-details">
		<div class="container">
			<div class="row g-4 d-flex align-content-stretch">
				<div class="col-lg-7 col-xl-6">
					<div class="scroll-box" data-aos="fade-up" data-aos-duration="1000">
						<router-link :to="{ name: 'warehouse-details', params: { id: 1 }}">
							<div class="row details-box">
								<div class="col-md-5 col-12">
									<img
									class="img-fluid"
									src="/website/warehouses/warehouse.jpg"
									alt="img"
									/>
								</div>
								<div class="col-md-7 col-12">
									<h5>HB Tower</h5>
									<p>
										<span><i class="fas fa-map-marker-alt"></i></span>1/A, HB Tower, Gulsan-1, Dhaka, Bangladesh
									</p>
									<!-- <h4>BDT 25000</h4> -->
								</div>
							</div>
						</router-link>
					</div>
				</div>
				<div class="col-lg-5 col-xl-6 flex-fill inherit-map-size" data-aos="zoom-in" data-aos-duration="1800">
					<div class="flex-fill">
						<iframe id="gmap_canvas" src="https://maps.google.com/maps?q=Kiva%20Han%20Gulshan%201&amp;t=&amp;z=15&amp;ie=UTF8&amp;iwloc=&amp;output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" style="width:100%;height:100%"></iframe>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- Gudam Details End  -->
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