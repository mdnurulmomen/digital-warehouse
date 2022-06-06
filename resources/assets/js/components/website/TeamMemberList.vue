<template>
	<!-- Team Area Started -->
      <div class="team" data-aos="fade-up" data-aos-duration="1000">
        <div class="container">
          <div class="row">
            <h3 class="title text-center mb-5 pb-5">Our Team</h3>
              <div class="main">
                <div class="team-profile-card mb-5">
                  <div class="img">
                    <img :src="'images/team/mohammed-abdus-salam.jpg'" alt="profile-img">
                  </div>
                  <div class="team-caption">
                    <h3>Colonel Mohammad Abdus Salam BP (Retd)</h3>
                    <p>Chairman</p>
                    <div class="team-social-links">
                      <a href="#"><i class="fab fa-facebook"></i></a>
                      <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                  </div>
                </div>
                <div class="team-profile-card mb-5">
                  <div class="img">
                    <img :src="'images/team/samit-bin-salam.jpg'" alt="profile-img">
                  </div>
                  <div class="team-caption">
                    <h3>Samit Bin Salam</h3>
                    <p>Founder, CEO</p>
                    <div class="team-social-links">
                      <a href="#"><i class="fab fa-facebook"></i></a>
                      <a href="#"><i class="fab fa-instagram"></i></a>
                      <a href="#"><i class="fab fa-twitter"></i></a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
        </div>
      </div>
      <!-- Team Area End  -->
</template>

<script>
export default {
  mounted(){
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