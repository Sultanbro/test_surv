<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\User;
use App\Models\QuartalBonus;
use App\Kpi;
use App\Salary;
use App\Models\Kpi\Bonus;
use App\PositionDescription;
use App\Position;
use App\ProfileGroup;
use App\Zarplata;
use App\Models\Admin\ObtainedBonus;
use App\Classes\Helpers\Currency;
use App\Models\TestBonus;
use App\Models\Admin\EditedBonus;
use App\Models\Admin\EditedKpi;

class ProfileSalaryController extends Controller
{

    public function get(Request $request)
    {
        /**
         * prepare vars
         */
        $user = User::find(auth()->id());
        $user_position = Position::find($user->position_id);
        $date = Carbon::createFromDate(date('Y'), $request->month, 1);

        $currency_rate = in_array($user->currency, array_keys(Currency::rates()))
            ? (float)Currency::rates()[$user->currency]
            : 0.0000001;
            
        /**
         * wtf with quartals
         */
        $d1 = $date->format('Y-m-d');
        $kv = intval((date('m', strtotime($d1)) + 2)/3);

        $quarters = QuartalBonus::on()->where('user_id',$user->id)
            ->where('year',$date->year)
            ->where('quartal',$kv)
            ->get()->toArray();

        $quarter_bonus = QuartalBonus::on()->where('user_id',$user->id)
            ->where('year',$date->year)
            ->where('quartal', $kv)
            ->sum('sum');

        /*** Группы пользователя */
        $groups = '';
        $_groups = ProfileGroup::where('active', 1)->get();
        
        $gs = [];
        foreach($_groups as $group) {
            if($group->users == null) {
                $group->users = '[]';
            }
            $group_users = json_decode($group->users);
            
            if(in_array($user->id, $group_users)) {
                array_push($gs, $group);
                $groups .= '<div>' . $group->name . '</div>';
            }
        }


        // zp
        $zarplata = Zarplata::where('user_id', $user->id)->first();

        $oklad = 0;
        if($zarplata) $oklad = $zarplata->zarplata;

        $oklad = round($oklad * $currency_rate, 0);

        
        //bonuses
        $bonuses = Salary::where('user_id', $user->id)
            ->whereYear('date',  $date->year)
            ->whereMonth('date', $date->month)
            ->where(function($query) {
                $query->where('award', '!=', 0)
                    ->orWhere('bonus', '!=', 0);
            })
            ->orderBy('id','desc')
            ->get();
        
        $bonus = $bonuses->sum('bonus');
        $bonus += ObtainedBonus::onMonth($user->id, $date->format('Y-m-d'));
        $bonus += TestBonus::where('user_id', $user->id)
            ->whereYear('date', $date->year)
            ->whereMonth('date', $date->month)
            ->get()
            ->sum('amount');

        $bonusHistory = ObtainedBonus::getHistory($user->id, $date->format('Y-m-d'), $currency_rate);

        // Бонусы 
        $editedBonus = EditedBonus::where('user_id', $user->id)
            ->whereYear('date',  $date->year)
            ->whereMonth('date',  $date->month)
            ->first();
        $bonus = $editedBonus ? $editedBonus->amount : $bonus;

        /**
         * EARNINGS COMPONENT
         */
        $editedKpi = EditedKpi::where('user_id', $user->id)
            ->whereYear('date', $date->year)
            ->whereMonth('date', $date->month)
            ->first();

        if($editedKpi) {
            $kpi = $editedKpi->amount;
        } else {
            $kpi = Kpi::userKpi($user->id);
        }   

        $salary = $user->getCurrentSalary();
        
        $potential_bonuses = '';
        if(count($gs) > 0) {
            foreach ($gs as $key => $g) {
                $potential_bonuses .= Bonus::getPotentialBonusesHtml($g->id);
                $potential_bonuses .= '<br>';
            }
        }
        
        // check exists ind kpi
        $kpis = $user->inGroups();
            
        // prepare user_earnigs 
        $user_earnings = [
            'quarter_bonus' => $quarter_bonus.' '. strtoupper($user->currency),
            'oklad' => round((float)$oklad * $currency_rate, 0),
            'bonus' => number_format(round((float)$bonus * $currency_rate), 0, '.', '\'') . ' ' . strtoupper($user->currency),
            'kpis' => $kpis,
            'bonusHistory' => $bonusHistory,
            'editedBonus' => $editedBonus,
            'editedKpi' => $editedKpi,
            'potential_bonuses' => $potential_bonuses,
            'salary_percent' => $oklad > 0 ? $salary / $oklad * 100 : 0,
            'kpi_percent' => $kpi / 400, // kpi / 40000 * 100
            'kpi' => number_format((float)$kpi * $currency_rate,  0, '.', '\''). ' ' . strtoupper($user->currency),
            'salary' => number_format((float)$salary * $currency_rate, 0, '.', '\''). ' ' . strtoupper($user->currency),
            'salary_info' => [
                'worked_days' => $user->worked_days(),
                'indexation_sum' => $user_position ? $user_position->sum : 0,
                'days_before_indexation' => $user->days_before_indexation(),
                'oklad' => number_format(round((float)$oklad * $currency_rate), 0, '.', '\'') . ' ' . strtoupper($user->currency),
            ]
        ];

        return [
            'user_earnings' => $user_earnings,
            'quarters' => $quarters,
        ];
        
        
    }

}
