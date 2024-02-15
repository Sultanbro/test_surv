<?php

namespace Tests\Feature\Signature;

use App\Models\File\File;
use App\Models\Integration\Integration;
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
 * @url POST signature/integrations
 * @url GET signature/integrations
 */
class IntegrationTest extends TenantTestCase
{
    use HasAuthUser;

    public function test_user_can_save_integration()
    {
        $this->authenticate();

        $params = [
            'app_id' => '454545',
            'api_key' => 'some api key'
        ];

        $response = $this->json('post', "/signature/integrations", $params);
        $response->assertStatus(200);
        $this->assertDatabaseHas('integrations', [
            'reference' => 'u-call'
        ]);
    }

    public function test_user_can_get_ucall_integration()
    {
        $this->authenticate();

        Integration::factory()->create([
            'reference' => 'u-call'
        ]);

        $response = $this->json('get', "/signature/integrations");
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'reference' => 'u-call'
        ]);
    }
}