<?php

namespace App\Service\Files;

use App\Entities\DataTransferObjects\FileStoreDTO;
use App\Exceptions\News\BusinessLogicException;
use App\Helpers\FileHelper;
use App\Models\File\File;
use App\Repositories\Interfaces\Article\FileRepositoryInterface;
use App\Service\Custom\Files\FileManager;
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
        $filename = FileHelper::save($file, config('app.file.path', "upload"), $disk);

        if (!$filename) {
            throw new BusinessLogicException(__('exception.save_error'));
        }

        return $this->save($filename, $file);
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

    public function storeV2(UploadedFile $file, string $disk): File
    {
        /** @var FileManager $manager */
        $manager = app(FileManager::class);
        $manager->apply($file, 'uploads');
        return $this->save($manager->name(), $file);
    }

    private function save(string $filename, UploadedFile $file): File
    {
        return $this->repository->store(new FileStoreDTO(
            $filename,
            $file->getClientOriginalName(),
            $file->getClientOriginalExtension(),
        ));
    }
}
