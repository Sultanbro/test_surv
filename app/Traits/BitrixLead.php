<?php

namespace App\Traits;

use App\Api\BitrixOld as Bitrix;
use App\Models\Bitrix\Lead;
use App\User;

trait BitrixLead
{
    public function changeDeal(User $user): void
    {
        $lead = Lead::userLeadByDesc($user)->first() ?? null;

        if (isset($lead)) {
            if($lead  && $lead->deal_id != 0) {
                $bitrix = new Bitrix();

                $bitrix->changeDeal($lead->deal_id, [
                    'STAGE_ID' => 'C4:WON' // не присутствовал на обучении
                ]);
            }
        }
    }
}