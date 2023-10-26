<?php

namespace App\Service\Files;

use App\Entities\DataTransferObjects\FileStoreDTO;
use App\Exceptions\News\BusinessLogicException;
use App\Helpers\FileHelper;
use App\Models\File\File;
use App\Repositories\Interfaces\Article\FileRepositoryInterface;
use Illuminate\Http\UploadedFile;

class FileService
{
    public function __construct(protected FileRepositoryInterface $repository)
    {
    }

    /**
     * @param UploadedFile $file
     * @param string $disk
     * @return File
     * @throws BusinessLogicException
     */
    public function store(UploadedFile $file, string $disk = 's3'): File
    {
        if (!$filename = FileHelper::save($file, config('app.file.path', $disk))) {
            throw new BusinessLogicException(__('exception.save_error'));
        }
        return $this->repository->store(new FileStoreDTO(
            $filename,
            $file->getClientOriginalName(),
            $file->getClientOriginalExtension(),
        ));
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
