<?php
namespace App\Classes\Analytics;

use Auth;
use App\User;
use Carbon\Carbon;
use App\Trainee;
use App\Timetracking;
use App\TimetrackingHistory;
use App\DayType;
use App\ProfileGroup;
use App\AnalyticsSettings;
use App\AnalyticsSettingsIndividually as ASI;

class PrCstll
{
    protected $group_id;
    public $date;
    public $day;

    public function __construct($group_id) {
        $this->group_id = $group_id;
        $this->date = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->day = 1;
    }

    /**
     * change date
     */
    public function setDate($date) {
        $this->day = Carbon::parse($date)->day;
        $this->date = Carbon::parse($date)->startOfMonth()->format('Y-m-d');
        return $this;
    }

    /**
     * get prctll to month
     * $date Y-m-d startOfMonth
     */
    public function get() {
        $values = [];
        
        if($this->group_id == 53) {
            $hours = $this->asiData(16);
            $aggrees = $this->asiData(18);
            for ($i = 1; $i <= Carbon::parse($this->date)->daysInMonth; $i++) {
                //$values[$i] = $hours[$i] * 0.01 + $aggrees[$i] * 0.5;
                $values[$i] = $hours[$i] * 7 / 1000;
            }
        } else {
            $hours = $this->asiData();
            for ($i = 1; $i <= Carbon::parse($this->date)->daysInMonth; $i++) {
                $values[$i] = $hours[$i] * $this->getRatio();
            }
        }

        return $values;
    }
    
    /**
     * get prctll total on month
     * $date Y-m-d startOfMonth
     */
    public function getTotal() {
        $total = 0;
        foreach($this->get() as $value) {
            $total += $value;
        }

        return $total;
    }

    /**
     * get prctll plan to month 
     * $date Y-m-d startOfMonth
     */
    public function getPlan() {
        $hours_sum = 0;
        $agrees_sum = 0;
        
        $of = new OperatorFact($this->group_id);
        $total_hours = $of->setDate($this->date)->getTotal();

        if($this->group_id == 53) {
            $hours = $this->asiData(16);
            $aggrees = $this->asiData(18);
            for ($i = 1; $i <= Carbon::parse($this->date)->daysInMonth; $i++) {
                $hours_sum += $hours[$i];
                $agrees_sum += $aggrees[$i];
            }
        } else {
            $hours = $this->asiData();
            for ($i = 1; $i <= Carbon::parse($this->date)->daysInMonth; $i++) {
                $hours_sum += $hours[$i];
            }
        }

        $plan = $total_hours * $this->getPlanRatio();
        return round($plan, 0);
    }

    /**
     * get prctll on day
     * $date Y-m-d startOfMonth
     */
    public function getDay() {
        return $this->get()[$this->day];
    }

    /**
     * @return array of fact hours 
     * keys is days of month
     */
    public function getActivity() {
        $a = [
            42 => 1,
            31 => 19,
            58 => 25,
            57 => 37,
            53 => 16,
            63 => 40,
        ];
        return array_key_exists($this->group_id, $a) ? $a[$this->group_id] : 0;
    }
    
    /**
     * @return array of fact hours 
     * keys is days of month
     */
    public function asiData($type = null) {
        if(is_null($type)) $type = $this->getActivity();
        $values = [];

        $individual_stats = ASI::where([
            'date' => $this->date,
            'group_id' => $this->group_id,
            'type' => $type
        ])->get();

        for ($i = 1; $i <= Carbon::parse($this->date)->daysInMonth; $i++) {
            $values[$i] = 0;
        }

        foreach($individual_stats as $individual_stat) {
            $ind_data = json_decode($individual_stat->data, true);
            if($ind_data) {
                for ($i = 1; $i <= Carbon::parse($this->date)->daysInMonth; $i++) {
                    if(array_key_exists($i, $ind_data)) {
                        $values[$i] += $ind_data[$i];
                    } 
                }  
            }
        }

        if($this->group_id == 42) { // Минуты стажеров которые заполняются вручную
            $as = AnalyticsSettings::where('group_id', $this->group_id)
                ->where('date', $this->date)
                ->where('type', 'basic')
                ->first();

            if($as && array_key_exists(5, $as->data)) {
                for ($i = 1; $i <= Carbon::parse($this->date)->daysInMonth; $i++) {
                    if(array_key_exists($i, $as->data[5])) {
                        $values[$i] += $as->data[5][$i];
                    } 
                } 
            }
        }

        return $values;
    }

    /**
     * Коэффициент который нужно умножать по формуле
     */
    public function getRatio() {
        $ratios = [
            42 => 0.03,
            31 => 0.97,
            58 => 0.97,
            57 => 0.036,
            53 => 0.1,
            63 => 0.875, // 1.475,
        ];
        return array_key_exists($this->group_id, $ratios) ? $ratios[$this->group_id] : 0;
    }

    /**
     * Коэффициент который нужно умножать по формуле для plan Prcstll
     */
    public function getPlanRatio() {
        $ratios = [
            42 => 3.076,
            31 => 3.076,
            58 => 3.076,
            57 => 3.076,
            53 => 3.076,
            63 => 3.076,
        ];
        return array_key_exists($this->group_id, $ratios) ? $ratios[$this->group_id] : 3.076;
    }
    /**
     * Формулы которые исплльзуются
     */
    public function formula() {
        $formulas = [
            42 => '(Минуты действующих + Минуты стажеров)  * 26) / 1000',
            31 => '(Факт часов * 970) / 1000',
            58 => '(Факт часов * 970) / 1000',
            57 => '(Факт минут * 36) / 1000',
            53 => '((Факт согласий * 500)+(Минуты штатных * 10))/100',
            63 => '(Факт часов * 970) / 1000',
        ];
        return array_key_exists($this->group_id, $formulas) ? $formulas[$this->group_id] : 'Formula Not found';
    }
}
