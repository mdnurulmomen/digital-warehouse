<template>
  <div>
    <div class="hero-area">
      <service-banners/>
    </div>

    <main>
      <service-list/>
      <query-contact/>
    </main>

    <footer-component/>
    <copyright-component/>
  </div>
</template>

<script>

  import ServiceBanners from '../../components/website/ServiceBanners.vue'
  import ServiceList from '../../components/website/ServiceList.vue'

  export default {
    components: {
      ServiceBanners, 
      ServiceList
    },

    data () {
      return {

      }
    }
  }
</script>

<style lang="css" scoped>
</style>