<?php

namespace Tests\Unit\Service\Referral;

use App\Api\Bitrix\LeadApiInterface;
use App\Models\Referral\Referral;
use App\Service\Referral\Core\ReferralLeadService;
use App\Service\Referral\Core\ReferralRequestDto;
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
        $service = new ReferralLeadService($leadApi);

        // Create a Referral
        $referral = Referral::factory()->create();

        // Create a ReferralRequestDto
        $request = new ReferralRequestDto(
            'test name',
            '37493270709',
        );

        // Test the create method of ReferralLeadService
        $service->create($referral, $request);

        // Assert that the Lead model has been created with the expected data
        $this->assertDatabaseHas('bitrix_leads', [
            'lead_id' => 123,
            'name' => 'test name',
            'phone' => '37493270709',
            'status' => 'NEW',
        ]);
    }
}
