<?php

namespace Eddir\Messenger\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
    public const TYPE_CHAT_PHOTO = 'chat_photo';
    public const TYPE_DESCRIPTION = 'description';
    public const TYPE_INVITE_LINK = 'invite_link';
    public const TYPE_INVITE_LINKS = 'invite_links';
    public const TYPE_ONLINE = 'online';
    public const TYPE_OFFLINE = 'offline';
    public const TYPE_REACTION = 'reaction';
    public const TYPE_CHAT_ADMIN = 'chat_admin';

    public static array $save_events = [
        self::TYPE_CHAT_CREATED,
        self::TYPE_JOIN,
        self::TYPE_LEAVE,
        self::TYPE_RENAME,
        self::TYPE_PIN,
        self::TYPE_UNPIN,
    ];

    protected $fillable = [
        'type',
        'message_id',
        'payload'
    ];

    protected $casts = [
        'payload' => 'array'
    ];

    public function message(): BelongsTo {
        return $this->belongsTo(MessengerMessage::class, 'message_id');
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
