<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use HTML;
use Hash;
use Redirect;
use Session;
use View;
use Input;
use Config;
use Validator;
use Response;
use DateTime;
use App\Models\Setting;
use App\Models\Group;
use App\Models\GuiUser;
use App\Models\ApiUser;
use App\Models\PortalApp;
use App\Models\SessionLog;
use App\Models\SystemLog;
use App\Models\Permission;
use App\Models\QueueStat;
use App\Models\ResourceLog;
use App\Models\UserHasPermission;
use App\Models\GroupHasPermission;
use App\Models\UserInGroup;
use phpseclib\Net\SSH2;
/*
 * A controller that implements administration functionality.
 * 
 * @author   Alexandros Gougousis
 */
class AdminController extends WebController {
    
    public function __construct() {
        parent::__construct();
        $this->template_view = 'internal_wrapper.template';
        $this->view_head = 'internal_wrapper.head';
        $this->view_body_top = 'internal_wrapper.body_top';
        $this->view_body_bottom = 'internal_wrapper.body_bottom';
    }
    
    public function biocluster_user($username){
        
        if(hasPermission('view_system_info')){
           $one_month_ago = date("Y-m-d H:m:s",strtotime("-1 month")); 
        
            if($username == 'total'){
                $fast_queue_last_month = QueueStat::where('queue_type','fast')
                                    ->select(DB::raw('SUM(jobs) as total_jobs'))
                                    ->where('when','>',$one_month_ago)
                                    ->groupBy('when')
                                    ->orderBy('when','ASC')
                                    ->get()->toArray();

                $batch_queue_last_month = QueueStat::where('queue_type','batch')
                                    ->select(DB::raw('SUM(jobs) as total_jobs'))
                                    ->where('when','>',$one_month_ago)
                                    ->groupBy('when')
                                    ->orderBy('when','ASC')
                                    ->get()->toArray();
                $bigmem_queue_last_month = QueueStat::where('queue_type','bigmem')
                                    ->select(DB::raw('SUM(jobs) as total_jobs'))
                                    ->where('when','>',$one_month_ago)
                                    ->groupBy('when')
                                    ->orderBy('when','ASC')
                                    ->get()->toArray();
            } else {
                $fast_queue_last_month = QueueStat::where('queue_type','fast')
                                    ->select(DB::raw('MAX(jobs) as total_jobs'))
                                    ->where('when','>',$one_month_ago)
                                    ->whereIn('user',array($username,'-baseline-'))
                                    ->groupBy('when')
                                    ->orderBy('when','ASC')
                                    ->get()->toArray();

                $batch_queue_last_month = QueueStat::where('queue_type','batch')
                                    ->select(DB::raw('MAX(jobs) as total_jobs'))
                                    ->where('when','>',$one_month_ago)
                                    ->whereIn('user',array($username,'-baseline-'))
                                    ->groupBy('when')
                                    ->orderBy('when','ASC')
                                    ->get()->toArray();
                $bigmem_queue_last_month = QueueStat::where('queue_type','bigmem')
                                    ->select(DB::raw('MAX(jobs) as total_jobs'))
                                    ->where('when','>',$one_month_ago)
                                    ->whereIn('user',array($username,'-baseline-'))
                                    ->groupBy('when')
                                    ->orderBy('when','ASC')
                                    ->get()->toArray();
            }                

            $older_fast_entry = QueueStat::where('queue_type','fast')->select('when')->where('when','>',$one_month_ago)->orderBy('when','ASC')->first();
            $older_batch_entry = QueueStat::where('queue_type','batch')->select('when')->where('when','>',$one_month_ago)->orderBy('when','ASC')->first();
            $older_bigmem_entry = QueueStat::where('queue_type','bigmem')->select('when')->where('when','>',$one_month_ago)->orderBy('when','ASC')->first();        

            $dateFormat = 'Y-m-d H:i:s';
            $fast_date = DateTime::createFromFormat($dateFormat, $older_fast_entry->when);
            $batch_date = DateTime::createFromFormat($dateFormat, $older_batch_entry->when);
            $bigmem_date = DateTime::createFromFormat($dateFormat, $older_bigmem_entry->when);

            $data['fast_start_year'] = $fast_date->format('Y');
            $data['fast_start_month'] = $fast_date->format('m');
            $data['fast_start_day'] = $fast_date->format('d');

            $data['batch_start_year'] = $batch_date->format('Y');
            $data['batch_start_month'] = $batch_date->format('m');
            $data['batch_start_day'] = $batch_date->format('d');

            $data['bigmem_start_year'] = $bigmem_date->format('Y');
            $data['bigmem_start_month'] = $bigmem_date->format('m');
            $data['bigmem_start_day'] = $bigmem_date->format('d');

            $response = array(
                'fast'  => array_map('intval',array_flatten($fast_queue_last_month)),
                'batch'  => array_map('intval',array_flatten($batch_queue_last_month)),
                'bigmem'  => array_map('intval',array_flatten($bigmem_queue_last_month)),            
                'fast_start_year'  =>  $fast_date->format('Y'),
                'fast_start_month'  =>  $fast_date->format('m'),
                'fast_start_day'  =>  $fast_date->format('d'),
                'batch_start_year'  =>  $fast_date->format('Y'),
                'batch_start_month'  =>  $fast_date->format('m'),
                'batch_start_day'  =>  $fast_date->format('d'),
                'bigmem_start_year'  =>  $fast_date->format('Y'),
                'bigmem_start_month'  =>  $fast_date->format('m'),
                'bigmem_start_day'  =>  $fast_date->format('d'),
            );   
            return response()->json($response)->setStatusCode(200,'');
        } else {
            return response()->json($response)->setStatusCode(403,'You are not allowed to access cluster statistics!');
        }                                  
        
    }
    
