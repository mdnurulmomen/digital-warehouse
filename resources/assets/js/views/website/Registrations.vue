<template>
  <div>
    <div class="hero-area">
      <registration-banner-list/>
    </div>

    <main>
      <registration-form/>
    </main>

    <footer-component/>
    <copyright-component/>
  </div>
</template>

<script>
  import RegistrationBannerList from '../../components/website/RegistrationBannerList.vue'
  import RegistrationForm from '../../components/website/RegistrationForm.vue'

  export default {
    components: {
      RegistrationBannerList, 
      RegistrationForm
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