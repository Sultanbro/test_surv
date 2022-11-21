<?php

namespace Eddir\Messenger\Http\Controllers;

use Eddir\Messenger\Facades\MessengerFacade;
use Eddir\Messenger\Models\MessengerMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class MessagesController {

    protected int $perPage = 30;

    /**
     * Get chat messages
     *
     * @param int $chatId
     *
     * @return JsonResponse
     */
    public function fetchMessages( int $chatId ): JsonResponse {
        // check if user is member of chat
        if ( ! MessengerFacade::isMember( $chatId, Auth::user()->id ) ) {
            return response()->json( [
                'error' => 'You are not member of this chat'
            ], 403 );
        }

        // get start message id from request or set to 0
        $startMessageId = (int) request()->input( 'start_message_id', 0 );
        $count          = (int) request()->input( 'count', $this->perPage );
        $including      = request()->boolean( 'including' );

        $messages = MessengerFacade::fetchMessages( $chatId, $count, $startMessageId, $including );

        // last message should contain readers
        $lastMessage = $messages->first();
        if ( $lastMessage ) {
            $lastMessage->readers = $lastMessage->readers()->get();
            // exclude current user from readers
            $lastMessage->readers = $lastMessage->readers->filter( function ( $reader ) {
                return $reader->id !== Auth::user()->id;
            } );
            // set default avatar if user has no img_url
            $lastMessage->readers->transform( function ( $reader ) {
                if ( ! $reader->img_url ) {
                    $reader->img_url = config( 'messenger.user_avatar.default' ) ?? asset( 'vendor/messenger/images/users.png' );
                }

                return $reader;
            } );
        }

        $messages = $messages->toArray();

        return response()->json( $messages );
    }

    /**
     * Search messages by text
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function searchMessages( Request $request ): JsonResponse {
        $search = $request->get( 'q' );
        // select where user is member of chat and message contains search text
        $messages = MessengerMessage::whereHas( 'chat', function ( $query ) {
            $query->whereHas( 'users', function ( $query ) {
                $query->where( 'user_id', Auth::user()->id );
            } );
        } )->where( 'body', 'like', "%$search%" )->get();

        return response()->json( $messages );
    }

    /**
     * Send message to chat
     *
     * @param int $chatId
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function sendMessage( int $chatId, Request $request ): JsonResponse {
        $message = [];
        // check is user is not authorized
        if ( ! Auth::check() ) {
            return response()->json( [ 'message' => 'Unauthorized' ], 401 );
        }
        // check if message is empty
        if ( $request->message == "" ) {
            return response()->json( [ 'message' => 'Message is empty: ' ], 400 );
        }
        // check if message is too long
        if ( strlen( $request->message ) > 1000 ) {
            // split message to parts
            $parts = str_split( $request->message, 1000 );
            // send each part
            foreach ( $parts as $part ) {
                $message = MessengerFacade::sendMessage( $chatId, Auth::user()->id, $part );
            }
        } else {
            $message = MessengerFacade::sendMessage( $chatId, Auth::user()->id, $request->get( 'message' ) );
        }
        // set message as read
        MessengerFacade::setMessageAsRead( $message, Auth::user() );

        return response()->json( $message );
    }

    /**
     * Edit message
     *
     * @param int $messageId
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function editMessage( int $messageId, Request $request ): JsonResponse {
        // check if user is sender of message
        if ( ! MessengerFacade::isSender( $messageId, Auth::user()->id ) ) {
            return response()->json( [ 'message' => 'You are not sender of this message' ], 403 );
        }
        // check if message is empty
        if ( empty( $request->message ) ) {
            return response()->json( [ 'message' => 'Message is empty' ], 400 );
        }
        // check if message is too long
        if ( strlen( $request->message ) <= 1000 ) {
            $response = MessengerFacade::editMessage( $messageId, $request->get( 'message' ) );
        } else {
            // return error
            return response()->json( [ 'message' => 'Message is too long' ], 400 );
        }

        return response()->json( $response );
    }

    /**
     * Delete message from chat
     *
     * @param int $messageId
     *
     * @return JsonResponse
     */
    public function deleteMessage( int $messageId ): JsonResponse {
        // check if user is sender of message
        if ( ! MessengerFacade::isSender( $messageId, Auth::user()->id ) ) {
            return response()->json( [ 'message' => 'You are not sender of this message' ], 403 );
        }
        // delete message
        $message = MessengerFacade::deleteMessage( $messageId, Auth::user() );

        return response()->json( $message );
    }

    /**
     * Set message as read
     *
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setMessagesAsRead(): JsonResponse {
        $messageIds = request()->get( 'messages' );
        // select messages by ids where user is a member of chat of each message
        $messages = MessengerMessage::whereIn( 'id', $messageIds )
                                    ->whereHas( 'chat', function ( $query ) {
                                        $query->whereHas( 'users', function ( $query ) {
                                            $query->where( 'user_id', Auth::user()->id );
                                        } );
                                    } )
                                    ->get();
        // set messages as read
        MessengerFacade::setMessagesAsRead( $messages, Auth::user() );

        // return get chats last messages
        return response()->json( MessengerFacade::fetchChatsLastMessages( Auth::user() ) );
    }

    /**
     * Pin message to top of chat
     *
     * @param int $messageId
     *
     * @return JsonResponse
     */
    public function pinMessage( int $messageId ): JsonResponse {
        /** @var MessengerMessage $message */
        $message = MessengerMessage::find( $messageId );

        // check if user is member of chat
        if ( ! MessengerFacade::isMember( $message->chat_id, Auth::user()->id ) ) {
            return response()->json( [ 'message' => 'You are not member of this chat' ], 403 );
        }
        // pin message
        $message = MessengerFacade::pinMessage( $messageId, Auth::user() );

        return response()->json( $message );
    }

    /**
     * Unpin message from top of chat
     *
     * @param int $messageId
     *
     * @return JsonResponse
     */
    public function unpinMessage( int $messageId ): JsonResponse {
        /** @var MessengerMessage $message */
        $message = MessengerMessage::find( $messageId );

        // check if user is member of chat
        if ( ! MessengerFacade::isMember( $message->chat_id, Auth::user()->id ) ) {
            return response()->json( [ 'message' => 'You are not member of this chat' ], 403 );
        }
        // unpin message
        $message = MessengerFacade::unpinMessage( $messageId, Auth::user() );

        return response()->json( $message );
    }

}
