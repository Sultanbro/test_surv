<?php
/**
 * Даты форматируются в соответствии с ISO 8601: YYYY-MM-DDThh:mm:ss±hhmm
 * Общая информация: https://github.com/hhru/api/blob/master/docs/general.md
 * 
 * Полученный access_token имеет неограниченный срок жизни. 
 * При повторном запросе ранее выданный токен отзывается и выдается новый. 
 * Запрашивать access_token можно не чаще, чем один раз в 5 минут.
 * 
 * $res->getStatusCode();// "200"
 * $res->getHeader('content-type')[0]; // 'application/json; charset=utf8'
 */
namespace App\Api;

use GuzzleHttp\Client as Guzzle;
use App\OauthClientToken;
use Carbon\Carbon;

class HeadHunter {
    
    CONST CLIENT_ID = 'LPAJVTT5AU6U3CJBC1M8RL0KQ5CR2N5OBBEBCHKDK5EJ8V450919BEOMSQOTHNTI';
	CONST CLIENT_SECRET = 'U417PFE80B6VFG39NJHP5M286FEM5SMUOLVLCDQ0UGRALDSTL61HUUAUS9G4FRQK';
	CONST ACCESS_TOKEN = 'K3DM82K7V65FPADKQ6KF5SFCEE2PM2GON78D1MDJ33MSQGMF3J08DFC7GMPKUP0N'; // Костыль, менять при новом токене
	CONST BASE_URL = 'https://api.hh.ru/'; 
	CONST REDIRECT_URI = 'https://bpartners.kz/token';  

	CONST COMPANY_ID = 2520517;   // TOO OKtrening  ID в HeadHuntere
	CONST MANAGER_ID = 7618556;   // Искомый менеджер. Амиров Олжас o_amir4@mail.ru. Подтягиваем только его вакансии
	CONST MANAGER_ID_2 = 7700035;   // Искомый менеджер. Денис Тастемиров

    CONST MANAGERS = [7618556, 7700035, 7792661];
    CONST SEGMENT = '1462'; // Сегмент в битриксе

    /**
     * Grant types
     */
    CONST AUTH_CODE = 'authorization_code';
    CONST REFRESH_TOKEN = 'refresh_token';

    CONST FROM_STATUS = 1;

    /**
     * Ссылка авторизации вручную, нужно войти в hh аккаунт, потом перейти по ссылке
     * Получает код авторизации
     */
    CONST AUTH_CODE_LINK = 'https://hh.ru/oauth/authorize?response_type=code&client_id=LPAJVTT5AU6U3CJBC1M8RL0KQ5CR2N5OBBEBCHKDK5EJ8V450919BEOMSQOTHNTI&state=um_state&redirect_uri=https://bpartners.kz/token'; 
    
    /**
     * OauthClientToken $oauth
     */

    protected $oauth;

    /**
     * GuzzleHttp\Client $client
     */

    protected $client;

    /**
     * Код авторизации
     */
    public $auth_code;

    /**
     * Refresh token
     */
    public $refreshToken;

    /**
     * BP
     */
    protected $company_id = 2520517;

    /**
     * _construct
     */
    function __construct() {
        $this->client = new Guzzle([
			'base_uri' => self::BASE_URL,
			'timeout'  => 10.0,
		]); 

        $this->getActualToken();
    }

    /**
     * Получить токен, если срок истек обновляет
     * @return void 
     */
    public function getActualToken() { 
        $oauth = OauthClientToken::where([
            'server' => 'hh'
        ])->first();

        if($oauth && strtotime($oauth->expires_at) - time() < 0) { 
            // Если срок токена истек do something
        } 
        
        $this->oauth = $oauth;
    }
    
    /**
     * @return OauthClientToken
     */
    public function getOauth() {
        return $this->oauth;
    }

    /**
     * Get request 
     */
    public function get(String $url, $params = []) 
    {
        if(count($params) == 0) $params = [
            'headers' => [
                'User-Agent' => 'Guzzle/6.3 PHP/7.3',
                'Authorization' => 'Bearer '. $this->oauth->access_token,
            ],
        ];
        $response = $this->client->request('GET', $url, $params);

        return $response;
    }

