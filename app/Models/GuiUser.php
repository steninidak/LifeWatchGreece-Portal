<?php

namespace App\Models;

use DB;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
* Domain
*/
class GuiUser extends Authenticatable {

    /**
   * Table name.
   *
   * @var string
   */
    protected $table = 'gui_users';
    public $timestamps = false;
  
    /**
    * Retrieves the names of the groups where a user belongs to 
    * 
    * @param int $user_id
    * @return array
    */
    static function getUserGroupNames($user_id){
        $user_groups = Group::join('user_in_group AS uig','uig.group_id','=','groups.id')
                ->where('uig.user_id',$user_id)
                ->select('groups.name')
                ->get()->toArray();

        return flatten($user_groups);
    }

    /**
     * Retrieves the groups where a user belongs to 
     * 
     * @param int $user_id
     * @return array
     */
    static function getUserGroups($user_id){
        $user_groups = Group::join('user_in_group AS uig','uig.group_id','=','groups.id')
                ->where('uig.user_id',$user_id)
                ->select('groups.id','groups.name','groups.description')
                ->get();

        return flatten($user_groups);
    }

    /**
     * Retrieves all the groups where a user does not belong to
     * 
     * @param int $user_id
     * @return array
     */
    static function getUserRemainingGroups($user_id){
        $user_group_ids = UserInGroup::where('user_id',$user_id)
                        ->select('group_id')->get()->toArray();
        if(empty($user_group_ids)){
            $rest_groups = Group::select('name')->get();
        } else {
            $rest_groups = Group::whereNotIn('id',$user_group_ids)
                ->select('name')
                ->get();
        }

        return $rest_groups;
    }

    /**
     * Retrieves all the user's effective privileges (just the names)
     * 
     * @param int $user_id
     * @return array
     */
    static function getUserPrivileges($user_id){

        $user_group_ids = Group::join('user_in_group AS uig','uig.group_id','=','groups.id')
                    ->where('uig.user_id',$user_id)
                    ->select('groups.id')
                    ->get()->toArray();

        $user_priviledges = Permission::join('user_has_permission AS uhp','uhp.permission_id','=','permissions.id')
                    ->where('uhp.user_id',$user_id)
                    ->select('permissions.name')
                    ->get()->toArray();

        if(!empty($user_group_ids)){
            $group_privileges = Permission::join('group_has_permission AS ghp','permissions.id','=','ghp.permission_id')
                    ->whereIn('ghp.group_id',$user_group_ids)
                    ->select('permissions.name')
                    ->get()->toArray();

            return array_unique(array_merge(flatten($user_priviledges),flatten($group_privileges)));
        } else {            
            return flatten($user_priviledges);
        }            

    }

    /**
     * Retrieves all the user's effective privileges (just the names) that are related to a certain app
     * 
     * @param int $user_id
     * @param string $app
     * @return array
     */
    static function getUserAppPrivileges($user_id,$app){

        $user_group_ids = Group::join('user_in_group AS uig','uig.group_id','=','groups.id')
                    ->where('uig.user_id',$user_id)
                    ->select('groups.id')
                    ->get()->toArray();

        $user_priviledges = Permission::join('user_has_permission AS uhp','uhp.permission_id','=','permissions.id')
                    ->where('uhp.user_id',$user_id)
                    ->where('permissions.used_by','=',$app)
                    ->select('permissions.name')
                    ->get()->toArray();

        if(!empty($user_group_ids)){
            $group_privileges = Permission::join('group_has_permission AS ghp','permissions.id','=','ghp.permission_id')
                    ->whereIn('ghp.group_id',$user_group_ids)
                    ->where('permissions.used_by','=',$app)
                    ->select('permissions.name')
                    ->get()->toArray();                            

            return array_unique(array_merge(flatten($user_priviledges),flatten($group_privileges)));
        } else {            
            return flatten($user_priviledges);
        }            

    }

    /**
     * Retrieves a list with the id and the full name of all the registered users
     * 
     * @return object
     */
    public static function getUserList(){
        return GuiUser::select('id',DB::raw('CONCAT(lastname," ",firstname, " (", email, ")") AS fullname_email'))
                ->orderBy('fullname_email','ASC')
                ->get();
    }

    /**
     * Retrieves a list with ids of the permissions that a user has
     * 
     * @param int $user_id
     * @return array
     */
    public static function getPermissionIds($user_id){
        $user_permission_ids = Permission::join('user_has_permission AS uhp','uhp.permission_id','=','permissions.id')
                                ->where('uhp.user_id',$user_id)
                                ->select('permissions.id')
                                ->get()->toArray();

        return $user_permission_ids;
    }
  
}

