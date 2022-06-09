<?php

namespace Tests\Unit;

use App\ProfileGroup;
use App\Timetracking;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TimetrackingCommandTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {

        $timetrackings = Timetracking::where('exit', null)->get();
        foreach ($timetrackings as $t) {
            $groups = ProfileGroup::where('active', 1)->get();
            $user_group = null;

            foreach ($groups as $key => $group) {
                if ($group->users != null) {
                    $users = json_decode($group->users);
                    if (in_array($t->user_id, $users)) {
                        $user_group = $group;
                    }
                }
            }

            $dt = $t->enter->format('d.m.Y');
            $worktime_start = Carbon::parse($dt . ' ' . $user_group->work_start);
            $worktime_end = Carbon::parse($dt . ' ' . $user_group->work_end);

            if($worktime_end->isPast()){
               $t->exit = $worktime_end;
               $t->save();
            }

            
        }

        $this->assertTrue(true);
    }
}
