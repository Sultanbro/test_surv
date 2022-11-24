<?php

namespace App\Repositories\News;


use App\Entities\DataTransferObjects\FileStoreDTO;
use App\Models\News\File;
use App\Repositories\Interfaces\News\FileRepositoryInterface;

class FileRepository implements FileRepositoryInterface
{
    public function store(FileStoreDTO $dto): File
    {
        return File::create($dto->toArray());
    }

    public function delete($id)
    {
        return File::where('id', $id)->delete();
    }
}
