<?php

namespace App\Models\Analytics;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\User;
use Auth;

class RecruiterStat extends Model
{
    protected $table = 'recruiter_stats';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'profile',
        'dials', //  наборы
        'calls', // успешные вх и исх от 10 сек
        'minutes', // минуты успешных
        'converts', // согласий
        'leads',
        'hour',
        'date',
    ];

    const PROFILES = [
        0 => 'кз',
        1 => 'все удаленные',
        2 => 'вацап',
        3 => 'уведомления',
        4 => 'inhouse',
        5 => 'иностранные',
        6 => 'hh',
        7 => 'чаты',
    ];

    public static function tables($date = null) {
        if(is_null($date)) $date = date('Y-m-d');

        $date = Carbon::parse($date);

        $tables = [];

        $records = self::whereYear('date', $date->year)
            ->whereMonth('date', $date->month)
            ->selectRaw("*,DATE_FORMAT(date, '%e') as day")
            ->get();

            
        for($i=1;$i<=$date->daysInMonth;$i++) {

            $a = $records->where('day', $i);

            $tables[$i] = self::table($a);
        }

        return $tables;
    }

    public static function tablesPerDay($date = null) {
        $date = $date ?? date('Y-m-d');
        $date = Carbon::parse($date);

        return self::table(
            self::whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->whereDay('date', $date->day)
                ->selectRaw("*,DATE_FORMAT(date, '%e') as day")
                ->get()
        );
    }

    private static function table($records) {
        
        $users = $records->pluck('user_id')->toArray();
        $users = array_unique($users);
        
        $records = $records->groupBy('hour');
        $arr = [];
        
        $arr['all'] = [
            'name' => 'ИТОГО',
            'agrees' => 0,
            '_agrees' => -101,
            'show' => true,
            'profile' => 0,
            'user_id' => 0,
        ];
        

        foreach($users as $user_id) {
            $arr[$user_id] = [
                'name' => $user_id,
                'agrees' => 0,
                '_agrees' => 0, // для сортировки
                'show' => false,
                'profile' => 0,
                'user_id' => $user_id,
            ];
        }

        if(count($users) > 0) {
            $xusers = User::withTrashed()->whereIn('id', $users)->get();
            foreach($xusers as $user) {
                $arr[$user->id] = [
                    'name' => $user->last_name . ' ' . $user->name,
                    'agrees' => 0,
                    '_agrees' => 0, // для сортировки
                    'show' => false,
                    'profile' => 0,
                    'user_id' => $user->id,
                ];
            }
        }   
        

        
        foreach($records as $key => $hour_records) {
            $_total = [
                'dials' => 0,
                'calls' => 0,
                'minutes' => 0,
                'converts' => 0,
            ];

            foreach($hour_records as $hour_record) {
                $dials = $hour_record->dials != 0 ? $hour_record->dials : '-';
                $calls = $hour_record->calls != 0 ? $hour_record->calls : '-';
                $minutes = $hour_record->minutes != 0 ? $hour_record->minutes : '-';
                $converts = $hour_record->converts != 0 ? $hour_record->converts : '-';

                if($hour_record->calls < 2 && $hour_record->minutes < 2 && $hour_record->converts == 0 && $hour_record->dials < 2) {
                    $total = '';
                } else {
                    $total = $dials . '/' . $calls . '/' . $minutes .'/' . $converts;
                    $_total['dials'] += (int)$dials;
                    $_total['calls'] += (int)$calls;
                    $_total['minutes'] += (int)$minutes;
                    $_total['converts'] += (int)$converts;
                    $arr[$hour_record->user_id]['show'] = true;
                }

                $arr[$hour_record->user_id][$key] = $total;
                $arr[$hour_record->user_id]['agrees'] += $hour_record->converts;
                $arr[$hour_record->user_id]['profile'] = $hour_record->profile;
                $arr['all']['agrees'] += $hour_record->converts;
            }

            $arr['all'][$key] = $_total['converts'];
            $arr['all']['_agrees'] = -101;
        }
        
        // // SET EMOJIS AND GET TOP USER
        $highest = 0;
        $top_users = [];
        
        foreach($users as $user_id) {
            $agrees = $arr[$user_id]['agrees']; 
            $arr[$user_id]['_agrees'] = $agrees;
            
            if(in_array($user_id, [5263,7372,9974,9975])) {
                $arr[$user_id]['_agrees'] = -100;
            } else {
                if($highest < $agrees) {
                    $highest = $agrees;
                    $top_users = [$user_id];
                }
                if($highest == $agrees && $highest != 0) {
                    array_push($top_users, $user_id);
                }
            }

            

            $emoji = $agrees;
            if($agrees == 0) $emoji = '0 <img src="/admin/images/bad.png" style="width:15px;" />';
            $arr[$user_id]['agrees'] = $emoji;

            if(in_array($user_id, [9974,9975,5263,7372])) {
                $arr[$user_id]['agrees'] = '';
                $arr[$user_id]['_agrees'] = -100;
            }
        }

        if(count($top_users) != 0) {
            foreach ($top_users as $key => $user_id) {
                $arr[$user_id]['agrees'] = $highest . ' 🏆';    
            }
        }
        
        $_agrees = array_column($arr, '_agrees');
        array_multisort($_agrees, SORT_DESC, $arr); 
        
        foreach($arr as $key => $user) {
            if(!$user['show']) unset($arr[$key]);
        }

        return array_values($arr);
    }

    /**
     * Смена профиля
     * @return void
     */
    public static function changeProfile($user_id, $profile, Carbon $date) {
        $items = self::where('user_id', $user_id)
            ->where('date', $date->format('Y-m-d'))
            ->get();
            
        foreach($items as $item) {
            $item->profile = $profile;
            $item->save();
        }
    }


    public static function leads($date = null) {
        if(is_null($date)) $date = date('Y-m-d');

        $date = Carbon::parse($date);

        $arr = [];
        for($i=1;$i<=$date->daysInMonth;$i++) {
            $arr[$i] = self::lead_info($date->day($i));
        }

        return $arr;
    }

    public static function leadsPerDay($date = null) {
        if(is_null($date)) $date = date('Y-m-d');

        $date = Carbon::parse($date);

        return self::lead_info($date);
    }

    public static function lead_info(Carbon $date) {
        $records = self::where('date', $date->format('Y-m-d'))
                        ->get();

        $users = $records->pluck('user_id')->toArray();
        $users = array_unique($users);

        $arr = [];
        
        foreach($users as $user_id) {
            $user = User::withTrashed()->find($user_id);

            $max_leads = $records->where('user_id', $user_id)->max('leads');
            if($max_leads > 0) {
                $arr[$user_id] = [
                    'name' => $user  ? $user->last_name . ' ' . $user->name : $user_id,
                    'count' => $max_leads,
                    'user_id' => $user_id
                ];
            }
            
        }

        $arr['0'] = [
            'name' => 'Создано лидов',
            'user_id' => 0,
            'count' => $records->where('user_id', 0)->max('leads'),
        ];

        $_agrees = array_column($arr, 'user_id');
        array_multisort($_agrees, SORT_ASC, $arr); 

        return array_values($arr);
    }   

    public static function lead_isnfo(Carbon $date) {
        // $records = self::where('date', $date->format('Y-m-d'))
        //                 ->get();

        // $users = $records->pluck('user_id')->toArray();
        // $users = array_unique($users);

        // $arr = [];
        
        // foreach($users as $user_id) {
        //     $user = User::withTrashed()->find($user_id);
        //     $arr[$user_id] = [
        //         'name' => $user  ? $user->last_name . ' ' . $user->name : $user_id,
        //         'count' => $records->where('user_id', $user_id)->max('leads'),
        //         'user_id' => $user_id
        //     ];
        // }
        
        $as = AnalyticsSettings::where('group_id', 48)
            ->where('type', 'leads')
            ->where('date', $date->format('Y-m-d'))
            ->first();
        
        $arr = [];

        if($as) {
            foreach($as->data as $key => $value) 
            array_push($arr, [
                'name' => $key,
                'user_id' => 0,
                'count' => $value,
            ]);
        }

        // $_agrees = array_column($arr, 'user_id');
        // array_multisort($_agrees, SORT_ASC, $arr); 

        return array_values($arr);
    }   
}
