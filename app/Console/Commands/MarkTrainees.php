<?php

namespace App\Console\Commands;

use App\DayType;
use App\Facade\Referring;
use App\Models\Bitrix\Lead;
use App\ProfileGroup;
use App\Service\Department\UserService;
use App\Trainee;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;

class MarkTrainees extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'timetracking:mark_trainees';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Отмечать стажеров оранжевым в табели пока их не примут в штат или отпустят';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        if (in_array(date('w'), ['6', '0'])) {
            return '';
        }

        $groups = ProfileGroup::where('active', 1)->get();


        $users = [];
        foreach ($groups as $group) {
            $gusers = (new UserService)->getTrainees($group->id, Carbon::now()->startOfMonth()->format('Y-m-d'));
            $gusers = collect($gusers)->pluck('id')->toArray();

            $users = array_merge($users, $gusers);
        }

        $trainees = DB::table('users')
            ->whereNull('deleted_at')
            ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
            ->where('ud.is_trainee', 1)
            ->whereIn('users.id', $users);

        if (date('w') == '6') {
            $trainees->where('working_day_id', 2); // 6-1
        }

        $trainees = $trainees->get();
        foreach ($trainees as $trainee) {

            $lead = Lead::where('user_id', $trainee->user_id)->first();

            if ($lead) {
                if ($lead->invite_at) {
                    if (Carbon::parse($lead->invite_at)->startOfDay()->timestamp - time() > 0) {
                        continue;
                    }
                } else {
                    continue;
                }
            }

            $daytype = Daytype::query()->where('user_id', $trainee->user_id)->where('date', date('Y-m-d'))->first();
            if (!$daytype) {
                DayType::query()->create([
                    'user_id' => $trainee->user_id,
                    'type' => 5, // Стажировка
                    'email' => '.',
                    'date' => date('Y-m-d'),
                    'admin_id' => 5,
                ]);
                if (tenant()->getKey() === 'bp') {
                    Referring::touchReferrerSalaryDaily($trainee, now());
                }
            }
        }
    }
}
