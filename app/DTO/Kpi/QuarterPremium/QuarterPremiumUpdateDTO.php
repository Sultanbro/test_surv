<?php

namespace App\DTO\Kpi\QuarterPremium;

class QuarterPremiumUpdateDTO
{
    /**
     * @param int $id
     * @param int|null $activityId
     * @param string|null $targetAbleType
     * @param string|null $title
     * @param string|null $text
     * @param string|null $plan
     * @param string|null $from
     * @param string|null $to
     * @param int|null $sum
     * @param string|null $cell
     */
    public function __construct(
        public int     $id,
        public ?int    $activityId,
        public ?string $targetAbleType,
        public ?string $title,
        public ?string $text,
        public ?string $plan,
        public ?string $from,
        public ?string $to,
        public ?int    $sum,
        public ?string $cell,
    )
    {
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'activity_id' => $this->activityId,
            'targetable_type' => $this->targetAbleType,
            'title' => $this->title,
            'text' => $this->text,
            'plan' => $this->plan,
            'from' => $this->from,
            'to' => $this->to,
            'sum' => $this->sum,
            'cell' => $this->cell,
        ];
    }
}