<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserHasPermission extends Model {	
    
    protected $table = 'user_has_permission';
    public $timestamps = false;

    public static function givePermissionToAll($permission_id){
            $users = GuiUser::all();
            foreach($users as $user){
                $permission = new Permission();
                $permission->user_id = $user->id;
                $permission->permission_id = $permission_id;
                $permission->save();
            }
    }
    
}