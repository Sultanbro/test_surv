<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\ProfileGroup;
use App\User;
use App\Trainee;
use App\DayType;
use App\Salary;
use App\UserNotification;
use Carbon\Carbon;
use App\Models\Bitrix\Lead;
use Illuminate\Http\Request;
use App\External\Bitrix\Bitrix;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use App\External\HeadHunter\HeadHunter;
use App\Sms\SmsBeeline;
use App\Classes\Helpers\Currency;
use App\Classes\Helpers\Phone;
use App\Http\Controllers\Controller;

class LeadController extends Controller {




	public function funnel_segment(Request $request){
        
        $segments = [
            'insta' => 1,
            'hh' => 2,
            'alina' => 8,
            'saltanat' => 9,
            'akzhol' => 10,
            'darkhan' => 11,
        ];
        
        
        if(array_key_exists($request->type, $segments)) {
            $leads = Lead::leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'bitrix_leads.user_id')
			->leftJoin('users as u', 'u.ID', '=', 'bitrix_leads.user_id')
            ->where([
                'bitrix_leads.segment' => $segments[$request->type]
            ])
            ->where(function($query) {
                $query->whereNotNull('skyped')
                    ->orWhereNotNull('inhouse');
            })
            ->orderBy('bitrix_leads.id', 'desc')
            ->get();
        } else {
            //$leads = Lead::where('id', 0)->get();
            abort(404);
        }



        foreach($leads as $lead) {

            if($lead->status == '39' || $lead->status == 'CON') {
                $lead->status = 'Ждет обучения';
            }

            if($lead->skyped) {
                $lead->skyped = $lead->skyped ? Carbon::parse($lead->skyped)->format('d.m.Y H:i:s') : '';
                $lead->date = $lead->skyped ? Carbon::parse($lead->skyped)->format('Y-m') : '';
            } else { 
                $lead->inhouse = $lead->inhouse ? Carbon::parse($lead->inhouse)->format('d.m.Y H:i:s'): '';
                $lead->inhouse = $lead->inhouse ? Carbon::parse($lead->inhouse)->format('Y-m'): '';
            }   
            
            
            $lead->invite_at = $lead->invite_at ? Carbon::parse($lead->invite_at)->format('d.m.Y H:i'): '';

            $lead->country = Phone::getCountry($lead->phone);

			if($lead->user_id) {

                $lead->status = 'Обучается';

                $daytype = DayType::where('user_id', $lead->user_id)->orderBy('date', 'desc')->first();
    
                if($daytype && $daytype->type == 2) {
                    $lead->status = 'Пропал с обучения';
                } 
				
			}
            
			if($lead->applied) {
                if($lead->fire_date) {
                    $applied = (Carbon::parse($lead->fire_date)->timestamp - Carbon::parse($lead->applied)->timestamp) / 3600 / 24; 
                } else {
                    $applied = (time() - Carbon::parse( $lead->applied)->timestamp) / 3600 / 24;
                }
                
                $lead->month_1 = $applied >= 0 ? 'colored' : '';
                $lead->month_2 = $applied >= 60 ? 'colored' : '';
                $lead->month_3 = $applied >= 90 ?'colored' : '';
                $lead->status = 'Принят на работу';
                $lead->applied = Carbon::parse($lead->applied)->format('d.m.Y');

                if($lead->fire_date) {
                    $lead->status = 'Уволен действующий';
                }
				
				
				
            } else {

                if($lead->fire_date) {
					$lead->status = 'Уволен стажер';
				}

                $lead->month_1 = '';
                $lead->month_2 = '';
                $lead->month_3 = '';
            }
            
            if($lead->NAME) {
                $lead->name = $lead->LAST_NAME . ' ' . $lead->NAME;
            }
        }

        //$leads = Lead::all();

        $dates = [
            '' => 'Все даты подписи',
            '2021-10' => 'Октябрь 2021',
            '2021-11' => 'Ноябрь 2021',
            '2021-12' => 'Декабрь 2021',
            '2022-01' => 'Январь 2022',
            '2022-02' => 'Февраль 2022',
            '2022-03' => 'Март 2022',
        ];

        $active_date = '';
        $active_status = '';

        $statuses = [
            '' => 'Все статусы',
            'Принят на работу' => 'Принят на работу',
            'Обучается' => 'Обучается',
            'Пропал с обучения' => 'Пропал с обучения',
            'Уволен стажер' => 'Уволен стажер',
            'Уволен действующий' => 'Уволен действующий',
            'Ждет обучения' => 'Ждет обучения',
        ];

        return view('admin.funnel_leads')->with([
            'leads' => $leads,
            'dates' => $dates,
            'statuses' => $statuses,
            'active_date' => $active_date,
            'active_status' => $active_status,
        ]);
    }
	
}