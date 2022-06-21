<template>
  <div>
    <div class="hero-area">
      <registration-banner-list/>
    </div>

    <main>
      <registration-form
      :csrf="csrf" 
      :form-submitted="formSubmitted" 
      :single-owner-registration-data="singleOwnerRegistrationData" 
      :single-merchant-registration-data="singleMerchantRegistrationData" 
      @submitOwnerRegistrationForm="submitOwnerRegistrationForm($event)" 
      @submitMerchantRegistrationForm="submitMerchantRegistrationForm($event)" 
      @resetParentProps="resetParentProps()" 
      :registration-form-success-message="registrationFormSuccessMessage" 
      :registration-form-failure-message="registrationFormFailureMessage" 
      />
    </main>

    <footer-component/>
    <copyright-component/>
  </div>
</template>

<script>
  import RegistrationBannerList from '../../components/website/RegistrationBannerList.vue'
  import RegistrationForm from '../../components/website/RegistrationForm.vue'

  let singleOwnerRegistrationData = {
    
    // first_name : '',
    // last_name : [],
    // email : null,
    // phone : null,
    // subject : null,
    // message : null
     
  };

  let singleMerchantRegistrationData = {
    
    // first_name : '',
    // last_name : [],
    // email : null,
    // phone : null,
    // subject : null,
    // message : null
     
  };

  export default {
    components: {
      RegistrationBannerList, 
      RegistrationForm
    },

    data () {
      return {
        formSubmitted : false,
        registrationFormFailureMessage : {}, 
        registrationFormSuccessMessage : null, 
        singleOwnerRegistrationData : singleOwnerRegistrationData,
        singleMerchantRegistrationData : singleMerchantRegistrationData,
        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      }
    },

    created() {

    },

    methods : {

      submitOwnerRegistrationForm(singleOwnerRegistrationData) {

        this.formSubmitted = true;  

        axios
          .post('/owners/', singleOwnerRegistrationData)
          .then(response => {
            if (response.status == 200) {
              // this.$toastr.s("Your message has been successfully delivered", "Success");
              this.singleOwnerRegistrationData = {};
              this.registrationFormFailureMessage = {};
              this.registrationFormSuccessMessage = "Submitted successfully, We will contact you soon";
            }
          })
          .catch(error => {
            if (error.response.status == 422) {
              
              this.registrationFormSuccessMessage = null;
              this.registrationFormFailureMessage = error.response.data.errors;

            }
          })
          .finally(response => {
            this.formSubmitted = false;
          });

      },

      submitMerchantRegistrationForm(singleMerchantRegistrationData) {
        
        this.formSubmitted = true;  

        axios
          .post('/merchants/', singleMerchantRegistrationData)
          .then(response => {
            if (response.status == 200) {
              // this.$toastr.s("Your message has been successfully delivered", "Success");
              this.singleMerchantRegistrationData = {};
              this.registrationFormFailureMessage = {};
              this.registrationFormSuccessMessage = "Submitted successfully, We will contact you soon";
            }
          })
          .catch(error => {
            if (error.response.status == 422) {
              
              this.registrationFormSuccessMessage = null;
              this.registrationFormFailureMessage = error.response.data.errors;

            }
          })
          .finally(response => {
            this.formSubmitted = false;
          });

      },

      resetParentProps() {

        this.singleOwnerRegistrationData = {};
        this.singleMerchantRegistrationData = {};

        this.registrationFormFailureMessage = {}; 
        this.registrationFormSuccessMessage = null; 

      }

    }
  }
</script>

<style lang="css" scoped>
</style>