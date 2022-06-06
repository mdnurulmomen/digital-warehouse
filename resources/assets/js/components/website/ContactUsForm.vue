<template>
	<!-- Contact Form Started  -->
	<section class="contact" id="send-us-a-message">
		<div class="container">
			<div class="row g-4">
				<div class="col-lg-7 col-12" data-aos="fade-up" data-aos-duration="1000">
					<form class="row g-3 form-bg">
						<div class="col-12">
							<h4>Send Us a Message</h4>
						</div>
						<div class="col-md-6 col-12">
							<label class="form-label">First Name*</label>
							<input type="text" class="form-control" required />
						</div>
						<div class="col-md-6 col-12">
							<label class="form-label">Last Name*</label>
							<input type="text" class="form-control" required />
						</div>
						<div class="col-12">
							<label class="form-label">Email*</label>
							<input
							class="form-control"
							type="email"
							required
							/>
						</div>
						<div class="col-12">
							<label class="form-label">Subject*</label>
							<input type="text" class="form-control" required />
						</div>
						<div class="col-12 mb-3">
							<label class="form-label">Message</label>
							<textarea class="form-control" rows="4"></textarea>
						</div>
						<div class="col-12 text-end">
							<a href="" class="more-btn w-100" type="submit">Send A Message</a>
						</div>
					</form>
				</div>
				<div class="col-lg-5 col-12">
					<div class="row g-4">
						<div class="col-12" data-aos="fade-up" data-aos-duration="1000">
							<div class="address">
								<h4 style="color: #23b8bf">Office Address</h4>
								<p>HB Tower, Unit-401, House-1/A, Road 23</p>
								<p>Gulshan - 1, Dhaka - 1212</p>
								<p class="link-text"><a href="tel:+8801971234727">+880 1971-234727</a></p>
								<p class="link-text"><a href="mailto:contact@gudam.com.bd">contact@gudam.com.bd</a></p>
							</div>
							<div class="map h-100" data-aos="zoom-in" data-aos-duration="1000">
								<iframe id="gmap_canvas" src="https://maps.google.com/maps?q=Kiva%20Han%20Gulshan%201&amp;t=&amp;z=15&amp;ie=UTF8&amp;iwloc=&amp;output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" style="width:100%;height:100%"></iframe>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- Contact Form End  -->
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