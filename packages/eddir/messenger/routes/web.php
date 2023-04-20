<?php

/**
 * API Routes
 */

use BeyondCode\LaravelWebSockets\Facades\WebSocketsRouter;
use Eddir\Messenger\Handlers\MessengerWebSocketHandler;
use Illuminate\Support\Facades\Route;

/**
 * Custom websocket handler
 */
WebSocketsRouter::webSocket('/messenger/app/{appKey}', MessengerWebSocketHandler::class);

/**
 * Authentication for pusher private channels
 */
Route::post('/chat/auth', 'ChatsController@pusherAuth')->name('api.pusher.auth');

/**
 * Get chats list
 */
Route::get('/v2/chats', 'ChatsController@fetchChats')->name('api.chats.fetch');

/**
 * Get company data
 */
Route::get('/v2/company', 'ChatsController@fetchCompany')->name('api.company.fetch');

/**
 * Get users list
 */
Route::get('/v2/users', 'ChatsController@fetchUsers')->name('api.users.fetch');

/**
 * Search chat by name
 */
Route::get('/v2/search/chats', 'ChatsController@search')->name('api.chats.search');

/**
 * Search messages by text
 */
Route::get('/v2/search/messages', 'MessagesController@searchMessages')->name('api.messages.search');

/**
 * Get chat messages
 */
Route::get('/v2/chat/{chat_id}/messages', 'MessagesController@fetchMessages')->name('api.messages.fetch');

/**
 * Get chat media files.
 */
Route::get('/v2/chat/{chat_id}/files', 'FilesController@fetchFiles')->name('api.files.fetch');

/**
 * Get private chat info
 */
Route::get('/v2/private/{user_id}', 'ChatsController@getPrivateChat')->name('api.v2.getPrivateChat');

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
 * Mute message.
 */
Route::post('/v2/chat/{chat_id}/mute', 'ChatsController@muteChat')->name('api.v2.muteChat');

/**
 * Unmute
 */
Route::delete('/v2/chat/{chat_id}/unmute', 'ChatsController@unmuteChat')->name('api.v2.unmuteChat');

/**
 * Set emoji reaction to message
 */
Route::post('/v2/message/{message_id}/reaction', 'MessagesController@setReaction')->name('api.v2.setReaction')->whereNumber('message_id');

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
 * Pin chat
 */
Route::post('/v2/chat/{chat_id}/pin', 'ChatsController@pinChat')->name('api.v2.pinChat');

/**
 * Unpin chat
 */
Route::delete('/v2/chat/{chat_id}/pin', 'ChatsController@unpinChat')->name('api.v2.unpinChat');

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
 * Edit chat
 */
Route::post('/v2/chat/{chat_id}/edit', 'ChatsController@editChat')->name('api.v2.editChat');

/**
 * Set messages as read
 */
Route::post('/v2/messages/read', 'MessagesController@setMessagesAsRead')->name('api.v2.setMessagesAsRead');

/**
 * Set user as chat admin
 */
Route::post('/v2/chat/{chat_id}/setAdmin/{user_id}', 'ChatsController@setAdmin')->name('api.v2.setAdmin');

/**
 * Remove user as chat admin
 */
Route::post('/v2/chat/{chat_id}/unsetAdmin/{user_id}', 'ChatsController@removeAdmin')->name('api.v2.removeAdmin');

/**
 * Upload file
 */
Route::post('/v2/chat/{chat_id}/upload', 'FilesController@upload')->name('api.v2.upload');

/**
 * Upload chat avatar
 */
Route::post('/v2/chat/{chat_id}/avatar', 'FilesController@uploadChatAvatar')->name('api.v2.uploadChatAvatar');
