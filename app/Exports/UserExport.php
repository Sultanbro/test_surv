<?php

namespace App\Exports;

use App\Models\Bitrix\Segment;
use App\Position;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Facades\Excel;

class UserExport implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize
{
    public $users;

    public $groups;

    public function __construct($users, $groups)
    {
        $this->users = $users;
        $this->groups = $groups;
    }

    public function collection()
    {
        $data['records'] = [];

        $userIds = (new Collection($this->users))
            ->pluck('id')
            ->toArray();

        $segments = Segment::get();
        $positions = Position::get()->pluck('position', 'id')->toArray();

        $groupUsers = DB::table('group_user')
          ->select('group_id', 'user_id')
          ->whereIn('user_id', $userIds)
          ->get();

        foreach($this->users as $user) {
            $seg = $segments->where('id', $user->segment)->first();
            $segment = $seg ? $seg->name : $user->segment;
            // dump($user->segment);

            $grs = '';

            $groupIds = $groupUsers
                ->where('user_id', $user->id)
                ->pluck('group_id')
                ->toArray();

            foreach($groupIds as $groupId) {
                try {
                    $grs .= $this->groups[$groupId] . '  ';
                } catch(\Exception $e) {
                    $grs .= $groupId . '  ';
                }
            }

            $data['records'][] = [
                0 => $user->id,
                1 => $user->last_name . ' ' . $user->name,
                2 => $user->email,
                3 => $grs,
                4 => $user->user_type == 'office' ? 'Офисный' : 'Удаленный',
                5 => $user->full_time == 1 ? 'Full-time' : 'Part-time',
                6 => $segment,
                7 => array_key_exists($user->position_id, $positions) ? $positions[$user->position_id] : $user->position_id,
                8 => $user->created_at,
                9 => $user->applied,
                10 => $user->deleted_at,
                11 => $user->fire_cause,
                12 => $user->phone,
                13 => $user->phone,
                14 => $user->phone,
                15 => $user->birthday,
                16 => $user->description,
                17 => $user->program_id == 1 ? "U-Calls" : 'Другое',
                18 => $user->working_day_id == 1 ? '5-2' : '6-1',
                19 => $user->working_time_id == 1 ? 8 : 9,
                20 => $user->work_start,
                21 => $user->work_end,
            ];
        }

        //dd(1);
        ob_end_clean();
        if (ob_get_length() > 0) ob_clean();

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'id',
            'ФИО',
            'Email',
            'Группы',
            'Тип',
            'Full/Part',
            'Сегмент',
            'Должность',
            'Дата регистрации',
            'Дата принятия',
            'Дата увольнения',
            'Причина увольнения',
            'Телефон',
            'Тел. 2',
            'Тел. 3',
            'День рождения',
            'Доп.',
            'Программа',
            'График',
            'Часы работы',
            'Начало',
            'Конец',
        ];
    }

    public function title(): string
    {
        return 'Отчет';
    }
}
