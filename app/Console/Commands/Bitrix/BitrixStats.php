<?php

namespace App\Console\Commands\Bitrix;

use Illuminate\Console\Command;
use App\Models\Bitrix\Lead;
use App\Api\BitrixOld as Bitrix;
use Carbon\Carbon;
use App\User;
use App\UserDescription;
use App\ProfileGroup;
use App\Classes\Analytics\Recruiting;
use App\Classes\Helpers\Phone;
use App\Service\Department\UserService;
use App\Service\RecruitingActivityService;

class BitrixStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string 
     */
    protected $signature = 'bitrix:stats {date?} {hour?}'; 

    /**
     * The console command description.
     *
     * @var string 
     */
    protected $description = 'Данные с битикс на рекрутинг';

    /**
     * helper service for recruiting
     */
    protected $recruiting_activity;

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
        $this->recruiting_activity = new RecruitingActivityService();
    }

    public $date; 
    public $year;
    public $month;
    public $day;
    public $hour;
    public $bitrix; // Битрикс разрешает 2 запроса в секунду

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if($this->argument('date')) $this->date = $this->argument('date');
        $this->hour = $this->argument('hour') ?? Carbon::now()->setTimezone('Asia/Almaty')->format('H');

        $datex = explode("-", date("Y-m-d", strtotime($this->date)));
        $this->year = $datex[0];
        $this->month = $datex[1];
        $this->day = $datex[2];
        
        $this->bitrix = new Bitrix();
 
        $this->getChatbotData();
        $this->getRecruiterStats();
        $this->inviteTrainees();
    }

    private function getChatbotData() {
        $helper = new Recruiting();

        $created_leads =  $this->bitrix->getLeads(0, '', 'ALL', 'ASC', $this->date, $this->date, 'DATE_CREATE', 0,'segment');
        usleep(1000000); // 1 sec
        $this->line($created_leads['total']);
        $failed_leads =  $this->bitrix->getLeads(0, '', 'F', 'ASC', $this->date, $this->date, 'DATE_MODIFY',0, 'segment');
        usleep(1000000); // 1 sec
        $bot_failed_leads =  $this->bitrix->getLeads(0, '37', 'ALL', 'ASC', $this->date, $this->date, 'DATE_MODIFY', 0,'segment');
        usleep(1000000); // 1 sec

        $converted_leads =  $this->bitrix->getDeals(0, '', 'ASC', $this->date, $this->date, 'DATE_CREATE'); //
        usleep(1000000); //

        $bot_id = '23900'; // Валерия Сидоренко
        $bot_converted_leads =  $this->bitrix->getDeals($bot_id, '', 'ASC', $this->date, $this->date, 'DATE_CREATE'); //
        usleep(1000000); // 1 sec

        $created = array_key_exists('total', $created_leads) ? $created_leads['total'] : 0;
        $failed = array_key_exists('total', $failed_leads) ? $failed_leads['total'] : 0;
        $converted = array_key_exists('total', $converted_leads) ? $converted_leads['total'] : 0;
        $bot_converted = array_key_exists('total', $bot_converted_leads) ? $bot_converted_leads['total'] : 0;
        $bot_failed = array_key_exists('total', $bot_failed_leads) ? $bot_failed_leads['total'] : 0;

        $asi = null;

        if($asi) {
            $data = json_decode($asi->data, true);

            $data[Recruiting::B_CREATED][(int)$this->day] = $created != 0 ? $created : ""; // Созданные лиды
            $data[Recruiting::B_FAILED][(int)$this->day] = $bot_failed != 0 ? $bot_failed : ""; // Забракованы лиды
            $data[Recruiting::B_CONVERTED][(int)$this->day] = $bot_converted != 0 ? $bot_converted : ""; // Сконвертированы лиды

            $asi->data = json_encode($data);
            $asi->save();
        }

        $this->line('------ Chatbot fetched');
    }

    private function getRecruiterStats() {
        
        $group = ProfileGroup::find(Recruiting::GROUP_ID);
        // $users = json_decode($group->users);

        $users = (new UserService)->getEmployees(Recruiting::GROUP_ID, Carbon::parse($this->date)->startOfMonth()->format('Y-m-d')); 
        $users = collect($users)->pluck('id')->toArray();

        $helper = new Recruiting();

        $asis = [];
                        
        foreach ($users as $user_id) {
            
            if(in_array($user_id, [5,18,5032,4192])) continue;

            $admin_user = User::find($user_id);

            if(!$admin_user) {
                $admin_user = User::withTrashed()->find($user_id);
                if(!($admin_user && time() - Carbon::parse($admin_user->deleted_at)->timestamp < 3600 * 24)) {
                    continue;
                }
            }

            $this->recruiting_activity->setDate($this->date);
            $this->recruiting_activity->setUser($user_id);

            $ud = UserDescription::where('bitrix_id', '!=', 0)->where('user_id', $user_id)->first();
            if($ud) {
                $this->bitrix_user = $ud->bitrix_id;
            } else {
                $bitrix_user = $this->bitrix->searchUser($admin_user->email);
                if(!$bitrix_user) continue;

                $this->bitrix_user = $bitrix_user['ID'];
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

            $time_f = '00:00';
            $time_l = '00:00';

            /// Requests to Infinitys.Bitrix.com
            $calls_all_f = $this->bitrix->getCalls($this->bitrix_user, 0, 'ASC', 'all', 0, $this->date, $this->date); // Первый звонок
            usleep(1000000); // 1 sec
            $calls_all_l = $this->bitrix->getCalls($this->bitrix_user, 0 ,'DESC', 'all', 0, $this->date, $this->date); // Последний звонок
            usleep(1000000); // 1 sec
            $calls_out_all = $this->bitrix->getCalls($this->bitrix_user, 1 ,'ASC', 'all', 0, $this->date, $this->date); // Все исходящие
            usleep(1000000); // 1 sec
            $calls_out_success = $this->bitrix->getCalls($this->bitrix_user, 0 ,'ASC', 'all', 10, $this->date, $this->date); // Успешные исх и вх от 10 с 
            usleep(1000000); // 1 sec
            $calls_in = $this->bitrix->getCalls($this->bitrix_user, 2 ,'ASC', '200', 0, $this->date, $this->date); // Усп ходящие
            usleep(1000000); // 1 sec
            $calls_passed =  $this->bitrix->getCalls($this->bitrix_user, 2 ,'ASC', '304', 0, $this->date, $this->date); // Пропущенные
            usleep(1000000); // 1 sec
            $converted_leads =  $this->bitrix->getDeals($this->bitrix_user, '' , 'ASC', $this->date, $this->date, 'DATE_CREATE'); //
            usleep(1000000); // 1 sec
            $applied_leads =  $this->bitrix->getDeals($this->bitrix_user, 'C4:WON', 'ASC', $this->date, $this->date, 'DATE_MODIFY');
            usleep(1000000); // 1 sec

            $this->line('Битрикс 10 запросов сделано');
           
            //////////////////////////////////////////

            if(array_key_exists('total', $calls_all_f) && count($calls_all_f['result']) > 0) {
                $time_f = strtotime($calls_all_f['result'][0]["CALL_START_DATE"]);
                $time_f = date('H:i', $time_f + 3600 * 6);
            }

            if(array_key_exists('total', $calls_all_l) && count($calls_all_l['result']) > 0) {
                $time_l = strtotime($calls_all_l['result'][0]["CALL_START_DATE"]);
                $time_l = date('H:i', $time_l + 3600 * 6);
            } 

            $total_out = array_key_exists('total', $calls_out_all) ? $calls_out_all['total'] : 0;
            $total_out_success = array_key_exists('total', $calls_out_success) ? $calls_out_success['total'] : 0;
            
            $total_in = array_key_exists('total', $calls_in) ? $calls_in['total'] : 0;
            $total_passed = array_key_exists('total', $calls_passed) ? $calls_passed['total'] : 0;

            $converted = array_key_exists('total', $converted_leads) ? $converted_leads['total'] : 0;
            $applied = array_key_exists('total', $applied_leads) ? $applied_leads['total'] : 0;

            //***//// */

            $asi  = $asis->where('employee_id', $user_id)->first(); // ????????????????
            $user = User::withTrashed()->find($user_id);
            
            if(!$user) continue;

            if($asi) {
                
                $data = json_decode($asi->data, true);

                $data[Recruiting::I_CALL_PLAN][(int)$this->day] = $total_out != 0 ? $total_out : ""; // Все звонки исходящие
                $data[Recruiting::I_CALLS_OUT][(int)$this->day] = $total_out_success != 0 ? $total_out_success : ""; // Успешные исходящие от 10 сек
                $data[Recruiting::I_FIRST_CALL][(int)$this->day] = $time_f != '00:00' ? $time_f : ""; // Первый исх
                $data[Recruiting::I_LAST_CALL][(int)$this->day] = $time_l != '00:00' ? $time_l : ""; // Последний исх
                $data[Recruiting::I_CALLS_IN][(int)$this->day] = $total_in != 0 ? $total_in : ""; // Входящие
                $data[Recruiting::I_CALLS_MISSED][(int)$this->day] = $total_passed != 0 ? $total_passed : ""; // Пропущенные
                $data[Recruiting::I_CONVERTED][(int)$this->day] = $converted != 0 ? $converted : ""; // Сконвертированные лиды
                $data[Recruiting::I_APPLIED][(int)$this->day] = $applied != 0 ? $applied : ""; // Приняты на работу
                
                $asi->data = json_encode($data);
                $asi->save(); 
                
            } else {

                $default = $helper->defaultUserTable($user_id);
                $data = $default['records']; 

                $data[Recruiting::I_CALL_PLAN][(int)$this->day] = $total_out != 0 ? $total_out : ""; // Все звонки исходящие
                $data[Recruiting::I_CALLS_OUT][(int)$this->day] = $total_out_success != 0 ? $total_out_success : ""; // Успешные исходящие от 10 сек
                $data[Recruiting::I_FIRST_CALL][(int)$this->day] = $time_f != '00:00' ? $time_f : ""; // Первый исх
                $data[Recruiting::I_LAST_CALL][(int)$this->day] = $time_l != '00:00' ? $time_l : ""; // Последний исх
                $data[Recruiting::I_CALLS_IN][(int)$this->day] = $total_in != 0 ? $total_in : ""; // Входящие
                $data[Recruiting::I_CALLS_MISSED][(int)$this->day] = $total_passed != 0 ? $total_passed : ""; // Пропущенные
                $data[Recruiting::I_CONVERTED][(int)$this->day] = $converted != 0 ? $converted : ""; // Сконвертированные лиды
                $data[Recruiting::I_APPLIED][(int)$this->day] = $applied != 0 ? $applied : ""; // Приняты на работу

                if (!(
                    (int)$total_out == 0 &&
                    (int)$total_out_success == 0 &&
                    (int)$total_in == 0 &&
                    (int)$converted == 0 &&
                    (int)$applied == 0 &&
                    (int)$total_passed == 0 &&
                    $time_f == '00:00' &&
                    $time_l == '00:00'
                )) {
                    // save to userstat
                }
            }

            /** 
             * save 8 activities
             */
            $this->recruiting_activity->save(Recruiting::I_CALL_PLAN,    $total_out != 0         ? $total_out : '');
            $this->recruiting_activity->save(Recruiting::I_CALLS_OUT,    $total_out_success != 0 ? $total_out_success : '');
            $this->recruiting_activity->save(Recruiting::I_FIRST_CALL,   $time_f != '00:00'      ? $time_f : '');
            $this->recruiting_activity->save(Recruiting::I_LAST_CALL,    $time_l != '00:00'      ? $time_l : '');
            $this->recruiting_activity->save(Recruiting::I_CALLS_IN,     $total_in != 0          ? $total_in : '');
            $this->recruiting_activity->save(Recruiting::I_CALLS_MISSED, $total_passed != 0      ? $total_passed : '');
            $this->recruiting_activity->save(Recruiting::I_CONVERTED,    $converted != 0         ? $converted : '');
            $this->recruiting_activity->save(Recruiting::I_APPLIED,      $applied != 0           ? $applied : '');

            $this->line('------' . $user->last_name . ' ' . $user->name . ' ' . $user->email);
        }

    }

    private function inviteTrainees() {
        $leads = Lead::whereNotNull('invite_at')
            ->whereDate('invite_at', '>=', '2022-06-02')
			->whereIn('invited', [1,2])
			->get();
        
        /////////// check group and zoom link existence
        $i = 0;
        dump($leads->count());
        foreach($leads as $lead) {
            $i++;
            dump($i);
            try {
                $group = ProfileGroup::find($lead->invite_group_id);
            } catch (\Exception $e) {
                continue;
            }
            
            if(!$group) {
                continue;
            } 

            // Invite Zoom link
            $deal_id = $this->bitrix->findDeal($lead->lead_id, false);

            if($deal_id == 0) {
                $lead->invited = 4; // Ошибка с битрикс не найдена сделка
                $lead->save();
            } else {
                usleep(1000000); // 1 sec

               // $invite_at = Carbon::parse($lead->invite_at)->hour(6)->minute(30)->second(0); // 6:30 по МСК UTC+3
                $invite_at = Carbon::parse($lead->invite_at)->subHours(3); // 6:30 по МСК UTC+3

                $project = $this->getBitrixProjectField($group->id);

                if($lead->inhouse) { 
                    $this->bitrix->changeDeal($deal_id, [
                        'UF_CRM_617AE1B71F27E' => Phone::getCountryBitrix($lead->phone), // Страна,
                        'UF_CRM_5F61AD2B3241C' => $project, // Проект,
                        'UF_CRM_1633579757' => $invite_at->format('Y-m-d H:i:s'), // Время стажировки (штатный)
                        'STAGE_ID' => 'C4:18', // Стадия сделки: Обучается
                    ]);
                } else { 
                    $this->bitrix->changeDeal($deal_id, [
                        'UF_CRM_617AE1B71F27E' => Phone::getCountryBitrix($lead->phone), // Страна,
                        'UF_CRM_5F61AD2B3241C' => $project, // Проект,
                        //  'UF_CRM_1628091947' => 'bpartners.kz/' . $group->bp_link, // Ссылка на обучение,
                        'UF_CRM_1568000119' => $invite_at->format('Y-m-d H:i:s'), // Время в тексте СМС (для стажировки удаленных)
                        'UF_CRM_1648978687' => $invite_at->addDays(1)->format('Y-m-d H:i:s'), // Время 2го дня стажировки
                        //'STAGE_ID' => 'C4:18', // Стадия сделки: Обучается
                    ]);
                }

                usleep(1000000); // 1 sec

                /////////////
                $lead->invited = 3; // Чтобы указать что это сделал крон
                $lead->save();
            }
        }
    }

    public function getBitrixProjectField(int $group_id) {
        $data = [
            42 => 1720,//=>"Каспи",
            31 => 1722,//=>"Детский Мир",
            61 => 1724,//=>"Tailor Suit",
            53 => 1726,//=>"Евраз",
           // 1728=>"Народный Банк",
            //1730=>"1 группа",
           // 1770=>"Ростелеком",
           // 1794=>"Альфа/МТС",
           // 1892=>"Сертификация",
            46 => 2080,//=>"Тинькофф",
            58 =>  2478,//=>"OZON 1",
            //2480=>"OZON 2",
            57 => 2492,//=>"Хоум Банк",
            //2502=>"Интернет магазин",
            48 => 2606, //=>"Рекрутинг",
        ];
        return array_key_exists($group_id, $data) ? $data[$group_id] : 0;
    }
}
