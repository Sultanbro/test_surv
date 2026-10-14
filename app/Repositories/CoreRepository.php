<?php
declare(strict_types=1);
namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class CoreRepository
{
    /**
     * @var Model
     */
    protected Model $model;

    public function __construct()
    {
        $this->model = app($this->getModelClass());
    }

    abstract protected function getModelClass();


    protected function model()
    {
        return clone $this->model;
    }
}