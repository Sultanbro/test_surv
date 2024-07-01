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
            $items[] = new TaxDTO(
                $arrItem['id'],
                $arrItem['name'],
                $arrItem['value'],
                $arrItem['is_percent'],
                $arrItem['isAssigned'],
                $arrItem['end_subtraction'],
            );
        }

        return new GetTaxesResponseDTO($items);
    }
}
