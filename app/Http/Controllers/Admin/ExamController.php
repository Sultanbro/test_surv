<?php

namespace App\Http\Controllers\admin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Controller;
use App\UserExperience;
use App\AnalyticsSettings;
use App\DayType;
use App\Exam;
use App\ProfileGroup;
use App\Salary;
use App\Timetracking;
use App\User;
use App\Models\Books\Book;
use App\Models\Books\BookResult;

class ExamController extends Controller
{
    public function __construct()
    {
        View::share('title', 'Экзамены по книгам');
        View::share('menu', 'timetrackingexam');

        $this->middleware('auth');
    }

    public function index()
    {
        $groups = ProfileGroup::where('active', 1)->get();
        return view('admin.exam', compact('groups'));
    }

    public function getexams(Request $request)
    {
        $request->validate([
            'month' => 'required|numeric',
            'group_id' => 'required|exists:profile_groups,id',
        ]);

        $year = date('Y');
        $month = Carbon::createFromFormat('m-Y', $request->month . '-' . $year);
        $currentUser = User::bitrixUser();

        $group = ProfileGroup::find($request['group_id']);
        $group_editors = is_array(json_decode($group->editors_id)) ? json_decode($group->editors_id) : [];
        // Доступ к группе
   

        if (!empty($group) && $group->users != null) {
            $users_ids = json_decode($group->users);
            $users_ids = array_unique($users_ids);

        }

        $group_editors = is_array(json_decode($group->editors_id)) ? json_decode($group->editors_id) : [];


        $users = User::whereIn('users.id', array_unique($users_ids))
            ->get(['users.id', 'email', DB::raw("CONCAT(name,' ',last_name) as full_name")]);

        $currentDate = Carbon::now();
        foreach ($users as $user) {

            $book = self::currentBook($user->id,$request->month,$request->year);

            if($currentDate->format("m") <= $request->month && $currentDate->format("Y") == $request->year){
                $user->book_id = $book['id'];
                $user->book_name = $book['name'];
                array_key_exists('success', $book) ? $user->success = $book['success'] : $user->success = '';
                array_key_exists('exam_date', $book) ? $user->exam_date = $book['exam_date'] : $user->exam_date = '';
                array_key_exists('link', $book) ? $user->link = $book['link'] : $user->link = '';
            }elseif (array_key_exists('success', $book)){
                $user->book_id = $book['id'];
                $user->book_name = $book['name'];
                array_key_exists('success', $book) ? $user->success = $book['success'] : $user->success = '';
                array_key_exists('exam_date', $book) ? $user->exam_date = $book['exam_date'] : $user->exam_date = '';
                array_key_exists('link', $book) ? $user->link = $book['link'] : $user->link = '';
            }

            $data['users'][] = $user;
        }   

        
        $data['skills'] = UserExperience::getSkills($users_ids);

        return response()->json(array_merge($data));
    }

    public function update(Request $request){

        
        $currentUser = User::bitrixUser();

        $group = ProfileGroup::find($request['group_id']);
        $group_editors = is_array(json_decode($group->editors_id)) ? json_decode($group->editors_id) : [];
        // Доступ к группе
        if (!in_array($currentUser->id, $group_editors) && $currentUser->id != 18) {
            return [
                'errors' => 'access',
            ];
        }

        $request->validate([
            'user_id' => 'exists:users,ID',
            'success' => 'numeric|between:1,2'
        ]);
        


        $file_headers = @get_headers($request->link);
        
        if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found') {
            return response()->json([
               'errors' => 'Введенный URL (Ссылка на файл) недействительна',
            ]);
        }


        
        $exam = Exam::where('month', $request->month)
            ->where('year', '=', $request->year)
            ->where('user_id', $request->user_id)->first();

        if($exam) {
            //обновляем экзамен
            
            if($request->key === 'link'){
                $exam->link = $request->link;
                $exam->exam_date = null;
                $exam->success = 0;
                $exam->book_id = $request->book_id;
                $exam->save();
            }

            if($request->key === 'success'){
                $exam->link = $request->link;
                $exam->book_id = $request->book_id;
                $exam->exam_date = now();
                $exam->success = $request->value;
                $exam->save();
                if($request->value) {
                    BookResult::setBookRead($request->user_id, $request->book_id);
                } else {
                    BookResult::setBookNotRead($request->user_id, $request->book_id);
                }
            }

        } else {
            
            if($request->key == 'success' && $request->value){
                $success = 1;
                BookResult::setBookRead($request->user_id, $request->book_id);
            } else {
                $success = 0;
                BookResult::setBookNotRead($request->user_id, $request->book_id);
            }

            $exam = Exam::create([
                'user_id' => $request->user_id,
                'link' => $request->link,
                'year' => $request->year,
                'success' => $success,
                'month' => $request->month,
                'book_id' => $request->book_id,
                'exam_date' => now()
            ]);
        }

        return $exam;
    }

    public function currentBook($user_id,$month,$year){

        $book[] = null;

        //ишем книгу на текущий месяц
        $exam = exam::where('user_id', $user_id)
            ->where('month', $month)
            ->where('year', $year)
            ->first(['book_id', 'success',DB::raw("DATE_FORMAT(exams.exam_date,'%d.%m.%Y') exam_date"),'link']);

        $unread_book_id = BookResult::getUnreadBook($user_id);   

        if($unread_book_id) {
            $book['id'] = $unread_book_id;
            $book['name'] = Book::getBookTitle($unread_book_id);
            $book['link_book'] = Book::getLink($unread_book_id);
        } else {
            $book['id'] = 0;
            $book['name'] = 'Все книги успешно прочитаны!';
            $book['link_book'] = '';
        }

        if($exam){
            $book['success'] = $exam->success;
            $book['exam_date'] = $exam->exam_date;
            $book['link'] = $exam->link;
            $book['id'] = $exam->book_id;
            $book['name'] = Book::getBookTitle($exam->book_id);
            $book['link_book'] = Book::getLink($exam->book_id);
            return $book;
        }

        return $book;
    }
}
