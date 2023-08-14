<?php

/**
 * MessengerFacade
 *
 * @noinspection PhpUndefinedFieldInspection
 */

namespace Eddir\Messenger;

use Eddir\Messenger\Models\MessengerUserOnline;
use Illuminate\Foundation\Auth\User;
use Eddir\Messenger\Models\MessengerChat;
use Eddir\Messenger\Models\MessengerEvent;
use Eddir\Messenger\Models\MessengerMessage;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Pusher\ApiErrorException;
use Pusher\Pusher;
use Pusher\PusherException;

class Messenger {
    public Pusher $pusher;

    /**
     * Get max file's upload size in MB.
     *
     * @return float|int
     */
    public function getMaxUploadSize(): float|int {
        return config( 'messenger.attachments.max_upload_size' ) * 1048576;
    }

    /**
     * Constructor.
     *
     * @throws PusherException
     */
    public function __construct() {
        $this->pusher = new Pusher(
            config( 'messenger.pusher.key' ),
            config( 'messenger.pusher.secret' ),
            config( 'messenger.pusher.app_id' ),
            config( 'messenger.pusher.options' ),
        );
    }

    // API v2

    /**
     * Get user's chats with last message and unread messages count for each chat.
     *
     * @param User $user
     *
     * @return Collection
     */
    public function fetchChatsWithLastMessages( User $user ): Collection {
        $chats = new Collection();

        MessengerChat::query()
            ->whereHas( 'members', function ( Builder $query ) use ( $user ) {
                $query->whereNull('deleted_at')->where( 'user_id', $user->id );
            } )
            ->get()
            ->each(function ( MessengerChat $chat ) use ( $user, $chats ) {
                $chat = $this->getChatAttributesForUser( $chat, $user );
                if ($chat) {
                    $chats->add($chat);
                }
            });

        return $chats;
    }

    /**
     * Get chat attributes.
     *
     * @param MessengerChat $chat
     * @param User $user
     *
     * @return ?MessengerChat
     */
    public function getChatAttributesForUser( MessengerChat $chat, User $user ): ?MessengerChat {
        $chat->unread_messages_count = $chat->getUnreadMessagesCount( $user );
        // last message with sender
        $chat->last_message = $chat->getLastMessage();
        if ( $chat->last_message ) {
            $chat->last_message->sender = $chat->last_message->sender;
            if ( $chat->last_message->event ) {
                $chat->last_message->body = "";
            }
        }
        $chat->pinned = $chat->isPinned( $user );
        $chat->is_mute = $chat->mute()?->where('user_id', $user->id)->exists();

        if ( $chat->private ) {
            // get second user in private chat
            $users =  $chat->recipients;

            $second_user = $users->count() >= 2 ? $users->firstWhere('id', '!=', $user->id) :
                ($users->wherePivot('is_yourself_chat', true)->exists() ? $users->firstWhere('id', '=', $user->id) : []);

            if ( $second_user && !$second_user->deleted_at) {
                $chat->second_user = $second_user;
                $chat->title       = $second_user->name . " " . $second_user->last_name;
                $chat->image       = $second_user->img_url;
                $chat->isOnline    = MessengerUserOnline::query()->where( 'user_id', $second_user->id )->exists();
                $chat->last_seen   = $second_user->last_seen;
            } else {
                return null;
            }
        }
        $chat->users = $chat->users->map( function ( $user ) {
            return collect( $user->toArray() )
                ->only( [ 'id', 'name', 'last_name' ] )
                ->all();
        } );


        return $chat;
    }

    /**
     * Search chat by name.
     *
     * @param string $name
     * @param int $userId
     * @param int $limit
     *
     * @return Collection
     */
    public function searchChats( string $name, int $userId, int $limit = 100 ): Collection {
        return MessengerChat::query()
                            ->whereHas( 'members', function ( Builder $query ) use ( $userId ) {
                                $query->where( 'user_id', $userId );
                            } )
                            ->where( 'title', 'like', "%$name%" )
                            ->where( 'private', false )
                            ->limit( $limit )
                            ->get();
    }

