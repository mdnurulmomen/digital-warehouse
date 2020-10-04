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
										<i class="feather icon-sidebar"></i>
									</span>
									<span class="pcoded-mtext">Page layouts</span>
									<span class="pcoded-badge label label-danger">NEW</span>
								</a>
								<ul class="pcoded-submenu">
									<li class="pcoded-hasmenu">
										<a href="javascript:void(0)" class="waves-effect waves-dark">
											<span class="pcoded-mtext">Vertical</span>
										</a>
										<ul class="pcoded-submenu">
											<li class="">
												<a href="menu-static.html" class="waves-effect waves-dark">
													<span class="pcoded-mtext">Static Layout</span>
												</a>
											</li>
											<li class="">
												<a href="menu-header-fixed.html" class="waves-effect waves-dark">
													<span class="pcoded-mtext">Header Fixed</span>
												</a>
											</li>
											<li class="">
												<a href="menu-compact.html" class="waves-effect waves-dark">
													<span class="pcoded-mtext">Compact</span>
												</a>
											</li>
											<li class="">
												<a href="menu-sidebar.html" class="waves-effect waves-dark">
													<span class="pcoded-mtext">Sidebar Fixed</span>
												</a>
											</li>
										</ul>
									</li>
									<li class=" pcoded-hasmenu">
										<a href="javascript:void(0)" class="waves-effect waves-dark">
											<span class="pcoded-mtext">Horizontal</span>
										</a>
										<ul class="pcoded-submenu">
											<li class="">
												<a href="menu-horizontal-static.html" target="_blank" class="waves-effect waves-dark">
													<span class="pcoded-mtext">Static Layout</span>
												</a>
											</li>
											<li class="">
												<a href="menu-horizontal-fixed.html" target="_blank" class="waves-effect waves-dark">
													<span class="pcoded-mtext">Fixed layout</span>
												</a>
											</li>
											<li class="">
												<a href="menu-horizontal-icon.html" target="_blank" class="waves-effect waves-dark">
													<span class="pcoded-mtext">Static With Icon</span>
												</a>
											</li>
											<li class="">
												<a href="menu-horizontal-icon-fixed.html" target="_blank" class="waves-effect waves-dark">
													<span class="pcoded-mtext">Fixed With Icon</span>
												</a>
											</li>
										</ul>
									</li>
									<li class="">
										<a href="menu-bottom.html" class="waves-effect waves-dark">
											<span class="pcoded-mtext">Bottom Menu</span>
										</a>
									</li>
								</ul>
							</li>
							<li class="">
								<a href="navbar-light.html" class="waves-effect waves-dark">
									<span class="pcoded-micon">
										<i class="feather icon-menu"></i>
									</span>
									<span class="pcoded-mtext">Navigation</span>
								</a>
							</li>
							<li class="pcoded-hasmenu">
								<a href="javascript:void(0)" class="waves-effect waves-dark">
									<span class="pcoded-micon">
										<i class="feather icon-layers"></i>
									</span>
									<span class="pcoded-mtext">Widget</span>
									<span class="pcoded-badge label label-danger">100+</span>
								</a>
								<ul class="pcoded-submenu">
									<li class="">
										<a href="widget-statistic.html" class="waves-effect waves-dark">
											<span class="pcoded-mtext">Statistic</span>
										</a>
									</li>
									<li class="">
										<a href="widget-data.html" class="waves-effect waves-dark">
											<span class="pcoded-mtext">Data</span>
										</a>
									</li>
									<li class="">
										<a href="widget-chart.html" class="waves-effect waves-dark">
											<span class="pcoded-mtext">Chart Widget</span>
										</a>
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