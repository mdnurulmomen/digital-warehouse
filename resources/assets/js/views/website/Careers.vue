<template>
  <div>
    <div class="hero-area">
      <career-banner-list/>
    </div>
    
  	<main>
      <job-list/>
    </main>

    <footer-component/>
    <copyright-component/>
  </div>
</template>

<script>
  import CareerBannerList from '../../components/website/CareerBannerList.vue'
  import JobList from '../../components/website/JobList.vue'

  export default {
    components: {
      CareerBannerList, 
      JobList
    },
    
    data () {
      return {

      }
    }
  }
</script>

<style lang="css" scoped>
</style>