    /**
     * Search users by name.
     *
     * @param string $name
     * @param int $limit
     *
     * @return Collection
     */
    public function searchUsers( string $name, int $limit = 100 ): Collection {
        return User::query()
                   ->whereNull( 'deleted_at' )
                   ->where(fn($query) => $query->where('name', 'like', "%$name%")->orWhere( 'last_name', 'like', "%$name%" ))
                   ->limit( $limit )
                   ->get()->map(function($item) {
                    $item->image = 'https://'.\request()->getHost().'/users_img/' . $item->img_url;
                    return $item;
               });
    }

    /**
     * Get chat information by id.
     *
     * @param int $chatId
     *
     * @return Builder|Model
     */
    public function getChat( int $chatId ): Builder|Model {
        return MessengerChat::query()
                            ->where( 'id', $chatId )
                            ->first();
    }

    /**
     * @param MessengerChat $chat
     * @return JsonResponse|bool
     */
    public function muteChatForUser(
        MessengerChat $chat
    ): JsonResponse|bool
    {
        $user = Auth::user();

        if ($chat->mute->contains($user->id))
        {
            return response()->json(['message' => 'You are already muted this chat'],400);
        }

        $chat->mute()->attach($user->id);

        return true;
    }

    /**
     * @param MessengerChat $chat
     * @return JsonResponse|bool
     */
    public function unmuteChatForUser(
        MessengerChat $chat
    ): JsonResponse|bool
    {
        $user = Auth::user();

        if (!$chat->mute->contains($user->id))
        {
            return response()->json(['message' => 'Before unmute chat you should mute!'],400);
        }

        $chat->mute()->detach($user->id);

        return true;
    }

    /**
     * Get chat by message id.
     *
     * @param int $messageId
     *
     * @return Builder|Model
     */
    public function getChatByMessageId( int $messageId ): Builder|Model {
        return MessengerMessage::query()
                               ->where( 'id', $messageId )
                               ->first()
            ->chat;
    }

    /**
     * Get private chat by user id or create new one if it doesn't exist.
     *
     * @param int $userId
     * @param int $otherUserId
     * @param bool $create
     *
     * @return Builder|Model|null
     */
    public function getPrivateChat( int $userId, int $otherUserId, bool $create = true ): Builder|Model|null {

        // get chat when has user userId
        $chat = MessengerChat::query()
            ->where( 'private', true )
            ->when($userId != $otherUserId, function ($query) use ($userId, $otherUserId) {
                $query->withWhereHas( 'members', function (  $query ) use ( $userId ) {
                    $query->where( 'user_id', $userId );
                } )
                    ->withWhereHas( 'members', function (  $query ) use ( $otherUserId ) {
                        $query->where( 'user_id', $otherUserId );
                    } );
            })
            ->when($userId == $otherUserId, function ($query) use ($userId) {
                $query->withWhereHas( 'members', function (  $query ) use ( $userId ) {
                    $query->where( 'user_id', $userId )->where('is_yourself_chat', true);
                } );
            })
            ->first();

        if ( $chat ) {
            return $chat;
        } else if ( ! $create ) {
            return null;
        }
        // create new chat if it doesn't exist
        $chat = MessengerChat::query()
                             ->create( [
                                 'owner_id' => $userId,
                                 'title'    => '',
                                 'private'  => true,
                             ] );

        // attach each user

        $chat->members()->attach( $userId, [ 'is_admin' => true , 'is_yourself_chat' => $userId == $otherUserId] );

        if ($userId != $otherUserId)
        {
            $chat->members()->attach( $otherUserId, [ 'is_admin' => true ] );
        }

        $chat->save();


        return $chat;
    }

