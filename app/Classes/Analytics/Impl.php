<?php
namespace App\Classes\Analytics;

use Auth;
use App\User;
use Carbon\Carbon;
use App\Timetracking;
use App\TimetrackingHistory;
use App\ProfileGroup;
use App\AnalyticsSettings;
use App\Classes\Analytics\PrCstll;
use App\Classes\Analytics\OperatorFact;
use App\AnalyticsSettingsIndividually as ASI;

class Impl
{
    protected $group_id;
    public $date;

    public function __construct($group_id) {
        $this->group_id = $group_id;
        $this->date = Carbon::now()->startOfMonth()->format('Y-m-d');
    }

    /**
     * change date
     */
    public function setDate($date) {
        $this->date = Carbon::parse($date)->startOfMonth()->format('Y-m-d');
        return $this;
    }

    /**
     * get impl to month
     * $date Y-m-d startOfMonth
     */
    public function get() {
        $prsctll = (new PrCstll($this->group_id))
            ->setDate($this->date)
            ->getTotal();
        
        $operator_fact = (new OperatorFact($this->group_id))
            ->setDate($this->date) 
            ->getTotal();
        
        if($operator_fact == 0) {
            $impl = 0;
        } else {
            // if($this->group_id  == 63) {
            //     dump($this->getRatio());
            //     dump($prsctll);
            //     dump($operator_fact);
            // }
           
            $impl = $this->getRatio() * $prsctll / $operator_fact;
        }
        
        return $impl;
    }

    /**
     * Коэффициент который нужно умножать по формуле
     */
    public function getRatio() {
        $ratios = [
            42 => 100 / 7.02,
            31 => 100 / 9.7,
            58 => 100 / 11.8,
            57 => 100 / 10.8,
            53 => 100 / 8.5,
            63 => 8.4745// 100 / 11.8, //11.8,
        ];
        return array_key_exists($this->group_id, $ratios) ? $ratios[$this->group_id] : 0;
    }

    /**
     * Формулы которые испoльзуются
     */
    public function formula() {
        $formulas = [
            42 => '100 * Cумма PrCstll / (7.02 * Факт операторов)',
            31 => '100 * Cумма PrCstll / (9.7 * Кол-во манеджеров)',
            58 => '100 * Cумма PrCstll / (11.8 * Факт операторов)',
            57 => '100 * Cумма PrCstll / (10.8 * Факт операторов)',
            53 => '100 * Cумма PrCstll / (8.5 * Кол-во операторов)',
            63 => '100 * Cумма PrCstll / (11.8 * Факт операторов)',
        ];
        return array_key_exists($this->group_id, $formulas) ? $formulas[$this->group_id] : 'Formula Not found';
    }
}
