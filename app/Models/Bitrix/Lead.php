<?php

namespace App\Models\Bitrix;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use App\Classes\Helpers\Phone;
use App\User;
use App\ProfileGroup;

class Lead extends Model
{   
    protected $table = 'bitrix_leads';

    public $timestamps = true;

    public $fields = [
        'skype' => [
            'code' => 'UF_CRM_1623492488',
            'name' => 'Скайп соискателя',
        ],
        'status' => [
            'code' => 'STATUS_ID',
            'name' => 'Статус лида',
        ]
    ];

    protected $fillable = [
        'lead_id',
        'deal_id',
        'user_id', 
        'resp_id', // responsible manager
        'segment', // сегмент Кандидат на вакансию
        'phone',
        'phone_2',
        'phone_3',
        'skype',
        'project',
        'status',
        'name',
        'house',
        'lang',
        'net',
        'files',
        'hash',
        'signed',
        'time',
        'email',
        'invited',
        'invite_at', // первый день стажировки
        'day_second', // второй день стажировки
        'invite_group_id',
        'rating',
        'rating_date',
        'rating2',
        'rating2_date',
        'skyped',
        'inhouse',
        'wishtime',
        'received_assessment', // Получено сообщений с оценкой в ватсап
        'received_fd', // Получено сообщений с оценкой в ватсап первого дня
    ];

    const NO_ASSESSMENT_RECEIVED = 0;
    const FIRST_DAY_ASSESSMENT_RECEIVED = 1;
    const FOURTH_DAY_ASSESSMENT_RECEIVED = 2;

    public $segment_field_bitrix = 'UF_CRM_1498210379';
    public $project_field_bitrix_deal = 'UF_CRM_5F61AD2B3241C';
    
    const SEGMENT_TARGET = 1; // 1018 Кандидаты на вакансию (таргет)
    const SEGMENT_HH = 2; // 1462 Кандидаты на вакансию (hh, nur, job)
    const SEGMENT_PROMO = 3; // 1604 Кандидаты на вакансию (promo акции)
    const SEGMENT_MESSENGER = 4; // 1666 Кандидаты на вакансию (месенджеры)
    const SEGMENT_GARANTY = 5; // 2012 Кандидаты на вакансию (Гарантия трудоустройства)
    const SEGMENT_FORUM = 6; // 1442 Кандидаты на вакансию (Участники семинаров, форумов, встреч)
    const SEGMENT_MUSA = 7;
    const SEGMENT_ALINA = 8;
    const SEGMENT_SALTANAT = 9;
    const SEGMENT_AKZHOL = 10;
    const SEGMENT_DARKHAN = 11;
    const SEGMENT_SHOLPAN = 12;
    const SEGMENT_SITE_BP = 13;
    const SEGMENT_INCOME = 14;

    CONST SEGMENTS = [
        'Кандидаты (таргет)' => 1,
        'Кандидаты (hh, nur и др.)' => 2,
        'Кандидаты (promo акции)' => 3, 
        'Кандидаты (вацап, телега и др мессенджеры)' => 4, 
        'Кандидаты на вакансию (Гарантия трудоустройства)' => 5,
        'Кандидаты на вакансию (Участники семинаров, форумов, встреч)' => 6, 
        'Кандидаты на вакансию (Муса)' => 7, // derprecated
        'Кандидаты на вакансию (Алина)' => 8, // derprecated
        'Кандидаты на вакансию (Салтанат)' => 9,  // derprecated
        'Кандидаты на вакансию (Акжол)' => 10, // derprecated
        'Кандидаты на вакансию (Дархан)' => 11, // derprecated
        'Кандидаты на вакансию (Шолпан)' => 12, // derprecated
        'Кандидаты на вакансию (Сайт BP)' => 13, // derprecated
        'Busines Partner (Входящее обращение)' => 14, 
        'Кандидаты (Интеллектуальный обзвон)' => 15,
        'Кандидаты (job.bpartners.kz)' => 16,
        'Кандидаты (QR)' => 17,
        'Кандидаты (ВХ звонок)' => 18,
    ];

