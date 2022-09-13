<?php

namespace App\Listeners;

use App\Events\TransferUserInGroupEvent;
use App\Models\GroupUser;
use App\Models\History;
use App\ProfileGroup;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class TransferUserInGroupListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param TransferUserInGroupEvent $event
     * @return void
     */
    public function handle(TransferUserInGroupEvent $event): void
    {
        $oldGroup = $event->oldGroup;
        $users    = $oldGroup->users()->get();

        $this->saveInHistory($users, $oldGroup);

        foreach ($users as $user)
        {
            GroupUser::query()->where([
                ['user_id', $user->id],
                ['group_id', $oldGroup->id]
            ])->update([
                'group_id' => $event->newGroup,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Сохраняем старые данные для сотрудника в отделе.
     * @param $users
     * @param $group
     * @return void
     */
    private function saveInHistory($users, $group): void
    {
        foreach ($users as $user)
        {
            History::query()->create([
                'reference_table'   => get_class($user),
                'reference_id'      => $user->id,
                'actor_id'  => auth()->id() ?? 1,
                'payload'   => json_encode([
                    'action'    => 'group-delete',
                    'id'        => $group->id,
                    'date'      => Carbon::now()->toDateString(),
                    'comment'   => 'Was an employee of the department ' . $group->name . ' the department has been deactivated.',
                    'working_hours'  => $group->work_start . '-' . $group->work_end,
                    'workdays'       => $group->workdays,
                    'salary'         => $user->zarplata->zarplata ?? null,
                    'type'           => $user->type,
                    'operating-mode' => $user->full_time == 1 ? 'Full time' : 'Part time'
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
