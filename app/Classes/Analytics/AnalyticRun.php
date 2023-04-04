<?php

namespace App\Classes\Analytics;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;

class AnalyticRun extends Controller
{
    public function run()
    {
        Artisan::call('analytics:pivots');
    }
}
