<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Enums\ErrorCode;
use App\Services\Sms\SmsErrors;
use PHPUnit\Framework\TestCase;

class SmsErrorsTest extends TestCase
{
    public function test_messages_are_mapped_correctly(): void
    {
        $this->assertSame('Not found', SmsErrors::message(ErrorCode::NotFound));
        $this->assertSame('Unexpected response from SMS provider', SmsErrors::message(ErrorCode::ProviderUnexpectedResponse));
        $this->assertSame('SMS provider request failed', SmsErrors::message(ErrorCode::ProviderRequestFailed));
        $this->assertSame('SMS provider returned error', SmsErrors::message(ErrorCode::ProviderReturnedError));
        $this->assertSame('Unknown error', SmsErrors::message(ErrorCode::UnknownError));
    }
}