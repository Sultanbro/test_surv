<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use App\Notification;
use App\UserNotification;
use App\User;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //  Instead of Tailwind 
       Paginator::useBootstrap();

        \Schema::defaultStringLength(125);

        \View::composer('layouts.admin', function($view) {

            if(!\Auth::guest()) {


                $user= auth()->user();
                $user_id = auth()->user()->id;
                $bonus_notification = null;

                $unread_notifications = UserNotification::where('user_id', $user_id)
                    ->whereNull('read_at')
                    ->orderBy('created_at', 'desc')
                    ->take(150)
                    ->get();

                foreach($unread_notifications as $item) {
                    $message = $item->message;

                    $message = str_replace('admin.u-marketing.org', 'bp.jobtron.org', $message);

                    $item->message = $message;
                }

                if($unread_notifications->count() > 0) {
                    $bonus_notification = UserNotification::where('user_id', $user_id)
                        ->where('title', 'Сегодня получили бонусы')
                        ->whereNull('read_at')
                        ->first();

                    if($bonus_notification) {
                        $bonus_notification->read_at = now();
                        $bonus_notification->save();
                    }
                }

                $read_notifications = UserNotification::where('user_id', $user_id)
                    ->where('read_at', '!=' , NULL)
                    ->orderBy('read_at', 'desc')
                    ->take(50)
                    ->get();

                $tec = UserNotification::select(DB::raw('count(*) as total'))
                    ->where('user_id', $user_id)
                    ->whereNull('read_at')
                    ->orderBy('created_at', 'desc')
                    ->groupBy('user_id')
                    ->first();

                $unread = $tec ? $tec->total : 0;


               
                $head_users = User::withTrashed()->select(DB::raw("CONCAT_WS(' ',ID, last_name, name) as name"), 'ID as id')->get()->toArray();
               

                $superusers = User::where('is_admin', 1)->get(['id'])->pluck('id')->toArray();
                $users = [];
                $users = array_unique(array_merge($users, $superusers));


                $corp_book_page_show = false;
                $corp_book_page = null;
                $reminder = false; // Уведомление о непрочитанных сообщениях, с 14-00 до 18-59
               
                if($user->isStartedDay()) {
             
                    if(!$user->readCorpBook()) {
                       
                        $corp_book_page_show = true;
                        $corp_book_page = \App\KnowBase::getRandomPage();

                        $xuser = User::find($user->id);

                        $xuser->notified_at = now();
                        $xuser->save();
                    } else {
                        if(auth()->id() == 13865) {
                            if($unread > 0 && Carbon::now()->timestamp - Carbon::parse($user->notified_at)->timestamp  >= 60) {
                                $reminder = true;
    
                                $xuser = User::find($user->id);
                                $xuser->notified_at = now();
                                $xuser->save();
                            }
                        } else {
                            if($unread > 0 && Carbon::now()->timestamp - Carbon::parse($user->notified_at)->timestamp  >= 3600) {
                                $reminder = true;
    
                                $xuser = User::find($user->id);
                                $xuser->notified_at = now();
                                $xuser->save();
    
                            }
                        }
                        
                    }
                }
               
                $view->with([
                    'reminder' => $reminder,
                    'unread_notifications' => $unread_notifications,
                    'read_notifications' => $read_notifications,
                    'unread' => $unread,
                    'head_users' => $head_users,
                    'bonus_notification' => $bonus_notification,
                ]);


            }

        });

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
