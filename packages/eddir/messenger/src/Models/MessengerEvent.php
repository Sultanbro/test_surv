<?php

namespace Eddir\Messenger\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

class MessengerEvent extends Model {
    public const TYPE_CHAT_CREATED = 'chat_created';
    public const TYPE_MESSAGE = 'message';
    public const TYPE_TYPING = 'typing';
    public const TYPE_READ = 'read';
    public const TYPE_JOIN = 'join';
    public const TYPE_LEAVE = 'leave';
    public const TYPE_INVITE = 'invite';
    public const TYPE_KICK = 'kick';
    public const TYPE_BAN = 'ban';
    public const TYPE_UNBAN = 'unban';
    public const TYPE_UPDATE = 'update';
    public const TYPE_DELETE = 'delete';
    public const TYPE_DELETE_CHAT = 'delete_chat';
    public const TYPE_EDIT = 'edit';
    public const TYPE_RESTORE = 'restore';
    public const TYPE_PIN = 'pin';
    public const TYPE_UNPIN = 'unpin';
    public const TYPE_MUTE = 'mute';
    public const TYPE_UNMUTE = 'unmute';
    public const TYPE_BLOCK = 'block';
    public const TYPE_UNBLOCK = 'unblock';
    public const TYPE_RENAME = 'rename';
    public const TYPE_PHOTO = 'photo';
    public const TYPE_DESCRIPTION = 'description';
    public const TYPE_INVITE_LINK = 'invite_link';
    public const TYPE_INVITE_LINKS = 'invite_links';
    public const TYPE_STICKER_SET = 'sticker_set';
    public const TYPE_PREVIEWS = 'previews';
    public const TYPE_HISTORY = 'history';
    public const TYPE_HISTORY_DELETE = 'history_delete';
    public const TYPE_HISTORY_RESTORE = 'history_restore';

    public static array $save_events = [
        self::TYPE_CHAT_CREATED,
        self::TYPE_JOIN,
        self::TYPE_LEAVE,
        self::TYPE_RENAME
    ];

    protected $fillable = [
        'type',
        'chat_id',
        'user_id',
        'payload'
    ];

    protected $casts = [
        'payload' => 'array'
    ];

    public function chat() {
        return $this->belongsTo( MessengerChat::class );
    }

    public function user() {
        return $this->belongsTo( User::class );
    }

    public function prepareUser( $user ): array {
        return [
            'id'         => $user->id,
            'created_at' => $user->created_at,
            'email'      => $user->email,
            'img_url'    => $user->img_url,
            'last_name'  => $user->last_name,
            'name'       => $user->name,
            'updated_at' => $user->updated_at,
        ];
    }

    public function getPayloadAttribute( $value ) {
        $payload = json_decode( $value );

        if ( isset( $payload->user_id ) ) {
            $payload->user = $this->prepareUser( User::find( $payload->user_id ) );
            unset( $payload->user_id );
        }

        return $payload;
    }

    public function setPayloadAttribute( $value ) {
        $this->attributes['payload'] = json_encode( $value );
    }

}
