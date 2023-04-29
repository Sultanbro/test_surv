<?php

namespace App\Http\Controllers\Salary;

use App\Repositories\SavedKpiRepository;
use App\Service\Bonus\ObtainedBonusService;
use App\Service\Bonus\TestBonusService;
use App\Service\Fine\FineService;
use App\Service\Salary\SalaryService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\User;
use App\Models\QuartalBonus;
use App\Salary;
use App\Models\Kpi\Bonus;
use App\Position;
use App\ProfileGroup;
use App\Zarplata;
use App\Models\Admin\ObtainedBonus;
use App\Classes\Helpers\Currency;
use App\Models\TestBonus;
use App\Models\Admin\EditedBonus;
use App\Models\Admin\EditedKpi;
use App\Models\QuartalPremium;
use App\Http\Controllers\Controller;

class ProfileSalaryController extends Controller
{
    public function __construct(
        public SalaryService        $salaryService,
        public FineService          $fineService,
        public ObtainedBonusService $obtainedBonusesService,
        public TestBonusService     $testBonusService,
    )
    {
    }

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

        $quarter_bonus = $user->qpremium()
            ->where('from', '<=', now()->format('Y-m-d'))
            ->where('to', '>=', now()->format('Y-m-d'))
            ->sum('sum') ?? 0;

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

            $sk = (new SavedKpiRepository)->getSavedKpiForMonth($user, $date)->first();
            $kpi = $sk ? $sk->total : 0;
        }

        $salary = $user->getCurrentSalary();

        $salarySum = $user->getTotalByCurrency($salary);

        $potential_bonuses = '';
        if(count($gs) > 0) {
            foreach ($gs as $key => $g) {
                $potential_bonuses .= Bonus::getPotentialBonusesHtml($g->id);
                $potential_bonuses .= '<br>';
            }
        }
        
        // check exists ind kpi
        $kpis = $user->inGroups();

        $sumKpi = $editedKpi ? $editedKpi->amount : $kpi;
        $sumBonus = $editedBonus ? $editedBonus->amount : $bonus;
        $sumQuarterPremium = $user->sumQuarterPremiums();

        $currency_rate = (float)(Currency::rates()[$user->currency] ??  0.00001);

        $userFinesInformation = $this->fineService->getUserFines($date->month, $user, $currency_rate);
        $salaryBonuses = $this->salaryService->getUserBonuses($date, $user);
        $obtainedBonuses = $this->obtainedBonusesService->getUserBonuses($date,$user);
        $testBonuses =  $this->testBonusService->getUserBonuses($date,$user);
        $advances = $this->salaryService->getUserAdvances($date, $user);

        $salarySum = $user->getTotalByCurrency($userFinesInformation + $salaryBonuses + $obtainedBonuses + $testBonuses + $advances);

        // prepare user_earnigs 
        $user_earnings = [
            'quarter_bonus' => $user->getTotalByCurrency($sumQuarterPremium).' '. strtoupper($user->currency),
            'oklad' => round((float)$oklad * $currency_rate, 0),
            'bonus' => number_format(round((float)$bonus * $currency_rate), 0, '.', '\'') . ' ' . strtoupper($user->currency),
            'currency' => strtoupper($user->currency) , 's',
            'kpis' => $kpis,
            'bonusHistory' => $bonusHistory,
            'editedBonus' => $editedBonus,
            'editedKpi' => $editedKpi,
            'potential_bonuses' => $potential_bonuses,
            'salary_percent' => $oklad > 0 ? $salary / $oklad * 100 : 0,
            'kpi_percent' => $kpi / 400, // kpi / 40000 * 100
            'kpi' => number_format((float)$kpi * $currency_rate,  0, '.', '\''). ' ' . strtoupper($user->currency),
            'kpiMax' => 30000,
            'sumKpi' => $user->getTotalByCurrency($sumKpi),
            'sumSalary' => $salarySum,
            'sumBonuses' => $user->getTotalByCurrency($sumBonus),
            'sumQuartalPremiums' => $user->getTotalByCurrency($sumQuarterPremium),
            'sumNominations' => 0, // кол-во номинаций
            'salary' => number_format((float)$salary * $currency_rate, 0, '.', '\''). ' ' . strtoupper($user->currency),
            'salary_info' => [
                'worked_days' => $user->worked_days(),
                'indexation_sum' => $user_position ? $user_position->sum : 0,
                'days_before_indexation' => $user->days_before_indexation(),
                'oklad' => number_format(round((float)$oklad * $currency_rate), 0, '.', '\'') . ' ' . strtoupper($user->currency),
            ]
        ];

        /**
         * DUPLICATED CODE
         */
        
    
        $user_id = $user->id;
        $position_id = $user->position_id;
        
        $groups = $user->groups->pluck('id')->toArray();

        $quarters = QuartalPremium::query()
            ->selectRaw('count(*) as count')
            ->where(function($query) use ($user_id, $groups, $position_id) {
                $query->where(function($q) use ($user_id) {
                        $q->where('targetable_id', $user_id)
                            ->where('targetable_type', 'App\User');
                    })
                    ->orWhere(function($q) use ($groups) {
                        $q->whereIn('targetable_id', $groups)
                            ->where('targetable_type', 'App\ProfileGroup');
                    })
                    ->orWhere(function($q) use ($position_id) {
                        $q->where('targetable_id', $position_id)
                            ->where('targetable_type', 'App\Position');
                    });
            })->first()->count;
           
        return [
            'user_earnings' => $user_earnings,
            'has_qp' => $quarters > 0,
        ];
        
        
    }

}