    public function biocluster(){
        
        if(hasPermission('view_system_info')){
            $one_month_ago = date("Y-m-d H:m:s",strtotime("-1 month"));               
        
            $users = QueueStat::where('when','>',$one_month_ago)
                    ->select('user')
                    ->where('user','<>','-baseline-')
                    ->distinct()->get()->toArray();

            $fast_queue_last_month = QueueStat::where('queue_type','fast')
                                    ->select(DB::raw('SUM(jobs) as total_jobs'))
                                    ->where('when','>',$one_month_ago)
                                    ->groupBy('when')
                                    ->orderBy('when','ASC')
                                    ->get()->toArray();
            
            $batch_queue_last_month = QueueStat::where('queue_type','batch')
                                    ->select(DB::raw('SUM(jobs) as total_jobs'))
                                    ->where('when','>',$one_month_ago)
                                    ->groupBy('when')
                                    ->orderBy('when','ASC')
                                    ->get()->toArray();

            $bigmem_queue_last_month = QueueStat::where('queue_type','bigmem')
                                    ->select(DB::raw('SUM(jobs) as total_jobs'))
                                    ->where('when','>',$one_month_ago)
                                    ->groupBy('when')
                                    ->orderBy('when','ASC')
                                    ->get()->toArray();

            $fast_utilization = ResourceLog::where('queue_type','fast')
                                    ->select('utilization')
                                    ->where('when','>',$one_month_ago)
                                    ->orderBy('when','ASC')
                                    ->get()->toArray();

            $batch_utilization = ResourceLog::where('queue_type','batch')
                                    ->select('utilization')
                                    ->where('when','>',$one_month_ago)
                                    ->orderBy('when','ASC')
                                    ->get()->toArray();

            $bigmem_utilization = ResourceLog::where('queue_type','bigmem')
                                    ->select('utilization')
                                    ->where('when','>',$one_month_ago)
                                    ->orderBy('when','ASC')
                                    ->get()->toArray();

            $data['total_fast'] = implode(',',array_flatten($fast_queue_last_month));
            $data['total_batch'] = implode(',',array_flatten($batch_queue_last_month));
            $data['total_bigmem'] = implode(',',array_flatten($bigmem_queue_last_month));
            $data['fast_utilization'] = implode(',',array_flatten($fast_utilization));
            $data['batch_utilization'] = implode(',',array_flatten($batch_utilization));
            $data['bigmem_utilization'] = implode(',',array_flatten($bigmem_utilization));
            $data['user_list'] = array_flatten($users);

            $older_fast_entry = QueueStat::where('queue_type','fast')->select('when')->where('when','>',$one_month_ago)->orderBy('when','ASC')->first();
            $older_batch_entry = QueueStat::where('queue_type','batch')->select('when')->where('when','>',$one_month_ago)->orderBy('when','ASC')->first();
            $older_bigmem_entry = QueueStat::where('queue_type','bigmem')->select('when')->where('when','>',$one_month_ago)->orderBy('when','ASC')->first();           
            
            $older_fast_util = ResourceLog::where('queue_type','fast')->select('when')->where('when','>',$one_month_ago)->orderBy('when','ASC')->first();        
            $older_batch_util = ResourceLog::where('queue_type','fast')->select('when')->where('when','>',$one_month_ago)->orderBy('when','ASC')->first();        
            $older_bigmem_util = ResourceLog::where('queue_type','fast')->select('when')->where('when','>',$one_month_ago)->orderBy('when','ASC')->first();

            $dateFormat = 'Y-m-d H:i:s';
            $fast_date = DateTime::createFromFormat($dateFormat, $older_fast_entry->when);
            $batch_date = DateTime::createFromFormat($dateFormat, $older_batch_entry->when);
            $bigmem_date = DateTime::createFromFormat($dateFormat, $older_bigmem_entry->when);            
            
            $fast_util_date = DateTime::createFromFormat($dateFormat, $older_fast_util->when);
            $batch_util_date = DateTime::createFromFormat($dateFormat, $older_batch_util->when);
            $bigmem_util_date = DateTime::createFromFormat($dateFormat, $older_bigmem_util->when);

            $data['fast_start_year'] = $fast_date->format('Y');
            $data['fast_start_month'] = $fast_date->format('m');
            $data['fast_start_day'] = $fast_date->format('d');

            $data['batch_start_year'] = $batch_date->format('Y');
            $data['batch_start_month'] = $batch_date->format('m');
            $data['batch_start_day'] = $batch_date->format('d');

            $data['bigmem_start_year'] = $bigmem_date->format('Y');
            $data['bigmem_start_month'] = $bigmem_date->format('m');
            $data['bigmem_start_day'] = $bigmem_date->format('d');

            $data['fast_util_year'] = $fast_util_date->format('Y');
            $data['fast_util_month'] = $fast_util_date->format('m');
            $data['fast_util_day'] = $fast_util_date->format('d');

            $data['batch_util_year'] = $batch_util_date->format('Y');
            $data['batch_util_month'] = $batch_util_date->format('m');
            $data['batch_util_day'] = $batch_util_date->format('d');

            $data['bigmem_util_year'] = $bigmem_util_date->format('Y');
            $data['bigmem_util_month'] = $bigmem_util_date->format('m');
            $data['bigmem_util_day'] = $bigmem_util_date->format('d');           

            $content = View::make('admin.queue_stats',$data);
            return $this->load_view('',$content);
        } else {
            return $this->unauthorized();
        }                
        
    }
    
