<?php
declare(strict_types=1);

namespace App\Service\Referral;

class ReferralSalaryCalculator implements ReferralSalaryCalculatorInterface
{

    private ReferrerInterface $referrer;

    public function calculate(ReferrerInterface $referrer): array
    {
        $this->referrer = $referrer;
        return $this->handle();
    }

    private function handle(ReferrerLevel|bool $level = ReferrerLevel::FIRST): array
    {
        $calculated = [];
        if ($this->referrer->hasParentReferrer() && $level) {
            // Recursively call handle and merge the results
            $calculated = array_merge($calculated, $this->handle($level->next()));
        }

        // Add the current referrer's id and level to the calculated array
        $calculated[$this->referrer->id] = $level->value;

        return $calculated;
    }

}