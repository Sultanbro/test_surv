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

        foreach ($request->items as $key => $item) {
            if($item['checked']) {
                    $qb = QuartalBonus::where('quartal', $item['quarter'])
                        ->where('year', date('Y'))
                        ->where('user_id', $request->user_id)
                        ->first();

                $arr = [
                    'user_id'=> $request->user_id,
                    'auth_id' => 0,
                    'quartal'=> $item['quarter'],
                    'sum'=> $item['sum'],
                    'year'=> date('Y'),
                    'text'=> $item['text'],
                ];

                if($qb) {
                    $qb->update($arr);
                } else {
                    QuartalBonus::create($arr);
                }
            } else {
                $qb = QuartalBonus::where('quartal', $item['quarter'])
                ->where('year', date('Y'))
                ->where('user_id', $request->user_id)
                ->first();
                if($qb) $qb->delete();
            }

           
        }
      



    }
}
