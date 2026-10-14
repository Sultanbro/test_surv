<?php

namespace App\Http\Controllers\Learning\Video;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Videos\Video;
use App\Models\Videos\VideoGroup as Group;

class VideoGroupController extends Controller
{

	public function __construct()
    {
		$this->middleware('auth');
    }

	public function save(Request $request) {
		$group = Group::where('id', $request->id)->update([
			'title' => $request->title,
		]);
	}

	public function create(Request $request) {

		$group = Group::create([
			'category_id' => $request->playlist_id,
			'parent_id' => $request->parent_id,
			'title' => 'Новый отдел'
		]);

		return [
			'id' => $group->id,
			'title' => $group->title,
		];
	}
	

	/**
	 * @param Request $request
	 * 
	 * @return [type]
	 */
	public function delete(Request $request)
	{
		// fetch group_ids should delete 

		$vgroup = Group::where('id', $request->id)->with('children')->first();
		$ids = [];
		if($vgroup) {
			$ids = $this->fetchGroupIds($vgroup->children);
			$ids[] = $vgroup->id;
		}
		
		// move videos out of group
		$videos = Video::whereIn('group_id', $ids)
			->update([
				'group_id' => 0
			]);

		// delete groups
		$group = Group::whereIn('id', $ids)->delete();
	}

	/**
	 * Fetch all ids from chidren of group
	 * including group id
	 * 
	 * @param mixed $id
	 * 
	 * @return array
	 */
	private function fetchGroupIds($items)
	{
		$arr = [];
		$arr = array_merge($arr, $items->pluck('id')->toArray());

		foreach ($items as $key => $group) {
			$arr = array_merge($arr, $this->fetchGroupIds($group->children));
		}

		return $arr;
	}

}