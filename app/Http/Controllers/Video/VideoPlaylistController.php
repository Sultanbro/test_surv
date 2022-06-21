<?php

namespace App\Http\Controllers\Video;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Auth;
use App\User;
use App\Models\TestQuestion;
use App\Models\Videos\Video;
use App\Models\Videos\VideoCategory as Category;
use App\Models\Videos\VideoComment as Comment;
use App\Models\Videos\VideoPlaylist as Playlist;
use App\Models\Videos\VideoGroup as Group;
use Illuminate\Support\Facades\View;


// $x, $y => побочные переменные в роуте указаны как admin.{domain}.{tld}
class VideoPlaylistController extends Controller {

	const PAGE = '/video_playlists';

	public function __construct()
    {
		$this->middleware('auth');
		View::share('menu', 'video_edit');
        View::share('link', 'video_edit');
		//$this->middleware('superuser');
    }

	public function index() {
		View::share('menu', 'video_edit');
        View::share('link', 'video_edit');
		return view('videolearning.playlists.index'); 
	}

	public function savedIndex(Request $request){
		View::share('menu', 'video_edit');
        View::share('link', 'video_edit');
		return view('videolearning.playlists.index',[ 
			'category' => $request->category,
			'playlist' => $request->playlist
		]); 
	}


	public function savedIndexVideo(Request $request){
		View::share('menu', 'video_edit');
        View::share('link', 'video_edit');
		return view('videolearning.playlists.index',[ 
			'category' => $request->category,
			'playlist' => $request->playlist,
			'video' => $request->video
		]); 
	}

	public function get() {
		return [
			'user_id' => auth()->user()->id,
			'categories' => Category::with('playlists')->get()
		];
	}

	public function deleteQuestion(Request $request){
		$question = TestQuestion::find($request->id);
		$question->delete();
	}

	public function getPlaylist(Request $request) {

		$pl =  Playlist::with('videos')->find($request->id);

		foreach($pl->videos as $video) {
			$video->questions = TestQuestion::where('testable_type', 'App\Models\Videos\Video')
				->where('testable_id', $video->id)
				->get();
		}
		return [
			'playlist' => $pl,
			'categories' => Category::all(),
			'all_videos' => Video::select('id', 'title', 'links')->where('playlist_id', 0)->get(),
		];
	}

	public function add_video(Request $request) {
		$video = Video::find($request->video_id);

		$was_in_playlist = false;
		if($video) {
			if($video->playlist_id == $request->id) $was_in_playlist = true;
			$video->playlist_id = $request->id;
			$video->save();
		}

		return [
			'video' => $video,
			'was_in_playlist' => $was_in_playlist
		]; 
	}	


	public function add(Request $request) {
		$pl = Playlist::create([
			'title' =>$request->title, 
			'category_id' => $request->cat_id,
			'text' => ' ',
		]);
		
		return $pl;
	}	

	public function save_video(Request $request) {


		$video = Video::find($request->video['id']);

		if($video) {
			$video->playlist_id = $request->id;
			$video->title = $request['video']['title'];
			$video->save();
		}
		
		return [
			'video' => $video,
		]; 
		
	}	

	public function remove_video(Request $request) {
		$video = Video::find($request->id);
		$video->playlist_id = 0;
		$video->save();
	}

	public function delete_video(Request $request) {
		$video = Video::find($request->id);
		if($video) {
			\Storage::delete('public/' . $video->links);
			$video->delete();
		}
	}
	

	public function save(Request $request) {
		$item = $request->playlist;
		$videos = $request->playlist['videos'];

		foreach ($videos as $index => $video) {
			$vid = Video::find($video['id']);
			if($vid) {
				$vid->order = $index;
				$vid->save;
			}
		}

		$playlist = Playlist::find($item['id']);

		$playlist->title = $item['title'];
		$playlist->category_id = $item['category_id'];
		$playlist->text = $item['text'];
		$playlist->save();
	}

	public function create() {
		$categories = Category::all();
		return view('videolearning.playlists.create', compact('categories')); 
	}

	public function edit($id) {
		$playlist = Playlist::find($id);
		
		return view('videolearning.playlists.edit')->with([
			'playlist_id' => $id
		]); 
	}

	public function show($id) {
		return redirect(self::PAGE);
	}

	public function update(Request $request) {
		$playlist = Playlist::find($request->id);
		if($playlist) $playlist->update($request->input());
		return redirect(self::PAGE);
	}

	public function destroy($id) {
		$playlist = Playlist::find($id);
		if($playlist) $playlist->delete();
		return redirect(self::PAGE);
	}

	public function delete(Request $request) {
		$playlist = Playlist::find($request->id);
		if($playlist) $playlist->delete();
	}

	public function store(Request $request) {
		Playlist::create($request->input());
		return redirect(self::PAGE);
	}

	public function saveTest(Request $request)
    {
        foreach ($request->questions as $key => $q) {
            $params = [
                'order' => 0,
                'page' => 0,
                'points' => $q['points'],
                'testable_id' => $request->id,
                'testable_type' => "App\Models\Videos\Video",
                'text' => $q['text'],
                'type' => $q['type'],
                'variants' => $q['variants'],
            ];

            if ($q['id'] != 0) {
                $testq = TestQuestion::find($q['id']);
                if ($testq) {
                    $testq->update($params);
                }

            } else {
                TestQuestion::create($params);
            }
        }
    }
	
}