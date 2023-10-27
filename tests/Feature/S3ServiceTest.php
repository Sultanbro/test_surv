<?php

namespace Tests\Feature;

use App\Service\Custom\S3\S3Manager;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class S3ServiceTest extends TestCase
{
    protected S3Manager $s3Manager;

    public function setUp(): void
    {
        parent::setUp();
        Storage::fake('s3'); // Mock the S3 disk
        $this->s3Manager = new S3Manager();
    }

    /** @test */
    public function testStoreFile()
    {
        $file = UploadedFile::fake()->image($filename = 'logo.png');
        $contents = file_get_contents($file->getRealPath());

        $this->s3Manager->storeFile($filename, $contents);
        Storage::disk('s3')->assertExists($filename);
    }

    /** @test */
    public function testRetrieveFile()
    {
        $file = UploadedFile::fake()->image($filename = 'logo.png');
        $contents = file_get_contents($file->getRealPath());
        Storage::disk('s3')->put($filename, $contents);
        $retrievedContent = $this->s3Manager->retrieveFile($filename);
        $this->assertEquals($contents, $retrievedContent);
    }

    /** @test */
    public function testFileExists()
    {
        $file = UploadedFile::fake()->image($filename = 'logo.png');
        $contents = file_get_contents($file->getRealPath());
        $this->assertFalse($this->s3Manager->fileExists($filename));
        Storage::disk('s3')->put($filename, $contents);
        $this->assertTrue($this->s3Manager->fileExists($filename));
    }

    /** @test */
    public function testDeleteFile()
    {
        $file = UploadedFile::fake()->image($filename = 'logo.png');
        $contents = file_get_contents($file->getRealPath());
        Storage::disk('s3')->put($filename, $contents);
        $this->s3Manager->deleteFile($filename);
        Storage::disk('s3')->assertMissing($filename);
    }

    // Further tests, like one for `generateTemporaryUrl`, might be more complex due to the signed URL generation.
    // Depending on the criticality, you might decide to skip it or use a more sophisticated method to test.
}