    /*
     * Displays a dashboard for the administrator.
     * 
     * @return string
     */
    public function control_panel(){
        
        $data = array();
        
        // Who is online
        $data['online_users'] = GuiUser::where('last_activity','>',strtotime("-10 minutes"))->get();
        
        // Number of unique IPs per application
        $unique_visitors = DB::table('session_logs')
                    ->select(DB::raw('count(distinct ip) as counter,app'))
                    ->groupBy('app')
                    ->get();        
        
        $uniques = array(
            'apps'  =>  array(),
            'counts'=>  array()
        );
        foreach($unique_visitors as $visitItem){
            $uniques['apps'][] = $visitItem->app;
            $uniques['counts'][] = $visitItem->counter;
        }
        
        $data['unique_visitors'] = $uniques;
        
        // How the number of registered users has been evolved during the last 6 months
        $now = date("Y-m-d H:m:s");
        $count_months = 12;
        $start_dates = array();
        $end_dates = array();
        $app_traffic = array();
        $app_list = flatten(PortalApp::select('codename')->where('status','<>','developing')->get()->toArray());
        
        $data['count_months'] = $count_months;
        
        for($i = 0; $i < $count_months; $i++){
            if($i == 0){
                $firstday = "$now first day of this month";
                $lastday = "$now last day of this month";
            } else {
                $firstday = "$now first day of last month";
                $lastday = "$now last day of last month";
            }           
            $first_date = date_create($firstday);
            $last_date = date_create($lastday);
            $start_dates[] = $first_date->format('d-m-Y');
            $end_dates[] = $last_date->format('d-m-Y');
            $now = $first_date->format('Y-m-d H:m:s');
            $month_name = $first_date->format('M');

            $total_traffic[$i] = SessionLog::where('created_at','<',$last_date)
                                        ->where('created_at','>',$first_date)
                                        ->count();
            
            // Find positive traffic for each application
            $app_monthly_traffic[$i] = DB::table('session_logs')
                                        ->select(DB::raw('count(*) as counter,app'))
                                        ->where('created_at','<',$last_date)
                                        ->where('created_at','>',$first_date)
                                        ->groupBy('app')
                                        ->get();     
            
            // Initialize the values for this month (in case there is no traffic 
            // for some apps, the array slot should be filled manually) 
            foreach($app_list as $app){
                $per_app_traffic[$app][$i] = "0";
            }
            
            // If there is traffic for an app, update the relevant array slot
            foreach($app_monthly_traffic[$i] as $monthly_traffic){
                $per_app_traffic[$monthly_traffic->app][$i] =  $monthly_traffic->counter;
            }                        
            
            $registered_value[$i] = GuiUser::where('created_at','<',$last_date)->count();
            $registered_month[$i] = $month_name;
        }                                               
            
        // Reversing the order of months
        $data['registered_value'] = array_reverse($registered_value);
        $data['registered_month'] = array_reverse($registered_month);
        foreach($app_list as $app){
            $per_app_traffic[$app] = array_reverse($per_app_traffic[$app]);
        }
        
        $data['total_traffic'] = array_reverse($total_traffic);
        $data['per_app_traffic'] = $per_app_traffic;
        
        // Number of important logs that took place during last week (+ when the most recent happened)
        $logs = array();
        $one_week_ago = date("Y-m-d H:m:s",strtotime("-1 week"));               
        
        $week_error_logs_query = SystemLog::where('category','error')
                                ->where('when','>',$one_week_ago)
                                ->orderBy('when','DESC');                                        
        $week_security_logs_query = SystemLog::where('category','security')
                                ->where('when','>',$one_week_ago)
                                ->orderBy('when','DESC'); 
        $week_registration_logs_query = SystemLog::where('category','registration')
                                ->where('when','>',$one_week_ago)
                                ->orderBy('when','DESC');
        
        $item['count'] = $week_error_logs_query->count();
        if($item['count'] == 0){
            $item['last_on'] = "";
        } else {
            $item['last_on'] = $week_error_logs_query->first()->when;
        }
        $logs['error'] = $item;
        
        $item['count'] = $week_security_logs_query->count();
        if($item['count'] == 0){
            $item['last_on'] = "";
        } else {
            $item['last_on'] = $week_security_logs_query->first()->when;
        }
        $logs['security'] = $item;

        $item['count'] = $week_registration_logs_query->count();
        if($item['count'] == 0){
            $item['last_on'] = "";
        } else {
            $item['last_on'] = $week_registration_logs_query->first()->when;
        }
        $logs['registration'] = $item;
        
        $data['logs'] = $logs;               

        
        if(hasPermission('backend_access')){
            $title = 'System Info';        
            $content = View::make('admin.control_panel',$data);

            return $this->load_view($title, $content);
        } else {
            return $this->unauthorized();
        }
    }
    
    public function traffic($app_name = ''){
        
        $apps_with_traffic = flatten(SessionLog::whereNotNull('country')->groupBy('app')->select('app')->get()->toArray());
        
        $data['app_name'] = $app_name;
        $data['apps_with_traffic'] = $apps_with_traffic;
        
        if(empty($app_name)){
            $data['countries'] = DB::table('session_logs')->whereNotNull('country')->groupBy('country')->select(DB::raw('count(*) as requests, country'))->get();
        } else {
            $data['countries'] = DB::table('session_logs')->where('app',$app_name)->whereNotNull('country')->groupBy('country')->select(DB::raw('count(*) as requests, country'))->get();
        }              
        
        $title = 'Portal Traffic';        
        $content = View::make('admin.traffic',$data);

        return $this->load_view($title, $content);
    }
    
    public function error_logs(){
        
        if(hasPermission('backend_access')){
            $data['error_logs'] = SystemLog::where('category','error')->orderBy('when','DESC')->limit(50)->get();
            $title = 'Last 50 error Logs';        
            $content = View::make('admin.error_logs',$data);
           
            return $this->load_view($title, $content);
        } else {
            return $this->unauthorized();
        }
                        
    }
    
    public function security_logs(){
        
        if(hasPermission('backend_access')){
            $data['security_logs'] = SystemLog::where('category','security')->orderBy('when','DESC')->limit(50)->get();            
            $title = 'Last 50 security Logs';        
            $content = View::make('admin.security_logs',$data);

            return $this->load_view($title, $content);
        } else {
            return $this->unauthorized();
        }
                        
    }
    
    public function registration_logs(){
        
        if(hasPermission('backend_access')){
            $data['registration_logs'] = SystemLog::where('category','registration')->orderBy('when','DESC')->limit(50)->get();
            $title = 'Last 50 registration Logs';        
            $content = View::make('admin.registration_logs',$data);

            return $this->load_view($title, $content);
        } else {
            return $this->unauthorised();
        }
                        
    }
    
    /*
     * Displays the user management page.
     * 
     * @return string
     */
    public function user_management(){
        if(hasPermission('manage_users')){
            $user_list = GuiUser::select('id','firstname','lastname','email','status','last_login','created_at')->orderBy('lastname','ASC')->get();
            
            $title = 'User Management';        
            $maxlengths = Config::get('maxlengths.new_user');
            $content = View::make('admin.user_management')
                    ->with('user_list',$user_list)
                    ->with('maxlengths',$maxlengths);
            

            return $this->load_view($title, $content);
        } else {
            $this->unauthorized();
        }
    }
    
