<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UserNotification extends Model
{   
	protected $table = 'user_notifications';

    protected $fillable = ['user_id', 'title', 'message', 'note', 'read_at', 'group', 'about_id']; // Maybe should add 'type' field in future

    public $timestamps = true;

    public function user() {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function formattedDate() {
        return Carbon::parse($this->created_at)->addHours(6)->format('d.m.Y H:i:s');
    }

    public function formattedReadAt() {
        if($this->read_at) {
            return Carbon::parse($this->read_at)->addHours(6)->format('d.m.Y H:i:s');
        } else {
            return '';
        }
        
    }

    /**
     * @param int $id
     * @return Model<UserNotification>
     */
    public static function getById(
        int $id
    ): Model
    {
        return self::query()->findOrFail($id);
    }

    /**
     * @param array|int $userIds
     * @return Builder
     */
    public static function getByUserId(
        array|int $userIds
    ): Builder
    {
        return self::query()
            ->when(is_integer($userIds), fn($query) => $query->where('user_id', $userIds))
            ->when(is_array($userIds), fn($query) => $query->whereIn('user_id', $userIds));
    }

    /**
     * @param string $title
     * @param string $message
     * @param int $userId
     * @param int $aboutId
     * @return Model|Builder
     */
    public static function createNotification(
        string $title,
        string $message,
        int $userId,
        int $aboutId = 0
    ): Model|Builder
    {
        return self::query()->create([
            'title'     => $title,
            'message'   => $message,
            'user_id'   => $userId,
            'about_id'  => $aboutId
        ]);
    }
}
