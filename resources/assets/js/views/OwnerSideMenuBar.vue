<template>

	<div class="pcoded-main-container">
		<div class="pcoded-wrapper">

			<nav class="pcoded-navbar">
				<div class="nav-list">
						
					<div class="pcoded-inner-navbar main-menu">
						<div class="pcoded-navigation-label">Navigation</div>

						<ul class="pcoded-item pcoded-left-item">

							<li 
								class="pcoded-hasmenu" 
								:class="['home', 'dashboar-2'].includes(currentRouteName) ? 'active pcoded-trigger' : ''"
							>
								<a href="javascript:void(0)" class="waves-effect waves-dark">
									<span class="pcoded-micon">
										<i class="feather icon-home"></i>
									</span>
									<span class="pcoded-mtext">Home</span>
									<!-- <span class="pcoded-badge label label-warning">NEW</span> -->
								</a>
								<ul class="pcoded-submenu">
									<li :class="currentRouteName=='home' ? 'active' : ''">
										<router-link :to="{ name: 'home' }" class="waves-effect waves-dark">
											<span class="pcoded-mtext">Dashboard</span>
										</router-link>
									</li>
									<li :class="currentRouteName=='dashboar-2' ? 'active' : ''">
										<router-link :to="{ name: 'dashboar-2' }" class="waves-effect waves-dark">
											<span class="pcoded-mtext">Dashboard 2</span>
										</router-link>
									</li>
								</ul>
							</li>

							<li :class="currentRouteName=='warehouses' ? 'active' : ''">
								<router-link :to="{ name: 'warehouses' }" class="waves-effect waves-dark">
									<span class="pcoded-micon">
										<i class="feather icon-aperture"></i>
									</span>
									<span class="pcoded-mtext">Warehouses</span>
								</router-link>
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