    CONST SEGMENTS_ALT = [
        '1018' => 1,
        '1462' => 2,
        '1604' => 3,
        '1666' => 4, 
        '2012' => 5,
        '1442' => 6,
        '2362' => 7,
        '2426' => 8,
        '2446' => 9,
        '2448' => 10,
        '2536' => 11,
        '2538' => 12,
        '2436' => 13,
        '873'  => 14,
        '2012' => 15,
        '2436' => 16,
        '2362' => 17,
        '2426' => 18,
    ];


    public static function getSegment($str) {
        if($str == NULL) return 0;
        $segment = Segment::where('name', $str)->first();
        return $segment ? $segment->id : 99;
    }
 
    public static function getSegmentAlt($str) {
        if($str == NULL) return 0;
        $segment = Segment::where('on_lead', $str)->first();
        return $segment ? $segment->id : 99;
    }
    
    /**
     * get leads
     */
    public static function fetch(array $date) {

        $skypes = self::whereNotNull('skyped')->whereMonth('skyped', $date['month'])->whereYear('skyped', $date['year'])->orderBy('skyped','desc')->get();
        $inhouses = self::whereNotNull('inhouse')->whereMonth('inhouse', $date['month'])->whereYear('inhouse', $date['year'])->orderBy('inhouse','desc')->get();
        /////////

        $groups = ProfileGroup::pluckIdName();

        $skypes = $skypes->merge($inhouses);
        
        foreach ($skypes as $skype) {
            $arr = json_decode($skype->files);
            
            if(count($arr) > 0) {
                $skype->file = 'https://' .tenant('id') . '.' . env('APP_DOMAIN', 'jobtron.org') . '/static/uploads/job/' . $arr[0];
            }
           
            
            $skype->checked = false;

            if(array_key_exists($skype->invite_group_id, $groups)) {
                $skype->invite_group = $groups[$skype->invite_group_id];
            } else {
                $skype->invite_group = '';
            }


            if($skype->skyped) {
                $skype->os = Carbon::parse($skype->skyped)->timestamp;
                $skype->skyped = date('d.m.Y H:i', Carbon::parse($skype->skyped)->timestamp);
                $skype->skyped_old = date('Y-m-d H:i:s', Carbon::parse($skype->skyped)->timestamp);
                $skype->user_type = 'remote';
            } else {
                $skype->os = Carbon::parse($skype->inhouse)->timestamp;
                $skype->user_type = 'office';
                $skype->skyped = date('d.m.Y H:i', Carbon::parse($skype->inhouse)->timestamp);
                $skype->skyped_old = date('Y-m-d H:i:s', Carbon::parse($skype->inhouse)->timestamp);
            }
            
            if($skype->invite_at) {
                $skype->invited_at = Carbon::parse($skype->invite_at)->format('d.m.Y H:i');
            }

            $skype->country = Phone::getCountry($skype->phone);

            $skype->resp = '';
            if($skype->resp_id != '0' && $skype->resp_id != NULL && $skype->resp_id != '') {
                $resp_user = User::withTrashed()->where('email', $skype->resp_id)->first();
                $skype->resp = $resp_user ? $resp_user->last_name . '<br>' . $resp_user->name : $skype->resp_id; 
            }
            
        }

        $skypes = $skypes->sortByDesc('os')->all();
        $skypes = array_values($skypes);


        return $skypes;
    }

    /**
     *  Trainee (Lead) has many daytypes 
     */
    public function daytypes() {
        return $this->hasMany('App\DayType','user_id', 'user_id');
    }

    /**
     * Получить лид сотрудника.
     *
     * @param $query
     * @param User $user
     * @return mixed
     */
    public function scopeUserLeadByDesc($query, User $user)
    {
        return $query->where('user_id', $user->id)->orderBy('id', 'desc')->first() ?? null;
    }
}
