<?php
declare(strict_types=1);

namespace App\Api;

use Illuminate\Support\Collection;

/**
 * This class is responsible for interact with bitrix api
*/
class BitrixOld
{
    /**
     * @var string
     */
    public string $link;

    /**
     * @param string $line
     */
    public function __construct(string $line = 'common')
    {
        $this->link = config('bitrix.host') . config('bitrix.token') . '/';

        // Headhunter 
        if ($line == 'hh') {
            $this->link = 'https://infinitys.bitrix24.kz/rest/1/h42tawdhk8sf2m41/';
        }

        // Intellect dialog
        if ($line == 'intellect') {
            $this->link = 'https://infinitys.bitrix24.kz/rest/2/dnd7yenlqg9mhbnj/';
        }
    }

    /**
     * @param int $lead_id
     * @param array $lead_fields
     * @return mixed
     */
    public function updateLead(int $lead_id, array $lead_fields): mixed
    {
        $fields = [
            'id' => $lead_id,
            'fields' => $lead_fields
        ];

        $query = http_build_query($fields);

        return $this->curl_post($this->link . 'crm.lead.update.json', $query);
    }

    /**
     * @param int $user_id
     * @param string $status
     * @param string $semantic
     * @param string $sort
     * @param string $start
     * @param string $end
     * @param string $date_type
     * @param int $lead_id
     * @param string $search_by
     * @return mixed
     */
    public function getLeads(int $user_id = 0, string $status = '', string $semantic = 'ALL', string $sort = 'ASC', string $start = '2010-01-01', string $end = '2050-01-01', string $date_type = "DATE_CREATE", int $lead_id = 0, string $search_by = 'title'): mixed
    {
        $filter = [];


        if ($user_id != 0) $filter['ASSIGNED_BY_ID'] = $user_id;
        if ($status != '') {
            $filter['STATUS_ID'] = $status;
        } else {
            if ($semantic != 'ALL') $filter['STATUS_SEMANTIC_ID'] = $semantic; // S успешный F неуспешный в процессе
        }

        /************   ТУТ КОСТЫЛИ ,
         * все поля надо было в один массив запихнуть
         *
         * getLeads($user_id = 0, $fields = [])
         *
         * */
        if ($search_by == 'title') $filter['?TITLE'] = ['кандидат qr', 'удаленный', 'Job.bp', 'inhouse', 'инхаус', 'ин хаус', 'in house', 'house'];
        if ($search_by == 'segment') $filter['UF_CRM_1498210379'] = [1018,1462,3452,2436,2362,2426,3460,1666,1604,3878,2012,1442,3480,3482,3548];

        if ($search_by == 'hh') $filter['UF_CRM_1498210379'] = [1462];
        if ($search_by == 'insta') $filter['UF_CRM_1498210379'] = [1018, 2436];
        if ($search_by == 'alina') $filter['UF_CRM_1498210379'] = [2426];
        if ($search_by == 'saltanat') $filter['UF_CRM_1498210379'] = [2446];
        if ($search_by == 'akzhol') $filter['UF_CRM_1498210379'] = [2448];

        /***************** */
        if (strlen($start) == 10) {
            $start = $start . "T00:00:00+06:00";
        }

        if (strlen($end) == 10) {
            $end = $end . "23:59:59+06:00";
        }

        $filter['>' . $date_type] = $start;
        $filter['<' . $date_type] = $end;

        if ($lead_id != 0) {
            $fields = [
                'id' => $lead_id
            ];
            $query = http_build_query($fields);

            return $this->curl_post($this->link . 'crm.lead.get.json', $query);
        } else {
            $fields = [
                'filter' => $filter,
                'ORDER' => [$date_type => $sort],
            ];
        }

        $query = http_build_query($fields);

        return $this->curl_post($this->link . 'crm.lead.list.json', $query);
    }

    /**
     * @param array $fields
     * @param ?bool $isNeedCallback
     * @return mixed
     */
    public function createLead(array $fields, ?bool $isNeedCallback = null): mixed
    {
        $arrayQuery = [
            'fields' => $fields,
            'params' => ['REGISTER_SONET_EVENT' => 'Y'],
        ];

        if (isset($isNeedCallback)) {
            $arrayQuery['is_need_callback'] = $isNeedCallback ? '1' : '0';
        }

        $query = http_build_query($arrayQuery);
        return $this->curl_post($this->link . 'crm.lead.add.json', $query);
    }

