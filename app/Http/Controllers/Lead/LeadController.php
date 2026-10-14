<?php

namespace App\Http\Controllers\Lead;

use App\Api\BitrixOld\Lead\PhoneLead\PhoneLead;
use App\Api\BitrixOld\Lead\PhoneLead\Data as PhoneLeadData;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Api\BitrixOld as Bitrix;
use Exception;

class LeadController extends Controller
{
    public function createLead(Request $request, Bitrix $bitrix)
    {
        try {
            $data = new PhoneLeadData($_POST['name'], $_POST['phone']);

            $result = (new PhoneLead($data, $bitrix))
                // Для автоматического использования обратного звонка при
                // отправке контакта и сделки нужно поменять false на true
                ->setNeedCallback(false)
                ->publish();

            return response()->json([
                'message' => 'Lead successfilly created',
                'data' => $result
            ]);
        } catch(Exception $err) {
            return response()->json([
                'message' => 'Lead creation failed'
            ], 400);
        }
    }
}
