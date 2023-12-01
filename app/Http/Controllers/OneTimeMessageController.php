<?php

namespace App\Http\Controllers;
 
use DB;
use Config;
use Input;
use Auth;
use Validator;
use Redirect;
use Session;
use App\Models\GuiUser;
use App\Models\OneTimeMessage;

/*
 * A controller that handles the one-time message functionality.
 * 
 * @author   Alexandros Gougousis
 */
class OneTimeMessageController extends WebController {
    
    public function __construct() {
        parent::__construct();   
        $this->view_head = 'internal_wrapper.head';
        $this->template_view = 'internal_wrapper.template';
        $this->view_body_top = 'internal_wrapper.body_top';
        $this->view_body_bottom = 'internal_wrapper.body_bottom';
    }
    
    /**
     * Displays the announcements control panel
     * 
     * @return String
     */
    public function manage() {                
        $title = 'One-time Message Management';        
        $messages = DB::table('one_time_message as otm')
            ->join('gui_users', 'otm.from', '=', 'gui_users.id')
            ->select('otm.id', 'otm.body','otm.type','otm.created_at','gui_users.email')
            ->get();
        $user = GuiUser::find(Auth::guard('web')->id());
        
        $content = view('onetime.manage')
                ->with('messages',$messages)
                ->with('timezone',$user->timezone);
        
        return $this->load_view($title, $content);
    }
    
    /**
     * Displays a form for making a new message
     * 
     * @return String
     */
    public function add_page() {  
        $title = 'New message';        
        $content = view('onetime.add');
        
        return $this->load_view($title, $content);
    } 
    
    /**
     * Saves a new message
     * 
     * @return Redirect
     */
    public function add(){
        if(hasPermission('manage_announcements')){
              
                $form = Input::all();                 
                $rules = Config::get('validation.add_one_time_message');
                $validation = Validator::make($form,$rules);

                if ($validation->fails()){                      
                    return Redirect::back()->withInput()->withErrors($validation);
                } else {
                    $uid = Auth::guard('web')->user()->id;
                    
                    $message = new OneTimeMessage();
                    $message->type = $form['type'];
                    $message->body = $form['body'];
                    $message->from = $uid;
                    $message->save();
                    
                    Session::flash('toastr',array('success','The message has been saved!'));
                    return Redirect::to('admin/ome_time_messages');
                }
        } else {
            $this->unauthorized();
        }
    }
    
    /**
     * Displays a page for editing a message
     * 
     * @param int $item_id
     * @return String
     */
    public function edit_page($item_id){
        $title = 'Edit Message';        
        $message = OneTimeMessage::find($item_id);
        $content = view('onetime.edit')
                ->with('message',$message);
        
        return $this->load_view($title, $content);
    }
    
    /**
     * Updates a message
     * 
     * @return Redirect
     */
    public function edit(){
        if(hasPermission('manage_announcements')){
            $form = Input::all();                 
            $rules = Config::get('validation.edit_one_time_message');
            $validation = Validator::make($form,$rules);

            if ($validation->fails()){           
                Session::flash('toastr',array('error','Illegal action!'));
                return Redirect::back()->withInput()->withErrors($validation);
            } else {
                $uid = Auth::guard('web')->user()->id;
                
                $message = OneTimeMessage::find($form['message_id']);
                $message->type = $form['type'];
                $message->body = $form['body'];
                $message->from = $uid;
                $message->save();
                
                Session::flash('toastr',array('success','The message was updated!'));
                return Redirect::back();
            }
        } else {
            $this->unauthorized();
        }
    }
    
    /**
     * Deletes a message
     * 
     * @return Redirect
     */
    public function delete(){
        if(hasPermission('manage_announcements')){
            $form = Input::all();                 
            $rules = Config::get('validation.delete_one_time_message');
            $validation = Validator::make($form,$rules);

            if ($validation->fails()){           
                Session::flash('toastr',array('error','Illegal action!'));
                return Redirect::back()->withInput()->withErrors($validation);
            } else {
                OneTimeMessage::find($form['message_id'])->delete();
                Session::flash('toastr',array('success','The message was deleted!'));
                return Redirect::back();
            }
        } else {
            $this->unauthorized();
        }
    }   
    
}