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
								:class="['home', 'overview', 'analytics'].includes(currentRouteName) ? 'active pcoded-trigger' : ''"
							>
								<a href="javascript:void(0)" class="waves-effect waves-dark">
									<span class="pcoded-micon">
										<img width="18px" src="icons/cms/home.png">
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

									<li :class="currentRouteName=='overview' ? 'active' : ''">
										<router-link :to="{ name: 'overview' }" class="waves-effect waves-dark">
											<span class="pcoded-mtext">Overview</span>
										</router-link>
									</li>

									<li :class="currentRouteName=='analytics' ? 'active' : ''">
										<router-link :to="{ name: 'analytics' }" class="waves-effect waves-dark">
											<span class="pcoded-mtext">Analytics</span>
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
										<img width="18px" src="icons/cms/roles.png">
									</span>
									<span class="pcoded-mtext">
										Roles
									</span>
								</router-link>
							</li>

							<li 
								class="pcoded-hasmenu" 
								:class="['owners', 'warehouses', 'storage-types', 'container-types', 'containers', 'rent-periods', 'warehouse-containers', 'warehouse-container-shelves', 'warehouse-container-shelf-units'].includes(currentRouteName) ? 'active pcoded-trigger' : ''" 
								v-if="userHasPermissionTo('view-warehouse-asset-index') || userHasPermissionTo('view-warehouse-owner-index') || userHasPermissionTo('view-warehouse-index')"
							>
								<a href="javascript:void(0)" class="waves-effect waves-dark">
									<span class="pcoded-micon">
										<img width="18px" src="icons/cms/warehouses.png">
									</span>
									<span class="pcoded-mtext">Warehouse</span>
								</a>
								<ul class="pcoded-submenu">
									<li 
										class="pcoded-hasmenu" 
										:class="['rent-periods', 'storage-types', 'container-types', 'containers'].includes(currentRouteName) ? 'active pcoded-trigger' : ''" 
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

											<li :class="currentRouteName=='container-types' ? 'active' : ''">
												<router-link :to="{ name: 'container-types' }" class="waves-effect waves-dark">
													<span class="pcoded-mtext">Container Types</span>
												</router-link>
											</li>

											<li :class="currentRouteName=='containers' ? 'active' : ''">
												<router-link :to="{ name: 'containers' }" class="waves-effect waves-dark">
													<span class="pcoded-mtext">Containers</span>
												</router-link>
											</li>
										</ul>
									</li>

									<li :class="currentRouteName=='owners' ? 'active' : ''" v-if="userHasPermissionTo('view-warehouse-owner-index')">
										<router-link :to="{ name: 'owners' }" class="waves-effect waves-dark">
											<span class="pcoded-mtext">Owners</span>
										</router-link>
									</li>

									<li :class="['warehouses', 'warehouse-containers', 'warehouse-container-shelves', 'warehouse-container-shelf-units'].includes(currentRouteName) ? 'active' : ''" v-if="userHasPermissionTo('view-warehouse-index')">
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
										<img width="18px" src="icons/cms/managers.png">
									</span>
									<span class="pcoded-mtext">
										Managers
									</span>
								</router-link>
							</li>

							<li :class="currentRouteName=='mails' ? 'active' : ''" 
								v-if="userHasPermissionTo('view-mail-index')"
							>
								<router-link :to="{ name: 'mails' }" class="waves-effect waves-dark">
									<span class="pcoded-micon">
										<img width="18px" src="icons/cms/mails.png">
									</span>
									<span class="pcoded-mtext">
										Mails
									</span>
								</router-link>
							</li>

							<li :class="['merchants', 'merchant-deals', 'deal-payments', 'merchant-products'].includes(currentRouteName) ? 'active' : ''" 
								v-if="userHasPermissionTo('view-merchant-index')"
							>
								<router-link :to="{ name: 'merchants' }" class="waves-effect waves-dark">
									<span class="pcoded-micon">
										<img width="18px" src="icons/cms/merchants.png">
									</span>
									<span class="pcoded-mtext">
										Merchants
									</span>
								</router-link>
							</li>

							<li 
								class="pcoded-hasmenu" 
								:class="['product-categories', 'category-products', 'product-manufacturers', 'variation-types', 'variations', 'products', 'product-merchants', 'product-stocks', 'stocks'].includes(currentRouteName) ? 'active pcoded-trigger' : ''" 
								v-if="userHasPermissionTo('view-product-index') || userHasPermissionTo('view-product-asset-index')"
							>
								<a href="javascript:void(0)" class="waves-effect waves-dark">
									<span class="pcoded-micon">
										<img width="18px" src="icons/cms/products.png">
									</span>
									<span class="pcoded-mtext">Product</span>
									<!-- <span class="pcoded-badge label label-warning">NEW</span> -->
								</a>
								<ul class="pcoded-submenu">
									<li 
										class="pcoded-hasmenu" 
										:class="['product-categories', 'category-products', 'product-manufacturers', 'variation-types', 'variations'].includes(currentRouteName) ? 'active pcoded-trigger' : ''" 
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
											<span class="pcoded-mtext">Products</span>
										</router-link>
									</li>

									<li 
										:class="['stocks'].includes(currentRouteName) ? 'active' : ''" 
										v-if="userHasPermissionTo('view-product-stock-index')"
									>
										<router-link :to="{ name: 'stocks' }" class="waves-effect waves-dark">
											<span class="pcoded-mtext">Stocks</span>
										</router-link>
									</li>									
								</ul>
							</li>

							<li :class="(currentRouteName=='requisitions' || currentRouteName=='product-requisitions') ? 'active' : ''" 
								v-if="userHasPermissionTo('view-requisition-index')"
							>
								<router-link :to="{ name: 'requisitions' }" class="waves-effect waves-dark">
									<span class="pcoded-micon">
										<img width="18px" src="icons/cms/requisitions.png">
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