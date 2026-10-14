<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\CallBaseTotal;
use Carbon\Carbon;

class CallBase extends Model
{
    protected $dates = ['date'];
    
    protected $casts = [
        'data' => 'array',
    ];

    protected $fillable = [
        'date',
        'group_id',
        'name',
        'data',
    ];


    public static function formTable($date) {
        $total = CallBaseTotal::where('date', $date)
            ->where('name', 'total')
            ->first();

        if(!$total) {
            CallBaseTotal::create([
                'date' => $date,
                'group_id' => 53,
                'name' => 'total',
                'value' => 0,
            ]);
        }

        $conversion = CallBaseTotal::where('date', $date)
            ->where('name', 'conversion')
            ->first();

        if(!$conversion) {
            CallBaseTotal::create([
                'date' => $date,
                'group_id' => 53,
                'name' => 'conversion',
                'value' => 0,
            ]);
        }

        $current_credits = [];
        $current_given = [];
        $next_credits = [];
        $next_given = [];

        $_current_credits = CallBase::where('date', $date)->where('name', 'current_credits')->first();
        $_current_given = CallBase::where('date', $date)->where('name', 'current_given')->first();
        $_next_credits = CallBase::where('date', $date)->where('name', 'next_credits')->first();
        $_next_given = CallBase::where('date', $date)->where('name', 'next_given')->first();

        $cc = $_current_credits ? $_current_credits->data : [];
        $cg = $_current_given ? $_current_given->data : [];
        $nc = $_next_credits ? $_next_credits->data : [];
        $ng = $_next_given ? $_next_given->data : [];

        if(!$_current_credits) {
            CallBase::create([
                'date' => $date,
                'group_id' => 53,
                'name' => 'current_credits',
                'data' => []
            ]);
        }

        if(!$_current_given) {
            CallBase::create([
                'date' => $date,
                'group_id' => 53,
                'name' => 'current_given',
                'data' => []
            ]);
        }

        if(!$_next_credits) {
            CallBase::create([
                'date' => $date,
                'group_id' => 53,
                'name' => 'next_credits',
                'data' => []
            ]);
        }

        if(!$_next_given) {
            CallBase::create([
                'date' => $date,
                'group_id' => 53,
                'name' => 'next_given',
                'data' => []
            ]);
        }

        for($i=1;$i<=31;$i++) {
            $current_credits[$i] = array_key_exists($i, $cc) ? $cc[$i] : '';
            $current_given[$i] = array_key_exists($i, $cg) ? $cg[$i] : '';
            $next_credits[$i] = array_key_exists($i, $nc) ? $nc[$i] : '';
            $next_given[$i] = array_key_exists($i, $ng) ? $ng[$i] : '';
        }

        $months = [
            1 => 'Январь',
            2 => 'Февраль',
            3 => 'Март',
            4 => 'Апрель',
            5 => 'Май',
            6 => 'Июнь',
            7 => 'Июль',
            8 => 'Август',
            9 => 'Сентябрь',
            10 => 'Октябрь',
            11 => 'Ноябрь',
            12 => 'Декабрь',
        ];

        return [
            'total' => $total ? $total->value : 0,
            'conversion' => $conversion ? $conversion->value : 0,
            'current_credits' => $current_credits,
            'next_credits' => $next_credits,
            'current_given' => $current_given,
            'next_given' => $next_given,
            'current_month' => $months[Carbon::parse($date)->month],
            'next_month' => $months[Carbon::parse($date)->addMonth()->month],
        ];
    }

    public static function saveTable($items, $date) {

        $total = CallBaseTotal::where('date', $date)
            ->where('name', 'total')
            ->first();

        if($total) {
            $total->value = $items['total'];
            $total->save();
        }

        $conversion = CallBaseTotal::where('date', $date)
            ->where('name', 'conversion')
            ->first();

        if($conversion) {
            $conversion->value = $items['conversion'];
            $conversion->save();
        }

        $_current_credits = CallBase::where('date', $date)->where('name', 'current_credits')->first();
        $_current_given = CallBase::where('date', $date)->where('name', 'current_given')->first();
        $_next_credits = CallBase::where('date', $date)->where('name', 'next_credits')->first();
        $_next_given = CallBase::where('date', $date)->where('name', 'next_given')->first();

        if($_current_credits) {
            $_current_credits->data = $items['current_credits'];
            $_current_credits->save();
        }

        if($_current_given) {
            $_current_given->data = $items['current_given'];
            $_current_given->save();
        }

        if($_next_credits) {
            $_next_credits->data = $items['next_credits'];
            $_next_credits->save();
        }

        if($_next_given) {
            $_next_given->data = $items['next_given'];
            $_next_given->save();
        }

    
    }

    public static function coefForEurasian($date) {
        $cc = CallBase::where('date', $date)->where('name', 'current_given')->first();
        $nc = CallBase::where('date', $date)->where('name', 'next_given')->first();

        $sum = 0;

        $cc = $cc ? $cc->data : [];
        $nc = $nc ? $nc->data : [];

        for($i=1;$i<=31;$i++) {
            $sum += array_key_exists($i, $cc) ? (int)$cc[$i] : 0;
            $sum += array_key_exists($i, $nc) ? (int)$nc[$i] : 0;
        }

        $conversion = CallBaseTotal::where('date', $date)
            ->where('name', 'conversion')
            ->first();

        $conversion = $conversion ? (float)$conversion->value : 0;

        $conversion = self::getConversionRate($conversion);
        $conversion = $conversion / 100;

        return round($conversion * $sum, 0);
    }

    public static function getConversionRate($value) {

        if($value >=0.25 && $value <=0.49) $value = 0.25;
        if($value >=0.50 && $value <=0.74) $value = 0.50;
        if($value >=0.75 && $value <=0.99) $value = 0.75;
        if($value >=1.00 && $value <=1.04) $value = 1.00;
        if($value >=1.05 && $value <=1.09) $value = 1.05;
        if($value >=1.10 && $value <=1.14) $value = 1.10;
        if($value >=1.15 && $value <=1.19) $value = 1.15;
        if($value >=1.20 && $value <=1.24) $value = 1.20;
        if($value >=1.25 && $value <=1.29) $value = 1.25;
        if($value >=1.30 && $value <=1.34) $value = 1.30;
        if($value >=1.35 && $value <=1.39) $value = 1.35;
        if($value >=1.40 && $value <=1.44) $value = 1.40;
        if($value >=1.45 && $value <=1.49) $value = 1.45;
        if($value >=1.50 && $value <=1.54) $value = 1.50;
        if($value >=1.55 && $value <=1.59) $value = 1.55;
        if($value >=1.60) $value = 1.6;
        
        return $value;
    }
    
}
