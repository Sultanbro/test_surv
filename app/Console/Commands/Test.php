<?php

namespace App\Console\Commands;

use App\ProfileGroup;
use App\User;
use Illuminate\Console\Command;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Запуск проверки таблиц учетам времени';

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
       
        $musers = \DB::connection('marketing')->table('b_user')->get();

		foreach ($musers as $key => $mar_user) {
			$user = User::withTrashed()->where('id', $mar_user->ID)->where('email', $mar_user->EMAIL)->first();
		
			if($user) {

               
                dump([
					'user_id' => $user->id,
					'email' => $user->email,
					'name' => $user->last_name . ' ' . $user->name, 
					'created_at' => $mar_user->DATE_REGISTER,
					'deleted_at' => $mar_user->deactivate_date == '0000-00-00 00:00:00' ? null :  $mar_user->deactivate_date
				]);

                $user->deleted_at = $mar_user->deactivate_date == '0000-00-00 00:00:00' ? null :  $mar_user->deactivate_date;
                $user->save();
                
             //   dd($mar_user->deactivate_date == '0000-00-00 00:00:00' ? null :  $mar_user->deactivate_date);
			}

		}




		dd($a);
    }
}