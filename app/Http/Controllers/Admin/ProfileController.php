<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Auth;
use App\Kpi;
use App\Salary;
use Carbon\Carbon;
use App\Downloads;
use App\Account;
use App\UserNotification;
use App\Position;
use App\Program;
use App\WorkingDay;
use App\WorkingTime;
use App\ProfileGroup;
use App\Setting;
use App\DayType;
use App\User;
use App\UserExperience;
use App\Trainee;
use App\UserDescription;
use App\UserDeletePlan;
use App\UserContact;
use App\Zarplata;
use App\TimetrackingHistory;
use App\Photo;
use App\Models\Admin\ObtainedBonus;
use App\External\Bitrix\Bitrix;
use App\Models\Bitrix\Lead;
use App\Models\Bitrix\Segment;
use App\Http\Controllers\IntellectController as IC;
use App\Classes\Helpers\Phone;
use App\Classes\Analytics\Recruiting as RM;
use App\Models\Analytics\RecruiterStat;
use App\AnalyticsSettings;
use App\AnalyticsSettingsIndividually;
use App\Models\CallCenter\Directory;
use App\Models\CallCenter\Agent;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Admin\History;
use App\UserReport;
use App\Models\User\NotificationTemplate;
use App\Models\User\Card;
use App\Classes\Helpers\Currency;
use App\Models\Admin\EditedBonus;
use App\Models\Admin\EditedKpi;

class ProfileController extends Controller
{
    public function __construct()
    {
    }

    /**
     * Мой профиль с новым дизайном
     */
    public function newprofile()
    {
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
                    array_push($files_full, 'https://bp.jobtron.org/' . $file_path . $filename);
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
