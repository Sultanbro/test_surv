<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckDuplicatesInReferral extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:ref-duplicates';

    protected $description = 'Delete duplicate records from referral_salaries';

    public function handle(): void
    {
        // Deleting 'ATTESTATION' Duplicates
        DB::statement("
         DELETE rs 
         FROM referral_salaries rs JOIN 
              (SELECT MIN(id) as id 
              FROM referral_salaries 
              WHERE type = 'ATTESTATION' 
              GROUP BY referral_id) as keepers 
              ON rs.id = keepers.id 
              WHERE rs.type = 'ATTESTATION' 
              AND keepers.id IS NULL");

        // Deleting 'FIRST_WORK' Duplicates
        DB::statement("
         DELETE rs 
         FROM referral_salaries rs JOIN 
              (SELECT MIN(id) as id 
              FROM referral_salaries 
              WHERE type = 'FIRST_WORK' 
              GROUP BY referral_id) as keepers 
              ON rs.id = keepers.id 
              WHERE rs.type = 'FIRST_WORK' 
              AND keepers.id IS NULL");

        // Deleting 'FIRST_QUERY' Duplicates (replace with actual condition)
        // DB::statement("YOUR SQL QUERY FOR 'FIRST_QUERY' DUPLICATES");

        // Deleting 'TRAINEE' and 'WORK' Duplicates
        DB::statement("
            DELETE rs 
            FROM referral_salaries rs JOIN 
                   (SELECT MIN(id) as id 
                   FROM referral_salaries 
                   WHERE type IN ('WORK', 'TRAINEE') 
                   GROUP BY referral_id, date) as keepers ON 
                   rs.id = keepers.id 
                   WHERE rs.type IN ('WORK', 'TRAINEE') 
                   AND keepers.id IS NULL");

        $this->info('All duplicate records deleted successfully.');
    }
}
