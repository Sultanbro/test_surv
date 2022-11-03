<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\SettingLogo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class LogoController extends Controller
{
    public function upload(Request $request){

       $fileName = $request['file']->getClientOriginalName();
       $image = $request['file'];
       unset($request['file']);
       $name = md5(Carbon::now(). '_' . $image->getClientOriginalName()). '.' . $image->getClientOriginalExtension();
       $filePuth = Storage::disk('public')->putFileAs('/logo', $image, $name);

       $logo = SettingLogo::create([
            'name' => $fileName,
            'url' => $filePuth
       ]);

       return response()->json($logo, 200);

    }
}