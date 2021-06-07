<template>  
    <div class="col-md-12 text-center">
        <span class="display-1 d-block">404</span>
        <div class="mb-4 lead">The page you are looking for was not found.</div>
        <button @click="$router.go(-1)" class="btn btn-success">
			<span class="pcoded-mtext">
				<i class="fa fa-arrow-left" aria-hidden="true"></i>
				Go Back
			</span>
		</button>

		<router-link :to="{ name: 'home' }" class="btn btn-success">
			<span class="pcoded-mtext">
				<i class="fa fa-home" aria-hidden="true"></i>
				Go Home
			</span>
		</router-link>
    </div>   
</template>