    /**
     * Fetch chat messages with pagination.
     *
     * @param int $chatId
     * @param int $count
     *
     * @param int $start_message_id
     * @param bool $including
     *
     * @return Collection
     */
    public function fetchMessages( int $chatId, int $count, int $year, int $month, int $start_message_id = 0, bool $including = false ): Collection {

        $user = Auth::user();

        $messages = MessengerMessage::query()
                                    ->with( 'sender' )
                                    ->with( 'event' )
                                    ->with( 'files' )
                                    ->with( 'readers' )
                                    ->with( 'parent' )
                                    ->whereDoesntHave('deletedMessage', fn($query) => $query->where('user_id', $user->id))
                                    ->with('deletedMessage')
                                    ->where( 'chat_id', $chatId )
                                    ->where( 'deleted', false );

        // if start_message_id is 0, get last messages
        if ( $start_message_id != 0 ) {
            if ( $count > 0 ) {
                $messages = $messages->orderBy( 'id', 'desc' );
                if ( $including ) {
                    $messages = $messages->where( 'id', '<=', $start_message_id );
                } else {
                    $messages = $messages->where( 'id', '<', $start_message_id );
                }
            } else {
                $messages = $messages->orderBy( 'id', 'asc' );
                if ( $including ) {
                    $messages = $messages->where( 'id', '>=', $start_message_id );
                } else {
                    $messages = $messages->where( 'id', '>', $start_message_id );
                }
            }
        } else {
            $messages = $messages->orderBy( 'id', 'desc' );
        }

        $messages = $messages->when($year && $month, fn($query) => $query->whereYear('created_at', $year)->whereMonth('created_at', $month));

        return $messages->limit( abs( $count ) )->get();
    }

    /**
     * @param int $chatId
     * @param int $count
     * @return LengthAwarePaginator
     */
    public function fetchFiles(int $chatId, int $count = 20): LengthAwarePaginator
    {
        $messages = MessengerMessage::query()->withWhereHas('files')->where('chat_id', $chatId);

        return $messages->orderBy('created_at', 'desc')->paginate(abs($count));
    }

    /**
     * Set messages as read.
     *
     * @param Collection $messages
     * @param User $user
     *
     * @return Collection
     * @throws GuzzleException
     * @throws PusherException
     */
    public function setMessagesAsRead( Collection $messages, User $user ): Collection {
        $messages->each( function ( MessengerMessage $message ) use ( $user ) {
            // add user to read users
            $message->readers()->syncWithoutDetaching( $user );
        } );

        // check if the highest message is really the last message
        $last_message = $messages->last();
        if ( $last_message->id == $last_message->chat->getLastMessage()->id ) {
            // send pusher event
            $this->createEvent( $last_message->chat, $user, MessengerEvent::TYPE_READ, [
                'message_id' => $last_message->id,
            ] );
        }

        return $messages;
    }

    /**
     * Set emoji reaction to message.
     *
     * @param int $messageId
     * @param User $user
     * @param int $emoji
     *
     * @return MessengerMessage
     * @throws ApiErrorException
     * @throws GuzzleException
     * @throws PusherException
     */
    public function setReaction( int $messageId, User $user, int $emoji ): MessengerMessage {
        $message = MessengerMessage::query()->find( $messageId );

        // if user already reacted to message with some reaction, remove reaction
        $reaction = null;
        $reader   = $message->readers()->wherePivot( 'user_id', $user->id )->first();
        if ( $reader ) {
            $reaction = $reader->pivot->reaction;
        }
        if ( $reaction == $emoji ) {
            // set null on pivot table
            $message->readers()->updateExistingPivot( $user, [
                'reaction' => null,
            ] );
            $emoji = null;
        } else {

            // add reader with reaction or update reaction
            $message->readers()->syncWithoutDetaching( [
                $user->id => [
                    'reaction' => $emoji,
                ],
            ] );
        }

        $this->createEvent( $message->chat, $user, MessengerEvent::TYPE_REACTION, [
            'message_id' => $message->id,
            'reaction'   => $emoji,
        ] );

        return $message;
    }

