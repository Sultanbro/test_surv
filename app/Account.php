<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{	
	protected $connection = 'callibro';
    protected $table = 'call_account';

	public $timestamps = true;

	const ADMIN = 'admin';
	const SUPERVISOR = 'supervisor';
	const DIALER_ADMIN = 'dialer_admin';
	const SIP_ADMIN = 'sip_admin';
    const OPERATOR = 'operator';
    const GUEST = 'guest';

    const SESSION_OWNER_USER_ID = 'OWNER_USER_ID';

    const IS_ROOT = 1;

    const NEW_STATUS = 0;
    const ACTIVE_STATUS = 1;
    const DELETE_STATUS = 2;

	protected $fillable = [
	    'password',
	    'owner_uid',
	    'is_root',
        'email',
        'name',
        'surname',
        'logo',
        'status',
        'role',
        'online',
        'today_auth_time',
        'current_state',
        'ws_session_id',
        'current_state_time',
        'activate_key'
    ];

	public function owner() {
		return $this->belongsTo( 'App\User', 'owner_uid', 'id');
    }
    
    public function call()
    {
        return $this->hasOne('App\Obzvon\Call', 'call_account_id', 'id');
    }
    
    public function calls()
    {
        return $this->hasMany('App\Call', 'call_account_id', 'id');
    }

    public static function currentAccount($user_email, $owner_uid){
        $account = Account::whereRaw('LOWER(TRIM(email)) = "'. strtolower(trim($user_email)).'"')->where('owner_uid', $owner_uid)->first();
        return $account;
    }

    public static function accountsOfOwner($owner_uid, $except_ids = []){
        $accounts = Account::where('owner_uid', $owner_uid)->whereNotIn('id', $except_ids)->get();
        return $accounts;
    }

    public static function rootAccount($owner_uid){
        $root = Account::where('owner_uid', $owner_uid)->where('is_root', 1)->first();
        return $root;
    }

    public static function roots($user_email){
        $accounts = Account::whereRaw('LOWER(TRIM(email)) = "'. strtolower(trim($user_email)).'"')
            ->where('status', self::ACTIVE_STATUS)
            ->orderBy( 'id', 'asc' )->get();
        $roots = [];

        foreach ($accounts as $account) {
            if($account->owner) {
                $roots[] = self::rootAccount($account->owner->id);
            }
        }

        return $roots;
    }

    public static function setOnline($id, $is_online = 1, $ws_session_id = null) {
        $account  = Account::find($id);
        if($account) {
            $account->online = $is_online;
            $account->ws_session_id = $ws_session_id;
            $account->save();
        }
    }

	public function setRoleAttribute($role) {
		$this->attributes['role'] = serialize($role);
	}

	public function getRoleAttribute($role) {
		return unserialize($role);
	}

	public function getRoleText() {
		if(!is_array($this->role)) {
			return '';
		}
		if(in_array(self::ADMIN, $this->role) || in_array(self::DIALER_ADMIN, $this->role)) {
			return 'Администратор';
		}
		if(in_array(self::SUPERVISOR, $this->role)) {
			return 'Супервайзер';
		}
        if(in_array(self::OPERATOR, $this->role)) {
            return 'Оператор';
        }

		return '';
	}
}
