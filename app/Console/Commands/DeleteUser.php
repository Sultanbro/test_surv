<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\UserDeletePlan;
use Illuminate\Http\Request;

class DeleteUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'userxxxxxxxxxxxxx:delete:dontusethisitsnotforsuign';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Уволить сотрудников с отработкой в указанное время';

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
        $delete_plans = UserDeletePlan::whereDate('delete_time', date('Y-m-d'))->get();

        foreach ($delete_plans as $plan) {
            $user = User::where([
                'id' => $plan->user_id,
                'UF_ADMIN' => 1,
            ])->first();

            if(!$user) continue;
            
            $request = new Request();
            $request->user_id = $plan->user_id;
            $request->month = date('m');
            $request->day = date('d');

            try {
                User::deleteUser($request);

                $plan->executed = 1;
                $plan->save();


            } catch (\Exception $e) {
                // 'Сотрудник не уволен по крону: '. $plan->user_id
                continue;
            }
            
            
        }
    }
    
}
