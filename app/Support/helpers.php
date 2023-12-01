<?php

function hasPermission($permission){
    if(Auth::guard('web')->check()){
        if(in_array($permission,Session::get('user_permissions')))
            return true;
        else                
            return false;
    } else {
        return false;
    }
}

function hasPermissions($permissions){
    if(Auth::guard('web')->check()){
        $user_permissions = Session::get('user_permissions');
        foreach($permissions as $perm){
            if(!in_array($perm,$user_permissions))
                    return $false;
        }        
        return true;
    } else {
        return false;
    }
}

function flatten($input_array){
    $output = array();
    array_walk_recursive($input_array, function ($current) use (&$output) {
        $output[] = $current;
    });
    return $output;
}

function safe_filename($string) {
    //Lower case everything
    $string = strtolower($string);
    //Make alphanumeric (removes all other characters)
    $string = preg_replace("/[^\pL\pN\s.\(\)_-]/u",'', $string);
    //Clean up multiple dashes or whitespaces
    $string = preg_replace("/[\s-]+/", " ", $string);
    //Convert whitespaces and underscore to dash
    $string = preg_replace("/[\s_]/", "-", $string);
    return $string;
}

function dateToTimezone($date_string,$timezone){
    $mydate = new DateTime($date_string);
    $target_timezone = new DateTimeZone($timezone);
    $mydate->setTimeZone($target_timezone);
    return $mydate->format('d-M-Y H:i:s');
}