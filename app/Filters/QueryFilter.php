<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

abstract class QueryFilter
{
    protected Request $request;
    protected Builder $builder;
    protected array $params;

    protected array $booleanFields = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->setFilters($this->request->all());
    }

    public function apply(Builder $builder): Builder
    {
        $this->builder = $builder;

        foreach ($this->fields() as $field => $value) {
            $method = Str::camel($field);
            if (method_exists($this, $method)) {
                call_user_func_array([$this, $method], [$value]);
            }
        }

        return $this->builder;
    }

    protected function fields(): array
    {
        return $this->checkBooleanFields(
            array_filter(
                array_map(function ($item) {
                    return is_string($item) ? trim($item) : $item;
                }, $this->params),
                function ($value) {
                    return $value !== '';
                }
            )
        );
    }

    private function checkBooleanFields(array $fields): array
    {
        $result = [];

        array_walk($fields, function ($value, $key) use (&$result) {
            $result[$key] = in_array($key, $this->booleanFields) && !is_bool($value) ? $this->request->boolean($key) : $value;
        });

        return $result;
    }

    public function setFilters(array $params): static
    {
        $this->params = $params;
        return $this;
    }
}
