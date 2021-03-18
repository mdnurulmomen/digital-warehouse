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
        return $this->belongsToMany(Role::class, 'role_has_permissions', 'permission_id', 'role_id');
    }

    /**
     * Get all of the managers that are assigned this permission.
     */
    public function managers()
    {
        return $this->morphedByMany(Manager::class, 'model', 'model_has_permissions');
    }

}
