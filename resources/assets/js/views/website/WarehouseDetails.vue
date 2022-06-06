<template>
	<div>
		<main>
			<!-- store image Section Started  -->
			<section class="store-img">
				<div class="container">
					<div class="row">
						<div class="col-md-8 tab-content" data-aos="zoom-in" data-aos-duration="1000">
							<img
							class="img-fluid responsive-margin"
							:src="'/images/about-us-banner.jpg'"
							alt="img"
							/>
						</div>
						<div class="col-md-4 text-center" >
							<img
							class="img-fluid small-img"
							data-aos="zoom-in" data-aos-duration="1000"
							:src="'/images/store-img-1.png'"
							alt="img"
							/>
							<img
							class="img-fluid small-img"
							data-aos="zoom-in" data-aos-duration="1000"
							:src="'/images/store-img-2.png'"
							alt="img"
							/>
							<a href="" class="btn-book btn-width mx-auto" data-bs-toggle="modal" data-bs-target="#staticBackdrop">Get A Quote</a>
						</div>
						<div class="col-12" data-aos="fade-up" data-aos-duration="1000">
							<h3>GHB Building</h3>
							<p>
								<span class="me-3"><i class="fas fa-map-marker-alt"></i></span>
								1/A, HB Tower, Gulsan-1, Dhaka, Bangladesh
							</p>
						</div>
					</div>
				</div>
			</section>
			<!-- store image Section End  -->

			<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
				<div class="modal-dialog modal-dialog-scrollable">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="staticBackdropLabel">Get A Quote</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">
							<!-- Form -->
							<form class="row g-3 form-bg form-bg-quote" name="get-a-quote" action="javascript:void(0)">
								<div class="col-md-6 col-12">
									<label class="form-label form-label-quote">First Name *</label>
									<input type="text" class="form-control" required />
								</div>
								<div class="col-md-6 col-12">
									<label class="form-label form-label-quote">Last Name *</label>
									<input type="text" class="form-control" required />
								</div>
								<div class="col-12">
									<label class="form-label form-label-quote">Email *</label>
									<input
									type="email"
									class="form-control"
									required
									/>
								</div>
								<div class="col-12">
									<label class="form-label form-label-quote">Phone *</label>
									<input
									type="tel"
									class="form-control"
									placeholder="01711111111"
									required
									/>
								</div>
								<div class="col-12">
									<label class="form-label form-label-quote">Company *</label>
									<input type="text" class="form-control" required />
								</div>
								<div class="col-12">
									<label class="form-label form-label-quote">Industry *</label><br>
									<select name="industry" id="industry" onchange="">
										<option value="" selected="selected"></option>
										<option value="adult">Adult</option>
										<option value="alcohol-and-soft-drinks">Alcohol and Soft Drinks</option>
										<option value="apparel">Apparel</option>
										<option value="arts-and-craft">Arts and Craft</option>
										<option value="automotive-and-parts">Automotive and Parts</option>
										<option value="baby-and-toddler">Baby and toddler</option>
										<option value="cosmetics-and-grooming">Cosmetics and Grooming</option>
										<option value="cds-vinyl-dvds-books-and-magazines">CDs, Vinyl, DVDs, Books and Magazines</option>
										<option value="diy">DIY</option>
										<option value="fitness-and-sporting-goods">Fitness and Sporting Goods</option>
										<option value="food">Food</option>
										<option value="health-and-nutrition">Health and Nutrition</option>
										<option value="homeware">Homeware</option>
										<option value="jewellery-and-watches">Jewellery and Watches</option>
										<option value="male-grooming">Male Grooming</option>
										<option value="medical">Medical</option>
										<option value="mobile-phones-and-accessories">Mobile Phones and Accessories</option>
										<option value="Other">Other</option>
										<option value="pet-products-foods-and-supplements">Pet Products, Foods and Supplements</option>
										<option value="Tech and Gadgets">Tech and Gadgets</option>
										<option value="toys-and-games">Toys and Games</option>
									</select>
								</div>
	                <!-- <div class="col-12">
	                  <label class="form-label form-label-quote">Expected orders dispatched per day *</label><br>
	                  <select name="expected-orders" id="expected-orders" onchange="">
	                    <option value="" selected="selected"></option>
	                    <option value="one-to-four">1 - 4</option>
	                    <option value="four-to-ten">4 - 10</option>
	                    <option value="eleven-to-thirty">11 - 30</option>
	                    <option value="thirty-one-to-one-hundred">31 - 100</option>
	                    <option value="one-hundred-one-to-five Hundred">101 - 500</option>
	                    <option value="five-hundred-one-to-nine-nundred">501 - 900</option>
	                    <option value="nine-hundred-plus">900+</option>
	                  </select>
	              </div> -->
	              <div class="col-12
	              select-warehouse">
	              <label class="form-label
	              form-label-quote">
	              Select Warehouse *
	          </label>
	          <select name="select-warehouse" id="select-warehouse" onchange="">
	          	<option value="" selected="selected"></option>
	          	<option value="warehouse-1">Warehouse - 1</option>
	          	<option value="warehouse-2">Warehouse - 2</option>
	          	<option value="warehouse-3">Warehouse - 3</option>
	          </select>
	      </div>
	      <div class="col-12
	      how-you-want-to-store-your-products">
	      <label class="form-label
	      form-label-quote">
	      How you want to store your products? *
	  </label>
	  <select name="expected-space" id="expected-space" onchange="">
	  	<option value="" selected="selected"></option>
	  	<option value="grey-space">Grey Space</option>
	  	<option value="rack">Rack</option>
	  	<option value="pallet">Pallet</option>
	  	<option value="Basket">Basket</option>
	  </select>
	</div>
	<div class="col-12
	select-your-preferred-grey-space-specification">
	<label class="form-label
	form-label-quote">
	Select your preferred grey space specification * (HxW)
