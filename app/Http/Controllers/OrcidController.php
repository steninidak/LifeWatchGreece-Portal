<?php

namespace App\Http\Controllers;

use DB;
use Hash;
use Auth;
use Config;
use View;
use Mail;
use Validator;
use Redirect;
use Session;
use Orcid\Oauth;
use Orcid\Profile;
use App\Models\PortalApp;
use App\Models\GuiUser;

/**
 * A controller that implements features common to most controllers.
 * 
 * @author   Alexandros Gougousis
 */
class OrcidController extends WebController {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function page4(){
        $content = View::make('page4');
        return $this->load_view('Page 4',$content);
    }
    
    public function orcid_login(){
        
        if(Auth::guard('web')->check()){
            // The user is already logged in
            return Redirect::to('home');
        } else {
            // Load orcid configuration
            $client_id = Config::get('orcid.client_id');
            $redirect_uri = url('/')."/".Config::get('orcid.redirect_uri');
            $state = Config::get('orcid.state');
           
            // Set up the config for the ORCID API instance
            $oauth = new Oauth;
            $oauth->setClientId($client_id)
                  ->setScope('/authenticate')
                  ->setState($state)
                  ->showLogin()
                  ->setRedirectUri($redirect_uri);

            return redirect()->away($oauth->getAuthorizationUrl());
        }
        
    }
    
    public function orcid_auth(){
        if (!isset($_GET['code'])) {
            // User didn't authorize our app
            throw new Exception('Authorization failed');
        }
        
        $client_id = Config::get('orcid.client_id');
        $client_secret = Config::get('orcid.client_secret');
        $redirect_uri = url('/')."/".Config::get('orcid.redirect_uri');
        
        $oauth = new Oauth;
        $oauth->setClientId($client_id)
              ->setClientSecret($client_secret)
              ->setRedirectUri($redirect_uri);
        
        // Authenticate the user
        $oauth->authenticate($_GET['code']);
        
        // Check for successful authentication
        if ($oauth->isAuthenticated()) {
            $profile = new Profile($oauth);
            
            // Get user ORCID profile
            $orcid_profile = array();
            $orcid_profile['id'] = $profile->id();
            $orcid_profile['email'] = $profile->email();
            $orcid_name = $profile->fullName();                       
            
            // Check if all required data are available
            $valid = true;
            
            $name_parts = explode(' ',$orcid_name);
            if(count($name_parts) < 2){
                $valid = false;
            } else {
                $orcid_profile['firstname'] = $name_parts[0];
                $orcid_profile['lastname'] = "";
                for($j = 1; $j<count($name_parts); $j++){
                    $orcid_profile['lastname'] .= " ".$name_parts[$j]; 
                }       
                $orcid_profile['lastname'] = trim($orcid_profile['lastname']);
            }            
            
            $rules = config('validation.orcid_profile');
            $validation = Validator::make($orcid_profile,$rules);

            if ($validation->fails() || (!$valid)){            
                $this->log_event("ORCID validation failed!",'login');
                Session::flash('toastr',array('error',"Incomplete ORCID profile! <a href='".url('/orcid/help')."'>Need more help?</a>"));
                return Redirect::back()->withErrors($validation);
            } 
            
            // Check if user exists in database
            $user = GuiUser::where('email',$orcid_profile['email'])->first();                            
            if(!empty($user)){
                // If exists, log him in
                Auth::guard('web')->login($user);
                // Update user record
                $user->last_login = date("Y-m-d H:i:s");
                $user->save();
                // Redirect to Home Page
                return Redirect::to('home'); 
            } else {
                
                DB::beginTransaction();
                try {
                    // If not, add the user in database and log him in
                    $new_user = new GuiUser();
                    $new_user->firstname = $orcid_profile['firstname'];
                    $new_user->lastname = $orcid_profile['lastname'];
                    $new_user->email = $orcid_profile['email'];
                    $new_user->password = Hash::make($this->randomString()); // Store a random password   
                    $new_user->status = 'enabled';
                    $new_user->verified = 1;
                    $new_user->origin = 'orcid';
                    $new_user->save();

                    // Retrieve controlled apps that are accessible by default
                    $apps = PortalApp::where('status','controlled')
                            ->where('reg_access',1)
                            ->get();
                    // Give the user permission for these apps
                    foreach($apps as $app){
                        // Retrieve the ID of app's permission
                        $app_permission = Permission::where('name',$app->codename)->first();
                        // Build the new permission 
                        $permission = new UserHasPermission();
                        $permission->user_id = $new_user->id;
                        $permission->permission_id = $app_permission->id;
                        $permission->save();
                    }
                } catch (Exception $ex) {
                    DB::rollback();
                    die($ex->getMessage());
                    if($this->is_mobile){
                        return Response::json(array(),500);
                    } else {
                        return view('errors.unexpected_error');
                    }      
                }
                      
                DB::commit();                                          
                
                // Log the ORCID registration event
                $ip = getenv('HTTP_CLIENT_IP')?:
                    getenv('HTTP_X_FORWARDED_FOR')?:
                    getenv('HTTP_X_FORWARDED')?:
                    getenv('HTTP_FORWARDED_FOR')?:
                    getenv('HTTP_FORWARDED')?:
                    getenv('REMOTE_ADDR');

                $this->log_event("New ORCID registration from $ip !",'registration');

                // Notify the admin about the new registration
                $data['user'] = $new_user;
                $settings = $this->system_settings;
                try {
                    Mail::send('emails.auth.new_registration', $data, function($message) use ($settings)
                    {
                        $message->to($settings['admin_email'])->subject('LWG: New ORCID registration');
                    });
                } catch (Exception $ex) {
                    $this->log_event("Mail could not be sent! Error message: ".$ex->getMessage(),'error');
                }        
                
            }

            // Log the user in
            Auth::guard('web')->login($new_user);
            // Update user record
            $new_user->last_login = date("Y-m-d H:i:s");
            $new_user->save();
            
            return Redirect::to('home');
        } else {
            Session::flash('toastr',array('error','ORCID authentication failed!'));
            return Redirect::back();
        }
    }
    
}