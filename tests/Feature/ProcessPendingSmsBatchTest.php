<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Jobs\ProcessPendingSmsBatch;
use App\Jobs\SendSmsJob;
use App\Models\Sms;
use App\Enums\SmsStatus;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

class ProcessPendingSmsBatchTest extends TestCase
{
    use RefreshDatabase;

    public function test_batch_dispatches_send_jobs_and_marks_processing(): void
    {
        Queue::fake();

        Sms::factory()->count(3)->pending()->create();
        $job = new ProcessPendingSmsBatch(limit: 2);
        $job->handle(app(\App\Repositories\SmsRepository::class));

        Queue::assertPushed(SendSmsJob::class, 2);

        $processed = Sms::query()->where('status', SmsStatus::Processing)->get();
        $this->assertCount(2, $processed);
    }
}
