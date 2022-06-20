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
namespace App\External\HeadHunter;

use GuzzleHttp\Client as Guzzle;
use App\OauthClientToken;
use Carbon\Carbon;

class HeadHunter {
    
    CONST CLIENT_ID = 'LPAJVTT5AU6U3CJBC1M8RL0KQ5CR2N5OBBEBCHKDK5EJ8V450919BEOMSQOTHNTI';
	CONST CLIENT_SECRET = 'U417PFE80B6VFG39NJHP5M286FEM5SMUOLVLCDQ0UGRALDSTL61HUUAUS9G4FRQK';
	CONST ACCESS_TOKEN = 'OJ9KDUFVAOMP1S011H8O8V70G8BLVE46F78PGGB74KE7LLIMUR2MG4N80OO9MBFE'; // Костыль, менять при новом токене
	CONST BASE_URL = 'https://api.hh.ru/'; 
	CONST REDIRECT_URI = 'https://bpartners.kz/';  

	CONST COMPANY_ID = 2520517;   // TOO OKtrening  ID в HeadHuntere
	CONST MANAGER_ID = 7618556;   // Искомый менеджер. Амиров Олжас o_amir4@mail.ru. Подтягиваем только его вакансии
	CONST MANAGER_ID_2 = 7700035;   // Искомый менеджер. Денис Тастемиров

    CONST MANAGERS = [7618556, 7700035];
    CONST SEGMENT = '1462'; // Сегмент в битриксе

    /**
     * Ссылка авторизации вручную, нужно войти в hh аккаунт, потом перейти по ссылке
     * Получает код авторизации, для refreshAccessToken
     */
    CONST AUTH_CODE_LINK = 'https://hh.ru/oauth/authorize?response_type=code&client_id=LPAJVTT5AU6U3CJBC1M8RL0KQ5CR2N5OBBEBCHKDK5EJ8V450919BEOMSQOTHNTI&state=um_state&redirect_uri=https://bpartners.kz/'; 
    
    /**
     * OauthClientToken $oauth
     */

    protected $oauth;

    /**
     * GuzzleHttp\Client $client
     */

    protected $client;

    /**
     * String 
     * Код авторизации
     */

    public $auth_code;


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

        if(strtotime($oauth->expires_at) - time() < 0) { // Если срок токена истек

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
     * @refresh OauthClientToken
     */

    public function refreshAccessToken()
    {
        try {
			$response = $this->client->request('POST', 'https://hh.ru/oauth/token', [
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'redirect_uri' => self::REDIRECT_URI,
                    'code' => $this->auth_code,
                    'client_id' => self::CLIENT_ID,
                    'client_secret' => self::CLIENT_SECRET,
                ]
            ]);
		} catch (ClientException $e) {
			dump(Psr7\Message::toString($e->getRequest()));
			dd(Psr7\Message::toString($e->getResponse()));
		}

		//dd($res->getStatusCode());// "200"
		//dd($res->getHeader('content-type')[0]); // 'application/json; charset=utf8'
		
		$arr = json_decode($response->getBody());

        return $arr;
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
        return !in_array($arr->manager->id, [self::MANAGER_ID, self::MANAGER_ID_2]) ? null : $arr;
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
