<?php

namespace App\Http\Controllers\Salary;

use App\Models\QuartalBonus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class QuartalBonusController extends Controller
{

    public function storePersonQuartal(Request $request)
    {


        foreach ($request['arr'] as $key => $query){

            if ($query['checked'] == true){
                $qb = QuartalBonus::where('quartal', $query['quarter'])
                    ->where('year', date('Y'))
                    ->where('user_id', $request->user_id)
                    ->first();
                $arr =
                      [
                       'user_id'=> $request->user_id,
                       'auth_id'=>auth()->user()->getAuthIdentifier(),
                       'quartal'=> $query['quarter'],
                       'sum'=> $query['sum'],
                       'year'=> date('Y'),
                       'text'=> $query['text'],
                      ];
                if(!empty($qb)){
                    $qb->update($arr);
                }else{
                    QuartalBonus::create($arr);
                }
            }else{
                QuartalBonus::where('quartal', $query['quarter'])
                    ->where('year', date('Y'))
                    ->where('user_id', $request->user_id)
                    ->delete();
            }
        }


        return response()->json([
            'success'=>'1'
        ]);



    }


    public function getQuartalBonuses(Request$request)
    {


            for ($i = 1;$i <= 4;$i++)
            {
                $qb = QuartalBonus::on()->where('user_id',$request->user_id)
                    ->where('year',date('Y'))
                    ->where('quartal',$i)
                    ->first();

                if (!empty($qb)){
                    $items[$i] =
                        [
                            'checked' => true,
                            'text' => $qb['text'],
                            'sum' => $qb['sum'],
                            'quarter' => $i,
                            'year' => date('Y'),
                        ];
                }else{
                    $items[$i] =
                        [
                            'checked' => false,
                            'text' => '',
                            'sum' => 0,
                            'quarter' => $i,
                            'year' => date('Y'),
                        ];
                }
            }

            return response()->json([
                $items
            ]);


    }


    public function deleteQuartal(Request $request)
    {
        if (QuartalBonus::on()->where('user_id',$request['user_id'])->delete()){

            return response(['success'=> '1']);
        }

    }
}
