<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentLog extends Model {	
    
    protected $table = 'agent_logs';
    public $timestamps = false; 
    protected $fillable = array('title');
    
}
