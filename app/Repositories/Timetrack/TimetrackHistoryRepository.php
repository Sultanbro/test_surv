<?php

namespace App\Repositories\Timetrack;

use App\Repositories\CoreRepository;
use App\Repositories\Interfaces\TimeTrackHistoryForTrainee;
use App\TimetrackingHistory as Model;
use App\User;
use Illuminate\Support\Facades\Auth;

class TimetrackHistoryRepository extends CoreRepository implements TimeTrackHistoryForTrainee
{
    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return Model::class;
    }

    /**
     * Принятие на работу стажера.
     * @param User $user
     * @return void
     */
    public function createTrainee(User $user): void
    {
        $this->model()->create([
            'author_id' => Auth::user()->id,
            'author' => Auth::user()->name .' '. Auth::user()->last_name,
            'user_id' => $user->id,
            'description' => 'Принятие на работу стажера',
            'date' => date('Y-m-d')
        ]);
    }
}