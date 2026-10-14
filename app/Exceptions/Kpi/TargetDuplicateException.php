<?php
 
namespace App\Exceptions\Kpi;
 
use Exception;
use Illuminate\Http\Response;
class TargetDuplicateException extends Exception
{
    /**
     * custom error response
     */
    public function render($request) : Response
    {       
        return response([
            "error" => 'TargetDuplicateException',
            "message" => 'Этой цели уже назначен бонус. Поменяйте "Кому" назначить задачи по бонусу'
        ], 409);       
    }
}