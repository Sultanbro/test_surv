<?php

namespace App\Http\Controllers;

use App\Api;
use App\Components\TelegramBot;
use App\GetResponceApi;
use App\Payment;
use App\User;
use App\Mail as Mailable;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Invoice;
use App\ReadNotification;
use App\Notification;
use App\UserNotification;
use App\Expense;
use App\Contact;
use App\Stoplist;
use Carbon\Carbon;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{

    public function __construct() {
        View::share('bigmenu', 'x');
        View::share('menu', 'setting');
        View::share('title', ' Настройки');
        View::share('js_file', 'setting.js');
        //$this->middleware('auth');
    }

    public function uniqname(Request $request) {
        View::share('menu', 'uniqname');
        View::share('title', 'Регистрация уникального имени');

        if ($request->isMethod('post')) {

            $template = 'setting.add_email';
            $mmTo = 'u-support@u-marketing.org';
            $subject = 'Регистрация уникального имени';
            $data = [
                'company' => $request->company,
                'name' => $request->name,
                'phone' => $request->phone,
            ];

            Mail::to($mmTo)->send(new Mailable($template, $subject, $data));


            // создаем Лида в bitrix24
            $roistatData = array(
                'roistat' => isset($_COOKIE['roistat_visit']) ? $_COOKIE['roistat_visit'] : null,
                'key' => 'NDAwOTI6NDU0MDg6N2VlMzI0ZTk5N2U2NTg3MWVhMzNjYjA3ZWYwYTk0NWE=',
                'title' => "Mediasend.kz - Регистрация уникального имени",
                'comment' => 'Организация: ' . $request->company . ',  Имя которое надо зарегистрировать: ' . $request->name . ' ' . ', Телефон: ' . $request->phone,
                'phone' => $request->phone,
                'is_need_callback' => '0', // Для автоматического использования обратного звонка при отправке контакта и сделки нужно поменять 0 на 1
                'fields' => array(
                    'ASSIGNED_BY_ID' => '48',
                ),
            );
            file_get_contents("https://cloud.roistat.com/api/proxy/1.0/leads/add?" . http_build_query($roistatData));


            return view('setting.uniqname')->with('alert', 'Заявка отправлена. Мы свяжемя с вами в ближайшее время.');
        }

        return view('setting.uniqname');

    }

    public function main(Request $request) {
        View::share('menu', 'main');
        View::share('title', ' Главная');
        return view('main');
    }

    public function profile(Request $request) {


        View::share('menu', 'profile');
        $user = Auth::user();

        if ($request->isMethod('post')) {
            $user = User::find(Auth::user()->ID);
            $user->NAME = $request->name;
            $user->LAST_NAME = $request->last_name;
            $user->PHONE = $request->phone;
            $user->CITY = $request->city;
            $user->ADDRESS = $request->address;
            $user->COMPANY = $request->company_name;
            $user->DESCRIPTION = $request->company_desc;
            $user->BALANCE_NOTIFY = $request->balance_notice;
            $user->timezone = $request->timezone;

            if(!empty($request->password) && $request->password == $request->repassword) {
                $salt = User::randString(8);
                $password = $salt.md5($salt.$request->password);
                $user->PASSWORD = $password;
            }
            $user->save();
        }
        return view('setting.profile')->with('user', $user);

    }
    public function transaction(Request $request) {
        View::share('menu', 'transaction');
        $user = User::bitrixUser();
        $uid = $user->ID;

        $transactions = [];
        $total_expence = 0;
        $total_income = 0;
        $start_date = $request->start_date?date("Y-m-d", strtotime($request->start_date)):date('Y-m-d',strtotime("-5 days"));
        $end_date = $request->end_date?date("Y-m-d", strtotime($request->end_date)):date('Y-m-d');

        if($request->product && $request->product != 'all') {
            $expences = DB::select("SELECT
                    FROM_UNIXTIME(`time`, '%Y-%m-%d') as day,
                    sum(amount) expense,
                    min(time) first_timestamp,
                    substring_index(group_concat(balance + amount order by id asc SEPARATOR ';'), ';', 1) first_balance
                    FROM b_expense
                    WHERE id_user = ? AND time >= UNIX_TIMESTAMP( ? ) AND time < UNIX_TIMESTAMP(DATE_ADD( ? , INTERVAL 1 DAY)) AND type = '".$request->product."'
                    GROUP BY day ORDER BY day DESC ", [$uid, $start_date, $end_date]);
        } else {
            $expences = DB::select("SELECT
                    FROM_UNIXTIME(`time`, '%Y-%m-%d') as day,
                    sum(amount) expense,
                    min(time) first_timestamp,
                    substring_index(group_concat(balance + amount order by id asc SEPARATOR ';'), ';', 1) first_balance FROM b_expense WHERE id_user = ? AND time >= UNIX_TIMESTAMP( ? ) AND time < UNIX_TIMESTAMP(DATE_ADD( ? , INTERVAL 1 DAY))
                    GROUP BY day ORDER BY day DESC ", [$uid, $start_date, $end_date]);
        }



        $payments = DB::select("SELECT
                              FROM_UNIXTIME(`time`, '%Y-%m-%d') as day,
                              sum(amount) income,
                              min(time) first_timestamp,
                              substring_index(group_concat(balance-amount order by id asc SEPARATOR ';'), ';',1) first_balance,
                              max(comment) comment
                            FROM b_payment
                            WHERE status = 1 AND id_user = ? AND time >= UNIX_TIMESTAMP(?) AND time < UNIX_TIMESTAMP(DATE_ADD(?, INTERVAL  1 DAY))
                            GROUP BY day
                            ORDER BY day DESC", [$uid, $start_date, $end_date]);



        foreach ($expences as $expence){
            $date = $expence->day;
            if(!isset($transactions[$date]))
                $transactions[$date] = array('income' => 0, 'expense' => 0, 'first_balance' => 0, 'first_timestamp' => 0, 'comment' => '');

            $total_expence += $expence->expense;
            $transactions[$date]['expense'] = $expence->expense;
            $transactions[$date]['first_balance'] = $expence->first_balance;
            $transactions[$date]['first_timestamp'] = $expence->first_timestamp;
        }



        foreach ($payments as $payment){
            $date = $payment->day;
            if(!isset($transactions[$date]))
                $transactions[$date] = array('income' => 0, 'expense' => 0, 'first_balance' => 0, 'first_timestamp' => $payment->first_timestamp, 'comment' => '');
            $total_income += $payment->income;
            $transactions[$date]['income'] = $payment->income;
            $transactions[$date]['comment'] = $payment->comment;

            if($payment->first_timestamp <= $transactions[$date]['first_timestamp']) {
                $transactions[$date]['first_balance'] = $payment->first_balance;
                $transactions[$date]['first_timestamp'] = $payment->first_timestamp;
            }
        }

        $start_date = date("d.m.Y", strtotime($start_date));
        $end_date = date("d.m.Y", strtotime($end_date));


        return view('setting.transaction')->with('total_expence', $total_expence)
            ->with('total_income', $total_income)
            ->with('transactions', $transactions)
            ->with('start_date', $start_date)
            ->with('end_date', $end_date);

    }

    public function detailedTransaction(Request $request) {
        $date = $request->input('date');

        $user = User::bitrixUser();
        $uid = $user->ID;

        $start_timestamp = strtotime($date);
        $end_timestamp = strtotime($date . "+1 days");

        $detailed = DB::table('b_expense')->select('type', DB::raw('count(type) as total_count'), DB::raw('sum(amount) as total'))->where('id_user', $uid)->whereBetween('time', [$start_timestamp, $end_timestamp])->groupBy('type')->get();

        return response()->json(['user_id' => $uid, 'date' => $date, 'interval' => $start_timestamp.'-'.$end_timestamp, 'rows' => $detailed]);
    }

    public function payment(Request $request) {
        View::share('menu', 'payment');
        View::share('title', ' Пополнить баланс');
        return view('setting.payment');

    }
    public function invoice(Request $request) {

        if ($request->isMethod('post')) {
            $amount = $request->amount;
            $phone = $request->phone;
            $name = $request->name;
            $service = $request->service;
            $country = $request->country;

            $user = User::bitrixUser();
            $email = $user->EMAIL;
            $idUser = $user->ID;

            // генерация счета на оплату
            $invoice = new Invoice();
            $invoice->user_id = $idUser;
            $invoice->phone = $phone;
            $invoice->service_name = $service;
            $invoice->amount = $amount;
            $invoice->country = $country;
            $invoice->save();

            $pdf = Invoice::generate($invoice->id, $idUser, $country, $service, $amount);

            $filename = 'invoices/invoice_'.Carbon::now().'.pdf';
            Storage::disk('public')->put($filename, $pdf->Output('', 'S'));

            if ($amount > 0) {
                $ORDER_ID = Payment::add('umar', $idUser, 0, $amount, 'Новый платёж "' . $name . '"', $phone, '');
                            Payment::addLink($ORDER_ID, env('APP_URL').'/storage/'.$filename);    
                if ($ORDER_ID) {

                    $template = 'setting.invoice_email';
                    /*$mmTo = 'sales@mediasend.kz';*/
                    $mmTo = 'u-sales@u-marketing.org';
                    $subject = 'Запрос на формирование счёта №' . $ORDER_ID;
                    $data = [
                        'name' => $name,
                        'phone' => $phone,
                        'email' => $email,
                        'amount' => $amount,
                        'idUser' => $idUser
                    ];

                    Mail::to($mmTo)->send(new Mailable($template, $subject, $data));
                    ob_end_clean();
                    return $pdf->Output('invoice.pdf', 'D', false);
                } else {
                    return response()->json(['error' => 'Ошибка!']);
                }
            }

            return response()->json(['error' => 'Ошибка! Неверный формат суммы.']);
        }

        return view('setting.invoice');

    }

    private static $skey = '625872427176716a675c49564c66767a7034785d47546b604c485b';

    public function paymentBpartners(Request $request) {

        if($request->api_token != 'as876fsdf15d9fr20') {
            return "";
        }

        $idUser = 5;
 
        $amount = $request->amount;
        $phone = $request->phone;
        $name = $request->name;
        $email = $request->email;
        
        $payment_amount = $request->amount;

        $fields['WMI_SUCCESS_URL'] = 'https://bpartners.kz/pay/index.php?status=1';
        $fields['WMI_FAIL_URL'] = 'https://bpartners.kz/pay/index.php?status=1';
        $fields['WMI_MERCHANT_ID'] = '134668659835';
        $fields['WMI_CURRENCY_ID'] = '398';
        $fields['WMI_CUSTOMER_EMAIL'] = $email;
        $fields['phone'] = $phone; 
        $fields['name'] = $name;
        
        $ORDER_ID = Payment::add('bp', $idUser, 0, $payment_amount, $name, $phone, $request->name, 'Платеж через Walletone Bpartners.kz');
        if($ORDER_ID){

            $fields['WMI_PAYMENT_NO'] = $ORDER_ID;
            $fields['WMI_PAYMENT_AMOUNT'] = (float)$amount;
            $fields['WMI_DESCRIPTION'] = 'bpartners.kz';

            $form = '';
            $value = '';
            uksort($fields, "strcasecmp");
            foreach($fields as $key => $field){
                $form .= '<input type="hidden" value="'.$field.'" name="'.$key.'">';
                $value .= iconv("utf-8", "windows-1251", $field);
            }
            $signature = base64_encode(pack("H*", md5($value.self::$skey)));
            $fields["WMI_SIGNATURE"] = $signature;

            $form = '<form id="walletone" style="opacity:0; display: none;" action="https://wl.walletone.com/checkout/checkout/Index" method="POST">
                    '.$form.'
                        <input type="hidden" name="WMI_SIGNATURE" value="'.$fields['WMI_SIGNATURE'].'">
                      
                        
                        <input type="submit">
                        </form>';
                        // <input name="WMI_PTENABLED"      value="CreditCardKZT"/>
            return response()->json(['success' => true, 'id_order' => $ORDER_ID, 'form' => $form]);

        }
       
    }

    public function paymentBpartnersStatus(Request $request) {
        

        if($request['WMI_ORDER_STATE'] == 'Accepted') {
            $status = 'success';
            $title = 'UC - BP - Переговоры в продажах - Счет оплачен';

            $payment = Payment::where('status', 0)->where('id', $request['WMI_PAYMENT_NO'])->first();
            //TelegramBot::send('accepted');
                if($payment){
                    //TelegramBot::send('into payment');
                    $idUser = $payment->id_user;

                    $amount = (float)$request->WMI_PAYMENT_AMOUNT;

                    foreach($request->all() as $name => $value)
                    {
                        if ($name !== "WMI_SIGNATURE") $params[$name] = $value;
                    }

                    uksort($params, "strcasecmp"); $values = "";

                    foreach($params as $name => $value)
                    {
                        $values .= $value;
                    }

                    $signature = base64_encode(pack("H*", md5($values . self::$skey)));

                    if($signature == $request->WMI_SIGNATURE){
                        //TelegramBot::send('into signment');
                        Payment::updateStatus($request['WMI_PAYMENT_NO'], $idUser, $payment->amount);
                        $response =  'WMI_RESULT=OK';
                    }
                } else {
                    $response = 'WMI_RESULT=RETRY&WMI_DESCRIPTION=Сервер временно недоступен';
                }


        } else {
            //TelegramBot::send('into failure');
            $status = 'failure';
            $title = 'UC - BP - Переговоры в продажах - Счет неоплачен';
        }
        $name = $request['name'];
        $phone = $request['phone'];



        ////////////////////////////////////////////////////////////////////

        // Находим Существующий лид
        // $queryUrl = 'https://infinitys.bitrix24.kz/rest/2/0cgtwatfrdxpeuae/crm.lead.list.json';

		// $queryData = http_build_query(array(
        //     'filter' =>  [ "SECOND_NAME" => 285878],
        //         'select' =>  [ "ID", "TITLE", 'SECOND_NAME']
        // ));

        // $curl = curl_init();
        // curl_setopt_array($curl, array(
        //     CURLOPT_SSL_VERIFYPEER => 0,
        //     CURLOPT_POST => 1,
        //     CURLOPT_HEADER => 0,
        //     CURLOPT_RETURNTRANSFER => 1,
        //     CURLOPT_URL => $queryUrl,
        //     CURLOPT_POSTFIELDS => $queryData,
        // ));
        // $result = curl_exec($curl);
        // curl_close($curl);
        // $result = json_decode($result, 1);
        // if (array_key_exists('error', $result)) echo "Ошибка при сохранении лида: ".$result['error_description']."<br/>";
       

        // if (count($result['result'])) { // Если есть лид то 
        //     $lead_id = $result['result'][0]['ID'];


        //     //    редактируем

        //     $queryUrl = 'https://infinitys.bitrix24.kz/rest/2/0cgtwatfrdxpeuae/crm.lead.update.json';

        //     $queryData = http_build_query(array(
        //         'id' =>  $lead_id,
        //         'fields' => [
        //             "SECOND_NAME" => $request['WMI_PAYMENT_NO'],   // В name записываю ID Payment
        //             'TITLE' => $title
        //         ]
        //     ));

        //     $curl = curl_init();
        //     curl_setopt_array($curl, array(
        //         CURLOPT_SSL_VERIFYPEER => 0,
        //         CURLOPT_POST => 1,
        //         CURLOPT_HEADER => 0,
        //         CURLOPT_RETURNTRANSFER => 1,
        //         CURLOPT_URL => $queryUrl,
        //         CURLOPT_POSTFIELDS => $queryData,
        //     ));
        //     $result = curl_exec($curl);
        //     curl_close($curl);
        //     $result = json_decode($result, 1);
        //     if (array_key_exists('error', $result)) echo "Ошибка при сохранении лида: ".$result['error_description']."<br/>";









        // } else {
            


        //     //     добавляем новый лид

        //     $queryUrl = 'https://infinitys.bitrix24.kz/rest/2/0cgtwatfrdxpeuae/crm.lead.add.json';
            
        //     $queryData = http_build_query(array(
        //         'fields' => array(
        //             'TITLE' => $title,
        //             'NAME' => $name,
        //             'SECOND_NAME' => $request['WMI_PAYMENT_NO'],
        //             "ASSIGNED_BY_ID" => 3861, // 9744
        //             'PHONE' => Array(
        //                 "n0" => Array(
        //                     "VALUE" => $phone,
        //                     "VALUE_TYPE" => "WORK",
        //                 ),
        //             ),
        //         ),
        //         'params' => array("REGISTER_SONET_EVENT" => "Y")
        //     ));

        //     //TelegramBot::send('Lid query created');
            

        //     $curl = curl_init();
        //     curl_setopt_array($curl, array(
        //         CURLOPT_SSL_VERIFYPEER => 0,
        //         CURLOPT_POST => 1,
        //         CURLOPT_HEADER => 0,
        //         CURLOPT_RETURNTRANSFER => 1,
        //         CURLOPT_URL => $queryUrl,
        //         CURLOPT_POSTFIELDS => $queryData,
        //     ));
        //     $result = curl_exec($curl);
        //     curl_close($curl);
        //     $result = json_decode($result, 1);
        //     if (array_key_exists('error', $result)) echo "Ошибка при сохранении лида: ".$result['error_description']."<br/>";
        //     //TelegramBot::send('Lid block passed');




        // }


            print $response;
            exit();
        
    }   

    public function card(Request $request) {

        if ($request->isMethod('post')) {
            $user = User::bitrixUser();
            $idUser = $user->ID;

            if($request->action == 'createOrder'){
                $amount = $request->amount;
                $phone = $request->phone;
                $email = $user->EMAIL;
            }
            else if($request->action  == 'linkPay'){
                $amount = $request->amount;
                $phone = $request->phone;
                $email = $request->login;
            }

            $payment_amount = $request->amount;

            if(auth()->user()->currency == 'rub') {
                $client = new Client();

                $response = $client->request('GET', 'https://nationalbank.kz/rss/rates_all.xml?switch=russian');

                $xml = simplexml_load_string($response->getBody());

                $currency_code = 'RUB';
                $currency_rate = $xml->xpath("//rss/channel/item/title[contains(text(), '$currency_code')]/following-sibling::description");
                if(count($currency_rate) > 0) {
                    $amount = $amount * (string) $currency_rate[0];
                }
            }





            $fields['WMI_SUCCESS_URL'] = config('app.url').'/setting/transaction';
            $fields['WMI_FAIL_URL'] = config('app.url').'/setting/transaction';
            $fields['WMI_MERCHANT_ID'] = '134668659835';
            $fields['WMI_CURRENCY_ID'] = '398';
            $fields['phone'] = $phone;
            $fields['userID'] = $idUser; // отправляем ID понадобиться
            $fields['email'] = $email; // передаем почту на всякий случай
            if($request->action == 'linkPay'){
                $fields['linkPay'] = "linkPay"; // создаем поле формы для проверки ответа об успешном платеже что он был именно по ссылке
            }


            if($amount > 0){

               $data = $_POST;

                // $queryUrl = 'https://infinitys.bitrix24.kz/rest/2/0cgtwatfrdxpeuae/crm.lead.add.json';

                // $queryData = http_build_query(array(
                //     'fields' => array(
                //         'TITLE' => 'cp.u-marketing.org - wallet one - ID '. $idUser,
                //         //'NAME' => $data['name'],
                //         "ASSIGNED_BY_ID" => 48, // 48
                //         'PHONE' => Array(
                //             "n0" => Array(
                //                 "VALUE" => $data['phone'],
                //                 "VALUE_TYPE" => "WORK",
                //             ),
                //         ),

                //     ),
                //     'params' => array("REGISTER_SONET_EVENT" => "Y")
                // ));
                // $curl = curl_init();
                // curl_setopt_array($curl, array(
                //     CURLOPT_SSL_VERIFYPEER => 0,
                //     CURLOPT_POST => 1,
                //     CURLOPT_HEADER => 0,
                //     CURLOPT_RETURNTRANSFER => 1,
                //     CURLOPT_URL => $queryUrl,
                //     CURLOPT_POSTFIELDS => $queryData,
                // ));
                // $result = curl_exec($curl);
                // curl_close($curl);
                // $result = json_decode($result, 1);
                // if (array_key_exists('error', $result)) echo "Ошибка при сохранении лида: ".$result['error_description']."<br/>";




                $ORDER_ID = Payment::add('umar', $idUser, 0, $payment_amount, 'Новый платёж', $phone, '', 'Платеж через Walletone');
                if($ORDER_ID){

                    $fields['WMI_PAYMENT_NO'] = $ORDER_ID;
                    $fields['WMI_PAYMENT_AMOUNT'] = (float)$amount;

                    $form = '';
                    $value = '';
                    uksort($fields, "strcasecmp");
                    foreach($fields as $key => $field){
                        $form .= '<input type="hidden" value="'.$field.'" name="'.$key.'">';
                        $value .= iconv("utf-8", "windows-1251", $field);
                    }
                    $signature = base64_encode(pack("H*", md5($value.self::$skey)));
                    $fields["WMI_SIGNATURE"] = $signature;

                    $form = '<form id="walletone" style="opacity:0; display: none;" action="https://wl.walletone.com/checkout/checkout/Index" method="POST">
                            '.$form.'
                              <input type="hidden" name="WMI_SIGNATURE" value="'.$fields['WMI_SIGNATURE'].'">
                              <input type="submit">
                             </form>';

                        



                      //  $quantypay = GetResponceApi::GetUserFieldVal(array(
                      //   'email' => $email,
                      //   'fieldId' => 'Ru79O'
                      //  ));

                      $quantypay = DB::table('b_payment')->where('id_user',$idUser)->value('amount');

                      if ($quantypay+1 == 1 && $amount < 5000) {
                               $template = 'setting.quantypay';
                               $to = 'u-support@u-marketing.org';
                               $subject = 'Первое пополнение баланса - '.$idUser;
                               $data = [
                                   'idUser' => $idUser,
                                   'amount' => $amount,
                                   'email' => $email,
                               ];

                               Mail::to($to)->send(new Mailable($template, $subject, $data));
                             }





                    // счетчик брошенной оплаты
                    //пока комментирую
                    /*$dropped_payment = GetResponceApi::GetUserFieldVal(array(
                        'email' => $email,
                        'fieldId' => 'epfOR' //dropped_payment
                    ));
                    $dropped_payment  = intval($dropped_payment[0]);
                    if(empty($dropped_payment)){
                        $dropped_payment = 1;
                    }
                    else{
                        $dropped_payment ++;
                    }*/

                    if ($user->currency == 'rub') {
                      $currencyuser = 'руб.';
                    } else { $currencyuser = 'тенге'; };

                    GetResponceApi::ChangedFieldsVal(array(
                        'email' => $email,
                        'fields' => array(
                          [
                            "customFieldId" => "JEbjP", // user_id
                            "value" => [
                              $idUser
                              ]
                              ],
                              [
                                "customFieldId" => "J0wjy", // inter_kassa_pyment_status
                                "value" => [
                                  "created"
                                  ]
                                  ],
                                  [
                                    "customFieldId" => "eceib", // last_payment
                                    "value" => [
                                      $amount
                                      ]
                                      ],
                                      [
                                         "customFieldId" => 'VKdqOO', // валюта
                                         "value" => [
                                             $currencyuser
                                         ]
                                     ]

                        )
                    ));

                    return response()->json(['success' => true, 'id_order' => $ORDER_ID, 'form' => $form]);

                }else
                    return response()->json(['error' => 'Ошибка']);
            }else{
                return response()->json(['error' => 'Ошибка! Неверный формат суммы.']);
            }
        }
        return view('setting.card');

    }

    public function kassa(Request $request) {

        if($request->isMethod('post')) {

            $user = User::bitrixUser();
            $userID = $user->ID;

            $amount = $request->amount;
            $payment_amount = $request->amount;
            $phone = $request->phone;
            $email = $user->EMAIL;

            if($amount > 0){

                $client = new Client();

                if(auth()->user()->currency == 'rub') {

                    $response = $client->request('GET', 'https://nationalbank.kz/rss/rates_all.xml?switch=russian');

                    $xml = simplexml_load_string($response->getBody());

                    $currency_code = 'RUB';
                    $currency_rate = $xml->xpath("//rss/channel/item/title[contains(text(), '$currency_code')]/following-sibling::description");
                    if(count($currency_rate) > 0) {
                        $amount = $amount * (string) $currency_rate[0];
                    }
                }


                if ($user->currency == 'rub') {
                  $currencyuser = 'руб.';
                } else { $currencyuser = 'тенге'; };

                GetResponceApi::ChangedFieldsVal(array(
                    'email' => $email,
                    'fields' => array(
                      [
                        "customFieldId" => "JEbjP", // user_id
                        "value" => [
                          $userID
                          ]
                          ],
                          [
                            "customFieldId" => "J0wjy", // inter_kassa_pyment_status
                            "value" => [
                              "created"
                              ]
                              ],
                              [
                                "customFieldId" => "eceib", // last_payment
                                "value" => [
                                  $amount
                                  ]
                                  ],
                                  [
                                     "customFieldId" => 'VKdqOO', // валюта
                                     "value" => [
                                         $currencyuser
                                     ]
                                 ]

                    )
                ));

                try {
                    $response = $client->request('POST', 'https://onepay.kassa24.kz/order/create', [
                        'auth' => [config('services.onepay.username'), config('services.onepay.password')],
                        'form_params' => [
                            'serviceId' => config('services.onepay.serviceID'),
                            'sum' => $amount,
                            'account'=> $phone,
                            'userID' => $userID,
                            'successUrl' => 'https://cp.u-marketing.org/setting/transaction',
                            'callbackUrl' => 'https://cp.u-marketing.org/setting/callback/kassa24'
                        ]
                    ]);
                } catch(\Exception $e) {
                    abort(500);
                }
                

                $data = json_decode($response->getBody(), true);

                $ORDER_ID = Payment::add('umar', $userID, 0, $payment_amount, 'Новый платёж', $phone, '', 'Платеж через Касса24', $data['orderId']);

                if($ORDER_ID) {
                    return redirect()->away($data['formUrl']);
                } else {
                    return back()->with('status', 'Error writing to database!');
                }





            }

            return back()->with('status', 'Invalid amount!');
        }

        return View('setting.kassa');
    }

    public function callbackKassa(Request $request) {

        Log::info('Request From Ip Address: '.$request->ip());

        // if ($request->ip() != "88.204.242.62") {
        //     return response('Forbidden', 403)
        //           ->header('Content-Type', 'text/plain');
        // }
        $orderID = $request->orderId;
        $status = $request->status;


        Log::info('Order ID: '.$orderID.' - Status: '.$status);

        $payment = Payment::where('orderID', $orderID)->first();

        if($status == 1 && $payment->status != 1) {
            Payment::updateStatus($payment->id, $payment->id_user, $payment->amount);
            Log::info('Payment Updated: '.$orderID);

            $idUser = $payment->id_user; // id пользователя
            $user = User::find($idUser);
            $user->notify_sent = 0;  // сбросим уведомления баланса на 0,
            $user->save();
            $email = $user->EMAIL;
            $amount = $payment->amount;

            $balance = User::balanceByUser($idUser);
            $total_balance = $balance;

            if ($user->currency == 'rub') {
              $currencyuser = 'руб.';
            } else { $currencyuser = 'тенге'; };

            GetResponceApi::ChangedFieldsVal(array(
                'email' => $email,
                'fields' => array(
                    [
                        "customFieldId" => "JEbjP", // user_id
                        "value" => [
                            $idUser
                        ]
                    ],
                    [
                        "customFieldId" => "ecmba", // link_pay_status
                        "value" => [
                            "confirmed"
                        ]
                    ],
                    [
                        "customFieldId" => "J0wjy", // inter_kassa_pyment_status
                        "value" => [
                            "confirmed"
                        ]
                    ],
                    [
                        "customFieldId" => 'epfOR', // dropped_payment
                        "value" => [
                            0
                        ]
                    ],
                    [
                        "customFieldId" => "eceib", // last_payment
                        "value" => [
                            $amount
                        ]
                    ],
                    [
                       "customFieldId" => 'ep1mn', // total_balance
                       "value" => [
                           $total_balance
                       ]
                   ],
                   [
                      "customFieldId" => 'VKdQpZ', // quantybalance
                      "value" => [
                          $amount
                      ]
                  ],
                  [
                     "customFieldId" => 'VKdqOO', // валюта
                     "value" => [
                         $currencyuser
                     ]
                 ]
                )
            ));



        } else {
            Log::info('Transaction Cancelled');
            $idUser = $payment->id_user; // id пользователя
            $user = User::find($idUser);
            $phone = $user->PHONE;

            if ($phone <> '') {
                $out = array();
                $phone = str_replace('(', '', $phone);
                $phone = str_replace(')', '', $phone);
                $phone = str_replace('+7', '8', $phone);
                $phone = str_replace('-', '', $phone);

                $data = [
                    'text' => '',
                    'phones' => [
                        ['name' =>'', 'surname' =>'', 'phone' => $phone]
                    ],
                    'appid' => 4568723
                ];

                if($curl = curl_init()){
                    $url = 'https://cp.u-marketing.org/api/sms/add?apiKey=30a3dbb43478ad7f05b297a7b144b65e&'.http_build_query($data, '', '&');

                    curl_setopt($curl, CURLOPT_URL, $url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    $out = curl_exec($curl);
                    curl_close($curl);
                }
            }



        }
        // $template = 'setting.mail_after_callback_wone';
        // $to = 'support@mediasend.kz';
        // $subject = 'Оплата с '.$idUser.' кабинета, прошла успешно!';
        // $data = [
        //     'idUser' => $idUser,
        //     'amount' => $amount,
        //     'email' => $email,
        //     'pay_type' => 'Касса24'
        // ];

        // Mail::to($to)->send(new Mailable($template, $subject, $data));

        return new Response('success', 200, ['Content-Type' => 'text/html']);

    }

    public function walletone(Request $request) {
        //TelegramBot::send('Walletone');

     
        if($request->WMI_DESCRIPTION == 'bpartners.kz') {
            $this->paymentBpartnersStatus($request);
        }

        
     
        

        Log::info('Walletone: '.$request.'  =='.$request->WMI_PAYMENT_NO);
        if(isset($request->WMI_PAYMENT_NO)){
            $id_order = $request->WMI_PAYMENT_NO;
            $state = $request->WMI_ORDER_STATE;
            $amount = $request->WMI_PAYMENT_AMOUNT; // сумма платежа
            $idUser = $request->userID; // id пользователя
            $email = $request->email; // получаем почту чтоб наверняка

            $user = User::find($idUser);

            $user->notify_sent = 0;  // сбросим уведомления баланса на 0,
            $user->save();

            Log::info('Payment walletone: '.$state);

            if ($user->currency == 'rub') {
              $currencyuser = 'руб.';

              $client = new Client();

              $response = $client->request('GET', 'https://nationalbank.kz/rss/rates_all.xml?switch=russian');

              $xml = simplexml_load_string($response->getBody());

              $currency_code = 'RUB';
              $currency_rate = $xml->xpath("//rss/channel/item/title[contains(text(), '$currency_code')]/following-sibling::description");
              if(count($currency_rate) > 0) {
                  $amount = $amount / (string) $currency_rate[0];
              }

            } else { $currencyuser = 'тенге'; };


            if($state == 'Accepted'){

                $balance = User::balanceByUser($idUser);

                $total_balance = $balance + $amount;

                if($request->linkPay == "linkPay"){ // пометка в getrespons что завершена оплата по ссылке

                    $bonus5percent = GetResponceApi::GetUserFieldVal(array(
                        'email' => $email,
                        'fieldId' => 'eaDoa' // present_5percent
                    ));

                    if($bonus5percent[0] == 'yes'){
                        /*============ Отправляем почту администрации чтобы пополнили бонусы при оплате по ссылке ==============*/
                        $template = 'setting.walletone_email';
                        $to = 'u-support@u-marketing.org'; //Почта получателя или получателей
                        $subject = 'пополните 5% бонусов кабинету id - '.$idUser; //Загаловок сообщения
                        $data = [
                            'idUser' => $idUser,
                            'amount' => $amount
                        ];

                        Mail::to($to)->send(new Mailable($template, $subject, $data)); //Отправка письма

                        /*======= End of Отправляем почту администрации чтобы пополнили бонусы при оплате по ссылке ============*/
                    }

                    GetResponceApi::ChangedFieldsVal(array(
                        'email' => $email,
                        'fields' => array(
                            [
                                "customFieldId" => "ecmba", // link_pay_status
                                "value" => [
                                    "confirmed"
                                ]
                            ],
                            [
                                "customFieldId" => "J0wjy", // inter_kassa_pyment_status
                                "value" => [
                                    "confirmed"
                                ]
                            ],
                            [
                                "customFieldId" => 'epfOR', // dropped_payment
                                "value" => [
                                    0
                                ]
                            ],
                            [
                                "customFieldId" => 'ep1mn', // total_balance
                                "value" => [
                                    $total_balance
                                ]
                            ]
                        )
                    ));
                }
                else{

                  GetResponceApi::ChangedFieldsVal(array(
                      'email' => $email,
                      'fields' => array(
                          [
                              "customFieldId" => "JEbjP", // user_id
                              "value" => [
                                  $idUser
                              ]
                          ],
                          [
                              "customFieldId" => "ecmba", // link_pay_status
                              "value" => [
                                  "confirmed"
                              ]
                          ],
                          [
                              "customFieldId" => "J0wjy", // inter_kassa_pyment_status
                              "value" => [
                                  "confirmed"
                              ]
                          ],
                          [
                              "customFieldId" => 'epfOR', // dropped_payment
                              "value" => [
                                  0
                              ]
                          ],
                          [
                              "customFieldId" => "eceib", // last_payment
                              "value" => [
                                  $amount
                              ]
                          ],
                          [
                             "customFieldId" => 'ep1mn', // total_balance
                             "value" => [
                                 $total_balance
                             ]
                         ],
                         [
                            "customFieldId" => 'VKdQpZ', // quantybalance
                            "value" => [
                                $amount
                            ]
                        ],
                        [
                           "customFieldId" => 'VKdqOO', // валюта
                           "value" => [
                               $currencyuser
                           ]
                       ]
                      )
                  ));


                }



                $payment = Payment::where('status', 0)->where('id', $id_order)->first();

                if($payment){
                    $idUser = $payment->id_user;

                    $amount = (float)$request->WMI_PAYMENT_AMOUNT;

                    foreach($request->all() as $name => $value)
                    {
                        if ($name !== "WMI_SIGNATURE") $params[$name] = $value;
                    }

                    uksort($params, "strcasecmp"); $values = "";

                    foreach($params as $name => $value)
                    {
                        $values .= $value;
                    }

                    $signature = base64_encode(pack("H*", md5($values . self::$skey)));

                    if($signature == $request->WMI_SIGNATURE){
                        Payment::updateStatus($id_order, $idUser, $payment->amount);
                        $response =  'WMI_RESULT=OK';


                        // $template = 'setting.mail_after_callback_wone';
                        // $to = 'support@mediasend.kz';
                        // $subject = 'Оплата с '.$idUser.' кабинета, прошла успешно!';
                        // $data = [
                        //     'idUser' => $idUser,
                        //     'amount' => $amount,
                        //     'email' => $email,
                        //     'pay_type' => 'Walletone(оплата с карты)'
                        // ];

                        // Mail::to($to)->send(new Mailable($template, $subject, $data));
                    }
                }

            } else {

                $idUser = $request->userID; // id пользователя
                $user = User::find($idUser);
                $phone = $user->PHONE;

                if ($phone <> '') {
                    $out = array();
                    $phone = str_replace('(', '', $phone);
                    $phone = str_replace(')', '', $phone);
                    $phone = str_replace('+7', '8', $phone);
                    $phone = str_replace('-', '', $phone);

                    $data = [
                        'text' => '',
                        'phones' => [
                            ['name' =>'', 'surname' =>'', 'phone' => $phone]
                        ],
                        'appid' => 4568723
                    ];

                    if($curl = curl_init()){
                        $url = 'https://cp.u-marketing.org/api/sms/add?apiKey=30a3dbb43478ad7f05b297a7b144b65e&'.http_build_query($data, '', '&');

                        curl_setopt($curl, CURLOPT_URL, $url);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        $out = curl_exec($curl);
                        curl_close($curl);
                    }
                }
            }

        }

        return new Response($response, 200, ['Content-Type' => 'text/html']);
    }

    public function logo(Request $request) {

        $user = User::bitrixUser();
        $uid  = $user->ID;

        if ( $request->hasFile( 'logo' ) && $request->file( 'logo' )->isValid() ) {
            $file = $request->file( 'logo' );

            $file_name = $uid . '_' . time() . '.' . $file->getClientOriginalExtension();

            $file->move( "users", $file_name );

            User::where('ID', $uid)
                ->update(['UF_LOGO' => '/users/'.$file_name]);

            //$file_id = DB::table('b_file')->insertGetId(['SUBDIR' =>  "/users",'FILE_NAME' => $file_name, 'ORIGINAL_NAME'=>$file->getClientOriginalName()]);
            //DB::update('UPDATE b_uts_user SET UF_LOGO = ? WHERE VALUE_ID = ?', [$file_id, $uid]);

        }


        return back();
    }


    public function reset(Request $request) {

        $accountUser = User::userByEmail($request->email);
        info($accountUser);
        if(!$accountUser) {
            return response()->json( ['success'=>false] );
        }
        $original_password = User::generateRandomString();
        $salt              = User::randString( 8 );
        $user_password          = $salt . md5( $salt . $original_password );
        info($original_password);
        info($user_password);
        $accountUser->PASSWORD = $user_password;
        $accountUser->save();


        $data = [
            'original_password'  => $original_password,
        ];

        $subject = "=?UTF-8?B?".base64_encode('Восстановление пароля на сайте https://cp.u-marketing.org')."?=";

        Mail::to($request->email)->send(new Mailable('auth.reset', $subject, $data));
        return response()->json( ['success'=>true] );
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function loginCallibro(Request $request)
    {
        return redirect('https://cp.callibro.org/setting/auth/'.Auth::user()->remember_token.'?route='.$request->route);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function auth(Request $request, $token)
    {

        $user = User::where('remember_token', $token)->first();
        if($user && !empty($token)) {
            Auth::login($user, true);
        }

        return redirect($request->route?$request->route:'/');
    }

    // Прочитано уведомление, которое получают все
    public function notificationRead($id = null) {
        $user_id = auth()->user()->ID;
        if(!is_null($id)) {
            $notifications = Notification::where('id', $id)->get();
        } else {
            $notifications = Notification::whereNotIn('id', function($query) use ($user_id) {
                $query->select('notification_id')
                    ->from('read_notifications')
                    ->where('user_id', $user_id);
            })->get();
        }

        foreach($notifications as $notification) {
            ReadNotification::create([
                'notification_id' => $notification->id,
                'user_id' => $user_id
            ]);

            Notification::find($notification->id)->increment('read');
        }

        $this->setUserNotificationsAreRead($user_id);

        return redirect()->back();
    }

    // Прочитано личное уведомление
    public function setUserNotificationIsRead($id = null) {

        $user_id = auth()->user()->ID;

        if(!is_null($id)) {
            $notification = UserNotification::where([
                'id' => $id,
                'user_id' => $user_id,
            ])->first();

            $notification->read_at = Carbon::now();
            $notification->save();

        }

        return redirect()->back();
    }

    // Все личные уведомления прочитаны одним кликом
    private function setUserNotificationsAreRead($user_id) {

        $notifications = UserNotification::where([
            'user_id' => $user_id,
        ])->get();

        foreach($notifications as $notification) {
            $notification->read_at = Carbon::now();
            $notification->save();
        }
    }

    public function price(Request $request) {
        View::share('menu', 'price');
        View::share('title', ' Тарифы');
        return view('setting.price');
    }

    public function my_lead(Request $request) {
        View::share('menu', 'my_lead');
        View::share('title', ' Мои лиды');
        View::share('js_file', 'lead.js');

        return view('leadmagnets.my_lead');
    }

    public function templates(Request $request) {
        View::share('menu', 'templates');
        View::share('title', 'Шаблоны');
        View::share('js_file', 'lead.js');

        return view('leadmagnets.templates');
    }


    public function add_lead(Request $request) {
        View::share('menu', 'add_lead');
        View::share('title', 'Создание формы');

        return view('leadmagnets.add_lead');
    }

    public function lead_integration(Request $request) {
        View::share('menu', 'lead_integration');
        View::share('title', 'Интеграция');

        return view('leadmagnets.lead_integration');
    }
    public function stop_list(Request $request) {
        View::share('menu', 'stop_list');
        View::share('title', 'Стоп лист');
        $user = Auth::user();
        User::set_timezone_of($user->ID);
        $stoplist = Stoplist::where('user_id', $user->ID)->get();
        $normalize_stoplist = [];
        $obj = new Stoplist();
        foreach ($stoplist as $item) {
            switch ($item->type) {
                case 'sip':
                    $item->type = 'Сип';
                    break;
                case 'robot':
                    $item->type = 'Робот';
                    break;
                case 'voice_autocall':
                    $item->type = 'Рассылка автозвонков';
                    break;
                case 'voice_integration':
                    $item->type = 'Голосовая интеграция';
                    break;
            }
            $normalize_stoplist[] = $item;
        }
        return view('setting.stop')->with('normalize_stoplist', $normalize_stoplist);
    }

    public function senderr(Request $request) {




      return view('sender');
    }
}
