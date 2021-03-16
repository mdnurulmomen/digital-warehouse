<?php
 
namespace App\Traits;
 
trait StoreImageTrait {
 
    public function givePermissionsTo(... $permissions) {
	
    	$expectedPermissions = $this->getAllPermissions($permissions);
    	$this->permissions()->attach($expectedPermissions);
    	return $this->permissions;

	}

	public function withdrawPermissionsTo( ... $permissions ) {

		$expectedPermissions = $this->getAllPermissions($permissions);
    	$this->permissions()->detach($expectedPermissions);
    	return $this->permissions;
	}

	public function refreshPermissions( ... $permissions ) {

		
	}

	public function hasPermissionTo($permission) {

		return $this->hasPermissionThroughRole($permission) || $this->hasPermission($permission);
	
	}

	public function hasPermissionThroughRole($permission) {

		foreach ($this->roles as $userRoleIndex => $userRole) {
			
			if ($userRole->permissions()->where('slug', $permission)->count()) {
				
				return true;

			}

		}

		return false;
	
	}

	public function hasRole( ... $roles ) {

		foreach ($roles as $roleIndex => $role) {
			
			if ($this->roles()->where('slug', $role)) {
				return true;
			}

			return false;

		}
	
	}

	public function roles() {

		return $this->morphToMany(Role::class, 'user', 'users_roles');

	}

	public function permissions() {

		return $this->morphToMany(Permission::class, 'user', 'users_permissions');

	}

	protected function hasPermission($permission) {

		return (bool) $this->permissions()->where('slug', $permission)->count();
	
	}

	protected function getAllPermissions(array $permissions) {

		$expectedPermissions = Permission::whereIn('slug', $permissions)->get()->pluck("id");
		return $expectedPermissions;

	}
 
}