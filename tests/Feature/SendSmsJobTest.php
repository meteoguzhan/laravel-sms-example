<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Jobs\SendSmsJob;
use App\Models\Sms;
use App\Enums\SmsStatus;
use App\Services\Sms\SmsResponse;
use App\Services\Sms\SmsServiceInterface;
use App\Repositories\SmsRepository;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\App;

class SendSmsJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_marks_sms_sent_on_success(): void
    {
        $sms = Sms::factory()->processing()->create();

        App::bind(SmsServiceInterface::class, function () {
            return new class implements SmsServiceInterface {
                public function send(Sms $sms): SmsResponse
                {
                    return SmsResponse::success('msg999', 'Accepted');
                }
            };
        });

        $job = new SendSmsJob($sms->id);
        $job->handle(app(SmsServiceInterface::class), app(SmsRepository::class));

        $sms->refresh();
        $this->assertSame(SmsStatus::Sent, $sms->status);
        $this->assertSame('msg999', $sms->provider_message_id);
        $this->assertNotNull($sms->sent_at);
        $this->assertNull($sms->error);
    }

    public function test_job_marks_sms_failed_on_error(): void
    {
        $sms = Sms::factory()->processing()->create();

        App::bind(SmsServiceInterface::class, function () {
            return new class implements SmsServiceInterface {
                public function send(Sms $sms): SmsResponse
                {
                    return SmsResponse::error('provider-error');
                }
            };
        });

        $job = new SendSmsJob($sms->id);
        $job->handle(app(SmsServiceInterface::class), app(SmsRepository::class));

        $sms->refresh();
        $this->assertSame(SmsStatus::Failed, $sms->status);
        $this->assertNull($sms->provider_message_id);
        $this->assertNull($sms->sent_at);
        $this->assertSame('provider-error', $sms->error);
    }

    public function test_job_returns_when_sms_not_found(): void
    {
        $job = new SendSmsJob(999);
        $job->handle(app(SmsServiceInterface::class), app(SmsRepository::class));

        $this->assertDatabaseCount('sms', 0);
        $this->assertTrue(true);
    }
}
