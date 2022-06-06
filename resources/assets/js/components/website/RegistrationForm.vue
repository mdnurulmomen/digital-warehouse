<template>
	<!-- Registration Form Started  -->
	<section class="registration" id="registration">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<form action="" class="reg-form">
						<ul
						class="nav nav-pills justify-content-evenly gap-3 mb-5"
						id="pills-tab"
						role="tablist"
						>
						<li class="nav-item w-100" role="presentation">
							<a
							class="tab-btn active"
							id="pills-home-tab"
							data-bs-toggle="pill"
							data-bs-target="#pills-home"
							type="button"
							role="tab"
							aria-controls="pills-home"
							aria-selected="true"
							>Owner</a
							>
						</li>
						<li class="nav-item w-100" role="presentation">
							<a
							class="tab-btn"
							id="pills-profile-tab"
							data-bs-toggle="pill"
							data-bs-target="#pills-profile"
							type="button"
							role="tab"
							aria-controls="pills-profile"
							aria-selected="false"
							>Merchant</a
							>
						</li>
					</ul>
					<div class="tab-content" id="pills-tabContent">
						<div
						class="tab-pane fade show active"
						id="pills-home"
						role="tabpanel"
						aria-labelledby="pills-home-tab"
						>
						<div class="row">
							<div class="col-md-6">
								<img
								class="img-fluid reg-img"
								:src="'/images/reg.jpg'"
								alt=""
								/>
							</div>
							<div class="col-md-6">
								<form class="row g-3 form-bg">
									<div class="col-12 mb-4">
										<input
										type="text"
										class="form-control custom-input"
										placeholder="First Name*"
										required
										/>
									</div>
									<div class="col-12 mb-4">
										<input
										type="text"
										class="form-control custom-input"
										placeholder="Last Name*"
										required
										/>
									</div>
									<div class="col-12 mb-4">
										<input
										type="email"
										class="form-control custom-input"
										placeholder="Email*"
										required
										/>
									</div>
									<div class="col-12 mb-4">
										<input
										type="text"
										class="form-control custom-input"
										placeholder="Company*"
										required
										/>
									</div>
									<div class="col-12 mb-5">
										<input
										type="tel"
										class="form-control custom-input"
										placeholder="Mobile Number*"
										required
										/>
									</div>
									<div class="col-12 mb-4">
										<h5>What's your situation?</h5>
										<div class="form-check">
											<input
											class="form-check-input"
											type="radio"
											name="flexRadioDefault"
											id="flexRadioDefault1"
											/>
											<label
											class="form-check-label"
											for="flexRadioDefault1"
											>
											I'm looking for space
										</label>
									</div>
									<div class="form-check">
										<input
										class="form-check-input"
										type="radio"
										name="flexRadioDefault"
										id="flexRadioDefault2"
										checked
										/>
										<label
										class="form-check-label"
										for="flexRadioDefault2"
										>
										I've got extra space
									</label>
								</div>
							</div>
							<div class="col-12 mb-3">
								<div class="form-check">
									<input
									class="form-check-input"
									type="checkbox"
									value=""
									id="flexCheckChecked"
									checked
									/>
									<label
									class="form-check-label"
									for="flexCheckChecked"
									>
									I have read and agree to PlaceHolder's Terms of
									Service and Privacy Policy
								</label>
							</div>
						</div>
						<div class="col-12 text-end">
							<a href="" class="more-btn w-100" type="submit"
							>SUBMIT</a
							>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div
		class="tab-pane fade"
		id="pills-profile"
		role="tabpanel"
		aria-labelledby="pills-profile-tab"
		>
		<div class="row">
			<div class="col-md-6">
				<form class="row g-3 form-bg">
					<div class="col-12 mb-4">
						<input
						type="text"
						class="form-control custom-input"
						placeholder="First Name*"
						required
						/>
					</div>
					<div class="col-12 mb-4">
						<input
						type="text"
						class="form-control custom-input"
						placeholder="Last Name*"
						required
						/>
					</div>
					<div class="col-12 mb-4">
						<input
						type="email"
						class="form-control custom-input"
						placeholder="Email*"
						required
						/>
					</div>
					<div class="col-12 mb-4">
						<input
						type="text"
						class="form-control custom-input"
						placeholder="Company*"
						required
						/>
					</div>
					<div class="col-12 mb-5">
						<input
						type="tel"
						class="form-control custom-input"
						placeholder="Mobile Number*"
						required
						/>
					</div>
					<div class="col-12 mb-4">
						<h5>What's your situation?</h5>
						<div class="form-check">
							<input
							class="form-check-input"
							type="radio"
							name="flexRadioDefault"
							id="flexRadioDefault1"
							/>
							<label
							class="form-check-label"
							for="flexRadioDefault1"
							>
							I'm looking for space
						</label>
					</div>
					<div class="form-check">
						<input
						class="form-check-input"
						type="radio"
						name="flexRadioDefault"
						id="flexRadioDefault2"
						checked
						/>
						<label
						class="form-check-label"
						for="flexRadioDefault2"
						>
						I've got extra space
					</label>
				</div>
			</div>
			<div class="col-12 mb-3">
				<div class="form-check">
					<input
					class="form-check-input"
					type="checkbox"
					value=""
					id="flexCheckChecked"
					checked
					/>
					<label
					class="form-check-label"
					for="flexCheckChecked"
					>
					I have read and agree to PlaceHolder's Terms of
					Service and Privacy Policy
				</label>
			</div>
		</div>
		<div class="col-12 text-end">
			<a href="" class="more-btn w-100" type="submit"
			>SUBMIT</a
			>
		</div>
	</form>
</div>
<div class="col-md-6 text-end">
	<img
	class="img-fluid reg-img"
	:src="'/images/reg.jpg'"
	alt=""
	/>
</div>
</div>
</div>
</div>
</form>
</div>
</div>
</div>
</section>
<!-- Registration Form End  -->
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