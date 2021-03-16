<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    /**
     * The permissions that belong to the role.
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'roles_permissions', 'role_id', 'permission_id');
    }

    /**
     * Get all of the managers that are assigned this role.
     */
    public function managers()
    {
        return $this->morphedByMany(Manager::class, 'user', 'users_roles');
    }

    /**
     * Get all of the owners that are assigned this role.
     */
    public function owners()
    {
        return $this->morphedByMany(WarehouseOwner::class, 'user', 'users_roles');
    }
}
