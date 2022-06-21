<template>
  <div>
      <div class="hero-area">
        <about-us-banner-list/>
      </div>

    <main>
      <!-- Description Started  -->
      <section class="description" id="description">
        <div class="container">
          <div class="row g-4">
            <div class="col-md-6" data-aos="fade-up" data-aos-duration="1000">
              <h3 class="title align-center">Who We Are</h3>
              <p>
                Gudam solves the hardest omnichannel logistics problems for the world’s largest retailers and brands. Integrating technology, open logistics networks, and elastic economic models allows Gudam customers to move fast, at scale, and with precision. Founded in 2021 and headquartered in Gulshan, Gudam brings deep logistics expertise and enterprise-grade technology to deliver innovative eCommerce fulfillment, retail distribution, same-day delivery, and network capacity programs to the Fortune 500.
              </p>
            </div>
            <div class="col-md-6" data-aos="zoom-in" data-aos-duration="1000">
              <img class="img-fluid" :src="'website/about/about-3.png'" alt="img" />
            </div>
          </div>
        </div>
      </section>
      <!-- Description End  -->

      <!-- Values section Start -->
      <section class="service-list values" data-aos="fade-up" data-aos-duration="1000">
        <div class="container">
          <h3 class="title text-center mb-4">Our Values</h3>
          <div class="row g-4">
            <div class="col-md-4 col-12">
              <div class="service-list-box mx-auto">
                <i class="fas fa-users fa-3x values-icon"></i>
                <h4 class="text-center px-5">Put customers first</h4>
                <p>
                  We focus on our customers so they can win. We make decisions based on their long-term success.
                </p>
              </div>
            </div>
            <div class="col-md-4 col-12">
              <div class="service-list-box mx-auto">
                <i class="fas fa-lightbulb fa-3x values-icon"></i>
                <h4 class="text-center px-5">Seek solutions</h4>
                <p>
                  Logistics is a puzzle and we love that. We start with a deep understanding of the problem, then roll up our sleeves and solve it.
                </p>
              </div>
            </div>
            <div class="col-md-4 col-12">
              <div class="service-list-box mx-auto">
                <i class="fas fa-handshake fa-3x values-icon"></i>
                <h4 class="text-center px-5">Work together</h4>
                <p>
                  We trust each other and have direct conversations. We value diversity, hire great people, build great teams, and know we are better together.
                </p>
              </div>
            </div>
            <div class="col-md-4 col-12">
              <div class="service-list-box mx-auto">
                <i class="fas fa-chart-line fa-3x values-icon"></i>
                <h4 class="text-center px-5">Make it better</h4>
                <p>
                  From the code we write to the actions we take, we leave things better than we found them.
                </p>
              </div>
            </div>
            <div class="col-md-4 col-12">
              <div class="service-list-box mx-auto">
                <i class="fas fa-expand fa-3x values-icon"></i>
                <h4 class="text-center px-5">Think big</h4>
                <p>
                  We’re on a mission. We are building something big and defining the future because we know there’s a better way.
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>
      <!-- Values section End -->

      <!-- History Section Started  -->
      <!-- <section class="history" data-aos="fade-up" data-aos-duration="1000">
        <div class="container">
          <h3 class="title mb-5">Our history</h3>
          <div class="row">
            <div class="accordion" id="accordionExample">
              <div class="accordion-item custom-accordion-item">
                <h2 class="accordion-header" id="headingOne">
                  <button
                    class="accordion-button custom-accordion-btn"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#collapseOne"
                    aria-expanded="true"
                    aria-controls="collapseOne"
                  >
                    <h5><b>2000 - Founded by XXXX</b></h5>
                  </button>
                </h2>
                <div
                  id="collapseOne"
                  class="accordion-collapse collapse show"
                  aria-labelledby="headingOne"
                  data-bs-parent="#accordionExample"
                >
                  <div class="accordion-body">
                    <p>
                      Lorem Ipsum is simply dummy text of the printing and
                      typesetting industry. Lorem Ipsum has been the industry’s
                      standard dummy text ever since the 1500s, when an unknown
                      printer took a galley of type and scrambled it to make a
                      type specimen book. It has survived not only five
                      centuries, but also the leap into electronic typesetting,
                      remaining essentially unchanged
                    </p>
                  </div>
                </div>
              </div>
              <div class="accordion-item custom-accordion-item">
                <h2 class="accordion-header" id="headingTwo">
                  <button
                    class="accordion-button custom-accordion-btn collapsed"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#collapseTwo"
                    aria-expanded="false"
                    aria-controls="collapseTwo"
                  >
                    <h5><b>2000 - New Service in Dhaka</b></h5>
                  </button>
                </h2>
                <div
                  id="collapseTwo"
                  class="accordion-collapse collapse"
                  aria-labelledby="headingTwo"
                  data-bs-parent="#accordionExample"
                >
                  <div class="accordion-body">
                    <p>
                      Lorem Ipsum is simply dummy text of the printing and
                      typesetting industry. Lorem Ipsum has been the industry’s
                      standard dummy text ever since the 1500s, when an unknown
                      printer took a galley of type and scrambled it to make a
                      type specimen book. It has survived not only five
                      centuries, but also the leap into electronic typesetting,
                      remaining essentially unchanged
                    </p>
                  </div>
                </div>
              </div>
              <div class="accordion-item custom-accordion-item">
                <h2 class="accordion-header" id="headingThree">
                  <button
                    class="accordion-button custom-accordion-btn collapsed"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#collapseThree"
                    aria-expanded="false"
                    aria-controls="collapseThree"
                  >
                    <h5><b>2000 - Lorem Ipsum is simply</b></h5>
                  </button>
                </h2>
                <div
                  id="collapseThree"
                  class="accordion-collapse collapse"
                  aria-labelledby="headingThree"
                  data-bs-parent="#accordionExample"
                >
                  <div class="accordion-body">
                    <p>
                      Lorem Ipsum is simply dummy text of the printing and
                      typesetting industry. Lorem Ipsum has been the industry’s
                      standard dummy text ever since the 1500s, when an unknown
                      printer took a galley of type and scrambled it to make a
                      type specimen book. It has survived not only five
                      centuries, but also the leap into electronic typesetting,
                      remaining essentially unchanged
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section> -->
      <!-- History Section End  -->

      <team-member-list/>
      <join-us/>
    </main>
    <footer-component/>
    <copyright-component/>
  </div>
</template>

<script>
  import AboutUsBannerList from '../../components/website/AboutUsBannerList.vue'
  import TeamMemberList from '../../components/website/TeamMemberList.vue'
  import JoinUs from '../../components/website/JoinUsComponent.vue'

export default {
  components: {
      AboutUsBannerList, 
      TeamMemberList, 
      JoinUs
    },
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