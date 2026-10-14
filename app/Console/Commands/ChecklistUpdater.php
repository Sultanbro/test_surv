<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use App\Models\Checklist;
use Carbon\Carbon;

class ChecklistUpdater extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checklist:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set checklist for all users for today';

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
        $this->line('updating');
        $checklists = Checklist::all();
        foreach($checklists as $checklist){
            $users = $checklist->users;
            $tasks = $checklist->tasks;
            foreach($tasks as $task){
                foreach($users as $user){
                    $task->checkedtasks()->updateOrCreate([
                        'created_date' => Carbon::now()->toDateString(),  
                        'task_id' => $task->id,
                        'user_id' => $user->id,      
                    ],
                    [              
                        'checked' => 'false',
                        'url' => ''
                    ]);
                }
            }
        }
    }
}
