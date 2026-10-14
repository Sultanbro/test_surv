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
    public function userTax(
        array $taxArray
    ): void
    {
        try {
            $taxes = $taxArray['taxes'] ?? null;
            $id = $taxArray['id'] ?? null;

            if ($taxArray['tax']) {
                $this->repository->insertMultipleTaxes($taxArray['tax']);
            } else if ($taxes) {
                $taxIds = [];
                foreach ($taxes as $id => $tax) {
                    $taxIds[] = $id;
                }

                $this->repository->updateOrDelete($taxArray['id'], $taxIds, $taxes);
            } else $this->repository->deleteAll($id);

        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }
}