<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    /**
     * The permissions that belong to the role.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'roles_permissions', 'permission_id', 'role_id');
    }

    /**
     * Get all of the managers that are assigned this permission.
     */
    public function managers()
    {
        return $this->morphedByMany(Manager::class, 'user', 'users_permissions');
    }

    /**
     * Get all of the owners that are assigned this permission.
     */
    public function owners()
    {
        return $this->morphedByMany(WarehouseOwner::class, 'user', 'users_permissions');
    }
}
