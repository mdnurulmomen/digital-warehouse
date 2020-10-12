<template>
	
	<div class="tab-content card-block">
		<div class="card">
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
									<span v-if="currentSorting.toUpperCase() === columnName.toUpperCase() && ascending">
										<i class="fa fa-sort-up" aria-hidden="true"></i>
									</span>
									<span v-if="currentSorting.toUpperCase() === columnName.toUpperCase() && descending">
										<i class="fa fa-sort-down" aria-hidden="true"></i>
									</span>
									<span v-if="currentSorting.toUpperCase() !== columnName.toUpperCase()">
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

						<tr v-if="currentContents.length" 
							v-for="content in currentContents" 
							:key="content.id"
						>
							<td v-for="columnValue in columnValuesToShow" :key="columnValue">
								{{ getColumnValue(content, columnValue) }}
							</td>
							
							<td>
								<button type="button" 
										class="btn btn-grd-info btn-icon"  
										@click="$emit('showContentDetails', content)"
								>
									<i class="fas fa-eye"></i>
								</button>

								<button type="button" 
										class="btn btn-grd-primary btn-icon" 
										v-show="!content.deleted_at" 
										@click="$emit('openContentEditForm', content)"
								>
									<i class="fas fa-edit"></i>
								</button>

								<button type="button" 
										class="btn btn-grd-danger btn-icon" 
										v-show="!content.deleted_at" 
										@click="$emit('openContentDeleteForm', content)"
								>
									<i class="fas fa-trash"></i>
								</button>

								<button type="button" 
										class="btn btn-grd-warning btn-icon" 
										v-show="content.deleted_at" 
										@click="$emit('openContentRestoreForm', content)"
								>
									<i class="fas fa-undo"></i>
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
								:key="columnName"
							>
								{{ columnName | capitalize }}
							</th>
							<th>
								Actions
							</th>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
		<div class="row d-flex align-items-center align-content-center">
			<div class="col-sm-2">
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
			<div class="col-sm-2">
				<button 
					type="button" 
					class="btn btn-primary btn-sm" 
					@click="query === '' ? $emit('fetchAllContents') : $emit('searchData')"
				>
					Reload
					<i class="fas fa-sync"></i>
				</button>
			</div>
			<div class="col-sm-8">
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
				value = value.toString()
				return value.charAt(0).toUpperCase() + value.slice(1)
			}
		},

		methods : {
			getFullName(object) {
				if (!object.first_name && !object.last_name) {
					return 'No Name';
				}

				return object.first_name + ' ' + object.last_name;
			},
			getColumnValue(object, columnValue) {
				
				if (columnValue.match(/full_name/gi)) {
					return this.getFullName(object);
				}
				else if (columnValue.match(/user_name/gi)) {
					return object.user_name;
				}
				else if (columnValue.match(/email/gi)) {
					return object.email;
				}
				else if (columnValue.match(/mobile/gi)) {
					return object.mobile;
				}
				else if (columnValue.match(/total_warhouses/gi)) {
					return object.warhouses.length;
				}

			},
			changeContentsOrder(columnName) {

				this.currentSorting = columnName;

				if (columnName.match(/name/gi)) {

					if (this.ascending) {

						this.ascending = false;
						this.descending = true;

						this.currentContents.hasOwnProperty('name') ? this.descendingAlphabets('name') : this.currentContents.hasOwnProperty('first_name') ? this.descendingAlphabets('first_name') : this.descendingAlphabets('last_name');

					}
					else if (this.descending) {

						this.ascending = true;
						this.descending = false;

						this.currentContents.hasOwnProperty('name') ? this.ascendingAlphabets('name') : this.currentContents.hasOwnProperty('first_name') ? this.ascendingAlphabets('first_name') : this.ascendingAlphabets('last_name');

					}
					else {

						this.ascending = true;
						this.descending = false;

						this.currentContents.hasOwnProperty('name') ? this.ascendingAlphabets('name') : this.currentContents.hasOwnProperty('first_name') ? this.ascendingAlphabets('first_name') : this.ascendingAlphabets('last_name');

					}

				}
				else if (columnName.match(/username/gi)) {

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
				else if (columnName.match(/#/gi) && columnName.match(/warhouse/gi)) {
					
					if (this.ascending) {
						this.ascending = false;
						this.descending = true;
						this.descendingNumeric('warhouses');
					}
					else if (this.descending) {
						this.ascending = true;
						this.descending = false;
						this.ascendingNumeric('warhouses');
					}
					else {
						this.ascending = true;
						this.descending = false;
						this.ascendingNumeric('warhouses');
					}
					
				}
				
			},
			ascendingAlphabets(columnValue) {
				this.currentContents.sort(
			 		function(a, b){
						var x = a[columnValue].toLowerCase();
						var y = b[columnValue].toLowerCase();
						if (x < y) {return -1;}
						if (x > y) {return 1;}
						return 0;
					}
				);
			},
			descendingAlphabets(columnValue) {
				this.currentContents.sort(
			 		function(a, b){
						var x = a[columnValue].toLowerCase();
						var y = b[columnValue].toLowerCase();
						if (x > y) {return -1;}
						if (x < y) {return 1;}
						return 0;
					}
				);
			},
			ascendingNumeric(columnValue) {
				this.currentContents.sort(
			 		function(a, b){
						return a[columnValue].length - b[columnValue].length;
					}
				);
			},
			descendingNumeric(columnValue) {
				this.currentContents.sort(
			 		function(a, b){
						return b[columnValue].length - a[columnValue].length;
					}
				);
			},
		}

	}

</script>