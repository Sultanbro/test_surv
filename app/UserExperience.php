<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UserExperience extends Model
{
    protected $table = 'user_experiences';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'question',
        'answer',
    ];

    CONST QUESTIONS = [
        1 => 'Расскажите, где вы ранее работали и в какой должности',
        2 => 'Расскажите, что вы умеете делать очень хорошо',
        3 => 'Напишите, какие профессиональные тренинги или книги вы проходили',
        4 => 'Управляли ли вы персоналом',
        5 => 'Какие у вас есть сертификаты',
    ];

    public static function make($user_id, $question, $answer) {
        $ue = self::where('user_id', $user_id)
            ->where('question', $question)
            ->first();
        if(!$ue) $ue = new self();
        $ue->user_id = $user_id;
        $ue->question = $question;
        $ue->answer = $answer;
        $ue->save();
    }

    public static function getAnswers($user_id) {
        $ues = self::where('user_id', $user_id)->get();

        $answers = [];

        $answers[1] = $ues->where('question', 1)->first() ? $ues->where('question', 1)->first()->answer : '';
        $answers[2] = $ues->where('question', 2)->first() ? $ues->where('question', 2)->first()->answer : '';
        $answers[3] = $ues->where('question', 3)->first() ? $ues->where('question', 3)->first()->answer : '';
        $answers[4] = $ues->where('question', 4)->first() ? $ues->where('question', 4)->first()->answer : '';

        $q5 = $ues->where('question', 5)->first();

        if($q5) {
            $arr = json_decode($q5->answer, true);
            foreach ($arr as $key => $file) {
                $arr[$key] = 'https://'.tenant('id').'.jobtron.org/' . $file;
            }
            $answers[5] = $arr;
        } else {
            $answers[5] = [];
        }

        return $answers;
    }

    /**
     *  для exam controllera
     */
    public static function getSkills($user_ids) {
        $skills = [];

        $users = \DB::table('users')
            ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
            ->whereIn('users.id', $user_ids)
            ->where('is_trainee', 0)
            ->get(['users.id','users.name','users.last_name']);

        foreach ($users as $key => $user) {
            $skill = [];
            $skill['name'] = $user->last_name . ' ' . $user->name; 
            $skill['text'] = '';
            $skill['head'] = false;

            $answers = self::getAnswers($user->id);

            $last_time = self::where('user_id', $user->id)
                ->orderBy('updated_at', 'desc')
                ->first();

            if($last_time) {
                $skill['last_time'] = Carbon::parse($last_time->updated_at)->format('d.m.Y');
                $skill['order'] = Carbon::parse($last_time->updated_at)->timestamp;
            }  else {
                $skill['last_time'] = '---';
                $skill['order'] = 0;
            }


            foreach(self::QUESTIONS as $key => $question) {
                
                

                if($key == 5) {
                    
                    if(count($answers[$key]) > 0) {
                        
                        $skill['text'] .= '<b>' . $question . '</b><br/>';
                        foreach ($answers[$key] as $file) {
                            $skill['text'] .= '<a href="' . $file . '" target="_blank" style="margin-right:10px;">Файл</a>';
                        } 
                    } 
                    
                } else if($key == 4){
                    $skill['head'] = $answers[$key] == 'true' ? true : false;
                } else {
                    if(strlen($answers[$key]) > 9) {
                        $skill['text'] .= '<b>' . $question . '</b><br/>';
                        $skill['text'] .= '<pre>' .$answers[$key] . '</pre><hr/>';
                    } 
                }
            }

            $skills[] = $skill;
        }

        $order_desc = array_column($skills, 'order');
        array_multisort($order_desc, SORT_DESC, $skills); 

        return $skills;
    }
}
