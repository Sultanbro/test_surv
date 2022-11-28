<?php

namespace App\Service\Awards\AwardTypes;

use App\Models\Award\AwardType;
use App\Service\Interfaces\Award\AwardInterface;
use App\User;
use Exception;

class NominationAwardService implements AwardInterface
{

    public function fetch(array $params): array
    {
        $user = User::query()->findOrFail($params['user_id']);

        try {
            $result = [];
            $awardType = AwardType::query()->findOrFail($params['award_type_id']);

            $userAwards = $this->awardRepository->relationAwardUser($user,$awardType);
            $availableAwards =$this->awardRepository->availableAwards($user,$awardType);
            if ($this->isNomination($awardType)) {
                $result['my'] =  $userAwards;
                $result['available'] =  $availableAwards ;
                $otherAwards = [];

                foreach ($userAwards as $award){
                    if (!$award['hide']){
                        $otherAwards= $this->awardRepository->relationAwardUser($user,$awardType,'!=' );
                    }
                }
                $result['other'] =  $otherAwards;

            }
        }catch (\Throwable $exception) {
            throw new Exception($exception->getMessage());
        }

        return $result;
    }
}