<?php

namespace App\Http\Controllers;

use phpseclib\Net\SSH2;

/**
 * A controller that implements features common to most controllers.
 *
 * @author   Alexandros Gougousis
 */
class WebController extends CommonController {

    protected $template_view = 'external_wrapper.template';
    protected $view_head = 'external_wrapper.head';
    protected $view_body_top = 'external_wrapper.body_top';
    protected $view_body_bottom = 'external_wrapper.body_bottom';


    public function __construct() {
        parent::__construct();
    }

    public function unauthorized(){

        $content = view('errors.unauthorised');
        return $this->load_view('Unauthorised access!', $content);

    }

    protected function load_view($title,$content){

        return view($this->template_view)
                ->with('view_head',$this->view_head)
                ->with('view_body_top',$this->view_body_top)
                ->with('view_body_bottom',$this->view_body_bottom)
                ->with('title',$title)
                ->with('content',$content);
    }

}