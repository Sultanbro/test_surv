<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Classes\Helpers\Currency;
use App\Salary;
use App\Models\TestBonus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObtainedBonus extends Model
{
    protected $table = 'kpi_obtained_bonuses';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'bonus_id',
        'date',
        'amount',
        'comment',
        'read',
    ];

    /**
     * @return BelongsTo
     */
    public function bonus(): BelongsTo
    {
        return $this->belongsTo('App\Models\Kpi\Bonus', 'bonus_id');
    }

    /**
     * create or update bonus on day or month
     */
    public static function createOrUpdate($data, $daypart = 0) : void
    {
        if($daypart == 2) {
            self::createOrUpdateForMonth($data);
        } else {
            self::createOrUpdateForDay($data);
        }
    }

    /**
     * create or update bonus on month
     */
    public static function createOrUpdateForMonth($arr) : void
    {
        $date = Carbon::parse($arr['date'])->startOfMonth();

        self::where('user_id', $arr['user_id'])
                ->whereMonth('date', $date->month)
                ->whereYear('date', $date->year)
                ->where('bonus_id', $arr['bonus_id'])
                ->delete();

        self::create([
            'user_id'  => $arr['user_id'],
            'date'     => $date->format('Y-m-d'),
            'bonus_id' => $arr['bonus_id'],
            'amount'   => $arr['amount'],
            'comment'  => $arr['comment'],
        ]);
    }

    /**
     * create or update bonus on month
     */
    public static function createOrUpdateForDay($arr) : void
    {
        $ob = self::where('user_id', $arr['user_id'])
                    ->where('date', $arr['date'])
                    ->where('bonus_id', $arr['bonus_id'])
                    ->first();
        if($ob) {
            $ob->amount = $arr['amount'];
            $ob->comment = $arr['comment'];
            $ob->save();
        } else {
            self::create([
                'user_id'  => $arr['user_id'],
                'date'     => $arr['date'],
                'bonus_id' => $arr['bonus_id'],
                'amount'   => $arr['amount'],
                'comment'  => $arr['comment'],
            ]);
        }
    }

    public static function onDay($user_id, $date) {
        return self::where('user_id', $user_id)
                    ->where('date', $date)
                    ->get()
                    ->sum('amount');
    }

    public static function onMonth($user_id, $date) {
        return self::where('user_id', $user_id)
                    ->whereYear('date', Carbon::parse($date)->year)
                    ->whereMonth('date', Carbon::parse($date)->month)
                    ->get()
                    ->sum('amount');
    }

    public static function getHistory($user_id, $date, $currency_rate = 1) {
        $bonusHistory = [];

        $month = Carbon::parse($date);

        $bonuses_all = self::where('user_id', $user_id)
            ->whereYear('date', $month->year)
            ->whereMonth('date', $month->month)
                ->get();

        $test_bonuses_all = TestBonus::where('user_id', $user_id)
            ->whereYear('date', $month->year)
            ->whereMonth('date', $month->month)
                ->get();

        $manual_bonus_all = Salary::where('user_id', $user_id)
            ->selectRaw('date, bonus, comment_bonus, day(date) as day')
            ->whereYear('date', $month->year)
            ->whereMonth('date', $month->month)
            ->get();

       

        for ($i = $month->daysInMonth; $i > 0; $i--) {

            $bonuses = $bonuses_all->where('date', $month->day($i)->format('Y-m-d'));
            $test_bonuses = $test_bonuses_all->where('date', $month->day($i)->format('Y-m-d'));
            $manual_bonus = $manual_bonus_all->where('day', $i)->first();

            $item = [];

            $sum = 0;
            $comment = '';

            
            if($manual_bonus) {
                $item['date'] = $manual_bonus->date->format('Y-m-d') ?? '';
                $sum += $manual_bonus->bonus * $currency_rate;
                $comment .= $manual_bonus->comment_bonus;
            }

            if($bonuses->count() > 0) {
                foreach ($bonuses as $key => $bon) {
                    $item['date'] = $bon->date;
                    $sum += $bon->amount * $currency_rate;
                    if(strlen($comment) > 0) {
                        $comment .= $bon->comment ? '<br>' . $bon->comment : '';
                    } else {
                        $comment = $bon->comment ? $bon->comment : '';
                    }
                }
            }

            if($test_bonuses->count() > 0) {
                foreach ($test_bonuses as $key => $bon) {
                    $item['date'] = $bon->date;
                    $sum += $bon->amount * $currency_rate;
                    if(strlen($comment) > 0) {
                        $comment .= $bon->comment;
                    } else {
                        $comment = $bon->comment;
                    }
                }
            }

            $item['sum'] = $sum;
            $item['comment'] = $comment;

            if($sum > 0) {
                array_push($bonusHistory, $item);
            }
            
        }


        return $bonusHistory;
    }
}
