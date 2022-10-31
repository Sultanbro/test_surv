<?php

namespace Eddir\Messenger\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessengerFile extends Model {
    use HasFactory;

    protected $fillable = [
        'name',
        'file_path'
    ];

    public function message(): BelongsTo {
        return $this->belongsTo( MessengerMessage::class, 'message_id' );
    }
}