    /**
     * Defines a new application
     * 
     * @return Redirect|string
     */
    public function add_app(){
        if(hasPermission('manage_apps')){
            
            $form = Input::all();   
            $rules = Config::get('validation.add_app');
            $validation = Validator::make($form,$rules);

            if ($validation->fails()){                    
                return Redirect::back()->withInput()->withErrors($validation);
            } else {
                DB::beginTransaction();
            
                try {
                    /* Add the portal application - START */
                    $app = new PortalApp();
                    $app->title = $form['title'];
                    $app->description = $form['description'];
                    $app->codename = $form['codename'];
                    if(!empty($form['url'])){
                        $app->url = $form['url'];
                    }                            
                    $app->status = $form['status'];
                    if(!empty($form['ip'])){
                        $app->ip = $form['ip'];
                    } 
                    if(!empty($form['access_by_default'])){
                        $app->reg_access = 1;
                    } else {
                        $app->reg_access = 0;
                    }

                    if(!empty($form['mobile_app'])){
                        $app->mobile_app = 1;
                    } else {
                        $app->mobile_app = 0;
                    }

                    if(!empty($form['mobile_version'])){
                        $app->mobile_version = $form['mobile_version'];
                    } else {
                        $app->mobile_version = null;
                    }

                    // If an image file has been sent
                    if(Input::hasFile('imageFile')) {
                        // Load the file
                        $file = Input::file('imageFile');

                        // Locate the target directory
                        $category_folder = public_path().'/images/apps/';
                        if(!file_exists($category_folder)){ // just in case
                            mkdir($category_folder);
                        }
                        // Build new file name
                        $remote_filename = $form['codename'].'_'.safe_filename($file->getClientOriginalName());

                        // Move file to the right directory
                        $file->move($category_folder,$remote_filename);   

                        $app->image = $remote_filename;
                    }
                    
                    // If an toolbar image file has been sent
                    if(Input::hasFile('toolbarImageFile')) {
                        // Load the file
                        $tfile = Input::file('toolbarImageFile');

                        // Locate the target directory
                        $category_folder = public_path().'/images/apps_toolbar/';
                        if(!file_exists($category_folder)){ // just in case
                            mkdir($category_folder);
                        }
                        // Build new file name
                        $remote_filename = $form['codename'].'_'.safe_filename($tfile->getClientOriginalName());

                        // Move file to the right directory
                        $tfile->move($category_folder,$remote_filename);   

                        $app->toolbar_image = $remote_filename;
                    }

                    $app->save();                        
                    /* Add the portal application - END */

                    // Add the respective api_user
                    $api_user = new ApiUser();
                    $api_user->app_name = $form['codename'];
                    $api_user->username = $form['api_username'];
                    $api_user->password = Hash::make($form['api_password']);
                    $api_user->description = "API user for application named '".$form['title']."'";
                    $api_user->save();

                    // Add the respective permissions
                    $permission = new Permission();
                    $permission->name = $form['codename'];
                    $permission->type = 'custom';
                    $permission->description = "Permission for accessing the '".$form['title']."' application.";
                    $permission->save();

                    // If the application is controlled and can be accessed 
                    // by default by all registered users           
                    if(($app->status == 'controlled')&&($app->reg_access == 1)){
                        UserHasPermission::givePermissionToAll($permission->id);
                    }                 


                } catch (Exception $ex) {
                    DB::rollback();
                    return $this->unexpected_error();
                }
                DB::commit();  
                $this->log_event("A new application named '".$form['title']."' was created.",'application'); 
                Session::flash('toastr',array('success','Application created successfully!'));
                return Redirect::back();
            }                                  
            
        } else {
            $this->unauthorized();
        }
    }
    
    /*
     * Creates a new group
     * 
     * Supposed to be called through AJAX
     * 
     * @return json
     */
    public function add_group(){
        try {
            if(hasPermission('manage_groups')){
              
                $form = Input::all();   
                $rules = Config::get('validation.add_group');
                $validation = Validator::make($form,$rules);

                if ($validation->fails()){                    
                    return Response::json($validation->getMessageBag()->toArray(),400);
                } else {
                    DB::beginTransaction();
                    // Create the group in the database
                    $new_group = new Group();
                    $new_group->name = $form['name'];
                    $new_group->description = $form['description'];
                    $new_group->save();                                        
                    
                    $fullname = Auth::guard('web')->user()->firstname." ".Auth::guard('web')->user()->lastname;
                    $this->log_event("Group '".$form['name']."' created by ".$fullname,'registration');
                    DB::commit();  
                    
                    return Response::json(array(),200);
                }                                
            } else {
                $fullname = Auth::guard('web')->user()->firstname." ".Auth::guard('web')->user()->lastname;
                $this->log_event($fullname." tried to create a new group.",'unauthorized');
                return Response::json(array(),401);
            }            
        } catch (Exception $ex) {
            DB::rollback();
            $this->log_event("Group creation failed! Error message: ".$ex->getMessage(),'error');
            return Response::json(array('error'=>$ex->getMessage()),500);
        }
        
    }
    
    /*
     * Makes a user, member of a group (from user management page)
     * 
     * Supposed to be called through AJAX
     * 
     * @return json
     */
    public function add_group_to_user(){
        if(hasPermission('manage_users')){
              
                $form = Input::all();   
                $rules = Config::get('validation.add_group_to_user');
                $validation = Validator::make($form,$rules);

                if ($validation->fails()){                    
                    return Response::json($validation->getMessageBag()->toArray(),400);
                } else {
                    $group = Group::where('name',$form['group_name'])->first();
                    
                    $instances = UserInGroup::where('user_id',$form['user_id'])
                                ->where('group_id',$group->id)
                                ->count();
                    
                    if($instances > 0){
                        Session::flash('toastr',array('error','User is already member of this group!'));
                        return Redirect::back();
                    } else {
                        $membership = new UserInGroup();
                        $membership->user_id = $form['user_id'];
                        $membership->group_id = $group->id;
                        $membership->save();
                        
                        Session::flash('toastr',array('success','User added to group!'));
                        return Redirect::back();
                    }
                }
        } else {
            $this->unauthorized();
        }
    }
    
