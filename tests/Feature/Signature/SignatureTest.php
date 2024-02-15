<?php

namespace Tests\Feature\Signature;

use App\Models\File\File;
use App\Models\SmsCode;
use App\ProfileGroup;
use App\Service\Sms\CodeGenerator;
use App\Service\Sms\CodeGeneratorInterface;
use App\Service\Sms\SmsInterface;
use App\Service\Sms\UCallSmsService;
use App\User;
use Illuminate\Http\UploadedFile;
use Mockery;
use Tests\HasAuthUser;
use Tests\TenantTestCase;

/**
 * @url POST signature/groups/{group}/files
 * @url GET signature/groups/{group}/files
 * @url PUT signature/files/{file}
 * @url GET signature/users/{user}/files
 * @url POST signature/users/{user}/sms
 * @url POST signature/users/{user}/files/{file}/verification
 */
class SignatureTest extends TenantTestCase
{
    use HasAuthUser;

    public function test_user_can_upload_documents()
    {
        $this->authenticate();
        $fileName = 'test_file.pdf';
        $fakeFile = UploadedFile::fake()->create($fileName, 1, 'pdf');
        $params = [
            'file' => $fakeFile,
            'local_name' => 'some name'
        ];
        $group = ProfileGroup::factory()->create();
        $response = $this->json('post', "/signature/groups/$group->id/files", $params);
        $response->assertStatus(200);
        $this->assertDatabaseHas('files', [
            'fileable_id' => $group->id,
            'fileable_type' => ProfileGroup::class,
            'local_name' => 'some name',
        ]);
    }

    public function test_user_can_update_document_by_id()
    {
        $this->authenticate();
        $file = File::factory()->create([
            'local_name' => 'before edit'
        ]);

        $params = [
            'local_name' => 'after edit'
        ];
        $response = $this->json('put', "/signature/files/$file->id", $params);
        $response->assertStatus(200);
        $this->assertDatabaseHas('files', [
            'id' => $file->id,
            'local_name' => 'after edit'
        ]);
    }

    public function test_user_can_get_group_documents()
    {
        $this->authenticate();

        $group = ProfileGroup::factory()->create();
        $files = File::factory(4)->create([
            'fileable_id' => $group->id,
            'fileable_type' => ProfileGroup::class
        ]);

        $response = $this->json('get', "/signature/groups/$group->id/files");
        $response->assertStatus(200);
        foreach ($files as $file) {
            $response->assertJsonFragment([
                'id' => $file->id,
                'url' => $file->url
            ]);
        }
    }

    public function test_user_can_get_documents()
    {
        $this->authenticate();

        $group = ProfileGroup::factory()->create();
        $user = User::factory()->create();
        $user->groups()->attach($group, [
            'status' => 'active'
        ]);

        $notSignedFiles = File::factory(4)->create([
            'fileable_id' => $group->id,
            'fileable_type' => ProfileGroup::class
        ]);
        $signedFiles = File::factory(2)->create([
            'fileable_id' => $group->id,
            'fileable_type' => ProfileGroup::class
        ]);
        $user->signedFiles()->attach($signedFiles);

        $response = $this->json('get', "/signature/users/$user->id/files");
        $response->assertStatus(200);

        foreach ($signedFiles as $file) {
            $response->assertJsonFragment([
                'id' => $file->id,
                'url' => $file->url,
                'signed' => true
            ]);
        }

        foreach ($notSignedFiles as $file) {
            $response->assertJsonFragment([
                'id' => $file->id,
                'url' => $file->url,
                'signed' => false
            ]);
        }
    }

    public function test_user_can_get_sms_code()
    {
        $fakeCode = 454545;
        // Mock the code generator
        $mockGeneratorService = Mockery::mock(CodeGenerator::class);
        $mockGeneratorService->shouldReceive('generate')
            ->once()
            ->andReturn($fakeCode);
        $this->app->instance(CodeGeneratorInterface::class, $mockGeneratorService);
        // Mock the SMS service
        $mockSmsService = Mockery::mock(UCallSmsService::class);
        // Mock the send method
        $mockSmsService->shouldReceive('send')
            ->once()
            ->andReturn([
                "success" => true,
                "code" => 200,
                "results" => [
                    "actionId" => 22207570,
                    "phone" => '4554545878'
                ]]);
        // Replace the real SMS service with the mock
        $this->app->instance(SmsInterface::class, $mockSmsService);

        $this->authenticate();

        $group = ProfileGroup::factory()->create();
        $user = User::factory()->create();
        $user->groups()->attach($group, [
            'status' => 'active'
        ]);

        $params = [
            'phone' => '45454545454'
        ];
        $response = $this->json('post', "/signature/users/$user->id/sms", $params);
        $response->assertStatus(200);
        $this->assertDatabaseHas('sms_codes', [
            'code' => $fakeCode,
            'user_id' => $user->id
        ]);
    }

    public function test_user_can_verify_sms_code_and_sign_file()
    {
        $this->authenticate();

        $group = ProfileGroup::factory()->create();
        $user = User::factory()->create();
        $user->groups()->attach($group, [
            'status' => 'active'
        ]);
        $fileToSign = File::factory()->create([
            'fileable_id' => $group->id,
            'fileable_type' => ProfileGroup::class
        ]);
        $smsCode = SmsCode::factory()->create([
            'user_id' => $user->id
        ]);

        $params = [
            'code' => $smsCode->code
        ];

        $response = $this->json('post', "/signature/users/$user->id/files/$fileToSign->id/verification", $params);
        $response->assertStatus(200);
        $response->assertJsonFragment(['message' => 'verified']);
        $this->assertDatabaseHas('user_signed_file', [
            'user_id' => $user->id,
            'file_id' => $fileToSign->id
        ]);
        $this->assertDatabaseMissing('sms_codes', [
            'user_id' => $user->id,
            'code' => $smsCode->code
        ]);
    }

    public function test_user_cant_verify_sms_with_wrong_code()
    {
        $this->authenticate();

        $group = ProfileGroup::factory()->create();
        $user = User::factory()->create();
        $user->groups()->attach($group, [
            'status' => 'active'
        ]);
        $fileToSign = File::factory()->create([
            'fileable_id' => $group->id,
            'fileable_type' => ProfileGroup::class
        ]);

        $params = [
            'code' => 454545
        ];
        $response = $this->json('post', "/signature/users/$user->id/files/$fileToSign->id/verification", $params);
        $response->assertStatus(422);
        $this->assertDatabaseMissing('user_signed_file', [
            'user_id' => $user->id,
            'file_id' => $fileToSign->id
        ]);
    }
}