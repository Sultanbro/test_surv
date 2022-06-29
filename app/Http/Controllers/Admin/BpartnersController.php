<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\UserDescription;
use App\KnowBase;

class BpartnersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        View::share('title', 'Админ панель');
        $this->middleware('auth');
    }

    public function books(Request $request, $id = null)
    {
      View::share('menu', 'kb');
      View::share('link', 'kb');
        
      $tree = KnowBase::whereNull('parent_id')->with('children')->orderBy('order')->get()->toArray();
     
      return view('admin.books');
    }

    public function getPage(Request $request, $id = null)
    {
      $page = KnowBase::find($request->id);
      return [
        'book' => $page
      ];
    } 

    public function pagesdelete(Request $request) {
    	if (isset($request['id'])) {
        $kb = KnowBase::where('id', $request->id)->first();

        if($kb) $kb->delete();
    	}

    	return response()->json([
	        'success' => true
	    ]);
    }

    public function uploadimages(Request $request) 
    {
      
    		$image = $request->file('attachment');
        $ext = $image->getClientOriginalExtension();
        if($ext == '') $ext ='jpg';
        $image_name = time() . '.' . $ext;
        $image->move("bpartners", $image_name);
  		
        return json_encode(array('location' => "/bpartners/".$image_name));

    }

    public function uploadaudio(Request $request)
    {
      
    		$audio = $request->file('attachment');
        $audio_name = time() . '.' . $audio->getClientOriginalExtension();
        $audio->move("bpartners/audio/", $audio_name);
  		
        return json_encode(array('location' => "/bpartners/audio/".$audio_name));
    }
}
