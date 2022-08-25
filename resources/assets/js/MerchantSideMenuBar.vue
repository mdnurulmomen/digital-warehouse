<template>
	<div class="pcoded-main-container">
		<div class="pcoded-wrapper">
			<nav class="pcoded-navbar">
				<div class="nav-list">
					<div class="pcoded-inner-navbar main-menu">
						<div class="pcoded-navigation-label">Navigation</div>

						<ul class="pcoded-item pcoded-left-item">
							<li :class="currentRouteName=='home' ? 'active' : ''">
								<router-link :to="{ name: 'home' }" class="waves-effect waves-dark">
									<span class="pcoded-micon">
										<i class="fa fa-tasks" aria-hidden="true"></i>
									</span>
									<span class="pcoded-mtext">
										Home
									</span>
								</router-link>
							</li>

							<!-- Merchant Special Routes -->
							<li :class="currentRouteName=='my-products' ? 'active' : ''">
								<router-link :to="{ name: 'my-products' }" class="waves-effect waves-dark">
									<span class="pcoded-micon">
										<img width="18px" src="icons/cms/products.png">
									</span>
									<span class="pcoded-mtext">
										My Products
									</span>
								</router-link>
							</li>

							<li :class="currentRouteName=='my-requisitions' ? 'active' : ''">
								<router-link :to="{ name: 'my-requisitions' }" class="waves-effect waves-dark">
									<span class="pcoded-micon">
										<img width="18px" src="icons/cms/requisitions.png">
									</span>
									<span class="pcoded-mtext">
										My Requisitions
									</span>
								</router-link>
							</li>

							<li :class="currentRouteName=='my-space-deals' ? 'active' : ''">
								<router-link :to="{ name: 'my-space-deals' }" class="waves-effect waves-dark">
									<span class="pcoded-micon">
										<img width="18px" src="icons/cms/deals.png">
									</span>
									<span class="pcoded-mtext">
										My Space Deals
									</span>
								</router-link>
							</li>

							<!-- Public Routes -->
							<li 
								class="pcoded-hasmenu" 
								:class="['packaging-packages', 'delivery-companies'].includes(currentRouteName) ? 'active pcoded-trigger' : ''"
							>
								<a href="javascript:void(0)" class="waves-effect waves-dark">
									<span class="pcoded-micon">
										<img width="18px" src="icons/cms/logistics.png">
									</span>
									<span class="pcoded-mtext">Logistics</span>
									<!-- <span class="pcoded-badge label label-warning">NEW</span> -->
								</a>
								<ul class="pcoded-submenu">
									<li :class="currentRouteName=='packaging-packages' ? 'active' : ''">
										<router-link :to="{ name: 'packaging-packages' }" class="waves-effect waves-dark">
											<span class="pcoded-mtext">Packaging</span>
										</router-link>
									</li>

									<li :class="currentRouteName=='delivery-companies' ? 'active' : ''">
										<router-link :to="{ name: 'delivery-companies' }" class="waves-effect waves-dark">
											<span class="pcoded-mtext">Delivery Companies</span>
										</router-link>
									</li>
								</ul>
							</li>
						</ul>
					</div>

				</div>
			</nav>

			<router-view></router-view>

			<div id="styleSelector">
			</div>

		</div>
	</div>

</template>

<script>

    export default {

    	computed: {
		    currentRouteName() {
		        return this.$route.name;
		    }
		}

    }
    
</script>