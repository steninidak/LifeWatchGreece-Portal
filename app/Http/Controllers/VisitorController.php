<?php

namespace App\Http\Controllers;

use DB;
use Hash;
use Auth;
use Captcha;
use Input;
use Validator;
use Redirect;
use Session;
use DateTime;
use Mail;
use HTML;
use App\Models\PortalApp;
use App\Models\GuiUser;
use App\Models\VerificationLink;
use App\Models\PasswordResetLink;
use Illuminate\Http\Request;

/**
 * A controller that implements features common to most controllers.
 *
 * @author   Alexandros Gougousis
 */
class VisitorController extends WebController {

    public function __construct() {
        parent::__construct();
    }

    public function landing_page(Request $request){

        $this->log_request($request,'portal');

        if(Auth::guard('web')->check()){
            return redirect()->to('/home');
        } else {
            $apps = PortalApp::select('title','description','url','status','image','hide_from_ui')->get();
            $content = view('landing')->with('apps',$apps);

            return $this->load_view('Intro Page',$content);
        }

    }

    /*
     * Logs in a user
     *
     * @return Redirect
     */
    public function login(){
        $form = Input::all();
        $rules = config('validation.login');
        $validation = Validator::make($form,$rules);

        if ($validation->fails()){
            $this->log_event("Validation failed!",'login');
            Session::flash('toastr',array('error','Wrong username or password!'));
            return Redirect::back()->withErrors($validation);
        } else {
            // If the validation didn't fail, an account with such email exists
            $check_user = GuiUser::where('email',$form['email'])
                            ->first();

            // Don't let accounts with unverified email to login
            if($check_user->verified == 0){
                $this->log_event("Email address has not been verified!",'login');
                Session::flash('toastr',array('error','Your email address has not been verified!'));
                return Redirect::back();
            }

            // Don't let diactivated accounts to login
            if($check_user->status == 'disabled'){
                $this->log_event("Account is not activated!",'login');
                Session::flash('toastr',array('error','Your account is not activated!'));
                return Redirect::back();
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

                    return Redirect::to('home');

            } else {
                $ip = getenv('HTTP_CLIENT_IP')?:
                getenv('HTTP_X_FORWARDED_FOR')?:
                getenv('HTTP_X_FORWARDED')?:
                getenv('HTTP_FORWARDED_FOR')?:
                getenv('HTTP_FORWARDED')?:
                getenv('REMOTE_ADDR');

                $this->log_event("Wrong username or password! (".$ip.")",'security');
                Session::flash('toastr',array('error','Wrong username or password!'));
                return Redirect::back();
            }
        }
    }

    /*
     * Displays a user registration page
     *
     * @return string
     */
    public function register_page(Request $request){

        $this->log_request($request,'portal');

        $title = 'Registration';
        $content = view('registration');

        return $this->load_view($title, $content);
    }

