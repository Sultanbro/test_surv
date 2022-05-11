<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Group extends Model {

    public $timestamps = true;

    protected $table = 'b_groups';

    protected $fillable = [
        'id_user',
        'name',
    ];

    public static function bitrixGroups($user_id) {

        $groups = Group::where('id_user', $user_id)->select('id as ID', 'name as NAME')->orderBy('updated_at', 'desc')->get();

        /*$groups = DB::select(DB::raw("
            SELECT BS.ID,
                 BS.NAME
            FROM b_iblock_section BS
              INNER JOIN b_iblock B ON BS.IBLOCK_ID = B.ID
              INNER JOIN b_uts_iblock_1_section BUF ON BUF.VALUE_ID = BS.ID
            WHERE BS.IBLOCK_ID = '1'AND
                  BS.GLOBAL_ACTIVE = 'Y' AND
                  B.ID = '1' AND
                  (BUF.UF_SMS = 0 OR BUF.UF_SMS IS NULL )AND
                  BUF.UF_USERID = '$user_id'
            GROUP BY BS.ID, B.ID
            ORDER BY BS.TIMESTAMP_X DESC"));*/

        return $groups;
    }

    public static function bitrixGroup($group_id) {

        $group = Group::where('id', $group_id)->select('id as ID', 'name as NAME')->first();

        /*$group = DB::select(DB::raw("
            SELECT BS.ID,
                 BS.NAME
            FROM b_iblock_section BS
              INNER JOIN b_iblock B ON BS.IBLOCK_ID = B.ID
            WHERE BS.IBLOCK_ID = '1'AND
                  BS.GLOBAL_ACTIVE = 'Y' AND
                  B.ID = '1' AND
                  BS.ID = '$group_id'
            GROUP BY BS.ID, B.ID
            ORDER BY BS.TIMESTAMP_X DESC"));*/

        return $group;
    }

    public static function contacts($group_id, $limit = 100){
    	if($limit) {
		    return Contact::whereIn('id_group', $group_id)->take($limit)->get();
	    }

	    return Contact::whereIn('id_group', $group_id)->get();
    }

    public static function count($group_id){
        if(empty($group_id)) {
            return 0;
        }
        return Contact::where('id_group', $group_id)->count();
    }
}
