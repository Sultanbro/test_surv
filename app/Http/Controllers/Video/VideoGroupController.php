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
class VideoGroupController extends Controller {

	const PAGE = '/video_groups';

	public function __construct()
    {
		$this->middleware('auth');
		View::share('menu', 'video_editor');
		//$this->middleware('superuser');
    }

	public function index() {
		$groups = Group::paginate(10);
		return view('videolearning.groups.index', compact('groups')); 
	}

	public function create() {
		$playlists = Playlist::orderBy('title', 'asc')->get();
		$groups = Group::where('parent_id', null)->get();
		$groups2 = Group::where('parent_id', 0)->get();
		$groups = $groups->merge($groups2);
		
		return view('videolearning.groups.create', compact('playlists', 'groups')); 
	}

	public function edit($id) {
		$group = Group::find($id);
		$playlists = Playlist::all();
		$groups = Group::where('parent_id', null)->get();
		$groups2 = Group::where('parent_id', 0)->get();
		$groups = $groups->merge($groups2);
		return view('videolearning.groups.edit', compact('group', 'playlists', 'groups')); 
	}

	public function show($id) {
		return redirect(self::PAGE);
	}

	public function update(Request $request) {
		
		$group = Group::find($request->id);
		
		if($group) $group->update([
			'title' => $request->title,
			'parent_id' => $request->parent_id,
			'category_id' => $request->category_id,
		]);
		return redirect(self::PAGE);
	}

	public function destroy($id) {
		$group = Group::find($id);
		if($group) $group->delete();
		return redirect(self::PAGE);
	}

	public function store(Request $request) {
		Group::create($request->input());
		return redirect(self::PAGE);
	}

}