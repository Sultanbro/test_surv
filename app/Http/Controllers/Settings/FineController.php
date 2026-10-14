<?php

namespace App\Http\Controllers\Settings;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Fine;
use Illuminate\Support\Facades\DB;

class FineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            "fines" => Fine::all()
        ]);
    }

    /**
     * Update the resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Fine $fineModel)
    {
        $resultTransaction = false;

        DB::transaction(function () use ( &$resultTransaction,  $request, $fineModel) {
            foreach ($request['newfines'] as $newFine) {
                $fineModel->addFine($newFine);
            }

            foreach ($request['editedfines'] as $editedfine) {
                $fineModel->editFine($editedfine);
            }

            foreach ($request['deletedfines'] as $id) {
                $fineModel->deleteFine($id);
            }

            $resultTransaction = true;
        }, 5);

        $message = $resultTransaction ? 'Данные успешно сохранены!' : 'Не удалось сохранить изменения. Попробуйте еще раз!';

        return response()->json([
            "message" => $message
        ]);
    }

}
