<?php

namespace App\Models\Anviz;

use Illuminate\Database\Eloquent\Model;
use DB;
use Exception;

class DBAnviz
{	
	const TABLES = [
        'times' => 'Checkinout', // фиксация прихода ухода
        'users' => 'Userinfo', // пользователи
        'depts' => 'Dept', // отделы
        'fingers' => 'UserFinger', // отпечатки 
        'OPinfo' => 'OPinfo',  // возможно пользователь Программы AIM Crosschex
        'UserTempShift' => 'UserTempShift', 
        'UserShift' => 'UserShift', 
        'UserPower' => 'UserPower',
        'UserLeave' => 'UserLeave',
        'UserAutoClass' => 'UserAutoClass',
        'TimeTable' => 'TimeTable',
        'Status' => 'Status', // Статусы In Out
        'StatItems' => 'StatItems', // Normal Late Early
        'SchTime' => 'SchTime',
        'Schedule' => 'Schedule',
        'OutProg' => 'OutProg',
        'OPLog' => 'OPLog',
        'OPGroup' => 'OPGroup', // Роли Админ или user
        'OPDept' => 'OPDept', // Возможно OPId == OPDept pivot table
        'OPClient' => 'OPClient', // OPID == ClientNumber
        'LeaveClass' => 'LeaveClass',
        'Holiday' => 'Holiday',
        'FingerClient' => 'FingerClient', // возможно инфо об оборудовании есть Ip
        'DefineField' => 'DefineField',
        'CheckLog' => 'CheckLog',
        'BasePara' => 'BasePara', // возможно настройка оборудования
        'AccessTime' => 'AccessTime',
        'AccessGroup' => 'AccessGroup',
        'Z_MemUClass' => 'Z_MemUClass',
        'Z_MemRecord' => 'Z_MemRecord',
        'Z_MemAbnor' => 'Z_MemAbnor',
        'WorkCode' => 'WorkCode',
    ]; 

	public function tables() { // Эта функция не используется
        ini_set('mssql.charset', 'UTF-8');
		$q = DB::connection('sqlsrv_anviz')->table('Checkinout')->select('CheckTime')->where('Userid', 52)->get();

        foreach($q as $item) {
            $item->Name = mb_detect_encoding($item->Name, mb_detect_order(), true) === 'UTF-8' ? $item->Name : iconv('iso-8859-1', 'utf-8', $item->Name);
        }
	}

    public static function table(String $table)
    {
        try {
            $result = DB::connection('sqlsrv_anviz')->table(self::TABLES[$table])->get();
        } catch(Exception $e) {
            $result = null;
        }
        return $result;
    }

}
