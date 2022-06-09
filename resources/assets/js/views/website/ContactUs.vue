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
      <contact-us-form
        :csrf="csrf" 
        :form-submitted="formSubmitted" 
        :single-contact-query-data="singleContactQueryData"  
        @submitContactQuery="submitContactQuery($event)" 
        :contact-query-form-success-message="contactQueryFormSuccessMessage" 
        :contact-query-form-failure-messages="contactQueryFormFailureMessages" 
      />
    </main>

    <footer-component/>
    <copyright-component/>
  </div>
</template>

<script>
  import ContactUsBannerList from '../../components/website/ContactUsBannerList.vue'
  import ContactUsForm from '../../components/website/ContactUsForm.vue'

  let singleContactQueryData = {
    
    // first_name : '',
    // last_name : [],
    // email : null,
    // phone : null,
    // subject : null,
    // message : null
     
  };

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
        formSubmitted : false,
        contactQueryFormFailureMessages : [], 
        contactQueryFormSuccessMessage : null, 
        singleContactQueryData : singleContactQueryData,
        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      }
    },

    created() {

    },

    methods : {

      submitContactQuery(singleContactQueryData) {
        
        this.formSubmitted = true;  

        axios
          .post('/contact-queries/', singleContactQueryData)
          .then(response => {
            if (response.status == 200) {
              // this.$toastr.s("Your message has been successfully delivered", "Success");
              this.contactQueryFormFailureMessages = [];
              this.contactQueryFormSuccessMessage = "Your message has been successfully delivered";
            }
          })
          .catch(error => {
            if (error.response.status == 422) {
              /*
              for (var x in error.response.data.errors) {
                this.$toastr.w(error.response.data.errors[x], "Warning");
              }
              */
              
              this.contactQueryFormFailureMessages = [];
              this.contactQueryFormSuccessMessage = null;

              for (var x in error.response.data.errors) {
                this.contactQueryFormFailureMessages.push(error.response.data.errors[x]);
              }
            }
          })
          .finally(response => {
            this.singleContactQueryData = {};
            this.formSubmitted = false;
          });

      },

    }

  }
</script>

<style lang="css" scoped>
</style>