<?php

namespace App\Http\Controllers;

use Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\KnowBase;
use App\User;
use App\Models\TestQuestion;
use Carbon\Carbon;

class KnowBaseController extends Controller
{
    public function index(Request $request)
    {   
        View::share('menu', 'kb');
        View::share('link', 'kb');

        return view('admin.books');
    }


    public function get(Request $request) {
        return [
            'books' => KnowBase::whereNull('parent_id')->orderBy('order')->get()->toArray(),
        ];
    }

    public function search(Request $request) {
      $phrase = '%'. $request->text . '%';
      $items = KnowBase::where('title', 'like' , $phrase)
        ->orWhere('text', 'like', $phrase)
        ->orderBy('order')
        ->get();
      
      foreach ($items as $key => $item) {
        $item->text = $this->cutFragment($item->text, $request->text);
      }

      return [
          'items' => $items
      ];
    }

    private function cutFragment($text, $fragment) {
	    $plainText = strip_tags($text);
  
      $start_pos = mb_stripos($plainText, $fragment);
      $start_pos -= 15;
      if($start_pos < 0) $start_pos = 0;
       
      return '...' . mb_substr($plainText, $start_pos, 50);
    }  

    public function getArchived(Request $request) {
        return [
            'books' => KnowBase::onlyTrashed()->whereNull('parent_id')->orderBy('order')->get()->toArray(),
        ];
    }
    

    public function getTree(Request $request) {
        $trees = KnowBase::where('parent_id', $request->id)->with('children')->orderBy('order')->get();

        foreach($trees as $tree) {
          $tree->parent_id = null;
        }
        return [
            'trees' => $trees->toArray(),
            'book' => KnowBase::find($request->id),
        ];
    }

    public function getPage(Request $request)
    {
      $page = KnowBase::withTrashed()->find($request->id);

      $user = User::withTrashed()->find($page->user_id);
      $page->author = $user ? $user->LAST_NAME . ' ' . $user->NAME : 'Неизвестный';
      $page->edited_at = Carbon::parse($page->updated_at)->setTimezone('Asia/Almaty')->format('d.m.Y H:i');
      

      $page->questions = TestQuestion::where('testable_type', 'kb')->where('testable_id', $request->id)->get();
      $breadcrumbs = $this->getBreadcrumbs($page);


      $trees = [];
      $top_parent = [];
      if($request->refresh) {

        $top_parent = $this->getTopParent($request->id);

        $trees = KnowBase::where('parent_id', $top_parent->id)->with('children')->orderBy('order')->get();

        foreach($trees as $tree) {
          $tree->parent_id = null;
        }
        $trees = $trees->toArray();
      }

      return [
        'book' => $page,
        'breadcrumbs' => $breadcrumbs,
        'tree' => $trees,
        'top_parent' => $top_parent,
      ];
    } 
    
    private function getTopParent($id) {
      $kb = KnowBase::withTrashed()->find($id);
      if($kb && $kb->parent_id != null) {
        return $this->getTopParent($kb->parent_id);
      }
      return $kb;
    }

    private function getBreadcrumbs($page) {
      $breadcrumbs = [];
      $breadcrumbs[] = $page;
      $parent_id = $page->parent_id;

      while($parent_id != null) {
        $xpage = KnowBase::withTrashed()->find($parent_id);
        if($xpage) {
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
      if($page) {
        $page->title = $request->title;
        $page->user_id = Auth::user()->ID;
        $page->save();
      }

    } 

    public function updatePage(Request $request, $id = null)
    {
      $page = KnowBase::find($request->id);
      if($page) {
        $page->text = $request->text ?? '';
        $page->title = $request->title ?? 'Без названия';
        $page->user_id = Auth::user()->ID;
        $page->save();
      }

    } 

    public function saveOrder(Request $request, $id = null)
    {
   
      $page = KnowBase::find($request->id);
      if($page) {
        $page->order = $request->order;
        $page->parent_id = $request->parent_id;
        $page->save();


        if($request->parent_id == null) {
          $pages =  KnowBase::where('parent_id', 0)
            ->where('id', '!=', $request->id)
            ->where('order', '>=', $request->order)
            ->orderBy('order', 'asc')
            ->get();

          $order = $request->order;
          foreach($pages as $page) {
            $order++;
            $page->order = $order;
            $page->save();
          }
        }
      }
      

    } 
    
    public function createPage(Request $request)
    {
      $kb = KnowBase::where('parent_id', $request->id)->orderBy('order', 'desc')->first();
      $order = $kb ? $kb->order + 1 : 0;

      $kb = KnowBase::create([
        'title' => 'Новая страница',
        'text' => '',
        'user_id' => Auth::user()->ID,
        'order' => $order,
        'parent_id' => $request->id,
        'hash' => md5(uniqid().mt_rand()),
        'is_deleted' => 0
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
        'user_id' => Auth::user()->ID,
        'order' => $kb ? $kb->order + 1 : 0,
        'parent_id' => null,
        'hash' => 'cat',
        'is_deleted' => 0
      ]);

    }

    
    public function deleteSection(Request $request)
    {
      $kb = KnowBase::find($request->id);
      if($kb) $kb->delete();
    }

    public function deletePage(Request $request)
    {
      $kb = KnowBase::find($request->id);
      if($kb) $kb->delete();
    }

    

    public function restoreSection(Request $request)
    {
      $kb = KnowBase::onlyTrashed()->find($request->id);
      if($kb) $kb->restore();
    }

    public function saveTest(Request $request)
    {
      foreach ($request->questions as $key => $q) {
        $params = [
            'order' => 0,
            'page'=> 0,
            'points'=> $q['points'],
            'testable_id'=> $request->id,
            'testable_type'=> "kb",
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
    
   

    

    
}
