<?php

/**
 * MessengerFacade
 *
 * @noinspection PhpUndefinedFieldInspection
 */

namespace Eddir\Messenger;

use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\User;
use Eddir\Messenger\Models\MessengerChat;
use Eddir\Messenger\Models\MessengerFile;
use Eddir\Messenger\Models\MessengerEvent;
use Eddir\Messenger\Models\MessengerMessage;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
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
     * @param Authenticatable $user
     *
     * @return Collection
     */
    public function fetchChatsLastMessages( User $user ): Collection {
        $chats = MessengerChat::query()
                              ->whereHas( 'users', function ( Builder $query ) use ( $user ) {
                                  $query->where( 'user_id', $user->id );
                              } )
                              ->get();
        $chats->each( function ( MessengerChat $chat ) use ( $user ) {
            $this->getChatAttributesForUser( $chat, $user );
        } );

        return $chats;
    }

    /**
     * Get chat attributes.
     *
     * @param MessengerChat $chat
     * @param User $user
     *
     * @return MessengerChat
     */
    public function getChatAttributesForUser( MessengerChat $chat, User $user ): MessengerChat {
        $chat->unread_messages_count = $chat->getUnreadMessagesCount( $user );
        // last message with sender
        $chat->last_message = $chat->getLastMessage();
        if ( $chat->last_message ) {
            $chat->last_message->sender = $chat->last_message->sender;
        }

        if ( $chat->private ) {
            // get second user in private chat
            $second_user = $chat->users->firstWhere( 'id', '!=', $user->id );
            

            $chat->title = 'Безымянный';
            $chat->image = '';

            if($second_user) {
                $chat->title = $second_user->name . " " . $second_user->last_name;
                $chat->image = $second_user->img_url;
            }

        }
        if ( empty( $chat->image ) ) {
            $chat->image = config( 'messenger.user_avatar.default' ) ?? asset( 'vendor/messenger/images/users.png' );
        }


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
                            ->whereHas( 'users', function ( Builder $query ) use ( $userId ) {
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
                   ->where( 'name', 'like', "%$name%" )
                   ->orWhere( 'last_name', 'like', "%$name%" )
                   ->limit( $limit )
                   ->get();
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
     * Get private chat by user id or create new one if it doesn't exist.
     *
     * @param int $userId
     * @param int $otherUserId
     *
     * @return Builder|Model
     */
    public function getPrivateChat( int $userId, int $otherUserId ): Builder|Model {
        // get chat when has user userId
        $chat = MessengerChat::query()
                             ->whereHas( 'users', function ( Builder $query ) use ( $userId ) {
                                 $query->where( 'user_id', $userId );
                             } )
                             ->whereHas( 'users', function ( Builder $query ) use ( $otherUserId ) {
                                 $query->where( 'user_id', $otherUserId );
                             } )
                             ->first();
        if ( $chat ) {
            return $chat;
        }
        // create new chat if it doesn't exist
        $chat = MessengerChat::query()
                             ->create( [
                                 'owner_id' => $userId,
                                 'title'    => '',
                                 'private'  => true,
                             ] );

        // attach each user
        $chat->users()->attach( $userId );
        $chat->users()->attach( $otherUserId );
        $chat->save();

        return $chat;
    }


    /**
     * Fetch chat messages with pagination.
     *
     * @param int $chatId
     * @param int $page
     * @param int $perPage
     *
     * @return Collection
     */
    public function fetchMessages( int $chatId, int $page = 0, int $perPage = 10 ): Collection {
        return MessengerMessage::query()
                               ->with( 'files' )
                               ->where( 'chat_id', $chatId )
                               ->where( 'deleted', false )
                               ->orderBy( 'created_at', 'desc' )
                               ->skip( $page * $perPage )
                               ->take( $perPage )
                               ->get();
    }

    /**
     * Set messages as read.
     *
     * @param Collection $messages
     * @param User $user
     *
     * @return Collection
     */
    public function setMessagesAsRead( Collection $messages, User $user ): Collection {
        $messages->each( function ( MessengerMessage $message ) use ( $user ) {
            // add user to read users
            $message->readers()->syncWithoutDetaching( $user );
            # get all users in chat
            $users = $message->chat->users;
            # send notification to all users in chat
            $users->each( function ( User $user ) use ( $message ) {
                $this->pusher->trigger( 'messages.' . $user->id, 'readMessage', [
                    'message' => $message->toArray(),
                    'user'    => $user->toArray(),
                ] );
            } );
        } );

        return $messages;
    }

    /**
     * Set message as read.
     *
     * @param MessengerMessage $message
     * @param User $user
     *
     * @return MessengerMessage
     */
    public function setMessageAsRead( MessengerMessage $message, User $user ): MessengerMessage {
        $message->readers()->syncWithoutDetaching( $user );
        # get all users in chat
        $users = $message->chat->users;
        # send notification to all users in chat
        $users->each( function ( User $user ) use ( $message ) {
            $this->pusher->trigger( 'messages.' . $user->id, 'readMessage', [
                'message' => $message->toArray(),
                'user'    => $user->toArray(),
            ] );
        } );

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
                            ->whereHas( 'users', function ( Builder $query ) use ( $userId ) {
                                $query->where( 'user_id', $userId );
                            } )
                            ->exists();
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
     *
     * @return MessengerMessage
     * @throws ApiErrorException
     * @throws PusherException
     * @throws Exception
     */
    public function sendMessage( int $chatId, int $userId, string $body, $file = null ): MessengerMessage {
        $message            = new MessengerMessage();
        $message->chat_id   = $chatId;
        $message->sender_id = $userId;
        $message->body      = $body;
        $message->save();

        // for every user in chat send message to pusher
        $chat = MessengerChat::find( $chatId );

        // check if user is member of chat
        if ( ! $chat->hasMember( User::find( $userId ) ) ) {
            throw new Exception( 'User is not member of chat ' . $chatId );
        }

        if ( $file ) {
            $file->message_id = $message->id;
            $file->save();
            /** @noinspection PhpExpressionResultUnusedInspection */
            $message->files;
        }

        // todo: for every online user
        $chat->users->each( function ( User $user ) use ( $message ) {
            $this->pusher->trigger( 'messages.' . $user->id, 'newMessage', [
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
     */
    public function editMessage( int $messageId, string $body ): MessengerMessage {
        /** @var MessengerMessage $message */
        $message       = MessengerMessage::find( $messageId );
        $message->body = $body;
        $message->save();

        $this->createEvent( $message->chat, $message->sender, MessengerEvent::TYPE_EDIT, [
            'message' => $message->toArray(),
        ] );

        return $message;
    }

    /**
     * Set message as deleted.
     *
     * @param int $messageId
     *
     * @return bool
     */
    public function deleteMessage( int $messageId, User $promote ): bool {

        /** @var MessengerMessage $message */
        $message = MessengerMessage::find( $messageId );

        $this->createEvent( $message->chat, $promote, MessengerEvent::TYPE_DELETE, [
            'message_id' => $messageId,
        ] );

        $message->deleted = true;
        $message->save();

        return true;
    }

    /**
     * Pin message.
     *
     * @param int $messageId
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
            'message_id' => $messageId
        ] );

        return $message;
    }

    /**
     * Unpin message.
     *
     * @param int $messageId
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

        // get last pinned message in chat
        $chat = $message->chat;
        /** @var MessengerMessage $lastPinnedMessage */
        $lastPinnedMessage = $chat->messages()
                                  ->where( 'pinned', true )
                                  ->orderBy( 'created_at', 'desc' )
                                  ->first();

        $messageArray = $lastPinnedMessage?->toArray();

        // trigger unpin event to pusher for every user in chat
        $chat->users->each( function ( User $user ) use ( $messageArray ) {
            $this->pusher->trigger( 'messages.' . $user->id, 'pinMessage', [
                'message' => $messageArray,
            ] );
        } );

        $this->createEvent( $chat, $promote, MessengerEvent::TYPE_UNPIN, [
            'message_id' => $message->id,
        ] );

        return $message;
    }

    /**
     * Create new chat.
     *
     * @param int $userId
     * @param string $title
     * @param string $description
     * @param array $members
     *
     * @return MessengerChat
     */
    public function createChat( User $user, string $title, string $description, array $members ): MessengerChat {
        $chat              = new MessengerChat();
        $chat->title       = $title;
        $chat->description = $description;
        $chat->owner()->associate( $user );
        $chat->save();
        $chat->users()->attach( $user->id );

        $this->createEvent( $chat, $user, MessengerEvent::TYPE_CHAT_CREATED );

        // attach every member to chat
        foreach ( $members as $member ) {
            $chat->users()->attach( $member );
        }

        return $chat;
    }

    /**
     * Add user to chat.
     *
     * @param int $chatId
     * @param int $userId
     *
     * @return MessengerChat
     */
    public function addUserToChat( int $chatId, int $userId, User $promote ): MessengerChat {
        $chat = MessengerChat::find( $chatId );
        $chat->users()->attach( $userId );

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
     *
     * @return MessengerChat
     */
    public function removeUserFromChat( int $chatId, int $userId, User $promote ): MessengerChat {
        $chat = MessengerChat::find( $chatId );
        $chat->users()->detach( $userId );

        $this->createEvent( $chat, $promote, MessengerEvent::TYPE_LEAVE, [
            'user_id' => $userId,
        ] );

        return $chat;
    }

    /**
     * Delete chat.
     *
     * @param int $chatId
     *
     * @return MessengerChat
     */
    public function deleteChat( int $chatId, User $user ): MessengerChat {
        $chat = MessengerChat::find( $chatId );

        $this->createEvent( $chat, $user, MessengerEvent::TYPE_DELETE_CHAT );

        $chat->delete();

        return $chat;
    }

    /**
     * Update chat.
     *
     * @param int $chatId
     * @param string $title
     * @param string $description
     *
     * @return MessengerChat
     */
    public function updateChat( int $chatId, string $title, string $description, User $user ): MessengerChat {

        /* @var MessengerChat $chat */
        $chat = MessengerChat::find( $chatId );
        $oldChat = $chat->toArray();
        $chat->title       = $title;
        $chat->description = $description;
        $chat->save();

        $this->createEvent( $chat, $user, MessengerEvent::TYPE_RENAME, [
            'chat'            => MessengerChat::find($chat->id)->toArray(),
            'old_title'       => $oldChat['title'],
            'old_description' => $oldChat['description'],
        ] );

        return $chat;
    }

    /**
     * Fetch events for chat.
     *
     * @param int $chatId
     *
     * @return Collection
     */
    public function fetchEvents( int $chatId ): Collection {
        return MessengerEvent::where( 'chat_id', $chatId )->with( 'user' )->get();
    }

    /**
     * Create event
     *
     * @param MessengerChat $chat
     * @param User $user
     * @param string $type
     * @param array $payload
     *
     * @return MessengerEvent
     */
    public function createEvent( MessengerChat $chat, User $user, string $type, array $payload = null ): MessengerEvent {
        $event          = new MessengerEvent();
        $event->chat_id = $chat->id;
        $event->user_id = $user->id;
        $event->type    = $type;
        $event->setPayloadAttribute( $payload );

        if ( in_array( $type, MessengerEvent::$save_events ) ) {
            $event->save();
        }

        $eventData         = $event->toArray();
        $eventData['user'] = $user->toArray();

        $chat->users->each( function ( User $user ) use ( $eventData ) {
            $this->pusher->trigger( 'messages.' . $user->id, 'newMessage', [
                'message' => $eventData,
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
        return $this->pusher->trigger( $channel, $event, $data );
    }

    /**
     * Authentication for pusher
     *
     * @param string $channelName
     * @param string $socket_id
     * @param string|null $data
     *
     * @return string
     */
    public function pusherAuth( string $channelName, string $socket_id, string $data = null ): string {
        try {
            return $this->pusher->socketAuth( $channelName, $socket_id, $data );
        } catch ( PusherException $e ) {
            return $e->getMessage();
        }
    }

}
