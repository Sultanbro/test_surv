<?php

namespace App\Service;

use App\Http\Requests\TaxStoreRequest;
use App\Repositories\TaxRepository;
use Exception;

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
}