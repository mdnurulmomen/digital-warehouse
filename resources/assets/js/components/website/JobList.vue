<template>
	<!-- Job Post Started  -->
      <section class="job" id="job">
        <div class="container">
          <div class="row g-4" data-aos="fade-up" data-aos-duration="1000">
            <div class="col-12">
              <div class="heading-box">
                <h3 class="title">Job Openings</h3>
                <p>2 jobs</p>
              </div>
            </div>
            <router-link style="color:#000000" :to="{ name: 'job-details', params: { id: 1 }}">
              <div class="row list-jobs">
                <div class="col-12 col-md-8">
                  <h6><b>Warehouse Manager</b></h6>
                  <p class="mb-0"><small>Gulshan - 1, Dhaka | Job ID: 1910896</small></p>
                </div>
                <div class="col-12 col-md-4 job-post-date">
                  <p class="mb-0"><small>Posted: January 24, 2022</small></p>
                  <p class="mb-0"><small>(Updated 2 months ago)</small></p>
                </div>
                <div class="col-12">
                  <p><b>PREFERRED QUALIFICATIONS</b></p>
                  <ul>
                    <li>Experience in inventory management tools</li>
                    <li>Familiar with waybills and shipping procedures with transport companies</li>
                    <li>Proficient with computers and Microsoft Office (Outlook and Excel)</li>
                  </ul>
                  <p class="mb-0" style="color:#23b8bf">...Read more</p>
                </div>
              </div>
            </router-link>

            <router-link style="color:#000000" :to="{ name: 'job-details', params: { id: 2 } }">
              <div class="row list-jobs">
                <div class="col-12 col-md-8">
                  <h6><b>Warehouse Picker / Packer</b></h6>
                  <p class="mb-0"><small>Gulshan - 1, Dhaka | Job ID: 1910896</small></p>
                </div>
                <div class="col-12 col-md-4 job-post-date">
                  <p class="mb-0"><small>Posted: January 24, 2022</small></p>
                  <p class="mb-0"><small>(Updated 2 months ago)</small></p>
                </div>
                <div class="col-12">
                  <p><b>Requirements Qualifications:</b></p>
                  <ul>
                    <li>Prior warehouse experience a plus (not necessary).</li>
                    <li>Must have a demonstrated ability to work in a fast-paced environment.</li>
                    <li>Must achieve performance objectives.</li>
                  </ul>
                  <p class="mb-0" style="color:#23b8bf">...Read more</p>
                </div>
              </div>
            </router-link>
          </div>
        </div>
      </section>
      <!-- Job Post End  -->
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