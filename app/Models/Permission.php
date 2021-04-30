<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    /**
     * Get all of the roles that has this permission.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_has_permissions', 'permission_id', 'role_id');
    }

    /**
     * Get all of the managers that are assigned this permission.
     */
    public function managers()
    {
        return $this->morphedByMany(Manager::class, 'model', 'model_has_permissions');
    }

    /**
     * Get all of the owners that are assigned this permission.
     */
    public function owners()
    {
        return $this->morphedByMany(WarehouseOwner::class, 'model', 'model_has_roles');
    }

    /**
     * Get all of the warehouses that are assigned this permission.
     */
    public function warehouses()
    {
        return $this->morphedByMany(Warehouse::class, 'model', 'model_has_roles');
    }

    /**
     * Get all of the merchants that are assigned this permission.
     */
    public function merchants()
    {
        return $this->morphedByMany(Merchant::class, 'model', 'model_has_roles');
    }

}
