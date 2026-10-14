<?php

namespace App\Models\Anviz;

use Illuminate\Database\Eloquent\Model;

class AnvizUser extends Model
{	
	protected $connection = 'sqlsrv_anviz';
    protected $table = 'Userinfo';

	public $timestamps = true;

	protected $fillable = [
	    "Userid",
        "UserCode",
        "Name",
        "Sex",
        "Pwd",
        "Deptid",
        "Nation",
        "Birthday",
        "EmployDate",
        "Telephone",
        "Duty",
        "NativePlace",
        "IDCard",
        "Address",
        "Mobile",
        "Educated",
        "Polity",
        "Specialty",
        "IsAtt",
        "Isovertime",
        "Isrest",
        "Remark",
        "MgFlag",
        "CardNum",
        "Picture",
        "UserFlag",
        "Groupid",
        "ClassFlag",
        "OtherInfo",
        "admingroupid",
    ];

	public function times() {
		//return $this->belongsTo( 'App\Models\AnvizUser', 'Userid', 'id');
    }
    
    public function name() {
        return mb_convert_encoding($this->Name, "utf-8", "windows-1251");
    }  
    
    public function getConverted() { 
        $this->Name = mb_convert_encoding($this->Name, "utf-8", "windows-1251");
        return $this;
    } 

}
