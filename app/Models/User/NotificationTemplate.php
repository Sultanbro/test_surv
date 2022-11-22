<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;
use App\UserDescription;
use App\User;
use App\ProfileGroup;

class NotificationTemplate extends Model
{
    protected $table = 'notification_templates';

    protected $fillable = [
        'title',
        'type',
        'action',
        'note',
        'message',
        'need_group',
        'ids',
    ];

    CONST USER = 0;
    CONST GROUP = 1;
    CONST POSITION = 2;
    CONST OTHER = 3;


    /**
     * @return array
     */
    public static function getReceivers($template_id, $group_id = 0) {
        $array = [];

        $uds = UserDescription::where('notifications', '!=', '[]')->get();
    
        foreach($uds as $ud) {  
            foreach($ud->notifications as $noti) {
                if($noti[0] == $template_id) {
                    if($group_id == 0) {
                        array_push($array, $ud->user_id);
                    } else if(in_array($group_id, $noti[1])) {
                        array_push($array, $ud->user_id);
                    }
                } 
            }
        }

        return $array;
    }

    /**
     * @return array
     */
    public static function getHeadReceivers($template_id, $group_id) {
        $array = [];

        $nt = self::find($template_id);
        $gr = ProfileGroup::find($group_id);

        if($gr && $nt) {
            $g_users = json_decode($gr->head_id);
            $t_users = json_decode($nt->ids);

            foreach($g_users as $user_id) {
                if(in_array($user_id, $t_users)) {
                    array_push($array, $user_id);
                }
            }
        }

        return $array;
    }
}
