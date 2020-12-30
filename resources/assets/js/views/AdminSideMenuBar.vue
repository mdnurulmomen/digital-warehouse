<template>

	<div class="pcoded-main-container">
		<div class="pcoded-wrapper">

			<nav class="pcoded-navbar">
				<div class="nav-list">
						
					<div class="pcoded-inner-navbar main-menu">
						<div class="pcoded-navigation-label">Navigation</div>

						<ul class="pcoded-item pcoded-left-item">

							<li class="pcoded-hasmenu">
								<a href="javascript:void(0)" class="waves-effect waves-dark">
									<span class="pcoded-micon">
										<i class="feather icon-home"></i>
									</span>
									<span class="pcoded-mtext">Home</span>
									<span class="pcoded-badge label label-warning">NEW</span>
								</a>
								<ul class="pcoded-submenu">
									<li class="">
										<router-link :to="{ name: 'home' }" class="waves-effect waves-dark">
											<span class="pcoded-mtext">Dashboard</span>
										</router-link>
									</li>
									<li class="">
										<router-link :to="{ name: 'dashboar-2' }" class="waves-effect waves-dark">
											<span class="pcoded-mtext">Dashboard 2</span>
										</router-link>
									</li>
									<li class="">
										<router-link :to="{ name: 'dashboar-3' }" class="waves-effect waves-dark">
											<span class="pcoded-mtext">Dashboard 3</span>
										</router-link>
									</li>
								</ul>
							</li>
							<li class="pcoded-hasmenu">
								<a href="javascript:void(0)" class="waves-effect waves-dark">
									<span class="pcoded-micon">
										<i class="feather icon-command"></i>
									</span>
									<span class="pcoded-mtext">Warhouse</span>
									<span class="pcoded-badge label label-warning">NEW</span>
								</a>
								<ul class="pcoded-submenu">
									<li class="">
										<router-link :to="{ name: 'owners' }" class="waves-effect waves-dark">
											<span class="pcoded-mtext">Owners</span>
										</router-link>
									</li>
									<li class="">
										<router-link :to="{ name: 'warhouses' }" class="waves-effect waves-dark">
											<span class="pcoded-mtext">Warhouses</span>
										</router-link>
									</li>
								</ul>
							</li>
							<li class="pcoded-hasmenu">
								<a href="javascript:void(0)" class="waves-effect waves-dark">
									<span class="pcoded-micon">
										<i class="feather icon-user"></i>
									</span>
									<span class="pcoded-mtext">Manager</span>
									<span class="pcoded-badge label label-warning">NEW</span>
								</a>
								<ul class="pcoded-submenu">
									<li class="">
										<router-link :to="{ name: 'managers' }" class="waves-effect waves-dark">
											<span class="pcoded-mtext">Managers</span>
										</router-link>
									</li>
									<li class="">
										<router-link :to="{ name: 'dashboar-2' }" class="waves-effect waves-dark">
											<span class="pcoded-mtext">Dashboard 2</span>
										</router-link>
									</li>
									<li class="">
										<router-link :to="{ name: 'dashboar-3' }" class="waves-effect waves-dark">
											<span class="pcoded-mtext">Dashboard 3</span>
										</router-link>
									</li>
								</ul>
							</li>
							<li class="pcoded-hasmenu">
								<a href="javascript:void(0)" class="waves-effect waves-dark">
									<span class="pcoded-micon">
										<i class="feather icon-users"></i>
									</span>
									<span class="pcoded-mtext">Merchant</span>
									<span class="pcoded-badge label label-warning">NEW</span>
								</a>
								<ul class="pcoded-submenu">
									<li class="">
										<router-link :to="{ name: 'merchants' }" class="waves-effect waves-dark">
											<span class="pcoded-mtext">Merchants</span>
										</router-link>
									</li>
									<li class="">
										<router-link :to="{ name: 'dashboar-2' }" class="waves-effect waves-dark">
											<span class="pcoded-mtext">Dashboard 2</span>
										</router-link>
									</li>
									<li class="">
										<router-link :to="{ name: 'dashboar-3' }" class="waves-effect waves-dark">
											<span class="pcoded-mtext">Dashboard 3</span>
										</router-link>
									</li>
								</ul>
							</li>
							<li class="pcoded-hasmenu">
								<a href="javascript:void(0)" class="waves-effect waves-dark">
									<span class="pcoded-micon">
										<i class="feather icon-briefcase"></i>
									</span>
									<span class="pcoded-mtext">Assets</span>
									<span class="pcoded-badge label label-danger">NEW</span>
								</a>
								<ul class="pcoded-submenu">
									<li class="pcoded-hasmenu">
										<a href="javascript:void(0)" class="waves-effect waves-dark">
											<span class="pcoded-mtext">Storage</span>
										</a>
										<ul class="pcoded-submenu">
											<li class="">
												<router-link :to="{ name: 'storage-types' }" class="waves-effect waves-dark">
													<span class="pcoded-mtext">Types</span>
												</router-link>
											</li>
											<li class="">
												<router-link :to="{ name: 'dashboar-3' }" class="waves-effect waves-dark">
													<span class="pcoded-mtext">Dashboard</span>
												</router-link>
											</li>
										</ul>
									</li>
									<li class="pcoded-hasmenu">
										<a href="javascript:void(0)" class="waves-effect waves-dark">
											<span class="pcoded-mtext">Container</span>
										</a>
										<ul class="pcoded-submenu">
											<li class="">
												<router-link :to="{ name: 'containers' }" class="waves-effect waves-dark">
													<span class="pcoded-mtext">Types</span>
												</router-link>
											</li>
											<li class="">
												<router-link :to="{ name: 'dashboar-3' }" class="waves-effect waves-dark">
													<span class="pcoded-mtext">Dashboard</span>
												</router-link>
											</li>
										</ul>
									</li>
									<li class="pcoded-hasmenu">
										<a href="javascript:void(0)" class="waves-effect waves-dark">
											<span class="pcoded-mtext">Rent Periods</span>
										</a>
										<ul class="pcoded-submenu">
											<li class="">
												<router-link :to="{ name: 'rent-periods' }" class="waves-effect waves-dark">
													<span class="pcoded-mtext">Periods</span>
												</router-link>
											</li>
											<li class="">
												<router-link :to="{ name: 'dashboar-3' }" class="waves-effect waves-dark">
													<span class="pcoded-mtext">Dashboard</span>
												</router-link>
											</li>
										</ul>
									</li>
									<li class="pcoded-hasmenu">
										<a href="javascript:void(0)" class="waves-effect waves-dark">
											<span class="pcoded-mtext">Variations</span>
										</a>
										<ul class="pcoded-submenu">
											<li class="">
												<router-link :to="{ name: 'variation-types' }" class="waves-effect waves-dark">
													<span class="pcoded-mtext">Types</span>
												</router-link>
											</li>
											<li class="">
												<router-link :to="{ name: 'variations' }" class="waves-effect waves-dark">
													<span class="pcoded-mtext">Variations</span>
												</router-link>
											</li>
										</ul>
									</li>
								</ul>
							</li>
							<li class="pcoded-hasmenu">
								<a href="javascript:void(0)" class="waves-effect waves-dark">
									<span class="pcoded-micon">
										<i class="feather icon-crosshair"></i>
									</span>
									<span class="pcoded-mtext">Product</span>
									<span class="pcoded-badge label label-warning">NEW</span>
								</a>
								<ul class="pcoded-submenu">
									<li class="">
										<router-link :to="{ name: 'products' }" class="waves-effect waves-dark">
											<span class="pcoded-mtext">Products</span>
										</router-link>
									</li>
									<li class="">
										<router-link :to="{ name: 'product-categories' }" class="waves-effect waves-dark">
											<span class="pcoded-mtext">Categories</span>
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
    export default {}
</script>