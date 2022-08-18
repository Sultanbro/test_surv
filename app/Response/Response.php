<?php

namespace App\Response;

interface Response
{
    public function getData();

    public function getStatus();

    public function getResult();

    public function first();
}