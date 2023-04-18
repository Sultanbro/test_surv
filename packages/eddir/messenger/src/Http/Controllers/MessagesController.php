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
        $year           = (int) request()->input('year') ?? null;
        $month          = (int) request()->input('month') ?? null;

        $messages = MessengerFacade::fetchMessages( $chatId, $count, $year, $month, $startMessageId, $including);
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
        $search     = $request->get( 'q' );
        $chat_id    = $request->get( 'chat_id' );
        $only_files = $request->boolean( 'only_files' );
        $date       = $request->get( 'date' );

        $messages = MessengerMessage::with('sender');

        if ( $chat_id ) {
            $messages = $messages->where( 'chat_id', $chat_id );
        } else {
            $messages = $messages->whereHas( 'chat', function ( $query ) {
                $query->whereHas( 'members', function ( $query ) {
                    $query->where( 'user_id', Auth::user()->id );
                } );
            } );
        }

        if ( $only_files ) {
            $messages = $messages->with('files')->whereHas( 'files', function ( $query ) use ( $search ) {
                $query->where( 'name', 'like', "%$search%" );
            } );
        } else {
            $messages = $messages->where( 'body', 'like', "%$search%" );
        }

        if ( $date ) {
            $messages = $messages->whereDate( 'created_at', $date );
        }

        return response()->json( $messages->get() );
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
                $message = MessengerFacade::sendMessage( $chatId, Auth::user()->id, $part, [], $request->get( 'cite_message_id' ) );
            }
        } else {
            $message = MessengerFacade::sendMessage( $chatId, Auth::user()->id, $request->get( 'message' ), [], $request->get( 'cite_message_id' ) );
        }

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
     * Set emoji reaction to message
     *
     * @param int $messageId
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function setReaction( int $messageId, Request $request ): JsonResponse {
        $chat = MessengerFacade::getChatByMessageId( $messageId );
        if ( ! $chat ) {
            return response()->json( [ 'message' => 'Chat not found' ], 404 );
        }
        // check if user is member of chat
        if ( ! MessengerFacade::isMember( $chat->id, Auth::user()->id ) ) {
            return response()->json( [ 'message' => 'You are not member of this chat' ], 403 );
        }
        // get emoji as int
        $emoji = (int) $request->emoji;
        // check if emoji is too long
        if ( $emoji >= 0 && $emoji <= 255 ) {
            $response = MessengerFacade::setReaction( $messageId, Auth::user(), $emoji );
        } else {
            // return error
            return response()->json( [ 'message' => 'Emoji is too long' ], 400 );
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
                                        $query->whereHas( 'members', function ( $query ) {
                                            $query->where( 'user_id', Auth::user()->id );
                                        } );
                                    } )
                                    ->get();
        if ( $messages->count() > 0 ) {
            // set messages as read
            MessengerFacade::setMessagesAsRead( $messages, Auth::user() );

            // return get chats last messages
            return response()->json( [
                'left' => $messages->first()->chat->getUnreadMessagesCount( Auth::user() ),
            ] );
        }

        return response()->json( [ 'ok' => false ] );
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
