<?php
namespace App\Packages\Dilab\Network;

interface Response {

    /**
     * @param $statusCode
     * @return mixed
     */
    public function header($statusCode);

}