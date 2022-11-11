<?php

namespace Eddir\Messenger\Http\Controllers;

use Eddir\Messenger\Facades\MessengerFacade;
use Eddir\Messenger\Models\MessengerFile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class FilesController {

    /**
     * Upload file
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function upload(int $chatId, Request $request): JsonResponse
    {
        $message = [];
        // check is user is not authorized
        if (!Auth::check()) {
            return response()->json([ 'message' => 'Unauthorized' ], 401);
        }
        // check if message is empty
        if ($request->message == "") {
            return response()->json([ 'message' => 'Message is empty: ' ], 400);
        }
        // check if message is too long
        if (strlen($request->message) > 1000) {
            return response()->json([ 'message' => 'Message is too long: ' ], 400);
        }

        $file = $request->file('file');

        if (!$file->isValid()) {
            return response()->json([ 'message' => 'File is not valid: ' ], 400);
        }

        $fileModel = new MessengerFile();

        $fileName = time().'_'.$request->file->getClientOriginalName();
        //$filePath = $request->file('file')->storeAs('uploads', $fileName, 'public');
        $fileModel->name = time().'_'.$request->file->getClientOriginalName();
        //$fileModel->file_path = '/storage/' . $filePath;

        /**
         * custom
         */

        $disk = \Storage::disk('s3');
        $path = $disk->putFileAs('messsages', $request->file, $fileName);
        $fileModel->file_path  = $path;

        $message = MessengerFacade::sendMessage($chatId, Auth::user()->id, $request->get('message'), $fileModel);
        // set message as read
        MessengerFacade::setMessageAsRead($message, Auth::user());

        if ($message->files) {

            $message->files['file_path'] = $disk->temporaryUrl(
                $message->files['file_path'], now()->addMinutes(360)
            );

            return response()->json($message);
        }
        return response()->json([ 'message' => 'File is not saved: ' ], 400);
    }

}
