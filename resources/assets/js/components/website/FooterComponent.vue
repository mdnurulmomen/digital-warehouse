<template>
	<footer class="align-center">
      <div class="container">
        <div class="row">
          <div class="col-lg-3 col-md-6">
            <router-link :to="{ name: 'home' }" class="notransition">
              <img :src="'/icon/logo.png'" alt="logo"
              />
            </router-link>
            <p class="footer-text">
              Gudam solves the hardest omnichannel logistics problems for the world’s largest retailers and brands.
            </p>
          </div>
          <div class="col-lg-3 col-md-6">
            <h5 class="space-sub-title mb-3">Our Company</h5>
            <ul>
              <li>
                <router-link aria-current="page" :to="{ name: 'about-us' }">
                  <span>About Us</span>
                </router-link>
              </li>
              <li>
                <router-link aria-current="page" :to="{ name: 'terms-and-conditions' }">
                  <span>Terms & Privacy Policy</span>
                </router-link>
              </li>
              <li>
                <router-link aria-current="page" :to="{ name: 'careers' }">
                  <span>Career</span>
                </router-link>
              </li>
            </ul>
          </div>
          <div class="col-lg-3 col-md-6">
            <h5 class="space-sub-title mb-3">Customer Service</h5>
            <ul>
              <li>
                <router-link aria-current="page" :to="{ name: 'services' }">
                  <span>Our Services</span>
                </router-link>
              </li>
              <li>
                <router-link aria-current="page" :to="{ name: 'warehouses' }">
                  <span>Warehouse List</span>
                </router-link>
              </li>
            </ul>
          </div>
          <div class="col-lg-3 col-md-6">
            <h5 class="space-sub-title mb-3">Contact Us</h5>
            <ul>
              <li class="d-flex align-items-center gap-2 mb-2 responsive-flex">
                <router-link aria-current="page" :to="'#'">
                  HB Tower, Unit-401, House-1/A, Road 23
                  <br>
                  Gulshan - 1, Dhaka - 1212
                </router-link>
              </li>
              <li class="d-flex align-items-center gap-2 mb-2 responsive-flex">
                <i class="fas fa-phone-alt"></i>
                <a href="tel:+8801971234727">+880 1971-234727</a>
              </li>
              <li class="d-flex align-items-center gap-2 responsive-flex">
                <i class="far fa-envelope"></i>
                <a href="mailto:contact@gudam.com.bd">contact@gudam.com.bd</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </footer>
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