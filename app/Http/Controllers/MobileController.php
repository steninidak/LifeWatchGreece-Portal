<?php

namespace App\Http\Controllers;

use Config;
use Input;
use Validator;
use Response;
use Auth;
use App\Models\GuiUser;
use App\Models\PortalApp;

/**
 * Description of MobileController
 *
 * @author Alexandros
 */
class MobileController extends WebController {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function login1(){
        $form = Input::all();            
        $rules = Config::get('validation.login');
        $validation = Validator::make($form,$rules);
        $response = array();

        if ($validation->fails()){
            $this->log_event("Validation failed!",'login');
            $response = array(
                'result'    =>  'failure',
                'message'   =>  'Wrong username or password'
            );
            return Response::json($response,200); 
        } else {                        
            
            // If the validation didn't fail, an account with such email exists
            $check_user = GuiUser::where('email',$form['email'])->first();
            
            // Don't let accounts with unverified email to login
            if($check_user->verified == 0){
                $this->log_event("Email address has not been verified!",'login');
                $response = array(
                    'result'    =>  'failure',
                    'message'   =>  'Your email address has not been verified'
                );
                return Response::json($response,200);
            }
            
            // Don't let diactivated accounts to login 
            if($check_user->status == 'disabled'){
                $this->log_event("Account is not activated!",'login');
                $response = array(
                    'result'    =>  'failure',
                    'message'   =>  'Your account is not activated'
                );
                return Response::json($response,200);
            }
            
            // (Try to) Login officially
            $authenticated = Auth::guard('web')->attempt(array(
                'email'     => $form['email'],
                'password'  => $form['password'],
            ));
            
            if($authenticated){
                    $user = GuiUser::find(Auth::guard('web')->user()->id);
                    $user->last_login = date("Y-m-d H:i:s");
                    $user->save();
                    
                    $response = array(
                        'result'    =>  'success',
                        'message'   =>  ''
                    );
                    return Response::json($response,200);
                         
            } else {
                $this->log_event("Wrong username or password!",'login');
                $response = array(
                    'result'    =>  'failure',
                    'message'   =>  'Wrong username or password'
                );
                return Response::json($response,200);
            }    
        }                        
    }
    
    public function index() {        
        
        $apps = PortalApp::select('title','description','url','status','image')->get();               
        return Response::json($apps,200);
    }        
    
    public function get_mobile_versions(){
        
        $apps = PortalApp::select('title','codename','status','mobile_app','mobile_version')->get();
        $versions = array();
        foreach($apps as $app){
            if(!empty($app->mobile_app)){
                $versions[$app->codename] = $app->mobile_version;
            }
        }
        return Response::json($versions,200);
        
    }
    
    public function get_token(){
        $token = csrf_token();
        $response = array(
            'token' =>  $token,
            'when'  =>  date('Y-m-d H:i:s')
        );
        return Response::json($response,200);
    }    
    
}
