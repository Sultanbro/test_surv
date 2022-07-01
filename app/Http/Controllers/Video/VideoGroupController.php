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


class VideoGroupController extends Controller {

	public function __construct()
    {
		$this->middleware('auth');
    }

	public function save(Request $request) {

		$playlist_id = $request->playlist['id'];
		$groups = $request->playlist['groups'];

		return [
			'groups' => $this->saveGroups($playlist_id, $groups)
		];
	}


	/**
	 * @param int $playlist_id
	 * @param array $groups
	 * 
	 * @return array
	 */
	private function saveGroups($playlist_id, $groups)
	{
		$ids = []; 

		// Save groups
		foreach ($groups as $key => $group) {
			
			// Main groups

			$g_input = [
				'title' => $group['title'],
				'parent_id' => 0,
				'category_id' => $playlist_id
			];

			if($group['id'] == 0) {
				$g = Group::create($g_input);
				$groups[$key]['id'] = $g->id;
				$ids[] = $g->id;
			} else {
				Group::where('id', $group['id'])->update($g_input);
				$ids[] = $group['id'];
			}
			
			// Children groups

			foreach ($group['children'] as $c_key => $child) {
				
			
				$c_input = [
					'title' => $child['title'],
					'parent_id' => $groups[$key]['id'],
					'category_id' => $playlist_id
				];
	
				if($child['id'] == 0) {
					$g = Group::create($c_input);
					$groups[$key]['children'][$c_key]['id'] = $g->id;
					$ids[] = $g->id;
				} else {
					Group::where('id', $child['id'])->update($c_input);
					$ids[] = $group['id'];
				}

			}

		}

		// delete groups not in Ids array
		$this->deleteGroups($playlist_id, $ids);
	
		// return array of groups with Ids
		return $groups;
	}

	/**
	 * delete groups not in Ids array
	 * 
	 * @param int $playlist_id
	 * @param array $ids
	 * 
	 * @return void
	 */
	private function deleteGroups($playlist_id, $ids)
	{
		// fetch group_ids should delete 
		$vgroups = Group::where('category_id', $playlist_id)->where('parent_id', 0)->get()->pluck('id')->toArray();
		$vgroups = array_values(array_diff($vgroups, $ids));
		$vgroups = Group::whereIn('id', $vgroups)->with('children')->get();
	
		$group_ids = $vgroups->pluck('id')->toArray();
		
		foreach ($vgroups as $key => $group) {

			// check children
			$group_ids = array_merge($group_ids, $group->children->pluck('id')->toArray());

			// delete group
			$group->delete();
		}

		// move videos out of group
		$videos = Video::whereIn('group_id', $group_ids)
			->update([
				'group_id' => 0
			]);
	}

}