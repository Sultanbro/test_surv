<?php


namespace App\Contracts;

interface CourseInterface
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

        
    /**
     * Get next element to chosen id
     * 
     * @param mixed $id
     * 
     * @return int $id
     * @return null
     */
    public function nextElement($id);
} 