    /**
     * Put request 
     */
    public function put(String $url, $params = []) 
    {
        if(count($params) == 0) $params = [
            'headers' => [
                'User-Agent' => 'Guzzle/6.3 PHP/7.3',
                'Authorization' => 'Bearer '. $this->oauth->access_token,
            ],
        ];
        $response = $this->client->request('PUT', $url, $params);

        return $response;
    }

    /**
     * Helper function to get response in array
     * @return array
     */
    public function toArray($response) {
        $arr = json_decode($response->getBody());
        return $arr;
    }

    /**
     * Получить код авторизации, для запроса нового access токена
     */
    public function getAuthCode()
    {   
        $url = 'https://hh.ru/oauth/authorize?response_type=code&client_id='. self::CLIENT_ID .'&state=um_state&redirect_uri=' . self::REDIRECT_URI;
        $response = $this->client->request('GET', $url, [
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => 'Guzzle/6.3 PHP/7.3',
            ]
        ]);

		//$this->toArray($response);

        dd($this->toArray($response));
        //$this->auth_code;
        return $this;
    }

    /**
     * Refresh access token
     * 
     * @return array|null
     */
    public function refresh($auth_code = '') 
    {
        $record = OauthClientToken::where('server', 'hh')->first();

        if($record) {
            $this->auth_code    = $auth_code;
            $this->refreshToken = $record->refresh_token;

            $grant_type = $auth_code == '' ? self::REFRESH_TOKEN : self::AUTH_CODE;

            return $this->refreshAccessToken($grant_type);
        }
    }   

    /**
     * Refresh Access token request
     * 
     * @return array
     */
    private function refreshAccessToken($grant_type = self::AUTH_CODE)
    {   
        $params = [
            'grant_type'    => $grant_type,
            'redirect_uri'  => self::REDIRECT_URI,
            'code'          => $grant_type == self::REFRESH_TOKEN ? $this->refreshToken : $this->auth_code,
            'client_id'     => self::CLIENT_ID,
            'client_secret' => self::CLIENT_SECRET,
        ];

        if($grant_type == self::REFRESH_TOKEN) {
            $params = [
                'grant_type'    => self::REFRESH_TOKEN,
                'refresh_token' => $this->refreshToken
            ];
        }
       // dd($params);
        try {
			$response = $this->client->request('POST', 'https://hh.ru/oauth/token', [
                'form_params' => $params
            ]);
		} catch (ClientException $e) {
			dump(Psr7\Message::toString($e->getRequest()));
			dd(Psr7\Message::toString($e->getResponse()));
		}

		//dd($res->getStatusCode());// "200"
		//dd($res->getHeader('content-type')[0]); // 'application/json; charset=utf8'
		
        

		$arr = json_decode($response->getBody());

        $this->saveToken($arr);

        return $arr;
    }

    /**
     * save token to OauthClientToken
     */
    private function saveToken($data)
    {   
        /**
           $data = {
            "access_token": "P3OBQURPAQSA5PKS49SFA3HEC6DSSSH1KP2J5KV4R23U7DQUB8GLBAIQV5PQ7HNM"
            "token_type": "bearer"
            "refresh_token": "RQ6GP8ECPPH7FH302DHGGVLL6QFM2N226V36SQG61T2795IGLF4J516K79PE1TK7"
            "expires_in": 1209599
            }
         */
        $data = [
            'user_id'      => 5,
            'auth_code'    => $this->auth_code,
            'access_token' => $data->access_token,
            'refresh_token'=> $data->refresh_token,
            'server'       => 'hh', 
            'grant_type'   => $this->auth_code == null ? self::REFRESH_TOKEN : self::AUTH_CODE, 
            'scope'        => 'bearer', 
            'domain'       => 'api.hh.ru', 
            'expires_at'   => Carbon::createFromTimestamp(time() + $data->expires_in), 
        ];

        $token = OauthClientToken::where('server', 'hh')->first();
        
        if($token) {
            $token->update($data);
        } else {
            OauthClientToken::create($data);
        }
    }
    
    /**
     * Получить отклик на вакансию по id
     */
    public function getNegotiation($id) {
        $response = $this->get('/negotiations/'.$id);
        return $this->toArray($response);
    }

    

    /**
     * Получить вакансию по id
     */
    public function getVacancy($id) {
        $response = $this->get('/vacancies/' . $id);
        $arr = $this->toArray($response);

        return $arr ?? null;
    }

    /**
     * Получить статистику по откликам вакансии по id
     */
    public function getVacancyStats($id) {
        $response = $this->get('/vacancies/' . $id . '/stats');
        return $this->toArray($response); 
    }


    /**
     * Получить информацию о нашей компании
     */
    public function getCompany() {
        $response = $this->get('/employers/' . self::COMPANY_ID);
        return $this->toArray($response);
    }

    /**
     * Получить мененджеров
     */
    public function getManagers() {
        $response = $this->get('/employers/' . self::COMPANY_ID . '/managers/');
        return $this->toArray($response);
    }

    /**
     * Неразобранные отклики к вакансии  
     * date в формате 2021-01-01
     */
    public function getNegotiations($vacancy_id, $date_from = '', $date_to = '') {

        $url = '/negotiations/response?vacancy_id='.$vacancy_id . '&order_by=created_at&has_updates=true';
        if($date_from != '') $url .= '&date_from=' . $date_from;
        if($date_to != '') $url .= '&date_to=' . $date_to;
       
        return $this->collect($url, $date_from, $date_to);
    }

    /**
     * Получить вакансии 
     */
    public function getVacancies($date_from = '', $date_to = '') {
        $url = '/vacancies?employer_id=2520517&archived=false&per_page=20';
        if($date_from != '') $url .= '&date_from=' . $date_from;
        if($date_to != '') $url .= '&date_to=' . $date_to;

        return $this->collect($url, $date_from, $date_to);
    }
    
    /** 
     * stop when item is not in date
     */
    private function getItemsByDate($final_arr, $items, $date_from = '', $date_to = '') {

        $break = false;
        foreach($items as $item) {
            $time = $item->created_at;
            $time[10] = ' ';
            $time = Carbon::parse($time)->setTimezone('Asia/Almaty');
            
            $from = true;
            if($date_from != '') {
                $from = $time->timestamp - $date_from->timestamp > 0 ? true : false;
            } 

            $to = true;
            if($date_to != '') {
                $to = $date_to->timestamp - $time->timestamp > 0 ? true : false;
            } 

            if($from && $to) {
                array_push($final_arr, $item);
            } else {
                $break = true;
                break;
            }
        }

        return [
            $final_arr, $break
        ];
    }

    /** 
     * Собрать все страницы
     */
    public function collect($url, $date_from = '', $date_to = '') {
        $response = $this->get($url. '&page=0');
        $arr = $this->toArray($response);
        
        if($date_from != '') $date_from = Carbon::parse($date_from)->setTimezone('Asia/Almaty');
        if($date_to != '') $date_to = Carbon::parse($date_to)->setTimezone('Asia/Almaty')->endOfDay();

        $itemsByDate = $this->getItemsByDate([], $arr->items, $date_from, $date_to);  
        $items = $itemsByDate[0];

        if($itemsByDate[1]) {
            return $items;
        }

        $pages = $arr->pages;
        
        if($pages > 0) {
            for($i=1;$i<$pages;$i++) {
                $response = $this->get($url.'&page='. $i);
                $arr_2 = $this->toArray($response);

                $itemsByDate = $this->getItemsByDate($items, $arr_2->items, $date_from, $date_to); 
                $items = $itemsByDate[0];

                if($itemsByDate[1]) {
                    return $items;
                }
            } 
        }

        return $items;
    }

    /**
     * Получить менеджеров
     */
    public function getDictionaries() {
        $response = $this->get('/dictionaries/'); 
        return $this->toArray($response);
    }
    
    public function getResume($id) {
        $response = $this->get('/resumes/'.$id);
        return $this->toArray($response);
    }
    
    /**
     * helper function
     */
    public function getPhone($contacts) {
        $phone = '';

        foreach($contacts as $contact) {
            if($contact->type->id == 'cell' && $contact->value) {
                $phone = $contact->value->formatted;
                break;
            }
        }
        
        return $phone;
    }


}
