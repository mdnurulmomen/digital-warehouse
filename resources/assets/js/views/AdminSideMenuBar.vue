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
								:class="['home', 'general-dashboar-2'].includes(currentRouteName) ? 'active pcoded-trigger' : ''"
							>
								<a href="javascript:void(0)" class="waves-effect waves-dark">
									<span class="pcoded-micon">
										<i class="fa fa-home"></i>
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

									<li :class="currentRouteName=='general-dashboar-2' ? 'active' : ''">
										<router-link :to="{ name: 'general-dashboar-2' }" class="waves-effect waves-dark">
											<span class="pcoded-mtext">Dashboard 1</span>
										</router-link>
									</li>
								</ul>
							</li>

							<li 
								class="pcoded-hasmenu" 
								:class="['storage-types', 'containers', 'rent-periods', 'variation-types', 'variations'].includes(currentRouteName) ? 'active pcoded-trigger' : ''" 
								v-if="userHasPermissionTo('view-asset-index')"
							>
								<a href="javascript:void(0)" class="waves-effect waves-dark">
									<span class="pcoded-micon">
										<i class="feather icon-box"></i>
									</span>
									<span class="pcoded-mtext">Assets</span>
									<!-- <span class="pcoded-badge label label-danger">NEW</span> -->
								</a>
								<ul class="pcoded-submenu">
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

									<li :class="currentRouteName=='rent-periods' ? 'active' : ''">
										<router-link :to="{ name: 'rent-periods' }" class="waves-effect waves-dark">
											<span class="pcoded-mtext">Rent Periods</span>
										</router-link>
									</li>

									<li 
										class="pcoded-hasmenu" 
										:class="['variation-types', 'variations'].includes(currentRouteName) ? 'active pcoded-trigger' : ''"
									>
										<a href="javascript:void(0)" class="waves-effect waves-dark">
											<span class="pcoded-mtext">Variations</span>
										</a>
										<ul class="pcoded-submenu">
											<li :class="currentRouteName=='variation-types' ? 'active' : ''">
												<router-link :to="{ name: 'variation-types' }" class="waves-effect waves-dark">
													<span class="pcoded-mtext">Types</span>
												</router-link>
											</li>
											<li :class="currentRouteName=='variations' ? 'active' : ''">
												<router-link :to="{ name: 'variations' }" class="waves-effect waves-dark">
													<span class="pcoded-mtext">Variations</span>
												</router-link>
											</li>
										</ul>
									</li>
								</ul>
							</li>

							<li 
								class="pcoded-hasmenu" 
								:class="currentRouteName=='packaging-packages' ? 'active pcoded-trigger' : ''" 
							>
								<a href="javascript:void(0)" class="waves-effect waves-dark">
									<span class="pcoded-micon">
										<i class="fa fa-gift" aria-hidden="true"></i>
									</span>
									<span class="pcoded-mtext">Packaging</span>
								</a>
								<ul class="pcoded-submenu">
									<li :class="currentRouteName=='packaging-packages' ? 'active' : ''">
										<router-link :to="{ name: 'packaging-packages' }" class="waves-effect waves-dark">
											<span class="pcoded-mtext">Packages</span>
										</router-link>
									</li>
								</ul>
							</li>

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
								:class="['owners', 'warehouses'].includes(currentRouteName) ? 'active pcoded-trigger' : ''" 
								v-if="userHasPermissionTo('view-owner-index') || userHasPermissionTo('view-warehouse-index')"
							>
								<a href="javascript:void(0)" class="waves-effect waves-dark">
									<span class="pcoded-micon">
										<i class="feather icon-command"></i>
									</span>
									<span class="pcoded-mtext">Warehouse</span>
								</a>
								<ul class="pcoded-submenu">
									<li :class="currentRouteName=='owners' ? 'active' : ''" v-if="userHasPermissionTo('view-warehouse-owner-index')">
										<router-link :to="{ name: 'owners' }" class="waves-effect waves-dark">
											<span class="pcoded-mtext">Owners</span>
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

							<li :class="currentRouteName=='merchants' ? 'active' : ''" 
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
								:class="['products', 'product-categories', 'product-stocks'].includes(currentRouteName) ? 'active pcoded-trigger' : ''" 
								v-if="userHasPermissionTo('view-product-index') || userHasPermissionTo('view-product-category-index')"
							>
								<a href="javascript:void(0)" class="waves-effect waves-dark">
									<span class="pcoded-micon">
										<i class="fab fa-product-hunt"></i>
									</span>
									<span class="pcoded-mtext">Product</span>
									<!-- <span class="pcoded-badge label label-warning">NEW</span> -->
								</a>
								<ul class="pcoded-submenu">
									<li 
										:class="currentRouteName=='products' ? 'active' : ''" 
										v-if="userHasPermissionTo('view-product-index')"
									>
										<router-link :to="{ name: 'products' }" class="waves-effect waves-dark">
											<span class="pcoded-mtext">Products</span>
										</router-link>
									</li>
									
									<li 
										:class="currentRouteName=='product-categories' ? 'active' : ''" 
										v-if="userHasPermissionTo('view-product-category-index')"
									>
										<router-link :to="{ name: 'product-categories' }" class="waves-effect waves-dark">
											<span class="pcoded-mtext">Categories</span>
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
						</ul>
					</div>

				</div>
			</nav>

			<router-view></router-view>

			<div id="styleSelector"></div>

		</div>
	</div>

</template>

<script>

    export default {

    	data() {

	        return {

	        	newAcceptance : [],
	        	newRequisition : [],

	        }

		},

    	created() {

    		Echo.private(`new-requisition`)
		    .listen('NewRequisitionMade', (e) => {
		        
		        if (this.currentRouteName!='requisitions') {

			        this.newRequisition.push(e);

		        }else {

		        	this.newRequisition = [];

		        }

		    });
			
			if (this.userHasPermissionTo('view-dispatch-index')) {

			    Echo.private(`product-received`)
			    .listen('ProductReceived', (e) => {
			        
			        if (this.currentRouteName!='requisitions') {

				        this.newAcceptance.push(e);

			        }else {

						this.newAcceptance = [];			        	

			        }

			    });	    

			}

    	},

    	computed: {

		    currentRouteName() {

		        return this.$route.name;
		    
		    }

		},

		watch: {

			$route(to, from) {
			  // react to route changes...
			  
			  if (to.name=='requisitions') {

			  	this.newAcceptance = [];
				this.newRequisition = [];

			  }

			}
			
		}

    }

</script>