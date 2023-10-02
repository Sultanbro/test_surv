<?php

namespace App\Http\Controllers\Learning;

use App\Http\Controllers\Controller;
use App\Models\Books\Book;
use App\Models\Videos\Video;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;

class FileUploadController extends Controller
{

    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('upload-file.index');
    }

    public function uploadLargeFiles(Request $request)
    {

        $receiver = new FileReceiver('file', $request, HandlerFactory::classFromRequest($request));


        if (!$receiver->isUploaded()) {
            // file not uploaded
        }

        $fileReceived = $receiver->receive(); // receive file
        if ($fileReceived->isFinished()) { // file uploading is complete / all chunks are uploaded
            $file = $fileReceived->getFile(); // get file
            $extension = $file->getClientOriginalExtension();
            $originalFileName = $file->getClientOriginalName();
            $fileName = uniqid() . '_' . md5(time()) . '.' . $extension; // a unique file name

            // dd($disk->put(
            // 	'cloud-filename.txt',
            // 	\Storage::disk('local')->get('videos/5_1642510994.png')
            // ));

            $disk = \Storage::disk('s3');

            $file_path = 'files';
            if ($request->type == 'video') $file_path = 'videos/' . $request->id;
            if ($request->type == 'book') $file_path = 'books';

            $path = $disk->putFileAs($file_path, $file, $fileName);
            $path = $disk->url($path);


            $model = $this->saveModel($request->type, $request->id, $file_path, $fileName, $originalFileName);

            // delete chunked file
            unlink($file->getPathname());


            return [
                'path' => $disk->temporaryUrl(
                    $file_path . '/' . $fileName, now()->addMinutes(360)
                ),
                'filename' => $originalFileName,
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


    private function saveModel($type, $id, $path, $filename, $originalFileName)
    {


        if ($type == 'video') { // Загружена video

            $model = Video::create([
                'title' => basename($originalFileName),
                'links' => '/' . $path . '/' . $filename,
                'domain' => 'storage.oblako.kz',
                'duration' => 0,
                'views' => 0,
                'playlist_id' => $id,
                'group_id' => 0,
                'order' => 0
            ]);

        }

        if ($type == 'book') {
            // // get first page of book
            // $pdf = new \Spatie\PdfToImage\Pdf(storage_path(). '/app/public/uploads/' .$filename);
            // $pdf->saveImage(public_path().'/images/bookcovers/' . $newFilename);
            //  '/images/bookcovers/' . $newFilename

            $model = Book::create([
                'title' => basename($originalFileName),
                'link' => '/' . $path . '/' . $filename,
                'author' => 'Название автора',
                'img' => '',
                'description' => '',
                'domain' => 'storage.oblako.kz',
                'group_id' => 0,
            ]);

        }

        return $model;
    }
}
