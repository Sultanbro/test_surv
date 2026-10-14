<?php

namespace App\Entities\DataTransferObjects\News;

use Illuminate\Contracts\Support\Arrayable;

class DictionaryItem implements Arrayable
{
    protected int $id;
    protected string $name;

    public function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return __($this->name);
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => __($this->name),
        ];
    }
}
