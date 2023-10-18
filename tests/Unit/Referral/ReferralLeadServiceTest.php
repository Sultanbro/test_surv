<?php

namespace Tests\Unit\Referral;

use App\Api\Bitrix\LeadApiInterface;
use App\Service\Referral\Core\RequestDto;
use App\Service\Referral\LeadService;
use App\User;
use Mockery;
use Tests\TenantTestCase;

class ReferralLeadServiceTest extends TenantTestCase
{
    public function test_can_create_lead(): void
    {
        // Create a mock for LeadApiInterface
        $leadApi = Mockery::mock(LeadApiInterface::class);
        $leadApi->shouldReceive('create')
            ->andReturn(['result' => 123]);

        // Create an instance of ReferralLeadService with the mock LeadApiInterface
        $service = new LeadService($leadApi);

        // Create a Referrer
        $referrer = User::factory()->create();

        // Create a ReferralRequestDto
        $request = new RequestDto(
            'test name',
            '37493270709',
        );

        // Test the create method of ReferralLeadService
        $service->create($referrer, $request);

        // Assert that the Lead model has been created with the expected data
        $this->assertDatabaseHas('bitrix_leads', [
            'lead_id' => 123,
            'name' => 'test name',
            'phone' => '37493270709',
            'status' => 'NEW',
        ]);
    }
}
