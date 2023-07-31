<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupUser extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'group_user';

    public $timestamps = false;

    protected $fillable = [
        'group_id',
        'user_id',
        'from',
        'to',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    const STATUS_DROP = 'drop';
    const STATUS_ACTIVE = 'active';
    const STATUS_FIRED = 'fired';

    public function user()
    {
        return $this->hasMany('App\User', 'user_id');
    }

    public function users(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public static function updateGroupUserWorkChart($dto, $old_work_chart):bool {
        try {
            $group_users = self::with(['users' => function ($query) use ($old_work_chart) {
                $query->where('work_chart_id', 0)
                    ->orWhere('work_chart_id', $old_work_chart)
                    ->whereNull('deleted_at');
            }])
                ->where('group_id', $dto->groupId)
                ->where('status', 'active')
                ->get();

            foreach ($group_users as $group_user){
                $user = $group_user->users;
                if($user !== null) {
                    $user->work_chart_id = $dto->workChartId;
                    $user->save();
                }
            }
            return true;
        }
        catch (\Exception $e){
            return false;
        }

    }
}
