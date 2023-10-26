<?php

namespace Tests\Unit\Upload;

use App\Service\Uploads\UploadService;
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
        /** @var UploadService $service */
        $service = app(UploadService::class);
        $result = $service->store($fakeFile);
        dd($result);
        $this->assertIsString($result);
    }
}
