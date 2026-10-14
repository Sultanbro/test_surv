<?php

namespace App\Http\Controllers\Learning\Video;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\User;
use App\Models\Videos\Video;
use App\Models\TestQuestion;
use App\Models\Videos\VideoComment as Comment;
use App\Models\Videos\VideoPlaylist as Playlist;
use App\Models\Videos\VideoGroup as Group;
use Illuminate\Support\Facades\View;
use Session;

class VideoController extends Controller
{

	const PAGE = '/videos';
	const SERVER = '185.48.149.174';
	const PORT = 50021;

	public function __construct()
    {
       $this->middleware('auth');
	   View::share('menu', 'video_edit');
       View::share('link', 'video_edit');
       //$this->middleware('superuser');
    }

	public function index(Request $request) {
		$videos = Video::orderBy('id', 'desc');

		if($request->pl) {
			$videos->where('playlist_id', $request->pl);
		}
		
		
		$videos = $videos->paginate(10);

		$playlists = Playlist::orderBy('id', 'desc')->get();

		return view('videolearning.videos.index', compact('videos', 'playlists')); 
	}

	public function show($id) {
		return redirect(self::PAGE);
	}

	public function create() {
		Session::put('upload_progress', 0);
		$playlists = Playlist::orderBy('title', 'asc')->get();
		$groups = Group::orderBy('title', 'asc')->get();

		// temp
		//$groups = Group::where('parent_id', 48)->get(); 


		// 7 - 48
		// 8 - 59
		// 9 - 64
		// 10 - 75
		// 11 - 79
		// 12 - 80
		// 13 - 81
		return view('videolearning.videos.create', compact('playlists', 'groups')); 
	}

	public function edit($id) {
		Session::put('upload_progress', 0);
		$video = Video::find($id);
		$playlists = Playlist::orderBy('title', 'asc')->get();
		$groups = Group::orderBy('title', 'asc')->get();
		return view('videolearning.videos.edit', compact('video', 'playlists', 'groups')); 
	}

	public function update(Request $request) {
		$video = Video::find($request->id);
		if($video) $video->update($request->input());

		
		return redirect(self::PAGE);
	}

	public function updateVideo(Request $request)
    {
		$video = Video::find($request->video['id']);
      	foreach ($request->video['questions'] as $key => $q) {
			$params = [
				'order' => 0,
				'page'=> 0,
				'points'=> $q['points'],
				'testable_id'=> $request->video['id'],
				'testable_type'=> "App\Models\Videos\Video",
				'text'=> $q['text'],
				'type'=> $q['type'],
				'variants'=> $q['variants'],
			];

			if($q['id'] != 0) {
				$testq = TestQuestion::find($q['id']);
				if($testq) $testq->update($params);
			} else {
				TestQuestion::create($params);
			}
		}
    }
	
	public function upload(Request $request) {
		
		if ($request->file('file')->isValid()) {
			$file = $request->file('file');
			
			$file_name = Auth::user()->id . '_' . time() . '.' . $file->getClientOriginalExtension();

			//$file_path = '/uploads/video/' . $request->folder . '/' . $file_name;
			$file_path = '/uploads/video/' . $file_name;
			
			//$this->ftp_mkdir($request->folder);
			$ftp_upload = $this->ftp_upload($file_path, $file->path(), $file->getSize());
			
			$duration = 30;
			
			return [
				'file' => 'https://'.tenant('id').'.jobtron.org/sharedvideos/' . $file_name,
				'ftp_status' => $ftp_upload,
			];
		}
	}

	public function destroy($id) {
		$video = Video::find($id);
		if($video) $video->delete();
		return redirect(self::PAGE);
	}

	public function store(Request $request) {
	
		Video::create($request->input());
		return redirect(self::PAGE);
	}

	private function ftp_upload(String $remote_file, String $local_file, $size, $type = FTP_BINARY) {

		$conn_id = ftp_connect(self::SERVER, self::PORT, 900);
		
		
		if($conn_id){

			//dd(ftp_get_option ($conn_id, FTP_TIMEOUT_SEC));
			$login_result = ftp_login($conn_id, 'anonymous', '');
			ftp_pasv($conn_id, true);
			
			$uploaded = true;
			$d = ftp_nb_put($conn_id, $remote_file, $local_file, FTP_BINARY);

			
			
			while($d == FTP_MOREDATA)
			{
			// do whatever you want
			
				//$last = Session::get('upload_progress');
				$last = session('upload_progress');
				session(['upload_progress' => $last + 4]);
				//Session::set('upload_progress', $last + 4);
				$d = ftp_nb_continue($conn_id);
				//echo 'hey';
			}

			if ($d != FTP_FINISHED)
			{
				//echo "Error uploading $local_file";
				$uploaded = false;
			}
			
			// close connection
			ftp_close($conn_id);

			// if (ftp_put($conn_id, $remote_file, $local_file, FTP_BINARY)) {
			// 	$uploaded =  true;
			// } else {
			// 	$uploaded = false;
			// }
			
			// ftp_close($conn_id);
			return $uploaded;
		}

		
	}	 

	public function get_comment(Request $request) {
		return $this->getComments($request->id);
	}

	public function add_comment(Request $request) {
		$user = Auth::user();
		if($user) {
			Comment::create([
				'video_id' => $request->id,
				'user_id' => $user->id,
				'text' => $request->text,
			]);
		}	
		
		return $this->getComments($request->id);
	}


	private function getComments($id) {
		$v = Video::find($id);
		
		$comments = Comment::where('video_id', $id)->orderBy('created_at', 'desc')->get();
		
		$users = User::withTrashed()->get();

		foreach($comments as $comment) {
			$_user = $users->where('id', $comment->user_id)->first();
			$comment->user = $_user->name . ' ' . $_user->last_name;
		}
		
		if($v) {
			return $comments;
		} else {
			return [];
		}
	}

	private function ftp_mkdir(String $folder) {
		$conn_id = ftp_connect(self::SERVER, self::PORT, 900);
		
		if($conn_id){
			$login_result = ftp_login($conn_id, 'anonymous', '');
			ftp_pasv($conn_id, true);

			ftp_mkdir($conn_id, $folder);
		}

		ftp_close($conn_id);
	}
	
	public function upload_progress(Request $request)
	{
		return json_encode(session('upload_progress'));
	}
}