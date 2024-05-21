<?php

namespace Database\Seeders;

use App\Enums\Mailing\MailingEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MailingTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $templates = [
            [
                'id'                => 1,
                'name'              => 'Оформление нового сотрудника',
                'title'             => 'Подготовьте документы для оформления нового сотрудника в штат',
                'type_of_mailing'   => json_encode(["in-app"]),
                'days'              => json_encode([]),
                'frequency'         => MailingEnum::TRIGGER_APPLIED,
                'status'            => 1,
                'is_template'       => 1,
                'created_by'        => 5
            ],
            [
                'id'                => 2,
                'name'              => 'Анкета уволенного',
                'title'             => 'Уважаемый коллега! Какими бы ни были причины расставания, мы благодарим 
                                        Вас за время, силы, знания и энергию, которые 
                                        Вы отдали для успешной работы и развития нашей организации, и просим заполнить эту небольшую анкету.',
                'type_of_mailing'   => json_encode(["in-app"]),
                'days'              => json_encode([]),
                'frequency'         => MailingEnum::TRIGGER_FIRED,
                'status'            => 1,
                'is_template'       => 1,
                'created_by'        => 5
            ],
            [
                'id'                => 3,
                'name'              => 'Не присутствовал на стажировке',
                'title'             => 'Стажер отсутствовал на обучении. Созвонитесь и верните его на стажировку',
                'type_of_mailing'   => json_encode(["in-app"]),
                'days'              => json_encode([]),
                'frequency'         => MailingEnum::TRIGGER_ABSENT_INTERNSHIP,
                'status'            => 1,
                'is_template'       => 1,
                'created_by'        => 5
            ],
            [
                'id'                => 4,
                'name'              => 'Оцените вашего руководителя',
                'title'             => 'Оцените работу Вашего руководителя и старшего специалиста за текущий месяц.',
                'type_of_mailing'   => json_encode(["in-app"]),
                'days'              => json_encode([]),
                'frequency'         => MailingEnum::TRIGGER_MANAGER_ASSESSMENT,
                'status'            => 1,
                'is_template'       => 1,
                'created_by'        => 5
            ],
            [
                'id'                => 5,
                'name'              => 'Оцените вашего тренера',
                'title'             => 'Добрый день! Пройдите небольшой опрос, чтобы оценить Вашего тренера. Быстро. Анонимно. Для Дела.',
                'type_of_mailing'   => json_encode(["in-app"]),
                'days'              => json_encode([]),
                'frequency'         => MailingEnum::TRIGGER_COACH_ASSESSMENT,
                'status'            => 1,
                'is_template'       => 1,
                'created_by'        => 5
            ]
        ];

        DB::table('mailing_notifications')->insert($templates);
    }
}
