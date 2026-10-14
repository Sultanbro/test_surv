<?php

namespace App\Models\CallCenter;

use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{   
    protected $table = 'agents';
    protected $connection = 'call_center';

    public $timestamps = false;
    
    protected $primaryKey = 'name';

    const CONTACT_PREFIX = "[leg_timeout=10,continue_on_fail=true,execute_on_answer='lua record.lua']user/";

    protected $fillable = [
        'name', // 1234@voip.cfpsa.ru
        'system', // single_box
        'uuid', // 
        'type', // callback
        'contact', // [leg_timeout=10, continue_on]
        'status', // Logged Out
        'state', // Waiting
        'max_no_answer',
        'wrap_up_time',
        'reject_delay_time',
        'busy_delay_time',
        'no_answer_delay_time',
        'last_bridge_start',
        'last_bridge_end',
        'last_offered_call',
        'last_status_change',
        'no_answer_count', 
        'calls_answered', 
        'talk_time',
        'ready_time',
        'external_calls_count',
    ];

}
