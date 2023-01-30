<?php

namespace App\Service\Settings\WorkChartService;

use App\DTO\Settings\WorkChartDTO\StoreWorkChartDTO;
use App\DTO\Settings\WorkChartDTO\UpdateWorkChartDTO;
use App\Repositories\WorkChartRepository;
use function App\DTO\Settings\toArray;

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

    public function update(UpdateWorkChartDTO $chartDTO,$id):object
    {
        return $this->workChartRepository->update($chartDTO->toArray(),$id);
    }

    public function delete($id):bool
    {
        return $this->workChartRepository->delete($id);
    }
}