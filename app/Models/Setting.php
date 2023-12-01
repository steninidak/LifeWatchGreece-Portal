<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model {
    
    protected $table = 'settings';
    public $timestamps = false;
 
    public static function getAllSettings(){
        $settingsSet = DB::table('settings')->select('name','value')->get();
        $settings = array();
        foreach($settingsSet as $setting){
            $settings[$setting->name] = $setting->value;
        }
        return $settings;
    }
    
}
