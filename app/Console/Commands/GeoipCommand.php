<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\GeoIp;
use App\Models\SessionLog;
use App\Models\SystemLog;

class GeoipCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resolveGeoipTraffic';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resolves IPs to countries/locations for session_logs table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $ipinfo_calls = 0;
        $b_class_private = array('192.168','172.16','172.17','172.18','172.19','172.20','172.21','172.22','172.23','172.24','172.25','172.26','172.27','172.28','172.29','172.30','172.31','172.32');

        // Load already resolved ip-country pairs from database
        $geoips = GeoIp::all();
        $geoip_map = array();   // we have to define it , in case the $geoips is empty
        foreach($geoips as $item){
            $geoip_map[$item->subnet] = $item->country;
        }            
        $new_geoips = array();

        // Find old logs with empty country and not empty IP
        $logs = SessionLog::whereNull('country')->whereNotNull('ip')->orderBy('created_at','ASC')->limit(50)->get();

        foreach($logs as $log){

            if($log->ip == "::1"){
                // In case of a IPv6 loopback IP, it makes no sense to split the addresss
                // Empty IP can come from applications that are not modified yet in order to send their IP
                $log->delete();
            } else {

                if(substr_count($log->ip,'.') > 0){
                    // IPv4
                    $parts = explode(".",$log->ip);
                    $b_class = $parts[0].".".$parts[1];
                    $c_class = $parts[0].".".$parts[1].".".$parts[2];

                    // Check for private IPs
                    if(($parts[0] == '10')||(in_array($b_class,$b_class_private))){                  
                        $log->delete();                     
                    } else {                    
                        // Subnet found in database
                        if(array_key_exists($c_class,$geoip_map)){
                            $log->country = $geoip_map[$c_class];
                            $log->save();                        
                        } else {

                            try {
                                // New subnet
                                $response = file_get_contents("http://ipinfo.io/".$log->ip);
                                $location = json_decode($response);
                                $ipinfo_calls++;
                                if(!empty($location)&&(!empty($location->country))){
                                    $log->country = $location->country;
                                    $log->save();

                                    $new_geoips[$c_class] = $location->country;
                                } else {
                                    $this->save_log('ipinfo response = '.$response,'info');
                                }       
                            } catch (Exception $ex) {
                                $this->save_log('Resolving IP location failed for IPv4 address '.$log->ip.' - Part0 = '.$parts[0].' - logID = '.$log->id.' '.$ex->getMessage(),'error');
                            }

                        }                                        
                    }  
                } else if (substr_count($log->ip,':') > 0) {
                    // IPv6
                    $parts = explode(":",$log->ip);
                    $subnet_small = $parts[0];
                    $subnet = $parts[0].":".$parts[1].":".$parts[2].":".$parts[3];

                    // Check for private IPs
                    if(($subnet_small == 'fe80')){                  
                        $log->delete();                     
                    } else {                    
                        // Subnet found in database
                        if(array_key_exists($subnet,$geoip_map)){
                            $log->country = $geoip_map[$subnet];
                            $log->save();                        
                        } else {

                            try {
                                // New subnet
                                $response = file_get_contents("http://ipinfo.io/".$log->ip);
                                $location = json_decode($response);
                                $ipinfo_calls++;
                                if(!empty($location)&&(!empty($location->country))){
                                    $log->country = $location->country;
                                    $log->save();

                                    $new_geoips[$subnet] = $location->country;
                                } else {
                                    $this->save_log('ipinfo response = '.$response,'info');
                                }
                            } catch (Exception $ex) {
                                $this->save_log('Resolving IP location failed for IPv6 address '.$log->ip.' - Small subnet = '.$subnet_small.' '.$ex->getMessage() ,'error');
                            }

                        }                                        
                    }
                } else {

                }                                           
            }                                                      
        }  

        // If new subnets has been resolved, save them to database
        if(!empty($new_geoips)){
            foreach($new_geoips as $subnet => $country){

                try {
                    $geoip = new GeoIp();
                    $geoip->subnet = $subnet;
                    $geoip->country = $country;
                    $geoip->save();
                } catch (Exception $ex) {
                    $known_subnets = implode(',',array_keys($geoip_map));
                    $this->save_log("Exception while adding subnet $subnet το geoip table - Message= ".$ex->getMessage()." - Dumping geoip keys: ".$known_subnets,'error');
                }

            }
            $num = count($new_geoips);                
        } 

        // Log how many calls to ipinfo took place
        //$this->save_log('ipinfo calls = '.$ipinfo_calls,'info');

        // Delete logs older than 6 months old
        // TO DO ?
    }
    
    private function save_log($message,$category){                    

        $log = new SystemLog();
        $log->when       =   date("Y-m-d H:i:s");
        $log->actor      =   0;
        $log->controller =  'Laravel Command';
        $log->method     =   'resolveGeoipTraffic';
        $log->message    =   $message;
        $log->category   =   $category;
        $log->save();

    }
    
}
