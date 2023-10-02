<?php

namespace App\Http\Controllers\Learning;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Models\Books\Book;
use App\Models\Books\BookGroup;
use App\Models\Books\BookSegment;
use App\Models\TestQuestion;
use App\Models\CourseItemModel;

class UpbookController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        View::share('menu', 'upbook');
        View::share('link', 'upbook');

        return view('upbooks');
    }

    public function admin_get(Request $request)
    {
        $cats = BookGroup::with('books')->get();


        $nocat_books = Book::where('group_id', 0)->get();

        if($nocat_books->count() > 0) {
            $cat = new BookGroup();
            $cat->id = 0;
            $cat->name = 'Без категории';
            $cat->books = $nocat_books;

            $cats->push($cat);
        }

        // get links

        $disk = \Storage::disk('s3');

        foreach ($cats as $xkey => $cat) {

            if(!$cat->books) continue;

            foreach ($cat->books as $key => $book) {
                if($book->domain == 'storage.oblako.kz') {
                    $book->link = $disk->temporaryUrl(
                        $book->link, now()->addMinutes(360)
                    );
                }
                if($book->img != null && $book->img != '') {
                    $book->img = $disk->temporaryUrl(
                        $book->img, now()->addMinutes(360)
                    );
                }

            }

        }


        return [
            'categories' => $cats->toArray()
        ];
    }


    public function createCategory(Request $request)
    {
        return BookGroup::create([
            'name' => $request->name,
            'books' => '[]'
        ]);
    }

    public function deleteCategory(Request $request)
    {
        $bg = BookGroup::find($request->id);
        if($bg) {
            $bg->delete();
        }
    }



    /**
     * Get book and it's tests
     *
     * @param Request $request
     *
     * @return array
     *
     * return [
     *    'tests' => array,
     *    'activeBook' => Book::class,
     * ];
     */
    public function getSegments(Request $request)
    {
        $user_id = auth()->id();
        $book = Book::where('id',$request->id)
            ->with('segments')
            ->first();


        if($book) {


            // @TODO
            // get test results

            foreach ($book->segments as $key => $segment) {
                $segment->page = $segment->page_start;
                $segment->pages = $segment->page_start;

                $course_item_model_id = 0;

                $segment->questions = TestQuestion::where('testable_type', 'App\Models\Books\BookSegment')
                    ->where('testable_id', $segment->id)
                    ->with('result', function ($query) use ($course_item_model_id, $user_id) {
                        $query->where('course_item_model_id', $course_item_model_id)
                            ->where('user_id', $user_id);
                    })
                    ->get();

                $segment->item_model = CourseItemModel::where('user_id', $user_id)
                    ->where('type', 1)
                    ->where('item_id', $segment->id)
                    ->where('course_item_id', $request->course_item_id ?? 0)
                    ->first();
            }

            // get link storage
            $disk = \Storage::disk('s3');

            if($book->domain == 'storage.oblako.kz') {


                if($book->link != '' && $book->link != null) {
                    $book->link = $disk->temporaryUrl(
                        $book->link, now()->addMinutes(360)
                    );
                }

            }

            if($book->img != '' && $book->img != null) {
                $book->img = $disk->temporaryUrl(
                    $book->img, now()->addMinutes(360)
                );
            }
        }


        return [
            'segments' => $book ? $book->segments : [],
            'activeBook' => $book,
        ];
    }

    public function save(Request $request)
    {
        $b = Book::find($request->id);
        $blink = "";

        if($b) {

            if($request->file('file')) {
                $links = $this->uploadFile('/bookcovers', $request->file('file'));
                $blink = $links['temp'];
                $b->img = $links['relative'];
            }

            $b->title = $request->title;
            $b->author = $request->author;
            $b->description = $request->description;

            $b->group_id = $request->group_id;
            $b->save();

        }

        return $blink;
    }

    /**
     * Upload file to S3 and return relative link
     * @param String $path
     * @param mixed $file
     *
     * @return array
     *
     * 'relative' => String
     * 'temp' => String
     */
    private function uploadFile(String $path, $file)
    {
        $disk = \Storage::disk('s3');

        $extension = $file->getClientOriginalExtension();
        $originalFileName = $file->getClientOriginalName();
        $fileName = uniqid() . '_' . md5(time()) . '.' . $extension; // a unique file name

        $disk->putFileAs($path, $file, $fileName);

        $xpath = $path . '/' . $fileName;

        return [
            'relative' => $xpath,
            'temp' => $disk->temporaryUrl(
                $xpath, now()->addMinutes(360)
            )
        ];
    }

    /**
     * Delete book
     *
     * @param Request $request
     *
     * @return [type]
     */
    public function delete(Request $request) {
        $book = Book::find($request->id);


        if($book) {
            $disk = \Storage::disk('s3');

            try {
                if($book->link && $book->link != '' && $disk->exists($book->link)) {
                    $disk->delete($book->link);
                }
            } catch (\Throwable $e) {
                // League \ Flysystem \ UnableToCheckDirectoryExistence
            }

            try {
                if($book->img && $book->img != '' && $disk->exists($book->img)) {
                    $disk->delete($book->img);
                }
            } catch (\Throwable $e) {
                // League \ Flysystem \ UnableToCheckDirectoryExistence
            }



            $book->delete();
        }
    }


    /**
     * Update book and save tests
     *
     * @param Request $request
     *
     * @return String
     */
    public function update(Request $request)
    {
        $book = json_decode($request->book, true);

        $img_link = '';

        $b = Book::find($book['id']);
        if($b) {

            if($request->file('file')) {

                if($b->img != '' && $b->img != null) {
                    $disk = \Storage::disk('s3');

                    try {
                        if($disk->exists($b->img)) {
                            $disk->delete($b->img);
                        }
                    } catch (\Throwable $e) {
                        // League \ Flysystem \ UnableToCheckDirectoryExistence
                    }

                }

                $links = $this->uploadFile('/bookcovers', $request->file('file'));
                $img_link = $links['temp'];
                $b->img = $links['relative'];
            }

            $b->title = $book['title'];
            $b->author = $book['author'];
            $b->description = $book['description'];
            $b->group_id = $book['group_id'];
            $b->save();
        }

        return $img_link;


    }

    public function saveSegment(Request $request)
    {
        $bs = BookSegment::where('id', $request->item['id'])->first();
        if(!$bs) {
            $bs = BookSegment::create([
                'title' => 'test',
                'book_id' => $request->book_id,
                'page_start' => $request->item['page_start'],
                'page_end' => $request->item['page_start'],
                'pass_grade' => array_key_exists('pass_grade', $request->item) ? $request->item['pass_grade'] : 1,
            ]);
        }

        $bs->pass_grade =  array_key_exists('pass_grade', $request->item) ? $request->item['pass_grade'] : 1;
        $bs->page_start = $request->item['page_start'];
        $bs->save();

        $ids = [];
        foreach ($request->item['questions'] as $q) {
            $params = [
                'order' => 0,
                'page'=> $request->item['page_start'],
                'points'=> $q['points'],
                'testable_id'=> $bs->id,
                'testable_type'=> "App\Models\Books\BookSegment",
                'text'=> $q['text'],
                'type'=> $q['type'],
                'variants'=> $q['variants'],
            ];

            if($q['id'] != 0) {
                $testq = TestQuestion::find($q['id']);
                if($testq) $testq->update($params);
                $ids[] = $q['id'];
            } else {
                $q = TestQuestion::create($params);
                $ids[] = $q->id;
            }
        }

        return [
            'id' =>  $bs->id,
            'ids' =>  $ids
        ];

    }

    public function saveCat(Request $request) {
        $bg = BookGroup::where('id', $request->id)->first();

        if($bg) {
            $bg->name = $request->title;
            $bg->save();
        }
    }

    public function deleteSegment(Request $request)
    {
        BookSegment::where('id', $request->id)->delete();
    }

}
