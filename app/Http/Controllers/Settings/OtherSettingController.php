<?php

namespace App\Http\Controllers\Settings;

use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Mail\PasswordReset;
use App\Models\CentralUser;
use App\Setting;
use App\User;
use Exception;
use Mail;
use Hash;

class OtherSettingController extends Controller
{
    protected function password_generate($chars): string
    {
        $data = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcefghijklmnopqrstuvwxyz';
        return substr(str_shuffle($data), 0, $chars);
    }

    /**
     * @throws TenantCouldNotBeIdentifiedById
     */
    public function reset(Request $request): JsonResponse
    {
        $centralUser = CentralUser::userByEmail($request->get('email'));

        if (!$centralUser) {
            return response()->json(['success' => false]);
        }
        // New pass
        $pass = $this->password_generate(7);
        $hashedPass = Hash::make($pass);

        // Update central user
        $centralUser->password = $hashedPass;
        $centralUser->save();

        // Update tenants
        $portals = $centralUser->cabinets->pluck('id')->toArray();
        foreach ($portals as $portal) {
            try {
                tenancy()->initialize($portal);
                User::query()->where('email', $centralUser->email)->update([
                    'password' => $hashedPass
                ]);
            } catch (Exception) {}
        }

        $mailData = [
            'password' => $pass
        ];

        $mail = new PasswordReset($mailData);

        Mail::to($request->get('email'))->send($mail);


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
    public function getSettings(Request $request): array
    {
        $keys = $this->setting_names($request->get('type'));

        $settings = []; // array to return
        $disk = \Storage::disk('s3');

        foreach ($keys as $key => $value) {
            /** @var Setting $setting */
            $setting = Setting::query()->where('name', $key)->first();

            if ($setting) {
                $settings[$key] = $setting->value == 1;

                if ($request->get('type') == 'company' && $setting->value) {
                    $settings['logo'] = $disk->temporaryUrl(
                        $setting->value, now()->addMinutes(360)

                    );
                }

                if (str_starts_with($key, 'custom_')) {
                    $settings[$key] = $setting->value;
                }
            }

            if (!$setting) {
                Setting::query()->create([
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
     * @return array
     */
    public function saveSettings(Request $request): array
    {
        $keys = $this->setting_names($request->get('type'));

        $settings = [];
        foreach ($keys as $key => $value) {
            $settings[$key] = $request[$key];

            if ($request->hasFile('file')) {
                $links = $this->upload_image($request->file('file'), $key);
                $request[$key] = $links['relative'];
                $settings[$key] = $links['temp'];

            }

            Setting::query()
                ->where('name', $key)
                ->update([
                    'value' => $request[$key]
                ]);
        }

        return $settings;
    }


    private function setting_names($type): array
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
     * @return array
     */
    public function upload_image($file, $key): array
    {
        $setting = Setting::query()->where('name', $key)->first();

        $disk = \Storage::disk('s3');

        try {
            if ($setting['logo'] != '' && $setting['logo'] != null && $setting['logo'] != 0) {
                if ($disk->exists($setting['logo'])) {
                    $disk->delete($setting['logo']);
                }
            }
        } catch (\Throwable) {
            // League \ Flysystem \ UnableToCheckDirectoryExistence
        }

        $extension = $file->getClientOriginalExtension();
        $fileName = uniqid() . '_' . md5(time()) . '.' . $extension;

        $path = $disk->putFileAs('company/logo', $file, $fileName); // загрузить

        return [
            'relative' => $path,
            'temp' => $disk->temporaryUrl(
                $path, now()->addMinutes(360)
            )
        ];
    }
}