</label>
<select name="preferred-grey-space-size" id="preferred-grey-space-size" onchange="">
	<option value="" selected="selected"></option>
	<option value="preferred-grey-space-size-1">1000 x 2000 sq ft (Available 500 sq ft)</option>
	<option value="preferred-grey-space-size-2">1000 x 3000 sq ft (Available 1000 sq ft)</option>
	<option value="preferred-grey-space-size-3">1000 x 4000 sq ft (Available 2000 sq ft)</option>
</select>
</div>
<div class="col-12
roughly-how-much-much-grey-space-do-you-need-to-store">
<label class="form-label
form-label-quote">
Roughly how much much grey space do you need to store? *
</label>
<select name="expected-grey-space" id="expected-grey-space" onchange="">
	<option value="" selected="selected"></option>
	<option value="500sft-1000sft">500sft - 1000sft</option>
	<option value="1001sft-3000sft">1001sft - 3000sft</option>
	<option value="3000sft+">3000sft+</option>
</select>
</div>
<div class="col-12
select-your-preferred-rack-specification">
<label class="form-label
form-label-quote">
Select your preferred rack specification * (LxWxH)
</label>
<select name="preferred-rack-size" id="preferred-rack-size" onchange="">
	<option value="" selected="selected"></option>
	<option value="preferred-rack-size-1">56X28X17cm (100 Pcs Available)</option>
	<option value="preferred-rack-size-2">86X38X17cm (190 Pcs Available)</option>
	<option value="preferred-rack-size-3">76X48X17cm (50 Pcs Available)</option>
</select>
</div>
<div class="col-12
roughly-how-many-racks-do-you-need-to-store">
<label class="form-label
form-label-quote">
Roughly how many racks do you need to store? *
</label>
<select name="expected-racks" id="expected-racks" onchange="">
	<option value="" selected="selected"></option>
	<option value="one-to-ten">1 - 10</option>
	<option value="eleven-to-thirty">11 - 30</option>
	<option value="thirty-one-to-fifty">31 - 50</option>
	<option value="fifty-one-to-one-hundred">51 - 100</option>
	<option value="one-hundred-plus">100+</option>
