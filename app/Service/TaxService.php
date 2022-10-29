<?php

namespace App\Service;

use App\Http\Requests\TaxStoreRequest;
use App\Repositories\TaxRepository;
use Exception;
use Illuminate\Http\Request;

class TaxService
{
    public TaxRepository $repository;

    public function __construct()
    {
        $this->repository = app(TaxRepository::class);
    }

    /**
     * @throws Exception
     */
    public function store(TaxStoreRequest $request): void
    {
        try {
            $this->repository->createNewTax($request->all());
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * Создание обновление и удаление налогов из профиля пользователя.
     *
     * @param Request $request
     * @return void
     * @throws Exception
     */
    public function userTax(Request $request): void
    {
        try{
            $taxes  = $request->input('taxes') ?? null;
            $id     = $request->id ?? null;

            if($request->input('tax')) {
                $this->repository->insertMultipleTaxes($request->input('tax'));
            }else if($taxes) {
                $taxIds = [];
                foreach ($taxes as $id => $tax)
                {
                    $taxIds[] = $id;
                }

                $this->repository->updateOrDelete($request->id, $taxIds, $taxes);
            }else $this->repository->deleteAll($id);

        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }
}