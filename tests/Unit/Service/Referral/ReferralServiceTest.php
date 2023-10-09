<?php

namespace Tests\Unit\Service\Referral;

use App\Models\Referral\Referral;
use App\Service\Referral\Core\ReferralDeterminationInterface;
use App\Service\Referral\Core\ReferralDto;
use App\Service\Referral\Core\ReferralGeneratorInterface;
use App\Service\Referral\ReferralService;
use Mockery;
use Tests\TenantTestCase;

class ReferralServiceTest extends TenantTestCase
{
    protected ReferralGeneratorInterface $generatorMock;
    protected ReferralDeterminationInterface $determinationMock;

    public function setUp(): void
    {    // Create mock instances of the interfaces
        $this->generatorMock = Mockery::mock(ReferralGeneratorInterface::class);
        $this->determinationMock = Mockery::mock(ReferralDeterminationInterface::class);
        parent::setUp();

    }

    public function test_it_can_generate_url()
    {
        $referral = Referral::factory()->create();
        $user = $referral->referrer->user;
        // Create a ReferralDto instance to be returned by the generator
        $referralDto = ReferralDto::fromModel($referral);

        // Expect the generator's generate method to be called with the user and return the referralDto
        $this->generatorMock->shouldReceive('generate')
            ->once()
            ->with($user)
            ->andReturn($referralDto);

        // Create an instance of ReferralService with the mocked dependencies
        $service = new ReferralService($this->generatorMock, $this->determinationMock);

        // Call the generateReferral method
        $result = $service->generateReferral($user);

        // Assert that the result matches the expected ReferralDto
        $this->assertEquals($referralDto, $result);
    }

    public function test_it_can_handle_request()
    {
        $referral = Referral::factory()->create();
        $referrer = $referral->referrer;

        // Expect the generator's generate method to be called with the user and return the referralDto
        $this->determinationMock->shouldReceive('determinate')
            ->once()
            ->with($referral)
            ->andReturn($referrer);

        // Create an instance of ReferralService with the mocked dependencies
        $service = new ReferralService($this->generatorMock, $this->determinationMock);

        // Call the generateReferral method
        $result = $service->request($referral);

        // Assert that the result matches the expected ReferralDto
        $this->assertEquals($referrer, $result);
    }
}
