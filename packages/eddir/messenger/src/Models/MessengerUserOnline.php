<?php

namespace Eddir\Messenger\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MessengerUserOnline extends Model {
    protected $table = 'messenger_users_online';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'socket_id',
        'domain',
        'last_seen',
    ];

    public function user() {
        return $this->belongsTo( config( 'auth.providers.users.model' ) );
    }

    // on update, update the timestamp
    public function touch( $attribute = null, $touches = true ) {
        // set last_seen to current timestamp in mysql format
        $this->last_seen = date( 'Y-m-d H:i:s' );
        $this->save();
    }

    /**
     * @param $chat_id
     *
     * @return Collection
     */
    public static function getOnlineMembers( $chat_id ): Collection {
        return DB::table( 'messenger_chat_users AS t1' )
                 ->select( 't1.user_id' )
                 ->join( 'messenger_chat_users AS t2', function ( $join ) {
                     $join->on( 't1.chat_id', '=', 't2.chat_id' )
                          ->on( 't1.user_id', '!=', 't2.user_id' );
                 } )
                 ->join( 'messenger_users_online AS o', 'o.user_id', '=', 't1.user_id' )
                 ->where( 't1.chat_id', '=', $chat_id )
                 ->distinct()
                 ->get();
    }
}
