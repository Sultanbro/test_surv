<?php

namespace App\DTO\Tax;

class GetTaxesResponseDTO
{
    /**
     * @param array<TaxDto> $items
     */
    public function __construct(
        public array $items
    )
    {}

    /**
     * @param array $array
     * @return GetTaxesResponseDTO
     */
    public static function fromArray(array $array): GetTaxesResponseDTO
    {
        $items = [];

        foreach ($array as $arrItem) {
            array_push($items, new TaxDto(
                $arrItem->id,
                $arrItem->name,
                $arrItem->value,
                $arrItem->isPercent,
                $arrItem->isAssigned,
            ));
        }

        return new GetTaxesResponseDTO($items);
    }
}