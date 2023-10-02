<?php

namespace App\Http\Controllers\Learning\Video;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TestQuestion;
use App\Models\CourseItemModel;
use App\Models\Videos\Video;
use App\Models\Videos\VideoCategory as Category;
use App\Models\Videos\VideoPlaylist as Playlist;
use Illuminate\Support\Facades\View;

class VideoPlaylistController extends Controller
{
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
        return view('admin.playlists.index');
    }

    public function saveIndex(Request $request){
        View::share('menu', 'video_edit');
        View::share('link', 'video_edit');
        return view('admin.playlists.index',[
            'category' => $request->category,
            'playlist' => $request->playlist
        ]);
    }


    public function saveIndexVideo(Request $request){
        View::share('menu', 'video_edit');
        View::share('link', 'video_edit');
        return view('admin.playlists.index',[
            'category' => $request->category,
            'playlist' => $request->playlist,
            'video' => $request->video
        ]);
    }

    public function get() {

        $categories = Category::with('playlists')->get();

        $disk = \Storage::disk('s3');

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
            ->orderBy('order', 'asc')
            ->get();

        if($no_group_videos->count() > 0) {
            $pl->groups->prepend([
                'title' => 'Без группы',
                'id' => 0,
                'videos' => $no_group_videos,
                'opened' => false,
                'children' => []
            ]);
        }

        // @TODO
        // get test results

        // cloud
        $disk = \Storage::disk('s3');

        if($pl->img != '' && $pl->img != null) {
            $pl->img = $disk->temporaryUrl(
                $pl->img, now()->addMinutes(360)
            );
        }

        $video_ids = $pl->getOrder();

        $item_models = CourseItemModel::whereIn('item_id', $video_ids)
            ->where('type', 2)
            ->where('user_id', auth()->id())
            ->where('course_item_id', $request->course_item_id)
            ->get();



        return [
            'playlist' => $pl,
            'categories' => [],//Category::all(),
            'all_videos' => [], //Video::select('id', 'title', 'links')->where('playlist_id', 0)->get(),
            'item_models' => $item_models
        ];
    }

    public function getVideo(Request $request) {

        $course_item_id = $request->course_item_id; //
        $user_id = auth()->id();

        $video =  Video::with('questions')
            ->with('questions.result', function ($query) use ($course_item_id, $user_id) {
                $query->where('course_item_model_id', $course_item_id)
                    ->where('user_id', $user_id);
            })
            ->find($request->id);

        $url = '';

        if($video->domain != 'storage.oblako.kz') {
            $url = $video->links;
        } else {
            $disk = \Storage::disk('s3');

            $url = $disk->temporaryUrl(
                $video->links, now()->addMinutes(360)
            );
        }

        $video->links = $url;

        $video->item_model = CourseItemModel::where('item_id', $video->id)
            ->where('type', 2)
            ->where('user_id', auth()->id())
            ->where('course_item_id', $course_item_id)
            ->first();

        return [
            'video' => $video,
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

    public function save_video_fast(Request $request) {

        $video = Video::find($request->id);

        if($video) {
            $video->title = $request->title;
            $video->save();
        }
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

            $disk = \Storage::disk('s3');

            try {
                if($disk->exists($video->links)){
                    $disk->delete($video->links);
                }
            } catch (\Throwable $e) {
                // League \ Flysystem \ UnableToCheckDirectoryExistence
            }


            $video->delete();
        }
    }


    public function save(Request $request) {
        $item = json_decode($request->playlist, true);



        $playlist = Playlist::find($item['id']);

        // img of playlist
        $link = '';

        $disk = \Storage::disk('s3');

        if($request->file('file')) {



            try {
                if($playlist->img && $playlist->img != '' && $disk->exists($playlist->img)) {
                    $disk->delete($playlist->img);
                }
            } catch (\Throwable $e) {
                // League \ Flysystem \ UnableToCheckDirectoryExistence
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

        $disk = \Storage::disk('s3');

        if($request->file('file')) {

            try {
                if($playlist->img && $playlist->img != '' && $disk->exists($playlist->img)) {
                    $disk->delete($playlist->img);
                }
            } catch (\Throwable $e) {
                // League \ Flysystem \ UnableToCheckDirectoryExistence
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

    public function create() {
        $categories = Category::all();
        return view('admin.playlists.create', compact('categories'));
    }

    public function edit($id) {
        $playlist = Playlist::find($id);

        return view('admin.playlists.edit')->with([
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
        if($pass_grade > count($request->questions)) $pass_grade = count($request->questions);

        Video::where('id', $request->id)->update(['pass_grade' => $pass_grade]);

        return $ids;
    }

    public function saveOrder(Request $request) {
        $item = Video::find($request->id);
        if ($item) {
            $item->order = $request->order;
            $item->save();
        }

        $videos = Video::where('group_id', $item->group_id)
            ->where('playlist_id', $item->playlist_id)
            ->where('id', '!=', $request->id)
            ->orderBy('order', 'asc')
            ->get();


        $order = 0;
        foreach ($videos as $video) {
            if($order == $request->order) {
                $order++;
            }
            $video->order = $order;
            $video->save();
            $order++;

        }

    }

    public function getPlaylistsToMove(Request $request) {
        $cats = Category::orderBy('title')->get(['id','title']);

        $all = collect([]);
        foreach ($cats as $key => $cat) {
            $playlists = Playlist::where('deleted_at', NULL)
                ->with('groups')
                ->where('category_id', $cat->id)
                ->get(['title', 'id']);


            foreach($playlists as $pl) {
                $pl->title = $cat->title . '  ->  ' . $pl->title;
                $pl->groupses = $this->extractGroups($pl->groups);

            }


            $all = $all->merge($playlists);
        }

        return $all;
    }

    private function extractGroups($groups, $prefix = '')
    {
        $arr = [];

        foreach ($groups as $key => $group) {
            $item = [];
            $arr[] = [
                'id' => $group->id,
                'title' => $prefix . ' ' . $group->title
            ];

            $arr = array_merge($arr, $this->extractGroups($group->children, $prefix . ' ' . $group->title  . '  ->  '));
        }

        return $arr;
    }

    public function moveToPlaylist(Request $request) {
        $video = Video::find($request->video_id);

        if($video) {
            $video->playlist_id = $request->playlist_id;
            $video->group_id = $request->has('group_id') ? $request->group_id : 0;
            $video->save();
        }
    }


}