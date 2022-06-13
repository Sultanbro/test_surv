<?php

namespace App\Http\Controllers;

use App\KnowBase;
use App\Models\KnowBaseModel;
use App\Models\TestQuestion;
use App\User;
use App\ProfileGroup;
use App\Position;
use DB;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class KnowBaseController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        View::share('menu', 'kb');
        View::share('link', 'kb');

        return view('admin.books');
    }

    public function get(Request $request)
    {   
        
        
                    
        if(auth()->user()->can('kb_edit')) {
            
            
            $books = KnowBase::whereNull('parent_id')
                ->orderBy('order')
                    ->whereIn('id', $this->getBooks())
                    ->get()
                    ->toArray();
            
        } else {
        
            $books = KnowBase::whereNull('parent_id')
                ->orderBy('order')
                    ->whereIn('id', $this->getBooks())
                    ->get()
                    ->toArray();
        }

       
        return [
            'books' => $books
        ];
    }

    private function getBooks() {

        $data = [];
        

        // $groups = auth()->user()->inGroups();
        // if(count($groups) > 0) {
        //     $roles = array_merge($roles, DB::table('model_has_roles')
        //         ->where('model_type', 'App\\ProfileGroup')
        //         ->whereIn('model_id', collect($groups)->pluck('id'))
        //         ->get()->pluck('role_id')->toArray());
        // }

        // KnowBaseModel::where([
        //     'model_type' => 'App\\User',
        //     'model_id' => $item['id'],
        //     'book_id' => $request->id,
        //     'access' => 2;
        // ]);
        
        // if($pos) $roles = array_merge($roles, DB::table('model_has_roles')
        //             ->where('model_type', 'App\\Position')
        //             ->where('model_id', $pos->id)
        //             ->get()->pluck('role_id')->toArray());

        if(auth()->user()->is_admin == 1)  {
            $books = KnowBase::get('id')->pluck('id')->toArray();
            
        } else {
            $books = KnowBaseModel::where([
                'model_type' => 'App\\User',
                'model_id' => auth()->id(),
                'access' => 1
            ])->get('book_id')->pluck('book_id')->toArray();
        }

        
            
        return $books;
    }

    public function search(Request $request)
    {

        if(!auth()->user()->can('kb_edit')) {
            return [
                'items' => [],
            ];
        }

        $phrase = '%' . $request->text . '%';
        $items = KnowBase::where('title', 'like', $phrase)
            ->orWhere('text', 'like', $phrase)
            ->orderBy('order')
            ->get();

        foreach ($items as $key => $item) {
            $item->text = $this->cutFragment($item->text, $request->text);
        }

        return [
            'items' => $items,
        ];
    }

    private function cutFragment($text, $fragment)
    {
        $plainText = strip_tags($text);

        $start_pos = mb_stripos($plainText, $fragment);
        $start_pos -= 15;
        if ($start_pos < 0) {
            $start_pos = 0;
        }

        return '...' . mb_substr($plainText, $start_pos, 50);
    }

    public function getArchived(Request $request)
    {
        if(auth()->user()->can('kb_edit')) {
            $books = KnowBase::onlyTrashed()->whereNull('parent_id')->orderBy('order')->get()->toArray();
        } else {
            $books = [];
        }

        return [
            'books' => $books,
        ];
    }

    public function getTree(Request $request)
    {
        $arr = $this->getBooks();

        if(in_array($request->id, $arr)) {
            $trees = KnowBase::where('parent_id', $request->id)
         
            ->with('children')
            ->orderBy('order')->get();

            foreach ($trees as $tree) {
                $tree->parent_id = null;
            }

            $trees->toArray();
        
            $book = KnowBase::whereNull('parent_id')
                ->orderBy('order')
                    ->whereIn('id', $this->getBooks())
                    ->where('id', $request->id)
                    ->first();
        }  else {
            $trees = [];
            $book = null;
        }
       

    
        

        return [
            'trees' => $trees,
            'book' => $book,
        ];
    }

    public function getPage(Request $request)
    {
        $arr = $this->getBooks();
        
        $page = KnowBase::withTrashed()->find($request->id);

        $user = User::withTrashed()->find($page->user_id);
        $editor = User::withTrashed()->find($page->editor_id);

        $page->author = $user ? $user->last_name . ' ' . $user->name : 'Неизвестный';
        $page->editor = $editor ? $editor->last_name . ' ' . $editor->name : 'Неизвестный';
        $page->edited_at = Carbon::parse($page->updated_at)->setTimezone('Asia/Almaty')->format('d.m.Y H:i');
        $page->created = Carbon::parse($page->created_at)->setTimezone('Asia/Almaty')->format('d.m.Y H:i');

        $page->questions = TestQuestion::where('testable_type', 'App\Knowbase')->where('testable_id', $request->id)->get();
        $breadcrumbs = $this->getBreadcrumbs($page);

        $trees = [];
        $top_parent = [];
        $top_parent = $this->getTopParent($request->id);
        
        if ($request->refresh) {

            
            if ($top_parent) {
                $trees = KnowBase::where('parent_id', $top_parent->id)->with('children')->orderBy('order')->get();
                foreach ($trees as $tree) {
                    $tree->parent_id = null;
                }
                $trees = $trees->toArray();
            }

        }
        
        $data = [
            'book' => $page,
            'breadcrumbs' => $breadcrumbs,
            'tree' => $trees,
            'top_parent' => $top_parent,
        ];


        if($top_parent == null && !in_array($top_parent->id, $arr)) {
            $data = [
                'book' => null,
                'breadcrumbs' => [],
                'tree' => [],
                'top_parent' => null,
            ];
        }

        return $data;
    }

    private function getTopParent($id)
    {
        $kb = KnowBase::withTrashed()->find($id);
        if ($kb && $kb->parent_id != null) {
            return $this->getTopParent($kb->parent_id);
        }
        return $kb;
    }

    private function getBreadcrumbs($page)
    {
        $breadcrumbs = [];
        $breadcrumbs[] = $page;
        $parent_id = $page->parent_id;

        while ($parent_id != null) {
            $xpage = KnowBase::withTrashed()->find($parent_id);
            if ($xpage) {
                $breadcrumbs[] = $xpage;
                $parent_id = $xpage->parent_id;
            } else {
                $parent_id = null;
            }
        }

        return array_reverse($breadcrumbs);
    }

    public function updateSection(Request $request)
    {
        $page = KnowBase::find($request->id);
        if ($page) {
            $page->title = $request->title;
            $page->editor_id = Auth::user()->id;
            $page->save();

            
            KnowBaseModel::where('book_id', $request->id)->delete();

            foreach ($request['who_can_read'] as $key => $item) {
                if($item['type'] == 1) $model = 'App\\User';
                if($item['type'] == 2) $model = 'App\\ProfileGroup';
                if($item['type'] == 3) $model = 'App\\Position';

                KnowBaseModel::create([
                    'model_type' => $model,
                    'model_id' => $item['id'],
                    'book_id' => $request->id,
                    'access' => 1
                ]);
            }

            foreach ($request['who_can_edit'] as $key => $item) {
                if($item['type'] == 1) $model = 'App\\User';
                if($item['type'] == 2) $model = 'App\\ProfileGroup';
                if($item['type'] == 3) $model = 'App\\Position';

                KnowBaseModel::create([
                    'model_type' => $model,
                    'model_id' => $item['id'],
                    'book_id' => $request->id,
                    'access' => 2 
                ]);
            }

        }

    }

    public function updatePage(Request $request, $id = null)
    {
        $page = KnowBase::find($request->id);
        if ($page) {
            $page->text = $request->text ?? '';
            $page->title = $request->title ?? 'Без названия';
            $page->editor_id = Auth::user()->id;
            $page->save();
        }

    }

    public function saveOrder(Request $request, $id = null)
    {

        $page = KnowBase::find($request->id);
        if ($page) {
            $page->parent_id = $request->parent_id;
            $page->order = $request->order;
            $page->save();
        }

        $pages = KnowBase::where('parent_id', $request->parent_id)
            ->where('id', '!=', $request->id)
            ->orderBy('order', 'asc')
            ->get();

        $order = 0;
        foreach ($pages as $page) {
            if($order == $request->order) {
                $order++;
            } 
            $page->order = $order;
                $page->save();
            $order++;
         
        }

    }

    public function createPage(Request $request)
    {
        $kb = KnowBase::where('parent_id', $request->id)->orderBy('order', 'desc')->first();
        $order = $kb ? $kb->order + 1 : 0;

        $kb = KnowBase::create([
            'title' => 'Новая страница',
            'text' => '',
            'user_id' => Auth::user()->id,
            'editor_id' => Auth::user()->id,
            'order' => $order,
            'parent_id' => $request->id,
            'hash' => md5(uniqid() . mt_rand()),
            'is_deleted' => 0,
        ]);

        $kb->children = [];
        $kb->parent_id = null;
        return $kb;

    }

    public function addSection(Request $request)
    {
        $kb = KnowBase::whereNull('parent_id')->orderBy('order', 'desc')->first();

        return KnowBase::create([
            'title' => $request->name,
            'text' => '',
            'user_id' => Auth::user()->id,
            'editor_id' => Auth::user()->id,
            'order' => $kb ? $kb->order + 1 : 0,
            'parent_id' => null,
            'hash' => 'cat',
            'is_deleted' => 0,
        ]);

    }

    public function deleteSection(Request $request)
    {
        $kb = KnowBase::find($request->id);
        if ($kb) {
            $kb->delete();
        }

    }

    public function deletePage(Request $request)
    {
        $kb = KnowBase::find($request->id);
        if ($kb) {
            $kb->delete();
        }

    }

    public function restoreSection(Request $request)
    {
        $kb = KnowBase::onlyTrashed()->find($request->id);
        if ($kb) {
            $kb->restore();
        }

    }

    public function saveTest(Request $request)
    {
        foreach ($request->questions as $key => $q) {
            $params = [
                'order' => 0,
                'page' => 0,
                'points' => $q['points'],
                'testable_id' => $request->id,
                'testable_type' => "App\Knowbase",
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


    public function getAccess(Request $request) {

        $read = KnowBaseModel::where([
            'book_id' => $request->id,
        ])->get();

        $edit = KnowBaseModel::where([
            'book_id' => $request->id,
        ])->get();

        $who_can_read = [];
        $who_can_edit = [];
        foreach ($read as $key => $item) {
            $arr = [];
            $arr['id'] = $item['model_id'];

            if($item->model_type == 'App\\User') {
                $arr['type'] = 1;
                $user = User::withTrashed()->find($item->model_id);
                if(!$user) continue;
                $arr['name'] = $user->last_name . ' ' . $user->name;
            }

            if($item->model_type == 'App\\ProfileGroup') {
                $arr['type'] = 2;
                $group = ProfileGroup::find($item->model_id);
                if(!$group) continue;
                $arr['name'] = $group->name;
            }

            if($item->model_type == 'App\\Position') {
                $arr['type'] = 3;
                $pos = Position::find($item->model_id);
                if(!$pos) continue;
                $arr['name'] = $pos->position;
            }

            if($item->access == 1) $who_can_read[] = $arr;
            if($item->access == 2) $who_can_edit[] = $arr;
        }
        
        return [
            'who_can_edit' => $who_can_edit,
            'who_can_read' => $who_can_read,
        ];
    }

}
