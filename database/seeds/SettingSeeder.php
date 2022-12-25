<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->insert( $this->getSettings() );
    }

    private function getSettings() : array
    {
        return [
            [
                'id' => 1,
                'name' => 'send_notification_after_edit',
                'description' => 'Отправлять уведомления сотрудникам об изменениях в базе знаний',
                'value' => '1',  
            ],
            [
                'id' => 2,
                'name' => 'show_page_from_kb_everyday',
                'description' => 'Показывать одну из страниц базы знаний каждый день, после нажатия на кнопку "начать рабочий день"',
                'value' => '1',  
            ],
            [
                'id' => 3,
                'name' => 'allow_save_kb_without_test',
                'description' => 'Разрешить вносить изменения без тестовых вопросов в разделах базы знаний',
                'value' => '1',  
            ],
            [
                'id' => 4,
                'name' => 'allow_save_video_without_test',
                'description' => 'Разрешить сохранять видео без тестовых вопросов',
                'value' => '1',  
            ],
            [
                'id' => 5,
                'name' => 'allow_save_book_without_test',
                'description' => 'Разрешить сохранять книгу без тестовых вопросов',
                'value' => '1',  
            ],
            [
                'id' => 6,
                'name' => 'currency_kzt',
                'description' => 'KZT тенге',
                'value' => '1',  
            ],
        ];
    }
}
