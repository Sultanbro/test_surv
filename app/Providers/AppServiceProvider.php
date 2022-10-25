<?php

namespace App\Providers;

use Illuminate\Support\Facades\Response;
use \Symfony\Component\HttpFoundation\Response as HttpFoundation;
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

                $reminder = false; // Уведомление о непрочитанных сообщениях, с 14-00 до 18-59
               
                if($user->isStartedDay()) {
             
                    if(!$user->readCorpBook()) {
                       
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
                    'laraveToVue' => json_encode([
                        'csrfToken'   => csrf_token(),
                        'userId'      => auth()->id(),
                        'email'       => auth()->user() ? auth()->user()->email : '',
                        'is_admin'    => auth()->user() ? auth()->user()->is_admin == 1 : false,
                        'permissions' => auth()->user() ? auth()->user()->getAllPermissions()->pluck('name')->toArray() : [] 
                    ], true)
                ]);


            }

        });

        \View::composer('layouts.app', function($view) {

            if(!\Auth::guest()) {

                $view->with([
                    'laravelToVue' => [
                        'csrfToken'   => csrf_token(),
                        'userId'      => auth()->id(),
                        'avatar'      => isset(auth()->user()->img_url) && !is_null(auth()->user()->img_url),
                        'email'       => auth()->user()->email,
                        'is_admin'    => auth()->user()->is_admin == 1,
                        'permissions' => auth()->user()->getAllPermissions()->pluck('name')->toArray() // Spatie permissions
                    ]
                ]);

            } else {
                $view->with([
                    'laravelToVue' => [
                        'csrfToken'   => csrf_token(),
                    ]
                ]);
            }

        });

        Response::macro('success', function ($data, $statusCode = HttpFoundation::HTTP_OK, $message = 'success',) {
            return response()->json([
                'status'  => $statusCode,
                'message' => $message,
                'data' => $data
            ]);
        });

        Response::macro('error', function ($message, $status_code) {
            return response()->json([
                'status'  => $status_code,
                'message' => $message
            ]);
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