    /*
     * Registers a user
     *
     * @return Redirect
     */
    public function register(){
        $form = Input::all();
        $rules = config('validation.registration');
        $validation = Validator::make($form,$rules);

        if ($validation->fails()){
            if($this->is_mobile){
                return Response::json($validation->messages(),400);
            } else {
                return Redirect::back()->withInput()->withErrors($validation);
            }
        } else {

            DB::beginTransaction();

            try {
                // Create the user in the database
                $new_user = new GuiUser();
                $new_user->firstname = $form['firstname'];
                $new_user->lastname = $form['lastname'];
                $new_user->email = $form['email'];
                $new_user->password = Hash::make($form['password']);
                $new_user->affiliation = $form['affiliation'];
                $new_user->position = $form['position'];
                $new_user->status = 'disabled';
                $new_user->verified = 0;
                $new_user->origin = 'portal';
                $new_user->save();

                // Create a verification link
                $verification_link = new VerificationLink();
                $verification_link->uid = $new_user->id;
                $random = str_random(24);
                $url = secure_url('registration/verify/'.$random);
                $verification_link->code = $random;
                $date = new DateTime();
                $date->modify("+2 day");
                $valid_until = $date->format("Y-m-d H:i:s");
                $verification_link->valid_until = $valid_until;
                $verification_link->save();

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

            $ip = getenv('HTTP_CLIENT_IP')?:
                getenv('HTTP_X_FORWARDED_FOR')?:
                getenv('HTTP_X_FORWARDED')?:
                getenv('HTTP_FORWARDED_FOR')?:
                getenv('HTTP_FORWARDED')?:
                getenv('REMOTE_ADDR');

            $this->log_event("New registration from $ip !",'registration');

            // Notify the admin about the new registration
            $data['user'] = $new_user;
            $settings = $this->system_settings;
            try {
                Mail::send('emails.auth.new_registration', $data, function($message) use ($settings)
                {
                    $message->to($settings['admin_email'])->subject('LWG: New registration');
                });
            } catch (Exception $ex) {
                $this->log_event("Mail could not be sent! Error message: ".$ex->getMessage(),'error');
            }

            // Notify the user about the administration approvement
            $data['user'] = $new_user;
            $data['link'] = $url;
            try {
                Mail::send('emails.auth.email_verification', $data, function($message) use ($form)
                {
                    $message->to($form['email'])->subject('LWG: Email verification');
                });
                if($this->is_mobile){
                    return Response::json(array(),200);
                } else {
                    return Redirect::to('successful_registration');
                }
            } catch (Exception $ex) {
                $this->log_event("Mail could not be sent! Error message: ".$ex->getMessage(),'error');
                if($this->is_mobile){
                    return Response::json(array(),500);
                } else {
                    return $this->unexpected_error("Something went wrong! Please try again later or contact the system administrator.");
                }
            }
        }
    }

    /*
     * Provides a new captcha image to replace the old one
     *
     * Supposed to be called through AJAX
     *
     * @return file
     */
    public function new_captcha_image_link(Request $request){
        if($request->ajax()){
            return Captcha::src();
        }
    }

    /*
     * Verifies a user's email
     *
     * Supposed to be called directly through a link that is contained in an email
     *
     * @return string|Redirect
     */
    public function email_verification($code){
        $linkInfo = VerificationLink::where('code','=',$code)->first();

        if(!empty($linkInfo)){
            $now = new DateTime();
            $valid_until = new DateTime($linkInfo->valid_until);
            if($now > $valid_until){
                $this->log_event("Expired verification link.",'registration');
                $title = 'Invalid link';
                $content = view('errors.expired_link');

                return $this->load_view($title, $content);
            } else {
                // Verify the user
                $user = GuiUser::find($linkInfo['uid']);
                $user->verified = 1;
                $user->status = 'enabled';
                $user->save();
                // Remove the link from database
                $linkInfo->delete();
                // Display the successful verification message
                return Redirect::to('post_verification');
            }
        } else {
            $this->log_event("Illegal verification link.",'registration');
            return view('errors.illegal');
        }
    }

    /*
     * Displays a message after a successful registration
     *
     * @return string
     */
    public function registration_message(){
        // Display registration success message
        $title = 'Successful Registration';
        $content = view('registration_success');
        return $this->load_view($title, $content);
    }

    /*
     * Displays a message after a successful email verification
     *
     * @return string
     */
    public function verification_message(){
        $title = 'Successful email verification';
        $content = view('verification_message');

        return $this->load_view($title, $content);
    }

    public function password_reset_request(){
        $title = 'Password reset request';
        $content = view('password_reset_request');

        return $this->load_view($title, $content);
    }

    public function send_reset_link(){
        $form = Input::all();
        $rules = config('validation.password_reset_request');
        $validation = Validator::make($form,$rules);

        if ($validation->fails()){
            return Redirect::back()->withInput()->withErrors($validation);
        } else {
            try {
                $user = GuiUser::where('email',$form['email'])->first();
                $uid = $user->id;

                 // Create and send a reset link
                $reset_link = new PasswordResetLink();
                $reset_link->uid = $uid;
                $random = str_random(24);
                $url = secure_url('password_reset/'.$random);
                $reset_link->code = $random;
                $date = new DateTime();
                $date->modify("+1 day");
                $valid_until = $date->format("Y-m-d H:i:s");
                $reset_link->valid_until = $valid_until;
                $reset_link->save();

                // Notify the user about the reset link
                $data['link'] = $url;
                try {
                    Mail::send('emails.auth.password_reset_link', $data, function($message) use ($user)
                    {
                      $message->to($user->email)->subject('LWG: Password reset request');
                    });
                } catch (Exception $ex) {
                    $this->log_event("Mail could not be sent! Error message: ".$ex->getMessage(),'error');
                }

                return Redirect::to('reset_link_sent');

            } catch (Exception $ex) {
                $this->log_event("Request for reset link raised an error: ".$ex->getMessage(),'error');
                return Redirect::back()->with('send_reset_link_failed','yes');
            }
        }
    }

    public function reset_link_sent(){
        $title = 'Password reset requested';
        $content = view('reset_link_sent');
        return $this->load_view($title, $content);
    }

    public function set_password_page($code){
        $linkInfo = PasswordResetLink::where('code','=',$code)->first();

        if(!empty($linkInfo)){
            $now = new DateTime();
            $valid_until = new DateTime($linkInfo->valid_until);
            if($now > $valid_until){
                $this->log_event("Expired reset link.",'authnetication');
                $title = 'Invalid link';
                $content = view('errors.expired_link');

                return $this->load_view($title, $content);
            } else {
                $title = 'Password reset page';
                $content = view('set_password_page')
                        ->with('code',$code);
                return $this->load_view($title, $content);
            }
        } else {
            $this->log_event("Illegal reset link.",'authentication');
            return view('errors.illegal');
        }
    }

    public function set_password($code){
        $linkInfo = PasswordResetLink::where('code','=',$code)->first();

        if(!empty($linkInfo)){
            $now = new DateTime();
            $valid_until = new DateTime($linkInfo->valid_until);
            if($now > $valid_until){
                $this->log_event("Expired reset link.",'authnetication');
                $title = 'Invalid link';
                $content = view('errors.expired_link');

                return $this->load_view($title, $content);
            } else {

                $form = Input::all();
                $rules = config('validation.password_reset');
                $validation = Validator::make($form,$rules);

                if ($validation->fails()){
                    return Redirect::back()->withInput()->withErrors($validation);
                } else {
                    try {
                        $user = GuiUser::find($linkInfo->uid);
                        $linkInfo->delete();
                        $user->password = Hash::make($form['new_password']);
                        $user->save();

                        Session::flash('toastr',array('success','Your password was reset!'));
                        return Redirect::to('/');
                    } catch (Exception $ex) {
                        $this->unexpected_error('An unexpected error occured while trying to reset your password. Message: '.$ex->getMessage());
                    }
                }
            }
        } else {
            $this->log_event("Illegal reset link.",'authentication');
            return view('errors.illegal');
        }
    }

}