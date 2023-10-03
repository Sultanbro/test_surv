<?php
declare(strict_types=1);

namespace App\Service\Referral\Core;

class ReferrerSalaryCalculator implements ReferrerSalaryCalculatorInterface
{

    public function calculate(ReferrerInterface $referrer): array
    {
        return $this->handle($referrer);
    }

    private function handle(ReferrerInterface $referrer, ReferrerLevel|bool $level = ReferrerLevel::FIRST): array
    {
        $calculated = [];
        if (!$level) {
            return $calculated;
        }

        // Add the current referrer's id and level to the calculated array
        $calculated[] = [
            'referrer_id' => $referrer->id,
            'expected_salary' => $level->value,
        ];

        if (!$referrer->hasParentReferrer()) {
            return $calculated;
        }

        // Recursively call handle and merge the results
        return array_merge($calculated, $this->handle($referrer->parentReferrer, $level->next()));
    }
}