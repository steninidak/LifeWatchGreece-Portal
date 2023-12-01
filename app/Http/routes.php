<?php

// API-authenticated routes
Route::group(['prefix'=>'api/v1/session','middleware' => ['myapi']], function () {

    Route::get('has_permission/{permission_name}',array('uses'=>'SessionController@has_permission'))->where('app_name', '[\pL\pM\pN_-]+');
    Route::get('test/{codename}',array('uses'=>'SessionController@test'));
    Route::post('flash',array('uses'=>'SessionController@flash'));

});

// Routes for visitors
Route::group(['middleware' => ['visitor']], function () {

    // Landing page
    Route::get('/', array('uses'=>'VisitorController@landing_page'));
    Route::post('login',array('uses'=>'VisitorController@login'));

    // Registration
    Route::get('register',array('uses'=>'VisitorController@register_page'));
    Route::post('register',array('uses'=>'VisitorController@register'));

    Route::get('registration/verify/{code}', array('https','uses' => 'VisitorController@email_verification'));
    Route::get('post_verification', array('https','uses' => 'VisitorController@verification_message'));
    Route::get('successful_registration', array('https','uses' => 'VisitorController@registration_message'));

    // Password Reset
    Route::get('password_reset_request',array('uses'=>'VisitorController@password_reset_request'));
    Route::post('password_reset_request',array('uses'=>'VisitorController@send_reset_link'));
    Route::get('reset_link_sent',array('uses'=>'VisitorController@reset_link_sent'));
    Route::get('password_reset/{code}', array('https','uses' => 'VisitorController@set_password_page'));
    Route::post('password_reset/{code}', array('https','uses' => 'VisitorController@set_password'));

    // Mobile routes
    Route::post('mobile/login',array('before'=>'csrf','uses'=>'MobileController@login1'));

});

// Public routes
Route::group(['middleware' => ['myweb']], function () {

    // Open Routes
    Route::get('new_captcha_link', array('https','uses' => 'VisitorController@new_captcha_image_link'));
    Route::get('orcid/howto',array('uses'=>'OpenController@orcid_howto'));
    Route::get('mobile_apps',array('uses'=>'OpenController@mobile'));
    Route::get('mobile_apps/video',array('uses'=>'OpenController@mobile_video'));
    Route::get('biospec',array('uses'=>'OpenController@biospec'));
    Route::post('biospec/subscribe',array('uses'=>'OpenController@biospec_subscribe'));
    Route::post('biospec/unsubscribe',array('uses'=>'OpenController@biospec_unsubscribe'));

    // ORCID Authentication
    Route::post('orcid_login',array('uses'=>'OrcidController@orcid_login'));
    Route::get('orcid_auth',array('uses'=>'OrcidController@orcid_auth'));

    // Mobile Routes
    Route::get('mobile',array('https','uses'=>'MobileController@index'));
    Route::get('mobile/get_token',array('https','uses'=>'MobileController@get_token'));
    Route::get('mobile/get_mobile_versions',array('https','uses'=>'MobileController@get_mobile_versions'));

});

