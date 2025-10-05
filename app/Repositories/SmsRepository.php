<?php

namespace App\Repositories;

use App\Models\Sms;
use App\Enums\SmsStatus;

class SmsRepository implements SmsRepositoryInterface
{
    public function create(array $attributes): Sms
    {
        return Sms::create($attributes);
    }

    public function createPending(string $recipientPhone, string $message): Sms
    {
        return $this->create([
            'recipient_phone' => $recipientPhone,
            'message' => $message,
            'status' => SmsStatus::Pending,
        ]);
    }

    public function find(int $id): ?Sms
    {
        return Sms::find($id);
    }

    public function fetchPending(int $limit = 2): array
    {
        return Sms::query()
            ->where('status', SmsStatus::Pending)
            ->orderBy('id')
            ->limit($limit)
            ->get()
            ->all();
    }

    public function fetchSent(int $limit = 50): array
    {
        return Sms::query()
            ->where('status', SmsStatus::Sent)
            ->orderByDesc('sent_at')
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->all();
    }

    public function save(Sms $sms): void
    {
        $sms->save();
    }

    public function markProcessing(Sms $sms): void
    {
        $sms->status = SmsStatus::Processing;
        $this->save($sms);
    }

    public function markSent(Sms $sms, ?string $providerMessageId): void
    {
        $sms->status = SmsStatus::Sent;
        $sms->provider_message_id = $providerMessageId;
        $sms->sent_at = now();
        $sms->error = null;
        $this->save($sms);
    }

    public function markFailed(Sms $sms, string $error): void
    {
        $sms->status = SmsStatus::Failed;
        $sms->error = $error;
        $this->save($sms);
    }
}
