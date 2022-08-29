<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Components\TelegramBot as TB;
use Illuminate\Http\Request;
use Auth;
use App\User;
use App\UserNotification;
use App\UserDescription;
use App\Trainee;
use App\Classes\Helpers\Phone;
use App\Models\Bitrix\Lead;
use App\External\Bitrix\Bitrix;
use App\Models\Admin\History;

class IntellectController extends Controller {

	/**
	*	Входные функции
    *
    *   index           	=> Вебхук с битрикса
	*	save				=> Вебхук с Intellect
    *
	*	updateFields	=> Изменения полей лида в битриксе
    *   curl_get            => get запрос 
    *   curl_post           => post запрос
    *
    *   Нужные запросы
    *   
    *   crm.lead.update             - обновление полей лида
    *   crm.lead.userfield.update   - обновление кастомных полей лида
    *   
    *   UF_CRM_1623492488    скайп
    *   UF_CRM_1623818774178 доп инфо
    *
	*/

    public $bot_webhook = 'https://connect.intellectdialog.com/api/w/event/4904bb39-90f4-460e-b6a7-cafc87a99c9d';
    public $message_webhook = 'https://connect.intellectdialog.com/api/w/event/c10977c8-2b3b-400b-b870-b21c8953cd2e';
    // public $contract_link = 'https://admin.u-marketing.org/bpr/';
    // public $time_link = 'https://admin.u-marketing.org/bp/choose_time?hash=';
    public $contract_link = 'https://bpartners.kz/bcontract?hash=';
    public $time_link = 'https://bpartners.kz/btime?hash=';


    public function bitrixCreateLead(Request $request) {
        History::bitrix('Переименовали лид в удаленный', $request->all());
        
        if($request->phone && $request->lead_id) {
            $hash = md5(uniqid().mt_rand());
            $phone = Phone::normalize($request->phone);

            ///// check this lead exists
            $lead = Lead::where('lead_id', $request->lead_id)->latest()->first();
            if($lead) {
                $lead->update([
                    'name' => $request->name ? $request->name : 'Без имени',
                    'email' => $request->email,
                    'phone' => $phone,
                    'resp_id' => $request->resp_email,
                    'status' => 'NEW',
                    'segment' => Lead::getSegment($request->segment),
                    'hash' => $hash
                ]);
            } else {
                Lead::create([
                    'lead_id' => $request->lead_id,
                    'name' => $request->name ? $request->name : 'Без имени',
                    'email' => $request->email,
                    'phone' => $phone,
                    'resp_id' => $request->resp_email,
                    'status' => 'NEW',
                    'segment' => Lead::getSegment($request->segment),
                    'hash' => $hash
                ]);
            }

            // Update bitrix fields
            
            $this->updateFields($request->lead_id, [
                'UF_CRM_1624530685082' => $this->time_link . $hash, // Ссылка для офисных кандидатов
                'UF_CRM_1624530730434' => $this->contract_link . $hash, // Ссылка для удаленных кандидатов
            ]);
        }    

    }

	public function start(Request $request) {   
        
            History::bitrix('Запуск чатбота', $request->all());

            if($request->phone && $request->lead_id) {
                
                $hash = md5(uniqid().mt_rand());

                $phone = Phone::normalize($request->phone);

                ///// check this lead exists
                $lead = Lead::where('lead_id', $request->lead_id)->latest()->first();

                $resp_id = $request->resp_email;

                if($lead) {
                    $lead->update([
                        'name' => $request->namex,
                        'email' => $request->email,
                        'phone' => $phone,
                        'status' => 'NEW',
                        'resp_id' => $resp_id,
                        'segment' => Lead::getSegment($request->segment),
                        'hash' => $hash
                    ]);
                } else {
                    Lead::create([
                        'lead_id' => $request->lead_id,
                        'name' => $request->namex,
                        'email' => $request->email,
                        'phone' => $phone,
                        'resp_id' => $resp_id,
                        'status' => 'NEW',
                        'segment' => Lead::getSegment($request->segment),
                        'hash' => $hash
                    ]);
                }

                // Update bitrix fields
                
                $this->updateFields($request->lead_id, [
                    'UF_CRM_1624530685082' => $this->time_link . $hash, // Ссылка для офисных кандидатов
                    'UF_CRM_1624530730434' => $this->contract_link . $hash, // Ссылка для удаленных кандидатов
                ]);

                $this->send_msg($phone, 'Добрый день, ' . $request->namex . '! %0aВы откликнулись на нашу вакансию менеджера по работе с клиентами. %0aМеня зовут Мадина 😊 . %0aЯ чат-бот, который поможет Вам устроиться на работу 😉');
                usleep(1000000); // 1 sec
                $this->send_msg($phone, '/unset_tag:new_recruiter_bot');
                usleep(1000000); // 1 sec
                $this->send_msg($phone, '/set_tag:new_recruiter_bot');
               // $this->send_msg($phone, '/unset_tag:new_recruiter_bot /set_tag:new_recruiter_bot');
                //$this->send_msg($phone, '++recruiter_bot');
      
                
            }        
            
    }
    
