<?php
namespace App\Http\Controllers;

use Illuminate\Http\Response;

use Illuminate\Support\Facades\File;

use App\Packages\Dilab\Network\SimpleRequest;
use App\Packages\Dilab\Network\SimpleResponse;
use App\Packages\Dilab\Resumable;
use Storage;
use App\Models\Videos\Video;
use App\Models\Books\Book;

class UploadController extends Controller
{
    /**
     * Handles resumeable uploads via resumable.js
     * 
     * @return Response
     */
    public function resumableUpload()
    {   

       

        $tmpPath    = storage_path().'/';
        $uploadPath = storage_path().'/app/public/uploads';
        if(!File::exists($tmpPath)) {
            File::makeDirectory($tmpPath, $mode = 0777, true, true);
        }

        if(!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, $mode = 0777, true, true);
        }

        $simpleRequest              = new SimpleRequest();
        $simpleResponse             = new SimpleResponse();

        $resumable                  = new Resumable($simpleRequest, $simpleResponse);
        $resumable->tempFolder      = $tmpPath;
        $resumable->uploadFolder    = $uploadPath;


        $result = $resumable->process();
      
        switch($result) {
            case 200:
                return response([
                    'message' => 'OK',
                ], 200);
                break;
            case 201:
                // mark uploaded file as complete etc

                $params = $resumable->resumableParams();
                $sizeInBytes = $params['resumableTotalSize'];
                $filename = $params['resumableFilename'];
                $format = $params['resumableType'];

             
                
                $newFilename = date('YmdHis') . '_' . $filename;
                
                
                if($format == 'application/pdf') { // Загружена книга
                    $path = 'books';
                    

                    // // get first page of book
                    // $pdf = new \Spatie\PdfToImage\Pdf(storage_path(). '/app/public/uploads/' .$filename);
                    // $pdf->saveImage(public_path().'/images/bookcovers/' . $newFilename);
                    
                    Storage::move('public/uploads/' . $filename, 'public/'. $path .'/' .$newFilename);

                 //   '/images/bookcovers/' . $newFilename

                    $model = Book::create([
                        'title' => $newFilename,
                        'link' => '/storage/' . $path .'/' .$newFilename,
                        'author' => 'Неизвестный',
                        'img' => '',
                        'group_id' => 0,
                    ]);

                } else {
                    $path = 'videos';
                    $model = Video::create([
                        'title' => $newFilename,
                        'links' => '/storage/' . $path .'/'. $newFilename,
                        'duration' => 0,
                        'views' => 0,
                        'playlist_id' => 0,
                        'group_id' => 0,
                        'order' => 0
                    ]);

                    Storage::move('public/uploads/' . $filename, 'public/'. $path .'/' .$newFilename);
                }

               
                

                
                
                return response([
                    'message' => 'OK',
                    'filename' => $newFilename,
                    'path' => '/storage',
                    'model' => $model,
                ], 200);
                break;
            case 204:
                return response([
                    'message' => 'Chunk not found',
                ], 204);
                break;
            default:
          
                return response([
                    'message' => 'An error occurred',
                ], 404);
        }
    }
}