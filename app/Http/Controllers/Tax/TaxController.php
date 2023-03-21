<?php

namespace App\Http\Controllers\Tax;

use App\DTO\Tax\GetTaxesResponseDTO;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Tax\Response\GetTaxesResponse;
use Exception;

class TaxController extends Controller
{   
    /**
     * @return GetTaxesResponse
     */
    public function get(): GetTaxesResponse
    {
        // use GetTaxesDTO
        // to get user_tax assigns

        // not implemented. used mock
        $mock = GetTaxesResponseDTO::fromArray([[
            'id' => 1,
            'name' => 'asd1',
            'value' => 1,
            'isPercent' => true,
            'isAssigned' => true,
        ], [
            'id' => 2,
            'name' => 'asd2',
            'value' => 1000,
            'isPercent' => false,
            'isAssigned' => true,
        ], [
            'id' => 3,
            'name' => 'asd3',
            'value' => 5,
            'isPercent' => true,
            'isAssigned' => true,
        ], [
            'id' => 4,
            'name' => 'aqq4',
            'value' => 5000,
            'isPercent' => false,
            'isAssigned' => false,
        ], [
            'id' => 5,
            'name' => 'qqw5',
            'value' => 1111,
            'isPercent' => false,
            'isAssigned' => false,
        ], [
            'id' => 6,
            'name' => 'wqqq6',
            'value' => 11,
            'isPercent' => true,
            'isAssigned' => false,
        ], [
            'id' => 7,
            'name' => 'asdqq7',
            'value' => 1,
            'isPercent' => true,
            'isAssigned' => false,
        ]]);

        return GetTaxesResponse::success($mock);
    }

    public function create() {
        // use CreateTaxDTO
        // creates tax
        // returns created Tax
        throw new Exception('not implemented');
    }

    public function update() {
        // use UpdateTaxDTO
        // returns success
        throw new Exception('not implemented');
    }

    public function setAssigned() {
        // use SetAssignedTaxDTO
        // creates user_tax connection
        // returns success
        throw new Exception('not implemented');
    }
}
