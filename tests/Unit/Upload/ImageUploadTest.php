<?php

namespace Tests\Unit\Upload;

use App\Helpers\FileHelper;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ImageUploadTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_it_can_upload_image()
    {
        $fileName = 'test_image.jpg';
        $fakeFile = UploadedFile::fake()->image($fileName);
        /** @var FileHelper $service */
        $service = app(FileHelper::class);
        $result = $service->save($fakeFile, 'uploads','public');
        $this->assertIsString($result);
    }
}
