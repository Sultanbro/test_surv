<?php

namespace App\Support\Response;

interface Response
{
    public function getData();

    public function getStatus();

    public function first();
}