<template>
	<div class="hero-area">
    <div
    id="carouselExampleIndicators"
    class="carousel slide"
    data-bs-ride="carousel"
    >
    <!-- 
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
    -->
    <div class="carousel-inner">
      <div class="carousel-item active bg-1" style="background-image: url('../website/index/banner.jpg')">
        <div class="container-fluid" data-aos="fade-right" data-aos-duration="1000">
          <div class="row align-items-center" style="height: 450px">
            <div class="col-lg-7 col-9 slider-content">
              <div class="slider-content-margin">
                <h1 class="text-white">Your Fulfilment Space Delivered</h1>
                <!-- <p class="text-white">Search online - see space fast</p> -->
                <router-link class="hero-btn" aria-current="page" :to="{ name: 'warehouses' }">
                  <span>Get Started</span>
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- 
      <div class="carousel-item bg-2" style="background-image: url('../website/index/banner-2.jpg')">
      <div class="container-fluid">
        <div class="row align-items-center" style="height: 450px">
          <div class="col-lg-7 col-9 slider-content">
            <div class="slider-content-margin">
              <h1 class="text-white">Find Your Perfect Space</h1>
              <p class="text-white">Search online - see space fast</p>
              <router-link class="hero-btn" aria-current="page" :to="{ name: 'warehouses' }">
                <span>Get Started</span>
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div> 

    <div class="carousel-item bg-3" style="background-image: url('../website/index/banner-3.jpg')">
      <div class="container-fluid">
        <div class="row align-items-center" style="height: 450px">
          <div class="col-lg-7 col-9 slider-content">
            <div class="slider-content-margin">
              <h1 class="text-white">Find Your Perfect Space</h1>
              <p class="text-white">Search online - see space fast</p>
              <router-link class="hero-btn" aria-current="page" :to="{ name: 'warehouses' }">
                <span>Get Started</span>
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div> 
    -->
  </div>
    <!-- 
    <button
    class="carousel-control-prev d-none"
    type="button"
    data-bs-target="#carouselExampleIndicators"
    data-bs-slide="prev"
    >
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button
  class="carousel-control-next d-none"
  type="button"
  data-bs-target="#carouselExampleIndicators"
  data-bs-slide="next"
  >
  <span class="carousel-control-next-icon" aria-hidden="true"></span>
  <span class="visually-hidden">Next</span>
  </button> 
-->
</div>
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