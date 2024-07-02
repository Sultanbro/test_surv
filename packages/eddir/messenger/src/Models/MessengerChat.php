<?php

namespace Eddir\Messenger\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Foundation\Auth\User;
use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**  @mixin Eloquent */
class MessengerChat extends Model
{
    use HasFactory;

    protected $hidden = ['members'];

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

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'messenger_chat_users', 'chat_id', 'user_id')
            ->whereNull('users.deleted_at');
    }

    public function hasMember(User $user): bool
    {
        return $this->members()->where('user_id', $user->id)->exists();
    }

    public function getLastMessage(): Model|HasMany|null
    {
        return $this->messages()
            ->where('deleted', false)
            ->latest()
            ->first();
    }

    /**
     * @return BelongsToMany
     */
    public function mute(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'messenger_chat_mute', 'chat_id', 'user_id')
            ->whereNull('users.deleted_at');
    }

    /**
     * @param \App\User|Authenticatable|null $user
     *
     * @return int
     */
    public function getUnreadMessagesCount(\App\User|Authenticatable $user = null): int
    {
        return $this->messages()
            ->where('sender_id', '!=', $user->id)
            ->where('deleted', false)
            ->where('created_at', '>=', $user->created_at)
            ->whereDoesntHave('readers', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->count();
    }

    /**
     * @param \App\User|Authenticatable|null $user
     *
     * @return bool
     */
    public function checkIsMentioned(\App\User|Authenticatable $user = null): bool
    {
        $messages = $this->messages()
            ->where('sender_id', '!=', $user->id)
            ->where('deleted', false)
            ->where('created_at', '>=', $user->created_at)
            ->whereDoesntHave('readers', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->pluck('body')->toArray();
        foreach ($messages as $message) {
            if (preg_match_all('/@[^#]+#(\d+);/', $message, $matches)) {
                if (in_array($user->id, $matches[1])) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param Authenticatable|null $user
     *
     * @return array
     */
    public function getUnreadMessagesIds(Authenticatable|null $user): array
    {
        return $this->messages()
            ->where('sender_id', '!=', $user->id)
            ->where('deleted', false)
            ->whereDoesntHave('readers', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->pluck('id')
            ->toArray();
    }

    public function getPinnedMessages(): Collection
    {
        return $this->messages()->where('pinned', true)->get();
    }

    public function isPinned(User $user): bool
    {
        // check pivot table
        return DB::table('messenger_chat_users')->where('chat_id', $this->id)->where('user_id', $user->id)->where('pinned', true)->exists();
    }

    public function getUsersAttribute(): Collection
    {
        return $this->members()->withPivot('is_admin')->select('id', 'name', 'last_name', 'img_url', 'position_id', 'last_seen')->get();
    }

    /**
     * @return Builder
     */
    public function getRecipientsAttribute(): Builder
    {
        return $this->members()->withPivot('is_admin', 'is_yourself_chat')->select('id', 'name', 'last_name', 'img_url', 'position_id', 'last_seen');
    }

    public function getImageAttribute($value): ?string
    {
        if ($value) {
            // if it is url
            if (filter_var($value, FILTER_VALIDATE_URL)) {
                return $value;
            } else {
                return Storage::url($value);
            }
        }

        return null;
    }

}
