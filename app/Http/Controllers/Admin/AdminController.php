<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Admin\OwnerRepository;

class AdminController extends Controller
{   
    public $date;

    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Admin.jobtron.org
     * 
     * @param Request $request
     */
    public function index(Request $request)
    {
        return view('admin');
    }

    /**
     * Owners
     * 
     * @param Request $request
     */
    public function owners(Request $request, OwnerRepository $ownerRepository)
    {

        return response()->json([
            'items' => $ownerRepository->getOwnersPaginate(10, $request)
        ]);
    }

}
