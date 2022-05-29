<?php

namespace App\Http\Controllers;

use App\Group;
use App\Message;
use App\User;
use App\Mail as Mailable;
use Illuminate\Http\Request;

use Auth;
use DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;


class MessageController extends Controller
{

    public function __construct() {
        View::share('menu', 'message');
        View::share('bigmenu', 'rassylka');
        View::share('title', 'Интеграция / СМС интеграция');
        View::share('js_file', 'message.js');
        //$this->middleware('auth');
    }

    public function index(Request $request) {
        $user = User::bitrixUser();
        $uid = $user->id;

        if ($request->isMethod('post') && $request->create_smpp) {

            $smpp = \App\User::getSMPPAccount();
            $smpp = json_decode($smpp);

            if(!$smpp) {
                $smpp = [
                    'login' => 'u'.$uid,
                    'ip' => $request->ip,
                    'password' => $request->password,
                    'time' => time(),
                    'status' => 0
                ];

                User::updateSMPPAccount($uid, json_encode($smpp));

                $template = 'message.smpp_email';
                $mmTo = [env('MEDIASEND_SUPPORT_EMAIL', 'u-support@u-marketing.org'), 'ali.akpanov@yandex.ru'];
                $subject = 'Запрос на SMPP аккаунт';
                $data = [
                    'user_id' => $uid,
                    'login' => $user->EMAIL,
                    'ip' => $request->ip,
                    'password' => $request->password,
                ];

                Mail::to($mmTo)->send(new Mailable($template, $subject, $data));

            }

            return redirect('/message/index');
        }

        $messages = Message::where('is_integration', 1)->where('user_id', $uid)->orderBy('updated_at', 'desc')->get();

        return view('message.index')->with('messages', $messages);

    }


    public function update(Request $request, $id = null) {
        $user = User::bitrixUser();
        $uid = $user->id;

        $groups = Group::bitrixGroups($uid);

        $message = Message::find($id);

        if (!$message) {
            $message = new Message();
        }

        if ($request->isMethod('post')) {
            $start_time = $request->start_time;
            $end_time = $request->end_time;
            if(trim($start_time) == '24:00') $start_time = '00:01';
            if(trim($end_time) == '24:00') $end_time = '23:59';
            if(trim($start_time) == '00:00') $start_time = '00:01';
            if(trim($end_time) == '00:00') $end_time = '23:59';
            if ($id) {
                $message->status = Message::STATUS_MODERATION; //Статус на модерации
                $message->name = $request->name;
                $message->description = $request->description;
                $message->message = $request->message;
                $message->group_id = $request->selected_group;
                $message->start_time = $start_time;
                $message->end_time = $end_time;
                $message->latin = $request->latin=='on'?1:0;
                $message->early = $request->early;
                $message->save();
            } else {
                $id = Message::create([
                    'user_id' => $uid,
                    'status' => Message::STATUS_MODERATION, //Статус на модерации
                    'name' => $request->name,
                    'is_integration' => 1,
                    'message' => $request->message,
                    'description' => $request->description,
                    'group_id' => $request->selected_group,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'latin' => $request->latin == 'on' ? 1 : 0,
                    'early' => $request->early,
                ])->id;
            }

            $message = Message::find($id);
            if($message->status == Message::STATUS_MODERATION) {

                $template = 'message.email';
                $mmTo    = env( 'MEDIASEND_SUPPORT_EMAIL', 'u-support@u-marketing.org' );
                $subject = 'Смс интеграция';
                $data= [
                    'user_id'         => $message->user_id,
                    'login'           => $message->user->EMAIL,
                    'name'            => $message->name,
                    'description'     => $message->description,
                    'message'         => $message->message,
                    'start_time'      => $message->start_time,
                    'end_time'        => $message->end_time,
                    'id'              => $message->id
                ];

                Mail::to($mmTo)->send(new Mailable($template, $subject, $data));
            }

            return redirect('/message/index');
        }


        return view('message.update')->with('message', $message)->with('groups', $groups)->with('title','Интеграция / Смс интегарция / '.$message->name);


    }

    public function status(Request $request, $id) {

        if($id == 'smpp') {
            $smpp = \App\User::getSMPPAccount($request->user_id);
            $smpp = json_decode($smpp);
            $smpp->status = 1;

            User::updateSMPPAccount($request->user_id, json_encode($smpp));
        } else {
            $message = Message::find($id);
            $message->status = $request->status;
            $message->save();
        }

        return redirect('/message/index');
    }
}
