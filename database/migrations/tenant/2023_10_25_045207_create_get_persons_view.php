<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
          CREATE VIEW get_persons_view AS
          (
            SELECT 
                users.id,
                users.email,
                users.user_type,
                users.segment as segment,
                users.last_name,
                users.name,
                users.full_time,
                CONCAT(users.last_name, ' ', users.name) as FULLNAME,
                CONCAT(users.name, ' ', users.last_name) as FULLNAME2,
                COALESCE(bl.created_at, users.created_at) as created_at,
                users.deleted_at,
                users.position_id,
                users.phone,
                users.birthday,
                users.description,
                users.working_day_id,
                users.working_time_id,
                users.work_start,
                users.work_end,
                users.program_id,
                ud.is_trainee,
                ud.fire_date,
                ud.fire_cause,
                ud.applied
            FROM users
                left join user_descriptions as ud on ud.user_id = users.id
                left join bitrix_leads as bl on bl.phone = users.phone
          )
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS get_persons_view');
    }
};