    /**
     * Set message as read.
     *
     * @param MessengerMessage $message
     * @param User $user
     *
     * @return MessengerMessage
     * @throws ApiErrorException
     * @throws GuzzleException
     * @throws PusherException
     */
    public function setMessageAsRead( MessengerMessage $message, User $user ): MessengerMessage {
        $message->readers()->syncWithoutDetaching( $user );
        // check if the highest message is really the last message
        if ( $message->id == $message->chat->getLastMessage()->id ) {
            // send pusher event
            $this->createEvent( $message->chat, $user, MessengerEvent::TYPE_READ );
        }

        return $message;
    }

    /**
     * Check if user is in chat.
     *
     * @param int $chatId
     * @param int $userId
     *
     * @return bool
     */
    public function isMember( int $chatId, int $userId ): bool {
        return MessengerChat::query()
                            ->where( 'id', $chatId )
                            ->whereHas( 'members', function ( Builder $query ) use ( $userId ) {
                                $query->where( 'user_id', $userId );
                            } )
                            ->exists();
    }

    /**
     * Check if user is already deleted message.
     *
     * @param MessengerMessage $message
     * @param int $userId
     * @return bool
     */
    public function isAlreadyDeleted(
        MessengerMessage $message,
        int $userId
    ): bool
    {
        return $message->deletedMessage()->get()->contains($userId);
    }

    /**
     * Check if user is chat admin.
     *
     * @param int $chatId
     * @param int $userId
     *
     * @return bool
     */
    public function isAdmin( int $chatId, int $userId ): bool {
        // check pivot is_admin in chat_user table
        return MessengerChat::query()
                            ->where( 'id', $chatId )
                            ->whereHas( 'members', function ( Builder $query ) use ( $userId ) {
                                $query->where( 'user_id', $userId )->where( 'users.is_admin', true );
                            } )
                            ->exists();
    }

    /**
     * Set user as chat admin
     *
     * @param MessengerChat $chat
     * @param User $user
     *
     * @return bool
     * @throws ApiErrorException
     * @throws GuzzleException
     * @throws PusherException
     */
    public function setAdmin( MessengerChat $chat, User $user ): bool {
        $chat->members()->updateExistingPivot( $user, [
            'is_admin' => true,
        ] );
        $this->createEvent( $chat, $user, MessengerEvent::TYPE_CHAT_ADMIN );

        return true;
    }

    /**
     * Remove user as chat admin
     *
     * @param MessengerChat $chat
     * @param User $user
     *
     * @return bool
     * @throws ApiErrorException
     * @throws GuzzleException
     * @throws PusherException
     */
    public function removeAdmin( MessengerChat $chat, User $user ): bool {
        $chat->members()->updateExistingPivot( $user, [
            'is_admin' => false,
        ] );
        $this->createEvent( $chat, $user, MessengerEvent::TYPE_CHAT_ADMIN );

        return true;
    }

    /**
     * Get all messages by chat id.
     *
     * @param int $chat_id
     *
     * @return Collection
     */
    public function getMessages( int $chat_id ): Collection {
        return MessengerMessage::where( 'chat_id', $chat_id )->where( 'deleted', false )->get();
    }

    /**
     * Get all chats by user id.
     *
     * @param int $user_id
     *
     * @return Collection
     */
    public function getChats( int $user_id ): Collection {
        return MessengerChat::whereHas( 'members', function ( Builder $query ) use ( $user_id ) {
            $query->where( 'user_id', $user_id );
        } )->get();
    }

    /**
     * Send message to chat.
     *
     * @param int $chatId
     * @param int $userId
     * @param string $body
     * @param array $files
     * @param null $parentId
     *
     * @return MessengerMessage
     * @throws ApiErrorException
     * @throws GuzzleException
     * @throws PusherException
     */
    public function sendMessage( int $chatId, int $userId, string $body, $files = [], $parentId = null ): MessengerMessage {
        $message            = new MessengerMessage();
        $message->chat_id   = $chatId;
        $message->sender_id = $userId;
        $message->parent_id = $parentId;
        $message->body      = $body;
        $message->save();

        // for every user in chat send message to pusher
        $chat = MessengerChat::find( $chatId );

        // check if user is member of chat
        if ( ! $chat->hasMember( User::find( $userId ) ) ) {
            throw new Exception( 'User is not member of chat ' . $chatId );
        }

        if ( count( $files ) > 0 ) {
            foreach ( $files as $file ) {
                $file->message_id = $message->id;
                $file->save();
            }
            /** @noinspection PhpExpressionResultUnusedInspection */
            $message->files;
        }
        $message->parent;
        $message->sender;

        $users = MessengerUserOnline::getOnlineMembers( $chatId );
        $users->each( function ( $user ) use ( $message ) {
            $this->push( 'messages.' . request()->getHost() . '.' . $user->user_id, 'newMessage', [
                'message' => $message->toArray(),
            ] );
        } );

        return $message;
    }

