<?php

namespace App\Http\Controllers\Admin;

use App\Models\QuartalBonus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;


class QuartalBonusController extends Controller
{

    public function storePersonQuartal(Request $request)
    {

        $quartalBonus = new QuartalBonus();
        $quartalBonus['user_id'] = 1;
        $quartalBonus['auth_id'] = 1;
        $quartalBonus['quartal'] = 1;
        $quartalBonus['sum'] = 1;
        $quartalBonus['year'] = 1;
        $quartalBonus['text'] = '1';
        $quartalBonus->save();


         QuartalBonus::created([
            'user_id'=>1,
            'auth_id'=>auth()->user()->id,
            'quartal'=>1,
            'sum'=>1,
            'year'=>1,
            'text'=>'1asdasd',
        ]);




    }
}
