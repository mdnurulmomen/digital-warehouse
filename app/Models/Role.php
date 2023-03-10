<?php

namespace App\Models;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['permissions'];

    /**
     * The permissions that belong to the role.
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions', 'role_id', 'permission_id');
    }

    /**
     * Get all of the managers that are assigned this role.
     */
    public function managers()
    {
        return $this->morphedByMany(Manager::class, 'model', 'model_has_roles');
    }

    /**
     * Get all of the owners that are assigned this role.
     */
    public function owners()
    {
        return $this->morphedByMany(WarehouseOwner::class, 'model', 'model_has_roles');
    }

    /**
     * Get all of the warehouses that are assigned this role.
     */
    public function warehouses()
    {
        return $this->morphedByMany(Warehouse::class, 'model', 'model_has_roles');
    }

    /**
     * Get all of the merchants that are assigned this role.
     */
    public function merchants()
    {
        return $this->morphedByMany(Merchant::class, 'model', 'model_has_roles');
    }

    public function setRolePermissionsAttribute($permissions = [])
    {
        if (count($permissions)) {
            
            $this->permissions()->detach();
            $this->permissions()->attach(Arr::pluck($permissions, 'id'));

        }
    }

}
