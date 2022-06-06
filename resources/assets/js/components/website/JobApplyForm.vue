<template>
	<!-- Modal Desc -->
	<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="staticBackdropLabel">Apply for Warehouse Manager</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<!-- Form -->
					<form class="row g-3 form-bg form-bg-quote" name="get-a-quote" action="javascript:void(0)">
						<div class="col-md-6 col-12">
							<label class="form-label form-label-quote">First Name *</label>
							<input type="text" class="form-control" required />
						</div>
						<div class="col-md-6 col-12">
							<label class="form-label form-label-quote">Last Name *</label>
							<input type="text" class="form-control" required />
						</div>
						<div class="col-12">
							<label class="form-label form-label-quote">Email *</label>
							<input
							type="email"
							class="form-control"
							required
							/>
						</div>
						<div class="col-12">
							<label class="form-label form-label-quote">Phone *</label>
							<input
							type="tel"
							class="form-control"
							placeholder="01*********"
							required
							/>
						</div>
						<div class="col-12">
							<label class="form-label form-label-quote">Address *</label>
							<input type="text" class="form-control" required />
						</div>
						<div class="col-12">
							<label class="form-label form-label-quote">Highest Education Level *</label>
							<select name="dispatch-orders" id="dispatch-orders" onchange="">
								<option value="" selected="selected"></option>
								<option value="primary">Primary</option>
								<option value="high-school">High School</option>
								<option value="ssc-o-level">SSC / O Level</option>
								<option value="Hsc-o-level">HSC / A Level</option>
								<option value="diploma">Diploma</option>
								<option value="honors-bba">Honors / BBA</option>
								<option value="masters-mba">Masters / MBA</option>
								<option value="phd-doctorate">PhD / Doctorate</option>
								<option value="no-education">No Education</option>
							</select>
						</div>
						<div class="col-12">
							<label class="form-label form-label-quote">CV *</label>
							<input type="file" id="cv" name="cv">
						</div>
						<div class="col-12 text-end">
							<a href="" class="quote-btn w-100" type="submit">Submit</a>
						</div>
					</form>
					<!-- Form -->
				</div>
			</div>
		</div>
	</div>
	<!-- Modal Desc -->
</template>

<script>
	export default {
		data () {
			return {

			}
		}
	}
</script>

<style lang="css" scoped>
</style>