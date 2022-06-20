<?php

namespace App\Validator;

use Illuminate\Database\Eloquent\Model;

class ValidatorFiles extends Model {

	const PAUSE = 0;
	const IN_PROGRESS = 1;
	const FINISHED = 2;
	const IN_MODERATION = 4;

	const CHECK_NUMBER_PRICE = 0.10;
	const TYPE = 'validator_check';

	protected $table = 'validator_files';

	public $timestamps = true;

    protected $fillable = [
        'user_id',
        'title',
        'file',
        'status',
        'db_checked',
    ];

	public static function status($status) {
		$status_text = '';
		switch ($status) {
			case self::PAUSE: $status_text = 'Приостановлен'; break;
			case self::IN_PROGRESS: $status_text = 'В процессе'; break;
			case self::FINISHED: $status_text = 'Завершен'; break;
			case self::IN_MODERATION: $status_text = 'В ожидании '; break;
		}
		return $status_text;
	}
}
