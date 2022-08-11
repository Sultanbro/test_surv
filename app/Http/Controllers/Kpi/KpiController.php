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

    /**
     * Возвращает страницу KPI c вкладками
     */
    public function index(Request $request)
    {
        View::share('title', 'KPI');
        View::share('menu', 'timetracking');

        return view('kpi')->with([
            'page' => 'kpi'
        ]);
    }

    /**
     * Возращает KPI::with('items)->paginate()
     */
    public function get(Request $request)
    {
        View::share('title', 'KPI');
        View::share('menu', 'timetracking');

        return view('kpi')->with([
            'activities' => $activities,
            'groups' => $groups,
            'kpis' => $kpis,
        ]);
    }

    /**
     * Сохраняет и возвращает Kpi::with('items')
     * @param Request $request
     * 
     * @return Kpi
     */
    public function save(Request $request)
    {
        
    }

    /**
     * Редактирует и возвращает Kpi::with('items') 
     * @param Request $request
     * 
     * @return Kpi
     */
    public function update(Request $request)
    {
        
    }

}
