<?php

namespace Eddir\Messenger\Models;

use Illuminate\Foundation\Auth\User;
use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**  @mixin Eloquent */

class MessengerChat extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'title',
        'description',
        'image',
        'private'
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(MessengerMessage::class, 'chat_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'messenger_chat_users', 'chat_id', 'user_id');
    }

    public function hasMember(User $user): bool
    {
        return $this->users()->where('user_id', $user->id)->exists();
    }

    public function getLastMessage(): Model|HasMany|null
    {
        return $this->messages()->where('deleted', false)->latest()->first();
    }

    /**
     * @param User $user
     *
     * @return int
     */
    public function getUnreadMessagesCount(User $user): int
    {
        return $this->messages()->whereDoesntHave('readers', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->count();
    }

    public function getPinnedMessages(): Collection {
        return $this->messages()->where('pinned', true)->get();
    }

}
