<template>
  <div>
  	<!-- service-list section Start -->
    <section class="service-list" id="service-list" data-aos="fade-up" data-aos-duration="1000">
      <div class="container">
        <div class="row g-4">
          <ul class="nav nav-pills p-0" id="pills-tab" role="tablist">
            <li class="nav-item col-md-4">
              <a class="tab-btn active"
              id="pills-profile-tab"
              data-bs-toggle="pill"
              data-bs-target="#pills-profile"
              type="button"
              role="tab"
              aria-controls="pills-profile"
              aria-selected="false">
              <div>
                <div class="service-list-box services-box mx-auto">
                  <i class="fas fa-warehouse fa-3x service-icon"></i>
                  <h4>Warehouse Fulfillment</h4>
                  <p>
                    Brands need multiple, distributed fulfillment locations in order to enable the one- and two-day shipping costumers expect, affordably.
                  </p>
                </div>
              </div>
            </a>
          </li>
          <li class="nav-item col-md-4">
            <a class="tab-btn"
            id="pills-part-tab"
            data-bs-toggle="pill"
            data-bs-target="#pills-part"
            type="button"
            role="tab"
            aria-controls="pills-part"
            aria-selected="false">
            <div>
              <div class="service-list-box services-box mx-auto">
                <i class="fas fa-boxes fa-3x service-icon"></i>
                <h4>Logistic Services</h4>
                <p>
                  Brands need multiple, distributed fulfillment locations in order to enable the one- and two-day shipping costumers expect, affordably.
                </p>
              </div>
            </div>
          </a>
        </li>
        <li class="nav-item col-md-4">
                  <!-- <a class="tab-btn"
                      id="pills-home-tab"
                      data-bs-toggle="pill"
                      data-bs-target="#pills-home"
                      type="button"
                      role="tab"
                      aria-controls="pills-home"
                      aria-selected="true"> -->
                      <div>
                        <div class="service-list-box-disabled services-box mx-auto">
                          <i class="fas fa-cart-plus fa-3x service-icon"></i>
                          <h4>Ecommerce Fulfillment</h4>
                          <p>
                            We take your stock, store it, pick, pack and deliver it for you automatically. In just a few simple steps, you’ll be selling, growing and focusing on the things you enjoy most.<br>
                            <span style="color: #23b8bf"><b>Coming Soon</b></span>
                          </p>
                        </div>
                      </div>
                      <!-- </a> -->
                    </li>
                  </ul>
                </div>
              </div>
            </section>
            <!-- service-list section End -->

            <!-- Description Started  -->
            <section class="description">
              <div class="container">
                <div class="tab-content" id="pills-tabContent">
                  <div class="tab-pane fade show active"
                  id="pills-profile"
                  role="tabpanel"
                  aria-labelledby="pills-profile-tab">
                  <div class="row">
                <!-- 
                <h2 class="title text-center pb-4">Warehousing and Fulfillment Center Network</h2>
                <div class="spec-features">
                  <div class="col-md-3 spec-features-width" data-aos="fade-up" data-aos-duration="1000">
                    <i class="fas fa-handshake fa-2x features-icon"></i>
                    <h5>Nationwide network capacity</h5>
                    <p>The Gudam Network spans the entire country, with hundreds of fulfillment centers running Gudam software in every city of Bangladesh</p>
                  </div>
                  <div class="col-md-3 spec-features-width" data-aos="fade-up" data-aos-duration="1000">
                    <i class="fas fa-handshake fa-2x features-icon"></i>
                    <h5>Nationwide network capacity</h5>
                    <p>The Gudam Network spans the entire country, with hundreds of fulfillment centers running Gudam software in every city of Bangladesh</p>
                  </div>
                  <div class="col-md-3 spec-features-width" data-aos="fade-up" data-aos-duration="1000">
                    <i class="fas fa-handshake fa-2x features-icon"></i>
                    <h5>Nationwide network capacity</h5>
                    <p>The Gudam Network spans the entire country, with hundreds of fulfillment centers running Gudam software in every city of Bangladesh</p>
                  </div>
                  <div class="col-md-3 spec-features-width" data-aos="fade-up" data-aos-duration="1000">
                    <i class="fas fa-handshake fa-2x features-icon"></i>
                    <h5>Nationwide network capacity</h5>
                    <p>The Gudam Network spans the entire country, with hundreds of fulfillment centers running Gudam software in every city of Bangladesh</p>
                  </div>
                </div> 
              -->
              <h4 class="text-center mb-5">How it Works</h4>
              <div class="spec-tabs">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                  <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="connect-your-brand-tab" data-bs-toggle="tab" data-bs-target="#connect-your-brand" type="button" role="tab" aria-controls="connect-your-brand" aria-selected="true"><h6>Move Fast</h6></button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" id="optimize-inventory-tab" data-bs-toggle="tab" data-bs-target="#optimize-inventory" type="button" role="tab" aria-controls="optimize-inventory" aria-selected="false"><h6>Set up Control Tower</h6></button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" id="omnichannel-operations-tab" data-bs-toggle="tab" data-bs-target="#omnichannel-operations" type="button" role="tab" aria-controls="omnichannel-operations" aria-selected="false"><h6>Improve Operations</h6></button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" id="fast-affordable-fulfillment-tab" data-bs-toggle="tab" data-bs-target="#fast-affordable-fulfillment" type="button" role="tab" aria-controls="fast-affordable-fulfillment" aria-selected="false"><h6>Fast, Affordable Fulfillment</h6></button>
                  </li>
                </ul>
                <div class="tab-content" id="nav-tabContent">
                  <div class="tab-pane mt-5 fade show active" id="connect-your-brand" role="tabpanel" aria-labelledby="connect-your-brand-tab">
                    <div class="spec-tabs-content">
                      <div class="col-md-5">
                        <img class="img-fluid" src="images/warehouse-fulfillment/move-fast.jpg" alt="Move Fast" />
                      </div>
                      <div class="col-md-7 content-box">
                        <h4>Move Fast</h4>
                        <p>Find flexible warehousing services where you need them, when you need them and for as long as you want them.</p>
                      </div>
                    </div>
                  </div>
                  <div class="tab-pane mt-5 fade" id="optimize-inventory" role="tabpanel" aria-labelledby="optimize-inventory-tab">
                    <div class="spec-tabs-content">
                      <div class="col-md-5">
                        <img class="img-fluid" src="images/warehouse-fulfillment/set-up-control-tower.jpg" alt="Set up Control Tower" />
                      </div>
                      <div class="col-md-7 content-box">
                        <h4>Set up Control Tower</h4>
                        <p>Take full control of your stock, replenishment and fulfilment; scale up or down use of warehousing in line with the business.</p>
                      </div>
                    </div>
                  </div>
                  <div class="tab-pane mt-5 fade" id="omnichannel-operations" role="tabpanel" aria-labelledby="omnichannel-operations-tab">
                    <div class="spec-tabs-content">
                      <div class="col-md-5">
                        <img class="img-fluid" src="images/warehouse-fulfillment/improve-operations.jpg" alt="Improve Operations" />
                      </div>
                      <div class="col-md-7 content-box">
                        <h4>Improve Operations</h4>
                        <p>Track and optimise performance of your logistics operations; effectively manage risks and business volatility.</p>
                      </div>
                    </div>
                  </div>
                  <div class="tab-pane mt-5 fade" id="fast-affordable-fulfillment" role="tabpanel" aria-labelledby="fast-affordable-fulfillment-tab">
                    <div class="spec-tabs-content">
                      <div class="col-md-5">
                        <img class="img-fluid" src="images/warehouse-fulfillment/fast-affordable-fulfillment.jpg" alt="Fast Affordable Fulfillment" />
                      </div>
                      <div class="col-md-7 content-box">
                        <h4>Satisfy customer demand for speedy shipping</h4>
                        <p>Gudam picks, packs, and ships your order to your exact specifications, delivering a seamless experience for your customers.</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="tab-pane fade"
          id="pills-part"
          role="tabpanel"
          aria-labelledby="pills-part-tab">
          <div class="row">
                <!-- 
                <h2 class="title text-center pb-4">Get A Extra Hand To Manage Your Logistic Services</h2>
                <div class="spec-features">
                  <div class="col-md-3 spec-features-width" data-aos="fade-up" data-aos-duration="1000">
                    <i class="fas fa-handshake fa-2x features-icon"></i>
                    <h5>Nationwide network capacity</h5>
                    <p>The Gudam Network spans the entire country, with hundreds of fulfillment centers running Gudam software in every city of Bangladesh</p>
                  </div>
                  <div class="col-md-3 spec-features-width" data-aos="fade-up" data-aos-duration="1000">
                    <i class="fas fa-handshake fa-2x features-icon"></i>
                    <h5>Nationwide network capacity</h5>
                    <p>The Gudam Network spans the entire country, with hundreds of fulfillment centers running Gudam software in every city of Bangladesh</p>
                  </div>
                  <div class="col-md-3 spec-features-width" data-aos="fade-up" data-aos-duration="1000">
                    <i class="fas fa-handshake fa-2x features-icon"></i>
                    <h5>Nationwide network capacity</h5>
                    <p>The Gudam Network spans the entire country, with hundreds of fulfillment centers running Gudam software in every city of Bangladesh</p>
                  </div>
                  <div class="col-md-3 spec-features-width" data-aos="fade-up" data-aos-duration="1000">
                    <i class="fas fa-handshake fa-2x features-icon"></i>
                    <h5>Nationwide network capacity</h5>
                    <p>The Gudam Network spans the entire country, with hundreds of fulfillment centers running Gudam software in every city of Bangladesh</p>
                  </div>
                </div> 
              -->
              <h4 class="text-center mb-5">How it Works</h4>
              <div class="spec-tabs">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                  <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="order-fulfillment-tab" data-bs-toggle="tab" data-bs-target="#order-fulfillment" type="button" role="tab" aria-controls="order-fulfillment" aria-selected="true"><h6>Order Fulfillment</h6></button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" id="transportation-services-tab" data-bs-toggle="tab" data-bs-target="#transportation-services" type="button" role="tab" aria-controls="transportation-services" aria-selected="false"><h6>Transportation Services</h6></button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" id="agent-service-tab" data-bs-toggle="tab" data-bs-target="#agent-service" type="button" role="tab" aria-controls="agent-service" aria-selected="false"><h6>Agent Service</h6></button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" id="shipping-tab" data-bs-toggle="tab" data-bs-target="#shipping" type="button" role="tab" aria-controls="shipping" aria-selected="false"><h6>Shipping</h6></button>
                  </li>
                </ul>
                <div class="tab-content" id="nav-tabContent">
                  <div class="tab-pane mt-5 fade show active" id="order-fulfillment" role="tabpanel" aria-labelledby="order-fulfillment-tab">
                    <div class="spec-tabs-content">
                      <div class="col-md-5">
                        <img class="img-fluid" src="images/warehouse-fulfillment/order-fulfillment.jpg" alt="Order Fulfillment" />
                      </div>
                      <div class="col-md-7 content-box">
                        <h4>Order Fulfillment</h4>
                        <p>If you’ve ever made a purchase online, then you know how nice it is to receive a package at your door shortly after you order it. But most people don’t think about the process behind order fulfillment services that make fast shipping possible. A lot goes on behind the scenes. The ability to ship an order quickly, accurately and affordably is a well-designed, streamlined process with several steps, each just as important as the next. Just one misstep can result in an unhappy customer and loss of future sales. This entire process is known as order fulfillment. Gudam is going to handle all those responsiblities for you.</p>
                      </div>
                    </div>
                  </div>
                  <div class="tab-pane mt-5 fade" id="transportation-services" role="tabpanel" aria-labelledby="transportation-services-tab">
                    <div class="spec-tabs-content">
                      <div class="col-md-5">
                        <img class="img-fluid" src="images/warehouse-fulfillment/transportation-services.jpg" alt="Transportation Services" />
                      </div>
                      <div class="col-md-7 content-box">
                        <h4>Transportation Services</h4>
                        <p>Gudam takes the carriage of Goods from one place to another on the instructions of the Customer, the loading and unloading of the Goods, the dispatch of transportation vehicles and the processing of a Customer order as it relates to Delivery.</p>
                      </div>
                    </div>
                  </div>
                  <div class="tab-pane mt-5 fade" id="agent-service" role="tabpanel" aria-labelledby="agent-service-tab">
                    <div class="spec-tabs-content">
                      <div class="col-md-5">
                        <img class="img-fluid" src="images/warehouse-fulfillment/agent-service.jpg" alt="Agent Service" />
                      </div>
                      <div class="col-md-7 content-box">
                        <h4>Agent Service</h4>
                        <p>Our experienced teams will coordinate with your partners and internal stakeholders to ensure all related filings, documentation and physical movements are effortlessly coordinated to ensure everything ships as planned with the carrier specified, and all exceptions effectively resolved.</p>
                      </div>
                    </div>
                  </div>
                  <div class="tab-pane mt-5 fade" id="shipping" role="tabpanel" aria-labelledby="shipping-tab">
                    <div class="spec-tabs-content">
                      <div class="col-md-5">
                        <img class="img-fluid" src="images/warehouse-fulfillment/shipping.jpg" alt="Shipping" />
                      </div>
                      <div class="col-md-7 content-box">
                        <h4>Shipping</h4>
                        <p>Get one step ahead of your competition and reach your customers with the strength of the Gudam. Gudam has one goal: making your parcel delivery services work to your logistical and competitive advantage.</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="tab-pane fade"
          id="pills-home"
          role="tabpanel"
          aria-labelledby="pills-home-tab">
          <div class="row">
            <h2 class="title text-center pb-4">Boost your eCommerce Brand</h2>
            <div class="spec-features">
              <div class="col-md-3 spec-features-width" data-aos="fade-up" data-aos-duration="1000">
                <i class="fas fa-handshake fa-2x features-icon"></i>
                <h5>Nationwide network capacity</h5>
                <p>The Gudam Network spans the entire country, with hundreds of fulfillment centers running Gudam software in every city of Bangladesh</p>
              </div>
              <div class="col-md-3 spec-features-width" data-aos="fade-up" data-aos-duration="1000">
                <i class="fas fa-handshake fa-2x features-icon"></i>
                <h5>Nationwide network capacity</h5>
                <p>The Gudam Network spans the entire country, with hundreds of fulfillment centers running Gudam software in every city of Bangladesh</p>
              </div>
              <div class="col-md-3 spec-features-width" data-aos="fade-up" data-aos-duration="1000">
                <i class="fas fa-handshake fa-2x features-icon"></i>
                <h5>Nationwide network capacity</h5>
                <p>The Gudam Network spans the entire country, with hundreds of fulfillment centers running Gudam software in every city of Bangladesh</p>
              </div>
              <div class="col-md-3 spec-features-width" data-aos="fade-up" data-aos-duration="1000">
                <i class="fas fa-handshake fa-2x features-icon"></i>
                <h5>Nationwide network capacity</h5>
                <p>The Gudam Network spans the entire country, with hundreds of fulfillment centers running Gudam software in every city of Bangladesh</p>
              </div>
            </div>
            <h4 class="text-center mb-5">How it Works</h4>
            <div class="spec-tabs">
              <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                  <button class="nav-link active" id="connect-your-brand-tab2" data-bs-toggle="tab" data-bs-target="#connect-your-brand2" type="button" role="tab" aria-controls="connect-your-brand2" aria-selected="true"><h6>Connect Your Brand</h6></button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link" id="optimize-inventory-tab2" data-bs-toggle="tab" data-bs-target="#optimize-inventory2" type="button" role="tab" aria-controls="optimize-inventory2" aria-selected="false"><h6>Optimize Inventory</h6></button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link" id="omnichannel-operations-tab2" data-bs-toggle="tab" data-bs-target="#omnichannel-operations2" type="button" role="tab" aria-controls="omnichannel-operations2" aria-selected="false"><h6>Omnichannel Operations</h6></button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link" id="fast-affordable-fulfillment-tab2" data-bs-toggle="tab" data-bs-target="#fast-affordable-fulfillment2" type="button" role="tab" aria-controls="fast-affordable-fulfillment2" aria-selected="false"><h6>Fast, Affordable Fulfillment</h6></button>
                </li>
              </ul>
              <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane mt-5 fade show active" id="connect-your-brand2" role="tabpanel" aria-labelledby="connect-your-brand-tab2">
                  <div class="spec-tabs-content">
                    <div class="col-md-5">
                      <img class="img-fluid" src="images/connect-your-brand.jpg" alt="img" />
                    </div>
                    <div class="col-md-7 content-box">
                      <h4>Connect your brand with Flowspace</h4>
                      <p>Connect your storefronts, shopping carts, ecommerce marketplaces and more to the Flowspace platform. Our integrations enable seamless, one-click set-up.</p>
                    </div>
                  </div>
                </div>
                <div class="tab-pane mt-5 fade" id="optimize-inventory2" role="tabpanel" aria-labelledby="optimize-inventory-tab2">
                  <div class="spec-tabs-content">
                    <div class="col-md-5">
                      <img class="img-fluid" src="images/connect-your-brand.jpg" alt="img" />
                    </div>
                    <div class="col-md-7 content-box">
                      <h4>Optimize Inventory</h4>
                      <p>Connect your storefronts, shopping carts, ecommerce marketplaces and more to the Flowspace platform. Our integrations enable seamless, one-click set-up.</p>
                    </div>
                  </div>
                </div>
                <div class="tab-pane mt-5 fade" id="omnichannel-operations2" role="tabpanel" aria-labelledby="omnichannel-operations-tab2">
                  <div class="spec-tabs-content">
                    <div class="col-md-5">
                      <img class="img-fluid" src="images/connect-your-brand.jpg" alt="img" />
                    </div>
                    <div class="col-md-7 content-box">
                      <h4>Omnichannel Operations</h4>
                      <p>Connect your storefronts, shopping carts, ecommerce marketplaces and more to the Flowspace platform. Our integrations enable seamless, one-click set-up.</p>
                    </div>
                  </div>
                </div>
                <div class="tab-pane mt-5 fade" id="fast-affordable-fulfillment2" role="tabpanel" aria-labelledby="fast-affordable-fulfillment-tab2">
                  <div class="spec-tabs-content">
                    <div class="col-md-5">
                      <img class="img-fluid" src="images/connect-your-brand.jpg" alt="img" />
                    </div>
                    <div class="col-md-7 content-box">
                      <h4>Fast, Affordable Fulfillment</h4>
                      <p>Connect your storefronts, shopping carts, ecommerce marketplaces and more to the Flowspace platform. Our integrations enable seamless, one-click set-up.</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- Description Started  -->
</div>
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