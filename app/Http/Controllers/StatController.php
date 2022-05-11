<?php

namespace App\Http\Controllers;

use App\Autocall;
use App\Contact;
use App\Message;
use App\Robot;
use App\Statistic;
use App\User;
use Illuminate\Http\Request;
use App\Mail as Mailable;

use Auth;
use DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;


class StatController extends Controller
{


    public function __construct() {
        View::share('bigmenu', 'rassylka');
        View::share('menu', 'stat');
        View::share('title', ' Статистика');
        //$this->middleware('auth');
    }

    public function autocall(Request $request) {

      View::share('title', ' Статистика / Автозвонки');

        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 1200);
        ini_set('request_terminate_timeout', 1200);
        $user = User::bitrixUser();
        $uid = $user->ID;

        if ( $request->isMethod( 'post' ) ) {
            $offset = $request->has('start') ? (int)$request->start : 0;
            $limit = $request->has('length') ? (int)$request->length : 10;
            $statistics = Statistic::where('id_user', $uid);

            // $statistics->whereIn('type', [Autocall::$VOICE_TYPE, Autocall::$SMS_TYPE]);
            if ($request->company) {
                if ($request->company == 'all') {
                    $statistics->whereIn('type', [Autocall::$VOICE_TYPE, Autocall::$SMS_TYPE]);
                } else {
                    $statistics->whereIn('type', [Autocall::$VOICE_TYPE, Autocall::$SMS_TYPE]);
                    $statistics->where('id_belong', $request->company);
                }
            }

            if ($request->start_date) {
                $statistics->where('date', '>=', $request->start_date.' 00:00');
            }
            if ($request->end_date) {
                $statistics->where('date', '<=', $request->end_date.' 23:59');
            }

            if ($request->status!=null) {
                $statistics->where('status', $request->status);
            } else {
                $statistics->where('status', '!=', 0);
                $statistics->where('status', '!=', -1);
            }

            if($request->number){
                $statistics->where('number', 'like', '%' . $request->number . '%');
            }
            $sum = $statistics->sum('cost');
            $totalCount = $statistics->count();
            if($request->excel) {
                $statistics->orderBy('date', 'asc');
            } else {
                $statistics->orderBy('date', 'desc');
            }

            if(!$request->excel) {
                $statistics->skip($offset)->take($limit);
            }

            $statistics = $statistics->with('contact')->get();

            $data = [];

            $statuses = [
                -1 => 'Ошибка',
                0 => 'В очереди',
                1 => 'Успешно',
                2 => 'Абонент не ответил',
                3 => 'Абонент занят',
                4 => 'Отключен',
                5 => 'Нет ответа',
                6 => 'Абонент не ответил',
                7 => 'Отклонено'
            ];


            
            foreach ($statistics as $statistic) {
                $rowData = json_decode($statistic->data, true);

                $contact_name = '';
                if(isset($rowData['contact'])) {
                    $contact = Contact::find($rowData['contact']);
                    $contactData = json_decode($contact['data'], true);
                    $contact_name = (isset($contactData['name'])?$contactData['name']:'') . ' ' . (isset($contactData['surname'])?$contactData['surname']:'');
                }

                $time = strtotime($statistic->date);
                $path1 = 'https://sip.u-marketing.org/record/37.18.30.15/'.date('Y/m/d', $time);
                $path2 = 'http://185.87.193.189/record/185.87.193.189/'.date('Y/m/d', $time);
                $path3 = 'http://185.87.193.189/record';

                if($request->excel) {
                    $audio = !empty($rowData['cc_record_filename']) ? $path1 . '/' . $rowData['cc_record_filename']:'';
                } else {
                    $audio = !empty($rowData['cc_record_filename']) ? '
                    <audio controls class="pleer">
                        <source src="' . $path1 . '/' . $rowData['cc_record_filename'] . '" type="audio/mpeg">
                        <source src="' . $path2 . '/' . $rowData['cc_record_filename'] . '" type="audio/mpeg">
                        <source src="' . $path3 . '/' . $rowData['cc_record_filename'] . '" type="audio/mpeg">
                        Для воспроизведения нужно обновить браузер
                        <a href="audio/music.mp3">Скачайте музыку</a>.
                    </audio>' : '<span>Нет записи</span>';
                }

                if($statistic->type==Autocall::$SMS_TYPE) {
                    $status =  $statistic->status == 1?'Отправлено':'Не отправлено';
                } else {
                    $status = trim($statuses[$statistic->status]);
                }

                if (!$request->excel) {
                    $status = ($statistic->status == 1 ? '<i class="ok"></i>' : '<i class="no"></i>') . $status;
                }

                $contact_data = json_decode($statistic->contact->data, true);

                if($request->excel) {
                    $data[] = [
                        $contact_data['name'],
                        $contact_data['info1'],
                        $contact_data['info2'],
                        $contact_data['info3'],
                        $statistic->number,
                        date('d.m.Y H:i:s', $time),
                        $status,
                        isset($rowData['talk_type'])?$rowData['talk_type']:" ",
                        isset($rowData['billsec'])?$rowData['billsec']:" ",
                        //$statistic->type==Autocall::$SMS_TYPE?mb_substr(, 0, 100):$audio,
                        $statistic->type==Autocall::$SMS_TYPE?$statistic->text:$audio,
                        $statistic->cost
                    ];
                } else {
                    $data[] = [
                        $contact_name,
                        $statistic->number,
                        date('d.m.Y H:i:s', $time),
                        $status,
                        isset($rowData['talk_type'])?$rowData['talk_type']:" ",
                        isset($rowData['billsec'])?$rowData['billsec']:" ",
                        $statistic->type==Autocall::$SMS_TYPE?$statistic->text:$audio,
                        $statistic->cost
                    ];
                }
            }

            if($request->excel) {
                $headings = [
                    'Имя контакта',
                    'Доп. поле 1',
                    'Доп. поле 2',
                    'Доп. поле 3',
                    'Номер телефона',
                    'Дата и время',
                    'Статус',
                    'Результат',
                    'Продолжительность',
                    'Запись',
                    'Стоимость',
                ];

                ob_end_clean();
                Excel::create('Отчет _'.date('Y_m_d').'_'.time(), function ($excel) use ($data, $headings) {
                    $excel->setTitle('Отчет');
                    $excel->setCreator('Laravale Media')->setCompany('MediaSend KZ');
                    $excel->setDescription('экспорт данных в Excel файл');
                    $excel->sheet('sheet1', function ($sheet) use ($data, $headings) {
                        $sheet->fromArray($data, null, 'A1', false, false);
                        //$sheet->rows($data);
                        $sheet->prependRow(1, $headings);
                    });
                })->export('xls');
            }

            $sending = [
                "recordsTotal" => $totalCount,
                "recordsFiltered" => $totalCount,
                "data" => $data,
                "sum" => $sum
            ];
            return response()->json($sending);
        }

