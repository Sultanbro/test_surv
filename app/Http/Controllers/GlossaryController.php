<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GlossaryWord as Word;
use App\User;

class GlossaryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function get(Request $request)
    {
        return Word::orderBy('word')->get();
    }
}