    public function changeResp(Request $request) {
        History::bitrix('Смена ответственного', $request->all());

        if($request->lead_id) {
            $lead = Lead::where('lead_id', $request->lead_id)->first();

            if($lead) {
                $lead->resp_id = $request->resp_email;
                $lead->status = 'CON';
                $lead->deal_id = $request->deal_id;
                if($request->project) $lead->project = $request->project;
                if($request->net) $lead->net = $request->net;
                if($request->remote == 'Y') $lead->skyped = date('Y-m-d H:i:s', time() + 3600 * 6);
                if($request->resp_id) $lead->net = $request->net;
                $lead->save();
            }
        }
        
    }

    public function loseDeal(Request $request) {

        History::bitrix('Cделка проиграна', $request->all());

        if($request->lead_id) {
            $trainee = Trainee::where('lead_id', $request->lead_id)->first();
            if($trainee) {
                $trainee->fired = now();
                $trainee->save();
            }

            $lead = Lead::where('lead_id', $request->lead_id)->orderBy('id', 'desc')->first();
            if($lead) {
                $lead->status = 'LOSE';
                $lead->save();
            }

            $ud = UserDescription::where('lead_id', $request->lead_id)->first();
            if($ud) {
                $ud->fired = now();
                $ud->save();

                $user = User::find($ud->user_id);
                if($user) {
                    $request->id = $user->id;
                    $request->day = date('d');
                    $request->month = date('m');
                    User::deleteUser($request);

                    $nootis = UserNotification::where([
                        'about_id' => $user->id,
                    ])->get();
    
                    foreach($nootis as $noti) {
                        $noti->read_at = now();
                        $noti->save();
                    }
                }
            }
            
        } 
        
        

    }

    public function newLead(Request $request) {

        History::bitrix('Ручная конвертация', $request->all());

        $name = $request->name ? $request->name : 'Без имени';
        $langs = [
            'Только Русский 100%' => 2, // Только Русский 100%
            'Русский 100% и Казахский 100%' => 3, // Русский 100% и Казахский 100%
            'Русский 50% и Казахский 100%' => 1, // Русский 50% и Казахский 100%
        ];

        try {
            $lang = $langs[$request->lang];
        } catch(\Exception $e) {
            $lang = 0;
        } 

        // find deal from Btirix
        $bitrix = new Bitrix();
        $deal_id = $bitrix->findDeal($request->lead_id, false);
       
        /////////////

        $lead = Lead::where('lead_id', $request->lead_id)->first();
        

        $phone = null;
        $phone_2 = null;
        $phone_3 = null;

        if($request->phone) {
            $phones = explode(',', $request->phone);
            
            if(count($phones) > 0) $phone   = Phone::normalize($phones[0]);
            if(count($phones) > 1) $phone_2 = Phone::normalize($phones[1]);
            if(count($phones) > 2) $phone_3 = Phone::normalize($phones[2]);
            
        }

        if($lead) {

            //$trainee = Trainee::where('lead_id', $lead->lead_id)->orderBy('id', 'desc')->first();
            $trainee = UserDescription::where('is_trainee', 1)->where('lead_id', $lead->lead_id)->orderBy('id', 'desc')->first();
            if($trainee) {
                $user = User::withTrashed()->find($trainee->user_id);
                if($user) {
                    if($phone) $user->phone   = Phone::normalize($phone);
                    // if($phone_2) $user->phone_1 = Phone::normalize($phone_2);
                    // if($phone_3) $user->phone_2 = Phone::normalize($phone_3);
                    $user->save();
                }
            }
            
            if($lead->status != 'LOSE') {
                $lead->skyped = date('Y-m-d H:i:s', time() + 3600 * 6);
            }
            
            
            $lead->status = 'CON';
            if($phone) $lead->phone   = Phone::normalize($phone);
            if($phone_2) $lead->phone_2 = Phone::normalize($phone_2);
            if($phone_3) $lead->phone_3 = Phone::normalize($phone_3);
            $lead->lang =  $lang;
            $lead->deal_id = $deal_id;
            $lead->save();
        } else {

            if($lead->status != 'LOSE') {
                $skyped_time = date('Y-m-d H:i:s', time() + 3600 * 6);
            } else {
                $skyped_time = null;
            }
            

            Lead::create([
                'lead_id' => $request->lead_id,
                'deal_id' => $deal_id,
                'name' => $request->name,
                'phone' => Phone::normalize($request->phone),
                'phone_2' => Phone::normalize($phone_2),
                'phone_3' => Phone::normalize($phone_3),
                'segment' => Lead::getSegment($request->segment),
                'status' => 'CON',
                'hash' => 'converted_manually',
                'skyped' => $skyped_time,
                'lang' => $lang,
            ]);
        }   

        
    }
    
