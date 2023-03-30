<?php

namespace App\Http\Controllers\Structure;

use App\Http\Controllers\Controller;

class StructureController extends Controller
{
    public function index()
    {
        return view('admin.structure');
    }
}
