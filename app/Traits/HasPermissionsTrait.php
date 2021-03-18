<?php
 
namespace App\Traits;

use App\Models\Role;
use App\Models\Permission;
 
trait HasPermissionTrait {
 
    public function givePermissionsTo(... $permissions) {
	
    	$expectedPermissions = $this->getExpectedPermissions($permissions);
    	$this->permissions()->attach($expectedPermissions);
    	return $this->permissions;

	}

	public function withdrawPermissionsTo( ... $permissions ) {

		$expectedPermissions = $this->getExpectedPermissions($permissions);
    	$this->permissions()->detach($expectedPermissions);
    	return $this->permissions;
	}

	public function assignRole(... $roles)
	{
		$expectedRoles = $this->getExpectedRoles($roles);
    	$this->roles()->attach($expectedRoles);
    	return $this->permissions;
	}

	public function refreshPermissions( ... $permissions ) {

		$this->permissions()->detach();
		$this->givePermissionsTo($permissions);
		
	}

	// if has any of given roles
	public function hasRole( ... $roles ) {

		foreach ($roles as $roleIndex => $role) {
			
			if ($this->roles()->where('name', $role)->count()) {
				return true;
			}

			return false;

		}
	
	}

	public function roles() {

		return $this->morphToMany(Role::class, 'model', 'model_has_roles');

	}

	public function permissions() {

		return $this->morphToMany(Permission::class, 'model', 'model_has_permissions');

	}

	public function hasPermissionTo($permission) {

		return $this->hasPermissionThroughRole($permission) || $this->hasPermission($permission);
	
	}

	protected function hasPermissionThroughRole($permission) {

		foreach ($this->roles as $userRoleIndex => $userRole) {
			
			if ($userRole->permissions()->where('name', $permission)->count()) {
				
				return true;

			}

		}

		return false;
	
	}

	protected function hasPermission($permission) {

		return (bool) $this->permissions()->where('name', $permission)->count();
	
	}

	protected function getExpectedPermissions(array $permissions) {

		$expectedPermissions = Permission::whereIn('name', $permissions)->get()->pluck("id");
		return $expectedPermissions;

	}

	protected function getExpectedRoles(array $roles) {

		$expectedPermissions = Role::whereIn('name', $roles)->get()->pluck("id");
		return $expectedPermissions;

	}
 
}