    /**
     * Check if user is sender of message.
     *
     * @param int $messageId
     * @param int $userId
     *
     * @return bool
     */
    public function isSender( int $messageId, int $userId ): bool {
        return MessengerMessage::query()
                               ->where( 'id', $messageId )
                               ->where( 'sender_id', $userId )
                               ->exists();
    }

    /**
     * Edit message.
     *
     * @param int $messageId
     * @param string $body
     *
     * @return MessengerMessage
     * @throws ApiErrorException
     * @throws GuzzleException
     * @throws PusherException
     */
    public function editMessage( int $messageId, string $body ): MessengerMessage {
        /** @var MessengerMessage $message */
        $message       = MessengerMessage::find( $messageId );
        $message->body = $body;
        $message->save();
        $message->files;

        $this->createEvent( $message->chat, $message->sender, MessengerEvent::TYPE_EDIT, [
            'message' => $message->toArray(),
        ] );

        return $message;
    }

    /**
     * Set message as deleted.
     *
     * @param int $messageId
     * @param User $promote
     *
     * @return bool|JsonResponse
     * @throws ApiErrorException
     * @throws GuzzleException
     * @throws PusherException
     */
    public function deleteMessage( int $messageId, User $promote ): bool|JsonResponse {

        /** @var MessengerMessage $message */
        $message = MessengerMessage::find( $messageId );

        if (self::isAlreadyDeleted($message, $promote->id))
        {
            return response()->json([ 'message' => 'You are already delete this message.' ], 400);
        }

        $message->deletedMessage()->attach($promote->id);

        $this->createEvent( $message->chat, $promote, MessengerEvent::TYPE_DELETE, [
            'message_id' => $messageId,
        ] );

        return true;
    }

    /**
     * Pin message.
     *
     * @param int $messageId
     * @param User $promote
     *
     * @return MessengerMessage
     * @throws ApiErrorException
     * @throws GuzzleException
     * @throws PusherException
     */
    public function pinMessage( int $messageId, User $promote ): MessengerMessage {
        /** @var MessengerMessage $message */
        $message         = MessengerMessage::find( $messageId );
        $message->pinned = true;
        $message->save();

        $this->createEvent( $message->chat, $promote, MessengerEvent::TYPE_PIN, [
            'message' => $message
        ] );

        return $message;
    }

    /**
     * Unpin message.
     *
     * @param int $messageId
     * @param User $promote
     *
     * @return MessengerMessage
     * @throws ApiErrorException
     * @throws GuzzleException
     * @throws PusherException
     */
    public function unpinMessage( int $messageId, User $promote ): MessengerMessage {
        /** @var MessengerMessage $message */
        $message         = MessengerMessage::find( $messageId );
        $message->pinned = false;
        $message->save();

        $this->createEvent( $message->chat, $promote, MessengerEvent::TYPE_UNPIN, [
            'message_id' => $message->id,
        ] );

        return $message;
    }

    /**
     * Pin chat
     *
     * @param int $chatId
     * @param User $promote
     *
     * @return bool
     */
    public function pinChat( int $chatId, User $promote ): bool {
        // update chat_users table
        DB::table( 'messenger_chat_users' )
          ->where( 'chat_id', $chatId )
          ->where( 'user_id', $promote->id )
          ->update( [
              'pinned' => true,
          ] );

        return true;
    }

