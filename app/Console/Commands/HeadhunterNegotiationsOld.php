<?php

namespace App\Console\Commands;

use App\User;
use App\External\HeadHunter\HeadHunter;
use App\Models\Admin\Headhunter\Negotiation;
use App\Models\Admin\Headhunter\Vacancy;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\External\Bitrix\Bitrix;
use App\Http\Controllers\IntellectController;
use Carbon\Carbon;
use App\Classes\Helpers\Phone;
use App\Models\Bitrix\Lead;

class HeadhunterNegotiationsOld extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'headhunter:fetchold   {stage?} {vacancy_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Берет отклики с HEADHUNTER и отправляет их в БИТРИКС';

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
     * 
     */
    protected $hh;

    /**
     * 
     */
    protected $bitrix;
    protected $vacancy_id;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
       
       // return 'Stopped manually';
        $this->bitrix = new Bitrix();
        $this->hh = new HeadHunter();

     
        //$this->updateVacancies();
        
        $this->vacancy_id = $this->argument('vacancy_id');
        $stage = $this->argument('stage');


        if($this->argument('vacancy_id')) {
            $vacancies = Vacancy::where([
                'status' => Vacancy::OPEN,
                'manager_id' => HeadHunter::MANAGER_ID,
               // 'vacancy_id' => $this->vacancy_id,
            ])->get();
        } else {
            $vacancies = Vacancy::where([
                'status' => Vacancy::OPEN,
                'manager_id' => HeadHunter::MANAGER_ID,
            ])->get();
        }
         
        if($stage == 1) {
            foreach($vacancies as $key => $vacancy) {
                $this->updateNegotiations($vacancy);
            }
        }
            
   
        
        if($stage == 2) $this->getPhonesByResume();


        if($stage == 3) $this->createLeadsOnBitrix();
   
       


    }

    public function createLeadsOnBitrix() {

        if($this->argument('vacancy_id')) {
            $negotiations = Negotiation::where('vacancy_id', $this->vacancy_id)->where('has_updated', 1)->where('lead_id', 0)->where('phone', '!=', '')->where('phone', '!=', 'null')->get()->take(150);
        } else {
            $negotiations = Negotiation::where('has_updated', 1)->where('lead_id', 0)->where('phone', '!=', '')->where('phone', '!=', 'null')->get()->take(150);
        }   
        
        $this->line('createLeadsOnBitrix: '. $negotiations->count());
        
        foreach($negotiations as $key => $n) {
            $phone = Phone::normalize($n->phone);
            
            $leads = Lead::where('phone', $phone)
                ->where('created_at', '>', $n->created_at->subDays(7))
                ->whereNotIn('status', ['LOSE'])
                ->get();
                
            if($leads->count() > 1) { 
                $this->line('Lead is dublicate' . $n->negotiation_id);
                if($n->lead_id == 0 && $leads->first()) {
                    $n->lead_id = $leads->first()->lead_id; 
                    $n->save();
                } else {
                    $n->lead_id = 100500;
                    $n->save();
                }
            } else {
                $lead_id = $this->createLead($n);
                $this->line('Lead created: '. $lead_id . '   ' . $key);
                $this->hh->put('/negotiations/consider/'. $n->negotiation_id);
                usleep(15 * 1000000); // 20 sec
            }
            
            
        }
    }

    public function getPhonesByResume() {

        if($this->argument('vacancy_id')) {
            $negotiations = Negotiation::where('has_updated', 1)->where('vacancy_id', $this->vacancy_id)->where('lead_id', 0)->where('phone', '')->where('phone', '!=', 'null')->where('resume_id', '!=', '')->get()->take(1000);
        } else {
            $negotiations = Negotiation::where('has_updated', 1)->where('lead_id', 0)->where('phone', '')->where('resume_id', '!=', '')->get()->take(1000);
        }   
        


        $this->line('getPhonesByResume: '. $negotiations->count());
        foreach($negotiations as $key => $n) {


            $this->line('resume: '. $n->resume_id);
            usleep(500000); 
            $resume = $this->hh->getResume($n->resume_id);
          
            $phone = $this->hh->getPhone($resume->contact);
            $neg = Negotiation::where('id', '!=', $n->id)->where('phone', $phone)->where('time', '>', $n->created_at->subDays(3))->first();

            if($phone == '') $phone = 'null';

            if($neg) {
                //$n->lead_id = $neg->lead_id;
                $n->phone = $phone;
                $n->save();
            } else {
                $n->phone = $phone;
                $n->save();
            }

            
        }
    }

    public function createLead(Negotiation $negotiation) {

        $ic = new IntellectController();
        $hash = md5(uniqid().mt_rand());
        
        //Phone::getCountry('+998 (99)278-71sd380')

        try {

            $countries = [
                'KZ' => '2330',
                'RU' => '2332',
                'KG' => '2334',
                'UZ' => '2336',
                'UA' => '2388',
                'BY' => '2390',
                'UN' => '0', // Неизвестно
            ];

            $vac = Vacancy::where('vacancy_id', $negotiation->vacancy_id)->first();
            
            $title = "Удаленный " . $negotiation->name . ' : hh.ru';
            if($vac && $vac->city == 'Шымкент') {
                $title = "inhouse " . $negotiation->name . ' : hh.ru';
            }
            
            $lead_id = $this->bitrix->createLead([
                "TITLE" => $title, 
                "NAME" => $negotiation->name,  
                'UF_CRM_1498210379' => HeadHunter::SEGMENT, // сегмент
                "UF_CRM_1635442762" => $countries[Phone::getCountry($negotiation->phone)], //страна
                "ASSIGNED_BY_ID" => 23900, // Валерия Сидоренко
                "UF_CRM_1635487718862" => 'https://wa.me/+' . Phone::normalize($negotiation->phone), // Ватсап линк 
                'UF_CRM_1624530685082' => $ic->time_link . $hash, // Ссылка для офисных кандидатов
                'UF_CRM_1624530730434' => $ic->contract_link . $hash, // Ссылка для удаленных кандидатов
                "PHONE"=> [["VALUE" => $negotiation->phone, "VALUE_TYPE" => "WORK"]]
            ]);
    
            
    
            $negotiation->lead_id = $lead_id['result'];
            $negotiation->save();

            $lead = Lead::where('lead_id', $lead_id['result'])->latest()->first();
            if($lead) {
                $lead->update([
                    'name' => $negotiation->name,
                    'phone' => $negotiation->phone,
                    'status' => 'NEW',
                    'segment' => Lead::getSegmentAlt(Headhunter::SEGMENT),
                    'hash' => $hash
                ]);
            } else {
                Lead::create([
                    'lead_id' => $lead_id['result'],
                    'name' => $negotiation->name,
                    'phone' => $negotiation->phone,
                    'status' => 'NEW',
                    'segment' => Lead::getSegmentAlt(Headhunter::SEGMENT),
                    'hash' => $hash
                ]);
            }

            return $lead_id['result']; 
        } catch(\Exception $e) {
            // save logs
            //dd($e);
            return 'НЕ СОЗДАЛСЯ'; 
        }

        
    }

    public function updateNegotiations($vacancy) {
        $negotiations = $this->hh->getNegotiations($vacancy->vacancy_id);

        $this->line('updateNegotiations: '. count($negotiations));

        foreach ($negotiations as $key => $hh_neg) {
            $neg = Negotiation::where('negotiation_id', $hh_neg->id)->first();
            dump($hh_neg->created_at);
            $time = $hh_neg->created_at;
            $time[10] = ' ';
            $time = Carbon::parse($time)->setTimezone('Asia/Almaty');

            $resume_id = $hh_neg->resume ? $hh_neg->resume->id : '';
            $name = 'Соискатель';
            if($hh_neg->resume) {
                $name = $hh_neg->resume->first_name ? $hh_neg->resume->first_name : '';
                $name .= $hh_neg->resume->last_name ? ' ' . $hh_neg->resume->last_name : '';
            }

            if($neg) {
                $neg->has_updated = $hh_neg->has_updates;
                $neg->time = $time;
                $neg->resume_id = $resume_id;
                $neg->name = $name;
                $neg->save();
            } else {

                Negotiation::create([
                    'vacancy_id' => $vacancy->vacancy_id,
                    'negotiation_id' => $hh_neg->id,
                    'lead_id' => 0,
                    'has_updated' => $hh_neg->has_updates,
                    'time' => $time,
                    'phone' => '',
                    'name' => $name,
                    'resume_id' => $resume_id,
                ]);
            }
        }
    }

    public function updateVacancies() {
        $vacancies = $this->hh->getVacancies();

        $last_vac = Vacancy::orderBy('updated_at', 'desc')->first();
        
        // if(!((int)Carbon::now()->format('H') == 0 && in_array((int)Carbon::now()->format('i'), [0,1,2]))) {
        //     $this->line('updateVacancies: is not the time');
        //     return null;
        // }

        // if(!((int)Carbon::now()->format('H') == 12 && in_array((int)Carbon::now()->format('i'), [0,1,2]))) {
        //     $this->line('updateVacancies: is not the time');
        //     return null;
        // }

        $this->line('updateVacancies: '. count($vacancies));

        foreach($vacancies as $vacancy) {
            $vac = Vacancy::where('vacancy_id', $vacancy->id)->first();
            
            $hh_vacancy = $this->hh->getVacancy($vacancy->id);

            $this->line('vacancy: '. $vacancy->id);
            if($hh_vacancy) {

                try {
                    $manager_id = $hh_vacancy->manager->id;
                } catch(\Exception $e) {
                    // save logs
                    //dd($hh_vacancy);
                }
                
                $status = $hh_vacancy->type->id;
                
                if($status == 'open') {
                    $status = Vacancy::OPEN;
                } else {
                    $status = Vacancy::CLOSED;
                }

                $city = $hh_vacancy->area->name ? $hh_vacancy->area->name : 'Не указан';
                dump($city);

               
                    if(!$vac) {
                        Vacancy::create([
                            'vacancy_id' => $hh_vacancy->id,
                            'title' => $hh_vacancy->name,
                            'manager_id' => $manager_id,
                            'city' => $city,
                            'status' => $status,
                        ]);
                    } else {
                        $vac->title = $hh_vacancy->name;
                        $vac->manager_id = $manager_id;
                        $vac->status = $status;
                        $vac->city = $city;
                        $vac->save();
                    }
              
                
            }   
            
        }
        


    }
}
