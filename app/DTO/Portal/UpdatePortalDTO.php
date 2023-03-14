<?php
declare(strict_types=1);

namespace App\DTO\Portal;

final class UpdatePortalDTO
{
    /**
     * @param int $ownerId
     * @param ?string $mainPageVideo
     * @param ?string $mainPageVideoShowDaysAmount
     */
    public function __construct(
        public int $ownerId,
        public ?string $mainPageVideo,
        public ?int $mainPageVideoShowDaysAmount,
    )
    {}

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'main_page_video' => $this->mainPageVideo ?? null,
            'main_page_video_show_days_amount' => $this->mainPageVideoShowDaysAmount ?? null,
        ];
    }
}