    /*
     * Adds a member to a group (from group management page)
     * 
     * Supposed to be called through AJAX
     * 
     * @return json
     */
    public function add_member(){
        try {
            if(hasPermission('manage_groups')){
              
                $form = Input::all();   
                $rules = Config::get('validation.add_member');
                $validation = Validator::make($form,$rules);

                if ($validation->fails()){                    
                    return Response::json($validation->getMessageBag()->toArray(),400);
                } else {
                    // Add user to the group
                    $uig = new UserInGroup();
                    $uig->user_id = $form['user_id'];
                    $uig->group_id = $form['group_id'];
                    $uig->save();                    
                    
                    // Change the number of members to this group
                    $group = Group::where('id',$form['group_id'])->first();
                    $group->count_members = $group->count_members + 1;
                    $group->save();
                    
                    return Response::json(array(),200);
                }                                
            } else {
                $fullname = Auth::guard('web')->user()->firstname." ".Auth::guard('web')->user()->lastname;
                $this->log_event($fullname." tried to add member to a group.",'unauthorized');
                return Response::json(array(),401);
            }            
        } catch (Exception $ex) {
            $this->log_event("Adding member to a group failed! Error message: ".$ex->getMessage(),'error');
            return Response::json(array('error'=>$ex->getMessage()),500);
        }
    }
    
    /*
     * Creates a new permission item
     * 
     * Supposed to be called through AJAX
     * 
     * @return json
     */
    public function add_permission(){
        try {
            if(hasPermission('manage_permissions')){
              
                $form = Input::all();   
                $rules = Config::get('validation.add_permission');
                $validation = Validator::make($form,$rules);

                if ($validation->fails()){                    
                    return Response::json($validation->getMessageBag()->toArray(),400);
                } else {
                    // Create the permission in the database
                    $new_permission = new Permission();
                    $new_permission->name = $form['pname'];
                    $new_permission->description = $form['description'];
                    $new_permission->type = "custom";
                    $new_permission->used_by = $form['used_by'];
                    $new_permission->save();                                        
                    
                    return Response::json(array(),200);
                }                                
            } else {
                $fullname = Auth::guard('web')->user()->firstname." ".Auth::guard('web')->user()->lastname;
                $this->log_event($fullname." tried to create a new permission.",'unauthorized');
                return Response::json(array(),401);
            }            
        } catch (Exception $ex) {
            $this->log_event("Permission creation failed! Error message: ".$ex->getMessage(),'error');
            return Response::json(array('error'=>$ex->getMessage()),500);
        }
    }
    
    /*
     * Creates a new user
     * 
     * Supposed to be called through AJAX
     * 
     * @return json
     */
    public function add_user(){
        try {
            if(hasPermission('manage_users')){
              
                $form = Input::all();   
                $rules = Config::get('validation.add_user');
                $validation = Validator::make($form,$rules);

                if ($validation->fails()){                    
                    return Response::json($validation->getMessageBag()->toArray(),400);
                } else {
                    // Create the user in the database
                    $new_user = new GuiUser();
                    $new_user->firstname = $form['firstname'];
                    $new_user->lastname = $form['lastname'];
                    $new_user->email = $form['email'];
                    $new_user->password = Hash::make($form['password']);            
                    $new_user->status = 'disabled';
                    $new_user->verified = 0;
                    $new_user->save();                                        
                    
                    return Response::json(array(),200);
                }                                
            } else {
                $fullname = Auth::guard('web')->user()->firstname." ".Auth::guard('web')->user()->lastname;
                $this->log_event($fullname." tried to create a new user.",'unauthorized');
                return Response::json(array(),401);
            }            
        } catch (Exception $ex) {
            $this->log_event("User creation failed! Error message: ".$ex->getMessage(),'error');
            return Response::json(array('error'=>$ex->getMessage()),500);
        }
    }
    
    /*
     * Displays the application management page
     * 
     * @return string
     */
    public function app_management(){
        if(hasPermission('manage_apps')){ 
            $apps = PortalApp::all();
            
            $title = 'Application Management';    
            $maxlengths = Config::get('maxlengths.new_app');
            $content = View::make('admin.app_management')
                    ->with('apps',$apps)
                    ->with('maxlengths',$maxlengths);
            
            return $this->load_view($title, $content);
        } else {
            $this->unauthorized();
        }
    }
    
    /**
     * Displays a page where information about a certain application can be edited
     * 
     * @param string $app_name
     * @return string
     */
    public function app_profile($app_name){
        if(hasPermission('manage_apps')){ 
            $app = PortalApp::where('codename',$app_name)->first();
            $api_user = ApiUser::where('app_name',$app_name)->first();
            
            $title = 'Application Profile';    
            $maxlengths = Config::get('maxlengths.edit_app');
            $content = View::make('admin.app_profile')
                    ->with('maxlengths',$maxlengths)
                    ->with('app',$app)
                    ->with('api_user',$api_user);
            
            return $this->load_view($title, $content);
        } else {
            $this->unauthorized();
        }
    }
    
