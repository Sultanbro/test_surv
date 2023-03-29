<?php
declare(strict_types=1);

namespace App\DTO\Portal;

final class UpdatePortalDTO
{
    /**
     * @param string $tenantId
     * @param ?string $mainPageVideo
     * @param ?int $mainPageVideoShowDaysAmount
     * @param ?KpiBacklightDTO $kpiBacklight
     */
    public function __construct(
        public string $tenantId, //TODO Portal refactor
        public ?string $mainPageVideo,
        public ?int $mainPageVideoShowDaysAmount,
        public ?KpiBacklightDTO $kpiBacklight,
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
            'kpi_backlight' => $this->kpiBacklight?->items,
        ];
    }
}