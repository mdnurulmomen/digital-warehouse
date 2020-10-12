<template>
	
	<div class="tab-content card-block">
		<div class="card">
			<div class="table-responsive">
				<table class="table table-striped table-bordered nowrap text-center">
					<thead>
						<tr>
							<th v-for="columnName in columnNames" :key="columnName">
								{{ columnName }}
								<i class="fa fa-sort" aria-hidden="true"></i>
							</th>
							<th>
								Actions
							</th>
						</tr>
					</thead>
					<tbody>

						<tr v-if="contentsToShow.length" 
							v-for="content in contentsToShow" 
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
										class="btn btn-grd-inverse btn-icon" 
										v-show="content.deleted_at" 
										@click="$emit('openContentUndoForm', content)"
								>
									<i class="fas fa-undo"></i>
								</button>
							</td>
					    
						</tr>
						<tr 
					  		v-show="!contentsToShow.length"
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
							<th v-for="columnName in columnNames" :key="columnName">
								{{ columnName }}
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
				required: true
			},
		},

		data() {
	        return {
	      		contentsPerPage : this.perPage 
	        }
		},

		methods : {
			getFullName(object) {
				if (!object.first_name && !object.last_name) {
					return 'No Name';
				}

				return object.first_name + object.last_name;
			},
			getColumnValue(object, columnValue) {
				if (columnValue=='full_name') {
					return this.getFullName(object);
				}
				else if (columnValue=='user_name') {
					return object.user_name;
				}
				else if (columnValue=='email') {
					return object.email;
				}
				else if (columnValue=='mobile') {
					return object.mobile;
				}
				else if (columnValue=='total_warhouses') {
					return object.warhouses.length;
				}
			},
		}

	}

</script>