    /**
     * Updates information about an application
     * 
     * @param string $app_name
     * @return Redirect|string
     */
    public function edit_app($app_name){        
        if(hasPermission('manage_apps')){
            
            $form = Input::all();   
            $rules = Config::get('validation.edit_app');
            $validation = Validator::make($form,$rules);           
            
            if ($validation->fails()){                    
                return Redirect::back()->withInput()->withErrors($validation);
            } else {
                
                // A last validation about the mobile version
                if(!empty($form['mobile_app'])){
                    if(!empty($form['mobile_version'])){
                        $app = PortalApp::where('codename',$app_name)->first();
                        if(!empty($app->mobile_version)){
                            $new_version = preg_replace('/[^0-9.]+/', '', $form['mobile_version']);
                            $old_version = preg_replace('/[^0-9.]+/', '', $app->mobile_version);
                            if($old_version >= $new_version){
                                $validation->getMessageBag()->add('mobile_version', 'Mobile version can only increase.');
                                return Redirect::back()->withInput()->withErrors($validation);
                            }
                        }                                                
                    }
                }
                
                DB::beginTransaction();
            
                try {
                    /* Edit the portal application - START */
                    $app = PortalApp::where('codename',$app_name)->first();
                    $app->title = $form['title'];
                    $app->description = $form['description'];

                    if(!empty($form['url'])){
                        $app->url = $form['url'];
                    } else {
                        $app->url = null;
                    }           
                    $app->status = $form['status'];
                    if(!empty($form['ip'])){
                        $app->ip = $form['ip'];
                    } else {
                        $app->ip = null;
                    }

                    $old_reg_access = $app->reg_access;
                    if(!empty($form['access_by_default'])){
                        $app->reg_access = 1;
                    } else {
                        $app->reg_access = 0;
                    }

                    if(!empty($form['mobile_app'])){
                        $app->mobile_app = 1;
                    } else {
                        $app->mobile_app = 0;
                    }

                    if(!empty($form['mobile_version'])){
                        $app->mobile_version = $form['mobile_version'];
                    } else {
                        $app->mobile_version = null;
                    }

                    // If an image file has been sent
                    if(Input::hasFile('imageFile')) {
                        // Load the file
                        $file = Input::file('imageFile');

                        // Locate the target directory
                        $category_folder = public_path().'/images/apps/';
                        if(!file_exists($category_folder)){ // just in case
                            mkdir($category_folder);
                        }
                        // Build new file name
                        $remote_filename = $app_name.'_'.safe_filename($file->getClientOriginalName());                                                           

                        // Move file to the right directory
                        $file->move($category_folder,$remote_filename);                       

                        // Remove the old file, if exists
                        if(!empty($app->image)){
                            unlink(public_path().'/images/apps/'.$app->image);
                        }

                        $app->image = $remote_filename;
                    }

                    // If toolbar image file has been sent
                    if(Input::hasFile('toolbarImageFile')) {
                        // Load the file
                        $file = Input::file('toolbarImageFile');

                        // Locate the target directory
                        $category_folder = public_path().'/images/apps_toolbar/';
                        if(!file_exists($category_folder)){ // just in case
                            mkdir($category_folder);
                        }
                        // Build new file name
                        $remote_filename = $app_name.'_'.safe_filename($file->getClientOriginalName());                                                           

                        // Move file to the right directory
                        $file->move($category_folder,$remote_filename);                       

                        // Remove the old file, if exists
                        if(!empty($app->toolbar_image)){
                            unlink(public_path().'/images/apps_toolbar/'.$app->toolbar_image);
                        }

                        $app->toolbar_image = $remote_filename;
                    }

                    $app->save();                        
                    /* Add the portal application - END */

                    // Update the respective api_user
                    $api_user = ApiUser::where('app_name',$app_name)->first();
                    $api_user->username = $form['api_username'];
                    if(!empty($form['api_password'])){
                        $api_user->password = Hash::make($form['api_password']);
                    }                
                    $api_user->description = "API user for application named '".$form['title']."'";
                    $api_user->save();

                    // If the application manager has decided to give access to all users
                    // by default and that was not the case before, we have to assign the
                    // relative privileges to all the registered users.
                    if($app->status == 'controlled'){
                        if(($app->reg_access == 1)&&($old_reg_access == 0)){
                            // Retrieve application's permission ID
                            $permission = Permission::where('name',$app->codename)->first();
                            // Assign this permission to all registered users
                            UserHasPermission::givePermissionToAll($permission->id);
                        }
                    }


                } catch (Exception $ex) {
                    DB::rollback();
                    return $this->unexpected_error($ex->getMessage());
                }
                DB::commit();  
                $this->log_event("Application named '".$form['title']."' was modified.",'application'); 
                Session::flash('toastr',array('success','Application info was updated successfully!'));
                return Redirect::back();
            }                                              
            
        } else {
            $this->unauthorized();
        }
    }        
    
    public function edit_permission($perm_id){
        
    }
    
    /**
     * Deletes an application
     * 
     * @return Redirect
     */
    public function delete_app(){
        if(hasPermission('manage_apps')){              
            $form = Input::all();   
            $rules = Config::get('validation.delete_app');
            $validation = Validator::make($form,$rules);

            if ($validation->fails()){  
                Session::flash('toastr',array('error','This applcation cannot be deleted'));
                return Redirect::back();
            } else {                    
                $app_id = $form['delete_app_id'];                                                                             
                DB::beginTransaction();

                try {
                    $app = PortalApp::find($app_id);
                    $api_user = ApiUser::where('app_name',$app->codename)->first();
                    $permission = Permission::where('name',$app->codename)->first();
                    UserHasPermission::where('permission_id',$permission->id)->delete();
                    GroupHasPermission::where('permission_id',$permission->id)->delete();
                    
                    if(!empty($app->image)){
                        $app_image = public_path().'/images/apps/'.$app->image;
                    }
                    
                    if(!empty($app->toolbar_image)){
                        $toolbar_image = public_path().'/images/logos/'.$app->toolbar_image;
                    }                    
                    
                    $permission->delete();
                    $api_user->delete();
                    $app->delete();                    
                    
                    if(!empty($app_image))      unlink($app_image);
                    if(!empty($toolbar_image))  unlink($toolbar_image);
                    
                } catch (Exception $ex) {
                        DB::rollback();
                        $this->log_event("Application deletion failed! Error message: ".$ex->getMessage(),'error');
                        return $ex->getMessage();
                        $title = 'Error happened!';    
                        $content = View::make('errors.unexpected_error');

                        return $this->load_view($title, $content);
                }

                DB::commit();                     

                Session::flash('toastr',array('success','The application has been deleted!'));
                return Redirect::back();
            }
        } else {
            $this->unauthorized();
        }
    }
    
    /*
     * Deletes a group
     * 
     * @return Redirect
     */
    public function delete_group(){
        if(hasPermission('manage_groups')){              
            $form = Input::all();   
            $rules = Config::get('validation.delete_group');
            $validation = Validator::make($form,$rules);

            if ($validation->fails()){  
                Session::flash('toastr',array('error','This group cannot be deleted'));
                return Redirect::back();
            } else {                    
                $group_id = $form['delete_group_id'];                                                                             

                DB::beginTransaction();

                try {
                    // Delete group permissions
                    GroupHasPermission::where('group_id',$group_id)->delete();

                    // Delete group memberships        
                    UserInGroup::where('group_id',$group_id)->delete();        
                            
                    // Delete group
                    $group = Group::find($group_id);
                    $group->delete();   
                } catch (Exception $ex) {
                        DB::rollback();
                        $this->log_event("Group deletion failed! Error message: ".$ex->getMessage(),'error');
                        return View::make('errors.unexpected_error');
                }

                DB::commit();                     

                Session::flash('toastr',array('success','The group has been deleted!'));
                return Redirect::back();
            }
        } else {
            $this->unauthorized();
        }
    }
    
