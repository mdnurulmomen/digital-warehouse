<template v-if="userHasPermissionTo('view-' + requiredPermission + '-index')">
	<div class="tab-content card-block pl-0 pr-0" v-show="!loading">

		<!-- <loading v-show="loading"></loading> -->

		<div class="table-responsive">
			<table class="table table-striped table-bordered nowrap text-center">
				<thead>
					<tr>
						<th 
							v-for="columnName in columnNames" 
							:key="columnName" 
						>
							<a href="javascript:void(0)" @click="changeContentsOrder(columnName)"> 
								{{ columnName | capitalize }}
								<span v-show="currentSorting.toUpperCase() === columnName.toUpperCase() && ascending">
									<i class="fa fa-sort-up" aria-hidden="true"></i>
								</span>
								<span v-show="currentSorting.toUpperCase() === columnName.toUpperCase() && descending">
									<i class="fa fa-sort-down" aria-hidden="true"></i>
								</span>
								<span v-show="currentSorting.toUpperCase() !== columnName.toUpperCase()">
									<i class="fa fa-sort" aria-hidden="true" style="opacity: 0.4;"></i>
								</span>
							</a>
						</th>

						<th 
							v-show="($route.name!='variation-types' && $route.name!='variations' && $route.name!='product-manufacturers' && $route.name!='rent-periods' && $route.name!='storage-types' && $route.name!='container-types') || (userHasPermissionTo('update-' + requiredPermission) || userHasPermissionTo('delete-' + requiredPermission))"
						>
							Actions
						</th>
					</tr>
				</thead>
				<tbody>

					<tr 
						v-for="content in currentContents" 
						:key="content.id" 
						:class="content.id==currentContent.id ? 'highlighted' : ''"
					>
						<td v-for="columnValue in columnValuesToShow" :key="columnValue">
							{{ getColumnValue(content, columnValue) }}
						</td>
						
						<td
							v-show="($route.name!='variation-types' && $route.name!='variations' && $route.name!='product-manufacturers' && $route.name!='rent-periods' && $route.name!='storage-types' && $route.name!='container-types') || (userHasPermissionTo('update-' + requiredPermission) || userHasPermissionTo('delete-' + requiredPermission))"
						>
							<button type="button" 
									class="btn waves-effect waves-light btn-info btn-outline-info btn-icon" 
									v-tooltip.bottom-end="'View Details'" 
									:disabled="formSubmitted"  
									@click="$emit('showContentDetails', content)" 
								 	v-show="$route.name!='variation-types' && $route.name!='variations' && $route.name!='product-manufacturers' && $route.name!='rent-periods' && $route.name!='storage-types' && $route.name!='container-types' && $route.name!='vendors' && $route.name!='locations'" 
							>
								<i class="fa fa-eye"></i>
							</button>

							<button type="button" 
									class="btn waves-effect waves-dark btn-primary btn-outline-primary btn-icon" 
									v-tooltip.bottom-end="'Edit'" 
									:disabled="formSubmitted"
									v-show="!content.deleted_at" 
									@click="$emit('openContentEditForm', content)" 
									v-if="userHasPermissionTo('update-' + requiredPermission)"
							>
								<i class="fa fa-edit"></i>
							</button>

							<button type="button" 
									class="btn waves-effect waves-dark btn-danger btn-outline-danger btn-icon" 
									v-tooltip.bottom-end="'Delete'" 
									v-show="!content.deleted_at" 
									@click="$emit('openContentDeleteForm', content)" 
									v-if="userHasPermissionTo('delete-' + requiredPermission)" 
									:disabled="formSubmitted || ($route.name=='warehouses' && immuteableWarehouse(content)) /* || ($route.name == 'merchants' && content.deals_count)*/"
							>
								<i class="fa fa-trash"></i>
							</button>

							<button type="button" 
									class="btn waves-effect waves-dark btn-warning btn-outline-warning btn-icon" 
									v-tooltip.bottom-end="'Restore'" 
									:disabled="formSubmitted"
									v-show="content.deleted_at" 
									@click="$emit('openContentRestoreForm', content)" 
									v-if="userHasPermissionTo('delete-' + requiredPermission)"
							>
								<i class="fa fa-undo"></i>
							</button>

							<button type="button" 
									class="btn waves-effect waves-dark btn-success btn-outline-success btn-icon" 
									v-tooltip.bottom-end="'Space-Deals'" 
									:disabled="formSubmitted" 
									@click="$emit('goMerchantDeals', content)" 
									v-if="$route.name=='merchants' && userHasPermissionTo('view-merchant-space-deal-index')"
							>
								<img src="icons/cms/deals.png">
							</button>

							<button type="button" 
									class="btn waves-effect waves-dark btn-warning btn-outline-warning btn-icon" 
									v-tooltip.bottom-end="'Products'" 
									:disabled="formSubmitted" 
									@click="$emit('goMerchantProducts', content)" 
									v-if="$route.name=='merchants' && userHasPermissionTo('view-merchant-product-index')"
							>
								<img src="icons/cms/products.png">
							</button>

							<button 
								type="button" 
								class="btn waves-effect waves-dark btn-default btn-outline-default btn-icon" 
								v-tooltip.bottom-end="'Support-Rents'" 
								:disabled="formSubmitted"  
								@click="$emit('goToSupportDealRents', content)" 
								v-if="$route.name=='merchants' && userHasPermissionTo('view-merchant-payment-index')" 
							>
								<img src="/icons/cms/deal-rents.png" width="22px">
							</button>

							<button type="button" 
									class="btn waves-effect waves-dark btn-inverse btn-outline-inverse btn-icon"  
									v-tooltip.bottom-end="'View Containers'" 
									:disabled="formSubmitted" 
									v-if="$route.name=='warehouses'" 
									@click="$emit('showContainerShelfDetails', content)"
							>
								<img src="icons/cms/containers.png" width="17px">
							</button>
						</td>
					</tr>
					<tr 
				  		v-show="!currentContents.length"
				  	>
			    		<td class="text-center" :colspan="columnNames.length+1">
				      		<div class="alert alert-danger" role="alert">
				      			Sorry, No data found.
				      		</div>
				    	</td>
				  	</tr>

				</tbody>
				<tfoot>
					<tr>	
						<th 
							v-for="columnName in columnNames" 
							:key="'table-footer-' + columnName" 
						>
							<a href="javascript:void(0)" @click="changeContentsOrder(columnName)"> 
								{{ columnName | capitalize }}
								<span v-show="currentSorting.toUpperCase() === columnName.toUpperCase() && ascending">
									<i class="fa fa-sort-up" aria-hidden="true"></i>
								</span>
								<span v-show="currentSorting.toUpperCase() === columnName.toUpperCase() && descending">
									<i class="fa fa-sort-down" aria-hidden="true"></i>
								</span>
								<span v-show="currentSorting.toUpperCase() !== columnName.toUpperCase()">
									<i class="fa fa-sort" aria-hidden="true" style="opacity: 0.4;"></i>
								</span>
							</a>
						</th>

						<th
							v-show="($route.name!='variation-types' && $route.name!='variations' && $route.name!='product-manufacturers' && $route.name!='rent-periods' && $route.name!='storage-types' && $route.name!='container-types') || (userHasPermissionTo('update-' + requiredPermission) || userHasPermissionTo('delete-' + requiredPermission))"
						>
							Actions
						</th>
					</tr>
				</tfoot>
			</table>
		</div>
		
		<div class="row d-flex align-items-center">
			<div class="col-sm-2 col-4">
				<select 
					class="form-control" 
					v-model.number="contentsPerPage" 
					@change="$emit('changeNumberContents', contentsPerPage)"
				>
					<option>10</option>
					<option>20</option>
					<option>30</option>
					<option>40</option>
					<option>50</option>
				</select>
			</div>
			<div class="col-sm-2 col-8">
				<button 
					type="button" 
					class="btn waves-effect waves-dark btn-primary btn-outline-primary btn-sm" 
					v-tooltip.bottom-end="'Reload'" 
					@click="pagination.current_page = 1; query === '' ? $emit('fetchAllContents') : $emit('searchData')"
				>
					Reload
					<i class="fa fa-sync"></i>
				</button>
			</div>
			<div class="col-sm-8 col-12 text-right form-group">
				<pagination
					v-if="pagination.last_page > 1"
					:pagination="pagination"
					:offset="5"
					@paginate="query === '' ? $emit('fetchAllContents') : $emit('searchData')"
				>
				</pagination>
			</div>
		</div>
	</div>

