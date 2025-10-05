<?php

namespace App\Jobs;

use App\Services\Sms\SmsServiceInterface;
use App\Services\Sms\SmsErrors;
use App\Enums\ErrorCode;
use App\Enums\SmsStatus;
use App\Repositories\SmsRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly int $smsId) {}

    public function handle(SmsServiceInterface $sender, SmsRepository $smsRepository): void
    {
        $sms = $smsRepository->find($this->smsId);
        if (!$sms) {
            return;
        }

        $result = $sender->send($sms);

        if ($result->isSuccess()) {
            $smsRepository->markSent(
                $sms,
                $result->messageId
            );
        } else {
            $smsRepository->markFailed(
                $sms,
                $result->error ?? SmsErrors::message(ErrorCode::UnknownError)
            );
        }
    }
}
