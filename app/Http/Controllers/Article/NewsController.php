<?php

namespace App\Http\Controllers\Article;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;

class NewsController extends Controller
{
    public function index()
    {

        View::share('title', 'Новости');
        View::share('menu', 'news');

        return view('news')->with([
            'page' => 'news'
        ]);
    }
}