        $status_options = [
            'Успешно' => '1',
            'Абонент не ответил' => '6',
            'Абонент занят' => '3',
            'Отключен' => '4',
            'Отклонено' => '5'
        ];

        $autocalls = Autocall::where('user_id', $uid)->where('is_integration', 0)->where('status', '!=', Autocall::STATUS_ARCHIVE)->orderBy('updated_at', 'desc')->get();

        return view('stat.autocall')->with('autocalls', $autocalls)->with('status_options', $status_options);
    }

    public function sonic(Request $request) {

      View::share('title', ' Статистика / Голосовая интеграция');

        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 1200);
        ini_set('request_terminate_timeout', 1200);
        $user = User::bitrixUser();
        $uid = $user->ID;

        if ( $request->isMethod( 'post' ) ) {
            $offset = $request->has('start') ? (int)$request->start : 0;
            $limit = $request->has('length') ? (int)$request->length : 10;
            $statistics = Statistic::where('id_user', $uid);

            if ($request->company == 'sip') {
                $statistics->where('type', Autocall::$VOICE_SIP_TYPE);
            } else if(strpos($request->company, Robot::STATISTIC_TYPE) !== false) {
                if (strpos($request->company, "all") !== false) {
                    $statistics->where('type', Robot::STATISTIC_TYPE);
                } else {
                    $statistics->where('type', Robot::STATISTIC_TYPE);
                    $statistics->where('id_belong', str_replace(Robot::STATISTIC_TYPE, '', $request->company));
                }
            } else if($request->company == 'all') {
                $types = [Autocall::$VOICE_SIP_TYPE, Autocall::$VOICE_INTEGRATION_TYPE];
                $statistics->whereIn('type', $types);
            } else {
                $statistics->where('type', Autocall::$VOICE_INTEGRATION_TYPE);
                $statistics->where('id_belong', $request->company);
            }
            if ($request->start_date) {
                $statistics->where('date', '>=', $request->start_date.' 00:00');
            }
            if ($request->end_date) {
                $statistics->where('date', '<=', $request->end_date.' 23:59');
            }

            if ($request->status!=null) {
                $status = $request->status;
                if($request->company == 'sip') {
                    if($status == 6) {
                        $status = 2;
                    }
                    if($status == 7) {
                        $status = 5;
                    }
                }
                $statistics->where('status', $status);
            } else {
                $statistics->where('status', '!=', 0);
                $statistics->where('status', '!=', -1);
            }

            if($request->number){
                $statistics->where('number', 'like', '%' . $request->number . '%');
            }
            $sum = $statistics->sum('cost');
            $totalCount = $statistics->count();
            if($request->excel) {
                $statistics->orderBy('date', 'asc');
            } else {
                $statistics->orderBy('date', 'desc');
            }

            if(!$request->excel) {
                $statistics->skip($offset)->take($limit);
            }

            $statistics = $statistics->with('contact')->get();

            $data = [];

            $statuses = [
                -1 => 'Ошибка',
                0 => 'В очереди',
                1 => 'Успешно',
                2 => 'Абонент не ответил',
                3 => 'Абонент занят',
                4 => 'Отключен',
                5 => 'Нет ответа',
                6 => 'Абонент не ответил',
                7 => 'Отклонено'
            ];


            
            foreach ($statistics as $statistic) {
                $rowData = json_decode($statistic->data, true);

                $time = strtotime($statistic->date);
                $path1 = 'https://sip.u-marketing.org/record/37.18.30.15/'.date('Y/m/d', $time);
                $path2 = 'http://185.87.193.189/record/185.87.193.189/'.date('Y/m/d', $time);
                $path3 = 'http://185.87.193.189/record';

                if($request->excel) {
                    $audio = !empty($rowData['cc_record_filename']) ? $path1 . '/' . $rowData['cc_record_filename']:'';
                } else {
                    $audio = !empty($rowData['cc_record_filename']) ? '
                    <audio controls class="pleer">
                        <source src="' . $path1 . '/' . $rowData['cc_record_filename'] . '" type="audio/mpeg">
                        <source src="' . $path2 . '/' . $rowData['cc_record_filename'] . '" type="audio/mpeg">
                        <source src="' . $path3 . '/' . $rowData['cc_record_filename'] . '" type="audio/mpeg">

                        Для воспроизведения нужно обновить браузер
                        <a href="audio/music.mp3">Скачайте музыку</a>.
                    </audio>' : '<span>Нет записи</span>';
                }

                if(empty($rowData['cc_record_filename'])) {
                    $audio = $statistic->text;
                }

                if($request->excel) {
                    $status = trim($statuses[$statistic->status]);
                } else {
                    $status = ($statistic->status == 1?'<i class="ok"></i>':'<i class="no"></i>').trim($statuses[$statistic->status]);
                }

                if($statistic->contact) {
                    $contact_data = json_decode($statistic->contact->data, true);
                }

                if($request->excel) {
                    $data[] = [
                        isset($contact_data) && isset($contact_data['name'])?$contact_data['name']:'',
                        isset($contact_data) && isset($contact_data['info1'])?$contact_data['info1']:'',
                        isset($contact_data) && isset($contact_data['info2'])?$contact_data['info2']:'',
                        isset($contact_data) && isset($contact_data['info3'])?$contact_data['info3']:'',
                        $statistic->number,
                        date('d.m.Y H:i:s', $time),
                        isset($rowData['destination_number'])?$rowData['destination_number']:" ",
                        $status,
                        isset($rowData['talk_type'])?$rowData['talk_type']:" ",
                        $audio,
                        $statistic->cost
                    ];
                } else {
                    $data[] = [
                        $statistic->number,
                        date('d.m.Y H:i:s', $time),
                        isset($rowData['destination_number'])?$rowData['destination_number']:" ",
                        $status,
                        isset($rowData['talk_type'])?$rowData['talk_type']:" ",
                        $audio,
                        $statistic->cost
                    ];
                }

            }

            if($request->excel) {
                $headings = [
                    'Имя контакта',
                    'Доп. поле 1',
                    'Доп. поле 2',
                    'Доп. поле 3',
                    'Номер телефона',
                    'Дата и время',
                    'Номер соединения',
                    'Статус',
                    'Результат',
                    'Запись',
                    'Стоимость',
                ];

                ob_end_clean();
                Excel::create('Отчет _'.date('Y_m_d').'_'.time(), function ($excel) use ($data, $headings) {
                    $excel->setTitle('Отчет');
                    $excel->setCreator('Laravale Media')->setCompany('MediaSend KZ');
                    $excel->setDescription('экспорт данных в Excel файл');
                    $excel->sheet('sheet1', function ($sheet) use ($data, $headings) {
                        $sheet->fromArray($data, null, 'A1', false, false);
                        //$sheet->rows($data);
                        $sheet->prependRow(1, $headings);
                    });
                })->export('xls');
            }

            $sending = [
                "recordsTotal" => $totalCount,
                "recordsFiltered" => $totalCount,
                "data" => $data,
                "sum" => $sum
            ];
            return response()->json($sending);
        }

        $status_options = [
            'Успешно' => '1',
            'Абонент не ответил' => '6',
            'Абонент занят' => '3',
            'Отключен' => '4',
            'Отклонено' => '5'
        ];

        $sonics = Autocall::where('user_id', $uid)->where('is_integration', 1)->where('status', '!=', Autocall::STATUS_ARCHIVE)->orderBy('updated_at', 'desc')->get();

        return view('stat.sonic')->with('sonics', $sonics)->with('status_options', $status_options);
    }

    public function message(Request $request) {

      View::share('title', ' Статистика / SMS интеграция');

        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 1200);
        ini_set('request_terminate_timeout', 1200);
        $user = User::bitrixUser();
        $uid = $user->ID;

        if ( $request->isMethod( 'post' ) ) {
            $offset = $request->has('start') ? (int)$request->start : 0;
            $limit = $request->has('length') ? (int)$request->length : 10;
            $statistics = Statistic::where('id_user', $uid);

            if ($request->company == 'smpp') {
                $statistics->where('type', Message::SMPP_TYPE);
            } else if ($request->company == 'all') {
                $statistics->where('type', Message::INTEGRATION_TYPE);
            } else {
                $statistics->where('type', Message::INTEGRATION_TYPE);
                $statistics->where('id_belong', $request->company);
            }
            if ($request->start_date) {
                $statistics->where('date', '>=', $request->start_date.' 00:00');
            }
            if ($request->end_date) {
                $statistics->where('date', '<=', $request->end_date.' 23:59');
            }

            if ($request->status == 0) {
                $statistics->where('status','!=', 1);
            }
            if ($request->status == 1) {
                $statistics->where('status', 1);
            }

            if($request->number){
                $statistics->where('number', 'like', '%' . $request->number . '%');
            }
            $sum = $statistics->sum('cost');
            $totalCount = $statistics->count();
            if($request->excel) {
                $statistics->orderBy('id', 'asc');
            } else {
                $statistics->orderBy('id', 'desc');
            }

            if(!$request->excel) {
                $statistics->skip($offset)->take($limit);
            }

            $statistics = $statistics->with('contact')->get();

            $data = [];
            
            
            foreach ($statistics as $statistic) {
                if($request->excel) {
                    $status = $statistic->status == 1?'Отправлено':'Не отправлено';
                } else {
                    $status = $statistic->status == 1?'<i class="ok"></i>':'<i class="no"></i>';
                }

                $contact_data = [];
                if($statistic->contact) {
                    $contact_data = json_decode($statistic->contact->data, true);
                }

                if($request->excel) {
                    $data[] = [
                        isset($contact_data['name'])?$contact_data['name']:'',
                        isset($contact_data['info1'])?$contact_data['info1']:'',
                        isset($contact_data['info2'])?$contact_data['info2']:'',
                        isset($contact_data['info3'])?$contact_data['info3']:'',
                        $statistic->number,
                        date('d.m.Y H:i:s', strtotime($statistic->date)),
                        $status,
                        $statistic->text,
                        $statistic->cost
                    ];
                } else {
                    $data[] = [
                        $statistic->number,
                        date('d.m.Y H:i:s', strtotime($statistic->date)),
                        $status,
                        '<div class="fixlengthw4" title="'.$statistic->text.'">'.$statistic->text.'</div>',
                        $statistic->cost
                    ];
                }
            }

            if($request->excel) {
                $headings = [
                    'Имя',
                    'Доп. поле 1',
                    'Доп. поле 2',
                    'Доп. поле 3',
                    'Номер телефона',
                    'Дата и время',
                    'Статус',
                    'Текст сообщения',
                    'Стоимость',
                ];

                ob_end_clean();
                Excel::create('Отчет _'.date('Y_m_d').'_'.time(), function ($excel) use ($data, $headings) {
                    $excel->setTitle('Отчет');
                    $excel->setCreator('Laravale Media')->setCompany('MediaSend KZ');
                    $excel->setDescription('экспорт данных в Excel файл');
                    $excel->sheet('sheet1', function ($sheet) use ($data, $headings) {
                        $sheet->fromArray($data, null, 'A1', false, false);
                        //$sheet->rows($data);
                        $sheet->prependRow(1, $headings);
                    });
                })->export('xls');
            }

            $sending = [
                "recordsTotal" => $totalCount,
                "recordsFiltered" => $totalCount,
                "data" => $data,
                "sum" => $sum
            ];
            return response()->json($sending);
        }

        $status_options = [
            'Отправлено' => '1',
            'Не отправлено' => '0',
        ];

        $messages = Message::where('user_id', $uid)->where('is_integration', 1)->where('status', '!=', Message::STATUS_ARCHIVE)->orderBy('updated_at', 'desc')->get();

        return view('stat.message')->with('messages', $messages)->with('status_options', $status_options);
    }


    public function sms(Request $request) {

        View::share('title', ' Статистика / СМС рассылка');

        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 1200);
        ini_set('request_terminate_timeout', 1200);
        $user = User::bitrixUser();
        $uid = $user->ID;

        if ( $request->isMethod( 'post' ) ) {
            $offset = $request->has('start') ? (int)$request->start : 0;
            $limit = $request->has('length') ? (int)$request->length : 10;
            $statistics = Statistic::where('id_user', $uid);

            // $statistics->where('type', Message::SMS_TYPE);

            if ($request->company) {
                if ($request->company == 'all') {
                   $statistics->where('type', Message::SMS_TYPE);
                } else {
                    $statistics->where('type', Message::SMS_TYPE);
                    $statistics->where('id_belong', $request->company);
                }
                
            }
            if ($request->start_date) {
                $statistics->where('date', '>=', $request->start_date.' 00:00');
            }
            if ($request->end_date) {
                $statistics->where('date', '<=', $request->end_date.' 23:59');
            }

            if ($request->status == 0) {
                $statistics->where('status','!=', 1);
            }
            if ($request->status == 1) {
                $statistics->where('status', 1);
            }

            if($request->number){
                $statistics->where('number', 'like', '%' . $request->number . '%');
            }
            $sum = $statistics->sum('cost');
            $totalCount = $statistics->count();
            if($request->excel) {
                $statistics->orderBy('id', 'asc');
            } else {
                $statistics->orderBy('id', 'desc');
            }

            if(!$request->excel) {
                $statistics->skip($offset)->take($limit);
            }

            $statistics = $statistics->with('contact')->get();

            $data = [];

            
            foreach ($statistics as $statistic) {
                if($request->excel) {
                    $status = $statistic->status == 1?'Отправлено':'Не отправлено';
                } else {
                    $status = $statistic->status == 1?'<i class="ok"></i>':'<i class="no"></i>';
                }

                $contact_data = json_decode($statistic->contact->data, true);

                if($request->excel) {
                    $data[] = [
                        isset($contact_data['name']) ? $contact_data['name'] : '',
                        isset($contact_data['info1']) ? $contact_data['info1'] : '',
                        isset($contact_data['info2']) ? $contact_data['info2'] : '',
                        isset($contact_data['info3']) ? $contact_data['info3'] : '',
                        $statistic->number,
                        date('d.m.Y H:i:s', strtotime($statistic->date)),
                        $status,
                        $statistic->text,
                        $statistic->cost
                    ];
                } else {
                    $data[] = [
                        $statistic->number,
                        date('d.m.Y H:i:s', strtotime($statistic->date)),
                        $status,
                        $statistic->text,
                        $statistic->cost
                    ];
                }
            }

            if($request->excel) {
                $headings = [
                    'Имя',
                    'Доп. поле 1',
                    'Доп. поле 2',
                    'Доп. поле 3',
                    'Номер телефона',
                    'Дата и время',
                    'Статус',
                    'Текст сообщения',
                    'Стоимость',
                ];
                
                ob_end_clean();
                Excel::create('Отчет _'.date('Y_m_d').'_'.time(), function ($excel) use ($data, $headings) {
                    $excel->setTitle('Отчет');
                    $excel->setCreator('Laravale Media')->setCompany('MediaSend KZ');
                    $excel->setDescription('экспорт данных в Excel файл');
                    $excel->sheet('sheet1', function ($sheet) use ($data, $headings) {
                        $sheet->fromArray($data, null, 'A1', false, false);
                        //$sheet->rows($data);
                        $sheet->prependRow(1, $headings);
                    });
                })->export('xls');
            }

            $sending = [
                "recordsTotal" => $totalCount,
                "recordsFiltered" => $totalCount,
                "data" => $data,
                "sum" => $sum
            ];
            return response()->json($sending);
        }

        $status_options = [
            'Отправлено' => '1',
            'Не отправлено' => '0',
        ];

        $smses = Message::where('user_id', $uid)->where('is_integration', 0)->where('status', '!=', Message::STATUS_ARCHIVE)->orderBy('updated_at', 'desc')->get();

        return view('stat.sms')->with('smses', $smses)->with('status_options', $status_options);
    }


    public function robot(Request $request) {


      View::share('title', ' Статистика / Роботы');

        $user = User::bitrixUser();
        $uid = $user->ID;


        $status_options = [
            'Успешно' => '1',
            'Абонент не ответил' => '6',
            'Абонент занят' => '3',
            'Отключен' => '4',
            'Отклонено' => '5'
        ];


        $robots = Robot::where('user_id', $uid)->where('status','!=', 0)->get();

        return view('stat.robot')->with('robots', $robots)->with('status_options', $status_options);
    }

    public function rent_numbers(Request $request) {
        abort(404);
        View::share('menu', 'rent_numbers');
        View::share('title', ' Аренда номеров');

        if ($request->isMethod('post')) {

            $template = 'setting.add_email_number';
            $mmTo = 'support@u-marketing.org';
            $subject = 'Подключить телефоный номер';
            $data = [
                'strana' => $request->strana,
                'name' => $request->name,
                'phone' => $request->phone,
            ];
            Mail::to($mmTo)->send(new Mailable($template, $subject, $data));
            // создаем Лида в bitrix24
            $roistatData = array(
                'roistat' => isset($_COOKIE['roistat_visit']) ? $_COOKIE['roistat_visit'] : null,
                'key' => 'NDAwOTI6NDU0MDg6N2VlMzI0ZTk5N2U2NTg3MWVhMzNjYjA3ZWYwYTk0NWE=',
                'title' => "U-Marketing.org - Подключить телефоный номер",
                'comment' => 'Страна: ' . $request->strana . ',  Имя : ' . $request->name . ' ' . ', Телефон: ' . $request->phone,
                'phone' => $request->phone,
                'is_need_callback' => '0', // Для автоматического использования обратного звонка при отправке контакта и сделки нужно поменять 0 на 1
                'fields' => array(
                    'ASSIGNED_BY_ID' => '5846',
                ),
            );
            file_get_contents("https://cloud.roistat.com/api/proxy/1.0/leads/add?" . http_build_query($roistatData));


            return view('setting.rent_numbers')->with('alert', 'Заявка отправлена. Мы свяжемя с вами в ближайшее время.');
        }

        return view('setting.rent_numbers');
    }



}
