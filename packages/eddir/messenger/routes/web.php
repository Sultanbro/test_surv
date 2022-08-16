<?php

/**
 * API Routes
 */

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/chat', function () {
    return Inertia::render('Chat/ChatWindow', [
        'phpVersion' => PHP_VERSION,
    ]);
});


/**
 * Authentication for pusher private channels
 */
Route::post('/chat/auth', 'Api\MessagesController@pusherAuth')->name('api.pusher.auth');

/**
 * Get chats list
 */
Route::get('/v2/chats', 'ChatsController@fetchChats')->name('api.chats.fetch');

/**
 * Get users list
 */
Route::get('/v2/users', 'ChatsController@fetchUsers')->name('api.users.fetch');

/**
 * Search chat by name
 */
Route::get('/v2/search', 'ChatsController@search')->name('api.chats.search');

/**
 * Get chat messages
 */
Route::get('/v2/chat/{chat_id}/messages', 'MessagesController@fetchMessages')->name('api.messages.fetch');

/**
 * Get chat info
 */
Route::get('/v2/chat/{chat_id}', 'ChatsController@getChat')->name('api.v2.getChat');

/**
 * Send message
 */
Route::post('/v2/chat/{chat_id}/messages', 'MessagesController@sendMessage')->name('api.v2.sendMessage');

/**
 * Edit message. Message id should be integer
 */
Route::post('/v2/message/{message_id}', 'MessagesController@editMessage')->name('api.v2.editMessage')->whereNumber('message_id');

/**
 * Delete message
 */
Route::delete('/v2/message/{message_id}', 'MessagesController@deleteMessage')->name('api.v2.deleteMessage');

/**
 * Pin message
 */
Route::post('/v2/message/{message_id}/pin', 'MessagesController@pinMessage')->name('api.v2.pinMessage');

/**
 * Unpin message
 */
Route::delete('/v2/message/{message_id}/pin', 'MessagesController@unpinMessage')->name('api.v2.unpinMessage');

/**
 * Create chat
 */
Route::post('/v2/chat', 'ChatsController@createChat')->name('api.v2.createChat');

/**
 * Remove chat
 */
Route::delete('/v2/chat/{chat_id}', 'ChatsController@removeChat')->name('api.v2.removeChat');

/**
 * Leave chat
 */
Route::post('/v2/chat/{chat_id}/leave', 'ChatsController@leaveChat')->name('api.v2.leaveChat');

/**
 * Add user to chat
 */
Route::post('/v2/chat/{chat_id}/addUser', 'ChatsController@addUser')->name('api.v2.addUser');

/**
 * Remove user from chat
 */
Route::post('/v2/chat/{chat_id}/removeUser/{user_id}', 'ChatsController@removeUser')->name('api.v2.removeUser');

/**
 * Set messages as read
 */
Route::post('/v2/messages/read', 'MessagesController@setMessagesAsRead')->name('api.v2.setMessagesAsRead');