    /**
     * @param $url
     * @param $query
     * @return mixed
     */
    private function curl_post($url, $query): mixed
    {

        // $query = http_build_query(array(
        // 	'phone' =>  $request->phone,
        // ));

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_POSTFIELDS => $query,
        ));
        $result = curl_exec($curl);
        curl_close($curl);
        return json_decode($result, true);
    }

    /**
     * @param string $id
     * @return bool
     */
    public function deleteUser(string $id): bool
    {
        $query = http_build_query([
            'id' => $id,
            'ACTIVE' => 'N',
        ]);
        $result = $this->curl_post($this->link . 'user.update.json', $query);

        $success = false;
        if (array_key_exists('result', $result)) $success = true;

        return $success;
    }

    /**
     * @param string $id
     * @return bool
     */
    public function recoverUser(string $id): bool
    {
        $query = http_build_query([
            'id' => $id,
            'ACTIVE' => 'Y',
        ]);
        $result = $this->curl_post($this->link . 'user.update.json', $query);

        $success = false;
        if (array_key_exists('result', $result)) $success = true;

        return $success;
    }

    /**
     * @param string $email
     * @return mixed|null
     */
    public function searchUser(string $email): mixed
    {
        $query = http_build_query([
            'FILTER' => [
                'email' => $email
            ]
        ]);

        $result = $this->curl_post($this->link . 'user.search.json', $query);

        if (array_key_exists('result', $result)) {
            return count($result['result']) > 0 ? $result['result'][0] : null;
        } else {
            return null;
        }
    }

    /**
     * @param int $deal_id
     * @param array $deal_fields
     * @return mixed
     */
    public function changeDeal(int $deal_id, array $deal_fields): mixed
    {
        $fields = [
            'id' => $deal_id,
            'fields' => $deal_fields
        ];

        $query = http_build_query($fields);

        return $this->curl_post($this->link . 'crm.deal.update.json', $query);
    }

    /**
     * @param $lead_id
     * @param bool $training
     * @return int|mixed
     */
    public function findDeal($lead_id, bool $training = true): mixed
    {
        if ($training) {
            $fields = [
                'order' => ['STAGE_ID' => 'ASC'],
                'filter' => ['STAGE_ID' => 'C4:18', 'LEAD_ID' => $lead_id],
                'select' => ['ID', 'TITLE', 'STAGE_SEMANTIC_ID', 'LEAD_ID', 'UF_CRM_60C72B525A4E8']
            ];
        } else {
            $fields = [
                'order' => ['STAGE_ID' => 'ASC'],
                'filter' => ['LEAD_ID' => $lead_id],
                'select' => ['ID', 'TITLE', 'STAGE_SEMANTIC_ID', 'LEAD_ID', 'UF_CRM_60C72B525A4E8']
            ];
        }

        $query = http_build_query($fields);

        $result = $this->curl_post($this->link . 'crm.deal.list.json', $query);

        $id = 0;
        if (array_key_exists('result', $result)) {

            if (count($result['result']) > 0) {
                $id = $result['result'][0]['ID'];
            }
        }
        return $id;
    }

    /**
     * $bitrix->getCalls(9974, 'ASC', 10, '2021-07-16', '2021-07-16');
     */
    public function getCalls($user_id = 0, $call_type = 0, $sort = 'ASC', $status = 'all', $duration = 0, $start = '2020-01-01', $end = '2050-01-01')
    {
        $filter = [];
        if ($user_id != 0) $filter['PORTAL_USER_ID'] = $user_id;
        if ($status != 'all') $filter['CALL_FAILED_CODE'] = $status;
        if ($call_type != 0) $filter['CALL_TYPE'] = $call_type; // 1 Исходящий; 2 Входящий

        return $this->extracted($start, $end, $filter, $duration, $sort);
    }

    /**
     * Temp func for calls
     * Had no time
     */
    public function getCallsAlt($user_id = 0, $call_type = [], $sort = 'ASC', $status = [], $duration = 0, $start = '2020-01-01', $end = '2050-01-01')
    {
        /*
            200	Успешный звонок.
            304	Пропущенный звонок.
            603	Отклонено.
            603-S	Вызов отменен.
            404	Неверный номер.
            486	Занято.
            484	Данное направление не доступно.
            503	Данное направление не доступно.
            480	Временно не доступен.
            480	Недостаточно средств на счете.
            402	Заблокировано.
        */

        if ($user_id != 0) $filter['PORTAL_USER_ID'] = $user_id;
        $filter['CALL_FAILED_CODE'] = $status;
        $filter['CALL_TYPE'] = $call_type; // 1 Исходящий; 2 Входящий

        return $this->extracted($start, $end, $filter, $duration, $sort);
    }

    /**
     * Get calls alt method
     */
    public function calls($start = '2020-01-01', $end = '2050-01-01'): Collection
    {
        if (strlen($start) == 10) {
            $start = $start . "T00:00:00";
        }

        if (strlen($end) == 10) {
            $end = $end . "T23:59:59";
        }

        $filter['>CALL_START_DATE'] = $start;
        $filter['<CALL_START_DATE'] = $end;

        $fields = [
            'ORDER' => 'ASC',
            'SORT' => 'CALL_START_DATE',
            'filter' => $filter
        ];

        return $this->collect('voximplant.statistic.get.json', $fields);
    }

    /**
     * Get deals alt method
     */
    public function deals($start = '2020-01-01', $end = '2050-01-01'): Collection
    {
        if (strlen($start) == 10) {
            $start = $start . "T00:00:00+06:00";
        }

        if (strlen($end) == 10) {
            $end = $end . "T23:59:59+06:00";
        }

        $filter['>DATE_CREATE'] = $start;
        $filter['<DATE_CREATE'] = $end;

        $fields = [
            'ORDER' => 'ASC',
            'SORT' => 'ID',
            'filter' => $filter
        ];

        return $this->collect('crm.deal.list.json', $fields);
    }

    /**
     * Get leads alt method
     */
    public function leads($start = '2020-01-01', $end = '2050-01-01'): Collection
    {
        if (strlen($start) == 10) {
            $start = $start . "T00:00:00+06:00";
        }

        if (strlen($end) == 10) {
            $end = $end . "T23:59:59+06:00";
        }

        $filter['>DATE_CREATE'] = $start;
        $filter['<DATE_CREATE'] = $end;

        $fields = [
            'SORT' => 'ID',
            'filter' => $filter
        ];

        return $this->collect('crm.lead.list.json', $fields);
    }

    /**
     * HELPER
     * Вытащить все записи по 50, вернуть как коллекцию
     */
    public function collect($method, $fields): Collection
    {

        $query = http_build_query($fields);

        // FIRST QUERY
        $first_result = $this->curl_post($this->link . $method, $query);


        $items = $first_result['result'];
        $page = 2;

        // NEXT QUERIES if their more than 50 while records end
        while (($page - 1) * 50 < $first_result['total']) {
            usleep(1000000); // 1 sec
            $fields['start'] = ($page - 1) * 50;
            $query = http_build_query($fields);
            $result = $this->curl_post($this->link . $method, $query);
            $items = array_merge($items, $result['result']);
            $page++;
        }

        return collect($items);
    }

    /**
     * @param $user_id
     * @param string $status
     * @param string $sort
     * @param string $start
     * @param string $end
     * @param string $date_type
     * @param string $type
     * @param int $page
     * @return mixed
     */
    public function getDeals($user_id, string $status = '', string $sort = 'ASC', string $start = '2020-01-01', string $end = '2050-01-01', string $date_type = "DATE_CREATE", string $type = 'all', int $page = 1): mixed
    {
        if (strlen($start) == 10) {
            $start = $start . "T00:00:00+06:00";
        }

        if (strlen($end) == 10) {
            $end = $end . "23:59:59+06:00";
        }

        $filter = [
            ">" . $date_type => $start,
            "<" . $date_type => $end,
        ];

        if ($status != '') {
            $filter["STAGE_ID"] = $status; // 1 Исходящий; 2 Входящий
        }

        if ($user_id != 0) {
            $filter["ASSIGNED_BY_ID"] = $user_id;
        }

        if ($type == 'all') {
            $filter['?TITLE'] = ['кандидат qr', 'удаленный', 'Job.bp', 'inhouse', 'инхаус', 'ин хаус', 'in house', 'house'];
        }

        if ($type == 'hh') $filter['UF_CRM_1498210393'] = [1466];
        if ($type == 'insta') $filter['UF_CRM_1498210393'] = [1020];
        if ($type == 'alina') $filter['UF_CRM_1498210393'] = [2428];
        if ($type == 'saltanat') $filter['UF_CRM_1498210393'] = [2458];
        if ($type == 'akzhol') $filter['UF_CRM_1498210393'] = [2460];
        if ($type == 'darken') $filter['UF_CRM_1498210393'] = [2548];
        if ($type == 'sholpan') $filter['UF_CRM_1498210393'] = [2550];

        $fields = [
            'ORDER' => [$date_type => $sort],
            'SORT' => $date_type,
            'filter' => $filter,
            'start' => ($page - 1) * 50
        ];

        $query = http_build_query($fields);

        return $this->curl_post($this->link . 'crm.deal.list.json', $query);
    }

    /**
     * @param string $start
     * @param string $end
     * @param array $filter
     * @param mixed $duration
     * @param mixed $sort
     * @return mixed
     */
    public function extracted(string $start, string $end, array $filter, mixed $duration, mixed $sort): mixed
    {
        if (strlen($start) == 10) {
            $start = $start . "T00:00:00";
        }

        if (strlen($end) == 10) {
            $end = $end . "T23:59:59";
        }

        $filter['>CALL_START_DATE'] = $start;
        $filter['<CALL_START_DATE'] = $end;
        if ($duration != 0) $filter['>CALL_DURATION'] = $duration;


        $fields = [
            'ORDER' => $sort,
            'SORT' => 'CALL_START_DATE',
            'filter' => $filter
        ];

        $query = http_build_query($fields);

        return $this->curl_post($this->link . 'voximplant.statistic.get.json', $query);
    }

    /**
     * @param $deal_id
     * @param $fields
     * @return mixed
     */
    public function updateDeal($deal_id, $fields): mixed
    {
        $fields = [
            'id' => $deal_id,
            'fields' => $fields
        ];

        $query = http_build_query($fields);

        return $this->curl_post($this->link . 'crm.deal.update.json', $query);
    }
}
