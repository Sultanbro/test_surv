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

	public function saveIndex(Request $request){
		View::share('menu', 'video_edit');
        View::share('link', 'video_edit');
		return view('videolearning.playlists.index',[ 
			'category' => $request->category,
			'playlist' => $request->playlist
		]); 
	}


	public function saveIndexVideo(Request $request){
		View::share('menu', 'video_edit');
        View::share('link', 'video_edit');
		return view('videolearning.playlists.index',[ 
			'category' => $request->category,
			'playlist' => $request->playlist,
			'video' => $request->video
		]); 
	}

	public function get() {

		$categories = Category::with('playlists')->get();

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

		foreach ($categories as $key => $cat) {
			foreach ($cat->playlists as  $playlist) {
				if($playlist->img != '' && $playlist->img != null) {
					$playlist->img = $disk->temporaryUrl(
						$playlist->img, now()->addMinutes(360)
					);
				}   
			}
		}

		return [
			'user_id' => auth()->user()->id,
			'categories' => $categories
		];
	}

	public function deleteQuestion(Request $request){
		$question = TestQuestion::find($request->id);
		$question->delete();
	}

	public function getPlaylist(Request $request) {

		$pl =  Playlist::with('groups')->find($request->id);

		$user_id = auth()->id();
	
		$no_group_videos = Video::where('group_id', 0)
			->where('playlist_id', $pl->id)
			->with('questions')
			->with('item_model', function ($query) use ($user_id){
				$query->where('type', 2)
					->where('user_id', $user_id);
			})->get();

		if($no_group_videos->count() > 0) {
			$pl->groups->prepend([
				'title' => 'Без группы',
				'id' => 0,
				'videos' => $no_group_videos,
				'opened' => false,
				'children' => []
			]);
		}

		foreach($pl->videos as $video) {
			$video->questions = TestQuestion::where('testable_type', 'App\Models\Videos\Video')
				->where('testable_id', $video->id)
				->get();
		}

		//
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

		if($pl->img != '' && $pl->img != null) {
			$pl->img = $disk->temporaryUrl(
				$pl->img, now()->addMinutes(360)
			);
		}   

		return [
			'playlist' => $pl,
			'categories' => [],//Category::all(),
			'all_videos' => [] //Video::select('id', 'title', 'links')->where('playlist_id', 0)->get(),
		];
	}

	public function getVideo(Request $request) {

		$video =  Video::find($request->id);

		$url = '';

		if($video) {

			if($video->domain != 'storage.oblako.kz') {
				$url = $video->links;
			} else {
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
	
				$url = $disk->temporaryUrl(
					$video->links, now()->addMinutes(360)
				);
			}

		}
	

		return [
			'links' => $url,
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

		$video = Video::with('questions')->find($request->video['id']);

		if($video) {
			$video->playlist_id = $request->id;
			$video->title = $request['video']['title'];
			$video->group_id = $request['group_id'];
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

			$video->playlist_id = 0;

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

			if($disk->exists($video->links)){
				$disk->delete($video->links);
			}
			
			$video->delete();
		}
	}
	

	public function save(Request $request) {
		$item = json_decode($request->playlist, true);
		$videos = $item['videos'];

		foreach ($videos as $index => $video) {
			$vid = Video::find($video['id']);
			if($vid) {
				$vid->order = $index;
				$vid->save;
			}
		}

		
		
		$playlist = Playlist::find($item['id']);

		// img of playlist
		$link = '';

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

		if($request->file('file')) {

			if($playlist->img && $playlist->img != '' && $disk->exists($playlist->img)) {
                $disk->delete($playlist->img);
            }
			
			$links = $this->uploadFile('/pl', $request->file('file')); 
			$link = $links['temp'];
			$playlist->img = $links['relative'];
		}

		$playlist->title = $item['title'];
		$playlist->category_id = $item['category_id'];
		$playlist->text = $item['text'];
		$playlist->save();

		return $link;
	}

	public function saveFast(Request $request)
	{
		$item = json_decode($request->playlist, true);
		
		$playlist = Playlist::find($item['id']);

		$link = '';

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

		if($request->file('file')) {

			if($playlist->img && $playlist->img != '' && $disk->exists($playlist->img)) {
                $disk->delete($playlist->img);
            }
			
			$links = $this->uploadFile('/pl', $request->file('file')); 
			$link = $links['temp'];
			$playlist->img = $links['relative'];
		}


		$playlist->title = $item['title'];
		$playlist->category_id = $item['category_id'];
		$playlist->text = $item['text'];
		$playlist->save();


		return $link;
		
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
		$ids = [];
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
				$ids[] = $testq['id'];
            } else {
                $q = TestQuestion::create($params);
				$ids[] = $q->id; 
            }
        }

		// count pass grade
		$pass_grade = $request->pass_grade;
		$min = count($request->questions) != 0 ? 100 / count($request->questions) : 100;
		if($pass_grade < $min) $pass_grade = floor($min);

		Video::where('id', $request->id)->update(['pass_grade' => $pass_grade]);
		
		return $ids; 
    }
	
}