    /**
     * Unpin chat
     *
     * @param int $chatId
     * @param User $promote
     *
     * @return bool
     */
    public function unpinChat( int $chatId, User $promote ): bool {
        // update chat_users table
        DB::table( 'messenger_chat_users' )
          ->where( 'chat_id', $chatId )
          ->where( 'user_id', $promote->id )
          ->update( [
              'pinned' => false,
          ] );

        return true;
    }

    /**
     * Create new chat.
     *
     * @param User $user
     * @param string $title
     * @param string $description
     * @param array $members
     *
     * @return MessengerChat
     * @throws ApiErrorException
     * @throws GuzzleException
     * @throws PusherException
     */
    public function createChat( User $user, string $title, string $description, array $members ): MessengerChat {
        $chat              = new MessengerChat();
        $chat->title       = $title;
        $chat->description = $description;
        $chat->owner()->associate( $user );
        $chat->save();

        $chat->members()->syncWithoutDetaching( collect($members)->pluck('id') );
        $chat->members()->syncWithoutDetaching( $user->id, [ 'is_admin' => true ] );
        DB::table('messenger_chat_users')
            ->where('chat_id', $chat->id)
            ->where('user_id', $user->id)
            ->update([ 'is_admin' => true ]);


        $this->createEvent( $chat, $user, MessengerEvent::TYPE_CHAT_CREATED, [
            'chat' => $chat->toArray(),
        ] );

        return $chat;
    }

    /**
     * Add user to chat.
     *
     * @param int $chatId
     * @param int $userId
     * @param User $promote
     *
     * @return MessengerChat
     * @throws ApiErrorException
     * @throws GuzzleException
     * @throws PusherException
     */
    public function addUserToChat( int $chatId, int $userId, User $promote ): MessengerChat {
        $chat = MessengerChat::find( $chatId );
        $chat->members()->syncWithoutDetaching( $userId );

        $this->createEvent( $chat, $promote, MessengerEvent::TYPE_JOIN, [
            'user' => User::find( $userId )->toArray(),
        ] );

        return $chat;
    }

    /**
     * Remove user from chat.
     *
     * @param int $chatId
     * @param int $userId
     * @param User $promote
     *
     * @return MessengerChat
     * @throws ApiErrorException
     * @throws GuzzleException
     * @throws PusherException
     */
    public function removeUserFromChat( int $chatId, int $userId, User $promote ): MessengerChat {
        $chat = MessengerChat::find( $chatId );
        $chat->members()->detach( $userId );

        $this->createEvent( $chat, $promote, MessengerEvent::TYPE_LEAVE, [
            'user_id' => $userId,
        ] );

        return $chat;
    }

    /**
     * Delete chat.
     *
     * @param int $chatId
     * @param User $user
     *
     * @return MessengerChat
     * @throws ApiErrorException
     * @throws GuzzleException
     * @throws PusherException
     */
    public function deleteChat( int $chatId, User $user ): MessengerChat {
        $chat = MessengerChat::find( $chatId );
        $chat->members()->detach();

        $this->createEvent( $chat, $user, MessengerEvent::TYPE_DELETE_CHAT );
        return $chat;
    }

    /**
     * Update chat.
     *
     * @param int $chatId
     * @param string $title
     * @param string $description
     * @param User $user
     *
     * @return MessengerChat
     * @throws ApiErrorException
     * @throws GuzzleException
     * @throws PusherException
     */
    public function updateChat( int $chatId, string $title, string $description, User $user ): MessengerChat {

        /* @var MessengerChat $chat */
        $chat              = MessengerChat::find( $chatId );
        $oldChat           = $chat->toArray();
        $chat->title       = $title;
        $chat->description = $description;
        $chat->save();

        $this->createEvent( $chat, $user, MessengerEvent::TYPE_RENAME, [
            'chat'            => MessengerChat::find( $chat->id )->toArray(),
            'old_title'       => $oldChat['title'],
            'old_description' => $oldChat['description'],
        ] );

        return $chat;
    }

