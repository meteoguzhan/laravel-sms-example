<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Sms;
use App\Enums\SmsStatus;
use App\Services\Sms\SmsService;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;

class SmsServiceTest extends TestCase
{
    private function createSms(): Sms
    {
        $sms = new Sms([
            'recipient_phone' => '905551234567',
            'message' => 'Test message',
            'status' => SmsStatus::Pending,
        ]);
        $sms->id = 1;
        return $sms;
    }

    public function test_send_success(): void
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'messageId' => 'msg123',
                'message' => 'Accepted'
            ]))
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $service = new SmsService('http://localhost:9000/', $client);
        $result = $service->send($this->createSms());

        $this->assertTrue($result->isSuccess());
        $this->assertSame('msg123', $result->messageId);
        $this->assertSame('Accepted', $result->message);
        $this->assertNull($result->error);
    }

    public function test_send_success_without_message(): void
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'messageId' => 'msg456'
            ]))
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $service = new SmsService('http://localhost:9000/', $client);
        $result = $service->send($this->createSms());

        $this->assertTrue($result->isSuccess());
        $this->assertSame('msg456', $result->messageId);
        $this->assertSame('Accepted', $result->message);
        $this->assertNull($result->error);
    }

    public function test_send_failure_missing_message_id(): void
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'message' => 'OK'
            ]))
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $service = new SmsService('http://localhost:9000/', $client);
        $result = $service->send($this->createSms());

        $this->assertFalse($result->isSuccess());
        $this->assertStringContainsString('Unexpected response from SMS provider', (string) $result->error);
    }

    public function test_send_failure_provider_error(): void
    {
        $mock = new MockHandler([
            new Response(400, ['Content-Type' => 'application/json'], json_encode([
                'error' => 'Invalid phone number'
            ]))
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $service = new SmsService('http://localhost:9000/', $client);
        $result = $service->send($this->createSms());

        $this->assertFalse($result->isSuccess());
        $this->assertSame('Invalid phone number', $result->error);
    }

    public function test_send_failure_invalid_json(): void
    {
        $mock = new MockHandler([
            new Response(400, ['Content-Type' => 'application/json'], 'invalid-json')
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $service = new SmsService('http://localhost:9000/', $client);
        $result = $service->send($this->createSms());

        $this->assertFalse($result->isSuccess());
        $this->assertSame('invalid-json', $result->error);
    }

    public function test_send_failure_connection_exception(): void
    {
        $mock = new MockHandler([
            new ConnectException('Connection failed', new Request('POST', 'sms'))
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $service = new SmsService('http://localhost:9000/', $client);
        $result = $service->send($this->createSms());

        $this->assertFalse($result->isSuccess());
        $this->assertSame('Connection failed', $result->error);
    }
}