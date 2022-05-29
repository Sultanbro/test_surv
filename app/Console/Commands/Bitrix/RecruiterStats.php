<?php

namespace App\Console\Commands\Bitrix;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Bitrix\Lead;
use App\External\Bitrix\Bitrix;
use Carbon\Carbon;
use App\User;
use App\UserDescription;
use App\Account;
use App\ProfileGroup;
use App\AnalyticsSettings;
use App\AnalyticsSettingsIndividually;
use App\Classes\Analytics\Recruiting;
use App\Components\TelegramBot;
use App\Trainee;
use App\DayType;
use App\Classes\Helpers\Phone;
use App\Models\Analytics\RecruiterStat;

class RecruiterStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string 
     */
    protected $signature = 'recruiter:stats {count_last_hour?} {hour?} {date?} {user?}'; 

    /**
     * The console command description.
     *
     * @var string 
     */
    protected $description = 'Данные с битрикс на рекрутинг таблицу по часам';

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
        $this->hour = $this->argument('hour') ?? Carbon::now()->setTimezone('Asia/Almaty')->format('H');
        if((int)$this->hour != 0) {
            if($this->argument('count_last_hour') && $this->argument('count_last_hour') == 1) $this->hour = Carbon::now()->setTimezone('Asia/Almaty')->subHour()->format('H');
        } 

        $datex = explode("-", date("Y-m-d", strtotime($this->date)));
        $this->year = $datex[0];
        $this->month = $datex[1];
        $this->day = $datex[2];
        
        $this->bitrix = new Bitrix();

        if($this->argument('user')){
            $this->getRecruiterStats();
        } else {
            $this->saveTotalStats();
            $this->getRecruiterStats();
        }
        
        

    }

    private function getRecruiterStats() {
        
        $group = ProfileGroup::find(Recruiting::GROUP_ID);
        $users = json_decode($group->users);
        

        if($this->argument('user')) $users = [(int)$this->argument('user')];
        
        foreach ($users as $user_id) {
            if(in_array($user_id, [5,18,5032,4192])) continue;
            
            $admin_user = User::find($user_id);

            if(!$admin_user) {
                $admin_user = User::withTrashed()->find($user_id);
                if(!($admin_user && time() - Carbon::parse($admin_user->deactivate_date)->timestamp < 3600 * 24)) {
                    continue;
                }
            }

            $ud = UserDescription::where('bitrix_id', '!=', 0)->where('user_id', $user_id)->first();
            if($ud) {
                $this->bitrix_user = $ud->bitrix_id;
            } else {
                $bitrix_user = $this->bitrix->searchUser($admin_user->email);
                if(!$bitrix_user) continue;

                $this->bitrix_user = $bitrix_user['id'];
                $ud = UserDescription::where('user_id', $user_id)->first();
                if($ud) {
                    
                    $ud->bitrix_id = $this->bitrix_user;
                    $ud->save();
                } else {
                    UserDescription::create([
                        'user_id' => $user_id,
                        'bitrix' => 1,
                        'bitrix_id' => $this->bitrix_user
                    ]);
                }
            }

            /// Requests to Infinitys.Bitrix.com 

            $start_hour = $this->date . 'T' . $this->hour . ':00:00';
            $end_hour = $this->date . 'T' . $this->hour . ':59:59';

            dump($start_hour);
            dump($end_hour);
           
            
            $hourly_dials = $this->bitrix->getCallsAlt($this->bitrix_user, [1,2] ,'ASC',  [200, 486, 603, '603-S'], 0, $start_hour . '+06:00', $end_hour .'+06:00'); // Наборы
            usleep(1000000); // 1 sec

            // dump($start_hour); 
            // dump($end_hour);

            $hourly_calls = $this->bitrix->getCallsAlt($this->bitrix_user, [1,2] ,'ASC', [200, 486, 603, '603-S'], 10, $start_hour. '+06:00', $end_hour. '+06:00'); // Успешные исх и вх от 10 сек
            usleep(1000000); // 1 sec

            $hourly_converted =  $this->bitrix->getDeals($this->bitrix_user, '', 'ASC', $start_hour . '+06:00', $end_hour . '+06:00', 'DATE_CREATE'); // 
            usleep(1000000); // 1 sec
            
            $hourly_leads = $this->bitrix->getLeads($this->bitrix_user, '', 'P', 'ASC', '2010-01-01T00:00:00+06:00', '2050-01-01T00:00:00+06:00', 'DATE_CREATE', 0, 'segment');
            usleep(1000000); // 1 sec
            
            $this->saveRecruiterStats($admin_user, $hourly_dials, $hourly_calls , $hourly_converted, $hourly_leads);

           


        }

        
            

    }

    /**
     * save total values to 0 user
     * @return void
     */
    private function saveTotalStats() {
        
        $start_hour = '2010-01-01T00:00:00';
        $end_hour = '2050-01-01T23:59:59';

        $hourly_leads_all = $this->bitrix->getLeads(0, '', 'P', 'ASC', $start_hour . '+06:00', $end_hour . '+06:00', 'DATE_CREATE', 0, 'segment');
        usleep(1000000); 

        $rs = RecruiterStat::where('user_id', 0)
                ->where('date', $this->date)
                ->first();

        if($rs) {
            $rs->delete();
        }

        $leads = 0;
		if(array_key_exists('result', $hourly_leads_all)) {
			$leads = (int)$hourly_leads_all['total']; 
		} 

        RecruiterStat::create([
            'user_id' => 0,
            'calls' => 0,
            'minutes' => 0,
            'converts' => 0,
            'leads' => (int)$leads,
            'hour' => 0,
            'date' => $this->date,
            'profile' => 0
        ]);

        $this->line('Total fetched');
    }

    /**
     * save hourly stats for recruiter
     * @param User $admin_user
     * @return void
     */
    private function saveRecruiterStats(User $admin_user, $hourly_dials, $hourly_calls , $hourly_converted, $hourly_leads) {

		$total = 0;
		$total_minutes = 0;  
        


		if(array_key_exists('result', $hourly_calls)) {
			$total_seconds = 0;
			$calls = $hourly_calls['result'];
			$total = (int)$hourly_calls['total'];
			foreach ($calls as $key => $call) {
				if($call['CALL_DURATION']) $total_seconds += (int)$call['CALL_DURATION'];
			}
			$total_minutes = (int)number_format($total_seconds / 60, 0);
		}

        $dials = 0;
		if(array_key_exists('result', $hourly_dials)) {
			$dials = (int)$hourly_dials['total']; 
		} 

        $converted = 0;
		if(array_key_exists('result', $hourly_converted)) {
			$converted = (int)$hourly_converted['total']; 
		} 

        $leads = 0;
		if(array_key_exists('result', $hourly_leads)) {
			$leads = (int)$hourly_leads['total']; 
		} 

        $rs = RecruiterStat::where('user_id', $admin_user->id)
            ->where('hour', (int)$this->hour)
            ->where('date', $this->date)
            ->first();

        if($rs) {
            $rs->delete();
        }

     
        if($total == 0 && (int)$total_minutes == 0 && $converted == 0 && $leads == 0 && $dials == 0) {
            
        } else {
            $last_rs = RecruiterStat::where('user_id', $admin_user->id)->orderBy('date', 'desc')->first();
            $profile = $last_rs ? $last_rs->profile : 0;
            RecruiterStat::create([
                'user_id' => $admin_user->id,
                'dials' => $dials,
                'calls' => $total,
                'minutes' => $total_minutes,
                'converts' => $converted,
                'leads' => (int)$leads,
                'hour' => (int)$this->hour,
                'date' => $this->date,
                'profile' => $profile,
            ]);
        }
        
        $this->line('------' . $admin_user->LAST_NAME . ' ' . $admin_user->NAME . ' ' . $admin_user->email);
    }

    
}