    /**
     * Upload chat avatar.
     *
     * @param int $chatId
     * @param string $path
     *
     * @return MessengerChat
     * @throws ApiErrorException
     * @throws GuzzleException
     * @throws PusherException
     */
    public function uploadChatAvatar( int $chatId, string $path ): MessengerChat {
        $chat        = MessengerChat::find( $chatId );
        $chat->image = $path;
        $chat->save();

        $this->createEvent( $chat, $chat->owner, MessengerEvent::TYPE_CHAT_PHOTO );

        return $chat;
    }

    /**
     * Create event
     *
     * @param MessengerChat $chat
     * @param User $user
     * @param string $type
     * @param array|null $payload
     *
     * @return MessengerEvent
     * @throws ApiErrorException
     * @throws GuzzleException
     * @throws PusherException
     */
    public function createEvent( MessengerChat $chat, User $user, string $type, array $payload = null ): MessengerEvent {
        $message = new MessengerMessage();
        $message->chat()->associate( $chat );
        $message->sender()->associate( $user );
        $message->body = 'Event';

        $event       = new MessengerEvent();
        $event->type = $type;
        $event->message()->associate( $message );
        $event->setPayloadAttribute( $payload );


        if ( in_array( $type, MessengerEvent::$save_events ) ) {
            $message->save();
            $event->message()->associate( $message );
            $event->save();
            /** @noinspection PhpExpressionResultUnusedInspection */
            $message->event;
            $messageData = $message->toArray();
        } else {
            $messageData          = $message->toArray();
            $messageData['event'] = $event->toArray();
            $messageData['chat']  = $chat->toArray();
        }
        $messageData['chat']['users'] = $chat->users->toArray();


        $users = MessengerUserOnline::getOnlineMembers( $chat->id );
        $users->each( function ( $user ) use ( $messageData ) {
            $this->push( 'messages.' . request()->getHost() . '.' . $user->user_id, 'newMessage', [
                'message' => $messageData,
            ] );
        } );

        return $event;
    }

    /**
     * Trigger an event using Pusher
     *
     * @param string $channel
     * @param string $event
     * @param array $data
     *
     * @return object
     * @throws ApiErrorException
     * @throws GuzzleException
     * @throws PusherException
     */
    public function push( string $channel, string $event, array $data ): object {
        return $this->pusher->trigger( "private-" . $channel, $event, $data );
    }

    /**
     * @param string $channel_name
     * @param string $socket_id
     * @param string|null $data
     * @param int|null $user_id
     *
     * @return string
     * @throws GuzzleException
     */
    public function pusherAuth( string $channel_name, string $socket_id, string $data = null, int $user_id = null, string $domain = null ): string {
        try {
            $response = $this->pusher->socketAuth( $channel_name, $socket_id, $data );
            if ( $user_id ) {
                // create MessengerUserOnline if not exists or update last_seen
                // this is used to show online status
                $user = MessengerUserOnline::where( 'socket_id', $socket_id )->first();
                if ( $user ) {
                    $user->user_id = $user_id;
                    $user->touch();
                } else {
                    MessengerUserOnline::create( [
                        'user_id'   => $user_id,
                        'socket_id' => $socket_id,
                        'domain' => $domain,
                    ] );
                }

                $users = DB::select( "
SELECT DISTINCT t2.user_id
FROM `messenger_chat_users` t1
         INNER JOIN `messenger_chat_users` t2
                    ON t1.chat_id = t2.chat_id
                        AND t1.user_id != t2.user_id
         INNER JOIN `messenger_users_online` o
                    ON o.user_id = t2.user_id
WHERE t1.user_id = {$user_id}" );

                // for each user trigger event
                foreach ( $users as $user ) {
                    $this->push( 'messages.' . request()->getHost() . '.' . $user->user_id, 'newMessage', [
                        'message' => [
                            'event'  => [
                                'type' => MessengerEvent::TYPE_ONLINE,
                            ],
                            'sender' => [
                                'id' => $user_id
                            ]
                        ]
                    ] );
                }
            }

            return $response;
        } catch ( PusherException $e ) {
            return $e->getMessage();
        }
    }

}
