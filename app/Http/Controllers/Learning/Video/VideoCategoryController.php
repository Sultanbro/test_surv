<?php

namespace App\Http\Controllers\Learning\Video;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Videos\VideoCategory as Category;
use Illuminate\Support\Facades\View;

class VideoCategoryController extends Controller
{

	const PAGE = '/video_categories';

	public function __construct()
    {
		$this->middleware('auth');
		View::share('menu', 'video_edit');
        View::share('link', 'video_edit');
		//$this->middleware('superuser');
    }

	public function index() {
		$categories = Category::paginate(10);
		return view('videolearning.categories.index', compact('categories')); 
	}

	public function create() {
		return view('videolearning.categories.create'); 
	}

	public function edit($id) {
		$category = Category::find($id);
		return view('videolearning.categories.edit', compact('category')); 
	}

	public function show($id) {
		return redirect(self::PAGE);
	}

	public function update(Request $request) {
		$category = Category::find($request->id);
		if($category) $category->update($request->input());
		return redirect(self::PAGE);
	}

	public function destroy($id) {
		$category = Category::find($id);
		if($category) $category->delete();
		return redirect(self::PAGE);
	}

	public function add(Request $request) {
		$cat =  Category::create([
			'title' => $request->title,
			'parent_id' => null
		]);

		$cat->playlists = [];
		return $cat; 
	}

	public function save(Request $request) {
		$cat =  Category::where([
			'id' => $request->id,
		])->first(); 

		if($cat) {
			$cat->title = $request->title;
			$cat->save();
		}
	}

	public function delete(Request $request) {
		$cat = Category::find($request->id)->delete();
	}

}