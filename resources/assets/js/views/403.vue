<template>  
    <div class="col-md-12 text-center">
        <span class="display-1 d-block">403</span>
        <div class="mb-4 lead">
        	You ain't authorized to view this page.
        </div>

		<router-link :to="{ name: 'home' }" class="btn btn-success">
			<span class="pcoded-mtext">
				<i class="fa fa-home" aria-hidden="true"></i>
				Go Home
			</span>
		</router-link>
    </div>   
</template>