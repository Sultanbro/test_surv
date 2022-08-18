<?php

/**
 * MessengerFacade package for Laravel.
 *
 * @noinspection PhpUndefinedFieldInspection
*/

namespace Eddir\Messenger\Http\Controllers\Api;

use Eddir\Messenger\Models\MessengerMessage;
use App\User;
use Eddir\Messenger\Facades\MessengerFacade;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

/**
 * Class MessagesController
 *
 * @mixin Builder
 * @deprecated since version 1.0.0
 */
class MessagesController extends Controller
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
            return MessengerFacade::pusherAuth(
                $request['channel_name'],
                $request['socket_id'],
                $authData
            );
        }
        // if not authorized
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    /**
     * Fetch data by id for (user/group)
     *
     * @param Request $request
     *
     * @return Authenticatable
     */
    public function idFetchData(Request $request): Authenticatable
    {
        return auth()->user();
        // Favorite
//        $favorite = MessengerFacade::inFavorite($request['id']);
//
//        // User data
//        if ($request['type'] == 'user') {
//            $fetch = User::where('id', $request['id'])->first();
//            if ($fetch) {
//                $userAvatar = MessengerFacade::getUserWithAvatar($fetch)->avatar;
//            }
//        }
//
//        // send the response
//        return Response::json([
//            'favorite' => $favorite,
//            'fetch' => $fetch ?? [],
//            'user_avatar' => $userAvatar ?? null,
//        ]);
    }

    /**
     * This method to make a links for the attachments
     * to be downloadable.
     *
     * @param string $fileName
     *
     * @return JsonResponse
     */
    public function download(string $fileName): JsonResponse
    {
        $path = config('messenger.attachments.folder') . '/' . $fileName;
        if (MessengerFacade::storage()->exists($path)) {
            return response()->json([
                'file_name' => $fileName,
                'download_path' => MessengerFacade::storage()->url($path),
            ]);
        } else {
            return response()->json([
                'message' => "Извините, файл не найден или был удален",
            ], 404);
        }
    }

    /**
     * Send a message to database
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function send(Request $request): JsonResponse
    {
        // default variables
        $error = (object) [
            'status' => 0,
            'message' => null,
        ];
        $attachment = null;
        $attachment_title = null;

        // if there is attachment [file]
        if ($request->hasFile('file')) {
            // allowed extensions
            $allowed_images = MessengerFacade::getAllowedImages();
            $allowed_files  = MessengerFacade::getAllowedFiles();
            $allowed        = array_merge($allowed_images, $allowed_files);

            $file = $request->file('file');
            // check file size
            if ($file->getSize() < MessengerFacade::getMaxUploadSize()) {
                if (in_array(strtolower($file->getClientOriginalExtension()), $allowed)) {
                    // get attachment name
                    $attachment_title = $file->getClientOriginalName();
                    // upload attachment and store the new name
                    $attachment = Str::uuid() . "." . $file->getClientOriginalExtension();
                    $file->storeAs(
                        config('messenger.attachments.folder'),
                        $attachment,
                        config('messenger.storage_disk_name')
                    );
                } else {
                    $error->status = 1;
                    $error->message = "Недопустимое расширение файла!";
                }
            } else {
                $error->status = 1;
                $error->message = "Файл слишком большой!";
            }
        }

        if (!$error->status) {
            // send to database
            $messageID = mt_rand(9, 999999999) + time();
            MessengerFacade::newMessage([
                'id' => $messageID,
                'type' => $request['type'] or 'user',
                'from_id' => Auth::user()->id,
                'to_id' => $request['id'],
                'body' => htmlentities(trim($request['message']), ENT_QUOTES, 'UTF-8'),
                'attachment' => ($attachment) ? json_encode((object) [
                    'new_name' => $attachment,
                    'old_name' => htmlentities(trim($attachment_title), ENT_QUOTES, 'UTF-8'),
                ]) : null,
            ]);

            // fetch message to send it with the response
            $messageData = MessengerFacade::fetchMessage($messageID);

            // send to user using pusher
            MessengerFacade::push('messages.' . $request['id'], 'NewMessage', [
                'from_id' => Auth::user()->id,
                'to_id' => $request['id'],
                'message' => MessengerFacade::messageCard($messageData, 'default'),
            ]);
        }

        // send the response
        return Response::json([
            'status' => '200',
            'error' => $error,
            'message' => $messageData ?? [],
            'tempID' => $request['temporaryMsgId'],
        ]);
    }

    /**
     * Fetch [user/group] messages from database
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function fetch(Request $request): JsonResponse
    {
        $query = MessengerFacade::fetchMessagesQuery($request['id'])->latest();
        $messages = $query->paginate($request->per_page ?? $this->perPage);
        $totalMessages = $messages->total();
        $lastPage = $messages->lastPage();
        $response = [
            'user_id' => $request['id'],
            'total' => $totalMessages,
            'last_page' => $lastPage,
            'last_message_id' => collect($messages->items())->last()->id ?? null,
            'messages' => $messages->items(),
        ];

        // get all unseen messages from messages
        $unseenMessages = [];
        foreach ($messages as $message) {
            if ($message->seen == 0) {
                $unseenMessages[] = $message->id;
            }
        }
        // check if there is unseen messages and seen them
        if (count($unseenMessages) > 0) {
            MessengerFacade::makeSeenList($unseenMessages);
        }

        return Response::json($response);
    }

    /**
     * Make messages as seen
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function seen(Request $request): JsonResponse
    {
        // make as seen
        $seen = MessengerFacade::makeSeen($request['id']);
        // send the response
        return Response::json([
            'status' => $seen,
        ]);
    }

    /**
     * Make messages list as seen
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function seenList(Request $request): JsonResponse
    {
        // make as seen
        $seen = MessengerFacade::makeSeenList($request['ids']);
        // send the response
        return Response::json([
            'status' => $seen,
        ]);
    }

    /**
     * Get chats list
     *
     * @param Request $request
     * @return JsonResponse response
     */
    public function getContacts(Request $request): JsonResponse
    {
        // get all users that received/sent message from/to [Auth user]
        $users = MessengerMessage::join('users', function ($join) {
            $join->on('messenger_messages.from_id', '=', 'users.id')
                 ->orOn('messenger_messages.to_id', '=', 'users.id');
        })
                        ->where('messenger_messages.hidden', false)
                        ->where(function ($q) {
                            $q->where('messenger_messages.from_id', Auth::user()->id)
                              ->orWhere('messenger_messages.to_id', Auth::user()->id);
                        })
                        ->where('users.id', '!=', Auth::user()->id)
                        ->select(
                            'users.*',
                            DB::raw('MAX(messenger_messages.created_at) max_created_at'),
                        )
                        ->orderBy('max_created_at', 'desc')
                        ->groupBy('users.id')
                        ->paginate($request->per_page ?? $this->perPage);

        if ($users->count() > 0) {
            foreach ($users as $user) {
                $user->last_message = MessengerFacade::getLastMessageQuery($user->id);
                $user->unseen = MessengerFacade::countUnseenMessages($user->id);
            }
        }

        return response()->json([
            'user_id' => Auth::user()->id,
            'chats' => $users->items(),
            'total' => $users->total() ?? 0,
            'last_page' => $users->lastPage() ?? 1,
        ], 200);
    }

    /**
     * Search in messenger
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $input = trim(filter_var($request['input']));
        $records = User::where('id', '!=', Auth::user()->id)
                       ->where('name', 'LIKE', "%$input%")
                       ->paginate($request->per_page ?? $this->perPage);

        foreach ($records->items() as $index => $record) {
            $records[$index] = MessengerFacade::getUserWithAvatar($record);
            // get last message
            $records[$index]->last_message = MessengerFacade::getLastMessageQuery($record->id);
        }

        return Response::json([
            'records' => $records->items(),
            'total' => $records->total(),
            'last_page' => $records->lastPage(),
            'request' => $request['input'],
        ]);
    }

    /**
     * Get shared photos
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sharedPhotos(Request $request): JsonResponse
    {
        $images = MessengerFacade::getSharedPhotos($request['user_id']);

        foreach ($images as $image) {
            $image = asset(config('messenger.attachments.folder') . $image);
        }
        // send the response
        return Response::json([
            'shared' => $images ?? [],
        ]);
    }

    /**
     * Edit message
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function edit(Request $request): JsonResponse
    {
        return Response::json([
            'edited' => MessengerFacade::editMessage($request['id'], $request['body']),
        ]);
    }

    /**
     * Delete message
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function delete(Request $request): JsonResponse
    {
        return Response::json([
            'deleted' => MessengerFacade::deleteMessage($request['id']),
        ]);
    }

    /**
     * Delete conversation
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function deleteConversation(Request $request): JsonResponse
    {
        // delete
        $delete = MessengerFacade::deleteConversation($request['id']);

        // send the response
        return Response::json([
            'deleted' => $delete ? 1 : 0,
        ]);
    }

    public function updateSettings(Request $request): JsonResponse
    {
        $msg = null;
        $error = $success = 0;

        // If messenger color selected
        if ($request['messengerColor']) {
            $messenger_color = trim(filter_var($request['messengerColor']));
            User::where('id', Auth::user()->id)
                ->update(['messenger_color' => $messenger_color]);
        }
        // if there is a [file]
        if ($request->hasFile('avatar')) {
            // allowed extensions
            $allowed_images = MessengerFacade::getAllowedImages();

            $file = $request->file('avatar');
            // check file size
            if ($file->getSize() < MessengerFacade::getMaxUploadSize()) {
                if (in_array(strtolower($file->getClientOriginalExtension()), $allowed_images)) {
                    // delete the older one
                    if (Auth::user()->avatar != config('messenger.user_avatar.default')) {
                        $path = MessengerFacade::getUserAvatarUrl(Auth::user()->avatar);
                        if (MessengerFacade::storage()->exists($path)) {
                            MessengerFacade::storage()->delete($path);
                        }
                    }
                    // upload
                    $avatar = Str::uuid() . "." . $file->getClientOriginalExtension();
                    $update = User::where('id', Auth::user()->id)->update(['avatar' => $avatar]);
                    $file->storeAs(
                        config('messenger.user_avatar.folder'),
                        $avatar,
                        config('messenger.storage_disk_name')
                    );
                    $success = $update ? 1 : 0;
                } else {
                    $msg = "Недопустимое расширение файла.";
                    $error = 1;
                }
            } else {
                $msg = "Размер файла превышает максимально допустимый.";
                $error = 1;
            }
        }

        // send the response
        return Response::json([
            'status' => $success ? 1 : 0,
            'error' => $error ? 1 : 0,
            'message' => $error ? $msg : 0,
        ]);
    }

    /**
     * Set user's active status
     *
     * @return JsonResponse
     */
    public function setActiveStatus(): JsonResponse
    {
        $update = User::where('id', Auth::user()->id)->update(['active_status' => 1]);
        // send the response
        return Response::json([
            'status' => $update,
        ]);
    }
}
