<?php

namespace App\Service\Settings\WorkChartService;

use App\DTO\Settings\WorkChartDTO\StoreWorkChartDTO;
use App\Repositories\WorkChartRepository;

/**
* Класс для работы с Service.
*/
class WorkChartService
{

    public function __construct(
        public WorkChartRepository $workChartRepository,
    )
    {}

    public function all():object
    {
        return $this->workChartRepository->all();
    }

    public function create(StoreWorkChartDTO $chartDTO):object
    {
        return $this->workChartRepository->create($chartDTO->toArray());
    }

    public function show($id):object
    {
        return $this->workChartRepository->show($id);
    }

    public function update($attribute,$id):object
    {
        return $this->workChartRepository->update($attribute,$id);
    }

    public function delete($id):bool
    {
        return $this->workChartRepository->delete($id);
    }
}