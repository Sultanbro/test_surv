<?php

namespace App\Repositories\Interfaces\News;


use App\Entities\DataTransferObjects\FileStoreDTO;
use App\Models\News\File;

interface FileRepositoryInterface
{
    public function store(FileStoreDTO $dto): File;

    public function  delete($id);
}