    public function inhouse(Request $request) {
        History::bitrix('inhouse', [
            $request->all(),
        ]);

        $lead = Lead::where('lead_id', $request->lead_id)->first();

        if($lead) {
            $lead->inhouse = date('Y-m-d H:i:s', time() + 3600 * 6);
            if($request->project) $lead->project = $request->project;
            if($request->net) $lead->net = $request->net;
            $lead->save();
        }
    }

    public function editDeal(Request $request) {
        
        History::bitrix('Edit deal', [
            $request->all(),
        ]);

        $langs = [
            'Только Русский 100%' => 2, // Только Русский 100%
            'Русский 100% и Казахский 100%' => 3, // Русский 100% и Казахский 100%
            'Русский 50% и Казахский 100%' => 1, // Русский 50% и Казахский 100%
        ];

        $wishtimes = [
            'с 08:45 - 19:00' => 1, // ,
            'с 13:00 - 23:00' => 2, //,
            'с 18:00 - 02:00' => 3, //,
            'c 08:45 - 13:00' => 4, //,
            'c 14:00 - 19:00' => 5, //,
            'c 19:00 - 23:00' => 6, //
        ];
        
        $lead = Lead::where('lead_id', $request->lead_id)->first();

        
        try {

        
        if($lead) {
            if($request->lang) $lead->lang = $langs[$request->lang];
            if($request->wishtime) $lead->wishtime = $wishtimes[$request->wishtime];

            if($request->phone) {
                $phones = explode(',', $request->phone);

                $phone = null;
                $phone_2 = null;
                $phone_3 = null;

                if(count($phones) > 0) $phone   = Phone::normalize($phones[0]);
                if(count($phones) > 1) $phone_2 = Phone::normalize($phones[1]);
                if(count($phones) > 2) $phone_3 = Phone::normalize($phones[2]);

                //$trainee = Trainee::where('lead_id', $lead->lead_id)->orderBy('id', 'desc')->first();

                $trainee = UserDescription::where('is_trainee', 1)->where('lead_id', $lead->lead_id)->orderBy('id', 'desc')->first();
                if($trainee) {
                    $user = User::withTrashed()->find($trainee->user_id);
                    if($user) {
                        if($phone) $user->phone     = Phone::normalize($phone);
                        // if($phone_2) $user->phone_1 = Phone::normalize($phone_2);
                        // if($phone_3) $user->phone_2 = Phone::normalize($phone_3);
                        $user->save();
                    }
                }
                
                if($phone) $lead->phone = $phone;
                if($phone_2) $lead->phone_2 = $phone_2;
                if($phone_3) $lead->phone_3 = $phone_3;
            }
            
            if($request->segment) {
                $lead->segment = Lead::getSegment($request->segment);
            }

            if($request->project) {
                $lead->project = $request->project;
            }
            
            if($request->net) $lead->net = $request->net;

            $lead->wishtime = $wishtimes[$request->wishtime];
            $lead->save();
        }

        } catch(\Exception $e) {
            //TelegramBot::send($e);
        } 
    }

    public function editLead(Request $request) {
        
        History::bitrix('Edit lead', [
            $request->all(),
        ]);

        // TelegramBot::send('Запрос с bitrix edit lead');
        // TelegramBot::send($request->all());
            
        $langs = [
            'Только Русский 100%' => 2, // Только Русский 100%
            'Русский 100% и Казахский 100%' => 3, // Русский 100% и Казахский 100%
            'Русский 50% и Казахский 100%' => 1, // Русский 50% и Казахский 100%
        ];

        $wishtimes = [
            'с 08:45 - 19:00' => 1, // ,
            'с 13:00 - 23:00' => 2, //,
            'с 18:00 - 02:00' => 3, //,
            'c 08:45 - 13:00' => 4, //,
            'c 14:00 - 19:00' => 5, //,
            'c 19:00 - 23:00' => 6, //
        ];


        $lead = Lead::where('lead_id', $request->lead_id)->first();

        

        try {

        
        if($lead) {
            if($request->lang) $lead->lang = $langs[$request->lang];
            if($request->wishtime) $lead->wishtime = $wishtimes[$request->wishtime];
            if($request->phone) {
                $phones = explode(',', $request->phone);
                
                $phone = null;
                $phone_2 = null;
                $phone_3 = null;

                if(count($phones) > 0) $phone   = Phone::normalize($phones[0]);
                if(count($phones) > 1) $phone_2 = Phone::normalize($phones[1]);
                if(count($phones) > 2) $phone_3 = Phone::normalize($phones[2]);

                //$trainee = Trainee::where('lead_id', $lead->lead_id)->orderBy('id', 'desc')->first();
                $trainee = UserDescription::where('is_trainee', 1)->where('lead_id', $lead->lead_id)->orderBy('id', 'desc')->first();
                if($trainee) {
                    $user = User::withTrashed()->find($trainee->user_id);
                    if($user) {

                        if($phone) $user->phone     = Phone::normalize($phone);
                        // if($phone_2) $user->phone_1 = Phone::normalize($phone_2);
                        // if($phone_3) $user->phone_2 = Phone::normalize($phone_3);

                        $user->save();
                    }
                }
                
                

                if($phone) $lead->phone   = $phone;
                if($phone_2) $lead->phone_2 = $phone_2;
                if($phone_3) $lead->phone_3 = $phone_3;
            }
            if($request->segment) {
                $lead->segment = Lead::getSegment($request->segment);
            }
            $lead->save();
        }

        } catch(\Exception $e) {
            //TelegramBot::send($e);
        } 
    }

