<?php


namespace App\Contracts;

interface HasOrderArray
{
    /**
     * Get array with id of elements order
     * 
     * Example:
     * [
     *     0 => 4,
     *     1 => 14
     * ]
     * 
     * @return array
     */
    public function getOrder();

} 