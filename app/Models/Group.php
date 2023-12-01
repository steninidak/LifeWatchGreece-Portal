<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model {	
    
    protected $table = 'groups';
    public $timestamps = false;
    
    public static function getMembers($group_id){
        $users = GuiUser::join('user_in_group AS uig','gui_users.id','=','uig.user_id')
                ->where('uig.group_id',$group_id)
                ->select('gui_users.id','gui_users.email','gui_users.firstname','gui_users.lastname')
                ->get();
        
        return $users;
    }
    
    public static function getPermissionIds($group_id){
        $group_permissions = Permission::join('group_has_permission AS ghp','ghp.permission_id','=','permissions.id')
                                ->where('ghp.group_id',$group_id)
                                ->select('permissions.id')
                                ->get()->toArray();
        
        return $group_permissions;
    }
    
}