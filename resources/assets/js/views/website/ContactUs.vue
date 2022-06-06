<template>
  <div>
    <div class="hero-area">
      <contact-us-banner-list/>
    </div>

    <main>
      <!-- Hero Area Started  -->
        <!-- 
        <section
          class="hero-area"
          style="background-image: url(images/contact.jpg)"
        >
          <div class="container">
            <div class="row g-0">
              <div class="col-12">
                <div class="text-content">
                  <h2>Contact Us</h2>
                  <p>
                    Send us a message if you have any questions or comments,<br>we'll
                    get back to you shortly.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </section> 
      -->
      <!-- Hero Area Started  -->
      <contact-us-form/>
    </main>

    <footer-component/>
    <copyright-component/>
  </div>
</template>

<script>
  import ContactUsBannerList from '../../components/website/ContactUsBannerList.vue'
  import ContactUsForm from '../../components/website/ContactUsForm.vue'

  export default {
    components: {

      ContactUsBannerList, 
      ContactUsForm

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