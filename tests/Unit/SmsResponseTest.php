<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\Sms\SmsResponse;
use PHPUnit\Framework\TestCase;

class SmsResponseTest extends TestCase
{
    public function test_success_factory_sets_message_id_and_no_error(): void
    {
        $res = SmsResponse::success('abc123', 'Accepted');
        $this->assertTrue($res->isSuccess());
        $this->assertSame('abc123', $res->messageId);
        $this->assertSame('Accepted', $res->message);
        $this->assertNull($res->error);
    }

    public function test_error_factory_sets_error_and_no_message_id(): void
    {
        $res = SmsResponse::error('boom');
        $this->assertFalse($res->isSuccess());
        $this->assertNull($res->messageId);
        $this->assertNull($res->message);
        $this->assertSame('boom', $res->error);
    }
}