<?php

namespace App\Repositories\Interfaces\Articles;


use App\Entities\DataTransferObjects\FileStoreDTO;
use App\Models\File\File;

interface FileRepositoryInterface
{
    public function store(FileStoreDTO $dto): File;

    public function  delete($id);
}
