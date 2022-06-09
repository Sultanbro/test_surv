<?php
namespace App\Classes\Analytics;

use DB;
use App\User;
use App\UserDescription;
use App\Trainee;
use App\DayType;
use App\ProfileGroup;
use App\AnalyticsSettings;
use App\AnalyticsSettingsIndividually;
use App\Models\Analytics\RecruiterStat;

class LayoffQuiz 
{
    public static $quiz = [
        '1' => [
            'q' => '1. Укажите причину, по которой Вы приняли решение покинуть компанию Business Partner',
            'answers' => [
                ["text" => "Переезд на новое место жительства","count" => 0],
                ["text" => "Низкая оплата труда","count" => 0],
                ["text" => "Состояние здоровья","count" => 0],
                ["text" => "Более привлекательное место работы","count" => 0],
                ["text" => "Плохие условия работы в организации","count" => 0],
                ["text" => "Другое","count" => 0], // Input в опроснике обозначается так
            ],
            'type' => 'variant'
        ],
        '2' => [
            'q' => '2. Оцените пожалуйста условия труда в нашей организации:',
            'answers' => [],
            'type' => 'star'
        ],
        '3' => [
            'q' => '3. Насколько удовлетворяла Вас оплата труда в нашей организации?',
            'answers' => [],
            'type' => 'star'
        ],
        '4' => [
            'q' => '4. Если оценивать в целом, то в коллективе атмосфера дружеская или враждебная?',
            'answers' => [],
            'type' => 'star'
        ],
        '5' => [
            'q' => '5. У вас лично возникали проблемы в общении с коллегами и если да, то какие?',
            'answers' => [
                ["text" =>  "Не чувствовал(а) себя частью коллектива","count" => 0],
                ["text" =>  "Получал(а) открытые насмешки и оскорбления","count" => 0],
                ["text" =>  "Получал(а) скрытые, завуалированные насмешки и оскорбления","count" => 0],
                ["text" =>  "Получал(а) несправедливые обвинения в плохой работе","count" => 0],
                ["text" =>  "У меня не возникало проблем в отношениях с коллегами","count" => 0],
            ],
            'type' => 'variant'
        ],
        '6' => [
            'q' => '6. Как Вы оцените отношение руководства к Вам в нашей организации:',
            'answers' => [],
            'type' => 'star'
        ],
        '7' => [
            'q' => '7. Что Вы посоветовали бы руководству для улучшения условий работы?',
            'answers' => [
                ["text" => "Другое","count" => 0],
            ],
            'type' => 'answer'
        ],
    ];

    public static function getQuestions() {
        return self::$quiz;
    }
}
