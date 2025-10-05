<?php

namespace App\Jobs;

use App\Repositories\SmsRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessPendingSmsBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly int $limit = 2) {}

    public function handle(SmsRepository $smsRepository): void
    {
        $items = $smsRepository->fetchPending($this->limit);
        foreach ($items as $sms) {
            SendSmsJob::dispatch($sms->id)->onQueue(config('sms.queue', 'default'));

            $smsRepository->markProcessing($sms);
        }
    }
}
