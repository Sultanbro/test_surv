<?php

namespace App\Console\Commands\RunOnce;

use App\Models\Bitrix\Lead;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class AlterUsersSegmentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:segment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'One time command';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $chunked = $this->users()->chunk(50);
        foreach ($chunked as $users) {
            foreach ($users as $user) {
                $segment = Lead::query()
                    ->where('phone', preg_replace("/[^[0-9]/", '', $user->phone))
                    ->orderByDesc('created_at')
                    ->first()?->segment;
                if ($segment) {
                    $user->update([
                        'segment' => $segment
                    ]);
                } else {
                    $user->update([
                        'segment' => 0
                    ]);
                }
            }
            sleep(1);
        }
    }

    private function users(): Collection
    {
        return User::query()
            ->whereIn('segment', [0, 99])
            ->where(DB::raw('YEAR(created_at)'), '=', '2023')
            ->get();
    }
}
