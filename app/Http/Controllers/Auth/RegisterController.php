<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\PartnerUsers;
use App\Http\Controllers\Controller;
use App\GetResponceApi;
use App\Contact;
use App\Group;
use App\Tarrif;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:b_user',
            'password' => 'required|string|min:6|confirmed',
            'currency' => [
                'required',
                Rule::in(['kzt', 'rub']),
            ],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data) {
        $salt = User::randString(8);
        $password = $salt.md5($salt.$data['password']);
        $user = User::create([
            'NAME'              => $data['name'],
            'EMAIL'             => $data['email'],
            'ACTIVE'            => 'Y',
            'PASSWORD'          => $password,
            'UF_BALANCE'        => $data['currency']=='kzt'?250:41,
            'PHONE'             => $data['phone'],
            'DATE_REGISTER'     => DB::raw('NOW()'),
            'AUTH_TOKEN'        => bin2hex(random_bytes(60)),
            'currency'          => $data['currency'],
            'timezone'          => $data['timezone'],
        ]);
        $group = Group::create([
			'id_user'           => $user->ID,
			'name'              => 'Тестовая группа',
        ]);
        $contact_data = [
        	'name' => $data['name'],
        	'surname' => '',
        	'info1' => '',
        	'info2' => '',
        	'info3' => ''
        ];

        $tarrifs = Tarrif::all();

        $phone = preg_replace("/[^0-9]/", '', $data['phone']);
        $codes = $tarrifs->filter(function ($value, $key) use ($phone) {
            return starts_with($phone, $value->prefix);
        });

        if(!$codes->isEmpty()) {
            $tarrif = $codes->sortByDesc('prefix')->first();
            $code = $tarrif->prefix;
            $length = $tarrif->length;

            $phone = $code.mb_substr($phone, strlen($code)-$length);
        } else {
            $phone = '87'.mb_substr(preg_replace("/[^0-9]/", '', $data['phone']), -9);
            $length = 11;
        }
        if(mb_strlen($phone, 'UTF-8') == $length) {
			$contact = Contact::add($user->ID, $group->id, $phone, $contact_data);
        }

        $referral = Cookie::get('referral');
        $source = Cookie::get('source');
        if (!empty($referral)) {
            PartnerUsers::create(['user_id' => $user->ID, 'partner_id' => $referral, 'source' => $source]);
        }


        GetResponceApi::AddContact(array(
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ));
        $data = $_POST;

        $queryUrl = 'https://infinitys.bitrix24.kz/rest/2/0cgtwatfrdxpeuae/crm.lead.add.json';

        $queryData = http_build_query(array(
            'fields' => array(
                'TITLE' => 'cp.u-marketing.org-регистрация',
                'NAME' => $data['name'],
                "ASSIGNED_BY_ID" => 30122, // Улдана Куанткан
                'PHONE' => Array(
                    "n0" => Array(
                        "VALUE" => $data['phone'],
                        "VALUE_TYPE" => "WORK",
                    ),
                ),
                'EMAIL' => Array(
                    "n0" => Array(
                        "VALUE" => $data['email'],
                        "VALUE_TYPE" => "WORK",
                    ),
                ),
            ),
            'params' => array("REGISTER_SONET_EVENT" => "Y")
        ));
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $queryUrl,
            CURLOPT_POSTFIELDS => $queryData,
        ));
        $result = curl_exec($curl);
        curl_close($curl);
        $result = json_decode($result, 1);
        if (array_key_exists('error', $result)) echo "Ошибка при сохранении лида: ".$result['error_description']."<br/>";

        // создаем Лида в bitrix24
        /*$roistatData = array(
            'roistat' => isset($_COOKIE['roistat_visit']) ? $_COOKIE['roistat_visit'] : null,
            'key'     => 'NDAwOTI6NDU0MDg6N2VlMzI0ZTk5N2U2NTg3MWVhMzNjYjA3ZWYwYTk0NWE=',
            'title'   => "u-marketing.org - регистрация пользователя",
            'comment' => 'Форма "Регистрация пользователя"',
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'is_need_callback' => '0', // Для автоматического использования обратного звонка при отправке контакта и сделки нужно поменять 0 на 1
            'fields'  => array(
                'ASSIGNED_BY_ID' => '1189',
                'UTM_SOURCE' => '{utmSource}',
                'UTM_CAMPAIGN' => '{utmCampaign}',
                'UTM_CONTENT' => '{utmContent}',
                'UTM_MEDIUM' => '{utmMedium}',
                'UTM_TERM' => '{utmTerm}',
            ),
        );
        file_get_contents("https://cloud.roistat.com/api/proxy/1.0/leads/add?" . http_build_query($roistatData));
*/
        if ($data['currency'] == 'kzt' && !empty($data['phone'])) {

          $phone = $data['phone'];

          $phone = str_replace('(', '', $phone);
          $phone = str_replace(')', '', $phone);
          $phone = str_replace('+7', '7', $phone);
          $phone = str_replace('-', '', $phone);

          $out = array();
          $url = 'https://cp.u-marketing.org';

            $phone = preg_replace("/[^0-9]/", '', $phone);
            $data = array(
              'action' => 'addCall',
              'apiKey' => '30a3dbb43478ad7f05b297a7b144b65e',
              'phone' => $phone,
              'appid' => 4565146
            );
            $url = $url.'/api/call?'.http_build_query($data);
            if($curl = curl_init()){
              curl_setopt($curl, CURLOPT_URL, $url);
              curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
              $out = curl_exec($curl);
              curl_close($curl);
            }

        } elseif (!empty($data['phone'])) {

          $phone = $data['phone'];

         /* $phone = str_replace('(', '', $phone);
          $phone = str_replace(')', '', $phone);
          $phone = str_replace('+7', '7', $phone);
          $phone = str_replace('-', '', $phone);*/

          $out = array();
          $url = 'https://cp.u-marketing.org';

            $phone = preg_replace("/[^0-9]/", '', $phone);
            $data = array(
              'action' => 'addCall',
              'apiKey' => '30a3dbb43478ad7f05b297a7b144b65e',
              'phone' => $phone,
              'appid' => 4565145
            );
            $url = $url.'/api/call?'.http_build_query($data);
            if($curl = curl_init()){
              curl_setopt($curl, CURLOPT_URL, $url);
              curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
              $out = curl_exec($curl);
              curl_close($curl);
            }

        }

        return $user;
    }
}
