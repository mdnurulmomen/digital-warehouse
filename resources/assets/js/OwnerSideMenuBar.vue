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
								:class="['home','general-dashboar-1','general-dashboar-2'].includes(currentRouteName) ? 'active pcoded-trigger' : ''"
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
											<span class="pcoded-mtext">Home</span>
										</router-link>
									</li>

									<li :class="currentRouteName=='general-dashboar-1' ? 'active' : ''" v-if="userHasPermissionTo('view-general-dashboard-one')">
										<router-link :to="{ name: 'general-dashboar-1' }" class="waves-effect waves-dark">
											<span class="pcoded-mtext">Dashboard 2</span>
										</router-link>
									</li>

									<li :class="currentRouteName=='general-dashboar-2' ? 'active' : ''" v-if="userHasPermissionTo('view-general-dashboard-two')">
										<router-link :to="{ name: 'general-dashboar-2' }" class="waves-effect waves-dark">
											<span class="pcoded-mtext">Dashboard 3</span>
										</router-link>
									</li>
								</ul>
							</li>

							<!-- Special Routes -->
							<li 
								:class="currentRouteName=='roles' ? 'active' : ''" 
								v-if="userHasPermissionTo('view-role-index')"
							>
								<router-link :to="{ name: 'roles' }" class="waves-effect waves-dark">
									<span class="pcoded-micon">
										<i class="fa fa-tasks" aria-hidden="true"></i>
									</span>
									<span class="pcoded-mtext">
										Roles
									</span>
								</router-link>
							</li>

							<li 
								class="pcoded-hasmenu" 
								:class="['storage-types', 'containers', 'rent-periods', 'owners', 'my-warehouses', 'warehouses'].includes(currentRouteName) ? 'active pcoded-trigger' : ''" 
							>
								<a href="javascript:void(0)" class="waves-effect waves-dark">
									<span class="pcoded-micon">
										<i class="fas fa-warehouse"></i>
									</span>
									<span class="pcoded-mtext">Warehouse</span>
								</a>
								<ul class="pcoded-submenu">
									<li 
										class="pcoded-hasmenu" 
										:class="['storage-types', 'containers', 'rent-periods'].includes(currentRouteName) ? 'active pcoded-trigger' : ''" 
										v-if="userHasPermissionTo('view-warehouse-asset-index')"
									>
										<a href="javascript:void(0)" class="waves-effect waves-dark">
											<span class="pcoded-mtext">
												Assets
											</span>
										</a>
										<ul class="pcoded-submenu">
											<li :class="currentRouteName=='rent-periods' ? 'active' : ''">
												<router-link :to="{ name: 'rent-periods' }" class="waves-effect waves-dark">
													<span class="pcoded-mtext">Rent Periods</span>
												</router-link>
											</li>
											
											<li :class="currentRouteName=='storage-types' ? 'active' : ''">
												<router-link :to="{ name: 'storage-types' }" class="waves-effect waves-dark">
													<span class="pcoded-mtext">Storage Types</span>
												</router-link>
											</li>

											<li :class="currentRouteName=='containers' ? 'active' : ''">
												<router-link :to="{ name: 'containers' }" class="waves-effect waves-dark">
													<span class="pcoded-mtext">Container Types</span>
												</router-link>
											</li>
										</ul>
									</li>

									<li :class="currentRouteName=='owners' ? 'active' : ''" v-if="userHasPermissionTo('view-warehouse-owner-index')">
										<router-link :to="{ name: 'owners' }" class="waves-effect waves-dark">
											<span class="pcoded-mtext">Owners</span>
										</router-link>
									</li>

									<li :class="currentRouteName=='my-warehouses' ? 'active' : ''">
										<router-link :to="{ name: 'my-warehouses' }" class="waves-effect waves-dark">
											<span class="pcoded-mtext">My Warehouses</span>
										</router-link>
									</li>

									<li :class="currentRouteName=='warehouses' ? 'active' : ''" v-if="userHasPermissionTo('view-warehouse-index')">
										<router-link :to="{ name: 'warehouses' }" class="waves-effect waves-dark">
											<span class="pcoded-mtext">Warehouses</span>
										</router-link>
									</li>
								</ul>
							</li>

							<li :class="currentRouteName=='managers' ? 'active' : ''" 
								v-if="userHasPermissionTo('view-manager-index')"
							>
								<router-link :to="{ name: 'managers' }" class="waves-effect waves-dark">
									<span class="pcoded-micon">
										<i class="fa fa-user"></i>
									</span>
									<span class="pcoded-mtext">
										Managers
									</span>
								</router-link>
							</li>

							<li :class="['merchants', 'merchant-deals', 'deal-payments', 'merchant-products'].includes(currentRouteName) ? 'active' : ''" 
								v-if="userHasPermissionTo('view-merchant-index')"
							>
								<router-link :to="{ name: 'merchants' }" class="waves-effect waves-dark">
									<span class="pcoded-micon">
										<i class="fa fa-users"></i>
									</span>
									<span class="pcoded-mtext">
										Merchants
									</span>
								</router-link>
							</li>

							<li 
								class="pcoded-hasmenu" 
								:class="['my-warehouse-products' ,'products', 'product-merchants', 'product-stocks', 'product-categories', 'category-products', 'variation-types', 'variations', 'product-manufacturers'].includes(currentRouteName) ? 'active pcoded-trigger' : ''" 
							>
								<a href="javascript:void(0)" class="waves-effect waves-dark">
									<span class="pcoded-micon">
										<i class="fab fa-product-hunt"></i>
									</span>
									<span class="pcoded-mtext">Product</span>
									<!-- <span class="pcoded-badge label label-warning">NEW</span> -->
								</a>
								<ul class="pcoded-submenu">
									<!-- 
									<li :class="currentRouteName=='my-warehouse-products' ? 'active' : ''">
										<router-link :to="{ name: 'my-warehouse-products' }" class="waves-effect waves-dark">
											<span class="pcoded-mtext">My Products</span>
										</router-link>
									</li> 
									-->

									<li 
										class="pcoded-hasmenu" 
										:class="['product-categories', 'category-products', 'variation-types', 'variations'].includes(currentRouteName) ? 'active pcoded-trigger' : ''" 
										v-if="userHasPermissionTo('view-product-asset-index')"
									>
										<a href="javascript:void(0)" class="waves-effect waves-dark">
											<span class="pcoded-mtext">Assets</span>
										</a>
										<ul class="pcoded-submenu">
											<li :class="['product-categories', 'category-products'].includes(currentRouteName) ? 'active' : ''">
												<router-link :to="{ name: 'product-categories' }" class="waves-effect waves-dark">
													<span class="pcoded-mtext">Categories</span>
												</router-link>
											</li>

											<li 
												:class="['product-manufacturers'].includes(currentRouteName) ? 'active' : ''" 
											>
												<router-link :to="{ name: 'product-manufacturers' }" class="waves-effect waves-dark">
													<span class="pcoded-mtext">Manufacturers</span>
												</router-link>
											</li>
											
											<li :class="['variation-types'].includes(currentRouteName) ? 'active' : ''">
												<router-link :to="{ name: 'variation-types' }" class="waves-effect waves-dark">
													<span class="pcoded-mtext">Variation-Types</span>
												</router-link>
											</li>

											<li :class="['variations'].includes(currentRouteName) ? 'active' : ''">
												<router-link :to="{ name: 'variations' }" class="waves-effect waves-dark">
													<span class="pcoded-mtext">Variation-Names</span>
												</router-link>
											</li>
										</ul>
									</li>

									<li 
										:class="['products', 'product-merchants', 'product-stocks'].includes(currentRouteName) ? 'active' : ''" 
										v-if="userHasPermissionTo('view-product-index')"
									>
										<router-link :to="{ name: 'products' }" class="waves-effect waves-dark">
											<span class="pcoded-mtext">All Products</span>
										</router-link>
									</li>								
								</ul>
							</li>

							<li :class="currentRouteName=='requisitions' ? 'active' : ''" 
								v-if="userHasPermissionTo('view-requisition-index')"
							>
								<router-link :to="{ name: 'requisitions' }" class="waves-effect waves-dark">
									<span class="pcoded-micon">
										<i class="fa fa-truck"></i>
									</span>
									<span class="pcoded-mtext">
										Requisitions
									</span>
									<span class="pcoded-badge label label-warning" v-show="currentRouteName!='requisitions' && newRequisition.length">
										{{ newRequisition.length }}
									</span>
									<span class="pcoded-badge label label-success" v-show="currentRouteName!='requisitions' && newAcceptance.length">
										{{ newAcceptance.length }}
									</span>
								</router-link>
							</li>

							<li 
								class="pcoded-hasmenu" 
								:class="['packaging-packages', 'delivery-companies'].includes(currentRouteName) ? 'active pcoded-trigger' : ''"
							>
								<a href="javascript:void(0)" class="waves-effect waves-dark">
									<span class="pcoded-micon">
										<i class="fas fa-boxes"></i>
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

							<!-- 
								Owner Special Routes
								<li :class="currentRouteName=='my-warehouses' ? 'active' : ''">
									<router-link :to="{ name: 'my-warehouses' }" class="waves-effect waves-dark">
										<span class="pcoded-mtext">My Warehouses</span>
									</router-link>
								</li>

								<li :class="currentRouteName=='my-warehouse-products' ? 'active' : ''">
									<router-link :to="{ name: 'my-warehouse-products' }" class="waves-effect waves-dark">
										<span class="pcoded-mtext">My Products</span>
									</router-link>
								</li>
							 -->
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