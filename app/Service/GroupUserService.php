<?php

namespace App\Service;

use App\User;
use Illuminate\Support\Facades\DB;

class GroupUserService
{
    /**
     * Сохраняем массив пользователей в таблицу group_user
     * @param $request
     * @return void
     */
    public function save($request): void
    {
        $users   = $request->users;
        $groupId = $request->group_id;

        foreach ($users as $id)
        {
            $exist = DB::table('group_user')->where([
                ['user_id',  '=', $id],
                ['group_id', '=', $groupId]
            ])->exists();

            if (!$exist) {
                DB::table('group_user')->insert([
                    'user_id'  => $id,
                    'group_id' => $groupId
                ]);
            }
        }
    }

    /**
     * @param $usersId
     * @param $groupId
     * @return void
     */
    public function drop($usersId, $groupId): void
    {
        DB::table('group_user')->whereIn('user_id', $usersId)->where('group_id', $groupId)->delete();
    }
}