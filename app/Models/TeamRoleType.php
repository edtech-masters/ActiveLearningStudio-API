<?php

namespace App\Models;

use App\Models\Traits\GlobalScope;
use Illuminate\Database\Eloquent\Model;

class TeamRoleType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'display_name'
    ];

    /**
     * The permissions that belong to the role.
     */
    public function permissions()
    {
        return $this->belongsToMany('App\Models\TeamPermissionType', 'team_role_permissions', 'team_role_type_id', 'team_permission_type_id')->withTimestamps();
    }

}
