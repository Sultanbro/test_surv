<?php

namespace App\Console\Commands\Tenancy;

use Illuminate\Console\Command;
use App\Models\Task;
use App\Models\Checklist;
use Carbon\Carbon;

class CollectUsersToTenantPivot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenancy:collect_users {force?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Collect users to table tenant_pivot from tenants\' databases by email';

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
        // dd('STOOOOOP');
        $this->line('start collect');

        $tenant_pivot = \DB::connection('mysql')->table('tenant_pivot')->get();

        if($this->argument('force') !== 'true' && $tenant_pivot->count() !== 0) {
            throw new \Exception('Can\'t collect users to non-empty tenant_pivot table');
        }

        $users = \App\User::withTrashed()->get()->filter(
            fn($u) => $tenant_pivot
                ->where('tenant_id', 'bp')
                ->where('user_id', $u->getKey())
                ->isNotEmpty()
        );

        $all = $users->count();

        $this->line('ALL USERS: ' . $all);

        $rows = [];
        $start = 1;
        foreach ($users as $user) {
            $this->line('Progress '  . round($start / $all, 3) . '%');  
            
            $centralUser = \App\Models\CentralUser::where('email', $user->email)->first();

            if(!$centralUser) {
                $centralUser = \App\Models\CentralUser::create([
                    'email' => $user->email,
                    'phone' => $user->phone ?? '77000000000',
                    'name' => $user->name ?? 'Noname',
                    'last_name' => $user->last_name ?? 'Nolastname',
                    'password' => $user->password,
                    'birthday' => $user->birthday,
                    'city' => $user->working_city,
                    'country' => $user->working_country,
                    'currency' => $user->currency,
                ]);

                $rows[] = [
                    'user_id' => $centralUser->id,
                    'tenant_id' => 'bp',
                    'owner' => $centralUser->id == 1 ? 1 : 0
                ];
            }

            $start++;
        }

        $this->line('NEW USERS: ' . count($rows));

        \DB::connection('mysql')->table('tenant_pivot')->insert($rows);

        $this->line('SUCCESS');
    }
}