</select>
</div>
<div class="col-12
select-your-preferred-pallet-specification">
<label class="form-label
form-label-quote">
Select your preferred pallet specification * (LxWxH)
</label>
<select name="preferred-pallet-size" id="preferred-pallet-size" onchange="">
	<option value="" selected="selected"></option>
	<option value="preferred-pallet-size-1">56X28X17cm (50 Pcs Available)</option>
	<option value="preferred-pallet-size-2">86X38X17cm (90 Pcs Available)</option>
	<option value="preferred-pallet-size-3">76X48X17cm (150 Pcs Available)</option>
</select>
</div>
<div class="col-12
roughly-how-many-pallets-do-you-need-to-store">
<label class="form-label
form-label-quote">
Roughly how many pallets do you need to store? *
</label>
<select name="expected-pallets" id="expected-pallets" onchange="">
	<option value="" selected="selected"></option>
	<option value="one-to-ten">1 - 10</option>
	<option value="eleven-to-thirty">11 - 30</option>
	<option value="thirty-one-to-fifty">31 - 50</option>
	<option value="fifty-one-to-one-hundred">51 - 100</option>
	<option value="one-hundred-plus">100+</option>
</select>
</div>
<div class="col-12
select-your-preferred-basket-specification">
<label class="form-label
form-label-quote">
Select your preferred basket specification * (LxWxH)
</label>
<select name="preferred-basket-size" id="preferred-basket-size" onchange="">
	<option value="" selected="selected"></option>
	<option value="preferred-basket-size-1">56X28X17cm (50 Pcs Available)</option>
	<option value="preferred-basket-size-2">86X38X17cm (90 Pcs Available)</option>
	<option value="preferred-basket-size-3">76X48X17cm (150 Pcs Available)</option>
</select>
</div>
<div class="col-12
roughly-how-many-basket-do-you-need-to-store">
<label class="form-label
form-label-quote">
Roughly how many basket do you need to store? *
</label>
<select name="expected-basket" id="expected-basket" onchange="">
	<option value="" selected="selected"></option>
	<option value="one-to-ten">1 - 10</option>
	<option value="eleven-to-thirty">11 - 30</option>
	<option value="thirty-one-to-fifty">31 - 50</option>
	<option value="fifty-one-to-one-hundred">51 - 100</option>
	<option value="one-hundred-plus">100+</option>
</select>
</div>
<div class="col-12">
	<label class="form-label form-label-quote">Where Would You Like to Dispatch Most of Your Orders From? *</label><br>
	<select name="dispatch-orders" id="dispatch-orders" onchange="">
		<option value="" selected="selected"></option>
		<option value="Dhaka">Dhaka</option>
		<option value="Chittagong">Chittagong</option>
		<option value="Rajshahi">Rajshahi</option>
		<option value="Sylhet">Sylhet</option>
		<option value="Rangpur">Rangpur</option>
		<option value="Mymensingh">Mymensingh</option>
		<option value="Khulna">Khulna</option>
		<option value="Barisal">Barisal</option>
	</select>
</div>
<div class="col-12 mb-3">
	<label class="form-label form-label-quote">Message</label>
	<textarea class="form-control" rows="4"></textarea>
</div>
<div class="col-12 text-end">
	<a href="" class="quote-btn w-100" type="submit">Submit</a>
</div>
</form>
<!-- Form -->
</div>
</div>
</div>
</div>
<!-- Modal Desc -->

<!-- 
<section class="details-heading" data-aos="fade-up" data-aos-duration="1000">
	<div class="container">
		<div class="row">
			<div class="col-12">
				<p>
					<span class="me-3"><i class="fas fa-server"></i></span>
					Description
				</p>
			</div>
		</div>
	</div>
</section> 
-->

