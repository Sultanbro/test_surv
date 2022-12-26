<?php

namespace App\Repositories\Articles;


use App\Entities\DataTransferObjects\FileStoreDTO;
use App\Models\File\File;
use App\Repositories\Interfaces\Article\FileRepositoryInterface;

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
