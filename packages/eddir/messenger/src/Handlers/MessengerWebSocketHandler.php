<?php

namespace Eddir\Messenger\Handlers;

use BeyondCode\LaravelWebSockets\Apps\App;
use BeyondCode\LaravelWebSockets\WebSockets\Channels\ChannelManager;
use BeyondCode\LaravelWebSockets\WebSockets\WebSocketHandler;
use Eddir\Messenger\Models\MessengerUserOnline;
use Illuminate\Support\Facades\DB;
use Ratchet\ConnectionInterface;
use Ratchet\RFC6455\Messaging\MessageInterface;

class MessengerWebSocketHandler extends WebSocketHandler {

    protected $channelManager;

    const APP_ID = '12345';

    public function __construct( ChannelManager $channelManager ) {
        $this->channelManager = $channelManager;
        MessengerUserOnline::truncate();
        parent::__construct( $channelManager );
    }

    public function onOpen( ConnectionInterface $conn ) {
        $conn->app = App::findById( self::APP_ID );
        
       // dd(tenant('id'));

        
        parent::onOpen( $conn );

        $this->userJoin( $conn->socketId, $conn->resourceId );
    }

    public function onMessage( ConnectionInterface $conn, MessageInterface $msg ) {
        parent::onMessage( $conn, $msg );
    }

    public function onError( ConnectionInterface $conn, \Exception $e ) {
        echo "An error has occurred: {$e->getMessage()}\n";
        $this->userEscape( $conn->resourceId );
        parent::onError( $conn, $e );
    }

    public function onClose( ConnectionInterface $conn ) {
        echo "Connection {$conn->resourceId} has disconnected\n";
        $this->userEscape( $conn->resourceId );
        parent::onClose( $conn );
    }

    public function userEscape( $resourceId ) {
        $user    = MessengerUserOnline::where( 'resource_id', $resourceId );
        $user_id = $user->first()->user_id;
        // remove user from online list
        $user->delete();

        $this->broadcastOffline( $user_id );
    }

    public function userJoin( $socketId, $resourceId ) {
        // в первый раз обмениваем socketId на resourceId
        echo "userJoin: s:{$socketId}, r:{$resourceId}\n";
        // find by socket id
        $userOnline = MessengerUserOnline::where( 'socket_id', $socketId )->first();
        if ( ! $userOnline ) {
            // create new user online
            $userOnline            = new MessengerUserOnline();
            $userOnline->socket_id = $socketId;
        }
        $userOnline->resource_id = $resourceId;
        $userOnline->save();
    }

    public function broadcastOffline($user_id) {
        // select all members of each chat which user is in
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
            echo "Send user escape event to user {$user->user_id}";
            $this->channelManager->find(self::APP_ID, 'private-messages.' . $user->user_id )->broadcast([
                'event' => 'newMessage',
                'channel' => 'private-messages.' . $user->user_id,
                'data'  => json_encode([
                    'message' => [
                        'event' => [
                            'type' => 'offline',
                        ],
                        'sender' => [
                            'id' => $user_id
                        ]
                    ]
                ]),
            ]);
        }
    }
}
