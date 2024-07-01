<?php

namespace App\Repositories\PromoCode;

interface PromoCodeRepositoryInterface
{
    public function getAllValidPromoCodes();

    public function getValidItemByCode(string $code): PromoCodeDto;

    public function exitsValidItem(string $code): bool;
}