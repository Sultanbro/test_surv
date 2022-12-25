<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\UserExperience;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Мой профиль с новым дизайном
     */
    public function newprofile()
    {
        // admin.jobtron.org
        if(request()->getHost() === 'admin.' .config('app.domain')) {
            return view('admin');
        }

        return view('newprofile');
    }

    /**
     * Вопросы в профиле о навыках и опыте
     */
    public function saveAnswer(Request $request)
    {
        $files = [];

        if($request->question == 5) {

            $ue = UserExperience::where('user_id', $request->user_id)
                ->where('question', 5)
                ->first();
            if($ue) {
                foreach(json_decode($ue->answer, true) as $file) {
                    $path = public_path() . '/' . $file;
                    unlink($path);
                }
                $ue->answer = json_encode([]);
                $ue->save();
            }

            if ($request->hasFile('answers')) {

                $r_files = $request->file('answers');
                $files = []; // для сохранения
                $files_full = []; //
                
                foreach ($r_files as $key => $file) {
                    $filename = $request->user_id . '_' . $key . '_'  . time() . '.' . $file->getClientOriginalExtension();
                    $file_path = "static/profiles/" . $request->user_id . "/certificates/";
                    $file->move($file_path, $filename);
                    
                    array_push($files, $file_path . $filename);
                    array_push($files_full, 'https://'.tenant('id').'.jobtron.org/' . $file_path . $filename);
                }
                
                UserExperience::make($request->user_id, $request->question, json_encode($files));
          
                return json_encode([
                    'code' => 200,
                    'files' => $files_full
                ]);
            }
        } else {
            UserExperience::make($request->user_id, $request->question, $request->answers);

            return json_encode([
                'code' => 200,
            ]);
        }

        
        

        
    }
}