<!-- Featurs Section Started  -->
<section class="features">
	<div class="container">
		<div class="row storage-list mb-4" data-aos="fade-up" data-aos-duration="1000">
			<div class="col-12">
				<h4 class="mb-4">Features :</h4>
			</div>
			<div class="col-md-6">
	              <!-- <div class="col-12 warehouse-features d-flex flex-row flex-wrap">
	                <div class="col-md-6 col-12 p-3">
	                  <h6><b>Safety And Security</b></h6>
	                  <ul>
	                    <li>24/7 Security</li>
	                    <li>Alarmed</li>
	                    <li>CCTV</li>
	                    <li>Patrolled</li>
	                  </ul>
	                </div>
	                <div class="col-md-6 col-12 p-3">
	                  <h6><b>Storage Capabilities</b></h6>
	                  <ul>
	                    <li>Container</li>
	                    <li>High Value</li>
	                    <li>Bulk Liquid</li>
	                    <li>Bulk Powder</li>
	                  </ul>
	                </div>
	                <div class="col-md-6 col-12 p-3">
	                  <h6><b>Fulfilment</b></h6>
	                  <ul>
	                    <li>3rd Party Couriers</li>
	                    <li>Domestic Fulfilment</li>
	                    <li>Online Inventory Management System</li>
	                    <li>Own Fleet</li>
	                    <li>Picking & Pack</li>
	                    <li>Receiving and Shipping</li>
	                  </ul>
	                </div>
	                <div class="col-md-6 col-12 p-3">
	                  <h6><b>Other Services</b></h6>
	                  <ul>
	                    <li>Container Devanning and Subsorting</li>
	                    <li>Critical Inventory Logistics</li>
	                    <li>Flexible Operations Schedule</li>
	                    <li>Shrink Wrapping</li>
	                    <li>Special Projects</li>
	                    <li>Waste Disposal</li>
	                  </ul>
	                </div>
	            </div> -->
	            <ul>
	            	<li>
	            		24/7 Security, Alarmed, CCTV & Patrolled.
	            	</li>
	            	<li>
	            		Container, High Value, Bulk Liquid & Bulk Powder.
	            	</li>
	            	<li>
	            		3rd Party Couriers, Domestic Fulfilment, Online Inventory Management System, Own Fleet, Picking & Pack, Receiving and Shipping.
	            	</li>
	            	<li>
	            		Container Devanning and Subsorting, Critical Inventory Logistics, Flexible Operations Schedule, Shrink Wrapping, Special Projects & Waste Disposal.
	            	</li>
	            </ul>
	        </div>
	        <div class="col-md-6">
	        	<iframe id="gmap_canvas" src="https://maps.google.com/maps?q=Kiva%20Han%20Gulshan%201&amp;t=&amp;z=15&amp;ie=UTF8&amp;iwloc=&amp;output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" style="
	        	width:100%;height:100%"></iframe>
	        </div>
	    </div>

	    <!-- accordion start -->
	    <div class="row storage-list" data-aos="fade-up" data-aos-duration="1000">
	    	<div class="col-12">
	    		<h4 class="mb-4">Storages</h4>
	    	</div>
	    	<div class="col-12">
	    		<div class="accordion" id="accordionExample">
	    			<div class="accordion-item custom-accordion-item">
	    				<h2 class="accordion-header" id="headingOne">
	    					<button class="accordion-button custom-accordion-btn" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
	    						<a href="javascript:void(0)"><h5 class="button1 third-tab-section-link"><strong>Grey Space</strong></h5></a>
	    					</button>
	    				</h2>
	    				<div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
	    					<div class="accordion-body custom-accordion-body d-flex flex-row flex-wrap">
	    						<div class="col-md-6 col-12">
	    							<p>
	    								<ul class="storage-list-item">
	    									<li>
	    										Grey Space 1: Unit sizes 250, 500, 1000, 1800 sq.ft.<br>
	    										Heated or cold storage<br>
	    										13’ clear ceiling height
	    									</li>
	    									<li>
	    										Grey Space 2: Unit sizes 250, 500, 1000, 1800, 2400 sq.ft.<br>
	    										Heated or cold storage<br>
	    										13’ clear ceiling height
	    									</li>
	    									<li>
	    										Grey Space 3: Unit sizes 250, 500, 1000 sq.ft.<br>
	    										Heated or cold storage<br>
	    										13’ clear ceiling height
	    									</li>
	    									<li>
	    										Grey Space 4: Unit sizes 250, 500, 1000, 1800, 2400, 2800 sq.ft.<br>
	    										Heated or cold storage<br>
	    										13’ clear ceiling height
	    									</li>
	    								</ul>
	    							</p>
	    						</div>
	    						<div class="col-md-6 col-12 d-flex flex-row flex-wrap">
	    							<div class="col-md-8 col-12 text-center p-2" data-aos="zoom-in" data-aos-duration="1000">
	    								<img class="img-fluid w-100" :src="'/images/grey-space.jpg'" alt="img">
	    							</div>
	    							<div class="col-md-4 col-12">
	    								<div class="col-12 p-2" data-aos="zoom-in" data-aos-duration="1000">
	    									<img class="img-fluid w-100" :src="'/images/grey-space.jpg'" alt="img">
	    								</div>
	    								<div class="col-12 p-2" data-aos="zoom-in" data-aos-duration="1000">
	    									<img class="img-fluid w-100" :src="'/images/grey-space.jpg'" alt="img">
	    								</div>
	    							</div>
	    						</div>
	    					</div>
	    				</div>
	    			</div>
	    			<div class="accordion-item custom-accordion-item">
	    				<h2 class="accordion-header" id="headingTwo">
	    					<button class="accordion-button custom-accordion-btn collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
	    						<a href="javascript:void(0)"><h5 class="button2 third-tab-section-link"><strong>Rack</strong></h5></a>
	    					</button>
	    				</h2>
	    				<div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
	    					<div class="accordion-body custom-accordion-body d-flex flex-row flex-wrap">
	    						<div class="col-md-6 col-12">
	    							<p>
	    								<ul class="storage-list-item">
	    									<li>
	    										Rack 1: Size (L x W x H) = 8 feet X 4 feet X 8 feet
	    										<br>
	    										Material : Metal<br>
	    										Each shelf load capacity up to 2 ton<br>
	    										Height can be increased up to 23 feet
	    									</li>
	    									<li>
	    										Rack 2: Size (L x W x H) = 2550 X 600 X 2400 mm
	    										<br>
	    										Material : Wood & Steel<br>
	    										Each shelf load capacity up to 2 ton<br>
	    										Height can be increased up to 23 feet
	    									</li>
	    								</ul>
	    							</p>
	    						</div>
	    						<div class="col-md-6 col-12 d-flex flex-row flex-wrap">
	    							<div class="col-md-8 col-12 text-center p-2" data-aos="zoom-in" data-aos-duration="1000">
	    								<img class="img-fluid w-100" :src="'/images/rack.jpg'" alt="img">
	    							</div>
	    							<div class="col-md-4 col-12">
	    								<div class="col-12 p-2" data-aos="zoom-in" data-aos-duration="1000">
	    									<img class="img-fluid w-100" :src="'/images/rack.jpg'" alt="img">
	    								</div>
	    								<div class="col-12 p-2" data-aos="zoom-in" data-aos-duration="1000">
	    									<img class="img-fluid w-100" :src="'/images/rack.jpg'" alt="img">
	    								</div>
	    							</div>
	    						</div>
	    					</div>
	    				</div>
	    			</div>
	    			<div class="accordion-item custom-accordion-item">
	    				<h2 class="accordion-header" id="headingThree">
	    					<button class="accordion-button custom-accordion-btn collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
	    						<a href="javascript:void(0)"><h5 class="button3 third-tab-section-link"><strong>Pallet</strong></h5></a>
	    					</button>
	    				</h2>
	    				<div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
	    					<div class="accordion-body custom-accordion-body d-flex flex-row flex-wrap">
	    						<div class="col-md-6 col-12">
	    							<p>
	    								<ul class="storage-list-item">
	    									<li>
	    										Pallet 1: Type = Single Pallet, Size (L x W x H) = 1200x1000x160mm
	    										<br>
	    										Load (KG): Floor Static: 1,000.
	    									</li>
	    									<li>
	    										Pallet 2: Type = Double Pallet, Size (L x W x H) = 1200x1000x160mm
	    										<br>
	    										Floor Static Load : 6000 kg<br>
	    										Dynamic Load: 1000 kg<br>
	    										Racking Load: 1000 kg
	    									</li>
	    									<li>
	    										Pallet 3: Type = Double Pallet, Size (L x W x H) = 1200x1000x150mm
	    										<br>
	    										Floor Static Load : 5000 kg<br>
	    										Dynamic Load: 1000 kg<br>
	    										Racking Load: 1000 kg
	    									</li>
	    								</ul>
	    							</p>
	    						</div>
	    						<div class="col-md-6 col-12 d-flex flex-row flex-wrap">
	    							<div class="col-md-8 col-12 text-center p-2" data-aos="zoom-in" data-aos-duration="1000">
	    								<img class="img-fluid w-100" :src="'/images/pallet.jpg'" alt="img">
	    							</div>
	    							<div class="col-md-4 col-12">
	    								<div class="col-12 p-2" data-aos="zoom-in" data-aos-duration="1000">
	    									<img class="img-fluid w-100" :src="'/images/pallet.jpg'" alt="img">
	    								</div>
	    								<div class="col-12 p-2" data-aos="zoom-in" data-aos-duration="1000">
	    									<img class="img-fluid w-100" :src="'/images/pallet.jpg'" alt="img">
	    								</div>
	    							</div>
	    						</div>
	    					</div>
	    				</div>
	    			</div>
	    			<div class="accordion-item custom-accordion-item">
	    				<h2 class="accordion-header" id="headingFour">
	    					<button class="accordion-button custom-accordion-btn collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
	    						<a href="javascript:void(0)"><h5 class="button4 third-tab-section-link"><strong>Basket</strong></h5></a>
	    					</button>
	    				</h2>
	    				<div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#accordionExample">
	    					<div class="accordion-body custom-accordion-body d-flex flex-row flex-wrap">
	    						<div class="col-md-6 col-12">
	    							<p>
	    								<ul class="storage-list-item">
	    									<li>
	    										Basket 1: Size (L x W x H) = 530x350x300 mm
	    										<br>
	    										Capacity = 40 ltr
	    									</li>
	    									<li>
	    										Basket 2: Size (L x W x H) = 530x350x450 mm
	    										<br>
	    										Capacity = 80 ltr
	    									</li>
	    									<li>
	    										Basket 3: Size (L x W x H) = 650x350x300 mm
	    										<br>
	    										Capacity = 90 ltr
	    									</li>
	    									<li>
	    										Basket 4: Size (L x W x H) = 790x350x300 mm
	    										<br>
	    										Capacity = 100 ltr
	    									</li>
	    									<li>
	    										Basket 5: Size (L x W x H) = 900x350x450 mm
	    										<br>
	    										Capacity = 120 ltr
	    									</li>
	    								</ul>
	    							</p>
	    						</div>
	    						<div class="col-md-6 col-12 d-flex flex-row flex-wrap">
	    							<div class="col-md-8 col-12 text-center p-2" data-aos="zoom-in" data-aos-duration="1000">
	    								<img class="img-fluid w-100" :src="'/images/basket.jpg'" alt="img">
	    							</div>
	    							<div class="col-md-4 col-12">
	    								<div class="col-12 p-2" data-aos="zoom-in" data-aos-duration="1000">
	    									<img class="img-fluid w-100" :src="'/images/basket.jpg'" alt="img">
	    								</div>
	    								<div class="col-12 p-2" data-aos="zoom-in" data-aos-duration="1000">
	    									<img class="img-fluid w-100" :src="'/images/basket.jpg'" alt="img">
	    								</div>
	    							</div>
	    						</div>
	    					</div>
	    				</div>
	    			</div>
	    		</div>
	    	</div>
	            <!-- <div class="col-md-6">
	              <div class="row third-tab-section-display active">
	                <div class="col-12 text-center p-0" data-aos="zoom-in" data-aos-duration="1000">
	                  <img class="img-fluid second-tab-section-display w-100 active" :src="/images/storage-big.png" alt="img">
	                </div>
	                <div class="col-12 text-center p-0" data-aos="zoom-in" data-aos-duration="1000">
	                  <img class="img-fluid second-tab-section-display w-100" :src="/images/storage-big-copy.png" alt="img">
	                </div>
	                <div class="col-6" data-aos="zoom-in" data-aos-duration="1000">
	                  <a href="javascript:void(0)">
	                  <img class="img-fluid button1 second-tab-section-link w-100" :src="/images/storage-small.png" alt="img">
	                  </a>
	                </div>
	                <div class="col-6" data-aos="zoom-in" data-aos-duration="1000">
	                  <a href="javascript:void(0)">
	                  <img class="img-fluid button2 second-tab-section-link w-100" :src="/images/storage-small.png" alt="img">
	                  </a>
	                </div>
	              </div>
	              <div class="row third-tab-section-display">
	                <div class="col-12 text-center p-0" data-aos="zoom-in" data-aos-duration="1000">
	                  <img class="img-fluid second-tab-section-display w-100 active" :src="/images/storage-big.png" alt="img">
	                </div>
	                <div class="col-12 text-center" data-aos="zoom-in" data-aos-duration="1000">
	                  <img class="img-fluid second-tab-section-display w-100" :src="/images/storage-big-copy.png" alt="img">
	                </div>
	                <div class="col-6 text-end" data-aos="zoom-in" data-aos-duration="1000">
	                  <a href="javascript:void(0)">
	                  <img class="img-fluid button1 second-tab-section-link w-100" :src="/images/storage-smal.png" alt="img">
	                  </a>
	                </div>
	                <div class="col-6" data-aos="zoom-in" data-aos-duration="1000">
	                  <a href="javascript:void(0)">
	                  <img class="img-fluid button2 second-tab-section-link w-100" :src="/images/storage-small.png" alt="img">
	                  </a>
	                </div>
	              </div>
	              <div class="row third-tab-section-display">
	                <div class="col-12 text-center p-0" data-aos="zoom-in" data-aos-duration="1000">
	                  <img class="img-fluid second-tab-section-display w-100 active" :src="/images/storage-big.png" alt="img">
	                </div>
	                <div class="col-12 text-center" data-aos="zoom-in" data-aos-duration="1000">
	                  <img class="img-fluid second-tab-section-display w-100" :src="/images/storage-big-copy.png" alt="img">
	                </div>
	                <div class="col-6 text-end" data-aos="zoom-in" data-aos-duration="1000">
	                  <a href="javascript:void(0)">
	                  <img class="img-fluid button1 second-tab-section-link w-100" :src="/images/storage-small.png" alt="img">
	                  </a>
	                </div>
	                <div class="col-6" data-aos="zoom-in" data-aos-duration="1000">
	                  <a href="javascript:void(0)">
	                  <img class="img-fluid button2 second-tab-section-link w-100" :src="/images/storage-smal.png" alt="img">
	                  </a>
	                </div>
	              </div>
	              <div class="row third-tab-section-display">
	                <div class="col-12 text-center p-0" data-aos="zoom-in" data-aos-duration="1000">
	                  <img class="img-fluid second-tab-section-display w-100 active" :src="/images/storage-big.png" alt="img">
	                </div>
	                <div class="col-12 text-center" data-aos="zoom-in" data-aos-duration="1000">
	                  <img class="img-fluid second-tab-section-display w-100" :src="/images/storage-big-copy.png" alt="img">
	                </div>
	                <div class="col-6 text-end" data-aos="zoom-in" data-aos-duration="1000">
	                  <a href="javascript:void(0)">
	                  <img class="img-fluid button1 second-tab-section-link w-100" :src="/images/storage-small.png" alt="1">
	                  </a>
	                </div>
	                <div class="col-6" data-aos="zoom-in" data-aos-duration="1000">
	                  <a href="javascript:void(0)">
	                  <img class="img-fluid button2 second-tab-section-link w-100" :src="/images/storage-small.png" alt="2">
	                  </a>
	                </div>
	              </div>
	          </div> -->
	      </div>
	      <!-- accordion end  -->
	  </div>
	</section>
	<!-- Features Section End  -->

	<query-contact/>
</main>

<footer-component/>
<copyright-component/>
</div>

</template>

<script>
	export default {
		mounted() {
			AOS.init({once: true});
		},
		data () {
			return {

			}
		}
	}
</script>

<style lang="css" scoped>
</style>