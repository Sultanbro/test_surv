<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use App\Models\User\Card;
use App\User;

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
        $users = User::withTrashed()->get(['id', \DB::raw("CONCAT(name,' ',last_name) as email")]);

        foreach($users as $user) {
            if($user->email == '') $user->email = 'x';
        }

        $admins = User::withTrashed()
            ->where('is_admin', 1)
            ->get(['id', \DB::raw("CONCAT(name,' ',last_name) as email")]);

        $user = User::find(auth()->user()->getAuthIdentifier());

        $user_payment = Card::where('user_id',auth()->user()->getAuthIdentifier())->select('id','bank','cardholder','country','number','phone')->get()->toArray();

        return [
            'users' => $users,
            'user' => $user,
            'admins' => $admins,
            'user_payment'=> $user_payment,
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

    /**
     * добавление карты и измение данный через профиль
     */
    public function editUserProfile(Request $request)
    {
        if (isset($request->cards) && !empty($request->cards)){
            Card::where('user_id',auth()->user()->getAuthIdentifier())->delete();
            foreach ($request->cards as $card) {
                Card::create([
                    'user_id' => auth()->user()->getAuthIdentifier(),
                    'bank' => $card['bank'],
                    'country'=> $card['country'],
                    'cardholder'=> $card['cardholder'],
                    'phone' => $card['phone'],
                    'number'=> $card['number'],
                ]);
            }
        }

        $user = User::find(auth()->user()->getAuthIdentifier());
        $user['name'] = $request['query']['name'];
        $user['birthday'] = $request['birthday'];
        if (isset($request->password) && !empty($request->password)){
            $user['password'] = Hash::make($request->password);
        }
        $user['last_name'] = $request['query']['last_name'];
        $user['working_country'] = $request['working_country'];
        $user['working_city'] = $request['working_city'];
        if ($user->save()){
            return response(['success'=>'1']);
        }
    }


    /**
     * Удаление карты через профиль индивидуально Kairat
     */
    public function removeCardProfile(Request$request)
    {
        Card::find($request['card_id'])->delete();

    }

    /**
     * Kairat uploadCroppedImageProfile
     */
    public function uploadCroppedImageProfile(Request $request)
    {


        $user = User::withTrashed()->find(auth()->user()->getAuthIdentifier());



        if ($user->cropped_img_url){
            $filename = "cropped_users_img/".$user->cropped_img_url;
            if (file_exists($filename)) {
                unlink(public_path('cropped_users_img/'.$user->cropped_img_url));
            }
        }




        if ($request->file == "null" || $request->file == 'undefined'){
            $user->cropped_img_url = null;
            $user->save();

            $img = '<img src="'.url('/cropped_users_img').'/'.'noavatar.png'.'" alt="avatar" />';

            return response(['img'=>$img,'filename'=>'noavatar.png','type'=>0]);

        }else{

            $request->validate([
                'file' => 'required|mimes:jpg,jpeg,png'
            ]);



            $upload_path = public_path('cropped_users_img/');
            $generated_new_name = time() . '.' .'png';
            $request->file->move($upload_path, $generated_new_name);
            $user->cropped_img_url = $generated_new_name;
            $user->save();

            $img = '<img src="'.url('/cropped_users_img/').'/'.$generated_new_name.'" alt="avatar" />';
            return response(['img'=>$img,'filename'=>$generated_new_name,'type'=>1]);
        }
    }
}
