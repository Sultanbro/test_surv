<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ProfileGroup;
use Carbon\Carbon;

class UserDescription extends Model
{
    protected $table = 'user_descriptions';

    protected $casts = [
        'quiz_after_fire' => 'array',
        'rating1' => 'array', // хранит оценку руковода и дату оценки
        'rating2' => 'array',
        'notifications' => 'array',
    ];

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'is_trainee', // Стажер ли
        'applied', // принят на работу
        'lead_id', // лид
        'deal_id', // сделка
        'fire_cause',
        'fire_date', // 
        'fired', // дата увольнения
        'requested', // запрос на принятие на работу :: возможно лишнее
        'bitrix', // отправлено приглашение в битрикс 0 или 1
        'bitrix_id', // ID пользователь битрикс
        'quiz_after_fire', // Ответы на анкету после увольнения
        'rating1', // первая оценка
        'rating2', // вторая оценка
        'notifications', // Личные уведомления
        'recruiter_comment', // Комментарии рекрутера. При заполнении приходит уведомление об оплате
        'headphones_amount', // Выданы наушники на сумма для вычета из зарплаты
        'headphones_date', // Выданы наушники дата
    ];

    public function user()
    {
       return $this->belongsTo('App\User');
    } 

    /**
     * Check if record exists and update or create new one
     * @return UserDescription
     */
    public static function make(array $data) {
        if(array_key_exists('user_id', $data)) {
            $ud = UserDescription::where('user_id', $data['user_id'])->first();
            if($ud) {
                $ud->update($data);
            } else {
                $ud = UserDescription::create($data);
            }

            return $ud;
        } else {
            throw new \Exception("User_id is not specified in array");
        }
    }

    /**
     * Avg rating of head of group
     */
    public static function getAvgRating($head_id, $with_quantity = false, $date = null) {

		$uds = UserDescription::where(function($query) use ($head_id){
			$query->where('rating1', 'like', '%"head_id":"' . $head_id. '"%')
				->orWhere('rating2', 'like', '%"head_id":"' . $head_id. '"%');
		})->get();

        if($date) {
            $start = $date->startOfMonth()->timestamp;
            $end = $date->endOfMonth()->timestamp;
        }
        

		$sum = 0;
		$count = 0;


        if($date) {
            foreach($uds as $ud) {	
                if($ud->rating1 
                    && array_key_exists('rating' ,$ud->rating1) 
                    && array_key_exists('date', $ud->rating1) 
                    && $ud->rating1['date'] > $start 
                    && $ud->rating1['date'] < $end) {
                    $sum += (int)$ud->rating1['rating'];
                    $count++;
                }
                if($ud->rating2 
                    && array_key_exists('rating' ,$ud->rating2) 
                    && array_key_exists('date', $ud->rating2) 
                    && $ud->rating2['date'] > $start 
                    && $ud->rating2['date'] < $end) {
                    $sum += (int)$ud->rating2['rating'];
                    $count++;
                }
            }         
        } else {
            foreach($uds as $ud) {	
                if($ud->rating1 && array_key_exists('rating' ,$ud->rating1)) {
                    $sum += (int)$ud->rating1['rating'];
                    $count++;
                }
                if($ud->rating2 && array_key_exists('rating' ,$ud->rating2)) {
                    $sum += (int)$ud->rating2['rating'];
                    $count++;
                }
            }    
        }
		

		$avg = 0;
		if($count != 0 && $sum != 0) {
			$avg = number_format($sum / $count,2);
		}
		
        if($with_quantity) {
            return [
                'quantity' => $count,
                'avg' => $avg
            ];
        } else {
            return $avg;
        }
		
    }

    /**
     * Получить средние оценки руководителей
     * @return array
     */
    public static function getHeadsRatings(Carbon $date) {
        $ratings_heads = [];

        $uds = UserDescription::where(function($query){
			$query->whereNotNull('rating1')
				->orwhereNotNull('rating2');
		})->get()->pluck('user_id')->toArray();

        $uds = array_unique($uds);
        foreach ($uds as $head_id) {
            $user = User::withTrashed()->find($head_id);
            if($user) {
                $ratings = self::getAvgRating($head_id, true, $date);
                $hgs = ProfileGroup::headIn($head_id, false);

                if($ratings['quantity'] > 0) {
                    array_push($ratings_heads, [
                        'name' => $user->LAST_NAME . ' ' .$user->NAME,
                        'group' => $hgs->count() > 0 ? $hgs[0]->name : '---',
                        'quantity' => $ratings['quantity'],
                        'avg' => $ratings['avg'],
                    ]);
                }
                
            }
        }

        return $ratings_heads;
    }
    
}