    public function create_lead(Request $request) {
        
        
        History::bitrix('Create lead QR', [
            $request->all(),
        ]);

        if($request->has('phone') && $request->has('name')) {

            $hash = md5(uniqid().mt_rand());
            
            $res = $this->createLead([
                "TITLE" => "Кандидат QR - " . $request->name, 
                "NAME" => $request->name,  
                "ASSIGNED_BY_ID" => 23900,
                'UF_CRM_1624530685082' => $this->time_link . $hash, // Ссылка для офисных кандидатов
                'UF_CRM_1624530730434' => $this->contract_link . $hash, // Ссылка для удаленных кандидатов
                "PHONE"=> [["VALUE" => $request->phone, "VALUE_TYPE" => "WORK"]]
            ]);

            if($res) {
                
                
                $phone = Phone::normalize($request->phone);
                Lead::create([
                    'lead_id' => $res['result'],
                    'name' => $request->name,
                    'phone' => $phone,
                    'segment' => Lead::getSegment($request->segment),
                    'status' => 'NEW',
                    'hash' => $hash
                ]);

                $this->send_msg($phone, 'Добрый день, ' . $request->name . '! %0aВы откликнулись на нашу вакансию менеджера по работе с клиентами. %0aМеня зовут Мадина 😊 . %0aЯ чат-бот, который поможет Вам устроиться на работу 😉');
                usleep(2000000); // 2 sec
                $this->send_msg($phone, '/unset_tag:recruiter_bot%0a/set_tag:recruiter_bot');

            }
        }
    }

    public function send_message(Request $request) {
        if($request->phone && $request->message) {
            return $this->send_msg($request->phone, $request->message);
        }
    }  

    public function send_msg(String $phone, String $message) {
        return $this->curl_get($this->message_webhook . '?phone=' . $phone .'&message='. $message);
    }  

	public function save(Request $request) {
        
        //TelegramBot::send('Запрос с intellect');
        //TelegramBot::send($request->all());
        
        if($request->has('phone')) {

            /// Для битрикса
            $req = [];
    
            if($request->has('city')) $req['ADDRESS_CITY'] = $request->city; 
            if($request->has('lang')) {
                
                if((int)$request->lang == 1 || (int)$request->lang == 2 || (int)$request->lang == 3) {
                    $langs = [
                        1 => 2180, // Русский 50% и Казахский 100%
                        2 => 2176, // Только Русский 100%
                        3 => 2178, // Русский 100% и Казахский 100%
                    ];
                    $req['UF_CRM_1626255643'] = $langs[(int)$request->lang]; 
                } else {
                    $req['COMMENTS'] = $request->lang;
                }   
                
            }
            

            if($request->has('house')) {
                if((int)$request->house == 1 || (int)$request->house == 2) {
                    $houses = [
                        1 => 'Частный дом',
                        2 => 'Квартира',
                    ];
                    $req['UF_CRM_1626847280342'] = $houses[(int)$request->house]; 
                } else {
                    $req['UF_CRM_1626847280342'] = $request->house;
                }
            }
            
            if($request->has('wishtime_inhouse')) {
                if(in_array((int)$request->wishtime_inhouse, [1,2,3,4,5,6])) {
                    $wishtimes = [
                        1 => 2260, // 'с 08:45 - 19:00',
                        2 => 2258, //'c 08:45 - 13:00',
                        3 => 2264, //'c 14:00 - 19:00',
                    ];
                    // $wishtimes = [
                    //     1 => 2260, // 'с 08:45 - 19:00',
                    //     2 => 2262, //'с 13:00 - 23:00',
                    //     3 => 2266, //'с 18:00 - 02:00',
                    //     4 => 2258, //'c 08:45 - 13:00',
                    //     5 => 2264, //'c 14:00 - 19:00',
                    //     6 => 2268, //'c 19:00 - 23:00'
                    // ];
                    $req['UF_CRM_1629291391354'] = $wishtimes[(int)$request->wishtime_inhouse]; 
                } else { 
                    $req['UF_CRM_1629291391354'] = 2260;
                }
            }

            if($request->has('wishtime_remote')) {
                if(in_array((int)$request->wishtime_remote, [1,2,3,4,5,6])) {
                    $wishtimes = [
                        1 => 2260, // 'с 08:45 - 19:00',
                        2 => 2262, //'с 13:00 - 23:00',
                    ];
                    $req['UF_CRM_1629291391354'] = $wishtimes[(int)$request->wishtime_remote]; 
                } else { 
                    $req['UF_CRM_1629291391354'] = 2260;
                }
            } 

            // Для лида 
            $lead = Lead::where('phone', $request->phone)->latest()->first();
            
            if($lead) {
                ///////
                
                if($request->has('lang')) $lead->lang = $request->lang;
                if($request->has('age')) $lead->age = $request->age;
                if($request->has('house')) $lead->house = $request->house;
                if($request->has('net')) {
                    if($request->net == 'Наличие интернета') {
                        $lead->net = 1;
                    } else {
                        $lead->net = $request->net;
                    }
                }
                if($request->has('wishtime_inhouse')) {
                    $lead->wishtime = $request->wishtime_inhouse;
                    if((int)$request->wishtime_inhouse == 2) $lead->wishtime = 4;
                    if((int)$request->wishtime_inhouse == 3) $lead->wishtime = 5;
                }
                if($request->has('wishtime_remote')) {
                    $lead->wishtime = $request->wishtime_remote;
                }
                if($request->has('city')) $lead->city = $request->city;
               
                $lead->save();
               
                //////
                return $this->updateFields($lead->lead_id, $req);
            } else {
                return 'Лид не найден в u-marketing.org!';
            }
        } else {
            return 'Телефон не указан!';
        }

 	}

