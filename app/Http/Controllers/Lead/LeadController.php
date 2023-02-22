<?php

namespace App\Http\Controllers\Lead;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Api\BitrixOld as Bitrix;

class LeadController extends Controller
{
    public function __construct(){
        $this->queryUrl = config('bitrix.host') . config('bitrix.token') . '/';
    }
    public function createLead(Request $request, Bitrix $bitrix)
    {
        $data = $_POST;

        $queryUrl = $this->queryUrl . 'crm.lead.add.json';

        $queryData = http_build_query([
            'is_need_callback' => '0', // Для автоматического использования обратного звонка при отправке контакта и сделки нужно поменять 0 на 1
            'fields' => [
                'TITLE' => "Jobtron.org - " . $data['name'] . ' - ' . $data['phone'],
                'NAME' => $data['name'],
                'PHONE' => [
                    "n0" => [
                        "VALUE" => $data['phone'],
                        "VALUE_TYPE" => "WORK",
                    ],
                ],
                'ASSIGNED_BY_ID' => 23900, // Валерия
            ],
            'params' => ["REGISTER_SONET_EVENT" => "Y"]
        ]);

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

        if (is_array($result) && array_key_exists('error', $result)) {
            return response()->json([
                'message' => 'Lead creation failed'
            ], 400);
        }

        return response()->json([
            'message' => 'Lead successfilly created',
            'data' => $result
        ]);
    }
}