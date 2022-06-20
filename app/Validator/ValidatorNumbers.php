<?php

namespace App\Validator;

use Illuminate\Database\Eloquent\Model;

class ValidatorNumbers extends Model {

	const ALIVE = 1;
	const DISCONNECTED = -1;
	const DIED = -2;
	const NONE = 0;
	const WAIT = 2;

	protected $table = 'validator_numbers';

	public $timestamps = true;

    protected $fillable = [
        'file_id',
        'name',
        'number',
	    'cell',
	    'is_alive',
        'cause',
        'session_id',
	    'bilsec'
    ];

	public static function state($state) {
		$state_text = '';
		switch ($state) {
			case self::NONE: $state_text = 'В очереди'; break;
			case self::WAIT: $state_text = 'В очереди'; break;
			case self::ALIVE: $state_text = 'Активный'; break;
			case self::DIED: $state_text = 'Не активный'; break;
			case self::DISCONNECTED: $state_text = 'Отключенный'; break;
		}
		return $state_text;
	}
}
