<?php

namespace App\Console\Commands\Admin;

use App\Models\CentralUser;
use App\Models\Tenant;
use App\User;
use App\UserDescription;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CreateAdminTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:admin_tenant';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create admin.jobtron.org';


    /**
     * Admin subdomain
     */
    protected $subdomain = 'admin';

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
        $this->line($this->subdomain . ' создается... Завершение через ~15 сек');

        $tenant = $this->createTenant($this->subdomain);
        $this->createUser($tenant);

        $central_domains = config('tenancy.central_domains');
        $domain = count($central_domains) > 0 ? $central_domains[0] : '';

        $this->line($this->subdomain . '.' . $domain . ' created');
    }

    private function createTenant($domain) : Tenant
    {   
        $centralUser = CentralUser::first();

        // create tenant
        $tenant = Tenant::create(['id' => $domain]);

        // create domain
        $tenant->createDomain($domain);

        // attach to owner
		if($centralUser) {
            $centralUser->tenants()->attach($tenant);
        } else {
            dd('В БД jobtron не существует пользователей (users)');
        }

        return $tenant;
    }

    private function createUser(Tenant $tenant) : void
    {
        tenancy()->initialize($tenant);

        $cu = CentralUser::first();

        $user = User::create([
            'name' => $cu->name,
            'last_name' => $cu->last_name,
            'email' => $cu->email,
            'phone' => $cu->phone,
            'password' => $cu->password,
            'is_admin' => 1
        ]);

        $ud = UserDescription::create([
            'is_trainee' => 0,
            'user_id'    => $user->id
        ]);
        
    }
}
