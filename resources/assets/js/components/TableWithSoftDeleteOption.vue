<template v-if="userHasPermissionTo('view-' + requiredPermission + '-index')">
	
	<div class="tab-content card-block">
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
						<th>
							Actions
						</th>
					</tr>
				</thead>
				<tbody>

					<tr v-for="content in currentContents" :key="content.id"
					>
						<td v-for="columnValue in columnValuesToShow" :key="columnValue">
							{{ getColumnValue(content, columnValue) }}
						</td>
						
						<td>
							<button type="button" 
									class="btn btn-grd-info btn-icon"  
									@click="$emit('showContentDetails', content)" 
								 	v-if="userHasPermissionTo('view-' + requiredPermission + '-index')" 
								 	v-show="$route.name!='variations'"
							>
								<i class="fa fa-eye"></i>
							</button>

							<button type="button" 
									class="btn btn-grd-primary btn-icon" 
									v-show="!content.deleted_at" 
									@click="$emit('openContentEditForm', content)" 
									v-if="userHasPermissionTo('update-' + requiredPermission)"
							>
								<i class="fa fa-edit"></i>
							</button>

							<button type="button" 
									class="btn btn-grd-danger btn-icon" 
									v-show="!content.deleted_at" 
									@click="$emit('openContentDeleteForm', content)" 
									v-if="userHasPermissionTo('delete-' + requiredPermission)" 
									:disabled="$route.name=='warehouses' && immuteableWarehouse(content)"
							>
								<i class="fa fa-trash"></i>
							</button>

							<button type="button" 
									class="btn btn-grd-warning btn-icon" 
									v-show="content.deleted_at" 
									@click="$emit('openContentRestoreForm', content)" 
									v-if="userHasPermissionTo('delete-' + requiredPermission)"
							>
								<i class="fa fa-undo"></i>
							</button>

							<button type="button" 
									class="btn btn-grd-warning btn-icon" 
									@click="$emit('goMerchantDeals', content)" 
									v-if="$route.name=='merchants' && userHasPermissionTo('view-merchant-deal-index')"
							>
								<i aria-hidden="true" class="fa fa-handshake-o"></i>
							</button>

							<button type="button" 
									class="btn btn-grd-warning btn-icon" 
									@click="$emit('goCategoryProducts', content)" 
									v-if="$route.name=='product-categories' && userHasPermissionTo('view-product-index')"
							>
								<i class="fa fa-product-hunt"></i>
							</button>
						</td>
					</tr>
					<tr 
				  		v-show="!currentContents.length"
				  	>
			    		<td :colspan="columnNames.length+1">
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
						<th>
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
					class="btn btn-primary btn-sm" 
					@click="query === '' ? $emit('fetchAllContents') : $emit('searchData')"
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
				default: {}
			},
			requiredPermission: {
				type: String,
				required: true
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
				if (!object.first_name && !object.last_name) {
					return 'NA';
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
				else if (columnValue.match(/mobile/gi)) {
					return object.mobile;
				}
				else if (columnValue.match(/status/gi)) {
					return object.active ? 'Approved' : 'Pending';
				}
				else if (columnValue.match(/owner_total_warehouses/gi)) {
					return object.warehouses_count;
				}
				else if (columnValue.match(/products_count/gi)) {
					return object.products_count;
				}
				else if (columnValue.match(/variation_type_name/gi)) {
					return object.variation_type ? this.$options.filters.capitalize(object.variation_type.name) : '';
				}
				else if (columnValue.match(/variation_parent_name/gi)) {
					return object.variation_parent ? this.$options.filters.capitalize(object.variation_parent.name) : '';
				}
				else if (columnValue.match(/name/gi)) {
					return this.$options.filters.capitalize(object.name);
				}
				else if (columnValue.match(/code/gi)) {
					return this.$options.filters.capitalize(object.code);
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

				else if (columnName.match(/#/gi) && columnName.match(/products/gi)) {
					
					if (this.ascending) {
						this.ascending = false;
						this.descending = true;
						this.descendingNumeric('products_count');
					}
					else if (this.descending) {
						this.ascending = true;
						this.descending = false;
						this.ascendingNumeric('products_count');
					}
					else {
						this.ascending = true;
						this.descending = false;
						this.ascendingNumeric('products_count');
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