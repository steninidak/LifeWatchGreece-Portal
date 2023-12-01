<?php

return array(
    'login'    =>  array(
                        'email'   =>  'required|email|max:50|exists:gui_users,email',
                        'password'   =>  'required|min:6|max:50',
                    ),
    'contact_us'    =>  array(
                        'subject'   =>  'required|min:2|max:150',
                        'related_to'=>  'required|max:50',
                        'message'   =>  'required|max:1000',
                        //'email'     =>  'required|email|max:50',
                        'captcha'   =>  'required|captcha',
    ),
    'registration'    =>  array(
                        'firstname' =>  'required|max:50',
                        'lastname'  =>  'required|max:50',
                        'email'     =>  'required|email|unique:gui_users',
                        'password'  =>  'required|min:6',
                        'verify_password'   =>  'required|same:password',
                        'affiliation'   =>  'max:250',
                        'position'      =>  'max:250',
                        'captcha'   =>  'required|captcha',
                    ),
    'password_reset_request'    =>  array(
                        'email'     =>  'required|email|exists:gui_users,email',
                        'captcha'   =>  'required|captcha',
    ),
    'password_reset'    =>  array(
                        'new_password'      =>  'required|min:6',
                        'repeat_password'   =>  'required|same:new_password',
    ),
    'add_announcement'  => array(
                        'title'         =>  'required|title|max:250',
                        'body'          =>  'required|max:2000',
                        'valid_from'    =>  'required|date_format:d/m/Y H:i',
                        'valid_to'      =>  'required|date_format:d/m/Y H:i',
    ),
    'add_one_time_message'  => array(
                        'type'         =>  'required||in:info,danger|max:50',
                        'body'          =>  'required|min:3|max:2000'
    ),
    'edit_one_time_message'  => array(
                        'message_id'    =>  'required|exists:one_time_message,id',
                        'type'          =>  'required||in:info,danger|max:50',
                        'body'          =>  'required|min:3|max:2000'
    ),
    'profile'       =>  array(
                        'affiliation'   =>  'max:150',
                        'position'      =>  'max:100',
                        'timezone'      =>  'required',
    ),
    'add_app'     =>    array(
                        'title'         =>  'required|title|max:100',
                        'description'   =>  'required',
                        'codename'      =>  'required|max:30|alpha_dash|unique:portal_apps',
                        'status'        =>  'required|max:20',
                        'url'           =>  'max:250|unique:portal_apps',
                        'ip'            =>  '',
                        'filename'      =>  'max:250|filename|unique:portal_apps,image',
                        'filesize'      =>  '1000000',
                        'filetype'      =>  'in:image/jpeg,image/jpg,image/png,image/gif',
                        'api_username'  =>  'required|min:6|max:30',
                        'api_password'  =>  'required|min:10|max:30',
                        'mobile_version'    =>  'software_version|max:20|required_with:mobile_app'
    ),
    'edit_app'     =>    array(
                        'title'         =>  'required|title|max:100',
                        'description'   =>  'required',
                        'status'        =>  'required|max:20',
                        'url'           =>  'max:250',
                        'ip'            =>  '',
                        'filename'      =>  'max:250|filename|unique:portal_apps,image',
                        'filesize'      =>  '1000000',
                        'filetype'      =>  'in:image/jpeg,image/jpg,image/png,image/gif',
                        'api_username'  =>  'min:6|max:30',
                        'api_password'  =>  'min:10|max:30',
                        'mobile_version'    =>  'software_version|max:20|required_with:mobile_app'
    ),
    'edit_announcement' =>  array(
                        'announcement_id'   =>  'required|exists:announcements,id',
                        'title'         =>  'required|title|max:250',
                        'body'          =>  'required|max:2000',
                        'valid_from'    =>  'required|date_format:d/m/Y H:i',
                        'valid_to'      =>  'required|date_format:d/m/Y H:i',
    ),
    'add_user'    =>  array(
                        'firstname' =>  'required|max:50',
                        'lastname'  =>  'required|max:50',
                        'email'     =>  'required|email|unique:gui_users',
                        'password'  =>  'required|min:6',
                        'verify_password'   =>  'required|same:password',
                    ),
    'add_group'    =>  array(
                        'name'      =>  'required|alpha_dash|max:50|unique:groups,name',
                        'description'  =>  'required|max:2000',
                    ),
    'add_permission'    =>  array(
                        'pname'      =>  'required|alpha_dash|max:50',
                        'description'  =>  'required|max:2000',
                    ),
    'add_member'    =>  array(
                        'group_id'  =>  'required|exists:groups,id',
                        'user_id'   =>  'required|exists:gui_users,id',
    ),
    'change_password'   =>  array(
                        'new_password'      =>  'required|min:6',
                        'repeat_password'   =>  'required|same:new_password',
    ),
    'remove_member'    =>  array(
                        'group_id'  =>  'required|exists:groups,id',
                        'user_id'   =>  'required|exists:gui_users,id',
    ),
    
    'update_group_permissions' =>  array(
                        'group_id'  =>  'required|exists:groups,id',                        
    ),
    'update_user_permissions' =>  array(
                        'user_id'  =>  'required|exists:gui_users,id',                        
    ),
    'delete_user' =>  array(
                        'delete_user_id'  =>  'required|exists:gui_users,id',                        
    ),
    'delete_announcement' =>  array(
                        'announcement_id'  =>  'required|exists:announcements,id',                        
    ),
    'delete_one_time_message' =>  array(
                        'message_id'  =>  'required|exists:one_time_message,id',                        
    ),
    'delete_app' =>  array(
                        'delete_app_id'  =>  'required|exists:portal_apps,id',                        
    ),
    'delete_group' =>  array(
                        'delete_group_id'  =>  'required|exists:groups,id',                        
    ),
    'delete_permission' =>  array(
                        'delete_permission_id'  =>  'required|exists:permissions,id',                        
    ),
    'remove_group' =>  array(
                        'user_id'  =>  'required|exists:gui_users,id',  
                        'group_id'  =>  'required|exists:groups,id',
    ),
    'add_group_to_user' =>  array(
                        'user_id'  =>  'required|exists:gui_users,id',  
                        'group_name'  =>  'required|exists:groups,name',
    ),
    'flash_to_session'  =>  array(
                        'session_var'   =>  'required|max:100',
                        'session_data'  =>  'required|max:1000'
    ),
    'biospec_subscribe' =>  array(
                        'fullname'  =>  'required|max:100',
                        'email'     =>  'required|email',
                        'captcha'   =>  'required|captcha'
    ),
    'biospec_unsubscribe'   =>  array(
                        'fullname'  =>  'required|max:100',
                        'email'     =>  'required|email',
                        'captcha'   =>  'required|captcha'
    ),
    'orcid_profile' =>  array(
                        'id'    =>  'required|min:2|max:50',
                        'email' =>  'required|email|max:100',
                        'firstname' =>  'required|min:2|max:50',
                        'lastname'  =>  'required|min:2|max:100'
    )
);