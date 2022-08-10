<?php

namespace App\Http\Controllers\Kpi;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\User;
use App\ProfileGroup;
use App\Position;
use App\Models\Analytics\Activity;
// use App\Models\Kpi\Kpi;

class KpiController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    } 

    public function index(Request $request)
    {
        View::share('title', 'KPI');
        View::share('menu', 'timetracking');

        return view('kpi')->with([
            'page' => 'kpi'
        ]);
    }

}
