<?php

namespace App\Http\Controllers;

use App\Models\User\Card;
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

        $authRole = auth()->user()->toArray();





        return view('cabinet',compact('authRole'));
    }

    public function get()
    {
        if(!auth()->user()->is_admin) {
            return redirect('/');
        }

        $users = User::withTrashed()->get(['id', DB::raw("CONCAT(name,' ',last_name) as email")]);

        foreach($users as $user) {
            if($user->email == '') $user->email = 'x';
        }


        $admins = User::withTrashed()
            ->where('is_admin', 1)
            ->get(['id', DB::raw("CONCAT(name,' ',last_name) as email")]);

        $user = User::find(auth()->user()->getAuthIdentifier());

        $user_payment = Card::where('user_id',auth()->user()->getAuthIdentifier())->get('bank','cardholder','country','number','phone')->toArray();



        return [
            'users' => $users,
            'user' => $user,
            'admins' => $admins,
            'user_payment'=>$user_payment,
        ];
    }

    public function save(Request $request)
    {
        $admins = User::withTrashed()
            ->where('is_admin', 1)
            ->get(['id'])
            ->pluck('id')
            ->toArray();

        foreach ($admins as $key => $id) {
            $admin = User::withTrashed()->find($id);
            if($admin) {
                $admin->is_admin = 0;
                $admin->save();
            }
        }

        foreach($request->admins as $index => $user) {
            $admin = User::withTrashed()->find($user['id']);
            if($admin) {
                $admin->is_admin = 1;
                $admin->save();
            }
        }

    }



}