    public function get_name(Request $request) {
        
        if($request->has('phone')) {
            $lead = Lead::where('phone', $request->phone)->latest()->first();
            //TelegramBot::send('Запрос с intellect');
		    //TelegramBot::send($lead);
            if($lead) {

                if($request->has('save')) {
                    $this->save($request);
                }

                return [
                    'name' => $lead->name
                ];
            } else {
                return abort(500, 'Lead is not found');
            }
        } 
	}

    public function get_link(Request $request) {

        TB::send('get link');
        
        if($request->has('phone')) {
            $lead = Lead::where('phone', $request->phone)->latest()->first();
            if($lead) {

                if($request->has('city')) {
                    $lead->city = $request->city;
                    $lead->save();
                    $this->updateFields($lead->lead_id, [
                        'UF_CRM_1658397129' => $request->city
                    ]);
                }
                
                $this->save($request);

                if($request->link == 1) { // ссылка для подписи договора дял удаленных

                    
                    $lead->files = json_encode([]);
                    $lead->signed = 2;   
                    $lead->status = '39';
    
                    if($lead->status != 'LOSE') {
                        $lead->skyped = date('Y-m-d H:i:s', time() + 3600 * 6); // Раньше был, чтобы фиксировать время заполнения скайпа. Сейчас для хранения времени подписи
                    }
                    
                    $lead->save();
    
                    History::system('Уд Соискатель готов', [
                        'lead_id' => $lead->lead_id,
                        'date' => date('Y-m-d H:i:s', time() + 3600 * 6),
                    ]);
    
                    $this->updateFields($lead->lead_id, [
                        'UF_CRM_1628091269' => 1, // Подписал соглашение о неразглашении
                    ]);
                  

                    // if($lead->signed != 2 && !in_array($lead->status,['39', 'CON', 'LOSE'])) {
                    //     $lead->status = '40';

                    //     $this->updateFields($lead->lead_id, [
                    //         'STATUS_ID' => '40' // Статус: Рекрут: Подходящий, ждем подписания
                    //     ]);

                    //     //////////

                    //     usleep(4000000); // 3 sec
                    //     $bitrix = new Bitrix();
                    //     $lead->deal_id = $bitrix->findDeal($lead->lead_id, false);
                    //     $lead->save();
                    // }
                    
               
                    // /////////////////
                    
                    // $link = $this->contract_link . $lead->hash;
                    // $this->send_msg($request->phone, 'Подписать соглашение: %0a' . $link);

                    // return [
                    //     'link' => $this->contract_link . $lead->hash
                    // ];    
                } 
    
                if($request->link == 2) { // ссылка для выбора времени для офисных

                    $link = $this->time_link . $lead->hash;
                    $this->send_msg($request->phone, 'Выберите, пожалуйста, удобное для вас время стажировки: %0a' . $link);
                    
                    return [
                        'link' => $this->time_link . $lead->hash
                    ];   
                } 
            }
            
        }
	}