    /*
     * Deletes a permission item
     * 
     * @return Redirect
     */
    public function delete_permission(){
        if(hasPermission('manage_permissions')){              
            $form = Input::all();   
            $rules = Config::get('validation.delete_permission');
            $validation = Validator::make($form,$rules);

            if ($validation->fails()){  
                Session::flash('toastr',array('error','This permission cannot be deleted'));
                return Redirect::back();
            } else {                    
                $permission_id = $form['delete_permission_id'];                                                                             

                DB::beginTransaction();

                try {
                    // Remove permission from users
                    UserHasPermission::where('permission_id',$permission_id)->delete();

                    // Remove permission from groups
                    GroupHasPermission::where('permission_id',$permission_id)->delete();

                    // Delete permission
                    $permission = Permission::find($permission_id);
                    $permission->delete();   
                } catch (Exception $ex) {
                        DB::rollback();
                        $this->log_event("Permission deletion failed! Error message: ".$ex->getMessage(),'error');
                        return View::make('errors.unexpected_error');
                }

                DB::commit();                     

                Session::flash('toastr',array('success','The permission has been deleted!'));
                return Redirect::back();
            }
        } else {
            $this->unauthorized();
        }
    }
    
    /*
     * Deletes a user
     * 
     * @return Redirect
     */
    public function delete_user(){
        if(hasPermission('manage_users')){              
            $form = Input::all();   
            $rules = Config::get('validation.delete_user');
            $validation = Validator::make($form,$rules);

            if ($validation->fails()){  
                Session::flash('toastr',array('error','This user cannot be deleted'));
                return Redirect::back();
            } else {                    
                $user_id = $form['delete_user_id'];                                                                             

                DB::beginTransaction();

                try {
                    // Delete user permissions
                    UserHasPermission::where('user_id',$user_id)->delete();

                    // Delete user memberships
                    UserInGroup::where('user_id',$user_id)->delete();

                    // Delete user
                    $user = GuiUser::find($user_id);
                    $user->delete();   
                } catch (Exception $ex) {
                        DB::rollback();
                        $this->log_event("User deletion failed! Error message: ".$ex->getMessage(),'error');
                        return View::make('errors.unexpected_error');
                }

                DB::commit();                     

                Session::flash('toastr',array('success','The user has been deleted!'));
                return Redirect::back();
            }
        } else {
            $this->unauthorized();
        }
    }
    
    /*
     * Disables a user account
     * 
     * @return Redirect
     */
    public function disable_user($user_id){
        if(hasPermission('manage_users')){
            $user = GuiUser::find($user_id);
            if(empty($user)){
                return View::make('errors.illegal');
            } else {
                $user->status = 'disabled';
                $user->save();
                return Redirect::to('admin/user_management');
            }
        } else {
            $this->unauthorized();
        }
    }
    
    /*
     * Enables a user account
     * 
     * @return Redirect
     */
    public function enable_user($user_id){

        if(hasPermission('manage_users')){
            $user = GuiUser::find($user_id);
            if(empty($user)){
                return View::make('errors.illegal');
            } else {
                $user->status = 'enabled';
                $user->save();
                return Redirect::to('admin/user_management');
            }
        } else {
            $this->unauthorized();
        }
    }
    
    /*
     * Displays the group management page
     * 
     * @return string
     */
    public function group_management(){
        if(hasPermission('manage_groups')){
            $groups = Group::all();
            
            $title = 'Group Management';     
            $maxlengths = Config::get('maxlengths.new_group');
            $content = View::make('admin.group_management')
                    ->with('groups',$groups)
                    ->with('maxlengths',$maxlengths);
            
            return $this->load_view($title, $content);
        } else {
            $this->unauthorized();
        }
    } 
    
    /*
     * Displays a group's profile
     * 
     * @return string
     */
    public function group_profile($group_name){
        if(hasPermission('manage_groups')){
            $group = Group::where('name',$group_name)->first();
            if(empty($group)){
                return $this->custom_error_message("Group name not found!");
            }
            $members = Group::getMembers($group->id);
            $user_list = GuiUser::getUserList();
            $permission_list = Permission::all();
            $group_permission_ids = Group::getPermissionIds($group->id);
            
            $title = 'Group Profile';     
            $content = View::make('admin.group_profile')
                        ->with('group',$group)
                        ->with('members',$members)
                        ->with('user_list',$user_list)
                        ->with('permission_list',$permission_list)
                        ->with('group_permission_ids',flatten($group_permission_ids));
            
            return $this->load_view($title, $content);
        } else {
            return $this->unauthorized();
        }
    }
    
    /*
     * Displays the permission management page
     * 
     * @return string
     */
    public function permission_management(){
        if(hasPermission('manage_permissions')){
            $permissions = Permission::all();
            $app_list = array('core'=>'core');
            $apps = PortalApp::all();
            foreach($apps as $app){
                $app_list[$app->codename] = $app->codename;
            }
            
            $title = 'Permission Management';      
            $maxlengths = Config::get('maxlengths.new_permission');
            $content = View::make('admin.permission_management')
                    ->with('permissions',$permissions)
                    ->with('maxlengths',$maxlengths)
                    ->with('app_list',$app_list);

            return $this->load_view($title, $content);
        } else {
            $this->unauthorized();
        }
    }
    
    /*
     * Cancels a user's membership to a group (from user management page)
     * 
     * @return Redirect
     */
    public function remove_group_from_user(){
        if(hasPermission('manage_users')){
            $form = Input::all();   
            $rules = Config::get('validation.remove_group');
            $validation = Validator::make($form,$rules);

            if ($validation->fails()){                    
                return Redirect::back()->withErrors($validation);
            } else {
                UserInGroup::where('user_id',$form['user_id'])
                        ->where('group_id',$form['group_id'])
                        ->delete();
                
                Session::flash('toastr',array('success','Group membership updated successfully!'));
                return Redirect::back();
            }
        } else {
            $this->unauthorized();
        }
    }
    
