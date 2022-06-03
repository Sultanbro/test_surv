<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;

class UsersExport implements FromCollection
{

     public function __construct(array $users)
    {
        $this->users = $users;
    }

    public function collection()
    {
        //dd(array_unique($this->users));
       
        return \DB::table('users')
            ->join('working_times as wt', 'wt.id', '=', 'users.working_time_id')
            ->join('working_days as wd', 'wd.id', '=', 'users.working_day_id')
            ->join('zarplata as z', 'z.user_id', '=', 'users.id')
            ->leftjoin('timetracking as t', 't.user_id', '=', 'users.id')
            ->whereIn('users.id', array_unique($this->users))
            ->selectRaw("users.id as id,
                        users.phone as phone,
                        users.program_id as program_id,
                        CONCAT(users.last_name,' ',users.name) as full_name,
                        users.working_time_id as working_time_id,
                        users.working_day_id as working_day_id,
                        users.birthday as birthday,
                        wd.name as workDay,
                        wt.time as workTime,
                        z.zarplata as salary,
                        z.card_kaspi as card_kaspi,
                        z.kaspi_cardholder as kaspi_cardholder,
                        z.jysan_cardholder as jysan_cardholder,
                        z.card_jysan as card_jysan,
                        z.kaspi as kaspi,
                        z.jysan as jysan,
                        users.currency as currency,
                        CONCAT('KASPI', '') as card
                        ")
            ->groupBy('id', 'phone', 'full_name', 'workDay', 'working_time_id', 'workTime', 'salary', 
            'card_kaspi', 'card_jysan', 'jysan', 'kaspi','kaspi_cardholder','jysan_cardholder', 'card', 'program_id', 'birthday','currency', 'working_day_id')
            ->get();
    }
}