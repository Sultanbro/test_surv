<?php

namespace App\Service\Files;

use App\Entities\DataTransferObjects\FileStoreDTO;
use App\Exceptions\News\BusinessLogicException;
use App\Helpers\FileHelper;
use App\Models\File\File;
use App\Repositories\Interfaces\Articles\FileRepositoryInterface;
use Illuminate\Http\UploadedFile;

class FileService
{
    public function __construct(protected FileRepositoryInterface $repository)
    {
    }

    /**
     * @param UploadedFile $file
     * @return File
     * @throws BusinessLogicException
     */
    public function store(UploadedFile $file): File
    {
        if (!$filename = FileHelper::save($file, config('app.file.path'))) {
            throw new BusinessLogicException(__('exception.save_error'));
        }

        if (!$model = $this->repository->store(new FileStoreDTO(
            $filename,
            $file->getClientOriginalName(),
            $file->getClientOriginalExtension(),
        ))) {
            throw new BusinessLogicException(__('exception.save_error'));
        }

        return $model;
    }

    /**
     * @param File $file
     * @return bool
     * @throws BusinessLogicException
     */
    public function delete(File $file): bool
    {
        $filename = $file->local_name;

        if (!$result = $this->repository->delete($file->id)) {
            throw new BusinessLogicException(__('exception.delete'));
        }

        FileHelper::delete($filename, config('app.file.path'));

        return $result;
    }
}
