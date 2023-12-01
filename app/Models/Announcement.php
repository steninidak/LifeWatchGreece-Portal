<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DateTime;

class Announcement extends Model {	
    
    protected $table = 'announcements';
 
    public static function getActiveAnnouncements(){
        $nowObj = new DateTime('now');
        $now = $nowObj->format('Y-m-d H:i:s');
        $list = Announcement::where('valid_from','<',$now)
                    ->where('valid_to','>',$now)
                    ->orderBy('valid_from','DESC')
                    ->get();
        return $list;
    }
    
}
