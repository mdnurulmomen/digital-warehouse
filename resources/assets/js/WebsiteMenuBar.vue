<template>
  <div>
    <header>
      <!-- Navbar Started  -->
      <nav
      class="
      navbar
      sticky-top
      navbar-expand-lg navbar-light
      bg-white
      custom-nav
      "
      >
      <div class="container">
        <router-link class="navbar-brand" :to="{ name: 'home' }">
          <img
          :src="'/icon/logo.png'"
          alt="logo"
          class="d-inline-block align-text-top"
          />
        </router-link>
        <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarNav"
        aria-controls="navbarNav"
        aria-expanded="false"
        aria-label="Toggle navigation"
        >
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item ms-3">
            <router-link class="nav-link" :class="currentRouteName=='home' ? 'active' : ''" aria-current="page" :to="{ name: 'home' }">
              <span>Home</span>
            </router-link>
          </li>
          <li class="nav-item ms-3">
            <router-link class="nav-link" :class="currentRouteName=='services' ? 'active' : ''" :to="{ name: 'services' }">
              <span>Services</span>
            </router-link>
          </li>
          <li class="nav-item ms-3">
            <router-link class="nav-link" :class="(['warehouses', 'warehouse-details']).includes(currentRouteName) ? 'active' : ''" :to="{ name: 'warehouses' }">
              <span>Warehouses</span>
            </router-link>
          </li>
          <li class="nav-item ms-3">
            <router-link class="nav-link" :class="currentRouteName=='contact-us' ? 'active' : ''" :to="{ name: 'contact-us' }">
              <span>Contact Us</span>
            </router-link>
          </li>
          <li class="nav-item ms-3">
            <router-link class="btn-start" :class="currentRouteName=='registrations' ? 'active' : ''" :to="{ name: 'registrations' }" :style="currentRouteName=='registrations' ? {background: '#0DA0A0', color: '#ffffff !important'} : ''">
              <span>Get Started</span>
            </router-link>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</header>
<!-- Navbar End -->

<router-view></router-view>

</div>
</template>

<script>
  export default {
    mounted() {
      AOS.init({once: true});
    },
    computed: {
      currentRouteName() {
        return this.$route.name;
      }
    },
    data () {
      return {

      }
    }
  }
</script>

<style lang="css" scoped>
</style>