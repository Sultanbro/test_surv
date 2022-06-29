<?php

namespace App\Http\Controllers\Video;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Auth;
use App\User;
use App\Models\Videos\Video;
use App\Models\Videos\VideoCategory as Category;
use App\Models\Videos\VideoComment as Comment;
use App\Models\Videos\VideoPlaylist as Playlist;
use App\Models\Videos\VideoGroup as Group;
use Illuminate\Support\Facades\View;

// $x, $y => побочные переменные в роуте указаны как admin.{domain}.{tld}
class VideolearningController extends Controller {

	public function __construct()
    {
		View::share('menu', 'learning');
		View::share('link', 'videolearning');
       // $this->middleware('auth');
    }

	public function playlist($id) {

		if(is_null(Auth::user()) && session('key') != 'cooper') return view('videolearning.login');

		$playlist = Playlist::find($id);
		
		if(!$playlist) return redirect('/videolearning');

		$video = Video::where('playlist_id', $playlist->id)->orderBy('id', 'asc')->first();
		if($video) {
			$video->views = $video->views + 1;
			$video->save();
			$video->group_info = $this->group_info($video);
		}
		

		//////////////
			$cats = Category::all();

			foreach($cats as $cat) {
				if($cat->id == $playlist->id) {
					$cat->active = 'active';
				} else {
					$cat->active = '';
				}
			}

			$videos = Video::where('playlist_id', $playlist->id)->get();
			
		///////////
			$_groups = [];

			foreach($videos as $vid) {
				array_push($_groups, $vid->group_id);
			}	
			
			$_groups = array_unique($_groups);
			
		/////////
			$groups = $playlist->groups->where('parent_id', null);
			$groups2 = $playlist->groups->where('parent_id', 0);
			$groups = $groups->merge($groups2);
			
			$subgroups = Group::whereIn('id', $_groups)->where('parent_id', '!=', null)->where('parent_id', '!=', 0)->get();
			
			foreach($groups as $group) {
				$sgs = $subgroups->where('parent_id', $group->id);

				foreach($sgs as $subgroup) {
					$subgroup->videos = $videos->where('group_id', $subgroup->id);
					$subgroup->active = false;
				}

				$group->groups = $sgs;
				$group->videos = $videos->where('group_id', $group->id);
				$group->active = ($video && $video->group_id == $group->id) ? true : false;
			}
			
			if($videos->where('group_id', 0)->count() > 0){
				$wgroup = new Group;
				$wgroup->title = 'Без категории';
				$wgroup->active = false;
				$wgroup->groups = collect();
				$wgroup->videos = $videos->where('group_id', 0);
				$groups->push($wgroup);
			}
		//////////////////
			$videos = $this->moderate($videos);
			
			//dd($groups);
		///////////////
		$backlink = '/videolearning/' .$playlist->category->id;
				
		
		return view('videolearning.video', compact('playlist', 'cats', 'video','backlink', 'videos', 'groups')); 
	}

	public function admin() {
		if(!in_array(Auth::user()->id, [5, 18])) return redirect('/videolearning');
		
		$videos = Video::paginate(10);

		return view('videolearning.admin.index', compact('videos')); 
	}

	public function views(Request $request) {
		$video = Video::find($request->id);
		$video->views = $video->views + 1;
		$video->save();
		return $video->views . ' просмотров'; 
	}

	private function group_info(Video $video)
	{
		$group_info = '';
		if($video->group) {
			if($video->group->parent) {
				$group_info = $video->group->parent->title .' : '. $video->group->title;
			} else {
				$group_info = $video->group->title;
			}
		} 
		return $group_info;
	}

	private function moderate($videos) {
	
		foreach($videos as $key => $v) {
			$v->duration = gmdate("H:i:s", $v->duration);
			$v->group_info = $this->group_info($v);
			if($key != 0 && $videos[$key - 1]) $videos[$key - 1]->next = $v->id;
		}
		return $videos;
	}
}