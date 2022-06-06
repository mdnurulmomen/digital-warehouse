<template>
	<!-- Our Customers start  -->
	<div class="customers" data-aos="fade-up" data-aos-duration="1000">
		<div class="container">
			<h3 class="title text-center mb-5 pb-5">Our Partners</h3>
			
			<carousel 
				:items="5" 
				:nav="false" 
				:loop="true" 
				:autoplay="true" 
				:margin="10" 
				:responsive="{0:{items:1,nav:false},600:{items:3,nav:false},1000:{items:5,nav:false}}"
			>
				<div class="item">
					<img class="img-fluid" :src="'/images/customer/kiva-han.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/images/customer/bao.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/images/customer/smiths-caffe-regalo.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/images/customer/shack.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/images/customer/smiths.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/images/customer/kiva-han-coffee-company.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/images/customer/coffee-connoisseur.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/images/customer/coffee-house.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/images/customer/kiva-han-patisserie.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/images/customer/baoxi.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/images/customer/awesome-dimsum.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/images/customer/georges-pizza.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/images/customer/pazzi-fox.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/images/customer/frangos.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/images/customer/florian.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/images/customer/qupaid.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/images/customer/davinci.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/images/customer/catcher.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/images/customer/astoria.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/images/customer/mazzer.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/images/customer/wega.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/images/customer/faema.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/images/customer/conti.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/images/customer/rok.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/images/customer/slayer.png'" alt="stiker" />
				</div>
			</carousel>
		</div>
	</div>
	<!-- Our Customers End  -->
</template>

<script>
export default {
  data () {
    return {

    }
  }
}
</script>

<style lang="css">
.owl-theme .owl-dots .owl-dot.active span {
	background: #23b8bf !important;
}
</style>