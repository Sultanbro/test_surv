<?php

namespace App\Console\Commands;

use App\Kpi;
use App\SavedKpi;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SaveUserKpi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:save_kpi {date?}';  //php artisan salary:update month 03.2021 // целый месяц , долго
                                                             //php artisan salary:update day 01.03.2021 // по одному дню

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Сохранить kpi';

    /**
     * Variables that used 
     *
     * @var mixed
     */
    public $date; // Дата пересчета 
    
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
        
        $date = $this->argument('date') ?? date('Y-m-d');
        
        $users =  \DB::table('users')
            ->whereNull('deleted_at')
            ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
            ->where('is_trainee', 0)
            ->select(['users.id','users.last_name', 'users.name'])
            ->get();

        $date = Carbon::parse($date)->day(1)->format('Y-m-d');

        $this->comment($users->count());
        foreach ($users as $key => $user) {

            $this->line($key . ' '. $user->id);
            $kpi = Kpi::userKpi($user->id, $date, 1);

            // save 
            $sk = SavedKpi::where('user_id', $user->id)->where('date', $date)->first();
            if($sk) {
                $sk->total = $kpi;
                $sk->save();
            } else {
                SavedKpi::create([
                    'user_id' => $user->id,
                    'total' => $kpi,
                    'date' => $date
                ]);
            }

        }
       



    }

}
