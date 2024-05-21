<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // this for job tron
//        $this->call([
//            JobtronSeeder::class
//        ]);

        // this for tenants
        $this->call([
            PositionSeeder::class,
            ProgramSeeder::class,
            ProfileGroupSeeder::class,
            PageAndPermissionSeeder::class,
            WorkingDaySeeder::class,
            WorkingTimeSeeder::class,
            SettingSeeder::class,
            TariffSeeder::class,
            WorkChartTypeRbSeeder::class
//            MailingTemplateSeeder::class,
            // KpiSeeder::class,
            //KpiItemSeeder::class,
            //QuartalPremiumSeeder::class,
            //BonusSeeder::class,
            //ActivitySeeder::class,
        ]);
    }
}
