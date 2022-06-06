<template>
	<!-- Call To Action  start-->
      <section class="about-us-cta text-center" data-aos="fade-up" data-aos-duration="1000">
        <div class="container">
          <div class="row">
            <h3 class="title text-center mb-3">Join Us</h3>
            <p class="w-100 mb-4">Click the button below to see job opening.</p>
          </div>

          <router-link class="more-btn" :to="{ name: 'careers' }">
            See Available Jobs
          </router-link>
        </div>
      </section>
      <!-- Call To Action  End-->
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