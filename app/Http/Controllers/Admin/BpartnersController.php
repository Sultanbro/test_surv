<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Obzvon\Script;
use App\SmsDailyReport;
use App\User;
use App\GetResponceApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Book;
use App\BookCategory;
use App\UserDescription;
use App\KnowBase;

class BpartnersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        View::share('title', 'Админ панель для Bpartners.kz');
        $this->middleware('auth');
    }

    public function news(Request $request)
    {
      $news = DB::connection('bpartners_db')->select("SELECT * FROM news");
      return view('admin.bnews')->with('news', $news);
    }

    public function redirectToBpartnersBooks(Request $request) {

      $tz = 'Asia/Almaty';
      $timestamp = time();
      $dt = new \DateTime("now", new \DateTimeZone($tz)); //first argument "must" be a string
      $dt->setTimestamp($timestamp);
      $code = md5($dt->format('d.m.Y, H').'secret');
      return redirect()->to('http://bpartners.kz/books/auth.php?code='.$code);
    }

    public function newsUpdate(Request $request, $domain, $tld, $id = null)
    {


        $news_one = DB::connection('bpartners_db')->table('news')
            ->where('id', $id)
            ->first();



        if ($request->isMethod('post')) {


            if ($id) {
              DB::connection('bpartners_db')->table('news')
              ->where('id', $id)
              ->update(['title' => $request->title]);

              DB::connection('bpartners_db')->table('news')
              ->where('id', $id)
              ->update(['small_text' => $request->small_text]);

              DB::connection('bpartners_db')->table('news')
              ->where('id', $id)
              ->update(['big_text' => $request->big_text]);

              DB::connection('bpartners_db')->table('news')
              ->where('id', $id)
              ->update(['date_create' => $request->date]);

              DB::connection('bpartners_db')->table('news')
              ->where('id', $id)
              ->update(['titles' => $request->titles]);

              DB::connection('bpartners_db')->table('news')
              ->where('id', $id)
              ->update(['deskription' => $request->deskription]);

              DB::connection('bpartners_db')->table('news')
              ->where('id', $id)
              ->update(['keyword' => $request->keyword]);

                  $image = $request->file('upfile');
                  $image_name = time() . '1.' . $image->getClientOriginalExtension();
                  $image->move("bpartners", $image_name);
                  $image2 = $request->file('upfile2');
                  $image_name2 = time() . '2.' . $image2->getClientOriginalExtension();
                  $image2->move("bpartners", $image_name2);

                $postfile = array(
                  'upfile' => curl_file_create("bpartners/".$image_name,'image/png'),
                  'upfile2' => curl_file_create("bpartners/".$image_name2,'image/png'),
                );
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://bpartners.kz/who_we/reciever.php");
                curl_setopt($ch, CURLOPT_HEADER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:54.0) Gecko/20100101 Firefox/54.0");
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postfile);
                $result = curl_exec($ch);
                curl_close($ch);

                DB::connection('bpartners_db')->table('news')
                ->where('id', $id)
                ->update(['image' => $image_name]);
                DB::connection('bpartners_db')->table('news')
                ->where('id', $id)
                ->update(['image1' => $image_name2]);
            }


            return redirect('/bnews/');
        }

        return view('admin.bnews_update')->with('news_one', $news_one);

    }

    public function newsCreate(Request $request, $domain, $tld, $id = null)
    {
        if ($request->isMethod('post')) {


            if($request->hasFile('upfile') && $request->file('upfile')->isValid()) {
                $image = $request->file('upfile');
                $image_name = time() . '1.' . $image->getClientOriginalExtension();
                $image->move("bpartners", $image_name);
            }
            if($request->hasFile('upfile2') && $request->file('upfile2')->isValid()) {
                $image2 = $request->file('upfile2');
                $image_name2 = time() . '2.' . $image2->getClientOriginalExtension();
                $image2->move("bpartners", $image_name2);
            }

            $postfile = array(
              'upfile' => curl_file_create("bpartners/".$image_name,'image/png'),
              'upfile2' => curl_file_create("bpartners/".$image_name2,'image/png'),
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://bpartners.kz/who_we/reciever.php");
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:54.0) Gecko/20100101 Firefox/54.0");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postfile);
            $result = curl_exec($ch);
            curl_close($ch);







            DB::connection('bpartners_db')->table('news')->insert(
                [
                  'title' => $request->title,
                  'small_text' => $request->small_text,
                  'big_text' => $request->big_text,
                  'image' => $image_name,
                  'image1' => $image_name2,
                  'is_active' => '1'
                ]
            );



            return redirect('/bnews/');
        }

        return view('admin.bnews_create');
    }


    public function newsDelete(Request $request, $domain, $tld, $id = null)
    {

        DB::connection('bpartners_db')->table('news')->where('id', $id)->delete();

        return redirect('/bnews/');


    }

    public function corp_book(Request $request, $id = null) {
      return view('admin.corp_book.corp_book');
    }
    
    

    public function corp_book_ajax(Request $request, $id = null) {
      
      if(!Auth::user()) return;
      if($request->isMethod('get')) return;

      $corp_books = [];
      $groups = Auth::user()->inGroups();
      foreach ($groups as $group) {
        $corp_books = array_merge($corp_books, json_decode($group->corp_books));
      }
      
      $corp_books = array_unique($corp_books); // книги в группе

      $ud = UserDescription::where('user_id', Auth::user()->id)->first(); // или лично настроенные в профиле
      if($ud && count(json_decode($ud->books)) > 0) {
        $corp_books = json_decode($ud->books);
      } 

      $tree = BookCategory::whereIn('id', $corp_books)->where('is_deleted', 0)->orderBy('queue_number')->get();
      
      
      $bc = new BookCategory();
      $categories = $bc->getAll($tree);
   
      $categories_id = [];
      foreach($categories as $cat) $categories_id[] = $cat->id;

      $books = DB::connection('bpartners_db')->table('books')
        ->whereIn('category_id', $categories_id)
        ->where('is_deleted', 0)
        ->orderBy('queue_number')
        ->get();
      
      $data = [
        "tree" => $categories->toArray(),
        "books" => $books->toArray(),
        "test" => $categories_id
      ];
        
      return json_encode($data);
      
    }

   
  

    public function books(Request $request, $id = null)
    {
      View::share('menu', 'kb');
      View::share('link', 'kb');
        
      $tree = KnowBase::whereNull('parent_id')->with('children')->orderBy('order')->get()->toArray();
     
      return view('admin.books');
    }

    public function getPage(Request $request, $id = null)
    {
      $page = KnowBase::find($request->id);
      return [
        'book' => $page
      ];
    } 


    
    

    public function bookscreate(Request $request)
    {
        if ($request->isMethod('post')) {

            $categoryes = $request->categoryes;
            if (isset($categoryes['login']) && isset($categoryes['pass'])) {
              $login = $categoryes['login'];
              $pass = $categoryes['pass'];
            }
            if (isset($request->parent_cat_id)) {
              $parent_cat_id = $request->parent_cat_id;
            }
            $id = DB::connection('bpartners_db')->table('category')->insertGetId(
                [
                    'login' => isset($login) ? $login : null,
                    'password' => isset($pass) ? $pass : null,
                    'name' => $categoryes['name'],
                    'group_id' => $categoryes['group_id'],
                    'parent_cat_id' => isset($parent_cat_id) ? $parent_cat_id : null
                ]
            );


            return response()->json([
                'success' => true,
                'id' => $id
            ]);
        }
    }

    // public function booksupdate(Request $request, $id)
    // {
    //     if ($request->isMethod('post')) {

    //         $categoryes = $request->categoryes;
    //         if (isset($categoryes['login']) && isset($categoryes['pass'])) {
    //           $login = $categoryes['login'];
    //           $pass = $categoryes['pass'];
    //         }
    //         DB::connection('bpartners_db')->table('category')->where('id', $id)->update(
    //             [
    //                 'login' => isset($login) ? $login : null,
    //                 'password' => isset($pass) ? $pass : null,
    //                 'name' => $categoryes['name'],
    //                 'groupid' => $categoryes['groupid']
    //             ]
    //         );
              
    //         return response()->json([
    //             'success' => true
    //         ]);
    //     }
    // }

    public function booksdelete(Request $request) {
    	if (isset($request['id'])) {
        	$category = DB::connection('bpartners_db')->table('category')->where('id', $request['id'])->update([

				'is_deleted' => 1
        	]);
          $books = DB::connection('bpartners_db')->table('books')->where('category_id', $request['id'])->update([

        'is_deleted' => 1
          ]);
        	return response()->json([
                'success' => true
            ]);
    	}
    }

    public function orderbooks(Request $request) {
      $ids = [];
      $numbers = [];
      foreach($request->tree as $category) {
        $ids[] = $category['id'];
        $numbers[$category['id']] = $category['queue_number'];
      }
      $books = BookCategory::whereIn('id', $ids)->get();
      foreach ($books as $category) {
        foreach ($numbers as $key => $number) {
          if ($key == $category->id) {
            $category->queue_number = $number;
            $category->save();
          }
        }
      }

      return response()->json([
          'success' => true
      ]);
    }

    public function renamebooks(Request $request) {

      $category = BookCategory::find($request['id']);
      $category->name = $request['name'];
      $category->save();

      return response()->json([
          'success' => true
      ]);

    }

    public function movebooks(Request $request) {

      $category = BookCategory::find($request->id);
      $id = $category->id;
      $max_order = $category::where('parent_cat_id', $request->parent)->max('queue_number');
      $category->queue_number = $max_order + 1;
      $category->parent_cat_id = $request->parent;
      $category->save();

      return $id;

    }

    public function pagescreate(Request $request)
    {
        if ($request->isMethod('post')) {

            $book = $request->page;


            $id = DB::connection('bpartners_db')->table('books')->insertGetId(
                [
                    'title' => $book['title'],
                    'hash' => md5(uniqid().mt_rand()),
                    'description' => $book['description'],
                    'category_id' => $book['category_id']
                ]
            );


            return response()->json([
                'success' => true,
                'id' => $id
            ]);
        }
    }

    public function pagesupdate(Request $request)
    {
        if ($request->isMethod('post')) {
			if (isset($request['id'])) {

	            DB::connection('bpartners_db')->table('books')->where('id', $request['id'])->update(
	                [
	                    
	                    'description' => $request['description']
	                ]
	            );
	              
	            return response()->json([
	                'success' => true
	            ]);
			}
            
        }
    }

    public function pagesdelete(Request $request) {
    	if (isset($request['id'])) {
        $kb = KnowBase::where('id', $request->id)->first();

        if($kb) $kb->delete();
    	}

    	return response()->json([
	        'success' => true
	    ]);
    }

    public function orderpages(Request $request) {
      $ids = [];
      $numbers = [];
    	foreach($request->books as $book) {
        $ids[] = $book['id'];
        $numbers[$book['id']] = $book['queue_number'];
    	}
      $books = Book::whereIn('id', $ids)->get();
      foreach ($books as $book) {
        foreach ($numbers as $key => $number) {
          if ($key == $book->id) {
            $book->queue_number = $number;
            $book->save();
          }
        }
      }

    	return response()->json([
	        'success' => true
	    ]);
    }

    public function copypages(Request $request) {

      $book = $request->books;

      $max_order = DB::connection('bpartners_db')->table('books')->where('category_id', $book['category_id'])->max('queue_number');

      $id = DB::connection('bpartners_db')->table('books')->insertGetId(
          [
              'title' => $book['title'],
              'description' => $book['description'],
              'category_id' => $book['category_id'],
              'queue_number'    => $max_order + 1
          ]
      );
      return $id;
    }

    public function movepages(Request $request) {

      $book = Book::find($request->id);
      $id = $book->id;
      $max_order = $book::where('category_id', $request->catid)->max('queue_number');
      $book->queue_number = $max_order + 1;
      $book->category_id = $request->catid;
      $book->save();

      return $id;
    }

    public function searchpages(Request $request) {

      $ids = $request->idbooks;

      foreach ($ids as $id) {
        $book = Book::find($id);

        $book->description = $request['text'];

        $book->save();
      }

      return response()->json([
          'success' => true
      ]);
    }

    public function renamepages(Request $request) {

      $book = Book::find($request['id']);
      $book->title = $request['name'];
      $book->save();

      return response()->json([
          'success' => true
      ]);
    }

    public function password(Request $request) {
  		if (isset($request['id'])) {
  			$category = BookCategory::find($request['id']);
  			$category->login = $request['login'];
  			$category->password = $request['password'];
  			$category->save();
  			return response()->json([
  		        'success' => true
  		    ]);
  		}
    	
    	
    }

    public function uploadimages(Request $request) {
      
    		$image = $request->file('attachment');
        $ext = $image->getClientOriginalExtension();
        if($ext == '') $ext ='jpg';
        $image_name = time() . '.' . $ext;
        $image->move("bpartners", $image_name);
  		
    		// $postfile = array(
        //               'upfile' => curl_file_create("bpartners/".$image_name,'image/png')
        //             );
        //             $ch = curl_init();
        //             curl_setopt($ch, CURLOPT_URL, "https://bpartners.kz/kk/get_image.php");
        //             curl_setopt($ch, CURLOPT_HEADER, false);
        //             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //             curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:54.0) Gecko/20100101 Firefox/54.0");
        //             curl_setopt($ch, CURLOPT_POST, true);
        //             curl_setopt($ch, CURLOPT_POSTFIELDS, $postfile);
        //             $result = curl_exec($ch);
        //             curl_close($ch);
    		// // return json_encode($postfile);

    		// $id = DB::connection('bpartners_db')->table('images')->insertGetId(
        //         [
        //             'book_id' => $request['id'],
        //             'url' => 'https://bp.jobtron.org/bpartners/',
        //             'name' => $image_name,
        //         ]
        //     );


        
        return json_encode(array('location' => "/bpartners/".$image_name));

    }

    public function uploadaudio(Request $request) {
      
    		$audio = $request->file('attachment');
        $audio_name = time() . '.' . $audio->getClientOriginalExtension();
        $audio->move("bpartners/audio/", $audio_name);
  		
    		$postfile = array(
                      'upfile' => curl_file_create("bpartners/audio/".$audio_name,'mp3')
                    );
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, "https://bpartners.kz/kk/get_audio.php");
                    curl_setopt($ch, CURLOPT_HEADER, false);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:54.0) Gecko/20100101 Firefox/54.0");
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfile);
                    $result = curl_exec($ch);
                    curl_close($ch);
    		// return json_encode($postfile);
        
        return json_encode(array('location' => "/bpartners/audio/".$audio_name));

    }

    public function getBook(Request $request) {
      return json_encode([
        'book' => Book::find($request->id)
      ]);
    }
}
