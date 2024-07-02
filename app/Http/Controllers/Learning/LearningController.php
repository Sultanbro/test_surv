<?php

namespace App\Http\Controllers\Learning;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Controller;

class LearningController extends Controller
{
    public function books(Request $request)
    {   
        View::share('menu', 'learning');
        View::share('link', 'books');

        return view('surv.books');
    }

    public function videos(Request $request)
    {   
        View::share('menu', 'learning');
        View::share('link', 'videos');

        return view('surv.videos');
    }
}
