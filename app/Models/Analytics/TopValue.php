<?php

namespace App\Models\Analytics;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\ProfileGroup;
use App\CallBaseTotal;
use App\Models\Analytics\UserStat;
use App\Models\Analytics\AnalyticStat;
use App\GroupSalary;
use App\Salary;

class TopValue extends Model
{
    /**
     * Таблица для хранения значений на странице ТОП
     */
    protected $table = 'top_values';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'group_id',
        'date',
        'value',
        'unit', // символ в конце
        'options',
        'min_value',
        'max_value',
        'cell', // cell from pivot table 
        'activity_id',
        'round', // округление
        'is_main', // ключевой, по которому будет сортировка
        'fixed', // некоторые поля не редактируются
        'value_type', // avg  или sum с активности
        'reversed', // 
    ];
    
    public function getOptions()
    {

        $arr = json_decode($this->options, true);

        $result = [
            'angle' => -0.01,
            'staticLabels' => [
                'font' => "10px sans-serif", // Specifies font
                'labels' => [0, 50, 75, 100, 150], // Print labels at these values
                'color' => "#000000", // Optional: Label text color
                'fractionDigits' => 0, // Optional: Numerical precision. 0=round off.
            ],
            'staticZones' => [
                [ 'strokeStyle' => "#F03E3E", 'min' => 0, 'max' => 49 ], // Red
                [ 'strokeStyle' => "#fd7e14", 'min' => 50, 'max' => 74 ], // Orange
                [ 'strokeStyle' => "#FFDD00", 'min' => 75, 'max' => 99 ], // Yellow
                [ 'strokeStyle' => "#30B32D", 'min' => 100, 'max' => 150 ], // Green
            ],
            'pointer' => [
                'length' => 0.5, // // Relative to gauge radius
                'strokeWidth' => 0.025, // The thickness
                'color' => "#000000", // Fill color
            ],
            'limitMax' =>true,
            'limitMin' =>true,
            'lineWidth' => 0.2,
            'radiusScale' => 0.8,
            'colorStart' => "#6FADCF",
            'generateGradient' => true,
            'highDpiSupport' => true,
            'key' => $this->id * 1000,
        ];
        
        if(array_key_exists('angle', $arr)) $result['angle'] = $arr['angle'];
        if(array_key_exists('staticLabels', $arr)) $result['staticLabels'] = $arr['staticLabels'];
        if(array_key_exists('staticZones', $arr)) $result['staticZones'] = $arr['staticZones'];
        if(array_key_exists('pointer', $arr)) $result['pointer'] = $arr['pointer'];
        if(array_key_exists('limitMax', $arr)) $result['limitMax'] = $arr['limitMax'];
        if(array_key_exists('limitMin', $arr)) $result['limitMin'] = $arr['limitMin'];
        if(array_key_exists('lineWidth', $arr)) $result['lineWidth'] = $arr['lineWidth'];
        if(array_key_exists('radiusScale', $arr)) $result['radiusScale'] = $arr['radiusScale'];
        if(array_key_exists('colorStart', $arr)) $result['colorStart'] = $arr['colorStart'];
        if(array_key_exists('generateGradient', $arr)) $result['generateGradient'] = $arr['generateGradient'];
        if(array_key_exists('highDpiSupport', $arr)) $result['highDpiSupport'] = $arr['highDpiSupport'];

        $result['minValue'] = $this->min_value;
        $result['maxValue'] = $this->max_value;
        $result['unit'] = $this->unit;
        $result['name'] = $this->name;
        
        if($this->unit == 'место') {
            $result['staticZones'] = [
                [ 'strokeStyle' => "#F03E3E", 'min' => 1, 'max' => 2 ], // Red
                [ 'strokeStyle' => "#fd7e14", 'min' => 2, 'max' => 3 ], // Orange
                [ 'strokeStyle' => "#ffc107", 'min' => 3, 'max' => 4 ], // oragne light
                [ 'strokeStyle' => "#FFDD00", 'min' => 4, 'max' => 5 ], // Yellow
                [ 'strokeStyle' => "#42e467", 'min' => 5, 'max' => 6 ], // Green light
                [ 'strokeStyle' => "#30B32D", 'min' => 6, 'max' => 7 ], // Green
            ];
            $result['staticLabels']['labels'] = [];
        }

        return $result;
    }

    public static function getUtilityGauges($date, $group_ids = [])
    {

        if(count($group_ids) == 0) {
            $carbon = Carbon::createFromFormat('Y-m-d', $date);

            $group_ids = ProfileGroup::profileGroupsWithArchived($carbon->year, $carbon->month);
        }

        $gauge_groups = [];

        $activities = Activity::whereIn('group_id', $group_ids)->get();

        foreach($group_ids as $group_id) {
            $group = ProfileGroup::find($group_id);

            if ($group->archive_utility)
            {
                continue;
            }



            $top_values = TopValue::where([
                'group_id' => $group_id,
                'date' => $date,
            ])->get();
            
            if($group) { 
               
                $gauges = [];

                
                $percento = 0;
                foreach($top_values as $index => $top_value) {
                    $gauge = [
                        'id' => $top_value->id,
                        'group_id' => $top_value->group_id,
                        'place' => 1,
                        'activity_id' => $top_value->activity_id,
                        'unit' => $top_value->unit,
                        'editable' => false,
                        'edit_value' => false,
                        'diff' => 0,
                        'cell' => $top_value->cell,
                        'round' => $top_value->round,
                        'fixed' => $top_value->fixed,
                        'is_main' => $top_value->is_main,
                        'value_type' => $top_value->value_type,
                        'key' => $top_value->id * 1000,
                    ] + self::getDynamicValue($group_id, $date, $top_value);
                        
                    $gauges[] = $gauge;

                    if((float)$top_value->max_value - (float)$top_value->min_value != 0 && ($top_value->is_main == 1 || $index == 0)) {
                        $percento = ((float)$top_value->value - (float)$top_value->min_value) / ((float)$top_value->max_value - (float)$top_value->min_value);
                    }
                    
                }
                


                $values = array_column($gauges, 'is_main');
                array_multisort($values, SORT_DESC, $gauges);

                $gauge_groups[] = [
                    'id' => $group->id,
                    'name' => $group->name,
                    'gauges' => $gauges,
                    'group_activities' => $activities->where('group_id', $group->id),
                    'percento' => $percento
                ];
            }
        }
        
        $values_asc = array_column($gauge_groups, 'percento');
        array_multisort($values_asc, SORT_ASC, $gauge_groups); 
        

        return $gauge_groups;
    } 
    
    public static function getDynamicValue($group_id, $date, $top_value)
    {
        $value = (float)$top_value->value;
        $min_value = (float)$top_value->min_value;
        $max_value = (float)$top_value->max_value;

        $alter_name = '';

        $months = [
            1 => 'Январь',
            2 => 'Февраль',
            3 => 'Март',
            4 => 'Апрель',
            5 => 'Май',
            6 => 'Июнь',
            7 => 'Июль',
            8 => 'Август',
            9 => 'Сентябрь',
            10 => 'Октябрь',
            11 => 'Ноябрь',
            12 => 'Декабрь',
        ];
        $options = json_decode($top_value->options, true);

        try {
            $sections = $options['staticLabels']['labels']; // Уязвимый
        } catch(\Exception $e) {
            $options = $top_value->getOptions();
            $sections = $options['staticLabels']['labels']; // Уязвимый
        }
         
        $gauge_sections = self::getGaugeSections([
            'value' => $value,
            'min_value' => $min_value,
            'max_value' => $max_value,
            'options' => $top_value->getOptions(),
            'zones' => 5,
            'reverse' => $top_value->reversed == 1,
            'round' => $top_value->round,
        ]);

        $sections = $gauge_sections['sections'];
        $options = $gauge_sections['options'];

        if($top_value->activity_id != 0) {
         
            if($top_value->activity_id == -1) {
                $value = AnalyticStat::getCellValue($top_value->group_id, $top_value->cell, $date, $top_value->round);
                
            } else {

                $activity = Activity::withTrashed()->find($top_value->activity_id);

             
                if($activity && $activity->type == 'quality') {

                    $carbon = Carbon::parse($date);
                    $value = \App\QualityRecordMonthlyStat::where([
                            'month' => $carbon->month, 
                            'year' => $carbon->year,
                            'group_id' => $top_value->group_id
                        ])
                        ->where('total', '!=', 0)
                        ->get()
                        ->avg('total');

                } else {
                    $value = UserStat::total_for_month($top_value->activity_id, $date, $top_value->value_type);
                }
              
            }

            $min_value = $top_value->min_value;
            $max_value = $top_value->max_value;

            $gauge_sections = self::getGaugeSections([
                'value' => $value,
                'min_value' => $min_value,
                'max_value' => $max_value,
                'options' => json_decode($top_value->options, true),
                'zones' => 5,
                'reverse' => $top_value->reversed == 1,
                'round' => $top_value->round,
            ]);

            $sections = $gauge_sections['sections'];
            $options = $gauge_sections['options'];
        } 
    
        if($group_id == 53) {

          
            if($top_value->value_type == 'pcb') {
              
                $dd = Carbon::parse($date)->subMonth();
                $alter_name = 'Полезность(' . $months[(int)$dd->format('m')] . ')';
                $сallBaseTotal = CallBaseTotal::where('date', $dd->format('Y-m-d'))
                    ->where('name', 'conversion')
                    ->first();

                $value = $сallBaseTotal ? $сallBaseTotal->value : 0;

                $gauge_sections = self::getGaugeSections([
                    'value' => $value,
                    'min_value' => 0,
                    'max_value' => 1.6,
                    'round' => 1,
                    'options' => $top_value->getOptions(),
                    'reverse' => $top_value->reversed == 1,
                    'zones' => 5
                ]);

                $sections = $gauge_sections['sections'];
                $options = $gauge_sections['options'];
            }

            if($top_value->value_type == 'ccb') {
                $сallBaseTotal = CallBaseTotal::where('date', $date)
                    ->where('name', 'conversion')
                    ->first();

                $dd = Carbon::parse($date);
                
                $alter_name = 'Полезность(' . $months[(int)$dd->format('m')] . ')';
                $value = $сallBaseTotal ? $сallBaseTotal->value : 0;

                $gauge_sections = self::getGaugeSections([
                    'value' => $value,
                    'min_value' => 0,
                    'max_value' => 1.6,
                    'round' => 1,
                    'options' => $top_value->getOptions(),
                    'reverse' => $top_value->reversed == 1,
                    'zones' => 5,
                ]);

               // dump($gauge_sections);
                $sections = $gauge_sections['sections'];
                $options = $gauge_sections['options'];
            }
        
            
        }

        $top_value->value = $value;
        $top_value->min_value = $min_value;
        $top_value->max_value = $max_value;
        $top_value->options = $options;
        $top_value->save();
        
        if($alter_name != '') {
            $top_value->name = $alter_name;
        }

        return [
            'name' => $top_value->name,
            'value' => round($value, $top_value->round),
            'min_value' => $min_value,
            'max_value' => $max_value,
            'options' => $options,
            'reversed' => $top_value->reversed,
           'sections' => json_encode($sections),
            'angle' =>$options['angle'],
        ];
    
    }

    public static function getGaugeSections($args)
    {
        $value = $args['value'];
        $min = (float)$args['min_value'];
        $max = (float)$args['max_value'];
        $round = array_key_exists('round', $args) ? (int)$args['round'] : 0;
        $options = $args['options'];
        $reverse = array_key_exists('reverse', $args) ? $args['reverse'] : false;

        
        $sections = $options['staticLabels']['labels']; //  
      
      
            $sections = [
                round($min, $round),
                round((($max - $min) * 0.2) + $min, $round),
                round((($max - $min) * 0.4) + $min, $round),
                round((($max - $min) * 0.6) + $min, $round),
                round((($max - $min) * 0.8) + $min, $round),
                round($max, $round),
            ];
     

       // dump((($max - $min) * 0.2) + $min);
        
        $options['staticLabels']['labels'] = $sections;

        if($reverse) {
        
            $options['staticZones'] = [
                [ 'strokeStyle' => "#30B32D", 'min' => $sections[0], 'max' => $sections[1] ], // Red
                [ 'strokeStyle' => "#42e467", 'min' => $sections[1], 'max' => $sections[2] ], // Orange
                [ 'strokeStyle' => "#FFDD00", 'min' => $sections[2], 'max' => $sections[3] ], // Yellow
                [ 'strokeStyle' => "#fd7e14", 'min' => $sections[3], 'max' => $sections[4] ], // Green light
                [ 'strokeStyle' => "#F03E3E", 'min' => $sections[4], 'max' => $sections[5] ], // Green
            ];
        } else {
            $options['staticZones'] = [
                [ 'strokeStyle' => "#F03E3E", 'min' => $sections[0], 'max' => $sections[1] ], // Red
                [ 'strokeStyle' => "#fd7e14", 'min' => $sections[1], 'max' => $sections[2] ], // Orange
                [ 'strokeStyle' => "#FFDD00", 'min' => $sections[2], 'max' => $sections[3] ], // Yellow
                [ 'strokeStyle' => "#42e467", 'min' => $sections[3], 'max' => $sections[4] ], // Green light
                [ 'strokeStyle' => "#30B32D", 'min' => $sections[4], 'max' => $sections[5] ], // Green
            ];
        } 

        
        
        return [
            'options' => $options,
            'sections' => $sections,
        ];

    }

    public static function getRentabilityGauges($date, $common_name = '')
    {
        $gauges = [];
        $carbon = Carbon::createFromFormat('Y-m-d', $date);

        $groups = ProfileGroup::profileGroupsWithArchived($carbon->year, $carbon->month);

        if(!$date) {
            $date = Carbon::now()->startOfMOnth()->format('Y-m-d');
        }
        
        foreach($groups as $group_id) {
            $gauges[] = self::getRentabilityGauge($date, $group_id, $common_name);    
        }

        $values_asc = array_column($gauges, 'value');
        array_multisort($values_asc, SORT_ASC, $gauges); 
        

        return $gauges;
    }

    public static function getRentabilityGaugesOfGroup($date, $group_id, $common_name = '')
    {
        $gauges = [];
     
        if(!$date) {
            $date = Carbon::now()->startOfMOnth()->format('Y-m-d');
        }
        
        $gauges[] = self::getRentabilityGauge($date, $group_id, $common_name);
        
        return $gauges;
    }

    private static function getRentabilityGauge($date, $group_id, $common_name)
    {
        $group = ProfileGroup::find($group_id);
            
        $tv = new TopValue();
        $tv->options = '[]';
        
        $options = $tv->getOptions();
        
        if(Carbon::parse($date)->month  == date('m') && Carbon::parse($date)->year  == date('Y')) {
            $tdate = Carbon::parse($date)->day(date('d'))->format('Y-m-d');
        } else {
            $tdate = Carbon::parse($date)->endOfMonth()->format('Y-m-d');
        }

        $options['staticZones'] = [
            [ 'strokeStyle' => "#F03E3E", 'min' => 0, 'max' => 49 ], // Red
            [ 'strokeStyle' => "#fd7e14", 'min' => 50, 'max' => 74 ], // Orange
            [ 'strokeStyle' => "#FFDD00", 'min' => 75, 'max' => 99 ], // Yellow
            [ 'strokeStyle' => "#30B32D", 'min' => 100, 'max' => $group->rentability_max ], // Green
        ];

        $options['staticLabels']['labels'] = [0,50,100, $group->rentability_max];

        $gauge = [
            'id' => 9991155,
            'name' => $common_name != '' ? $common_name : $group->name,
            'value' => (float)AnalyticStat::getRentability($group_id, $date),
            'group_id' => $group_id,
            'place' => 1,
            'unit' => '%',
            'editable' => false,
            'edit_value' => false,
            'activity_id' => 0,
            'key' => 999 * 1000,
            'min_value' => 0,
            'max_value' => $group->rentability_max,
            'round' => 2,
            'cell' => '',
            'is_main' => 0,
            'fixed' => 0,
            'value_type' => 'sum',
            'sections' => $options['staticLabels']['labels'], 
            'options' => $options,
            'diff' =>  AnalyticStat::getRentabilityDiff($group_id, $tdate)
        ];
        
        return $gauge;
    }

    /**
     * table on TOP page -> rentability tab
     */
    public  static function getPivotRentability($year, $month) : array
    {
        $table = [];

        $date = Carbon::createFromDate($year,$month,1);

        $groups = ProfileGroup::whereNotIn('id', [34,58,26])->where([
            ['has_analytics','=',1],
            ['active','=',1]
        ])->orWhere(fn ($query) => $query
            ->whereYear('archived_date','<=', $year)
            ->whereMonth('archived_date','>=', $date->month)
        )->get();

        $r_counts = []; // for count avg rentability on every monht
        $total_row = []; // first row
        for($i=1;$i<=12;$i++) {
            $total_row['l' . $i] = 0;
            $total_row['c' . $i] = 0;
            $total_row['r' . $i] = 0;
            $r_counts[$i] = 0;
        }

        
        $edited_proceeds = TopEditedValue::whereYear('date', $year)->get();

        foreach ($groups as $key => $group) {
            $row = [];

            $row['group_id'] = $group->id;
            $row['name'] = $group->name;

            $row['date'] = $group->created_at->diffInDays();
            $row['date_formatted'] = $group->created_at->format('d.m.Y');
            
            for($i=1;$i<=12;$i++) {

                $xdate = $date->month($i)->format('Y-m-d');
                
                /**
                 * get salary
                 */
                $salary = GroupSalary::where('group_id', $group->id)->where('date', $xdate)->get()->sum('total');

                /**
                 * Temp for DM2
                 * count ФОТ
                 */
                $date_diff = $date->timestamp - Carbon::parse('2022-09-01')->timestamp;
                if($date_diff >= 0 && $group->id == 93) {

                    $data = Salary::salariesTable(0, $xdate, $group->users()->pluck('id')->toArray(), 93);
                    $sum = 0;
        
                    foreach( $data['users'] as $user) {
                        $sum += array_sum(array_values($user['earnings']));
                        if($user['edited_bonus']) {
                            $sum += $user->edited_bonus->amount;
                        } else {
                            $sum += array_sum(array_values($user['bonuses']));
                            $sum += array_sum(array_values($user['awards']));
                            $sum += array_sum(array_values($user['test_bonus']));
                        }

                        $sum += $user['edited_kpi']
                            ? $user['edited_kpi']->amount
                            : $user['kpi'];
                    }
        
                    $salary = $sum;
                }

                // TEMP
                $rentability = 0;
                $proceeds = 0;
                
                $proceeds = AnalyticStat::getProceedsSum($group->id, $xdate);
               
                
                $edited_proceed = $edited_proceeds->where('date', $xdate)
                    ->where('group_id', $group->id)
                    ->first();

                if($edited_proceed) {
                    $proceeds = (int)$edited_proceed->value;
                    $row['ed' . $i] = true;
                } else {
                    $row['ed' . $i] = false;
                }
                
                $rentability = $proceeds > 0 ?  ($proceeds - $salary) / $proceeds : 0;
                if($rentability > 0) $r_counts[$i]++;
                $row['l' . $i] = $proceeds > 0 ? round($proceeds) : '';
                $row['c' . $i] = $salary > 0 ? round($salary) : '';
                $row['r' . $i] = $rentability > 0 ? round($rentability, 1) . '%' : '';
                $row['rc' . $i] = $rentability > 0 ? round($rentability, 1) : -1;
                $total_row['l' . $i] += $proceeds;
                $total_row['c' . $i] += $salary;
                $total_row['r' . $i] += $rentability;


            }

            $table[] = $row;
        }


        
        for($i=1;$i<=12;$i++) { 
            $total_row['l' . $i] = round($total_row['l' . $i]);
            $total_row['c' . $i] = round($total_row['c' . $i]);
            $total_row['r' . $i] = $r_counts[$i] > 0 ? round($total_row['r' . $i] / $r_counts[$i], 1) . '%' : '';
        } 

        array_unshift($table, $total_row);

       
        return $table;
    }

    public  static function getPivotRentabilityOnMonth($year, $month) : array
    {
        $table = [];

        $date = Carbon::createFromDate($year, $month, 1);
        $groups = ProfileGroup::whereNotIn('id', [34,58,26])->where('active', 1)->get();
        
        $edited_proceeds = TopEditedValue::whereYear('date', $year)->get();

        foreach ($groups as $key => $group) {
            $row = [];

            $row['group_id'] = $group->id;
            $row['name']     = $group->name;

            $row['date'] = $group->created_at->diffInDays();
            $row['date_formatted'] = $group->created_at->format('d.m.Y');
            
           
                $xdate = $date->format('Y-m-d');
                
                /**
                 * get salary
                 */
                $salary = GroupSalary::where('group_id', $group->id)->where('date', $xdate)->get()->sum('total');

                /**
                 * Temp for DM2
                 * count ФОТ
                 */
                $date_diff = $date->timestamp - Carbon::parse('2022-09-01')->timestamp;
                if($date_diff >= 0 && $group->id == 93) {

                    $data = Salary::salariesTable(0, $xdate, $group->users()->pluck('id')->toArray(), 93);
                    $sum = 0;
        
                    foreach( $data['users'] as $user) {
                        $sum += array_sum(array_values($user['earnings']));
                        if($user['edited_bonus']) {
                            $sum += $user['edited_bonus'];
                        } else {
                            $sum += array_sum(array_values($user['bonuses']));
                            $sum += array_sum(array_values($user['awards']));
                            $sum += array_sum(array_values($user['test_bonus']));
                        }
                        $sum += $user['edited_kpi'] ? $user['edited_kpi'] : $user['kpi'];
                    }
        
                    $salary =  $sum;
                }

                // TEMP
                $rentability = 0;
                $proceeds = 0;
                
                $proceeds = AnalyticStat::getProceedsSum($group->id, $xdate);
               
                $edited_proceed = $edited_proceeds->where('date', $xdate)
                    ->where('group_id', $group->id)
                    ->first();

                if($edited_proceed) {
                    $proceeds = (int)$edited_proceed->value;
                } 
                
                $rentability = $proceeds > 0 ?  ($proceeds - $salary) / $proceeds : 0;
                //if($rentability > 0) $r_counts[$i]++;

                $row['proceeds'] = $proceeds > 0 ? round($proceeds) : '';
                $row['salary'] = $salary > 0 ? round($salary) : '';
                $row['margin'] = $rentability > 0 ? round($rentability, 1) . '%' : '';
              
                $table[] = $row;
            

        }
       
        return $table;
    }
}
