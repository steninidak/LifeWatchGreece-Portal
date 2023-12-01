<?php

namespace App\Http\Controllers;

use Hash;
use Auth;
use HTML;
use Redirect;
use Session;
use View;
use Input;
use Config;
use Validator;
use DateTime;
use Mail;
use DB;
use Captcha;
use DateTimeZone;
use App\Models\Contacted;
use App\Models\GuiUser;
use App\Models\PortalApp;
use App\Models\OneTimeMessage;
use App\Models\Announcement;
use Illuminate\Http\Request;

/**
 * A controller that implements features common to most controllers.
 *
 * @author   Alexandros Gougousis
 */
class LoggedController extends WebController {

    public function __construct() {
        parent::__construct();
        $this->template_view = 'internal_wrapper.template';
        $this->view_head = 'internal_wrapper.head';
        $this->view_body_top = 'internal_wrapper.body_top';
        $this->view_body_bottom = 'internal_wrapper.body_bottom';
    }

    public function home(Request $request){

        $this->log_request($request,'portal');

        $announcements = Announcement::getActiveAnnouncements();
        $apps = PortalApp::select('codename','title','description','url','image','status','hide_from_ui')->get();
        $user = GuiUser::find(Auth::guard('web')->id());

        // Get new messages for home page
        // (if it is a new user, we are not going to display all the message history. Maybe last day's ?)
        if(!empty($user->last_home_visit)){
            $one_time_messages = OneTimeMessage::where('created_at','>',$user->last_home_visit)->get();
        } else {
            $one_time_messages = array();
        }

        // Update the last time user visited home page
        $user->last_home_visit = date("Y-m-d H:i:s");
        $user->save();

        $content = view('home')
                ->with('apps',$apps)
                ->with('announcements',$announcements)
                ->with('user_permissions',Session::get('user_permissions'))
                ->with('one_time_messages',$one_time_messages)
                ->with('timezone',$user->timezone);

        return $this->load_view('Home Page',$content);

    }

    /*
     * The user's profile page
     *
     * @return string
     */
    public function profile(Request $request){
        $this->log_request($request,'portal');

        $user_info = GuiUser::find(Auth::guard('web')->id());
        $timezones = DateTimeZone::listIdentifiers();

        $content = View::make('profile')
                ->with('user',$user_info)
                ->with('timezones',$timezones);

        return $this->load_view('My Profile', $content);
    }

    public function update_profile(){
        $form = Input::all();
        $rules = Config::get('validation.profile');
        $validation = Validator::make($form,$rules);

        if ($validation->fails()){
            return Redirect::back()->withErrors($validation);
        } else {
            $uid = Auth::guard('web')->user()->id;
            $user = GuiUser::find($uid);
            $user->affiliation = $form['affiliation'];
            $user->position = $form['position'];
            $user->timezone = $form['timezone'];
            $user->save();

            Session::flash('toastr',array('success','Profile updated successfully!'));
            return Redirect::to('profile');
        }
    }

    public function change_password(){
        $form = Input::all();
        $rules = Config::get('validation.change_password');
        $validation = Validator::make($form,$rules);

        if ($validation->fails()){
            return Redirect::back()->withErrors($validation)->with('pwd_failed','yes');
        } else {
            $uid = Auth::guard('web')->user()->id;
            $user = GuiUser::find($uid);
            $user->password = Hash::make($form['new_password']);
            $user->save();

            Session::flash('toastr',array('success','Password changed successfully!'));
            return Redirect::to('profile');
        }
    }

    /*
     * Logs out a user
     *
     * @return Redirect
     */
    public function logout(){
        Auth::guard('web')->logout();
        return Redirect::to('/');
    }

    public function contact_us(Request $request){
        $this->log_request($request,'portal');

        $apps = PortalApp::select('codename','title')->get();
        $options = array('generic'=>'Generic');
        foreach($apps as $app){
            $options[$app->codename] = $app->title;
        }
        $content = View::make('contact_us')
                ->with('options',$options);

        return $this->load_view('Contact Us', $content);
    }

    public function contacted(){

        // Validate form data
        $form = Input::all();
        $rules = Config::get('validation.contact_us');
        $validation = Validator::make($form,$rules);

        if ($validation->fails()){
            return Redirect::back()->withErrors($validation);
        } else {
            $user = Auth::guard('web')->user();

            // Retrieve all information about the contact form
            $data['subject'] = $form['subject'];
            $data['email'] = $user->email;
            $data['message_body'] = $form['message'];
            $data['related_to'] = $form['related_to'];
            $data['ip'] = $ip = getenv('HTTP_CLIENT_IP')?:
                                getenv('HTTP_X_FORWARDED_FOR')?:
                                getenv('HTTP_X_FORWARDED')?:
                                getenv('HTTP_FORWARDED_FOR')?:
                                getenv('HTTP_FORWARDED')?:
                                getenv('REMOTE_ADDR');
            $date = new DateTime();
            $data['when'] = $date->format("Y-m-d H:i:s");
            $settings = $this->system_settings;

            try {
                // Send the email
                Mail::send('emails.to_info.contact_form', $data, function($message) use ($form,$settings)
                {
                    $message->to($settings['contact_email'])->subject('LWG Portal: Contact Form ('.$form['related_to'].')');
                });

                // Store the contact form information into the database
                $contacted = new Contacted();
                $contacted->when = $data['when'];
                $contacted->email = $data['email'];
                $contacted->subject = $data['subject'];
                $contacted->message = $data['message_body'];
                $contacted->related_to = $data['related_to'];
                $contacted->ip = $data['ip'];
                $contacted->status = 1;
                $contacted->save();

                return Redirect::to('successful_contact');
            } catch (Exception $ex) {
                // Log the mailing failure
                $this->log_event("Mail could not be sent! Error message: ".$ex->getMessage(),'error');

                // Store the contact form information into the database
                $contacted = new Contacted();
                $contacted->when = $data['when'];
                $contacted->email = $data['email'];
                $contacted->subject = $data['subject'];
                $contacted->message = $data['message_body'];
                $contacted->related_to = $data['related_to'];
                $contacted->ip = $data['ip'];
                $contacted->status = 0;
                $contacted->save();

                return Redirect::to('failed_contact');
            }
        }
    }

    public function successful_contact(){
        $title = 'Message Sent';
        $content = View::make('successful_contact');

        return $this->load_view($title, $content);
    }

    public function failed_contact(){
        $title = 'Message Failed';
        $content = View::make('failed_contact');

        return $this->load_view($title, $content);
    }

}