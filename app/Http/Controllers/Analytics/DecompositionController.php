<?php

namespace App\Http\Controllers\Analytics;

use App\Http\Controllers\Controller;
use App\Models\Analytics\DecompositionValue;
use Illuminate\Http\Request;

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

        DecompositionValue::query()->updateOrCreate([
            'id' => $id
        ], [
            'date' => $date,
            'group_id' => $group_id,
            'name' => $name,
            'values' => $values,
        ]);

        return $id;
    }

    public function delete(Request $request): void
    {
        DecompositionValue::query()->find($request->id)?->delete();
    }

}

