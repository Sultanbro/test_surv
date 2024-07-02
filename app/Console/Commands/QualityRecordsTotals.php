<?php

namespace App\Console\Commands;

use App\ProfileGroup;
use App\User;
use Carbon\Carbon;
use App\QualityRecord;
use App\QualityRecordWeeklyStat;
use App\QualityRecordMonthlyStat;
use Illuminate\Console\Command;
use App\Models\Analytics\Activity;
use App\Models\Analytics\UserStat;

class QualityRecordsTotals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quality:totals {month?} {year?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Расчет недельных и месячных средних значений по контролю качества в Каспи';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {   
        $year = $this->argument('year');
        $month = $this->argument('month');

        if(!$year) $year = date('Y');
        if(!$month) $month = date('m');

        $this->line('Start of week counting');
        $this->weekly($month, $year);
        $this->line('End');

        $this->line('Start of month counting');
        $this->monthly($year);
        $this->line('End');    


           
    }


    private function weekly(int $month, int $year) {
        $daysInMonth = Carbon::createFromFormat('m-Y', $month . '-' . $year)->daysInMonth;
        
        $dgroups = QualityRecord::whereYear('listened_on', $year)
            ->whereMonth('listened_on', $month)
            ->where('total', '>', 0)
            ->get(['group_id'])->pluck('group_id')->toArray();
        $dgroups = array_unique($dgroups);


        foreach ($dgroups as $dgroup_id) {
            for($d=1;$d<=$daysInMonth;$d++) {
                $records = QualityRecord::whereYear('listened_on', $year)
                                            ->whereMonth('listened_on', $month)
                                            ->whereDay('listened_on', $d)
                                            ->where('group_id', $dgroup_id)
                                            ->where('total', '>', 0)->get()->groupBy('employee_id'); // Записи на выбранный день $d в месяце
    
                foreach ($records as $key => $_records) {
    
                    $count = 0; // Колво элементов не равных нулю
                    $total = 0; // Сумма элементов
                    
                    foreach($_records as $_record) {
                        if($_record->total != 0){
                            $total += $_record->total;
                            $count++;
                        }
                    }
    
                    if($count != 0){ // Проверка деления на ноль
                        $avg = round($total / $count);
                    } else {
                        $avg = 0;
                    }
    
                    // Если ср. знач не 0, то записываем в таблицу
                    if($avg != 0) {
                        // Проверка существ записи
                        $qrws = QualityRecordWeeklyStat::where([
                            'day' => $d,
                            'month' => $month,
                            'year' => $year,
                            'user_id' => $key,
                            'group_id' => $dgroup_id
                        ])->first();
                        
                        if($qrws) { // существует
                            $qrws->total = $avg;
                            $qrws->save();
                        } else { // не существует
                            QualityRecordWeeklyStat::create([
                                'day' => $d,
                                'month' => $month,
                                'year' => $year,
                                'user_id' => $key,
                                'total' => $avg,
                                'group_id' => $dgroup_id
                            ]);
                        }
                        
                        /** */
                        UserStat::saveQuality([
                            'date'     => Carbon::createFromDate($year, $month, $d)->format('Y-m-d'),
                            'user_id'  => $key,
                            'value'    => $avg,
                            'group_id' => $dgroup_id,
                        ]);
                    }
                    
                }
            }
        }
        
    }

    /**
     * count monthly stats
     */
    private function monthly(int $year)
    {

        for($m=1;$m<=12;$m++) {

            $wgroups = QualityRecordWeeklyStat::where('year', $year)
                ->where('month', $m)->get(['group_id'])->pluck('group_id')->toArray();
            $wgroups = array_unique($wgroups); // группы у которых уже есть ежедневные записи

            foreach($wgroups as $wgroup_id) {
                $weeklyRecords = QualityRecordWeeklyStat::where('year', $year)
                    ->where('month', $m)
                    ->where('group_id', $wgroup_id)
                    ->get()
                    ->groupBy('user_id'); // Записи на выбранный день $d в месяце
                
                foreach ($weeklyRecords as $key => $_records) {
                    $count = 0; // Колво элементов не равных нулю
                    $total = 0; // Сумма элементов
                    
                    foreach($_records as $_record) {
                        if($_record->total != 0){
                            $total += $_record->total;
                            $count++;
                        }
                    }

                    if($count != 0){ // Проверка деления на ноль
                        $avg = round($total / $count);
                    } else {
                        $avg = 0;
                    }

                    // Если ср. знач не 0, то записываем в таблицу
                    if($avg != 0) {
                        // Проверка существ записи
                        $qrws = QualityRecordMonthlyStat::where([
                            'month' => $m,
                            'year' => $year,
                            'user_id' => $key,
                            'group_id' => $wgroup_id
                        ])->first();
                        
                        if($qrws) { // существует
                            $qrws->total = $avg;
                            $qrws->group_id = $wgroup_id;
                            $qrws->save();
                        } else { // не существует
                            QualityRecordMonthlyStat::create([
                                'month' => $m,
                                'year' => $year,
                                'user_id' => $key,
                                'total' => $avg,
                                'group_id' => $wgroup_id,
                            ]);
                        }
                        
                    }
                }
            }
            
            
        }
        
    }
    

}