</template>

<script type="text/javascript">
	
	export default {

		props: {
			// Required string
			query: {
				type: String,
				required: true
			},
			perPage: {
				type: Number,
				required: true
			},
			columnNames: {
				type: Array,
				required: true
			},
			columnValuesToShow: {
				type: Array,
				required: true
			},
			contentsToShow: {
				type: Array,
				required: true
			},
			pagination: {
				type: Object,
				required: true,
				default: () => ({})
			},
			requiredPermission: {
				type: String,
				required: true
			},
			formSubmitted : {
				type : Boolean,
				default: false
			},
			loading : {
				type : Boolean,
				default: false
			},
			currentContent: {
				type: Object,
				default: {}
			},
		},

		data() {
	        return {
	      		ascending : false,
	      		descending : false,
	      		currentSorting : '',
	      		currentContents : [],
	      		contentsPerPage : this.perPage,
	        }
		},

		watch: {
			contentsToShow: function (val) {
				this.currentContents = this.contentsToShow
			},
		},

		filters: {
			capitalize: function (value) {
				if (!value) return ''

				const words = value.split(" ");

				for (let i = 0; i < words.length; i++) {
					
					if (words[i]) {

				    	words[i] = words[i][0].toUpperCase() + words[i].substr(1);

					}
					
				}

				return words.join(" ");
			}
		},

		methods : {
			
			getFullName(object) {
				if (! object.first_name && ! object.last_name) {
					return this.$options.filters.capitalize(object.user_name) || 'NA' ;
				}

				return this.$options.filters.capitalize(object.first_name) + ' ' + this.$options.filters.capitalize(object.last_name);
			},
			getColumnValue(object, columnValue) {
				
				if (columnValue.match(/full_name/gi)) {
					return this.getFullName(object);
				}
				else if (columnValue.match(/user_name/gi)) {
					return this.$options.filters.capitalize(object.user_name);
				}
				else if (columnValue.match(/email/gi)) {
					return object.email;
				}
				else if (columnValue.match(/name/gi)) {
					return this.$options.filters.capitalize(object.name);
				}
				else if (columnValue.match(/code/gi)) {
					return this.$options.filters.capitalize(object.code);
				}
				else if (columnValue.match(/price/gi)) {
					return object.price;
				}
				else if (columnValue.match(/mobile/gi)) {
					return object.mobile;
				}
				else if (columnValue.match(/status/gi)) {
					return object.active ? 'Approved' : 'Pending';
				}
				else if (columnValue.match(/owner_total_warehouses/gi)) {
					return object.warehouses_count;
				}
				else if (columnValue.match(/variation_type_name/gi)) {
					return object.variation_type ? this.$options.filters.capitalize(object.variation_type.name) : '';
				}
				else if (columnValue.match(/variation_parent_name/gi)) {
					return object.variation_parent ? this.$options.filters.capitalize(object.variation_parent.name) : '';
				}
				else if (columnValue.match(/number_space_deals/gi)) {
					return object.space_deals_count ?? 0;
				}
				else if (columnValue.match(/number_products/gi)) {
					return object.products_count ?? 0;
				}
				else if (columnValue.match(/number_days/gi)) {
					return object.number_days ?? 0;
				}
				else if (columnValue.match(/commission/gi)) {
					return object.commission ?? 0;
				}
				else if (columnValue.match(/storageType/gi)) {
					return Object.keys(object.storage_type).length ? this.$options.filters.capitalize(object.storage_type.name) : 'NA';
				}

			},
			immuteableWarehouse(warehouse) {

				if (warehouse.containers.length) {

					return warehouse.containers.some(
						warehouseContainer => warehouseContainer.engaged_quantity != 0 || warehouseContainer.partially_engaged != 0
					);

				}

				return false;
				
			},
			changeContentsOrder(columnName) {

				this.currentSorting = columnName;

				if (columnName.match(/username/gi)) {

					if (this.ascending) {
						this.ascending = false;
						this.descending = true;
						this.descendingAlphabets('user_name');
					}
					else if (this.descending) {
						this.ascending = true;
						this.descending = false;
						this.ascendingAlphabets('user_name');
					}
					else {
						this.ascending = true;
						this.descending = false;
						this.ascendingAlphabets('user_name');
					}
					
				}
				else if (columnName.match(/name/gi)) {

					const nameExists = (object) => object.hasOwnProperty('name');
					const firstNameExists = (object) => object.hasOwnProperty('first_name');

					if (this.ascending) {

						this.ascending = false;
						this.descending = true;

						this.currentContents.some(nameExists) ? this.descendingAlphabets('name') : this.currentContents.some(firstNameExists) ? this.descendingAlphabets('first_name') : this.descendingAlphabets('last_name');

					}
					else if (this.descending) {

						this.ascending = true;
						this.descending = false;

						this.currentContents.some(nameExists) ? this.ascendingAlphabets('name') : this.currentContents.some(firstNameExists) ? this.ascendingAlphabets('first_name') : this.ascendingAlphabets('last_name');

					}
					else {

						this.ascending = true;
						this.descending = false;

						this.currentContents.some(nameExists) ? this.ascendingAlphabets('name') : this.currentContents.some(firstNameExists) ? this.ascendingAlphabets('first_name') : this.ascendingAlphabets('last_name');

					}

				}
				else if (columnName.match(/email/gi)) {
					
					if (this.ascending) {
						this.ascending = false;
						this.descending = true;
						this.descendingAlphabets('email');
					}
					else if (this.descending) {
						this.ascending = true;
						this.descending = false;
						this.ascendingAlphabets('email');
					}
					else {
						this.ascending = true;
						this.descending = false;
						this.ascendingAlphabets('email');
					}

				}
				else if (columnName.match(/mobile/gi)) {

					if (this.ascending) {
						this.ascending = false;
						this.descending = true;
						this.descendingAlphabets('mobile');
					}
					else if (this.descending) {
						this.ascending = true;
						this.descending = false;
						this.ascendingAlphabets('mobile');
					}
					else {
						this.ascending = true;
						this.descending = false;
						this.ascendingAlphabets('mobile');
					}

				}
				else if (columnName.match(/#/gi) && columnName.match(/warehouses/gi)) {
					
					if (this.ascending) {
						this.ascending = false;
						this.descending = true;
						this.descendingNumeric('warehouses_count');
					}
					else if (this.descending) {
						this.ascending = true;
						this.descending = false;
						this.ascendingNumeric('warehouses_count');
					}
					else {
						this.ascending = true;
						this.descending = false;
						this.ascendingNumeric('warehouses_count');
					}
					
				}
				else if (columnName.match(/status/gi)) {
					
					if (this.ascending) {
						this.ascending = false;
						this.descending = true;
						this.descendingNumeric('active');
					}
					else if (this.descending) {
						this.ascending = true;
						this.descending = false;
						this.ascendingNumeric('active');
					}
					else {
						this.ascending = true;
						this.descending = false;
						this.ascendingNumeric('active');
					}
					
				}
				else if (columnName.match(/code/gi)) {

					if (this.ascending) {
						this.ascending = false;
						this.descending = true;
						this.descendingAlphabets('code');
					}
					else if (this.descending) {
						this.ascending = true;
						this.descending = false;
						this.ascendingAlphabets('code');
					}
					else {
						this.ascending = true;
						this.descending = false;
						this.ascendingAlphabets('code');
					}

				}
				else if (columnName.match(/#/gi) && columnName.match(/days/gi)) {
					
					if (this.ascending) {
						this.ascending = false;
						this.descending = true;
						this.descendingNumeric('number_days');
					}
					else if (this.descending) {
						this.ascending = true;
						this.descending = false;
						this.ascendingNumeric('number_days');
					}
					else {
						this.ascending = true;
						this.descending = false;
						this.ascendingNumeric('number_days');
					}
					
				}
				
			},
			ascendingAlphabets(columnValue) {
				this.currentContents.sort(
			 		function(a, b){
						var x = a[columnValue] ? a[columnValue].toLowerCase() : '';
						var y = b[columnValue] ? b[columnValue].toLowerCase() : '';
						if (x < y) {return -1;}
						if (x > y) {return 1;}
						return 0;
					}
				);
			},
			descendingAlphabets(columnValue) {
				this.currentContents.sort(
			 		function(a, b){
						var x = a[columnValue] ? a[columnValue].toLowerCase() : '';
						var y = b[columnValue] ? b[columnValue].toLowerCase() : '';
						if (x > y) {return -1;}
						if (x < y) {return 1;}
						return 0;
					}
				);
			},
			ascendingNumeric(columnValue) {
				this.currentContents.sort(
			 		function(a, b){
						return a[columnValue] - b[columnValue];
					}
				);
			},
			descendingNumeric(columnValue) {
				this.currentContents.sort(
			 		function(a, b){
						return b[columnValue] - a[columnValue];
					}
				);
			},
			ascendingArrayLength(columnValue) {
				this.currentContents.sort(
			 		function(a, b){
						return a[columnValue].length - b[columnValue].length;
					}
				);
			},
			descendingArrayLength(columnValue) {
				this.currentContents.sort(
			 		function(a, b){
						return b[columnValue].length - a[columnValue].length;
					}
				);
			},
		}

	}

</script>