    public function change_status(Request $request) {
        
        History::intellect('Смена статуса', $request->all());

        if($request->has('phone')) {
            
            $lead = Lead::where('phone', $request->phone)->latest()->first();

            if($lead) { // сущетсвуте лид
                if($lead->skype == null || $lead->skype == '') { // если нет скайпа
                    if($request->has('status') && $request->status == 36) {

                        if($lead->signed != 2 && !in_array($lead->status,['39', 'CON', 'LOSE'])) {
                            $lead->status = '36';
                            $lead->save();
                        
                            return $this->updateFields($lead->lead_id, [
                                'STATUS_ID' => '36' // Статус: кандидат просит перезвонить
                            ]);
                        }
                    }

                    if($request->has('status') && $request->status == 37) {

                        if($lead->signed != 2 && !in_array($lead->status,['39', 'CON', 'LOSE'])) {
                            $lead->status = '37';
                            $lead->save();

                            return $this->updateFields($lead->lead_id, [
                                'STATUS_ID' => '37' // Статус: забраковал чат бот
                            ]);
                        } 
                        
                    }

                    if($request->has('status') && $request->status == 28) {

                        if($lead->signed != 2 && !in_array($lead->status,['39', 'CON', 'LOSE'])) {
                            $lead->status = '28';
                            $lead->save();

                            return $this->updateFields($lead->lead_id, [
                                'STATUS_ID' => '28' // Хочет на пол дня
                            ]);
                        }
                        

                        
                    }

                }
               

                
            }

        }
	}

    private function check_time() {

        $times = [];

        $evening = strtotime(date('Y-m-d'). ' 10:00:00'); // 16:00 UTC+6
        $morning = strtotime(date('Y-m-d'). ' 04:00:00'); // 10:00 UTC+6
        $now = time(); 

        if(date('w') == '4') {
            $times = [
                ['text' => 'Завтра в 10:00', 'value' => $morning + 3600 * 24],
                ['text' => 'Понедельник в 10:00', 'value' => $morning + 3600 * 4 * 24]
            ];
        }

        if(date('w') == '5') {
            $times = [
                ['text' => 'Понедельник в 10:00', 'value' => $morning + 3600 * 3 * 24],
                ['text' => 'Вторник в 10:00', 'value' => $morning + 3600 * 4 * 24]
            ];
        }

        if(date('w') == '6') {
            $times = [
                ['text' => 'Понедельник в 10:00', 'value' => $morning + 3600 * 2 * 24],
                ['text' => 'Вторник в 10:00', 'value' => $morning + 3600 * 3 * 24]
            ];
        }

        if(in_array(date('w'), ['0','1','2','3'])) {
            $times = [
                ['text' => 'Завтра в 10:00', 'value' => $morning + 3600 * 24],
                ['text' => 'Послезавтра в 10:00', 'value' => $morning + 3600 * 2 * 24]
            ];
        }
        
      
        

        return $times;
        
    }

    public function updateFields(int $lead_id, array $lead_fields)
    {
        $fields = [
            'id' =>  $lead_id,
            'fields' => $lead_fields
        ];
        
        $query = http_build_query($fields);
        $result = $this->curl_post('https://infinitys.bitrix24.kz/rest/2/0cgtwatfrdxpeuae/crm.lead.update.json', $query);
        
        return $result;
    }

    private function getLeads(array $status = [], String $title = '', String $from = '2010-01-01', String $to = '2050-01-01')
    {
        $filter = [];
        $filter['>DATE_CREATE'] = $from . 'T00:00:00';
        $filter['<DATE_CREATE'] = $to . 'T23:59:59';

        if($title != '') $filter['?TITLE'] = 'удаленный | inhouse';
        if(count($status) > 0) $filter['STATUS_ID'] = $status;
        
        $fields = [ 
            'filter' => $filter,
            'select' => ['id']
        ];
        
        $query = http_build_query($fields);
        
        $result = $this->curl_post('https://infinitys.bitrix24.kz/rest/2/0cgtwatfrdxpeuae/crm.lead.list.json', $query);
        
        return $result;
    }

    private function createLead(array $fields)
    {
        $query = http_build_query([
            'fields' => $fields,
            'params' => ['REGISTER_SONET_EVENT' => 'Y'],
        ]);
        
        $result = $this->curl_post('https://infinitys.bitrix24.kz/rest/2/0cgtwatfrdxpeuae/crm.lead.add.json', $query);
        
        return $result;
    }

	public function curl_get($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $json_resuls = curl_exec($curl);

        return json_decode($json_resuls);
    }

