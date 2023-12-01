<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\QueueStat;
use App\Models\ResourceLog;
use App\Models\SystemLog;
use phpseclib\Net\SSH2;

class QueueStatsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logQueueStats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Logs the queue status of biocluster';

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
        try {
            $port = 22;
            $username = "agougousis";
            $password = "mhdenER0-";
           
            $ssh = new SSH2('biocluster.her.hcmr.gr');
            if (!$ssh->login($username,$password)) {
                exit('Login Failed');
            }

            // Read queue status
            $df = $ssh->exec('/usr/bin/qstat -a');
            $df_lines = preg_split('/[\n]/',$df);            

            $stats = array();
            $datetime = date("Y-m-d H:i:s");
            foreach($df_lines as $line){

                    $line_parts = preg_split('/\s+/',$line);
                    if((count($line_parts) == 11)&&(in_array($line_parts[2],array('fast','batch','bigmem')))){
                        $user = $line_parts[1];
                        $queue_type = $line_parts[2];
                        if(empty($stats[$user])){
                            $stats[$user] = array();
                            if(empty($stats[$user][$queue_type])){
                                $stats[$user][$queue_type] = 1;
                            } else {
                                $stats[$user][$queue_type]++;
                            }                            
                        } else {
                            if(empty($stats[$user][$queue_type])){
                                $stats[$user][$queue_type] = 1;
                            } else {
                                $stats[$user][$queue_type]++;
                            } 
                        }
                    }                               
            }
            
            // Save queue status logs
            foreach($stats as $user => $info){
                if(!empty($info['fast'])){
                    $stat = new QueueStat();
                    $stat->when = $datetime;
                    $stat->user = $user;
                    $stat->jobs = $info['fast'];
                    $stat->queue_type = 'fast';
                    $stat->save();
                }
                if(!empty($info['batch'])){
                    $stat = new QueueStat();
                    $stat->when = $datetime;
                    $stat->user = $user;
                    $stat->jobs = $info['batch'];
                    $stat->queue_type = 'batch';
                    $stat->save();
                }       
                if(!empty($info['bigmem'])){
                    $stat = new QueueStat();
                    $stat->when = $datetime;
                    $stat->user = $user;
                    $stat->jobs = $info['bigmem'];
                    $stat->queue_type = 'bigmem';
                    $stat->save();
                }  
            }
            
            // We need a baseline to fill the gaps for each user's timeline
            $stat = new QueueStat();
            $stat->when = $datetime;
            $stat->user = '-baseline-';
            $stat->jobs = 0;
            $stat->queue_type = 'fast';
            $stat->save();
            
            $stat = new QueueStat();
            $stat->when = $datetime;
            $stat->user = '-baseline-';
            $stat->jobs = 0;
            $stat->queue_type = 'batch';
            $stat->save();
            
            $stat = new QueueStat();
            $stat->when = $datetime;
            $stat->user = '-baseline-';
            $stat->jobs = 0;
            $stat->queue_type = 'bigmem';
            $stat->save();
            
            // Get available resources for each queue
            $datetime = date("Y-m-d H:i:s");
            $df = $ssh->exec('/usr/local/bin/pbs-status');
            $df_lines = preg_split('/[\n]/',$df);
            $fast_free = array();
            $batch_free = array();
            $bigmem_free = array();
            $fast_used = array();
            $batch_used = array();
            $bigmem_used = array();
            foreach($df_lines as $line){
                    $line_parts = preg_split('/\s+/',$line);
                    if((count($line_parts) == 9)&&(in_array($line_parts[1],array('fast','batch','bigmem')))){
                        switch($line_parts[1]){
                            case 'fast':
                                $fast_free[] = $line_parts[8];
                                $fast_used[] = $line_parts[7];
                                break;
                            case 'batch':
                                $batch_free[] = $line_parts[8];
                                $batch_used[] = $line_parts[7];
                                break;
                            case 'bigmem':
                                $bigmem_free[] = $line_parts[8];
                                $bigmem_used[] = $line_parts[7];
                                break;
                        }                            
                    }
            }
            // Utilization will be calculated by percentage of CPUs that are considered free across all nodes
            $fast_load = array_sum($fast_used)/(array_sum($fast_free)+array_sum($fast_used));
            $batch_load = array_sum($batch_used)/(array_sum($batch_free)+array_sum($batch_used));
            $bigmem_load = array_sum($bigmem_used)/(array_sum($bigmem_free)+array_sum($bigmem_used));
            
            $stat = new ResourceLog();
            $stat->when = $datetime;
            $stat->utilization = $fast_load;
            $stat->queue_type = 'fast';
            $stat->save();
            
            $stat = new ResourceLog();
            $stat->when = $datetime;
            $stat->utilization = $batch_load;
            $stat->queue_type = 'batch';
            $stat->save();
            
            $stat = new ResourceLog();
            $stat->when = $datetime;
            $stat->utilization = $bigmem_load;
            $stat->queue_type = 'bigmem';
            $stat->save();
            
        } catch (Exception $ex) {
            $this->save_log('Logging queue status failed! '.$ex->getMessage(),'error');
        } 
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
