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
					<img class="img-fluid" :src="'/website/index/customers/kiva-han.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/website/index/customers/bao.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/website/index/customers/smiths-caffe-regalo.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/website/index/customers/shack.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/website/index/customers/smiths.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/website/index/customers/kiva-han-coffee-company.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/website/index/customers/coffee-connoisseur.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/website/index/customers/coffee-house.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/website/index/customers/kiva-han-patisserie.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/website/index/customers/baoxi.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/website/index/customers/awesome-dimsum.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/website/index/customers/georges-pizza.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/website/index/customers/pazzi-fox.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/website/index/customers/frangos.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/website/index/customers/florian.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/website/index/customers/qupaid.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/website/index/customers/davinci.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/website/index/customers/catcher.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/website/index/customers/astoria.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/website/index/customers/mazzer.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/website/index/customers/wega.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/website/index/customers/faema.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/website/index/customers/conti.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/website/index/customers/rok.png'" alt="stiker" />
				</div>
				<div class="item">
					<img class="img-fluid" :src="'/website/index/customers/slayer.png'" alt="stiker" />
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