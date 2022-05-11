<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Message;
use Carbon\Carbon;

class MessageTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test message time check.
     * @test
     * @return void
     */
    public function inTime()
    {
        $message = new Message();

        $message->start_date_time = Carbon::now()->format('d.m.Y');
        $message->start_time = '9:00';
        $message->end_time = '22:00';

        $this->assertTrue($message->inTime());


        $message->start_date_time = Carbon::now()->addDay()->format('d.m.Y');
        $message->start_time = '9:00';
        $message->end_time = '22:00';

        $this->assertTrue(!$message->inTime());


        $message->start_date_time = Carbon::now()->subDay()->format('d.m.Y');
        $message->start_time = '9:00';
        $message->end_time = '22:00';

        $this->assertTrue(!$message->inTime());
    }
}
