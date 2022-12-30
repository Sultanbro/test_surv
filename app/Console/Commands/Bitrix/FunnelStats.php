<?php

namespace App\Console\Commands\Bitrix;

use Illuminate\Console\Command;
use App\Models\Bitrix\Lead;
use App\Api\BitrixOld as Bitrix;
use Carbon\Carbon;
use App\User;
use App\ProfileGroup;
use App\AnalyticsSettings;
use App\Classes\Analytics\Recruiting;
use App\DayType;
use App\Classes\Analytics\FunnelTable;

class FunnelStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string 
     */
    protected $signature = 'bitrix:funnel:stats {date?} {segment?}'; 

    /**
     * The console command description.
     *
     * @var string 
     */
    protected $description = 'Данные с битрикс на воронку по сегментам';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->date = date('Y-m-d'); 
        $this->year = date('Y');
        $this->month = date('m');
        $this->day = date('d');
        $this->bitrix = new Bitrix();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */


    public $date; 
    public $year;
    public $month;
    public $day;
    public $hour;
    public $bitrix; // Битрикс разрешает 2 запроса в секунду

    public function handle()
    {
        if($this->argument('date')) $this->date = $this->argument('date');
        $this->hour = Carbon::now()->setTimezone('Asia/Almaty')->format('H');

        $this->bitrix = new Bitrix();

        if($this->argument('date')) {
            $this->date = $this->argument('date');

            $datex = explode("-", $this->date);
            $this->year = $datex[0];
            $this->month = $datex[1];
            $this->day = $datex[2];
            
            $this->getData();
        
            
        } else {
            $dates = [
                Carbon::now()->format('Y-m-d'),
                Carbon::now()->subDays(7)->format('Y-m-d'),
                Carbon::now()->subDays(14)->format('Y-m-d'),
                Carbon::now()->subDays(21)->format('Y-m-d'),
                Carbon::now()->subDays(28)->format('Y-m-d'),
                Carbon::now()->subDays(35)->format('Y-m-d'),
                Carbon::now()->subDays(42)->format('Y-m-d'),
                Carbon::now()->subDays(49)->format('Y-m-d'),
                Carbon::now()->subDays(56)->format('Y-m-d'),
                Carbon::now()->subDays(63)->format('Y-m-d'),
            ];
    
            foreach($dates as $d) {
                $this->date = $d;

                $datex = explode("-", $d);
                $this->year = $datex[0];
                $this->month = $datex[1];
                $this->day = $datex[2];

                
                $this->getData();
                
            }
        }
        

      
        
    }

    private function getData() {
     
        $group = ProfileGroup::find(Recruiting::GROUP_ID);

        /// Requests to Infinitys.Bitrix.com 

        if($this->argument('segment')) {
            if($this->argument('segment') == 'alina')  $segments = [Lead::SEGMENT_ALINA =>'alina'];
            if($this->argument('segment') == 'hh')  $segments = [Lead::SEGMENT_HH =>'hh'];
            if($this->argument('segment') == 'insta')  $segments = [Lead::SEGMENT_TARGET =>'insta'];
            if($this->argument('segment') == 'saltanat')  $segments = [Lead::SEGMENT_SALTANAT =>'saltanat'];
            if($this->argument('segment') == 'akzhol')  $segments = [Lead::SEGMENT_AKZHOL =>'akzhol'];
            if($this->argument('segment') == 'darkhan')  $segments = [Lead::SEGMENT_DARKHAN =>'darkhan'];
        } else {
            $segments = [
                Lead::SEGMENT_TARGET => 'insta',
                Lead::SEGMENT_HH => 'hh',
            ];
        }
        

        foreach($segments as $key => $segment) {
            $as = AnalyticsSettings::where('date', Carbon::parse($this->date)->startOfMonth()->format('Y-m-d'))
                ->where('group_id', Recruiting::GROUP_ID)
                ->where('type', $segment)
                ->first();
                
            /******* */
            $fetch = $this->fetch($segment, $key);
            dump($fetch);

            if($this->month == 1) {
                $weekOfYear =  Carbon::parse($this->date)->weekOfYear;
                if($weekOfYear == 52) {
                    $week_number = 1;
                } else {
                    $week_number = $weekOfYear + 1;
                }
            } else {
                $start = Carbon::parse($this->date)->startOfMonth()->weekOfYear;
                $week_number = Carbon::parse($this->date)->weekOfYear - $start + 1;
            }
            /************ */
 
            if($as) {
                $data = $as->data;
                $this->line('Found AS: '. $segment);
            } else {
                $as = AnalyticsSettings::create([
                    'date' => Carbon::parse($this->date)->startOfMonth()->format('Y-m-d'),
                    'group_id' => Recruiting::GROUP_ID,
                    'type' => $segment,
                    'data' => FunnelTable::getTemplate($segment)
                ]);
                
                $data = $as->data;
                $this->line('Created AS '. $segment); 
            }
          
            if($segment == 'hh') {
                $data[1][$week_number] = $fetch['created'];
                $data[2][$week_number] = $fetch['converted'];
                $data[3][$week_number] = $fetch['first_day'];
                $data[4][$week_number] = $fetch['second_day'];
                $data[8][$week_number] = $fetch['hired'];
            }

            if($segment == 'insta') {
                $data[4][$week_number] = $fetch['created'];
                $data[5][$week_number] = $fetch['converted'];
                $data[6][$week_number] = $fetch['first_day'];
                $data[7][$week_number] = $fetch['second_day'];
                $data[12][$week_number] = $fetch['hired'];
            }

            if(in_array($segment, ['alina', 'saltanat', 'akzhol', 'darkhan'])) {
                $data[0][$week_number] = $fetch['converted'];
                $data[1][$week_number] = $fetch['first_day'];
                $data[2][$week_number] = $fetch['second_day'];
                $data[3][$week_number] = $fetch['training'];
                $data[5][$week_number] = $fetch['hired'];
            }

            /** save */
            $as->data = $data;
            $as->save();
            $this->line('Segment fetched: '. $segment);

        }    

    }

    public function fetch($segment, $segment_id)
    {
        

        $not_this_month = Carbon::parse($this->date)->endOfWeek()->month != Carbon::parse($this->date)->month;
        if(Carbon::parse($this->date)->endOfWeek()->day >= 7) {
            $start = Carbon::parse($this->date)->startOfWeek()->format('Y-m-d');
        } else if($not_this_month) {
            $start = Carbon::parse($this->date)->startOfWeek()->format('Y-m-d');
        } else {
            $start = Carbon::parse($this->date)->startOfMonth()->format('Y-m-d');
        }
        
        

        if($not_this_month) {
            $end = Carbon::parse($this->date)->endOfMonth()->format('Y-m-d');
        } else {
            $end = Carbon::parse($this->date)->endOfWeek()->format('Y-m-d');
        }
        
        dump($start, $end);
        
        $start_hour = $start . 'T00:00:00';
        $end_hour = $end . 'T23:59:59'; 
        
        if(in_array($segment, ['hh', 'insta'])) {
            $created = $this->bitrix->getLeads(0, '', 'ALL', 'ASC', $start_hour . '+06:00', $end_hour . '+06:00', 'DATE_CREATE', 0, $segment);
                usleep(1000000); // 1 sec
                $created = array_key_exists('total', $created) ? $created['total'] : 0;
        } else {
            $created = 0;
        }
        dump($created);
        $converted = $this->bitrix->getDeals(0, '', 'ASC', $start_hour . '+06:00', $end_hour . '+06:00', 'DATE_CREATE', $segment);
            usleep(1000000); // 1 sec
            $converted = array_key_exists('total', $converted) ? $converted['total'] : 0;
        dump($converted);
       
        
        $hired_users = User::withTrashed()
            ->with('user_description')
            ->whereHas('user_description', function ($query) {
                $query->where('is_trainee', 0);
            })
            ->with('lead')
            ->whereHas('lead', function ($query) use ($segment_id, $start, $end){
                $query->where('segment', $segment_id)
                    ->whereDate('created_at', '>=', $start)
                    ->whereDate('created_at', '<=', $end);
            })
            ->get(['id','full_time']);

        dump('hired  users   '. $hired_users->count());

        $hired = 0;
        foreach($hired_users as $h) {
             $hired += $h->full_time == 1 ? 1 : 0.5;             
        }
        
        dump('hired  total   '. $hired);
        
        $first_day_users  = 0;
        $second_day_users = 0;
        $training = 0;

        $leads = Lead::where('segment', $segment_id)
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('invite_at')
            ->get();
        
        foreach($leads as $lead) {
         
            if($lead->user_id != 0) {
                $user = User::where('id', $lead->user_id)->first();

                if($user) {
                    $daytype = DayType::whereIn('type', [5,7])
                        ->where('date', Carbon::parse($lead->invite_at)->format('Y-m-d'))
                        ->where('user_id', $user->id)
                        ->first();
                    //dump($daytype ? $daytype->user_id : '');
                    if($daytype) $first_day_users++;

                    $daytype = DayType::whereIn('type', [5,7])
                        ->where('date', '>=', Carbon::parse($lead->day_second)->format('Y-m-d'))
                        ->where('user_id', $user->id)
                        ->first();

                    if($daytype) {
                        $second_day_users++;
                    } 
                     
                    $today = Carbon::now();
                    if($today->dayOfWeek == 6) $today = $today->subDays(1);
                    if($today->dayOfWeek == 0) $today = $today->subDays(2);

                    $daytypex = DayType::whereIn('type', [5,7])
                        ->where('date', $today->format('Y-m-d'))
                        ->where('user_id', $user->id)
                        ->first();
                    
                    if($daytypex) $training++;
                }

            }
            
            
        }
        
        dump($first_day_users);
        dump($second_day_users);
        dump($training);

        // Костыль удалить после 31 января 2022
        if($segment == 'darkhan' && $start == '2021-12-20') {
            $converted = $converted >= 17 ? $converted - 17 : $converted;
        }
        // конец костыля
        return [
            'created' => $created,
            'converted' => $converted,
            'hired' => $hired,
            'first_day' => $first_day_users,
            'second_day' => $second_day_users,
            'training' => $training,
        ];
    }
}
