<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Models\Course;
use App\Models\CourseItem;
use App\Models\CourseResult;
use App\Models\CourseProgress;
use App\Models\Videos\VideoPlaylist;
use App\Models\Books\Book;
use App\KnowBase;
use App\User;
use DB;

class MyCourseController extends Controller
{
    public function myCourses(Request $request) {
        View::share('menu', 'mycourse');
        return view('admin.mycourse');
    }   
    
    public function getMyCourse(Request $request) {
        $user_id = auth()->user()->ID;
        $active_course = CourseResult::where('user_id', $user_id)->whereIn('status', [0,2])->orderBy('status', 'desc')->first();
        
        // $test_results = TestResult::where('user_id', $user_id)->user_id

        if($active_course) {
            //$course_items = $active_course ? 
        } else {
            $CourseProgress = CourseProgress::where('user_id', $user_id)->where()->get();
        }

        
        
        return $active_course;
    }   

   
}
