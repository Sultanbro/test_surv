<?php

namespace App\Service\Interfaces\Award;

interface AwardBuilderInterface
{
    public function handle(string $typeName);

}