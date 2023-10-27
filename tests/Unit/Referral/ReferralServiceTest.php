<?php

namespace Tests\Unit\Referral;

use App\Service\Referral\Core\GeneratorInterface;
use App\Service\Referral\Core\ReferralUrlDto;
use App\Service\Referral\ReferralService;
use App\User;
use Mockery;
use Tests\TenantTestCase;

class ReferralServiceTest extends TenantTestCase
{
    protected GeneratorInterface $generatorMock;

    public function setUp(): void
    {    // Create mock instances of the interfaces
        $this->generatorMock = Mockery::mock(GeneratorInterface::class);
        parent::setUp();

    }

    public function test_it_can_generate_url()
    {
        $referrer = User::factory()->create();

        // Create a ReferralDto instance to be returned by the generator
        $referralDto = ReferralUrlDto::make(
            $referrer->url(),
            $referrer->id,
        );

        // Expect the generator's generate method to be called with the user and return the referralDto
        $this->generatorMock->shouldReceive('generate')
            ->once()
            ->with($referrer)
            ->andReturn($referralDto);

        // Create an instance of ReferralService with the mocked dependencies
        $service = new ReferralService($this->generatorMock);

        // Call the generateReferral method
        $result = $service->url($referrer);

        // Assert that the result matches the expected ReferralDto
        $this->assertEquals($referralDto, $result);
    }
}
