<?php

namespace App\Http\Controllers\Admin;

use DB;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Classes\Analytics\Recruiting as RM;
use App\Classes\Analytics\Ozon;
use App\Classes\Analytics\DM;
use App\Classes\Analytics\HomeCredit;
use App\Classes\Analytics\Eurasian;
use App\User;
use App\Models\Analytics\DecompositionItem as Item;
use App\Models\Analytics\DecompositionValue as Value;
use App\Http\Controllers\Controller;

class DecompositionController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function save(Request $request)
    {
        $id = $request->id;
        $date = $request->date;
        $name = $request->name;
        $values = $request->values;
        $group_id = $request->group_id;

        $value = Value::find($id);

        if($value) {
            $value->values = $values;
            $value->name = $name;
            $value->save();
        } else {
            $id = Value::create([
                'date' => $date,
                'group_id' => $group_id,
                'name' => $name,
                'values' => $values,
            ]);
        } 
        
        return $id;
    }

    public function delete(Request $request)
    {
        if(Auth::user()->id == 5) {
            $value = Value::find($request->id);
            if($value) $value->delete();
        }
    }

}