    public function get_int($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Authorization: b62e1a582280e46dd8aa8bf6c6cb8db1755a3af09de5530e2fdeb02e3dcc10ae37eb95485c850252b6941de351249b40d748903039648a5822a7886299bd0514HKO6zmKCXcJqiSowFbAbxXFOakIRp0KdHLhaxDaEd8hk6XvjscKoHUBRvgfcy6d+aNAdqoU77WpR6/jP3mRiTcZNcscw+lfRGS6vCXdUveUgHp7sNnQGW49fXkZLZVtS484+tYX+OUpYkMOS2ii1dEbyerdmb86RPAazENSc44aCTSqpU33eukFO6s8cTw2BqmlWHSBEck4U99Faolk08HRhY8gMoK8Den81PqmFrq0=',
            'Content-Type: application/json',
        ));
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        $json_resuls = curl_exec($curl);

        return json_decode($json_resuls);
    }

	public function curl_post($url, $query) {

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_URL => $url,
            CURLOPT_POSTFIELDS => $query,
        ));
        $result = curl_exec($curl);
        curl_close($curl);
        $result = json_decode($result, 1);
        if (array_key_exists('error', $result)) {
            History::bitrix('Ошибка IntellectController::curl_post', $result);
        } 

        return $result;
    }

    /* Подпись договора и заполнения скайпа */
    public function contract(Request $request) {

        $lead = Lead::where('hash', $request->hash)->latest()->first();

        if($lead) {
            
            if ($request->isMethod('get')) {
                if($lead->signed == 0) {
                    return view('recruiting.contract')->with('hash', $lead->hash);
                }
            }

            
            if($lead->signed == 2) {
                return view('recruiting.skype')->with([
                    'view'=> 2,
                    'msg'=> 'Обучение идет в 09:30 утра по понедельникам, средам и пятницам. Точное время вы получите в вацап.',
                ]);
            }

            
            
            if ($request->isMethod('post')) {
          
                $front = $request->file('front');

                try {
                    $front_name = $lead->phone. '_front_' . time() . '.' . $front->getClientOriginalExtension();
                    $front->move("static/uploads/job/", $front_name);
                    $files = [$front_name];
                } catch(\Exception $e) {

                    History::system('Ошибка в подписании соглашения', [
                        'error' => $e->getMessage(),
                        'request' => $request->all(),
                    ]);
                    
                    return '';
                }
                
                

                ////////////////////////////////////

                $lead->files = json_encode($files);
                $lead->signed = 2; // подписал (1 был раньше, и значил Соискатель подписал, ждем скайпа)
                $lead->name = $request->name;
                $lead->status = '39';

                if($lead->status != 'LOSE') {
                    $lead->skyped = date('Y-m-d H:i:s', time() + 3600 * 6); // Раньше был, чтобы фиксировать время заполнения скайпа. Сейчас для хранения времени подписи
                }
                
                $lead->save();

                History::system('Подписание соглашения', [
                    'lead_id' => $lead->lead_id,
                    'date' => date('Y-m-d H:i:s', time() + 3600 * 6),
                ]);

                ////////// РАСЧЕТ БЛИЖАЙШЕГО ВРЕМЕНИ

                $morning = strtotime(date('Y-m-d'). '03:30:00'); // 09:30 UTC+6

                if(date('w') == '1') $date = $morning + 3600 * 24 * 2;
                if(date('w') == '2') $date = $morning + 3600 * 24;
                if(date('w') == '3') $date = $morning + 3600 * 24 * 2;
                if(date('w') == '4') $date = $morning + 3600 * 24;
                if(date('w') == '5') $date = $morning + 3600 * 24 * 3;
                if(date('w') == '6') $date = $morning + 3600 * 24 * 2;
                if(date('w') == '0') $date = $morning + 3600 * 24;

                if(date('w') == '1') $msg = 'Обучение начнется в эту среду '. date('d.m.Y', $date + 3600 * 6) .' в 09:30 утра.';
                if(date('w') == '2') $msg = 'Обучение начнется завтра '. date('d.m.Y', $date + 3600 * 6) .' в 09:30 утра.';
                if(date('w') == '3') $msg = 'Обучение начнется в эту пятницу '. date('d.m.Y', $date + 3600 * 6) .'  в 09:30 утра.';
                if(date('w') == '4') $msg = 'Обучение начнется завтра '. date('d.m.Y', $date + 3600 * 6) .' в 09:30 утра.';
                if(date('w') == '5') $msg = 'Обучение начнется в понедельник '. date('d.m.Y', $date + 3600 * 6) .' в 09:30 утра.';
                if(date('w') == '6') $msg = 'Обучение начнется в понедельник '. date('d.m.Y', $date + 3600 * 6) .' в 09:30 утра.';
                if(date('w') == '0') $msg = 'Обучение начнется завтра '. date('d.m.Y', $date + 3600 * 6) .' в 09:30 утра.';

                //////////////////////////////
                
                
                $this->updateFields($lead->lead_id, [
                    'UF_CRM_1628091269' => 1, // Подписал соглашение о неразглашении
                    //'UF_CRM_1624365378' => date('Y-m-d H:i:s', $date + 3600 * 3), // Время в тексте СМС (для стажировки удаленных и штатных сотрудников)
                ]);
                
                //$this->send_msg($lead->phone, $msg . '%0aПеред началом обучения Вы получите ссылку в сообщении на вацап.');

                return view('recruiting.skype')->with([
                    'view'=> 2,
                    'msg'=> $msg,
                ]);
                    
            }
        } else {
            return abort(404, 'Запрашиваемая страница не найдена');
        }
        
        
    } 

    /* Выбор времени собеседования дял офисных кандидатов */
    public function choose_time(Request $request) {
        
        if($request->has('hash')) {
            
            $lead = Lead::where('hash', $request->hash)->latest()->first();
            
          
            if($lead) {

                if($lead->time != null) {
                
                    return view('recruiting.choose_time')->with([
                        'view' => 2,
                        'msg' => 'Поздравляем вас, '. $lead->name . '! Вам назначена стажировка. <br><br>дата: '. date('d.m.Y', strtotime($lead->time) + 3600 * 6) . ' <br>время:'. date('H:i', strtotime($lead->time) + 3600 * 6) .' <br><br>Пожалуйста приходите ровно в это время 😉'
                    ]);
                }

                if($request->isMethod('get')) {

                    return view('recruiting.choose_time')->with([
                        'view' => 1,
                        'days' => $this->check_time(), 
                        'hash' => $lead->hash,
                    ]);
                }

                if($request->isMethod('post')) {
                    TB::send('choose time');

                    $lead->time = date('Y-m-d H:i:s', $request->time);
                    $lead->save();

                    TB::send($request->all());

                    $msg = 'Поздравляю, вам назначена стажировка на '. date('H:i d.m.Y', $request->time + 3600 * 6) . '.%0a%0aМы находимся по Адресу г. Шымкент ул. Рыскулова 10А%0aТрех этажное здание "Автомир"%0aПоднимайтесь на 3й этаж и ищите 2ю дверь по левой стороне с табличкой "Business Partner"%0aКак войдете в офис, я Вас встречу 😉%0a%0ahttps://go.2gis.com/x8ppu%0a%0aПожалуйста, не опаздывайте 😊';
                    
                    $this->send_msg($lead->phone, $msg); 
                    
                    $this->updateFields($lead->lead_id, [
                        'UF_CRM_1624274105' => date('Y-m-d H:i:s', $request->time + 3600 * 3), // Время в тексте СМС (собеседование со штатными)
                        'UF_CRM_1633575435' => date('Y-m-d H:i:s', $request->time + 3600 * 3), // Время в тексте СМС (собеседование со штатными)
                        //'UF_CRM_1624274210' => date('Y-m-d H:i:s', $request->time + 3600 * 1.5), // Время прихода СМС (собеседование со штатными)
                    ]);
                    TB::send(date('Y-m-d H:i:s', $request->time + 3600 * 3));
                    usleep(2000000); // 2 sec

                    try {
                        $bitrix = new Bitrix();
                        $lead->deal_id = $bitrix->findDeal($lead->lead_id, false);
                        $lead->save();
                    } catch (\Exception $e) {}
                    

                    return redirect('https://api.whatsapp.com/send/?phone=77715942364&text&app_absent=0');
                }

            }
             
            
        }
	}
    
    /**
     * Saves answers after Whatsapp questions
     */
    public function quiz_after_fire(Request $request) {
        
        History::intellect('Уволенный анкета', $request->all());
        
        if($request->has('phone')) {
            
            
            
            $users = User::withTrashed()->get();
            
            foreach($users as $user) {
                $phone = Phone::normalize($user->phone);
                if($phone == Phone::normalize($request->phone)) {
                    $ud = UserDescription::where('user_id', $user->id)->first();
                    if(!$ud) {
                        $ud = UserDescription::create([
                            'user_id' => $user->id
                        ]);
                    }
                }
            }
          
            if($ud) {   
                $answers = [];
            
                $answers['1'] = $request->has('answer1') ? $request->answer1 : "";
                $answers['2'] = $request->has('answer2') ? $request->answer2 : "";
                $answers['3'] = $request->has('answer3') ? $request->answer3 : "";
                $answers['4'] = $request->has('answer4') ? $request->answer4 : "";
                $answers['5'] = $request->has('answer5') ? $request->answer5 : "";
                $answers['6'] = $request->has('answer6') ? $request->answer6 : "";
                $answers['7'] = $request->has('answer7') ? $request->answer7 : "";
                $answers['8'] = $request->has('answer8') ? $request->answer8 : "";
                $answers['9'] = $request->has('answer9') ? $request->answer9 : "";
           
                $ud->quiz_after_fire = $answers;
                
                $ud->save();
            }


        } 

    }



}