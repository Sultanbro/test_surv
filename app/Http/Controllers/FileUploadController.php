<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use App\Models\Videos\Video;
use App\Models\Books\Book;

class FileUploadController extends Controller {

    /**
     * @return Application|Factory|View
     */
    public function index() {
        return view('upload-file.index');
    }

    public function uploadLargeFiles(Request $request) {
        $receiver = new FileReceiver('file', $request, HandlerFactory::classFromRequest($request));

        if (!$receiver->isUploaded()) {
            // file not uploaded
        }

        $fileReceived = $receiver->receive(); // receive file
        if ($fileReceived->isFinished()) { // file uploading is complete / all chunks are uploaded
            $file = $fileReceived->getFile(); // get file
            $extension = $file->getClientOriginalExtension();
            $fileName = str_replace('.'.$extension, '', $file->getClientOriginalName()); //file name without extenstion
            $fileName .= '_' . md5(time()) . '.' . $extension; // a unique file name

        
            // $disk = \Storage::build([
            //     'driver' => 's3',
            //     'key' => 'O4493_admin',
            //     'secret' => 'nzxk4iNukQWx',
            //     'region' => 'us-east-1',
            //     'bucket' => 'video',
            //     'endpoint' => 'https://storage.oblako.kz:443',
            //     'use_path_style_endpoint' => true,
            //     'throw' => false
            // ]);
            
            // dd($disk->put(
            // 	'cloud-filename.txt',
            // 	\Storage::disk('local')->get('videos/5_1642510994.png')
            // ));

            //$disk = Storage::disk(config('filesystems.default'));

            $disk = \Storage::build([
                'driver' => 's3',
                'key' => 'O4493_admin',
                'secret' => 'nzxk4iNukQWx',
                'region' => 'us-east-1',
                'bucket' => 'tenantbp',
                'endpoint' => 'https://storage.oblako.kz:443',
                'use_path_style_endpoint' => true,
                'throw' => false,
                'visibility' => 'public'
            ]);

            $path = $disk->putFileAs('videos', $file, $fileName);
            $path = $disk->url($path);

            if($extension == 'mp4') {
                $file_path = 'videos';
            } else {
                $file_path = 'books';
            }

            $model = $this->saveModel($extension, $path, $fileName);
                
            // delete chunked file
            unlink($file->getPathname());
            return [
                'path' => $file_path . '/' . $fileName,
                'filename' => $fileName,
                'model' => $model,
            ];
        }

        // otherwise return percentage informatoin
        $handler = $fileReceived->handler();
        return [
            'done' => $handler->getPercentageDone(),
            'status' => true
        ];
    }


    private function saveModel($extension, $path, $filename)
    {
       
        
        if($extension == 'mp4') { // Загружена video

            $model = Video::create([
                'title' => $filename,
                'links' => $path,
                'duration' => 0,
                'views' => 0,
                'playlist_id' => 0,
                'group_id' => 0,
                'order' => 0
            ]);
           
        } else {
           
            // // get first page of book
            // $pdf = new \Spatie\PdfToImage\Pdf(storage_path(). '/app/public/uploads/' .$filename);
            // $pdf->saveImage(public_path().'/images/bookcovers/' . $newFilename);
            //  '/images/bookcovers/' . $newFilename

            $model = Book::create([
                'title' => $filename,
                'link' => $path,
                'author' => 'Неизвестный',
                'img' => '',
                'group_id' => 0,
            ]);

        }

        return $model;
    }
}
