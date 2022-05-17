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

        $qb = QuartalBonus::where('quartal', $request->quartal)
            ->where('year', date('Y'))
            ->where('user_id', $request->user_id)
            ->first();

        $arr = [
            'user_id'=> $request->user_id,
            'auth_id' => 0,
            'quartal'=> $request->quartal,
            'sum'=> $request->sum,
            'year'=> date('Y'),
            'text'=> $request->text,
        ];

        if($qb) {
            $qb->update($arr);
        } else {
            QuartalBonus::create($arr);
        }



    }
}
