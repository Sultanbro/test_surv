<?php

namespace App\Models\Anviz;

use Illuminate\Database\Eloquent\Model;
use App\Models\Anviz\AnvizUser;

class Time extends Model
{	
	protected $connection = 'sqlsrv_anviz';
    protected $table = 'Checkinout';

	public $timestamps = true;

    public $primaryKey  = 'Logid';
 
	protected $fillable = [
	    "Logid",
        "Userid",
        "CheckTime",
        "CheckType",
        "Sensorid",
        "WorkType", 
        "AttFlag",
        "Checked", 
        "Exported",
        "OpenDoorFlag",
    ]; 

	public function user() {
        $user = AnvizUser::where('Userid', $this->Userid)->first();
		return $user ? $user->name() : null;
    }
 
}
