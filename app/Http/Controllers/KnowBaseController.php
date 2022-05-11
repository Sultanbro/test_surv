<?php

namespace App\Http\Controllers;

use Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\KnowBase;

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
            'books' => KnowBase::whereNull('parent_id')->with('children')->orderBy('order')->get()->toArray(),
        ];
    }

    public function getTree(Request $request) {
        $trees = KnowBase::where('parent_id', $request->id)->with('children')->orderBy('order')->get();

        foreach($trees as $tree) {
          $tree->parent_id = null;
        }
        return [
            'trees' => $trees->toArray()
        ];
    }

    public function getPage(Request $request, $id = null)
    {
      $page = KnowBase::find($request->id);
      return [
        'book' => $page
      ];
    } 
    
    public function updatePage(Request $request, $id = null)
    {
      $page = KnowBase::find($request->id);
      if($page) {
        $page->text = $request->text ?? '';
        $page->title = $request->title ?? 'Без названия';
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
    
    public function createPage(Request $request, $id = null)
    {
      $kb = KnowBase::where('order', 'desc')->first();
   
      return KnowBase::create([
        'title' => 'Новая страница',
        'text' => '',
        'order' => $kb ? $kb->order + 1 : 0,
        'parent_id' => $request->id,
        'hash' => 'adssad',
        'is_deleted' => 0
      ]);

    } 

   

    

    
}
