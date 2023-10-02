<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Setting;
use App\User;
use Illuminate\Http\Request;

class OtherSettingController extends Controller
{

    public function __construct()
    {
        //$this->middleware('auth');
    }

    protected function password_generate($chars)
    {
        $data = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcefghijklmnopqrstuvwxyz';
        return substr(str_shuffle($data), 0, $chars);
    }

    public function reset(Request $request)
    {

        $accountUser = User::userByEmail($request->email);
        info($accountUser);
        if (!$accountUser) {
            return response()->json(['success' => false]);
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


        return response()->json(['success' => true]);
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
        $disk = \Storage::disk('s3');

        foreach ($keys as $key => $value) {
            $setting = Setting::where('name', $key)->first();

            if ($setting) {
                $settings[$key] = $setting->value == 1;

                if ($request->type == 'company' && $setting->value) {
                    $settings['logo'] = $disk->url(
                        $setting->value
                    );
                }

                if (substr($key, 0, 7) == 'custom_') {
                    $settings[$key] = $setting->value;
                }
            }

            if (!$setting) {
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
     */
    public function saveSettings(Request $request)
    {
        $keys = $this->setting_names($request->type);

        $settings = [];
        foreach ($keys as $key => $value) {
            $settings[$key] = $request[$key];

            if ($request->hasFile('file')) {
                $links = $this->upload_image($request->file('file'), $key);
                $request[$key] = $links['relative'];
                $settings[$key] = $links['temp'];

            }

            Setting::where('name', $key)
                ->update([
                    'value' => $request[$key]
                ]);
        }
        return $settings;
    }


    private function setting_names($type)
    {
        $arr = [];

        if ($type == 'book') $arr = [
            'allow_save_book_without_test' => 'Разрешить сохранять книгу без тестовых вопросов',
        ];

        if ($type == 'video') $arr = [
            'allow_save_video_without_test' => 'Разрешить сохранять видео без тестовых вопросов',
        ];

        if ($type == 'kb') $arr = [
            'send_notification_after_edit' => 'Отправлять уведомления сотрудникам об изменениях в базе знаний',
            'show_page_from_kb_everyday' => 'Показывать одну из страниц базы знаний каждый день, после нажатия на кнопку "начать рабочий день"',
            'allow_save_kb_without_test' => 'Разрешить вносить изменения без тестовых вопросов в разделах базы знаний',
        ];
        if ($type == 'company') $arr = [
            'logo' => 'Логотип компании',
        ];

        if (empty($arr)) $arr = [
            'custom_' . $type => '',
        ];

        return $arr;
    }

    /**
     * @param $file
     * @param $key
     */
    public function upload_image($file, $key): array
    {
        $setting = Setting::where('name', $key)->first();

        $disk = \Storage::disk('s3');

        try {
            if ($setting['logo'] != '' && $setting['logo'] != null && $setting['logo'] != 0) {
                if ($disk->exists($setting['logo'])) {
                    $disk->delete($setting['logo']);
                }
            }
        } catch (\Throwable $e) {
            // League \ Flysystem \ UnableToCheckDirectoryExistence
        }

        $extension = $file->getClientOriginalExtension();
        $fileName = uniqid() . '_' . md5(time()) . '.' . $extension;

        $path = $disk->putFileAs('company/logo', $file, $fileName); // загрузить

        return [
            'relative' => $path,
            'temp' => $disk->url(
                $path, now()->addMinutes(360)
            )
        ];
    }


}
