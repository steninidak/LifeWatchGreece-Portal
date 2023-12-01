<?php

namespace App\Http\Controllers;

use Auth;
use Response;
use Input;
use Config;
use Validator;
use Session;
use App\Models\PortalApp;
use App\Models\GuiUser;
use App\Models\ApiUser;
use App\Models\SessionLog;
use App\Models\AgentLog;
use Illuminate\Http\Request;

/**
 * A controller that exposes portal's sessions through an API
 *
 * @author   Alexandros Gougousis
 */
class SessionController extends WebController {

        public function __construct() {
            parent::__construct();
        }

        /*
         * Checks the user status
         *
         * Returns the user status (logged in or not), if the user can access
         * this application and if the user is logged in it returns the
         * portal's HTML wrapper code. Supposed to be called through AJAX by
         * web applications that have been integrated to the portal.
         *
         * @return json
         */
	//public function test(){
        public function test(Request $request, $codename){
            // Retrieve information about the calling application
            $app_info = PortalApp::where('codename',$codename)->first();
            if(empty($app_info)){
                return Response::json(array(),400);
            }

            // Access control by IP
            if (!empty($app_info->ip)){

                $valid_ips = explode(';',$app_info->ip);

                $ip = getenv('HTTP_CLIENT_IP')?:
                getenv('HTTP_X_FORWARDED_FOR')?:
                getenv('HTTP_X_FORWARDED')?:
                getenv('HTTP_FORWARDED_FOR')?:
                getenv('HTTP_FORWARDED')?:
                getenv('REMOTE_ADDR');

                if(!in_array($ip,$valid_ips)){
                    $this->log_event("Application with codename '".$codename."' should not be access by IP ".$ip,'unauthorized');
                    $response = array(
                            'status'        =>  'visitor',
                            'authorized'    =>  'no',
                            'head'          =>  '',
                            'body_top'      =>  '',
                            'body_bottom'   =>  '',
                            'email'         =>  ''
                    );
                    return Response::json($response,401);
                }

            }

            try {
                if((!empty($app_info->mobile_app))&&(!empty($app_info->mobile_version))){
                    $mobile_version = $app_info->mobile_version;
                } else {
                    $mobile_version = '';
                }

                // If the request was made in part of a logged in
                if(Auth::guard('web')->check()){
                    // Access control according to user's privileges
                    switch($app_info->status){
                        case 'controlled':
                            if(hasPermission($codename)){
                                $authorized = 'yes';
                            } else {
                                $authorized = 'no';
                            }
                            break;
                        case 'free':
                            $authorized = 'yes';
                            break;
                        case 'developing':
                            if(hasPermission("access_unfinished_apps")){
                                $authorized = 'yes';
                            } else {
                                $authorized = 'no';
                            }
                            break;
                        case 'open':
                            $authorized = 'yes';
                            break;
                    }

                    $api_username = $_SERVER['PHP_AUTH_USER'];
                    $api_user = ApiUser::where('username',$api_username)->first();
                    $app_name = $api_user->app_name;
                    $portalApp = PortalApp::where('codename',$app_name)->first();
                    $related_privileges = GuiUser::getUserAppPrivileges(Auth::guard('web')->id(),$codename);

                    $head = view('vlab_wrapper.head');
                    if($codename == 'medobis'){
                        $body_top = view('vlab_wrapper.body_top')
                                    ->with('app',$portalApp)
                                    ->with('application_name','medobis');
                    } else {
                        $body_top = view('vlab_wrapper.body_top')
                                    ->with('app',$portalApp);
                    }
                    $body_bottom = view('vlab_wrapper.body_bottom');

                    $response = array(
                            'status'        =>  'identified',
                            'authorized'    =>  $authorized,
                            'head'          =>  "$head",
                            'body_top'      =>  "$body_top",
                            'body_bottom'   =>  "$body_bottom",
                            'email'         =>  Auth::guard('web')->user()->email,
                            'mobile_version'=>  $mobile_version,
                            'privileges'    =>  $related_privileges,
                            'timezone'      =>  Auth::guard('web')->user()->timezone,
                    );
                    $response2 = array(
                            'status'        =>  'identified',
                            'authorized'    =>  $authorized,
                            'head'          =>  "",
                            'body_top'      =>  "",
                            'body_bottom'   =>  "",
                            'email'         =>  Auth::guard('web')->user()->email,
                            'mobile_version'=>  $mobile_version,
                            'privileges'    =>  $related_privileges,
                            'timezone'      =>  Auth::guard('web')->user()->timezone,
                    );

                    // Add a traffic record in database
                    $this->log_request($request,'vlab', $codename);

                    // Log current time for last user's activity (we do the same in routes.php)
                    $user = GuiUser::where('id',Auth::guard('web')->id())->first();
                    $user->last_activity = time();
                    $user->save();
                    $this->log_event(json_encode($response2), 'info');
                    return Response::json($response,200);
                } else {

                    if($app_info->status == 'open'){
                        $head = view('external_wrapper.head');
                        $body_top = view('external_wrapper.body_top')
                                        ->with('app',$app_info);
                        $body_bottom = view('external_wrapper.body_bottom');

                        $response = array(
                            'status'        =>  'visitor',
                            'authorized'    =>  'yes',
                            'head'          =>  "$head",
                            'body_top'      =>  "$body_top",
                            'body_bottom'   =>  "$body_bottom",
                            'mobile_version'    =>  $mobile_version
                        );
                        $responseString = Response::json($response,200);

                        // Add a traffic record in database
                        $this->log_request($request,'vlab', $codename);

                        return $responseString;
                    } else {
                        $response = array(
                            'status'        =>  'visitor',
                            'authorized'    =>  'no',
                            'head'          =>  '',
                            'body_top'      =>  '',
                            'body_bottom'   =>  '',
                            'mobile_version'    =>  $mobile_version
                        );
                        return Response::json($response,200);
                    }

                }
            } catch (Exception $ex) {
                $this->log_event("Session testing failed! Error message: ".$ex->getMessage(),'error');
                return Response::json(array($ex->getMessage(), 'Exception in test method.'),500);
            }
        }

        /*
         * Flashes data to the user's session
         *
         * @return json
         */
        public function flash(){

            $form = Input::all();
            $rules = Config::get('validation.flash_to_session');
            $validation = Validator::make($form,$rules);

            if ($validation->fails()){
                return Response::json(array('message'=>'Session var or session data had not been set or exceeded size limit!'),200);
            }

            // Load the database session using the session cookie
            Config::set('session.driver', 'database');
            Session::flash($form['session_var'],json_decode($form['session_data']));

            return Response::json(array(),200);
        }

        /**
         * Checks if the user has a specific permission
         *
         * @param String $permission_name
         * @return json
         */
        public function has_permission($permission_name){

            try {
                if(Auth::guard('web')->check()){
                    if(hasPermission($permission_name)){
                        $response = array("permitted"=>"yes");
                        return Response::json($response,200);
                    } else {
                        $response = array("permitted"=>"no");
                        return Response::json($response,200);
                    }
                } else {
                    $response = array("permitted"=>"no");
                    return Response::json($response,200);
                }
            } catch (Exception $ex) {
                $this->log_event("Permission query failed! Error message: ".$ex->getMessage(),'error');
                return Response::json(array($ex->getMessage(), 'Persmission error!'),500);
            }
        }
}