<template>
  <div>
    <main>
      <warehouse-search-filter/>
      <warehouse-list/>
    </main>

    <footer-component/>
    <copyright-component/>
  </div>
</template>

<script>
  import WarehouseSearchFilter from '../../components/website/WarehouseSearchFilter.vue'
  import WarehouseList from '../../components/website/WarehouseList.vue'

  export default {
    components: {
      WarehouseSearchFilter, 
      WarehouseList
    },

    data () {
      return {

      }
    }
  }
</script>

<style lang="css" scoped>
</style>