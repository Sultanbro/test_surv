<?php

namespace App\Console\Commands;

use App\Api\HeadHunter;
use App\Models\Admin\Headhunter\Negotiation;
use App\Models\Admin\Headhunter\Vacancy;
use Illuminate\Console\Command;
use App\Api\BitrixOld as Bitrix;
use Carbon\Carbon;
use App\Classes\Helpers\Phone;
use App\Models\Bitrix\Lead;
use App\Models\Admin\History;
use Illuminate\Support\Str;

class HeadhunterNegotiations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'headhunter:fetch {stage?}';

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

        $this->bitrix = new Bitrix('hh');
        $this->hh = new HeadHunter();
        $this->date = Carbon::now()->subDays(7)->format('Y-m-d');
    }

    protected $hh;

    protected $date;

    protected Bitrix $bitrix;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $stage = $this->argument('stage');
        
        $this->line("Start from " . $this->date);

        if($stage == 0) $this->updateVacancies();
        if($stage == 1) $this->updateNegotiations();
        if($stage == 2) $this->getPhonesByResume();
        if($stage == 3) $this->createLeadsOnBitrix();
    }

    public function createLeadsOnBitrix() : void
    {
        $negotiations = Negotiation::where('has_updated', 1)
            ->where('lead_id', 0)
            ->where('phone', '!=', '')
            ->where('phone', '!=', 'null')
            ->where('from', '=', HeadHunter::FROM_STATUS)
            ->get()
            ->take(5);

        $this->line('createLeadsOnBitrix: '. $negotiations->count());

        foreach($negotiations as $n) {
            $phone = Phone::normalize($n->phone);

            $leads = Lead::where('phone', $phone)
                ->where('created_at', '>', Carbon::now()->subDays(7))
                ->whereNotIn('status', ['LOSE'])
                ->get();

            $leadId = null;

            if($leads->count() > 0) {
                if($n->lead_id == 0) {
                    $n->lead_id = $leads->first()->lead_id;
                    $n->save();

                    History::system('Дубликат hh.ru', [
                        'lead_id' => $leads->first()->lead_id,
                        'phone' => $n->phone, 
                        'negotiation_id' => $n->negotiation_id,
                    ]);
                }
            } else {
                $lead_id = $this->createLead($n);
                $leadId = $lead_id;
                $this->line('Lead created: '. $lead_id);

                History::system('Создание лида hh.ru', [
                    'lead_id' => $lead_id,
                    'phone' => $n->phone,
                    'negotiation_id' => $n->negotiation_id,
                ]);

                usleep(4000000); // 4 sec
            }

            try {
                $this->hh->put('/negotiations/consider/'. $n->negotiation_id);
            } catch(\Exception $e) {
                History::system($e->getCode() == 403 ? 'Отклик архивирован hh.ru' : 'Ошибка hh.ru', [
                    'lead_id' => $leads->first()->lead_id ?? $leadId,
                    'phone' => $n->phone, 
                    'negotiation_id' => $n->negotiation_id,
                ]);
            }
        }
    }

    public function getPhonesByResume() : void
    {
        $negotiations = Negotiation::whereDate('time', '>=', $this->date)
            ->where('has_updated', 1)
            ->where('lead_id', 0)
            ->where('phone', '')
            ->where('phone', '!=', 'null')
            ->where('resume_id', '!=', '')
            ->where('from',HeadHunter::FROM_STATUS)
            ->get();

        $this->line('getPhonesByResume: '. $negotiations->count());

        foreach($negotiations as $n) {

            $this->line('resume: '. $n->resume_id);

            try {
                $resume = $this->hh->getResume($n->resume_id);
            } catch (\Exception $e) {
                History::system('Ошибка hh.ru: резюме', [
                    'error' => $e,
                    'resume' => $n->resume_id,
                ]);
                $this->line('Ошибка hh.ru: '.$e);
                continue;
            }
            
            $phone = $this->hh->getPhone($resume->contact);
            $neg = Negotiation::where('id', '!=', $n->id)->where('phone', $phone)->where('time', '>', Carbon::now()->subDays(3))->first();

            if($phone == '') $phone = 'null';
            if($neg) {
                $n->phone = $phone;
                $n->save();
            } else {
                $n->phone = $phone;
                $n->save();
            }
        }
    }

    public function createLead(Negotiation $negotiation)
    {
        $hash = md5(uniqid().mt_rand());
        $countries = [
            'KZ' => '2330',
            'RU' => '2332',
            'KG' => '2334',
            'UZ' => '2336',
            'UA' => '2388',
            'BY' => '2390',
            'UN' => '0', // Неизвестно
        ];

        try {

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
                'UF_CRM_1624530685082' => config('services.intellect.time_link') . $hash, // Ссылка для офисных кандидатов
                'UF_CRM_1624530730434' => config('services.intellect.contract_link') . $hash, // Ссылка для удаленных кандидатов
                "PHONE"=> [["VALUE" => $negotiation->phone, "VALUE_TYPE" => "WORK"]],
                "UF_CRM_1658397129" => $vac ? $vac->city : '', // город
                "UF_CRM_1679562806674" => 'https://hh.ru/resume/'. $negotiation->resume_id,
            ]);
            
            // bitrix_leads
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
                $lead = Lead::create([
                    'lead_id' => $lead_id['result'],
                    'name' => $negotiation->name,
                    'phone' => $negotiation->phone,
                    'status' => 'NEW',
                    'segment' => Lead::getSegmentAlt(Headhunter::SEGMENT),
                    'hash' => $hash
                ]);
            }
            
            $negotiation->lead_id = $lead_id['result'];
            $negotiation->save();

            return $lead_id['result']; 
        } catch(\Exception $e) {
            // save logs
            return 'НЕ СОЗДАЛСЯ'; 
        }
    }

    public function updateNegotiations() : void
    {
        $vacancies = Vacancy::where('status', Vacancy::OPEN)
            ->whereIn('manager_id', HeadHunter::MANAGERS)
            ->get();

        foreach($vacancies as $vacancy) {
            $this->updateNegotiationsOnVacancy($vacancy);
        }
    }

    private function updateNegotiationsOnVacancy($vacancy) : void
    {

        $negotiations = $this->hh->getNegotiations($vacancy->vacancy_id, $this->date);

        $this->line('updateNegotiationsOnVacancy: '. count($negotiations));

        foreach ($negotiations as $key => $hh_neg) {
            $neg = Negotiation::where('negotiation_id', $hh_neg->id)->first();
            
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
                $neg->from = HeadHunter::FROM_STATUS;
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
                    'from' => HeadHunter::FROM_STATUS,
                ]);
            }
        }
    }

    public function updateVacancies() : void
    {
        $vacancies = $this->hh->getVacancies();

        $this->line('updateVacancies: '. count($vacancies));
       
        foreach($vacancies as $vacancy) {
           
            $vac = Vacancy::where('vacancy_id', $vacancy->id)->first();
            
            $hh_vacancy = $this->hh->getVacancy($vacancy->id);

           
            
            if($hh_vacancy) {

                try {
                    $manager_id = 7792661;
                } catch(\Exception $e) {
                    // save logs
                }
                
                if($this->vacancyNameHasNotWords($hh_vacancy->name, [
                    'Оператор', 'удаленно', 'удалённо'
                ])) continue;

                $this->line('vacancy: #'. $vacancy->id .  ' - ' . $hh_vacancy->name);
                
                $status = $hh_vacancy->type->id == 'open' ? Vacancy::OPEN : Vacancy::CLOSED;
                $city = $hh_vacancy->area->name ? $hh_vacancy->area->name : 'Не указан';

                if(!$vac) {
                    Vacancy::create([
                        'vacancy_id' => $hh_vacancy->id,
                        'title' => $hh_vacancy->name,
                        'manager_id' => $manager_id,
                        'city' => $city,
                        'status' => $status,
                        'from' => HeadHunter::FROM_STATUS
                    ]);
                } else {
                    $vac->title = $hh_vacancy->name;
                    $vac->manager_id = $manager_id;
                    $vac->status = $status;
                    $vac->city = $city;
                    $vac->from = HeadHunter::FROM_STATUS;
                    $vac->save();
                }
            }   
            
        }
    }

    private function vacancyNameHasNotWords(String $name, array $words) : bool
    {
        foreach ($words as $word) {
            if (Str::contains(Str::lower($name), Str::lower($word))) {
                return false;
            }
        }
        return true;
    }
}