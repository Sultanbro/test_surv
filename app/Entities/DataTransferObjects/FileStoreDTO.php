<?php

namespace App\Entities\DataTransferObjects;

use Illuminate\Contracts\Support\Arrayable;

class FileStoreDTO implements Arrayable
{
    public function __construct(
        protected string $localName,
        protected string $originalName,
        protected string $extension,
    )
    {
    }

    public function toArray(): array
    {
        return [
            'original_name' => $this->originalName,
            'local_name' => $this->localName,
            'extension' => $this->extension,
        ];
    }

    public function getLocalName(): string
    {
        return $this->localName;
    }

    public function getOriginalName(): string
    {
        return $this->originalName;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }
}