    /*
     * Cancels a user's membership to a group (from group management page)
     * 
     * @return Redirect
     */
    public function remove_member(){
        if(hasPermission('manage_groups')){
            $form = Input::all();   
            $rules = Config::get('validation.remove_member');
            $validation = Validator::make($form,$rules);

            if ($validation->fails()){                    
                return Redirect::back()->withErrors($validation);
            } else {
                UserInGroup::where('user_id',$form['user_id'])
                        ->where('group_id',$form['group_id'])
                        ->first()
                        ->delete();
                
                // Change the number of members to this group
                $group = Group::where('id',$form['group_id'])->first();
                $group->count_members = $group->count_members - 1;
                $group->save();
                
                Session::flash('toastr',array('success','Group memberships updated successfully!'));
                return Redirect::back();
            }          
        } else {
            $this->unauthorized();
        }
    }
    
    /*
     * Displays the system settings page
     * 
     * @return string
     */
    public function system_settings(){
        if(hasPermission('manage_settings')){ 
            $settings = Setting::all();
        
            $title = 'System Settings';        
            $content = View::make('admin.settings')
                    ->with('settings',$settings);

            return $this->load_view($title, $content);
        } else {
            $this->unauthorized();
        }
    }
    
    /*
     * Saves the new system settings
     * 
     * @return Redirect
     */
    public function save_system_settings(){
        if(hasPermission('manage_settings')){ 
            
            $form = Input::all();   
            if(!empty($form)){
                foreach($form as $key => $value){
                    $setting = Setting::where('name',$key)->first();
                    if(!empty($setting)){
                        $setting->value = $value;
                        $setting->last_modified = (new DateTime())->format("Y-m-d H:i:s");
                        $setting->save();
                    }
                }
            }                               
            Session::flash('toastr',array('success','System settings were updated successfully!'));
            return Redirect::to('admin/system_settings');
        } else {
            $this->unauthorized();
        }
    }
    
    /*
     * Updates group permissions
     * 
     * @return Redirect
     */
    public function update_group_permissions(){
        
        if(hasPermission('manage_groups')){
            $form = Input::all();   
            $rules = Config::get('validation.update_group_permissions');
            $validation = Validator::make($form,$rules);

            if ($validation->fails()){                    
                return Redirect::back()->with('permissions_error',$validation);
            } else {
                // Remove old group permissions
                GroupHasPermission::where('group_id',$form['group_id'])->delete();
                
                // Add the new permissions
                $permissions = Permission::all();
                foreach($permissions as $permission){
                    if(!empty($form[$permission->name."_enabled"])){
                        $new_permission = new GroupHasPermission();
                        $new_permission->group_id = $form['group_id'];
                        $new_permission->permission_id = $form[$permission->name."_enabled"];
                        $new_permission->save();
                    }
                }
                Session::flash('toastr',array('success','Group permissions updated!'));
                return Redirect::back();
            }           
        } else {
            $this->unauthorized();
        }                
    }
    
    /*
     * Updates user permissions
     * 
     * @return Redirect
     */
    public function update_user_permissions(){        
        
        if(hasPermission('manage_users')){
            $form = Input::all();   
            $rules = Config::get('validation.update_user_permissions');
            $validation = Validator::make($form,$rules);

            if ($validation->fails()){                    
                return Redirect::back()->with('permissions_error',$validation);
            } else {
                // Remove old user permissions
                UserHasPermission::where('user_id',$form['user_id'])->delete();
                
                // Add the new permissions
                $permissions = Permission::all();
                foreach($permissions as $permission){
                    if(!empty($form[$permission->name."_enabled"])){
                        $new_permission = new UserHasPermission();
                        $new_permission->user_id = $form['user_id'];
                        $new_permission->permission_id = $form[$permission->name."_enabled"];
                        $new_permission->save();
                    }
                }
                Session::flash('toastr',array('success','User permissions updated!'));
                return Redirect::back();
            }           
        } else {
            $this->unauthorized();
        }                
    }
    
    /*
     * Displays a user's profile to an administrator
     * 
     * @return Redirect
     */
    public function user_profile_management($user_id){
        if(hasPermission('manage_users')){
            $user = GuiUser::find($user_id);   
            if(empty($user)){
                return $this->custom_error_message("User not found!");
            }            
            $user_groups = GuiUser::getUserGroups($user_id);
            $remaining_groups = GuiUser::getUserRemainingGroups($user_id);
            $permission_list = Permission::all();
            $user_permission_ids = flatten(GuiUser::getPermissionIds($user_id));            
            
            $rest_of_groups = array();
            foreach($remaining_groups as $group){
                $rest_of_groups[$group->name] = $group->name;
            }
            
            $title = 'User Profile';     
            $content = View::make('admin.user_profile_manage')
                        ->with('user',$user)
                        ->with('groups',$user_groups)
                        ->with('rest_of_groups',$rest_of_groups)
                        ->with('permission_list',$permission_list)
                        ->with('user_permission_ids',$user_permission_ids);
            
            return $this->load_view($title, $content);
            
            
        } else {
            $this->unauthorized();
        }
    }
    
    /**
     * Validates the form that is used to create a new application
     * 
     * Supposed to be called through AJAX
     * 
     * @return json
     */
    public function validate_app_info(){
        try {
            if(hasPermission('manage_apps')){
              
                $form = Input::all();   
                $rules = Config::get('validation.add_app');
                $validation = Validator::make($form,$rules);

                if ($validation->fails()){                    
                    return Response::json($validation->getMessageBag()->toArray(),400);
                } else {
                    return Response::json(array(),200);
                }                                
            } else {
                $fullname = Auth::guard('web')->user()->firstname." ".Auth::guard('web')->user()->lastname;
                $this->log_event($fullname." tried to create a new app.",'unauthorized');
                return Response::json(array(),401);
            }            
        } catch (Exception $ex) {
            $this->log_event("App creation failed! Error message: ".$ex->getMessage(),'error');
            return Response::json(array('error'=>$ex->getMessage()),500);
        }
    }
    
}
