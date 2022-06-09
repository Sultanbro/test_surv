<?php
namespace App\Packages\Dilab\Network;

use App\Packages\Dilab\Network\Response;

class SimpleResponse implements Response
{
    /**
     * @param $statusCode
     * @return mixed
     */
    public function header($statusCode)
    {
       
        if (200==$statusCode) {
            return header("HTTP/1.0 200 Ok");
        } else if (201==$statusCode) {
            return header("HTTP/1.0 201 Ok");
        } else if (404==$statusCode) {
            return header("HTTP/1.0 404 Not Found");
        }
        return header("HTTP/1.0 404 Not Found");
    }

}
