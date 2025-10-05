<?php

namespace App\Repositories;

use App\Models\Sms;

interface SmsRepositoryInterface
{
    /**
     * Create a new SMS record with given attributes.
     */
    public function create(array $attributes): Sms;

    /**
     * Create a new pending SMS from recipient and message.
     */
    public function createPending(string $recipientPhone, string $message): Sms;

    /**
     * Find SMS by id or return null.
     */
    public function find(int $id): ?Sms;

    /**
     * Fetch up to limit pending SMS records ordered by oldest first.
     */
    public function fetchPending(int $limit = 2): array;

    /**
     * Fetch up to limit sent SMS records ordered by newest first.
     */
    public function fetchSent(int $limit = 50): array;

    /**
     * Persist changes to the given SMS model.
     */
    public function save(Sms $sms): void;

    /**
     * Mark SMS as processing and persist.
     */
    public function markProcessing(Sms $sms): void;

    /**
     * Mark SMS as sent, set provider id and sent_at, then persist.
     */
    public function markSent(Sms $sms, ?string $providerMessageId): void;

    /**
     * Mark SMS as failed with error and persist.
     */
    public function markFailed(Sms $sms, string $error): void;
}