// Web-authenticated routes
Route::group(['middleware' => ['myweb','auth:web']], function () {

     // Basic Routes
    Route::get('/home', array('uses'=>'LoggedController@home'));
    Route::get('profile',array('uses'=>'LoggedController@profile'));
    Route::post('profile',array('uses'=>'LoggedController@update_profile'));
    Route::post('change_password',array('uses'=>'LoggedController@change_password'));
    Route::get('logout',array('uses'=>'LoggedController@logout'));
    Route::get('contact_us',array('uses'=>'LoggedController@contact_us'));
    Route::post('contact_us',array('uses'=>'LoggedController@contacted'));
    Route::get('successful_contact',array('uses'=>'LoggedController@successful_contact'));
    Route::get('failed_contact',array('uses'=>'LoggedController@failed_contact'));

    // Admin Area
    Route::get('admin/traffic/{app_name?}',array('uses'=>'AdminController@traffic'));
    Route::get('admin/logs/error',array('uses'=>'AdminController@error_logs'));
    Route::get('admin/logs/security',array('uses'=>'AdminController@security_logs'));
    Route::get('admin/logs/registration',array('uses'=>'AdminController@registration_logs'));
    Route::get('admin',array('uses'=>'AdminController@control_panel'));
    Route::get('admin/biocluster/user/{username}',array('uses'=>'AdminController@biocluster_user'));
    Route::get('admin/biocluster',array('uses'=>'AdminController@biocluster'));

    // User management
    Route::get('admin/user_management',array('uses'=>'AdminController@user_management'));
    Route::post('admin/user_management/add',array('uses'=>'AdminController@add_user'));
    Route::get('admin/user_management/enable/{userId}',array('uses'=>'AdminController@enable_user'))->where('userId', '[0-9]+');
    Route::get('admin/user_management/disable/{userId}',array('uses'=>'AdminController@disable_user'))->where('userId', '[0-9]+');
    Route::get('admin/user_management/edit/{userId}',array('uses'=>'AdminController@user_profile_management'))->where('userId', '[0-9]+');
    Route::post('admin/user_management/update_user_permissions',array('uses'=>'AdminController@update_user_permissions'));
    Route::post('admin/user_management/delete',array('uses'=>'AdminController@delete_user'));
    Route::post('admin/user_management/remove_group',array('uses'=>'AdminController@remove_group_from_user'));
    Route::post('admin/user_management/add_group',array('uses'=>'AdminController@add_group_to_user'));

    // Group management
    Route::get('admin/group_management',array('uses'=>'AdminController@group_management'));
    Route::post('admin/group_management/add',array('uses'=>'AdminController@add_group'));
    Route::post('admin/group_management/remove_member',array('uses'=>'AdminController@remove_member'));
    Route::get('admin/group_management/{groupName}',array('uses'=>'AdminController@group_profile'))->where('groupName', '[\pL\pM\pN_-]+');
    Route::post('admin/group_management/add_member',array('uses'=>'AdminController@add_member'));
    Route::post('admin/group_management/update_group_permissions',array('uses'=>'AdminController@update_group_permissions'));
    Route::post('admin/group_management/delete',array('uses'=>'AdminController@delete_group'));

    //Permission Management
    Route::get('admin/permission_management',array('uses'=>'AdminController@permission_management'));
    Route::post('admin/permission_management/add',array('uses'=>'AdminController@add_permission'));
    Route::get('admin/permission_management/edit/{permId}',array('uses'=>'AdminController@edit_permission'));
    Route::post('admin/permission_management/delete',array('uses'=>'AdminController@delete_permission'));

    // Manage system settings
    Route::get('admin/system_settings',array('uses'=>'AdminController@system_settings'));
    Route::post('admin/save_settings',array('uses'=>'AdminController@save_system_settings'));

    // Application Management
    Route::post('admin/app_management/validate',array('uses'=>'AdminController@validate_app_info'));
    Route::post('admin/app_management/add',array('uses'=>'AdminController@add_app'));
    Route::get('admin/app_management/profile/{app_name}',array('uses'=>'AdminController@app_profile'))->where('app_name', '[\pL\pM\pN_-]+');
    Route::post('admin/app_management/profile/{app_name}',array('uses'=>'AdminController@edit_app'))->where('app_name', '[\pL\pM\pN_-]+');
    Route::post('admin/app_management/delete',array('uses'=>'AdminController@delete_app'));
    Route::get('admin/app_management',array('uses'=>'AdminController@app_management'));

    // Announcements Management
    Route::get('admin/announcements',array('uses'=>'AnnouncementController@manage'));
    Route::get('admin/announcements/all',array('uses'=>'AnnouncementController@show_all'));
    Route::get('admin/announcements/add',array('uses'=>'AnnouncementController@add_page'));
    Route::get('admin/announcements/edit/{item_id}',array('uses'=>'AnnouncementController@edit_page'))->where('item_id', '[0-9]+');
    Route::post('admin/announcements/add',array('uses'=>'AnnouncementController@add'));
    Route::post('admin/announcements/edit',array('uses'=>'AnnouncementController@edit'));
    Route::post('admin/announcements/delete',array('uses'=>'AnnouncementController@delete'));

    // One-time message Management
    Route::get('admin/ome_time_messages',array('uses'=>'OneTimeMessageController@manage'));
    Route::get('admin/ome_time_messages/add',array('uses'=>'OneTimeMessageController@add_page'));
    Route::post('admin/ome_time_messages/add',array('uses'=>'OneTimeMessageController@add'));
    Route::get('admin/ome_time_messages/edit/{item_id}',array('uses'=>'OneTimeMessageController@edit_page'))->where('item_id', '[0-9]+');
    Route::post('admin/ome_time_messages/edit',array('uses'=>'OneTimeMessageController@edit'));
    Route::post('admin/ome_time_messages/delete',array('uses'=>'OneTimeMessageController@delete'));

});
