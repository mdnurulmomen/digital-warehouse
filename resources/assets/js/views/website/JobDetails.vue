<template>
    <div>
        <div class="hero-area">
    	   <job-detail-banner-list/> 
        </div>

    	<main>
    		<!-- Hero Area Started  -->
          <!-- <section
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
        </section> -->
        <!-- Hero Area Started  -->
        <job-apply-form/>
        <!-- Job Description Started  -->
        <section class="contact" id="send-us-a-message">
        	<div class="container">
        		<div class="row g-4">
        			<div class="col-lg-9 col-12" data-aos="fade-up" data-aos-duration="1000">
        				<h2>DESCRIPTION</h2>
        				<p class="pb-3">Job summary</p>
        				<p>Gudam is growing and we are looking for dependable candidates to join the Logistics team. The ideal candidate possesses a strong work ethic, attention to details, aptitude to tidiness, and can have fun working after all.</p>
        				<p>Key job responsibilities</p>
        				<p>Warehouse Specialists receive parts, maintain inventory, transfer and return parts as needed for Technicians in multiple data centers. This role manages the storage, and ensures a safe and neat workplace . Duties include but not limited to:</p>
        				<ul class="pb-3">
        					<li>Receiving, stowing and picking, packaging and shipping, sorting and counting of parts</li>
        					<li>Working on part request from Technicians and reviewing inventory availability</li>
        					<li>Coordination with procurement and vendor to schedule part delivery</li>
        					<li>Storage facility management and administration</li>
        					<li>Complying with inventory process in part handling activities</li>
        					<li>Participation in continuous improvement initiatives in addition to day-to-day inventory requests</li>
        				</ul>
        				<p>A day in the life</p>
        				<ul class="pb-3">
        					<li>Working hours from 9:00-18:00 on Monday to Friday business days</li>
        				</ul>
        				<p>About the team</p>
        				<p class="pb-3">Logistics is committed to ensure the inventory is accurate and optimal in Data Centers for different teams of Technicians. We work closely with Technicians, and coordinates with Procurement and vendors throughout the lifecycle of inventory.</p>
        				<h2>BASIC QUALIFICATIONS</h2>
        				<ul class="pb-3">
        					<li>Capable of manual handling of computer hardware</li>
        					<li>Able to communicate with stakeholders and to understand procedures in English</li>
        					<li>Conversational level of Cantonese</li>
        					<li>Good attention to details and accuracy</li>
        					<li>2+ years of hands-on experience in logistics and warehouse operations (less experienced candidate will be considered for entry-level position)</li>
        				</ul>
        				<h2>PREFERRED QUALIFICATIONS</h2>
        				<ul>
        					<li>Experience in inventory management tools</li>
        					<li>Familiar with waybills and shipping procedures with transport companies</li>
        					<li>Proficient with computers and Microsoft Office (Outlook and Excel)</li>
        					<li>Experience in Remedy or similar ticketing operations</li>
        					<li>Knowledge in computer hardware logistics</li>
        				</ul>
        			</div>
        			<div class="col-lg-3 col-12">
        				<div class="current-job-details">
        					<h2>Job details</h2>
        					<hr>
        					<div class="job-location d-flex align-items-center">
        						<i class="fas fa-map-marker-alt p-2"></i>
        						<p class="mb-0 p-2">Gulshan - 1, Dhaka</p>
        					</div>
        					<hr>
        					<div class="job-category d-flex align-items-center">
        						<i class="fa-solid fa-suitcase p-2"></i>
        						<a class="mb-0 p-2" href="#">Operations, IT, & Support Engineering</a>
        					</div>
        					<hr>
        				</div>
        				<div class="related-job-details">
        					<h2>Related jobs</h2>
        					<hr>
        					<a href="job-page-2.html">Warehouse Picker / Packer</a>
        					<p class="mb-0"><small><b>Location:</b> Gulshan - 1, Dhaka</small></p>
        					<p class="mb-0"><small><b>Posted:</b> January 24, 2022</small></p>
        					<p class="mb-0"><small>(Updated 2 months ago)</small></p>
        					<hr>
        				</div>
        			</div>
        		</div>
        	</div>
        </section>
        <!-- Job Description End  -->
    </main>

    <footer-component/>
    <copyright-component/>
</div>
</template>

<script>
    import JobDetailBannerList from '../../components/website/JobDetailBannerList.vue'
    import JobApplyForm from '../../components/website/JobApplyForm.vue'

    export default {
        components: {
          JobDetailBannerList, 
          JobApplyForm
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