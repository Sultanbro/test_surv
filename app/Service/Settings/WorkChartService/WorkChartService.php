<?php

namespace App\Service\Settings\WorkChartService;

use App\DTO\Settings\WorkChartDTO\StoreWorkChartDTO;
use App\DTO\Settings\WorkChartDTO\UpdateWorkChartDTO;
use App\Models\WorkChartModel;
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
        return  WorkChartModel::indexModel();
    }

    public function create(StoreWorkChartDTO $chartDTO):object
    {
        return WorkChartModel::createModel($chartDTO->toArray());
    }

    public function show($id):object
    {
        return WorkChartModel::showModel($id);
    }

    public function update(UpdateWorkChartDTO $chartDTO,$id):object
    {
        return WorkChartModel::updateModel($chartDTO->toArray(),$id);
    }

    public function delete($id):bool
    {
        return WorkChartModel::deleteModel($id);
    }
}