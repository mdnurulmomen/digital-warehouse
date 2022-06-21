<template>
	<div>
		<main>
			<!-- store image Section Started  -->
			<section class="store-img">
				<div class="container">
					<div class="row">
						<div class="col-md-8 tab-content" data-aos="zoom-in" data-aos-duration="1000" style="margin-bottom: 20px">
							<img
							class="img-fluid"
							:src="warehousePreviews[0]"
							alt="img"
							/>
						</div>
						<div class="col-md-4 text-center" >
							<img
							class="img-fluid small-img"
							data-aos="zoom-in" data-aos-duration="1000"
							:src="warehousePreviews[1]" 
							@click="changePreviewOrder(1)" 
							alt="img"
							/>
							<img
							class="img-fluid small-img"
							data-aos="zoom-in" data-aos-duration="1000"
							:src="warehousePreviews[2]" 
							@click="changePreviewOrder(2)" 
							alt="img"
							/>
						</div>
					</div>
					<div class="row align-items-center mt-2">
						<div class="col-md-6" data-aos="fade-up" data-aos-duration="1000">
							<h3>GHB Building</h3>
							<p>
								<span class="me-3"><i class="fas fa-map-marker-alt"></i></span>
								1/A, HB Tower, Gulsan-1, Dhaka, Bangladesh
							</p>
						</div>
						<div class="col-md-6" data-aos="fade-up" data-aos-duration="1000">
							<a href="javascript::void(0)" class="btn-book btn-width" data-bs-toggle="modal" data-bs-target="#staticBackdrop">Get A Quote</a>
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
							<form 
							class="row g-3 form-bg form-bg-quote" 
							@submit.prevent="submitQuotation()" 
							autocomplete="off" 
							>
							<div class="col-md-6 col-sm-12 form-group">
								<label class="form-label form-label-quote">First Name *</label>
								<input 
								type="text" 
								class="form-control" 
								v-model="singleWarehouseQuotationData.first_name" 
								placeholder="Place Your First Name" 
								:class="!errors.first_name ? 'is-valid' : 'is-invalid'" 
								:readonly="formSubmitted" 
								@change="validateFormInput('first_name')"  
								/>

								<div class="invalid-feedback">
									{{ errors.first_name }}
								</div>
							</div>
							<div class="col-md-6 col-sm-12 form-group">
								<label class="form-label form-label-quote">Last Name *</label>
								<input 
								type="text" 
								class="form-control" 
								v-model="singleWarehouseQuotationData.last_name" 
								placeholder="Place Your Last Name" 
								:class="!errors.last_name ? 'is-valid' : 'is-invalid'" 
								:readonly="formSubmitted" 
								@change="validateFormInput('last_name')" 
								/>

								<div class="invalid-feedback">
									{{ errors.last_name }}
								</div>
							</div>

							<div class="col-md-12 col-sm-12 form-group">
								<label class="form-label form-label-quote">Company *</label>
								<input 
								type="text" 
								class="form-control" 
								v-model="singleWarehouseQuotationData.company_name" 
								placeholder="Place Your Company Name" 
								:class="!errors.company_name ? 'is-valid' : 'is-invalid'" 
								:readonly="formSubmitted" 
								@change="validateFormInput('company_name')" 
								/>

								<div class="invalid-feedback">
									{{ errors.company_name }}
								</div>
							</div>

							<div class="col-md-12 col-sm-12 form-group">
								<label class="form-label form-label-quote">Email *</label>
								<input
								type="email" 
								class="form-control" 
								v-model="singleWarehouseQuotationData.email" 
								placeholder="Place Your Email" 
								:class="!errors.email ? 'is-valid' : 'is-invalid'" 
								:readonly="formSubmitted" 
								@change="validateFormInput('email')" 
								/>

								<div class="invalid-feedback">
									{{ errors.email }}
								</div>
							</div>

							<div class="col-md-12 col-sm-12 form-group">
								<label class="form-label form-label-quote">Contact No *</label>
								<input
								type="tel" 
								class="form-control" 
								v-model="singleWarehouseQuotationData.mobile" 
								placeholder="Place Your Contact Number" 
								:class="!errors.mobile ? 'is-valid' : 'is-invalid'" 
								:readonly="formSubmitted" 
								@change="validateFormInput('mobile')" 
								/>

								<div class="invalid-feedback">
									{{ errors.mobile }}
								</div>
							</div>

							<div class="col-md-12 col-sm-12 form-group">
								<label class="form-label form-label-quote">Industry *</label><br>
								<select 
								class="form-control" 
								v-model="singleWarehouseQuotationData.industry" 
								:class="!errors.industry ? 'is-valid' : 'is-invalid'" 
								:readonly="formSubmitted" 
								@change="validateFormInput('industry')" 
								>
								<option disabled selected>Mention Your Industry Name</option>
								<option value="alcohol-and-soft-drinks">Alcohol and Soft Drinks</option>
								<option value="apparel">Apparel</option>
								<option value="arts-and-craft">Arts and Craft</option>
								<option value="automotive-and-parts">Automotive and Parts</option>
								<option value="baby-and-toddler">Baby and toddler</option>
								<option value="cosmetics-and-grooming">Cosmetics and Grooming</option>
								<option value="fitness-and-sporting-goods">Fitness and Sporting Goods</option>
								<option value="food">Food</option>
								<option value="health-and-nutrition">Health and Nutrition</option>
								<option value="homeware">Homeware</option>
								<option value="jewellery-and-watches">Jewellery and Watches</option>
								<option value="male-grooming">Male Grooming</option>
								<option value="medical">Medical</option>
								<option value="mobile-phones-and-accessories">Mobile Phones and Accessories</option>
								<option value="pet-products-foods-and-supplements">Pet Products, Foods and Supplements</option>
								<option value="tech and Gadgets">Tech and Gadgets</option>
								<option value="toys-and-games">Toys and Games</option>
								<option value="other">Other</option>
							</select>

							<div class="invalid-feedback">
								{{ errors.industry }}
							</div>
						</div>

						<div class="col-md-12 col-sm-12 form-group">
							<label class="form-label
							form-label-quote">
							Selected Warehouse *
						</label>
						<select 
						class="form-control is-valid" 
						v-model="singleWarehouseQuotationData.warehouse_id" 
						:readonly="formSubmitted" 
						>
						<option value="1" selected>Warehouse - 1</option>
					</select>

									<!-- 
									<div class="invalid-feedback">
							        	{{ errors.warehouse }}
							  		</div> 
							  	-->
							  </div>

							  <div class="col-md-12 col-sm-12 form-group">
							  	<label class="form-label
							  	form-label-quote">
							  	How you want to store your products? *
							  </label>
							  <select 
							  class="form-control" 
							  v-model="singleWarehouseQuotationData.container_type_id" 
							  :class="!errors.container_type ? 'is-valid' : 'is-invalid'" 
							  :readonly="formSubmitted" 
							  @change="validateFormInput('container_type')" 
							  >
							  <option disabled selected>Required Space Type</option>
							  <option value="1">Grey Space</option>
							  <option value="2">Rack</option>
							  <option value="3">Pallet</option>
							</select>

							<div class="invalid-feedback">
								{{ errors.container_type }}
							</div>
						</div>

						<div class="col-md-12 col-sm-12 form-group">
							<label class="form-label form-label-quote">
								Select your preferred rack specification * (LxWxH)
							</label>
							<select
							class="form-control is-valid" 
							v-model="singleWarehouseQuotationData.container_id" 
							:readonly="formSubmitted" 
							>
							<option disabled selected>Required Rack Size</option>
							<option value="1">56X28X17cm (100 Pcs Available)</option>
							<option value="2">86X38X17cm (190 Pcs Available)</option>
							<option value="3">76X48X17cm (50 Pcs Available)</option>
						</select>

									<!-- 
									<div class="invalid-feedback">
							        	{{ errors.container }}
							  		</div> 
							  	-->
							  </div>

							  <div class="col-md-12 col-sm-12 form-group">
							  	<label class="form-label form-label-quote">
							  		Roughly how many racks do you need ? *
							  	</label>

							  	<input 
							  	type="number" 
							  	class="form-control" 
							  	v-model="singleWarehouseQuotationData.quantity" 
							  	placeholder="Required Rack Number" 
							  	:class="!errors.quantity ? 'is-valid' : 'is-invalid'" 
							  	:readonly="formSubmitted" 
							  	@change="validateFormInput('quantity')" 
							  	/>

							  	<div class="invalid-feedback">
							  		{{ errors.quantity }}
							  	</div>
							  </div>
							  <div class="col-md-12 col-sm-12 form-group mb-3">
							  	<label class="form-label form-label-quote">Addtional Message</label>
							  	<textarea 
							  	class="form-control" 
							  	v-model="singleWarehouseQuotationData.message" 
							  	:class="!errors.message ? 'is-valid' : 'is-invalid'" 
							  	:readonly="formSubmitted" 
							  	@change="validateFormInput('message')" 
							  	rows="4"
							  	></textarea>

							  	<div class="invalid-feedback">
							  		{{ errors.message }}
							  	</div>
							  </div>

							  <div class="col-md-12 col-sm-12 text-center" v-show="Object.keys(quotationFormFailureMessages).length > 0">
							  	<ul>
							  		<li 
							  		class="text-danger text-start" 
							  		v-for="x in quotationFormFailureMessages"
							  		>
							  		{{ x[0] }}
							  	</li>
							  </ul>
							</div>

							<div class="col-md-12 col-sm-12 text-center" v-show="quotationFormSuccessMessage && Object.keys(quotationFormFailureMessages).length == 0 && Object.keys(errors).length == 0">
								<span style="color: #23b8bf">
									{{ quotationFormSuccessMessage }}
								</span>
							</div>

							<div class="col-md-12 col-sm-12 text-end">
								<button class="quote-btn w-100">Submit Requirement</button>
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
	            <!-- 
	            <div class="col-12 warehouse-features d-flex flex-row flex-wrap">
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
	            </div> 
	        -->
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
									<img class="img-fluid w-100" :src="'/website/warehouses/grey-space.jpg'" alt="img">
								</div>
								<div class="col-md-4 col-12">
									<div class="col-12 p-2" data-aos="zoom-in" data-aos-duration="1000">
										<img class="img-fluid w-100" :src="'/website/warehouses/grey-space.jpg'" alt="img">
									</div>
									<div class="col-12 p-2" data-aos="zoom-in" data-aos-duration="1000">
										<img class="img-fluid w-100" :src="'/website/warehouses/grey-space.jpg'" alt="img">
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
									<img class="img-fluid w-100" :src="'/website/warehouses/rack.jpg'" alt="img">
								</div>
								<div class="col-md-4 col-12">
									<div class="col-12 p-2" data-aos="zoom-in" data-aos-duration="1000">
										<img class="img-fluid w-100" :src="'/website/warehouses/rack.jpg'" alt="img">
									</div>
									<div class="col-12 p-2" data-aos="zoom-in" data-aos-duration="1000">
										<img class="img-fluid w-100" :src="'/website/warehouses/rack.jpg'" alt="img">
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
									<img class="img-fluid w-100" :src="'/website/warehouses/pallet.jpg'" alt="img">
								</div>
								<div class="col-md-4 col-12">
									<div class="col-12 p-2" data-aos="zoom-in" data-aos-duration="1000">
										<img class="img-fluid w-100" :src="'/website/warehouses/pallet.jpg'" alt="img">
									</div>
									<div class="col-12 p-2" data-aos="zoom-in" data-aos-duration="1000">
										<img class="img-fluid w-100" :src="'/website/warehouses/pallet.jpg'" alt="img">
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
	let singleWarehouseQuotationData = {

	    // first_name : '',
	    // last_name : [],
	    // email : null,
	    // phone : null,
	    // subject : null,
	    // message : null

	};

	export default {
		mounted() {
			AOS.init({once: true});
		},
		
		data () {
			return {
				errors : {},

				formSubmitted : false,

				singleWarehouseQuotationData : singleWarehouseQuotationData,

				warehousePreviews : [
				'/website/warehouses/about-us-banner.jpg', '/website/warehouses/store-img-1.png', '/website/warehouses/store-img-2.png'
				],

				quotationFormFailureMessages : {},
				quotationFormSuccessMessage : null
			}
		},
		
		methods : {

			changePreviewOrder(index) {

				let tempImage = this.warehousePreviews[0];

				// this.warehousePreviews[0] = this.warehousePreviews[index];
				// this.warehousePreviews[index] = tempImage;
				
				this.$set(this.warehousePreviews, 0, this.warehousePreviews[index]);
				this.$set(this.warehousePreviews, index, tempImage);

			},

			submitQuotation() {

				this.validateFormInput('first_name');
				this.validateFormInput('last_name');
				this.validateFormInput('email');
				this.validateFormInput('mobile');
				this.validateFormInput('company_name');
				this.validateFormInput('industry');
				// this.validateFormInput('warehouse');
				this.validateFormInput('container_type');
				// this.validateFormInput('container');
				this.validateFormInput('quantity');
				this.validateFormInput('message');

				if (Object.keys(this.errors).length > 0) {
					this.submitForm = false;
					return;
				}
				
				axios
				.post('/quotations/', this.singleWarehouseQuotationData)
				.then(response => {
					if (response.status == 200) {
				// this.$toastr.s("Your message has been successfully delivered", "Success");
				this.singleWarehouseQuotationData = {};
				this.quotationFormFailureMessages = {};
				this.quotationFormSuccessMessage = "Submitted successfully, We will contact you soon";
			}
		})
				.catch(error => {
					if (error.response.status == 422) {

						this.quotationFormSuccessMessage = null;
						this.quotationFormFailureMessages = error.response.data.errors;

					}
				})
				.finally(response => {
					this.formSubmitted = false;
				});

			},

			validateFormInput (formInputName) {

				this.submitForm = false;

				switch(formInputName) {

					case 'first_name' : 

					if (! this.singleWarehouseQuotationData.first_name && ! this.singleWarehouseQuotationData.last_name) {

							// this.errors.first_name = 'First or last name is required'
							this.$set(this.errors, 'first_name', 'First or last name is required');

						}
						else if (this.singleWarehouseQuotationData.first_name && ! this.singleWarehouseQuotationData.first_name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {

							// this.errors.first_name = 'No special characters';
							this.$set(this.errors, 'first_name', 'No special characters');

						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'first_name');
							this.$delete(this.errors, 'last_name');
						}

						break;

						case 'last_name' : 
						
						if (! this.singleWarehouseQuotationData.first_name && ! this.singleWarehouseQuotationData.last_name) {
							
							// this.errors.last_name = 'First or last name is required'
							this.$set(this.errors, 'last_name', 'First or last name is required');

						}
						else if (this.singleWarehouseQuotationData.last_name && ! this.singleWarehouseQuotationData.last_name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {

							// this.errors.last_name = 'No special characters';
							this.$set(this.errors, 'last_name', 'No special characters');

						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'first_name');
							this.$delete(this.errors, 'last_name');
						}

						break;

						case 'email' : 
						
						if (! this.singleWarehouseQuotationData.email) {
							// this.errors.email = 'Email is required';
							this.$set(this.errors, 'email', 'Email is required');
						}
						else if (! this.singleWarehouseQuotationData.email.match(/[a-z0-9._+-]+@[a-z0-9.-]+\.[a-z]{2,}$/g)) {
							// this.errors.email = 'Invalid email address';
							this.$set(this.errors, 'email', 'Invalid email address');
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'email');
						}

						break;

						case 'mobile' :

						if (! this.singleWarehouseQuotationData.mobile) {
							// this.errors.mobile = 'Contact is required';
							this.$set(this.errors, 'mobile', 'Mobile is required');
						}
						else if (! this.singleWarehouseQuotationData.mobile.match(/\+?(88)?0?1[123456789][0-9]{8}\b/g)) {
							// this.errors.mobile = 'Invalid contact number';
							this.$set(this.errors, 'mobile', 'Invalid contact number');
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'mobile');
						}

						break;

						case 'company_name' :

						if (this.singleWarehouseQuotationData.company_name && ! this.singleWarehouseQuotationData.company_name.match(/^[A-Za-z0-9\s\-_,\.;:()]+$/g)) {
							// this.errors.company = 'No special character';
							this.$set(this.errors, 'company_name', 'No special character');
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'company_name');
						}

						break;

						case 'industry' :

						if (! this.singleWarehouseQuotationData.industry) {
							// this.errors.industry = 'Industry is required';
							this.$set(this.errors, 'industry', 'Industry is required');
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'industry');
						}

						break;

					/*
					case 'warehouse' :

						if (! this.singleWarehouseQuotationData.warehouse) {
							// this.errors.warehouse = 'Warehouse is required';
							this.$set(this.errors, 'warehouse', 'Warehouse is required');
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'warehouse');
						}

						break;
						*/

						case 'container_type' :

						if (! this.singleWarehouseQuotationData.container_type_id) {
							// this.errors.container_type = 'Space type is required';
							this.$set(this.errors, 'container_type', 'Space type is required');
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'container_type');
						}

						break;

					/*
					case 'container' :

						if (! this.singleWarehouseQuotationData.container) {
							this.errors.container = 'Space size is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'container');
						}

						break;
						*/

						case 'quantity' :

						if (! this.singleWarehouseQuotationData.quantity) {
							// this.errors.quantity = 'Space type is required';
							this.$set(this.errors, 'quantity', 'Quantity is required');
						}
						else if (this.singleWarehouseQuotationData.quantity < 1) {
							// this.errors.quantity = 'Invalid quantity';
							this.$set(this.errors, 'quantity', 'Invalid quantity');
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors, 'quantity');
						}

						break;

						case 'message' :

						if (! this.singleWarehouseQuotationData.message) {
							// this.errors.message = 'Message is required';
							this.$set(this.errors, 'message', 'Message is required');
						}
						else if (! this.singleWarehouseQuotationData.message.match(/^[A-Za-z0-9\s\-_,\.;:()]+$/g)) {
							// this.errors.message = 'No special character';
							this.$set(this.errors, 'message', 'No special character');
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'message');
						}

						break;

					}

				}

			}

		}
	</script>

	<style lang="css" scoped>
</style>