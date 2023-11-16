<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\App;
use App\User;
use Tests\TestCase;

class EmployeeControllerTest extends TestCase
{
    protected User $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::find(5);
    }

    public function test_compare_responses_filter_all()
    {
        $expectedResponse = $this->prepareResponse('all', 'old');
//        $chunks1 = collect($expectedResponse['users'])->chunk(1000);

        $newResponse = $this->prepareResponse('all');
//        $chunks2 = collect($newResponse['users'])->chunk(1000);

        $this->assertEquals($expectedResponse['users'], $newResponse['users']);
    }

    public function test_compare_responses_filter_deactivated()
    {
        $expectedResponse = $this->prepareResponse('deactivated', 'old');
        $newResponse = $this->prepareResponse('deactivated');

        $this->assertEquals($expectedResponse, $newResponse);
    }

    public function test_compare_responses_filter_nonfilled()
    {
        $expectedResponse = $this->prepareResponse('nonfilled', 'old');

        $newResponse = $this->prepareResponse('nonfilled');

        $this->assertEquals($expectedResponse, $newResponse);
    }

    public function test_compare_responses_filter_trainees()
    {
        $expectedResponse = $this->prepareResponse('trainees', 'old');

        $newResponse = $this->prepareResponse('trainees');

        $this->assertEquals($expectedResponse, $newResponse);
    }

    public function test_compare_responses_filter_reactivated()
    {
        $expectedResponse = $this->prepareResponse('reactivated', 'old');

        $newResponse = $this->prepareResponse('reactivated');

        $this->assertEquals($expectedResponse, $newResponse);
    }

    public function test_compare_responses_filter_active()
    {
        $expectedResponse = $this->prepareResponse('active');

        $newResponse = $this->prepareResponse('active', false);

        $this->assertEquals($expectedResponse, $newResponse);
    }

    protected function prepareResponse($filter = null, $expected = null)
    {
        if ($expected) {
            $url = 'https://bp.jobtron.org/timetracking/get-persons';
        } else {
            $url = 'https://bp.jobtron.org/timetracking/get-persons-testing';
        }

        $response = $this->actingAs($this->user)
            ->call('GET', $url, [
                "filter" => $filter,
                "segment" => [],
                "job" => 0,
                "notrainees" => false
            ])->json();

        foreach ($response['users'] as $index => $model) {
            unset($response['users'][$index]['created_at']);
            unset($response['users'][$index]['deleted_at']);
            $response['users'][$index]['groups'] = array_unique($model['groups']);
            sort($response['users'][$index]['groups']);
        }

        return $response;
    }
}
