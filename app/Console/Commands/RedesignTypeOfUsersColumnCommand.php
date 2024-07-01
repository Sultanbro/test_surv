<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RedesignTypeOfUsersColumnCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:redesign_column';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Поменять тип хранение данных для id пользователей в таблице profile_groups';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->loopForUsersData();
    }

    /**
     * Получаем ID Отдела и ID пользователя как ключ => значение
     */
    private function getUsersData(): array
    {
        return DB::table('profile_groups')->pluck('users', 'id')->toArray();
    }

    /**
     * Получаем данные и вставляем в таблицу group_user
     * @return void
     */
    private function loopForUsersData(): void
    {
        foreach ($this->getUsersData() as $departmentId => $users)
        {
            $users = User::withTrashed()->whereIn('id', json_decode($users))->get();
            foreach ($users as $user)
            {
                $exist = DB::table('group_user')->where([
                    ['user_id',  '=', $user->id],
                    ['group_id', '=', $departmentId]
                ])->exists();

                if (!$exist) {
                    DB::table('group_user')->insert([
                        'group_id' => $departmentId,
                        'user_id' => $user->id,
                        'from' => $user->created_at ?? null,
                        'to' => $user->deleted_at ?? null,
                        'status' => $user->deleted_at == '' ? 'active' : 'drop',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }
    }
}
