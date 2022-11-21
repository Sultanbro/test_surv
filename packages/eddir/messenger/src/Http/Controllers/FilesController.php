<?php

namespace Eddir\Messenger\Http\Controllers;

use Eddir\Messenger\Facades\MessengerFacade;
use Eddir\Messenger\Models\MessengerFile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FilesController {

    /**
     * Upload file
     *
     * @param int $chatId
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function upload( int $chatId, Request $request ): JsonResponse {
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
            return response()->json( [ 'message' => 'Message is too long: ' ], 400 );
        }

        $file = $request->file( 'file' );

        if ( ! $file->isValid() ) {
            return response()->json( [ 'message' => 'File is not valid: ' ], 400 );
        }

        $fileModel = new MessengerFile();
        // get current milliseconds
        $milliseconds = round( microtime( true ) * 1000 );
        // file name should be unique with a timestamp
        $fileModel->name = $milliseconds . '_' . $file->getClientOriginalName();

        // if file doesnt contain extension
        if ( ! $file->getClientOriginalExtension() ) {
            // add extension based on mime type
            switch ( $file->getMimeType() ) {
                case 'audio/ogg':
                    $fileModel->name .= '.ogg';
                    break;
                case 'audio/mpeg':
                    $fileModel->name .= '.mp3';
                    break;
                case 'video/webm':
                case 'audio/webm':
                    $fileModel->name .= '.webm';
                    break;
                case 'audio/wav':
                    $fileModel->name .= '.wav';
                    break;
                default:
                    dd( $file->getMimeType() );
            }
        }

        $fileModel->file_path = $file->storeAs( 'messenger', $fileModel->name );

        // if file is image, create thumbnail
        if ( in_array( $file->getMimeType(), [ 'image/jpeg', 'image/png', 'image/gif' ] ) ) {

            $tmpFile = tempnam( sys_get_temp_dir(), $fileModel->name );
            $this->resize_crop_image( 350, 200, $file->getRealPath(), $tmpFile );

            // upload thumbnail to storage
            $fileModel->thumbnail_path = 'messenger/thumbs/thumb_' . $fileModel->name;
            Storage::put( 'messenger/thumbs/thumb_' . $fileModel->name, file_get_contents( $tmpFile ) );

            unlink( $tmpFile );
        }

        $message = MessengerFacade::sendMessage( $chatId, Auth::user()->id, $request->get( 'message' ), $fileModel );

        if ( ! $message->files ) {
            return response()->json( [ 'message' => 'File is not saved: ' ], 400 );
        }

        return response()->json( $message );
    }

    public function resize_crop_image( $max_width, $max_height, $source_file, $dst_dir, $quality = 80 ): bool {
        $img_size = getimagesize( $source_file );
        $width    = $img_size[0];
        $height   = $img_size[1];
        $mime     = $img_size['mime'];

        switch ( $mime ) {
            case 'image/gif':
                $image_create = "imagecreatefromgif";
                $image        = "imagegif";
                break;

            case 'image/png':
                $image_create = "imagecreatefrompng";
                $image        = "imagepng";
                $quality      = 7;
                break;

            case 'image/jpeg':
                $image_create = "imagecreatefromjpeg";
                $image        = "imagejpeg";
                $quality      = 80;
                break;

            default:
                return false;
        }

        $dst_img = imagecreatetruecolor( $max_width, $max_height );
        $src_img = $image_create( $source_file );

        $width_new  = (int) ( $height * $max_width / $max_height );
        $height_new = (int) ( $width * $max_height / $max_width );
        //if the new width is greater than the actual width of the image, then the height is too large and the rest cut off, or vice versa
        if ( $width_new > $width ) {
            //cut point by height
            $h_point = (int) ( ( $height - $height_new ) / 2 );
            //copy image
            imagecopyresampled( $dst_img, $src_img, 0, 0, 0, $h_point, $max_width, $max_height, $width, $height_new );
        } else {
            //cut point by width
            $w_point = (int) ( ( $width - $width_new ) / 2 );
            imagecopyresampled( $dst_img, $src_img, 0, 0, $w_point, 0, $max_width, $max_height, $width_new, $height );
        }

        $image( $dst_img, $dst_dir, $quality );

        if ( $dst_img ) {
            imagedestroy( $dst_img );
        }
        if ( $src_img ) {
            imagedestroy( $src_img );
        }

        return true;
    }
}
