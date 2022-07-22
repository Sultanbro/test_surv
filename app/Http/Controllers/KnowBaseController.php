<?php

namespace App\Http\Controllers;

use App\KnowBase;
use App\Models\KnowBaseModel;
use App\Models\TestQuestion;
use App\User;
use App\ProfileGroup;
use App\Position;
use App\Models\CourseItemModel;
use DB;
use Auth;
use App\Setting;
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

        $books = KnowBase::whereNull('parent_id')
                ->orderBy('order');
        
        if(!auth()->user()->can('kb_edit')) $books->whereIn('id', $this->getBooks());
        
        $books = $books->get();

        return [
            'books' => $books->toArray()
        ];
    }

    private function getBooks($access = 0) {
        
        $books = [];
        if(auth()->user()->is_admin == 1)  {
            $books = KnowBase::whereNull('parent_id')->get('id')->pluck('id')->toArray();
        } else {

            $groups = auth()->user()->inGroups();
            $group_ids = collect($groups)->pluck('id')->toArray();
            $position_id =  auth()->user()->position_id;
            $user_id =  auth()->id();

            $up = KnowBaseModel::
                where(function($query) use ($group_ids, $access) {
                    $query->where('model_type', 'App\\ProfileGroup')
                        ->whereIn('model_id', $group_ids);
                    if($access == 2) $query->where('access', 2);
                })
                ->orWhere(function($query) use ($position_id, $access) {
                    $query->where('model_type', 'App\\Position')
                        ->where('model_id', $position_id);
                    if($access == 2) $query->where('access', 2);
                })
                ->orWhere(function($query) use ($user_id, $access) {
                    $query->where('model_type', 'App\\User')
                        ->where('model_id', $user_id);
                    if($access == 2) $query->where('access', 2);
                });

            $up = $up->get('book_id')
                ->pluck('book_id')
                ->toArray();
               
            $books = array_merge($books, $up);
            
           
            $books_with_read_access =  KnowBase::withTrashed()
                ->whereNull('parent_id')
                ->whereIn('access', $access == 2 ? [2] : [1,2])
                ->get('id')->pluck('id')
                ->toArray();
                
            $books = array_merge($books, $books_with_read_access);
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
            ->limit(10)
            ->get();

    
           

        foreach ($items as $key => $item) {
            $item->text = $this->cutFragment($item->text, $request->text);
            $item->book = $this->getTopParent($item->id);
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

        return '...' . mb_substr($plainText, $start_pos, 50) . '...';
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
        
        $trees = [];
        $book = null;

        $can_read = false;
        if(auth()->user()->can('kb_edit')) {
            $can_read = true;
        } else if(in_array($request->id, $this->getBooks())) {
            $can_read = true;  
        } 

        

        if($can_read || ($request->has('can_read') && $request->can_read)) {
            $trees = KnowBase::where('parent_id', $request->id)
                ->with('children')
                ->with('questions')
                ->orderBy('order')
                ->get();

            foreach ($trees as $tree) {
                $tree->parent_id = null;
            }

            $trees->toArray();
        
            $book = KnowBase::whereNull('parent_id')
                ->orderBy('order')
                ->where('id', $request->id)
                ->first();

       
           if($book) $book->access = in_array($book->id, $this->getBooks(2)) ? 2 : 1;
            
        }
        
        $kb_ids = $book->getOrder();

        $item_models = CourseItemModel::whereIn('item_id', $kb_ids)
            ->where('type', 3)
            ->where('user_id', auth()->id())
            ->where('course_item_id', 0)
            ->get();
        
        return [
            'trees' => $trees,
            'book' => $book,
            'item_models' => $item_models,
            'can_save' => $this->canSaveWithoutTest()
        ];
    }

    public function getPage(Request $request)
    {
        $course_item_model_id = 0;
        $user_id = auth()->id();

        $page = KnowBase::withTrashed()
            //->with('questions')
			->with('questions.result', function ($query) use ($course_item_model_id, $user_id) {
				$query->where('course_item_model_id', $course_item_model_id)
					->where('user_id', $user_id);
			})
            ->find($request->id);

        $page->item_model = CourseItemModel::where('user_id', $user_id)
            ->where('type', 3)
            ->where('item_id', $page->id)
            ->where('course_item_id', 0)
            ->first();
            
        $author = User::withTrashed()->find($page->user_id);
        $editor = User::withTrashed()->find($page->editor_id);

        $page->author = $author ? $author->last_name . ' ' . $author->name : 'Неизвестный';
        $page->editor = $editor ? $editor->last_name . ' ' . $editor->name : 'Неизвестный';
        $page->edited_at = Carbon::parse($page->updated_at)->setTimezone('Asia/Almaty')->format('d.m.Y H:i');
        $page->created = Carbon::parse($page->created_at)->setTimezone('Asia/Almaty')->format('d.m.Y H:i');
        $page->questions = TestQuestion::where('testable_type', 'App\Knowbase')->where('testable_id', $request->id)->get();

        $breadcrumbs = $this->getBreadcrumbs($page);
        $top_parent = $this->getTopParent($request->id);
        
        $trees = [];

        if ($request->refresh) {
            if ($top_parent) {
                $trees = KnowBase::where('parent_id', $top_parent->id)->with('children')
                ->orderBy('order')->get();

                foreach ($trees as $tree) {
                    $tree->parent_id = null;
                }
                $trees = $trees->toArray();
            }
        }
        

        $can_read = false;
        if($top_parent != null && auth()->user()->can('kb_edit')) {
            $can_read = true;
        } else if($top_parent != null && in_array($top_parent->id, $this->getBooks())) {
            $can_read = true;  
        } 

        if($can_read) {
            $data = [
                'book' => $page,
                'breadcrumbs' => $breadcrumbs,
                'tree' => $trees,
                'top_parent' => $top_parent,
            ];
        } else {
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

            $access = 0;
            if(
                count($request['who_can_read']) == 1  
                && $request['who_can_read'][0]['id'] == 0 
                && $request['who_can_read'][0]['type'] == 0
            ) {
                $access = 1;
               
            } else {
                $this->saveBookAccesses($request->id, $request['who_can_read'], 1);
            }
            
            if(
                count($request['who_can_edit']) == 1  
                && $request['who_can_edit'][0]['id'] == 0 
                && $request['who_can_edit'][0]['type'] == 0
            ) {
                $access = 2;
            } else {
                $this->saveBookAccesses($request->id, $request['who_can_edit'], 2);
            }
            
            $page->access = $access;
            $page->save();

        }

    }
    
    private function saveBookAccesses($book_id, $items, $level = 1)
    {
        foreach ($items as $key => $item) {
            if($item['type'] == 1) $model = 'App\\User';
            if($item['type'] == 2) $model = 'App\\ProfileGroup';
            if($item['type'] == 3) $model = 'App\\Position';

            KnowBaseModel::create([
                'model_type' => $model,
                'model_id' => $item['id'],
                'book_id' => $book_id,
                'access' => $level
            ]);
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

           // $this->notifyAboutChanges('Страница: ', $page);
        }
    }

    private function canSaveWithoutTest() {
        $setting = Setting::where('name', 'allow_save_kb_without_test')
                ->first();

        return $setting && $setting->value == 1;
    }

    private function notifyAboutChanges($text, KnowBase $page) {
        $setting = Setting::where('name', 'send_notification_after_edit')
                ->first();

        if($setting && $setting->value == 1) {

            $TOP_parent = $this->getTopParent($page->id);

            if(!$TOP_parent) return;


            if($TOP_parent->access == 1 || $TOP_parent->access == 2) {
                $users = User::with('user_description')
                    ->whereHas('user_description', function ($query) {
                        $query->where('is_trainee', 0);
                    })
                    ->get('id')
                    ->pluck('id')
                    ->toArray();
            } else {
                $users = $TOP_parent->getUsersWithAccess();
            }
            
            
            $message = 'База знаний: <b>' . $TOP_parent->title . '</b><br><b>'. $text . ':</b> ';
            $message .= '<a href="/kb?s=' . $TOP_parent->id .'&b=' . $page->id . '" target="_blank">' . $page->title . '</a>';

            foreach ($users as $key => $user_id) {
                \App\UserNotification::create([
                    'user_id' => $user_id,
                    'about_id' => 0,
                    'title' => 'Изменения в базе знаний',
                    'group' => now(),
                    'message' => $message
                ]);
            } 
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
        $kb->questions = [];
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
        $ids = [];
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
                $ids[] = $q['id']; 
                
            } else {
                $q = TestQuestion::create($params);
                $ids[] = $q->id; 
            }
        }

        // count pass grade
        $pass_grade = $request->pass_grade;
        if($pass_grade > count($request->questions)) $pass_grade = count($request->questions);

        $book = KnowBase::withTrashed()->find($request->id);
        $book->pass_grade = $pass_grade;
        $book->save();
        
        return $ids;
    }


    public function getAccess(Request $request) {

        $book = KnowBase::withTrashed()->find($request->id);

        $selected_all_badge = [ // All badge in superselect.vue
            'id' => 0,
            'type' => 0,
            'name' => 'Все',
        ];

        return [
            'who_can_read' => $book->access == 1 ? [$selected_all_badge] : $this->getWhoCanReadOrEdit($request->id, 'read'),
            'who_can_edit' => $book->access == 2 ? [$selected_all_badge] : $this->getWhoCanReadOrEdit($request->id, 'edit'),
        ];
    }

    private function getWhoCanReadOrEdit($book_id, $access = 'read')
    {       
        $can = [];

        $items = KnowBaseModel::where([
            'book_id' => $book_id,
            'access' => $access == 'edit' ? 2 : 1
        ])->get();
     
        foreach ($items as $key => $item) {

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

            $can[] = $arr;
        }

        return $can;

    }

}
