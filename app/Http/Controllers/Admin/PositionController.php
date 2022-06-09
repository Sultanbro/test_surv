<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Position;
use App\PositionDescription;
use Carbon\Carbon;

class PositionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function savePositionDesc() {
        
    }
}
