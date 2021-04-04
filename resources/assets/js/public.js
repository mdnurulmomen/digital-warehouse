
// the route needs permission
const routeNeedsPermission = (record) => {
    return record.meta.hasOwnProperty('requiredPermission')
};

const hasPermissionThroughRole = (permissionName) => {
    
    let roles = JSON.parse(window.localStorage.getItem("roles"));

    return roles.some(

        userRole => {

            return userRole.permissions.some(permission => permission.name === permissionName);

        }

    );

}

const hasPermission = (permissionName) => {

    let permissions = JSON.parse(window.localStorage.getItem("permissions"));

    return permissions.some(
        permission => permission.name === permissionName
    );
    
}

const userHasPermissionTo = (permissionName) => {
    
    return hasPermissionThroughRole(permissionName) || hasPermission(permissionName);

}

// Exporting variables and functions
export { routeNeedsPermission, userHasPermissionTo };