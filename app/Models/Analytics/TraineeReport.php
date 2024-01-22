<?php

namespace App\Models\Analytics;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\User;
use App\ProfileGroup;

class TraineeReport extends Model
{
    /**
     * Таблица для хранения отчета о присутствии стажеров с 1 по 7 день обучения и ответы для оценки обучения
     */
    protected $table = 'trainee_report';

    protected $casts = [
        'data' => 'array',
    ];

    public $timestamps = true;

    protected $fillable = [
        'date',
        'group_id',
        'leads',
        'day_1',
        'day_2',
        'day_3',
        'day_4',
        'day_5',
        'day_6',
        'day_7',
        'data',
    ];

    /**
     * @return array
     */
    public static function getBlocks($date, $groups = [])
    {
        $date = Carbon::parse($date);

        $reports = self::query()
            ->whereYear('date', $date->year)
            ->whereMonth('date', $date->month)
            ->get();

        if (!count($groups)) $groups = $reports->pluck('group_id')->toArray();

        $groups_key_value = ProfileGroup::query()
            ->where('active', 1)
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $result = [];

        for ($i = $date->daysInMonth; $i >= 1; $i--) {
            foreach ($groups as $group_id) {
                $date->day($i);
                $tr = $reports
                    ->where('date', $date->format('Y-m-d'))
                    ->where('group_id', $group_id)
                    ->first();

                if ($tr && $tr->leads > 0) {
                    $group = [
                        'day' => $date->day,
                        'date' => $date->format('d.m.Y'),
                        'group_id' => $group_id,
                        'group' => array_key_exists($group_id, $groups_key_value) ? $groups_key_value[$group_id] : 'Отдел №' . $group_id,
                        'quiz' => self::formAnswers($tr->data),
                        'presence' => [
                            0 => $tr->leads,
                            1 => $tr->day_1,
                            2 => $tr->day_2,
                            3 => $tr->day_3,
                            4 => $tr->day_4,
                            5 => $tr->day_5,
                            6 => $tr->day_6,
                            7 => $tr->day_7,
                        ]
                    ];
                    $result[] = $group;
                }
            }
        }

        $_sort = array_column($result, 'day');
        array_multisort($_sort, SORT_DESC, $result);

        return $result;
    }

    public static function formAnswers($data)
    {
        if ($data == null) $data = [];
        $count[1] = 0;
        $count[2] = 0;
        $count[3] = 0;
        $questions = [
            1 => [
                2 => [
                    'count' => 0,
                    'text' => 'Обраб. вх и исх'
                ],
                1 => [
                    'count' => 0,
                    'text' => 'Cет. маркетинг'
                ],
                0 => [
                    'count' => 0,
                    'text' => 'Не понятно'
                ],
            ],
            2 => [
                2 => [
                    'count' => 0,
                    'text' => 'Cвободный'
                ],
                1 => [
                    'count' => 0,
                    'text' => '6 - 1'
                ],
                0 => [
                    'count' => 0,
                    'text' => 'Eще не знаю'
                ],
            ], // like 1
            3 => [
                1 => [
                    'count' => 0,
                    'text' => 'фикс 100 - 120'
                ],
                2 => [
                    'count' => 0,
                    'text' => 'оплата за каждый час'
                ],
                3 => [
                    'count' => 0,
                    'text' => 'фикс 80 + 40 бон'
                ],
                0 => [
                    'count' => 0,
                    'text' => 'Не знаю'
                ],
            ], // like 1
            4 => [ // avg rating
                'count' => 0,
                'sum' => 0,
                'avg' => 0,
                'percent' => 0,
            ],
            5 => [], //array of items
        ];

        foreach ($data as $key => $item) {

            for ($i = 1; $i <= 3; $i++) { // $i is question number
                if (array_key_exists($i, $item)) {
                    // $answer_index = 1; // yes
                    // if($item[$i] == 0) { // no
                    //     $answer_index = 0;
                    // }

                    $questions[$i][$item[$i]]['count'] = $questions[$i][$item[$i]]['count'] + 1;
                    $count[$i] = $count[$i] + 1;
                }
            }

            if (array_key_exists(4, $item) && $item[4] > 0) {
                $questions[4]['count'] = $questions[4]['count'] + 1;
                $questions[4]['sum'] += (int)$item[4];
            }

            if (array_key_exists(5, $item)) {
                $questions[5][] = $item[5];
            }
        }

        foreach ($questions as $key => $q) {
            if ($key == 1 || $key == 2) {
                if ($count[$key] > 0) {
                    $questions[$key][2]['percent'] = round($questions[$key][2]['count'] / $count[$key] * 100, 1);
                    $questions[$key][1]['percent'] = round($questions[$key][1]['count'] / $count[$key] * 100, 1);
                    $questions[$key][0]['percent'] = round($questions[$key][0]['count'] / $count[$key] * 100, 1);
                } else {
                    $questions[$key][2]['percent'] = 0;
                    $questions[$key][1]['percent'] = 0;
                    $questions[$key][0]['percent'] = 0;
                }
            }

            if ($key == 3) {
                if ($count[$key] > 0) {
                    $questions[$key][3]['percent'] = round($questions[$key][3]['count'] / $count[$key] * 100, 1);
                    $questions[$key][2]['percent'] = round($questions[$key][2]['count'] / $count[$key] * 100, 1);
                    $questions[$key][1]['percent'] = round($questions[$key][1]['count'] / $count[$key] * 100, 1);
                    $questions[$key][0]['percent'] = round($questions[$key][0]['count'] / $count[$key] * 100, 1);
                } else {
                    $questions[$key][3]['percent'] = 0;
                    $questions[$key][2]['percent'] = 0;
                    $questions[$key][1]['percent'] = 0;
                    $questions[$key][0]['percent'] = 0;
                }
            }

            if ($key == 4 && $questions[4]['count'] > 0) {
                $questions[4]['avg'] = round($questions[4]['sum'] / $questions[4]['count'], 1);
                $questions[4]['percent'] = $questions[4]['avg'] * 10;
            }
        }

        return $questions;
    }
}
