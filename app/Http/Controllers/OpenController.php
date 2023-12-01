<?php

namespace App\Http\Controllers;

use Auth;
use View;
use Session;
use Input;
use Config;
use Validator;
use DateTime;
use Redirect;
use Mail;
use Html;
use Illuminate\Http\Request;
 
/**
 * A controller that implements features common to most controllers.
 * 
 * @author   Alexandros Gougousis
 */
class OpenController extends WebController {
    
    public function __construct() {
        parent::__construct();       
        $this->template_view = 'external_wrapper.template';
        $this->view_head = 'applications.head';
        $this->view_body_top = 'external_wrapper.body_top';
        $this->view_body_bottom = 'external_wrapper.body_bottom';
    }
    
    public function orcid_howto(Request $request){
        
        $this->log_request($request,'portal');
        $content = view('orcid_howto');         
        return $this->load_view('How to login with ORCID', $content);
    }
    
    public function mobile(Request $request){              
        $this->log_request($request,'portal');
        
        // If the user is logged in
        if(Auth::guard('web')->check()){
            $this->template_view = 'internal_wrapper.template';
            $this->view_head = 'applications.head';
            $this->view_body_top = 'internal_wrapper.body_top';
            $this->view_body_bottom = 'internal_wrapper.body_bottom';
        } 
                                
        $content = view('applications.mobile');         
        return $this->load_view('Mobile Applications', $content);
    }        
 
    public function mobile_video(Request $request){ 
        $this->log_request($request,'portal');
        
        // If the user is logged in
        if(Auth::guard('web')->check()){
            $this->template_view = 'internal_wrapper.template';
            $this->view_head = 'applications.head';
            $this->view_body_top = 'internal_wrapper.body_top';
            $this->view_body_bottom = 'internal_wrapper.body_bottom';
        } 
        
        $content = view('applications.mobile_video');         
        return $this->load_view('Mobile Applications Video', $content);
    }
    
    public function biospec(){
        
        // If the user is logged in
        if(Auth::guard('web')->check()){  
            
            $this->template_view = 'internal_wrapper.template';
            $this->view_head = 'applications.head';
            $this->view_body_top = 'internal_wrapper.body_top';
            $this->view_body_bottom = 'internal_wrapper.body_bottom';
        } 
    
        $title = 'Biological Specimens Collection Services';                        
        $content = view('applications.biospec');        
        return $this->load_view($title, $content);
        
    }
    
    public function biospec_subscribe(){        
        
        $form = Input::all();                                  
        $rules = Config::get('validation.biospec_subscribe');
        $validation = Validator::make($form,$rules);

        if ($validation->fails()){                    
            return Redirect::back()->withInput()->withErrors($validation);
        } else {
            // Check submission time stamp (24 hours are substracted from both dates)
            $current_stamp = (new DateTime())->modify('next day');
            // The form should be submitted at least 5 sec after the form was loaded
            $submission_lower_limit = (new DateTime($form['stamp']))->add(DateInterval::createFromDateString('5 seconds'));
            if($submission_lower_limit > $current_stamp){
                    $validation->getMessageBag()->add('generic', 'Something went wrong! Please try again later.');
                    return Redirect::back()->withInput()->withErrors($validation);
            }
            
            // Send email to notify biospec admin that a user requested subscription
            $data['fullname'] = $form['fullname'];
            $data['email'] = $form['email'];
            $settings = $this->system_settings;
            try {
                
                $recipients = explode(';',$settings['biospec_mail_recipient']);
                foreach($recipients as $recipient){
                    Mail::send('emails.biospec.subscription', $data, function($message) use ($form,$recipient)
                    {
                        $message->to($recipient)->subject('Biospec Portal Page:  Subscription');
                    });
                }                                
            } catch (Exception $ex) {
                Session::flash('toastr',array('error','Something went wrong! Please try again later.'));
                $this->log_event("Biospec subscription notification could not be sent! Error message: ".$ex->getMessage(),'error');
                return Redirect::back();     
            }  
            
            // Load a flash message
            Session::flash('toastr',array('success','You request submitted successfully!'));
            
            //Redirect back to biospec page
            return Redirect::back();                        
            
        }
        
    }
    
    public function biospec_unsubscribe(){
        $form = Input::all();                                  
        $rules = Config::get('validation.biospec_subscribe');
        $validation = Validator::make($form,$rules);

        if ($validation->fails()){                    
            return Redirect::back()->withInput()->withErrors($validation);
        } else {
            // Check submission time stamp (24 hours are substracted from both dates)
            $current_stamp = (new DateTime())->modify('next day');
            // The form should be submitted at least 5 sec after the form was loaded
            $submission_lower_limit = (new DateTime($form['stamp']))->add(DateInterval::createFromDateString('5 seconds'));
            if($submission_lower_limit > $current_stamp){
                    $validation->getMessageBag()->add('generic', 'Something went wrong! Please try again later.');
                    return Redirect::back()->withInput()->withErrors($validation);
            }
            
            // Send email to notify biospec admin that a user requested subscription
            $data['fullname'] = $form['fullname'];
            $data['email'] = $form['email'];
            $settings = $this->system_settings;
            try {
                Mail::send('emails.biospec.unsubscription', $data, function($message) use ($form,$settings)
                {
                    $message->to($settings['biospec_mail_recipient'])->subject('Biospec Portal Page:  Unsubscription');
                });
            } catch (Exception $ex) {
                Session::flash('toastr',array('error','Something went wrong! Please try again later.'));
                $this->log_event("Biospec unsubscription notification could not be sent! Error message: ".$ex->getMessage(),'error');
                return Redirect::back();     
            }  
            
            // Load a flash message
            Session::flash('toastr',array('success','You request submitted successfully!'));
            
            //Redirect back to biospec page
            return Redirect::back();                        
            
        }
    }
    
}