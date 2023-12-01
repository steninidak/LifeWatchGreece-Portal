<?php

namespace App\Http\Controllers;

use Config;
use Input;
use Auth;
use Validator;
use Redirect;
use DateTime;
use Session;
use App\Models\GuiUser;
use App\Models\Announcement;

/*
 * A controller that handles the announcements functionality.
 *
 * @author   Alexandros Gougousis
 */
class AnnouncementController extends WebController {

    public function __construct() {
        parent::__construct();
        $this->view_head = 'announcements_wrapper.head';
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
        $title = 'Announcement Management';
        $announements = Announcement::getActiveAnnouncements();
        $user_info = GuiUser::find(Auth::guard('web')->id());

        $content = view('announcements.manage')
                ->with('announcements',$announements)
                ->with('timezone',$user_info->timezone);

        return $this->load_view($title, $content);
    }

    /**
     * Displays announcements history
     *
     * @return String
     */
    public function show_all() {
        $title = 'All announcements';
        $announements = Announcement::orderBy('valid_from','DESC')->paginate(20);
        $user_info = GuiUser::find(Auth::guard('web')->id());

        $content = view('announcements.list_all')
                ->with('announcements',$announements)
                ->with('timezone',$user_info->timezone);

        return $this->load_view($title, $content);
    }

    /**
     * Displays a form for making a new announcement
     *
     * @return String
     */
    public function add_page() {
        $title = 'New announcement';
        $content = view('announcements.add');

        return $this->load_view($title, $content);
    }

    /**
     * Saves a new announcement
     *
     * @return Redirect
     */
    public function add(){
        if(hasPermission('manage_announcements')){

                $form = Input::all();
                $rules = Config::get('validation.add_announcement');
                $validation = Validator::make($form,$rules);

                if ($validation->fails()){
                    return Redirect::back()->withInput()->withErrors($validation);
                } else {
                    $uid = Auth::guard('web')->user()->id;

                    $announcement = new Announcement();
                    $announcement->title = $form['title'];
                    $announcement->body = $form['body'];
                    $from_date = DateTime::createFromFormat('d/m/Y H:i',$form['valid_from']);
                    $announcement->valid_from = $from_date->format('Y/m/d H:i:s');
                    $to_date = DateTime::createFromFormat('d/m/Y H:i',$form['valid_to']);
                    $announcement->valid_to = $to_date->format('Y/m/d H:i:s');
                    $announcement->author = $uid;
                    $announcement->save();

                    Session::flash('toastr',array('success','The announcement has been saved!'));
                    return Redirect::to('admin/announcements');
                }
        } else {
            $this->unauthorized();
        }
    }

    /**
     * Displays a page for editing an announcement
     *
     * @param int $item_id
     * @return String
     */
    public function edit_page($item_id){
        $title = 'Edit Announcement';
        $announcement = Announcement::find($item_id);
        $content = view('announcements.edit')
                ->with('announcement',$announcement);

        return $this->load_view($title, $content);
    }

    /**
     * Updates an announcement
     *
     * @return Redirect
     */
    public function edit(){
        if(hasPermission('manage_announcements')){
            $form = Input::all();
            $rules = Config::get('validation.edit_announcement');
            $validation = Validator::make($form,$rules);

            if ($validation->fails()){
                Session::flash('toastr',array('error','Illegal action!'));
                return Redirect::back()->withInput()->withErrors($validation);
            } else {
                $uid = Auth::guard('web')->user()->id;

                $announcement = Announcement::find($form['announcement_id']);
                $announcement->title = $form['title'];
                $announcement->body = $form['body'];
                $announcement->valid_from = DateTime::createFromFormat('d/m/Y H:i',$form['valid_from']);
                $announcement->valid_to = DateTime::createFromFormat('d/m/Y H:i',$form['valid_to']);
                $announcement->author = $uid;
                $announcement->save();

                Session::flash('toastr',array('success','The announcement was updated!'));
                return Redirect::back();
            }
        } else {
            $this->unauthorized();
        }
    }

    /**
     * Deletes an announcement
     *
     * @return Redirect
     */
    public function delete(){
        if(hasPermission('manage_announcements')){
            $form = Input::all();
            $rules = Config::get('validation.delete_announcement');
            $validation = Validator::make($form,$rules);

            if ($validation->fails()){
                Session::flash('toastr',array('error','Illegal action!'));
                return Redirect::back()->withInput()->withErrors($validation);
            } else {
                Announcement::find($form['announcement_id'])->delete();
                Session::flash('toastr',array('success','The announcement was deleted!'));
                return Redirect::back();
            }
        } else {
            $this->unauthorized();
        }
    }

}