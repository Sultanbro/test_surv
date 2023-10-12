<?php

namespace Tests\Unit\Service\Referral;

use Tests\TenantTestCase;

class ProcessCreateBitrixLeadTest extends TenantTestCase
{
//    public function test_can_create_lead(): void
//    {
//        // Create a Referral
//        $referral = Referral::factory()->create();
//
//        // Create a ReferralRequestDto
//        $request = new ReferralRequestDto(
//            'test name',
//            '37493270709',
//        );
//
//        // Create a mock for ReferralLeadServiceInterface
//        $leadService = Mockery::mock(ReferralLeadServiceInterface::class);
//        $leadService->shouldReceive('create')
//            ->once()
//            ->with($referral, $request);
//
//        // Dispatch the job and pass the mock objects
//        Queue::fake();
//        Bus::fake();
//
//        $job = new ProcessCreateBitrixLead($referral, $request);
//        $job->handle($leadService);
//        // Assert that the job was dispatched and handled correctly
//        Queue::assertNotPushed(ProcessCreateBitrixLead::class);
//        Bus::assertDispatched(ProcessCreateBitrixLead::class, function ($job) use ($referral, $request, $leadService) {
//            return $job->referral === $referral
//                && $job->request === $request
//                && $job->leadService === $leadService;
//        });
//    }
}
