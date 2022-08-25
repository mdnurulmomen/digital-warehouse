
<script>
	
	// define a mixin object
	export default {

		data() {

			return {

				roles : JSON.parse(window.localStorage.getItem('roles')) ?? [],
				permissions : JSON.parse(window.localStorage.getItem('permissions')) ?? [],

			}

		},

		methods: {

			routeNeedsPermission() {

				return this.$route.meta.hasOwnProperty('requiredPermission');

			},
			hasPermissionThroughRole(permissionName) {

				return this.roles.some(

			        userRole => {

			            return userRole.permissions.some(permission => permission.name === permissionName);

			        }

			    );

			},
			hasPermission(permissionName) {

			    return this.permissions.some(
			        permission => permission.name === permissionName
			    );

			},
			userHasPermissionTo(permissionName) {

				return this.hasPermissionThroughRole(permissionName) || this.hasPermission(permissionName);
				
			},

		}

    }

</script>