<template>
	<!-- Search Area Start  -->
      <section class="search-gudam">
        <div class="container">
          <div class="row">
            <div class="col-12">
              <div class="search-box mb-3">
                <input
                  type="text"
                  class="custom-form"
                  placeholder="Find your nearest gudam"
                />
                <!-- <button
                  class="btn-start search-btn"
                  type="button"
                  id="button-addon2"
                >
                  Search
                </button> -->
              </div>
            </div>
            <div class="col-md-4 col-lg-3">
              <select
                class="form-select form-select-lg mb-3"
                aria-label=".form-select-lg example"
              >
                <option selected disabled>Location</option>
                <option value="gulshan">Gulshan</option>
                <option value="banani">Banani</option>
                <option value="badda">Badda</option>
                <option value="mirpur">Mirpur</option>
              </select>
            </div>
            <div class="col-md-4 col-lg-3">
              <select
                class="form-select form-select-lg mb-3"
                aria-label=".form-select-lg example"
              >
                <option selected disabled>Storage Type</option>
                <option value="all">All</option>
                <option value="not-controlled">Not Controlled</option>
                <option value="ambient">Ambient</option>
                <option value="chilled">Chilled</option>
                <option value="frozen">Frozen</option>
              </select>
            </div>
            <div class="col-md-4 col-lg-3">
              <select
                class="form-select form-select-lg mb-3"
                aria-label=".form-select-lg example"
              >
                <option selected disabled>Space Type</option>
                <option value="grey-space">Grey Space</option>
                <option value="rack">Rack</option>
                <option value="pallet">Pallet</option>
              </select>
            </div>
            <div class="col-md-4
                        col-lg-3
                        grey-space-required"
                        >
              <select
                class="form-select form-select-lg mb-3"
                aria-label=".form-select-lg example"
              >
                <option selected disabled>Grey Space Required</option>
                <option value="1">10 - 99</option>
                <option value="2">100 - 499</option>
                <option value="3">500 - 2,000</option>
                <option value="4">2,000+</option>
              </select>
            </div>
            <!-- <div class="col-md-4
                        col-lg-3
                        rack-required"
                        >
              <select
                class="form-select form-select-lg mb-3"
                aria-label=".form-select-lg example"
              >
                <option selected disabled>Rack Required</option>
                <option value="1">10 - 99</option>
                <option value="2">100 - 499</option>
                <option value="3">500 - 2,000</option>
                <option value="4">2,000+</option>
              </select>
            </div>
            <div class="col-md-4
                        col-lg-3
                        pallet-required"
                        >
              <select
                class="form-select form-select-lg mb-3"
                aria-label=".form-select-lg example"
              >
                <option selected disabled>Pallet Required</option>
                <option value="1">10 - 99</option>
                <option value="2">100 - 499</option>
                <option value="3">500 - 2,000</option>
                <option value="4">2,000+</option>
              </select>
            </div> -->
          </div>
        </div>
      </section>
      <!-- Search Area End  -->
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