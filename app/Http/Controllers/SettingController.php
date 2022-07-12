<?php

namespace App\Http\Controllers;

use App\Components\TelegramBot;
use App\Setting;
use App\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Mail;

class SettingController extends Controller
{

    public function __construct() {
        //$this->middleware('auth');
    }

    protected function password_generate($chars) 
    {
      $data = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcefghijklmnopqrstuvwxyz';
      return substr(str_shuffle($data), 0, $chars);
    }

    public function reset(Request $request) {

        $accountUser = User::userByEmail($request->email);
        info($accountUser);
        if(!$accountUser) {
            return response()->json( ['success'=>false] );
        }
        

        $pass = $this->password_generate(7);
        $accountUser->password = \Hash::make($pass);
        $accountUser->save();

        $mailData = [
            'password' => $pass,
            'subdomain' => tenant('id')
        ];

        $mail = new \App\Mail\PasswordReset($mailData);
        
        \Mail::to($request->email)->send($mail);
        

        return response()->json(['success'=>true]);
    }

    /**
     * 
     * @param Request $request
     * 
     * @return array
     * 
     * 'settings' => [
     *     'send_notification_after_edit' => true,
     *     'show_page_from_kb_everyday' => false,
     *     'allow_save_kb_without_test' => true,
     *  ]
     * 
     */
    public function getSettings(Request $request)
    {    
        $keys = $this->setting_names($request->type);

        $settings = []; // array to return

        foreach ($keys as $key => $value) {
            $setting = Setting::where('name', $key)->first();

            $settings[$key] = $setting && $setting->value == 1 ? true : false;
            if(!$setting) {
                Setting::create([
                    'name' => $key,
                    'value' => false,
                    'description' => $value
                ]);
            } 
            
        }

        return [
            'settings' => $settings
        ];
    }

    /**
     * @param Request $request
     * 
     * @return void
     */
    public function saveSettings(Request $request)
    {   
        $keys = $this->setting_names($request->type);

        foreach ($keys as $key => $value) {
            $setting = Setting::where('name', $key)
                ->update([
                    'value' => $request[$key]
                ]);
        }
    }

    private function setting_names($type)
    {
        $arr = [];

        if($type == 'book') $arr = [
            'allow_save_book_without_test' => 'Разрешить сохранять книгу без тестовых вопросов',
        ];

        if($type == 'video') $arr = [
            'allow_save_video_without_test' => 'Разрешить сохранять видео без тестовых вопросов',
        ];

        if($type == 'kb') $arr = [
            'send_notification_after_edit' => 'Отправлять уведомления сотрудникам об изменениях в базе знаний',
            'show_page_from_kb_everyday' => 'Показывать одну из страниц базы знаний каждый день, после нажатия на кнопку "начать рабочий день"',
            'allow_save_kb_without_test'=> 'Разрешить вносить изменения без тестовых вопросов в разделах базы знаний',
        ];
        
        return $arr;
    }

}
