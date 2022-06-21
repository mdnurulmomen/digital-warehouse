<template>
	<!-- Carousel Started  -->
  <div class="hero-area">
    <div
    id="carouselExampleIndicators"
    class="carousel slide"
    data-bs-ride="carousel"
    >
    <div class="carousel-indicators">
      <button
      type="button"
      data-bs-target="#carouselExampleIndicators"
      data-bs-slide-to="0"
      class="active dot"
      aria-current="true"
      aria-label="Slide 1"
      ></button>
      <button
      type="button"
      class="dot"
      data-bs-target="#carouselExampleIndicators"
      data-bs-slide-to="1"
      aria-label="Slide 2"
      ></button>
      <button
      type="button"
      class="dot"
      data-bs-target="#carouselExampleIndicators"
      data-bs-slide-to="2"
      aria-label="Slide 3"
      ></button>
    </div>
    <div class="carousel-inner">
      <div class="carousel-item active bg-1" style="background-image: url('../website/services/banner.jpg')">
        <div class="container-fluid" data-aos="fade-right" data-aos-duration="1000">
          <div class="row align-items-center" style="height: 450px">
            <div class="col-lg-7 col-9 slider-content">
              <h1 class="text-white">Warehouse Fulfillment</h1>
              <p class="text-white">Search online - see space fast</p>
              <a class="hero-btn" href="#service-list">Get Started</a>
            </div>
          </div>
        </div>
      </div>
      <div class="carousel-item bg-2" style="background-image: url('../website/services/banner-2.jpg')">
      <div class="container-fluid">
        <div class="row align-items-center" style="height: 450px">
          <div class="col-lg-7 col-9 slider-content">
            <h1 class="text-white">Logistic Services</h1>
            <p class="text-white">Search online - see space fast</p>
            <a class="hero-btn" href="#service-list">Get Started</a>
          </div>
        </div>
      </div>
    </div>
            <!-- <div class="carousel-item bg-1">
              <div class="container-fluid">
                <div class="row align-items-center" style="height: 450px">
                  <div class="col-lg-7 col-9 slider-content">
                    <h1 class="text-white">Ecommerce Fulfillment</h1>
                    <p class="text-white">Search online - see space fast</p>
                    <a class="hero-btn" href="#">Get Started</a>
                  </div>
                </div>
              </div>
            </div> -->
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