<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\User;
use App\Admin;
use DB;

class CabinetController extends Controller
{
    public function index()
    {
        View::share('menu', 'cabinet');
        View::share('link', 'cabinet');

        return view('cabinet');
    }

    public function get()
    {
        $users = User::get(['id', DB::raw("CONCAT(NAME,' ',LAST_NAME) as email")]);

        foreach($users as $user) {
            if($user->email == '') $user->email = 'x'; 
        }

        $admin = Admin::where('owner_id', 18)->first(); 
        $admins = User::withTrashed()->whereIn('id', $admin->users)->get(['id', DB::raw("CONCAT(NAME,' ',LAST_NAME) as email")]);

        return [
            'users' => $users,
            'admins' => $admins
        ];
    }

    public function save(Request $request)
    {
        $admins = [];
        foreach($request->admins as $index => $user) {
            $admins[] = $user['id'];
        }
        $admin = Admin::where('owner_id', 18)->first(); 
        $admin->users = array_unique($admins);
        $admin->save();
        
    }
}
