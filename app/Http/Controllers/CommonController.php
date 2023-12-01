<?php

namespace App\Http\Controllers;

use Auth;
use Session;
use Redirect;
use App\Models\SystemLog;
use App\Models\Setting;
use App\Models\GuiUser;
use App\Models\DirectRequestLog;
use App\Models\SessionLog;
use App\Models\AgentLog;
use Illuminate\Http\Request;

/**
 * A controller that implements features common to most controllers.
 *
 * @author   Alexandros Gougousis
 */
class CommonController extends Controller {

    protected $system_settings;
    protected $is_mobile;
    protected $spiders = array(
        'Googlebot',
        'bingbot',
        'Baiduspider',
        'Slurp',
        'LinkedInBot',
        'FlipboardProxy',
        'facebookexternalhit',
        'spaidu',
        'witterbot',
        'YandexBot',
        'SeznamBot',
        'linkdexbot',
        'MojeekBot',
        'Robot',
        'robot',
        'Twitterbot',
        'Wotbox',
        'Crawler',
        'crawler',
        'MJ12bot',
        'bot.html',
        'bot.php'
    );

    public function __construct() {

        // Identify if the request comes from a mobile client
        if(isset($_SERVER['HTTP_AAAA1']))
            $this->is_mobile = true;
        else
            $this->is_mobile = false;

        if(Auth::guard('web')->check()){
            $this->loadUserInfo();
        }

        // Load system settings
        $this->system_settings = Setting::getAllSettings();

    }

    /*
     * Loads user information to session.
     *
     * The groups that the user is a member of and all the user's
     * permissions are loaded to session.
     */
    protected function loadUserInfo(){

        // if the user is disabled, log him out and redirect him to login page
        if(Auth::guard('web')->user()->status == 'disabled'){
            Auth::guard('web')->logout();
            Session::flash('toastr',array('error','You account is not activated!'));
            return Redirect::to('/');
        }

        // Load user's credentials
        $user_id = Auth::guard('web')->id();
        $user_groups = GuiUser::getUserGroupNames($user_id);
        $user_permissions = GuiUser::getUserPrivileges($user_id);

        Session::put('user_permissions',$user_permissions);
        Session::put('user_groups',$user_groups);
    }

    protected function randomString($length = 32) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $rString = '';
        for ($i = 0; $i < $length; $i++) {
            $rString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $rString;
    }

    /*
     * Logs an event
     *
     * @param string $message
     * @param string $category
     */
    protected function log_event($message,$category){

        // Define the actor
        if(Auth::guard('web')->check()) {
            $user_id = Auth::guard('web')->user()->id;            // GUI user
        } elseif (Auth::guard('api')->check()) {
            $user_id = Auth::guard('api')->user()->id;           // API user
        } else {
            $user_id = 0;                           // system action
        }

        $action = app('request')->route()->getAction();
        $controller = class_basename($action['controller']);
        list($controller, $action) = explode('@', $controller);

	$log = new SystemLog();
	$log->when 	=   date("Y-m-d H:i:s");
	$log->actor 	=   $user_id;
	$log->controller =  $controller;
	$log->method 	=   $action;
	$log->message 	=   $message;
        $log->category   =   $category;
	$log->save();
    }

    protected function log_request($request, $type,$codename=null){

            $spider = false;
            $agent = $_SERVER['HTTP_USER_AGENT'];

            // Log the agent
            if(AgentLog::where('title',$agent)->count() == 0){
                $agentLog = new AgentLog(['title'=>$agent]);
                $agentLog->save();
            }

            if(!Auth::guard('web')->check()){
                // Check if it is a known bot/spider (a bot cannot be logged in)
                foreach($this->spiders as $spider_name){
                    if(strpos($agent,$spider_name) !== false){
                        $spider = true;
                        break;
                    }
                }
            }

            // Best effort client IP identification
            $ip =   getenv('HTTP_CLIENT_IP')?:
                    getenv('HTTP_X_FORWARDED_FOR')?:
                    getenv('HTTP_X_FORWARDED')?:
                    getenv('HTTP_FORWARDED_FOR')?:
                    getenv('HTTP_FORWARDED')?:
                    getenv('REMOTE_ADDR');

            // Log traffic (if not aj AJAX request)
            if(!$request->ajax() && (!$spider)){

                // Identify browser
                $browser_identified = true;
                $browser = new \Sinergi\BrowserDetector\Browser();
                if($browser == 'unknown'){
                    $browser = $_SERVER['HTTP_USER_AGENT'];
                    $this->log_event($codename." with browser = ".$_SERVER['HTTP_USER_AGENT']."from IP = ".$ip,'warning');
                    $browser_identified = false;
                } else if( strpos($_SERVER['HTTP_USER_AGENT'],'BestHTTP') !== false ){
                    $browser = 'BestHTTP';
                    $browser_identified = false;
                }

                // Identify O.S
                $os = new \Sinergi\BrowserDetector\Os();

                // Identify language
                $lang = new \Sinergi\BrowserDetector\Language();
                switch($type){
                    case 'portal':
                        $request_log = new DirectRequestLog();
                        $request_log->path = $request->path();
                        break;
                    case 'vlab':
                        $request_log = new SessionLog();
                        $request_log->app = $codename;
                        break;
                    default:
                        $this->log_event("log_request() was called with type = '".$type."'.",'warning');
                        return;
                        break;
                }
                $request_log->agent = $agent;
                if($browser_identified)
                    $request_log->browser = $browser->getName();
                else
                    $request_log->browser = $browser;
                $request_log->os = $os->getName();
                $request_log->lang = $lang->getLanguage();

                // If this is a vlab request, look for the original IP
                if($type == 'vlab'){
                    if(isset($_SERVER['HTTP_X_ORIGINAL_IP'])){
                        // Checking for multiple IPs because the format of X-Forwarded-For HTTP header is
                        // X-Forwarded-For: client1, proxy1, proxy2, ...
                        // So the IP address of the client that we want should be the first one in the list
                        if( strpos($_SERVER['HTTP_X_ORIGINAL_IP'],',') !== false ){
                            $ips = explode(',',$_SERVER['HTTP_X_ORIGINAL_IP']);
                            $request_log->ip = trim($ips[0]);
                        } else {
                            $request_log->ip = $_SERVER['HTTP_X_ORIGINAL_IP'];
                        }
                        if(strpos($request_log->ip,'188.143') !== false){
                            $this->log_event("Russia? IP = ".$request_log->ip.' , Browser = '.$request_log->browser,'info');
                        }
                    } else {
                        // If IP was not sent by portal application
                        $this->log_event("$codename request without IP came from $ip",'warning');
                    }
                } else {
                    $request_log->ip = $ip;
                }

                $request_log->save();

                // Log HTTP_USER_AGENT for microct and lm, to find those with unidentified browsers
                /*
                if(in_array($codename,array('microct','lm'))){
                    $this->log_event($codename." with browser = ".$_SERVER['HTTP_USER_AGENT']."from IP = ".$ip,'warning');
                }
                */
            }

        }

}