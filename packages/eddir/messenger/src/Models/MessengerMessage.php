<?php


namespace Eddir\Messenger\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User;
use Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**  @mixin Eloquent */

class MessengerMessage extends Model {
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'chat_id',
        'body',
        'read',
        'deleted',
    ];

    public function sender(): BelongsTo {
        return $this->belongsTo( User::class, 'sender_id' );
    }

    public function chat(): BelongsTo {
        return $this->belongsTo( MessengerChat::class, 'chat_id' );
    }

    // users who has been seen this message
    public function readers(): BelongsToMany {
        return $this->belongsToMany( User::class, 'messenger_message_users_read', 'message_id', 'user_id' );
    }

    public function files(): HasMany {
        return $this->hasMany( MessengerFile::class, 'message_id' );
    }

    public function event(): BelongsTo {
        return $this->belongsTo( MessengerEvent::class, 'id', 'message_id' );
    }

}
