<?php

/**
 * ChatsController.php
 *
 * @noinspection PhpUndefinedFieldInspection
 */

namespace Eddir\Messenger\Http\Controllers;

use App\Position;
use App\ProfileGroup;
use Eddir\Messenger\Facades\MessengerFacade;
use Eddir\Messenger\Models\MessengerChat;
use Exception;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatsController extends Controller
{
    protected int $perPage = 30;

    /**
     * Authenticate the connection for pusher
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function pusherAuth(Request $request): JsonResponse
    {
        // Auth data
        $authData = json_encode([
            'user_id' => Auth::user()->id,
            'user_info' => [
                'name' => Auth::user()->name,
            ],
        ]);
        // check if user authorized
        if (Auth::check()) {
            $response = json_decode(MessengerFacade::pusherAuth(
                $request['channel_name'],
                $request['socket_id'],
                $authData,
                Auth::user()->id,
                \request()->getHost()
            ));
            return response()->json($response);
        }

        // if not authorized
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    /**
     * Get chats list for current user with last message and unread messages count for each chat
     *
     * @return JsonResponse
     */
    public function fetchChats(): JsonResponse
    {
        $chats = MessengerFacade::fetchChatsWithLastMessages(Auth::user());

        return response()->json([
            'chats' => $chats,
            'user' => Auth::user(),
        ]);
    }

    /**
     * Get company data
     *
     * @return JsonResponse
     */
    public function fetchCompany(): JsonResponse
    {
        $company = ['errors' => []];

        try {
            $company['users'] = User::where('deleted_at', null)->get();
        } catch (Exception $e) {
            $company['users'] = [];
            $company['errors'][] = $e->getMessage();
        }
        try {
            // positions
            $company['positions'] = Position::with(['activeUsers' => fn($query) => $query->select('id', 'name', 'position_id')])->get();
        } catch (Exception $e) {
            $company['positions'] = [];
            $company['errors'][] = $e->getMessage();
        }
        try {
            $company['profile_groups'] = ProfileGroup::with(['users' => fn($query) => $query
                ->select('id', 'name')
                ->wherePivot('status', 'active')
                ->where('users.deleted_at', null)])
                ->get();
        } catch (Exception $e) {
            $company['profile_groups'] = [];
            $company['errors'][] = $e->getMessage();
        }

        return response()->json($company);
    }

    /**
     * Get users list
     *
     * @return JsonResponse
     */
    public function fetchUsers(): JsonResponse
    {
        // return users list except current user
        return response()->json(User::where('id', '!=', Auth::user()->id)->get());
    }

    /**
     * Search chat by name
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        // check search query length
        if (strlen($request->get('q')) < 1) {
            return response()->json(['message' => 'Search query must be at least 1 characters long'], 422);
        }
        $chats = MessengerFacade::searchChats($request->get('q'), Auth::user()->id);
        $users = MessengerFacade::searchUsers($request->get('q'));

        foreach ($chats as $chat) {
            MessengerFacade::getChatAttributesForUser($chat, Auth::user());
        }

        return response()->json([
            'chats' => $chats,
            'users' => $users,
        ]);
    }

    /**
     * Create new chat
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function createChat(Request $request): JsonResponse
    {
        // check if user is authorized
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // chat should have title
        if (empty($request->title)) {
            return response()->json(['message' => 'Title is empty'], 400);
        }

        // chat title should be less than 50 characters
        if (strlen($request->title) > 255) {
            return response()->json(['message' => 'Title is too long'], 400);
        }

        // chat description should be less than 255 characters
        if (strlen($request->description) > 255) {
            return response()->json(['message' => 'Description is too long'], 400);
        }

        if (!$request->members) {
            return response()->json(['message' => 'Members are empty'], 400);
        }

        // create chat
        $chat = MessengerFacade::createChat(Auth::user(), $request->title, $request->description ?? "", $request->members);

        return response()->json($chat);
    }

    /**
     * Delete chat
     *
     * @param int $chat_id
     *
     * @return JsonResponse
     */
    public function removeChat(int $chat_id): JsonResponse
    {
        // check if user is authorized
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        // check if user is member of chat
        if (!MessengerFacade::isMember($chat_id, Auth::user()->id)) {
            return response()->json(['message' => 'You are not a member of this chat'], 403);
        }
        // check if user is owner of chat
        if (!MessengerFacade::isAdmin($chat_id, Auth::user()->id)) {
            return response()->json(['message' => 'You are not an owner of this chat'], 403);
        }
        // delete chat
        $chat = MessengerFacade::deleteChat($chat_id, Auth::user());

        return response()->json($chat);
    }

    /**
     * Add user to chat
     *
     * @param Request $request
     * @param int $chat_id
     *
     * @return JsonResponse
     */
    public function addUser(Request $request, int $chat_id): JsonResponse
    {
        // check if user is authorized
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        // check if user is member of chat
        if (!MessengerFacade::isMember($chat_id, Auth::user()->id)) {
            return response()->json(['message' => 'You are not a member of this chat'], 403);
        }
        // check if user exists
        if (!User::find($request->user_id)) {
            return response()->json(['message' => 'User not found'], 404);
        }
        // add user to chat
        $chat = MessengerFacade::addUserToChat($chat_id, $request->user_id, Auth::user());

        return response()->json($chat);
    }

    /**
     * Remove user from chat
     *
     * @param int $chat_id
     * @param int $user_id
     *
     * @return JsonResponse
     */
    public function removeUser(int $chat_id, int $user_id): JsonResponse
    {
        // check if user is authorized
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        // check if user is member of chat
        if (!MessengerFacade::isMember($chat_id, Auth::user()->id)) {
            return response()->json(['message' => 'You are not a member of this chat'], 403);
        }
        // remove user from chat
        $chat = MessengerFacade::removeUserFromChat($chat_id, $user_id, Auth::user());

        return response()->json($chat);
    }

    /**
     * Leave chat
     *
     * @param int $chat_id
     *
     * @return JsonResponse
     */
    public function leaveChat(string $chat_id): JsonResponse
    {
        // if chat_id contains prefix user, find chat with both users
        if (str_contains($chat_id, 'user')) {
            $chat_id = MessengerFacade::getPrivateChat(Auth::user()->id, str_replace('user', '', $chat_id))->id;
            if (!$chat_id) {
                return response()->json(['message' => 'Chat not found'], 404);
            }
        } // check if user is member of chat
        else if (!MessengerFacade::isMember((int)$chat_id, Auth::user()->id)) {
            return response()->json(['message' => 'You are not a member of this chat'], 403);
        }
        // leave chat
        $chat = MessengerFacade::removeUserFromChat($chat_id, Auth::user()->id, Auth::user());

        return response()->json($chat);
    }

    /**
     * Get private chat info
     *
     * @param int $user_id
     *
     * @return JsonResponse
     */
    public function getPrivateChat(int $user_id): JsonResponse
    {
        $chat = MessengerFacade::getPrivateChat(Auth::user()->id, $user_id);

        return response()->json(MessengerFacade::getChatAttributesForUser($chat, Auth::user()));
    }

    /**
     * Edit chat
     *
     * @param Request $request
     * @param int $chat_id
     *
     * @return JsonResponse
     */
    public function editChat(Request $request, int $chat_id): JsonResponse
    {
        // check if user is authorized
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        // check if user is member of chat
        if (!MessengerFacade::isMember($chat_id, Auth::user()->id)) {
            return response()->json(['message' => 'You are not a member of this chat'], 403);
        }
        // check if chat exists
        if (!MessengerFacade::getChat($chat_id)) {
            return response()->json(['message' => 'Chat not found'], 404);
        }
        // check if title is empty
        if (empty($request->title)) {
            return response()->json(['message' => 'Title is empty'], 400);
        }
        // check if title is too long
        if (strlen($request->title) > 50) {
            return response()->json(['message' => 'Title is too long'], 400);
        }
        // check if description is too long
        if (!empty($request->description) && strlen($request->description) > 255) {
            return response()->json(['message' => 'Description is too long'], 400);
        }
        // update chat
        $chat = MessengerFacade::updateChat($chat_id, $request->title, $request->description ?? "", Auth::user());

        return response()->json($chat);
    }

    /**
     * Get chat information by id
     *
     * @param int $chat_id
     *
     * @return JsonResponse
     */
    public function getChat(int $chat_id): JsonResponse
    {
        // get chat
        $chat = MessengerFacade::getChat($chat_id);

        // check if chat exists
        if (!$chat->users) { // need to check users, so we can return later chat data with users as polymorphic call
            return response()->json(['message' => 'Chat not found'], 404);
        }

        $chat = MessengerFacade::getChatAttributesForUser($chat, Auth::user());
        $chat->pinned_message = $chat->getPinnedMessages()->last();

        if ($chat->private) {
            $chat->users->map(function ($user) use ($chat) {
                if ($user->id !== Auth::user()->id) {
                    $position = DB::query()
                        ->select('position.position as position')
                        ->from('position')
                        ->join('users', 'users.position_id', '=', 'position.id')
                        ->where('users.id', $user->id)
                        ->first();
                    $chat->position = $position?->position;
                }
            });
        }

        // return chat model
        return response()->json($chat);
    }

    /**
     * Update chat
     *
     * @param Request $request
     * @param int $chat_id
     *
     * @return JsonResponse
     */
    public function updateChat(Request $request, int $chat_id): JsonResponse
    {
        // check if user is authorized
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        // check if user is member of chat
        if (!MessengerFacade::isMember($chat_id)) {
            return response()->json(['message' => 'You are not a member of this chat'], 403);
        }
        // check if chat exists
        if (!MessengerFacade::getChat($chat_id)) {
            return response()->json(['message' => 'Chat not found'], 404);
        }
        // check if title is empty
        if (empty($request->title)) {
            return response()->json(['message' => 'Title is empty'], 400);
        }
        // check if title is too long
        if (strlen($request->title) > 50) {
            return response()->json(['message' => 'Title is too long'], 400);
        }
        // check if description is too long
        if (strlen($request->description) > 255) {
            return response()->json(['message' => 'Description is too long'], 400);
        }
        // update chat
        $chat = MessengerFacade::updateChat($chat_id, $request->title, $request->description, Auth::user());

        return response()->json($chat);
    }

    /**
     * Pin chat
     *
     * @param int $chat_id
     *
     * @return JsonResponse
     */
    public function pinChat(int $chat_id): JsonResponse
    {
        // check if user is authorized
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        // check if chat exists
        if (!MessengerFacade::getChat($chat_id)) {
            return response()->json(['message' => 'Chat not found'], 404);
        }
        // check if user is member of chat
        if (!MessengerFacade::isMember($chat_id, Auth::user()->id)) {
            return response()->json(['message' => 'You are not a member of this chat'], 403);
        }
        // pin chat
        $chat = MessengerFacade::pinChat($chat_id, Auth::user());

        return response()->json($chat);
    }

    /**
     * Unpin chat
     *
     * @param int $chat_id
     *
     * @return JsonResponse
     */
    public function unpinChat(int $chat_id): JsonResponse
    {
        // check if user is authorized
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        // check if chat exists
        if (!MessengerFacade::getChat($chat_id)) {
            return response()->json(['message' => 'Chat not found'], 404);
        }
        // check if user is member of chat
        if (!MessengerFacade::isMember($chat_id, Auth::user()->id)) {
            return response()->json(['message' => 'You are not a member of this chat'], 403);
        }
        // unpin chat
        $chat = MessengerFacade::unpinChat($chat_id, Auth::user());

        return response()->json($chat);
    }

    /**
     * Set user as admin
     *
     * @param int $chat_id
     * @param int $user_id
     *
     * @return JsonResponse
     */
    public function setAdmin(int $chat_id, int $user_id): JsonResponse
    {
        // check if user is authorized
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        // check if user is admin
        if (!MessengerFacade::isAdmin($chat_id, Auth::user()->id)) {
            return response()->json(['message' => 'You are not an admin of this chat'], 403);
        }
        // check if user exists
        if (!User::find($user_id)) {
            return response()->json(['message' => 'User not found'], 404);
        }
        // check if user is already admin
        if (MessengerFacade::isAdmin($chat_id, $user_id)) {
            return response()->json(['message' => 'User is already an admin'], 400);
        }
        // set user as admin
        $chat = MessengerFacade::setAdmin(MessengerChat::find($chat_id), User::find($user_id));

        return response()->json($chat);
    }

    /**
     * Remove user as admin
     *
     * @param int $chat_id
     * @param int $user_id
     *
     * @return JsonResponse
     */
    public function removeAdmin(int $chat_id, int $user_id): JsonResponse
    {
        // check if user is authorized
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        // check if user is admin
        if (!MessengerFacade::isAdmin($chat_id, Auth::user()->id)) {
            return response()->json(['message' => 'You are not an admin of this chat'], 403);
        }
        // check if user exists
        if (!User::find($user_id)) {
            return response()->json(['message' => 'User not found'], 404);
        }
        // check if user is already admin
        if (!MessengerFacade::isAdmin($chat_id, $user_id)) {
            return response()->json(['message' => 'User is not an admin'], 400);
        }
        // remove user as admin
        $chat = MessengerFacade::removeAdmin(MessengerChat::find($chat_id), User::find($user_id));

        return response()->json($chat);
    }

    /**
     * @param int $chatId
     * @return JsonResponse
     */
    public function muteChat(int $chatId): JsonResponse
    {
        // check if user is authorized
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if ($chatId == 0) {
            $chat = MessengerFacade::getGeneralChat();
        } else {
            $chat = MessengerChat::findOrFail($chatId);
        }


        /**
         * Mute taken chat.
         */
        $chat = MessengerFacade::muteChatForUser($chat);

        return response()->json($chat);
    }

    /**
     * @param int $chatId
     * @return JsonResponse
     */
    public function unmuteChat(int $chatId): JsonResponse
    {
        // check if user is authorized
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if ($chatId == 0) {
            $chat = MessengerFacade::getGeneralChat();
        } else {
            $chat = MessengerChat::findOrFail($chatId);
        }


        /**
         * Unmute taken chat.
         */
        $chat = MessengerFacade::unmuteChatForUser($chat);

        return response()->json($chat);
    }
}
