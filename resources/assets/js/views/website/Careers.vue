<template>
  <div>
    <career-banner-list/>
    
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